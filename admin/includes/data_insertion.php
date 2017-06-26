<?php
function questionMarks($matrixLength, $rowLength)
{
	$questionMarksImplode = function ($elem) { return '(' . implode( ',', $elem ) . ')'; };
	$chunk = array_chunk(array_fill(0, $matrixLength, '?'), $rowLength);
	$QMarks = implode( ',', array_map( $questionMarksImplode, $chunk ) );
	return $QMarks;
}

/* Domain */
$query = "INSERT IGNORE INTO  `T_Domain` (`Domain`, `Type`, `DomainStartPos`, `DomainEndPos`, `DomainSequence`, `Domain_Interpro_ID`, `Domain_EnsPID`, `Domain_EnsTID`)
		  VALUES " . questionMarks(8 * ($columnLength - $pointer), 8);
$handler = $dbh->prepare($query);
$handler->execute($domains);

/* PWM */
$query = "INSERT IGNORE INTO  `T_PWM` (`PWM` ,`Domain`) VALUES " . questionMarks(2 * ($columnLength - $pointer), 2);
$handler = $dbh->prepare($query);
$handler->execute($PWM);

/* Interactions */
$query = "INSERT IGNORE INTO  `T_Interactions` (`IID`, `PWM`, `Domain_EnsPID`, `Interaction_EnsPID`, `Dataset_ID`)
		  VALUES " . questionMarks(5 * ($columnLength - $pointer), 5);
$handler = $dbh->prepare($query);
$handler->execute($interactions);

/* Mutation */
$query = "INSERT IGNORE INTO  `T_Mutations` (`Mutation_ID` ,`Mut_Description` ,`Tumour_Site`, `Mutation_Source_ID`, `Source`, `EnsGID`)
		  VALUES " . questionMarks(6 * ($columnLength - $pointer), 6);
$handler = $dbh->prepare($query);
$handler->execute($mutations);

/* Interactions_MT */
$query = "INSERT IGNORE INTO  `T_Interactions_MT` (`IID`, `Mutation_ID`, `WT`, `MT`, `WTscore`, `MTscore`, `DeltaScore`, `LOG2`, `Eval`)
		  VALUES " . questionMarks(9 * ($columnLength - $pointer), 9);
$handler = $dbh->prepare($query);
$handler->execute($interactions_MT);

/* Interactions_Eval */
$query = "INSERT IGNORE INTO  `T_Interactions_Eval` (`IID`, `Gene_expression`, `Protein_expression`, `Disorder`,`Surface_accessibility`, `Peptide_conservation`, `Molecular_function`, `Biological_process`, `Localization`, `Sequence_signature`, `Avg`)
		  VALUES " . questionMarks(11 * ($columnLength - $pointer), 11);
$handler = $dbh->prepare($query);
$handler->execute($interactions_Eval);

/* Ensembl */
$query = "INSERT IGNORE INTO  `T_Ensembl` (`EnsPID`, `EnsTID`, `EnsGID`, `Version`, `GeneName`, `Description`, `Sequence`)
		  VALUES " . questionMarks(2 * 7 * ($columnLength - $pointer), 7);
$handler = $dbh->prepare($query);
$handler->execute($ensembl);
