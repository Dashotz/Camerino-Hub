<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_GET['file'])) {
    showError('Unauthorized access');
}

try {
    $db = new DbConnector();
    $file_path = $_GET['file'];
    $teacher_id = $_SESSION['teacher_id'];

    // Verify teacher has access to this file
    $verify_query = "
        SELECT sf.file_path 
        FROM submission_files sf
        JOIN student_activity_submissions sas ON sf.submission_id = sas.submission_id
        JOIN activities a ON sas.activity_id = a.activity_id
        JOIN section_subjects ss ON a.section_subject_id = ss.id
        WHERE sf.file_path = ? AND ss.teacher_id = ?";

    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("si", $file_path, $teacher_id);
    $stmt->execute();
    
    if (!$stmt->get_result()->fetch_assoc()) {
        showError('Unauthorized access to file');
    }

    // Get the full file path
    $full_path = '../../' . $file_path;
    
    if (!file_exists($full_path)) {
        showError('File not found. The file might have been moved or deleted.');
    }

    // Get file info
    $file_name = basename($file_path);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_type = mime_content_type($full_path);

    // Set appropriate headers based on file type
    header('Content-Type: ' . $file_type);
    
    if ($file_ext === 'pdf') {
        // Display PDF inline
        header('Content-Disposition: inline; filename="' . $file_name . '"');
    } else {
        // Force download for other file types
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
    }

    header('Content-Length: ' . filesize($full_path));
    readfile($full_path);
    exit();

} catch (Exception $e) {
    showError($e->getMessage());
}

// Function to show error using SweetAlert
function showError($message) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: 'Error!',
                text: '<?php echo addslashes($message); ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                window.close(); // Close the window/tab after showing the error
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>
