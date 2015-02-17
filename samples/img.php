<?php
// Create connection
$con = mysqli_connect("localhost","root","Ensabahnur1!","crawls");

// Check connection
if (mysqli_connect_errno($con)){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
$sql = "SELECT url FROM url WHERE url_id = " . mysqli_real_escape_string($con,$_GET['url_id']);
$result = mysqli_query($con,$sql);
while($obj = $result->fetch_object()) {
  // Obtain image for this page as well
  $results = array();
  // Yoink in image name
  $filename = realpath(dirname(__FILE__)) . '/temp/page' . mysqli_real_escape_string($con,$_GET['url_id']) . '.png';

  if (!file_exists($filename)) {   
    // Create image file
    exec('phantomjs rasterize.js ' . $obj->url . ' ' . $filename, $results);	  
  } 
  $size = getimagesize($filename);
  $res = array_pop($results);	  

  if ($res == 'success') {
    header("Content-type: " . $size['mime']);
    print file_get_contents($filename);
  }  
  unlink($filename);
}

mysqli_close($con);