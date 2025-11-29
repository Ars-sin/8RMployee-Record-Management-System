<?php
// Set the header to output JSON
header('Content-Type: application/json');

// Use your existing config file
require_once('config.php');

// Check for a database connection
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// Check if an employee ID was sent from the app (e.g., ...?id=98765)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Employee ID is required.']);
    exit();
}

$employeeId = $_GET['id'];

// --- START: MODIFIED SECTION ---
// This SQL query replaces "SELECT *" to explicitly select and rename columns
// to perfectly match the @SerializedName annotations in your Android Employee model.
$sql = "SELECT 
            id AS employee_id, 
            first_name, 
            last_name, 
            address,
            email,
            contact_no, 
            birth_date, 
            status, 
            position, 
            date_hired, 
            assigned_project, 
            daily_rate, 
            sss_no, 
            pagibig_no, 
            philhealth_no 
        FROM employee 
        WHERE id = ?";
// --- END: MODIFIED SECTION ---

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    $conn->close();
    exit();
}

// Bind the employee ID parameter and execute
$stmt->bind_param("s", $employeeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Success: Fetch the single employee's data
    $employee = $result->fetch_assoc();
    echo json_encode($employee);
} else {
    // No employee found with that ID
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Employee not found.']);
}

$stmt->close();
$conn->close();
?>