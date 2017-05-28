<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
	if (isset($_POST['dir'])) {
		
		if ($_POST['dir'] === 'png') {

			$files = glob('/admin/data/IMG/*');
			foreach($files as $file)
			{
				if(is_file($file)) unlink($file);
			}

		} elseif ($_POST['dir'] === 'txt') {

			$files = glob('/admin/data/TXT/*');
			foreach($files as $file)
			{
				if(is_file($file)) unlink($file);
			}

		} elseif ($_POST['dir'] === 'pwm') {

			$files = glob('/admin/data/PWM/*');
			foreach($files as $file)
			{
				if(is_file($file)) unlink($file);
			}

		}
		header("Location: index.php?message=success");
	}
} else {
	echo "Error: unauthorized!";
}