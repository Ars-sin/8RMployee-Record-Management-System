<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Employee ID is required']);
    exit();
}

$employeeId = $_GET['id'];

try {
    // Get employee details with emergency contact
    $stmt = $conn->prepare("
        SELECT 
            e.id,
            e.first_name,
            e.last_name, 
            e.address,
            e.email,
            e.contact_no,
            e.birth_date,
            e.status,
            e.position,
            e.date_hired,
            e.assigned_project,
            e.daily_rate,
            e.sss_no,
            e.pagibig_no,
            e.philhealth_no,
            ec.name as emergency_name,
            ec.contact_no as emergency_contact,
            ec.address as emergency_address
        FROM employee e
        LEFT JOIN emergency_contacts ec ON e.id = ec.employee_id
        WHERE e.id = ?
    ");
    
    $stmt->bind_param("s", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode([
            'success' => true, 
            'message' => 'Employee found',
            'employee' => $employee
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Employee not found'
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>