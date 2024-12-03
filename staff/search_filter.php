<?php
include '..\config\db.php';

// Function to search for books/resources
function searchResources($searchTerm, $category = null, $availableOnly = false) {
    global $pdo;

    $query = "SELECT r.ResourceID, r.Title, b.Author, b.ISBN, r.AccessionNumber 
              FROM LibraryResources r 
              LEFT JOIN Books b ON r.ResourceID = b.ResourceID 
              WHERE (r.Title LIKE ? OR b.Author LIKE ? OR b.ISBN LIKE ? OR r.AccessionNumber LIKE ?)";

    $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];

    if ($availableOnly) {
        $query .= " AND r.ResourceID NOT IN (SELECT BookID FROM BorrowingTransactions WHERE ReturnDate IS NULL)";
    }

    if ($category) {
        $query .= " AND r.Category = ?";
        $params[] = $category;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to search for users
function searchUsers($searchTerm) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name, membershipID FROM users WHERE name LIKE ? OR membershipID LIKE ?");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Initialize variables
$searchResults = [];
$userResults = [];

// Start the session to pass data between pages
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_resources'])) {
        $searchTerm = $_POST['search_term'];
        $category = $_POST['category'] ?? null;
        $availableOnly = isset($_POST['available_only']);
        $searchResults = searchResources($searchTerm, $category, $availableOnly);
        
        // Store results in session to pass to search.php
        $_SESSION['searchResults'] = $searchResults;
        
        // Redirect back to search.php
        header('Location: search.php');
        exit();
    } elseif (isset($_POST['search_users'])) {
        $searchTerm = $_POST['user_search_term'];
        $userResults = searchUsers($searchTerm);
        
        // Store results in session to pass to search.php
        $_SESSION['userResults'] = $userResults;
        
        // Redirect back to search.php
        header('Location: search.php');
        exit();
    }
}

// If accessed directly without POST, redirect to search.php
header('Location: search.php');
exit();
?>