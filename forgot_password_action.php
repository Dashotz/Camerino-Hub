<?php
session_start();
require_once('db/dbConnector.php');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Check if composer autoload exists
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require 'vendor/phpmailer/phpmailer/src/Exception.php';
    require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'vendor/phpmailer/phpmailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Ensure we're sending JSON response
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['email'])) {
        throw new Exception('Email is required');
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }

    $db = new DbConnector();

    // First check if it's a teacher's email
    $stmt = $db->prepare("SELECT teacher_id, firstname, email FROM teacher WHERE email = ? AND status = 'active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // It's a teacher account
        $user = $result->fetch_assoc();
        $isTeacher = true;
    } else {
        // Check student table
        $stmt = $db->prepare("SELECT student_id, firstname, email FROM student WHERE email = ? AND status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('No active account found with this email');
        }
        $user = $result->fetch_assoc();
        $isTeacher = false;
    }

    // Generate temporary password
    $temp_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    $hashed_password = md5($temp_password);

    if ($isTeacher) {
        // Update teacher password
        $update_stmt = $db->prepare("UPDATE teacher SET 
            password = ?,
            password_recovery = 'yes'
            WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
    } else {
        // Update student password
        $update_stmt = $db->prepare("UPDATE student SET 
            password = ?,
            password_recovery = 'yes'
            WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
    }

    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update password: ' . $update_stmt->error);
    }

    if ($update_stmt->affected_rows === 0) {
        throw new Exception('Failed to update password in database');
    }

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'christianpacifico874@gmail.com';
        $mail->Password = 'njeigequvjxmssjj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('noreply@gdmcschool.edu.ph', 'GDMC School System');
        $mail->addAddress($email, $user['firstname']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Recovery - GDMC School System';

        $userType = $isTeacher ? "teacher" : "student";
        
        $mailContent = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c3e50;'>Password Recovery</h2>
                    <p>Dear {$user['firstname']},</p>
                    <p>We received a request to recover your {$userType} password for the GDMC School System.</p>
                    <p>Your temporary password is: <strong>{$temp_password}</strong></p>
                    <p>Please log in using this temporary password and change it immediately.</p>
                    <p style='margin-top: 20px;'>If you didn't request this, please contact the system administrator immediately.</p>
                    <hr style='border: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #666;'>This is an automated message, please do not reply.</p>
                </div>
            </body>
            </html>";

        $mail->Body = $mailContent;
        $mail->AltBody = strip_tags($mailContent);

        $mail->send();
        
        echo json_encode([
            'success' => true,
            'message' => 'A temporary password has been sent to your email'
        ]);

    } catch (Exception $e) {
        throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
    }

} catch (Exception $e) {
    error_log("Password Recovery Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
