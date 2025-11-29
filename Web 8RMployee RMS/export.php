<?php
// Use the new, silent authentication file that sends no headers.
require_once 'check_auth_export.php'; 

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// --- Determine Report Type ---
$report_type = $_GET['report_type'] ?? 'monthly'; 

// --- Common Variables ---
$filter_year = $_GET['year'] ?? date('Y');
$months_map = [];
for ($m = 1; $m <= 12; $m++) {
    $months_map[$m] = date('F', mktime(0, 0, 0, $m, 1));
}

// ===================================================================
//  LOGIC FOR INDIVIDUAL EMPLOYEE REPORT
// ===================================================================
if ($report_type === 'individual') {
    $search_term = $_GET['search'] ?? '';
    if (empty($search_term)) {
        die("No employee name specified for individual report.");
    }

    // Find the employee ID based on the search term
    $find_id_stmt = $conn->prepare("SELECT id, first_name, last_name FROM employee WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR CONCAT(last_name, ', ', first_name) LIKE ? LIMIT 1");
    $like_search = "%{$search_term}%";
    $find_id_stmt->bind_param("ss", $like_search, $like_search);
    $find_id_stmt->execute();
    $id_result = $find_id_stmt->get_result();
    if ($id_result->num_rows === 0) {
        die("Employee not found for individual report.");
    }
    $employee_info = $id_result->fetch_assoc();
    $employee_id = $employee_info['id'];
    $find_id_stmt->close();
    
    // --- Fetch Remittance Data for that employee ---
    $sql = "
        SELECT 
            r.remittance_month, r.amount, r.remittance_type,
            e.first_name, e.last_name, e.sss_no, e.pagibig_no, e.philhealth_no
        FROM remittances r
        JOIN employee e ON r.employee_id = e.id
        WHERE r.employee_id = ? AND r.remittance_year = ?
        ORDER BY r.remittance_type, r.remittance_month ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $employee_id, $filter_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $remittances = [];
    $employee_name = $employee_info['first_name'] . ' ' . $employee_info['last_name'];
    $employee_ids = []; // To store SSS, Pagibig etc.

    while ($row = $result->fetch_assoc()) {
        $remittances[$row['remittance_type']][] = $row;
        // Store the ID numbers for later use
        if (!isset($employee_ids['SSS'])) $employee_ids['SSS'] = $row['sss_no'];
        if (!isset($employee_ids['Pag-IBIG'])) $employee_ids['Pag-IBIG'] = $row['pagibig_no'];
        if (!isset($employee_ids['PhilHealth'])) $employee_ids['PhilHealth'] = $row['philhealth_no'];
    }
    $stmt->close();
    
    // --- Generate CSV ---
    $filename = "Individual_Report_{$employee_name}_{$filter_year}.csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');

    foreach ($remittances as $type => $records) {
        // Get the correct ID number for the current remittance type
        $id_number = $employee_ids[$type] ?? 'N/A';

        fputcsv($output, ['NAME:', $employee_name]);
        fputcsv($output, [$type]);
        fputcsv($output, ['ID NO:', $id_number]); // ADDED: ID Number row
        fputcsv($output, ['MONTH COVERED', 'YEAR COVERED', 'AMOUNT']);
        
        $total = 0;
        foreach ($records as $record) {
            fputcsv($output, [
                $months_map[$record['remittance_month']],
                $filter_year,
                $record['amount']
            ]);
            $total += $record['amount'];
        }
        
        fputcsv($output, ['', 'TOTAL:', $total]);
        fputcsv($output, []); 
    }

    fclose($output);
    $conn->close();
    exit();
}

// ===================================================================
//  LOGIC FOR MONTHLY REPORT
// ===================================================================
if ($report_type === 'monthly') {
    $filter_month = $_GET['month'] ?? date('n');
    $remittance_type = $_GET['type'] ?? 'Pag-IBIG';

    // --- Fetch Data ---
    $sql = "
        SELECT 
            e.first_name, e.last_name, r.amount
        FROM remittances r
        JOIN employee e ON r.employee_id = e.id
        WHERE r.remittance_month = ? AND r.remittance_year = ? AND r.remittance_type = ?
        ORDER BY e.last_name, e.first_name ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $filter_month, $filter_year, $remittance_type);
    $stmt->execute();
    $result = $stmt->get_result();
    <?php
// Use the new, silent authentication file that sends no headers.
require_once 'check_auth_export.php'; 

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// --- Determine Report Type ---
$report_type = $_GET['report_type'] ?? 'monthly'; 

// --- Common Variables ---
$filter_year = $_GET['year'] ?? date('Y');
$months_map = [];
for ($m = 1; $m <= 12; $m++) {
    $months_map[$m] = date('F', mktime(0, 0, 0, $m, 1));
}

