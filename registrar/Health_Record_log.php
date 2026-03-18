<?php session_start(); if (!isset($_SESSION['admin_id'])) { header('Location: ../index.php'); exit(); } include 'api/data_loader.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Health Record Log</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1; --primary-light: #a5b4fc; --primary-dark: #4338ca;
            --secondary-color: #10b981; --health-color: #f43f5e;
            --bg-color: #f4f7fe; --surface-color: rgba(255, 255, 255, 0.85);
            --text-main: #1e293b; --text-muted: #64748b; --sidebar-width: 280px;
            --header-height: 80px; --radius-lg: 20px; --radius-md: 14px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%); color: var(--text-main); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-brand { padding: 0 2rem 2rem 2rem; display: flex; align-items: center; gap: 15px; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary-color); background: rgba(99,102,241,0.1); padding: 10px; border-radius: 12px; }
        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }
        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }
        .nav-list { list-style: none; padding: 0 1.2rem; }
        .nav-item { margin-bottom: 0.4rem; }
        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: var(--radius-md); transition: all 0.3s; gap: 14px; font-weight: 500; }
        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }
        .nav-link:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link.active { background: var(--primary-color); color: white; box-shadow: 0 8px 16px rgba(99,102,241,0.25); }
        .nav-link.active i { color: white; }

        /* Layout */
        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .header { height: var(--header-height); display: flex; align-items: center; justify-content: space-between; padding: 0 3rem; position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.1); }
        .search-bar { background: white; padding: 10px 20px; border-radius: 99px; display: flex; align-items: center; color: var(--text-muted); width: 300px; border: 1px solid rgba(0,0,0,0.05); }
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; padding: 8px 16px; background: white; border-radius: 99px; border: 1px solid rgba(0,0,0,0.05); }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2.5rem; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.5rem; }

        /* Table Container */
        .data-table-container { background: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.8); padding: 1.5rem; }
        .table-header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn { background: var(--primary-color); color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 99px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 10px; }
        .btn:hover { transform: translateY(-2px); background: var(--primary-dark); }
        .btn-view { background: rgba(244,63,94,0.1); color: var(--health-color); border: none; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; }
        .btn-view:hover { background: var(--health-color); color: white; transform: scale(1.05); }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; }
        .data-table tbody tr { transition: all 0.3s; background: white; border-radius: 16px; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        /* Blood Type Badge */
        .blood-badge { display: inline-block; padding: 4px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 800; background: rgba(244,63,94,0.1); color: var(--health-color); }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.65); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 860px; border-radius: 24px; padding: 3rem; overflow-y: auto; max-height: 92vh; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; }
        .modal-header h2 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; }

        /* Form */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #64748b; margin-left: 4px; }
        .form-group input, .form-group select, .form-group textarea { padding: 0.9rem 1.2rem; border-radius: 14px; border: 1.5px solid #e2e8f0; font-size: 0.95rem; font-family: 'Outfit', sans-serif; transition: all 0.3s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--health-color); box-shadow: 0 0 0 4px rgba(244,63,94,0.08); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .modal-footer { margin-top: 3rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #f1f5f9; padding-top: 2rem; }
        .btn-modal { background: var(--health-color); color: white; border: none; padding: 1rem 2rem; border-radius: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; font-family: 'Outfit', sans-serif; }
        .btn-modal:hover { filter: brightness(1.1); transform: translateY(-2px); }
        .btn-ghost { background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; padding: 1rem 2rem; border-radius: 16px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; }

        /* Info section styling (view modal) */
        .info-section { background: #f8fafc; border-radius: 18px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #f1f5f9; }
        .section-tag { display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; border-radius: 10px; font-size: 0.72rem; font-weight: 800; margin-bottom: 1.2rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .tag-student { background: rgba(99,102,241,0.12); color: var(--primary-dark); }
        .tag-health { background: rgba(244,63,94,0.12); color: var(--health-color); }
        .tag-logs { background: rgba(16,185,129,0.12); color: #059669; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; }
        .info-item { display: flex; flex-direction: column; gap: 5px; }
        .info-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; }
        .info-value { font-size: 1rem; font-weight: 600; color: var(--text-main); }

        /* Timeline / Log Table */
        .log-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .log-table th { text-align: left; padding: 0.8rem 1rem; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
        .log-table td { padding: 0.9rem 1rem; border-bottom: 1px solid #f8fafc; vertical-align: top; }
        .log-table tbody tr:hover { background: #fafbff; }
        .log-date { font-weight: 700; color: var(--primary-color); }
        .no-logs { text-align: center; padding: 2rem; color: var(--text-muted); }

        /* Step label */
        .step-label { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; font-weight: 700; }
        .step-num { width: 26px; height: 26px; background: var(--health-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.78rem; font-weight: 800; flex-shrink: 0; }

        /* Dark mode */
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30,41,59,0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .search-bar, :root[data-theme="dark"] .user-profile,
        :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card,
        :root[data-theme="dark"] .info-section { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .form-group input, :root[data-theme="dark"] .form-group select, :root[data-theme="dark"] .form-group textarea { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
        :root[data-theme="dark"] .btn-ghost { background: #0f172a; color: white; border-color: rgba(255,255,255,0.1); }
        :root[data-theme="dark"] .log-table td { border-color: rgba(255,255,255,0.05); }
        :root[data-theme="dark"] .log-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        .theme-toggle { background: white; border: 1px solid rgba(0,0,0,0.05); width: 44px; height: 44px; border-radius: 50%; cursor: pointer; margin-right: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        :root[data-theme="dark"] .theme-toggle { background: #1e293b; color: white; }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
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
            <li class="nav-item"><a href="Personal_info.php" class="nav-link"><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
            <li class="nav-item"><a href="Guardian&Emergency_Contact.php" class="nav-link"><i class="fa-solid fa-hands-holding-child"></i><span>Guardian & Contact</span></a></li>
            <li class="nav-item"><a href="Academic_history.php" class="nav-link"><i class="fa-solid fa-book-open"></i><span>Academic History</span></a></li>
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link active"><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
            <li class="nav-item"><a href="id_generator.php" class="nav-link"><i class="fa-solid fa-id-card"></i><span>ID Generation</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link"><i class="fa-solid fa-wifi"></i><span>RFID Scanner</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link"><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link"><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link"><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link"><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i><span style="margin-left: 10px; font-size: 0.9rem;">Search health records...</span></div>
            <div style="display: flex; align-items: center;">
                <button id="themeToggle" class="theme-toggle"><i class="fa-solid fa-moon"></i></button>
                <div class="user-profile">
                    <div style="text-align: right; margin-right: 15px;">
                        <h4>Registrar</h4>
                        <p style="font-size: 0.95rem; color: var(--text-muted); font-weight: 500; margin-top: 2px;"><?php echo htmlspecialchars($_SESSION['admin_user']); ?></p>
                    </div>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['admin_user'], 0, 1)); ?></div>
                </div>
            </div>
        </header>

        <div class="dashboard-body">
            <div class="page-title">
                <h1><i class="fa-solid fa-heart-pulse" style="color: var(--health-color); margin-right: 15px;"></i>Health Record Log</h1>
                <p>Student health profiles and clinic visit history.</p>
            </div>

            <div class="data-table-container">
                <div class="table-header-controls">
                    <h3 style="font-size: 1.25rem; font-weight: 700;">Health Records</h3>
                    <button class="btn" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add New Record</button>
                </div>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Grade / Section</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mock_health)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>
                                    No health records found.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($mock_health as $row): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--primary-dark);"><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($row['student_name'] ?? 'Unknown'); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['program'] ?? '—'); ?>
                                    <?php if (!empty($row['section'])): ?>
                                        &nbsp;&mdash;&nbsp;<?php echo htmlspecialchars($row['section']); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <button class="btn-view" onclick="viewRecord('<?php echo $row['id']; ?>')">
                                        <i class="fa-solid fa-stethoscope"></i> View Information
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- ============================================================ -->
    <!-- ADD NEW RECORD MODAL (Registrar inputs health profile only)  -->
    <!-- ============================================================ -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2>New Health Profile</h2>
                    <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">Enter the student's basic health information as provided during enrollment.</p>
                </div>
                <button class="btn-ghost" style="padding: 10px 14px; border-radius: 50%;" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="api/add_record.php" method="POST">
                <input type="hidden" name="action" value="add_health">

                <!-- Student Selection -->
                <div class="step-label"><span class="step-num">1</span><span>Link to Student</span></div>
                <div class="form-grid" style="margin-bottom: 2.5rem;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Select Student</label>
                        <select name="student_id" required>
                            <option value="">Choose Student...</option>
                            <?php foreach($mock_info as $s): ?>
                            <option value="<?php echo $s['student_id']; ?>"><?php echo $s['student_id'] . ' — ' . $s['student_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Health Profile Fields -->
                <div class="step-label"><span class="step-num">2</span><span>Health Profile Information</span></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Blood Type</label>
                        <select name="blood_type" required>
                            <option value="">Select...</option>
                            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                            <option value="<?php echo $bt; ?>"><?php echo $bt; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Height (cm)</label>
                        <input type="number" name="height" placeholder="e.g. 165" step="0.1" min="50" max="250">
                    </div>
                    <div class="form-group">
                        <label>Weight (kg)</label>
                        <input type="number" name="weight" placeholder="e.g. 55" step="0.1" min="10" max="300">
                    </div>
                    <div class="form-group">
                        <label>Allergies</label>
                        <input type="text" name="allergies" placeholder="e.g. Peanuts, Dust, None">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Existing Conditions</label>
                        <input type="text" name="existing_conditions" placeholder="e.g. Asthma, Diabetes, None">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Current Medications</label>
                        <input type="text" name="medications" placeholder="e.g. Salbutamol Inhaler, None">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Emergency Notes</label>
                        <textarea name="emergency_notes" placeholder="Any critical information first responders should know..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn-modal"><i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i>Save Health Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- VIEW RECORD MODAL                                            -->
    <!-- ============================================================ -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2><i class="fa-solid fa-heart-pulse" style="color: var(--health-color); margin-right: 10px;"></i>Health Record</h2>
                    <p id="view_subtitle" style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;"></p>
                </div>
                <button class="btn-ghost" style="padding: 10px 14px; border-radius: 50%;" onclick="closeModal('viewModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Section 1: Student Info -->
            <div class="info-section">
                <span class="section-tag tag-student"><i class="fa-solid fa-user"></i> Student Info</span>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Student ID</span><span class="info-value" id="v_student_id"></span></div>
                    <div class="info-item"><span class="info-label">Full Name</span><span class="info-value" id="v_name"></span></div>
                    <div class="info-item"><span class="info-label">Grade / Section</span><span class="info-value" id="v_grade_sec"></span></div>
                </div>
            </div>

            <!-- Section 2: Health Profile -->
            <div class="info-section">
                <span class="section-tag tag-health"><i class="fa-solid fa-stethoscope"></i> Health Profile</span>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Blood Type</span><span class="info-value" id="v_blood"></span></div>
                    <div class="info-item"><span class="info-label">Height</span><span class="info-value" id="v_height"></span></div>
                    <div class="info-item"><span class="info-label">Weight</span><span class="info-value" id="v_weight"></span></div>
                    <div class="info-item"><span class="info-label">Allergies</span><span class="info-value" id="v_allergies"></span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Existing Conditions</span><span class="info-value" id="v_conditions"></span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Current Medications</span><span class="info-value" id="v_meds"></span></div>
                    <div class="info-item" style="grid-column: span 2;"><span class="info-label">Emergency Notes</span><span class="info-value" id="v_emergency_notes"></span></div>
                </div>
            </div>

            <!-- Section 3: Health Logs Timeline -->
            <div class="info-section">
                <span class="section-tag tag-logs"><i class="fa-solid fa-clock-rotate-left"></i> Medical History Timeline</span>
                <div id="v_logs_container"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-ghost" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <script>
        const healthData = <?php echo json_encode($mock_health); ?>;

        function openAddModal() { document.getElementById('addModal').classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function viewRecord(id) {
            const rec = healthData.find(r => r.id == id);
            if (!rec) return;

            document.getElementById('view_subtitle').innerText = rec.student_name + ' · ' + rec.student_id;
            document.getElementById('v_student_id').innerText = rec.student_id;
            document.getElementById('v_name').innerText = rec.student_name || 'Unknown';
            document.getElementById('v_grade_sec').innerText = (rec.program || '—') + (rec.section ? ' — ' + rec.section : '');
            document.getElementById('v_blood').innerHTML = rec.blood_type ? `<span class="blood-badge">${rec.blood_type}</span>` : '—';
            document.getElementById('v_height').innerText = rec.height ? rec.height + ' cm' : '—';
            document.getElementById('v_weight').innerText = rec.weight ? rec.weight + ' kg' : '—';
            document.getElementById('v_allergies').innerText = rec.allergies || 'None';
            document.getElementById('v_conditions').innerText = rec.existing_conditions || 'None';
            document.getElementById('v_meds').innerText = rec.medications || 'None';
            document.getElementById('v_emergency_notes').innerText = rec.emergency_notes || 'None';

            // Logs timeline
            const logsContainer = document.getElementById('v_logs_container');
            if (!rec.logs || rec.logs.length === 0) {
                logsContainer.innerHTML = `<p class="no-logs"><i class="fa-solid fa-notes-medical" style="display:block;font-size:2rem;margin-bottom:10px;opacity:0.3;"></i>No clinic visit records on file.</p>`;
            } else {
                let html = `
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Complaint</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                                <th>Attended By</th>
                            </tr>
                        </thead>
                        <tbody>`;
                rec.logs.forEach(log => {
                    html += `
                        <tr>
                            <td class="log-date">${log.log_date || '—'}</td>
                            <td>${log.complaint || '—'}</td>
                            <td>${log.diagnosis || '—'}</td>
                            <td>${log.treatment || '—'}</td>
                            <td>${log.attended_by || '—'}</td>
                        </tr>`;
                });
                html += `</tbody></table>`;
                logsContainer.innerHTML = html;
            }

            document.getElementById('viewModal').classList.add('active');
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('active');
        }

        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        const updateIcon = t => {
            if (t === 'dark') themeIcon.classList.replace('fa-moon', 'fa-sun');
            else themeIcon.classList.replace('fa-sun', 'fa-moon');
        };
        updateIcon(document.documentElement.getAttribute('data-theme'));
        themeToggle.addEventListener('click', () => {
            const t = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', t);
            localStorage.setItem('theme', t);
            updateIcon(t);
        });
    </script>
</body>
</html>