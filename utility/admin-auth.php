<?php
session_start();

function checkAdminAuth()
{
    // Check if admin is logged in
    if (!isset($_SESSION["adminID"]) && !isset($_SESSION["email"])) {
        header("Location: ../pages/admin-login.php");
        exit();
    }

    $_SESSION['last_activity'] = time();
}

function adminLogout()
{
    $_SESSION = array();
    session_destroy();

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    header("Location: ../pages/admin-login.php");
    exit();
}
