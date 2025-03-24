<?php
require_once '../utility/auth.php';
checkAuth();

require "../utility/dp-connection.php";

function tableData($row, $borrowedIn, $returnOn) {
    echo '<tr>';
    echo '<td>' . $row['title'] . '</td>';
    echo '<td>' . $row['genre'] . '</td>';
    echo '<td>' . $row['type'] . '</td>';
    echo '<td>' . $borrowedIn . '</td>';
    echo '<td>' . $returnOn . '</td>';
    echo '</tr>';
}

function tableDataR($bookData, $requestedOn) {
    echo '<tr>';
    echo '<td>' . $bookData["title"] . '</td>';
    echo '<td>' . $bookData["genre"] . '</td>';
    echo '<td>' . $requestedOn . '</td>';
    echo '</tr>';
}

$getUserInfo = $conn->prepare("SELECT studentNumber, firstName, lastName FROM users WHERE studentNumber =?");
$getUserInfo->bind_param("i", $_SESSION["studentNumber"]);
$getUserInfo->execute();
$userInfoResult = $getUserInfo->get_result();
$userInfoRow = $userInfoResult->fetch_assoc();

$getUserBorrowedBooks = $conn->prepare("SELECT bookRef, borrowedOn, dueDate FROM borrowed_books WHERE borrower = ?");
$getUserBorrowedBooks->bind_param("i", $_SESSION["studentNumber"]);
$getUserBorrowedBooks->execute();
$borrowedBooksResult = $getUserBorrowedBooks->get_result();

$countBorrowedBooks = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE borrower = ?");
$countBorrowedBooks->bind_param("i", $_SESSION["studentNumber"]);
$countBorrowedBooks->execute();
$countBorrowedBooksResult = $countBorrowedBooks->get_result();
$countBorrowedBooksRow = $countBorrowedBooksResult->fetch_assoc();

$countRequestedBooks = $conn->prepare("SELECT COUNT(*) as total FROM request_books WHERE requesterId = ?");
$countRequestedBooks->bind_param("i", $_SESSION["studentNumber"]);
$countRequestedBooks->execute();
$countRequestedBooksResult = $countRequestedBooks->get_result();
$countRequestedBooksRow = $countRequestedBooksResult->fetch_assoc();


$getUserRequestedBooks = $conn->prepare("SELECT bookRef, requestOn FROM request_books WHERE requesterId =?");
$getUserRequestedBooks->bind_param("i", $_SESSION["studentNumber"]);
$getUserRequestedBooks->execute();
$getUserRequestedBooksResult = $getUserRequestedBooks->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/student-dashboard.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <title>Student | Dashboard</title>
</head>

<body>
    <header>
        <div class="user-profile">
            <h2>Welcome, <?php echo $userInfoRow["firstName"] . " " . $userInfoRow["lastName"] ?></h2>
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
            <div class="table-container section-1-table-container">
                <h1>Requested Books</h1>
                <div class="table section-1-table">
                    <table>
                        <thead>
                            <th>Name</th>
                            <th>Genre</th>
                            <th>Requested On</th>
                        </thead>
                        <tbody>
                            <?php
                            if ($getUserRequestedBooksResult->num_rows > 0) {
                                while ($borrowData = $getUserRequestedBooksResult->fetch_assoc()) {
                                    try {

                                        $requestedOn = date("Y-m-d", strtotime($borrowData["requestOn"]));

                                        $getBook = $conn->prepare("SELECT title, genre FROM books WHERE id =?");
                                        $getBook->bind_param("i", $borrowData["bookRef"]);
                                        $getBook->execute();
                                        $bookData = $getBook->get_result()->fetch_assoc();

                                        tableDataR($bookData, $requestedOn);
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
        <div class="table-container">
            <h1>Borrowed Books</h1>
            <div class="table">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>Type</th>
                        <th>Borrowed in</th>
                        <th>Return on</th>
                    </thead>
                    <tbody>
                        <?php
                        if ($borrowedBooksResult->num_rows > 0) {
                            while ($borrowData = $borrowedBooksResult->fetch_assoc()) {
                                try {
                                    $borrowedIn = $borrowData["borrowedOn"];
                                    $dueDate = $borrowData["dueDate"];

                                    $getCopy = $conn->prepare("SELECT bookRef FROM books_copy WHERE id = ?");
                                    $getCopy->bind_param("i", $borrowData["bookRef"]);
                                    $getCopy->execute();
                                    $copyData = $getCopy->get_result()->fetch_assoc();

                                    $stmt = $conn->prepare("SELECT title, genre, type FROM books WHERE id = ?");
                                    $stmt->bind_param("i", $copyData["bookRef"]);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();

                                    tableData($row, $borrowedIn, $dueDate);
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