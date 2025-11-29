<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (!file_exists('config.php')) {
    die(json_encode(['success' => false, 'message' => 'Config file not found']));
}

require_once 'config.php';

function logMessage($message) {
    $log_file = __DIR__ . '/email_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    
    if (@file_put_contents($log_file, $log_entry, FILE_APPEND) === false) {
        @touch($log_file);
        @chmod($log_file, 0666);
        @file_put_contents($log_file, $log_entry);
    }
    
    error_log($log_entry);
}

logMessage("========================================");
logMessage("=== EMAIL SCRIPT STARTED ===");
logMessage("Request from: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
logMessage("Request method: " . $_SERVER['REQUEST_METHOD']);

$raw_input = file_get_contents('php://input');
logMessage("Raw input received (length: " . strlen($raw_input) . ")");
logMessage("Raw input: " . $raw_input);

$input = json_decode($raw_input, true);

if (!$input) {
    $error = json_last_error_msg();
    logMessage("✗ JSON decode error: " . $error);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input: ' . $error]);
    exit();
}

logMessage("JSON decoded successfully");

$email = $input['email'] ?? '';
$employee_id = $input['employee_id'] ?? 0;
$month_covered = $input['month_covered'] ?? 0;
$year_covered = $input['year_covered'] ?? 0;
$remittances = $input['remittances'] ?? [];

logMessage("Parsed data:");
logMessage("  - Email: " . ($email ?: 'EMPTY'));
logMessage("  - Employee ID: " . $employee_id);
logMessage("  - Month: " . $month_covered);
logMessage("  - Year: " . $year_covered);
logMessage("  - Remittances count: " . count($remittances));

// Validation
if (empty($email)) {
    logMessage("✗ Validation failed: Email is empty");
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    logMessage("✗ Validation failed: Invalid email format - " . $email);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (empty($employee_id)) {
    logMessage("✗ Validation failed: Employee ID is empty");
    echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
    exit();
}

if (empty($remittances) || !is_array($remittances)) {
    logMessage("✗ Validation failed: Remittances is empty or not an array");
    echo json_encode(['success' => false, 'message' => 'Remittances data is required']);
    exit();
}

logMessage("✓ All validations passed");

try {
    logMessage("Connecting to database...");
    
    $stmt = $conn->prepare("SELECT first_name, last_name FROM employee WHERE id = ?");
    if (!$stmt) {
        logMessage("✗ Database prepare failed: " . $conn->error);
        throw new Exception("Database prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("i", $employee_id);
    
    if (!$stmt->execute()) {
        logMessage("✗ Database execute failed: " . $stmt->error);
        throw new Exception("Database execute error: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    
    if (!$employee) {
        logMessage("✗ Employee not found for ID: $employee_id");
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
        exit();
    }
    
    $employee_name = $employee['first_name'] . ' ' . $employee['last_name'];
    logMessage("✓ Employee found: " . $employee_name);
    
    // Convert month number to name
    $month_names = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
    $month_name = $month_names[$month_covered] ?? 'Unknown';
    $period = "$month_name $year_covered";
    
    logMessage("Period: " . $period);
    
    // Build remittance details
    $remittance_rows = '';
    $total_amount = 0;
    
    logMessage("Processing " . count($remittances) . " remittances:");
    
    foreach ($remittances as $index => $remittance) {
        $type = htmlspecialchars($remittance['type'] ?? 'Unknown');
        $amount = (float)($remittance['amount'] ?? 0);
        $amount_formatted = number_format($amount, 2);
        $total_amount += $amount;
        
        logMessage("  Remittance " . ($index + 1) . ": " . $type . " = ₱" . $amount_formatted);
        
        $remittance_rows .= "<tr>
            <td style='padding: 12px; border-bottom: 1px solid #e2e8f0;'>{$type}</td>
            <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;'>₱{$amount_formatted}</td>
        </tr>";
    }
    
    $total_formatted = number_format($total_amount, 2);
    logMessage("Total amount: ₱" . $total_formatted);
    
    // Email subject
    $subject = "Government Remittance Notification - $period";
    
    // Email body (HTML) - UPDATED TO MATCH THE BEAUTIFUL DESIGN
    $html_message = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f5f5f5;'>
    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
        <div style='background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 30px; text-align: center;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>Government Remittance Notification</h1>
        </div>
        <div style='padding: 30px;'>
            <p style='margin: 0 0 20px;'>Dear <strong>{$employee_name}</strong>,</p>
            <p style='margin: 0 0 20px;'>This is to notify you that the following government remittances have been recorded for the period of <strong>{$period}</strong>:</p>
            
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #e2e8f0;'>
                <thead>
                    <tr style='background-color: #f8fafc;'>
                        <th style='padding: 12px; text-align: left; border-bottom: 2px solid #e2e8f0;'>Remittance Type</th>
                        <th style='padding: 12px; text-align: right; border-bottom: 2px solid #e2e8f0;'>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    {$remittance_rows}
                    <tr style='background-color: #f8fafc; font-weight: bold;'>
                        <td style='padding: 12px;'>Total</td>
                        <td style='padding: 12px; text-align: right;'>₱{$total_formatted}</td>
                    </tr>
                </tbody>
            </table>
            
            <div style='background-color: #dcfce7; border-left: 4px solid #22c55e; padding: 12px; margin: 20px 0;'>
                <p style='margin: 0; color: #166534;'><strong>Status:</strong> Paid</p>
            </div>
            
            <p style='margin: 20px 0;'>If you have any questions regarding these remittances, please contact the HR department.</p>
            
            <p style='margin: 20px 0 0;'>Best regards,<br>
            <strong>8 RM Utility Projects Construction</strong><br>
            HR Department</p>
        </div>
        <div style='background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b;'>
            <p style='margin: 0;'>This is an automated notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>";
    
    // Email configuration
    $from_email = "noreply@8rmployee.swuitapp.com";
    $from_name = "HR Department";
    
    // Email headers
    $headers = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/html; charset=UTF-8";
    $headers[] = "From: {$from_name} <{$from_email}>";
    $headers[] = "Reply-To: {$from_email}";
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headers[] = "X-Priority: 3";
    
    $headers_string = implode("\r\n", $headers);
    
    logMessage("========================================");
    logMessage("Sending email:");
    logMessage("  To: " . $email);
    logMessage("  Subject: " . $subject);
    logMessage("  From: " . $from_email);
    logMessage("========================================");
    
    // Check if mail function exists
    if (!function_exists('mail')) {
        logMessage("✗ CRITICAL: mail() function is not available on this server");
        echo json_encode([
            'success' => false,
            'message' => 'Email function is disabled on this server'
        ]);
        exit();
    }
    
    // Send email
    $mail_sent = mail($email, $subject, $html_message, $headers_string);
    
    if ($mail_sent) {
        logMessage("✓✓✓ EMAIL SENT SUCCESSFULLY to: " . $email);
        logMessage("========================================");
        echo json_encode([
            'success' => true,
            'message' => 'Email sent successfully to ' . $email
        ]);
    } else {
        $error = error_get_last();
        $error_message = $error ? $error['message'] : 'mail() returned false';
        
        logMessage("✗✗✗ FAILED TO SEND EMAIL");
        logMessage("Error: " . $error_message);
        logMessage("========================================");
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email: ' . $error_message
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    logMessage("✗✗✗ EXCEPTION OCCURRED");
    logMessage("Exception: " . $e->getMessage());
    logMessage("Stack trace: " . $e->getTraceAsString());
    logMessage("========================================");
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

if (isset($conn)) {
    $conn->close();
}

logMessage("=== EMAIL SCRIPT COMPLETED ===");
logMessage("========================================\n");
?>