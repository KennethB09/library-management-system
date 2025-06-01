<?php 

$server = "sql301.infinityfree.com";
$dbUsername = "if0_39118372";
$dbPassword = "VSG0WMGwBV";
$dbname = "if0_39118372_lms_db";

$conn = new mysqli($server, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}

?>