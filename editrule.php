<?php
include_once('includes/header.php');  
if ($ruleid) {
  if (isset($_POST['op']) && $_POST['op'] == 'Save rule') {
    $fields = array('param','param_values','rule_id');
    $args = array($_POST['param'],$_POST['param_values'],$ruleid);
    $args = array_map('db_mysqli_real_escape_string', $args);  
    $values = array();
    $sql = "UPDATE rules SET ";
    for ($i=0;$i<count($fields);$i++) {
      $values[] = $fields[$i] . " = '" . $args[$i] . "'";
    }
    $sql .= implode(",",$values) . ' WHERE rule_id = ' . $ruleid;
    $result = mysqli_query($con,$sql);
    if (!$result) {    
      die('Error: ' . mysqli_error($con) . ' in ' . $sql);
    }
    header("Location:  managerules.php?testid=" . $testid);
  }
  print file_get_contents('content/header.html');
  $content = file_get_contents('content/ruleform.html');
  $content = str_replace('*|PARAM|*',$cfg->param,$content);
  $content = str_replace('*|PARAM_VALUES|*',$cfg->param,$content);
  $content = str_replace('*|TEST_ID|*',$testid,$content);
  print $content;
} else {
  if ($testid) header("Location:  managerules.php?testid=" . $testid);
  header("Location:  managetests.php");
}
print file_get_contents('content/footer.html');