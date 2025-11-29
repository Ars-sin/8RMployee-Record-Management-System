<?php require_once 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changelog</title>
    
     <link rel="icon" type="image/png" href="/logo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        
        /* === UPDATED: Final styles for layout and scrolling === */
        .main-content { 
            flex-grow: 1; 
            padding: 30px 40px; 
            background-color: #ffffff; 
            display: flex; 
            flex-direction: column;
            overflow: hidden; 
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
        
        .page-title {
            font-size: 42px;
            font-weight: 900;
            color: #1a202c;
            margin: 0 0 25px 0;
            padding-bottom: 0;
            border-bottom: none;
            flex-shrink: 0;
        }

        .table-container {
            flex-grow: 1;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        .table-header table, .table-body-scroll table {
            table-layout: fixed; /* The key to perfect alignment */
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-header {
            flex-shrink: 0;
            background-color: #1e3a8a;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .table-header th { 
            padding: 16px 20px; 
            text-align: left;
            font-weight: 600; 
            font-size: 15px; 
            text-transform: uppercase;
            color: white; /* UPDATED: Text color */
        }

        .table-body-scroll {
            overflow-y: auto;
            flex-grow: 1;
        }
        .table-body-scroll::-webkit-scrollbar { width: 8px; }
        .table-body-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .table-body-scroll::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        .table-body-scroll::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        
        .table-body-scroll td { 
            padding: 16px 20px; 
            text-align: left; 
            border-bottom: 1px solid #e2e8f0; 
            vertical-align: middle;
            font-size: 15px; 
            color: #4A5568; 
            word-wrap: break-word;
        }
        .table-body-scroll tbody tr:last-child td { border-bottom: none; }
        
        /* Set column widths */
        th:nth-child(1), td:nth-child(1) { width: 15%; }
        th:nth-child(2), td:nth-child(2) { width: 15%; }
        th:nth-child(3), td:nth-child(3) { width: 20%; }
        th:nth-child(4), td:nth-child(4) { width: 50%; }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.png.png" alt="8 RM Logo">
                <div class="logo-text">
                    <div class="main-name"><span class="logo-8">8</span><span class="logo-rm">R M</span></div>
                    <span class="sub-name">Utility Projects Construction</span>
                </div>
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li><a href="employee.php"><i class="fas fa-users"></i> Employee Information</a></li>
                    <li><a href="government_beneficiary.php"><i class="fas fa-file-invoice"></i> Government Remittances</a></li>
                    <li class="active"><a href="changelog.php"><i class="fas fa-book"></i> Changelog</a></li>
                    <li><a href="employee_archive.php"><i class="fas fa-archive"></i> Employee Archive</a></li>
                    <li><a href="overview.php"><i class="fas fa-chart-bar"></i> Overview</a></li>
                </ul>
                <div class="logout-item">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <h1 class="page-title">CHANGELOG</h1>

            <div class="table-container">
                <div class="table-header">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action Type</th>
                                <th>Timestamp</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-body-scroll">
                    <table>
                        <tbody>
                            <?php
                            // --- Database Connection ---
                            $servername = "localhost";
                            $username = "u987478351_ruth";
                            $password = "Qwertyuiop143!";
                            $dbname = "u987478351_8rm_admin";

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                echo "<tr><td colspan='4'>Connection Failed: " . $conn->connect_error . "</td></tr>";
                            } else {
                                // --- Fetch Logs ---
                                $sql = "SELECT user_name, action_type, description, created_at FROM modification_logs ORDER BY created_at DESC";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["user_name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["action_type"]) . "</td>";
                                        echo "<td>" . date('m/d/Y H:i:s', strtotime($row["created_at"])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No modifications have been recorded yet.</td></tr>";
                                }
                                $conn->close();
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- REMOVED unnecessary JavaScript for column alignment -->
</body>
</html>