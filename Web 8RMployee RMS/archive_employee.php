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
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
if (!$employee_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
    exit();
}

$conn->begin_transaction();

try {
    // Step 1: Fetch employee data from the main employee table
    $stmt_fetch = $conn->prepare("SELECT * FROM employee WHERE id = ?");
    $stmt_fetch->bind_param("i", $employee_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Employee not found");
    }
    
    $employee_data = $result->fetch_assoc();
    $stmt_fetch->close();
    
    // Step 2: Insert into employee_archive with email field
    $stmt_archive = $conn->prepare(
        "INSERT INTO employee_archive (id, first_name, last_name, email, address, contact_no, birth_date, status, position, date_hired, assigned_project, daily_rate, sss_no, pagibig_no, philhealth_no, created_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    $stmt_archive->bind_param(
        "isssssssssdsssss",
        $employee_data['id'],
        $employee_data['first_name'],
        $employee_data['last_name'],
        $employee_data['email'],
        $employee_data['address'],
        $employee_data['contact_no'],
        $employee_data['birth_date'],
        $employee_data['status'],
        $employee_data['position'],
        $employee_data['date_hired'],
        $employee_data['assigned_project'],
        $employee_data['daily_rate'],
        $employee_data['sss_no'],
        $employee_data['pagibig_no'],
        $employee_data['philhealth_no'],
        $employee_data['created_at']
    );
    
    if (!$stmt_archive->execute()) {
        throw new Exception("Failed to archive employee: " . $stmt_archive->error);
    }
    $stmt_archive->close();
    
    // Step 3: Move remittances to remittances_archive
    $stmt_move_rem = $conn->prepare("INSERT INTO remittances_archive SELECT * FROM remittances WHERE employee_id = ?");
    $stmt_move_rem->bind_param("i", $employee_id);
    $stmt_move_rem->execute();
    $stmt_move_rem->close();
    
    // Step 4: Delete remittances from main table
    $stmt_del_rem = $conn->prepare("DELETE FROM remittances WHERE employee_id = ?");
    $stmt_del_rem->bind_param("i", $employee_id);
    $stmt_del_rem->execute();
    $stmt_del_rem->close();
    
    // Step 5: Delete emergency contacts
    $stmt_del_ec = $conn->prepare("DELETE FROM emergency_contacts WHERE employee_id = ?");
    $stmt_del_ec->bind_param("i", $employee_id);
    $stmt_del_ec->execute();
    $stmt_del_ec->close();
    
    // Step 6: Delete employee from main table
    $stmt_delete = $conn->prepare("DELETE FROM employee WHERE id = ?");
    $stmt_delete->bind_param("i", $employee_id);
    
    if (!$stmt_delete->execute()) {
        throw new Exception("Failed to delete employee from main table");
    }
    $stmt_delete->close();
    
    $conn->commit();
    
    log_action('Archive Employee', "Archived employee: {$employee_data['first_name']} {$employee_data['last_name']} (ID: {$employee_id})");
    
    echo json_encode(['success' => true, 'message' => 'Employee archived successfully']);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Archive Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>