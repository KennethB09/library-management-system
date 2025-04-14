<?php
require_once("vendor/autoload.php");

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

// Your VAPID authentication keys
$auth = [
    'VAPID' => [
        'subject' => 'kennethbacaltos091@gmail.com',
        'publicKey' => 'BM2ApnJjDV_efsvitcM3c_Ylu8tzraD_Zpo4OcyUDVR61ObJbY95g1tHVu_U7oNSeYcOJ5zLS73VaFhEzRKtofg',
        'privateKey' => 'Hy8iAU7wb3QN12IcsaMu6Ix56eSWushfI9Hl62SIGVQ',
    ],
];

$webPush = new WebPush($auth);

// Check if credential is provided
$webPush = new WebPush($auth);

// Check if credential is provided and not empty
if (isset($_POST['credential']) && !empty(trim($_POST['credential']))) {
    // Get the title and body from POST data or use defaults
    $title = isset($_POST['title']) ? $_POST['title'] : 'Important Notification';
    $body = isset($_POST['body']) ? $_POST['body'] : 'You have a new notification';
    
    // Set the notification payload
    $payload = json_encode([
        'title' => $title,
        'body' => $body,
        'url' => 'http://localhost/library-management-system/pages/dashboard.php'
    ]);
    
    // Parse the credential JSON
    $subscription = json_decode($_POST['credential'], true);
    
    if ($subscription && isset($subscription['endpoint']) && isset($subscription['keys'])) {
        try {
            // Send notification to this subscription
            $webPush->sendOneNotification(
                Subscription::create($subscription),
                $payload,
                ['TTL' => 5000]
            );
            
            // Flush the queue
            $reports = $webPush->flush();
            
            // Process the report
            foreach ($reports as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                
                if ($report->isSuccess()) {
                    echo "Successfully sent notification to endpoint";
                } else {
                    echo "Failed to send notification: " . $report->getReason();
                    
                    // If subscription is expired or invalid
                    if ($report->isSubscriptionExpired()) {
                        echo "Subscription has expired or is invalid";
                        // Here you could add code to remove this subscription from your database
                    }
                }
            }
        } catch (Exception $e) {
            echo "Error sending notification: " . $e->getMessage();
        }
    } else {
        echo "Invalid credential format - missing required subscription properties";
    }
} else {
    echo "No valid credential provided";
}
?>