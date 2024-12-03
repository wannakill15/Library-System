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

    <form method="POST" action="auth.php">
        <input type="hidden" id="user_id">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <br>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <br>

        <button type="submit">Login</button>
    </form>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>
