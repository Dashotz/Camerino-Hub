<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

session_start();
require_once('../../db/dbConnector.php');

try {
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized access');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $db = new DbConnector();
    
    // Get file extension
    $fileExtension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    
    // Validate file extension
    if ($fileExtension !== 'csv') {
        throw new Exception('Invalid file type. Please upload a CSV file.');
    }

    // Read CSV file
    $handle = fopen($_FILES['file']['tmp_name'], 'r');
    if (!$handle) {
        throw new Exception('Failed to open file');
    }

    // Skip header row and store it
    $header = fgetcsv($handle);
    if (!$header) {
        throw new Exception('Empty or invalid file');
    }

    $successCount = 0;
    $errors = [];
    $rowNumber = 2; // Start from row 2 (after header)

    while (($row = fgetcsv($handle)) !== false) {
        try {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Extract and validate data
            $studentId = trim($row[0] ?? '');
            $firstname = trim($row[1] ?? '');
            $lastname = trim($row[2] ?? '');
            $email = trim($row[3] ?? '');
            $contactNumber = trim($row[4] ?? '');
            $gender = trim($row[5] ?? '');
            $birthdate = trim($row[6] ?? '');

            // Validate required fields
            if (empty($studentId) || empty($firstname) || empty($lastname)) {
                $errors[] = "Row $rowNumber: Missing required fields (Student ID, First Name, or Last Name)";
                $rowNumber++;
                continue;
            }

            // Generate username and password
            $username = strtolower($firstname . '.' . $lastname);
            $baseUsername = $username;
            $counter = 1;

            // Check for duplicate username
            while (true) {
                $checkUsernameStmt = $db->prepare("SELECT username FROM student WHERE username = ?");
                $checkUsernameStmt->bind_param('s', $username);
                $checkUsernameStmt->execute();
                if ($checkUsernameStmt->get_result()->num_rows === 0) {
                    break;
                }
                $username = $baseUsername . $counter;
                $counter++;
            }

            $password = password_hash($firstname . '.' . $lastname, PASSWORD_DEFAULT);

            // Check if student ID already exists
            $checkStmt = $db->prepare("SELECT student_id FROM student WHERE student_id = ?");
            $checkStmt->bind_param('s', $studentId);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $errors[] = "Row $rowNumber: Student ID '$studentId' already exists";
                $rowNumber++;
                continue;
            }

            // Format birthdate if provided
            $formattedBirthdate = null;
            if (!empty($birthdate)) {
                $formattedBirthdate = date('Y-m-d', strtotime($birthdate));
                if ($formattedBirthdate === false) {
                    $errors[] = "Row $rowNumber: Invalid date format for birthdate";
                    $rowNumber++;
                    continue;
                }
            }

            // Insert student
            $stmt = $db->prepare("INSERT INTO student (
                student_id, username, firstname, lastname, 
                email, password, contact_number, gender, 
                birthdate, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");

            if (!$stmt) {
                throw new Exception("Database prepare error: " . $db->error);
            }

            $stmt->bind_param('sssssssss',
                $studentId,
                $username,
                $firstname,
                $lastname,
                $email,
                $password,
                $contactNumber,
                $gender,
                $formattedBirthdate
            );

            if ($stmt->execute()) {
                $successCount++;
            } else {
                $errors[] = "Row $rowNumber: Database error - " . $stmt->error;
            }

        } catch (Exception $e) {
            $errors[] = "Row $rowNumber: " . $e->getMessage();
        }
        $rowNumber++;
    }

    fclose($handle);

    if ($successCount === 0) {
        throw new Exception("No students were imported successfully. Please check the error messages.");
    }

    $message = "Successfully imported $successCount students.";
    if (!empty($errors)) {
        $message .= " Encountered " . count($errors) . " errors.";
    }

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'details' => [
            'success_count' => $successCount,
            'errors' => $errors
        ]
    ]);

} catch (Exception $e) {
    error_log("Import error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'details' => [
            'success_count' => 0,
            'errors' => isset($errors) ? $errors : [$e->getMessage()]
        ]
    ]);
} 