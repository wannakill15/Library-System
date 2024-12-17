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

// Function to add a periodical
function addPeriodical($title, $issn, $volume, $issue, $publicationDate) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Generate Accession Number
        $accessionNumber = generateAccessionNumber('Periodical');

        // Insert into LibraryResources
        $stmt = $pdo->prepare("INSERT INTO LibraryResources (Title, AccessionNumber, Category) VALUES (?, ?, 'Periodical')");
        $stmt->execute([$title, $accessionNumber]);

        // Get the last inserted ResourceID
        $resourceId = $pdo->lastInsertId();

        // Insert into Periodicals
        $stmt = $pdo->prepare("INSERT INTO Periodicals (ResourceID, ISSN, Volume, Issue, PublicationDate) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$resourceId, $issn, $volume, $issue, $publicationDate]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error adding periodical: " . $e->getMessage());
        return false;
    }
}

// Function to edit a periodical
function editPeriodical($periodicalId, $title, $issn, $volume, $issue, $publicationDate) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Update LibraryResources
        $stmt = $pdo->prepare("UPDATE LibraryResources SET Title = ? WHERE ResourceID = (SELECT ResourceID FROM Periodicals WHERE PeriodicalID = ?)");
        $stmt->execute([$title, $periodicalId]);

        // Update Periodicals
        $stmt = $pdo->prepare("UPDATE Periodicals SET ISSN = ?, Volume = ?, Issue = ?, PublicationDate = ? WHERE PeriodicalID = ?");
        $stmt->execute([$issn, $volume, $issue, $publicationDate, $periodicalId]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error editing periodical: " . $e->getMessage());
        return false;
    }
}

// Function to delete a periodical
function deletePeriodical($periodicalId) {
    global $pdo;

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get ResourceID for the periodical
        $stmt = $pdo->prepare("SELECT ResourceID FROM Periodicals WHERE PeriodicalID = ?");
        $stmt->execute([$periodicalId]);
        $resourceId = $stmt->fetchColumn();

        // Delete from Periodicals
        $stmt = $pdo->prepare("DELETE FROM Periodicals WHERE PeriodicalID = ?");
        $stmt->execute([$periodicalId]);

        // Delete from LibraryResources
        $stmt = $pdo->prepare("DELETE FROM LibraryResources WHERE ResourceID = ?");
        $stmt->execute([$resourceId]);

        // Commit the transaction
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error deleting periodical: " . $e->getMessage());
        return false;
    }
}

// Function to get all periodicals
function getAllPeriodicals() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT p.PeriodicalID, r.Title, p.ISSN, p.Volume, p.Issue, p.PublicationDate, r.AccessionNumber 
                              FROM Periodicals p 
                              JOIN LibraryResources r ON p.ResourceID = r.ResourceID");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching periodicals: " . $e->getMessage());
        return [];
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        if (isset($_POST['add'])) {
            $result = addPeriodical($_POST['title'], $_POST['issn'], 
                                     $_POST['volume'], $_POST['issue'], 
                                     $_POST['publication_date']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Periodical added successfully' : 'Failed to add periodical';
        } elseif (isset($_POST['edit'])) {
            $result = editPeriodical($_POST['periodical_id'], $_POST['title'], 
                                      $_POST['issn'], $_POST['volume'], 
                                      $_POST['issue'], $_POST['publication_date']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Periodical updated successfully' : 'Failed to update periodical';
        } elseif (isset($_POST['delete'])) {
            $result = deletePeriodical($_POST['periodical_id']);
            $response['success'] = $result;
            $response['message'] = $result ? 'Periodical deleted successfully' : 'Failed to delete periodical';
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }

    // Redirect back to periodical.php with a status message
    header("Location: periodical.php?status=" . ($response['success'] ? 'success' : 'error') . 
           "&message=" . urlencode($response['message']));
    exit();
}

// Fetch all periodicals for display
$periodicals = getAllPeriodicals();
?>