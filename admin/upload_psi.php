<?php
header('Content-type: text/html; charset=utf-8');
session_start();
set_time_limit(0);
include_once('../common.php');

function questionMarks($matrixLength, $rowLength)
{
  $questionMarksImplode = function ($elem) { return '(' . implode( ',', $elem ) . ')'; };
  $chunks = array_chunk(array_fill(0, $matrixLength, '?'), $rowLength);
  $QMarks = implode( ',', array_map( $questionMarksImplode, $chunks ) );
  return $QMarks;
}

ob_implicit_flush(true);

if (!isset($_SESSION['user']) || !isset($_FILES))
{
  echo "Error: No File uploaded!";
  die;
}
if (is_uploaded_file($_FILES['file']['tmp_name']))
{
  if ($_FILES["file"]["error"] > 0)
  {
    echo "Error: " . $_FILES["file"]["error"];
  }
  else
  {
    $name = 'DATA_' . time() . '.psi';
    move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__ . "/upload/" . $name);

    if ($_POST['action'] == "replace")
    {
      $tables = ['T_Domain', 'T_Ensembl', 'T_Interactions', 'T_Interactions_Eval', 'T_Interactions_MT', 'T_Mutations', 'T_PWM', 'T_Dataset'];

      foreach ($tables as $table)
      {
        $command = "TRUNCATE TABLE $table;";
        $dbh->query($command);
      }    
    }

    $fileLoc = __DIR__ . "/upload/" . $name;
    $arrayContent = file($fileLoc);
    $columnLength = count($arrayContent);
    $fileChunk = ceil($columnLength/10000);
    $chunk = array_chunk($arrayContent, 10000);
    $row = array();
?>
<html>
  <head>
    <title>DV-IMPACT :: Admin</title>
    <link rel="shortcut icon" href="/assets/images/canvd.ico">
    <link href="/assets/css/bootstrap.css" rel="stylesheet">    
    <link href="/assets/css/styles.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/admin.css" rel="stylesheet" type="text/css">    
  </head>
  <body>
    <div class="container">
      <div class="alert alert-info">
        <h6>The uploaded file consists of <?=$columnLength?> rows. The file is being inserted as chunks of <?=$fileChunk?> files each with 10000 rows. This may take a while.</h6>
      </div>
      <div class="alert alert-info">
        <h5>Uploading Files...</h5>
<?php
flush();    

    foreach($chunk as $n => $subArrayContent)
    {
      // Table Arrays
      $domains = array();
      $interactions = array();
      $interactions_MT = array();
      $interactions_Eval = array();
      $ensembl = array();
      $PWM = array();
      $dataset = array();
      $mutations = array();
      $pointer = 0;      

      foreach($subArrayContent as $i => $rowString)
      {
        $row = explode("\t", $rowString);

        if($i == 0 && $row[0] === "Unique identifier for interactor A") 
        {
          $pointer = 1;
          continue;
        }

        // Domain Table
        $domainData = explode(";", $row[25]);
        $Domain = end((explode(":", $domainData[0])));
        $Domain_EnsPID = end((explode(":", $row[0])));
        $Domain_EnsTID = end((explode(":", $row[2])));
        $PWM_Val = end((explode(":", $domainData[6])));

        if($Domain != '')
        {
          $domains[] = $Domain; // Domain
          $domains[] = end((explode(":", $domainData[1]))); // Type
          $domains[] = end((explode(":", $domainData[2]))); // DomainStartPos
          $domains[] = end((explode(":", $domainData[3]))); // DomainEndPos
          $domains[] = end((explode(":", $domainData[4]))); // DomainSequence
          $domains[] = end((explode(":", $domainData[5]))); // Domain_Interpro_ID
          $domains[] = $Domain_EnsPID; // Domain_EnsPID
          $domains[] = $Domain_EnsTID; // Domain_EnsTID

          // PWM Table
          $PWM[] = $PWM_Val; // PWM
          $PWM[] = $Domain; // Domain
        }      

        // Dataset Table
        $Author = end((explode(":", $row[7])));
        $Publication = end((explode(":", $row[8])));
        $dataset = array(':author' => $Author, ':publication' => $Publication);
        $query = "SELECT Dataset_ID FROM T_Dataset WHERE Author=:author AND Publication=:publication LIMIT 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute($dataset);
        $Dataset = $stmt->fetch()[0];

        if (count($Dataset) > 0)
        {
          $Dataset_ID = $Dataset;
        }
        else
        {
          $query = "INSERT INTO T_Dataset (`Author`, `Publication`) VALUES (:author, :publication)";
          $stmt = $dbh->prepare($query);
          $stmt->execute($dataset);
          $Dataset_ID = $dbh->lastInsertId();
        }

        // Interactions Table
        $IID = end((explode(":", $row[13])));
        $Peptide_EnsPID = end((explode(":", $row[1])));

        $MutationData = explode(";", $row[36]);
        $ensemblData1 = explode(";", $row[32]);
        $ensemblData2 = explode(";", $row[33]);
        $Mutation_ID = end((explode(":", $MutationData[0])));
        $Peptide_EnsGID = end((explode(":", $ensemblData2[0])));

        if($IID != '')
        {
          $interactions[] = $IID; // IID
          $interactions[] = $PWM_Val; // PWM
          $interactions[] = $Domain_EnsPID; // Domain_EnsPID
          $interactions[] = $Peptide_EnsPID; // Peptide_EnsPID
          $interactions[] = $Dataset_ID; // Dataset_ID

          // Interactions_MT Table
          $Int_Mt_Data_1 = explode(";", $row[14]);
          $Int_Mt_Data_2 = explode(";", $row[26]);

          $interactions_MT[] = $IID; //IID
          $interactions_MT[] = end((explode(":", $MutationData[0]))); // Muatation_ID
          $interactions_MT[] = end((explode(":", $Int_Mt_Data_2[0]))); // WT
          $interactions_MT[] = end((explode(":", $Int_Mt_Data_2[1]))); // MT
          $interactions_MT[] = end((explode(":", $Int_Mt_Data_1[0]))); // WTScore
          $interactions_MT[] = end((explode(":", $Int_Mt_Data_1[1]))); // MTScore
          $interactions_MT[] = end((explode(":", $Int_Mt_Data_1[2]))); // DeltaScore
          $interactions_MT[] = end((explode(":", $Int_Mt_Data_1[3]))); // LOG2
          $interactions_MT[] = end((explode(":", $row[35]))); // Eval

          // Interactions_Eval Table
          $interactions_Eval[] = $IID; // IID
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[4]))); // Gene_expression
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[5]))); // Protein_Expression
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[6]))); // Disorder
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[7]))); // Surface_accessibility
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[8]))); // Peptide_conservation
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[9]))); // Molecular_function
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[10]))); // Biological_process
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[11]))); // Localization
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[12]))); // Sequence_signature
          $interactions_Eval[] = end((explode(":", $Int_Mt_Data_1[13]))); // Avg
        }

        // Mutations Table
        if($Mutation_ID != '')
        {
          $mutations[] = $Mutation_ID; // Mutation_ID
          $mutations[] = end((explode(":", $MutationData[1]))); // Mut_Description
          $mutations[] = end((explode(":", $MutationData[2]))); // Tumour_Site
          $mutations[] = end((explode(":", $MutationData[3]))); // Mutation_Source_ID
          $mutations[] = end((explode(":", $MutationData[4]))); // Source
          $mutations[] = $Peptide_EnsGID; // Peptide_EnsGID
          $mutations[] = $Peptide_EnsPID; // Peptide_EnsPID
        }

        // Ensembl Table (Domain)
        if($Domain_EnsPID != '')
        {
          $ensembl[] = $Domain_EnsPID; // EnsPID
          $ensembl[] = $Domain_EnsTID; // EnsTID
          $ensembl[] = end((explode(":", $ensemblData1[0]))); // EnsGID
          $ensembl[] = end((explode(":", $row[12]))); // Version
          $ensembl[] = end((explode(":", $row[4]))); // GeneName
          $ensembl[] = end((explode(":", $ensemblData1[1]))); // Description
          $ensembl[] = end((explode(":", $ensemblData1[2]))); // Sequence
        }

        // Ensembl Table (Peptide)
        if($Peptide_EnsPID != '')
        {
          $ensembl[] = $Peptide_EnsPID; // EnsPID
          $ensembl[] = end((explode(":", $row[3]))); // EnsTID
          $ensembl[] = $Peptide_EnsGID; // EnsGID
          $ensembl[] = end((explode(":", $row[12]))); // Version
          $ensembl[] = end((explode(":", $row[5]))); // GeneName
          $ensembl[] = end((explode(":", $ensemblData2[1]))); // Description
          $ensembl[] = end((explode(":", $ensemblData2[2]))); // Sequence
        }
      }
      include('includes/data_insertion.php');
      echo '<h5>File ' . ($n + 1) . ' successfully inserted!</h5>';
      flush();
    }
    echo '<h5>All Done! You will be redirected in a moment...</h5>';
    echo '</div></div>';
    flush();
  }  

  echo '<script>window.location.href = "./index.php?submit=PSI-MI"</script>';
  flush();
}
else
{
	echo "Error: Unauthorized!";
}
