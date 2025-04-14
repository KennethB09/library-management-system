<?php
require_once("../vendor/autoload.php");

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$privateKey = $_ENV['PRIVATE_KEY'];

$credential = $_POST["credential"];

$payload = json_encode([
    'title' => $_POST["title"],
    'body' => $_POST["body"],
    'url' => 'http://localhost/library-management-system/pages/dashboard.php'
]);

$auth = [
    'VAPID' => [
        'subject' => 'kennethbacaltos091@gmail.com',
        'publicKey' => 'BM2ApnJjDV_efsvitcM3c_Ylu8tzraD_Zpo4OcyUDVR61ObJbY95g1tHVu_U7oNSeYcOJ5zLS73VaFhEzRKtofg',
        'privateKey' => $privateKey,
    ],
];

$webPush = new WebPush($auth);

$report = $webPush->sendOneNotification(
    Subscription::create(json_decode($credential, true)),
    $payload,
    ['TTL' => 5000]
);

print_r($report);
