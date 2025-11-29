<?php
// Use the silent auth check to prevent unwanted output in the CSV file
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

// --- Get Filters from URL ---
$search_term = $_GET['search'] ?? '';
$position_filter = $_GET['position'] ?? '';

// --- Build the WHERE clause based on the provided filters ---
$where_clauses = []; 
$params = [];
$param_types = '';

if (!empty($search_term)) {
    $where_clauses[] = "(first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
    $like_search = "%" . $search_term . "%";
    $params[] = $like_search;
    $params[] = $like_search;
    $params[] = $like_search;
    $param_types .= 'sss';
}

if (!empty($position_filter)) {
    $where_clauses[] = "position = ?";
    $params[] = $position_filter;
    $param_types .= 's';
}

$sql_where = '';
if (!empty($where_clauses)) {
    $sql_where = " WHERE " . implode(" AND ", $where_clauses);
}

// --- Fetch ALL matching employees (no pagination LIMIT) ---
// UPDATED: Added DATE_FORMAT() to the date_hired column
$sql = "SELECT id, first_name, last_name, position, status, DATE_FORMAT(date_hired, '%Y-%m-%d') as date_hired FROM employee" . $sql_where . " ORDER BY last_name ASC, first_name ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// --- Generate and Send the CSV File ---

$filename = "Employee_Report_" . date('Y-m-d') . ".csv";

// Set headers to trigger a file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open the output stream
$output = fopen('php://output', 'w');

// Add the header row to the CSV file
fputcsv($output, ['Employee ID', 'Last Name', 'First Name', 'Position', 'Status', 'Date Hired']);

// Loop through the database results and write each row to the CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['last_name'],
            $row['first_name'],
            $row['position'],
            $row['status'],
            $row['date_hired'] // This is now the correctly formatted date
        ]);
    }
}

$stmt->close();
$conn->close();
exit(); // Ensure no other output is sent
?>