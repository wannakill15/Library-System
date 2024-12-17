<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        // Use PDO for preparing and executing the statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['UserId'] = $user['id'];
            $_SESSION['UserType'] = $user['user_type'];

            // Redirect based on user type
            switch ($user['user_type']) {
                case 'student':
                    header('Location: student/index.php');
                    exit();
                case 'staff':
                    header('Location: staff/index.php');
                    exit();
                case 'admin':
                    header('Location: admin/index.php');
                    exit();
                default:
                    $_SESSION['error'] = "Invalid user type.";
                    header('Location: login.php');
                    exit();
            }
        } else {
            // Set error message in session
            $_SESSION['error'] = "Invalid email or password.";
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        // Log the error and show a generic error message
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again.";
        header('Location: login.php');
        exit();
    }
}
?>