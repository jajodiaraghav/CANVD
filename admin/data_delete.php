<?php
if(isset($_POST["url"])) {
	$deleteLink = '/admin/data/' . $_POST["url"];

	if (is_file($deleteLink)) {
	    unlink($deleteLink);
	}
}
