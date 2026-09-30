<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "3h"; // MUST MATCH THE DATABASE NAME IN PHPMYADMIN

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>