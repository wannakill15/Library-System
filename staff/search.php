<?php
include '..\config\db.php';

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
    <title>Search and Filter</title>
</head>
<body>
    <?php

    // Initialize variables
    $searchResults = [];
    $userResults = [];

    // Check for search results in session
    if (isset($_SESSION['searchResults'])) {
        $searchResults = $_SESSION['searchResults'];
        // Clear the session variable
        unset($_SESSION['searchResults']);
    }

    if (isset($_SESSION['userResults'])) {
        $userResults = $_SESSION['userResults'];
        // Clear the session variable
        unset($_SESSION['userResults']);
    }
    ?>

    <h1>Search and Filter</h1>

    <h2>Search Books and Resources</h2>
    <form method="POST" action="search_filter.php">
        <input type="text" name="search_term" placeholder="Search by title, author, ISBN, or Accession Number" required>
        <select name="category">
            <option value="">All Categories</option>
            <option value="Book">Books</option>
            <option value="Periodical">Periodicals</option>
            <option value="Media">Media Resources</option>
        </select>
        <label>
            <input type="checkbox" name="available_only"> Available Only
        </label>
        <button type="submit" name="search_resources">Search</button>
    </form>

    <?php if (!empty($searchResults)): ?>
        <h3>Search Results</h3>
        <table border="1">
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Accession Number</th>
            </tr>
            <?php foreach ($searchResults as $resource): ?>
            <tr>
                <td><?php echo htmlspecialchars($resource['Title']); ?></td>
                <td><?php echo htmlspecialchars($resource['Author']); ?></td>
                <td><?php echo htmlspecialchars($resource['ISBN']); ?></td>
                <td><?php echo htmlspecialchars($resource['AccessionNumber']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Search Users</h2>
    <form method="POST" action="search_filter.php">
        <input type="text" name="user_search_term" placeholder="Search by name or Membership ID" required>
        <button type="submit" name="search_users">Search</button>
    </form>

    <?php if (!empty($userResults)): ?>
        <h3>User Search Results</h3>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Membership ID</th>
            </tr>
            <?php foreach ($userResults as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['membershipID']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>