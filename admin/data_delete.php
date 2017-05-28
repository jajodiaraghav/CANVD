<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
	if(isset($_POST["url"])) {
		$deleteLink = $_POST["url"];

		if (is_file($deleteLink)) unlink($deleteLink);
	}
}
