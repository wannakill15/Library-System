<?php


// Include necessary files
require_once '..\config\db.php';
include 'search_book.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../login.php');
//     exit();
// }

// Fetch user's borrowed books and fines
$user_id = $_SESSION['user_id'];

// Get total number of borrowed books
$borrowed_stmt = $conn->prepare("SELECT COUNT(*) as total_borrowed FROM BorrowingTransactions WHERE UserID = ? AND ReturnDate IS NULL");
$borrowed_stmt->bind_param("i", $user_id);
$borrowed_stmt->execute();
$borrowed_result = $borrowed_stmt->get_result();
$borrowed_count = $borrowed_result->fetch_assoc()['total_borrowed'];

// Get total fines
$fines_stmt = $conn->prepare("SELECT SUM(Fine) as total_fines FROM BorrowingTransactions WHERE UserID = ?");
$fines_stmt->bind_param("i", $user_id);
$fines_stmt->execute();
$fines_result = $fines_stmt->get_result();
$total_fines = $fines_result->fetch_assoc()['total_fines'] ?? 0;

// Fetch recent borrowed books
$recent_books_stmt = $conn->prepare("
    SELECT lr.Title, bt.BorrowDate, bt.DueDate 
    FROM BorrowingTransactions bt
    JOIN LibraryResources lr ON bt.BookID = lr.ResourceID
    WHERE bt.UserID = ? 
    ORDER BY bt.BorrowDate DESC 
    LIMIT 5
");
$recent_books_stmt->bind_param("i", $user_id);
$recent_books_stmt->execute();
$recent_books = $recent_books_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin: 5px 0;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #e0e0e0;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
        .dashboard-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 15px;
            width: 30%;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .search-container {
            margin-bottom: 20px;
        }
        .recent-books table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-books th, .recent-books td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Library Menu</h2>
        <a href="index.php">Dashboard</a>
        <a href="search_book.php">Search Books</a>
        <a href="borrowing.php">Borrowed Books</a>
        <a href="user_password.php">Change Password</a>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Welcome to Library Dashboard</h1>

        <!-- Dashboard Statistics -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Borrowed Books</h3>
                <p><?php echo $borrowed_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Fines</h3>
                <p>$<?php echo number_format($total_fines, 2); ?></p>
            </div>
            <div class="stat-card">
                <h3>Available Books</h3>
                <p>-</p>
            </div>
        </div>

        <!-- Book Search -->
        <div class="search-container">
            <h2>Search Books</h2>
            <form action="search_filter.php" method="POST">
                <input type="text" name="search_term" placeholder="Search books by title, author, or ISBN" style="width: 70%; padding: 10px;">
                <select name="category" style="padding: 10px;">
                    <option value="">All Categories</option>
                    <option value="Fiction">Fiction</option>
                    <option value="Non-Fiction">Non-Fiction</option>
                    <option value="Science">Science</option>
                    <option value="History">History</option>
                </select>
                <input type="submit" name="search_available_books" value="Search" style="padding: 10px;">
            </form>
        </div>

        <!-- Recently Borrowed Books -->
        <div class="recent-books">
            <h2>Recently Borrowed Books</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($book = $recent_books->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['Title']); ?></td>
                        <td><?php echo htmlspecialchars($book['BorrowDate']); ?></td>
                        <td><?php echo htmlspecialchars($book['DueDate']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>