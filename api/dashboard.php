<?php
/**
 * Dashboard API Endpoints
 * Handle dashboard data aggregation
 */

require_once '../api/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'stats':
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            jsonResponse(['success' => false, 'error' => 'User ID required'], 400);
        }
        
        $user = getUserById($userId);
        if (!$user) {
            jsonResponse(['success' => false, 'error' => 'User not found'], 404);
        }
        
        $reportStats = getUserReportStats($userId);
        $pointsHistory = getUserPointsHistory($userId, 5);
        $recentReports = getUserReports($userId);
        
        // Get upcoming events
        $events = getUpcomingEvents($user['city'] ?? null, 3);
        
        $dashboard = [
            'user' => $user,
            'report_stats' => $reportStats,
            'recent_points' => $pointsHistory,
            'recent_reports' => array_slice($recentReports, 0, 5),
            'upcoming_events' => $events,
            'progress_to_next_level' => calculateProgressToNextLevel($user['green_points'])
        ];
        
        jsonResponse(['success' => true, 'dashboard' => $dashboard]);
        break;
        
    case 'impact':
        $impact = getImpactStatistics();
        jsonResponse(['success' => true, 'impact' => $impact]);
        break;
        
    default:
        jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
}

/**
 * Calculate progress to next level
 */
function calculateProgressToNextLevel($points) {
    $levels = [
        'Seedling' => 0,
        'Sprout' => 100,
        'Sapling' => 500,
        'Tree' => 1000,
        'Eco Warrior' => 2000
    ];
    
    $currentLevel = 'Seedling';
    $nextLevel = 'Sprout';
    $currentMin = 0;
    $nextMin = 100;
    
    foreach ($levels as $level => $min) {
        if ($points >= $min) {
            $currentLevel = $level;
            $currentMin = $min;
        } else {
            $nextLevel = $level;
            $nextMin = $min;
            break;
        }
    }
    
    if ($currentLevel === 'Eco Warrior') {
        return [
            'current_level' => $currentLevel,
            'next_level' => null,
            'progress_percent' => 100,
            'points_to_next' => 0
        ];
    }
    
    $progress = (($points - $currentMin) / ($nextMin - $currentMin)) * 100;
    
    return [
        'current_level' => $currentLevel,
        'next_level' => $nextLevel,
        'progress_percent' => min(100, max(0, $progress)),
        'points_to_next' => $nextMin - $points
    ];
}
?>
