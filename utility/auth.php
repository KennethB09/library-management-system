<?php

function checkAuth()
{
    // Check if user is not logged in
    if (!isset($_COOKIE["student"])) {
        header("Location: login.php");
        exit();
    }
}

function logout()
{
    // Delete the cookie
    if (isset($_COOKIE["student"])) {
        setcookie("student", "", time() - 3600, "/", "", false, true);
    }

    // Redirect to login page
    header("Location: ../pages/login.php");
    exit();
}
