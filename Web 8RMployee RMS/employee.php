<?php
require_once 'check_auth.php';

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// --- Hardcoded list of positions ---
$positions_list = [
    "Engineer", "Technician", "Project In-Charge", "Project Engineer", "Office Manager",
    "Liaison Officer", "Manager", "Project Manager", "Document Controller", "Supervisor",
    "Safety Officer", "Foreman", "Lead-Man Technician"
];

// --- Pagination, Search, and Filter Logic ---
$records_per_page = 8;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$position_filter = isset($_GET['position']) ? trim($_GET['position']) : '';

$where_clauses = []; 
$params = [];
$param_types = '';

if (!empty($search_term)) {
    // Search now checks first name, last name, or a combination
    $where_clauses[] = "(first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
    $search_like = "%" . $search_term . "%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
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

$total_sql = "SELECT COUNT(id) AS total FROM employee" . $sql_where;
$stmt_total = $conn->prepare($total_sql);
if (!empty($params)) {
    $stmt_total->bind_param($param_types, ...$params);
}
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT id, first_name, last_name, position, status FROM employee" . $sql_where . " ORDER BY last_name ASC, first_name ASC LIMIT ? OFFSET ?";
$current_param_types = $param_types . 'ii';
$current_params = $params;
$current_params[] = $records_per_page;
$current_params[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param($current_param_types, ...$current_params);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information</title>
    <link rel="icon" type="image/png" href="/logo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Modern Reset and Base Styles */
        
        
        body { 
            margin: 0; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            color: #1a202c; 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            line-height: 1.6;
        }
        
        .dashboard-container { 
            display: flex; 
            width: 100%; 
            height: 100vh; 
            overflow: hidden; 
        }
        
       /* Sidebar Improvements */
        .sidebar { 
            width: 280px; 
            flex-shrink: 0; 
            background: linear-gradient(180deg, #ffffff 0%, #fefcf5 100%);
            padding: 24px; 
            display: flex; 
            flex-direction: column; 
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.05);
            
        }

        .logo { 
            display: flex; 
            align-items: center; 
            margin-bottom: 48px; 
            padding: 0 12px; 
        }
        .logo img { 
            width: 44px; 
            height: 44px; 
            margin-right: 12px; 
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .logo-text { display: flex; flex-direction: column; }
        .logo-text .main-name { 
            font-size: 26px; 
            font-weight: 800; 
            display: flex; 
            align-items: center; 
            letter-spacing: -0.5px;
        }
        .logo-8 { color: #22c55e; }
        .logo-rm { color: #1e3a8a; margin-left: 0.2rem; }
        .sub-name { 
            font-size: 11px; 
            color: #f59e0b; 
            font-weight: 600; 
            margin-top: 2px;
            letter-spacing: 0.3px;
        }
        
        .navigation {
            flex-grow: 1; 
            display: flex;
            flex-direction: column;
        }
        .navigation ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .navigation li a {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            font-size: 15px;
        }
        .navigation li a i { 
            margin-right: 16px; 
            font-size: 18px; 
            width: 20px; 
            text-align: center; 
        }
        .navigation li a:hover { 
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .navigation li.active > a { 
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white; 
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3);
            
        }

        .logout-item {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            width: 280px;
        }
        .logout-item a {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            text-decoration: none;
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #c53030;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
        }
        .logout-item a:hover {
            background: linear-gradient(135deg, #feb2b2 0%, #fc8181 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(197, 48, 48, 0.2);
        }
        
        /* Modernized Main Content */
        .main-content { 
            flex-grow: 1; 
            padding: 32px 48px; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            background: transparent;
        }
        
        .main-content header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid rgba(226, 232, 240, 0.3);
        }
        
        .main-content h1 { 
            margin: 0; 
            font-size: 48px; 
            font-weight: 900; 
            color: #1a202c;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Enhanced Header Actions */
        .header-actions { 
            display: flex; 
            align-items: center; 
            gap: 16px; 
        }
        
       /* Employee Search Bar Animation (copied from remittances) */
        .search-bar { 
            position: relative; 
            display: flex; 
            align-items: center; 
        }

        .search-bar i { 
            position: absolute; 
            left: 16px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #a0aec0; 
            font-size: 16px;
        }

        .search-bar input { 
            width: 240px; 
            padding: 0px 16px 0px 44px; 
            border: 2px solid #e2e8f0; 
            border-radius: 12px; 
            font-size: 14px; 
            background: rgba(255, 255, 255, 0.9); 
            height: 44px; 
            line-height: 1; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .search-bar input:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.15);
            transform: translateY(-1px);
        }

        
        .header-icon-group { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }
        
        .icon-btn { 
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(226, 232, 240, 0.6);
            cursor: pointer; 
            font-size: 18px; 
            color: #64748b; 
            padding: 0; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 16px; 
            width: 48px; 
            height: 48px; 
            box-sizing: border-box; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }
        
        .user-btn { 
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            color: white;
            border-color: transparent;
        }
        
        .user-btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.25);
        }
        
        .icon-btn:hover { 
            color: #1e3a8a;
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
        }
        
        .btn-export {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            text-decoration: none;
            border-color: transparent;
        }
        
        .btn-export:hover {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 55, 72, 0.25);
        }
        
        /* Enhanced Filter Dropdown */
        .filter-dropdown { 
            position: relative; 
            display: flex; 
            align-items: center; 
        }
        
        .filter-options-content { 
            display: none; 
            position: absolute; 
            right: 0; 
            top: 56px; 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            min-width: 240px; 
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            z-index: 10; 
            border-radius: 16px; 
            border: 1px solid rgba(226, 232, 240, 0.6);
            overflow: hidden; 
            max-height: 320px; 
            overflow-y: auto; 
        }
        
        .filter-options-content a { 
            color: #374151; 
            padding: 16px 20px; 
            text-decoration: none; 
            display: block; 
            font-size: 15px; 
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .filter-options-content a:hover { 
            background: rgba(59, 130, 246, 0.08);
            color: #1e3a8a;
        }
        
        .filter-options-content a.active { 
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            color: white; 
            font-weight: 600;
        }
        
        .filter-dropdown .show { 
            display: block; 
        }
        
        /* Enhanced Table Styles */
        .table-wrapper { 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 24px; 
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }
        
        .table-container { 
            flex-grow: 1; 
            overflow-y: auto; 
        }
        
        .table-container table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
        }
        
        .table-container thead th { 
            background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
            color: #1a202c; 
            font-weight: 700; 
            font-size: 14px; 
            text-transform: uppercase; 
            position: sticky; 
            top: 0; 
            z-index: 1;
            letter-spacing: 0.5px;
        }
        
        th, td { 
            padding: 20px 24px; 
            text-align: left; 
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            word-wrap: break-word; 
        }
        
        td { 
            font-size: 15px; 
            color: #374151;
            font-weight: 500;
        }
        
        th:nth-child(1), td:nth-child(1) { width: 10%; }
        th:nth-child(2), td:nth-child(2) { width: 30%; }
        th:nth-child(3), td:nth-child(3) { width: 25%; }
        th:nth-child(4), td:nth-child(4) { width: 15%; }
        th:nth-child(5), td:nth-child(5) { width: 20%; }
        
        tbody tr:last-child td { 
            border-bottom: none; 
        }
        
        tbody tr { 
            transition: all 0.2s ease;
        }
        
        tbody tr:hover { 
            background: rgba(59, 130, 246, 0.04);
            transform: translateY(-1px);
        }
        
        .employee-name-link { 
            color: #1e3a8a; 
            text-decoration: none; 
            cursor: pointer; 
            font-weight: 600;
            transition: color 0.2s ease;
        }
        
        .employee-name-link:hover { 
            color: #3730a3;
            text-decoration: underline; 
        }
        
        .archive-btn { 
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #475569; 
            border: 1px solid rgba(203, 213, 225, 0.6);
            padding: 8px 16px; 
            border-radius: 10px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s ease;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .archive-btn:hover { 
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(71, 85, 105, 0.15);
        }
        
        /* Enhanced Pagination */
        .pagination-container { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 24px 0; 
            flex-shrink: 0; 
            border-top: 1px solid rgba(226, 232, 240, 0.5);
            background: rgba(248, 250, 252, 0.5);
        }
        
        .pagination-container a, .pagination-container span { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-decoration: none; 
            color: #475569; 
            border: 2px solid rgba(226, 232, 240, 0.6);
            background: rgba(255, 255, 255, 0.9);
            margin: 0 4px; 
            border-radius: 12px; 
            min-width: 40px; 
            height: 40px; 
            font-size: 14px; 
            font-weight: 600;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
        }
        
        .pagination-container a:hover { 
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        }
        
        .pagination-container a.active { 
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            color: white; 
            border-color: transparent;
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3);
        }
        
        .pagination-container span.disabled { 
            color: #cbd5e1; 
            border-color: #f1f5f9; 
            pointer-events: none;
            background: #f8fafc;
        }
        
        .pagination-container span.ellipsis { 
            border: none; 
            background: none; 
            padding: 0 8px; 
            color: #94a3b8;
        }
        
        /* Enhanced Modals */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
        }
        
        .modal-content { 
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            margin: 2% auto; 
            padding: 0; 
            border: none;
            width: 90%; 
            max-width: 900px; 
            border-radius: 24px; 
            position: relative; 
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            animation: modalSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; 
            flex-direction: column; 
            max-height: 90vh; 
        }
        
        @keyframes modalSlideIn { 
            from { 
                opacity: 0; 
                transform: translateY(-30px) scale(0.95); 
            } 
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            } 
        }
        
        .close-btn { 
            color: #94a3b8; 
            position: absolute; 
            top: 20px; 
            right: 28px; 
            font-size: 32px; 
            font-weight: bold; 
            cursor: pointer;
            transition: color 0.2s ease;
            z-index: 10; 
        }
        
        .close-btn:hover, .close-btn:focus { 
            color: #ef4444; 
        }
        
        /* === NEW STYLES FOR STICKY HEADER/FOOTER === */
        .modal-form {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            overflow: hidden;
        }

        .modal-header {
            flex-shrink: 0;
            padding: 48px 48px 32px 48px;
        }

        .modal-header h2 { 
            margin: 0; 
            color: #1a202c; 
            text-align: center; 
            font-weight: 800;
            font-size: 28px;
            letter-spacing: -0.5px;
        }
        
        .modal-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0 36px 0 48px;
        }

        .modal-body::-webkit-scrollbar {
          width: 8px;
        }
        .modal-body::-webkit-scrollbar-track {
          background: #f1f5f9;
          border-radius: 10px;
        }
        .modal-body::-webkit-scrollbar-thumb {
          background: #cbd5e1;
          border-radius: 10px;
        }
        .modal-body::-webkit-scrollbar-thumb:hover {
          background: #94a3b8;
        }

        .modal-footer {
            flex-shrink: 0;
            padding: 32px 48px 48px 48px;
            display: flex;
            justify-content: flex-end;
        }
        /* ========================================= */
        
        /* Enhanced Form Styles */
        .form-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 24px 32px; 
        }
        
        .form-field { 
            display: flex; 
            flex-direction: column; 
        }
        
        .form-field label { 
            font-weight: 600; 
            color: #374151; 
            margin-bottom: 8px; 
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-field label.required::after { 
            content: " *"; 
            color: #ef4444; 
            font-weight: bold; 
        }
        
        .form-field input, .form-field select { 
            width: 100%; 
            padding: 14px 16px; 
            border: 2px solid rgba(226, 232, 240, 0.6);
            border-radius: 12px; 
            box-sizing: border-box; 
            font-size: 15px; 
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .form-field input:focus, .form-field select:focus { 
            outline: none; 
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .form-field select:disabled { 
            background: #f1f5f9;
            cursor: not-allowed; 
            color: #94a3b8;
        }
        
        .contact-group { 
            display: flex; 
            align-items: center; 
        }
        
        .contact-group .country-code { 
            padding: 10px 16px; 
            background: #f1f5f9;
            border: 2px solid rgba(226, 232, 240, 0.6);
            border-right: none; 
            border-radius: 12px 0 0 12px;
            font-weight: 600;
            color: #475569;
        }
        
        .contact-group input { 
            border-radius: 0 12px 12px 0; 
        }
        
        .emergency-contact { 
            grid-column: 1 / -1; 
            margin-top: 32px; 
            padding-top: 32px; 
            border-top: 2px solid rgba(226, 232, 240, 0.3); 
        }
        
        .emergency-contact h3 { 
            margin-top: 0; 
            margin-bottom: 24px; 
            font-size: 20px; 
            text-align: center; 
            font-weight: 700; 
            color: #1a202c;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .emergency-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 24px 32px; 
        }
        
        .modal-content button[type="submit"] { 
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            color: white; 
            padding: 16px 24px; 
            border: none; 
            border-radius: 16px; 
            cursor: pointer; 
            font-size: 16px; 
            margin: 0;
            transition: all 0.3s ease;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .modal-content button[type="submit"]:hover { 
            background: linear-gradient(135deg, #3730a3 0%, #4338ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.25);
        }
        
        /* Enhanced Notifications */
        .notification { 
            position: fixed; 
            top: 20px; 
            left: 50%; 
            transform: translateX(-50%); 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 16px; 
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            display: flex; 
            align-items: center; 
            padding: 16px; 
            z-index: 2000; 
            width: 90%; 
            max-width: 420px; 
            opacity: 0; 
            visibility: hidden; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .notification.show { 
            opacity: 1; 
            visibility: visible; 
            top: 40px; 
        }
        
        .notification-bar { 
            width: 6px; 
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            align-self: stretch; 
            margin-right: 16px; 
            border-radius: 4px; 
        }
        
        .notification-content { 
            display: flex; 
            align-items: center; 
            flex-grow: 1; 
        }
        
        .notification-icon { 
            color: #22c55e; 
            font-size: 24px; 
            margin-right: 12px; 
        }
        
        .notification-message { 
            font-size: 16px;
            font-weight: 600; 
            color: #1a202c; 
        }
        
        .notification-close { 
            background: none; 
            border: none; 
            font-size: 20px; 
            color: #94a3b8; 
            cursor: pointer; 
            padding: 0 8px; 
            margin-left: 16px;
            transition: color 0.2s ease;
        }
        
        .notification-close:hover { 
            color: #ef4444; 
        }
        
        /* Enhanced Confirmation Modals */
        .confirm-modal, .warning-modal { 
            display: none; 
            position: fixed; 
            z-index: 2000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px); 
        }
        
        .confirm-modal-content, .warning-modal-content { 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: #fff; 
            padding: 40px; 
            border-radius: 20px; 
            width: 90%; 
            max-width: 480px; 
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .warning-modal-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .confirm-modal-body, .warning-modal-body { 
            font-size: 18px; 
            margin-top: 16px; 
            margin-bottom: 32px;
            font-weight: 500;
            line-height: 1.6;
        }
        
        .confirm-modal-actions, .warning-modal-actions { 
            display: flex; 
            justify-content: center;
            gap: 16px; 
        }
        
        .confirm-modal-actions button, .warning-modal-actions button { 
            border: none; 
            border-radius: 16px; 
            padding: 12px 32px; 
            font-size: 16px; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .confirm-modal-actions button:hover, .warning-modal-actions button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        #confirmOkBtn { 
            background: linear-gradient(135deg, #e879f9 0%, #d946ef 100%);
            color: white;
        }
        
        #confirmCancelBtn { 
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white; 
        }
        
        .warning-icon { 
            font-size: 56px; 
            color: #facc15; 
            margin-bottom: 24px; 
        }
        
        .warning-modal-actions button { 
            background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
            color: #1a202c; 
        }
        
        /* Animation for row removal */
        .row-fade-out { 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0; 
            transform: translateX(-20px) scale(0.95); 
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                padding: 24px 32px;
            }
            
            .search-bar input {
                width: 240px;
            }
            
            .main-content h1 {
                font-size: 40px;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                padding: 16px;
            }
            
            .main-content {
                padding: 16px;
            }
            
            .main-content header {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .search-bar input {
                width: 200px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .emergency-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                padding: 24px;
                margin: 5% auto;
            }
        }
    </style>
</head>
<body>
    <div id="custom-notification" class="notification">
        <div class="notification-bar"></div>
        <div class="notification-content">
            <i class="fas fa-check-circle notification-icon"></i>
            <span class="notification-message"></span>
        </div>
        <button class="notification-close">&times;</button>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo"><img src="logo.png.png" alt="8 RM Logo Icon"><div class="logo-text"><div class="main-name"><span class="logo-8">8</span><span class="logo-rm">R M</span></div><span class="sub-name">Utility Projects Construction</span></div></div>
            <nav class="navigation">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li class="active"><a href="employee.php"><i class="fas fa-users"></i><span>Employee Information</span></a></li>
                    <li><a href="government_beneficiary.php"><i class="fas fa-file-invoice"></i><span>Government Remittances</span></a></li>
                    <li><a href="changelog.php"><i class="fas fa-book"></i><span>Changelog</span></a></li>
                    <li><a href="employee_archive.php"><i class="fas fa-archive"></i><span>Employee Archive</span></a></li>
                    <li><a href="overview.php"><i class="fas fa-chart-bar"></i><span>Overview</span></a></li>
                </ul>
                <div class="logout-item">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>EMPLOYEE</h1> 
                <div class="header-actions">
                    <form method="GET" action="employee.php" class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search Name" value="<?= htmlspecialchars($search_term) ?>">
                        <?php if (!empty($position_filter)): ?>
                            <input type="hidden" name="position" value="<?= htmlspecialchars($position_filter) ?>">
                        <?php endif; ?>
                    </form>
                    
                    <div class="header-icon-group">
                        <div class="filter-dropdown">
                            <i class="fas fa-filter icon-btn" id="filterBtn" title="Filter by Position"></i>
                            <div id="filterOptions" class="filter-options-content">
                                <?php
                                    $link_params = [];
                                    if (!empty($search_term)) $link_params['search'] = $search_term;
                                ?>
                                <a href="?<?= http_build_query($link_params) ?>" class="<?= empty($position_filter) ? 'active' : '' ?>">All Positions</a>
                                <?php foreach ($positions_list as $pos): ?>
                                    <?php $link_params['position'] = $pos; ?>
                                    <a href="?<?= http_build_query($link_params) ?>" class="<?= ($position_filter == $pos) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($pos) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- EXPORT BUTTON -->
                        <a href="export_employees.php?<?php echo http_build_query(['search' => $search_term, 'position' => $position_filter]); ?>" class="icon-btn btn-export" title="Export to Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>

                        <button type="button" id="addUserBtn" class="icon-btn user-btn" title="Add Employee">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                </div>
            </header>
            
            <div class="table-wrapper">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr data-employee-id='" . $row['id'] . "'>";
                                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td><a href='#' class='employee-name-link'>" . htmlspecialchars($row["last_name"] . ", " . $row["first_name"]) . "</a></td>";
                                    echo "<td>" . htmlspecialchars($row["position"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                                    echo "<td><button class='archive-btn'>ARCHIVE</button></td>";
                                    echo "</tr>";
                                }
                            } else { 
                                echo "<tr><td colspan='5' style='text-align:center; padding: 40px;'>No employees found.</td></tr>"; 
                            }
                            $stmt->close();
                            $stmt_total->close();
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav class="pagination-container">
                    <?php
                        $query_params = [];
                        if (!empty($search_term)) $query_params['search'] = $search_term;
                        if (!empty($position_filter)) $query_params['position'] = $position_filter;

                        // First & Previous Page
                        if ($current_page > 1) {
                            $query_params['page'] = 1;
                            echo '<a href="?' . http_build_query($query_params) . '">&laquo;</a>';
                            $query_params['page'] = $current_page - 1;
                            echo '<a href="?' . http_build_query($query_params) . '">&lt;</a>';
                        } else {
                            echo '<span class="disabled">&laquo;</span>';
                            echo '<span class="disabled">&lt;</span>';
                        }
                        
                        // Numeric Pages
                        $links_to_show = 2;
                        $start = max(1, $current_page - $links_to_show);
                        $end = min($total_pages, $current_page + $links_to_show);

                        if ($start > 1) {
                            $query_params['page'] = 1;
                            echo '<a href="?' . http_build_query($query_params) . '">1</a>';
                            if ($start > 2) {
                                echo '<span class="ellipsis">...</span>';
                            }
                        }

                        for ($i = $start; $i <= $end; $i++) {
                            $query_params['page'] = $i;
                            $active_class = ($i == $current_page) ? 'active' : '';
                            echo '<a href="?' . http_build_query($query_params) . '" class="' . $active_class . '">' . $i . '</a>';
                        }

                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) {
                                echo '<span class="ellipsis">...</span>';
                            }
                            $query_params['page'] = $total_pages;
                            echo '<a href="?' . http_build_query($query_params) . '">' . $total_pages . '</a>';
                        }

                        // Next & Last Page
                        if ($current_page < $total_pages) {
                            $query_params['page'] = $current_page + 1;
                            echo '<a href="?' . http_build_query($query_params) . '">&gt;</a>';
                            $query_params['page'] = $total_pages;
                            echo '<a href="?' . http_build_query($query_params) . '">&raquo;</a>';
                        } else {
                            echo '<span class="disabled">&gt;</span>';
                            echo '<span class="disabled">&raquo;</span>';
                        }
                    ?>
                </nav>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <!-- ADD EMPLOYEE MODAL -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <form id="addEmployeeForm" action="employee_add.php" method="POST" class="modal-form">
                <div class="modal-header">
                    <h2>EMPLOYEE INFORMATION</h2>
                </div>
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-field"><label for="firstName" class="required">First Name:</label><input type="text" id="firstName" name="firstName" required></div>
                        <div class="form-field"><label for="lastName" class="required">Last Name:</label><input type="text" id="lastName" name="lastName" required></div>
                        <div class="form-field"><label for="employee_id" class="required">Employee ID:</label><input type="text" id="employee_id" name="employee_id" required></div>
                        <div class="form-field"><label for="email" class="required">Email:</label><input type="email" id="email" name="email" required></div>
                        <div class="form-field"><label for="address" class="required">Address:</label><input type="text" id="address" name="address" required></div>
                        <div class="form-field"><label for="contactNo" class="required">Contact No.:</label><div class="contact-group"><span class="country-code">+63</span><input type="tel" id="contactNo" name="contactNo" pattern="[0-9]{10}" required></div></div>
                        <div class="form-field"><label for="birthDate" class="required">Birth Date:</label><input type="date" id="birthDate" name="birthDate" required></div>
                        <div class="form-field">
                            <label for="status" class="required">Status:</label>
                            <input type="text" id="status" name="status" value="Active" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #94a3b8; font-weight: 500;">
                        </div>
                        <div class="form-field">
                            <label for="position" class="required">Position:</label>
                            <select id="position" name="position" required>
                                <option value="">-- Select Position --</option>
                                <?php foreach ($positions_list as $pos): ?>
                                    <option value="<?= htmlspecialchars($pos) ?>"><?= htmlspecialchars($pos) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-field"><label for="dateHired" class="required">Date Hired:</label><input type="date" id="dateHired" name="dateHired" required></div>
                        <div class="form-field"><label for="assignedProject" class="required">Assigned Project:</label><input type="text" id="assignedProject" name="assignedProject" required></div>
                        <div class="form-field" style="grid-column: 1 / -1;"><label for="dailyRate" class="required">Daily Rate:</label><input type="number" id="dailyRate" name="dailyRate" step="0.01" required></div>
                    </div>
                    <div class="emergency-contact">
                        <h3>GOVERNMENT REMITTANCE NUMBERS</h3>
                        <div class="emergency-grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                            <div class="form-field"><label for="sss_no">SSS No.:</label><input type="text" id="sss_no" name="sss_no" ></div>
                            <div class="form-field"><label for="pagibig_no">Pag-IBIG No.:</label><input type="text" id="pagibig_no" name="pagibig_no" ></div>
                            <div class="form-field"><label for="philhealth_no">PhilHealth No.:</label><input type="text" id="philhealth_no" name="philhealth_no" ></div>
                        </div>
                    </div>
                    <div class="emergency-contact">
                        <h3>EMERGENCY CONTACT INFORMATION</h3>
                        <div class="emergency-grid" style="grid-template-columns: 1fr 1fr;">
                            <div class="form-field" style="grid-column: 1 / -1;"><label for="emergencyName" class="required">Name:</label><input type="text" id="emergencyName" name="emergencyName" required></div>
                            <div class="form-field"><label for="emergencyAddress" class="required">Address:</label><input type="text" id="emergencyAddress" name="emergencyAddress" required></div>
                            <div class="form-field"><label for="emergencyContactNo" class="required">Contact No.:</label><input type="tel" id="emergencyContactNo" name="emergencyContactNo" required></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- EDIT EMPLOYEE MODAL -->
    <div id="editEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <form id="editEmployeeForm" method="POST" class="modal-form">
                <input type="hidden" id="edit_employee_id" name="employee_id">
                <div class="modal-header">
                    <h2>EDIT EMPLOYEE INFORMATION</h2>
                </div>
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-field"><label for="edit_firstName" class="required">First Name:</label><input type="text" id="edit_firstName" name="firstName" required></div>
                        <div class="form-field"><label for="edit_lastName" class="required">Last Name:</label><input type="text" id="edit_lastName" name="lastName" required></div>
                        <div class="form-field"><label for="edit_email" class="required">Email:</label><input type="email" id="edit_email" name="email" required></div>
                        <div class="form-field"><label for="edit_address" class="required">Address:</label><input type="text" id="edit_address" name="address" required></div>
                        <div class="form-field"><label for="edit_contactNo" class="required">Contact No.:</label><div class="contact-group"><span class="country-code">+63</span><input type="tel" id="edit_contactNo" name="contactNo" pattern="[0-9]{10}" placeholder="9123456789" required></div></div>
                       <div class="form-field">
                            <label for="edit_birthDate" class="required">Birth Date:</label>
                            <input type="date" id="edit_birthDate" name="birthDate" required>
                        </div>
                        <div class="form-field">
                            <label for="edit_status">Status:</label>
                            <select id="edit_status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="On-Leave">On-Leave</option>
                                <option value="Terminated">Terminated</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="edit_position" class="required">Position:</label>
                            <select id="edit_position" name="position" required>
                                <option value="">-- Select Position --</option>
                                <?php foreach ($positions_list as $pos): ?>
                                    <option value="<?= htmlspecialchars($pos) ?>"><?= htmlspecialchars($pos) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-field">
                            <label for="edit_dateHired">Date Hired:</label>
                            <input type="date" id="edit_dateHired" name="dateHired">
                        </div>

                        <div class="form-field"><label for="edit_assignedProject" class="required">Assigned Project:</label><input type="text" id="edit_assignedProject" name="assignedProject" required></div>
                        <div class="form-field" style="grid-column: 1 / -1;"><label for="edit_dailyRate" class="required">Daily Rate:</label><input type="number" id="edit_dailyRate" name="dailyRate" step="0.01" required></div>
                    </div>
                    <div class="emergency-contact">
                        <h3>GOVERNMENT REMITTANCE NUMBERS</h3>
                        <div class="emergency-grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                            <div class="form-field"><label for="edit_sss_no">SSS No.:</label><input type="text" id="edit_sss_no" name="sss_no" ></div>
                            <div class="form-field"><label for="edit_pagibig_no">Pag-IBIG No.:</label><input type="text" id="edit_pagibig_no" name="pagibig_no" ></div>
                            <div class="form-field"><label for="edit_philhealth_no">PhilHealth No.:</label><input type="text" id="edit_philhealth_no" name="philhealth_no" ></div>
                        </div>
                    </div>
                    <div class="emergency-contact">
                        <h3>EMERGENCY CONTACT INFORMATION</h3>
                        <div class="emergency-grid" style="grid-template-columns: 1fr 1fr;">
                            <div class="form-field" style="grid-column: 1 / -1;"><label for="edit_emergencyName" class="required">Name:</label><input type="text" id="edit_emergencyName" name="emergencyName" required></div>
                            <div class="form-field"><label for="edit_emergencyAddress" class="required">Address:</label><input type="text" id="edit_emergencyAddress" name="emergencyAddress" required></div>
                            <div class="form-field"><label for="edit_emergencyContactNo" class="required">Contact No.:</label><input type="tel" id="edit_emergencyContactNo" name="emergencyContactNo" required></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit">Update Employee</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="confirmationModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div id="confirmModalBody" class="confirm-modal-body">Are you sure?</div>
            <div class="confirm-modal-actions">
                <button id="confirmOkBtn">OK</button>
                <button id="confirmCancelBtn">Cancel</button>
            </div>
        </div>
    </div>

    <!-- WARNING MODAL -->
    <div id="warningModal" class="warning-modal">
        <div class="warning-modal-content">
            <i class="fas fa-exclamation-triangle warning-icon"></i>
            <div id="warningModalBody" class="warning-modal-body"></div>
            <div class="warning-modal-actions">
                <button id="warningOkBtn">OK</button>
            </div>
        </div>
    </div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Filter Dropdown Logic ---
        const filterBtn = document.getElementById('filterBtn');
        const filterOptions = document.getElementById('filterOptions');
        if (filterBtn && filterOptions) {
            filterBtn.addEventListener('click', function(event) { 
                event.stopPropagation(); 
                filterOptions.classList.toggle('show'); 
            });
            window.addEventListener('click', function(event) { 
                if (!filterBtn.contains(event.target) && !filterOptions.contains(event.target) && filterOptions.classList.contains('show')) { 
                    filterOptions.classList.remove('show'); 
                } 
            });
        }
        
        // --- Notification Logic ---
        const notification = document.getElementById('custom-notification');
        const notificationMessage = notification.querySelector('.notification-message');
        const notificationClose = notification.querySelector('.notification-close');
        let notificationTimeout;
        function showNotification(message) { 
            clearTimeout(notificationTimeout); 
            notificationMessage.textContent = message; 
            notification.classList.add('show'); 
            notificationTimeout = setTimeout(() => { 
                notification.classList.remove('show'); 
            }, 4000); 
        }
        notificationClose.onclick = function() { 
            clearTimeout(notificationTimeout); 
            notification.classList.remove('show'); 
        }

        // --- Custom Confirmation Modal Logic ---
        const confirmationModal = document.getElementById('confirmationModal');
        const confirmModalBody = document.getElementById('confirmModalBody');
        const confirmOkBtn = document.getElementById('confirmOkBtn');
        const confirmCancelBtn = document.getElementById('confirmCancelBtn');
        let actionCallback = null;
        function showConfirmationModal(message, callback) { 
            confirmModalBody.textContent = message; 
            actionCallback = callback; 
            confirmationModal.style.display = 'block'; 
        }
        function hideConfirmationModal() { 
            actionCallback = null; 
            confirmationModal.style.display = 'none'; 
        }
        confirmCancelBtn.onclick = hideConfirmationModal;
        confirmOkBtn.onclick = function() { 
            if (actionCallback) { 
                actionCallback(); 
            } 
            hideConfirmationModal(); 
        };

        // --- Warning Modal Logic ---
        const warningModal = document.getElementById('warningModal');
        const warningModalBody = document.getElementById('warningModalBody');
        const warningOkBtn = document.getElementById('warningOkBtn');
        function showWarningModal(message) { 
            warningModalBody.textContent = message; 
            warningModal.style.display = 'block'; 
        }
        warningOkBtn.onclick = function() { 
            warningModal.style.display = 'none'; 
        };

        // --- ADD EMPLOYEE MODAL LOGIC ---
        const addModal = document.getElementById('addEmployeeModal');
        const addUserBtn = document.getElementById('addUserBtn');
        const addCloseBtn = addModal.querySelector('.close-btn');
        const addEmployeeForm = document.getElementById('addEmployeeForm');
        addUserBtn.onclick = function() { 
            addModal.style.display = "block"; 
        }
        addCloseBtn.onclick = function() { 
            addModal.style.display = "none"; 
        }
        addEmployeeForm.addEventListener('submit', function(event) { 
            event.preventDefault(); 
            const formData = new FormData(this);
            fetch('employee_add.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => { 
                if (d.success) { 
                    addModal.style.display = "none";
                    showNotification(d.message || "Successfully Added");
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else { 
                    showWarningModal(d.message || "An error occurred."); 
                } 
            }).catch(e => {
                console.error(e);
                showWarningModal('A network or server error occurred.');
            }); 
        });

        // --- EDIT MODAL & ARCHIVE LOGIC ---
        const editModal = document.getElementById('editEmployeeModal');
        const editCloseBtn = editModal.querySelector('.close-btn');
        const editEmployeeForm = document.getElementById('editEmployeeForm');
        const tableBody = document.querySelector('.table-container tbody');

        editCloseBtn.onclick = function() { 
            editModal.style.display = "none"; 
        }
        
        if (tableBody) {
            tableBody.addEventListener('click', function(event) {
                const row = event.target.closest('tr');
                if (!row) return;

                const employeeId = row.dataset.employeeId;
                if (!employeeId) return;

                // ARCHIVE BUTTON LOGIC
                if (event.target.classList.contains('archive-btn')) {
                    showConfirmationModal('Are you sure you want to archive this employee?', () => {
                        const formData = new FormData();
                        formData.append('employee_id', employeeId);
                        
                        // Optimistic UI update
                        row.classList.add('row-fade-out');

                        fetch('archive_employee.php', { method: 'POST', body: formData })
                            .then(r => r.json())
                            .then(data => { 
                                if (data.success) { 
                                    showNotification(data.message || 'Employee archived successfully.'); 
                                    setTimeout(() => {
                                        // Reload to reflect pagination changes and remove the row
                                        window.location.reload(); 
                                    }, 500); 
                                } else { 
                                    showWarningModal(data.message || "Failed to archive."); 
                                    row.classList.remove('row-fade-out'); // Revert on failure
                                } 
                            })
                            .catch(e => { 
                                console.error('Archive Fetch Error:', e); 
                                showWarningModal('An error occurred during archiving.');
                                row.classList.remove('row-fade-out'); // Revert on failure
                            });
                    });
                }

                // EDIT MODAL LOGIC (when name is clicked)
                if (event.target.classList.contains('employee-name-link')) {
                    event.preventDefault();
                    fetch(`get_employee_details.php?id=${employeeId}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            const data = result.data;
                            document.getElementById('edit_employee_id').value = data.id;
                            document.getElementById('edit_lastName').value = data.last_name;
                            document.getElementById('edit_firstName').value = data.first_name;
                            document.getElementById('edit_email').value = data.email || '';
                            document.getElementById('edit_address').value = data.address;
                            
                            // Contact number handling
                            const contactInput = document.getElementById('edit_contactNo');
                            let contactNumber = data.contact_no || '';
                            
                            // Check if the number starts with "+63" and remove it
                            if (contactNumber.startsWith('+63')) {
                                contactInput.value = contactNumber.substring(3);
                            } else {
                                contactInput.value = contactNumber;
                            }

                            document.getElementById('edit_status').value = data.status;
                            document.getElementById('edit_position').value = data.position;
                            document.getElementById('edit_dateHired').value = data.date_hired;
                            document.getElementById('edit_assignedProject').value = data.assigned_project;
                            document.getElementById('edit_dailyRate').value = data.daily_rate;
                            document.getElementById('edit_emergencyName').value = data.emergencyName;
                            document.getElementById('edit_emergencyAddress').value = data.emergencyAddress;
                            document.getElementById('edit_emergencyContactNo').value = data.emergencyContactNo;
                            document.getElementById('edit_birthDate').value = data.birth_date;
                            document.getElementById('edit_sss_no').value = data.sss_no;
                            document.getElementById('edit_pagibig_no').value = data.pagibig_no;
                            document.getElementById('edit_philhealth_no').value = data.philhealth_no;
                            
                            editModal.style.display = 'block';
                        } else { 
                            showWarningModal(result.message || "Could not fetch details."); 
                        }
                    }).catch(e => {
                        console.error('Fetch error:', e);
                        showWarningModal('A critical error occurred while fetching employee details.');
                    });
                }
            });
        }
        
        // FORM SUBMISSION FOR EDITING
        editEmployeeForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('update_employee.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editModal.style.display = "none";
                    showNotification(data.message || "Update Successful");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else { 
                    showWarningModal(data.message || "Update failed."); 
                }
            }).catch(e => {
                console.error('Update error:', e);
                showWarningModal('A network or server error occurred during update.');
            });
        });
        
        // General click handler to close modals
        window.onclick = function(event) {
            if (event.target == addModal) { addModal.style.display = "none"; }
            if (event.target == editModal) { editModal.style.display = "none"; }
            if (event.target == confirmationModal) { hideConfirmationModal(); }
            if (event.target == warningModal) { warningModal.style.display = 'none'; }
        }
    });
</script>


</body>
</html>
                                        