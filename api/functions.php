<?php
/**
 * EcoConnect API Helper Functions
 * Common database operations for the platform
 */

require_once __DIR__ . '/../database/config.php';

/**
 * Send JSON response
 */
function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

/**
 * Get request body (JSON or form data)
 */
function getRequestBody() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
    
    return $_POST;
}

/**
 * Validate required fields
 */
function validateFields($data, $required) {
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * ================= USER FUNCTIONS =================
 */

/**
 * Register a new user
 */
function registerUser($data) {
    $pdo = getDBConnection();
    if (!$pdo) return ['success' => false, 'error' => 'Database connection failed'];
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Email already registered'];
        }
        
        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, phone, city, user_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $passwordHash,
            $data['phone'] ?? null,
            $data['city'] ?? null,
            $data['user_type'] ?? 'resident'
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Award welcome bonus points
        awardPoints($userId, 50, 'Welcome bonus for joining EcoConnect!', 'bonus');
        
        return ['success' => true, 'user_id' => $userId, 'message' => 'Registration successful'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Authenticate user login
 */
function loginUser($email, $password) {
    $pdo = getDBConnection();
    if (!$pdo) return ['success' => false, 'error' => 'Database connection failed'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = TRUE");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }
        
        // Update last login
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
        
        // Remove sensitive data
        unset($user['password_hash']);
        
        return ['success' => true, 'user' => $user];
        
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get user by ID
 */
function getUserById($userId) {
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    try {
        $stmt = $pdo->prepare("SELECT id, full_name, email, phone, city, area, user_type, green_points, level, profile_image, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Update user profile
 */
function updateUserProfile($userId, $data) {
    $pdo = getDBConnection();
    if (!$pdo) return ['success' => false, 'error' => 'Database connection failed'];
    
    try {
        $allowedFields = ['full_name', 'phone', 'city', 'area', 'profile_image'];
        $updates = [];
        $values = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return ['success' => false, 'error' => 'No fields to update'];
        }
        
        $values[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        
        return ['success' => true, 'message' => 'Profile updated successfully'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * ================= GREEN POINTS FUNCTIONS =================
 */

/**
 * Award points to user
 */
function awardPoints($userId, $points, $description, $type = 'earned', $referenceId = null, $referenceType = null) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $pdo->beginTransaction();
        
        // Add transaction record
        $stmt = $pdo->prepare("INSERT INTO green_points_transactions (user_id, transaction_type, points, description, reference_id, reference_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $type, $points, $description, $referenceId, $referenceType]);
        
        // Update user total points
        $stmt = $pdo->prepare("UPDATE users SET green_points = green_points + ? WHERE id = ?");
        $stmt->execute([$points, $userId]);
        
        // Update user level based on points
        updateUserLevel($userId);
        
        $pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Award points failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user level based on total points
 */
function updateUserLevel($userId) {
    $pdo = getDBConnection();
    if (!$pdo) return;
    
    $stmt = $pdo->prepare("SELECT green_points FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $points = $stmt->fetchColumn();
    
    $level = 'Seedling';
    if ($points >= 2000) $level = 'Eco Warrior';
    elseif ($points >= 1000) $level = 'Tree';
    elseif ($points >= 500) $level = 'Sapling';
    elseif ($points >= 100) $level = 'Sprout';
    
    $pdo->prepare("UPDATE users SET level = ? WHERE id = ?")->execute([$level, $userId]);
}

/**
 * Get user points history
 */
function getUserPointsHistory($userId, $limit = 20) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM green_points_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * ================= WASTE REPORT FUNCTIONS =================
 */

/**
 * Submit waste report/pickup request
 */
function submitWasteReport($userId, $data) {
    $pdo = getDBConnection();
    if (!$pdo) return ['success' => false, 'error' => 'Database connection failed'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO waste_reports (user_id, waste_type, waste_category, location_address, city, pincode, description, image_path, scheduled_date, scheduled_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $data['waste_type'],
            $data['waste_category'] ?? null,
            $data['location_address'],
            $data['city'],
            $data['pincode'] ?? null,
            $data['description'] ?? null,
            $data['image_path'] ?? null,
            $data['scheduled_date'] ?? null,
            $data['scheduled_time'] ?? null
        ]);
        
        $reportId = $pdo->lastInsertId();
        
        // Award points for reporting
        awardPoints($userId, 20, 'Waste report submitted', 'earned', $reportId, 'waste_report');
        
        return ['success' => true, 'report_id' => $reportId, 'message' => 'Report submitted successfully'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get user's waste reports
 */
function getUserReports($userId, $status = null) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        if ($status) {
            $stmt = $pdo->prepare("SELECT * FROM waste_reports WHERE user_id = ? AND status = ? ORDER BY created_at DESC");
            $stmt->execute([$userId, $status]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM waste_reports WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get report statistics for dashboard
 */
function getUserReportStats($userId) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stats = [];
        
        // Total reports
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM waste_reports WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_reports'] = $stmt->fetchColumn();
        
        // Active requests
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM waste_reports WHERE user_id = ? AND status IN ('pending', 'assigned', 'in_progress')");
        $stmt->execute([$userId]);
        $stats['active_requests'] = $stmt->fetchColumn();
        
        // Completed pickups
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM waste_reports WHERE user_id = ? AND status = 'completed'");
        $stmt->execute([$userId]);
        $stats['completed_pickups'] = $stmt->fetchColumn();
        
        // Total points earned from reports
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(points), 0) FROM green_points_transactions WHERE user_id = ? AND reference_type = 'waste_report'");
        $stmt->execute([$userId]);
        $stats['points_from_reports'] = $stmt->fetchColumn();
        
        return $stats;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * ================= SERVICES FUNCTIONS =================
 */

/**
 * Get all active services
 */
function getServices($category = null) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE category = ? AND is_active = TRUE ORDER BY display_order");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM services WHERE is_active = TRUE ORDER BY category, display_order");
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * ================= RECYCLING CENTERS FUNCTIONS =================
 */

/**
 * Get recycling centers
 */
function getRecyclingCenters($city = null, $wasteType = null) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $sql = "SELECT * FROM recycling_centers WHERE is_active = TRUE";
        $params = [];
        
        if ($city) {
            $sql .= " AND city = ?";
            $params[] = $city;
        }
        
        if ($wasteType) {
            $sql .= " AND JSON_CONTAINS(accepted_waste_types, ?)";
            $params[] = '"' . $wasteType . '"';
        }
        
        $sql .= " ORDER BY rating DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $centers = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($centers as &$center) {
            $center['accepted_waste_types'] = json_decode($center['accepted_waste_types'], true) ?? [];
            $center['services_offered'] = json_decode($center['services_offered'], true) ?? [];
        }
        
        return $centers;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * ================= QUIZ FUNCTIONS =================
 */

/**
 * Get quiz questions
 */
function getQuizQuestions($category = null, $difficulty = null, $limit = 10) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $sql = "SELECT * FROM quiz_questions WHERE is_active = TRUE";
        $params = [];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $sql .= " AND difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY RAND() LIMIT ?";
        $params[] = $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $questions = $stmt->fetchAll();
        
        // Decode options JSON
        foreach ($questions as &$q) {
            $q['options'] = json_decode($q['options'], true) ?? [];
        }
        
        return $questions;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Submit quiz attempt
 */
function submitQuizAttempt($userId, $score, $totalQuestions, $correctAnswers, $timeTaken) {
    $pdo = getDBConnection();
    if (!$pdo) return ['success' => false, 'error' => 'Database connection failed'];
    
    try {
        // Calculate points based on score
        $pointsEarned = $score;
        
        $stmt = $pdo->prepare("INSERT INTO quiz_attempts (user_id, score, total_questions, correct_answers, points_earned, time_taken) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $score, $totalQuestions, $correctAnswers, $pointsEarned, $timeTaken]);
        
        // Award points
        awardPoints($userId, $pointsEarned, 'Quiz completed - Score: ' . $score, 'earned', null, 'quiz');
        
        return ['success' => true, 'points_earned' => $pointsEarned];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * ================= EVENTS FUNCTIONS =================
 */

/**
 * Get upcoming events
 */
function getUpcomingEvents($city = null, $limit = 10) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        if ($city) {
            $stmt = $pdo->prepare("SELECT * FROM sustainability_events WHERE status = 'upcoming' AND city = ? AND event_date >= CURDATE() ORDER BY event_date LIMIT ?");
            $stmt->execute([$city, $limit]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM sustainability_events WHERE status = 'upcoming' AND event_date >= CURDATE() ORDER BY event_date LIMIT ?");
            $stmt->execute([$limit]);
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Register for event
 */
function registerForEvent($eventId, $userId, $data) {
    $pdo = getDBConnection();
    if (!$pdo) return ['success' => false, 'error' => 'Database connection failed'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO event_registrations (event_id, user_id, full_name, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $eventId,
            $userId,
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? null
        ]);
        
        // Update registered count
        $pdo->prepare("UPDATE sustainability_events SET registered_count = registered_count + 1 WHERE id = ?")->execute([$eventId]);
        
        return ['success' => true, 'message' => 'Successfully registered for event'];
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return ['success' => false, 'error' => 'Already registered for this event'];
        }
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * ================= IMPACT STATISTICS FUNCTIONS =================
 */

/**
 * Get platform impact statistics
 */
function getImpactStatistics() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    try {
        $stmt = $pdo->query("SELECT * FROM impact_statistics");
        $stats = $stmt->fetchAll();
        
        $result = [];
        foreach ($stats as $stat) {
            $result[$stat['metric_name']] = [
                'value' => $stat['metric_value'],
                'unit' => $stat['unit'],
                'description' => $stat['description']
            ];
        }
        
        return $result;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Update impact statistics
 */
function updateImpactStat($metricName, $value) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("UPDATE impact_statistics SET metric_value = ? WHERE metric_name = ?");
        return $stmt->execute([$value, $metricName]);
    } catch (PDOException $e) {
        return false;
    }
}
?>
