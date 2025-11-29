<?php
// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database Connection Failed']);
    exit();
}

// --- Get Data from the Frontend ---
$employee_id = $_POST['employee_id'];
$remittance_id = $_POST['remittance_id'];
$amount = $_POST['amount'];
$status = $_POST['status'];
$type = $_POST['type']; // e.g., 'Pag-IBIG', 'SSS'
$month = $_POST['month'];
$year = $_POST['year'];

try {
    // Determine if this is an INSERT or an UPDATE
    $is_new_record = (empty($remittance_id) || $remittance_id == '0');

    if (!$is_new_record) {
        $stmt = $conn->prepare("UPDATE remittances SET amount = ?, status = ? WHERE id = ?");
        $stmt->bind_param("dsi", $amount, $status, $remittance_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO remittances (employee_id, remittance_type, remittance_month, remittance_year, amount, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiids", $employee_id, $type, $month, $year, $amount, $status);
    }

    if ($stmt->execute()) {
        $new_remittance_id = $is_new_record ? $conn->insert_id : $remittance_id;

        // --- LOG THE MODIFICATION ---
        // First, get the employee's name for a better log description
        $name_stmt = $conn->prepare("SELECT name FROM employee WHERE id = ?");
        $name_stmt->bind_param("i", $employee_id);
        $name_stmt->execute();
        $result = $name_stmt->get_result();
        $employee_row = $result->fetch_assoc();
        $employee_name = $employee_row['name'] ?? 'Unknown Employee';
        $name_stmt->close();
        
        $log_user = "Admin";
        $month_name = date("F", mktime(0, 0, 0, $month, 10)); // Get month name
        
        if ($is_new_record) {
            $log_action = "Add Remittance";
            $log_description = "Added {$type} remittance for {$employee_name} for {$month_name} {$year}. Amount: {$amount}, Status: {$status}.";
        } else {
            $log_action = "Update Remittance";
            $log_description = "Updated {$type} remittance for {$employee_name} for {$month_name} {$year}. New Amount: {$amount}, New Status: {$status}.";
        }

        $log_stmt = $conn->prepare("INSERT INTO modification_logs (user_name, action_type, description) VALUES (?, ?, ?)");
        $log_stmt->bind_param("sss", $log_user, $log_action, $log_description);
        $log_stmt->execute();
        $log_stmt->close();
        // --- END LOGGING ---

        echo json_encode(['success' => true, 'message' => 'Remittance saved successfully!', 'new_remittance_id' => $new_remittance_id]);
    } else {
        throw new Exception($stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>