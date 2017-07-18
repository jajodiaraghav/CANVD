<?php

// Select Domain EnsPID from the GeneName
if(isset($_GET['genename']) && $_GET['genename'] != '') {
	$gene_name = $_GET['genename'];
} else {
	echo "$('#cy').append('<h2> Please enter a valid domain name.</h2>');";
	echo '</script></body></html>';
	exit;
}

$all_data = array();
$domains = array();
$domain_names = array();
$domain_info = array();

$query = 'SELECT * FROM T_Domain WHERE Domain=:gene OR Domain_EnsPID=:gene_pid';
$gene = $_GET['genename'];
$query_params = array(':gene' => $gene, ':gene_pid' => $gene);
$stmt = $dbh->prepare($query);
$stmt->execute($query_params);

while ($row = $stmt->fetch())
{
	$domains[] = $row["Domain_EnsPID"];
	$domain_names[$row['Domain_EnsPID']] = $row['Domain'];

	// Get T_Ensembl description
	$query2 = 'SELECT Description FROM T_Ensembl WHERE EnsPID=:ens_pid';
	$query_params2 = array(':ens_pid' => $row["Domain_EnsPID"]);
	$stmt2 = $dbh->prepare($query2);
	$stmt2->execute($query_params2);
	while ($row2 = $stmt2->fetch())
	{
		$desc = $row2['Description'];
	}
	$domain_info[] = [
						"EnsPID" => $row["Domain_EnsPID"],
						"DomainName" => $row['Domain'],
						"Type" => $row["Type"],
						"Description" => $desc
					];
}

// For each domain, get Interaction data
$i = 0;
foreach ($domains as $domain) {

	$query = 'SELECT PWM FROM T_PWM WHERE Domain=:ens_pid';
	$query_params = array(':ens_pid' => $domain_names[$domain]);
	$stmt = $dbh->prepare($query);
	$stmt->execute($query_params);
	while ($row = $stmt->fetch())
	{
		$pwm = $row['PWM'];
	}

	$query = 'SELECT * FROM T_Interactions WHERE Domain_EnsPID=:ens_pid';
	$query_params = array(':ens_pid' => $domain);
	$stmt = $dbh->prepare($query);
	$stmt->execute($query_params);

	$interactions = array();
	$interaction_raw_data = array();
	$iid_to_enspid = array();
	$partners = array();
	$mut_interaction_types = array();

	while ($row = $stmt->fetch())
	{
		$interactions[] = $row['IID'];
		$partners[] = $row['Peptide_EnsPID'];
		$iid_to_enspid[$row['IID']] = $row['Peptide_EnsPID'];
		$mut_interaction_types[$row['Peptide_EnsPID']] = [];
		$interaction_raw_data[] = [
									'domain' => $row['Domain_EnsPID'],
									'interaction' => $row['Peptide_EnsPID']
								];
	}

	// Get all T_Interaction_Eval data for all interactions
	$P_List = "'" . implode("','", $interactions) . "'";
	$query = 'SELECT * FROM T_Interactions_Eval WHERE IID IN(' . $P_List . ')';
	$stmt = $dbh->prepare($query);
	$stmt->execute();

	$interaction_eval = [];
	while ($row = $stmt->fetch())
	{
		$interaction_eval[$iid_to_enspid[$row['IID']]] = $row;
	}

	// Get all Interaction_MT for all Interactions
	$P_List = "'" . implode("','", $interactions) . "'";

	$query = 'SELECT * FROM T_Interactions_MT WHERE IID IN(' . $P_List . ')';
	$stmt = $dbh->prepare($query);
	$stmt->execute();

	$mut_interactions = array();
	$mut_impacts = array();
	while ($row = $stmt->fetch())
	{
		$mut_interactions[$row['IID']][] = [
													$row['WT'],
													$row['MT'],
													$row['WTscore'],
													$row['MTscore'],
													$row['LOG2'],
													$row['Eval'],
													$row['DeltaScore']
												];
		$mut_interaction_types[$row['IID']][] = $row['Eval'];
	}

	// Assign each interaction as "loss, "gain", "neutral", or "both"
	$mut_interaction_labels = array();
	foreach ($mut_interaction_types as $key => $value) {
		if (in_array('loss of function', $value) && in_array('gain of function', $value)) {
			$mut_interaction_labels[$key] = 'both';
		} elseif (in_array('loss of function', $value)) {
			$mut_interaction_labels[$key] = 'loss';
		} elseif (in_array('gain of function', $value)) {
			$mut_interaction_labels[$key] = 'gain';
		} else {
			$mut_interaction_labels[$key] = 'neutral';
		}
	}

	// Get all mutations, mark those w/ impact. save tissue types and AA syntax.
	$P_List = "'" . implode("','", $partners) . "'";

	if (isset($_GET['main_search'])) {
		if (isset($_GET['source'])) {
		    $source = implode("|", $_GET['source']);
		} else {
			$source = "";
		}
	} else{
		$source = ".*";
		$_GET['source'] = '';
	}


	if (isset($_GET['main_search'])) {
		if (isset($_GET['mut_type'])) {
	    	$type = implode("|", $_GET['mut_type']);
		} else {
			$type = "";
		}
	} else {
	    $type = ".*";
	    $_GET['mut_type'] = '';
	}

	$query = 'SELECT * FROM T_Mutations WHERE Source RLIKE :source
			AND Mut_Description RLIKE :type AND Peptide_EnsPID IN(' . $P_List . ')';
	$query_params = array(':source' => $source, ':type' => $type);
	$stmt = $dbh->prepare($query);
	$stmt->execute($query_params);
	$mutation_information = array();
	while ($row = $stmt->fetch())
	{
		if (array_key_exists($row['Peptide_EnsPID'], $mut_impacts))
		{
			if (array_key_exists($row['Mutation_ID'], $mut_impacts[$row['Peptide_EnsPID']]))
			{
				if ($mut_impacts[$row['Peptide_EnsPID']][$row['Mutation_ID']] == "loss of function") {
					$impact = -1;
				} else {
					$impact = 1;
				}
			} else {
				$impact = 0;
			}
		} else {
			$impact = 0;
		}

		// Add mutation info to array
		$mutation_information[] = [
								'Syntax' => $row['Mutation_ID'],
								'Tissue' => $row['Tumour_Site'],
								'Source' => $row['Source'],
								'EnsPID' => $row['Peptide_EnsPID'],
								'Impact' => $impact
							];
	}

	// Get all interaction partner names
	$gene_info = array();
	$P_List = "'" . implode("','", $partners) . "'";
	$query = 'SELECT GeneName, Description, EnsPID FROM T_Ensembl WHERE EnsPID IN(' . $P_List . ');';
	$stmt = $dbh->prepare($query);
	$stmt->execute();

	while ($row = $stmt->fetch()) {
		$gene_info[$row['EnsPID']] = [
									'GeneName' => $row['GeneName'],
									'Description' => $row['Description'],
									'EnsPID' => $row['EnsPID']
									];
	}

	// Our info
	$all_data[$domain] = [
							"domain_info" => $domain_info[$i],
							"gene_info" => $gene_info,
							"mut_int" => $mut_interaction_labels,
							"muts" => $mutation_information,
							"mut_effects" => $mut_interactions,
							"raw_interactions" => $interaction_raw_data,
							"interaction_eval" => $interaction_eval,
							"pwm" => $pwm,
							"int_start" => 0,
							"int_limit" => 100
						];
	$i = $i + 1;
}
?>

var networkData = <?php echo json_encode($all_data); ?>;