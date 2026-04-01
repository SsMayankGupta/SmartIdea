<?php
/**
 * Nearby Recycling Centers API
 * Fetches recycling centers based on user's location
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database/config.php';

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get parameters
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
$city = isset($_GET['city']) ? trim($_GET['city']) : null;
$radius = isset($_GET['radius']) ? intval($_GET['radius']) : 10; // Default 10km radius

// If coordinates provided, find nearby centers using Haversine formula
if ($lat !== null && $lng !== null) {
    try {
        $sql = "SELECT 
            id,
            name,
            type,
            address,
            city,
            state,
            pincode,
            phone,
            operating_hours,
            accepted_waste_types,
            services_offered,
            rating,
            latitude,
            longitude,
            (6371 * acos(
                cos(radians(?)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(latitude))
            )) AS distance
        FROM recycling_centers 
        WHERE is_active = 1
        HAVING distance <= ?
        ORDER BY distance ASC
        LIMIT 20";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$lat, $lng, $lat, $radius]);
        
        $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Parse JSON fields
        foreach ($centers as &$center) {
            $center['accepted_waste_types'] = json_decode($center['accepted_waste_types'], true) ?: [];
            $center['services_offered'] = json_decode($center['services_offered'], true) ?: [];
            $center['distance'] = round(floatval($center['distance']), 2);
        }
        
        // Store search in database for analytics (optional)
        storeSearchLocation($pdo, $lat, $lng, $city, count($centers));
        
        echo json_encode([
            'success' => true,
            'user_location' => ['lat' => $lat, 'lng' => $lng],
            'count' => count($centers),
            'centers' => $centers
        ]);
    } catch (PDOException $e) {
        error_log("Database error in GPS search: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// If city provided, search by city name
if ($city) {
    try {
        $sql = "SELECT 
            id,
            name,
            type,
            address,
            city,
            state,
            pincode,
            phone,
            operating_hours,
            accepted_waste_types,
            services_offered,
            rating,
            latitude,
            longitude
        FROM recycling_centers 
        WHERE is_active = 1 AND (city LIKE :city OR address LIKE :city)
        ORDER BY rating DESC, name ASC
        LIMIT 20";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':city' => '%' . $city . '%']);
        
        $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($centers as &$center) {
            $center['accepted_waste_types'] = json_decode($center['accepted_waste_types'], true) ?: [];
            $center['services_offered'] = json_decode($center['services_offered'], true) ?: [];
            $center['distance'] = null;
        }
        
        echo json_encode([
            'success' => true,
            'search_city' => $city,
            'count' => count($centers),
            'centers' => $centers
        ]);
    } catch (PDOException $e) {
        error_log("Database error in city search: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// If no parameters, return all active centers
try {
    $sql = "SELECT 
        id,
        name,
        type,
        address,
        city,
        state,
        pincode,
        phone,
        operating_hours,
        accepted_waste_types,
        services_offered,
        rating,
        latitude,
        longitude
    FROM recycling_centers 
    WHERE is_active = 1
    ORDER BY rating DESC, name ASC
    LIMIT 50";

    $stmt = $pdo->query($sql);
    $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($centers as &$center) {
        $center['accepted_waste_types'] = json_decode($center['accepted_waste_types'], true) ?: [];
        $center['services_offered'] = json_decode($center['services_offered'], true) ?: [];
        $center['distance'] = null;
    }

    echo json_encode([
        'success' => true,
        'count' => count($centers),
        'centers' => $centers
    ]);
} catch (PDOException $e) {
    error_log("Database error fetching all centers: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

/**
 * Store user search location for analytics
 */
function storeSearchLocation($pdo, $lat, $lng, $city, $resultsCount) {
    try {
        // Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS location_searches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            latitude DECIMAL(10, 8),
            longitude DECIMAL(11, 8),
            city VARCHAR(100),
            results_count INT DEFAULT 0,
            ip_address VARCHAR(45),
            searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_searched_at (searched_at)
        ) ENGINE=InnoDB");
        
        // Insert search record
        $stmt = $pdo->prepare("INSERT INTO location_searches 
            (latitude, longitude, city, results_count, ip_address) 
            VALUES (:lat, :lng, :city, :results, :ip)");
        $stmt->execute([
            ':lat' => $lat,
            ':lng' => $lng,
            ':city' => $city,
            ':results' => $resultsCount,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        // Silently fail - analytics shouldn't break main functionality
        error_log("Failed to store search location: " . $e->getMessage());
    }
}
?>
