<?php
session_start();
// Check if user is logged in and has appropriate access
if (!isset($_SESSION['UserId'])) {
    die("You must be logged in to access this page.");
}
require_once 'periodical_management.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Periodical Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Periodical Management</h2>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Return to Dashboard
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPeriodicalModal">
                            <i class="bi bi-plus-circle me-2"></i>Add New Periodical
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Success/Error Message Display -->
                        <?php if(isset($_GET['status'])): ?>
                            <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_GET['message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Periodicals Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light position-sticky top-0">
                                    <tr>
                                        <th>Accession No</th>
                                        <th>Title</th>
                                        <th>ISSN</th>
                                        <th>Volume</th>
                                        <th>Issue</th>
                                        <th>Publication Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($periodicals as $periodical): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($periodical['AccessionNumber']); ?></td>
                                            <td><?php echo htmlspecialchars($periodical['Title']); ?></td>
                                            <td><?php echo htmlspecialchars($periodical['ISSN']); ?></td>
                                            <td><?php echo htmlspecialchars($periodical['Volume']); ?></td>
                                            <td><?php echo htmlspecialchars($periodical['Issue']); ?></td>
                                            <td><?php echo htmlspecialchars($periodical['PublicationDate']); ?></td>
                                            <td class="text-center action-buttons">
                                                <button class="btn btn-sm btn-warning edit-periodical" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editPeriodicalModal"
                                                    data-id="<?php echo $periodical['PeriodicalID']; ?>"
                                                    data-title="<?php echo htmlspecialchars($periodical['Title']); ?>"
                                                    data-issn="<?php echo htmlspecialchars($periodical['ISSN']); ?>"
                                                    data-volume="<?php echo htmlspecialchars($periodical['Volume']); ?>"
                                                    data-issue="<?php echo htmlspecialchars($periodical['Issue']); ?>"
                                                    data-publication-date="<?php echo htmlspecialchars($periodical['PublicationDate']); ?>">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-periodical" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deletePeriodicalModal"
                                                    data-id="<?php echo $periodical['PeriodicalID']; ?>">
                                                    <i class="bi bi-trash me-1"></i>Delete
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
        </div>
    </div>


    <!-- Add Periodical Modal -->
    <div class="modal fade" id="addPeriodicalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Periodical</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="periodical_management.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ISSN</label>
                            <input type="text" class="form-control" name="issn" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Volume</label>
                            <input type="text" class="form-control" name="volume">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Issue</label>
                            <input type="text" class="form-control" name="issue">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Publication Date</label>
                            <input type="date" class="form-control" name="publication_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add">Add Periodical</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Periodical Modal -->
    <div class="modal fade" id="editPeriodicalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Periodical</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="periodical_management.php">
                    <div class="modal-body">
                        <input type="hidden" name="periodical_id" id="edit-periodical-id">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="edit-title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ISSN</label>
                            <input type="text" class="form-control" name="issn" id="edit-issn" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Volume</label>
                            <input type="text" class="form-control" name="volume" id="edit-volume">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Issue</label>
                            <input type="text" class="form-control" name="issue" id="edit-issue">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Publication Date</label>
                            <input type="date" class="form-control" name="publication_date" id="edit-publication-date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="edit">Update Periodical</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Periodical Modal -->
    <div class="modal fade" id="deletePeriodicalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Periodical</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="periodical_management.php">
                    <div class="modal-body">
                        <input type="hidden" name="periodical_id" id="delete-periodical-id">
                        <p>Are you sure you want to delete this periodical?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.6/dist/inputmask.min.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            // Input masking for ISSN
            Inputmask({"mask": "9999-9999", "placeholder": ""}).mask("#edit-issn, input[name='issn']");

            const editButtons = document.querySelectorAll('.edit-periodical');
            const deleteButtons = document.querySelectorAll('.delete-periodical');

            // Form validation function
            function validateForm(form) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                return isValid;
            }

            // Add event listeners to all forms for validation
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!validateForm(this)) {
                        e.preventDefault();
                    }
                });
            });
        });
        
        // JavaScript to populate edit and delete modals
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-periodical');
            const deleteButtons = document.querySelectorAll('.delete-periodical');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit-periodical-id').value = this.dataset.id;
                    document.getElementById('edit-title').value = this.dataset.title;
                    document.getElementById('edit-issn').value = this.dataset.issn;
                    document.getElementById('edit-volume').value = this.dataset.volume;
                    document.getElementById('edit-issue').value = this.dataset.issue;
                    document.getElementById('edit-publication-date').value = this.dataset.publicationDate;
                });
            });

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('delete-periodical-id').value = this.dataset.id;
                });
            });
        });
    </script>
</body>
</html>