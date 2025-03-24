<?php

require "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $borrowId = $_POST["borrowId"];
    $borrower = $_POST["borrower"];
    $bookRef = $_POST["bookRef"];

    try {

        $updateCopyStmt = $conn->prepare("UPDATE books_copy SET status = 'available' WHERE id = ?");
        $updateCopyStmt->bind_param("i", $bookRef);
        
        if ($updateCopyStmt->execute()) {

            $borrowStmt = $conn->prepare("DELETE FROM borrowed_books WHERE id = ? AND borrower = ?");
            $borrowStmt->bind_param("ii", $borrowId, $borrower);
            $borrowStmt->execute();
    
            echo "Book returned successfully";

        } else {
            throw new Exception("Failed to update book status");
        }

        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>