<?php
include_once('includes/header.php');  
// Include header
print file_get_contents('content/header.html');
?>
<div id="createrule">
  <a href="/createrule.php?testid=<?php print $testid; ?>">Create rule</a>
</div>
<div id="managerules">
  <table>
    <thead>
      <td>Parameter</td>
      <td>Value</td>
      <td>Actions</td>
    </thead>
<?php    
  $sql = "SELECT rule_id,test_id, param, param_values FROM rules WHERE test_id=" . $testid;
  $result = mysqli_query($con,$sql);
  if (!$result) {    
    die('Error: ' . mysqli_error($con) . ' in ' . $sql);
  }
  while($row = mysqli_fetch_object($result)) {
    print '<tr>';
    print '<td>' . $row->param . '</td>';
    print '<td>' . $row->param_values . '</td>';
    print '<td><a href="managerules.php?deleterule=' . $row->rule_id . '">delete</a></td>';
    print '</tr>';
  }
?>
  </table>
</div>
<a href="viewtest.php?testid=<?php print $testid; ?>">cancel</a>
<?php
print file_get_contents('content/footer.html');
?>