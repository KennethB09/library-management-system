<?php

require "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $waitListId = $_POST["id"];

    try {

        $removeWaitListStmt = $conn->prepare("DELETE FROM waitList WHERE id =?");

        if (!$removeWaitListStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $removeWaitListStmt->bind_param(
            "i",
            $waitListId
        );

        $removeWaitListStmt->execute();
        echo "Wait list item removed";

        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
