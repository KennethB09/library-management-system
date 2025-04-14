<?php
require_once("vendor/autoload.php");

use Minishlink\WebPush\VAPID;

print_r(VAPID::createVapidKeys());

/*
"subject": "mailto: <kennethbacaltos091@gmail.com>",
[publicKey] => BM2ApnJjDV_efsvitcM3c_Ylu8tzraD_Zpo4OcyUDVR61ObJbY95g1tHVu_U7oNSeYcOJ5zLS73VaFhEzRKtofg
[privateKey] => Hy8iAU7wb3QN12IcsaMu6Ix56eSWushfI9Hl62SIGVQ
*/