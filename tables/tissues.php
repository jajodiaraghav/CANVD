<?php
include_once('../common.php');

// Get list of unique tissues in entire database
$start = $_POST['start'];
$query = "SELECT * FROM tissue_table_browser LIMIT " . strval($start) . ", 10";
$stmt = $dbh->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch())
{
?>
	<tr>
		<td><?php echo ucwords(str_replace("_"," ", $row[1])); ?></td>
		<td><?php echo $row[2]; ?></td>
		<td><?php echo $row[4]; ?></td>
		<td><?php echo $row[5]; ?></td>
		<td><?php echo $row[3];	?></td>
		<td><?php echo "<a href='./variants/?search=yes&tissue%5B%5D=".$row[1]."'>View Proteins</a>" ?></td>
	</tr>
<?php } ?>
