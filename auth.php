<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND email = ?");
    $stmt->execute([$user_id, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['UserId'] = $user['id'];
        $_SESSION['UserType'] = $user['user_type'];

        switch ($user['user_type']) {
            case 'student':
                header('Location: student\index.php');
                break;
            case 'staff':
                header('Location: staff\index.php');
                break;
            case 'admin':
                header('Location: admin\index.php');
                break;
            default:
                echo "Invalid user type.";
                break;
        }
        exit();
    } else {
        echo "Invalid email or password.";
    }
}
?>
