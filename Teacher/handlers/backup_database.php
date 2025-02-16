<?php
// Turn off error reporting for the entire file
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buffer output to prevent any unwanted output before JSON
ob_start();

require_once('../../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

class DatabaseBackup {
    private $db;
    private $backup_dir;
    
    public function __construct() {
        $this->db = new DbConnector();
        $this->backup_dir = dirname(dirname(dirname(__FILE__))) . '/backups/';
        
        if (!file_exists($this->backup_dir)) {
            if (!@mkdir($this->backup_dir, 0755, true)) {
                throw new Exception("Failed to create backup directory");
            }
            
            // Create .htaccess to protect the backup directory
            $htaccess = $this->backup_dir . '.htaccess';
            if (!file_exists($htaccess)) {
                file_put_contents($htaccess, "Order Deny,Allow\nDeny from all");
            }
        }
    }
    
    public function createBackup($type = 'full', $teacher_id = null) {
        try {
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
            
            // Generate backup filename
            $backup_file = $this->backup_dir . $this->db->getDatabase() . '_' . 
                          $type . '_' . date('Y-m-d_H-i-s') . '.sql';
            
            $handle = fopen($backup_file, 'w');
            if ($handle === false) {
                throw new Exception("Unable to create backup file");
            }

            // Write header
            fwrite($handle, "-- Database backup created on " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            // Get tables based on backup type
            $tables = $this->getTablesForBackup($type, $teacher_id, $mysqli);

            foreach ($tables as $table) {
                // Get create table syntax
                $result = $mysqli->query("SHOW CREATE TABLE `$table`");
                if (!$result) {
                    continue;
                }
                $row = $result->fetch_row();
                fwrite($handle, "\n\n" . $row[1] . ";\n\n");

                // Get table data with conditions if needed
                $where = $this->getWhereClause($table, $type, $teacher_id);
                $query = "SELECT * FROM `$table`" . ($where ? " WHERE $where" : "");
                $result = $mysqli->query($query);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $values = array_map(function($value) use ($mysqli) {
                            if ($value === null) return 'NULL';
                            return "'" . $mysqli->real_escape_string($value) . "'";
                        }, $row);
                        
                        fwrite($handle, "INSERT INTO `$table` VALUES (" . 
                               implode(", ", $values) . ");\n");
                    }
                }
            }

            // Write footer
            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
            $mysqli->close();

            // Verify backup file
            if (!file_exists($backup_file) || filesize($backup_file) === 0) {
                throw new Exception("Backup file was not created successfully");
            }

            // Cleanup old backups
            $this->cleanupOldBackups();

            return [
                'success' => true,
                'message' => 'Backup created successfully',
                'file' => basename($backup_file)
            ];

        } catch (Exception $e) {
            if (isset($handle)) fclose($handle);
            if (isset($mysqli)) $mysqli->close();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function getTablesForBackup($type, $teacher_id, $mysqli) {
        switch ($type) {
            case 'students':
                return ['student', 'student_sections', 'student_activity_submissions', 
                        'student_answers', 'student_grades'];
            case 'activities':
                return ['activities', 'activity_files', 'quiz_questions', 
                        'question_choices', 'student_activity_submissions', 
                        'student_answers'];
            case 'classes':
                return ['sections', 'section_subjects', 'student_sections', 
                        'activities', 'student_activity_submissions'];
            case 'full':
                $tables = [];
                $result = $mysqli->query("SHOW TABLES");
                while ($row = $result->fetch_row()) {
                    $tables[] = $row[0];
                }
                return $tables;
            default:
                throw new Exception('Invalid backup type');
        }
    }

    private function getWhereClause($table, $type, $teacher_id) {
        if (!$teacher_id || $type === 'full') return '';
        
        switch ($table) {
            case 'student_sections':
            case 'section_subjects':
                return "section_id IN (SELECT section_id FROM sections WHERE adviser_id = $teacher_id)";
            case 'activities':
            case 'activity_files':
                return "teacher_id = $teacher_id";
            case 'student_activity_submissions':
                return "activity_id IN (SELECT activity_id FROM activities WHERE teacher_id = $teacher_id)";
            default:
                return '';
        }
    }

    public function getBackupHistory() {
        try {
            $backups = [];
            
            if (!is_dir($this->backup_dir)) {
                return ['success' => true, 'data' => []];
            }
            
            $files = glob($this->backup_dir . '*.sql');
            
            if ($files === false) {
                return ['success' => true, 'data' => []];
            }
            
            foreach ($files as $file) {
                if (is_readable($file)) {
                    $backups[] = [
                        'filename' => basename($file),
                        'date' => date('Y-m-d H:i:s', filemtime($file)),
                        'size' => $this->formatSize(filesize($file))
                    ];
                }
            }
            
            // Sort by date (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            return ['success' => true, 'data' => $backups];
        } catch (Exception $e) {
            error_log("Error getting backup history: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    private function cleanupOldBackups() {
        try {
            $files = glob($this->backup_dir . '*.sql');
            if (count($files) > 5) {
                // Sort files by modification time
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                // Delete all but the 5 most recent files
                for ($i = 5; $i < count($files); $i++) {
                    if (is_writable($files[$i])) {
                        @unlink($files[$i]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error cleaning up old backups: " . $e->getMessage());
        }
    }

    // Rest of the class methods remain the same...
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['backup_db'])) {
    try {
        $backup = new DatabaseBackup();
        $backup_type = isset($_POST['backup_type']) ? $_POST['backup_type'] : 'full';
        
        if (!in_array($backup_type, ['full', 'students', 'activities', 'classes'])) {
            throw new Exception('Invalid backup type selected');
        }
        
        $result = $backup->createBackup($backup_type, $_SESSION['teacher_id']);
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
        
    } catch (Exception $e) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

// For AJAX requests to get backup history
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json');
        $backup = new DatabaseBackup();
        echo json_encode($backup->getBackupHistory());
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
?> 