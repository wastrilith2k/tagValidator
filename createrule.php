<?php
include_once('includes/header.php');  

if ($ruleid) {
  header("Location:  " . $current_url . "editrule.php?ruleid=" . $ruleid);
} else {
  if (isset($_POST['op']) && $_POST['op'] == 'Save rule') {
    $args = array($testid,$_POST['param'],$_POST['param_values'],0);
    $args = array_map('db_mysqli_real_escape_string', $args);  
    $sql = "INSERT INTO rules (test_id, param, param_values, last_iteration) VALUES ('" . implode("','",$args) . "')";
    $result = mysqli_query($con,$sql);
    if (!$result) {    
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
    $ruleid=mysqli_insert_id($con);
    header("Location:  managerules.php?testid=" . $testid);
  } else {
    // Include header
    print file_get_contents('content/header.html');
    $content = file_get_contents('content/ruleform.html');
    $content = str_replace('*|PARAM|*','',$content);
    $content = str_replace('*|PARAM_VALUES|*','',$content);
    $content = str_replace('*|TEST_ID|*',$testid,$content);
    print $content;
  }
}
print file_get_contents('content/footer.html');
?>