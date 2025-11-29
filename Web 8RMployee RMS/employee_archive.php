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

// --- Pagination and Search Logic ---
$records_per_page = 8;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_clauses = [];
$params = [];
$param_types = '';

if (!empty($search_term)) {
    $where_clauses[] = "(first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
    $search_like = "%" . $search_term . "%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $param_types .= 'sss';
}

$sql_where = '';
if (!empty($where_clauses)) {
    $sql_where = " WHERE " . implode(" AND ", $where_clauses);
}

// Get total number of records for pagination
$total_sql = "SELECT COUNT(id) AS total FROM employee_archive" . $sql_where;
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

// Fetch the records for the current page
$sql = "SELECT id, first_name, last_name, position, status FROM employee_archive" . $sql_where . " ORDER BY last_name ASC, first_name ASC LIMIT ? OFFSET ?";
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
    <title>Employee Archive - 8 RM Utility Projects Construction</title>
    
     <link rel="icon" type="image/png" href="/logo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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
        
        .header-actions { display: flex; align-items: center; gap: 15px; }
        
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
        
       .retrieve-btn { 
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
        
        .retrieve-btn:hover { 
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(71, 85, 105, 0.15);
        }
        
        /* CORRECTED: Enhanced Pagination Styles */
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
        
        .row-fade-out {
            transition: opacity 0.4s ease-out, transform 0.4s ease-out;
            opacity: 0;
            transform: translateX(-20px);
        }

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
        
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            padding: 16px;
            z-index: 3000;
            width: 90%;
            max-width: 420px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .notification.show { opacity: 1; visibility: visible; top: 40px; }
        .notification-bar { width: 6px; background-color: #4CAF50; align-self: stretch; margin-right: 16px; border-radius: 4px; }
        .notification-content { display: flex; align-items: center; flex-grow: 1; }
        .notification-icon { color: #4CAF50; font-size: 24px; margin-right: 12px; }
        .notification-message { font-size: 16px; font-weight: 600; color: #1a202c; }
        .notification-close { background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; padding: 0 8px; margin-left: 16px; transition: color 0.2s ease; }
        .notification-close:hover { color: #ef4444; }
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
        <!-- Sidebar Navigation -->
       <aside class="sidebar">
            <div class="logo"><img src="logo.png.png" alt="8 RM Logo Icon"><div class="logo-text"><div class="main-name"><span class="logo-8">8</span><span class="logo-rm">R M</span></div><span class="sub-name">Utility Projects Construction</span></div></div>
            <nav class="navigation">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li><a href="employee.php"><i class="fas fa-users"></i> Employee Information</a></li>
                    <li><a href="government_beneficiary.php"><i class="fas fa-file-invoice"></i> Government Remittances</a></li>
                    <li><a href="changelog.php"><i class="fas fa-book"></i> Changelog</a></li>
                    <li class="active"><a href="employee_archive.php"><i class="fas fa-archive"></i> Employee Archive</a></li>
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
                <h1>EMPLOYEE ARCHIVE</h1>
                <form method="GET" action="employee_archive.php" class="header-actions">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search Name" value="<?= htmlspecialchars($search_term) ?>">
                    </div>
                </form>
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
                                    echo "<td>" . htmlspecialchars($row["last_name"] . ", " . $row["first_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["position"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                                    echo "<td><button class='retrieve-btn'>RETRIEVE</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding: 40px;'>The employee archive is empty or no results found.</td></tr>";
                            }
                            $stmt->close();
                            $stmt_total->close();
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <nav class="pagination-container">
                    <?php
                        $query_params = [];
                        if (!empty($search_term)) $query_params['search'] = $search_term;
                        
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

    <div id="confirmationModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div id="confirmModalBody" class="confirm-modal-body">Are you sure?</div>
            <div class="confirm-modal-actions">
                <button id="confirmOkBtn">OK</button>
                <button id="confirmCancelBtn">Cancel</button>
            </div>
        </div>
    </div>

    <div id="warningModal" class="warning-modal">
        <div class="warning-modal-content">
            <i class="fas fa-exclamation-triangle warning-icon"></i>
            <div id="warningModalBody" class="warning-modal-body"></div>
            <div class="warning-modal-actions">
                <button id="warningOkBtn">OK</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- CORRECTED: CLIENT-SIDE SEARCH SCRIPT REMOVED ---
        // Search functionality is now correctly handled by the server-side PHP.
        
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
        
        // --- Confirmation Modal Logic ---
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
        
        window.onclick = function(event) {
            if (event.target == confirmationModal) { hideConfirmationModal(); }
            if (event.target == warningModal) { warningModal.style.display = 'none'; }
        }
        
        // --- RETRIEVE EMPLOYEE LOGIC ---
        document.querySelectorAll('.retrieve-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const employeeId = row.dataset.employeeId;
                
                showConfirmationModal('Retrieve employee? Their assignment details will be cleared.', () => {
                    const formData = new FormData();
                    formData.append('employee_id', employeeId);

                    row.classList.add('row-fade-out');

                    fetch('retrieve_employee.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message || 'Employee retrieved successfully.');
                            setTimeout(() => {
                                // Reload page to reflect changes in pagination and remove the row
                                window.location.reload(); 
                            }, 500); 
                        } else {
                            showWarningModal(data.message || 'Failed to retrieve employee.');
                            row.classList.remove('row-fade-out'); // Restore row on failure
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showWarningModal('An error occurred. Please check the console.');
                        row.classList.remove('row-fade-out'); // Restore row on failure
                    });
                });
            });
        });
    });
    </script>
</body>
</html>