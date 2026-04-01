<?php
/**
 * Quiz API Endpoints
 * Handle quiz questions and attempts
 */

require_once '../api/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'questions':
                $category = $_GET['category'] ?? null;
                $difficulty = $_GET['difficulty'] ?? null;
                $limit = $_GET['limit'] ?? 10;
                
                $questions = getQuizQuestions($category, $difficulty, $limit);
                
                // Remove correct answer from response (for security)
                foreach ($questions as &$q) {
                    unset($q['correct_answer']);
                }
                
                jsonResponse(['success' => true, 'questions' => $questions]);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    case 'POST':
        $data = getRequestBody();
        
        switch ($action) {
            case 'submit':
                $userId = $data['user_id'] ?? null;
                $score = $data['score'] ?? 0;
                $totalQuestions = $data['total_questions'] ?? 0;
                $correctAnswers = $data['correct_answers'] ?? 0;
                $timeTaken = $data['time_taken'] ?? null;
                
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                $result = submitQuizAttempt($userId, $score, $totalQuestions, $correctAnswers, $timeTaken);
                jsonResponse($result, $result['success'] ? 200 : 400);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
?>
