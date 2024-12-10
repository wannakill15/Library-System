<?php
include '..\config\db.php';

// Function to add user
function addUser ($name, $email, $password, $user_type) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $user_type]);
}

// Function to edit user
function editUser ($id, $name, $email, $user_type) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, user_type = ? WHERE id = ?");
    $stmt->execute([$name, $email, $user_type, $id]);
}

// Function to delete user
function deleteUser ($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

// Function to get user details
function getUserDetails($id) {    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get borrowing history
function getBorrowingHistory($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM borrowing_history WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        addUser ($_POST['name'], $_POST['email'], $_POST['password'], $_POST['user_type']);
        header("Location: user.php"); // Redirect back to user.php after adding user
        exit(); // Stop further script execution
    } elseif (isset($_POST['edit'])) {
        editUser ($_POST['id'], $_POST['name'], $_POST['email'], $_POST['user_type']);
        header("Location: user.php"); // Redirect back to user.php after editing user
        exit(); // Stop further script execution
    } elseif (isset($_POST['delete'])) {
        deleteUser ($_POST['id']);
        header("Location: user.php"); // Redirect back to user.php after deleting user
        exit(); // Stop further script execution
    }
}

// Fetch all users for display
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>