<?php
include '..\config\db.php';

// Function to borrow a book
function borrowBook($bookId, $userId) {
    global $pdo;

    try {
        // First, verify that the user exists
        $userStmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $userExists = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$userExists) {
            throw new Exception("Invalid User ID. User not found.");
        }

        // Verify that the book exists and is available
        $bookStmt = $pdo->prepare("SELECT BookID FROM books WHERE BookID = ?");
        $bookStmt->execute([$bookId]);
        $bookExists = $bookStmt->fetch(PDO::FETCH_ASSOC);

        if (!$bookExists) {
            throw new Exception("Invalid Book ID. Book not found.");
        }

        // Set the borrowing and due dates
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+14 days')); // Assuming a 14-day borrowing period

        // Insert into BorrowingTransactions
        $stmt = $pdo->prepare("INSERT INTO BorrowingTransactions (BookID, UserID, BorrowDate, DueDate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$bookId, $userId, $borrowDate, $dueDate]);

        // Optionally, update user's borrowed books count
        $updateStmt = $pdo->prepare("UPDATE users SET borrowed_books = borrowed_books + 1 WHERE id = ?");
        $updateStmt->execute([$userId]);

        return true;
    } catch (Exception $e) {
        // Log the error or display a user-friendly message
        error_log($e->getMessage());
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Function to return a book
function returnBook($transactionId) {
    global $pdo;

    // Get the transaction details
    $stmt = $pdo->prepare("SELECT DueDate FROM BorrowingTransactions WHERE TransactionID = ?");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        $dueDate = $transaction['DueDate'];
        $returnDate = date('Y-m-d');
        
        // Calculate fine if overdue
        $fine = 0;
        if ($returnDate > $dueDate) {
            $daysOverdue = (strtotime($returnDate) - strtotime($dueDate)) / (60 * 60 * 24);
            $fine = $daysOverdue * 0.50; // Assuming $0.50 per day fine
        }

        // Update return date and fine in BorrowingTransactions
        $stmt = $pdo->prepare("UPDATE BorrowingTransactions SET ReturnDate = ?, Fine = ? WHERE TransactionID = ?");
        $stmt->execute([$returnDate, $fine, $transactionId]);
    }
}

// Function to get all borrowing transactions
function getAllBorrowingTransactions() {
    global $pdo;
    $stmt = $pdo->query("SELECT bt.TransactionID, lr.Title, u.name AS Borrower, bt.BorrowDate, bt.DueDate, bt.ReturnDate, bt.Fine 
                          FROM BorrowingTransactions bt 
                          JOIN Books b ON bt.BookID = b.BookID 
                          JOIN LibraryResources lr ON b.ResourceID = lr.ResourceID
                          JOIN users u ON bt.UserID = u.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['borrow'])) {
        borrowBook($_POST['book_id'], $_POST['user_id']);
    } elseif (isset($_POST['return'])) {
        returnBook($_POST['transaction_id']);
    }
    
    header('Location: borrowing.php');
    exit();
}

// Fetch all borrowing transactions for display
$transactions = getAllBorrowingTransactions();
?>