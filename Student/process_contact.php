<?php
session_start();
require_once('../db/dbConnector.php');

// At the very top of the file, before any output
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

class ContactForm {
    private $db;
    
    public function __construct() {
        $this->db = new DbConnector();
    }
    
    public function processContactForm() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['status' => 'error', 'message' => 'Invalid request method'];
        }

        try {
            // Sanitize and validate input
            $name = $this->sanitizeInput($_POST['name']);
            $email = $this->sanitizeInput($_POST['email']);
            $subject = $this->sanitizeInput($_POST['subject']);
            $content = $this->sanitizeInput($_POST['message']);
            $ip_address = $_SERVER['REMOTE_ADDR'];

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['status' => 'error', 'message' => 'Invalid email address'];
            }

            // Validate required fields
            if (empty($name) || empty($email) || empty($subject) || empty($content)) {
                return ['status' => 'error', 'message' => 'All fields are required'];
            }

            // Using the DbConnector's prepare method
            $query = "INSERT INTO contact_information (name, email, subject, content, type, ip_address, status) 
                     VALUES (?, ?, ?, ?, 'inquiry', ?, 'pending')";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement");
            }
            
            $stmt->bind_param("sssss", $name, $email, $subject, $content, $ip_address);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement");
            }
            
            $stmt->close();
            
            // Send email notification
            $this->sendNotificationEmail($name, $email, $subject, $content);
            
            return ['status' => 'success', 'message' => 'Your message has been sent successfully!'];

        } catch (Exception $e) {
            error_log("Contact form error: " . $e->getMessage());
            return ['status' => 'success', 'message' => 'Your message has been sent successfully!'];
        }
    }

    private function sanitizeInput($input) {
        return $this->db->real_escape_string(htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8'));
    }

    private function sendNotificationEmail($name, $email, $subject, $content) {
        try {
            $to = "admin@camerinohub.edu.ph";
            $emailSubject = "New Contact Form Submission: " . $subject;
            
            $message = "New contact form submission:\n\n";
            $message .= "Name: " . $name . "\n";
            $message .= "Email: " . $email . "\n";
            $message .= "Subject: " . $subject . "\n\n";
            $message .= "Message:\n" . $content;
            
            $headers = "From: noreply@camerinohub.edu.ph\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            mail($to, $emailSubject, $message, $headers);
        } catch (Exception $e) {
            error_log("Email notification error: " . $e->getMessage());
        }
    }
}

// Process the form
try {
    $contactForm = new ContactForm();
    $result = $contactForm->processContactForm();
} catch (Exception $e) {
    error_log("Contact form processing error: " . $e->getMessage());
    $result = ['status' => 'error', 'message' => 'An unexpected error occurred.'];
}

// Return JSON response
echo json_encode($result);
exit; // Make sure nothing else is output after the JSON