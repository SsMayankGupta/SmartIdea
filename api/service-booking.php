<?php
/**
 * EcoConnect Service Booking API
 * Single file handling: cities/centers fetch, request submission, tracking, dummy data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'ecoconnect';
$username = 'root';
$password = '';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    // Database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    switch ($action) {
        // ============================================
        // GET ALL CITIES
        // ============================================
        case 'get_cities':
            $stmt = $pdo->query("SELECT DISTINCT city FROM recycling_centers WHERE is_active = TRUE ORDER BY city ASC");
            $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['success' => true, 'cities' => $cities]);
            break;

        // ============================================
        // GET CENTERS BY CITY
        // ============================================
        case 'get_centers':
            $city = $_GET['city'] ?? '';
            if (empty($city)) {
                echo json_encode(['success' => false, 'error' => 'City parameter required']);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT id, name, type, address, phone, operating_hours, accepted_waste_types, services_offered, rating 
                                  FROM recycling_centers 
                                  WHERE city = ? AND is_active = TRUE 
                                  ORDER BY rating DESC");
            $stmt->execute([$city]);
            $centers = $stmt->fetchAll();
            echo json_encode(['success' => true, 'centers' => $centers]);
            break;

        // ============================================
        // SUBMIT SERVICE REQUEST
        // ============================================
        case 'submit_request':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'POST method required']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $required = ['user_id', 'city', 'center_id', 'request_type', 'contact_name', 'contact_email', 'contact_phone', 'address'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    echo json_encode(['success' => false, 'error' => "Field '$field' is required"]);
                    exit;
                }
            }

            // Generate unique request ID
            $request_id = 'REQ-' . strtoupper(substr(md5(uniqid()), 0, 8));

            // Get center name
            $centerStmt = $pdo->prepare("SELECT name FROM recycling_centers WHERE id = ?");
            $centerStmt->execute([$data['center_id']]);
            $center = $centerStmt->fetch();
            $center_name = $center ? $center['name'] : 'Unknown Center';

            // Insert request
            $stmt = $pdo->prepare("INSERT INTO request_for_services 
                (request_id, user_id, city, center_id, center_name, request_type, contact_name, contact_email, contact_phone, address, pincode, preferred_date, preferred_time, special_instructions, points_awarded) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 50)");
            
            $stmt->execute([
                $request_id,
                $data['user_id'],
                $data['city'],
                $data['center_id'],
                $center_name,
                $data['request_type'],
                $data['contact_name'],
                $data['contact_email'],
                $data['contact_phone'],
                $data['address'],
                $data['pincode'] ?? '',
                $data['preferred_date'] ?? null,
                $data['preferred_time'] ?? '',
                $data['special_instructions'] ?? ''
            ]);

            echo json_encode([
                'success' => true, 
                'message' => 'Service request submitted successfully', 
                'request_id' => $request_id,
                'points_awarded' => 50
            ]);
            break;

        // ============================================
        // GET USER REQUESTS (TRACKING)
        // ============================================
        case 'get_user_requests':
            $user_id = $_GET['user_id'] ?? '';
            if (empty($user_id)) {
                echo json_encode(['success' => false, 'error' => 'User ID required']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT request_id, city, center_name, request_type, status, contact_name, contact_phone, 
                                  address, preferred_date, preferred_time, special_instructions, points_awarded, 
                                  created_at, updated_at, assigned_at, completed_at
                                  FROM request_for_services 
                                  WHERE user_id = ? 
                                  ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $requests = $stmt->fetchAll();
            
            // Calculate estimated completion time based on status
            foreach ($requests as &$request) {
                $request['estimated_completion'] = calculateEstimatedCompletion($request['status'], $request['created_at']);
                $request['status_color'] = getStatusColor($request['status']);
                $request['status_icon'] = getStatusIcon($request['status']);
                $request['progress_percent'] = getProgressPercent($request['status']);
            }

            echo json_encode(['success' => true, 'requests' => $requests]);
            break;

        // ============================================
        // GET SINGLE REQUEST STATUS
        // ============================================
        case 'get_request_status':
            $request_id = $_GET['request_id'] ?? '';
            if (empty($request_id)) {
                echo json_encode(['success' => false, 'error' => 'Request ID required']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT request_id, city, center_name, request_type, status, contact_name, contact_phone, 
                                  address, preferred_date, preferred_time, special_instructions, points_awarded, 
                                  created_at, updated_at, assigned_at, completed_at
                                  FROM request_for_services 
                                  WHERE request_id = ?");
            $stmt->execute([$request_id]);
            $request = $stmt->fetch();

            if ($request) {
                $request['estimated_completion'] = calculateEstimatedCompletion($request['status'], $request['created_at']);
                $request['status_color'] = getStatusColor($request['status']);
                $request['status_icon'] = getStatusIcon($request['status']);
                $request['progress_percent'] = getProgressPercent($request['status']);
                $request['status_timeline'] = generateStatusTimeline($request);
                echo json_encode(['success' => true, 'request' => $request]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Request not found']);
            }
            break;

        // ============================================
        // INSERT DUMMY DATA FOR DEMONSTRATION
        // ============================================
        case 'insert_dummy_data':
            // Create dummy users if not exist
            $dummyUsers = [
                ['id' => 9991, 'name' => 'Rahul Sharma', 'email' => 'rahul.demo@ecoconnect.com', 'phone' => '9876543210', 'city' => 'Delhi'],
                ['id' => 9992, 'name' => 'Priya Patel', 'email' => 'priya.demo@ecoconnect.com', 'phone' => '9876543211', 'city' => 'Noida'],
                ['id' => 9993, 'name' => 'Amit Kumar', 'email' => 'amit.demo@ecoconnect.com', 'phone' => '9876543212', 'city' => 'Gurugram']
            ];

            foreach ($dummyUsers as $user) {
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
                $checkStmt->execute([$user['id']]);
                if (!$checkStmt->fetch()) {
                    $insertStmt = $pdo->prepare("INSERT INTO users (id, full_name, email, password_hash, phone, city, user_type, green_points) 
                                                VALUES (?, ?, ?, ?, ?, ?, 'resident', 100)");
                    $insertStmt->execute([$user['id'], $user['name'], $user['email'], password_hash('demo123', PASSWORD_DEFAULT), $user['phone'], $user['city']]);
                }
            }

            // Create dummy service requests
            $dummyRequests = [
                ['REQ-DEMO-001', 9991, 'Delhi', 3, 'Delhi Municipal Recycling Hub', 'Household Waste Pickup', 'Completed', 'Rahul Sharma', 'rahul.demo@ecoconnect.com', '9876543210', '123 Karol Bagh, Delhi', '110005', 50],
                ['REQ-DEMO-002', 9992, 'Noida', 1, 'Noida Sector 62 Recycling Center', 'Recycling Services', 'In Progress', 'Priya Patel', 'priya.demo@ecoconnect.com', '9876543211', '456 Sector 62, Noida', '201309', 50],
                ['REQ-DEMO-003', 9993, 'Gurugram', 2, 'Gurugram Electronic Waste Center', 'Hazardous Waste Handling', 'Approved', 'Amit Kumar', 'amit.demo@ecoconnect.com', '9876543212', '789 DLF Phase 2, Gurugram', '122002', 75],
                ['REQ-DEMO-004', 9991, 'Delhi', 3, 'Delhi Municipal Recycling Hub', 'Bulk Pickup', 'Pending', 'Rahul Sharma', 'rahul.demo@ecoconnect.com', '9876543210', '123 Karol Bagh, Delhi', '110005', 0],
                ['REQ-DEMO-005', 9992, 'Noida', 1, 'Noida Sector 62 Recycling Center', 'Industrial Waste Disposal', 'Cancelled', 'Priya Patel', 'priya.demo@ecoconnect.com', '9876543211', '456 Sector 62, Noida', '201309', 0]
            ];

            $inserted = 0;
            foreach ($dummyRequests as $req) {
                $checkStmt = $pdo->prepare("SELECT id FROM request_for_services WHERE request_id = ?");
                $checkStmt->execute([$req[0]]);
                if (!$checkStmt->fetch()) {
                    $stmt = $pdo->prepare("INSERT INTO request_for_services 
                        (request_id, user_id, city, center_id, center_name, request_type, status, contact_name, contact_email, contact_phone, address, pincode, points_awarded, created_at, preferred_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY), DATE_ADD(NOW(), INTERVAL 2 DAY))");
                    
                    $days_ago = rand(1, 10);
                    $stmt->execute([$req[0], $req[1], $req[2], $req[3], $req[4], $req[5], $req[6], $req[7], $req[8], $req[9], $req[10], $req[11], $req[12], $days_ago]);
                    $inserted++;
                }
            }

            echo json_encode(['success' => true, 'message' => "Inserted $inserted dummy requests", 'dummy_users_created' => count($dummyUsers)]);
            break;

        // ============================================
        // UPDATE REQUEST STATUS (Admin/Simulation)
        // ============================================
        case 'update_status':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'POST method required']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $request_id = $data['request_id'] ?? '';
            $new_status = $data['status'] ?? '';
            $valid_statuses = ['Pending', 'Approved', 'In Progress', 'Completed', 'Cancelled'];

            if (empty($request_id) || !in_array($new_status, $valid_statuses)) {
                echo json_encode(['success' => false, 'error' => 'Invalid request ID or status']);
                exit;
            }

            // Set timestamps based on status
            $updateFields = ['status' => $new_status];
            if ($new_status === 'Approved') {
                $updateFields['assigned_at'] = date('Y-m-d H:i:s');
            } elseif ($new_status === 'Completed') {
                $updateFields['completed_at'] = date('Y-m-d H:i:s');
            }

            $fields = [];
            $values = [];
            foreach ($updateFields as $key => $val) {
                $fields[] = "$key = ?";
                $values[] = $val;
            }
            $values[] = $request_id;

            $stmt = $pdo->prepare("UPDATE request_for_services SET " . implode(', ', $fields) . " WHERE request_id = ?");
            $stmt->execute($values);

            echo json_encode(['success' => true, 'message' => 'Status updated successfully', 'new_status' => $new_status]);
            break;

        // ============================================
        // GET REQUEST STATISTICS
        // ============================================
        case 'get_statistics':
            $user_id = $_GET['user_id'] ?? '';
            
            if ($user_id) {
                // User-specific stats
                $stmt = $pdo->prepare("SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(points_awarded) as total_points
                    FROM request_for_services WHERE user_id = ?");
                $stmt->execute([$user_id]);
            } else {
                // Global stats
                $stmt = $pdo->query("SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(points_awarded) as total_points
                    FROM request_for_services");
            }
            
            $stats = $stmt->fetch();
            echo json_encode(['success' => true, 'statistics' => $stats]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action. Available actions: get_cities, get_centers, submit_request, get_user_requests, get_request_status, insert_dummy_data, update_status, get_statistics']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function calculateEstimatedCompletion($status, $created_at) {
    $created = strtotime($created_at);
    switch ($status) {
        case 'Pending':
            return date('Y-m-d H:i:s', strtotime('+24 hours', $created));
        case 'Approved':
            return date('Y-m-d H:i:s', strtotime('+48 hours', $created));
        case 'In Progress':
            return date('Y-m-d H:i:s', strtotime('+12 hours', $created));
        case 'Completed':
            return $created_at;
        case 'Cancelled':
            return null;
        default:
            return date('Y-m-d H:i:s', strtotime('+48 hours', $created));
    }
}

function getStatusColor($status) {
    $colors = [
        'Pending' => '#FFA500',
        'Approved' => '#4CAF50',
        'In Progress' => '#2196F3',
        'Completed' => '#2E7D32',
        'Cancelled' => '#F44336'
    ];
    return $colors[$status] ?? '#999';
}

function getStatusIcon($status) {
    $icons = [
        'Pending' => '⏳',
        'Approved' => '✅',
        'In Progress' => '🔄',
        'Completed' => '🎉',
        'Cancelled' => '❌'
    ];
    return $icons[$status] ?? '❓';
}

function getProgressPercent($status) {
    $progress = [
        'Pending' => 20,
        'Approved' => 40,
        'In Progress' => 70,
        'Completed' => 100,
        'Cancelled' => 0
    ];
    return $progress[$status] ?? 10;
}

function generateStatusTimeline($request) {
    $timeline = [];
    $created = strtotime($request['created_at']);
    
    // Always show request submitted
    $timeline[] = [
        'status' => 'Request Submitted',
        'time' => $request['created_at'],
        'completed' => true,
        'icon' => '📝'
    ];
    
    // Show approval
    if ($request['assigned_at']) {
        $timeline[] = [
            'status' => 'Request Approved',
            'time' => $request['assigned_at'],
            'completed' => true,
            'icon' => '✅'
        ];
    } elseif ($request['status'] !== 'Cancelled') {
        $timeline[] = [
            'status' => 'Awaiting Approval',
            'time' => null,
            'completed' => false,
            'icon' => '⏳'
        ];
    }
    
    // Show in progress
    if ($request['status'] === 'In Progress' || $request['status'] === 'Completed') {
        $timeline[] = [
            'status' => 'Pickup In Progress',
            'time' => $request['updated_at'],
            'completed' => $request['status'] === 'Completed',
            'icon' => '🚛'
        ];
    }
    
    // Show completion
    if ($request['status'] === 'Completed') {
        $timeline[] = [
            'status' => 'Service Completed',
            'time' => $request['completed_at'] ?? $request['updated_at'],
            'completed' => true,
            'icon' => '🎉'
        ];
    } elseif ($request['status'] === 'Cancelled') {
        $timeline[] = [
            'status' => 'Request Cancelled',
            'time' => $request['updated_at'],
            'completed' => true,
            'icon' => '❌'
        ];
    } else {
        $timeline[] = [
            'status' => 'Service Completion',
            'time' => null,
            'completed' => false,
            'icon' => '🏁'
        ];
    }
    
    return $timeline;
}
