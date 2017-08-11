<?php
include_once('../../common.php');

$query ="DELETE FROM tissue_table_browser";
$stmt = $dbh->prepare($query);
$stmt->execute();

$query = "SELECT DISTINCT Tumour_Site FROM T_Mutations";
$stmt = $dbh->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch())
{
	$param = array(":tissue" => $row[0]);

	$query = "SELECT COUNT(Mutation_ID) FROM T_Mutations use index (Mutation_ID) WHERE Tumour_Site=:tissue";
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute($param);
	$mutation_count = $stmt2->fetch()[0];

	$query = "SELECT COUNT(Distinct Peptide_EnsPID) FROM T_Mutations WHERE Tumour_Site=:tissue";
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute($param);
	$protein_count = $stmt2->fetch()[0];

	$query = "SELECT Distinct Peptide_EnsPID FROM T_Mutations WHERE Tumour_Site=:tissue";
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute($param);

	$PID = array();
	while ($tmp = $stmt2->fetch())
	{
		$PID[] = $tmp[0];
	}
	$P_List = "'" . implode("','", $PID) . "'";

	// Count Interactions	
	$query = "SELECT COUNT(*) FROM T_Interactions_MT INNER JOIN T_Interactions
				ON T_Interactions.IID = T_Interactions_MT.IID
				WHERE Peptide_EnsPID IN(" . $P_List . ") AND Eval='loss of function'";
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute();
	$loss_num = $stmt2->fetch()[0];

	$query = "SELECT COUNT(*) FROM T_Interactions_MT INNER JOIN T_Interactions
				ON T_Interactions.IID = T_Interactions_MT.IID
				WHERE Peptide_EnsPID IN(" . $P_List . ") AND Eval='gain of function'";
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute();
	$gain_num = $stmt2->fetch()[0];

	$query = "INSERT INTO  `tissue_table_browser` (`Tissue` ,`Variants` ,`Proteins`, `Gain`, `Loss`)
				VALUES (:tissue,  :muts,  :prots, :gain, :loss);";
	$params = array(
						":tissue" => $row[0],
						":muts" => $mutation_count,
						":prots" => $protein_count,
						':gain' => $gain_num,
						':loss' => $loss_num
					);
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute($params);
}

header("Location: ../index.php?submit=Tissue");
