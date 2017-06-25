<?php
include_once('../common.php');

$counts = array();

$query = "SELECT COUNT(*) FROM tissue_table_browser;";
$stmt = $dbh->prepare($query);
$stmt->execute();
$counts[] = $stmt->fetch()[0];

$query = "SELECT COUNT(PWM) FROM T_PWM;";
$stmt = $dbh->prepare($query);
$stmt->execute();
$counts[] = $stmt->fetch()[0];

$query = "SELECT COUNT(Domain) FROM T_Domain;";
$stmt = $dbh->prepare($query);
$stmt->execute();
$counts[] = $stmt->fetch()[0];

print json_encode($counts);
?>