<?php
require_once('../../db/dbConnector.php');

function deleteExpiredArchivedStudents() {
    $db = new DbConnector();
    
    // Get students archived more than 3 days ago
    $query = "SELECT student_id, firstname, lastname, 
              DATEDIFF(CURRENT_TIMESTAMP, updated_at) as days_archived 
              FROM student 
              WHERE status = 'archived' 
              AND DATEDIFF(CURRENT_TIMESTAMP, updated_at) > 3";
    
    $result = $db->query($query);
    $deleted = [];
    $errors = [];

    while ($row = $result->fetch_assoc()) {
        // Delete related records first (due to foreign key constraints)
        $deleteQueries = [
            "DELETE FROM student_sections WHERE student_id = ?",
            "DELETE FROM student_activity_submissions WHERE student_id = ?",
            "DELETE FROM student_login_logs WHERE student_id = ?",
            "DELETE FROM security_violations WHERE student_id = ?",
            "DELETE FROM remember_tokens WHERE student_id = ?",
            "DELETE FROM active_sessions WHERE student_id = ?",
            // Finally delete the student
            "DELETE FROM student WHERE student_id = ?"
        ];

        try {
            // Start transaction
            $db->begin_transaction();

            foreach ($deleteQueries as $deleteQuery) {
                $stmt = $db->prepare($deleteQuery);
                $stmt->bind_param("s", $row['student_id']);
                $stmt->execute();
            }

            // Commit transaction
            $db->commit();
            $deleted[] = "{$row['firstname']} {$row['lastname']} (ID: {$row['student_id']})";
        } catch (Exception $e) {
            // Rollback on error
            $db->rollback();
            $errors[] = "Failed to delete {$row['student_id']}: {$e->getMessage()}";
        }
    }

    // Log the results
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "=== Auto-Delete Run: $timestamp ===\n";
    $logMessage .= "Deleted Students:\n" . implode("\n", $deleted) . "\n";
    if (!empty($errors)) {
        $logMessage .= "Errors:\n" . implode("\n", $errors) . "\n";
    }
    $logMessage .= "=====================================\n\n";

    file_put_contents(__DIR__ . '/auto_delete.log', $logMessage, FILE_APPEND);

    return [
        'deleted' => count($deleted),
        'errors' => count($errors)
    ];
}

// Run the auto-delete function
$result = deleteExpiredArchivedStudents();
echo json_encode($result); 