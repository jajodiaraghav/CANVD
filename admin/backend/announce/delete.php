<?php
	session_start();
	include_once('../../../common.php');
	if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin')
	{
		$aid = (int) $_POST['a_id'];
		$query = 'DELETE FROM announcements WHERE id=' . $aid;
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		echo "Success";
	}
	else
	{
		echo "Error: unauthorized.";
	}
