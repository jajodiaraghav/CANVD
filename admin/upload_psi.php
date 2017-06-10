<?php
session_start();
include_once('../common.php');

if (isset($_SESSION['user']) && is_uploaded_file($_FILES['file']['tmp_name']) && $_SESSION['user'] == 'admin') {

  if ($_FILES["file"]["error"] > 0) {

    echo "Error: " . $_FILES["file"]["error"] . "<br>";

  } else {

    move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__ . "/upload/" . $_FILES["file"]["name"]);

    if ($_POST['action'] == "replace") {

      $tables = ['T_Domain', 'T_Ensembl', 'T_Interaction', 'T_Interactions_Eval', 'T_Interaction_MT', 'T_Mutations', 'T_PWM', 'dataset'];

      foreach ($tables as $table) {
        $command = "TRUNCATE TABLE $table;";
        $dbh->query($command);
      }    
    }

    $fileLoc = __DIR__ . "/upload/" . $_FILES["file"]["name"];
    $arrayContent = file($fileLoc);
    $columnLength = count($arrayContent);
    $returnArray = array();
    $domains = array();
    $interactions = array();
    $interactions_MT = array();
    $interactions_Eval = array();
    $ensembl = array();
    $PWM = array();
    $dataset = array();
    $mutations = array();
    $count_1 = $count_2 = $count_3 = $count_4 = $count_5 = $count_6 = $count_7 = $count_8 = 0;

    foreach($arrayContent as $i => $row)
    {
      $returnArray[$i] = explode("\t", $row);

      if($i == 0 && $returnArray[$i][0] === "Unique identifier for interactor A") continue;
      $returnArray[$i] = str_replace("uniprotkb:", "", $returnArray[$i]);

      $domains[$count_1++] = $returnArray[$i][25];
      $domains[$count_1++] = $returnArray[$i][25];
      $domains[$count_1++] = $returnArray[$i][25];
      $domains[$count_1++] = $returnArray[$i][25];
      $domains[$count_1++] = $returnArray[$i][25];

      $interactions[$count_2++] = $returnArray[$i][13];
      $interactions[$count_2++] = $returnArray[$i][0];
      $interactions[$count_2++] = $returnArray[$i][1];      

      $interactions_MT[$count_3++] = $returnArray[$i][13];
      $interactions_MT[$count_3++] = $returnArray[$i][14];
      $interactions_MT[$count_3++] = $returnArray[$i][14];
      $interactions_MT[$count_3++] = $returnArray[$i][14];
      $interactions_MT[$count_3++] = $returnArray[$i][14];
      $interactions_MT[$count_3++] = $returnArray[$i][26];
      $interactions_MT[$count_3++] = $returnArray[$i][26];
      $interactions_MT[$count_3++] = $returnArray[$i][35];

      $interactions_Eval[$count_4++] = $returnArray[$i][13];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];
      $interactions_Eval[$count_4++] = $returnArray[$i][14];

      $ensembl[$count_5++] = $returnArray[$i][2];
      $ensembl[$count_5++] = $returnArray[$i][4];
      $ensembl[$count_5++] = $returnArray[$i][12];
      $ensembl[$count_5++] = $returnArray[$i][33];
      $ensembl[$count_5++] = $returnArray[$i][33];
      $ensembl[$count_5++] = $returnArray[$i][3];
      $ensembl[$count_5++] = $returnArray[$i][5];
      $ensembl[$count_5++] = $returnArray[$i][12];
      $ensembl[$count_5++] = $returnArray[$i][33];
      $ensembl[$count_5++] = $returnArray[$i][33];

      $mutations[$count_6++] = $returnArray[$i][36];
      $mutations[$count_6++] = $returnArray[$i][36];
      $mutations[$count_6++] = $returnArray[$i][36];
      $mutations[$count_6++] = $returnArray[$i][36];
      $mutations[$count_6++] = $returnArray[$i][36];

      $PWM[$count_7++] = $returnArray[$i][25];
      $PWM[$count_7++] = $returnArray[$i][25];
      $PWM[$count_7++] = $returnArray[$i][25];

      $dataset[$count_8++] = $returnArray[$i][13];
      $dataset[$count_8++] = $returnArray[$i][7];
      $dataset[$count_8++] = $returnArray[$i][8];
    }

    $pointer = $returnArray[0][0] === "Unique identifier for interactor A" ? 1 : 0;
    include_once('includes/data_insertion.php');
  }

  header('Location: index.php?submit=PSI-MI');

} else {
	echo "Error: Unauthorized!";
}
