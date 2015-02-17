<?php

include('../includes/db.inc.php');

// Make sure to set the content type to utf8
header('Content-Type: text/html; charset=utf-8');

function t($text) {
  print $text . "<br />\n";
}

$target_params = explode(',',$_GET['target_params']);
$displayby = (isset($_GET['displayby'])) ? $_GET['displayby'] : 'param';


// Show results
$sql = "SELECT * FROM url WHERE crawled = 1";
$result = mysqli_query($con,$sql);
if (!$result) {
  die('Error: ' . mysqli_error($con) . ' in ' . $sql);
}
$params_urls = array();
while($row = mysqli_fetch_array($result)) {
  if ($displayby == 'url') {
    print '<fieldset><legend><a href="' . stripslashes($row['url']) . '">' . $row['title'] . '</a></legend>';
    t($row['url']);
  } elseif ($displayby == 'param') {
    $params_on_url = array();
  }
  $content = $row['results'];        
  $content = stripslashes($content);
  $hits = json_decode($content);
  $hitcount = 0;
  foreach ($hits as $hit) {    
    $hitcount++;
    $parts = parse_url($hit);
    if ($displayby == 'url') t("Data collector: " . $parts['host']);
    if ($displayby == 'url') t("DCSID: " . str_replace('/dcs.gif','',$parts['path']));
    foreach(explode('&',$parts['query']) as $param) {   
      $param = urldecode(str_replace('%25','%',$param));   
      $parm = explode("=",$param);
      if (in_array($parm[0],$target_params)) {
        if ($displayby == 'url') t($param);
      } else {
        if ($displayby == 'url') print '<!-- ' . $param . " -->\n";
      }
      if ($displayby == 'param')  {
        if (in_array($parm[0],$target_params)) {
            $params_on_url[] = $parm[0] . '=' . $parm[1];
        }
      }
    }
  }
  if ($displayby == 'param')  {
    if (count($params_on_url) > 0) {
      $unique = array_unique($params_on_url);
      sort($unique);
      $params_urls[json_encode($unique)][] = $row['url'];
    }
    if ($hitcount == 0) {
      $params_urls[json_encode(array('Not tagged'))][] = $row['url'];
    }
  }
  if ($displayby == 'url') {
    print "</fieldset>\n";
  }
}

if ($displayby == 'param') {
  t('<h2>Reporting on the following parameters:</h2>');
  t(implode('<br />',$target_params));
  print("<hr />\n");
  foreach($params_urls as $param_set => $urls) {
    print "<fieldset><legend>Parameter Set\n<br/></legend>";    
    foreach(json_decode($param_set) as $pair) {
      t($pair);
    }
    print("<br />\n");
    foreach($urls as $url) {
      t("<a href='$url' target='_blank'>$url</a>");
    }
    t('</fieldset>');
    print("<hr />\n");
  }
}