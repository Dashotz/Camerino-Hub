<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Fetch sections data with proper ordering
$query = "SELECT 
    s.section_id,
    s.section_name,
    s.grade_level,
    s.status,
    t.firstname AS adviser_firstname,
    t.lastname AS adviser_lastname,
    (SELECT COUNT(*) FROM student_sections 
     WHERE section_id = s.section_id 
     AND status = 'active') as student_count
FROM sections s
LEFT JOIN teacher t ON s.adviser_id = t.teacher_id
ORDER BY s.grade_level ASC, s.section_name ASC";

$result = $db->query($query);

// Fetch statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM sections WHERE status = 'active') as total_sections,
    (SELECT COUNT(*) FROM student_sections WHERE status = 'active') as total_students,
    (SELECT COUNT(DISTINCT adviser_id) FROM sections WHERE adviser_id IS NOT NULL) as total_advisers,
    (SELECT COUNT(*) FROM sections WHERE status = 'active') as active_sections";
$stats_result = $db->query($stats_query);
$stats = $stats_result->fetch_assoc();

// Fetch available teachers for adviser selection
$teachers_query = "SELECT teacher_id, firstname, lastname FROM teacher WHERE status = 'active' ORDER BY lastname, firstname";
$teachers_result = $db->query($teachers_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sections - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .welcome-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-card .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background-color: rgba(52, 152, 219, 0.1);
        }

        .stats-card i {
            font-size: 1.5rem;
        }

        .stats-info h5 {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stats-info h3 {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .section-table th {
            font-weight: 600;
            color: #2c3e50;
        }

        .section-table td {
            vertical-align: middle;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Manage Sections</h1>
                <p>Add, edit, and manage school sections</p>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <h3 id="totalSections"><?php echo $stats['total_sections']; ?>
                                <small class="text-muted d-block">Total</small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <h3 id="totalStudents"><?php echo $stats['total_students']; ?>
                                <small class="text-muted d-block">Students</small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <h3 id="assignedAdvisers"><?php echo $stats['total_advisers']; ?>
                                <small class="text-muted d-block">Advisers</small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body text-center">
                            <h3 id="activeSections"><?php echo $stats['active_sections']; ?>
                                <small class="text-muted d-block">Active</small>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage Sections</h5>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#sectionModal">
                        <i class="fas fa-plus"></i> Add Section
                    </button>
                </div>
                <div class="card-body">
                    <table id="sectionTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Section Name</th>
                                <th>Grade Level</th>
                                <th>Adviser</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['section_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['grade_level']); ?></td>
                                <td><?php echo $row['adviser_firstname'] ? htmlspecialchars($row['adviser_firstname'] . ' ' . $row['adviser_lastname']) : 'Not Assigned'; ?></td>
                                <td><?php echo $row['student_count']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group action-buttons">
                                        <button class="btn btn-sm btn-info" onclick="assignAdviser(<?php echo $row['section_id']; ?>, '<?php echo htmlspecialchars($row['section_name']); ?>')">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <a href="edit_section.php?id=<?php echo $row['section_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSection(<?php echo $row['section_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Modal -->
    <div class="modal fade" id="sectionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Section</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="sectionForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_section">
                        
                        <div class="form-group">
                            <label>Section Name*</label>
                            <input type="text" class="form-control" name="section_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Grade Level*</label>
                            <select class="form-control" name="grade_level" required>
                                <option value="">Select Grade Level</option>
                                <option value="7">Grade 7</option>
                                <option value="8">Grade 8</option>
                                <option value="9">Grade 9</option>
                                <option value="10">Grade 10</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Adviser Assignment Modal -->
    <div class="modal fade" id="assignAdviserModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Adviser</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="assignAdviserForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="assign_adviser">
                        <input type="hidden" name="section_id" id="assignSectionId">
                        
                        <div class="form-group">
                            <label>Section Name:</label>
                            <input type="text" class="form-control" id="sectionNameDisplay" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Select Adviser*</label>
                            <select class="form-control" name="adviser_id" required>
                                <option value="">Select an Adviser</option>
                                <?php 
                                $teachers_result->data_seek(0);
                                while($teacher = $teachers_result->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $teacher['teacher_id']; ?>">
                                        <?php echo htmlspecialchars($teacher['lastname'] . ', ' . $teacher['firstname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign Adviser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#sectionTable').DataTable({
                order: [[1, 'asc'], [0, 'asc']]
            });

            // Form submission handler
            $('#sectionForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get form data
                var formData = $(this).serialize();

                // Send AJAX request
                $.ajax({
                    url: 'handlers/section_handler.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.close();
                        
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Section added successfully',
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to add section'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process request. Please try again.'
                        });
                    }
                });
            });

            $('#assignAdviserForm').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = $(this).serialize();

                $.ajax({
                    url: 'handlers/section_handler.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.close();
                        
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Adviser assigned successfully',
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to assign adviser'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process request. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
    <script>
    function deleteSection(sectionId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'handlers/section_handler.php',
                    type: 'POST',
                    data: {
                        action: 'delete_section',
                        section_id: sectionId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Deleted!',
                                'Section has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to delete section',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Failed to process request',
                            'error'
                        );
                    }
                });
            }
        });
    }
    </script>
    <script>
    function assignAdviser(sectionId, sectionName) {
        $('#assignSectionId').val(sectionId);
        $('#sectionNameDisplay').val(sectionName);
        $('#assignAdviserModal').modal('show');
    }
    </script>
</body>
</html>
