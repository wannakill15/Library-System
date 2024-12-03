<?php

include 'borrowing_management.php'
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Borrowing Transactions</title>
</head>
<body>
    <h1>Books You Borrowed</h1>
    <table border="1">
        <tr>
            <th>Transaction ID</th>
            <th>Title</th>
            <th>Borrow Date</th>
            <th>Due Date</th>
            <th>Return Date</th>
            <th>Fine</th>
        </tr>
        <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?php echo htmlspecialchars($transaction['TransactionID']); ?></td>
            <td><?php echo htmlspecialchars($transaction['Title']); ?></td>
            <td><?php echo htmlspecialchars($transaction['BorrowDate']); ?></td>
            <td><?php echo htmlspecialchars($transaction['DueDate']); ?></td>
            <td><?php echo htmlspecialchars($transaction['ReturnDate']); ?></td>
            <td><?php echo htmlspecialchars($transaction['Fine']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
