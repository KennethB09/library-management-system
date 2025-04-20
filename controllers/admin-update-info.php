<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        require_once "../utility/dp-connection.php";

        // Get admin info from database
        $adminInfoStmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
        $adminInfoStmt->bind_param("i", $_COOKIE["admin"]);
        $adminInfoStmt->execute();
        $adminInfo = $adminInfoStmt->get_result()->fetch_assoc();

        $userId = $_COOKIE["admin"];
        $firstName = $_POST["first-name"];
        $lastName = $_POST["last-name"];
        $newPass = $_POST["new-pass"];
        $reNewPass = $_POST["re-new-pass"];
        $oldPass = $_POST["old-pass"];

        // Handle password change if requested
        if (empty($newPass) && empty($reNewPass) && empty($oldPass)) {
            // Only update profile info
            $updateProfile = $conn->prepare("UPDATE admin SET firstName = ?, lastName = ? WHERE id = ?");
            if (!$updateProfile) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $updateProfile->bind_param("ssi", $firstName, $lastName, $userId);

            if ($updateProfile->execute()) {
                // Set session alert for after page reload
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Profile updated successfully!'
                ];
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => 'Failed to update profile!'
                ];
                exit;
            }
            $updateProfile->close();
        } else {
            // Verify old password
            if (!password_verify($oldPass, $adminInfo["password"])) {
                $_SESSION['alert'] = [
                    'type' => 'warning',
                    'message' => "Your typed password doesn't match your old password."
                ];
                exit;
            }

            // Update profile and password
            $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
            $updateProfile = $conn->prepare("UPDATE admin SET firstName = ?, lastName = ?, password = ? WHERE id = ?");

            if (!$updateProfile) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $updateProfile->bind_param("sssi", $firstName, $lastName, $hashedPassword, $userId);

            if ($updateProfile->execute()) {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Profile and password updated successfully!'
                ];
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => 'Failed to update profile and password!'
                ];
                exit;
            }

            $updateProfile->close();
        }
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Invalid request method'
    ];
}
