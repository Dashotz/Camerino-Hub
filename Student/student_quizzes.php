<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch quizzes for the student's sections
$quizzes_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.type,
        a.due_date,
        a.points,
        a.quiz_link,
        sec.section_name,
        s.subject_code,
        s.subject_name,
        t.firstname as teacher_firstname,
        t.lastname as teacher_lastname,
        COALESCE(sas.points, NULL) as student_points,
        sas.submitted_at
    FROM student_sections ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN section_subjects ssub ON sec.section_id = ssub.section_id
    JOIN subjects s ON ssub.subject_id = s.id
    JOIN teacher t ON ssub.teacher_id = t.teacher_id
    JOIN activities a ON a.section_subject_id = ssub.id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND a.status = 'active'
        AND a.type = 'quiz'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    ORDER BY 
        CASE 
            WHEN sas.submitted_at IS NULL AND a.due_date >= CURDATE() THEN 1
            WHEN sas.submitted_at IS NULL AND a.due_date < CURDATE() THEN 2
            ELSE 3
        END,
        a.due_date ASC";

$stmt = $db->prepare($quizzes_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$quizzes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .quiz-card {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: box-shadow 0.3s;
            margin-bottom: 20px;
            background: #fff;
        }
        .quiz-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .quiz-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            position: relative;
        }
        .quiz-body {
            padding: 20px;
        }
        .quiz-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }
        .subject-tag {
            display: inline-block;
            padding: 4px 8px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        .status-indicator {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .teacher-info {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        .teacher-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .due-date {
            color: #666;
            font-size: 0.9rem;
        }
        .points-badge {
            background: #4caf50;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        /* Search Bar Styles */
.search-container {
    position: relative;
    max-width: 300px;
}

.search-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3498db;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

/* Header Layout */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.page-title {
    margin: 0;
    color: #2c3e50;
    font-size: 1.75rem;
    font-weight: 500;
}
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>My Quizzes</h1>
                <p class="text-muted">Complete your quizzes in a secure environment</p>
            </div>

            <!-- Filter Tabs -->
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="pending">To do</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="done">Done</a>
                </li>
            </ul>

            <?php if (empty($quizzes)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No quizzes available at the moment.
                </div>
            <?php else: ?>
                <div class="quiz-container">
                    <?php foreach ($quizzes as $quiz): ?>
                        <?php 
                        $is_submitted = !empty($quiz['submitted_at']);
                        $is_late = strtotime($quiz['due_date']) < time();
                        $status_class = $is_submitted ? 'success' : ($is_late ? 'danger' : 'warning');
                        $status_text = $is_submitted ? 'Done' : ($is_late ? 'Missing' : 'Assigned');
                        ?>
                        <div class="quiz-card">
                            <div class="quiz-header">
                                <span class="subject-tag">
                                    <?php echo htmlspecialchars($quiz['subject_code']); ?> - 
                                    <?php echo htmlspecialchars($quiz['section_name']); ?>
                                </span>
                                <span class="status-indicator badge badge-<?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                                <h5><?php echo htmlspecialchars($quiz['title']); ?></h5>
                                <div class="teacher-info">
                                    <div class="teacher-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <?php echo htmlspecialchars($quiz['teacher_firstname'] . ' ' . $quiz['teacher_lastname']); ?>
                                        <div class="due-date">
                                            Due <?php echo date('M j, Y', strtotime($quiz['due_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="quiz-body">
                                <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                                <?php if ($is_submitted): ?>
                                    <div class="mt-3">
                                        <span class="points-badge">
                                            <?php echo $quiz['student_points'] ?? 'Not graded'; ?> / 
                                            <?php echo $quiz['points']; ?> points
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="quiz-footer">
                                <?php if (!$is_submitted): ?>
                                    <a href="quiz_iframe.php?quiz_id=<?php echo $quiz['activity_id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-pencil-alt mr-2"></i>Take Quiz
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-check-circle"></i> Completed
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    function startQuiz(quizId) {
        // Show warning about quiz rules
        Swal.fire({
            title: 'Ready to start the quiz?',
            html: `
                <ul class="text-left">
                    <li>You must stay in fullscreen mode</li>
                    <li>Tab switching is not allowed</li>
                    <li>Time limits will be enforced</li>
                    <li>Ensure stable internet connection</li>
                </ul>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Start Quiz',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to quiz_iframe.php instead of take_quiz.php
                window.location.href = `quiz_iframe.php?quiz_id=${quizId}`;
            }
        });
    }

    // Filter functionality
    document.querySelectorAll('.nav-pills .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            
            document.querySelectorAll('.nav-pills .nav-link').forEach(l => 
                l.classList.remove('active'));
            this.classList.add('active');
            
            document.querySelectorAll('.quiz-card').forEach(card => {
                const status = card.querySelector('.status-indicator').textContent.trim().toLowerCase();
                if (filter === 'all' || 
                    (filter === 'pending' && status === 'assigned') ||
                    (filter === 'done' && status === 'done')) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html> 