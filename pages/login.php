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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-container">
                    <label for="studentNumber">Student Number</label>
                    <input type="number" id="studentNumber" name="studentNumber" required>
                </div>
                <div class="input-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
    
                <button type="submit">Login</button>
            </form>
            <p>Already have an account? <a href="signup.php">Sign-up</a></p>
        </div>

        <a class="role" href="admin-login.php">Admin</a>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $server = "localhost";
        $username = "lms_admin";
        $password = "admin12345";
        $dbname = "lms_db";

        try {
            $conn = new mysqli($server, $username, $password, $dbname);

            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
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
                    session_start();
                    $_SESSION["studentNumber"] = $_POST["studentNumber"];
                    
                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "Invalid password";
                }
            } else {
                echo "Student number not found";
            }

            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>

</body>
</html>