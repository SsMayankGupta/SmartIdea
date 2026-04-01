<?php
/**
 * Waste Report API Endpoints
 * Handle waste pickup requests and reports
 */

require_once '../api/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        $data = getRequestBody();
        
        switch ($action) {
            case 'submit':
                $userId = $data['user_id'] ?? null;
                
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                // Validate required fields
                $required = ['waste_type', 'location_address', 'city'];
                $missing = validateFields($data, $required);
                
                if (!empty($missing)) {
                    jsonResponse(['success' => false, 'error' => 'Missing fields: ' . implode(', ', $missing)], 400);
                }
                
                // Handle image upload if present
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../uploads/reports/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileName = time() . '_' . basename($_FILES['image']['name']);
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $data['image_path'] = 'uploads/reports/' . $fileName;
                    }
                }
                
                $result = submitWasteReport($userId, $data);
                jsonResponse($result, $result['success'] ? 201 : 400);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    case 'GET':
        switch ($action) {
            case 'my_reports':
                $userId = $_GET['user_id'] ?? null;
                $status = $_GET['status'] ?? null;
                
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                $reports = getUserReports($userId, $status);
                jsonResponse(['success' => true, 'reports' => $reports]);
                break;
                
            case 'stats':
                $userId = $_GET['user_id'] ?? null;
                
                if (!$userId) {
                    jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
                }
                
                $stats = getUserReportStats($userId);
                jsonResponse(['success' => true, 'stats' => $stats]);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
?>
