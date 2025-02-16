<?php

// After successful password change
$update_query = "UPDATE teacher SET 
    password = ?, 
    password_recovery = 'no' 
    WHERE teacher_id = ?";
$stmt = $db->prepare($update_query);
$stmt->bind_param("si", $new_password_hash, $_SESSION['teacher_id']);
$stmt->execute();

// Clear the password change requirement
unset($_SESSION['require_password_change']);