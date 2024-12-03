<?php
include '..\config\db.php';

// Retrieve results from GET parameters
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$overdueBooks = isset($_GET['overdue_books']) ? json_decode($_GET['overdue_books'], true) : [];
$fineHistory = isset($_GET['fine_history']) ? json_decode($_GET['fine_history'], true) : [];
$paymentResult = isset($_GET['payment_result']) ? $_GET['payment_result'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fine Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        form {
            background-color: #f4f4f4;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Fine Management</h1>

    <h2>Check Overdue Books and Fines</h2>
    <form method="POST" action="fine_management.php">
        <input type="text" name="user_id" placeholder="Enter User ID" required value="<?php echo htmlspecialchars($userId ?? ''); ?>">
        <button type="submit" name="check_overdue">Check Overdue Books</button>
    </form>

    <?php if (!empty($paymentResult)): ?>
        <div class="message"><?php echo htmlspecialchars($paymentResult); ?></div>
    <?php endif; ?>

    <?php if (!empty($overdueBooks)): ?>
        <h3>Overdue Books</h3>
        <table>
            <tr>
                <th>Transaction ID</th>
                <th>Book Title</th>
                <th>Due Date</th>
                <th>Fine Amount</th>
            </tr>
            <?php 
            $totalFines = 0;
            foreach ($overdueBooks as $book): 
                $totalFines += $book['Fine'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars($book['TransactionID']); ?></td>
                <td><?php echo htmlspecialchars($book['Title']); ?></td>
                <td><?php echo htmlspecialchars($book['DueDate']); ?></td>
                <td>$<?php echo number_format($book['Fine'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total Fines</strong></td>
                <td><strong>$<?php echo number_format($totalFines, 2); ?></strong></td>
            </tr>
        </table>

        <h2>Pay Fine</h2>
        <form method="POST" action="fine_management.php">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
            <input type="text" name="transaction_id" placeholder="Enter Transaction ID" required>
            <input type="number" step="0.01" name="amount" placeholder="Enter Amount" required>
            <button type="submit" name="pay_fine">Pay Fine</button>
        </form>
    <?php endif; ?>

    <h2>Fine Payment History</h2>
    <form method="POST" action="fine_management.php">
        <input type="text" name="user_id" placeholder="Enter User ID" required value="<?php echo htmlspecialchars($userId ?? ''); ?>">
        <button type="submit" name="view_history">View Payment History</button>
    </form>

    <?php if (!empty($fineHistory)): ?>
        <h3>Payment History</h3>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Payment Date</th>
            </tr>
            <?php foreach ($fineHistory as $payment): ?>
            <tr>
                <td><?php echo htmlspecialchars($payment['PaymentID']); ?></td>
                <td><?php echo htmlspecialchars($payment['TransactionID']); ?></td>
                <td>$<?php echo number_format($payment['Amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($payment['PaymentDate']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>