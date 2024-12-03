<?php
session_start();

// Ensure user is logged in (add your authentication check)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Book Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .search-container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .search-form input, 
        .search-form select {
            padding: 10px;
            flex-grow: 1;
        }
        .book-results {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .book-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .book-card h3 {
            margin-top: 0;
            color: #333;
        }
        .no-results {
            text-align: center;
            color: #666;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <form action="search_filter.php" method="POST" class="search-form">
            <input type="text" name="search_term" placeholder="Search books by title, author, or ISBN" required>
            
            <select name="category">
                <option value="">All Categories</option>
                <option value="Fiction">Fiction</option>
                <option value="Non-Fiction">Non-Fiction</option>
                <option value="Reference">Reference</option>
                <option value="Science">Science</option>
                <option value="History">History</option>
                <option value="Technology">Technology</option>
            </select>
            
            <input type="hidden" name="search_available_books" value="1">
            <button type="submit">Search Available Books</button>
        </form>
    </div>

    <div class="book-results">
        <?php
        if (isset($_SESSION['availableBooks'])) {
            $availableBooks = $_SESSION['availableBooks'];
            
            if (empty($availableBooks)) {
                echo "<div class='no-results'>
                        <p>No available books found matching your search criteria.</p>
                      </div>";
            } else {
                foreach ($availableBooks as $book) {
                    echo "<div class='book-card'>";
                    echo "<h3>" . htmlspecialchars($book['Title']) . "</h3>";
                    echo "<p><strong>Author:</strong> " . htmlspecialchars($book['Author']) . "</p>";
                    echo "<p><strong>ISBN:</strong> " . htmlspecialchars($book['ISBN']) . "</p>";
                    echo "<p><strong>Category:</strong> " . htmlspecialchars($book['Category']) . "</p>";
                    echo "<p><strong>Accession Number:</strong> " . htmlspecialchars($book['AccessionNumber']) . "</p>";
                    echo "</div>";
                }
            }
            
            // Clear the session after displaying
            unset($_SESSION['availableBooks']);
        }
        ?>
    </div>
</body>
</html>