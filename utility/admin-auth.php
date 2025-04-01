<?php
function checkAdminAuth()
{
    // Check if admin is logged in
    if (!isset($_COOKIE["admin"])) {
        header("Location: ../pages/admin-login.php");
        exit();
    }
}

function adminLogout()
{
    setcookie("admin", "", time() - 3600, "/", "", false, true);

    header("Location: ../pages/admin-login.php");
    exit();
}
