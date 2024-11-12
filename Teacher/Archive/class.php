<?php
session_start();

// Initialize login status and role
$isLoggedIn = isset($_SESSION['id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Redirect if not logged in
if (!$isLoggedIn) {
    header("Location: ../Student/Teacher-Login.php");
    exit();
}

// Get teacher data
$userData = null;
if ($isLoggedIn) {
    require_once('../db/dbConnector.php');
    $db = new DbConnector();
    
    $teacher_id = $_SESSION['id'];
    $query = "SELECT * FROM teacher WHERE teacher_id = '$teacher_id'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_array($result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="class.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Enhanced Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            color: #495057;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .btn-class {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-class:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .action-buttons .btn {
            margin: 0 5px;
            border-radius: 20px;
            padding: 5px 15px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 35px;
            border-radius: 20px;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-link {
            border-radius: 20px;
            margin: 0 5px;
            color: #007bff;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .pagination .page-link:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
                    <span class="logo-text">Gov D.M. Camerino</span>
                </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="class.php">Class</a></li>
                    <li class="nav-item"><a class="nav-link" href="subject.php">Subject</a></li>
                    <li class="nav-item"><a class="nav-link" href="student.php">Student</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="teacher_dashboard.php">Dashboard</a>
                                <a class="dropdown-item" href="teacher_profile.php">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero section-gap">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Welcome<?php echo $isLoggedIn ? ', ' . htmlspecialchars($userData['firstname']) : ''; ?> to<br>
                        <span class="highlight">Gov D.M. Camerino</span>
                    </h1>
                    <p class="lead">Learn Anywhere, Anytime: Empower Your Education</p>
                    <?php if ($isLoggedIn): ?>
                    <div class="cta-buttons">
                        <a href="teacher_dashboard.php" class="btn btn-primary">Dashboard</a>
                        <a href="class.php" class="btn btn-outline-primary">Manage Classes</a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="hero-image">
                    <img src="../images/student.png" alt="Students">
                </div>
            </div>
            
            <div class="search-container">
                <input type="text" placeholder="Search something...">
                <button class="btn-search">Search</button>
            </div>
            
            <div class="quick-links">
                <p>You may be looking for</p>
                <div class="links">
                    <a href="home.php" class="link-item">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="class.php" class="link-item active">
                        <i class="fas fa-users"></i>
                        <span>Class</span>
                    </a>
                    <a href="subject.php" class="link-item">
                        <i class="fas fa-book"></i>
                        <span>Subjects</span>
                    </a>
                    <a href="student.php" class="link-item">
                        <i class="fas fa-user-graduate"></i>
                        <span>Student</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    <!-- Table Class Section -->
    <div class="container mt-5">
        <div class="table-container">
            <div class="table-header">
                <h2 class="mb-0"><i class="fas fa-chalkboard-teacher mr-2"></i>My Classes</h2>
                <div class="d-flex align-items-center">
                    <div class="dropdown mr-3">
                        <select class="form-control" id="recordsPerPage">
                            <option value="10">10 records</option>
                            <option value="25">25 records</option>
                            <option value="50">50 records</option>
                        </select>
                    </div>
                    <div class="search-box mr-3">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search classes...">
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-light" id="filterBtn">
                            <i class="fas fa-filter"></i>
                        </button>
                        <button class="btn btn-light" id="exportBtn">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Subject</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Modified query to match your exact database structure
                    $query = "SELECT c.class_id, c.course_id, c.subject_id, 
                              s.subject_code, s.subject_title,
                              COUNT(DISTINCT st.student_id) as student_count 
                              FROM class c
                              JOIN subjects s ON c.subject_id = CAST(s.subject_id AS VARCHAR(100))
                              LEFT JOIN student st ON c.subject_id = st.subject_id 
                              WHERE c.teacher_id = ? 
                              GROUP BY c.class_id, c.course_id, c.subject_id, s.subject_code, s.subject_title";

                    $stmt = $db->prepare($query);
                    $stmt->bind_param("s", $teacher_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <span class="badge badge-primary">
                                <?php echo htmlspecialchars($row['course_id']); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['subject_title']); ?>
                            <small class="text-muted d-block">
                                Code: <?php echo htmlspecialchars($row['subject_code']); ?>
                            </small>
                        </td>
                        <td><?php echo $row['student_count']; ?> students</td>
                        <td>
                            <a href="view_class.php?class_id=<?php echo $row['class_id']; ?>" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View Class
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            </nav>
        </div>
    </div>


      <!-- Footer -->
      <footer class="bg-primary text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">Gov D.M. Camerino</h5>
                    <p>Medicion 2, A.Imus City, Cavite 4103</p>
                    <p>+(64) 456 - 5874</p>
                    <p>profcamerino@yahoo.com</p>
                    <div class="social-icons">
                        <a href="#" class="text-light"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Quicklinks</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Home</a></li>
                        <li><a href="#" class="text-light">About Us</a></li>
                        <li><a href="#" class="text-light">Our Gallery</a></li>
                        <li><a href="#" class="text-light">News and Updates</a></li>
                        <li><a href="#" class="text-light">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Government Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Learner Information System</a></li>
                        <li><a href="#" class="text-light">DepEd CALABARZON</a></li>
                        <li><a href="#" class="text-light">DepEd Imus City</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; 2024 All Rights Reserved</p>
        </div>
    </footer>

</body>
</html>
