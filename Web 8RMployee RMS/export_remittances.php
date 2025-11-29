<?php
require_once 'check_auth.php'; // Ensure only authenticated users can export

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// --- Get and Validate Filters ---
$valid_types = ['Pag-IBIG', 'SSS', 'PhilHealth'];
$active_type = $_GET['type'] ?? 'Pag-IBIG';
if (!in_array($active_type, $valid_types)) {
    $active_type = 'Pag-IBIG';
}

$filter_month = $_GET['month'] ?? date('n');
$filter_year = $_GET['year'] ?? date('Y');

// Determine the correct ID column based on the active type
if ($active_type == 'Pag-IBIG') {
    $id_column_name = 'pagibig_no';
} else {
    $id_column_name = str_replace('-', '_', strtolower($active_type)) . '_no';
}

// --- Fetch Data from Database ---
// This query is identical to the one on the main page to ensure consistency.
$sql = "SELECT 
            CONCAT(e.last_name, ', ', e.first_name) as name, 
            e.{$id_column_name} as id_no, 
            r.status, 
            r.amount 
        FROM remittances r 
        JOIN employee e ON r.employee_id = e.id 
        WHERE r.remittance_type = ? AND r.remittance_month = ? AND r.remittance_year = ? 
        ORDER BY e.last_name ASC, e.first_name ASC, r.id ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("sii", $active_type, $filter_month, $filter_year);
$stmt->execute();
$result = $stmt->get_result();

// --- Generate and Send CSV File ---

// Create a dynamic filename, e.g., "Remittances_Pag-IBIG_2025-09.csv"
$month_name = date('F', mktime(0, 0, 0, $filter_month, 1));
$filename = sprintf("Remittances_%s_%s_%d.csv", $active_type, $month_name, $filter_year);

// Set headers to force browser download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open the output stream
$output = fopen('php://output', 'w');

// Add the header row to the CSV file
fputcsv($output, ['Name', 'ID Number', 'Status', 'Amount', 'Month Covered', 'Year Covered']);

// Loop through the database results and write each row to the CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $csv_row = [
            $row['name'],
            $row['id_no'],
            $row['status'],
            $row['amount'],
            $month_name,
            $filter_year
        ];
        fputcsv($output, $csv_row);
    }
}

$stmt->close();
$conn->close();
exit(); // Important to stop any other output
?>