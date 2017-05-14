<?php
	session_start();
	include_once('../common.php');
	if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
		$a_id = (int) $_POST['a_id'];
		$query = 'DELETE FROM announcements WHERE id=' .$a_id.';';
		$query_params = array();
		$stmt = $dbh->prepare($query);
		$stmt->execute($query_params);
		echo "Success";
	} else {
		echo "Error: unauthorized."
	}
?>