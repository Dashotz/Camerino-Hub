<?php
session_start();
require_once('../db/dbConnector.php');
require '../vendor/autoload.php';

// Ensure we start with a clean output buffer
if (ob_get_level()) {
    ob_end_clean();
}
header('Content-Type: application/json');

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate email input
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        throw new Exception('Email is required');
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Invalid email format');
    }

    $db = new DbConnector();
    
    // Check if student exists and get their information
    $stmt = $db->prepare("SELECT student_id, firstname, email, status 
                         FROM student 
                         WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Database error: Failed to prepare statement');
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception('Database error: Failed to execute query');
    }
    
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        throw new Exception('No account found with this email');
    }

    if ($student['status'] !== 'active') {
        throw new Exception('This account is not active. Please contact the administrator.');
    }

    // Generate temporary password
    $temp_password = bin2hex(random_bytes(5)); // 10 characters
    $hashed_temp_password = md5($temp_password);

    try {
        $db->begin_transaction();

        // Update password, set recovery status to 'yes', and update timestamp
        $update_stmt = $db->prepare("UPDATE student 
                                   SET password = ?, 
                                       password_recovery = 'yes',
                                       updated_at = NOW() 
                                   WHERE student_id = ?");
        if (!$update_stmt) {
            throw new Exception('Failed to prepare password update');
        }

        $update_stmt->bind_param("si", $hashed_temp_password, $student['student_id']);
        if (!$update_stmt->execute()) {
            throw new Exception('Failed to update password');
        }

        // Log the recovery attempt
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $db->prepare("INSERT INTO student_login_logs 
                                 (student_id, ip_address, status) 
                                 VALUES (?, ?, 'password_recovery')");
        if (!$log_stmt) {
            throw new Exception('Failed to prepare log entry');
        }

        $log_stmt->bind_param("is", $student['student_id'], $ip_address);
        if (!$log_stmt->execute()) {
            throw new Exception('Failed to log password recovery attempt');
        }

        // Configure and send email
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'christianpacifico874@gmail.com';
            $mail->Password = 'njeigequvjxmssjj';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = 0; // Disable debug output

            // Recipients
            $mail->setFrom('christianpacifico874@gmail.com', 'GDMC Learning Hub');
            $mail->addAddress($student['email']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Recovery - GDMC Learning Hub';

            $mailContent = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #2c3e50;'>Password Recovery</h2>
                        <p>Hello {$student['firstname']},</p>
                        <p>A password recovery was requested for your account. Here is your temporary password:</p>
                        <div style='background: #f9f9f9; padding: 10px; margin: 15px 0; border-left: 4px solid #3498db;'>
                            <strong>Temporary Password:</strong> {$temp_password}
                        </div>
                        <p style='color: #e74c3c;'><strong>Important:</strong> For security reasons, you will be required to change this password immediately after logging in.</p>
                        <p style='color: #7f8c8d;'>If you didn't request this password recovery, please contact the administrator immediately.</p>
                        <hr>
                        <p style='font-size: 12px; color: #95a5a6;'>This is an automated message, please do not reply.</p>
                    </div>
                </body>
                </html>";

            $mail->Body = $mailContent;
            $mail->AltBody = strip_tags($mailContent);

            if (!$mail->send()) {
                throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
            }

            // If everything succeeded, commit the transaction
            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'A temporary password has been sent to your email'
            ]);

        } catch (Exception $e) {
            throw new Exception('Email sending failed: ' . $e->getMessage());
        }

    } catch (Exception $e) {
        $db->rollback();
        throw new Exception('Password recovery failed: ' . $e->getMessage());
    }

} catch (Exception $e) {
    error_log("Password Recovery Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit(); 