<?php
session_start();
include '..\config\db.php';
include 'borrowing_management.php';

// Function to get number of users with fines
function getUsersWithFines($pdo) {
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT UserID) as users_with_fines 
        FROM BorrowingTransactions 
        WHERE Fine > 0 AND ReturnDate IS NULL
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['users_with_fines'];
}

// Function to get number of current borrowings
function getCurrentBorrowings($pdo) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as current_borrowings 
        FROM BorrowingTransactions 
        WHERE ReturnDate IS NULL
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['current_borrowings'];
}

// Get total fines and users with fines
$usersWithFines = getUsersWithFines($pdo);
$currentBorrowings = getCurrentBorrowings($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #333;
            background-color: #e0e0e0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #d0d0d0;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .dashboard-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            width: 30%;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin-top: 0;
            color: #555;
        }
        .stat-card .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Library Menu</h2>
        <a href="borrowing.php">Borrowing Management</a>
        <a href="fines.php">Fine Management</a>
        <a href="search.php">Search Resources</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Library Management Dashboard</h1>

        <!-- Dashboard Statistics -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Users with Fines</h3>
                <div class="stat-number"><?php echo $usersWithFines; ?></div>
            </div>
            <div class="stat-card">
                <h3>Current Borrowings</h3>
                <div class="stat-number"><?php echo $currentBorrowings; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Resources</h3>
                <div class="stat-number">
                    <?php 
                    $stmt = $pdo->query("SELECT COUNT(*) as total_resources FROM LibraryResources");
                    echo $stmt->fetch(PDO::FETCH_ASSOC)['total_resources'];
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>