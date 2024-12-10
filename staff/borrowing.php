<?php
 include 'borrowing_management.php';

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
    <title>Borrowing Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-book-open mr-3"></i>Borrowing Management


                </h1>                    
                <div class="flex justify-end">
                    <a href="index.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-100 transition">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </div>
                
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Borrow Book Section -->
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <h2 class="text-xl font-semibold mb-4 text-blue-700">
                            <i class="fas fa-plus-circle mr-2"></i>Borrow Book
                        </h2>
                        <form method="POST" action="borrowing_management.php" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 mb-2">Select Book</label>
                                <select name="book_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Book</option>
                                    <?php
                                    // Existing book population logic
                                    $books = $pdo->query("SELECT b.BookID, lr.Title 
                                        FROM Books b 
                                        JOIN LibraryResources lr ON b.ResourceID = lr.ResourceID")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($books as $book) {
                                        echo "<option value=\"{$book['BookID']}\">{$book['Title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2">User ID</label>
                                <input type="text" name="user_id" placeholder="Enter User ID" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" name="borrow" 
                                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300 flex items-center justify-center">
                                <i class="fas fa-book mr-2"></i>Borrow Book
                            </button>
                        </form>
                    </div>

                    <!-- Return Book Section -->
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                        <h2 class="text-xl font-semibold mb-4 text-green-700">
                            <i class="fas fa-undo mr-2"></i>Return Book
                        </h2>
                        <form method="POST" action="borrowing_management.php" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 mb-2">Select Transaction</label>
                                <select name="transaction_id" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Transaction</option>
                                    <?php
                                    // Existing transaction population logic
                                    $transactions = getAllBorrowingTransactions();
                                    foreach ($transactions as $transaction) {
                                        echo "<option value=\"{$transaction['TransactionID']}\">Transaction ID: {$transaction['TransactionID']} - {$transaction['Title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="return" 
                                class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-300 flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i>Return Book
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Borrowing Transactions List -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">
                        <i class="fas fa-list-alt mr-2"></i>Borrowing Transactions
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fine</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($transactions as $transaction): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3"><?php echo $transaction['TransactionID']; ?></td>
                                    <td class="px-4 py-3"><?php echo $transaction['Title']; ?></td>
                                    <td class="px-4 py-3"><?php echo $transaction['Borrower']; ?></td>
                                    <td class="px-4 py-3"><?php echo $transaction['BorrowDate']; ?></td>
                                    <td class="px-4 py-3"><?php echo $transaction['DueDate']; ?></td>
                                    <td class="px-4 py-3">
                                        <?php echo $transaction['ReturnDate'] ? $transaction['ReturnDate'] : '<span class="text-yellow-600">Not Returned</span>'; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <?php 
                                        $fineAmount = $transaction['Fine'] ? '$' . number_format($transaction['Fine'], 2) : '<span class="text-green-600">No Fine</span>';
                                        echo $fineAmount;
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>