<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $activity_id = $_GET['id'];
    $db = new DbConnector();
    
    $query = "SELECT * FROM activities WHERE activity_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $activity_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $activity = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'activity' => $activity
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Activity not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No activity ID provided'
    ]);
}
?> 