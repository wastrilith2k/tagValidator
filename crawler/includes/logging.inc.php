<?php

function watchdog() {  
  $args = func_get_args();
  if (count($args) >= 2) {
    $message = call_user_func_array('sprintf', $args);
  } else {
    $message = $args[0];
  }
  print $message . "\n";
}