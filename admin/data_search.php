<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
	if (isset($_POST)) {

		$dir = __DIR__ . '/data/'.$_POST['type'];
		if (is_dir($dir)) {

			if ($dh = opendir($dir)) {

				while (($file = readdir($dh)) !== false) {

					if (!is_dir($file) && strpos($file, $_POST['str']) !== false) {
						echo "<tr>";
						echo "<td>".$file."</td>";
						echo "<td><button class='btn btn-xs btn-danger'>Delete</button></td>";
						echo "</tr>";
					}
				}
				closedir($dh);
			}
		}
	}
}
