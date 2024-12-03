<?php
include '..\config\db.php';

// Function to generate Accession Number
function generateAccessionNumber($type) {
    global $pdo;
    $year = date('Y');
    $prefix = '';
    
    // Determine prefix based on resource type
    switch ($type) {
        case 'Book':
            $prefix = 'B';
            break;
        case 'Periodical':
            $prefix = 'P';
            break;
        case 'Media':
            $prefix = 'R';
            break;
        default:
            return null;
    }

    // Get the count of existing resources for the current year
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM LibraryResources WHERE AccessionNumber LIKE ? AND YEAR(CURDATE()) = YEAR(CURDATE())");
    $stmt->execute([$prefix . '-' . $year . '-%']);
    $count = $stmt->fetchColumn();

    // Sequential number for the new accession number
    $sequentialNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    return "{$prefix}-{$year}-{$sequentialNumber}";
}

// Function to add a book
function addBook($title, $author, $isbn, $publisher, $edition, $publicationDate) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Generate Accession Number
        $accessionNumber = generateAccessionNumber('Book');

        // Insert into LibraryResources
        $stmt = $pdo->prepare("INSERT INTO LibraryResources (Title, AccessionNumber, Category) VALUES (?, ?, 'Book')");
        $stmt->execute([$title, $accessionNumber]);

        // Get the last inserted ResourceID
        $resourceId = $pdo->lastInsertId();

        // Insert into Books
        $stmt = $pdo->prepare("INSERT INTO Books (ResourceID, Author, ISBN, Publisher, Edition, PublicationDate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$resourceId, $author, $isbn, $publisher, $edition, $publicationDate]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error adding book: " . $e->getMessage());
        return false;
    }
}

// Function to edit a book
function editBook($bookId, $title, $author, $isbn, $publisher, $edition, $publicationDate) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Update LibraryResources
        $stmt = $pdo->prepare("UPDATE LibraryResources SET Title = ? WHERE ResourceID = (SELECT ResourceID FROM Books WHERE BookID = ?)");
        $stmt->execute([$title, $bookId]);

        // Update Books
        $stmt = $pdo->prepare("UPDATE Books SET Author = ?, ISBN = ?, Publisher = ?, Edition = ?, PublicationDate = ? WHERE BookID = ?");
        $stmt->execute([$author, $isbn, $publisher, $edition, $publicationDate, $bookId]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error editing book: " . $e->getMessage());
        return false;
    }
}

// Function to delete a book
function deleteBook($bookId) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get ResourceID for the book
        $stmt = $pdo->prepare("SELECT ResourceID FROM Books WHERE BookID = ?");
        $stmt->execute([$bookId]);
        $resourceId = $stmt->fetchColumn();

        // Delete from Books
        $stmt = $pdo->prepare("DELETE FROM Books WHERE BookID = ?");
        $stmt->execute([$bookId]);

        // Delete from LibraryResources
        $stmt = $pdo->prepare("DELETE FROM LibraryResources WHERE ResourceID = ?");
        $stmt->execute([$resourceId]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error deleting book: " . $e->getMessage());
        return false;
    }
}

// Function to get all books
function getAllBooks() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT b.BookID, r.Title, b.Author, b.ISBN, b.Publisher, b.Edition, b.PublicationDate, r.AccessionNumber 
                              FROM Books b 
                              JOIN LibraryResources r ON b.ResourceID = r.ResourceID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching books: " . $e->getMessage());
        return [];
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        if (isset($_POST['add'])) {
            $result = addBook($_POST['title'], $_POST['author'], $_POST['isbn'], 
                               $_POST['publisher'], $_POST['edition'], $_POST['publication_date']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Book added successfully' : 'Failed to add book';
        } elseif (isset($_POST['edit'])) {
            $result = editBook($_POST['book_id'], $_POST['title'], $_POST['author'], 
                                $_POST['isbn'], $_POST['publisher'], $_POST['edition'], 
                                $_POST['publication_date']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Book updated successfully' : 'Failed to update book';
        } elseif (isset($_POST['delete'])) {
            $result = deleteBook($_POST['book_id']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Book deleted successfully' : 'Failed to delete book';
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }

    // Redirect back to book.php with a status message
    header("Location: book.php?status=" . ($response['success'] ? 'success' : 'error') . 
           "&message=" . urlencode($response['message']));
    exit();
}

// Fetch all books for display
$books = getAllBooks();
?>