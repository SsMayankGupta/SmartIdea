<?php
/**
 * Login Handler with Session Management
 * EcoConnect User Authentication
 */

session_start();

require_once __DIR__ . '/../database/config.php';

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
    exit;
}

// Validate required fields
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Get user by email
    $stmt = $pdo->prepare("
        SELECT id, full_name, email, password_hash, phone, city, area, user_type, green_points, level, profile_image, is_active 
        FROM users WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
        exit;
    }
    
    // Check if account is active
    if (!$user['is_active']) {
        echo json_encode(['success' => false, 'error' => 'Account is deactivated. Please contact support.']);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
        exit;
    }
    
    // Update last login
    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
    
    // Remove password hash from response
    unset($user['password_hash']);
    unset($user['is_active']);
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => $user,
        'redirect' => '../pages/dashboard.html'
    ]);
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Login failed. Please try again.']);
}
?>
