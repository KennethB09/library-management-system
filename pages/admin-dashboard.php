<?php
require_once '../utility/admin-auth.php';
checkAdminAuth();

require "../utility/dp-connection.php";

$adminInfoStmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$adminInfoStmt->bind_param("i", $_COOKIE["admin"]);
$adminInfoStmt->execute();
$adminInfo = $adminInfoStmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/admin-stylesheet.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <script defer src="../js/admin-home.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href)
        }
    </script>

    <title>Admin Dashboard</title>
</head>

<body class="admin-dashboard">
    <nav class="admin-nav">

        <button class="admin-account-btn" onclick="onToggleAdminInfo()">
            <span class="admin-account-btn-span">Admin</span>
            <span><?php echo $adminInfo["firstName"], " ", $adminInfo["lastName"] ?></span>
        </button>

        <div class="admin-nav-tab-container">
            <div class="tab-container">
                <button class="tab-link" id="defaultTab" onclick="changeTab(event, 'home')">Home</button>
                <button class="tab-link" id="manageTab" onclick="changeTab(event, 'manage')">Manage</button>
                <button class="tab-link" id="borrowedTab" onclick="changeTab(event, 'borrowed')">Borrowed</button>
                <button class="tab-link" id="requestTab" onclick="changeTab(event, 'request')">Request</button>
            </div>
        </div>

        <button class="admin-logout-btn ghost-btn" onclick="onToggleLogout()">Log-out</button>
    </nav>
    <main>
        <section id="home" class="tab-content">
            <?php require '../components/home.php' ?>
        </section>
        <section id="manage" class="tab-content">
            <?php require '../components/manage.php' ?>
        </section>
        <section id="borrowed" class="tab-content">
            <?php require '../components/borrowed.php' ?>
        </section>
        <section id="request" class="tab-content">
            <?php require '../components/request.php' ?>
        </section>
    </main>

    <!-- ADMIN INFO MODAL -->
    <div class="admin-info-main-container" data-visible="false">
        <div class="admin-info-container">
            <div class="admin-info-header">
                <h1>admin profile</h1>
                <button onclick="onToggleAdminInfo()"><img src="../assets/close.svg"></button>
            </div>
            <div class="admin-info-edit-btn-container">
                <button><img src="../assets/pencil.svg"></button>
                <button><img src="../assets/person-add.svg"></button>
            </div>
            <div class="admin-info-content" data-visible="true">
                <div class="admin-first-name-container">
                    <span class="admin-first-name-label">first name</span>
                    <span class="admin-first-name"><?php echo $adminInfo["firstName"] ?></span>
                </div>
                <div class="admin-last-name-container">
                    <span class="admin-last-name-label">last name</span>
                    <span class="admin-last-name"><?php echo $adminInfo["lastName"] ?></span>
                </div>
            </div>
            <div class="admin-info-edit-form-container" data-visible="false">
                <form>
                    <div>
                        <label for="first-name">first name</label>
                        <input type="text" id="edit-first-name" name="first-name">
                    </div>
                    <div>
                        <label for="last-name">last name</label>
                        <input type="text" id="edit-last-name" name="last-name">
                    </div>
                    <div>
                        <label for="new-pass">change password</label>
                        <input type="password" id="edit-new-pass" name="new-pass">
                    </div>
                    <div>
                        <label for="re-new-pass">re-type new password</label>
                        <input type="password" id="edit-re-new-pass" name="re-new-pass">
                    </div>
                    <div>
                        <label for="old-pass">type old password</label>
                        <input type="password" id="edit-old-pass" name="old-pass">
                    </div>
                    <div>
                        <button type="button">cancel</button>
                        <button type="submit">save</button>
                    </div>
                </form>
            </div>
            <div class="admin-info-new-admin-form-container" data-visible="false">
                <form>
                    <div>
                        <label for="first-name">first name</label>
                        <input type="text" id="first-name" name="first-name">
                    </div>
                    <div>
                        <label for="last-name">last name</label>
                        <input type="text" id="last-name" name="last-name">
                    </div>
                    <div>
                        <label for="new-pass">password</label>
                        <input type="password" id="new-pass" name="new-pass">
                    </div>
                    <div>
                        <button type="button">cancel</button>
                        <button type="submit">save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADMIN LOGOUT MODAL -->
    <div class="admin-logout-modal-container" data-visible="false">
        <div class="admin-logout-modal">
            <div class="admin-logout-modal-header">
                <h1>logout</h1>
                <button onclick="onToggleLogout()"><img src="../assets/close.svg"></button>
            </div>
            <p>are you sure you want to log-out?</p>
            <div class="admin-logout-modal-btn-container">
                <button class="ghost-btn" onclick="onToggleLogout()">no</button>
                <button class="cta-btn-primary" onclick="window.location.href = '../utility/admin-logout.php'">yes</button>
            </div>
        </div>
    </div>
</body>
<script defer src="../js/admin-dashboard.js"></script>

</html>