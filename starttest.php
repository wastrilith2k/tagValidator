<?php
include_once('includes/header.php');  
$result = db_query("UPDATE test SET status = 1 WHERE test_id = '$testid'");
header("Connection: close");
flush();
$nothing = shell_exec("php crawler/crawler.php $testid > /dev/null");
