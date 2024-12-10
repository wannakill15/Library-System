<?php
session_start();
require_once '..\config\db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ..\login.php');
    exit();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .password-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            width: 100%;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-container">
            <h2 class="text-center mb-4">Change Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                    <small class="form-text text-muted">Minimum 8 characters</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="dashboard.php" class="text-muted">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>