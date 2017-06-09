<?php
function questionMarks($matrixLength, $rowLength)
{
	$questionMarksImplode = function ($elem) { return '(' . implode( ',', $elem ) . ')'; };
	$chunk = array_chunk(array_fill(0, $matrixLength, '?'), $rowLength);
	$QMarks = implode( ',', array_map( $questionMarksImplode, $chunk ) );
	return $QMarks;
}

/* Domain */
$query = "INSERT INTO  `t_domain` (`Domain`, `Type`, `DomainStartPos`, `DomainEndPos`, `DomainSequence`)
		  VALUES " . questionMarks(5 * ($columnLength - $pointer), 5);
$handler = $dbh->prepare($query);
$handler->execute($domains);

/* Interaction */
$query = "INSERT INTO  `t_interaction` (`IID`, `Domain_EnsPID`, `Interaction_EnsPID`)
		  VALUES " . questionMarks(3 * ($columnLength - $pointer), 3);
$handler = $dbh->prepare($query);
$handler->execute($interactions);

/* Interaction_MT */
$query = "INSERT INTO  `t_interaction_mt` (`IID`, `WTscore`, `MTscore`, `DeltaScore`, `LOG2`, `WT`, `MT`, `Eval`)
		  VALUES " . questionMarks(8 * ($columnLength - $pointer), 8);
$handler = $dbh->prepare($query);
$handler->execute($interactions_MT);

/* Interaction_Eval */
$query = "INSERT INTO  `t_interactions_eval` (`IID`, `Gene_expression`, `Protein_expression`, `Disorder`,`Surface_accessibility`, `Peptide_conservation`, `Molecular_function`, `Biological_process`, `Localization`, `Sequence_signature`, `Avg`)
		  VALUES " . questionMarks(11 * ($columnLength - $pointer), 11);
$handler = $dbh->prepare($query);
$handler->execute($interactions_Eval);

/* Ensembl */
$query = "INSERT INTO  `t_ensembl` (`EnsTID` ,`GeneName` ,`Version`, `Description`, `Sequence`)
		  VALUES " . questionMarks(2 * 5 * ($columnLength - $pointer), 5);		  
$handler = $dbh->prepare($query);
$handler->execute($ensembl);

/* Mutation */
$query = "INSERT INTO  `t_mutations` (`MUTATION_ID` ,`mut_description` ,`tumour_site`, `Mutation_Source_ID`, `Source`)
		  VALUES " . questionMarks(5 * ($columnLength - $pointer), 5);
$handler = $dbh->prepare($query);
$handler->execute($mutations);

/* PWM */
$query = "INSERT INTO  `t_pwm` (`PWM` ,`Domain` ,`Domain_Interpro_ID`)
		  VALUES " . questionMarks(3 * ($columnLength - $pointer), 3);
$handler = $dbh->prepare($query);
$handler->execute($PWM);

/* Dataset */
$query = "INSERT INTO  `dataset` (`IID` ,`author` ,`publication`)
		  VALUES " . questionMarks(3 * ($columnLength - $pointer), 3);
$handler = $dbh->prepare($query);
$handler->execute($dataset);
