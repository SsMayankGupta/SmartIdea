<?php
/**
 * Database Setup Script
 * Run this to initialize the EcoConnect database
 */

require_once 'config.php';

echo "EcoConnect Database Setup\n";
echo "========================\n\n";

// Step 1: Create database
echo "Step 1: Creating database...\n";
if (initializeDatabase()) {
    echo "✓ Database 'ecoconnect' created or already exists\n\n";
} else {
    echo "✗ Failed to create database\n\n";
    exit(1);
}

// Step 2: Get connection and execute schema
echo "Step 2: Creating tables...\n";
$pdo = getDBConnection();

if (!$pdo) {
    echo "✗ Failed to connect to database\n";
    exit(1);
}

// Read and execute schema
$schema = file_get_contents(__DIR__ . '/schema.sql');

// Split by semicolon to execute each statement
$statements = array_filter(array_map('trim', explode(';', $schema)));

$success = 0;
$failed = 0;

foreach ($statements as $statement) {
    if (empty($statement)) continue;
    
    try {
        $pdo->exec($statement);
        $success++;
        echo ".";
    } catch (PDOException $e) {
        $failed++;
        echo "\n✗ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n\n";
echo "✓ Successfully executed $success statements\n";
if ($failed > 0) {
    echo "✗ Failed to execute $failed statements\n";
}

echo "\nDatabase setup completed!\n";
echo "You can now use the EcoConnect platform.\n";
?>
