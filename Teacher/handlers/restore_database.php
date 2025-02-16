<?php
session_start();
require_once('../../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

class DatabaseRestore {
    private $db;
    private $backup_dir;
    
    public function __construct() {
        $this->db = new DbConnector();
        $this->backup_dir = dirname(dirname(dirname(__FILE__))) . '/backups/';
    }
    
    public function restore($backup_file) {
        try {
            // Validate file
            if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No backup file uploaded or upload error occurred');
            }

            // Check file extension
            $file_extension = strtolower(pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION));
            if ($file_extension !== 'sql') {
                throw new Exception('Invalid file type. Only SQL files are allowed.');
            }

            // Check file size (10MB limit)
            if ($_FILES['backup_file']['size'] > 10 * 1024 * 1024) {
                throw new Exception('File size too large. Maximum size is 10MB.');
            }

            // Create database connection
            $mysqli = new mysqli(
                $this->db->getHost(),
                $this->db->getUsername(),
                $this->db->getPassword(),
                $this->db->getDatabase()
            );

            if ($mysqli->connect_error) {
                throw new Exception("Connection failed: " . $mysqli->connect_error);
            }

            $mysqli->set_charset("utf8");

            // Read SQL file content
            $sql_content = file_get_contents($_FILES['backup_file']['tmp_name']);
            if ($sql_content === false) {
                throw new Exception('Failed to read backup file');
            }

            // Split file into individual queries
            $queries = array_filter(
                array_map(
                    'trim',
                    explode(';', $sql_content)
                ),
                'strlen'
            );

            // Begin transaction
            $mysqli->begin_transaction();

            try {
                // Disable foreign key checks
                $mysqli->query('SET FOREIGN_KEY_CHECKS=0');

                // Execute each query
                foreach ($queries as $query) {
                    if (!empty(trim($query)) && !$mysqli->query($query)) {
                        throw new Exception("Error executing query: " . $mysqli->error);
                    }
                }

                // Re-enable foreign key checks
                $mysqli->query('SET FOREIGN_KEY_CHECKS=1');

                // Commit transaction
                $mysqli->commit();

                return [
                    'success' => true,
                    'message' => 'Database restored successfully'
                ];

            } catch (Exception $e) {
                // Rollback on error
                $mysqli->rollback();
                throw $e;
            } finally {
                $mysqli->close();
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// Handle the restore request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');
        
        $restore = new DatabaseRestore();
        $result = $restore->restore($_FILES['backup_file']);
        
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
?> 