<?php
$variant_id = $_GET['variant_ids'];
$variant_ids = explode(",",$variant_id);
$newfile = "";

foreach ($variant_ids as $var)
{
	$prot = fopen("../proteins/mt/" . $var . ".fasta", "r");
	while ($line = fgets($prot))
	{
		$newfile = $newfile . $line;
	}
	fclose($prot);
}

$file = 'variants.fasta';
header("Content-Disposition: attachment; filename='" . basename($file) . "'");
header("Content-Type: application/force-download");
header("Connection: close");
echo $newfile;
