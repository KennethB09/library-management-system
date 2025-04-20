<?php
session_start();
require_once "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bookId = $_POST["bookId"];
    $format = $_POST["format"];

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

            if ($format !== "physical") {

                $getPath = $conn->prepare("SELECT location FROM uploads WHERE bookRef = ?");
                $getPath->bind_param("i", $bookId);
                $getPath->execute();
                $result = $getPath->get_result();
                $row = $result->fetch_assoc();

                if (unlink($row["location"])) {

                    $deleteBookCopiesStmt = $conn->prepare("DELETE FROM books_copy WHERE bookRef = ?");
                    $deleteBookCopiesStmt->bind_param(
                        "s",
                        $bookId,
                    );
                    $deleteBookCopiesStmt->execute();

                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'message' => "File deleted successfully"
                    ];
                }
            }

            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => "Book is Deleted"
            ];
        } else {
            throw new Exception("Error executing statement: " . $deleteBookStmt->error);
        }

        $conn->close();
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => "Error: " . $e->getMessage()
        ];
    }
}
