<?php
// Set headers for CORS and JSON content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 1. Include your existing database connection file
require_once('config.php');

// 2. SQL Query to fetch all logs from the correct table name.
//    - We format the 'created_at' timestamp into a single readable string.
//    - We order by 'id' DESC to get the newest logs first.
$sql = "SELECT 
            user_name, 
            action_type, 
            description, 
            DATE_FORMAT(created_at, '%m/%d/%Y %H:%i:%s') as created_at 
        FROM modification_logs 
        ORDER BY id DESC";

$result = $conn->query($sql);

if ($result) {
    $logs = array();
    // Fetch all rows from the result into a new array
    while($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    // Set the HTTP response code to 200 (OK)
    http_response_code(200);
    // Return the complete array of logs as a JSON response
    echo json_encode($logs);
} else {
    // If the query fails, return an error message
    http_response_code(500);
    echo json_encode(["message" => "Error fetching logs: " . $conn->error]);
}

$conn->close();
?>