<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get section ID from URL
$section_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$section_id) {
    header("Location: manage_sections.php");
    exit();
}

// Fetch section data
$query = "SELECT * FROM sections WHERE section_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$section = $stmt->get_result()->fetch_assoc();

if (!$section) {
    header("Location: manage_sections.php");
    exit();
}

// Fetch available teachers for adviser selection
$teachers_query = "SELECT teacher_id, firstname, lastname 
                  FROM teacher 
                  WHERE status = 'active' 
                  ORDER BY lastname, firstname";
$teachers_result = $db->query($teachers_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Section - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Edit Section</h4>
                            </div>
                            <div class="card-body">
                                <form id="editSectionForm">
                                    <input type="hidden" name="action" value="edit_section">
                                    <input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
                                    
                                    <div class="form-group">
                                        <label>Section Name*</label>
                                        <input type="text" class="form-control" name="section_name" 
                                               value="<?php echo htmlspecialchars($section['section_name']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Grade Level*</label>
                                        <select class="form-control" name="grade_level" required>
                                            <option value="">Select Grade Level</option>
                                            <?php
                                            $grades = range(7, 10);
                                            foreach ($grades as $grade) {
                                                $selected = ($section['grade_level'] == $grade) ? 'selected' : '';
                                                echo "<option value=\"$grade\" $selected>Grade $grade</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="active" <?php echo $section['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo $section['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Update Section</button>
                                        <a href="manage_sections.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        $('#editSectionForm').on('submit', function(e) {
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

            $.ajax({
                url: 'handlers/section_handler.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.close();
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Section updated successfully',
                            showConfirmButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'manage_sections.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update section'
                        });
                    }
                },
                error: function() {
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
</body>
</html>
