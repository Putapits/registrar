<?php session_start(); if (!isset($_SESSION["admin_id"])) { header("Location: ../index.php"); exit(); } include "api/data_loader.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Document Requests</title>
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
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; padding: 8px 16px; background: white; border-radius: 99px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.2rem; }

        .btn-primary { background: var(--primary-color); color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); background: var(--primary-dark); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); }

        .data-table-container { background: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 1rem; border: 1px solid rgba(255,255,255,0.8); padding: 1.5rem; }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; vertical-align: middle; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; }
        .data-table tbody tr { transition: all 0.3s; background: white; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        .status-badge {
            padding: 0.4rem 1rem; border-radius: 99px; font-size: 0.78rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .status-pending { background: #fee2e2; color: #991b1b; }
        .status-processing { background: #e0e7ff; color: #3730a3; }
        .status-ready { background: #fef3c7; color: #92400e; }
        .status-released { background: #dcfce7; color: #166534; }
        .status-rejected { background: #f1f5f9; color: #475569; }

        .btn-action {
            background: rgba(99, 102, 241, 0.08); color: var(--primary-color);
            border: none; padding: 0.6rem 1.1rem; border-radius: 10px;
            font-weight: 700; cursor: pointer; transition: all 0.3s;
            font-size: 0.8rem; display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-action:hover { background: var(--primary-color); color: white; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 550px; border-radius: 24px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .modal-header h2 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; }
        .close-btn { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; }

        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.2rem; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .form-group input, .form-group select, .form-group textarea { padding: 0.8rem 1rem; border-radius: 12px; border: 1.5px solid #e2e8f0; font-family: inherit; font-size: 0.95rem; }
        .form-group input:focus { border-color: var(--primary-color); outline: none; }

        .modal-footer { margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .btn-submit { background: var(--primary-color); color: white; border: none; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 700; cursor: pointer; }
        .btn-cancel { background: transparent; border: 1px solid #e2e8f0; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; cursor: pointer; }

        /* Processing Modal Specifics */
        .request-info { background: #f8fafc; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; border: 1px solid #f1f5f9; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 0.6rem; }
        .info-key { color: var(--text-muted); font-size: 0.85rem; font-weight: 600; }
        .info-val { font-weight: 700; color: var(--text-main); }
        
        .status-button-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; }
        .btn-status-update {
            padding: 1rem; border-radius: 12px; border: 1.5px solid #e2e8f0; background: white;
            font-weight: 700; cursor: pointer; transition: all 0.2s; text-align: left;
            display: flex; align-items: center; gap: 12px; font-size: 0.9rem;
        }
        .btn-status-update:hover { border-color: var(--primary-color); background: rgba(99, 102, 241, 0.03); transform: translateX(4px); }
        .btn-status-update.btn-approve { border-color: var(--primary-light); color: var(--primary-dark); }
        .btn-status-update.btn-processing { border-color: #a5b4fc; color: #4338ca; }
        .btn-status-update.btn-ready { border-color: #fde68a; color: #92400e; }
        .btn-status-update.btn-released { border-color: #bbf7d0; color: #166534; }
        .btn-status-update.btn-reject { border-color: #fecaca; color: #991b1b; }

        /* Dark Mode Support */
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .user-profile, :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card, :root[data-theme="dark"] .request-info, :root[data-theme="dark"] .btn-status-update { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .form-group input, :root[data-theme="dark"] .form-group select, :root[data-theme="dark"] .form-group textarea { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
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
            <li class="nav-item"><a href="rfid.php" class="nav-link "><i class="fa-solid fa-wifi"></i><span>RFID / QR Module</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link active"><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link "><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link "><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link "><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i> <span style="margin-left: 10px; font-size: 0.9rem;">Search requests...</span></div>
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
                <div>
                    <h1><i class="fa-solid fa-file-invoice" style="color: var(--primary-color); margin-right: 15px;"></i>Document Requests</h1>
                    <p>Process and track applications for official school documents.</p>
                </div>
                <button class="btn-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> New Request</button>
            </div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RID</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Document Type</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mock_documents as $row): 
                            $status = $row['status'] ?? 'Pending';
                            $status_class = 'status-pending';
                            if ($status === 'Processing') $status_class = 'status-processing';
                            elseif ($status === 'Ready for Pickup') $status_class = 'status-ready';
                            elseif ($status === 'Released') $status_class = 'status-released';
                            elseif ($status === 'Rejected') $status_class = 'status-rejected';
                        ?>
                        <tr>
                            <td><span style="font-weight:700; color:var(--text-muted);"><?php echo str_pad($row['request_id'], 3, '0', STR_PAD_LEFT); ?></span></td>
                            <td><span style="font-weight:700; color:var(--primary-dark);"><?php echo $row["student_id"]; ?></span></td>
                            <td><?php echo $row["student_name"]; ?></td>
                            <td><span style="font-weight:600;"><?php echo $row["document_type"]; ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($row["request_date"])); ?></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                            <td style="text-align: right;">
                                <button class="btn-action" onclick="openProcessModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                    <i class="fa-solid fa-gears"></i> Process
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mock_documents)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 4rem; color:var(--text-muted);">No document requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal: Add New Request -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2>New Document Request</h2>
                <button class="close-btn" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="api/add_record.php" method="POST">
                <input type="hidden" name="action" value="add_doc_request">
                <input type="hidden" name="request_date" value="<?php echo date('Y-m-d'); ?>">
                <input type="hidden" name="status" value="Pending">

                <div class="form-group">
                    <label>Select Student</label>
                    <select name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach($mock_info as $student): ?>
                        <option value="<?php echo $student['student_id']; ?>"><?php echo $student['student_id'] . " - " . $student['student_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Document Type</label>
                    <select name="document_type" required>
                        <option value="Form 137">Form 137</option>
                        <option value="Transcript of Records (TOR)">Transcript of Records (TOR)</option>
                        <option value="Good Moral Certificate">Good Moral Certificate</option>
                        <option value="Certificate of Enrollment">Certificate of Enrollment</option>
                        <option value="Diploma">Diploma</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Purpose</label>
                    <textarea name="purpose" rows="3" placeholder="e.g. For Scholarship, Admission, Employment..." required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn-submit">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Process Request -->
    <div class="modal-overlay" id="processModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Process Document</h2>
                <button class="close-btn" onclick="closeModal('processModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="request-info">
                <div class="info-row"><span class="info-key">Student Name</span> <span class="info-val" id="proc_name">Juan Dela Cruz</span></div>
                <div class="info-row"><span class="info-key">Student ID</span> <span class="info-val" id="proc_id">2026-0001</span></div>
                <div class="info-row"><span class="info-key">Document</span> <span class="info-val" id="proc_doc">Form 137</span></div>
                <div class="info-row"><span class="info-key">Purpose</span> <span class="info-val" id="proc_purpose">Scholarship</span></div>
            </div>

            <form action="api/add_record.php" method="POST" id="statusForm">
                <input type="hidden" name="action" value="update_doc_status">
                <input type="hidden" name="request_id" id="proc_req_id">
                <input type="hidden" name="status" id="proc_status_val">

                <div class="status-button-grid">
                    <button type="button" class="btn-status-update btn-approve" onclick="updateStatus('Processing')">
                        <i class="fa-solid fa-spinner fa-spin"></i> Mark as Processing
                    </button>
                    <button type="button" class="btn-status-update btn-ready" onclick="updateStatus('Ready for Pickup')">
                        <i class="fa-solid fa-circle-check"></i> Ready for Pickup
                    </button>
                    <button type="button" class="btn-status-update btn-released" onclick="updateStatus('Released')">
                        <i class="fa-solid fa-handshake"></i> Mark as Released
                    </button>
                    <button type="button" class="btn-status-update btn-reject" onclick="updateStatus('Rejected')">
                        <i class="fa-solid fa-circle-xmark"></i> Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() { document.getElementById('addModal').classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function openProcessModal(data) {
            document.getElementById('proc_req_id').value = data.request_id;
            document.getElementById('proc_name').innerText = data.student_name;
            document.getElementById('proc_id').innerText = data.student_id;
            document.getElementById('proc_doc').innerText = data.document_type;
            document.getElementById('proc_purpose').innerText = data.purpose;
            document.getElementById('processModal').classList.add('active');
        }

        function updateStatus(status) {
            document.getElementById('proc_status_val').value = status;
            document.getElementById('statusForm').submit();
        }

        // Theme Toggle Logic
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
</body>
</html>