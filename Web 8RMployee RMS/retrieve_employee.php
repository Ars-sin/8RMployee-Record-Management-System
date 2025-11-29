<?php
header('Content-Type: application/json');
require_once 'check_auth_core.php'; 
require_once 'logger.php';

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
if (!$employee_id) {
    echo json_encode(['success' => false, 'message' => 'Error: Employee ID was not provided.']);
    exit();
}

$conn->begin_transaction();

try {
    // Step 1: Find the employee in the archive table
    $stmt_find = $conn->prepare("SELECT * FROM employee_archive WHERE id = ?");
    $stmt_find->bind_param("i", $employee_id);
    $stmt_find->execute();
    $result = $stmt_find->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Employee not found in the archive.");
    }
    
    $employee_data = $result->fetch_assoc();
    $stmt_find->close();
    
    // Step 2: Re-insert the employee into the main 'employee' table with email
    $stmt_insert = $conn->prepare(
        "INSERT INTO employee (id, first_name, last_name, email, address, contact_no, birth_date, status, position, date_hired, assigned_project, daily_rate, sss_no, pagibig_no, philhealth_no) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    // Set default values for fields that should be reset
    $status = 'Active';
    $position = null;
    $date_hired = null;
    $assigned_project = null;
    $daily_rate = null;
    
    // Handle email - use archived email or null if not present
    $email = isset($employee_data['email']) ? $employee_data['email'] : null;
    
    $stmt_insert->bind_param(
        "issssssssssdsss",
        $employee_data['id'],
        $employee_data['first_name'],
        $employee_data['last_name'],
        $email,  // Now properly includes email
        $employee_data['address'],
        $employee_data['contact_no'],
        $employee_data['birth_date'],
        $status,
        $position,
        $date_hired,
        $assigned_project,
        $daily_rate,
        $employee_data['sss_no'],
        $employee_data['pagibig_no'],   
        $employee_data['philhealth_no']
    );
    
    if (!$stmt_insert->execute()) {
        throw new Exception("Failed to re-insert employee: " . $stmt_insert->error);
    }
    $stmt_insert->close();
    
    // Step 3: Copy remittance records back from the archive
    $stmt_copy_rem = $conn->prepare("INSERT INTO remittances SELECT * FROM remittances_archive WHERE employee_id = ?");
    $stmt_copy_rem->bind_param("i", $employee_id);
    $stmt_copy_rem->execute();
    $stmt_copy_rem->close();
    
    // Step 4: Delete the remittance records from the archive
    $stmt_del_rem = $conn->prepare("DELETE FROM remittances_archive WHERE employee_id = ?");
    $stmt_del_rem->bind_param("i", $employee_id);
    $stmt_del_rem->execute();
    $stmt_del_rem->close();
    
    // Step 5: Delete the employee from the 'employee_archive' table
    $stmt_delete = $conn->prepare("DELETE FROM employee_archive WHERE id = ?");
    $stmt_delete->bind_param("i", $employee_id);
    
    if (!$stmt_delete->execute()) {
        throw new Exception("Failed to delete employee from the archive.");
    }
    $stmt_delete->close();
    
    $conn->commit();
    
    // Log the action with employee name
    log_action('Retrieve Employee', "Retrieved employee: {$employee_data['first_name']} {$employee_data['last_name']} (ID: {$employee_id})");
    
    echo json_encode([
        'success' => true, 
        'message' => "Employee {$employee_data['first_name']} {$employee_data['last_name']} has been successfully retrieved from archive."
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Retrieve Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred during retrieval: ' . $e->getMessage()
    ]);
}

$conn->close();
?>