<?php
/**
 * Insert dummy data into request_for_services table
 */

require_once 'config.php';

$pdo = getDBConnection();

if (!$pdo) {
    die("Failed to connect to database\n");
}

$sql = file_get_contents(__DIR__ . '/dummy_requests.sql');

try {
    $pdo->exec($sql);
    echo "✓ Dummy service requests inserted successfully!\n";
    echo "✓ 7 sample requests added to request_for_services table\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
