<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
	if(isset($_POST["list"])) {
		foreach ($_POST["list"] as $key => $deleteLink) {
			$deleteLink = __DIR__ . '/../../data/' . $deleteLink;
			if (is_file($deleteLink)) unlink($deleteLink);
		}
	}
}
