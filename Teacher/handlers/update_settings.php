<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

try {
    $db = new DbConnector();
    $teacher_id = $_SESSION['teacher_id'];
    
    // Get POST data
    $department_id = $_POST['department_id'] ?? null;
    $notification_settings = isset($_POST['notification_settings']) ? 
        json_encode($_POST['notification_settings']) : null;
    $interface_settings = isset($_POST['interface_settings']) ? 
        json_encode($_POST['interface_settings']) : null;
    $teaching_settings = isset($_POST['teaching_settings']) ? 
        json_encode($_POST['teaching_settings']) : null;

    // Initialize query parts
    $updates = [];
    $params = [];
    $types = '';

    // Add department_id if it exists
    if ($department_id !== null) {
        $updates[] = "department_id = ?";
        $params[] = $department_id;
        $types .= "i";
    }

    // Add notification settings if they exist
    if ($notification_settings !== null) {
        $updates[] = "notification_settings = ?";
        $params[] = $notification_settings;
        $types .= "s";
    }

    // Add interface settings if they exist
    if ($interface_settings !== null) {
        $updates[] = "interface_settings = ?";
        $params[] = $interface_settings;
        $types .= "s";
    }

    // Add teaching settings if they exist
    if ($teaching_settings !== null) {
        $updates[] = "teaching_settings = ?";
        $params[] = $teaching_settings;
        $types .= "s";
    }

    // If no updates, return success
    if (empty($updates)) {
        echo json_encode([
            'success' => true,
            'message' => 'No changes to update'
        ]);
        exit();
    }

    // Build the query
    $update_query = "UPDATE teacher SET " . implode(", ", $updates) . " WHERE teacher_id = ?";
    
    // Add teacher_id to params
    $params[] = $teacher_id;
    $types .= "i";

    // Prepare and execute the query
    $stmt = $db->prepare($update_query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {
    error_log("Settings update error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update settings: ' . $e->getMessage()
    ]);
}
?> 