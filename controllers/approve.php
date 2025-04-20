<?php
session_start();
require "../utility/dp-connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    function notifyUser($conn, $studentNumber)
    {
        $findUserStmt = $conn->prepare("SELECT firstName, credential FROM users WHERE studentNumber = ?");
        $findUserStmt->bind_param("i", $studentNumber);
        $findUserStmt->execute();
        $result = $findUserStmt->get_result()->fetch_assoc();

        $credential = isset($result["credential"]) ? $result["credential"] : null;

        if ($credential != null) {
            $title = "Librarian Approve your request!";
            $body = $result["firstName"] . ", librarian approved your borrow request. Come pick up your book or start reading it now on your student dashboard if it's a digital copy.";

            $url = 'http://localhost/library-management-system/utility/sendUserNotification.php';

            $data = [
                'title' => $title,
                'body' => $body,
                'credential' => $credential
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new Exception("cURL Error: " . $error);
            }

            $storeNotifications = $conn->prepare("INSERT INTO notifications (studentNumber, title, content) VALUES (?, ?, ?)");
            if (!$storeNotifications) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $storeNotifications->bind_param(
                "sss",
                $studentNumber,
                $title,
                $body
            );
            $storeNotifications->execute();
            $storeNotifications->close();

            return "Student " . $result["firstName"] . " with the ID " . $studentNumber . ", successfully notified.";
        } else {
            return "Student " . $result["firstName"] . " with the ID " . $studentNumber . ", request approved but the student is not subscribe to notifications.";
        }
    }

    $requestId = $_POST["requestId"];
    $bookRef = $_POST["bookRef"];
    $dueDate = $_POST["dueDate"];
    $borrower = $_POST["borrower"];
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
                $approveBookStmt = $conn->prepare("INSERT INTO borrowed_books (bookRef, dueDate, borrower) VALUES (?, ?, ?)");
                if (!$approveBookStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $approveBookStmt->bind_param(
                    "sss",
                    $availableCopyId,
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

                notifyUser($conn, $borrower);

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Request Approved and notified the user'
                ];
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
                $approveBookStmt = $conn->prepare("INSERT INTO borrowed_books (bookRef, dueDate, borrower) VALUES (?, ?, ?)");
                if (!$approveBookStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $approveBookStmt->bind_param(
                    "sss",
                    $availableCopyId,
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

                notifyUser($conn, $borrower);

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Request Approved and notified the user'
                ];
            } else {
                throw new Exception("No available copies found for this book");
            }
        }

        $conn->close();
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
