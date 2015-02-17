<?php

include_once('lib/simple_html_dom.php');
include_once('includes/config.inc.php');
include_once('includes/logging.inc.php');

watchdog("Using character set: %s", mysqli_character_set_name($con));

ini_set('max_execution_time', 3600);

// Clear the tables

if ($reset) {
  $result = db_query("DELETE FROM url WHERE test_id = '$testid'");
  $result = db_query("DELETE FROM url_relationship WHERE test_id = '$testid'");
}
$result = db_query("UPDATE test SET status =1 WHERE test_id = '$testid'");

function crawl_url($url) {
  global $con,$throttle,$successful_crawls,$blacklist,$whitelist,$testid;

  try {
    // Throttling is based on attempted crawls, not successful crawls
    $throttle--;
    if ($throttle < 0) wrapup();

    // Has crawling been cancelled by the user?
    $test = db_fetch_obj("SELECT status FROM test WHERE test_id = '$testid'");
    if ($test->status == 0) exit();

    $url_id = get_url_id($url);
    // If this URL isn't valid, abort
    if (!$url_id) return false;
    // Reset script timeout. Should not take more than 2 minutes for a page. If it does, cancel!
    set_time_limit(120);
    // Mark this URL as crawled
    set_as_crawled($url_id);

    print "Processing $url\n"; 

    // We've already made sure it's valid so we shouldn't get here if it's invalid
    $html = crawler_get_file_contents($url);
    try {
      if (empty($html)) {
        print "Empty html found for $url\n";
      }elseif (!$html){
        // If HTML is false, this wasn't a page we want to track
      } else {
        // Add or Update title
        $title = '';
        try {
          $titleObj = $html->find("title",0);
          $title = addslashes(mysqli_real_escape_string($con,$titleObj->plaintext));
        } catch (Exception $e) {
        }

        $sql = "UPDATE url SET title = '" . $title . "' WHERE url_id=" . $url_id;
        $result = mysqli_query($con, $sql);
        if (!$result) {
          die('Error: ' . mysqli_error($con) . ' in ' . $sql);
        }

        // Obtain webtrends calls
        $results = array();
        exec('phantomjs --cookies-file=cookies.txt sniffer.js ' . $url . ' &', $results);    
        if (count($results) > 0) {        
          $filtered_results = array();
          foreach($results as $result) {
            if (preg_match('/https{0,1}\:\/\/.*\/dcs[a-z0-9]{22}_[a-z0-9]{4}\/dcs.gif*/i',$result) === 1) {
              $filtered_results[] = $result;
            }
          }
          print implode(',',$filtered_results);
          if (count($filtered_results) > 0) {
            $content = json_encode($filtered_results);        
            $content = iconv(iconv_get_encoding('in_charset'), 'utf-8', $content);
            $content = addslashes($content);
            $content = mysqli_real_escape_string($con, $content);

            $sql = sprintf("UPDATE url SET results = '%s' WHERE url_id=%d",$content,$url_id);
            $result = mysqli_query($con,$sql);
            if (!$result) {
              die('Error: ' . mysqli_error($con) . ' in ' . $sql);
            }
          }
        }  

        // Cycle through all the links on the page!
        print "Crawling $url\n"; 
        foreach($html->find("a") as $link){
          if ($link->href && strtolower(substr($link->href, 0,4)) == 'http') {
            $link->href = strip_hash($link->href);
            // Obtain the HREF
            $dest_url = addslashes($link->href);

            // Is the URL Whitelisted?
            if (count($whitelist) > 0 && preg_match('/(' . implode(')|(',$whitelist) . ')/i', parse_url($link->href,PHP_URL_HOST)) != 1) {
              print parse_url($link->href,PHP_URL_HOST) . " is not whitelisted.\n";
              continue;
            }

            if (count($blacklist) > 0 && preg_match('/(' . implode(')|(',$blacklist) . ')/i', parse_url($link->href,PHP_URL_HOST)) === 1) {
              print parse_url($link->href,PHP_URL_HOST) . " is blacklisted.\n";
              continue;
            }

            // Get the HREF's url_id
            $dest_url_id = get_url_id($dest_url);
            // We'll take invalid URLs here as we'll want to track that

            $linktext = addslashes(mysqli_real_escape_string($con,$link->plaintext));

            // Add the URL to link_relationships
            $sql = "INSERT INTO url_relationship (source_id, destination_id, link_text,test_id) VALUES ($url_id, $dest_url_id, '$linktext', $testid)";
            $result = mysqli_query($con,$sql);
            if (!$result) {    
              die('Error: ' . mysqli_error($con) . ' in ' . $sql);
            }
          }
        }
      }
    } catch (Exception $e) {
      debug_backtrace();
    }
  }
  catch (Exception $e) {
    debug_backtrace();
  }

  $successful_crawls++;
  print "Crawls left: $throttle Crawls successful: $successful_crawls\n";
}

function strip_hash($url) {
  return strtok($url, "#");
}

function get_url_id($url) {
  global $con,$testid;
  $url_id = '';
  $url = addslashes($url);
    
  // Get the URL ID for the current page if there is one  
  $result = mysqli_query($con,"SELECT url_id FROM url WHERE url = '" . $url . "'");
  if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result)) {
      return $row['url_id'];
    }
  // if $url_id is still null
  } else {
    $sql = "INSERT INTO url (url,test_id) VALUES ('$url', $testid)";
    $result = mysqli_query($con,$sql);
    if ($result) {
      $url_id = mysqli_insert_id($con);
    } else {
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
  }

  // We now definitely have a $url_id
  return $url_id;
}

function crawler_get_file_contents($url, $contentType = array('text/html')) {
  $url = stripslashes($url);
  // the request
  $ch = curl_init(stripslashes($url));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_exec($ch);

  // Ensure the file exists/is accessible
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE) >= 400) {
    set_as_crawled(get_url_id($url), curl_getinfo($ch, CURLINFO_HTTP_CODE));
  print $url . " return code of " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    return false;
  }
  
  // Ensure it's the proper content type
  $valid = false;
  $html = false;
  foreach ($contentType as $ct) {
    if ((substr(strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE)),0,strlen($ct)) == $ct)) $valid = true;
  // Check for the content type being html as we'll need to use simple html dom to get the contents
  if ((substr(strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE)),0,9) == 'text/html')) $html = true;
  }
  if (!$valid) return false;
  
  // Get redirect if there is one
  $all_ch = curl_getinfo($ch);
  if ($all_ch['redirect_url'] != '') $url = $all_ch['redirect_url']; 
  
  if ($html) return file_get_html($url);
  return file_get_contents($url);
}

function set_as_crawled($url_id, $return_code = 200) {
  global $con;
  $sql = "UPDATE url SET crawled = 1, return_code = $return_code WHERE url_id=" . $url_id;
  $result = mysqli_query($con,$sql);
  if (!$result) {
    die('Error: ' . mysqli_error($con) . ' in ' . $sql);
  }
}


print "Starting at " . $initial_url;
// Add initial URL to the database
$url_id = get_url_id($initial_url);

// Now for the main event
while ($throttle > 0 && $depth > -1) {
  // Get list of uncrawled URLs
  $result = mysqli_query($con,"SELECT url FROM url WHERE crawled = 0 and crawlable = 1");
  while($row = mysqli_fetch_array($result)) {
  // Call crawl_url for each URL
    crawl_url(stripslashes($row['url']));
  }
  // Decrement depth
  $depth--;
}

function wrapup() {
  global $testid;
  $sql = "UPDATE test SET status = 2 WHERE test_id = " . $testid;
  $result = db_query($sql) ;
  exit();
}
