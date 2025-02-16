<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get teacher's subjects and their corresponding sections
$subjects_query = "
    SELECT DISTINCT 
        ss.subject_id,
        sub.subject_name,
        sub.subject_code,
        GROUP_CONCAT(DISTINCT ss.section_id) as section_ids,
        GROUP_CONCAT(DISTINCT s.section_name) as section_names
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ? 
    AND ss.status = 'active'
    AND s.status = 'active'
    AND sub.status = 'active'
    GROUP BY ss.subject_id, sub.subject_name, sub.subject_code
    ORDER BY sub.subject_name";

$stmt = $db->prepare($subjects_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$subjects_result = $stmt->get_result();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
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
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createAnnouncementModal">
                                <i class="fas fa-plus"></i> New Announcement
                            </button>
                        </div>
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
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Announcement Type</label>
                            <select class="form-control" name="type" id="announcement_type" required>
                                <option value="normal">Normal Announcement</option>
                                <option value="quiz">Quiz Announcement</option>
                                <option value="activity">Activity Announcement</option>
                                <option value="assignment">Assignment Announcement</option>
                            </select>
                        </div>

                        <!-- Dynamic fields for assignments/quizzes/activities -->
                        <div class="form-group assignment-fields" style="display: none;">
                            <label>Due Date</label>
                            <input type="datetime-local" class="form-control" name="due_date">
                            
                            <label>Points</label>
                            <input type="number" class="form-control" name="points" min="0">
                            
                            <label>Instructions</label>
                            <textarea class="form-control" name="instructions" rows="3"></textarea>
                        </div>

                        <div class="form-group reference-selector" style="display: none;">
                            <label>Select Reference</label>
                            <select class="form-control" name="reference_id" id="reference_id">
                                <option value="">Select...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Subject (Announcement will be posted to all assigned sections)</label>
                            <select class="form-control" name="subject_id" id="create_subject_id" required>
                                <option value="">Select Subject</option>
                                <?php 
                                $stmt = $db->prepare($subjects_query);
                                $stmt->bind_param("i", $teacher_id);
                                $stmt->execute();
                                $subjects_result = $stmt->get_result();
                                
                                while ($row = $subjects_result->fetch_assoc()): 
                                    $sections = explode(',', $row['section_names']);
                                ?>
                                    <option value="<?php echo $row['subject_id']; ?>" 
                                            data-section-ids="<?php echo $row['section_ids']; ?>"
                                            data-sections="<?php echo htmlspecialchars(implode(', ', $sections)); ?>">
                                        <?php echo htmlspecialchars($row['subject_code'] . ' - ' . $row['subject_name']); ?>
                                        (<?php echo count($sections); ?> sections)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <small class="form-text text-muted" id="sections-info"></small>
                        </div>
                        <input type="hidden" name="section_ids" id="create_section_ids">
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
                    <button type="button" class="btn btn-primary" id="createAnnouncementBtn">Create Announcement</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAnnouncementForm" enctype="multipart/form-data">
                        <input type="hidden" id="edit_announcement_id" name="announcement_id">
                        
                        <div class="form-group">
                            <label for="edit_subject_id">Subject</label>
                            <select class="form-control" id="edit_subject_id" name="subject_id" required>
                                <option value="">Select Subject</option>
                                <?php 
                                $subjects_query = "
                                    SELECT DISTINCT 
                                        ss.subject_id,
                                        sub.subject_name,
                                        sub.subject_code,
                                        GROUP_CONCAT(DISTINCT s.section_name) as sections
                                    FROM section_subjects ss
                                    JOIN sections s ON ss.section_id = s.section_id
                                    JOIN subjects sub ON ss.subject_id = sub.id
                                    WHERE ss.teacher_id = ? 
                                    AND ss.status = 'active'
                                    AND s.status = 'active'
                                    GROUP BY ss.subject_id, sub.subject_name, sub.subject_code
                                    ORDER BY sub.subject_name";
                                
                                $stmt = $db->prepare($subjects_query);
                                $stmt->bind_param("i", $teacher_id);
                                $stmt->execute();
                                $subjects_result = $stmt->get_result();
                                
                                while ($row = $subjects_result->fetch_assoc()) {
                                    $sections = explode(',', $row['sections']);
                                    echo "<option value='{$row['subject_id']}'>{$row['subject_code']} - {$row['subject_name']} (" . count($sections) . " sections)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_content">Content</label>
                            <textarea class="form-control" id="edit_content" name="content" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_attachment">New Attachment (optional)</label>
                            <input type="file" class="form-control-file" id="edit_attachment" name="attachment">
                            <div id="current_attachment_info" class="mt-2">
                                <!-- Current attachment info will be populated dynamically -->
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Announcement</button>
                        </div>
                    </form>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle subject change
        $('#create_subject_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var sectionIds = selectedOption.data('section-ids');
            var sections = selectedOption.data('sections');
            
            if (sectionIds && sections) {
                $('#create_section_ids').val(sectionIds);
                $('#sections-info').text('Will be posted to sections: ' + sections);
            } else {
                $('#create_section_ids').val('');
                $('#sections-info').text('');
            }
        });

        // Handle announcement type change
        $('#announcement_type').on('change', function() {
            var type = $(this).val();
            if (type !== 'normal') {
                $('.reference-selector').show();
                // Load references based on type
                loadReferences(type);
            } else {
                $('.reference-selector').hide();
            }
        });

        // Handle create announcement button click
        $('#createAnnouncementBtn').on('click', function() {
            // Validate required fields
            if (!$('#create_subject_id').val()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a subject'
                });
                return;
            }

            var formData = new FormData($('#createAnnouncementForm')[0]);
            formData.append('action', 'create');

            $.ajax({
                url: 'handlers/announcement_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Raw response:', response); // Debug line
                    try {
                        // Handle both string and object responses
                        const result = typeof response === 'object' ? response : JSON.parse(response);
                        
                        if (result.success) {
                            // Clear form and close modal
                            $('#createAnnouncementModal').modal('hide');
                            $('#createAnnouncementForm')[0].reset();
                            $('#sections-info').text('');
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: result.message || 'Announcement created successfully',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Reload announcements after the alert closes
                                loadAnnouncements();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Failed to create announcement'
                            });
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        console.error('Response:', response);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Server returned invalid response'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    console.error('Response Text:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to create announcement: ' + error
                    });
                }
            });
        });

        // Function to load references based on type
        function loadReferences(type) {
            $.ajax({
                url: 'handlers/get_references.php',
                type: 'GET',
                data: { type: type },
                success: function(response) {
                    try {
                        var references = JSON.parse(response);
                        var select = $('#reference_id');
                        select.empty();
                        select.append('<option value="">Select...</option>');
                        references.forEach(function(ref) {
                            select.append(`<option value="${ref.id}">${ref.title}</option>`);
                        });
                    } catch (e) {
                        console.error('Failed to parse references');
                    }
                }
            });
        }

        // Function to edit announcement
        function editAnnouncement(announcementId) {
            $.ajax({
                url: 'handlers/announcement_handler.php',
                type: 'POST',
                data: {
                    action: 'get_announcement',
                    announcement_id: announcementId
                },
                success: function(response) {
                    if (response.success) {
                        const announcement = response.data;
                        
                        // Populate the edit form
                        $('#edit_announcement_id').val(announcement.id);
                        $('#edit_subject_id').val(announcement.subject_id);
                        $('#edit_content').val(announcement.content);
                        
                        // Handle attachment display
                        if (announcement.attachment) {
                            $('#current_attachment_info').html(`
                                Current attachment: 
                                <a href="${announcement.attachment}" target="_blank">View</a>
                                <small class="text-muted">Upload a new file to replace the current attachment</small>
                            `);
                        } else {
                            $('#current_attachment_info').html('<small class="text-muted">No current attachment</small>');
                        }
                        
                        // Show the modal
                        $('#editAnnouncementModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fetch announcement details'
                        });
                    }
                }
            });
        }

        // Handle edit form submission
        $('#editAnnouncementForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('action', 'update');

            $.ajax({
                url: 'handlers/announcement_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = typeof response === 'object' ? response : JSON.parse(response);
                        
                        if (result.success) {
                            $('#editAnnouncementModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: result.message || 'Announcement updated successfully',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message || 'Failed to update announcement'
                            });
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Server returned invalid response'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update announcement: ' + error
                    });
                }
            });
        });

        // Function to fetch subjects
        function loadSubjectsForEdit(selectedSubjectId) {
            $.ajax({
                url: 'handlers/announcement_handler.php',
                type: 'POST',
                data: {
                    action: 'get_teacher_subjects'
                },
                success: function(response) {
                    try {
                        const data = (typeof response === 'string') ? JSON.parse(response) : response;
                        if (data.success) {
                            const selectElement = $('#edit_subject_id');
                            selectElement.empty();
                            
                            // Add a default option
                            selectElement.append(new Option('Please select a subject', ''));
                            
                            data.subjects.forEach(function(subject) {
                                const option = new Option(subject.subject_name, subject.id);
                                selectElement.append(option);
                            });
                            
                            // Set the selected subject if provided
                            if (selectedSubjectId) {
                                selectElement.val(selectedSubjectId);
                            }
                        } else {
                            console.error('Failed to load subjects:', data.message);
                        }
                    } catch (e) {
                        console.error('Error parsing subjects:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });
        }

        // Add this inside your existing $(document).ready() function
        $('#edit_subject_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var sectionIds = selectedOption.data('section-ids');
            var sections = selectedOption.data('sections');
            
            if (sections) {
                $('#edit-sections-info').text('Available sections: ' + sections);
                loadSectionsForSubject($(this).val());
            } else {
                $('#edit-sections-info').text('');
                $('#edit_section_id').empty().append('<option value="">Select Section</option>');
            }
        });

        function loadSectionsForSubject(subjectId) {
            $.ajax({
                url: 'handlers/announcement_handler.php',
                type: 'POST',
                data: {
                    action: 'get_sections',
                    subject_id: subjectId
                },
                success: function(response) {
                    if (response.success) {
                        var select = $('#edit_section_id');
                        select.empty();
                        select.append('<option value="">Select Section</option>');
                        
                        response.sections.forEach(function(section) {
                            select.append(`<option value="${section.id}">${section.name}</option>`);
                        });
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
