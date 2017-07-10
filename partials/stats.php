<?php
include_once('common.php');

/* Generates sitewide statistics for CANVD */

//Count # of Domains
$query = 'SELECT COUNT(Domain) FROM T_Domain;';
$stmt = $dbh->prepare($query);
$stmt->execute();
$domain_count = $stmt->fetch()[0];

//Count # of Proteins
$query = 'SELECT COUNT(EnsPID) FROM T_Ensembl;';
$stmt = $dbh->prepare($query);
$stmt->execute();
$protein_count = $stmt->fetch()[0];

//Count # of Interactions
$query = 'SELECT COUNT(IID) FROM T_Interactions;';
$stmt = $dbh->prepare($query);
$stmt->execute();
$interaction_count = $stmt->fetch()[0];

//Count # of Mutations
$query = 'SELECT COUNT(Mutation_ID) FROM T_Mutations;';
$stmt = $dbh->prepare($query);
$stmt->execute();
$mutation_count = $stmt->fetch()[0];

//Count # of Rewiring Effects
$query = 'SELECT COUNT(IID) FROM T_Interactions_MT;';
$stmt = $dbh->prepare($query);
$stmt->execute();
$rewire_count = $stmt->fetch()[0];

//Count # of PWMs
$query = 'SELECT COUNT(PWM) FROM T_PWM;';
$stmt = $dbh->prepare($query);
$stmt->execute();
$pwm_count = $stmt->fetch()[0];	

?>