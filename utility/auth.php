<?php
session_start();

function checkAuth()
{
    // Check if user is not logged in
    if (!isset($_SESSION["studentNumber"])) {
        header("Location: login.php");
        exit();
    }
}

function logout()
{
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Delete the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Redirect to login page
    header("Location: ../pages/login.php");
    exit();
}
