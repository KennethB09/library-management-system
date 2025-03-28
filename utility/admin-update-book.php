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

    $title = $_POST["title"];
    $type = $_POST["type"];
    $genre = $_POST["genre"];
    $description = $_POST["description"];
    $author = $_POST["author"];
    $bookId = $_POST["id"];
    $copies = $_POST["copies"];

    try {

        $updateBookStmt = $conn->prepare("UPDATE books SET title = ?, type = ?, genre = ?, description = ?, author = ? WHERE id = ?");

        if (!$updateBookStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $updateBookStmt->bind_param(
            "ssssss",
            $title,
            $type,
            $genre,
            $description,
            $author,
            $bookId,
        );

        if ($updateBookStmt->execute()) {

            //Update book copies
            $countQuery = "SELECT COUNT(*) as total FROM books_copy WHERE bookRef = ?";
            $stmt = $conn->prepare($countQuery);
            $stmt->bind_param("i", $bookId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $totalCopies = $row['total'];

            // Count available copies
            $availableQuery = "SELECT COUNT(*) as available FROM books_copy WHERE bookRef = ? AND status = 'available'";
            $stmt = $conn->prepare($availableQuery);
            $stmt->bind_param("i", $bookId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $availableCopies = $row['available'];

            if ($copies > $totalCopies) {

                $num_copies = (int) $copies - $totalCopies;

                $values = [];

                for ($i = 0; $i < $num_copies; $i++) {
                    $values[] = "($bookId, 'available')";
                }

                $sql = "INSERT INTO books_copy (bookRef, status) VALUES " . implode(", ", $values);
                $conn->query($sql);
          
            } else {
                // Calculate how many copies to delete
                $copiesToDelete = max(0, $totalCopies - $copies);

                // If we need to delete more than available, we can only delete what's available
                $copiesToDelete = min($copiesToDelete, $availableCopies);

                if ($copiesToDelete > 0) {
                    // Delete the excess copies that are available
                    $deleteQuery = "DELETE FROM books_copy 
                              WHERE bookRef = ? 
                              AND status = 'available' 
                              AND id IN (
                                  SELECT id FROM (
                                      SELECT id FROM books_copy
                                      WHERE bookRef = ?
                                      AND status = 'available'
                                      ORDER BY id
                                      LIMIT ?
                                  ) as copies_to_delete
                              )";

                    $stmt = $conn->prepare($deleteQuery);
                    $stmt->bind_param("iii", $bookId, $bookId, $copiesToDelete);
                    $stmt->execute();

                    $deletedCount = $stmt->affected_rows;
                }
            }

            echo "updated";
        } else {
            throw new Exception("Error executing statement: " . $updateBookStmt->error);
        }

        $updateBookStmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
