<?php require_once 'check_auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missing Remittances Audit</title>
    <link rel="icon" type="image/png" href="/logo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* All of your existing CSS styles go here */
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; color: #333; }
        .dashboard-container { display: flex; width: 100%; height: 100vh; background-color: #fff; overflow: hidden; }
        .sidebar { width: 260px; flex-shrink: 0; background-color: #fefcf5; padding: 20px; display: flex; flex-direction: column; border-right: 1px solid #e0e0e0; }
        .logo { display: flex; align-items: center; margin-bottom: 40px; padding: 0 10px; }
        .logo img { width: 40px; height: 40px; margin-right: 10px; }
        .logo-text { display: flex; flex-direction: column; }
        .logo-text .main-name { font-size: 24px; font-weight: bold; display: flex; align-items: center; }
        .logo-text .main-name .logo-8 { color: #22c55e; }
        .logo-text .main-name .logo-rm { color: #1e3a8a; margin-left: 0.2rem; }
        .logo-text .sub-name { font-size: 10px; color: #facc15; font-weight: bold; }
        .navigation { flex-grow: 1; display: flex; flex-direction: column; }
        .navigation ul { list-style: none; padding: 0; margin: 0; }
        .navigation li a { display: flex; align-items: center; padding: 15px 20px; text-decoration: none; color: #4A5568; font-weight: 500; border-radius: 8px; margin-bottom: 5px; transition: background-color 0.3s, color 0.3s; }
        .navigation li a i { margin-right: 15px; font-size: 18px; width: 20px; text-align: center; }
        .navigation li a:hover { background-color: #e2e8f0; }
        .navigation li.active > a { background-color: #1e3a8a; color: white; }
        .dropdown-arrow { margin-left: auto; font-size: 12px; transition: transform 0.3s ease; }
        .submenu { list-style: none; padding-left: 25px; margin: 0; max-height: 0; overflow: hidden; transition: max-height 0.4s ease-in-out; }
        .navigation li.open > .submenu { max-height: 200px; margin-top: 5px; margin-bottom: 5px; }
        .navigation li.open > a .dropdown-arrow { transform: rotate(180deg); }
        .submenu li a { padding: 10px 30px; color: #5f6b7a; background-color: transparent; font-size: 15px; border-radius: 6px; }
        .submenu li.sub-active a { background-color: #e5e7eb; font-weight: 600; }
        .submenu li a:hover { background-color: #e2e8f0; }
        .logout-item { margin-top: auto; padding-top: 20px; border-top: 1px solid #e0e0e0; }
        .logout-item a { background-color: #f8d7da; color: #721c24; font-weight: bold; }
        .logout-item a:hover { background-color: #f5c6cb; color: #721c24; }
        .main-content { flex-grow: 1; padding: 30px 40px; background-color: #ffffff; display: flex; flex-direction: column; }
        .main-header { display: flex; justify-content: space-between; align-items: center; background-color: #2c5282; color: white; padding: 20px 30px; border-radius: 8px; margin-bottom: 25px; }
        .main-header h1 { margin: 0; font-size: 28px; font-weight: bold; }
        .table-container { flex-grow: 1; overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 16px 10px; text-align: left; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        th { font-weight: 600; font-size: 15px; color: #4A5568; }
        td { font-size: 15px; color: #4A5568; }
        tbody tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo"><img src="logo.png.png" alt="8 RM Logo Icon"><div class="logo-text"><div class="main-name"><span class="logo-8">8</span><span class="logo-rm">R M</span></div><span class="sub-name">Utility Projects Construction</span></div></div>
            <nav class="navigation">
                <ul>
                    <li><a href="overview.php"><i class="fas fa-chart-bar"></i> Overview</a></li>
                    <li><a href="employee.php"><i class="fas fa-users"></i> Employee Information</a></li>
                    <li class="has-submenu">
                        <a href="#" class="dropdown-toggle"><i class="fas fa-file-alt"></i> Government Remittances <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                        <ul class="submenu">
                            <li><a href="pag_ibig.php">Pag-IBIG</a></li>
                            <li><a href="phil_health.php">PhilHealth</a></li>
                            <li><a href="sss.php">SSS</a></li>
                        </ul>
                    </li>
                    <li class="has-submenu active">
                        <a href="#" class="dropdown-toggle"><i class="fas fa-book"></i> Changelog <i class="fas fa-chevron-down dropdown-arrow"></i></a>
                         <ul class="submenu">
                            <li><a href="modification.php">Modifications</a></li>
                            <li><a href="history_remittances.php">History Remittances</a></li>
                            <!-- ADDED LINK TO NEW PAGE -->
                            <li class="sub-active"><a href="missing_remittances.php">Missing Remittances</a></li>
                         </ul>
                    </li>
                    <li><a href="employee_archive.php"><i class="fas fa-archive"></i> Employee Archive</a></li>
                </ul>
                <div class="logout-item"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <h1>Missing Remittances Audit</h1>
            </header>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Missing For Month</th>
                            <th>Missing Remittance Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // --- Database Connection ---
                        $servername = "localhost";
                        $username = "u987478351_ruth";
                        $password = "Qwertyuiop143!";
                        $dbname = "u987478351_8rm_admin";
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            echo "<tr><td colspan='3'>Connection Failed: " . $conn->connect_error . "</td></tr>";
                        } else {
                            // --- THIS IS THE ADVANCED SQL QUERY ---
                            $sql = "
                                -- Step 3: Select all the generated combinations that are NOT in the remittances table
                                SELECT
                                    all_expected.name,
                                    all_expected.remittance_month,
                                    all_expected.remittance_year,
                                    all_expected.remittance_type
                                FROM
                                    (
                                        -- Step 2: Generate all possible combinations of active employees, months, and types
                                        SELECT
                                            e.id,
                                            e.name,
                                            months.month_num AS remittance_month,
                                            YEAR(CURRENT_DATE) AS remittance_year,
                                            types.remittance_type
                                        FROM
                                            employee e
                                        CROSS JOIN
                                            -- Step 1a: Generate a list of months to check (e.g., the last 6 months)
                                            (
                                                SELECT 1 as month_num UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
                                                SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL
                                                SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
                                                SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
                                            ) AS months
                                        CROSS JOIN
                                            -- Step 1b: Generate a list of remittance types
                                            (
                                                SELECT 'SSS' as remittance_type UNION ALL
                                                SELECT 'Pag-IBIG' UNION ALL
                                                SELECT 'PhilHealth'
                                            ) AS types
                                        WHERE
                                            e.status = 'Active' AND months.month_num <= MONTH(CURRENT_DATE)
                                    ) AS all_expected
                                LEFT JOIN
                                    remittances r ON all_expected.id = r.employee_id
                                    AND all_expected.remittance_month = r.remittance_month
                                    AND all_expected.remittance_year = r.remittance_year
                                    AND all_expected.remittance_type = r.remittance_type
                                WHERE
                                    r.id IS NULL -- This is the key: it finds where there was no match
                                ORDER BY
                                    all_expected.remittance_year DESC,
                                    all_expected.remittance_month DESC,
                                    all_expected.name ASC
                            ";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    // Format the month and year for display
                                    $month_covered = date("F Y", mktime(0, 0, 0, $row["remittance_month"], 1, $row["remittance_year"]));
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($month_covered) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["remittance_type"]) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No missing remittances found for active employees this year.</td></tr>";
                            }
                            $conn->close();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropdownToggles = document.querySelectorAll('.has-submenu > a');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', (event) => {
                    event.preventDefault();
                    toggle.parentElement.classList.toggle('open');
                });
            });
            document.querySelector('.has-submenu.active')?.classList.add('open');
        });
    </script>
</body>
</html>