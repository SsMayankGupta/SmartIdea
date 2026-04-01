<?php
/**
 * Test script to verify database and API setup
 * Access this at: http://localhost/ecoconnect/api/test.php
 */

header('Content-Type: text/html');

echo '<h1>EcoConnect API Diagnostics</h1>';
echo '<hr>';

// 1. Test database connection
echo '<h2>1. Database Connection</h2>';
try {
    require_once '../database/config.php';
    $pdo = getDBConnection();
    if ($pdo) {
        echo '<p style="color:green;">Database connection: SUCCESS</p>';
    } else {
        echo '<p style="color:red;">Database connection: FAILED</p>';
    }
} catch (Exception $e) {
    echo '<p style="color:red;">Database connection: FAILED - ' . $e->getMessage() . '</p>';
}

// 2. Check if recycling_centers table exists
echo '<h2>2. Table Check</h2>';
if ($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'recycling_centers'");
        if ($stmt->rowCount() > 0) {
            echo '<p style="color:green;">recycling_centers table: EXISTS</p>';
            
            // Count records
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM recycling_centers WHERE is_active = 1");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo '<p>Active centers: ' . $count . '</p>';
            
            // Show sample data
            $stmt = $pdo->query("SELECT id, name, city, latitude, longitude FROM recycling_centers WHERE is_active = 1 LIMIT 3");
            $centers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<h3>Sample Data:</h3>';
            echo '<table border="1" cellpadding="5">';
            echo '<tr><th>ID</th><th>Name</th><th>City</th><th>Latitude</th><th>Longitude</th></tr>';
            foreach ($centers as $center) {
                echo '<tr>';
                echo '<td>' . $center['id'] . '</td>';
                echo '<td>' . $center['name'] . '</td>';
                echo '<td>' . $center['city'] . '</td>';
                echo '<td>' . $center['latitude'] . '</td>';
                echo '<td>' . $center['longitude'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
        } else {
            echo '<p style="color:red;">recycling_centers table: MISSING</p>';
            echo '<p>Run <code>database/setup.php</code> or import <code>database/schema.sql</code></p>';
        }
    } catch (PDOException $e) {
        echo '<p style="color:red;">Error checking table: ' . $e->getMessage() . '</p>';
    }
}

// 3. Test API endpoints
echo '<hr><h2>3. API Endpoint Tests</h2>';

// Test basic fetch
echo '<h3>Test 1: Fetch All Centers</h3>';
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/centers.php';
echo '<p>API URL: ' . $apiUrl . '</p>';

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo '<p>HTTP Status: ' . $httpCode . '</p>';
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo '<p style="color:green;">API Response: SUCCESS</p>';
            echo '<p>Centers found: ' . ($data['count'] ?? 0) . '</p>';
        } else {
            echo '<p style="color:orange;">API Response: Invalid JSON</p>';
            echo '<pre>' . htmlspecialchars($response) . '</pre>';
        }
    } else {
        echo '<p style="color:red;">API Response: HTTP ' . $httpCode . '</p>';
    }
} catch (Exception $e) {
    echo '<p style="color:red;">API Test Failed: ' . $e->getMessage() . '</p>';
}

// Test city search
echo '<h3>Test 2: Search by City (Delhi)</h3>';
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '?city=Delhi');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo '<p>HTTP Status: ' . $httpCode . '</p>';
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo '<p style="color:green;">City Search: SUCCESS (Found ' . $data['count'] . ' centers)</p>';
        } else {
            echo '<p style="color:red;">City Search: ' . ($data['message'] ?? 'Unknown error') . '</p>';
        }
    }
} catch (Exception $e) {
    echo '<p style="color:red;">City Search Failed: ' . $e->getMessage() . '</p>';
}

// Test GPS search
echo '<h3>Test 3: GPS Search (Delhi/NCR coordinates)</h3>';
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '?lat=28.6139&lng=77.2090&radius=20');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo '<p>HTTP Status: ' . $httpCode . '</p>';
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo '<p style="color:green;">GPS Search: SUCCESS (Found ' . $data['count'] . ' centers)</p>';
        } else {
            echo '<p style="color:red;">GPS Search: ' . ($data['message'] ?? 'Unknown error') . '</p>';
        }
    }
} catch (Exception $e) {
    echo '<p style="color:red;">GPS Search Failed: ' . $e->getMessage() . '</p>';
}

echo '<hr>';
echo '<h2>Quick Fixes</h2>';
echo '<ol>';
echo '<li>If database connection fails: Check config in <code>database/config.php</code></li>';
echo '<li>If table is missing: Run <code>database/setup.php</code> or import <code>database/schema.sql</code> in phpMyAdmin</li>';
echo '<li>If API returns 404: Check that <code>api/centers.php</code> exists</li>';
echo '<li>If API returns 500: Check PHP error logs in Laragon/logs</li>';
echo '</ol>';

echo '<hr>';
echo '<p><a href="../pages/centers.html">Go to Centers Page</a></p>';
?>
