<?php
// --- START: CRUCIAL DEBUGGING LINES ---
// These lines will force PHP to show any fatal errors instead of returning a blank page.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END: CRUCIAL DEBUGGING LINES ---

header('Content-Type: application/json');
require_once('config.php');

// Check for logger.php but don't fail if it's missing
if (file_exists('logger.php')) {
    require_once('logger.php');
}

// Get the raw JSON POST data from the app
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if the received data is valid JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid JSON format sent. Error: ' . json_last_error_msg()]);
    exit();
}

// --- Detailed Input Validation ---
if (empty($data['employee_id'])) {
    http_response_code(400); echo json_encode(['success' => false, 'message' => 'Employee ID is missing.']); exit();
}
if (empty($data['month_covered'])) {
    http_response_code(400); echo json_encode(['success' => false, 'message' => 'Month Covered is missing.']); exit();
}
if (empty($data['year_covered'])) {
    http_response_code(400); echo json_encode(['success' => false, 'message' => 'Year Covered is missing.']); exit();
}
if (empty($data['remittances']) || !is_array($data['remittances'])) {
    http_response_code(400); echo json_encode(['success' => false, 'message' => 'Remittances list is missing or invalid.']); exit();
}

$employee_id = $data['employee_id'];
$month = $data['month_covered'];
$year = $data['year_covered'];
$remittances_array = $data['remittances'];

// This variable is kept for the log message formatting
$month_covered_date_for_log = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';

$conn->begin_transaction();

try {
    $total_amount = 0;
    $count = 0;
    $employee_name = '';

    // Fetch employee name and validate that the employee exists
    $name_stmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) as name FROM employee WHERE id = ?");
    $name_stmt->bind_param("i", $employee_id);
    $name_stmt->execute();
    $name_result = $name_stmt->get_result();
    if($name_row = $name_result->fetch_assoc()) {
        $employee_name = $name_row['name'];
    } else {
        throw new Exception("Employee with ID {$employee_id} does not exist.");
    }
    $name_stmt->close();

    // --- START: CORRECTED INSERT STATEMENT ---
    // The columns `created_at` and `month_covered` have been replaced with the correct ones
    // from your database: `payment_date`, `remittance_month`, and `remittance_year`.
    $stmt = $conn->prepare(
        "INSERT INTO remittances (employee_id, remittance_type, amount, status, remittance_month, remittance_year, payment_date) 
         VALUES (?, ?, ?, 'Paid', ?, ?, NOW())"
    );
    // --- END: CORRECTED INSERT STATEMENT ---

    foreach ($remittances_array as $remittance) {
        if (empty($remittance['type']) || !isset($remittance['amount']) || !is_numeric($remittance['amount'])) {
            throw new Exception('Invalid remittance entry. Type and Amount are required and amount must be a number.');
        }
        
        $type = $remittance['type'];
        $amount = (float) $remittance['amount'];

        // --- START: CORRECTED BIND PARAMETERS ---
        // The types "isdii" and variables now match the 5 question marks (?) in the query above.
        $stmt->bind_param("isdii", $employee_id, $type, $amount, $month, $year);
        // --- END: CORRECTED BIND PARAMETERS ---

        if (!$stmt->execute()) {
            throw new Exception('Database Insert Error: ' . $stmt->error);
        }
        
        $total_amount += $amount;
        $count++;
    }
    $stmt->close();
    
    // Log the action if the logger function exists
    if (function_exists('log_action')) {
        $period = date('F Y', strtotime($month_covered_date_for_log));
        $log_description = "Added {$count} remittance(s) for {$employee_name} for the period of {$period}, totaling " . number_format($total_amount, 2);
        log_action('Add Remittance', $log_description);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Remittances added successfully!']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>