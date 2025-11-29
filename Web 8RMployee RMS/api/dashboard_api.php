<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Try to include config
    $configPath = __DIR__ . '/config.php';
    if (!file_exists($configPath)) {
        throw new Exception("Config file not found at: " . $configPath);
    }
    
    require_once $configPath;
    
    // Check if connection exists
    if (!isset($conn)) {
        throw new Exception("Database connection (\$conn) not found in config.php");
    }
    
    // Check if it's mysqli or PDO
    $isMysqli = ($conn instanceof mysqli);
    $isPDO = ($conn instanceof PDO);
    
    if (!$isMysqli && !$isPDO) {
        throw new Exception("Unknown connection type");
    }
    
    if ($isMysqli) {
        // MYSQLI VERSION
        
        // Get active employees count (only from employee table, not archived)
        $activeQuery = "SELECT COUNT(*) as count FROM employee";
        $activeResult = $conn->query($activeQuery);
        if (!$activeResult) {
            throw new Exception("Active query failed: " . $conn->error);
        }
        $activeCount = $activeResult->fetch_assoc()['count'];
        
        // Get archived employees count from employee_archive table
        $archivedQuery = "SELECT COUNT(*) as count FROM employee_archive";
        $archivedResult = $conn->query($archivedQuery);
        if (!$archivedResult) {
            throw new Exception("Archived query failed: " . $conn->error);
        }
        $archivedCount = $archivedResult->fetch_assoc()['count'];
        
        // Get total employees count (active + archived)
        $totalQuery = "SELECT 
            (SELECT COUNT(*) FROM employee) + 
            (SELECT COUNT(*) FROM employee_archive) as count";
        $totalResult = $conn->query($totalQuery);
        if (!$totalResult) {
            throw new Exception("Total query failed: " . $conn->error);
        }
        $totalEmployees = $totalResult->fetch_assoc()['count'];
        
        // Get recent hires (last 30 days, only active employees)
        $recentHiresQuery = "
            SELECT COUNT(*) as count 
            FROM employee 
            WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ";
        $recentHiresResult = $conn->query($recentHiresQuery);
        if (!$recentHiresResult) {
            throw new Exception("Recent hires query failed: " . $conn->error);
        }
        $recentHires = $recentHiresResult->fetch_assoc()['count'];
        
        // Get monthly hiring trend (last 6 months)
        $monthlyQuery = "
            SELECT 
                DATE_FORMAT(date_hired, '%b') as monthName,
                DATE_FORMAT(date_hired, '%Y-%m') as month,
                COUNT(*) as count
            FROM employee
            WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(date_hired, '%Y-%m')
            ORDER BY month ASC
        ";
        $monthlyResult = $conn->query($monthlyQuery);
        if (!$monthlyResult) {
            throw new Exception("Monthly query failed: " . $conn->error);
        }
        
        $monthlyHires = [];
        while ($row = $monthlyResult->fetch_assoc()) {
            $monthlyHires[] = $row;
        }
        
    } else {
        // PDO VERSION (your original code)
        
        // Get active employees count (only from employee table, not archived)
        $activeQuery = "SELECT COUNT(*) as count FROM employee";
        $activeStmt = $conn->query($activeQuery);
        $activeCount = $activeStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get archived employees count from employee_archive table
        $archivedQuery = "SELECT COUNT(*) as count FROM employee_archive";
        $archivedStmt = $conn->query($archivedQuery);
        $archivedCount = $archivedStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get total employees count (active + archived)
        $totalQuery = "SELECT 
            (SELECT COUNT(*) FROM employee) + 
            (SELECT COUNT(*) FROM employee_archive) as count";
        $totalStmt = $conn->query($totalQuery);
        $totalEmployees = $totalStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get recent hires (last 30 days, only active employees)
        $recentHiresQuery = "
            SELECT COUNT(*) as count 
            FROM employee 
            WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ";
        $recentHiresStmt = $conn->query($recentHiresQuery);
        $recentHires = $recentHiresStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get monthly hiring trend (last 6 months)
        $monthlyQuery = "
            SELECT 
                DATE_FORMAT(date_hired, '%b') as monthName,
                DATE_FORMAT(date_hired, '%Y-%m') as month,
                COUNT(*) as count
            FROM employee
            WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(date_hired, '%Y-%m')
            ORDER BY month ASC
        ";
        $monthlyStmt = $conn->query($monthlyQuery);
        $monthlyHires = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Prepare response
    $response = [
        'success' => true,
        'data' => [
            'activeCount' => (int)$activeCount,
            'archivedCount' => (int)$archivedCount,
            'totalEmployees' => (int)$totalEmployees,
            'recentHires' => (int)$recentHires,
            'monthlyHires' => $monthlyHires
        ]
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>