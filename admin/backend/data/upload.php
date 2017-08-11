<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin')
{
	$type = $_REQUEST['directory'];

	for ($i = 0; $i < sizeof($_FILES["files"]["name"]); $i++)
	{
		if ($_FILES["files"]["error"][$i] > 0)
		{
    		$temp = array('error' => $_FILES["files"]["error"][$i]);
			$response = array("files" => [$temp]);			
			die(json_encode($response));
		}
		else
		{
			$temp = array(
				'name' => $_FILES["files"]["name"][$i],
				'type' => $_FILES["files"]["type"][$i],
				'size' => $_FILES["files"]["size"][$i]
			);
			$response = array("files" => [$temp]);

			$destination = __DIR__ . "/../../data/" . $type . "/" . $_FILES["files"]["name"][$i];

			move_uploaded_file($_FILES["files"]["tmp_name"][$i], $destination);
			die(json_encode($response));
		}
	}
}
else
{
	echo "Error: unauthorized!";
}
