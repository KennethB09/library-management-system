<?php
require "../utility/dp-connection.php";

$resultSearch;
$result;
$noResult = "";

$stmt = $conn->prepare("SELECT id, title, author, genre, type, description, format FROM books");
$stmt->execute();
$resultTotal = $stmt->get_result();

$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM books");
$countStmt->execute();
$booksCount = $countStmt->get_result()->fetch_assoc();
$countRow = $booksCount['total'];

if (isset($_GET['search'])) {
    $searchParam = isset($_GET['search']) ? $_GET['search'] : '';
    $typeParam = $_GET['type'];

    if ($searchParam != "" && $typeParam == "all") {

        $stmtSearch = $conn->prepare("SELECT * FROM books WHERE title LIKE ?");
        $stmtSearch->bind_param("s", $searchParam);
        $stmtSearch->execute();
        $resultSearch = $stmtSearch->get_result();

        if ($resultSearch->num_rows === 0) {
            $noResult = "No " . $searchParam . " book found in " .  $typeParam;
        } else {
            $result = $resultSearch;
        }
    } else if ($searchParam != "" && $typeParam != "all") {

        $stmtSearch = $conn->prepare("SELECT * FROM books WHERE title LIKE ? AND type = ?");
        $stmtSearch->bind_param("ss", $searchParam, $typeParam);
        $stmtSearch->execute();
        $resultSearch = $stmtSearch->get_result();

        if ($resultSearch->num_rows === 0) {
            $noResult = "No " . $searchParam . " book found in " .  $typeParam;
        } else {
            $result = $resultSearch;
        }
    } else {

        if ($typeParam != "all") {
            $stmtSearch = $conn->prepare("SELECT * FROM books WHERE type = ?");
            $stmtSearch->bind_param("s", $typeParam);
            $stmtSearch->execute();
            $resultSearch = $stmtSearch->get_result();

            $result = $resultSearch;
        } else {
            $result = $resultTotal;
        }
    }
} else {
    $result = $resultTotal;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles/admin-component-style.css">
    <link rel="stylesheet" type="text/css" href="../styles/main.css">
    <script defer src="../js/admin-manage.js"></script>
    <script defer src="../js/admin-dashboard-crud.js"></script>
    <title>Dashboard | manage</title>
</head>

<body>
    <main>

        <div class="search-main-container">
            <form action="" method="GET" id="search-form-manage">
                <div class="search-input-container">
                    <select name="type" class="select-style">
                        <option value="all">All</option>
                        <option value="academic">Academic</option>
                        <option value="non-academic">Non-Academic</option>
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

        <div class="table-container manage-table-container">
            <div class="title-btn-container">
                <h1><?php echo $countRow ?> Books</h1>
                <div class="crud-btn-container">
                    <button onclick="openAddModal(true)"><img class="icon-clr" src="../assets/add.svg" /></button>
                    <button class="crud-btn-edit" onclick="openEditModal(true)" id="editButton" disabled data-active="false"><img class="icon-clr" src="../assets/pencil.svg" /></button>
                    <button class="crud-btn-delete" onclick="openDeleteModal(true)" id="deleteButton" disabled data-active="false"><img class="icon-clr" src="../assets/trash-outline.svg" /></button>
                </div>
            </div>
            <div class="manage-table table">
                <table>
                    <thead>
                        <th>Name</th>
                        <th>Genre</th>
                        <th>Type</th>
                        <th>Copies</th>
                    </thead>
                    <tbody id="table-body">

                        <?php if ($result->num_rows > 0) { ?>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <?php try { ?>
                                    <?php
                                    $copiesStmt = $conn->prepare("SELECT COUNT(*) as total FROM books_copy WHERE bookRef = ?");
                                    $copiesStmt->bind_param("s", $row['id']);
                                    $copiesStmt->execute();
                                    $copies = $copiesStmt->get_result()->fetch_assoc();
                                    $copiesRow = $copies['total'];

                                    $escaped_string = addslashes($row['description']);
                                    ?>

                                    <tr class="table-row" onclick="onClickBook('<?= $row['id'] ?>', '<?= $row['title'] ?>', '<?= $row['author'] ?>', '<?= $row['type'] ?>', '<?= $row['genre'] ?>', '<?= $copiesRow ?>', '<?= $escaped_string ?>', '<?= $row['format'] ?>')">
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= htmlspecialchars($row['genre']) ?></td>
                                        <td><?= htmlspecialchars($row['type']) ?></td>
                                        <td><?= $row["format"] === "digital" ? htmlspecialchars($row["format"]) : htmlspecialchars($copies['total']) ?></td>
                                    </tr>

                                <?php } catch (Exception $e) { ?>
                                    <?php echo "Error: " . $e->getMessage(); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php echo '<tr class="table-row-no-result"><td colspan="4">No Books in Database</td></tr>'; ?>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal" id="add-modal">
            <div class="modal-content">
                <div class="modal-title-btn-container">
                    <h1>Add new book</h1>
                    <button onclick="openAddModal(false)">
                        <img class="icon-clr" src="../assets/close.svg" />
                    </button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" method="post">
                    <div class="input-container title">
                        <label for="add-form-title">Title</label>
                        <input class="input-style" id="add-form-title" name="title" placeholder="Enter book title" required />
                    </div>
                    <div class="input-container author">
                        <label for="add-form-author">Author</label>
                        <input class="input-style" id="add-form-author" name="author" placeholder="Enter book author" required />
                    </div>
                    <div class="type-genre-copies-container">
                        <div class="input-container">
                            <label for="add-form-type">Type</label>
                            <select class="select-style" id="add-form-type" name="type" required>
                                <option value="academic">academic</option>
                                <option value="non-academic">non-academic</option>
                            </select>
                        </div>
                        <div class="input-container">
                            <label for="add-form-genre">Genre</label>
                            <select class="select-style" id="add-form-genre" name="genre" required>
                                <option value="fantasy">fantasy</option>
                                <option value="sci-fi">sci-fi</option>
                                <option value="romance">romance</option>
                                <option value="horror">horror</option>
                                <option value="triller">triller</option>
                                <option value="science">science</option>
                                <option value="psychology">psychology</option>
                                <option value="history">history</option>
                                <option value="lang_ref">language reference</option>
                            </select>
                        </div>
                        <div class="input-container" id="addFormCopiesInput">
                            <label for="add-form-copies">Copies</label>
                            <input class="input-style" id="add-form-copies" name="copies" type="number" placeholder="Enter book copies" />
                        </div>
                    </div>
                    <!--- upload --->
                    <div class="availableIn-ebook-container">
                        <div class="input-container">
                            <label for="formatAdd">Format</label>
                            <select class="select-style" name="format" id="formatAdd" required>
                                <option value="physical">physical</option>
                                <option value="digital">digital</option>
                                <option value="both">both</option>
                            </select>
                        </div>
                        <div class="input-container">
                            <label for="fileInputAdd">PDF</label>
                            <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                            <input class="input-style" type="file" id="fileInputAdd" name="ebook" accept=".pdf" disabled>
                        </div>
                    </div>
                    <!--- upload --->
                    <div class="input-container">
                        <label for="add-form-description">Book about</label>
                        <textarea id="add-form-description" name="description" class="form-textarea" placeholder="Enter book description" required autocomplete="off"></textarea>
                    </div>
                    <div class="btn-container">
                        <button type="button" class="cancel ghost-btn" onclick="openAddModal(false)">Cancel</button>
                        <button type="submit" class="save cta-btn-primary">save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="edit-modal">
            <div class="modal-content">
                <div class="modal-title-btn-container">
                    <h1>Edit book</h1>
                    <button onclick="openEditModal(false)">
                        <img class="icon-clr" src="../assets/close.svg" />
                    </button>
                </div>
                <form onsubmit="updateBook(event)" enctype="multipart/form-data" method="post" id="edit-form">
                    <input id="edit-form-bookId" name="id" hidden />
                    <div class="input-container title">
                        <label for="edit-form-title">Title</label>
                        <input class="input-style" id="edit-form-title" name="title" placeholder="Enter book title" required />
                    </div>
                    <div class="input-container author">
                        <label for="edit-form-author">Author</label>
                        <input class="input-style" id="edit-form-author" name="author" placeholder="Enter book author" required />
                    </div>
                    <div class="type-genre-copies-container">
                        <div class="input-container">
                            <label for="edit-form-type">Type</label>
                            <select class="select-style" id="edit-form-type" name="type" required>
                                <option value="academic">academic</option>
                                <option value="non-academic">non-academic</option>
                            </select>
                        </div>
                        <div class="input-container">
                            <label for="edit-form-genre">Genre</label>
                            <select class="select-style" id="edit-form-genre" name="genre" required>
                                <option value="fantasy">fantasy</option>
                                <option value="sci-fi">sci-fi</option>
                                <option value="romance">romance</option>
                                <option value="horror">horror</option>
                                <option value="triller">triller</option>
                                <option value="science">science</option>
                                <option value="psychology">psychology</option>
                                <option value="history">history</option>
                                <option value="lang_ref">language reference</option>
                            </select>
                        </div>
                        <div class="input-container">
                            <label for="edit-form-copies">Copies</label>
                            <input class="input-style" id="edit-form-copies" name="copies" type="number" placeholder="Enter book copies" required />
                        </div>
                    </div>
                    <div class="availableIn-ebook-container">
                        <div class="input-container">
                            <label for="formatEdit">Format</label>
                            <input class="input-style" type="text" name="format" id="formatEdit" readonly>
                        </div>
                        <div class="input-container" id="uploadInputContainer">
                            <label for="fileInputEdit">Upload PDF</label>
                            <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                            <input class="input-style" type="file" id="fileInputEdit" name="ebook" accept=".pdf">
                        </div>
                    </div>
                    <div class="input-container">
                        <label for="edit-form-des">Book about</label>
                        <textarea id="edit-form-des" class="form-textarea" name="description" placeholder="Enter book description" required></textarea>
                    </div>
                    <div class="btn-container">
                        <button type="button" class="cancel ghost-btn" onclick="openEditModal(false)">Cancel</button>
                        <button type="submit" class="save cta-btn-primary">save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal" id="delete-modal">
            <div class="modal-content">
                <div class="modal-title-btn-container">
                    <h1>Delete book</h1>
                    <button onclick="openDeleteModal(false)">
                        <img class="icon-clr" src="../assets/close.svg" />
                    </button>
                </div>
                <p>Are you sure you want to delete this book?</p>
                <div class="btn-container">
                    <button class="cancel ghost-btn" onclick="openDeleteModal(false)">Cancel</button>
                    <button class="save cta-btn-primary" onclick="deleteBook(event)">Yes</button>
                </div>
            </div>
        </div>
    </main>
</body>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        if ((isset($_POST['format']) && ($_POST['format'] === "physical" || $_POST['format'] === "both")) &&
            (empty($_POST["copies"]) || (int)$_POST["copies"] <= 0)
        ) {
            throw new Exception("Please add at least one copy for physical books.");
        }

        $insertBookStmt = $conn->prepare("INSERT INTO books (title, type, genre, description, author, format) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$insertBookStmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $insertBookStmt->bind_param(
            "ssssss",
            $_POST["title"],
            $_POST["type"],
            $_POST["genre"],
            $_POST["description"],
            $_POST["author"],
            $_POST["format"]
        );

        $uploadDir = "../uploads/";
        $uploadFile = $uploadDir . basename($_FILES['ebook']['name']);

        if (isset($_POST['format']) && $_POST['format'] !== "physical") {


            if (move_uploaded_file($_FILES['ebook']['tmp_name'], $uploadFile)) {

                $insertBookStmt->execute();

                $bookId = $insertBookStmt->insert_id;

                $insertBookStmt->close();

                $uploadStmt = $conn->prepare("INSERT INTO uploads (bookRef, location, fileName) VALUES (?, ?, ?)");

                if (!$uploadStmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }

                $uploadStmt->bind_param("iss", $bookId, $uploadFile, $_FILES['ebook']['name']);

                if ($uploadStmt->execute()) {

                    $insertOneCopy = $conn->prepare("INSERT INTO books_copy (bookRef, format) VALUES (?, ?)");

                    if (!$insertOneCopy) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }

                    $insertOneCopy->bind_param("is", $bookId, $_POST['format']);

                    $insertOneCopy->execute();
                } else {
                    throw new Exception("Error inserting digital copy: " . $conn->error);
                }
            } else {
                $_SESSION['alert'] = [
                    'type' => 'warning',
                    'message' => 'Error: ' . $_FILES['ebook']['error']
                ];
                exit("Upload Error");
            }
        } else if (isset($_POST['format']) && $_POST['format'] === "both") {

            $num_copies = (int) $_POST["copies"];

            if ($insertBookStmt->execute()) {

                $bookId = $insertBookStmt->insert_id;

                $insertBookStmt->close();

                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                    $uploadStmt = $conn->prepare("INSERT INTO uploads (bookRef, location, fileName) VALUES (?, ?, ?)");
                    if (!$uploadStmt) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }
                    $uploadStmt->bind_param("iss", $bookId, $uploadFile, $_FILES['file']['name']);
                    $uploadStmt->execute();
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'warning',
                        'message' => 'Error: ' . $_FILES['file']['error']
                    ];
                    exit("Upload Error");
                }

                $values = [];
                for ($i = 0; $i < $num_copies; $i++) {
                    $values[] = "($bookId, 'available')";
                }

                $sql = "INSERT INTO books_copy (bookRef, status) VALUES " . implode(", ", $values);
                $conn->query($sql);

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "New record created successfully"
                ];
            } else {
                throw new Exception("Error executing statement: " . $insertBookStmt->error);
            }
        } else {
            $num_copies = (int) $_POST["copies"];

            if ($insertBookStmt->execute()) {

                $bookId = $insertBookStmt->insert_id;

                $insertBookStmt->close();

                $values = [];
                for ($i = 0; $i < $num_copies; $i++) {
                    $values[] = "($bookId, 'available')";
                }

                $sql = "INSERT INTO books_copy (bookRef, status) VALUES " . implode(", ", $values);
                $conn->query($sql);

                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => "New record created successfully"
                ];
            } else {
                throw new Exception("Error executing statement: " . $insertBookStmt->error);
            }
        }

        $conn->close();
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => "Error: " . $e->getMessage()
        ];
    }
}
?>

</html>