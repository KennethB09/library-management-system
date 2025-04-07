<?php

require "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $requestId = $_POST["requestId"];
    $bookRef = $_POST["bookRef"];
    $dueDate = $_POST["dueDate"];
    $borrower = $_POST["borrower"];
    $borrowedOn = date("Y/m/d");
    $format = $_POST["format"];

    try {

        if ($format === "physical") {
            // First, SELECT the ID of an available copy before updating it
            $selectAvailableCopyStmt = $conn->prepare("SELECT id FROM books_copy WHERE bookRef = ? AND status = 'available' LIMIT 1");
            $selectAvailableCopyStmt->bind_param("i", $bookRef);
            $selectAvailableCopyStmt->execute();
            $result = $selectAvailableCopyStmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $availableCopyId = $row['id'];

                // Now update the copy's status
                $updateCopyStmt = $conn->prepare("UPDATE books_copy SET status = 'borrowed' WHERE id = ?");
                $updateCopyStmt->bind_param("i", $availableCopyId);
                $updateCopyStmt->execute();

                // Continue with your borrowing process using $availableCopyId
                $approveBookStmt = $conn->prepare("INSERT INTO borrowed_books (bookRef, borrowedOn, dueDate, borrower) VALUES (?, ?, ?, ?)");
                if (!$approveBookStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $approveBookStmt->bind_param(
                    "ssss",
                    $availableCopyId,
                    $borrowedOn,
                    $dueDate,
                    $borrower
                );
                $approveBookStmt->execute();

                // Delete request from request book table

                $deleteRequestStmt = $conn->prepare("DELETE FROM request_books WHERE id =?");
                if (!$deleteRequestStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $deleteRequestStmt->bind_param(
                    "i",
                    $requestId
                );
                $deleteRequestStmt->execute();

                echo "Request Approved successfully!";
            } else {
                throw new Exception("No available copies found for this book");
            }
        } else {
            // First, SELECT the ID of an available copy before updating it
            $selectAvailableCopyStmt = $conn->prepare("SELECT id FROM books_copy WHERE bookRef = ? AND format = 'digital' LIMIT 1");
            $selectAvailableCopyStmt->bind_param("i", $bookRef);
            $selectAvailableCopyStmt->execute();
            $result = $selectAvailableCopyStmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $availableCopyId = $row['id'];

                // Continue with your borrowing process using $availableCopyId
                $approveBookStmt = $conn->prepare("INSERT INTO borrowed_books (bookRef, borrowedOn, dueDate, borrower) VALUES (?, ?, ?, ?)");
                if (!$approveBookStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $approveBookStmt->bind_param(
                    "ssss",
                    $availableCopyId,
                    $borrowedOn,
                    $dueDate,
                    $borrower
                );
                $approveBookStmt->execute();

                // Delete request from request book table

                $deleteRequestStmt = $conn->prepare("DELETE FROM request_books WHERE id =?");
                if (!$deleteRequestStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $deleteRequestStmt->bind_param(
                    "i",
                    $requestId
                );
                $deleteRequestStmt->execute();

                echo "Request Approved successfully!";
            } else {
                throw new Exception("No available copies found for this book");
            }
        }


        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
