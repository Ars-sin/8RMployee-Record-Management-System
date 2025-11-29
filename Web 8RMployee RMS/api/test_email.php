<?php
// Test script to verify email functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Email System Test</h1>";
echo "<pre>";

// Test 1: Check if mail function exists
echo "\n1. Testing mail() function availability...\n";
if (function_exists('mail')) {
    echo "   ✓ mail() function is available\n";
} else {
    echo "   ✗ FAILED: mail() function is NOT available\n";
    echo "   Your server doesn't support PHP mail()\n";
}

// Test 2: Check config file
echo "\n2. Testing config.php...\n";
if (file_exists('config.php')) {
    echo "   ✓ config.php exists\n";
    require_once 'config.php';
    
    if (isset($conn) && $conn->ping()) {
        echo "   ✓ Database connection works\n";
    } else {
        echo "   ✗ Database connection failed\n";
    }
} else {
    echo "   ✗ config.php NOT found\n";
}

// Test 3: Check send_remittance_email.php
echo "\n3. Testing send_remittance_email.php...\n";
if (file_exists('send_remittance_email.php')) {
    echo "   ✓ send_remittance_email.php exists\n";
} else {
    echo "   ✗ send_remittance_email.php NOT found\n";
}

// Test 4: Check log file writability
echo "\n4. Testing log file creation...\n";
$log_file = __DIR__ . '/email_log.txt';
if (file_put_contents($log_file, "Test entry at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND)) {
    echo "   ✓ Can write to email_log.txt\n";
    if (file_exists($log_file)) {
        echo "   ✓ email_log.txt exists\n";
        $size = filesize($log_file);
        echo "   File size: $size bytes\n";
    }
} else {
    echo "   ✗ Cannot write to email_log.txt\n";
    echo "   Check file permissions\n";
}

// Test 5: Get sample employee data
echo "\n5. Testing employee data retrieval...\n";
if (isset($conn)) {
    $result = $conn->query("SELECT id, first_name, last_name, email FROM employee LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $emp = $result->fetch_assoc();
        echo "   ✓ Found sample employee:\n";
        echo "     ID: " . $emp['id'] . "\n";
        echo "     Name: " . $emp['first_name'] . " " . $emp['last_name'] . "\n";
        echo "     Email: " . $emp['email'] . "\n";
        
        $sample_employee_id = $emp['id'];
        $sample_email = $emp['email'];
    } else {
        echo "   ✗ No employees found in database\n";
    }
}

// Test 6: Try to send a test email
echo "\n6. Testing actual email send...\n";
if (isset($sample_email) && !empty($sample_email)) {
    $to = $sample_email;
    $subject = "Test Email from Remittance System";
    $message = "This is a test email. If you receive this, the email system is working!";
    $headers = "From: noreply@8rmployee.swuitapp.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    echo "   Attempting to send to: $to\n";
    
    $result = @mail($to, $subject, $message, $headers);
    
    if ($result) {
        echo "   ✓ mail() returned TRUE (email sent to system)\n";
        echo "   Note: This doesn't guarantee delivery - check spam folder\n";
    } else {
        echo "   ✗ mail() returned FALSE (email failed)\n";
        $error = error_get_last();
        if ($error) {
            echo "   Error: " . print_r($error, true) . "\n";
        }
    }
} else {
    echo "   ⚠ Skipping - no valid email address found\n";
}

// Test 7: Check server configuration
echo "\n7. Server Email Configuration:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";
echo "   Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "   sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "   SMTP: " . ini_get('SMTP') . "\n";
echo "   smtp_port: " . ini_get('smtp_port') . "\n";

// Test 8: Check if email_log.txt has content
echo "\n8. Recent Email Log Entries:\n";
if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $lines = explode("\n", $log_content);
    $recent_lines = array_slice($lines, -10); // Last 10 lines
    
    if (!empty(trim(implode('', $recent_lines)))) {
        foreach ($recent_lines as $line) {
            if (!empty(trim($line))) {
                echo "   " . $line . "\n";
            }
        }
    } else {
        echo "   (Log file is empty)\n";
    }
} else {
    echo "   (No log file found)\n";
}

echo "\n========================================\n";
echo "TEST COMPLETED\n";
echo "</pre>";

// Test 9: Simulate API call
echo "<h2>Simulate API Call Test</h2>";
echo "<pre>";

if (isset($sample_employee_id) && isset($sample_email)) {
    echo "9. Simulating API call to send_remittance_email.php...\n\n";
    
    $test_data = [
        'email' => $sample_email,
        'employee_id' => $sample_employee_id,
        'month_covered' => 10,
        'year_covered' => 2025,
        'remittances' => [
            ['type' => 'SSS', 'amount' => '1000.00'],
            ['type' => 'PhilHealth', 'amount' => '500.00']
        ]
    ];
    
    echo "Test Data:\n";
    echo json_encode($test_data, JSON_PRETTY_PRINT) . "\n\n";
    
    // Simulate the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/send_remittance_email.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    echo "Response Code: $http_code\n";
    if ($curl_error) {
        echo "cURL Error: $curl_error\n";
    }
    echo "Response:\n";
    echo $response . "\n";
    
    // Try to decode response
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo "\nDecoded Response:\n";
        print_r($decoded);
    }
}

echo "</pre>";
?>