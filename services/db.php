<?php
include __DIR__."/envs.php";

$servername = $env_variables["DB_HOST"] ?? 'localhost';
$username = $env_variables["DB_USER"] ?? 'username';
$password = $env_variables["DB_PASS"] ?? 'password';
$dbname = $env_variables["DB_NAME"] ?? 'database';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
