<?php
include 'reporting.php'
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Reporting</title>
</head>
<body>
    <h1>Library Reporting</h1>

    <h2>Generate Borrowing History Report</h2>
    <form method="POST" action="reporting.php">
        <input type="text" name="user_id" placeholder="Enter User ID" required>
        <button type="submit">Get Borrowing History</button>
    </form>

    <?php if (!empty($borrowingHistory)): ?>
        <h3>Borrowing History</h3>
        <table border="1">
            <tr>
                <th>Transaction ID</th>
                <th>Book Title</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Fine</th>
            </tr>
            <?php foreach ($borrowingHistory as $record): ?>
            < tr>
                <td><?php echo $record['TransactionID']; ?></td>
                <td><?php echo $record['Title']; ?></td>
                <td><?php echo $record['BorrowDate']; ?></td>
                <td><?php echo $record['DueDate']; ?></td>
                <td><?php echo $record['ReturnDate'] ? $record['ReturnDate'] : 'Not Returned'; ?></td>
                <td><?php echo $record['Fine']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Identify Popular Books</h2>
    <form method="POST" action="reporting.php">
        <button type="submit" name="popular_books">Get Popular Books</button>
    </form>

    <?php if (!empty($popularBooks)): ?>
        <h3>Popular Books</h3>
        <table border="1">
            <tr>
                <th>Book Title</th>
                <th>Borrow Count</th>
            </tr>
            <?php foreach ($popularBooks as $book): ?>
            <tr>
                <td><?php echo $book['Title']; ?></td>
                <td><?php echo $book['BorrowCount']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>List Overdue Books</h2>
    <form method="POST" action="reporting.php">
        <button type="submit" name="overdue_books">Get Overdue Books</button>
    </form>

    <?php if (!empty($overdueBooks)): ?>
        <h3>Overdue Books</h3>
        <table border="1">
            <tr>
                <th>Transaction ID</th>
                <th>Book Title</th>
                <th>User Name</th>
                <th>Membership ID</th>
                <th>Due Date</th>
                <th>Fine</th>
            </tr>
            <?php foreach ($overdueBooks as $overdue): ?>
            <tr>
                <td><?php echo $overdue['TransactionID']; ?></td>
                <td><?php echo $overdue['Title']; ?></td>
                <td><?php echo $overdue['Name']; ?></td>
                <td><?php echo $overdue['MembershipID']; ?></td>
                <td><?php echo $overdue['DueDate']; ?></td>
                <td><?php echo $overdue['Fine']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Create Inventory Summary</h2>
    <form method="POST" action="reporting.php">
        <button type="submit" name="inventory_summary">Get Inventory Summary</button>
    </form>

    <?php if (!empty($inventorySummary)): ?>
        <h3>Inventory Summary</h3>
        <table border="1">
            <tr>
                <th>Category</th>
                <th>Total Books</th>
                <th>Borrowed Books</th>
            </tr>
            <?php foreach ($inventorySummary as $summary): ?>
            <tr>
                <td><?php echo $summary['Category']; ?></td>
                <td><?php echo $summary['TotalBooks']; ?></td>
                <td><?php echo $summary['BorrowedBooks']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>