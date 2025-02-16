<?php
session_start();
require_once('../../db/dbConnector.php');

// Ensure no output before headers
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Enable error handling for all types of errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

header('Content-Type: application/json');

try {
    // Validate session
    if (!isset($_SESSION['teacher_id'])) {
        error_log('No teacher_id in session');
        throw new Exception('Unauthorized access');
    }

    // Get and validate input data
    $input = file_get_contents('php://input');
    error_log('Raw input: ' . $input);
    
    $data = json_decode($input, true);
    if ($data === null) {
        error_log('Failed to parse JSON input. JSON error: ' . json_last_error_msg());
        throw new Exception('Invalid JSON data');
    }
    error_log('Received quiz question data: ' . print_r($data, true));

    if (!isset($data['quiz_id']) || !isset($data['text']) || !isset($data['type'])) {
        error_log('Missing required fields in data: ' . print_r($data, true));
        throw new Exception('Missing required fields');
    }

    // Validate data types
    if (!is_numeric($data['quiz_id'])) {
        throw new Exception('Invalid quiz_id');
    }
    if (!is_string($data['text']) || empty(trim($data['text']))) {
        throw new Exception('Invalid question text');
    }
    if (!in_array($data['type'], ['multiple_choice', 'true_false', 'short_answer'])) {
        throw new Exception('Invalid question type');
    }

    $db = new DbConnector();
    if (!$db) {
        error_log('Failed to create database connection');
        throw new Exception('Database connection failed');
    }
    
    // Start transaction
    $db->begin_transaction();
    error_log('Transaction started');

    try {
        // Validate teacher owns this quiz
        $validate_query = "SELECT 1 FROM section_subjects ss 
                          JOIN activities a ON ss.id = a.section_subject_id
                          WHERE ss.teacher_id = ? AND a.activity_id = ?";
        $stmt = $db->prepare($validate_query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $db->error);
        }
        
        $stmt->bind_param("ii", $_SESSION['teacher_id'], $data['quiz_id']);
        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        error_log('Validation query result rows: ' . $result->num_rows);
        
        if ($result->num_rows === 0) {
            throw new Exception('Unauthorized access');
        }
        $stmt->close();

        // Insert question
        $question_query = "INSERT INTO quiz_questions (quiz_id, question_text, question_type, points) 
                          VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($question_query);
        if (!$stmt) {
            error_log('Failed to prepare question insert query: ' . $db->error);
            throw new Exception('Database error');
        }
        $points = 1; // Default points per question
        $stmt->bind_param("issi", $data['quiz_id'], $data['text'], $data['type'], $points);
        
        if (!$stmt->execute()) {
            error_log('Failed to insert question: ' . $stmt->error);
            throw new Exception('Failed to save question');
        }
        
        $question_id = $stmt->insert_id;
        $stmt->close();

        error_log('Question type: ' . $data['type']);
        error_log('Question ID: ' . $question_id);

        // Handle choices based on question type
        if ($data['type'] === 'multiple_choice') {
            error_log('Processing multiple choice options');
            if (!isset($data['choices']) || empty($data['choices'])) {
                error_log('No choices provided for multiple choice question');
                throw new Exception('Multiple choice questions require choices');
            }

            error_log('Number of choices: ' . count($data['choices']));
            foreach ($data['choices'] as $index => $choice) {
                error_log('Processing choice: ' . print_r($choice, true));
                if (!isset($choice['text']) || !isset($choice['is_correct'])) {
                    error_log('Invalid choice data: ' . print_r($choice, true));
                    throw new Exception('Invalid choice data');
                }

                $choice_query = "INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) 
                               VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($choice_query);
                if (!$stmt) {
                    error_log('Failed to prepare choice insert query: ' . $db->error);
                    throw new Exception('Database error');
                }

                // Convert values to appropriate types
                $choice_text = (string)$choice['text'];
                $is_correct = (int)$choice['is_correct'];
                $choice_order = (int)($index + 1);

                $stmt->bind_param("isii", 
                    $question_id,
                    $choice_text,
                    $is_correct,
                    $choice_order
                );

                error_log("Binding parameters: question_id={$question_id}, text={$choice_text}, is_correct={$is_correct}, order={$choice_order}");

                if (!$stmt->execute()) {
                    error_log('Failed to save choice: ' . $stmt->error);
                    throw new Exception('Failed to save choice');
                }
                $stmt->close();
            }
        } elseif ($data['type'] === 'true_false') {
            // Add True choice
            $stmt = $db->prepare("INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) VALUES (?, 'True', ?, 1)");
            $is_true = $data['correct_tf'] === 'true' ? 1 : 0;
            $stmt->bind_param("ii", $question_id, $is_true);
            $stmt->execute();
            $stmt->close();

            // Add False choice
            $stmt = $db->prepare("INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) VALUES (?, 'False', ?, 2)");
            $is_false = $data['correct_tf'] === 'false' ? 1 : 0;
            $stmt->bind_param("ii", $question_id, $is_false);
            $stmt->execute();
            $stmt->close();
        } elseif ($data['type'] === 'short_answer') {
            $stmt = $db->prepare("INSERT INTO quiz_answers (question_id, answer_text) VALUES (?, ?)");
            $stmt->bind_param("is", $question_id, $data['correct_answer']);
            $stmt->execute();
            $stmt->close();
        }

        $db->commit();
        error_log('Successfully saved question and choices');
        echo json_encode(['success' => true, 'question_id' => $question_id]);
        
    } catch (Exception $e) {
        $db->rollback();
        error_log('Inner transaction error: ' . $e->getMessage());
        throw $e;
    }

} catch (Throwable $e) {
    error_log('Error in save_quiz_question.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    if (isset($db)) {
        try {
            $db->rollback();
        } catch (Exception $e) {
            error_log('Rollback failed: ' . $e->getMessage());
        }
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'details' => 'Check server logs for more information'
    ]);
    
} finally {
    if (isset($db)) {
        try {
            $db->close();
        } catch (Exception $e) {
            error_log('Failed to close database connection: ' . $e->getMessage());
        }
    }
} 