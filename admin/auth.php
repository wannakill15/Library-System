<?php
include '..\config\db.php';

// Function to check if a user has a specific role
function isUserRole($userId, $user_type) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE UserID = ? AND user_type = ?");
    $stmt->execute([$userId, $user_type]);
    return $stmt->fetchColumn() > 0;
}

// Example usage
if (!isUserRole($_SESSION['UserId'], 'admin')) {
    die('Unauthorized access.');
}