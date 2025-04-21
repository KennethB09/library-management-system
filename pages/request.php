<?php
require_once '../utility/auth.php';
checkAuth(); // Will redirect to login if not authenticated
require "../utility/dp-connection.php";

// Process form submission FIRST (before any HTML)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["returnDate"])) {
    try {
        $server = "localhost";
        $username = "lms_admin";
        $password = "admin12345";
        $dbname = "lms_db";

        $conn = new mysqli($server, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $requestBookStmt = $conn->prepare("INSERT INTO request_books (bookRef, requesterId, dueDate) VALUES (?, ?, ?)");

        if (!$requestBookStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $bookId = $_POST["bookId"];
        $studentNumber = $_COOKIE["student"];
        $days = (int)$_POST["returnDate"];
        $time = $_POST["returnTime"];
        $format = $_POST["format"];

        date_default_timezone_set("Asia/Manila");

        if ($format === "digital") {
            $returnDate = date('Y-m-d', strtotime("+$days days")) . " " . $time . ":00";
        } else {
            $returnDate = date('Y-m-d', strtotime("+$days days"));
        }

        $requestBookStmt->bind_param(
            "sss",
            $bookId,
            $studentNumber,
            $returnDate
        );

        $requestBookStmt->execute();
        $conn->close();

        header("Location: search-book.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get book ID - check both POST and GET methods
$selectedBookId = isset($_POST["bookId"]) ? $_POST["bookId"] : (isset($_GET["bookId"]) ? $_GET["bookId"] : null);

// If no book ID is provided, redirect to search page
if (!$selectedBookId) {
    header("Location: search-book.php");
    exit();
}

// Fetch book details
$selectedBookStmt = $conn->prepare("SELECT id, title, type, genre, description, author, format FROM books WHERE id = ?");
$selectedBookStmt->bind_param("i", $selectedBookId);
$selectedBookStmt->execute();

$selectedBookResult = $selectedBookStmt->get_result()->fetch_assoc();

// Get the number of copies for each book
$copiesStmt = $conn->prepare("SELECT COUNT(*) as total FROM books_copy WHERE bookRef = ?");
$copiesStmt->bind_param("s", $selectedBookId);
$copiesStmt->execute();
$copies = $copiesStmt->get_result()->fetch_assoc();
$copiesRow = $copies['total'];

// Get the available number of book copies that can be borrow
$availableStmt = $conn->prepare("SELECT COUNT(*) as available FROM books_copy WHERE bookRef =? AND status = 'available'");
$availableStmt->bind_param("s", $selectedBookId);
$availableStmt->execute();
$available = $availableStmt->get_result()->fetch_assoc();
$availableRow = $available['available'];

$studentNum = $_COOKIE["student"];
$studentStmt = $conn->prepare("SELECT studentNumber, firstName, lastName, email, course, section FROM users WHERE studentNumber = ?");
$studentStmt->bind_param("i", $studentNum);
$studentStmt->execute();

$studentResult = $studentStmt->get_result();
$studentRow = $studentResult->fetch_assoc();

$studentNumber = $studentRow["studentNumber"];
$firstName = $studentRow["firstName"];
$lastName = $studentRow["lastName"];
$email = $studentRow["email"];
$course = $studentRow["course"];
$section = $studentRow["section"];
?>

<!DOCTYPE html>
<html lang="en" id="root" data-scheme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/request-book.css">
    <link rel="icon" type="image/x-icon" href="../assets/logo.png">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <script>
        const selectedScheme = localStorage.getItem("scheme");
        const root = document.getElementById("root");

        if (selectedScheme === "light") {
            root.setAttribute("data-scheme", "light");
        } else {
            root.setAttribute("data-scheme", "dark");
        }
    </script>
    <title>Student | Request Book</title>
</head>

<body>
    <div class="request-book-header">
        <button onclick="window.location.href = '../pages/search-book.php'"><img src="../assets/arrow-back.svg"> back</button>
    </div>
    <div class="main-container">
        <div class="requested-book-container">
            <h1 class="requested-book-title" id="bookTitle"><?php echo $selectedBookResult["title"] ?></h1>
            <div class="requested-book-info-container">
                <div class="requested-book-about">
                    <h1>books about</h1>
                    <p id="bookAbout"><?php echo $selectedBookResult["description"] ?></p>
                </div>
                <div class="requested-book-author">
                    <h1>author: </h1>
                    <p id="bookAuthor"><?php echo $selectedBookResult["author"] ?></p>
                </div>
                <div class="requested-book-type">
                    <h1>type: </h1>
                    <p id="bookType"><?php echo $selectedBookResult["type"] ?></p>
                </div>
                <div class="requested-book-genre">
                    <h1>genre: </h1>
                    <p id="bookGenre"><?php echo $selectedBookResult["genre"] ?></p>
                </div>
                <div class="requested-book-available">
                    <h1>available: </h1>
                    <p><span id="bookAvailable"><?php echo $availableRow ?></span>/<span id="bookTotal"><?php echo $copiesRow ?></span></p>
                </div>
            </div>
        </div>
        <hr>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="request-book-form">
            <h1>borrower information</h1>
            <div class="info">
                <input type="hidden" id="bookId" name="bookId" value="<?php echo $selectedBookId; ?>">
                <div class="col">
                    <div class="label-input">
                        <span>First Name</span>
                        <p class="input"><?php echo $firstName; ?></p>
                    </div>
                    <div class="label-input">
                        <span>Last Name</span>
                        <p class="input"><?php echo $lastName; ?></p>
                    </div>
                </div>
                <div class="col">
                    <div class="label-input">
                        <span>Course</span>
                        <p class="input"><?php echo $course; ?></p>
                    </div>
                    <div class="label-input">
                        <span>Section</span>
                        <p class="input"><?php echo $section; ?></p>
                    </div>
                </div>
                <div class="label-input">
                    <span>Email</span>
                    <p class="input"><?php echo $email; ?></p>
                </div>
                <div class="label-input">
                    <span>Student Number</span>
                    <p class="input"><?php echo $studentNumber; ?></p>
                </div>
                <div class="label-input">
                    <label for="returnDate">Choose the Day of Return</label>
                    <div>
                        <select name="returnDate" id="returnDate" required>
                            <option value="2">1 day</option>
                            <option value="3">2 days</option>
                            <option value="4">3 days</option>
                            <option value="5">4 days</option>
                            <option value="6">5 days</option>
                        </select>
                        <?php if ($selectedBookResult["format"] === "digital") { ?>
                            <input type="hidden" name="format" value="<?php echo $selectedBookResult["format"] ?>" readonly>
                            <input class="input-style" type="time" name="returnTime">
                        <?php } ?>
                    </div>
                </div>
                <div class="request-btn-container">
                    <button type="button" class="ghost-btn" onclick="window.location.href = '../pages/search-book.php'">cancel</button>
                    <button type="submit" class="cta-btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>