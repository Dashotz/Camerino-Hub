<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch activities with files
$activities_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.type,
        a.due_date,
        a.points,
        s.subject_name,
        s.subject_code,
        sec.section_name,
        GROUP_CONCAT(DISTINCT af.file_id) as file_ids,
        GROUP_CONCAT(DISTINCT af.file_name) as file_names,
        GROUP_CONCAT(DISTINCT af.file_path) as file_paths,
        sas.submission_id,
        sas.points as achieved_points,
        sas.submitted_at,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'Submitted'
            WHEN a.due_date < NOW() THEN 'Overdue'
            ELSE 'Pending'
        END as status
    FROM student_sections ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN section_subjects ssub ON sec.section_id = ssub.section_id
    JOIN subjects s ON ssub.subject_id = s.id
    JOIN activities a ON ssub.id = a.section_subject_id
    LEFT JOIN activity_files af ON a.activity_id = af.activity_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND a.status = 'active'
        AND ssub.status = 'active'
    GROUP BY 
        a.activity_id,
        sas.submission_id
    ORDER BY a.due_date DESC";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .activity-files {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }

        .file-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .file-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .file-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .file-link {
            color: #333;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }

        .file-link:hover {
            text-decoration: none;
            color: #007bff;
        }

        .file-name {
            font-size: 0.95rem;
            margin-right: 10px;
        }

        .badge {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .card-header {
            position: relative;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        .activity-description {
            white-space: pre-line;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>My Activities</h1>
            </div>

            <div class="activities-container">
                <?php foreach ($activities as $activity): ?>
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo htmlspecialchars($activity['title']); ?></h5>
                            <span class="badge badge-<?php echo getStatusBadgeClass($activity['status']); ?>">
                                <?php echo $activity['status']; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="activity-description">
                                        <?php echo nl2br(htmlspecialchars($activity['description'])); ?>
                                    </p>
                                    
                                    <?php if ($activity['file_ids']): ?>
                                        <div class="activity-files mt-3">
                                            <h6><i class="fas fa-paperclip"></i> Activity Files:</h6>
                                            <div class="file-list">
                                                <?php
                                                $file_ids = explode(',', $activity['file_ids']);
                                                $file_names = explode(',', $activity['file_names']);
                                                
                                                for ($i = 0; $i < count($file_ids); $i++):
                                                    $file_extension = pathinfo($file_names[$i], PATHINFO_EXTENSION);
                                                    $icon_class = getFileIconClass($file_extension);
                                                ?>
                                                    <div class="file-item mb-2">
                                                        <a href="download_file.php?file_id=<?php echo $file_ids[$i]; ?>" 
                                                           class="btn btn-light btn-sm text-left d-flex align-items-center">
                                                            <i class="<?php echo $icon_class; ?> mr-2"></i>
                                                            <span class="file-name"><?php echo htmlspecialchars($file_names[$i]); ?></span>
                                                            <i class="fas fa-download ml-2 text-primary"></i>
                                                        </a>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <div class="activity-details">
                                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($activity['subject_code']); ?></p>
                                        <p><strong>Type:</strong> <?php echo ucfirst($activity['type']); ?></p>
                                        <p><strong>Due Date:</strong> <?php echo date('M d, Y h:i A', strtotime($activity['due_date'])); ?></p>
                                        <p><strong>Points:</strong> <?php echo $activity['points']; ?></p>
                                        <?php if ($activity['achieved_points'] !== null): ?>
                                            <p><strong>Score:</strong> <?php echo $activity['achieved_points']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Add loading animation when downloading
        $('.file-link').click(function() {
            const $fileItem = $(this);
            const $icon = $fileItem.find('.fa-download');
            
            $icon.removeClass('fa-download')
                 .addClass('fa-spinner fa-spin');
            
            setTimeout(() => {
                $icon.removeClass('fa-spinner fa-spin')
                     .addClass('fa-download');
            }, 1000);
        });

        // Add tooltips for long filenames
        $('.file-name').each(function() {
            const $this = $(this);
            if (this.offsetWidth < this.scrollWidth) {
                $this.attr('title', $this.text());
            }
        });
    });
    </script>
</body>
</html>

<?php
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Submitted':
            return 'success';
        case 'Overdue':
            return 'danger';
        case 'Pending':
            return 'warning';
        default:
            return 'secondary';
    }
}

function getFileIconClass($extension) {
    switch (strtolower($extension)) {
        case 'pdf':
            return 'fas fa-file-pdf text-danger';
        case 'doc':
        case 'docx':
            return 'fas fa-file-word text-primary';
        case 'xls':
        case 'xlsx':
            return 'fas fa-file-excel text-success';
        case 'ppt':
        case 'pptx':
            return 'fas fa-file-powerpoint text-warning';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'fas fa-file-image text-info';
        case 'zip':
        case 'rar':
            return 'fas fa-file-archive text-secondary';
        default:
            return 'fas fa-file text-secondary';
    }
}
?>