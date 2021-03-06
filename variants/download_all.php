<?php
include_once('../common.php');

if(!isset($_GET['current_count'])) $start = 0;
else $start = $_GET['current_count'];

if (isset($_GET['end'])) $end = $_GET['end'];
else $end = '10';

$tissues = '';
$protein_name = '';
$source = '';
$type = '';

if (!isset($_GET['prot']) || $_GET['prot'] == '')
{
    $protein_name = ".*";
    $_GET['prot'] = '';
}
else
{
    $protein_name = $_GET['prot'];
    $protein_name = str_replace(',', '$|^', $protein_name);
    $protein_name = "^" . $protein_name . "$";
}

if (isset($_GET['source']) && $_GET['source'] != '')
{
    $source = str_replace('["', '', $_GET['source']);
    $source = str_replace('"]', '', $source);
    $source = str_replace('","', '|', $source);
}
else
{
    if (isset($_GET['variant_search'])) $source = ".*";
    else
    {
        $source = ".*";
        $_GET['source'] = '';
    }
}

if (isset($_GET['type']) && $_GET['type'] != '')
{
    $type = str_replace('["', '', $_GET['type']);
    $type = str_replace('"]', '', $type);
    $type = str_replace('","', '|', $type);
}
else
{
    if (isset($_GET['variant_search'])) $type = ".*";
    else 
    {
        $type = ".*";
        $_GET['type'] = '';
    }
}

if(isset($_GET['tissue']))
{
    $tissues = $_GET['tissue'];
    function sanitize($s) { return htmlspecialchars($s); }

    $tissue_array = explode(',', $tissues);
    $t = array_map('sanitize', $tissue_array);
    $P_List = "'" . implode("','", $t) . "'";

    if (isset($_GET['variant_search']) && $_GET['variant_search'] == '"true"')
    {
        $query = "SELECT GeneName, Sequence FROM T_Ensembl LEFT JOIN T_Mutations
                  ON T_Ensembl.EnsPID=T_Mutations.Peptide_EnsPID WHERE T_Mutations.Source RLIKE :source
                  AND T_Ensembl.GeneName RLIKE :name AND T_Mutations.Mut_Description RLIKE :type
                  AND T_Mutations.Tumour_Site IN (" . $P_List . ")";
        $query_params = array(":source" => $source, ":name" => $protein_name, ":type" => $type);
        $stmt = $dbh->prepare($query);
        $stmt->execute($query_params);
    }
    else
    {
        $query = "SELECT GeneName, Sequence FROM T_Ensembl LEFT JOIN T_Mutations
                  ON T_Ensembl.EnsPID=T_Mutations.Peptide_EnsPID WHERE T_Mutations.Tumour_Site IN (" . $P_List . ")";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
    }
}
else
{
    if (isset($_GET['variant_search']))
    {
        $query = "SELECT GeneName, Sequence FROM T_Ensembl LEFT JOIN T_Mutations
              ON T_Ensembl.EnsPID=T_Mutations.Peptide_EnsPID WHERE T_Mutations.Source RLIKE :source
              AND T_Ensembl.GeneName RLIKE :name AND T_Mutations.Mut_Description RLIKE :type";

        $query_params = array(":source" => $source, ":name" => $protein_name, ":type" => $type);
        $stmt = $dbh->prepare($query);
        $stmt->execute($query_params);
    }
}

$variant_proteins = array();
$newfile = "";
while ($row = $stmt->fetch())
{
    $newfile = $newfile . '>' . $row['GeneName'] . "\n";
    $newfile = $newfile . $row['Sequence'] . "\n";
}

$File = 'variants.fasta';
header("Content-Disposition: attachment; filename=\"" . basename($File) . "\"");
header("Content-Type: application/force-download");
header("Connection: close");
echo $newfile;
