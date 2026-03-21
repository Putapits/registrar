<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}
include 'api/data_loader.php';

// Fetch Dynamic Stats
$q_active = $conn->query("SELECT COUNT(*) AS c FROM personal_info WHERE status='Active'");
$active_students = $q_active ? $q_active->fetch_assoc()['c'] : 0;

$q_docs = $conn->query("SELECT COUNT(*) AS c FROM document_requests WHERE status IN ('Pending', 'Processing')");
$pending_docs = $q_docs ? $q_docs->fetch_assoc()['c'] : 0;

$q_id = $conn->query("SELECT COUNT(*) AS c FROM student_ids WHERE status IN ('Pending', 'Partial')");
$id_queue = $q_id ? $q_id->fetch_assoc()['c'] : 0;

$q_health = $conn->query("SELECT COUNT(*) AS c FROM health_logs WHERE DATE(log_date) = CURDATE()");
$health_today = $q_health ? $q_health->fetch_assoc()['c'] : 0;

$q_gender = $conn->query("SELECT gender, COUNT(*) AS c FROM personal_info GROUP BY gender");
$males = 0;
$females = 0;
if ($q_gender) {
    while ($row = $q_gender->fetch_assoc()) {
        if ($row['gender'] == 'Male')
            $males = $row['c'];
        if ($row['gender'] == 'Female')
            $females = $row['c'];
    }
}
?>
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
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }

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

        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }

        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }

        .nav-list {
            list-style: none;
            padding: 0 1.2rem;
        }

        .nav-item {
            margin-bottom: 0.4rem;
        }

        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: var(--radius-md); transition: all 0.3s; gap: 14px; font-weight: 500; }

        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }

        .nav-link:hover { background: rgba(255, 255, 255, 0.1); color: white; }

        .nav-link.active {
            background: var(--primary-color); color: white; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
        }

        .nav-link.active i { color: white; }

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

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            padding: 8px 16px;
            background: white;
            border-radius: 99px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s;
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
            font-weight: 800; letter-spacing: -1.5px;
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

        .stat-details h3 { font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 4px; }
        .stat-details p { font-size: 0.9rem; color: var(--text-muted); font-weight: 600; }

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
        }

        .module-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.6rem; letter-spacing: -0.5px; }
        .module-desc { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; }

        /* Dark Mode */
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .module-card, :root[data-theme="dark"] .stat-card, :root[data-theme="dark"] .user-profile { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .module-icon, :root[data-theme="dark"] .stat-icon { background: rgba(15, 23, 42, 0.6); }

        .theme-toggle { background: white; border: 1px solid rgba(0,0,0,0.05); width: 44px; height: 44px; border-radius: 50%; cursor: pointer; margin-right: 15px; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        :root[data-theme="dark"] .theme-toggle { background: #1e293b; color: white; border-color: rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2>Registrar SIS<span>Management Portal</span></h2>
        </div>
        <ul class="nav-list">
            <li class="nav-item"><a href="dashboard.php" class="nav-link active"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="Personal_info.php" class="nav-link"><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
            <li class="nav-item"><a href="Guardian&Emergency_Contact.php" class="nav-link"><i class="fa-solid fa-hands-holding-child"></i><span>Guardian & Contact</span></a></li>
            <li class="nav-item"><a href="Academic_history.php" class="nav-link"><i class="fa-solid fa-book-open"></i><span>Academic History</span></a></li>
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link"><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
            <li class="nav-item"><a href="id_generator.php" class="nav-link"><i class="fa-solid fa-id-card"></i><span>ID Processing</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link"><i class="fa-solid fa-wifi"></i><span>RFID / QR Module</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link"><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link"><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link"><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link"><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div style="font-weight: 700; color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-calendar-day"></i> <?php echo date('F d, Y'); ?> | <span id="liveTime" style="font-weight: 600;"></span></div>
            <div style="display: flex; align-items: center;">
                <div class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon"></i></div>
                <div class="user-profile">
                    <div class="user-info" style="text-align: right;"><h4>Registrar</h4><p><?php echo htmlspecialchars($_SESSION["admin_user"]); ?></p></div>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION["admin_user"], 0, 1)); ?></div>
                </div>
            </div>
        </header>

        <div class="dashboard-body">
            <div class="page-title">
                <h1>Overview Dashboard</h1>
                <p>Welcome back! Comprehensive monitoring for your school information system.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fa-solid fa-user-graduate"></i></div>
                    <div class="stat-details"><h3><?php echo number_format($active_students); ?></h3><p>Active Students</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div class="stat-details"><h3><?php echo number_format($pending_docs); ?></h3><p>Pending Documents</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-orange"><i class="fa-solid fa-id-badge"></i></div>
                    <div class="stat-details"><h3><?php echo number_format($id_queue); ?></h3><p>ID Printing Queue</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-purple"><i class="fa-solid fa-heart-pulse"></i></div>
                    <div class="stat-details"><h3><?php echo number_format($health_today); ?></h3><p>Health Logs Today</p></div>
                </div>
            </div>

            <h2 class="section-header"><i class="fa-solid fa-cubes"></i> System Modules</h2>
            <div class="modules-grid">
                <a href="Personal_info.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-address-card"></i></div>
                    <h3 class="module-title">Personal Info</h3>
                    <p class="module-desc">Manage core student details and demographics.</p>
                </a>
                <a href="Guardian&Emergency_Contact.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-users-viewfinder"></i></div>
                    <h3 class="module-title">Guardian & Contact</h3>
                    <p class="module-desc">Maintain emergency contact and parent records.</p>
                </a>
                <a href="Academic_history.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-award"></i></div>
                    <h3 class="module-title">Academic History</h3>
                    <p class="module-desc">Track scholastic records and grades.</p>
                </a>
                <a href="Health_Record_log.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-notes-medical"></i></div>
                    <h3 class="module-title">Health Record Log</h3>
                    <p class="module-desc">Monitor student medical conditions and clinic visits.</p>
                </a>
                <a href="docu.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-file-signature"></i></div>
                    <h3 class="module-title">Document Requests</h3>
                    <p class="module-desc">Process official certificates and transcript requests.</p>
                </a>
                <a href="id_generator.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-id-card"></i></div>
                    <h3 class="module-title">ID Processing</h3>
                    <p class="module-desc">Manage physical ID printing and queue.</p>
                </a>
                <a href="rfid.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-wifi"></i></div>
                    <h3 class="module-title">RFID / QR Module</h3>
                    <p class="module-desc">Assign and link digital identities to students.</p>
                </a>
                <a href="tracker.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-bars-progress"></i></div>
                    <h3 class="module-title">Status Tracker</h3>
                    <p class="module-desc">Track enrollment status and student transitions.</p>
                </a>
                <a href="storage.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-folder-tree"></i></div>
                    <h3 class="module-title">Digital Storage</h3>
                    <p class="module-desc">Archive digital copies of submitted requirements.</p>
                </a>
                <a href="masterlist.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-list-ol"></i></div>
                    <h3 class="module-title">Masterlist Generator</h3>
                    <p class="module-desc">Export official class lists and reporting.</p>
                </a>
            </div>
            
            <div class="analytics-row" style="margin-top: 3rem; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <div class="stat-card" style="flex-direction: column; align-items: flex-start;">
                    <h3 style="margin-bottom: 2rem; font-size: 1.1rem;"><i class="fa-solid fa-chart-line"></i> Enrollment Trends</h3>
                    <div style="width: 100%; height: 300px;"><canvas id="enrollmentChart"></canvas></div>
                </div>
                <div class="stat-card" style="flex-direction: column; align-items: flex-start;">
                    <h3 style="margin-bottom: 2rem; font-size: 1.1rem;"><i class="fa-solid fa-chart-pie"></i> Gender Split</h3>
                    <div style="width: 100%; height: 300px;"><canvas id="genderChart"></canvas></div>
                </div>
            </div>

        </div>
    </main>

    <script>
        const ctxEnrollment = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(ctxEnrollment, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Enrollments', data: [0, 0, 0, 0, 0, 0],
                    borderColor: '#6366f1', backgroundColor: 'rgba(99, 102, 241, 0.1)', borderWidth: 3, fill: true, tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        const ctxGender = document.getElementById('genderChart').getContext('2d');
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'], datasets: [{ data: [<?php echo $males; ?>, <?php echo $females; ?>], backgroundColor: ['#3b82f6', '#ec4899'], borderWidth: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom' } } }
        });

        const themeToggle = document.getElementById('themeToggle');
        themeToggle.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const target = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', target);
            localStorage.setItem('theme', target);
            themeToggle.querySelector('i').className = target === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        });
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            themeToggle.querySelector('i').className = 'fa-solid fa-sun';
        }
    </script>
<script>
        function updateTime() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; 
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            document.getElementById('liveTime').innerText = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>
