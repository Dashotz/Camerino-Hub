<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$db = new DbConnector();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        updateProfile($db);
        break;
    case 'update_password':
        updatePassword($db);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}

function updateProfile($db) {
    try {
        $admin_id = $_SESSION['admin_id'];
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';

        // Validate inputs
        if (empty($username) || empty($email)) {
            throw new Exception('Username and email are required');
        }

        // Handle profile image upload
        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
            }

            $upload_path = '../uploads/profile/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $new_filename = uniqid() . '.' . $ext;
            $destination = $upload_path . $new_filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                $profile_image = 'uploads/profile/' . $new_filename;
            }
        }

        // Update database
        $query = "UPDATE admin SET username = ?, email = ?";
        $params = [$username, $email];
        $types = "ss";

        if ($profile_image) {
            $query .= ", profile_image = ?";
            $params[] = $profile_image;
            $types .= "s";
        }

        $query .= " WHERE admin_id = ?";
        $params[] = $admin_id;
        $types .= "i";

        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

function updatePassword($db) {
    try {
        $admin_id = $_SESSION['admin_id'];
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            throw new Exception('All password fields are required');
        }

        if ($new_password !== $confirm_password) {
            throw new Exception('New passwords do not match');
        }

        // Verify current password
        $query = "SELECT password FROM admin WHERE admin_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if (!password_verify($current_password, $admin['password'])) {
            throw new Exception('Current password is incorrect');
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE admin SET password = ? WHERE admin_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("si", $hashed_password, $admin_id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>
