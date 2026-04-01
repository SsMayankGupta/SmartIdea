<?php
/**
 * API to submit service request
 * Inserts into request_for_services table
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database/config.php';

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// Validate required fields
$required = ['user_id', 'city', 'center_id', 'request_type', 'contact_name', 'contact_email', 'address'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Generate unique request ID
$requestId = 'REQ' . date('Ymd') . strtoupper(substr(uniqid(), -6));

try {
    // Get center name
    $stmt = $pdo->prepare("SELECT name FROM recycling_centers WHERE id = ?");
    $stmt->execute([$data['center_id']]);
    $center = $stmt->fetch(PDO::FETCH_ASSOC);
    $centerName = $center ? $center['name'] : 'Unknown Center';
    
    // Insert service request
    $sql = "INSERT INTO request_for_services (
        request_id, user_id, city, center_id, center_name, request_type,
        contact_name, contact_email, contact_phone, address, pincode,
        preferred_date, preferred_time, special_instructions, status, points_awarded
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 10)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $requestId,
        $data['user_id'],
        $data['city'],
        $data['center_id'],
        $centerName,
        $data['request_type'],
        $data['contact_name'],
        $data['contact_email'],
        $data['contact_phone'] ?? null,
        $data['address'],
        $data['pincode'] ?? null,
        $data['preferred_date'] ?? null,
        $data['preferred_time'] ?? null,
        $data['special_instructions'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Service request submitted successfully',
        'request_id' => $requestId,
        'points_awarded' => 10
    ]);
    
} catch (PDOException $e) {
    error_log("Service request error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
