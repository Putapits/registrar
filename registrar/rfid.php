<?php session_start(); if (!isset($_SESSION["admin_id"])) { header("Location: ../index.php"); exit(); } include "api/data_loader.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - RFID & QR Module</title>
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
        
        /* Sidebar & Header inherited from dash for consistency */
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
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; padding: 8px 16px; background: white; border-radius: 99px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2.5rem; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.5rem; }

        .data-table-container { background: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.8); padding: 1.5rem; }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; vertical-align: middle; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; }
        .data-table tbody tr { transition: all 0.3s; background: white; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        .status-badge {
            padding: 0.4rem 1rem; border-radius: 99px; font-size: 0.8rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .status-pending { background: #fee2e2; color: #991b1b; }
        .status-partial { background: #e0e7ff; color: #3730a3; }
        .status-complete { background: #dcfce7; color: #166534; }

        .btn-process {
            background: var(--primary-color); color: white; border: none;
            padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600;
            cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;
        }
        .btn-process:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .icon-check { color: #10b981; font-size: 1.1rem; }
        .icon-cross { color: #ef4444; font-size: 1.1rem; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 500px; border-radius: 24px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .modal-header h2 { font-size: 1.6rem; font-weight: 800; }
        .close-btn { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; }

        .student-display { background: #f8fafc; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid #f1f5f9; }
        .display-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
        .display-key { color: var(--text-muted); font-size: 0.85rem; font-weight: 600; }
        .display-val { font-weight: 700; color: var(--text-main); }

        .processing-grid { display: grid; gap: 1rem; }
        .action-card {
            border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 1.2rem;
            display: flex; align-items: center; gap: 1rem; cursor: pointer;
            transition: all 0.3s; background: white;
        }
        .action-card:hover { border-color: var(--primary-color); background: rgba(99, 102, 241, 0.02); }
        .action-card i { font-size: 1.5rem; color: var(--primary-color); }
        .action-desc h4 { font-size: 0.95rem; font-weight: 700; margin-bottom: 2px; }
        .action-desc p { font-size: 0.8rem; color: var(--text-muted); }

        .modal-footer { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; }
        .btn-save { background: var(--secondary-color); color: white; border: none; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-save:hover { background: #059669; transform: scale(1.02); }

        .input-hidden { position: absolute; opacity: 0; width: 0; height: 0; }

        /* Dark Mode Support */
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .user-profile, :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card, :root[data-theme="dark"] .student-display, :root[data-theme="dark"] .action-card { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .sidebar-brand i { background: rgba(99, 102, 241, 0.2); }
        :root[data-theme="dark"] .close-btn { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
    </style>
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
            <li class="nav-item"><a href="id_generator.php" class="nav-link "><i class="fa-solid fa-id-card"></i><span>ID Processing</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link active"><i class="fa-solid fa-wifi"></i><span>RFID / QR Module</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link "><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link "><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link "><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link "><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i> <span style="margin-left: 10px; font-size: 0.9rem;">Search students...</span></div>
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
                <h1><i class="fa-solid fa-qrcode" style="color: var(--primary-color); margin-right: 15px;"></i>RFID & QR Code Module</h1>
                <p>Assign digital identities and physical identification cards to existing student records.</p>
            </div>

            <div class="data-table-container">
                <div class="table-header-controls">
                    <h3 style="font-size: 1.25rem; font-weight: 700;">Student Identity Masterlist</h3>
                </div>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th style="text-align: center;">QR Code</th>
                                <th style="text-align: center;">RFID</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($mock_info as $row): 
                                $identity = $mock_identities[$row['student_id']] ?? null;
                                $has_qr = !empty($identity['qr_code']);
                                $has_rfid = !empty($identity['rfid_uid']);
                                $status = $identity['status'] ?? 'Pending';
                                
                                $status_class = 'status-pending';
                                if ($status === 'Complete') $status_class = 'status-complete';
                                elseif ($status === 'Partial') $status_class = 'status-partial';
                            ?>
                            <tr>
                                <td><span style="font-weight: 700;"><?php echo $row["student_id"]; ?></span></td>
                                <td><?php echo $row["student_name"]; ?></td>
                                <td style="text-align: center;">
                                    <?php echo $has_qr ? '<i class="fa-solid fa-circle-check icon-check"></i>' : '<i class="fa-solid fa-circle-xmark icon-cross"></i>'; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $has_rfid ? '<i class="fa-solid fa-circle-check icon-check"></i>' : '<i class="fa-solid fa-circle-xmark icon-cross"></i>'; ?>
                                </td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                                <td style="text-align: right;">
                                    <button class="btn-process" onclick="openProcessModal('<?php echo $row['student_id']; ?>', '<?php echo htmlspecialchars($row['student_name']); ?>', '<?php echo $identity['rfid_uid'] ?? ''; ?>', '<?php echo $identity['qr_code'] ?? ''; ?>')">
                                        <i class="fa-solid fa-id-card-clip"></i> <?php echo $status === 'Complete' ? 'View/Update' : 'Process'; ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal: Process Identity -->
    <div class="modal-overlay" id="processModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2 id="modalTitle">Process Student Identity</h2>
                <button class="close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form action="api/add_record.php" method="POST" id="processForm">
                <input type="hidden" name="action" value="assign_id">
                <input type="hidden" name="student_id" id="modal_student_id">
                <input type="hidden" name="qr_code" id="modal_qr_code">

                <div class="student-display">
                    <div class="display-row">
                        <span class="display-key">Student Name</span>
                        <span class="display-val" id="disp_name">Doe, John</span>
                    </div>
                    <div class="display-row">
                        <span class="display-key">Student ID</span>
                        <span class="display-val" id="disp_id">2026-0001</span>
                    </div>
                </div>

                <div class="processing-grid">
                    <div class="action-card" id="btnGenQR" onclick="generateQR()">
                        <i class="fa-solid fa-qrcode"></i>
                        <div class="action-desc">
                            <h4 id="qr_status_text">Generate QR Code</h4>
                            <p id="qr_sub_text">Create digital identity for mobile scans</p>
                        </div>
                        <div id="qr_check" style="display:none; margin-left:auto;"><i class="fa-solid fa-circle-check icon-check"></i></div>
                    </div>

                    <div class="action-card" onclick="document.getElementById('rfid_input').focus()">
                        <i class="fa-solid fa-wifi"></i>
                        <div class="action-desc" style="flex-grow: 1;">
                            <h4>Scan RFID Card</h4>
                            <p>Link a physical card to this student</p>
                            <input type="text" name="rfid_uid" id="rfid_input" placeholder="Tap card now..." 
                                   style="width: 100%; margin-top: 10px; padding: 10px; border-radius: 8px; border: 1.5px solid #e2e8f0; font-family: monospace; font-weight: 700;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn-save">Save Assignments</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openProcessModal(id, name, rfid, qr) {
            document.getElementById('modal_student_id').value = id;
            document.getElementById('disp_id').innerText = id;
            document.getElementById('disp_name').innerText = name;
            
            document.getElementById('rfid_input').value = rfid;
            document.getElementById('modal_qr_code').value = qr;

            if (qr) {
                document.getElementById('qr_status_text').innerText = "QR Code Generated";
                document.getElementById('qr_sub_text').innerText = "Path: " + qr;
                document.getElementById('qr_check').style.display = "block";
            } else {
                document.getElementById('qr_status_text').innerText = "Generate QR Code";
                document.getElementById('qr_sub_text').innerText = "Create digital identity for mobile scans";
                document.getElementById('qr_check').style.display = "none";
            }

            document.getElementById('processModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('processModal').classList.remove('active');
        }

        function generateQR() {
            const sid = document.getElementById('modal_student_id').value;
            // Mocking QR Generation Path
            const mockPath = "assets/qrcodes/" + sid + ".png";
            document.getElementById('modal_qr_code').value = mockPath;
            document.getElementById('qr_status_text').innerText = "QR Code Ready";
            document.getElementById('qr_sub_text').innerText = "Assigned: " + mockPath;
            document.getElementById('qr_check').style.display = "block";
        }

        const themeToggle = document.getElementById('themeToggle');
        themeToggle.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const target = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', target);
            localStorage.setItem('theme', target);
            themeToggle.querySelector('i').className = target === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        });

        // Initialize theme icon
        if (localStorage.getItem('theme') === 'dark') {
            themeToggle.querySelector('i').className = 'fa-solid fa-sun';
        }
    </script>
</body>
</html>