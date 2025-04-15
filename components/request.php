<?php

require "../utility/dp-connection.php";

$stmt = $conn->prepare("SELECT id, bookRef, requestOn, dueDate, requesterId FROM request_books");
$stmt->execute();
$requestResult = $stmt->get_result();

$resultSearch;
$result = $requestResult;
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

        $stmtSearch = $conn->prepare("SELECT * FROM request_books WHERE id = ?");
        $stmtSearch->bind_param("s", $searchParam);
        $stmtSearch->execute();
        $resultSearch = $stmtSearch->get_result();

        if ($resultSearch->num_rows === 0) {
            $noResult = "No borrowed book with ID " . $searchParam . " found.";
        } else {
            $result = $resultSearch;
        }
    } else {
        $result = $requestResult;
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
    <script src="../js/admin-dashboard-request.js"></script>
    <title>Dashboard | request</title>
</head>

<body>
    <main>

        <div class="search-main-container">
            <form action="" method="GET" id="search-form-request">
                <div class="search-input-container">
                    <input
                        type="search"
                        name="search"
                        value="<?php if (isset($_GET['search'])) {
                                    echo $_GET['search'];
                                } ?>" class="form-control input-style" placeholder="Search Requested Books ID">
                    <button type="submit" class="cta-btn-primary">Search</button>
                </div>
            </form>
            <p class="search-no-result"><?php echo $noResult ?></p>
        </div>

        <div class="table-container request-table-container">

            <div class="request-table table">
                <table>
                    <thead>
                        <th>id</th>
                        <th>Requester ID</th>
                        <th>Book Title</th>
                        <th>Request On</th>
                        <th>Due Date</th>
                    </thead>
                    <tbody id="table-body">

                        <?php if ($result->num_rows > 0) { ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <?php try { ?>
                                    <?php

                                    $bookTitleStmt = $conn->prepare("SELECT id, title, format FROM books WHERE id = ?");
                                    $bookTitleStmt->bind_param("s", $row['bookRef']);
                                    $bookTitleStmt->execute();
                                    $title = $bookTitleStmt->get_result()->fetch_assoc();

                                    $requestedOn = date("Y-m-d h:i:s A", strtotime($row['requestOn']));
                                    $dueDate = date("Y-m-d h:i:s A", strtotime($row['dueDate']));
                                    ?>
                                    <tr class="table-row">
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['requesterId']) ?></td>
                                        <td><?= htmlspecialchars($title['title']) ?></td>
                                        <td><?= htmlspecialchars($requestedOn) ?></td>
                                        <td><?= htmlspecialchars($dueDate) ?></td>
                                        <td class="table-row-approve"><button class="cta-btn-primary" onclick="onApprove(event, '<?= $row['id'] ?>', '<?= $title['id'] ?>', '<?= htmlspecialchars($row['dueDate']) ?>', '<?= $row['requesterId'] ?>', '<?= $title['format'] ?>')">Approve</button></td>
                                        <td class="table-row-decline"><button class="cta-btn-secondary" onclick="onDecline(event, '<?= $row['id'] ?>')">Decline</button></td>
                                    </tr>

                                <?php } catch (Exception $e) { ?>
                                    <?php echo "Error: " . $e->getMessage(); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo '<tr class="table-row-no-result"><td colspan="7">No Students Book Request Available</td></tr>'; ?>
                        <?php } ?>

                    </tbody>
                </table>
            </div>

        </div>

    </main>
</body>

</html>