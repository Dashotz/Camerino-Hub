<?php

// Add this after session check
if (isset($_SESSION['require_password_change']) && 
    !in_array(basename($_SERVER['PHP_SELF']), ['teacher_profile.php', 'change_password.php', 'logout.php'])) {
    header("Location: teacher_profile.php?tab=security&force_change=1");
    exit();
} 