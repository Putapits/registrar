<?php session_start(); if (!isset($_SESSION["admin_id"])) { header("Location: ../index.php"); exit(); } include "api/data_loader.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Status Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1; --primary-light: #a5b4fc; --primary-dark: #4338ca;
            --secondary-color: #10b981; --bg-color: #f4f7fe; --surface-color: rgba(255, 255, 255, 0.85);
            --text-main: #1e293b; --text-muted: #64748b; --sidebar-width: 280px;
            --header-height: 80px; --radius-lg: 20px; --radius-md: 14px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.05); --shadow-hover: 0 20px 40px -10px rgba(99,102,241,0.15);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Outfit", sans-serif; }
        body { background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%); color: var(--text-main); display: flex; min-height: 100vh; }
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-brand { padding: 0 2rem 2rem 2rem; display: flex; align-items: center; gap: 15px; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary-color); background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 12px; }
        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }
        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }
        .nav-list { list-style: none; padding: 0 1.2rem; }
        .nav-item { margin-bottom: 0.4rem; }
        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: var(--radius-md); transition: all 0.3s; gap: 14px; font-weight: 500; }
        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }
        .nav-link:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link.active { background: var(--primary-color); color: white; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25); }
        .nav-link.active i { color: white; }
        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .header { height: var(--header-height); display: flex; align-items: center; justify-content: space-between; padding: 0 3rem; position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); }
        .search-bar { background: white; padding: 10px 20px; border-radius: 99px; display: flex; align-items: center; color: var(--text-muted); width: 300px; border: 1px solid rgba(0,0,0,0.05); }
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; padding: 8px 16px; background: white; border-radius: 99px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; }
        .user-profile:hover { box-shadow: var(--shadow-hover); }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2.5rem; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.5rem; }
        .data-table-container { background: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.8); padding: 1.5rem; }
        .table-header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn { background: var(--primary-color); color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 99px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 10px; }
        .btn:hover { transform: translateY(-2px); background: var(--primary-dark); }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; }
        .data-table tbody tr { transition: all 0.3s; background: white; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .status-badge { padding: 0.35rem 1rem; border-radius: 99px; font-size: 0.8rem; font-weight: 700; background: #e0e7ff; color: var(--primary-dark); }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 800px; border-radius: 20px; padding: 2.5rem; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #64748b; }
        .form-group input, .form-group select { padding: 0.8rem 1rem; border-radius: 14px; border: 1px solid #e2e8f0; }
        .modal-footer { margin-top: 2.5rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .btn-modal { background: var(--primary-color); color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 99px; font-weight: 600; cursor: pointer; }
        .btn-ghost { background: transparent; border: 1px solid #e2e8f0; color: #1e293b; padding: 0.8rem 1.6rem; border-radius: 99px; cursor: pointer; }
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .search-bar, :root[data-theme="dark"] .user-profile, :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .form-group input, :root[data-theme="dark"] .form-group select { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
        .theme-toggle { background: white; border: 1px solid rgba(0,0,0,0.05); font-size: 1.1rem; display: flex; justify-content: center; align-items: center; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; margin-right: 15px; transition: all 0.3s; }
        :root[data-theme="dark"] .theme-toggle { background: #1e293b; color: #f8fafc; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem("theme");
            if (theme === "dark" || (!theme && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
                document.documentElement.setAttribute("data-theme", "dark");
            }
        })();
    </script>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-graduation-cap"></i><h2>Registrar SIS<span>Management Portal</span></h2></div>
        <ul class="nav-list">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="Personal_info.php" class="nav-link "><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
            <li class="nav-item"><a href="Guardian&Emergency_Contact.php" class="nav-link "><i class="fa-solid fa-hands-holding-child"></i><span>Guardian & Contact</span></a></li>
            <li class="nav-item"><a href="Academic_history.php" class="nav-link "><i class="fa-solid fa-book-open"></i><span>Academic History</span></a></li>
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link "><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
            <li class="nav-item"><a href="id_generator.php" class="nav-link "><i class="fa-solid fa-id-card"></i><span>ID Generation</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link "><i class="fa-solid fa-wifi"></i><span>RFID Scanner</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link "><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link active"><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link "><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link "><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>
    <main class="main-content">
        <header class="header">
            <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i> <span style="margin-left: 10px; font-size: 0.9rem;">Search records...</span></div>
            <div style="display: flex; align-items: center;">
                <button id="themeToggle" class="theme-toggle"><i class="fa-solid fa-moon"></i></button>
                <div class="user-profile">
                    <div class="user-info" style="text-align: right;">
                        <h4>Registrar</h4><p><?php echo htmlspecialchars($_SESSION["admin_user"]); ?></p>
                    </div>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION["admin_user"], 0, 1)); ?></div>
                </div>
            </div>
        </header>
        <div class="dashboard-body">
            <div class="page-title">
                <h1><i class="fa-solid fa-chart-line" style="color: var(--primary-color); margin-right: 15px;"></i>Status Tracker</h1>
                <p>Live tracking of registrar processes and status updates.</p>
            </div>
            <div class="data-table-container">
                <div class="table-header-controls">
                    <h3 style="font-size: 1.25rem; font-weight: 700;">Data Overview</h3>
                    <button class="btn" onclick="openModal()"><i class="fa-solid fa-plus"></i> Add New Record</button>
                </div>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                    <tr><th>Student ID</th><th>Name</th><th>Academic Status</th><th>Enrollment Status</th><th>Clearance</th></tr>
                </thead>
                        <tbody>
                    <?php if (empty($mock_tracker)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>
                            No status tracking records found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($mock_tracker as $row): ?>
                    <tr><td><?php echo $row["student_id"]; ?></td><td><?php echo $row["name"]; ?></td><td><?php echo $row["academic_status"]; ?></td><td><?php echo $row["enrollment_status"]; ?></td><td><?php echo $row["clearance"]; ?></td></tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <div class="modal-overlay" id="addModal">
        <div class="modal-card">
            <div class="modal-header"><h2>Add New Record - Status Tracker</h2><button class="close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button></div>
            <form action="api/add_record.php" method="POST">
                <input type="hidden" name="action" value="add_status_tracker">
                <div class="form-grid">
                    <div class="form-group"><label>Student ID</label><input type="text" name="student_id" placeholder="e.g. 2024-0001" required></div>
                    <div class="form-group"><label>Full Name</label><input type="text" name="student_name" placeholder="Doe, John A." required></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Additional Details</label><input type="text" name="details" placeholder="Enter relevant information..." required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn-ghost" onclick="closeModal()">Cancel</button><button type="submit" class="btn-modal">Save Record</button></div>
            </form>
        </div>
    </div>
    <script>
        function openModal() { document.getElementById("addModal").classList.add("active"); }
        function closeModal() { document.getElementById("addModal").classList.remove("active"); }
        window.onclick = function(e) { if (e.target == document.getElementById("addModal")) closeModal(); }
        const themeToggle = document.getElementById("themeToggle");
        const themeIcon = themeToggle.querySelector("i");
        const updateIcon = (theme) => {
            if (theme === "dark") { themeIcon.classList.replace("fa-moon", "fa-sun"); }
            else { themeIcon.classList.replace("fa-sun", "fa-moon"); }
        };
        updateIcon(document.documentElement.getAttribute("data-theme"));
        themeToggle.addEventListener("click", () => {
            let newTheme = document.documentElement.getAttribute("data-theme") === "dark" ? "light" : "dark";
            document.documentElement.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            updateIcon(newTheme);
        });
    </script>
</body>
</html>