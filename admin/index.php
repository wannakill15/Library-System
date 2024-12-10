<?php
require_once '..\config\db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    echo "You are not logged in. Please log in first.";
    header('Location: ..\login.php');
}


try {
    // Fetch total books
    $bookStmt = $pdo->query("SELECT COUNT(*) as total_books FROM books");
    $totalBooks = $bookStmt->fetchColumn();

    // Fetch total users
    $userStmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $totalUsers = $userStmt->fetchColumn();

    // Fetch books borrowed today
    $borrowedStmt = $pdo->query("SELECT COUNT(*) as books_borrowed FROM book_transactions WHERE DATE(BorrowDate) = CURDATE()");
    $booksBorrowedToday = $borrowedStmt->fetchColumn();

    // Fetch overdue books
    $overdueStmt = $pdo->query("SELECT COUNT(*) as overdue_books FROM book_transactions WHERE DueDate < CURDATE() AND ReturnDate IS NULL");
    $overdueBooks = $overdueStmt->fetchColumn();
} catch (PDOException $e) {
    // Handle any database errors
    $totalBooks = $totalUsers = $booksBorrowedToday = $overdueBooks = 0;
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-600 text-white h-screen p-5 fixed left-0 top-0 bottom-0 overflow-y-auto">
        <h2 class="text-2xl font-bold mb-10">Library Management</h2>
        
        <nav>
            <h3 class="text-lg font-semibold mb-4">Reports</h3>
            <ul>
                <li class="mb-2">
                    <a href="reports.php?action=borrowing_history" class="hover:bg-blue-700 p-2 block rounded">
                        Borrowing History
                    </a>
                </li>
                <li class="mb-2">
                    <a href="reports.php?action=popular_books" class="hover:bg-blue-700 p-2 block rounded">
                        Popular Books
                    </a>
                </li>
                <li class="mb-2">
                    <a href="reports.php?action=overdue_books" class="hover:bg-blue-700 p-2 block rounded">
                        Overdue Books
                    </a>
                </li>
                <li class="mb-2">
                    <a href="reports.php?action=inventory_summary" class="hover:bg-blue-700 p-2 block rounded">
                        Inventory Summary
                    </a>
                </li>
            </ul>
            
            <h3 class="text-lg font-semibold mt-6 mb-4">Management</h3>
            <ul>
                <li class="mb-2">
                    <a href="book.php" class="hover:bg-blue-700 p-2 block rounded">
                        Book Management
                    </a>
                </li>
                <li class="mb-2">
                    <a href="user.php" class="hover:bg-blue-700 p-2 block rounded">
                        User Management
                    </a>
                </li>
                <li class="mb-2">
                    <a href="..\logout.php" class="hover:bg-blue-700 p-2 block rounded">
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 flex-1 p-10">
        <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Total Books</h2>
                <p class="text-4xl font-bold text-blue-600"><?php echo htmlspecialchars($totalBooks); ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Total Users</h2>
                <p class="text-4xl font-bold text-green-600"><?php echo htmlspecialchars($totalUsers); ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Books Borrowed Today</h2>
                <p class="text-4xl font-bold text-purple-600"><?php echo htmlspecialchars($booksBorrowedToday); ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Overdue Books</h2>
                <p class="text-4xl font-bold text-red-600"><?php echo htmlspecialchars($overdueBooks); ?></p>
            </div>
        </div>
    </div>
</body>
</html>