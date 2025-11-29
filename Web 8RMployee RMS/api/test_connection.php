<?php
header('Content-Type: application/json');
require_once('config.php');

try {
    // Test database connection
    $result = $conn->query("SELECT 1");
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Database connection OK']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Query failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Connection error: ' . $e->getMessage()]);
}
?>