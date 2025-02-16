<?php
session_start();
require_once('../../db/dbConnector.php');

define('DEBUG_MODE', true);

function debug($data) {
    if (DEBUG_MODE) {
        error_log(print_r($data, true));
    }
}

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$db = new DbConnector();

$action = $_REQUEST['action'] ?? '';
debug("Received action: " . $action);

switch ($action) {
    case 'get_students':
        debug("Getting students");
        getStudents($db);
        break;
    case 'add_student':
        addStudent($db);
        break;
    case 'edit_student':
        editStudent($db);
        break;
    case 'archive_student':
        archiveStudent($db);
        break;
    case 'get_student_details':
        getStudentDetails($db);
        break;
    case 'get_archived_students':
        try {
            $query = "SELECT student_id, lrn, firstname, lastname, email, status, 
                            contact_number, gender, middlename 
                     FROM student 
                     WHERE status = 'archived' 
                     ORDER BY student_id DESC";
                      
            $result = $db->query($query);
            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = [
                    'student_id' => $row['student_id'],
                    'lrn' => htmlspecialchars($row['lrn']),
                    'firstname' => htmlspecialchars($row['firstname']),
                    'lastname' => htmlspecialchars($row['lastname']),
                    'email' => htmlspecialchars($row['email'] ?? '-'),
                    'status' => htmlspecialchars($row['status'])
                ];
            }

            echo json_encode([
                'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                'recordsTotal' => count($students),
                'recordsFiltered' => count($students),
                'data' => $students
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
        break;
    case 'restore_student':
        if (!isset($_POST['student_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Student ID is required']);
            break;
        }

        $studentId = $_POST['student_id'];
        $query = "UPDATE student SET status = 'active' WHERE student_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $studentId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student restored successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to restore student']);
        }
        break;
    case 'delete_archived_student':
        if (!isset($_POST['student_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Student ID is required']);
            break;
        }

        $studentId = $_POST['student_id'];
        
        // Check if student is actually archived
        $checkQuery = "SELECT status FROM student WHERE student_id = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->bind_param("s", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        if (!$student || $student['status'] !== 'archived') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid student or student is not archived']);
            break;
        }

        try {
            $db->begin_transaction();

            // Delete related records first
            $deleteQueries = [
                "DELETE FROM student_sections WHERE student_id = ?",
                "DELETE FROM student_activity_submissions WHERE student_id = ?",
                "DELETE FROM student_login_logs WHERE student_id = ?",
                "DELETE FROM security_violations WHERE student_id = ?",
                "DELETE FROM remember_tokens WHERE student_id = ?",
                "DELETE FROM active_sessions WHERE student_id = ?",
                "DELETE FROM student WHERE student_id = ?"
            ];

            foreach ($deleteQueries as $query) {
                $stmt = $db->prepare($query);
                $stmt->bind_param("s", $studentId);
                $stmt->execute();
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Student account permanently deleted']);
        } catch (Exception $e) {
            $db->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete student: ' . $e->getMessage()]);
        }
        break;
    case 'get_active_students':
        try {
            // Set header before any output
            header('Content-Type: application/json');
            
            $query = "SELECT 
                student_id,
                lrn,
                firstname,
                lastname,
                middlename,
                email,
                contact_number,
                gender,
                status
            FROM student 
            WHERE status = 'active'
            ORDER BY lastname ASC, firstname ASC";

            $result = $db->query($query);
            
            if (!$result) {
                throw new Exception("Database query failed: " . $db->error);
            }

            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = [
                    'student_id' => $row['student_id'],
                    'lrn' => htmlspecialchars($row['lrn']),
                    'firstname' => htmlspecialchars($row['firstname']),
                    'lastname' => htmlspecialchars($row['lastname']),
                    'middlename' => htmlspecialchars($row['middlename'] ?? ''),
                    'email' => htmlspecialchars($row['email'] ?? ''),
                    'contact_number' => htmlspecialchars($row['contact_number'] ?? ''),
                    'gender' => htmlspecialchars($row['gender'] ?? ''),
                    'status' => htmlspecialchars($row['status'])
                ];
            }

            echo json_encode([
                'data' => $students
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'data' => []
            ]);
        }
        break;
    case 'import_students':
        try {
            if (!isset($_FILES['file'])) {
                throw new Exception('No file uploaded');
            }

            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload error: ' . $file['error']);
            }

            $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file type
            if (!in_array($fileType, ['csv', 'xlsx', 'xls'])) {
                throw new Exception('Invalid file type. Please upload CSV or Excel files.');
            }

            $added = 0;
            $skipped = 0;
            $errors = [];
            
            if ($fileType === 'csv') {
                // Process CSV file
                $handle = @fopen($file['tmp_name'], "r");
                if ($handle === FALSE) {
                    throw new Exception('Failed to open uploaded file');
                }

                try {
                    // Skip header row
                    fgetcsv($handle);
                    $rowNumber = 2; // Start from row 2 (after header)
                    
                    while (($data = fgetcsv($handle)) !== FALSE) {
                        try {
                            if (empty(array_filter($data))) continue; // Skip empty rows
                            
                            if (count($data) < 7) {
                                throw new Exception("Invalid number of columns");
                            }

                            $lrn = trim((string)$data[0]);
                            $firstname = trim((string)$data[1]);
                            $middlename = trim((string)$data[2]);
                            $lastname = trim((string)$data[3]);
                            $email = trim((string)$data[4]);
                            $contact = trim((string)$data[5]);
                            $gender = trim((string)$data[6]);

                            // Basic validation
                            if (empty($lrn) || empty($firstname) || empty($lastname)) {
                                throw new Exception("Missing required fields");
                            }

                            // Validate LRN format (12 digits)
                            $lrn = preg_replace('/[^0-9]/', '', $lrn); // Remove any non-numeric characters
                            if (!preg_match('/^\d{12}$/', $lrn)) {
                                throw new Exception("Invalid LRN format - must be 12 digits");
                            }

                            // Check if LRN already exists
                            $checkStmt = $db->prepare("SELECT student_id FROM student WHERE lrn = ?");
                            $checkStmt->bind_param("s", $lrn);
                            $checkStmt->execute();
                            if ($checkStmt->get_result()->num_rows > 0) {
                                throw new Exception("LRN already exists");
                            }

                            // Validate gender
                            $gender = ucfirst(strtolower($gender));
                            if (!in_array($gender, ['Male', 'Female'])) {
                                throw new Exception("Invalid gender - must be 'Male' or 'Female'");
                            }

                            // Generate default password
                            $current_year = date('Y');
                            $default_password = strtolower($firstname . $current_year . $lastname);
                            $default_password = preg_replace('/[^a-z0-9]/', '', $default_password);
                            $hashed_password = md5($default_password);

                            // Insert student
                            $stmt = $db->prepare("INSERT INTO student (lrn, password, firstname, lastname, middlename, email, contact_number, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            if (!$stmt) {
                                throw new Exception("Database prepare error: " . $db->error);
                            }

                            $stmt->bind_param("ssssssss", $lrn, $hashed_password, $firstname, $lastname, $middlename, $email, $contact, $gender);

                            if ($stmt->execute()) {
                                $added++;
                            } else {
                                throw new Exception("Database error: " . $stmt->error);
                            }

                        } catch (Exception $e) {
                            $skipped++;
                            $errors[] = "Row $rowNumber: " . $e->getMessage();
                        }
                        $rowNumber++;
                    }
                } finally {
                    fclose($handle);
                }
            } else {
                // Process Excel file
                require_once __DIR__ . '/../../vendor/autoload.php';
                
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
                $spreadsheet = $reader->load($file['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Skip header row
                $rowNumber = 2;
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (!empty(array_filter($row))) { // Skip empty rows
                        processStudentImport($row, $db, $added, $skipped, $errors, $rowNumber);
                    }
                    $rowNumber++;
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'added' => $added,
                'skipped' => $skipped,
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            error_log("Import error: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => isset($errors) ? $errors : []
            ]);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function getStudents($db) {
    try {
        debug("Starting getStudents function");
        
        $query = "SELECT 
            s.student_id,
            s.username,
            s.firstname,
            s.lastname,
            s.middlename,
            s.email,
            s.cys,
            s.status
        FROM student s
        WHERE s.status != 'deleted'
        ORDER BY s.lastname ASC, s.firstname ASC";

        $result = $db->query($query);
        
        if (!$result) {
            throw new Exception("Database query failed: " . $db->error);
        }

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'student_id' => $row['student_id'],
                'username' => htmlspecialchars($row['username']),
                'firstname' => htmlspecialchars($row['firstname']),
                'lastname' => htmlspecialchars($row['lastname']),
                'middlename' => htmlspecialchars($row['middlename'] ?? ''),
                'email' => htmlspecialchars($row['email']),
                'cys' => htmlspecialchars($row['cys']),
                'section_name' => 'Not Assigned', // Default value
                'status' => htmlspecialchars($row['status'])
            ];
        }

        debug("Found " . count($students) . " students");

        header('Content-Type: application/json');
        echo json_encode([
            'data' => $students
        ]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $e->getMessage(),
            'data' => []
        ]);
    }
}

