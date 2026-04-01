<?php
/**
 * Events API Endpoints
 * Handle sustainability events and registrations
 */

require_once '../api/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'upcoming':
                $city = $_GET['city'] ?? null;
                $limit = $_GET['limit'] ?? 10;
                
                $events = getUpcomingEvents($city, $limit);
                jsonResponse(['success' => true, 'events' => $events]);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    case 'POST':
        $data = getRequestBody();
        
        switch ($action) {
            case 'register':
                $eventId = $data['event_id'] ?? null;
                $userId = $data['user_id'] ?? null;
                
                if (!$eventId || !$userId) {
                    jsonResponse(['success' => false, 'error' => 'Event ID and User ID required'], 400);
                }
                
                // Validate required fields
                $required = ['full_name', 'email'];
                $missing = validateFields($data, $required);
                
                if (!empty($missing)) {
                    jsonResponse(['success' => false, 'error' => 'Missing fields: ' . implode(', ', $missing)], 400);
                }
                
                $result = registerForEvent($eventId, $userId, $data);
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
