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
                    <input class="input-style" name="title" placeholder="Title of Notification" required>
                    <input class="input-style" name="body" placeholder="Body of Notification" required>
                    <button class="cta-btn-primary" name="sendToAll" type="submit">Broadcast</button>
                </form>

                <?php
                
                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sendToAll"])) {
                    try {
                        require "../utility/dp-connection.php";

                        // Fetch all users with credentials
                        $findUserStmt = $conn->prepare("SELECT studentNumber, credential FROM users WHERE credential IS NOT NULL AND TRIM(credential) != ''");
                        $findUserStmt->execute();
                        $result = $findUserStmt->get_result();

                        $users = [];
                        while ($row = $result->fetch_assoc()) {
                            $users[] = $row;
                        }

                        $title = $_POST['title'];
                        $body = $_POST['body'];

                        $url = 'http://localhost/library-management-system/utility/sendUsersNotification.php';

                        $data = [
                            'title' => $title,
                            'body' => $body,
                            'users' => json_encode($users), // Pass the user list as JSON
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
                        } else {
                            echo $response; // Display the result from sendUsersNotification.php
                        }
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }

                ?>

            </div>
            <div class="broadcast-form-2" data-visible="false">

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input class="input-style" name="studentNum" type="number" placeholder="Student Number of Student" required>
                    <input class="input-style" name="title" placeholder="Title of Notification" required>
                    <input class="input-style" name="body" placeholder="Body of Notification" required>
                    <button class="cta-btn-primary" name="sendToUser" type="submit">Send</button>
                </form>

                <?php

                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sendToUser"])) {

                    try {
                        require "../utility/dp-connection.php";

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

                        $storeNotifications = $conn->prepare("INSERT INTO notifications (studentNumber, title, content) VALUES (?, ?, ?)");
                        $storeNotifications->bind_param(
                            "sss",
                            $_POST["studentNum"],
                            $title,
                            $body
                        );

                        $storeNotifications->execute();
                        $storeNotifications->close();
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