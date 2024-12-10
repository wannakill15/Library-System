<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    die("You must be logged in to access this page.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <?php
        // Include the book management logic file
        include 'book_management.php';
        
        // If $books is still not defined, initialize it as an empty array
        if (!isset($books)) {
            $books = [];
        }
        ?>

        <div class="bg-white shadow-md rounded-lg">
            <div class="bg-blue-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold">Book Management</h1>
                <a href="index.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-100 transition">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </div>

            <?php
            // Display status messages
            if (isset($_GET['status']) && isset($_GET['message'])) {
                $status = $_GET['status'];
                $message = urldecode($_GET['message']);
                $cssClass = ($status === 'success') 
                    ? 'bg-green-100 border-green-400 text-green-700' 
                    : 'bg-red-100 border-red-400 text-red-700';
                echo "<div class='$cssClass p-4 rounded-b-lg'>$message</div>";
            }
            ?>

            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Add/Edit Book</h2>
                <form method="POST" action="book_management.php" class="grid grid-cols-2 gap-4">
                    <input type="hidden" name="book_id" value="">
                    <input type="text" name="title" placeholder="Title" required 
                           class="border rounded px-3 py-2 col-span-2 w-full">
                    <input type="text" name="author" placeholder="Author" required 
                           class="border rounded px-3 py-2">
                    <input type="text" name="isbn" placeholder="ISBN" required 
                           class="border rounded px-3 py-2">
                    <input type="text" name="publisher" placeholder="Publisher" 
                           class="border rounded px-3 py-2">
                    <input type="text" name="edition" placeholder="Edition" 
                           class="border rounded px-3 py-2">
                    <input type="date" name="publication_date" placeholder="Publication Date" 
                           class="border rounded px-3 py-2">
                    <div class="col-span-2 flex space-x-4">
                        <button type="submit" name="add" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i>Add Book
                        </button>
                        <button type="submit" name="edit" id="editButton" 
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition" 
                                style="display:none;">
                            <i class="fas fa-edit mr-2"></i>Update Book
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Books List</h2>
                <div class="overflow-x-auto">
                    <table class="w-full bg-white border">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left">Book ID</th>
                                <th class="px-4 py-2 text-left">Title</th>
                                <th class="px-4 py-2 text-left">Author</th>
                                <th class="px-4 py-2 text-left">ISBN</th>
                                <th class="px-4 py-2 text-left">Publisher</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($book['BookID']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($book['Title']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($book['Author']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($book['ISBN']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($book['Publisher']); ?></td>
                                <td class="px-4 py-2 flex space-x-2">
                                    <form method="POST" action="book_management.php" style="display:inline;">
                                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['BookID']); ?>">
                                        <button type="submit" name="delete" 
                                                onclick="return confirm('Are you sure you want to delete this book?');"
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </button>
                                    </form>
                                    <button onclick="editBook(<?php echo htmlspecialchars(json_encode($book)); ?>)"
                                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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

            // Show edit button and hide add button
            document.getElementById('editButton').style.display = 'inline-block';
            document.querySelector('button[name="add"]').style.display = 'none';
        }
    </script>
</body>
</html>