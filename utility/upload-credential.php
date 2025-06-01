<?php

require "./dp-connection.php";

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentId = $_COOKIE["student"];
    $credential = $_POST["credential"];

    try {

        $insertCredential = $conn->prepare("UPDATE users SET credential = ? WHERE studentNumber = ?");

        if (!$insertCredential) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $insertCredential->bind_param(
            "ss",
            $credential,
            $studentId
        );

        $insertCredential->execute();

        $insertCredential->close();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}