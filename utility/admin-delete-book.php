<?php

$server = "localhost";
$dbUsername = "lms_admin";
$dbPassword = "admin12345";
$dbname = "lms_db";

$conn = new mysqli($server, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bookId = $_POST["bookId"];
    $availableOn = $_POST["availableOn"];

    try {

        $deleteBookStmt = $conn->prepare("DELETE FROM books WHERE id = ? ");

        if (!$deleteBookStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $deleteBookStmt->bind_param(
            "s",
            $bookId,
        );

        if ($deleteBookStmt->execute()) {

            $deleteBookStmt->close();

            $deleteBookCopiesStmt = $conn->prepare("DELETE FROM books_copy WHERE bookRef = ?");
            $deleteBookCopiesStmt->bind_param(
                "s",
                $bookId,
            );
            $deleteBookCopiesStmt->execute();

            if ($availableOn !== "p") {

                $getPath = $conn->prepare("SELECT location FROM uploads WHERE bookRef = ?");
                $getPath->bind_param("i", $bookId);
                $getPath->execute();
                $result = $getPath->get_result();
                $row = $result->fetch_assoc();
                
                if (unlink($row["location"])) {
                    echo "File deleted successfully";
                }
            }

            echo "Book is Deleted";
        } else {
            throw new Exception("Error executing statement: " . $deleteBookStmt->error);
        }

        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
