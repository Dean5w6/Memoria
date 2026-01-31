<?php


define('BASE_URL', 'http://localhost/memoria/');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "memoria_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>