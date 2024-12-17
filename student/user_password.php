<?php
session_start();
require_once dirname(__DIR__) . '/config/db.php';

// Improved login check with more secure redirection
if (!isset($_SESSION['UserId'])) {
    // Use absolute path or relative path based on your application structure
    header('Location: ../login.php');
    exit();
}

// Get the current user's ID using prepared statement with PDO
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['UserId']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Handle case where user is not found
        session_destroy();
        header('Location: ../login.php');
        exit();
    }
} catch (PDOException $e) {
    // Log error securely
    error_log('Database error: ' . $e->getMessage());
    die('An error occurred while fetching user information.');
}

// Initialize error and success messages
$error = '';
$success = '';

// Handle form submission with improved validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Comprehensive password validation
    $validation_errors = [];

    // Check current password
    if (empty($current_password) || !password_verify($current_password, $user['password'])) {
        $validation_errors[] = "Current password is incorrect.";
    }

    // New password complexity checks
    if (empty($new_password)) {
        $validation_errors[] = "New password cannot be empty.";
    } elseif (strlen($new_password) < 12) {
        $validation_errors[] = "New password must be at least 12 characters long.";
    } elseif ($new_password === $current_password) {
        $validation_errors[] = "New password must be different from the current password.";
    }

    // Additional password complexity checks (optional but recommended)
    if (!preg_match('/[A-Z]/', $new_password)) {
        $validation_errors[] = "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $new_password)) {
        $validation_errors[] = "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $new_password)) {
        $validation_errors[] = "Password must contain at least one number.";
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $new_password)) {
        $validation_errors[] = "Password must contain at least one special character.";
    }

    // Confirm password match
    if ($new_password !== $confirm_password) {
        $validation_errors[] = "New passwords do not match.";
    }

    // Process password update if no validation errors
    if (empty($validation_errors)) {
        try {
            // Hash the new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password using PDO
            $update_stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
            $update_stmt->execute([
                'new_password' => $new_password_hash,
                'user_id' => $_SESSION['UserId']
            ]);

            // Log the password change activity
            $log_stmt = $pdo->prepare("INSERT INTO activitylog (UserID, Operation) VALUES (:user_id, 'Password Changed')");
            $log_stmt->execute(['user_id' => $_SESSION['UserId']]);

            $success = "Password updated successfully!";

            // Optional: Invalidate all other sessions for added security
            // You'd need to implement a session management mechanism for this
        } catch (PDOException $e) {
            // Log error securely
            error_log('Password update error: ' . $e->getMessage());
            $error = "Error updating password. Please try again later.";
        }
    } else {
        // Collect all validation errors
        $error = implode('<br>', $validation_errors);
    }
}
?>

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
    <div class="container d-flex justify-content-center">
        <div class="password-container">
            <h2 class="text-center mb-4">Change Password</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" novalidate>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
                </div>
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password">
                    <small class="form-text text-muted">
                        Password must be at least 12 characters long and include:
                        <ul class="small text-muted">
                            <li>Uppercase letter</li>
                            <li>Lowercase letter</li>
                            <li>Number</li>
                            <li>Special character</li>
                        </ul>
                    </small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-muted">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>