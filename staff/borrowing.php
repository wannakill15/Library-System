<?php
 include 'borrowing_management.php';

 session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    echo "You are not logged in. Please log in first.";
    header('Location: ..\login.php');
 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowing Management</title>
</head>
<body>
    <h1>Borrowing Management</h1>

    <h2>Borrow Book</h2>
    <form method="POST" action="borrowing_management.php">
        <select name="book_id" required>
            <option value="">Select Book</option>
            <?php
            // Fetch and display books from the database
            $books = $pdo->query("SELECT b.BookID, lr.Title 
                                FROM Books b 
                                JOIN LibraryResources lr ON b.ResourceID = lr.ResourceID")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($books as $book) {
                echo "<option value=\"{$book['BookID']}\">{$book['Title']}</option>";
            }
            ?>
        </select>
        <input type="text" name="user_id" placeholder="User  ID" required>
        <button type="submit" name="borrow">Borrow Book</button>
    </form>

    <h2>Return Book</h2>
    <form method="POST" action="borrowing_management.php">
        <select name="transaction_id" required>
            <option value="">Select Transaction</option>
            <?php
            // Fetch and display borrowing transactions from the database
            $transactions = getAllBorrowingTransactions();
            foreach ($transactions as $transaction) {
                echo "<option value=\"{$transaction['TransactionID']}\">Transaction ID: {$transaction['TransactionID']} - {$transaction['Title']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="return">Return Book</button>
    </form>

    <h2>Borrowing Transactions List</h2>
    <table border="1">
        <tr>
            <th>Transaction ID</th>
            <th>Book Title</th>
            <th>Borrower</th>
            <th>Borrow Date</th>
            <th>Due Date</th>
            <th>Return Date</th>
            <th>Fine</th>
        </tr>
        <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?php echo $transaction['TransactionID']; ?></td>
            <td><?php echo $transaction['Title']; ?></td>
            <td><?php echo $transaction['Borrower']; ?></td>
            <td><?php echo $transaction['BorrowDate']; ?></td>
            <td><?php echo $transaction['DueDate']; ?></td>
            <td><?php echo $transaction['ReturnDate'] ? $transaction['ReturnDate'] : 'Not Returned'; ?></td>
            <td><?php echo $transaction['Fine'] ? '$' . number_format($transaction['Fine'], 2) : 'No Fine'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>