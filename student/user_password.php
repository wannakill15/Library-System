<?php
session_start();
require_once '..\config\db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

// Get the current user's ID
$current_user_id = $_SESSION['user_id'];

// Fetch current user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate current password
    if (!password_verify($_POST['current_password'], $user['password'])) {
        $error = "Current password is incorrect.";
    } 
    // Validate new password
    elseif (empty($_POST['new_password']) || strlen($_POST['new_password']) < 8) {
        $error = "New password must be at least 8 characters long.";
    } 
    // Validate password confirmation
    elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
        $error = "New passwords do not match.";
    } 
    else {
        // Hash the new password
        $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        
        // Update password in database
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_password_hash, $current_user_id);
        
        if ($update_stmt->execute()) {
            $success = "Password updated successfully!";
            
            // Log the activity
            $log_stmt = $conn->prepare("INSERT INTO activitylog (UserID, Operation) VALUES (?, 'Password Changed')");
            $log_stmt->bind_param("i", $current_user_id);
            $log_stmt->execute();
        } else {
            $error = "Error updating password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
        }
        label {
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>Change Password</h2>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
        
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        
        <input type="submit" value="Update Password">
    </form>
    
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>