<?php
session_start();
include_once('../common.php');

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {

	for ($i = 0; $i < sizeof($_FILES["files"]["name"]); $i++) {
		if ($_FILES["files"]["error"][$i] > 0) {
    		echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
		} else {
			$temp = array(
				'deleteType' => "DELETE",
				'deleteUrl' => '/admin/data/' . $_FILES["files"]["name"][$i],
				'name' => $_FILES["files"]["name"][$i],
				'type' => $_FILES["files"]["type"][$i],
				'size' => $_FILES["files"]["size"][$i]
			);
			$deleteLink = array(
				"files" => [$temp]
			);

			$type = "" // PWM or PNG or TXT ?

			move_uploaded_file($_FILES["files"]["tmp_name"][$i], "/admin/data/" . $type . $_FILES[$i]["files"]["name"][$i]);
			die(json_encode($deleteLink));
		}
	}

} else {
	echo "Error: unauthorized!";
}