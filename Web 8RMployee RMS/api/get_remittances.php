<?php
require_once('config.php');

header('Content-Type: application/json');

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// 1. --- GET THE FILTER PARAMETERS FROM THE ANDROID APP ---
// These names ('type', 'search', 'month', 'year') must match your Retrofit ApiService interface
$remittanceType = isset($_GET['type']) ? $_GET['type'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// 2. --- BUILD THE SQL QUERY DYNAMICALLY ---
$sql = "SELECT 
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
            r.status,
            r.remittance_type, 
            CONCAT(LPAD(r.remittance_month, 2, '0'), '/', r.remittance_year) AS month_covered, 
            r.amount,
            CASE
                WHEN r.remittance_type = 'Pag-IBIG' THEN e.pagibig_no
                WHEN r.remittance_type = 'SSS' THEN e.sss_no
                WHEN r.remittance_type = 'PhilHealth' THEN e.philhealth_no
                ELSE ''
            END AS remittance_no
        FROM 
            remittances r
        INNER JOIN 
            employee e ON r.employee_id = e.id";

// This array will hold the conditions for our WHERE clause
$whereConditions = [];
$params = [];
$types = '';

// Add remittance type condition
if (!empty($remittanceType)) {
    $whereConditions[] = "r.remittance_type = ?";
    $params[] = $remittanceType;
    $types .= 's';
}

// Add search term condition (searches both first and last name)
if (!empty($searchTerm)) {
    $whereConditions[] = "CONCAT(e.first_name, ' ', e.last_name) LIKE ?";
    $params[] = "%" . $searchTerm . "%";
    $types .= 's';
}

// Add month condition
if (!empty($month)) {
    $whereConditions[] = "r.remittance_month = ?";
    $params[] = $month;
    $types .= 's';
}

// Add year condition
if (!empty($year)) {
    $whereConditions[] = "r.remittance_year = ?";
    $params[] = $year;
    $types .= 's';
}

// If there are any conditions, append them to the SQL query
if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

$sql .= " ORDER BY r.payment_date DESC";

// 3. --- USE A PREPARED STATEMENT FOR SECURITY ---
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'SQL Prepare Failed: ' . $conn->error]);
    $conn->close();
    exit();
}

// Bind the parameters if any exist
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$remittances = array(); 
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $remittances[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($remittances);
?>