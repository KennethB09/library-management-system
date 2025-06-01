<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/stylesheet.css">
    <title>Student | Sign-up</title>
</head>

<body>

    <div class="sign-up-main-container">
        <div class="sign-up-form-container">
            <h1>Student<br>Sign-up Form</h1>

            <?php

            $error_studentNum = "";
            $error_form = "";
            $error_studentEmail = "";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                try {
                    require "../utility/dp-connection.php";

                    $checkStmt = $conn->prepare("SELECT studentNumber FROM users WHERE studentNumber = ?");

                    if (!$checkStmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }

                    $checkStmt->bind_param("s", $_POST["studentNumber"]);
                    $checkStmt->execute();
                    $checkStmt->store_result();

                    if ($checkStmt->num_rows() > 0) {

                        $error_studentNum = "Student number is already used.";
                        $checkStmt->close();
                    } else {

                        $checkEmailStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");

                        if (!$checkEmailStmt) {
                            throw new Exception("Prepare failed: " . $conn->error);
                        }

                        $checkEmailStmt->bind_param("s", $_POST["email"]);
                        $checkEmailStmt->execute();
                        $checkEmailStmt->store_result();

                        if ($checkEmailStmt->num_rows() > 0) {

                            $error_studentEmail = "Email is already used.";
                            $checkEmailStmt->close();
                        } else {

                            $stmt = $conn->prepare("INSERT INTO users (studentNumber, firstName, lastName, password, email, course, section) VALUES (?, ?, ?, ?, ?, ?, ?)");

                            if (!$stmt) {
                                throw new Exception("Prepare failed: " . $conn->error);
                            }

                            // Hash the password before storing
                            $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);

                            $stmt->bind_param(
                                "sssssss",
                                $_POST["studentNumber"],
                                $_POST["firstName"],
                                $_POST["lastName"],
                                $hashedPassword,
                                $_POST["email"],
                                $_POST["course"],
                                $_POST["section"]
                            );

                            if ($stmt->execute()) {

                                setcookie("student", $_POST["studentNumber"], time() + (86400 * 30), "/");

                                // Redirect to dashboard
                                header("Location: dashboard.php");
                                exit();
                            } else {
                                throw new Exception("Error executing statement: " . $stmt->error);
                            }

                            $stmt->close();
                            $conn->close();
                        }
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="input-container">
                    <label for="studentNumber">Student Number</label>
                    <input type="number" id="studentNumber" name="studentNumber" pattern="[0-9]{10}"
                        maxlength="10" minlength="10" required>
                    <?php if ($error_studentNum !== "") { ?>
                        <span class="form-error"><?php echo htmlspecialchars($error_studentNum); ?></span>
                    <?php } ?>
                </div>

                <div class="input-container">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>

                <div class="input-container">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>

                <div class="input-container">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <?php if ($error_studentEmail !== "") { ?>
                        <span class="form-error"><?php echo htmlspecialchars($error_studentEmail); ?></span>
                    <?php } ?>
                </div>

                <div class="sign-up-form-course-section-container">
                    <div class="course-container">
                        <label for="course">Course</label>
                        <select id="course" name="course">
                            <option value="BSIT">BSIT</option>
                            <option value="BSTM">BSTM</option>
                            <option value="CRIM">CRIM</option>
                            <option value="EDUC">EDUC</option>
                            <option value="BSCS">BSCS</option>
                            <option value="BSEE">BSEE</option>
                            <option value="BSCE">BSCE</option>
                            <option value="BSHM">BSHM</option>
                            <option value="BSBA">BSBA</option>
                            <option value="BSED">BSED</option>
                        </select>
                    </div>

                    <div class="section-container">
                        <label for="section">Section</label>
                        <input type="text" id="section" name="section" required>
                    </div>
                </div>
                <div class="input-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Submit</button>
            </form>
            <p>Already have an account? <a href="login.php">Log-in</a></p>
        </div>

        <a class="role" href="admin-login.php">Admin</a>
    </div>
</body>

</html>