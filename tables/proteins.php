<?php
include_once('../common.php');

$start = $_POST['start'];
$end = (int) $start + 10;
$query = 'SELECT GeneName, Type, EnsPID FROM T_Domain ORDER BY GeneName LIMIT ' . strval($start) . ",10;";
$query_params = array();
$stmt = $dbh->prepare($query);
$stmt->execute($query_params);
$proteins = array();
while ($row = $stmt->fetch()) {
	$proteins[] = $row;
}

foreach ($proteins as $protein) {

	$query = "SELECT COUNT(DISTINCT Int_EnsPID) FROM T_Interaction_MT WHERE T_Interaction_MT.IID IN (SELECT T_Interaction.IID FROM T_Interaction WHERE T_Interaction.Domain_EnsPID=:protid) AND T_Interaction_MT.Eval='loss of function'";
	$query_params = array(':protid' => $protein[2]);
	$stmt = $dbh->prepare($query);
	$stmt->execute($query_params);
	$loss_num = $stmt->fetch()[0];

	$query = "SELECT COUNT(DISTINCT Int_EnsPID) FROM T_Interaction_MT WHERE T_Interaction_MT.IID IN (SELECT T_Interaction.IID FROM T_Interaction WHERE T_Interaction.Domain_EnsPID=:protid) AND T_Interaction_MT.Eval='gain of function'";
	$query_params = array(':protid' => $protein[2]);
	$stmt = $dbh->prepare($query);
	$stmt->execute($query_params);
	$gain_num = $stmt->fetch()[0];

	$query = "SELECT COUNT(DISTINCT Interaction_EnsPID) FROM T_Interaction WHERE Domain_EnsPID=:protid;";
	$query_params = array(':protid' => $protein[2]);
	$stmt = $dbh->prepare($query);
	$stmt->execute($query_params);
	$mut_num = $stmt->fetch()[0];

?>
	<tr>
		<td><a href="http://ensembl.org/id/<?php echo $protein[2];?>"><?php echo $protein[2];?></a></td>
        <td><?php echo $protein[0];?></td>
        <td><?php echo $protein[1];?></td>
        <td><?php echo $gain_num;?></td>
        <td><?php echo $loss_num;?></td>
        <td><?php echo $mut_num;?></td>
        <td><a href="./network/?limit=50&genename=<?php echo $protein[0];?>">Network</a></td>
	</tr>
<?php } ?>