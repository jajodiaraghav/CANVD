<?php
	session_start();
	include_once('../../../common.php');

	if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin')
	{
		$switch = $_POST['switchV'];
		$value = (int) $_POST['value'];
		$aid = (int) $_POST['a_id'];

		$query = 'UPDATE announcements SET `' . $switch . '` = ' . $value . ' WHERE id = ' . $aid;
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		echo "Success";
	}
	else
	{
		echo "Error: unauthorized.";
	}
