<?php
header('Content-Type: application/json');
require_once 'check_auth_core.php';

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection Failed: ' . $conn->connect_error]);
    exit();
}

$employee_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$employee_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid employee ID.']);
    exit();
}

// This single query correctly fetches all employee and emergency contact details.
$sql = "
    SELECT 
        e.*, 
        ec.name AS emergencyName,
        ec.address AS emergencyAddress,
        ec.contact_no AS emergencyContactNo
    FROM 
        employee e
    LEFT JOIN 
        emergency_contacts ec ON e.id = ec.employee_id
    WHERE 
        e.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee_data = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $employee_data]);
} else {
    echo json_encode(['success' => false, 'message' => 'Employee not found.']);
}

$stmt->close();
$conn->close();
?>