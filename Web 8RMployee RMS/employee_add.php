<?php
header('Content-Type: application/json');
require_once 'check_auth_core.php';
require_once 'logger.php'; // 1. INCLUDE THE LOGGER
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
// --- Input Validation ---
$required_fields = [
    'employee_id', 'lastName', 'firstName', 'email', 'address', 'contactNo', 'birthDate', 
    'status', 'position', 'dateHired', 'assignedProject', 'dailyRate', 
    'emergencyName', 'emergencyAddress', 'emergencyContactNo'
];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Error: Required field '{$field}' is missing."]);
        exit();
    }
}
// --- Data Preparation ---
$employee_id = $_POST['employee_id'];
$lastName = $_POST['lastName'];
$firstName = $_POST['firstName'];
$email = $_POST['email'];
$address = $_POST['address'];
$contactNo = $_POST['contactNo'];
$birthDate = $_POST['birthDate'];
$status = $_POST['status'];
$position = $_POST['position'];
$dateHired = $_POST['dateHired'];
$assignedProject = $_POST['assignedProject'];
$dailyRate = $_POST['dailyRate'];
$sss_no = $_POST['sss_no'] ?? null;
$pagibig_no = $_POST['pagibig_no'] ?? null;
$philhealth_no = $_POST['philhealth_no'] ?? null;
// Use a transaction to ensure both inserts succeed or fail together
$conn->begin_transaction();
try {
    // Step 1: Insert into the main 'employee' table with the new columns
    $sql_employee = "INSERT INTO employee 
        (id, last_name, first_name, email, address, contact_no, birth_date, status, position, date_hired, assigned_project, daily_rate, sss_no, pagibig_no, philhealth_no) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_employee = $conn->prepare($sql_employee);
    $stmt_employee->bind_param(
        "issssssssssdsss", // i for id, s for strings, d for double (dailyRate)
        $employee_id,
        $lastName,
        $firstName,
        $email,
        $address,
        $contactNo,
        $birthDate,
        $status,
        $position,
        $dateHired,
        $assignedProject,
        $dailyRate,
        $sss_no,
        $pagibig_no,
        $philhealth_no
    );
    if (!$stmt_employee->execute()) {
        throw new Exception("Failed to add employee: " . $stmt_employee->error);
    }
    $stmt_employee->close();
    // Step 2: Insert into the 'emergency_contacts' table
    $sql_emergency = "INSERT INTO emergency_contacts (employee_id, name, address, contact_no) VALUES (?, ?, ?, ?)";
    $stmt_emergency = $conn->prepare($sql_emergency);
    $stmt_emergency->bind_param(
        "isss",
        $employee_id, // Use the same manually entered ID
        $_POST['emergencyName'],
        $_POST['emergencyAddress'],
        $_POST['emergencyContactNo']
    );
    if (!$stmt_emergency->execute()) {
        throw new Exception("Failed to add emergency contact: " . $stmt_emergency->error);
    }
    $stmt_emergency->close();
    // If both queries were successful, commit the transaction
    $conn->commit();
    
    // 2. LOG THE ACTION
    log_action('Add Employee', "Added new employee: {$firstName} {$lastName} (ID: {$employee_id})");
    echo json_encode(['success' => true, 'message' => 'Employee added successfully.']);
} catch (Exception $e) {
    // If any step failed, roll back all changes
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
$conn->close();
?>