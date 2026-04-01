<?php
/**
 * API to get user's service requests
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database/config.php';

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT 
        id,
        request_id,
        city,
        center_name,
        request_type,
        status,
        contact_name,
        contact_email,
        contact_phone,
        address,
        pincode,
        preferred_date,
        preferred_time,
        special_instructions,
        points_awarded,
        created_at,
        updated_at
    FROM request_for_services 
    WHERE user_id = ? 
    ORDER BY created_at DESC");
    
    $stmt->execute([$userId]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'count' => count($requests),
        'requests' => $requests
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
