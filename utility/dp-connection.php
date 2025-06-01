<?php 

$server = "localhost";
$dbUsername = "lms_admin";
$dbPassword = "admin12345";
$dbname = "lms_db";

$conn = new mysqli($server, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}

?>