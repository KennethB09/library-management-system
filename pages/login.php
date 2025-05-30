<?php
if (isset($_COOKIE["student"])) {
    header("Location: dashboard.php");
    exit();
}

$error_password = "";
$error_studentNum = "";
$error_form = "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/stylesheet.css">
    <title>Student | Login</title>
</head>

<body>
    <div class="login-main-container">
        <div class="login-form-container">
            <h1>Student Login</h1>

            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                try {
                    require "../utility/dp-connection.php";

                    if ($conn->connect_error) {
                        throw new Exception("Connection failed: " . $conn->connect_error);
                    }

                    if (isset($_POST["studentNumber"]) && strlen((string)$_POST["studentNumber"]) !== 10) {
                        throw new Exception("Student Number length should be 10.");
                    }

                    $stmt = $conn->prepare("SELECT password FROM users WHERE studentNumber = ?");

                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }

                    $stmt->bind_param("s", $_POST["studentNumber"]);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();

                        // Verify the password
                        if (password_verify($_POST["password"], $user["password"])) {
                            // Password is correct - Start session
                            setcookie("student", $_POST["studentNumber"], time() + (86400 * 30), "/");

                            // Redirect to dashboard
                            header("Location: dashboard.php");
                            exit();
                        } else {
                            $error_password = "Invalid password";
                        }
                    } else {
                        $error_studentNum = "Student number not found";
                    }

                    $stmt->close();
                    $conn->close();
                } catch (Exception $e) {
                    $error_form = "Error: " . $e->getMessage();
                }
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-container">
                    <label for="studentNumber">Student Number</label>
                    <input type="number" id="studentNumber" name="studentNumber" required>
                    <?php if ($error_studentNum !== "") { ?>
                        <span class="form-error"><?php echo htmlspecialchars($error_studentNum); ?></span>
                    <?php } ?>
                </div>
                <div class="input-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <?php if ($error_password !== "") { ?>
                        <span class="form-error"><?php echo htmlspecialchars($error_password); ?></span>
                    <?php } ?>
                </div>

                <?php if ($error_form !== "") { ?>
                    <span class="form-error"><?php echo htmlspecialchars($error_form); ?></span>
                <?php } ?>

                <button type="submit">Login</button>
            </form>
            <p>Already have an account? <a href="signup.php">Sign-up</a></p>
        </div>

        <a class="role" href="admin-login.php">Admin</a>
    </div>

</body>

</html>