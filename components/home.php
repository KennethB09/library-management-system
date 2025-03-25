<?php

require "../utility/dp-connection.php";

function tableData($row, $copies)
{
    echo '<tr>';
    echo '<td>' . $row['title'] . '</td>';
    echo '<td>' . $row['genre'] . '</td>';
    echo '<td>' . $row['type'] . '</td>';
    echo '<td>' . $copies . '</td>';
    echo '</tr>';
}

$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM books");
$countStmt->execute();
$count = $countStmt->get_result()->fetch_assoc();
$counted = $count['total'];

$borrowStmt = $conn->prepare("SELECT COUNT(*) as total FROM borrowed_books");
$borrowStmt->execute();
$borrowed = $borrowStmt->get_result()->fetch_assoc();
$borrowedCount = $borrowed['total'];

$requestStmt = $conn->prepare("SELECT COUNT(*) as total FROM request_books");
$requestStmt->execute();
$requested = $requestStmt->get_result()->fetch_assoc();
$requestCount = $requested['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/admin-component-style.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <title>Dashboard | home</title>
</head>

<body>
    
    <main>
        <div class="card-container">
            <div class="card-books card">
                <h1>Books</h1>
                <div class="count-container">
                    <div class="count">
                        <span class="number"><?php echo $counted; ?></span>
                        <span class="total">TOTAL</span>
                    </div>
                </div>
            </div>
            <div class="card-borrow card">
                <h1>Borrow</h1>
                <div class="count-container">
                    <div class="count">
                        <span class="number"><?php echo $borrowedCount; ?></span>
                        <span class="total">TOTAL</span>
                    </div>
                </div>
            </div>
            <div class="card-request card">
                <h1>Request</h1>
                <div class="count-container">
                    <div class="count">
                        <span class="number"><?php echo $requestCount; ?></span>
                        <span class="total">TOTAL</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-container">
            <h1>Book List</h1>
            <div class="table">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>Type</th>
                        <th>Copies</th>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT id, title, genre, type FROM books");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                try {
                                    $copiesStmt = $conn->prepare("SELECT COUNT(*) as total FROM books_copy WHERE bookRef = ?");
                                    $copiesStmt->bind_param("s", $row['id']);
                                    $copiesStmt->execute();
                                    $copies = $copiesStmt->get_result()->fetch_assoc();
                                    $copiesRow = $copies['total'];
                                    tableData($row, $copiesRow);
                                } catch (Exception $e) {
                                    echo "Error: " . $e->getMessage();
                                }
                            }
                        } else {
                            echo '<tr class="table-row-no-result"><td colspan="4">No Books Available</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>
<?php $conn->close(); ?>