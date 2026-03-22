<?php session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit();
}
include 'api/data_loader.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Personal Info</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #a5b4fc;
            --primary-dark: #4338ca;
            --secondary-color: #10b981;
            --bg-color: #f4f7fe;
            --surface-color: rgba(255, 255, 255, 0.85);
            --text-main: #1e293b;
            --text-muted: #64748b;
            --sidebar-width: 280px;
            --header-height: 80px;
            --radius-lg: 20px;
            --radius-md: 14px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.05);
            --shadow-hover: 0 20px 40px -10px rgba(99,102,241,0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

        body {
            background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

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

        .main-content {
            flex-grow: 1; margin-left: var(--sidebar-width);
            min-height: 100vh; display: flex; flex-direction: column;
        }

        .header {
            height: var(--header-height);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 3rem; position: sticky; top: 0; z-index: 50;
            backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
        }

        .search-bar {
            background: white; padding: 10px 20px; border-radius: 99px;
            display: flex; align-items: center; color: var(--text-muted);
            width: 300px; border: 1px solid rgba(0,0,0,0.05);
        }

        .user-profile {
            display: flex; align-items: center; gap: 15px; cursor: pointer;
            padding: 8px 16px; background: white; border-radius: 99px;
            border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s;
        }
        .user-profile:hover { box-shadow: var(--shadow-hover); }

        .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;
        }

        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2.5rem; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.5rem; }

        .data-table-container {
            background: var(--surface-color); backdrop-filter: blur(10px);
            border-radius: var(--radius-lg); box-shadow: var(--shadow-soft);
            overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.8);
            padding: 1.5rem;
        }
        .table-header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn {
            background: var(--primary-color); color: white; border: none;
            padding: 0.8rem 1.6rem; border-radius: 99px; font-weight: 600;
            cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 10px;
        }
        .btn:hover { transform: translateY(-2px); background: var(--primary-dark); }
        .btn-view {
            background: rgba(99, 102, 241, 0.1); color: var(--primary-color);
            border: none; padding: 0.6rem 1.2rem; border-radius: 12px;
            font-weight: 600; cursor: pointer; transition: all 0.3s;
            display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem;
        }
        .btn-view:hover { background: var(--primary-color); color: white; transform: scale(1.05); }

        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; }
        .data-table tbody tr { transition: all 0.3s; background: white; border-radius: 16px; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        /* Modal */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);
            z-index: 1000; display: none; align-items: center; justify-content: center;
            opacity: 0; transition: all 0.3s;
        }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 800px; border-radius: 24px; padding: 3rem; overflow-y: auto; max-height: 90vh; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .modal-header h2 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }

        /* Success Modal */
        .success-modal-card {
            background: white; width: 100%; max-width: 480px; border-radius: 28px;
            padding: 3rem; box-shadow: 0 25px 60px -12px rgba(0,0,0,0.3);
            text-align: center; animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes popIn {
            from { transform: scale(0.7); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }
        .success-icon {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 12px 30px rgba(16,185,129,0.35);
            animation: pulse-green 2s ease-in-out infinite;
        }
        @keyframes pulse-green {
            0%,100% { box-shadow: 0 12px 30px rgba(16,185,129,0.35); }
            50%      { box-shadow: 0 12px 40px rgba(16,185,129,0.6); }
        }
        .success-icon i { font-size: 2rem; color: white; }
        .success-title { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
        .success-subtitle { color: #64748b; font-size: 0.95rem; margin-bottom: 2rem; }
        .student-id-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white; font-size: 2rem; font-weight: 800;
            letter-spacing: 2px; padding: 0.8rem 2.5rem;
            border-radius: 16px; margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(99,102,241,0.4);
        }
        .id-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.4rem; }
        .success-note { font-size: 0.85rem; color: #94a3b8; margin-bottom: 2rem; }
        :root[data-theme="dark"] .success-modal-card { background: #1e293b; }
        :root[data-theme="dark"] .success-title { color: #f8fafc; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #64748b; margin-left: 4px; }
        .form-group input, .form-group select { padding: 0.9rem 1.2rem; border-radius: 14px; border: 1.5px solid #e2e8f0; font-size: 0.95rem; transition: all 0.3s; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        
        .modal-footer { margin-top: 3rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #f1f5f9; padding-top: 2rem; }
        .btn-modal { background: var(--primary-color); color: white; border: none; padding: 1rem 2rem; border-radius: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-modal:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .btn-ghost { background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; padding: 1rem 2rem; border-radius: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-ghost:hover { background: #f1f5f9; }

        /* Info Display System */
        .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 2rem; }
        .info-item { display: flex; flex-direction: column; gap: 6px; }
        .info-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 1.05rem; font-weight: 600; color: var(--text-main); }
        .info-card-section { background: #f8fafc; border-radius: 18px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #f1f5f9; }
        .section-tag { display: inline-block; padding: 4px 12px; background: var(--primary-color); color: white; border-radius: 8px; font-size: 0.7rem; font-weight: 800; margin-bottom: 1rem; text-transform: uppercase; }

        /* Dark Mode Support */
        :root[data-theme="dark"] {
            --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85);
            --text-main: #f8fafc; --text-muted: #94a3b8;
        }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .search-bar, :root[data-theme="dark"] .user-profile,
        :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card,
        :root[data-theme="dark"] .info-card-section {
            background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main);
        }
        :root[data-theme="dark"] .form-group input, :root[data-theme="dark"] .form-group select { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
        :root[data-theme="dark"] .btn-ghost { background: #0f172a; color: white; border-color: rgba(255,255,255,0.1); }
        :root[data-theme="dark"] .btn-view { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; }
        :root[data-theme="dark"] .info-label { color: #64748b; }

        .theme-toggle {
            background: white; border: 1px solid rgba(0,0,0,0.05); font-size: 1.1rem;
            display: flex; justify-content: center; align-items: center;
            width: 44px; height: 44px; border-radius: 50%; cursor: pointer; margin-right: 15px;
        }
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
        <div class="sidebar-brand">
            <i class="fa-solid fa-graduation-cap"></i>
            <h2>Registrar SIS<span>Management Portal</span></h2>
        </div>
        <ul class="nav-list">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="Personal_info.php" class="nav-link active"><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
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
                <h1><i class="fa-solid fa-users" style="color: var(--primary-color); margin-right: 15px;"></i>Personal Info Database</h1>
                <p>Core student profiles and comprehensive demographic records.</p>
            </div>
            
            <div class="data-table-container">
                <div class="table-header-controls">
                    <h3 style="font-size: 1.25rem; font-weight: 700;">Student Records</h3>
                    <button class="btn" onclick="openAddModal()"><i class="fa-solid fa-user-plus"></i> Add New Record</button>
                </div>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr><th>Student ID</th><th>Student Name</th><th style="text-align: right;">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mock_info)): ?>
                            <tr><td colspan="3" style="text-align: center; padding: 3rem; color: var(--text-muted);"><i class="fa-solid fa-folder-open" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>No student records found.</td></tr>
                            <?php
else: ?>
                            <?php foreach ($mock_info as $row): ?>
                            <tr>
                                <td><span style="font-weight: 700; color: var(--primary-dark);"><?php echo $row["student_id"]; ?></span></td>
                                <td><?php echo $row["student_name"]; ?></td>
                                <td style="text-align: right;">
                                    <button class="btn-view" onclick="viewDetails('<?php echo $row['student_id']; ?>')">
                                        <i class="fa-solid fa-eye"></i> View Information
                                    </button>
                                </td>
                            </tr>
                            <?php
    endforeach; ?>
                            <?php
endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal: Add New Record -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Register New Student</h2>
                <button class="btn-ghost" style="padding: 10px; border-radius: 50%;" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="api/add_record.php" method="POST">
                <input type="hidden" name="action" value="add_student">

                <!-- Student ID Read-only Field -->
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="display:flex; align-items:center; gap:6px;">
                        <i class="fa-solid fa-id-badge" style="color:var(--primary-color);"></i>
                        Student ID
                    </label>
                    <div style="position:relative;">
                        <input type="text" value="Auto-generated (e.g., 2026-0001)" readonly
                               style="width:100%; background:#f0f1fe; border:1.5px solid rgba(99,102,241,0.35); color:var(--primary-color); font-weight:700; border-radius:14px; padding:0.9rem 1.2rem 0.9rem 3rem; cursor:not-allowed; letter-spacing:0.5px;">
                        <i class="fa-solid fa-lock" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--primary-light); font-size:0.85rem;"></i>
                    </div>
                    <span style="font-size:0.75rem; color:var(--text-muted); margin-left:4px;">
                        <i class="fa-solid fa-circle-info" style="color:#6366f1; margin-right:3px;"></i>
                        The system will generate this automatically — you cannot edit it.
                    </span>
                </div>

                <div class="form-grid">
                    <div class="form-group"><label>First Name</label><input type="text" name="first_name" placeholder="John" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="last_name" placeholder="Doe" required></div>
                    <div class="form-group"><label>Middle Name</label><input type="text" name="middle_name" placeholder="Santos"></div>
                    <div class="form-group"><label>Suffix (Optional)</label><input type="text" name="suffix" placeholder="Jr., III, etc."></div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" required></div>
                    <div class="form-group"><label>Place of Birth</label><input type="text" name="pob" placeholder="City/Province" required></div>
                    
                    <div class="form-group"><label>Gender</label>
                        <select name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group"><label>Civil Status</label>
                        <select name="civil_status" required>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Separated">Separated</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                    
                    <div class="form-group"><label>Nationality</label><input type="text" name="nationality" placeholder="Filipino" required></div>
                    <div class="form-group"><label>Contact Number</label><input type="text" name="contact_number" placeholder="09123456789" required></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Present Address</label><input type="text" name="present_address" placeholder="Current Residence" required></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Permanent Address</label><input type="text" name="permanent_address" placeholder="Home Province/Address" required></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Email Address</label><input type="email" name="email" placeholder="juan@email.com" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn-modal"><i class="fa-solid fa-floppy-disk" style="margin-right:8px;"></i>Save Student Record</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Student ID Success -->
    <div class="modal-overlay" id="successModal">
        <div class="success-modal-card">
            <div class="success-icon"><i class="fa-solid fa-check"></i></div>
            <div class="success-title">Student Record Saved!</div>
            <div class="success-subtitle">The student has been registered successfully.</div>

            <!-- Read-only Student ID field -->
            <div style="margin-bottom:1.2rem;">
                <div class="id-label" style="margin-bottom:0.5rem;">Student ID</div>
                <div style="position:relative;">
                    <input type="text" id="generatedStudentID" readonly
                           value="—"
                           style="width:100%; text-align:center; font-size:1.6rem; font-weight:800; letter-spacing:3px;
                                  padding:0.85rem 1rem; border-radius:14px;
                                  background: linear-gradient(135deg,rgba(99,102,241,0.1),rgba(67,56,202,0.07));
                                  border:2px solid rgba(99,102,241,0.4); color:var(--primary-dark);
                                  cursor:default;">
                    <i class="fa-solid fa-lock" style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); color:var(--primary-light); font-size:0.85rem;"></i>
                </div>
            </div>

            <!-- System message panel -->
            <div style="background:#f0fdf4; border:1.5px solid #bbf7d0; border-radius:14px; padding:1rem 1.2rem; margin-bottom:1.5rem; text-align:left;">
                <div style="font-size:0.75rem; font-weight:700; color:#16a34a; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                    <i class="fa-solid fa-terminal" style="margin-right:5px;"></i>System Message
                </div>
                <div style="font-size:0.9rem; color:#166534; font-weight:500; line-height:1.6;">
                    Student record saved successfully<br>
                    Student ID: <strong id="systemMsgID" style="color:#15803d;">—</strong>
                </div>
            </div>

            <div class="success-note"><i class="fa-solid fa-circle-info" style="margin-right:5px; color:#6366f1;"></i>Note this ID — it will be required for all subsequent modules.</div>
            <button class="btn-modal" style="width:100%; height:55px; font-size:1rem; border-radius:18px;" onclick="proceedToAccount()">
                <i class="fa-solid fa-arrow-right" style="margin-right:8px;"></i>Proceed to Next Step
            </button>
        </div>
    </div>

    <!-- Modal: Step 2 - Create Account -->
    <div class="modal-overlay" id="accountModal">
        <div class="modal-card" style="max-width: 500px;">
            <div class="modal-header">
                <div>
                    <span class="section-tag" style="background:#fef3c7; color:#d97706;">Step 2</span>
                    <h2>Create Student Account</h2>
                </div>
                <button class="btn-ghost" style="padding: 10px; border-radius: 50%;" onclick="closeModal('accountModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:16px; padding:1.2rem; margin-bottom:2rem; display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; border-radius:10px; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Registration ID</div>
                    <div style="font-weight:800; color:#17388A; font-size:1.1rem;" id="acc_student_id_display">—</div>
                </div>
            </div>

            <form action="api/add_record.php" method="POST">
                <input type="hidden" name="action" value="create_account">
                <input type="hidden" name="student_id" id="acc_student_id_input">
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Assigned Username</label>
                    <div style="position:relative;">
                        <i class="fa-solid fa-user" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem;"></i>
                        <input type="text" name="username" id="suggested_username" placeholder="e.g. john_doe_2026" required
                               style="padding-left:2.8rem; width:100%;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label>Assigned Password</label>
                    <div style="position:relative;">
                        <i class="fa-solid fa-key" style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem;"></i>
                        <input type="password" name="password" id="student_pass" placeholder="Enter secure password" required
                               style="padding-left:2.8rem; width:100%;">
                        <button type="button" onclick="const p=document.getElementById('student_pass'); p.type = p.type==='password'?'text':'password';"
                                style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="modal-footer" style="margin-top:0; padding-top:0; border:none;">
                    <button type="submit" class="btn-modal" style="width:100%; height:55px; border-radius:18px;">
                        <i class="fa-solid fa-user-check" style="margin-right:8px;"></i>Finalize Account & Access
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: View Information -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Full Student Information</h2>
                <button class="btn-ghost" style="padding: 10px; border-radius: 50%;" onclick="closeModal('viewModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="info-card-section">
                <span class="section-tag">Primary Records</span>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Student ID</span><span class="info-value" id="view_student_id"></span></div>
                    <div class="info-item"><span class="info-label">Full Name</span><span class="info-value" id="view_name"></span></div>
                    <div class="info-item"><span class="info-label">Gender</span><span class="info-value" id="view_gender"></span></div>
                    <div class="info-item"><span class="info-label">Date of Birth</span><span class="info-value" id="view_dob"></span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Place of Birth</span><span class="info-value" id="view_pob"></span></div>
                </div>
            </div>

            <div class="info-card-section">
                <span class="section-tag">Personal Details</span>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Civil Status</span><span class="info-value" id="view_civil"></span></div>
                    <div class="info-item"><span class="info-label">Nationality</span><span class="info-value" id="view_nationality"></span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Present Address</span><span class="info-value" id="view_present_address"></span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Permanent Address</span><span class="info-value" id="view_permanent_address"></span></div>
                </div>
            </div>

            <div class="info-card-section">
                <span class="section-tag">Contact Information</span>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Contact Number</span><span class="info-value" id="view_contact"></span></div>
                    <div class="info-item"><span class="info-label">Email Address</span><span class="info-value" id="view_email"></span></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-ghost" onclick="closeModal('viewModal')">Close View</button>
                <button type="button" class="btn-modal" onclick="closeModal('viewModal'); openAddModal();"><i class="fa-solid fa-pen"></i> Edit Record</button>
            </div>
        </div>
    </div>

    <script>
        // Store all data in JS for quick viewing
        const students = <?php echo json_encode($mock_info); ?>;

        function openAddModal() { document.getElementById('addModal').classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        // Show success modal with new student ID if redirected back with new_id param
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('new_id')) {
                const id = params.get('new_id');
                document.getElementById('generatedStudentID').value = id;
                document.getElementById('systemMsgID').textContent = id;
                document.getElementById('successModal').classList.add('active');
            }
            if (params.has('account_created')) {
                alert('Student account successfully created! They can now log in to the student portal.');
            }
            // Clean URL without reloading
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, '', cleanUrl);
        })();

        let current_new_id = "";

        function proceedToAccount() {
            const id = document.getElementById('generatedStudentID').value;
            current_new_id = id;
            
            // Set values in account modal
            document.getElementById('acc_student_id_display').innerText = id;
            document.getElementById('acc_student_id_input').value = id;
            
            // Suggest a username based on ID (simplified)
            document.getElementById('suggested_username').value = "student_" + id.replace('-', '');
            
            // Switch modals
            closeModal('successModal');
            document.getElementById('accountModal').classList.add('active');
        }

        function viewDetails(id) {
            const student = students.find(s => s.student_id === id);
            if (student) {
                document.getElementById('view_student_id').innerText = student.student_id;
                document.getElementById('view_name').innerText = student.student_name;
                document.getElementById('view_gender').innerText = student.gender || 'Not set';
                document.getElementById('view_dob').innerText = student.dob || 'Not set';
                document.getElementById('view_pob').innerText = student.pob || 'Not set';
                document.getElementById('view_civil').innerText = student.civil_status || 'Single';
                document.getElementById('view_nationality').innerText = student.nationality || 'Filipino';
                document.getElementById('view_present_address').innerText = student.present_address || 'Not set';
                document.getElementById('view_permanent_address').innerText = student.permanent_address || 'Not set';
                document.getElementById('view_contact').innerText = student.contact_number || 'Not set';
                document.getElementById('view_email').innerText = student.email || 'Not set';
                
                document.getElementById('viewModal').classList.add('active');
            }
        }

        window.onclick = function(e) { 
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        }

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