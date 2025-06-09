<?php
require_once("../vendor/autoload.php");
require "../utility/dp-connection.php";

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$privateKey = $_ENV['PRIVATE_KEY'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $auth = [
        'VAPID' => [
            'subject' => 'kennethbacaltos091@gmail.com',
            'publicKey' => 'BM2ApnJjDV_efsvitcM3c_Ylu8tzraD_Zpo4OcyUDVR61ObJbY95g1tHVu_U7oNSeYcOJ5zLS73VaFhEzRKtofg',
            'privateKey' => $privateKey,
        ],
    ];

    $webPush = new WebPush($auth);

    $title = $_POST['title'];
    $body = $_POST['body'];
    $users = json_decode($_POST['users'], true);

    $successCount = 0;
    $failureCount = 0;

    foreach ($users as $user) {
        $credential = json_decode($user['credential'], true);

        if ($credential && isset($credential['endpoint']) && isset($credential['keys'])) {
            try {
                $webPush->sendOneNotification(
                    Subscription::create($credential),
                    json_encode([
                        'title' => $title,
                        'body' => $body,
                        'url' => 'http://localhost/library-management-system/pages/dashboard.php',
                    ]),
                    ['TTL' => 5000]
                );

                // Store notification in the database
                $storeNotifications = $conn->prepare("INSERT INTO notifications (studentNumber, title, content) VALUES (?, ?, ?)");
                $storeNotifications->bind_param(
                    "sss",
                    $user['studentNumber'],
                    $title,
                    $body
                );
                $storeNotifications->execute();
                $storeNotifications->close();

                $successCount++;
            } catch (Exception $e) {
                $failureCount++;
            }
        } else {
            $failureCount++;
        }
    }

    echo "Notifications sent successfully to $successCount users. Failed for $failureCount users.";
}
