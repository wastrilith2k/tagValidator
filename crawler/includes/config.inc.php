n<?php

include_once($_SERVER['DOCUMENT_ROOT'] . 'includes/db.inc.php');

$testid=NULL;
if(isset($argv[1]) && is_numeric($argv[1])) {
  $testid = $argv[1];
} else {
  exit();
}
$reset = FALSE;
if(isset($argv[2]) && $argv[2] == 'RESET') {
  $reset = TRUE;
}

$successful_crawls = 0;
$sql = "SELECT * FROM test WHERE test_id = " . $testid;
$cfg = db_fetch_obj($sql);
  
// ******* CONFIG *******
$depth = $cfg->max_link_depth;
$throttle = $cfg->max_links_crawled;
$successful_crawls = 0;
$initial_url = $cfg->initial_url;

// Requires ALL variations of the host
$whitelist = $cfg->whitelist != '' ? explode(',',$cfg->whitelist) : array(); 
$blacklist = $cfg->blacklist != '' ? explode(',',$cfg->blacklist) : array(); 

if ($cfg->allow_offsite != 1) {
  $url = parse_url($initial_url);
  // Escape the periods in the host name and add to the whitelist
  $whitelist[] = $url['host'];
  print 'Added ' . $url['host'] . " to whitelist\n";
}

print "Whitelist: " . implode(',',$whitelist);
