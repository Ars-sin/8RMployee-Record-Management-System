<?php
// Set headers for JSON response
header('Content-Type: application/json');

// This will catch the exact reason for the crash
function handle_exception(Throwable $e) {
    global $conn;
    if ($conn && $conn->ping() && $conn->thread_id) {
        $conn->rollback();
    }
    http_response_code(500); // Internal Server Error
    error_log("Update Employee CRASH: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode(['success' => false, 'message' => 'Server Script Error: ' . $e->getMessage()]);
    exit();
}
set_exception_handler('handle_exception');

// ========================================================================= //
// =================== THIS IS THE ONLY LINE THAT CHANGED ================== //
// ========================================================================= //
require_once('config.php');

// --- Parameter Validation ---
$required_fields = [
    'id', 'address', 'email', 'contact_no', 'position', 'assigned_project', 'daily_rate',
    'emergency_name', 'emergency_contact', 'emergency_address'
];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Field '{$field}' is required and was not received."]);
        exit();
    }
}

// --- Assign variables from POST data safely ---
$id = $_POST['id'];
$address = $_POST['address'];
$email = $_POST['email'];
$contact_no = $_POST['contact_no'];
$position = $_POST['position'];
$assigned_project = $_POST['assigned_project'];
$daily_rate = $_POST['daily_rate'];
$emergency_name = $_POST['emergency_name'];
$emergency_contact = $_POST['emergency_contact'];
$emergency_address = $_POST['emergency_address'];
$sss_no = $_POST['sss_no'] ?? '';
$pagibig_no = $_POST['pagibig_no'] ?? '';
$philhealth_no = $_POST['philhealth_no'] ?? '';

// Start a database transaction
$conn->begin_transaction();

// --- Update the main 'employee' table ---
$stmt_employee = $conn->prepare(
    "UPDATE employee SET 
        address = ?, email = ?, contact_no = ?, position = ?, assigned_project = ?,
        daily_rate = ?, sss_no = ?, pagibig_no = ?, philhealth_no = ?
    WHERE id = ?"
);
if ($stmt_employee === false) { throw new Exception("Failed to prepare statement for 'employee' table. Check column names. MySQL Error: " . $conn->error); }
$stmt_employee->bind_param("sssssdsssi",
    $address, $email, $contact_no, $position, $assigned_project, $daily_rate,
    $sss_no, $pagibig_no, $philhealth_no, $id
);
$stmt_employee->execute();
$stmt_employee->close();

// --- "UPSERT" logic for emergency contacts ---
$stmt_check = $conn->prepare("SELECT id FROM emergency_contacts WHERE employee_id = ?");
if ($stmt_check === false) { throw new Exception("Failed to prepare statement for 'emergency_contacts' check. Check table/column names. MySQL Error: " . $conn->error); }
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$result = $stmt_check->get_result();
$stmt_check->close();

if ($result->num_rows > 0) {
    $stmt_ec = $conn->prepare("UPDATE emergency_contacts SET name=?, address=?, contact_no=? WHERE employee_id=?");
    if ($stmt_ec === false) { throw new Exception("Failed to prepare statement for 'emergency_contacts' UPDATE. Check column names. MySQL Error: " . $conn->error); }
    $stmt_ec->bind_param("sssi", $emergency_name, $emergency_address, $emergency_contact, $id);
} else {
    $stmt_ec = $conn->prepare("INSERT INTO emergency_contacts (employee_id, name, address, contact_no) VALUES (?, ?, ?, ?)");
    if ($stmt_ec === false) { throw new Exception("Failed to prepare statement for 'emergency_contacts' INSERT. Check column names. MySQL Error: " . $conn->error); }
    $stmt_ec->bind_param("isss", $id, $emergency_name, $emergency_address, $emergency_contact);
}
$stmt_ec->execute();
$stmt_ec->close();

// If everything was successful, commit the changes
$conn->commit();

echo json_encode([
    'success' => true, 
    'message' => 'Employee details updated successfully!'
]);

$conn->close();
?>