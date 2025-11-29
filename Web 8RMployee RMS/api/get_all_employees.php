<?php
// Set the header to output JSON
header('Content-Type: application/json');
// Use your existing config file for the connection
require_once('config.php');
// Check if the connection object $conn was created successfully
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection failed. Check credentials in config.php.']);
    exit();
}
// --- START: CORRECTED LOGIC ---
// Get the search and position parameters from the URL
$search = isset($_GET['search']) ? $_GET['search'] : null;
$position = isset($_GET['position']) ? $_GET['position'] : null;
// 1. Start with YOUR working base query.
// IMPORTANT: Parentheses are added around the status conditions to ensure they are evaluated correctly with AND.
// FIXED: Added 'email' to the SELECT statement
$sql = "SELECT id, first_name, last_name, email, position, status FROM employee WHERE (status = 'Active' OR status = 'On Leave')";
// 2. Append the search condition ONLY if a search term is provided
if (!empty($search)) {
    // Sanitize the input to prevent SQL injection
    $safe_search = $conn->real_escape_string($search);
    // Append with AND
    $sql .= " AND (first_name LIKE '%$safe_search%' OR last_name LIKE '%$safe_search%')";
}
// 3. Append the position filter ONLY if a position is provided
if (!empty($position)) {
    // Sanitize the input to prevent SQL injection
    $safe_position = $conn->real_escape_string($position);
    // Append with AND
    $sql .= " AND position = '$safe_position'";
}
// 4. Add ordering for a consistent list
$sql .= " ORDER BY first_name ASC";
// --- END: CORRECTED LOGIC ---
// The rest of your code remains untouched because it works correctly.
$result = $conn->query($sql);
if ($result === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'SQL Query Failed: ' . $conn->error]);
    $conn->close();
    exit();
}
$employees = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
$conn->close();
echo json_encode($employees);
?>