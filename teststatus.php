<?php
include_once('includes/header.php');  

$sql = "SELECT COUNT(*) AS cnt FROM url WHERE test_id = $testid and crawled=1";
$crawled = db_fetch_obj($sql);

$sql = "SELECT status FROM test WHERE test_id = $testid";
$test = db_fetch_obj($sql);

$ret = new StdClass();
$ret->status = $test->status;
$ret->count  = $crawled->cnt;

print json_encode($ret);