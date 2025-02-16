<?php
session_start();
require_once('../../db/dbConnector.php');

// Ensure proper error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception('Unauthorized access');
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['activity_id'])) {
        throw new Exception('Missing required data');
    }

    $db = new DbConnector();
    $db->begin_transaction();

    // Validate teacher ownership
    $validate_query = "SELECT 1 FROM section_subjects ss 
                      JOIN activities a ON ss.id = a.section_subject_id
                      WHERE ss.teacher_id = ? AND a.activity_id = ?";
    $stmt = $db->prepare($validate_query);
    $stmt->bind_param("ii", $_SESSION['teacher_id'], $data['activity_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Unauthorized access');
    }

    // Prepare the base update query
    if ($data['type'] === 'quiz') {
        $update_query = "UPDATE activities 
                        SET section_subject_id = ?,
                            title = ?,
                            description = ?,
                            due_date = ?,
                            points = ?,
                            quiz_duration = ?,
                            prevent_tab_switch = ?,
                            fullscreen_required = ?
                        WHERE activity_id = ?";
                        
        $stmt = $db->prepare($update_query);
        $section_subject_id = intval($data['section_subject_id']);
        $title = $data['title'];
        $description = $data['description'];
        $due_date = $data['due_date'];
        $points = intval($data['points']);
        $quiz_duration = intval($data['quiz_duration']);
        $prevent_tab_switch = intval($data['prevent_tab_switch']);
        $fullscreen_required = intval($data['fullscreen_required']);
        $activity_id = intval($data['activity_id']);
        
        $stmt->bind_param(
            "isssiiiii",
            $section_subject_id,
            $title,
            $description,
            $due_date,
            $points,
            $quiz_duration,
            $prevent_tab_switch,
            $fullscreen_required,
            $activity_id
        );
    } else {
        $update_query = "UPDATE activities 
                        SET section_subject_id = ?,
                            title = ?,
                            description = ?,
                            due_date = ?,
                            points = ?
                        WHERE activity_id = ?";
                        
        $stmt = $db->prepare($update_query);
        $section_subject_id = intval($data['section_subject_id']);
        $title = $data['title'];
        $description = $data['description'];
        $due_date = $data['due_date'];
        $points = intval($data['points']);
        $activity_id = intval($data['activity_id']);
        
        $stmt->bind_param(
            "isssii",
            $section_subject_id,
            $title,
            $description,
            $due_date,
            $points,
            $activity_id
        );
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to update activity: ' . $stmt->error);
    }

    // Update quiz questions if applicable
    if ($data['type'] === 'quiz' && isset($data['questions'])) {
        foreach ($data['questions'] as $question) {
            // Update question
            $question_query = "UPDATE quiz_questions 
                              SET question_text = ?,
                                  question_type = ?,
                                  points = ?,
                                  image_path = ?
                              WHERE question_id = ?";

            $stmt = $db->prepare($question_query);
            $stmt->bind_param("ssisi", 
                $question['text'],
                $question['type'],
                $question['points'],
                $question['image_path'],
                $question['question_id']
            );
            $stmt->execute();

            // Delete existing choices
            $stmt = $db->prepare("DELETE FROM question_choices WHERE question_id = ?");
            $stmt->bind_param("i", $question['question_id']);
            $stmt->execute();

            // Add new choices based on question type
            if ($question['type'] === 'multiple_choice' && isset($question['choices'])) {
                foreach ($question['choices'] as $index => $choice) {
                    $stmt = $db->prepare("INSERT INTO question_choices 
                                        (question_id, choice_text, is_correct, choice_order) 
                                        VALUES (?, ?, ?, ?)");
                    
                    $choice_text = $choice['text'];
                    $is_correct = intval($choice['is_correct']);
                    $choice_order = $index + 1;
                    
                    $stmt->bind_param("isii", $question['question_id'], $choice_text, $is_correct, $choice_order);
                    $stmt->execute();
                }
            } elseif ($question['type'] === 'true_false') {
                // Insert True choice
                $stmt = $db->prepare("INSERT INTO question_choices 
                                    (question_id, choice_text, is_correct, choice_order) 
                                    VALUES (?, 'True', ?, 1)");
                $is_true = $question['correct_tf'] === 'true' ? 1 : 0;
                $stmt->bind_param("ii", $question['question_id'], $is_true);
                $stmt->execute();

                // Insert False choice
                $stmt = $db->prepare("INSERT INTO question_choices 
                                    (question_id, choice_text, is_correct, choice_order) 
                                    VALUES (?, 'False', ?, 2)");
                $is_false = $question['correct_tf'] === 'false' ? 1 : 0;
                $stmt->bind_param("ii", $question['question_id'], $is_false);
                $stmt->execute();
            }
        }
    }

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log('Error in update_activity.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($db)) {
        $db->close();
    }
} 