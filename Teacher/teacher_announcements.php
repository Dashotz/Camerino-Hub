<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get teacher's sections and their corresponding subjects
$sections_query = "
    SELECT DISTINCT 
        ss.section_id,
        s.section_name,
        ss.subject_id,
        sub.subject_name
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ? 
    AND ss.status = 'active'
    AND s.status = 'active'
    ORDER BY s.section_name";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$sections_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Teacher Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/announcements.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="welcome-section">
                <h1>Announcements</h1>
                <p>Create and manage your class announcements</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="announcements-header">
                        <div class="filters">
                            <select class="form-control" id="sectionFilter">
                                <option value="">All Sections</option>
                                <?php 
                                $sections_result->data_seek(0);
                                while ($row = $sections_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['section_id']; ?>" 
                                            data-subject-id="<?php echo $row['subject_id']; ?>">
                                        <?php echo htmlspecialchars($row['section_name']); ?> - 
                                        <?php echo htmlspecialchars($row['subject_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createAnnouncementModal">
                            <i class="fas fa-plus"></i> New Announcement
                        </button>
                    </div>

                    <div class="announcements-list" id="announcementsList">
                        <!-- Announcements will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createAnnouncementModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createAnnouncementForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Announcement Type</label>
                            <select class="form-control" name="type" id="announcement_type" required>
                                <option value="normal">Normal Announcement</option>
                                <option value="quiz">Quiz Announcement</option>
                                <option value="activity">Activity Announcement</option>
                                <option value="assignment">Assignment Announcement</option>
                            </select>
                        </div>

                        <div class="form-group reference-selector" style="display: none;">
                            <label>Select Reference</label>
                            <select class="form-control" name="reference_id" id="reference_id">
                                <option value="">Select...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Section</label>
                            <select class="form-control" name="section_id" id="create_section_id" required>
                                <option value="">Select Section</option>
                                <?php 
                                $sections_result->data_seek(0);
                                while ($row = $sections_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['section_id']; ?>" 
                                            data-subject-id="<?php echo $row['subject_id']; ?>"
                                            data-subject-name="<?php echo htmlspecialchars($row['subject_name']); ?>">
                                        <?php echo htmlspecialchars($row['section_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" id="create_subject_display" readonly>
                            <input type="hidden" name="subject_id" id="create_subject_id" required>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea class="form-control" name="content" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Attachment (optional)</label>
                            <input type="file" class="form-control-file" name="attachment">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createAnnouncement">Create Announcement</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAnnouncementForm" enctype="multipart/form-data">
                        <input type="hidden" name="announcement_id" id="edit_announcement_id">
                        <div class="form-group">
                            <label>Section</label>
                            <select class="form-control" name="section_id" id="edit_section_id" required>
                                <option value="">Select Section</option>
                                <?php 
                                $sections_result->data_seek(0);
                                while ($row = $sections_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['section_id']; ?>" 
                                            data-subject-id="<?php echo $row['subject_id']; ?>"
                                            data-subject-name="<?php echo htmlspecialchars($row['subject_name']); ?>">
                                        <?php echo htmlspecialchars($row['section_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" id="edit_subject_display" readonly>
                            <input type="hidden" name="subject_id" id="edit_subject_id" required>
                        </div>
                        <div class="form-group">
                            <lab el>Content</label>
                            <textarea class="form-control" name="content" id="edit_content" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>New Attachment (optional)</label>
                            <input type="file" class="form-control-file" name="attachment">
                            <div id="current_attachment" class="mt-2"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateAnnouncement">Update Announcement</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this announcement? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/announcements.js"></script>
</body>
</html>
