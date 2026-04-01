<?php
/**
 * API to cancel a service request
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database/config.php';

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$requestId = isset($data['request_id']) ? intval($data['request_id']) : null;
$userId = isset($data['user_id']) ? intval($data['user_id']) : null;

if (!$requestId || !$userId) {
    echo json_encode(['success' => false, 'message' => 'Request ID and User ID required']);
    exit;
}

try {
    // Verify the request belongs to the user and is pending
    $stmt = $pdo->prepare("SELECT status FROM request_for_services WHERE id = ? AND user_id = ?");
    $stmt->execute([$requestId, $userId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit;
    }
    
    if ($request['status'] !== 'Pending') {
        echo json_encode(['success' => false, 'message' => 'Only pending requests can be cancelled']);
        exit;
    }
    
    // Cancel the request
    $stmt = $pdo->prepare("UPDATE request_for_services SET status = 'Cancelled', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$requestId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Request cancelled successfully'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
