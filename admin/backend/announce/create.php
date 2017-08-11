<?php
session_start();
include_once('../../../common.php');
date_default_timezone_set('America/New_York');

if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin')
{
	$title =  $_GET['title'];
	$body =  $_GET['body'];
	$date = date("F j, Y, g:i a");
	$query = 'INSERT INTO announcements (`date`,title,body) VALUES (:date,:title,:body)';
	$params = array(':title' => $title, ':body' => $body,':date' => $date);
	$stmt = $dbh->prepare($query);
	$stmt->execute($params);
	header('Location: /admin');
}
else
{
	echo "Error: Unauthorized.";
}
