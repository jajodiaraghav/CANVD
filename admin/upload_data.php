<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {

	$type = $_REQUEST['directory'];

	for ($i = 0; $i < sizeof($_FILES["files"]["name"]); $i++) {
		if ($_FILES["files"]["error"][$i] > 0) {
    		echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
		} else {
			$temp = array(
				'deleteType' => "DELETE",
				'deleteUrl' => '/admin/data/' . $type . '/' . $_FILES["files"]["name"][$i],
				'name' => $_FILES["files"]["name"][$i],
				'type' => $_FILES["files"]["type"][$i],
				'size' => $_FILES["files"]["size"][$i]
			);
			$deleteLink = array(
				"files" => [$temp]
			);			

			move_uploaded_file($_FILES["files"]["tmp_name"][$i], __DIR__ . "/data/" . $type . "/" . $_FILES["files"]["name"][$i]);
			die(json_encode($deleteLink));
		}
	}

} else {
	echo "Error: unauthorized!";
}