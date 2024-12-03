<?php
include '..\config\db.php';

// Function to search for available books
function searchAvailableBooks($searchTerm, $category = null) {
    global $pdo;

    // Query to find books that are currently available
    $query = "SELECT r.ResourceID, r.Title, b.Author, b.ISBN, r.AccessionNumber, r.Category 
              FROM LibraryResources r 
              LEFT JOIN Books b ON r.ResourceID = b.ResourceID 
              WHERE (r.Title LIKE ? OR b.Author LIKE ? OR b.ISBN LIKE ? OR r.AccessionNumber LIKE ?)
              AND r.ResourceID NOT IN (
                  SELECT BookID 
                  FROM BorrowingTransactions 
                  WHERE ReturnDate IS NULL
              )";

    $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

    // Add category filter if specified
    if ($category) {
        $query .= " AND r.Category = ?";
        $params[] = $category;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Start the session to pass data between pages
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_available_books'])) {
        $searchTerm = $_POST['search_term'];
        $category = $_POST['category'] ?? null;
        
        // Search for available books
        $availableBooks = searchAvailableBooks($searchTerm, $category);
        
        // Store results in session to pass to search.php
        $_SESSION['availableBooks'] = $availableBooks;
        
        // Redirect back to search.php
        header('Location: search_book.php');
        exit();
    }
}

// If accessed directly without POST, redirect to search.php
header('Location: search_book.php');
exit();
?>