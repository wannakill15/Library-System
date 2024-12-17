<?php
include '..\config\db.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    echo "You are not logged in. Please log in first.";
    header('Location: ..\login.php');
    exit();
}

// Fetch all users for dropdown
function getAllUsers($pdo) {
    $stmt = $pdo->prepare("SELECT id, name FROM users ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Retrieve results from GET parameters
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$overdueBooks = isset($_GET['overdue_books']) ? json_decode($_GET['overdue_books'], true) : [];
$fineHistory = isset($_GET['fine_history']) ? json_decode($_GET['fine_history'], true) : [];
$paymentResult = isset($_GET['payment_result']) ? $_GET['payment_result'] : '';

$users = getAllUsers($pdo);
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
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h1 {
            color: #333;
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
        }
        form {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group select, 
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #f44336;
        }
        .btn-secondary:hover {
            background-color: #d32f2f;
        }
        .btn-print {
            background-color: #2196F3;
        }
        .btn-print:hover {
            background-color: #1976D2;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 4px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #print-section, 
            #print-section * {
                visibility: visible;
            }
            #print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .btn, .button-group, form {
                display: none;
            }
        }
    </style>
    <script>
        function printFines() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="button-group">
            <h1>Fine Management</h1>
            <div>
                <a href="index.php" class="btn btn-secondary">Return to Dashboard</a>
                <?php if (!empty($overdueBooks)): ?>
                    <button onclick="printFines()" class="btn btn-print">Print Fines</button>
                <?php endif; ?>
            </div>
        </div>

        <h2>Check Overdue Books and Fines</h2>
        <form method="POST" action="fine_management.php">
            <div class="form-group">
                <label for="user_id">Select User</label>
                <select name="user_id" id="user_id" required>
                    <option value="">Select a User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>" 
                                <?php echo ($userId == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="check_overdue" class="btn">Check Overdue Books</button>
        </form>

        <?php if (!empty($paymentResult)): ?>
            <div class="message"><?php echo htmlspecialchars($paymentResult); ?></div>
        <?php endif; ?>

        <?php if (!empty($overdueBooks)): ?>
            <div id="print-section">
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
            </div>

            <h2>Pay Fine</h2>
            <form method="POST" action="fine_management.php">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                <div class="form-group">
                    <label for="transaction_id">Transaction ID</label>
                    <input type="text" id="transaction_id" name="transaction_id" placeholder="Enter Transaction ID" required>
                </div>
                <div class="form-group">
                    <label for="amount">Payment Amount</label>
                    <input type="number" id="amount" step="0.01" name="amount" placeholder="Enter Amount" required>
                </div>
                <button type="submit" name="pay_fine" class="btn">Pay Fine</button>
            </form>
        <?php endif; ?>

        <h2>Fine Payment History</h2>
        <form method="POST" action="fine_management.php">
            <div class="form-group">
                <label for="history_user_id">Select User</label>
                <select name="user_id" id="history_user_id" required>
                    <option value="">Select a User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="view_history" class="btn">View Payment History</button>
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
    </div>
</body>
</html>