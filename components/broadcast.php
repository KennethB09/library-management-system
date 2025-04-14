<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/admin-component-style.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <script defer src="../js/admin-broadcast.js"></script>
    <title>Document</title>
</head>

<body>
    <main>
        <div class="broadcast-form-container">
            <h1>Make An Announcement</h1>
            <select class="select-style" id="broadcastFormSelector">
                <option value="all">Send to All</option>
                <option value="one">Send to Student</option>
            </select>
            <div class="broadcast-form-1" data-visible="true">

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="input-style" name="title" placeholder="Title of Notification">
                    <input class="input-style" name="body" placeholder="Body of Notification">
                    <button class="cta-btn-primary" name="sendToAll" type="submit">Broadcast</button>
                </form>

                <?php
                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sendToAll"])) {
                    try {
                        $server = "localhost";
                        $username = "lms_admin";
                        $password = "admin12345";
                        $dbname = "lms_db";

                        $conn = new mysqli($server, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            throw new Exception("Connection failed: " . $conn->connect_error);
                        }

                        // Fetch all users - we'll filter out empty credentials in PHP
                        $findUserStmt = $conn->prepare("SELECT credential FROM users");
                        $findUserStmt->execute();
                        $result = $findUserStmt->get_result();

                        $title = $_POST['title'];
                        $body = $_POST['body'];

                        $url = 'http://localhost/library-management-system/utility/sendUserNotification.php';
                        $sentCount = 0;

                        // Process each user
                        while ($row = $result->fetch_assoc()) {
                            // Check if credential is not empty (check for empty string or just whitespace)
                            if (!empty(trim($row["credential"]))) {
                                $data = [
                                    'title' => $title,
                                    'body' => $body,
                                    'credential' => $row["credential"]
                                ];

                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

                                $response = curl_exec($ch);
                                $error = curl_error($ch);
                                curl_close($ch);

                                if ($error) {
                                    echo "cURL Error: $error<br>";
                                } else {
                                    $sentCount++;
                                }
                            }
                        }

                        echo "Notifications sent successfully to $sentCount users with valid credentials.";
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                ?>

            </div>
            <div class="broadcast-form-2" data-visible="false">

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="input-style" name="studentNum" type="number" placeholder="Student Number of Student">
                    <input class="input-style" name="title" placeholder="Title of Notification">
                    <input class="input-style" name="body" placeholder="Body of Notification">
                    <button class="cta-btn-primary" name="sendToUser" type="submit">Send</button>
                </form>

                <?php

                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sendToUser"])) {

                    try {
                        $server = "localhost";
                        $username = "lms_admin";
                        $password = "admin12345";
                        $dbname = "lms_db";

                        $conn = new mysqli($server, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            throw new Exception("Connection failed: " . $conn->connect_error);
                        }

                        $findUserStmt = $conn->prepare("SELECT credential FROM users WHERE studentNumber = ?");
                        $findUserStmt->bind_param("i", $_POST["studentNum"]);
                        $findUserStmt->execute();
                        $result = $findUserStmt->get_result()->fetch_assoc();

                        $credential = isset($result["credential"]) ? $result["credential"] : null;
                        if ($credential == null) {
                            echo "<script>alert('Student is not Subscribe to Notification')</script>";
                            throw new Exception("Student " . $_POST["studentNum"] . "is not Subscribe to Notification");
                        }

                        $title = $_POST['title'];
                        $body = $_POST['body'];

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
                            echo "cURL Error: $error";
                            throw new Exception("cURL Error: " . $error);
                        }
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }

                ?>
            </div>

        </div>
    </main>
</body>

</html>