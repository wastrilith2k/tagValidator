<?php
// Create connection
$con=mysqli_connect("localhost","***","***","validator");

// Set encoding
mysqli_set_charset($con,'utf8');

// Check connection
if (mysqli_connect_errno($con)){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

/*
 * Just run a query. Return the result
 *
 */
function db_query() {
  global $con;  

  $query = db_build_query(func_get_args());
  $result = mysqli_query($con,$query);
  
  if (!$result) {
    die('Error: ' . mysqli_error($con) . ' in ' . $query);
  }
  return $result;
}

/*
 * Build the SQL statement and sanitize the arguments
 *
 */
function db_build_query($args) {
  if (count($args) < 2) return $args[0];
  $query = array_shift($args);
  $args = array_map('db_mysqli_real_escape_string', $args);  
  array_unshift($args, $query);
  $query = call_user_func_array('sprintf', $args);
  return $query;
}

/*
 * Sanitizes whatever is passed to it
 *
 */
function db_mysqli_real_escape_string($arg) {
    global $con;
    return mysqli_real_escape_string($con, $arg);
}

function db_fetch_obj($sql) {
  $result = db_query($sql);
  $objs = array();
  while($row = mysqli_fetch_object($result)) {
    $objs[] = $row;
  }
  if (count($objs) == 1) return $objs[0];
  return $objs;
}