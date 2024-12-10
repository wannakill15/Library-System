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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg">
            <div class="bg-blue-600 text-white p-4 rounded-t-lg flex justify-between items-center">
                <h1 class="text-2xl font-bold">User Management</h1>
                <a href="index.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-100 transition">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </div>

            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Add User</h2>
                <form method="POST" action="user_management.php" class="grid grid-cols-2 gap-4">
                    <input type="hidden" name="id" value="">
                    <input type="text" name="name" placeholder="Name" required 
                           class="border rounded px-3 py-2 col-span-2">
                    <input type="email" name="email" placeholder="Email" required 
                           class="border rounded px-3 py-2">
                    <input type="password" name="password" placeholder="Password" required 
                           class="border rounded px-3 py-2">
                    <select name="user_type" required 
                            class="border rounded px-3 py-2 col-span-2">
                        <option value="">Select User Type</option>
                        <option value="student">Student</option>
                        <option value="faculty">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                    <div class="col-span-2 flex space-x-4">
                        <button type="submit" name="add" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            <i class="fas fa-user-plus mr-2"></i>Add User
                        </button>
                        <button type="submit" name="edit" 
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            <i class="fas fa-edit mr-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Users List</h2>
                <div class="overflow-x-auto">
                    <table class="w-full bg-white border">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">User Type</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="px-4 py-2"><?php echo $user['id']; ?></td>
                                <td class="px-4 py-2"><?php echo $user['name']; ?></td>
                                <td class="px-4 py-2"><?php echo $user['email']; ?></td>
                                <td class="px-4 py-2"><?php echo $user['user_type']; ?></td>
                                <td class="px-4 py-2 flex space-x-2">
                                    <form method="POST" action="user_management.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete" 
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </button>
                                    </form>
                                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo $user['name']; ?>', '<?php echo $user['email']; ?>', '<?php echo $user['user_type']; ?>')"
                                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editUser(id, name, email, userType) {
            document.querySelector('input[name="id"]').value = id;
            document.querySelector('input[name="name"]').value = name;
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('select[name="user_type"]').value = userType;
        }
    </script>
</body>
</html>