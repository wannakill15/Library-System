<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
    <style>
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php
    // Include the book management logic file
    include 'book_management.php';
    
    // If $books is still not defined, initialize it as an empty array
    if (!isset($books)) {
        $books = [];
    }
    ?>

    <h1>Book Management</h1>

    <?php
    // Display status messages
    if (isset($_GET['status']) && isset($_GET['message'])) {
        $status = $_GET['status'];
        $message = urldecode($_GET['message']);
        $cssClass = ($status === 'success') ? 'success-message' : 'error-message';
        echo "<div class='$cssClass'>$message</div>";
    }
    ?>

    <h2>Add/Edit Book</h2>
    <form method="POST" action="book_management.php">
        <input type="hidden" name="book_id" value="">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="text" name="isbn" placeholder="ISBN" required>
        <input type="text" name="publisher" placeholder="Publisher">
        <input type="text" name="edition" placeholder="Edition">
        <input type="date" name="publication_date" placeholder="Publication Date">
        <button type="submit" name="add">Add Book</button>
        <button type="submit" name="edit" style="display:none;" id="editButton">Edit Book</button>
    </form>

    <h2>Books List</h2>
    <table border="1">
        <tr>
            <th>Book ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Publisher</th>
            <th>Edition</th>
            <th>Publication Date</th>
            <th>Accession Number</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($books as $book): ?>
        <tr>
            <td><?php echo htmlspecialchars($book['BookID']); ?></td>
            <td><?php echo htmlspecialchars($book['Title']); ?></td>
            <td><?php echo htmlspecialchars($book['Author']); ?></td>
            <td><?php echo htmlspecialchars($book['ISBN']); ?></td>
            <td><?php echo htmlspecialchars($book['Publisher']); ?></td>
            <td><?php echo htmlspecialchars($book['Edition']); ?></td>
            <td><?php echo htmlspecialchars($book['PublicationDate']); ?></td>
            <td><?php echo htmlspecialchars($book['AccessionNumber']); ?></td>
            <td>
                <form method="POST" action="book_management.php" style="display:inline;">
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['BookID']); ?>">
                    <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this book?');">Delete</button>
                </form>
                <button onclick="editBook(<?php echo htmlspecialchars(json_encode($book)); ?>)">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function editBook(book) {
            // Set form values
            document.querySelector('input[name="book_id"]').value = book.BookID;
            document.querySelector('input[name="title"]').value = book.Title;
            document.querySelector('input[name="author"]').value = book.Author;
            document.querySelector('input[name="isbn"]').value = book.ISBN;
            document.querySelector('input[name="publisher"]').value = book.Publisher;
            document.querySelector('input[name="edition"]').value = book.Edition;
            document.querySelector('input[name="publication_date"]').value = book.PublicationDate;

            // Show edit button and change add button behavior
            document.getElementById('editButton').style.display = 'inline';
            document.querySelector('button[name="add"]').style.display = 'none';
        }
    </script>
</body>
</html>