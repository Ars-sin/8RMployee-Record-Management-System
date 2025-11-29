<?php
// --- START: CORS HEADERS (Fixes "Network Error") ---
// Allows your mobile app to access this script.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// The app might send an OPTIONS request first (a "preflight" check).
// If it does, we just acknowledge it and exit.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
// --- END: CORS HEADERS ---


// Set the content type of the response to JSON.
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
    // If connection fails, send a server error status and a JSON response.
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database Connection Failed: ' . $conn->connect_error]);
    exit();
}

// --- Input Validation ---
// Ensure the employee_id is a valid integer from the POST request.
$employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
if (!$employee_id) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'A valid Employee ID is required.']);
    exit();
}

// --- Begin Atomic Transaction ---
// This guarantees that all steps succeed or none of them are saved.
$conn->begin_transaction();

try {
    // Step 1: Copy the employee to the archive table and explicitly set their status to 'Archived'.
    // Listing columns explicitly is safer than `SELECT *` and prevents errors if table structures differ.
    $stmt1 = $conn->prepare(
        "INSERT INTO employee_archive 
            (id, first_name, last_name, address, email, contact_no, birth_date, status, position, date_hired, assigned_project, daily_rate, created_at, sss_no, pagibig_no, philhealth_no) 
         SELECT 
            id, first_name, last_name, address, email, contact_no, birth_date, 'Archived', position, date_hired, assigned_project, daily_rate, created_at, sss_no, pagibig_no, philhealth_no
         FROM employee WHERE id = ?"
    );
    $stmt1->bind_param("i", $employee_id);
    
    if (!$stmt1->execute()) {
        // If the query fails, throw an exception to trigger the rollback.
        throw new Exception("Failed to copy employee to archive: " . $stmt1->error);
    }
    
    // If no rows were affected, it means the employee didn't exist.
    if ($stmt1->affected_rows === 0) {
        throw new Exception("Employee with ID {$employee_id} not found.");
    }
    $stmt1->close();

    // Step 2: Copy all related remittance records to the remittance archive table.
    // IMPORTANT: Make sure the column names and order in `remittances_archive` and `remittances` match.
    // If they don't, you must list the columns explicitly like in Step 1.
    $stmt2 = $conn->prepare("INSERT INTO remittances_archive SELECT * FROM remittances WHERE employee_id = ?");
    $stmt2->bind_param("i", $employee_id);
    $stmt2->execute(); // It's okay if an employee has no remittances, so we don't need to check for errors here.
    $stmt2->close();

    // Step 3: Delete the original remittance records from the active table.
    $stmt3 = $conn->prepare("DELETE FROM remittances WHERE employee_id = ?");
    $stmt3->bind_param("i", $employee_id);
    $stmt3->execute();
    $stmt3->close();

    // Step 4: Finally, delete the employee from the main active employee table.
    $stmt4 = $conn->prepare("DELETE FROM employee WHERE id = ?");
    $stmt4->bind_param("i", $employee_id);
    if (!$stmt4->execute()) {
        throw new Exception("Failed to delete employee from main table: " . $stmt4->error);
    }
    $stmt4->close();

    // If all previous steps succeeded without throwing an exception, commit the transaction.
    $conn->commit();
    
    // Optional: Log the successful action.
    // log_action('Archive Employee', "Archived employee and remittances for ID: {$employee_id}");

    echo json_encode(['success' => true, 'message' => 'Employee and all related records have been successfully archived.']);

} catch (Exception $e) {
    // If any step in the 'try' block failed, undo all database changes from this transaction.
    $conn->rollback(); 
    
    // Send a server error status and report the specific failure message.
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Archive operation failed: ' . $e->getMessage()]);
}

$conn->close();
?>