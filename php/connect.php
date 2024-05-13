<?php
@ini_set('zlib.output_compression', 1);
ob_implicit_flush(true);

require "class.php";

$hostname = 'dbsolonescano.ctckaoqkklx3.ap-southeast-2.rds.amazonaws.com';
$username = 'moltsimon';
$password = 'MoltSimon12345';
$database = 'dbDittobase';

$db = new Database($hostname, $username, $password, $database);

session_start();
$currentUser = isset($_SESSION['trainerID']) ? $_SESSION['trainerID'] : null;

$pokedexIndex = 0;

?>