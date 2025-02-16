<?php
session_start();
require_once('../../db/dbConnector.php');

// Prevent warnings from being output
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 0);

// Set JSON header right away
header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode([
        'success' => false,
        'swal' => [
            'title' => 'Error',
            'text' => 'Unauthorized access',
            'confirmButtonText' => 'OK'
        ]
    ]);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

try {
    // Start transaction
    $db->begin_transaction();

    // Validate required fields
    $required_fields = ['title', 'description', 'section_subject_id', 'due_date', 'points', 'questions'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Please fill in all required fields");
        }
    }

    // Insert quiz into activities table
    $query = "INSERT INTO activities (
        title,
        description,
        teacher_id,
        section_subject_id,
        type,
        due_date,
        points,
        quiz_duration,
        prevent_tab_switch,
        fullscreen_required,
        status
    ) VALUES (?, ?, ?, ?, 'quiz', ?, ?, ?, ?, ?, 'active')";

    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception("Database error: " . $db->error);
    }

    $quiz_duration = isset($_POST['quiz_duration']) ? (int)$_POST['quiz_duration'] : 60;
    $prevent_tab_switch = isset($_POST['prevent_tab_switch']) ? 1 : 0;
    $fullscreen_required = isset($_POST['fullscreen_required']) ? 1 : 0;
    $points = (int)$_POST['points'];
    $section_subject_id = (int)$_POST['section_subject_id'];

    $stmt->bind_param("ssiisiiii", 
        $_POST['title'],
        $_POST['description'],
        $teacher_id,
        $section_subject_id,
        $_POST['due_date'],
        $points,
        $quiz_duration,
        $prevent_tab_switch,
        $fullscreen_required
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to save quiz: " . $stmt->error);
    }

    $quiz_id = $stmt->insert_id;

    // Save questions and choices
    $questions = $_POST['questions'];
    $question_order = 1;

    foreach ($questions as $index => $question) {
        $question_type = $question['type'];
        $question_text = $question['text'];
        $points = $question['points'];
        $image_path = $question['image_path'] ?? null;

        // Insert question
        $stmt = $db->prepare("
            INSERT INTO quiz_questions 
            (quiz_id, question_text, question_type, points, image_path) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issis", $quiz_id, $question_text, $question_type, $points, $image_path);
        $stmt->execute();
        $question_id = $stmt->insert_id;

        if ($question_type === 'multiple_choice') {
            if (!isset($question['choices']) || !isset($question['correct_choice'])) {
                throw new Exception("Missing choices or correct answer for multiple choice question");
            }
            
            foreach ($question['choices'] as $index => $choice_text) {
                $is_correct = (isset($question['correct_choice']) && $question['correct_choice'] == $index) ? 1 : 0;
                $choice_order = $index + 1;
                
                $choice_query = "INSERT INTO question_choices (
                    question_id,
                    choice_text,
                    is_correct,
                    choice_order
                ) VALUES (?, ?, ?, ?)";
                
                $stmt = $db->prepare($choice_query);
                $stmt->bind_param("isii", 
                    $question_id,
                    $choice_text,
                    $is_correct,
                    $choice_order
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to save choice: " . $stmt->error);
                }
            }
        } elseif ($question_type === 'true_false') {
            if (!isset($question['correct_choice'])) {
                throw new Exception("Missing correct answer for true/false question");
            }
            
            // Add True choice
            $true_query = "INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) 
                           VALUES (?, 'True', ?, 1)";
            $stmt = $db->prepare($true_query);
            $is_true_correct = (isset($question['correct_choice']) && $question['correct_choice'] === 'true') ? 1 : 0;
            $stmt->bind_param("ii", $question_id, $is_true_correct);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to save True choice: " . $stmt->error);
            }

            // Add False choice
            $false_query = "INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) 
                            VALUES (?, 'False', ?, 2)";
            $stmt = $db->prepare($false_query);
            $is_false_correct = (isset($question['correct_choice']) && $question['correct_choice'] === 'false') ? 1 : 0;
            $stmt->bind_param("ii", $question_id, $is_false_correct);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to save False choice: " . $stmt->error);
            }
        } elseif ($question_type === 'short_answer') {
            // Insert the correct answer for short answer questions
            $correct_answer = $question['correct_answer'];
            $stmt = $db->prepare("
                INSERT INTO quiz_answers 
                (question_id, answer_text) 
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $question_id, $correct_answer);
            $stmt->execute();
        }
    }

    // Create announcement for the quiz
    $announcement_query = "INSERT INTO announcements (
        teacher_id,
        section_id,
        subject_id,
        title,
        content,
        type,
        reference_id,
        status
    ) SELECT 
        ?,
        ss.section_id,
        ss.subject_id,
        ?,
        ?,
        'quiz',
        ?,
        'active'
    FROM section_subjects ss 
    WHERE ss.id = ?";

    $announcement_title = "New Quiz: " . $_POST['title'];
    $announcement_content = "A new quiz has been posted: " . $_POST['title'] . "\nDue date: " . $_POST['due_date'] . "\nTotal Points: " . $points;
    
    $stmt = $db->prepare($announcement_query);
    $stmt->bind_param("issii", 
        $teacher_id,
        $announcement_title,
        $announcement_content,
        $quiz_id,
        $section_subject_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to create announcement");
    }

    // Create notifications for students
    $notification_query = "INSERT INTO notifications (
        user_id,
        section_id,
        subject_id,
        user_type,
        type,
        reference_id,
        activity_id,
        title,
        message
    ) 
    SELECT 
        s.student_id,
        ss.section_id,
        ss.subject_id,
        'student',
        'quiz',
        ?,
        ?,
        ?,
        ?
    FROM section_subjects ss
    JOIN student_sections st ON ss.section_id = st.section_id
    JOIN student s ON st.student_id = s.student_id
    WHERE ss.id = ? AND st.status = 'active'";

    $stmt = $db->prepare($notification_query);
    $stmt->bind_param("iissi", 
        $quiz_id,
        $quiz_id,
        $announcement_title,
        $announcement_content,
        $section_subject_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to create notifications");
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'swal' => [
            'title' => 'Success',
            'text' => 'Quiz created successfully!',
            'confirmButtonText' => 'OK'
        ]
    ]);

} catch (Exception $e) {
    $db->rollback();
    error_log("Error creating quiz: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'swal' => [
            'title' => 'Error',
            'text' => 'Failed to create quiz. Please try again.',
            'confirmButtonText' => 'OK'
        ]
    ]);
}

$db->close();