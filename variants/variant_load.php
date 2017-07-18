<?php
include_once('../common.php');

$start = isset($_GET['current_count']) ? $_GET['current_count'] : 0;
$end = isset($_GET['end']) ? $_GET['end'] : 10;

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
  $source = implode("|", $_GET['source']);
}
else
{
  if (isset($_GET['variant_search'])) $source = "";
  else
  {
    $source = ".*";
    $_GET['source'] = '';
  }
}

if (isset($_GET['mut_type']) && $_GET['mut_type'] != '') $type = implode("|", $_GET['mut_type']);
else
{
  if (isset($_GET['variant_search'])) $type = "";
  else
  {
    $type = ".*";
    $_GET['mut_type'] = '';
  }
}

if(isset($_GET['tissue']))
{
  $tissues = $_GET['tissue'];
  function sanitize($s) { return htmlspecialchars($s); }

  $t = array_map('sanitize', $tissues);
  $P_List = "'" . implode("','", $t) . "'";

  $query = "SELECT DISTINCT EnsPID FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND GeneName RLIKE :name AND Mut_Description RLIKE :type
            AND Tumour_Site IN (" . $P_List . ") LIMIT " . $start . ',' . $end;

  $query2 = "SELECT COUNT(EnsPID) FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND GeneName RLIKE :name AND Mut_Description RLIKE :type
            AND Tumour_Site IN (" . $P_List . ")";

  $query3 = "SELECT COUNT(Mutation_ID) FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND GeneName RLIKE :name AND Mut_Description RLIKE :type
            AND Tumour_Site IN (" . $P_List . ")";
}
else
{
  $query = "SELECT DISTINCT EnsPID FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND GeneName RLIKE :name AND Mut_Description RLIKE :type
            LIMIT " . $start . ',' . $end;

  $query2 = "SELECT COUNT(EnsPID) FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND GeneName RLIKE :name AND Mut_Description RLIKE :type";

  $query3 = "SELECT COUNT(Mutation_ID) FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND GeneName RLIKE :name AND Mut_Description RLIKE :type";
}

//Get first twenty protein IDs
$query_params = array(":source" => $source, ":name" => $protein_name, ":type" => $type);
$stmt = $dbh->prepare($query);
$stmt->execute($query_params);
$variant_proteins = array();
while ($row = $stmt->fetch())
  $variant_proteins[] = $row[0];

//If AJAX
if (!isset($_GET['is_ajax']) && !isset($_GET['download']))
{
  //Count total protein #
  $stmt = $dbh->prepare($query2);
  $stmt->execute($query_params);
  $prot_count = $stmt->fetch()[0];
  echo "<script>var prot_count = '" . number_format($prot_count) . "';</script>";

  //Count total variant #
  $stmt = $dbh->prepare($query3);
  $stmt->execute($query_params);
  $mut_count = $stmt->fetch()[0];
  echo "<script>var mut_count = '" . number_format($mut_count) . "';</script>";
}

//If reloading tissue type, update totals
if (isset($_GET['is_tissue']))
{
  //Count total protein #
  $stmt = $dbh->prepare($query2);
  $stmt->execute($query_params);
  $prot_count = $stmt->fetch()[0];
  echo "<tr id='prot_c' data-count=". number_format($prot_count). "></tr>";

  //Count total variant #
  $stmt = $dbh->prepare($query3);
  $stmt->execute($query_params);
  $mut_count = $stmt->fetch()[0];
  echo "<tr id='mut_c' data-count=". number_format($mut_count). "></tr>";
}

//Get all variants of proteins which match the original Tissue, Source, and Mutation Type specifications
$E_List = "'" . implode("','", $variant_proteins) . "'";
$query_params = array(":source" => $source, ":type" => $type);
if(isset($_GET['tissue']))
{
  $query4 = "SELECT EnsPID, Tumour_site, GeneName FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND Mut_Description RLIKE :type AND Tumour_Site IN (" . $P_List . ")
            AND EnsPID IN (" . $E_List . ")";
}
else
{
  $query4 = "SELECT EnsPID, Tumour_Site, GeneName FROM T_Ensembl INNER JOIN T_Mutations
            ON T_Mutations.Peptide_EnsGID = T_Ensembl.EnsGID WHERE Source RLIKE :source
            AND Mut_Description RLIKE :type AND EnsPID IN (" . $E_List . ")";
}

$stmt = $dbh->prepare($query4);
$stmt->execute($query_params);

$variants = array();
$variant_count = array();
$variant_names = array();
$variant_ids = array();

while ($row = $stmt->fetch())
{
  if(array_key_exists($row[0],$variants)) $variant_count[$row[0]] += 1;
  else $variant_count[$row[0]] = 1;

  if(!array_key_exists($row[0],$variants))
  {
    $variants[$row[0]][] = array($row[1]);
    $variant_names[$row[0]] = $row[2];
    $variant_ids[] = $row[0];
  }
  else
  {
    $variants[$row[0]][] = array($row[1]);
  }
}

// Get the interactions
$interactions = array();
$interaction_ids = array();
$P_List = "'" . implode("','", $variant_ids) . "'";
$query = 'SELECT IID, Peptide_EnsPID FROM T_Interactions WHERE Peptide_EnsPID IN(' . $P_List . ')';
$stmt = $dbh->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch())
{
  $interaction_ids[] = $row[0];
  if(!array_key_exists($row[1],$interactions)) $interactions[$row[1]] = array($row[0]);
  else $interactions[$row[1]][] = $row[0];
}

// Get the effects
$effects = array();
$P_List = "'" . implode("','", $interaction_ids) . "'";
$query = 'SELECT IID, Eval FROM T_Interactions_MT WHERE IID IN(' . $P_List . ');';
$stmt = $dbh->prepare($query);
$stmt->execute();

while ($row = $stmt->fetch())
{
  $protein = '';
  foreach($interactions as $prot_name => $int)
    foreach($int as $i)
      if ($i == $row[0]) $protein = $prot_name;

  if(!array_key_exists($protein,$effects)) $effects[$protein] = array($row[1]);
  else
  {
    if(!in_array($row[1],$effects[$protein]))
      $effects[$protein][] = $row[1];
  }
}

foreach ($variants as $name => $data)
{
  // Get interactions
  if (array_key_exists($name, $interactions)) $int_num = count($interactions[$name]);
  else $int_num = '0';

  $tissues = array();
  $tissue_data = array();

  foreach ($data as $d)
  {
    if(!in_array(ucwords(str_replace("_"," ", $d[0])),$tissues))
    {
      $tissues[] = ucwords(str_replace("_"," ", $d[0]));
      $tissue_data[] = $d[0];
    }
  }

  if (array_key_exists($name, $effects)) $E_List = implode(', ',$effects[$name]);
  else $E_List = 'None';

  if (!isset($_GET['tissue'])) $_GET['tissue'] = $tissue_data;

  if (isset($_GET['download']))
  {
    echo implode('+',$tissues) . "\t" . $name . "\t" . $variant_names[$name] . "\t" . count($data) . "\t" . $int_num . "\t" . $E_List . "\n";
  }
  else
  {
?>
    <tr data-protein="<?=$name?>" class="normal">
      <td><?=implode(', ', $tissues)?></td>
      <td class="selectable"><?=$name?></td>
      <td class="selectable"><?=$variant_names[$name]?></td>
      <td class="mut-count"><?=$variant_count[$name]?></td>
      <td><?=$int_num?></td>
      <td><?=$E_List?></td>
    </tr>
<?php
  }
}
?>
