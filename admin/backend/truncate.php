<?php
session_start();
include_once('../../common.php');

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin')
{

	$tables = ['T_Domain', 'T_Ensembl', 'T_Interactions', 'T_Interactions_Eval', 'T_Interactions_MT', 'T_Mutations', 'T_PWM', 'T_Dataset', 'tissue_table_browser'];

	foreach ($tables as $table)
	{
		$command = "TRUNCATE TABLE $table;";
		$dbh->query($command);
	}

	header("Location: ../index.php?message=success");

}
else
{
	echo "Error: unauthorized!";
}
