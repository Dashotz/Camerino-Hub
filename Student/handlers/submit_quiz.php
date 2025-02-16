<?php
session_start();
require_once('../../db/dbConnector.php');

// Prevent any output before headers
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log('Raw input data: ' . file_get_contents('php://input'));
    error_log('Decoded data: ' . print_r($data, true));
    error_log('Answers array: ' . print_r($data['answers'], true));
    
    if (!$data || !isset($data['quiz_id']) || !isset($data['answers'])) {
        throw new Exception('Invalid input data');
    }

    // Add this near the start of the file, after decoding the JSON data
    error_log("Received quiz submission data: " . print_r([
        'quiz_id' => $data['quiz_id'],
        'answers' => $data['answers'],
        'raw_post' => $_POST,
        'raw_input' => file_get_contents('php://input')
    ], true));

    $db = new DbConnector();
    $db->begin_transaction();

    $student_id = $_SESSION['id'];
    $quiz_id = intval($data['quiz_id']);
    $answers = $data['answers'];
    $time_spent = intval($data['time_spent'] ?? 0);
    $security_violation = (bool)($data['security_violation'] ?? false);
    $violation_count = intval($data['violation_count'] ?? 0);

    // Check for existing submission
    $stmt = $db->prepare("SELECT 1 FROM student_activity_submissions WHERE student_id = ? AND activity_id = ?");
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Quiz already submitted');
    }

    // Get all questions for this quiz
    $stmt = $db->prepare("
        SELECT 
            q.*,
            GROUP_CONCAT(
                CONCAT(c.choice_id, ':', c.is_correct) 
                ORDER BY c.choice_order 
                SEPARATOR '|'
            ) as choices,
            qa.answer_text as correct_answer
        FROM quiz_questions q
        LEFT JOIN question_choices c ON q.question_id = c.question_id
        LEFT JOIN quiz_answers qa ON q.question_id = qa.question_id
        WHERE q.quiz_id = ?
        GROUP BY q.question_id
    ");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $total_points = 0;
    $correct_answers = 0;
    $total_questions = count($questions);

    error_log("Received answers: " . print_r($answers, true));

    foreach ($questions as $question) {
        $question_id = $question['question_id'];
        $student_answer = $answers[$question_id] ?? null;
        $is_correct = 0;
        $selected_choice_id = null;
        $text_answer = '';

        error_log("Processing question: " . print_r([
            'question_id' => $question_id,
            'type' => $question['question_type'],
            'student_answer' => $student_answer,
            'raw_answers' => $answers
        ], true));

        switch ($question['question_type']) {
            case 'multiple_choice':
                if ($student_answer) {
                    $selected_choice_id = intval($student_answer);
                    $stmt = $db->prepare("SELECT is_correct FROM question_choices WHERE choice_id = ? AND question_id = ?");
                    $stmt->bind_param("ii", $selected_choice_id, $question_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $is_correct = $row['is_correct'];
                    }
                    if ($is_correct) {
                        $correct_answers++;
                        $total_points += $question['points'];
                    }
                }
                break;

            case 'true_false':
                if ($student_answer !== null) {
                    $true_value = strtolower($student_answer) === 'true' ? 1 : 0;
                    $stmt = $db->prepare("SELECT choice_id, is_correct FROM question_choices WHERE question_id = ? AND choice_text = ?");
                    $choice_text = $true_value ? 'True' : 'False';
                    $stmt->bind_param("is", $question_id, $choice_text);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $selected_choice_id = $row['choice_id'];
                        $is_correct = $row['is_correct'];
                    }
                    if ($is_correct) {
                        $correct_answers++;
                        $total_points += $question['points'];
                    }
                }
                break;

            case 'short_answer':
                if (isset($answers[$question_id])) {
                    $text_answer = trim(strval($answers[$question_id]));
                    $selected_choice_id = null;
                    
                    // Get the correct answer from quiz_answers table
                    $check_stmt = $db->prepare("
                        SELECT answer_text 
                        FROM quiz_answers 
                        WHERE question_id = ? 
                        LIMIT 1
                    ");
                    $check_stmt->bind_param("i", $question_id);
                    $check_stmt->execute();
                    $result = $check_stmt->get_result();
                    
                    if ($row = $result->fetch_assoc()) {
                        $correct_answer = trim($row['answer_text']);
                        // Case-insensitive comparison
                        $is_correct = (strcasecmp($text_answer, $correct_answer) === 0) ? 1 : 0;
                        
                        if ($is_correct) {
                            $correct_answers++;
                            $total_points += $question['points'];
                        }
                    }
                }
                break;

            default:
                // Handle other question types...
                break;
        }

        // Move the insert statement outside the switch, so it handles all question types
        $insert_stmt = $db->prepare("
            INSERT INTO student_answers 
            (student_id, quiz_id, question_id, text_answer, selected_choice_id, is_correct) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $insert_stmt->bind_param("iiissi", 
            $student_id,
            $quiz_id,
            $question_id,
            $text_answer,
            $selected_choice_id,
            $is_correct
        );

        if (!$insert_stmt->execute()) {
            error_log("Failed to insert answer: " . $insert_stmt->error);
            throw new Exception('Failed to save answer');
        }
    }

    // Calculate total possible points
    $total_possible_points = 0;
    foreach ($questions as $question) {
        $total_possible_points += $question['points'];
    }

    // Insert submission
    $status = $security_violation ? 'missing' : 'submitted';
    $final_points = $security_violation ? 0 : $total_points;

    $stmt = $db->prepare("
        INSERT INTO student_activity_submissions 
        (student_id, activity_id, points, time_spent, status, security_violation, correct_answers, total_answers)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("iiiisiii", 
        $student_id, 
        $quiz_id, 
        $final_points,
        $time_spent,
        $status,
        $security_violation,
        $correct_answers,
        $total_questions
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to save submission: ' . $stmt->error);
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'score' => $final_points,
        'total_points' => $total_possible_points,
        'correct_answers' => $correct_answers
    ]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log('Quiz submission error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
} 