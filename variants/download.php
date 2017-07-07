<?php
echo "Tissue(s)\tProtein ID\tProtein Name\tVariants\tInteractions\tEffects\n";

$tissues = $_GET['tissue'];
$_GET['tissue'] = explode(",", $tissues);
$_GET['type'] = json_decode($_GET['type']);
$_GET['source'] = json_decode($_GET['source']);
$_GET['download'] = "true";

include('./variant_load.php');

$file = 'variant-download.csv';
header("Content-Disposition: attachment; filename='" . basename($file) . "'");
header("Content-Type: application/force-download");
header("Connection: close");
