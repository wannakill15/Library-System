<?php
include '..\config\db.php';

// Constants
define('FINE_PER_DAY', 0.50); // Example fine rate per day

// Function to calculate fine for overdue books
function calculateFine($dueDate) {
    $currentDate = new DateTime();
    $dueDate = new DateTime($dueDate);
    $interval = $currentDate->diff($dueDate);

    if ($interval->invert === 1) { // If the due date is in the past
        return $interval->days * FINE_PER_DAY;
    }

    return 0; // No fine if not overdue
}

// Function to record fine payment
function recordFinePayment($userId, $transactionId, $amount) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO finepayments (UserID, TransactionID, Amount) VALUES (?, ?, ?)");
    return $stmt->execute([$userId, $transactionId, $amount]);
}

// Function to get fine payment history for a user
function getFinePaymentHistory($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT fp.PaymentID, bt.TransactionID, fp.Amount, fp.PaymentDate 
                            FROM finepayments fp 
                            JOIN borrowingtransactions bt ON fp.TransactionID = bt.TransactionID 
                            WHERE fp.UserID = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get overdue books with fines
function getOverdueBooks($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bt.TransactionID, lr.Title, bt.DueDate 
                            FROM borrowingtransactions bt 
                            JOIN books b ON bt.BookID = b.BookID 
                            JOIN libraryresources lr ON b.ResourceID = lr.ResourceID
                            WHERE bt.UserID = ? AND bt.ReturnDate IS NULL");
    $stmt->execute([$userId]);
    $overdueBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate fines for overdue books
    foreach ($overdueBooks as &$book) {
        $book['Fine'] = calculateFine($book['DueDate']);
    }

    return $overdueBooks;
}

// Handle form submissions
$userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$overdueBooks = [];
$fineHistory = [];
$paymentResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check Overdue Books
    if (isset($_POST['check_overdue'])) {
        $overdueBooks = getOverdueBooks($userId);
    } 
    // Pay Fine
    elseif (isset($_POST['pay_fine'])) {
        $transactionId = $_POST['transaction_id'];
        $amount = $_POST['amount'];

        if (recordFinePayment($userId, $transactionId, $amount)) {
            $paymentResult = "Fine payment of $amount recorded successfully.";
            // Refresh overdue books after payment
            $overdueBooks = getOverdueBooks($userId);
        } else {
            $paymentResult = "Failed to record fine payment.";
        }
    } 
    // View Fine Payment History
    elseif (isset($_POST['view_history'])) {
        $fineHistory = getFinePaymentHistory($userId);
    }
}

// Redirect back to fines.php with results
header("Location: fines.php?user_id=" . urlencode($userId) . 
       "&overdue_books=" . urlencode(json_encode($overdueBooks)) . 
       "&fine_history=" . urlencode(json_encode($fineHistory)) . 
       "&payment_result=" . urlencode($paymentResult));
exit();
?>