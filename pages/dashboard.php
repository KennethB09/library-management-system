<?php
require_once '../utility/auth.php';
checkAuth();

require "../utility/dp-connection.php";

function tableDataR($bookData, $requestedOn)
{
    echo '<tr>';
    echo '<td>' . $bookData["title"] . '</td>';
    echo '<td>' . $bookData["genre"] . '</td>';
    echo '<td>' . $requestedOn . '</td>';
    echo '</tr>';
}

$getUserInfo = $conn->prepare("SELECT id, studentNumber, firstName, lastName, section, email, course, password, credential FROM users WHERE studentNumber =?");
$getUserInfo->bind_param("i", $_COOKIE["student"]);
$getUserInfo->execute();
$userInfoResult = $getUserInfo->get_result();
$userInfoRow = $userInfoResult->fetch_assoc();

$getUserBorrowedBooks = $conn->prepare("SELECT bookRef, borrowedOn, dueDate FROM borrowed_books WHERE borrower = ?");
$getUserBorrowedBooks->bind_param("i", $_COOKIE["student"]);
$getUserBorrowedBooks->execute();
$borrowedBooksResult = $getUserBorrowedBooks->get_result();

$countBorrowedBooks = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE borrower = ?");
$countBorrowedBooks->bind_param("i", $_COOKIE["student"]);
$countBorrowedBooks->execute();
$countBorrowedBooksResult = $countBorrowedBooks->get_result();
$countBorrowedBooksRow = $countBorrowedBooksResult->fetch_assoc();

$countRequestedBooks = $conn->prepare("SELECT COUNT(*) as total FROM request_books WHERE requesterId = ?");
$countRequestedBooks->bind_param("i", $_COOKIE["student"]);
$countRequestedBooks->execute();
$countRequestedBooksResult = $countRequestedBooks->get_result();
$countRequestedBooksRow = $countRequestedBooksResult->fetch_assoc();


$getUserRequestedBooks = $conn->prepare("SELECT bookRef, requestOn FROM request_books WHERE requesterId =?");
$getUserRequestedBooks->bind_param("i", $_COOKIE["student"]);
$getUserRequestedBooks->execute();
$getUserRequestedBooksResult = $getUserRequestedBooks->get_result();

$getUserWaitList = $conn->prepare("SELECT id, bookRef FROM waitlist WHERE userId =? ");
$getUserWaitList->bind_param("i", $_COOKIE["student"]);
$getUserWaitList->execute();
$getUserWaitListResult = $getUserWaitList->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/student-dashboard.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href)
        }
    </script>
    <script defer src="../js/student-dashboard.js"></script>
    <script src="../js/view-book-pdf.js"></script>
    <script defer src="../js/subscribe-notification.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.0.943/pdf.min.js"></script>
    <title>Student | Dashboard</title>
</head>

