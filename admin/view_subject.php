<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_subjects.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get admin info
$admin_query = "SELECT * FROM admin WHERE admin_id = ?";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->bind_param("i", $admin_id);
$admin_stmt->execute();
$admin = $admin_stmt->get_result()->fetch_assoc();

$subject_id = $_GET['id'];

// Get subject details
$query = "SELECT s.*, 
    (SELECT COUNT(DISTINCT ss.teacher_id) 
     FROM section_subjects ss 
     WHERE ss.subject_id = s.id 
     AND ss.status = 'active') as teacher_count
    FROM subjects s 
    WHERE s.id = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

// Get assigned teachers
$teachers_query = "SELECT 
    ss.*,
    CONCAT(t.firstname, ' ', t.lastname) as teacher_name,
    d.department_name as department,
    CONCAT(s.grade_level, ' - ', s.section_name) as section_name,
    ay.school_year
    FROM section_subjects ss
    JOIN teacher t ON ss.teacher_id = t.teacher_id
    JOIN departments d ON t.department_id = d.department_id
    JOIN sections s ON ss.section_id = s.section_id
    JOIN academic_years ay ON ss.academic_year_id = ay.id
    WHERE ss.subject_id = ?
    ORDER BY ay.school_year DESC, ss.status DESC, s.grade_level ASC";

$teachers_stmt = $db->prepare($teachers_query);
$teachers_stmt->bind_param("i", $subject_id);
$teachers_stmt->execute();
$teachers_result = $teachers_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subject - <?php echo htmlspecialchars($subject['subject_code']); ?></title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .subject-header {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .teacher-card {
            background: #fff;
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            padding: 1.25rem;
            transition: transform 0.2s;
        }
        
        .teacher-card:hover {
            transform: translateY(-3px);
        }
        
        .status-active {
            color: #1cc88a;
            font-weight: 600;
        }
        
        .status-inactive {
            color: #e74a3b;
            font-weight: 600;
        }
        
        .content-wrapper {
            margin-left: 14rem;
            padding: 1.5rem;
            transition: margin 0.3s;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: #5a5c69;
            margin-bottom: 1.5rem;
        }
        
        .subject-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .subject-info .badge {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }
        
        .department-tag {
            background: #4e73df;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        
        .schedule-info {
            background: #f8f9fc;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- navigation -->
        <?php include 'includes/navigation.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Subject Details</h1>
                <a href="manage_subjects.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left fa-sm"></i> Back to Subjects
                </a>
            </div>

            <!-- Subject Header -->
            <div class="subject-header">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="h4 mb-2"><?php echo htmlspecialchars($subject['subject_code']); ?> - 
                            <?php echo htmlspecialchars($subject['subject_title']); ?></h2>
                        <div class="subject-info">
                            <span class="department-tag"><?php echo htmlspecialchars($subject['category']); ?></span>
                            <span class="badge badge-<?php echo $subject['status'] === 'active' ? 'success' : 'danger'; ?> ml-2">
                                <?php echo ucfirst($subject['status']); ?>
                            </span>
                        </div>
                        <p class="text-gray-600 mt-3"><?php echo htmlspecialchars($subject['description'] ?: 'No description available'); ?></p>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Assigned Teachers</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $subject['teacher_count']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Assignments -->
            <h3 class="section-title">Teacher Assignments</h3>
            <div class="row">
                <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="teacher-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><?php echo htmlspecialchars($teacher['teacher_name']); ?></h5>
                            <span class="status-<?php echo $teacher['status'] === 'active' ? 'active' : 'inactive'; ?>">
                                <i class="fas fa-circle fa-sm mr-1"></i><?php echo ucfirst($teacher['status']); ?>
                            </span>
                        </div>
                        <div class="department-tag mb-3"><?php echo htmlspecialchars($teacher['department']); ?></div>
                        <p class="mb-2"><i class="fas fa-users fa-fw mr-2"></i><?php echo htmlspecialchars($teacher['section_name']); ?></p>
                        <div class="schedule-info">
                            <p class="mb-1"><i class="fas fa-calendar-alt fa-fw mr-2"></i><?php echo htmlspecialchars($teacher['schedule_day']); ?></p>
                            <p class="mb-0"><i class="fas fa-clock fa-fw mr-2"></i><?php echo htmlspecialchars($teacher['schedule_time']); ?></p>
                        </div>
                        <p class="mt-2 mb-0"><i class="fas fa-graduation-cap fa-fw mr-2"></i>SY <?php echo htmlspecialchars($teacher['school_year']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php if ($teachers_result->num_rows === 0): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>No teachers currently assigned to this subject.
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>