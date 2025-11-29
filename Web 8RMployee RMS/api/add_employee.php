<?php
// --- START: CRUCIAL DEBUGGING LINES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END: CRUCIAL DEBUGGING LINES ---
header('Content-Type: application/json');
require_once('config.php');
// Check for logger but don't fail if it's missing
if (file_exists('logger.php')) {
    require_once('logger.php');
}
// --- Detailed Input Validation ---
$required_fields = [
    'employeeId', 'firstName', 'lastName', 'address', 'email', 'birthDate', 'contactNo', 'status', 
    'position', 'dateHired', 'dailyRate', 'assignedProject', 'emergencyName', 
    'emergencyContact', 'emergencyAddress'
];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '{$field}' is missing or empty."]);
        exit();
    }
}
// --- Begin Transaction for Data Integrity ---
$conn->begin_transaction();
try {
    // --- Check if Employee ID already exists ---
    $check_stmt = $conn->prepare("SELECT id FROM employee WHERE id = ?");
    $check_stmt->bind_param("s", $_POST['employeeId']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("Employee ID '{$_POST['employeeId']}' already exists. Please use a different ID.");
    }
    $check_stmt->close();
    
    // --- Step 1: Insert into the 'employee' table ---
    $stmt_employee = $conn->prepare(
        "INSERT INTO employee (id, first_name, last_name, address, email, contact_no, birth_date, status, position, date_hired, assigned_project, daily_rate, sss_no, pagibig_no, philhealth_no) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    // Use isset with null coalescing operator (??) for optional fields
    $sss_no = $_POST['sssNo'] ?? null;
    $pagibig_no = $_POST['pagibigNo'] ?? null;
    $philhealth_no = $_POST['philhealthNo'] ?? null;
    
    $stmt_employee->bind_param("sssssssssssdsss", 
        $_POST['employeeId'], $_POST['firstName'], $_POST['lastName'], $_POST['address'], 
        $_POST['email'], $_POST['contactNo'], $_POST['birthDate'], $_POST['status'], $_POST['position'], 
        $_POST['dateHired'], $_POST['assignedProject'], $_POST['dailyRate'], 
        $sss_no, $pagibig_no, $philhealth_no
    );
    
    if (!$stmt_employee->execute()) {
        throw new Exception("Error adding to employee table: " . $stmt_employee->error);
    }
    $stmt_employee->close();
    
    // --- Step 2: Insert into the 'emergency_contacts' table ---
    // Use the manually entered employeeId as the foreign key
    $stmt_contact = $conn->prepare(
        "INSERT INTO emergency_contacts (employee_id, name, contact_no, address) 
         VALUES (?, ?, ?, ?)"
    );
    
    $stmt_contact->bind_param("ssss", 
        $_POST['employeeId'], $_POST['emergencyName'], 
        $_POST['emergencyContact'], $_POST['emergencyAddress']
    );
    
    if (!$stmt_contact->execute()) {
        throw new Exception("Error adding to emergency_contacts table: " . $stmt_contact->error);
    }
    $stmt_contact->close();
    
    // --- Step 3: Log the successful action ---
    if (function_exists('log_action')) {
        $employee_name_for_log = $_POST['firstName'] . ' ' . $_POST['lastName'];
        log_action('Add Employee', "Added new employee: {$employee_name_for_log} (Employee ID: {$_POST['employeeId']})");
    }
    
    // If both inserts were successful, commit the changes
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Employee added successfully!']);
    
} catch (Exception $e) {
    // If any error occurred, roll back all database changes
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
$conn->close();
?>