<?php
include_once('includes/header.php');  

if ($testid) {
  header("Location:  edittest.php?testid=" . $testid);
} else {
  if (isset($_POST['op']) && $_POST['op'] == 'Save test') {
    $offsite = isset($_POST['allow_offsite']) ? 1 : 0;
    $args = array($_POST['test_name'],$_POST['initial_url'],$_POST['max_link_depth'],$_POST['max_links_crawled'],$_POST['whitelist'],$_POST   ['blacklist'],$offsite);
    $args = array_map('db_mysqli_real_escape_string', $args);  
    $sql = "INSERT INTO test (test_name, initial_url, max_link_depth, max_links_crawled, whitelist, blacklist, allow_offsite) VALUES ('" . implode  ("','",$args) . "')";
    $result = mysqli_query($con,$sql);
    if (!$result) {    
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
    $testid=mysqli_insert_id($con);
    header("Location:  managetests.php");
  } else {
    // Include header
    print file_get_contents('content/header.html');
    $content = file_get_contents('content/testform.html');
    $content = str_replace('*|TESTNAME|*','',$content);
    $content = str_replace('*|INITIAL_URL|*','',$content);
    $content = str_replace('*|MAX_LINK_DEPTH|*','2',$content);
    $content = str_replace('*|MAX_LINKS_CRAWLED|*','100',$content);
    $content = str_replace('*|WHITELIST|*','',$content);
    $content = str_replace('*|BLACKLIST|*','',$content);
    $content = str_replace('*|ALLOW_OFFSITE|*','checked="checked"',$content);
    print $content;
  }
}
print file_get_contents('content/footer.html');
?>