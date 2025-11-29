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
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit();
}

// Get all data from the form
$employee_id = $_POST['employee_id'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$address = $_POST['address'];
$contactNo = $_POST['contactNo'];
$position = $_POST['position'];
$assignedProject = $_POST['assignedProject'];
$dailyRate = $_POST['dailyRate'];
$sss_no = $_POST['sss_no'];
$pagibig_no = $_POST['pagibig_no'];
$philhealth_no = $_POST['philhealth_no'];
$emergencyName = $_POST['emergencyName'];
$emergencyAddress = $_POST['emergencyAddress'];
$emergencyContactNo = $_POST['emergencyContactNo'];
$dateHired = $_POST['dateHired'];

$conn->begin_transaction();

try {
    // Update the main 'employee' table - now includes email
    $stmt_employee = $conn->prepare(
        "UPDATE employee SET first_name=?, last_name=?, email=?, address=?, contact_no=?, position=?, assigned_project=?, daily_rate=?, sss_no=?, pagibig_no=?, philhealth_no=?, date_hired=? WHERE id=?"
    );
    
    $stmt_employee->bind_param("sssssssdssssi", 
        $firstName, $lastName, $email, $address, $contactNo, $position, $assignedProject, 
        $dailyRate, $sss_no, $pagibig_no, $philhealth_no, 
        $dateHired,
        $employee_id
    );
    
    if (!$stmt_employee->execute()) { 
        throw new Exception("Error updating employee: " . $stmt_employee->error); 
    }
    $stmt_employee->close();

    // "UPSERT" logic for emergency contacts
    $stmt_check = $conn->prepare("SELECT id FROM emergency_contacts WHERE employee_id = ?");
    $stmt_check->bind_param("i", $employee_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $stmt_check->close();

    if ($result->num_rows > 0) {
        $stmt_ec = $conn->prepare("UPDATE emergency_contacts SET name=?, address=?, contact_no=? WHERE employee_id=?");
        $stmt_ec->bind_param("sssi", $emergencyName, $emergencyAddress, $emergencyContactNo, $employee_id);
    } else {
        $stmt_ec = $conn->prepare("INSERT INTO emergency_contacts (employee_id, name, address, contact_no) VALUES (?, ?, ?, ?)");
        $stmt_ec->bind_param("isss", $employee_id, $emergencyName, $emergencyAddress, $emergencyContactNo);
    }
    
    if (!$stmt_ec->execute()) { 
        throw new Exception("Error updating emergency contact: " . $stmt_ec->error); 
    }
    $stmt_ec->close();

    $conn->commit();

    // Log the action
    log_action('Update Employee', "Updated details for employee: {$firstName} {$lastName} (ID: {$employee_id})");

    // Fetch the current status from database
    $stmt_fetch = $conn->prepare("SELECT status FROM employee WHERE id = ?");
    $stmt_fetch->bind_param("i", $employee_id);
    $stmt_fetch->execute();
    $updated_result = $stmt_fetch->get_result();
    $updated_data_row = $updated_result->fetch_assoc();
    $stmt_fetch->close();

    echo json_encode([
        'success' => true, 
        'message' => 'Employee details updated successfully!',
        'updated_data' => [
            'id' => $employee_id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'position' => $position,
            'status' => $updated_data_row['status']
        ]
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Update Employee Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>