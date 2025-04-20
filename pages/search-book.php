<?php

require_once '../utility/auth.php';
checkAuth(); // Will redirect to login if not authenticated

require "../utility/dp-connection.php";

$resultSearch;
$result;
$noResult = "";

$bookStmt = $conn->prepare("SELECT id, title, type, genre, description, author, format FROM books");
$bookStmt->execute();

$bookResult = $bookStmt->get_result();

$countBorrowedBooks = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books WHERE borrower = ?");
$countBorrowedBooks->bind_param("i", $_COOKIE["student"]);
$countBorrowedBooks->execute();
$countBorrowedBooksResult = $countBorrowedBooks->get_result();
$countBorrowedBooksRow = $countBorrowedBooksResult->fetch_assoc();

if (isset($_GET['search'])) {
    $searchParam = isset($_GET['search']) ? $_GET['search'] : '';
    $typeParam = "";

    if (isset($_GET['type'])) {
        $typeParam = $_GET['type'];
    }

    if ($searchParam !== "") {

        if ($typeParam == "all") {

            $stmtSearch = $conn->prepare("SELECT * FROM books WHERE title LIKE ?");
            $stmtSearch->bind_param("s", $searchParam);
            $stmtSearch->execute();
            $resultSearch = $stmtSearch->get_result();

            if ($resultSearch->num_rows === 0) {
                $noResult = "No " . $searchParam . " book found.";
                $result = $bookResult;
            } else {
                $result = $resultSearch;
            }

        } else {

            $stmtSearch = $conn->prepare("SELECT * FROM books WHERE title LIKE ? AND type = ?");
            $stmtSearch->bind_param("ss", $searchParam, $typeParam);
            $stmtSearch->execute();
            $resultSearch = $stmtSearch->get_result();
    
            if ($resultSearch->num_rows === 0) {
                $noResult = "No " . $searchParam . " book found in " .  $typeParam;
                $result = $bookResult;
            } else {
                $result = $resultSearch;
            }
        }

    } else if ($searchParam == "" && ($typeParam != "all")) {

        $stmtSearch = $conn->prepare("SELECT * FROM books WHERE type = ?");
        $stmtSearch->bind_param("s", $typeParam);
        $stmtSearch->execute();
        $resultSearch = $stmtSearch->get_result();

        $result = $resultSearch;
        
    } else {

        $result = $bookResult;

    }
} else {
    $result = $bookResult;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/search-book.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <script defer src="../js/search-book.js"></script>
    <title>Student | Search</title>
</head>

<body>
    <header class="search-book-header">
        <button onclick="window.location.href = './dashboard.php'" class="search-book-header-back-btn"><img src="../assets/arrow-back.svg"> Back</button>

        <div class="search-main-container search-book-search-bar">
            <form action="" method="GET" id="search-form">
                <div class="search-input-container">
                    <select name="type" class="select-style">
                        <option value="all">All</option>
                        <option value="academic">Academic</option>
                        <option value="non-academic">Non-academic</option>
                    </select>
                    <input
                        type="search"
                        name="search"
                        value="<?php if (isset($_GET['search'])) {
                                    echo $_GET['search'];
                                } ?>" class="form-control input-style" placeholder="Search Books">
                    <button type="submit" class="cta-btn-primary">Search</button>
                </div>
            </form>
            <p class="search-no-result"><?php echo $noResult ?></p>
        </div>

    </header>
    <main>
        <div class="search-result-container">
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <?php try { ?>
                        <?php
                        // Get the number of copies for each book
                        $copiesStmt = $conn->prepare("SELECT COUNT(*) as total FROM books_copy WHERE bookRef = ?");
                        $copiesStmt->bind_param("s", $row['id']);
                        $copiesStmt->execute();
                        $copies = $copiesStmt->get_result()->fetch_assoc();
                        $copiesRow = $copies['total'];

                        // Get the available number of book copies that can be borrow
                        $availableStmt = $conn->prepare("SELECT COUNT(*) as available FROM books_copy WHERE bookRef =? AND status = 'available'");
                        $availableStmt->bind_param("s", $row['id']);
                        $availableStmt->execute();
                        $available = $availableStmt->get_result()->fetch_assoc();
                        $availableRow = $available['available'];

                        $escaped_string = json_encode($row['description'], JSON_HEX_QUOT | JSON_HEX_APOS);
                        $escaped_string = substr($escaped_string, 1, -1);
                        ?>

                        <div class="search-result-card" onclick="onClickBook('<?= $row['id'] ?>', '<?= $row['title'] ?>', '<?= $row['author'] ?>', '<?= $row['type'] ?>', '<?= $row['genre'] ?>', '<?= $escaped_string ?>', '<?= $availableRow ?>', '<?= $copiesRow ?>', '<?php echo htmlspecialchars($row['format']) ?>')">
                            <h1 class="search-result-card-title"><?= htmlspecialchars($row['title']) ?></h1>
                            <div class="search-result-card-info">
                                <h2>Type: <?= htmlspecialchars($row['type']) ?></h2>
                                <h3><?= htmlspecialchars($row['genre']) ?></h3>
                                <h4>Available <?= htmlspecialchars($availableRow) . "/" . htmlspecialchars($copiesRow) ?></h4>
                            </div>
                        </div>

                    <?php } catch (Exception $e) { ?>
                        <?php echo "Error: " . $e->getMessage(); ?>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <?php echo '<div><p>0 result</p></div>'; ?>
            <?php } ?>
        </div>

        <aside class="search-book-sidebar-form">
            <form action="./request.php" method="post">
                <input type="hidden" name="bookId" id="bookId" readonly>
                <h1 class="search-book-sidebar-title" id="bookTitle"></h1>
                <div class="search-book-sidebar-info-container">
                    <div class="search-book-sidebar-about">
                        <h1>books about</h1>
                        <p id="bookAbout"></p>
                    </div>
                    <div class="search-book-sidebar-author">
                        <h1>author: </h1>
                        <p id="bookAuthor"></p>
                    </div>
                    <div class="search-book-sidebar-type">
                        <h1>type: </h1>
                        <p id="bookType"></p>
                    </div>
                    <div class="search-book-sidebar-genre">
                        <h1>genre: </h1>
                        <p id="bookGenre"></p>
                    </div>
                    <div class="search-book-sidebar-available">
                        <h1>available copies: </h1>
                        <p><span id="bookAvailable"></span>/<span id="bookTotal"></span></p>
                    </div>
                    <div class="search-book-sidebar-format">
                        <h1>format: </h1>
                        <p id="bookFormat"></p>
                    </div>
                </div>
                <div class="search-book-sidebar-note-container" style="display: <?php echo $countBorrowedBooksRow["total"] == 15 ? "block" : "none" ?>;">
                    <p>You can only borrow a maximum of 15 books.</p>
                </div>
                <div class="search-book-sidebar-btn-container" style="display: <?php echo $countBorrowedBooksRow["total"] == 15 ? "none" : "block" ?>;">
                    <button class="cta-btn-primary search-book-sidebar-btn-request" type="submit" id="requestBtn" disabled>request</button>
                    <button class="ghost-btn search-book-sidebar-btn-wait-list" type="button" onclick="waitList(event, '<?= $_COOKIE['student'] ?>')" id="waitListBtn" disabled>add to wait list</button>
                </div>
            </form>
        </aside>

    </main>
</body>

</html>