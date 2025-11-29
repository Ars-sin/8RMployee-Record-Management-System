<?php
// --- START: CORS HEADERS (For communication with your app) ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
// --- END: CORS HEADERS ---

header('Content-Type: application/json');

// --- Core Includes (Uncomment if needed) ---
// require_once 'check_auth_core.php'; 
// require_once 'logger.php'; 

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Connection Failed: ' . $conn->connect_error]);
    exit();
}

// --- Input Validation ---
$employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
if (!$employee_id) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid Employee ID.']);
    exit();
}

// --- Begin Atomic Transaction ---
$conn->begin_transaction();

try {
    // Step 1: ROBUST METHOD - Copy employee from archive back to the main employee table.
    // We explicitly list columns and, most importantly, set the status back to 'Active'.
    $stmt1 = $conn->prepare(
        "INSERT INTO employee 
            (id, first_name, last_name, email, address, contact_no, birth_date, status, position, date_hired, assigned_project, daily_rate, created_at, sss_no, pagibig_no, philhealth_no) 
         SELECT 
            id, first_name, last_name, email, address, contact_no, birth_date, 'Active', position, date_hired, assigned_project, daily_rate, created_at, sss_no, pagibig_no, philhealth_no
         FROM employee_archive WHERE id = ?"
    );
    $stmt1->bind_param("i", $employee_id);

    if (!$stmt1->execute()) {
        throw new Exception("Failed to copy employee from archive: " . $stmt1->error);
    }
    
    // If no rows were affected, the employee wasn't in the archive.
    if ($stmt1->affected_rows === 0) {
        throw new Exception("Employee with ID {$employee_id} not found in the archive.");
    }
    $stmt1->close();

    // Step 2: SAFER METHOD - Copy remittances from archive back to the main remittances table.
    // IMPORTANT: Make sure the column names in both INSERT and SELECT lists match perfectly.
    // Replace `col1`, `col2`, etc., with your actual column names if they are not identical.
    $stmt2 = $conn->prepare("INSERT INTO remittances SELECT * FROM remittances_archive WHERE employee_id = ?");
    $stmt2->bind_param("i", $employee_id);
    $stmt2->execute();
    $stmt2->close();

    // Step 3: Delete the moved remittances from the archive table.
    $stmt3 = $conn->prepare("DELETE FROM remittances_archive WHERE employee_id = ?");
    $stmt3->bind_param("i", $employee_id);
    $stmt3->execute();
    $stmt3->close();

    // Step 4: Delete the employee from the archive table now that they have been moved.
    $stmt4 = $conn->prepare("DELETE FROM employee_archive WHERE id = ?");
    $stmt4->bind_param("i", $employee_id);
    if (!$stmt4->execute()) {
        throw new Exception("Failed to delete employee from archive table: " . $stmt4->error);
    }
    $stmt4->close();

    // If everything succeeded, commit the changes to the database.
    $conn->commit();
    
    // Optional: Log the successful action
    // log_action('Retrieve Employee', "Retrieved employee with ID: {$employee_id} from archive.");

    echo json_encode(['success' => true, 'message' => 'Employee has been successfully retrieved.']);

} catch (Exception $e) {
    // If any step failed, undo all previous steps.
    $conn->rollback(); 
    
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Retrieve failed: ' . $e->getMessage()]);
}

$conn->close();
?>