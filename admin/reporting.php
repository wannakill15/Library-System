<?php
include '..\config\db.php';

// Function to get all user names
function getAllUserNames() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name FROM users ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to generate borrowing history for a specific user
function getUserBorrowingHistory($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bt.TransactionID, lr.Title, bt.BorrowDate, bt.DueDate, bt.ReturnDate, bt.Fine 
                            FROM BorrowingTransactions bt 
                            JOIN Books b ON bt.BookID = b.BookID 
                            JOIN LibraryResources lr ON b.ResourceID = lr.ResourceID 
                            WHERE bt.UserID = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to identify popular books based on borrowing frequency
function getPopularBooks($limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT lr.Title, COUNT(bt.TransactionID) AS BorrowCount 
                            FROM LibraryResources lr
                            JOIN Books b ON lr.ResourceID = b.ResourceID 
                            LEFT JOIN BorrowingTransactions bt ON b.BookID = bt.BookID 
                            GROUP BY lr.ResourceID, lr.Title 
                            ORDER BY BorrowCount DESC 
                            LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to list overdue books with fines and user details
function getOverdueBooks() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bt.TransactionID, lr.Title, u.name, u.membershipID, bt.DueDate, bt.Fine 
                            FROM BorrowingTransactions bt 
                            JOIN Books b ON bt.BookID = b.BookID 
                            JOIN LibraryResources lr ON b.ResourceID = lr.ResourceID 
                            JOIN Users u ON bt.UserID = u.id 
                            WHERE bt.ReturnDate IS NULL AND bt.DueDate < CURDATE()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to create inventory summaries by category and availability
function getInventorySummary() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.Category, 
                                   COUNT(b.BookID) AS TotalBooks, 
                                   SUM(CASE WHEN bt.ReturnDate IS NULL THEN 1 ELSE 0 END) AS BorrowedBooks 
                            FROM LibraryResources r 
                            LEFT JOIN Books b ON r.ResourceID = b.ResourceID 
                            LEFT JOIN BorrowingTransactions bt ON b.BookID = bt.BookID 
                            GROUP BY r.Category");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Initialize variables
$borrowingHistory = [];
$popularBooks = [];
$overdueBooks = [];
$inventorySummary = [];
$userNames = getAllUserNames();

// Handle different report types
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $borrowingHistory = getUserBorrowingHistory($_POST['user_id']);
    } 
    
    if (isset($_POST['popular_books'])) {
        $popularBooks = getPopularBooks();
    } 
    
    if (isset($_POST['overdue_books'])) {
        $overdueBooks = getOverdueBooks();
    } 
    
    if (isset($_POST['inventory_summary'])) {
        $inventorySummary = getInventorySummary();
    }
}
?>