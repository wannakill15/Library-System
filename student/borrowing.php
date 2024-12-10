<?php
include 'borrowing_management.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    header('Location: ..\login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
        }
        .table-responsive {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,123,255,0.05);
        }
        .fine-column {
            font-weight: bold;
        }
        .fine-column.high-fine {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">My Borrowed Books</h1>
                
                <?php if (empty($transactions)): ?>
                    <div class="alert alert-info text-center" role="alert">
                        You have no current book borrowings.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Title</th>
                                    <th>Borrow Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Fine</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['TransactionID']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['Title']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['BorrowDate']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['DueDate']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['ReturnDate'] ?? 'Not Returned'); ?></td>
                                    <td class="fine-column <?php echo floatval($transaction['Fine']) > 0 ? 'high-fine' : ''; ?>">
                                        <?php echo htmlspecialchars($transaction['Fine']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>