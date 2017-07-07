<?php
function questionMarks($matrixLength, $rowLength)
{
	$questionMarksImplode = function ($elem) { return '(' . implode( ',', $elem ) . ')'; };
	$chunk = array_chunk(array_fill(0, $matrixLength, '?'), $rowLength);
	$QMarks = implode( ',', array_map( $questionMarksImplode, $chunk ) );
	return $QMarks;
}

$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);

/* Domain */
$query = "INSERT IGNORE INTO `T_Domain` (`Domain`, `Type`, `DomainStartPos`, `DomainEndPos`, `DomainSequence`, `Domain_Interpro_ID`, `Domain_EnsPID`, `Domain_EnsTID`)
		  VALUES " . questionMarks(count($domains), 8);
$handler = $dbh->prepare($query);
$handler->execute($domains);

/* PWM */
$query = "INSERT IGNORE INTO  `T_PWM` (`PWM` ,`Domain`) VALUES " . questionMarks(count($PWM), 2);
$handler = $dbh->prepare($query);
$handler->execute($PWM);

/* Interactions */
$query = "INSERT IGNORE INTO  `T_Interactions` (`IID`, `PWM`, `Domain_EnsPID`, `Peptide_EnsPID`, `Dataset_ID`)
		  VALUES " . questionMarks(count($interactions), 5);
$handler = $dbh->prepare($query);
$handler->execute($interactions);

/* Mutation */
$query = "INSERT IGNORE INTO  `T_Mutations` (`Mutation_ID` ,`Mut_Description` ,`Tumour_Site`, `Mutation_Source_ID`, `Source`, `Peptide_EnsGID`, `Peptide_EnsPID`)
		  VALUES " . questionMarks(count($mutations), 7);
$handler = $dbh->prepare($query);
$handler->execute($mutations);

/* Interactions_MT */
$query = "INSERT IGNORE INTO  `T_Interactions_MT` (`IID`, `Mutation_ID`, `WT`, `MT`, `WTscore`, `MTscore`, `DeltaScore`, `LOG2`, `Eval`)
		  VALUES " . questionMarks(count($interactions_MT), 9);
$handler = $dbh->prepare($query);
$handler->execute($interactions_MT);

/* Interactions_Eval */
$query = "INSERT IGNORE INTO  `T_Interactions_Eval` (`IID`, `Gene_expression`, `Protein_expression`, `Disorder`,`Surface_accessibility`, `Peptide_conservation`, `Molecular_function`, `Biological_process`, `Localization`, `Sequence_signature`, `Avg`)
		  VALUES " . questionMarks(count($interactions_Eval), 11);
$handler = $dbh->prepare($query);
$handler->execute($interactions_Eval);

/* Ensembl */
$query = "INSERT IGNORE INTO  `T_Ensembl` (`EnsPID`, `EnsTID`, `EnsGID`, `Version`, `GeneName`, `Description`, `Sequence`)
		  VALUES " . questionMarks(count($ensembl), 7);
$handler = $dbh->prepare($query);
$handler->execute($ensembl);
