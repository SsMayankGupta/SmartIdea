<?php
/**
 * Signup Handler with Session Management
 * EcoConnect User Registration
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
$required = ['full_name', 'email', 'password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

// Validate password length
if (strlen($data['password']) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
    
    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password_hash, phone, city, user_type, green_points, level) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['full_name'],
        $data['email'],
        $passwordHash,
        $data['phone'] ?? null,
        $data['city'] ?? null,
        $data['user_type'] ?? 'resident',
        50, // Welcome bonus points
        'Seedling'
    ]);
    
    $userId = $pdo->lastInsertId();
    
    // Add points transaction
    $stmt = $pdo->prepare("
        INSERT INTO green_points_transactions (user_id, transaction_type, points, description, reference_type) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, 'earned', 50, 'Welcome bonus for joining EcoConnect!', 'bonus']);
    
    // Get user data for session
    $stmt = $pdo->prepare("
        SELECT id, full_name, email, phone, city, user_type, green_points, level, profile_image, created_at 
        FROM users WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Welcome bonus: 50 points',
        'user' => $user,
        'redirect' => '../pages/dashboard.html'
    ]);
    
} catch (PDOException $e) {
    error_log("Signup error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Registration failed. Please try again.']);
}
?>
