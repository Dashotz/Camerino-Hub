<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher details
$query = "SELECT 
    t.*,
    d.department_name,
    COUNT(DISTINCT ss.section_id) as total_sections,
    COUNT(DISTINCT ss.subject_id) as total_subjects
FROM teacher t
LEFT JOIN departments d ON t.department_id = d.department_id
LEFT JOIN section_subjects ss ON t.teacher_id = ss.teacher_id
    AND ss.status = 'active'
    AND ss.academic_year_id = (
        SELECT id FROM academic_years
        WHERE status = 'active'
        LIMIT 1
    )
WHERE t.teacher_id = ?
GROUP BY t.teacher_id, d.department_name";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

// If no sections/subjects found, set defaults
if ($teacher) {
    $teacher['total_sections'] = $teacher['total_sections'] ?? 0;
    $teacher['total_subjects'] = $teacher['total_subjects'] ?? 0;
}

// Get active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    
    <style>
        .profile-header {
            background: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-stats {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-pills .nav-link {
            color: #495057;
            background: #fff;
            margin-bottom: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link.active {
            background: #007bff;
            color: #fff;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .form-control {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        
        .btn-primary {
            border-radius: 5px;
            padding: 0.5rem 1.5rem;
        }
        
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <!-- Profile Header -->
                <div class="profile-header text-center">
                    <img src="<?php echo $teacher['profile_image'] ?? '../images/teacher.png'; ?>" 
                         alt="Profile" class="profile-image mb-3">
                    <h2><?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?></h2>
                    <p class="text-muted"><?php echo htmlspecialchars($teacher['department_name'] ?? 'Department not assigned'); ?></p>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <!-- Profile Stats -->
                        <div class="profile-stats mb-4">
                            <h5 class="mb-3">Statistics</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sections:</span>
                                <span class="badge badge-primary"><?php echo $teacher['total_sections']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subjects:</span>
                                <span class="badge badge-info"><?php echo $teacher['total_subjects']; ?></span>
                            </div>
                        </div>

                        <!-- Navigation Pills -->
                        <div class="nav flex-column nav-pills" role="tablist">
                            <a class="nav-link <?php echo $activeTab === 'profile' ? 'active' : ''; ?>" 
                               href="?tab=profile">
                                <i class="fas fa-user mr-2"></i>Profile Information
                            </a>
                            <a class="nav-link <?php echo $activeTab === 'settings' ? 'active' : ''; ?>" 
                               href="?tab=settings">
                                <i class="fas fa-cog mr-2"></i>Account Settings
                            </a>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="tab-content">
                            <?php if ($activeTab === 'profile'): ?>
                                <!-- Profile Tab -->
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Profile Information</h4>
                                        <hr>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Name:</strong></div>
                                            <div class="col-md-8">
                                                <?php echo htmlspecialchars($teacher['firstname'] . ' ' . 
                                                ($teacher['middlename'] ? $teacher['middlename'] . ' ' : '') . 
                                                $teacher['lastname']); ?>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Email:</strong></div>
                                            <div class="col-md-8"><?php echo htmlspecialchars($teacher['email']); ?></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Department:</strong></div>
                                            <div class="col-md-8">
                                                <?php echo htmlspecialchars($teacher['department_name'] ?? 'Not Assigned'); ?>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Username:</strong></div>
                                            <div class="col-md-8"><?php echo htmlspecialchars($teacher['username']); ?></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Status:</strong></div>
                                            <div class="col-md-8">
                                                <span class="badge badge-<?php echo $teacher['status'] === 'active' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($teacher['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Settings Tab -->
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Account Settings</h4>
                                        <hr>
                                        <form id="updateProfileForm" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Profile Image</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="profileImage" name="profile_image" accept="image/*">
                                                    <label class="custom-file-label" for="profileImage">Choose file</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" 
                                                       value="<?php echo htmlspecialchars($teacher['email']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Current Password</label>
                                                <input type="password" class="form-control" name="current_password">
                                            </div>
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input type="password" class="form-control" name="new_password">
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm New Password</label>
                                                <input type="password" class="form-control" name="confirm_password">
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-2"></i>Update Profile
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <!-- Your existing JavaScript for form handling -->
    <script>
    $(document).ready(function() {
        // Custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Form submission
        $('#updateProfileForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'update_profile.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Profile updated successfully',
                            icon: 'success',
                            confirmButtonColor: '#007bff'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonColor: '#007bff'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update profile',
                        icon: 'error',
                        confirmButtonColor: '#007bff'
                    });
                }
            });
        });
    });
    </script>
</body>
</html> 