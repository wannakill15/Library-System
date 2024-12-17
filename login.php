<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php 
    session_start();
    if (isset($_SESSION['error'])) {
        echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
        unset($_SESSION['error']); // Clear the error after displaying
    }
    ?>

    <form method="POST" action="auth.php">
        <input type="hidden" name="user_id" id="user_id">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <br>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <br>

        <button type="submit">Login</button>
    </form>
</body>
</html>