function addStudent($db) {
    header('Content-Type: application/json');
    
    try {
        // Validate required fields
        $required_fields = ['lrn', 'firstname', 'lastname', 'gender'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Generate default password using the new format
        $current_year = date('Y');
        $default_password = strtolower($_POST['firstname'] . $current_year . $_POST['lastname']);
        // Remove spaces and special characters
        $default_password = preg_replace('/[^a-z0-9]/', '', $default_password);
        
        // Hash the password using MD5
        $hashed_password = md5($default_password);

        $db->begin_transaction();

        $query = "INSERT INTO student (lrn, password, firstname, lastname, middlename, email, contact_number, gender) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssss",
            $_POST['lrn'],
            $hashed_password,
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['middlename'],
            $_POST['email'],
            $_POST['contact_number'],
            $_POST['gender']
        );

        if (!$stmt->execute()) {
            throw new Exception($db->error);
        }

        $db->commit();

        echo json_encode([
            'status' => 'success',
            'message' => "Student added successfully\nDefault password: $default_password"
        ]);

    } catch (Exception $e) {
        $db->rollback();
        error_log("Add Student Error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function editStudent($db) {
    try {
        if (!isset($_POST['student_id'])) {
            throw new Exception('Student ID is required');
        }

        // Check if email exists for other students
        $check_query = "SELECT student_id FROM student 
                       WHERE email = ? AND student_id != ? AND status != 'deleted'";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("si", $_POST['email'], $_POST['student_id']);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception('Email already exists');
        }

        $query = "UPDATE student SET 
            firstname = ?,
            lastname = ?,
            email = ?,
            contact_number = ?,
            gender = ?,
            lrn = ?
            WHERE student_id = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssi",
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['email'],
            $_POST['contact_number'],
            $_POST['gender'],
            $_POST['lrn'],
            $_POST['student_id']
        );

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Student updated successfully'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function archiveStudent($db) {
    try {
        if (!isset($_POST['student_id'])) {
            throw new Exception('Student ID is required');
        }

        $query = "UPDATE student SET status = 'archived' WHERE student_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $_POST['student_id']);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Student archived successfully'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getStudentDetails($db) {
    try {
        if (!isset($_GET['student_id'])) {
            throw new Exception('Student ID is required');
        }

        $query = "SELECT * FROM student WHERE student_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $_GET['student_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($student = $result->fetch_assoc()) {
            echo json_encode([
                'status' => 'success',
                'data' => $student
            ]);
        } else {
            throw new Exception('Student not found');
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function logError($message) {
    $logFile = __DIR__ . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

function parsePDFContent($text) {
    // Split text into lines
    $lines = explode("\n", $text);
    $rows = [];
    
    // Skip header lines (including instructions)
    $dataStarted = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        debug("Processing line: " . $line); // Add debugging
        
        // Skip empty lines
        if (empty($line)) continue;
        
        // Check if this line contains the header
        if (strpos($line, 'LRN*') !== false || strpos($line, 'LRN') !== false) {
            $dataStarted = true;
            debug("Found header line"); // Add debugging
            continue; // Skip the header line
        }
        
        if ($dataStarted) {
            // Split line into columns (using multiple spaces or tabs as delimiter)
            $columns = preg_split('/[\t\s]{2,}/', $line);
            $columns = array_map('trim', $columns);
            
            debug("Split columns: " . print_r($columns, true)); // Add debugging
            
            // Validate that we have all required columns
            if (count($columns) >= 7) {
                // Validate LRN format (12 digits)
                if (preg_match('/^\d{12}$/', $columns[0])) {
                    $rows[] = [
                        $columns[0], // LRN
                        $columns[1], // First Name
                        $columns[2], // Middle Name
                        $columns[3], // Last Name
                        $columns[4], // Email
                        $columns[5], // Contact Number
                        $columns[6]  // Gender
                    ];
                    debug("Added row: " . print_r(end($rows), true)); // Add debugging
                }
            }
        }
    }
    
    return $rows;
}

function handleExcelImport($rows, $db) {
    $added = 0;
    $skipped = 0;
    $errors = [];

    foreach ($rows as $row) {
        try {
            if (count($row) < 7) continue;

            $lrn = trim($row[0]);
            $firstname = trim($row[1]);
            $middlename = trim($row[2]);
            $lastname = trim($row[3]);
            $email = trim($row[4]);
            $contact = trim($row[5]);
            $gender = trim($row[6]);

            // Generate password using the new format
            $current_year = date('Y');
            $default_password = strtolower($firstname . $current_year . $lastname);
            // Remove spaces and special characters
            $default_password = preg_replace('/[^a-z0-9]/', '', $default_password);
            
            // Hash the password using MD5
            $hashed_password = md5($default_password);

            $query = "INSERT INTO student (lrn, password, firstname, lastname, middlename, email, contact_number, gender) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("ssssssss", 
                $lrn,
                $hashed_password,
                $firstname,
                $lastname,
                $middlename,
                $email,
                $contact,
                $gender
            );

            if ($stmt->execute()) {
                $added++;
            } else {
                $skipped++;
                $errors[] = "Failed to add student: $firstname $lastname";
            }

        } catch (Exception $e) {
            $skipped++;
            $errors[] = "Error for $firstname $lastname: " . $e->getMessage();
        }
    }

    return [
        'added' => $added,
        'skipped' => $skipped,
        'errors' => $errors
    ];
}

function processStudentImport($data, $db, &$added, &$skipped, &$errors, $rowNumber) {
    try {
        if (empty(array_filter($data))) return; // Skip empty rows
        
        if (count($data) < 7) {
            throw new Exception("Invalid number of columns");
        }

        // Convert all data to strings and trim
        $lrn = trim((string)$data[0]);
        $firstname = trim((string)$data[1]);
        $middlename = trim((string)$data[2]);
        $lastname = trim((string)$data[3]);
        $email = trim((string)$data[4]);
        $contact = trim((string)$data[5]);
        $gender = trim((string)$data[6]);

        // Basic validation
        if (empty($lrn) || empty($firstname) || empty($lastname)) {
            throw new Exception("Missing required fields");
        }

        // Validate LRN format (12 digits)
        $lrn = preg_replace('/[^0-9]/', '', $lrn); // Remove any non-numeric characters
        if (!preg_match('/^\d{12}$/', $lrn)) {
            throw new Exception("Invalid LRN format - must be 12 digits");
        }

        // Check if LRN already exists
        $checkStmt = $db->prepare("SELECT student_id FROM student WHERE lrn = ?");
        $checkStmt->bind_param("s", $lrn);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            throw new Exception("LRN already exists");
        }

        // Validate gender
        $gender = ucfirst(strtolower($gender));
        if (!in_array($gender, ['Male', 'Female'])) {
            throw new Exception("Invalid gender - must be 'Male' or 'Female'");
        }

        // Generate default password
        $current_year = date('Y');
        $default_password = strtolower($firstname . $current_year . $lastname);
        $default_password = preg_replace('/[^a-z0-9]/', '', $default_password);
        $hashed_password = md5($default_password);

        // Insert student
        $stmt = $db->prepare("INSERT INTO student (lrn, password, firstname, lastname, middlename, email, contact_number, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $lrn, $hashed_password, $firstname, $lastname, $middlename, $email, $contact, $gender);

        if ($stmt->execute()) {
            $added++;
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

    } catch (Exception $e) {
        $skipped++;
        $errors[] = "Row $rowNumber: " . $e->getMessage();
    }
}
