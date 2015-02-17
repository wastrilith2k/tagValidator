<?php
include_once('includes/header.php');  
// Include header
print file_get_contents('content/header.html');
?>
<div id="createtest">
  <a href="/createtest.php">Create test</a>
</div>
<div id="managetests">
  <table>
    <thead>
      <td>Name</td>
      <td>Status</td>
      <td>Action</td>
    </thead>
<?php    
  $sql = "SELECT test_id,test_name,status FROM test";
  $result = mysqli_query($con,$sql);
  if (!$result) {    
    die('Error: ' . mysqli_error($con) . ' in ' . $sql);
  }
  while($row = mysqli_fetch_object($result)) {
    print '<tr>';
    print '<td><a href="viewtest.php?testid=' . $row->test_id . '">' . $row->test_name . '</a></td>';
    print '<td>' . $status[$row->status] . '</td>';
    print '<td><a href="edittest.php?testid=' . $row->test_id . '">edit</a>|<a href="deletetest.php?testid=' . $row->test_id . '">delete</a></td>';
    print '</tr>';
  }
?>
  </table>
</div>
<?php
print file_get_contents('content/footer.html');
?>