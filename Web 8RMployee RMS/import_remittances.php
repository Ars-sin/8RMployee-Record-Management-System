<?php
require_once 'check_auth.php';
require_once 'email_utility.php'; // Include email utility
header('Content-Type: application/json');

$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$month = $_POST['import_month'] ?? null;
$year = $_POST['import_year'] ?? null;
$type = $_POST['import_type'] ?? null;

if (!$month || !$year || !$type) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: month, year, or type']);
    exit();
}

$file_extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
if (strtolower($file_extension) !== 'csv') {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload a .csv file.']);
    exit();
}

try {
    $file = $_FILES['import_file']['tmp_name'];
    $handle = fopen($file, 'r');
    
    if (!$handle) {
        throw new Exception('Could not open file');
    }
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    $rowNum = 0;
    $emails_sent = 0;
    $emails_failed = 0;
    
    // Track remittances per employee for email notifications
    $employee_remittances = [];
    
    // Read and skip header row
    $header = fgetcsv($handle);
    
    if (!$header) {
        throw new Exception('CSV file is empty or invalid');
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
        $rowNum++;
        
        // Skip empty rows
        if (empty(array_filter($data))) continue;
        
        // Only 2 columns needed: Employee ID, Amount
        // Status is always "Paid" since we only record paid remittances
        $employee_id = trim($data[0] ?? '');
        $amount = trim($data[1] ?? '');
        $status = 'Paid';
        
        // Validate required fields
        if (empty($employee_id)) {
            $errorCount++;
            $errors[] = "Row $rowNum: Missing Employee ID";
            continue;
        }
        
        if (empty($amount)) {
            $errorCount++;
            $errors[] = "Row $rowNum: Missing Amount";
            continue;
        }
        
        // Validate numeric employee_id
        if (!is_numeric($employee_id)) {
            $errorCount++;
            $errors[] = "Row $rowNum: Employee ID must be numeric (got: $employee_id)";
            continue;
        }
        
        // Validate numeric amount
        if (!is_numeric($amount)) {
            $errorCount++;
            $errors[] = "Row $rowNum: Amount must be numeric (got: $amount)";
            continue;
        }
        
        // Validate status if provided
        if (!empty($status) && !in_array($status, ['Paid', 'Unpaid'])) {
            $errorCount++;
            $errors[] = "Row $rowNum: Status must be 'Paid' or 'Unpaid' (got: $status)";
            continue;
        }
        
        // Convert to proper types
        $employee_id = intval($employee_id);
        $amount = floatval($amount);
        
        // Validate employee exists
        $check_sql = "SELECT id FROM employee WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if (!$check_stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $check_stmt->bind_param("i", $employee_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $errorCount++;
            $errors[] = "Row $rowNum: Employee ID $employee_id not found in database";
            $check_stmt->close();
            continue;
        }
        $check_stmt->close();
        
        // Insert remittance
        $insert_sql = "INSERT INTO remittances (employee_id, remittance_type, amount, status, remittance_month, remittance_year) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if (!$insert_stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $insert_stmt->bind_param("isdsii", $employee_id, $type, $amount, $status, $month, $year);
        
        if ($insert_stmt->execute()) {
            $successCount++;
            
            // Track remittances for this employee
            if (!isset($employee_remittances[$employee_id])) {
                $employee_remittances[$employee_id] = [];
            }
            $employee_remittances[$employee_id][] = [
                'type' => $type,
                'amount' => $amount
            ];
        } else {
            $errorCount++;
            $errors[] = "Row $rowNum: Database insert error - " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
    
    fclose($handle);
    
    // Commit transaction
    $conn->commit();
    
    // Send email notifications to each employee
    foreach ($employee_remittances as $emp_id => $remittances) {
        $email_sent = send_remittance_notification_html($emp_id, $remittances, $month, $year, $conn);
        
        if ($email_sent) {
            $emails_sent++;
        } else {
            $emails_failed++;
        }
    }
    
    $message = "Import completed: $successCount records added";
    if ($errorCount > 0) {
        $message .= ", $errorCount errors";
    }
    if ($emails_sent > 0) {
        $message .= ". Email notifications sent to $emails_sent employee(s)";
    }
    if ($emails_failed > 0) {
        $message .= " ($emails_failed email(s) failed)";
    }
    
    echo json_encode([
        'success' => $errorCount === 0,
        'message' => $message,
        'successCount' => $successCount,
        'errorCount' => $errorCount,
        'emailsSent' => $emails_sent,
        'emailsFailed' => $emails_failed,
        'errors' => array_slice($errors, 0, 10), // Return first 10 errors
        'totalRowsProcessed' => $rowNum
    ]);
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log("Import error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}

$conn->close();
?>