<?php

require "../utility/dp-connection.php";

$stmt = $conn->prepare("SELECT id, bookRef, borrowedOn, dueDate, borrower FROM borrowed_books");
$stmt->execute();
$borrowedResult = $stmt->get_result();

$resultSearch;
$result = $borrowedResult;
$noResult = "";

if (isset($_GET['search'])) {
    $searchParam = isset($_GET['search']) ? $_GET['search'] : '';

    if ($searchParam !== "") {
        if (isset($_GET['type'])) {
            $typeParam = $_GET['type'];
            echo $typeParam;
        } else {
            $filterType = 'academic';
        }

        $stmtSearch = $conn->prepare("SELECT * FROM borrowed_books WHERE id = ?");
        $stmtSearch->bind_param("s", $searchParam);
        $stmtSearch->execute();
        $resultSearch = $stmtSearch->get_result();

        if ($resultSearch->num_rows === 0) {
            $noResult = "No borrowed book with ID " . $searchParam . " found.";
        } else {
            $result = $resultSearch;
        }
    } else {
        $result = $borrowedResult;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/admin-component-style.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <script src="../js/admin-dashboard-borrow.js"></script>
    <title>Dashboard | borrowed</title>
</head>

<body>
    <main>

        <div class="search-main-container">
            <form action="" method="GET" id="search-form-borrowed">
                <div class="search-input-container">
                    <input
                        type="search"
                        name="search"
                        value="<?php if (isset($_GET['search'])) {
                                    echo $_GET['search'];
                                } ?>" class="form-control input-style" placeholder="Search Borrowed Books ID">
                    <button type="submit" class="cta-btn-primary">Search</button>
                </div>
            </form>
            <p class="search-no-result"><?php echo $noResult ?></p>
        </div>

        <div class="table-container borrow-table-container">

            <div class="borrow-table table">
                <table>
                    <thead>
                        <th>id</th>
                        <th>Borrower ID</th>
                        <th>Book Title</th>
                        <th>Borrowed On</th>
                        <th>Due Date</th>
                    </thead>
                    <tbody id="table-body">

                        <?php if ($result->num_rows > 0) { ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <?php try { ?>
                                    <?php
                                    $bookStmt = $conn->prepare("SELECT bookRef FROM books_copy WHERE id = ?");
                                    $bookStmt->bind_param("s", $row['bookRef']);
                                    $bookStmt->execute();
                                    $ref = $bookStmt->get_result()->fetch_assoc();

                                    $bookTitleStmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
                                    $bookTitleStmt->bind_param("s", $ref['bookRef']);
                                    $bookTitleStmt->execute();
                                    $title = $bookTitleStmt->get_result()->fetch_assoc();
                                    ?>
                                    <tr class="table-row">
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['borrower']) ?></td>
                                        <td><?= htmlspecialchars($title['title']) ?></td>
                                        <td><?= htmlspecialchars($row['borrowedOn']) ?></td>
                                        <td><?= htmlspecialchars($row['dueDate']) ?></td>
                                        <td class="table-row-returned"><button class="cta-btn-primary" onclick="onReturn(event, '<?= $row['id'] ?>', '<?= $row['bookRef'] ?>', '<?= $row['borrower'] ?>')">Returned</button></td>
                                    </tr>

                                <?php } catch (Exception $e) { ?>
                                    <?php echo "Error: " . $e->getMessage(); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo '<tr class="table-row-no-result"><td colspan="6">No Books Borrowed</td></tr>'; ?>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>
    </main>
</body>

</html>