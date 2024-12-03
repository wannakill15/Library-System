<?php
include '..\config\db.php';

// Function to generate borrowing history for a specific user
function getUserBorrowingHistory($userId) {    global $pdo;
    $stmt = $pdo->prepare("SELECT bt.TransactionID, b.Title, bt.BorrowDate, bt.DueDate, bt.ReturnDate, bt.Fine 
                            FROM BorrowingTransactions bt 
                            JOIN Books b ON bt.BookID = b.BookID 
                            WHERE bt.UserID = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to identify popular books based on borrowing frequency
function getPopularBooks($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT b.Title, COUNT(bt.TransactionID) AS BorrowCount 
                            FROM Books b 
                            LEFT JOIN BorrowingTransactions bt ON b.BookID = bt.BookID 
                            GROUP BY b.BookID 
                            ORDER BY BorrowCount DESC 
                            LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to list overdue books with fines and user details
function getOverdueBooks() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bt.TransactionID, b.Title, u.Name, u.MembershipID, bt.DueDate, bt.Fine 
                            FROM BorrowingTransactions bt 
                            JOIN Books b ON bt.BookID = b.BookID 
                            JOIN Users u ON bt.UserID = u.UserID 
                            WHERE bt.ReturnDate IS NULL AND bt.DueDate < CURDATE()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to create inventory summaries by category and availability
function getInventorySummary() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.Category, COUNT(b.BookID) AS TotalBooks, 
                                   SUM(CASE WHEN bt.ReturnDate IS NULL THEN 1 ELSE 0 END) AS BorrowedBooks 
                            FROM LibraryResources r 
                            LEFT JOIN Books b ON r.ResourceID = b.ResourceID 
                            LEFT JOIN BorrowingTransactions bt ON b.BookID = bt.BookID 
                            GROUP BY r.Category");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Example usage
$borrowingHistory = [];
$popularBooks = [];
$overdueBooks = [];
$inventorySummary = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
    $borrowingHistory = getUserBorrowingHistory($_POST['user_id']);
    } elseif (isset($_POST['popular_books'])) {
        $popularBooks = getPopularBooks();
    } elseif (isset($_POST['overdue_books'])) {
        $overdueBooks = getOverdueBooks();
    } elseif (isset($_POST['inventory_summary'])) {
        $inventorySummary = getInventorySummary();
    }
}
?>