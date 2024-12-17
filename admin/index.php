<?php
require_once '..\config\db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    header('Location: ..\login.php');
    exit();
}

try {
    // Fetch total books from libraryresources
    $bookStmt = $pdo->query("SELECT COUNT(*) as total_books FROM libraryresources WHERE Category = 'Book'");
    $totalBooks = $bookStmt->fetchColumn();

    // Fetch total users
    $userStmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $totalUsers = $userStmt->fetchColumn();

    // Fetch books borrowed today from borrowingtransactions
    $borrowedStmt = $pdo->query("SELECT COUNT(*) as books_borrowed FROM borrowingtransactions WHERE DATE(BorrowDate) = CURDATE()");
    $booksBorrowedToday = $borrowedStmt->fetchColumn();

    // Fetch overdue books from borrowingtransactions
    $overdueStmt = $pdo->query("SELECT COUNT(*) as overdue_books FROM borrowingtransactions WHERE DueDate < CURDATE() AND ReturnDate IS NULL");
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-blue-600 to-blue-800 text-white h-screen p-5 fixed left-0 top-0 bottom-0 overflow-y-auto shadow-lg">
        <div class="flex items-center mb-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h2 class="text-2xl font-bold">Library Hub</h2>
        </div>
        
        <nav>
            <h3 class="text-lg font-semibold mb-4 border-b border-blue-500 pb-2">Reports</h3>
            <ul class="space-y-2">
                <li>
                    <a href="reports.php?action=borrowing_history" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        Borrowing History
                    </a>
                </li>
                <li>
                    <a href="reports.php?action=popular_books" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                        Popular Books
                    </a>
                </li>
                <li>
                    <a href="reports.php?action=overdue_books" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Overdue Books
                    </a>
                </li>
                <li>
                    <a href="reports.php?action=inventory_summary" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                        Inventory Summary
                    </a>
                </li>
            </ul>
            
            <h3 class="text-lg font-semibold mt-6 mb-4 border-b border-blue-500 pb-2">Management</h3>
            <ul class="space-y-2">
                <li>
                    <a href="book.php" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        Book Management
                    </a>
                </li>
                <li>
                    <a href="media.php" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1a1 1 0 001.894-.448v-6a1 1 0 00-1.894-.448l-2 1z"/>
                        </svg>
                        Media Resources
                    </a>
                </li>
                <li>
                    <a href="periodical.php" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                            <path d="M15 7h1a2 2 0 012 2v6a2 2 0 01-2 2h-1V7z"/>
                        </svg>
                        Periodicals
                    </a>
                </li>
                <li>
                    <a href="user.php" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0113 16.69V17h-.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        User Management
                    </a>
                </li>
                <li>
                    <a href="fines.php" class="flex items-center hover:bg-blue-700 p-2 rounded transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/>
                            <path d="M16 4a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4zm-6 12H6v-2h4v2zm6-4H6V6h10v6z"/>
                        </svg>
                        Fine Management
                    </a>
                </li>
                <li>
                    <a href="..\logout.php" class="flex items-center hover:bg-red-700 p-2 rounded transition duration-300 text-red-300 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L14.586 11H7a1 1 0 110-2h7.586l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
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