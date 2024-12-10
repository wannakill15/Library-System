<?php
 include 'user_management.php';

 session_start();

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    echo "You are not logged in. Please log in first.";
    header('Location: ..\login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<body>
    <h1>User Management</h1>

    <h2>Add User</h2>
    <form method="POST" action="user_management.php">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name=" user_type" required>
            <option value="student">Student</option>
            <option value="faculty">Admin</option>
            <option value="staff">Staff</option>
        </select>
        <button type="submit" name="add">Add User</button>
    </form>

    <h2>Users List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>User Type</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['name']; ?></td>
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['user_type']; ?></td>
            <td>
                <form method="POST" action="user_management.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
                <button onclick="editUser (<?php echo $user['id']; ?>, '<?php echo $user['name']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['user_type']; ?>')">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function editUser (id, name, email, userType) {
            document.querySelector('input[name="id"]').value = id;
            document.querySelector('input[name="name"]').value = name;
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('select[name="user_type"]').value = userType;
        }
    </script>
</body>
</html>