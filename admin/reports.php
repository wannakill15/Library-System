<?php
include 'reporting.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    echo "You are not logged in. Please log in first.";
    header('Location: ..\login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Reporting</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="container mx-auto bg-white shadow-md rounded-lg p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">Library Reporting</h1>
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                Return to Dashboard
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Generate Borrowing History Report</h2>
                <form method="POST" action="reporting.php" class="space-y-4">
                    <input type="text" name="user_id" placeholder="Enter User ID" required 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-md transition duration-300">
                        Get Borrowing History
                    </button>
                </form>

                <?php if (!empty($borrowingHistory)): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Borrowing History</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white border rounded-lg">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="p-3 text-left">Transaction ID</th>
                                        <th class="p-3 text-left">Book Title</th>
                                        <th class="p-3 text-left">Borrow Date</th>
                                        <th class="p-3 text-left">Due Date</th>
                                        <th class="p-3 text-left">Return Date</th>
                                        <th class="p-3 text-left">Fine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($borrowingHistory as $record): ?>
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="p-3"><?php echo htmlspecialchars($record['TransactionID']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($record['Title']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($record['BorrowDate']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($record['DueDate']); ?></td>
                                        <td class="p-3"><?php echo $record['ReturnDate'] ? htmlspecialchars($record['ReturnDate']) : 'Not Returned'; ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($record['Fine']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="space-y-6">
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Identify Popular Books</h2>
                    <form method="POST" action="reporting.php" class="mb-4">
                        <button type="submit" name="popular_books" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-md transition duration-300">
                            Get Popular Books
                        </button>
                    </form>

                    <?php if (!empty($popularBooks)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white border rounded-lg">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="p-3 text-left">Book Title</th>
                                        <th class="p-3 text-left">Borrow Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($popularBooks as $book): ?>
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="p-3"><?php echo htmlspecialchars($book['Title']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($book['BorrowCount']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">List Overdue Books</h2>
                    <form method="POST" action="reporting.php" class="mb-4">
                        <button type="submit" name="overdue_books" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-md transition duration-300">
                            Get Overdue Books
                        </button>
                    </form>

                    <?php if (!empty($overdueBooks)): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white border rounded-lg">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="p-3 text-left">Transaction ID</th>
                                        <th class="p-3 text-left">Book Title</th>
                                        <th class="p-3 text-left">User Name</th>
                                        <th class="p-3 text-left">Membership ID</th>
                                        <th class="p-3 text-left">Due Date</th>
                                        <th class="p-3 text-left">Fine</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($overdueBooks as $overdue): ?>
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="p-3"><?php echo htmlspecialchars($overdue['TransactionID']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($overdue['Title']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($overdue['Name']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($overdue['MembershipID']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($overdue['DueDate']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($overdue['Fine']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-gray-50 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Create Inventory Summary</h2>
            <form method="POST" action="reporting.php" class="mb-4">
                <button type="submit" name="inventory_summary" 
                        class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 rounded-md transition duration-300">
                    Get Inventory Summary
                </button>
            </form>

            <?php if (!empty($inventorySummary)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full bg-white border rounded-lg">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="p-3 text-left">Category</th>
                                <th class="p-3 text-left">Total Books</th>
                                <th class="p-3 text-left">Borrowed Books</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventorySummary as $summary): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="p-3"><?php echo htmlspecialchars($summary['Category']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($summary['TotalBooks']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($summary['BorrowedBooks']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>