<?php
	session_start();
	include_once('../common.php');

	if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
		$id = $_POST['id'];
		$author = $_POST['author'];
		$publication = $_POST['pub'];
		$description = $_POST['descr'];
		$title = $_POST['title'];
		
		$query = 'UPDATE T_Dataset SET Author=?, Publication=?, Description=?, Title=? WHERE Dataset_ID=?';
		$params = array($author, $publication, $description, $title, $id);
		$stmt = $dbh->prepare($query);
		$stmt->execute($params);
		header("Location: index.php?submit=Dataset");
	} else {
		echo "Error: unauthorized.";
	}
?>