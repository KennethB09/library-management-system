<?php
session_start();
require_once '../utility/admin-auth.php';
checkAdminAuth();

require "../utility/dp-connection.php";

$adminInfoStmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$adminInfoStmt->bind_param("i", $_COOKIE["admin"]);
$adminInfoStmt->execute();
$adminInfo = $adminInfoStmt->get_result()->fetch_assoc();

$errorMsg = "";

$fNameErr = $lNameErr = $newPassErr = $reNewPassErr = $oldPassErr = "";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/admin-stylesheet.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href)
        }
    </script>

    <title>Admin Dashboard</title>
</head>

<body class="admin-dashboard">

    <!-- CUSTOM ALERT -->
    <?php if (isset($_SESSION['alert'])) { ?>
        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>" id="alert" role="alert">
            <p><?php echo $_SESSION['alert']['message']; ?></p>
            <button onclick="alertDismiss()"><img src="../assets/close.svg"></button>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php } ?>

    <nav class="admin-nav">

        <button class="admin-account-btn" onclick="toggleModal('adminInfoMainContainer')">
            <span class="admin-account-btn-span">Admin</span>
            <span><?php echo $adminInfo["firstName"], " ", $adminInfo["lastName"] ?></span>
        </button>

        <div class="admin-nav-tab-container">
            <div class="tab-container">
                <button class="tab-link" id="defaultTab" onclick="changeTab(event, 'home')">Home</button>
                <button class="tab-link" id="manageTab" onclick="changeTab(event, 'manage')">Manage</button>
                <button class="tab-link" id="borrowedTab" onclick="changeTab(event, 'borrowed')">Borrowed</button>
                <button class="tab-link" id="requestTab" onclick="changeTab(event, 'request')">Request</button>
                <button class="tab-link" id="broadcastTab" onclick="changeTab(event, 'broadcast')">Broadcast</button>
            </div>
        </div>

        <button class="admin-logout-btn ghost-btn" onclick="toggleModal('adminLogoutModalContainer')">Log-out</button>
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
        <section id="broadcast" class="tab-content">
            <?php require '../components/broadcast.php' ?>
        </section>
    </main>

    <!-- ADMIN INFO MODAL -->
    <div class="admin-info-main-container" id="adminInfoMainContainer" data-visible="false">
        <div class="admin-info-container">
            <div class="admin-info-header">
                <h1>admin profile</h1>
                <button onclick="toggleModal('adminInfoMainContainer')"><img src="../assets/close.svg"></button>
            </div>
            <div class="admin-info-edit-btn-container">
                <button onclick="showForm('adminInfoContent', 'adminInfoEditFormContainer', 'adminInfoNewAdminFormContainer')"><img src="../assets/pencil.svg"> Edit Information</button>
                <button onclick="showForm('adminInfoContent', 'adminInfoNewAdminFormContainer', 'adminInfoEditFormContainer')"><img src="../assets/person-add.svg"> Add New Admin</button>
            </div>
            <div class="admin-info-content" id="adminInfoContent" data-visible="true">
                <h1>Current Logged-in</h1>
                <div class="admin-first-name-container">
                    <span class="admin-first-name-label">first name</span>
                    <span class="admin-first-name"><?php echo $adminInfo["firstName"] ?></span>
                </div>
                <div class="admin-last-name-container">
                    <span class="admin-last-name-label">last name</span>
                    <span class="admin-last-name"><?php echo $adminInfo["lastName"] ?></span>
                </div>
            </div>
            <div class="admin-info-edit-form-container" id="adminInfoEditFormContainer" data-visible="false">

                <form id="adminUpdateInfo" method="post">
                    <div class="input-container">
                        <label for="edit-first-name">First Name</label>
                        <input class="input-style" type="text" id="edit-first-name" name="first-name" value="<?php echo htmlspecialchars($adminInfo['firstName']) ?>" required minlength="2">
                        <span class="error"><?php echo $fNameErr ?></span>
                    </div>
                    <div class="input-container">
                        <label for="edit-last-name">Last Name</label>
                        <input class="input-style" type="text" id="edit-last-name" name="last-name" value="<?php echo htmlspecialchars($adminInfo['lastName']) ?>" required minlength="2">
                        <span class="error"><?php echo $lNameErr ?></span>
                    </div>
                    <div class="input-container">
                        <label for="edit-new-pass">Change Password</label>
                        <input class="input-style" type="password" id="edit-new-pass" name="new-pass" minlength="8">
                        <span class="error"><?php echo $newPassErr ?></span>
                    </div>
                    <div class="input-container">
                        <label for="edit-re-new-pass">Re-type New Password</label>
                        <input class="input-style" type="password" id="edit-re-new-pass" name="re-new-pass">
                        <span class="error"><?php echo $reNewPassErr ?></span>
                    </div>
                    <div class="input-container">
                        <label for="edit-old-pass">Type Old Password</label>
                        <input class="input-style" type="password" id="edit-old-pass" name="old-pass">
                        <span class="error"><?php echo $oldPassErr ?></span>
                    </div>
                    <div class="btn-container">
                        <button class="ghost-btn" type="button" onclick="showForm('adminInfoContent', 'adminInfoEditFormContainer', 'adminInfoNewAdminFormContainer')">Cancel</button>
                        <button class="cta-btn-primary" type="submit" name="editAdmin">Save</button>
                    </div>
                </form>
            </div>
            <div class="admin-info-new-admin-form-container" id="adminInfoNewAdminFormContainer" data-visible="false">
                <?php
                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["newAdmin"])) {
                    // After form processing, store alert in session

                    try {
                        require "../utility/dp-connection.php";

                        // Retrieve form inputs
                        $firstName = trim($_POST["first-name"]);
                        $lastName = trim($_POST["last-name"]);
                        $email = trim($_POST["email"]);
                        $password = $_POST["new-pass"];

                        // Validate inputs
                        if (empty($firstName)) {
                            throw new Exception("First name is required.");
                        }
                        if (empty($lastName)) {
                            throw new Exception("Last name is required.");
                        }
                        if (empty($email)) {
                            throw new Exception("Email is required.");
                        }
                        if (strlen($password) < 8) {
                            throw new Exception("Password must be at least 8 characters long.");
                        }

                        // Hash the password
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        // Insert new admin into the database
                        $insertAdminStmt = $conn->prepare("INSERT INTO admin (firstName, lastName, email, password) VALUES (?, ?, ?, ?)");
                        if (!$insertAdminStmt) {
                            throw new Exception("Prepare failed: " . $conn->error);
                        }
                        $insertAdminStmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

                        if ($insertAdminStmt->execute()) {
                            $_SESSION['alert'] = [
                                'type' => 'success',
                                'message' => 'Form submitted successfully!'
                            ];
                        } else {
                            throw new Exception("Failed to add new admin.");
                            $_SESSION['alert'] = [
                                'type' => 'danger',
                                'message' => 'Form not submitted!'
                            ];
                        }

                        $insertAdminStmt->close();
                    } catch (Exception $e) {
                        echo "<p class='error'>" . $e->getMessage() . "</p>";
                    }
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-container">
                        <label for="first-name">First Name</label>
                        <input class="input-style" type="text" id="first-name" name="first-name" autocomplete="true" required>
                    </div>
                    <div class="input-container">
                        <label for="last-name">Last Name</label>
                        <input class="input-style" type="text" id="last-name" name="last-name" autocomplete="true" required>
                    </div>
                    <div class="input-container">
                        <label for="last-name">Email</label>
                        <input class="input-style" type="email" id="email" name="email" autocomplete="true" required>
                    </div>
                    <div class="input-container">
                        <label for="new-pass">Password</label>
                        <input class="input-style" type="password" id="new-pass" name="new-pass" minlength="8" required>
                    </div>
                    <div class="btn-container">
                        <button class="ghost-btn" type="button" onclick="showForm('adminInfoContent', 'adminInfoNewAdminFormContainer', 'adminInfoEditFormContainer')">Cancel</button>
                        <button class="cta-btn-primary" type="submit" name="newAdmin">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADMIN LOGOUT MODAL -->
    <div class="admin-logout-modal-container" id="adminLogoutModalContainer" data-visible="false">
        <div class="admin-logout-modal">
            <div class="admin-logout-modal-header">
                <h1>logout</h1>
                <button onclick="toggleModal('adminLogoutModalContainer')"><img src="../assets/close.svg"></button>
            </div>
            <p>are you sure you want to log-out?</p>
            <div class="admin-logout-modal-btn-container">
                <button class="ghost-btn" onclick="toggleModal('adminLogoutModalContainer')">no</button>
                <button class="cta-btn-primary" onclick="window.location.href = '../utility/admin-logout.php'">yes</button>
            </div>
        </div>
    </div>
</body>
<script defer src="../js/admin-dashboard.js"></script>

</html>