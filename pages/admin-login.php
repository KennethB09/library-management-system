<?php
// Redirect if already logged in
if (isset($_COOKIE["admin"])) {
    header("Location: admin-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/stylesheet.css">
    <title>Admin Login</title>
</head>

<?php

$error_password = "";
$error_admin_name = "";
$error_form = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        require "../utility/dp-connection.php";

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id, firstName, lastName, email, password FROM admin WHERE firstName = ? AND lastName = ?");
        $stmt->bind_param("ss", $_POST["firstName"], $_POST["lastName"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($_POST["password"], $admin["password"])) {

                setcookie("admin", $admin["id"], time() + (86400 * 30), "/");

                header("Location: admin-dashboard.php");
                exit();
            } else {
                $error_password = "Invalid password.";
            }
        } else {
            $error_admin_name = "Invalid admin name.";
        }
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $error_form = "Error: " . $e->getMessage();
    }
}
?>

<body>
    <div class="admin-main-container">
        <div class="admin-form-container">
            <h1>Admin Login</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-container">
                    <label for="firstName">Admin Name</label>
                    <input type="text" id="firstName" name="firstName" required autocomplete="off">
                </div>
                <div class="input-container">
                    <label for="lastName">Admin Surname</label>
                    <input type="text" id="lastName" name="lastName" required autocomplete="off">
                </div>
                <?php if ($error_admin_name !== "") { ?>
                    <span class="form-error"><?php echo htmlspecialchars($error_admin_name); ?></span>
                <?php } ?>
                <div class="input-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="off">
                    <?php if ($error_password !== "") { ?>
                        <span class="form-error"><?php echo htmlspecialchars($error_password); ?></span>
                    <?php } ?>
                </div>

                <button type="submit">Login</button>
            </form>
        </div>

        <a class="role" href="login.php">Student</a>
    </div>

</body>

</html>