<?php

require "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bookId = $_POST["bookId"];
    $userId = $_POST["userId"];

    try {

        // First, SELECT the ID of a book copy
        $selectAvailableCopyStmt = $conn->prepare("SELECT id FROM books_copy WHERE bookRef = ? LIMIT 1");
        $selectAvailableCopyStmt->bind_param("i", $bookId);
        $selectAvailableCopyStmt->execute();
        $result = $selectAvailableCopyStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $availableCopyId = $row['id'];

            // Insert the the copy of the book to the wait list table
            $addToWaitList = $conn->prepare("INSERT INTO waitlist (userId, bookRef) VALUES (?, ?)");
            if (!$addToWaitList) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $addToWaitList->bind_param(
                "ss",
                $userId,
                $availableCopyId
            );
            $addToWaitList->execute();

            echo "Added to wait list!";
        } else {
            throw new Exception("There's no book available");
        }

        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}