<?php
/**
 * API to get recycling centers for dropdown
 * Returns centers grouped by city or filtered by city
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../database/config.php';

$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$city = isset($_GET['city']) ? trim($_GET['city']) : null;

if ($city) {
    // Get centers for specific city
    try {
        $stmt = $pdo->prepare("SELECT id, name, address, city FROM recycling_centers WHERE city = ? AND is_active = 1 ORDER BY name ASC");
        $stmt->execute([$city]);
        $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'city' => $city, 'centers' => $centers]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Get all cities with their centers
    try {
        $stmt = $pdo->query("SELECT DISTINCT city FROM recycling_centers WHERE is_active = 1 ORDER BY city ASC");
        $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get all centers grouped by city
        $stmt = $pdo->query("SELECT id, name, address, city FROM recycling_centers WHERE is_active = 1 ORDER BY city ASC, name ASC");
        $allCenters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group centers by city
        $centersByCity = [];
        foreach ($allCenters as $center) {
            $centersByCity[$center['city']][] = [
                'id' => $center['id'],
                'name' => $center['name'],
                'address' => $center['address']
            ];
        }
        
        echo json_encode([
            'success' => true, 
            'cities' => $cities,
            'centers_by_city' => $centersByCity
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
