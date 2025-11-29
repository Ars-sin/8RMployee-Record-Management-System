<?php
// The user must be logged in to see this page.
require_once 'check_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8RM Dashboard Overview</title>
    <link rel="icon" type="image/png" href="/logo.png.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Base layout styles from employee_archive.php */
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
        
       /* Sidebar Styles (Unchanged) */
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
        
        /* Main Content Area (Gray background space) */
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

        /* The White Floating Card that holds all content */
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

        /* --- THIS IS YOUR ORIGINAL main-content-wrapper, RENAMED --- */
        /* It now correctly controls the scrolling INSIDE the white card */
        .scrollable-area { 
            flex-grow: 1; 
            overflow-y: auto; 
            /* background is removed here to show the card's white bg */
        }
        .scrollable-area::-webkit-scrollbar { width: 8px; }
        .scrollable-area::-webkit-scrollbar-track { background: #f1f5f9; }
        .scrollable-area::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 10px; 
        }
        .scrollable-area::-webkit-scrollbar-thumb:hover { background: #64748b; }
        
        
        /* --- YOUR ORIGINAL, DETAILED SECTION STYLES ARE NOW RESTORED --- */
        section { 
            padding: 60px 60px 80px; 
            position: relative;
        }
        
        section h1 { 
            font-weight: 900; 
            color: #1e293b;
            font-size: 42px;
            margin-bottom: 48px;
            position: relative;
            letter-spacing: -1px;
        }
        
        section h1::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
            border-radius: 2px;
        }
        
        section h2 { 
            font-weight: 800; 
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }
        .overview-content .content-section { margin-bottom: 48px; padding: 32px; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .overview-content .content-section:hover { transform: translateY(-2px); box-shadow: 0 12px 48px rgba(0, 0, 0, 0.1); }
        .overview-content .content-section h2 { margin: 0 0 20px 0; font-size: 28px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 12px; }
        .overview-content .content-section p { margin: 0 0 16px 0; line-height: 1.8; color: #475569; font-size: 17px; font-weight: 400; text-align: justify; }
        .overview-content .content-section p:last-child { margin-bottom: 0; }
        .project-plan-content { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%); color: #ffffff; position: relative; overflow: hidden; }
        .project-plan-content::before { content: ''; position: absolute; top: -50%; right: -20%; width: 40%; height: 200%; background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%); transform: rotate(15deg); }
        .project-plan-content > * { position: relative; z-index: 1; }
        .project-plan-content h1 { font-size: 42px; margin: 0 0 48px 0; color: #ffffff; }
        .project-plan-content h1::after { background: linear-gradient(90deg, #ffffff 0%, rgba(255, 255, 255, 0.7) 100%); }
        .project-plan-content h2 { font-size: 26px; font-weight: 800; margin: 40px 0 24px 0; color: #f1f5f9; padding-left: 20px; border-left: 4px solid #22c55e; }
        .project-plan-content p, .project-plan-content ul { font-size: 17px; line-height: 1.8; max-width: 90%; color: #e2e8f0; }
        .project-plan-content ul { padding-left: 24px; margin-bottom: 32px; }
        .project-plan-content li { margin-bottom: 12px; position: relative; }
        .project-plan-content li::marker { color: #22c55e; }
        .services-offered-content { position: relative; }
        .service-category { margin-bottom: 48px; padding: 32px; background: rgba(255, 255, 255, 0.9); border-radius: 20px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06); border: 1px solid rgba(226, 232, 240, 0.5); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .service-category:hover { transform: translateY(-4px); box-shadow: 0 20px 48px rgba(0, 0, 0, 0.1); }
        .services-offered-content h2 { font-size: 24px; margin: 0 0 24px 0; color: #1e293b; font-weight: 800; letter-spacing: -0.5px; }
        .services-offered-content ul { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px 24px; padding: 0; list-style: none; }
        .services-offered-content li { padding: 12px 16px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 12px; border-left: 4px solid #e2e8f0; color: #475569; font-weight: 500; font-size: 15px; transition: all 0.3s ease; }
        .services-offered-content li:hover { border-left-color: #22c55e; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: #166534; transform: translateX(4px); }
        .clients-content { background: linear-gradient(135deg, #1e3a8a 0%, #2c5282 50%, #2563eb 100%); color: white; position: relative; overflow: hidden; }
        .clients-content::before { content: ''; position: absolute; top: -30%; left: -20%; width: 40%; height: 160%; background: radial-gradient(circle, rgba(34, 197, 94, 0.1) 0%, transparent 70%); transform: rotate(-15deg); }
        .clients-content > * { position: relative; z-index: 1; }
        .clients-content h1 { margin: 0 0 48px 0; font-size: 42px; color: white; }
        .clients-content h1::after { background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%); }
        .client-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 40px 32px; }
        .client-item { display: flex; flex-direction: column; align-items: center; text-align: center; transition: transform 0.3s ease; }
        .client-item:hover { transform: translateY(-8px); }
        .client-image-container { width: 100%; max-width: 220px; height: 180px; border: 3px solid rgba(255, 255, 255, 0.2); border-radius: 20px; display: flex; justify-content: center; align-items: center; padding: 16px; box-sizing: border-box; overflow: hidden; backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2); transition: all 0.3s ease; }
        .client-item:hover .client-image-container { border-color: #22c55e; box-shadow: 0 12px 48px rgba(34, 197, 94, 0.2); }
        .client-image-container.logo-bg { background: rgba(255, 255, 255, 0.95); }
        .client-image-container img.logo-image { max-width: 100%; max-height: 100%; object-fit: contain; }
        .client-image-container img.photo-image { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
        .client-item p { margin-top: 20px; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #e2e8f0; }

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
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li><a href="employee.php"><i class="fas fa-users"></i><span>Employee Information</span></a></li>
                    <li><a href="government_beneficiary.php"><i class="fas fa-file-invoice"></i><span>Government Remittances</span></a></li>
                    <li><a href="changelog.php"><i class="fas fa-book"></i><span>Changelog</span></a></li>
                    <li><a href="employee_archive.php"><i class="fas fa-archive"></i><span>Employee Archive</span></a></li>
                    <li class="active"><a href="overview.php"><i class="fas fa-chart-bar"></i><span>Overview</span></a></li>
                </ul>
                <div class="logout-item">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <header>
                <h1>OVERVIEW</h1>
            </header>
            
            <div class="content-wrapper">
                <div class="scrollable-area">
                    <!-- Block 1: Overview Content -->
                    <section class="overview-content">
                        <div class="content-section">
                            <p>8RM Utility Projects Construction was founded in December 2021 with the goal of providing more effective services at the most affordable prices, meeting deadlines, and maintaining high standards of quality.</p>
                            <p>Personnel with vast expertise in construction, engineering, management, design, technical services, and facilities management lead and work at 8RM Utility Projects Construction years of management and executive duties on top.</p>
                            <p>The goal of 8RM Utility Projects Construction is to promote high-quality services while adopting strict safety measures, methodical job execution, adherence to standards, and on-time service delivery.</p>
                        </div>
                        <div class="content-section">
                            <h2>Vision</h2>
                            <p>To provide top-tier design, structural engineering, and construction services as a renowned general construction firm.</p>
                        </div>
                        <div class="content-section">
                            <h2>Mission</h2>
                            <p>Our goal is to satisfy all of our stakeholders by providing construction excellence via the development of our team members.</p>
                        </div>
                    </section>
                    
                    <!-- Block 2: Project Quality Plan -->
                    <section class="project-plan-content">
                        <h1>PROJECT QUALITY PLAN</h1>
                        <h2>Quality Objectives</h2>
                        <ul>
                            <li>Achieve a minimum 95% minimum client satisfaction</li>
                            <li>Ensure 100% compliance with local and international codes</li>
                            <li>Reduce rework and defects</li>
                        </ul>
                       <h2>Quality Control Procedures</h2>
                        <ul>
                            <li>Assign a dedicated quality assurance team responsible for monitoring quality standards</li>
                            <li>Define project standards and specifications based on client requirements and industry best practices.</li>
                            <li>Conduct tests on materials and workmanship to verify compliance with specifications.</li>
                            <li>Maintain detailed records of inspections, test results, and corrective actions taken</li>
                        </ul>
                        <h2>Quality Control Activities</h2>
                        <ul>
                            <li>Regular Audits: Conduct weekly audits to assess adherence to quality standards.</li>
                            <li>Non-Conformance Reporting: Implement a system for reporting and addressing non-conformanceissues promptly</li>
                            <li>Corrective Actions: Define procedures for corrective actions, including responsible parties and timelines.</li>
                        </ul>
                        <h2>Training and Communication</h2>
                        <ul>
                            <li>Provide ongoing training for staff on quality standards and procedures.</li>
                            <li>Facilitate open communication channels for team members to report quality concerns.</li>
                        </ul>
                        <h2>Client Involvement</h2>
                        <ul>
                            <li>Involve clients in key key quality reviews to ensure their expectations are being met.</li>
                            <li>Schedule regular updates and feedback sessions to keep clients informed of progress.</li>
                        </ul>
                        <h2>Continuous Improvement</h2>
                        <ul>
                            <li>Review and analyze quality performance metrics regularly.</li>
                            <li>Use feedback and lessons learned to refine quality control processes for future projects.</li>
                        </ul>
                    </section>

                    <!-- Block 3: Services Offered -->
                    <section class="services-offered-content">
                        <h1>SERVICES OFFERED</h1>
                        <div class="service-category">
                            <h2>CIVIL, STRUCTURAL AND ARCHITECTURAL WORKS </h2>
                            <ul>
                                <li>Site Development</li>
                                <li>Earthworks</li>
                                <li>Truss / Beam and Steel Works</li>
                                <li>Road and pavement construction</li>
                                <li>Dry wall and Acoustic/Laminated Ceiling Installation</li>
                                <li>Tiling Works</li>
                                <li>Roofing Works</li>
                                <li>CHB Laying and Concreting Works</li>
                                <li>Painting and Waterproofing Works</li>
                                <li>Glass and Aluminum Works</li>
                                <li>Steel Fabrication Works</li>
                            </ul>
                        </div>
                        <div class="service-category">
                            <h2>MECHANICAL WORKS</h2>
                            <ul>
                                <li>Air Conditioning System from air cooled, water cooled, VRV/VRF systems and to small types of units</li>
                                <li>Chilled and Cooling Water Piping Works</li>
                                <li>Compressed Dry Air Piping Works</li>
                                <li>Process Piping Works</li>
                                <li>Fire Protection System Works</li>
                                <li>Lift and Conveyance System Works</li>
                                <li>Stair Pressurization and Smoke Evacuation System Works</li>
                                <li>Plumbing Works</li>
                                <li>Ducting Works</li>
                                <li>Preventive Maintenance of Mechanical Equipments</li>
                            </ul>
                        </div>
                        <div class="service-category">
                            <h2>ELECTRICAL AND ELECTRONIC WORKS</h2>
                            <ul>
                                <li>Incoming Power and Unit Substation</li>
                                <li>Solar Power System</li>
                                <li>Power Distribution System for Residential, Commercial and Industrial</li>
                                <li>Overhead/Underground Power Distribution System</li>
                                <li>Secondary works and Production Equipment Power Supply</li>
                                <li>Panel boards and MCC Sales</li>
                                <li>Preventive Maintenance of Electrical Equipment</li>
                                <li>Lightning Protection and Grounding System</li>
                                <li>Fire Detection and Alarm System</li>
                                <li>LAN, PABX, CCTV and Telephone System</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Block 4: List of Clients -->
                    <section class="clients-content">
                        <h1>LIST OF CLIENTS</h1>
                        <div class="client-grid">
                            <div class="client-item"><div class="client-image-container logo-bg"><img class="logo-image" src="Chong_Hua.png" alt="Chong Hua Hospital Logo"></div></div>
                            <div class="client-item"><div class="client-image-container logo-bg"><img class="logo-image" src="Sky.png" alt="Solidsteel Logo"></div></div>
                            <div class="client-item"><div class="client-image-container logo-bg"><img class="logo-image" src="Steelasia.png" alt="SteelAsia Logo"></div></div>
                            <div class="client-item"><div class="client-image-container logo-bg"><img class="logo-image" src="Flats.png" alt="The Flats Logo"></div></div>
                            <div class="client-item"><div class="client-image-container"><img class="photo-image" src="Aruga.png" alt="Aruga Resort"></div></div>
                            <div class="client-item"><div class="client-image-container"><img class="photo-image" src="BDO.png" alt="BDO Tower"></div><p>BDO CEBU FUENTE TOWER</p></div>
                            <div class="client-item"><div class="client-image-container"><img class="photo-image" src="Madanibay.png" alt="Mandani Bay"></div><p>MANDANI BAY</p></div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>