// ===================================================================
//  LOGIC FOR INDIVIDUAL EMPLOYEE REPORT
// ===================================================================
if ($report_type === 'individual') {
    $search_term = $_GET['search'] ?? '';
    if (empty($search_term)) {
        die("No employee name specified for individual report.");
    }

    // Find the employee ID based on the search term
    $find_id_stmt = $conn->prepare("SELECT id, first_name, last_name FROM employee WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR CONCAT(last_name, ', ', first_name) LIKE ? LIMIT 1");
    $like_search = "%{$search_term}%";
    $find_id_stmt->bind_param("ss", $like_search, $like_search);
    $find_id_stmt->execute();
    $id_result = $find_id_stmt->get_result();
    if ($id_result->num_rows === 0) {
        die("Employee not found for individual report.");
    }
    $employee_info = $id_result->fetch_assoc();
    $employee_id = $employee_info['id'];
    $find_id_stmt->close();
    
    // --- Fetch Remittance Data for that employee ---
    $sql = "
        SELECT 
            r.remittance_month, r.amount, r.remittance_type,
            e.first_name, e.last_name, e.sss_no, e.pagibig_no, e.philhealth_no
        FROM remittances r
        JOIN employee e ON r.employee_id = e.id
        WHERE r.employee_id = ? AND r.remittance_year = ?
        ORDER BY r.remittance_type, r.remittance_month ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $employee_id, $filter_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $remittances = [];
    $employee_name = $employee_info['first_name'] . ' ' . $employee_info['last_name'];
    $employee_ids = []; // To store SSS, Pagibig etc.

    while ($row = $result->fetch_assoc()) {
        $remittances[$row['remittance_type']][] = $row;
        // Store the ID numbers for later use
        if (!isset($employee_ids['SSS'])) $employee_ids['SSS'] = $row['sss_no'];
        if (!isset($employee_ids['Pag-IBIG'])) $employee_ids['Pag-IBIG'] = $row['pagibig_no'];
        if (!isset($employee_ids['PhilHealth'])) $employee_ids['PhilHealth'] = $row['philhealth_no'];
    }
    $stmt->close();
    
    // --- Generate CSV ---
    $filename = "Individual_Report_{$employee_name}_{$filter_year}.csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');

    foreach ($remittances as $type => $records) {
        // Get the correct ID number for the current remittance type
        $id_number = $employee_ids[$type] ?? 'N/A';

        fputcsv($output, ['NAME:', $employee_name]);
        fputcsv($output, [$type]);
        fputcsv($output, ['ID NO:', $id_number]); // ADDED: ID Number row
        fputcsv($output, ['MONTH COVERED', 'YEAR COVERED', 'AMOUNT']);
        
        $total = 0;
        foreach ($records as $record) {
            fputcsv($output, [
                $months_map[$record['remittance_month']],
                $filter_year,
                $record['amount']
            ]);
            $total += $record['amount'];
        }
        
        fputcsv($output, ['', 'TOTAL:', $total]);
        fputcsv($output, []); 
    }

    fclose($output);
    $conn->close();
    exit();
}

// ===================================================================
//  LOGIC FOR MONTHLY REPORT
// ===================================================================
if ($report_type === 'monthly') {
    $filter_month = $_GET['month'] ?? date('n');
    $remittance_type = $_GET['type'] ?? 'Pag-IBIG';

    // --- Fetch Data ---
    $sql = "
        SELECT 
            e.first_name, e.last_name, r.amount
        FROM remittances r
        JOIN employee e ON r.employee_id = e.id
        WHERE r.remittance_month = ? AND r.remittance_year = ? AND r.remittance_type = ?
        ORDER BY e.last_name, e.first_name ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $filter_month, $filter_year, $remittance_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // --- Generate CSV ---
    $month_name = $months_map[$filter_month];
    $filename = "Monthly_Report_{$remittance_type}_{$month_name}_{$filter_year}.csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [$remittance_type]);
    fputcsv($output, ['MONTH', $month_name]);
    fputcsv($output, ['LAST NAME', 'FIRST NAME', 'AMOUNT']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['last_name'], $row['first_name'], $row['amount']]);
    }

    $stmt->close();
    fclose($output);
    $conn->close();
    exit();
}

die("Invalid report type specified.");
?>
    // --- Generate CSV ---
    $month_name = $months_map[$filter_month];
    $filename = "Monthly_Report_{$remittance_type}_{$month_name}_{$filter_year}.csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, [$remittance_type]);
    fputcsv($output, ['MONTH', $month_name]);
    fputcsv($output, ['LAST NAME', 'FIRST NAME', 'AMOUNT']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['last_name'], $row['first_name'], $row['amount']]);
    }

    $stmt->close();
    fclose($output);
    $conn->close();
    exit();
}

die("Invalid report type specified.");
?>