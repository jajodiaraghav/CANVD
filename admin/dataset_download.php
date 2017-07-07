<?php
include_once('../common.php');
ini_set('memory_limit', '-1');
set_time_limit(0);

$dataset = $_GET['set'];
$query = 'SELECT * FROM T_Domain, T_Ensembl, T_PWM, T_Interactions, T_Interactions_Eval,
		T_Mutations, T_Interactions_MT, T_Dataset
		WHERE T_Dataset.Dataset_ID=:dataset
		AND T_Interactions.Dataset_ID=T_Dataset.Dataset_ID
		AND T_Domain.Domain_EnsPID=T_Interactions.Domain_EnsPID
		AND T_PWM.Domain=T_Domain.Domain
		AND T_Interactions_Eval.IID=T_Interactions.IID
		AND T_Interactions_MT.IID=T_Interactions.IID
		AND T_Mutations.Peptide_EnsPID=T_Interactions.Peptide_EnsPID';
$params = array(":dataset" => $dataset);
$stmt = $dbh->prepare($query);
$stmt->execute($params);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
	$query = '';
	$params = array(":dataset" => $dataset);
	$stmt = $dbh->prepare($query);
	$stmt->execute($params);

	$col[0] = "Domain_EnsPID:".$row['Domain_EnsPID'];
	$col[1] = "Interaction_EnsPID:".$row['Interaction_EnsPID'];
	$col[2] = "EnsTID:".$row['EnsTID'];
	$col[3] = '-';
	$col[4] = "GeneName:".$row['GeneName'];
	$col[5] = '-';
	$col[6] = '-';
	$col[7] = "Author:NA";
	$col[8] = "Publication:NA";
	$col[9] = '-';
	$col[10] = '-';
	$col[11] = '-';
	$col[12] = "Version:".$row['Version'];
	$col[13] = "IID:".$row['IID'];
	$col[14] = "Wtscore:".$row['WTscore'].";Mtscore:".$row['MTscore'].";DeltaScore:".$row['DeltaScore'].";LOG2:".$row['LOG2'].";Gene_expression:".$row['Gene_expression'].";Protein_expression:".$row['Protein_expression'].";Disorder:".$row['Disorder'].";Surface_accessibility:".$row['Surface_accessibility'].";Peptide_conservation:".$row['Peptide_conservation'].";Molecular_function:".$row['Molecular_function'].";Biological_process:".$row['Biological_process'].";Localization:".$row['Localization'].";Sequence_signature:".$row['Sequence_signature'].";Overall_score:".$row['Avg'];
	$col[15] = '-';
	$col[16] = '-';
	$col[17] = '-';
	$col[18] = '-';
	$col[19] = '-';
	$col[20] = '-';
	$col[21] = '-';
	$col[22] = '-';
	$col[23] = '-';
	$col[24] = '-';
	$col[25] = "Domain:".$row['Domain'].";Type:".$row['Type'].";DomainStartPos:".$row['DomainStartPos'].";DomainEndPos:".$row['DomainEndPos'].";DomainSequence:".$row['DomainSequence'].";Domain_InterPro_ID:NA;PWM:".$row['PWM'];
	$col[26] = "WT:".$row['WT'].";MT:".$row['MT'];
	$col[27] = '-';
	$col[28] = '-';
	$col[29] = '-';
	$col[30] = '-';
	$col[31] = '-';
	$col[32] = "EnsGID:".$row['EnsGID'].";Description:".$row['Description'].";Sequence:".$row['Sequence'];
	$col[33] = '-';
	$col[34] = '-';
	$col[35] = "Eval:".$row['Eval'];
	$col[36] = "Mutation_ID:".$row['ID'].";Mut_Description:".$row['mut_description'].";Tumour_Site:".$row['tumour_site'].";Mutation,Source_ID:".$row['Mutation_Source_ID'].";Source:".$row['Source'].";EnsGID:".$row['Mut_EnsGID'];
	$col[37] = '-';
	$col[38] = '-';
	$col[39] = '-';
	$col[40] = '-';
	$col[41] = '-';

	$str[] = implode("	", $col);
}

$s = implode("\n", $str);

$File = 'Publication.psi';
header("Content-Disposition: attachment; filename='" . basename($File) . "'");
header("Content-Type: application/force-download");
header("Connection: close");
echo $s;
