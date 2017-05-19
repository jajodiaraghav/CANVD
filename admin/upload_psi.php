<?php
session_start();
include_once('../common.php');

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {

  if ($_FILES["file"]["error"] > 0) {

    echo "Error: " . $_FILES["file"]["error"] . "<br>";

  } else {
  die('File Uploaded successfully');
  move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/admin/upload/" . $_FILES["file"]["name"]);

  if ($_POST['action'] == "replace") {

  	exec("mysql -u root -e \"use dvimpact;truncate table T_psi_mi_tab\";");

  }
  
  $query = "use dvimpact;";
  $query = $query . "LOAD DATA LOCAL INFILE '". "/var/www/admin/upload/" . $_FILES["file"]["name"];
  $query = $query . "' INTO TABLE " . $_POST['table-name'] . " FIELDS TERMINATED BY '\\t' IGNORE 1 LINES";

  exec("mysql -u root -e \"". $query . "\"; ");

  }

  header('Location: index.php?submit=PSI-MI');

} else {
	echo "Error: unauthorized!";
}
