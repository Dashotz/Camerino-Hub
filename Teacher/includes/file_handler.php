<?php
function handleActivityFiles($activityId, $teacherId, $files) {
    // Define upload directory
    $uploadDir = '../uploads/activities/';
    $teacherDir = $uploadDir . "teacher_{$teacherId}/";
    $activityDir = $teacherDir . "activity_{$activityId}/";
    
    // Create directories if they don't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!file_exists($teacherDir)) {
        mkdir($teacherDir, 0777, true);
    }
    if (!file_exists($activityDir)) {
        mkdir($activityDir, 0777, true);
    }
    
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    
    $uploadedFiles = [];
    
    foreach ($files['activity_files']['tmp_name'] as $key => $tmp_name) {
        if (empty($tmp_name)) continue;
        
        $file_name = $files['activity_files']['name'][$key];
        $file_type = $files['activity_files']['type'][$key];
        $file_size = $files['activity_files']['size'][$key];
        
        // Validate file type and size
        if (!in_array($file_type, $allowedTypes)) {
            continue;
        }
        
        // 10MB limit
        if ($file_size > 10485760) {
            continue;
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
        $file_path = $activityDir . $new_filename;
        
        if (move_uploaded_file($tmp_name, $file_path)) {
            $uploadedFiles[] = [
                'file_name' => $file_name,
                'file_path' => 'uploads/activities/teacher_' . $teacherId . '/activity_' . $activityId . '/' . $new_filename,
                'file_type' => $file_type,
                'file_size' => $file_size
            ];
        }
    }
    
    return $uploadedFiles;
} 