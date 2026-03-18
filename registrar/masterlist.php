<?php session_start(); if (!isset($_SESSION["admin_id"])) { header("Location: ../index.php"); exit(); } include "api/data_loader.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Student Masterlist</title>
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

        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .header { height: var(--header-height); display: flex; align-items: center; justify-content: space-between; padding: 0 3rem; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.3); position: sticky; top: 0; z-index: 50; }
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; padding: 8px 16px; background: white; border-radius: 99px; border: 1px solid rgba(0,0,0,0.05); }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2rem; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; }

        /* Filter Section */
        .filter-card { background: white; border-radius: var(--radius-lg); padding: 1.5rem 2rem; box-shadow: var(--shadow-soft); margin-bottom: 2rem; border: 1px solid rgba(0,0,0,0.02); }
        .filter-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) auto; gap: 1rem; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-group label { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .filter-group select { padding: 0.7rem 1rem; border-radius: 10px; border: 1.5px solid #e2e8f0; font-family: inherit; font-size: 0.9rem; font-weight: 600; }
        .btn-generate { background: var(--primary-color); color: white; border: none; padding: 0.75rem 2rem; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
        .btn-generate:hover { background: var(--primary-dark); transform: scale(1.02); }

        /* Table Card */
        .table-card { background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; border: 1px solid rgba(0,0,0,0.02); }
        .table-header { padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .table-header h3 { font-size: 1.1rem; font-weight: 800; color: var(--text-main); }
        .output-actions { display: flex; gap: 10px; }
        .btn-action { padding: 0.5rem 1.2rem; border-radius: 8px; border: 1.5px solid #e2e8f0; background: white; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.3s; }
        .btn-action:hover { border-color: var(--primary-color); color: var(--primary-color); background: rgba(99, 102, 241, 0.05); }

        .data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .data-table th { padding: 1.2rem 2rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; background: #f8fafc; border-bottom: 2px solid #f1f5f9; }
        .data-table td { padding: 1.2rem 2rem; border-bottom: 1px solid #f8fafc; font-size: 0.95rem; font-weight: 500; }
        .data-table tbody tr:hover { background: #fdfdff; }
        
        .status-badge { padding: 0.3rem 0.8rem; border-radius: 99px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }

        /* Print Specifics */
        @media print {
            .sidebar, .header, .filter-card, .output-actions, .btn-generate { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .dashboard-body { padding: 0 !important; }
            .table-card { box-shadow: none !important; border: 1px solid #eee !important; }
            .print-only-header { display: block !important; text-align: center; margin-bottom: 2rem; }
            body { background: white !important; }
        }
        .print-only-header { display: none; }

        /* Dark Mode */
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .user-profile, :root[data-theme="dark"] .filter-card, :root[data-theme="dark"] .table-card, :root[data-theme="dark"] .btn-action { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .data-table th { background: #0f172a; color: var(--text-muted); }
        :root[data-theme="dark"] .data-table td { border-color: rgba(255,255,255,0.02); }
        :root[data-theme="dark"] select { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-graduation-cap"></i><h2>Registrar SIS<span>Management Portal</span></h2></div>
        <ul class="nav-list">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="Personal_info.php" class="nav-link"><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
            <li class="nav-item"><a href="Guardian&Emergency_Contact.php" class="nav-link"><i class="fa-solid fa-hands-holding-child"></i><span>Guardian & Contact</span></a></li>
            <li class="nav-item"><a href="Academic_history.php" class="nav-link"><i class="fa-solid fa-book-open"></i><span>Academic History</span></a></li>
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link"><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
            <li class="nav-item"><a href="id_generator.php" class="nav-link"><i class="fa-solid fa-id-card"></i><span>ID Processing</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link"><i class="fa-solid fa-wifi"></i><span>RFID / QR Module</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link"><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link"><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link"><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link active"><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div style="font-weight: 700; color: var(--text-muted); font-size: 0.9rem;">
                <i class="fa-solid fa-calendar-day"></i> <?php echo date('F d, Y'); ?> | <span id="liveTime" style="font-weight: 600;"></span>
            </div>
            <div style="display: flex; align-items: center;">
                <div class="theme-toggle" id="themeToggle" style="margin-right:20px; cursor:pointer; font-size:1.2rem;"><i class="fa-solid fa-moon"></i></div>
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
                <h1>Student Masterlist Generator</h1>
                <p>Compile and export official student records for various administrative purposes.</p>
            </div>

            <!-- Filter Section -->
            <div class="filter-card">
                <form action="masterlist.php" method="GET" class="filter-form">
                    <div class="filter-group">
                        <label>Grade Level</label>
                        <select name="grade">
                            <option value="">All Levels</option>
                            <option value="Grade 7" <?php if(isset($_GET['grade']) && $_GET['grade'] == 'Grade 7') echo 'selected'; ?>>Grade 7</option>
                            <option value="Grade 8" <?php if(isset($_GET['grade']) && $_GET['grade'] == 'Grade 8') echo 'selected'; ?>>Grade 8</option>
                            <option value="Grade 9" <?php if(isset($_GET['grade']) && $_GET['grade'] == 'Grade 9') echo 'selected'; ?>>Grade 9</option>
                            <option value="Grade 10" <?php if(isset($_GET['grade']) && $_GET['grade'] == 'Grade 10') echo 'selected'; ?>>Grade 10</option>
                            <option value="BSIT - 1" <?php if(isset($_GET['grade']) && $_GET['grade'] == 'BSIT - 1') echo 'selected'; ?>>BSIT - 1</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Section</label>
                        <select name="section">
                            <option value="">All Sections</option>
                            <option value="Euler" <?php if(isset($_GET['section']) && $_GET['section'] == 'Euler') echo 'selected'; ?>>Euler</option>
                            <option value="Einstein" <?php if(isset($_GET['section']) && $_GET['section'] == 'Einstein') echo 'selected'; ?>>Einstein</option>
                            <option value="Newton" <?php if(isset($_GET['section']) && $_GET['section'] == 'Newton') echo 'selected'; ?>>Newton</option>
                            <option value="1-A" <?php if(isset($_GET['section']) && $_GET['section'] == '1-A') echo 'selected'; ?>>1-A</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="Active" <?php if(isset($_GET['status']) && $_GET['status'] == 'Active') echo 'selected'; ?>>Active</option>
                            <option value="Inactive" <?php if(isset($_GET['status']) && $_GET['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                            <option value="Graduated" <?php if(isset($_GET['status']) && $_GET['status'] == 'Graduated') echo 'selected'; ?>>Graduated</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-generate"><i class="fa-solid fa-list-check"></i> Generate List</button>
                </form>
            </div>

            <!-- Masterlist Table Card -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Generated Masterlist</h3>
                    <div class="output-actions">
                        <button class="btn-action" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
                        <button class="btn-action" onclick="exportCSV()"><i class="fa-solid fa-file-excel"></i> Export Excel</button>
                    </div>
                </div>

                <div class="print-only-header">
                    <h1 style="font-size: 1.5rem; margin-bottom: 5px;">Your School Name</h1>
                    <h2 style="font-size: 1.1rem; margin-bottom: 15px;">Official Student Masterlist</h2>
                    <p style="font-size: 0.9rem; color: #666;">Generated on: <?php echo date('F d, Y'); ?> | <span id="liveTime" style="font-weight: 600;"></span></p>
                    <hr style="margin: 20px 0; border: 0.5px solid #eee;">
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mock_masterlist as $row): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;"><?php echo $row["no"]; ?></td>
                            <td style="font-weight: 700; color: var(--primary-dark);"><?php echo $row["student_id"]; ?></td>
                            <td style="font-weight: 700;"><?php echo $row["name"]; ?></td>
                            <td><?php echo $row["course_grade"]; ?></td>
                            <td><?php echo $row["section"]; ?></td>
                            <td>
                                <span class="status-badge <?php echo ($row['status'] === 'Active') ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $row["status"]; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mock_masterlist)): ?>
                        <tr><td colspan="6" style="text-align:center; padding: 4rem; color:var(--text-muted);">No records found matching your filters.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
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

        function exportCSV() {
            let csv = [];
            let rows = document.querySelectorAll(".data-table tr");
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) row.push('"' + cols[j].innerText.trim().replace(/"/g, '""') + '"');
                csv.push(row.join(","));        
            }
            let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
            let downloadLink = document.createElement("a");
            downloadLink.download = "Student_Masterlist_" + new Date().toISOString().slice(0,10) + ".csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
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