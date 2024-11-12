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
    try {
        $db->begin_transaction();

        // Generate username from firstname and lastname
        $firstname = strtolower($_POST['firstname']);
        $lastname = strtolower($_POST['lastname']);
        $base_username = substr($firstname, 0, 1) . $lastname;
        $username = $base_username;
        
        // Check if username exists and append number if needed
        $counter = 1;
        while (true) {
            $check_query = "SELECT student_id FROM student WHERE username = ?";
            $stmt = $db->prepare($check_query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) break;
            $username = $base_username . $counter;
            $counter++;
        }

        // Generate password
        $random_number = rand(1000, 9999);
        $default_password = "camerino-" . $random_number;
        $hashed_password = md5($default_password);

        $query = "INSERT INTO student (
            username,
            password,
            email,
            firstname,
            lastname,
            middlename,
            cys,
            status,
            created_at,
            updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssss",
            $username,
            $hashed_password,
            $_POST['email'],
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['middlename'],
            $_POST['cys'],
            $_POST['status']
        );

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $student_id = $db->insert_id;
        $db->commit();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => "Student account created successfully!\n\nUsername: $username\nPassword: $default_password\n\nPlease save these credentials."
        ]);

    } catch (Exception $e) {
        $db->rollback();
        header('Content-Type: application/json');
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
