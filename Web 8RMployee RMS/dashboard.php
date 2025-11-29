<?php
// Session and authentication check
session_start();

// Security headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if user is logged in - FIXED to match login_process.php
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: index.php?expired=1');
    exit();
}

// Initialize variables with default values
$active_count = 0;
$archived_count = 0;
$total_employees = 0;
$recent_hires = 0;
$monthly_hires = [];

try {
    // --- Database Connection ---
    $servername = "localhost";
    $username = "u987478351_ruth";
    $password = "Qwertyuiop143!";
    $dbname = "u987478351_8rm_admin";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset to prevent SQL injection
    $conn->set_charset("utf8mb4");

    // --- Fetch Statistics ---
    // Count active employees from 'employee' table
    $active_query = "SELECT COUNT(*) as total FROM employee";
    $active_result = $conn->query($active_query);
    if ($active_result && $row = $active_result->fetch_assoc()) {
        $active_count = (int)$row['total'];
    }

    // Count archived employees from 'employee_archive' table
    $archived_query = "SELECT COUNT(*) as total FROM employee_archive";
    $archived_result = $conn->query($archived_query);
    if ($archived_result && $row = $archived_result->fetch_assoc()) {
        $archived_count = (int)$row['total'];
    }

    // Total employees (active + archived)
    $total_employees = $active_count + $archived_count;

    // Get recent hires from active employees (last 30 days)
    $recent_query = "SELECT COUNT(*) as total FROM employee WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $recent_result = $conn->query($recent_query);
    if ($recent_result && $row = $recent_result->fetch_assoc()) {
        $recent_hires = (int)$row['total'];
    }

    // Monthly hiring trend (last 6 months) from employee table
    $monthly_query = "SELECT DATE_FORMAT(date_hired, '%b') as month_name, DATE_FORMAT(date_hired, '%Y-%m') as month, COUNT(*) as count FROM employee WHERE date_hired >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(date_hired, '%Y-%m'), DATE_FORMAT(date_hired, '%b') ORDER BY month";
    $result = $conn->query($monthly_query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $monthly_hires[] = $row;
        }
    }

    $conn->close();

} catch (Exception $e) {
    // Log error (in production, log to file instead of displaying)
    error_log("Dashboard Error: " . $e->getMessage());
    // Set default values if database fails
    $active_count = 0;
    $archived_count = 0;
    $total_employees = 0;
    $recent_hires = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8RM Dashboard Statistics</title>
    <link rel="icon" type="image/png" href="/logo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        /* Base layout styles */
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
        
        /* Sidebar Styles */
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
        .logo { display: flex; align-items: center; margin-bottom: 48px; padding: 0 12px; }
        .logo img { width: 44px; height: 44px; margin-right: 12px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .logo-text { display: flex; flex-direction: column; }
        .logo-text .main-name { font-size: 26px; font-weight: 800; display: flex; align-items: center; letter-spacing: -0.5px; }
        .logo-8 { color: #22c55e; }
        .logo-rm { color: #1e3a8a; margin-left: 0.2rem; }
        .sub-name { font-size: 11px; color: #f59e0b; font-weight: 600; margin-top: 2px; letter-spacing: 0.3px; }
        .navigation { flex-grow: 1; display: flex; flex-direction: column; }
        .navigation ul { list-style: none; padding: 0; margin: 0; }
        .navigation li a { display: flex; align-items: center; padding: 16px 20px; text-decoration: none; color: #4a5568; font-weight: 500; border-radius: 12px; margin-bottom: 6px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; font-size: 15px; }
        .navigation li a i { margin-right: 16px; font-size: 18px; width: 20px; text-align: center; }
        .navigation li a:hover { background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%); transform: translateX(4px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .navigation li.active > a { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: white; box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3); }
        .logout-item { margin-top: auto; padding-top: 20px; border-top: 1px solid #e2e8f0; width: 280px; }
        .logout-item a { display: flex; align-items: center; padding: 16px 20px; text-decoration: none; background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%); color: #c53030; font-weight: 600; border-radius: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-size: 15px; }
        .logout-item a:hover { background: linear-gradient(135deg, #feb2b2 0%, #fc8181 100%); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(197, 48, 48, 0.2); }
        
        /* Main Content Area */
        .main-content { 
            flex-grow: 1; 
            padding: 32px 48px; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden;
            background: transparent;
        }
        
        header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 32px;
        }
        
        header h1 { 
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

        /* White Floating Card */
        .content-wrapper {
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border-radius: 24px; 
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }

        .scrollable-area { 
            flex-grow: 1; 
            overflow-y: auto; 
            padding: 60px;
        }
        .scrollable-area::-webkit-scrollbar { width: 8px; }
        .scrollable-area::-webkit-scrollbar-track { background: #f1f5f9; }
        .scrollable-area::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 10px; 
        }
        .scrollable-area::-webkit-scrollbar-thumb:hover { background: #64748b; }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
        }

        .stat-card.blue::before {
            background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
        }

        .stat-card.orange::before {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-card.purple::before {
            background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
        }

        .stat-card.blue .stat-icon {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .stat-card.orange .stat-icon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #b45309;
        }

        .stat-card.purple .stat-icon {
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
            color: #6d28d9;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 48px;
            font-weight: 900;
            color: #1e293b;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-description {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }

        /* Summary Section */
        .summary-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
            border-radius: 20px;
            padding: 48px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .summary-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 40%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            transform: rotate(15deg);
        }

        .summary-section > * {
            position: relative;
            z-index: 1;
        }

        .summary-section h2 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 24px 0;
            color: #ffffff;
        }

        .summary-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 32px;
        }

        .summary-item {
            text-align: center;
            padding: 24px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-item-label {
            font-size: 14px;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-item-value {
            font-size: 36px;
            font-weight: 900;
            color: #ffffff;
        }

        .summary-item-percentage {
            font-size: 14px;
            color: #22c55e;
            margin-top: 8px;
            font-weight: 600;
        }

        /* Chart Section */
        .chart-section {
            margin-bottom: 48px;
        }

        .chart-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 4px 0;
        }

        .chart-subtitle {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        .chart-total {
            font-size: 36px;
            font-weight: 900;
            color: #1e293b;
            line-height: 1;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <img src="logo.png.png" alt="8 R M Logo Icon">
                <div class="logo-text">
                    <div class="main-name"><span class="logo-8">8</span><span class="logo-rm">R M</span></div>
                    <span class="sub-name">Utility Projects Construction</span>
                </div>
            </div>
            <nav class="navigation">
                <ul>
                    <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li><a href="employee.php"><i class="fas fa-users"></i><span>Employee Information</span></a></li>
                    <li><a href="government_beneficiary.php"><i class="fas fa-file-invoice"></i><span>Government Remittances</span></a></li>
                    <li><a href="changelog.php"><i class="fas fa-book"></i><span>Changelog</span></a></li>
                    <li><a href="employee_archive.php"><i class="fas fa-archive"></i><span>Employee Archive</span></a></li>
                    <li><a href="overview.php"><i class="fas fa-chart-bar"></i><span>Overview</span></a></li>
                </ul>
                <div class="logout-item">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>DASHBOARD</h1>
            </header>
            
            <div class="content-wrapper">
                <div class="scrollable-area">
                    <!-- Statistics Cards -->
                    <div class="stats-grid">
                        <div class="stat-card blue">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-label">Active Employees</div>
                            <div class="stat-value"><?php echo number_format($active_count); ?></div>
                            <div class="stat-description">Currently working</div>
                        </div>

                        <div class="stat-card orange">
                            <div class="stat-icon">
                                <i class="fas fa-archive"></i>
                            </div>
                            <div class="stat-label">Archived Employees</div>
                            <div class="stat-value"><?php echo number_format($archived_count); ?></div>
                            <div class="stat-description">No longer active</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="stat-label">Recent Hires</div>
                            <div class="stat-value"><?php echo number_format($recent_hires); ?></div>
                            <div class="stat-description">Last 30 days</div>
                        </div>

                        <div class="stat-card purple">
                            <div class="stat-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="stat-label">Total Employees</div>
                            <div class="stat-value"><?php echo number_format($total_employees); ?></div>
                            <div class="stat-description">All time</div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="chart-section">
                        <div class="chart-card">
                            <div class="chart-header">
                                <div>
                                    <h3 class="chart-title">Employee Hiring Trend</h3>
                                    <p class="chart-subtitle">Last 6 months activity</p>
                                </div>
                                <div class="chart-total"><?php echo array_sum(array_column($monthly_hires, 'count')); ?></div>
                            </div>
                            <div class="chart-container">
                                <canvas id="hiringTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="summary-section">
                        <h2>Employee Overview</h2>
                        <div class="summary-content">
                            <div class="summary-item">
                                <div class="summary-item-label">Active Rate</div>
                                <div class="summary-item-value">
                                    <?php 
                                    $active_rate = $total_employees > 0 ? ($active_count / $total_employees) * 100 : 0;
                                    echo number_format($active_rate, 1); 
                                    ?>%
                                </div>
                                <div class="summary-item-percentage">of total workforce</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-item-label">Archive Rate</div>
                                <div class="summary-item-value">
                                    <?php 
                                    $archive_rate = $total_employees > 0 ? ($archived_count / $total_employees) * 100 : 0;
                                    echo number_format($archive_rate, 1); 
                                    ?>%
                                </div>
                                <div class="summary-item-percentage">of total workforce</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-item-label">Monthly Growth</div>
                                <div class="summary-item-value">
                                    <?php 
                                    $growth_rate = $active_count > 0 ? ($recent_hires / $active_count) * 100 : 0;
                                    echo number_format($growth_rate, 1); 
                                    ?>%
                                </div>
                                <div class="summary-item-percentage">new hires this month</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Hiring Trend Line Chart
        const hiringCtx = document.getElementById('hiringTrendChart');
        if (hiringCtx) {
            const hiringData = {
                labels: <?php echo json_encode(array_column($monthly_hires, 'month_name')); ?>,
                datasets: [{
                    label: 'Employees Hired',
                    data: <?php echo json_encode(array_column($monthly_hires, 'count')); ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2
                }]
            };

            new Chart(hiringCtx, {
                type: 'line',
                data: hiringData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            titleColor: '#1e293b',
                            bodyColor: '#64748b',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            bodyFont: {
                                size: 13
                            },
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                },
                                precision: 0,
                                padding: 8
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                },
                                padding: 8
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>