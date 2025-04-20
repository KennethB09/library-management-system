<?php
session_start();
require "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $requestId = $_POST["requestId"];

    try {

        $deleteRequestStmt = $conn->prepare("DELETE FROM request_books WHERE id =?");

        if (!$deleteRequestStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $deleteRequestStmt->bind_param(
            "i",
            $requestId
        );

        $deleteRequestStmt->execute();
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => "Request declined"
        ];

        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
