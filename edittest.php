<?php
include_once('includes/header.php');  
if ($testid) {
  if (isset($_POST['op']) && $_POST['op'] == 'Save test') {
    $offsite = isset($_POST['allow_offsite']) ? 1 : 0;
    $fields = array('test_name','initial_url','max_link_depth','max_links_crawled','whitelist','blacklist','allow_offsite','test_id');
    $args = array($_POST['test_name'],$_POST['initial_url'],$_POST['max_link_depth'],$_POST['max_links_crawled'],$_POST['whitelist'],$_POST['blacklist'],$offsite,$testid);
    $args = array_map('db_mysqli_real_escape_string', $args);  
    $values = array();
    $sql = "UPDATE test SET ";
    for ($i=0;$i<count($fields);$i++) {
      $values[] = $fields[$i] . " = '" . $args[$i] . "'";
    }
    $sql .= implode(",",$values) . ' WHERE test_id = ' . $testid;
    $result = mysqli_query($con,$sql);
    if (!$result) {    
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
    header("Location:  managetests.php");
  }
  print file_get_contents('content/header.html');
  $content = file_get_contents('content/testform.html');
  $content = str_replace('*|TESTNAME|*',$cfg->test_name,$content);
  $content = str_replace('*|INITIAL_URL|*',$cfg->initial_url,$content);
  $content = str_replace('*|MAX_LINK_DEPTH|*',$cfg->max_link_depth,$content);
  $content = str_replace('*|MAX_LINKS_CRAWLED|*',$cfg->max_links_crawled,$content);
  $content = str_replace('*|WHITELIST|*','',$content);
  $content = str_replace('*|BLACKLIST|*','',$content);
  $content = str_replace('*|TEST_ID|*',$cfg->test_id,$content);
  $content = str_replace('*|ALLOW_OFFSITE|*',($cfg->allow_offsite == 1)?'checked="checked"':'',$content);
  print $content;
} else {
  header("Location:  managetests.php");
}
print file_get_contents('content/footer.html');