<body>

    <div class="user-profile-modal-container" data-visible="false">
        <div class="user-profile-modal">
            <div class="user-profile-modal-title-container">
                <h1>Your Profile</h1>
                <img src="../assets/close.svg" class="user-profile-modal-close-icon" onclick="clickProfile()">
            </div>
            <div class="user-profile-modal-info-container" data-visible="true">
                <img src="../assets/pencil.svg" class="user-profile-modal-edit-icon" onclick="editProfile()">
                <div class="user-profile-modal-info">
                    <div class="user-profile-modal-info-1">
                        <div class="user-info">
                            <h1>First Name</h1>
                            <p><?php echo $userInfoRow["firstName"] ?></p>
                        </div>
                        <div class="user-info">
                            <h1>Last Name</h1>
                            <p><?php echo $userInfoRow["lastName"] ?></p>
                        </div>
                        <div class="user-info">
                            <h1>Section</h1>
                            <p><?php echo $userInfoRow["section"] ?></p>
                        </div>
                    </div>
                    <div class="user-profile-modal-info-2">
                        <div class="user-info">
                            <h1>Course</h1>
                            <p><?php echo $userInfoRow["course"] ?></p>
                        </div>
                        <div class="user-info">
                            <h1>Student Number</h1>
                            <p><?php echo $userInfoRow["studentNumber"] ?></p>
                        </div>
                    </div>
                    <div class="user-profile-modal-info-3">
                        <h1>Email</h1>
                        <p><?php echo $userInfoRow["email"] ?></p>
                    </div>
                </div>
            </div>
            <!-- User Profile Edit Form -->
            <div class="user-profile-modal-form-container" data-visible="false">
                <form class="user-profile-modal-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="user-profile-modal-form-1">
                        <div class="user-form">
                            <label for="fName">First Name</label>
                            <input name="fName" value="<?php echo $userInfoRow["firstName"] ?>">
                        </div>
                        <div class="user-form">
                            <label for="lName">Last Name</label>
                            <input name="lName" value="<?php echo $userInfoRow["lastName"] ?>">
                        </div>
                        <div class="user-form">
                            <label for="section">Section</label>
                            <input name="section" value="<?php echo $userInfoRow["section"] ?>">
                        </div>
                    </div>
                    <div class="user-profile-modal-form-2">
                        <label for="cp">Change Password</label>
                        <input name="cp" placeholder="Type your new password" minlength="8">
                    </div>
                    <div class="user-profile-modal-form-btn">
                        <button class="ghost-btn" type="button" onclick="editProfile()">Cancel</button>
                        <button class="cta-btn-primary" name="user-profile-update" type="submit">Save</button>
                    </div>
                </form>

                <?php

                if (isset($_POST["user-profile-update"])) {

                    $server = "localhost";
                    $username = "lms_admin";
                    $password = "admin12345";
                    $dbname = "lms_db";

                    $firstName = $_POST["fName"];
                    $lastName = $_POST["lName"];
                    $section = $_POST["section"];
                    $newPass = $_POST["cp"];
                    $userId = $userInfoRow["id"];

                    try {
                        $conn = new mysqli($server, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            throw new Exception("Connection failed: " . $conn->connect_error);
                        }

                        if (empty($_POST["cp"])) {

                            $updateProfile = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, section = ? WHERE id = ?");

                            if (!$updateProfile) {
                                throw new Exception("Prepare failed: " . $conn->error);
                            }

                            $updateProfile->bind_param("ssss", $firstName, $lastName, $section, $userId);
                            $updateProfile->execute();
                        } else {

                            $updateProfile = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, section = ?, password = ? WHERE id = ?");

                            if (!$updateProfile) {
                                throw new Exception("Prepare failed: " . $conn->error);
                            }

                            $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);

                            $updateProfile->bind_param("sssss", $firstName, $lastName, $section, $hashedPassword, $userId);
                            $updateProfile->execute();
                        }

                        $updateProfile->close();
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }

                ?>

            </div>
        </div>
    </div>

    <div class="view-book" data-visible="false" id="viewBook">
        <div id="my_pdf_viewer" class="pdf-viewer-container">
            <div class="view-book-close-icon-container"><img src="../assets/close.svg" class="view-book-close-icon" onclick="openBook()"></div>
            <div id="canvas_container">
                <canvas id="pdf_renderer"></canvas>
            </div>
            <div class="pdf-viewer-navigation-container">
                <div id="navigation_controls">
                    <button id="go_previous">Previous</button>
                    <input id="current_page" value="1" type="number" />
                    <button id="go_next">Next</button>
                </div>
                <div id="zoom_controls">
                    <button id="zoom_in">+</button>
                    <button id="zoom_out">-</button>
                </div>
            </div>
        </div>
    </div>

    <header>
        <div class="search" onclick="window.location.href = './search-book.php'">
            <img src="../assets/search.svg" class="search-icon">
            <p>search books</p>
        </div>
        <div class="user-profile">
            <button><img src="../assets/notifications-outline.svg" class="notifications-icon"></button>
            <button onclick="toggleMenu()" class="user-profile-btn"><img src="../assets/person-circle.svg" class="user-profile-icon"></button>

            <div class="user-profile-dialogue" data-visible="false">
                <div class="user-profile-dialogue-items">
                    <button onclick="clickProfile()"><img src="../assets/person-outline.svg"> Profile</button>
                    <button><img src="../assets/sunny-outline.svg"> Theme</button>
                    <button onclick="enableNotification()"><img src="../assets/notifications-off-outline.svg"> Notification</button>
                </div>
                <button onclick="window.location.href = '../utility/logout.php'">Log-out</button>
            </div>
        </div>
    </header>

    <main>

        <div class="section-1-container">

            <div class="section-1-welcome-card">
                <h1>Welcome, <wbr> <?php echo $userInfoRow["firstName"] ?>!</h1>
                <br>
                <p>Today is <span><?php echo date("F d, Y") ?></span></p>
            </div>

            <div class="section-1-card-container">
                <div class="section-1-card-borrowed">
                    <h1>Borrowed</h1>
                    <div>
                        <h1>
                            <?php echo $countBorrowedBooksRow["total"] ?>
                        </h1>
                        <span>TOTAL</span>
                    </div>
                </div>

                <div class="section-1-card-requested">
                    <h1>Requested</h1>
                    <div>
                        <h1>
                            <?php echo $countRequestedBooksRow["total"] ?>
                        </h1>
                        <span>TOTAL</span>
                    </div>
                </div>
            </div>

        </div>

        </div>
        <div class="table-container">

            <div class="table-title-container">
                <h1 class="borrow-table-title title-clicked" onclick="switchTable('borrow')">Borrowed Books</h1>
                <hr>
                <h2 class="request-table-title" onclick="switchTable('request')">Requested Books</h2>
                <hr>
                <h2 class="waitList-table-title" onclick="switchTable('waitList')">Wait-List</h2>
            </div>

            <div class="table" data-visible="true" id="borrowTable">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>Type</th>
                        <th>Borrowed In</th>
                        <th>Due Date</th>
                    </thead>
                    <tbody>
                        <?php if ($borrowedBooksResult->num_rows > 0) { ?>
                            <?php while ($borrowData = $borrowedBooksResult->fetch_assoc()) { ?>
                                <?php try { ?>
                                    <?php

                                    $getCopy = $conn->prepare("SELECT bookRef FROM books_copy WHERE id = ?");
                                    $getCopy->bind_param("i", $borrowData["bookRef"]);
                                    $getCopy->execute();
                                    $copyData = $getCopy->get_result()->fetch_assoc();

                                    $stmt = $conn->prepare("SELECT id, title, genre, type, format FROM books WHERE id = ?");
                                    $stmt->bind_param("i", $copyData["bookRef"]);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();

                                    $getPathStmt = $conn->prepare("SELECT location FROM uploads WHERE bookRef = ?");
                                    $getPathStmt->bind_param("i", $row["id"]);
                                    $getPathStmt->execute();
                                    $pathResult = $getPathStmt->get_result();
                                    $pathRow = $pathResult->fetch_assoc();

                                    $borrowedIn = date("Y-m-d", strtotime($borrowData['borrowedOn']));
                                    $returnOn = date("Y-m-d", strtotime($borrowData['dueDate']));

                                    // SEND NOTIFICATION TO USER IF DUE DATE IS TOMORROW
                                    $currentDate = date('Y-m-d');
                                    $checkDate = date('Y-m-d', strtotime($currentDate . " + 1 days"));

                                    if ($checkDate == $returnOn) {

                                        $title = $row['title'];

                                        $url = 'http://localhost/library-management-system/utility/sendUserNotification.php';

                                        $data = [
                                            'title' => 'Borrowed Book is Due Tomorrow',
                                            'body' => 'Your borrowed book ' . $title . ' is Due tomorrow.',
                                            'credential' => $userInfoRow["credential"]
                                        ];

                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_POST, true);
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

                                        $response = curl_exec($ch);
                                        $error = curl_error($ch);
                                        curl_close($ch);

                                        if ($error) {
                                            echo "cURL Error: $error";
                                        }
                                    }

                                    if (date("Y-m-d") > $returnOn) {

                                        $title = $row['title'];

                                        $url = 'http://localhost/library-management-system/utility/sendUserNotification.php';

                                        $data = [
                                            'title' => 'Borrowed Book is pass the Due Date',
                                            'body' => 'Your borrowed book ' . $title . ' is already pass the Due Date, you should return in immediately.',
                                            'credential' => $userInfoRow["credential"]
                                        ];

                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_POST, true);
                                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

                                        $response = curl_exec($ch);
                                        $error = curl_error($ch);
                                        curl_close($ch);

                                        if ($error) {
                                            echo "cURL Error: $error";
                                        }
                                    }

                                    ?>

                                    <?php if (isset($row["format"]) && $row["format"] === "digital") { ?>
                                        <tr onClick="viewBook('<?php echo $pathRow["location"]; ?>')">
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['genre']); ?></td>
                                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                                            <td><?php echo htmlspecialchars($borrowedIn); ?></td>
                                            <td><?php echo htmlspecialchars($returnOn); ?></td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['genre']); ?></td>
                                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                                            <td><?php echo htmlspecialchars($borrowedIn); ?></td>
                                            <td><?php echo htmlspecialchars($returnOn); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } catch (Exception $e) { ?>
                                    <?php echo "Error: " . $e->getMessage(); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo '<tr><td>0 result</td></tr>'; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="table" data-visible="false" id="requestTable">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>requestedOn</th>
                    </thead>
                    <tbody>
                        <?php if ($getUserRequestedBooksResult->num_rows > 0) { ?>
                            <?php while ($requestData = $getUserRequestedBooksResult->fetch_assoc()) { ?>
                                <?php try { ?>
                                    <?php
                                    $requestedOn = $requestData["requestOn"];

                                    // Get referenced Book on Book copy
                                    $stmt = $conn->prepare("SELECT title, genre, type FROM books WHERE id = ?");
                                    $stmt->bind_param("i", $requestData["bookRef"]);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();

                                    ?>

                                    <tr>
                                        <td><?php echo htmlspecialchars($row["title"]) ?></td>
                                        <td><?php echo htmlspecialchars($row["genre"]) ?></td>
                                        <td><?php echo htmlspecialchars($requestedOn) ?></td>
                                    </tr>

                                <?php } catch (Exception $e) { ?>
                                    <?php echo "Error: " . $e->getMessage(); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo '<tr><td>0 result</td></tr>'; ?>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

            <div class="table waitList-table-container" data-visible="false" id="waitListTable">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>Status</th>
                    </thead>
                    <tbody>

                        <?php if ($getUserWaitListResult->num_rows > 0) { ?>
                            <?php while ($waitListData = $getUserWaitListResult->fetch_assoc()) { ?>
                                <?php try { ?>

                                    <?php
                                    $getCopy = $conn->prepare("SELECT bookRef, status FROM books_copy WHERE id = ?");
                                    $getCopy->bind_param("i", $waitListData["bookRef"]);
                                    $getCopy->execute();
                                    $copyData = $getCopy->get_result()->fetch_assoc();

                                    $stmt = $conn->prepare("SELECT title, genre FROM books WHERE id = ?");
                                    $stmt->bind_param("i", $copyData["bookRef"]);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();
                                    ?>

                                    <tr>
                                        <td><?php echo htmlspecialchars($row["title"]) ?></td>
                                        <td><?php echo htmlspecialchars($row["genre"]) ?></td>
                                        <td><?php echo htmlspecialchars($copyData["status"]) ?></td>
                                        <td><button class="cta-btn-secondary" onclick="waitListRemove(event, '<?php echo htmlspecialchars($waitListData['id']) ?>')">Remove</button></td>
                                    </tr>

                                <?php } catch (Exception $e) { ?>
                                    <?php echo "Error: " . $e->getMessage(); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo '<tr><td>0 result</td></tr>'; ?>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
    </main>

</body>

</html>