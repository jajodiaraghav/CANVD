<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
	if (isset($_GET['dir'])) {
		
		if ($_GET['dir'] === 'png') {

			$files = glob('./../../data/IMG/*');

			foreach($files as $file)
			{
				if(is_file($file) && $file != './../../data/IMG/index.php') unlink($file);
			}

		} elseif ($_GET['dir'] === 'txt') {

			$files = glob('./../../data/TXT/*');
			foreach($files as $file)
			{
				if(is_file($file) && $file != './../../data/TXT/index.php') unlink($file);
			}

		} elseif ($_GET['dir'] === 'pwm') {

			$files = glob('./../../data/PWM/*');
			foreach($files as $file)
			{
				if(is_file($file) && $file != './../../data/PWM/index.php') unlink($file);
			}

		}
		header("Location: /admin/index.php?message=success");
	}
} else {
	echo "Error: unauthorized!";
}