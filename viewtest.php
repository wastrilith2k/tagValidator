<?php
include_once('includes/header.php');  
// Include header
print file_get_contents('content/header.html');

$content = file_get_contents('content/viewtest.html');
$content = str_replace('*|TESTNAME|*',$cfg->test_name,$content);
$content = str_replace('*|INITIAL_URL|*',$cfg->initial_url,$content);
$content = str_replace('*|MAX_LINK_DEPTH|*',$cfg->max_link_depth,$content);
$content = str_replace('*|MAX_LINKS_CRAWLED|*',$cfg->max_links_crawled,$content);
$content = str_replace('*|WHITELIST|*',$cfg->whitelist,$content);
$content = str_replace('*|BLACKLIST|*',$cfg->blacklist,$content);
$content = str_replace('*|TEST_ID|*',$cfg->test_id,$content);
$content = str_replace('*|ALLOW_OFFSITE|*',($cfg->allow_offsite == 1)?'Yes':'No',$content);
$content = str_replace('*|RULES_RESULTS|*','',$content);
$content = str_replace('*|STATUS|*',$status[$cfg->status],$content);
print $content;
print file_get_contents('content/footer.html');
