<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
        :root {
            --primary-color: #6366f1;      /* Indigo 500 */
            --primary-light: #a5b4fc;      /* Indigo 300 */
            --primary-dark: #4338ca;       /* Indigo 700 */
            --secondary-color: #10b981;    /* Emerald 500 */
            --bg-color: #f4f7fe;           /* Soft pastel blue/gray */
            --surface-color: rgba(255, 255, 255, 0.85); /* Glassy white */
            --text-main: #1e293b;          /* Slate 800 */
            --text-muted: #64748b;         /* Slate 500 */
            --sidebar-width: 280px;
            --header-height: 80px;
            --radius-lg: 20px;
            --radius-md: 14px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.05);
            --shadow-hover: 0 20px 40px -10px rgba(99,102,241,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--surface-color);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-right: 1px solid rgba(255, 255, 255, 0.5);
            color: var(--text-main);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            padding-top: 2rem;
            z-index: 100;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 0 2rem 2rem 2rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .sidebar-brand i {
            font-size: 2rem;
            color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
            padding: 10px;
            border-radius: 12px;
        }

        .sidebar-brand h2 {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.2;
            color: var(--text-main);
        }

        .sidebar-brand h2 span {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 4px;
        }

        .nav-list {
            list-style: none;
            padding: 0 1.2rem;
        }

        .nav-item {
            margin-bottom: 0.4rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.2rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
            gap: 14px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            color: #94a3b8;
        }

        .nav-link:hover {
            background-color: rgba(99, 102, 241, 0.05);
            color: var(--primary-color);
        }

        .nav-link:hover i {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
        }

        .nav-link.active i {
            color: white;
        }

        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .header {
            height: var(--header-height);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 3rem;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }

        .search-bar {
            background: white;
            padding: 10px 20px;
            border-radius: 99px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            color: var(--text-muted);
            width: 300px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            padding: 8px 16px;
            background: white;
            border-radius: 99px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .user-profile:hover {
            box-shadow: var(--shadow-hover);
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .user-info h4 {
            font-size: 0.9rem;
            font-weight: 700;
        }
        
        .user-info p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Dashboard Body */
        .dashboard-body {
            padding: 3rem;
            flex-grow: 1;
        }

        .page-title {
            margin-bottom: 2.5rem;
            color: var(--text-main);
        }

        .page-title h1 {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .page-title p {
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-size: 1.05rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.8rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background-color: var(--surface-color);
            backdrop-filter: blur(10px);
            padding: 1.8rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.4s ease;
            border: 1px solid rgba(255,255,255,0.8);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: white;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-green { background: #ecfdf5; color: #10b981; }
        .icon-orange { background: #fff7ed; color: #f97316; }
        .icon-purple { background: #f5f3ff; color: #8b5cf6; }

        .stat-details h3 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .stat-details p {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Modules Grid */
        .section-header {
            margin-bottom: 1.8rem;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.8rem;
        }

        .module-card {
            background-color: var(--surface-color);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-soft);
            text-decoration: none;
            color: var(--text-main);
            border: 1px solid rgba(255,255,255,0.8);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .module-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: var(--shadow-hover);
            background-color: white;
            border-color: white;
        }

        .module-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            background: #f1f5f9;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
            transition: all 0.4s ease;
        }

        .module-card:hover .module-icon {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            transform: rotate(5deg) scale(1.1);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .module-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
            letter-spacing: -0.5px;
        }

        .module-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .module-arrow {
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            color: #cbd5e1;
            font-size: 1.3rem;
            transition: all 0.4s ease;
            opacity: 0;
            transform: translateX(-15px);
        }

        .module-card:hover .module-arrow {
            opacity: 1;
            transform: translateX(0);
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            :root {
                --sidebar-width: 90px;
            }
            .sidebar-brand h2 {
                display: none;
            }
            .nav-link span {
                display: none;
            }
            .nav-link {
                justify-content: center;
                padding: 1.2rem;
            }
            .nav-link i {
                margin: 0;
                font-size: 1.4rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-body {
                padding: 1.5rem;
            }
            .header {
                padding: 0 1.5rem;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2>Registrar SIS<span>Management Portal</span></h2>
        </div>
        
        <ul class="nav-list">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Personal_info.php" class="nav-link">
                    <i class="fa-solid fa-users"></i>
                    <span>Personal Info</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Guardian&Emergency_Contact.php" class="nav-link">
                    <i class="fa-solid fa-hands-holding-child"></i>
                    <span>Guardian & Contact</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Academic_history.php" class="nav-link">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Academic History</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="Health_Record_log.php" class="nav-link">
                    <i class="fa-solid fa-truck-medical"></i>
                    <span>Health Record Log</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="id_generator.php" class="nav-link">
                    <i class="fa-solid fa-id-card"></i>
                    <span>ID Generation</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="rfid.php" class="nav-link">
                    <i class="fa-solid fa-wifi"></i>
                    <span>RFID Scanner</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="docu.php" class="nav-link">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Document Requests</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="tracker.php" class="nav-link">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Status Tracker</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="storage.php" class="nav-link">
                    <i class="fa-solid fa-folder-tree"></i>
                    <span>Digital File Storage</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="masterlist.php" class="nav-link">
                    <i class="fa-solid fa-list-ol"></i>
                    <span>Student Masterlist</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../index.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="header">
            <div class="search-bar" style="color: var(--text-muted);">
                <i class="fa-solid fa-magnifying-glass"></i> <span style="margin-left: 10px; font-size: 0.9rem;">Search records...</span>
            </div>
            
            <div class="user-profile">
                <div class="user-info" style="text-align: right;">
                    <h4>Admin User</h4>
                    <p>Head Registrar</p>
                </div>
                <div class="avatar">
                    A
                </div>
            </div>
        </header>

        <!-- Dashboard Body -->
        <div class="dashboard-body">
            
            <div class="page-title">
                <h1>Overview Dashboard</h1>
                <p>Welcome back! Here's what's happening in the system today.</p>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div class="stat-details">
                        <h3>1,248</h3>
                        <p>Total Active Students</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-green">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="stat-details">
                        <h3>42</h3>
                        <p>Pending Document Requests</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-orange">
                        <i class="fa-solid fa-id-badge"></i>
                    </div>
                    <div class="stat-details">
                        <h3>15</h3>
                        <p>Pending ID Generates</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-purple">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div class="stat-details">
                        <h3>5</h3>
                        <p>Health Logs Today</p>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <h2 class="section-header">
                <i class="fa-solid fa-chart-pie"></i> Data Analytics
            </h2>
            <div class="analytics-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.8rem; margin-bottom: 3rem;">
                <!-- Line Chart -->
                <div class="stat-card" style="flex-direction: column; align-items: flex-start;">
                    <h3 style="font-size: 1.1rem; color: var(--text-main); margin-bottom: 1rem;"><i class="fa-solid fa-graduation-cap"></i> Student Enrollment Trends</h3>
                    <div style="width: 100%; height: 300px;">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>
                <!-- Doughnut Chart -->
                <div class="stat-card" style="flex-direction: column; align-items: flex-start;">
                    <h3 style="font-size: 1.1rem; color: var(--text-main); margin-bottom: 1rem;"><i class="fa-solid fa-venus-mars"></i> Gender Distribution</h3>
                    <div style="width: 100%; height: 300px;">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Modules Section -->
            <h2 class="section-header">
                <i class="fa-solid fa-cubes"></i> System Modules
            </h2>

            <div class="modules-grid">
                
                <!-- Personal Info -->
                <a href="Personal_info.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-address-card"></i>
                    </div>
                    <h3 class="module-title">Personal Info</h3>
                    <p class="module-desc">Manage core student details, profiles, and demographic information.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Guardian & Emergency Contact -->
                <a href="Guardian&Emergency_Contact.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <h3 class="module-title">Guardian & Emergency Contact</h3>
                    <p class="module-desc">Maintain records of parents, guardians, and contact persons for emergencies.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Academic History -->
                <a href="Academic_history.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <h3 class="module-title">Academic History</h3>
                    <p class="module-desc">Track previous schools attended, grades, scholastic records, and achievements.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Health Record Log -->
                <a href="Health_Record_log.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-notes-medical"></i>
                    </div>
                    <h3 class="module-title">Health Record Log</h3>
                    <p class="module-desc">Monitor student medical conditions, clinic visits, and health interventions.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Document Requests -->
                <a href="docu.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <h3 class="module-title">Document Requests</h3>
                    <p class="module-desc">Process requests for Form 137, Cert. of Good Moral Character, and transcripts.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- ID Generation / RFID -->
                <a href="id_generator.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 class="module-title">ID & RFID</h3>
                    <p class="module-desc">Generate student IDs, QR/RFID systems, and manage ID printing.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Status Tracker -->
                <a href="tracker.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-bars-progress"></i>
                    </div>
                    <h3 class="module-title">Student Status Tracker</h3>
                    <p class="module-desc">Track enrollment status, promotions, drop-outs, and transferees.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Digital File Storage -->
                <a href="storage.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-folder-tree"></i>
                    </div>
                    <h3 class="module-title">Digital File Storage</h3>
                    <p class="module-desc">Securely store and retrieve digital copies of submitted student requirements.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

                <!-- Masterlist Generator -->
                <a href="masterlist.php" class="module-card">
                    <div class="module-icon">
                        <i class="fa-solid fa-list-ol"></i>
                    </div>
                    <h3 class="module-title">Masterlist Generator</h3>
                    <p class="module-desc">Generate and export official class masterlists and registrar reports.</p>
                    <i class="fa-solid fa-arrow-right module-arrow"></i>
                </a>

            </div>
        </div>
    </main>

    <script>
        // Enrollment Line Chart
        const ctxEnrollment = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(ctxEnrollment, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Enrollments',
                    data: [150, 180, 210, 190, 250, 290],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Gender Doughnut Chart
        const ctxGender = document.getElementById('genderChart').getContext('2d');
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female', 'Other'],
                datasets: [{
                    data: [55, 42, 3],
                    backgroundColor: ['#3b82f6', '#ec4899', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>
