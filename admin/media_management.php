<?php
include '..\config\db.php';

// Function to generate Accession Number (same as in book_management.php)
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

// Function to add a media resource
function addMedia($title, $format, $runtime, $mediaType) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Generate Accession Number
        $accessionNumber = generateAccessionNumber('Media');

        // Insert into LibraryResources
        $stmt = $pdo->prepare("INSERT INTO LibraryResources (Title, AccessionNumber, Category) VALUES (?, ?, 'Media')");
        $stmt->execute([$title, $accessionNumber]);

        // Get the last inserted ResourceID
        $resourceId = $pdo->lastInsertId();

        // Insert into MediaResources
        $stmt = $pdo->prepare("INSERT INTO MediaResources (ResourceID, Format, Runtime, MediaType) VALUES (?, ?, ?, ?)");
        $stmt->execute([$resourceId, $format, $runtime, $mediaType]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error adding media resource: " . $e->getMessage());
        return false;
    }
}

// Function to edit a media resource
function editMedia($mediaId, $title, $format, $runtime, $mediaType) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Update LibraryResources
        $stmt = $pdo->prepare("UPDATE LibraryResources SET Title = ? WHERE ResourceID = (SELECT ResourceID FROM MediaResources WHERE MediaID = ?)");
        $stmt->execute([$title, $mediaId]);

        // Update MediaResources
        $stmt = $pdo->prepare("UPDATE MediaResources SET Format = ?, Runtime = ?, MediaType = ? WHERE MediaID = ?");
        $stmt->execute([$format, $runtime, $mediaType, $mediaId]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error editing media resource: " . $e->getMessage());
        return false;
    }
}

// Function to delete a media resource
function deleteMedia($mediaId) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get ResourceID for the media resource
        $stmt = $pdo->prepare("SELECT ResourceID FROM MediaResources WHERE MediaID = ?");
        $stmt->execute([$mediaId]);
        $resourceId = $stmt->fetchColumn();

        // Delete from MediaResources
        $stmt = $pdo->prepare("DELETE FROM MediaResources WHERE MediaID = ?");
        $stmt->execute([$mediaId]);

        // Delete from LibraryResources
        $stmt = $pdo->prepare("DELETE FROM LibraryResources WHERE ResourceID = ?");
        $stmt->execute([$resourceId]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error deleting media resource: " . $e->getMessage());
        return false;
    }
}

// Function to get all media resources
function getAllMedia() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT m.MediaID, r.Title, m.Format, m.Runtime, m.MediaType, r.AccessionNumber 
                              FROM MediaResources m 
                              JOIN LibraryResources r ON m.ResourceID = r.ResourceID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching media resources: " . $e->getMessage());
        return [];
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        if (isset($_POST['add'])) {
            $result = addMedia($_POST['title'], $_POST['format'], 
                                $_POST['runtime'], $_POST['media_type']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Media resource added successfully' : 'Failed to add media resource';
        } elseif (isset($_POST['edit'])) {
            $result = editMedia($_POST['media_id'], $_POST['title'], 
                                 $_POST['format'], $_POST['runtime'], 
                                 $_POST['media_type']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Media resource updated successfully' : 'Failed to update media resource';
        } elseif (isset($_POST['delete'])) {
            $result = deleteMedia($_POST['media_id']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Media resource deleted successfully' : 'Failed to delete media resource';
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }

    // Redirect back to media.php with a status message
    header("Location: media.php?status=" . ($response['success'] ? 'success' : 'error') . 
           "&message=" . urlencode($response['message']));
    exit();
}

// Fetch all media resources for display
$mediaResources = getAllMedia();
?>