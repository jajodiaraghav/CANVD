<?php
session_start();

$file = 'interactions.txt';
header("Content-Disposition: attachment; filename='" . basename($file) . "'");
header("Content-Type: application/force-download");
header("Connection: close");
echo $_SESSION['download_data'];
unset($_SESSION['download_data']);
