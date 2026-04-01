<?php
/**
 * User API Endpoints
 * Handle user registration, login, and profile operations
 */

require_once '../api/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        $data = getRequestBody();
        
        switch ($action) {
            case 'register':
                // Validate required fields
                $required = ['full_name', 'email', 'password'];
                $missing = validateFields($data, $required);
                
                if (!empty($missing)) {
                    jsonResponse(['success' => false, 'error' => 'Missing fields: ' . implode(', ', $missing)], 400);
                }
                
                // Validate email format
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    jsonResponse(['success' => false, 'error' => 'Invalid email format'], 400);
                }
                
                // Validate password strength
                if (strlen($data['password']) < 6) {
                    jsonResponse(['success' => false, 'error' => 'Password must be at least 6 characters'], 400);
                }
                
                $result = registerUser($data);
                jsonResponse($result, $result['success'] ? 201 : 400);
                break;
                
            case 'login':
                $required = ['email', 'password'];
                $missing = validateFields($data, $required);
                
                if (!empty($missing)) {
                    jsonResponse(['success' => false, 'error' => 'Missing fields: ' . implode(', ', $missing)], 400);
                }
                
                $result = loginUser($data['email'], $data['password']);
                jsonResponse($result, $result['success'] ? 200 : 401);
                break;
                
            case 'update_profile':
                $userId = $data['user_id'] ?? null;
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                $result = updateUserProfile($userId, $data);
                jsonResponse($result, $result['success'] ? 200 : 400);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    case 'GET':
        switch ($action) {
            case 'get_profile':
                $userId = $_GET['user_id'] ?? null;
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                $user = getUserById($userId);
                if ($user) {
                    jsonResponse(['success' => true, 'user' => $user]);
                } else {
                    jsonResponse(['success' => false, 'error' => 'User not found'], 404);
                }
                break;
                
            case 'points_history':
                $userId = $_GET['user_id'] ?? null;
                $limit = $_GET['limit'] ?? 20;
                
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                $history = getUserPointsHistory($userId, $limit);
                jsonResponse(['success' => true, 'history' => $history]);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
?>
