<?php
session_start();

include '..\config\db.php';

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    die("You must be logged in to access this page.");
}

require_once 'media_management.php';

// Ensure only admin or staff can access this page
// if ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'staff') {
//     die("You do not have permission to access this page.");
// }

// Pagination setup
$itemsPerPage = 10;
$totalItems = count($mediaResources);
$totalPages = ceil($totalItems / $itemsPerPage);
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedMedia = array_slice($mediaResources, $offset, $itemsPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Resources Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <main class="col-md-12">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Media Resources Management</h1>
                    <div>
                        <a href="index.php" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i> Return to Dashboard
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMediaModal">
                            <i class="fas fa-plus"></i> Add New Media Resource
                        </button>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if(isset($_GET['status'])): ?>
                    <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Media Resources Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Accession No</th>
                                <th>Title</th>
                                <th>Format</th>
                                <th>Runtime</th>
                                <th>Media Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($paginatedMedia as $media): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($media['AccessionNumber']); ?></td>
                                    <td><?php echo htmlspecialchars($media['Title']); ?></td>
                                    <td><?php echo htmlspecialchars($media['Format']); ?></td>
                                    <td><?php echo htmlspecialchars($media['Runtime']); ?></td>
                                    <td><?php echo htmlspecialchars($media['MediaType']); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-warning edit-media" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editMediaModal"
                                                data-id="<?php echo $media['MediaID']; ?>"
                                                data-title="<?php echo htmlspecialchars($media['Title']); ?>"
                                                data-format="<?php echo htmlspecialchars($media['Format']); ?>"
                                                data-runtime="<?php echo htmlspecialchars($media['Runtime']); ?>"
                                                data-media-type="<?php echo htmlspecialchars($media['MediaType']); ?>">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-media" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteMediaModal"
                                                data-id="<?php echo $media['MediaID']; ?>"
                                                data-title="<?php echo htmlspecialchars($media['Title']); ?>">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Media Resource Modal -->
    <div class="modal fade" id="addMediaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Media Resource</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMediaForm" method="POST" action="media_management.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Format</label>
                            <input type="text" class="form-control" name="format" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Runtime</label>
                            <input type="text" class="form-control" name="runtime" placeholder="e.g., 2h 30m">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Media Type</label>
                            <select class="form-select" name="media_type" required>
                                <option value="">Select Media Type</option>
                                <option value="DVD">DVD</option>
                                <option value="Blu-ray">Blu-ray</option>
                                <option value="CD">CD</option>
                                <option value="Digital">Digital</option>
                                <option value="VHS">VHS</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="add">Add Media Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Media Resource Modal -->
    <div class="modal fade" id="editMediaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Media Resource</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMediaForm" method="POST" action="media_management.php">
                    <div class="modal-body">
                        <input type="hidden" name="media_id" id="edit-media-id">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="edit-title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Format</label>
                            <input type="text" class="form-control" name="format" id="edit-format" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Runtime</label>
                            <input type="text" class="form-control" name="runtime" id="edit-runtime">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Media Type</label>
                            <select class="form-select" name="media_type" id="edit-media-type" required>
                                <option value="DVD">DVD</option>
                                <option value="Blu-ray">Blu-ray</option>
                                <option value="CD">CD</option>
                                <option value="Digital">Digital</option>
                                <option value="VHS">VHS</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="edit">Update Media Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Media Resource Modal -->
    <div class="modal fade" id="deleteMediaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="media_management.php">
                    <div class="modal-body">
                        <input type="hidden" name="media_id" id="delete-media-id">
                        <p>Are you sure you want to delete the media resource: <strong id="delete-media-title"></strong>?</p>
                        <div class="alert alert-warning">
                            <small>This action cannot be undone.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete">Confirm Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap and JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-media');
            const deleteButtons = document.querySelectorAll('.delete-media');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit-media-id').value = this.dataset.id;
                    document.getElementById('edit-title').value = this.dataset.title;
                    document.getElementById('edit-format').value = this.dataset.format;
                    document.getElementById('edit-runtime').value = this.dataset.runtime;
                    
                    const mediaTypeSelect = document.getElementById('edit-media-type');
                    mediaTypeSelect.value = this.dataset.mediaType;
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('delete-media-id').value = this.dataset.id;
                    document.getElementById('delete-media-title').textContent = this.dataset.title;
                });
            });
        });
    </script>
</body>
</html>