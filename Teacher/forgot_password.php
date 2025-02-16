<?php
// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log');

session_start();

// Check if composer autoload exists
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
} else {
    require '../vendor/phpmailer/phpmailer/src/Exception.php';
    require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require '../vendor/phpmailer/phpmailer/src/SMTP.php';
}

require_once('../db/dbConnector.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }

    $db = new DbConnector();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Check if email exists
    $stmt = $db->prepare("SELECT teacher_id, firstname FROM teacher WHERE email = ? AND status = 'active'");
    if (!$stmt) {
        throw new Exception($db->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('No active account found with this email');
    }

    $teacher = $result->fetch_assoc();

    // Generate a temporary password (8 characters)
    $temp_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

    // Hash the temporary password using MD5
    $hashed_temp_password = md5($temp_password);

    // Update the teacher's password and status in the database
    $update_stmt = $db->prepare("UPDATE teacher SET 
        password = ?, 
        password_recovery = 'yes' 
        WHERE teacher_id = ?");
    $update_stmt->bind_param("si", $hashed_temp_password, $teacher['teacher_id']);
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update password');
    }

    // Update the teacher_login_action.php check to use the new status
    $check_temp_pwd = "SELECT * FROM teacher 
                     WHERE teacher_id = ? 
                     AND password_recovery = 'yes'";
    $stmt = $db->prepare($check_temp_pwd);
    $stmt->bind_param("i", $teacher['teacher_id']);
    $stmt->execute();
    $temp_pwd_result = $stmt->get_result();

    try {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'christianpacifico874@gmail.com';
        $mail->Password = 'njeigequvjxmssjj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply@gdmcschool.edu.ph', 'GDMC School System');
        $mail->addAddress($email, $teacher['firstname']);
        $mail->isHTML(true);
        $mail->Subject = 'Password Recovery - GDMC School System';

        // Email template with the temporary password (not the MD5 hash)
        $mailContent = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2c3e50;'>Password Recovery</h2>
                    <p>Dear {$teacher['firstname']},</p>
                    <p>We received a request to recover your password for the GDMC School System.</p>
                    <p>Your temporary password is: <strong>{$temp_password}</strong></p>
                    <p>Please log in using this temporary password and change it immediately.</p>
                    <p style='margin-top: 20px;'>If you didn't request this, please contact the system administrator immediately.</p>
                    <hr style='border: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #666;'>This is an automated message, please do not reply.</p>
                </div>
            </body>
            </html>
        ";

        $mail->Body = $mailContent;
        $mail->AltBody = strip_tags($mailContent);

        if (!$mail->send()) {
            throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
        }

        // Log the successful password recovery attempt
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $db->prepare("INSERT INTO teacher_login_logs (teacher_id, ip_address, status, details) VALUES (?, ?, 'password_recovery', 'Temporary password sent')");
        $log_stmt->bind_param("is", $teacher['teacher_id'], $ip_address);
        $log_stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'A temporary password has been sent to your email'
        ]);

    } catch (Exception $e) {
        throw new Exception('Email sending failed: ' . $e->getMessage());
    }

} catch (Exception $e) {
    error_log("Password Recovery Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
exit;