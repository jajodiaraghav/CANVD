<?php
include_once('../common.php');

$start = $_POST['start'];
$end = (int) $start + 10;

$query = 'SELECT GeneName, Type, Domain_EnsPID FROM T_Domain, T_Ensembl
			WHERE T_Ensembl.EnsPID = T_Domain.Domain_EnsPID ORDER BY GeneName
			LIMIT ' . strval($start) . ", 10;";
$stmt = $dbh->prepare($query);
$stmt->execute();

$proteins = array();
while ($row = $stmt->fetch())
{
	$proteins[] = $row;
}

foreach ($proteins as $protein)
{
	$param = array(':protid' => $protein[2]);

	$query = "SELECT COUNT(DISTINCT Interaction_EnsPID) FROM T_Interactions
				WHERE T_Interactions.IID = T_Interactions_MT.IID
				AND T_Interactions.Domain_EnsPID=:protid
				AND T_Interactions_MT.Eval='loss of function'";
	$stmt = $dbh->prepare($query);
	$stmt->execute($param);
	$loss_num = $stmt->fetch()[0];

	$query = "SELECT COUNT(DISTINCT Interaction_EnsPID) FROM T_Interactions
				WHERE T_Interactions.IID = T_Interactions_MT.IID
				AND T_Interactions.Domain_EnsPID=:protid
				AND T_Interactions_MT.Eval='gain of function'";
	$stmt = $dbh->prepare($query);
	$stmt->execute($param);
	$gain_num = $stmt->fetch()[0];

	$query = "SELECT COUNT(DISTINCT Interaction_EnsPID) FROM T_Interactions WHERE Domain_EnsPID=:protid;";
	$stmt = $dbh->prepare($query);
	$stmt->execute($param);
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
