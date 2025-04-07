<?php
require_once '../utility/auth.php';
checkAuth();

require "../utility/dp-connection.php";

function tableData($row, $borrowedIn, $returnOn, $path)
{
    if ($path !== "digital") {
        echo '<tr onClick="viewBook(" $path")">';
        echo '<td>' . $row['title'] . '</td>';
        echo '<td>' . $row['genre'] . '</td>';
        echo '<td>' . $row['type'] . '</td>';
        echo '<td>' . $borrowedIn . '</td>';
        echo '<td>' . $returnOn . '</td>';
        echo '</tr>';
    } else {
        echo '<tr>';
        echo '<td>' . $row['title'] . '</td>';
        echo '<td>' . $row['genre'] . '</td>';
        echo '<td>' . $row['type'] . '</td>';
        echo '<td>' . $borrowedIn . '</td>';
        echo '<td>' . $returnOn . '</td>';
        echo '</tr>';
    }
}

function tableDataR($bookData, $requestedOn)
{
    echo '<tr>';
    echo '<td>' . $bookData["title"] . '</td>';
    echo '<td>' . $bookData["genre"] . '</td>';
    echo '<td>' . $requestedOn . '</td>';
    echo '</tr>';
}

function waitListData($data, $status)
{
    echo '<tr>';
    echo '<td>' . $data["title"] . '</td>';
    echo '<td>' . $data["genre"] . '</td>';
    echo '<td>' . $status . '</td>';
    echo '</tr>';
}

$getUserInfo = $conn->prepare("SELECT studentNumber, firstName, lastName, section, email, course FROM users WHERE studentNumber =?");
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

$getUserWaitList = $conn->prepare("SELECT bookRef FROM waitlist WHERE userId =? ");
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
    <script defer src="../js/student-dashboard.js"></script>
    <script src="../js/view-book-pdf.js"></script>
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
            <div class="user-profile-model-info-container">
                <img src="../assets/pencil.svg" class="user-profile-modal-edit-icon">
                <div class="user-profile-model-info">
                    <div class="user-profile-model-info-1">
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
                    <div class="user-profile-model-info-2">
                        <div class="user-info">
                            <h1>Course</h1>
                            <p><?php echo $userInfoRow["course"] ?></p>
                        </div>
                        <div class="user-info">
                            <h1>Student Number</h1>
                            <p><?php echo $userInfoRow["studentNumber"] ?></p>
                        </div>
                    </div>
                    <div class="user-profile-model-info-3">
                        <h1>Email</h1>
                        <p><?php echo $userInfoRow["email"] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="view-book" data-visible="false" id="viewBook">
        <div id="my_pdf_viewer">
            <div class="view-book-close-icon-container"><img src="../assets/close.svg" class="view-book-close-icon" onclick="openBook()"></div>
            <div id="canvas_container">
                <canvas id="pdf_renderer"></canvas>
            </div>
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

    <header>
        <div class="user-profile">
            <img src="../assets/person-circle.svg" class="user-profile-icon" onclick="clickProfile()">
            <h2><?php echo $userInfoRow["firstName"] . " " . $userInfoRow["lastName"] ?></h2>
        </div>
        <div class="search">
            <a href="./search-book.php">search books</a>
        </div>
        <div class="logout-container">
            <button class="dashboard-logout-btn ghost-btn" onclick="window.location.href = '../utility/logout.php'">logout</button>
        </div>
    </header>
    <main>
        <div class="section-1-container">
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
            <div class="table-container section-1-table-container">
                <h1>Wait list</h1>
                <div class="table section-1-table">
                    <table>
                        <thead>
                            <th>Name</th>
                            <th>Genre</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <?php
                            if ($getUserWaitListResult->num_rows > 0) {
                                while ($waitListData = $getUserWaitListResult->fetch_assoc()) {
                                    try {

                                        $getCopy = $conn->prepare("SELECT bookRef, status FROM books_copy WHERE id = ?");
                                        $getCopy->bind_param("i", $waitListData["bookRef"]);
                                        $getCopy->execute();
                                        $copyData = $getCopy->get_result()->fetch_assoc();

                                        $stmt = $conn->prepare("SELECT title, genre FROM books WHERE id = ?");
                                        $stmt->bind_param("i", $copyData["bookRef"]);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_assoc();

                                        waitListData($row, $copyData["status"]);
                                    } catch (Exception $e) {
                                        echo "Error: " . $e->getMessage();
                                    }
                                }
                            } else {
                                echo '<tr><td>0 result</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="table-container">
            <div class="table-title-container">
                <h1 class="borrow-table-title title-clicked" onclick="switchTable('borrow')">Borrowed Books</h1>
                <h2 class="request-table-title" onclick="switchTable('request')">Requested Books</h2>
            </div>
            <div class="table" data-visible="true" id="borrowTable">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>Type</th>
                        <th>Borrowed in</th>
                        <th>Return on</th>
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
                        <?php
                        if ($getUserRequestedBooksResult->num_rows > 0) {
                            while ($requestData = $getUserRequestedBooksResult->fetch_assoc()) {
                                try {
                                    $requestedOn = $requestData["requestOn"];

                                    // Get referenced Book on Book copy
                                    $stmt = $conn->prepare("SELECT title, genre, type FROM books WHERE id = ?");
                                    $stmt->bind_param("i", $requestData["bookRef"]);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();

                                    tableDataR($row, $requestedOn);
                                } catch (Exception $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                            }
                        } else {
                            echo '<tr><td>0 result</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>

</html>