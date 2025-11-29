<?php
require_once('config.php');
header('Content-Type: application/json');

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

$searchTerm = $_GET['search'] ?? '';

// Query the correct 'employee_archive' table
$sql = "SELECT id, first_name, last_name, position FROM employee_archive";
$params = [];
$paramTypes = '';

if (!empty($searchTerm)) {
    $sql .= " WHERE CONCAT(first_name, ' ', last_name) LIKE ?";
    $params[] = "%" . $searchTerm . "%";
    $paramTypes .= 's';
}

$sql .= " ORDER BY first_name ASC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($paramTypes, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $archived_employees = array();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $archived_employees[] = $row;
        }
    }
    
    $stmt->close();
    echo json_encode($archived_employees);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'SQL statement failed.']);
}

$conn->close();
?>