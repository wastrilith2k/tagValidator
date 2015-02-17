<?php
session_start();
//$current_url = "http://$_SERVER[HTTP_HOST]/";
include_once('includes/db.inc.php');

$cfg = new stdClass();
$testid = NULL;
$ruleid = NULL;
$status = array('0'=>'Inactive','1'=>'Crawling','2'=>'Testing','3'=>'Complete');
if (isset($_GET['testid'])) {
  $testid = $_GET['testid'];
  $sql = "SELECT * FROM test WHERE test_id = " . $testid;
  $cfg = db_fetch_obj($sql);
}
if (isset($_GET['ruleid'])) {
  $ruleid = $_GET['ruleid'];
  $sql = "SELECT * FROM rules WHERE rule_id = " . $ruleid;
  $cfg = db_fetch_obj($sql);
}

if (isset($_SESSION['post'])) {
  $_POST = $_SESSION['post'];
  unset($_SESSION['post']);
}
