<?php include 'sample_data.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    

        /* Chillax Table Styles */
        .data-table-container {
            background: var(--surface-color);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            margin-top: 2rem;
            border: 1px solid rgba(255,255,255,0.8);
            padding: 1.5rem;
        }
        .table-header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }
        .table-header-controls .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 99px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }
        .table-header-controls .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
            background: var(--primary-dark);
        }
        .data-table-wrapper {
            overflow-x: auto;
        }
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px; /* For pill-shaped rows */
            text-align: left;
        }
        .data-table th, .data-table td {
            padding: 1.2rem 1.5rem;
        }
        .data-table th {
            background-color: transparent;
            font-weight: 700;
            color: #94a3b8;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .data-table tbody tr {
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.01);
        }
        .data-table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.03);
            transform: scale(1.008);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            z-index: 10;
            position: relative;
        }
        .data-table td {
            color: var(--text-main);
            font-size: 0.95rem;
            font-weight: 500;
        }
        .data-table tbody td:first-child {
            border-top-left-radius: var(--radius-md);
            border-bottom-left-radius: var(--radius-md);
            font-weight: 700;
            color: var(--primary-dark);
        }
        .data-table tbody td:last-child {
            border-top-right-radius: var(--radius-md);
            border-bottom-right-radius: var(--radius-md);
        }
        .status-badge {
            padding: 0.35rem 1rem;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 700;
            background-color: #e0e7ff;
            color: var(--primary-dark);
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
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
                <a href="dashboard.php" class="nav-link">
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
                <a href="tracker.php" class="nav-link active">
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
                <h1><i class="fa-solid fa-chart-line" style="color: var(--primary-color); margin-right: 15px; font-size: 0.9em;"></i>Status Tracker</h1>
                <p>Enrollment status, promotions, transferees. Here is the raw data available.</p>
            </div>
            
            <div class="data-table-container">
        <div class="table-header-controls">
            <h3 style="font-size: 1.25rem; color: var(--text-main); font-weight: 700; letter-spacing: -0.5px;">Data Overview</h3>
            <button class="btn"><i class="fa-solid fa-plus"></i> Add New Record</button>
        </div>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>Student ID</th><th>Name</th><th>Academic Status</th><th>Enrollment Status</th><th>Clearance</th></tr>
                </thead>
                <tbody>
                    <?php foreach($mock_tracker as $row): ?>
                    <tr><td><?php echo $row["student_id"]; ?></td><td><?php echo $row["name"]; ?></td><td><?php echo $row["academic_status"]; ?></td><td><?php echo $row["enrollment_status"]; ?></td><td><?php echo $row["clearance"]; ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
        </div>
    </main>
</body>
</html>