<?php
include_once('includes/header.php');  
$result = db_query("UPDATE test SET status = 0 WHERE test_id = '$testid'");