<?php
	session_start();
	include_once('../common.php');

	if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
		$switch = $_POST['switchV'];
		$value = (int) $_POST['value'];
		$a_id = (int) $_POST['a_id'];

		if ($switch == 'show_homepage') {
			$switch = 'show_homepage';
		} else {
			$switch = 'show';
		}

		$query = 'UPDATE announcements SET  `' . $switch . '` ='.$value.' WHERE  id ='.$a_id.';';
		$query_params = array();
		$stmt = $dbh->prepare($query);
		$stmt->execute($query_params);
		echo "Success";
	} else {
		echo "Error: unauthorized.";
	}
?>