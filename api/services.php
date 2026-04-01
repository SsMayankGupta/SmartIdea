<?php
/**
 * Services & Centers API Endpoints
 * Handle service listings and recycling center information
 */

require_once '../api/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'list':
                $category = $_GET['category'] ?? null;
                $services = getServices($category);
                jsonResponse(['success' => true, 'services' => $services]);
                break;
                
            case 'recycling_centers':
                $city = $_GET['city'] ?? null;
                $wasteType = $_GET['waste_type'] ?? null;
                
                $centers = getRecyclingCenters($city, $wasteType);
                jsonResponse(['success' => true, 'centers' => $centers]);
                break;
                
            default:
                jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
        }
        break;
        
    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
?>
