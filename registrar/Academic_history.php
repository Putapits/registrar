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
    <title>Registrar SIS - Academic History</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
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
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2.5rem; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.5rem; }

        .data-table-container { background: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.8); padding: 1.5rem; }
        .table-header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn { background: var(--primary-color); color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 99px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 10px; }
        .btn:hover { transform: translateY(-2px); background: var(--primary-dark); }
        .btn-view { background: rgba(99, 102, 241, 0.1); color: var(--primary-color); border: none; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; }
        .btn-view:hover { background: var(--primary-color); color: white; transform: scale(1.05); }

        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; }
        .data-table tbody tr { transition: all 0.3s; background: white; border-radius: 16px; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        .status-badge { padding: 0.4rem 1rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-ongoing { background: #fee2e2; color: #ef4444; }
        .status-completed { background: #d1fae5; color: #10b981; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 900px; border-radius: 24px; padding: 3rem; overflow-y: auto; max-height: 90vh; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #64748b; margin-left: 4px; }
        .form-group input, .form-group select { padding: 0.9rem 1.2rem; border-radius: 14px; border: 1.5px solid #e2e8f0; font-size: 0.95rem; }
        
        /* Subject Table */
        .subject-encoding { margin-top: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 18px; border: 1px solid #e2e8f0; }
        .subject-row { display: grid; grid-template-columns: 2fr 1fr 50px; gap: 1rem; margin-bottom: 0.8rem; align-items: center; }
        .btn-add-subject { background: var(--secondary-color); color: white; border: none; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 0.8rem; margin-top: 10px; }
        .btn-remove { color: #ef4444; cursor: pointer; background: none; border: none; font-size: 1.1rem; }

        .section-tag { display: inline-block; padding: 4px 12px; background: var(--primary-color); color: white; border-radius: 8px; font-size: 0.7rem; font-weight: 800; margin-bottom: 1rem; text-transform: uppercase; }
        
        .modal-footer { margin-top: 3rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #f1f5f9; padding-top: 2rem; }
        .btn-modal { background: var(--primary-color); color: white; border: none; padding: 1rem 2rem; border-radius: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-ghost { background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; padding: 1rem 2rem; border-radius: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; }

        /* Dark Mode Support */
        :root[data-theme="dark"] {
            --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85);
            --text-main: #f8fafc; --text-muted: #94a3b8;
        }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .search-bar, :root[data-theme="dark"] .user-profile,
        :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card,
        :root[data-theme="dark"] .subject-encoding {
            background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main);
        }
        :root[data-theme="dark"] .form-group input, :root[data-theme="dark"] .form-group select { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
        :root[data-theme="dark"] .btn-ghost { background: #0f172a; color: white; border-color: rgba(255,255,255,0.1); }
        
        .theme-toggle { background: white; border: 1px solid rgba(0,0,0,0.05); width: 44px; height: 44px; border-radius: 50%; cursor: pointer; margin-right: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        :root[data-theme="dark"] .theme-toggle { background: #1e293b; color: white; }

        .transferee-option { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
        .transferee-option input { width: 18px; height: 18px; cursor: pointer; }
        .step-label { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; font-size: 1rem; font-weight: 700; }
        .step-num { width: 28px; height: 28px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0; }
        .form-group small { font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; display: block; }
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
            <li class="nav-item"><a href="Personal_info.php" class="nav-link"><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
            <li class="nav-item"><a href="Guardian&Emergency_Contact.php" class="nav-link"><i class="fa-solid fa-hands-holding-child"></i><span>Guardian & Contact</span></a></li>
            <li class="nav-item"><a href="Academic_history.php" class="nav-link active"><i class="fa-solid fa-book-open"></i><span>Academic History</span></a></li>
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link"><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
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
            <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i> <span style="margin-left: 10px; font-size: 0.9rem;">Search academic records...</span></div>
            <div style="display: flex; align-items: center;">
                <button id="themeToggle" class="theme-toggle"><i class="fa-solid fa-moon"></i></button>
                <div class="user-profile">
                    <div class="user-info" style="text-align: right; margin-right: 15px;">
                        <h4>Registrar</h4><p style="font-size: 0.95rem; color: var(--text-muted); font-weight: 500; margin-top: 2px;"><?php echo htmlspecialchars($_SESSION["admin_user"]); ?></p>
                    </div>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION["admin_user"], 0, 1)); ?></div>
                </div>
            </div>
        </header>

        <div class="dashboard-body">
            <div class="page-title">
                <h1><i class="fa-solid fa-book-open" style="color: var(--primary-color); margin-right: 15px;"></i>Academic History</h1>
                <p>Manage school years, grade levels, and scholastic achievements.</p>
            </div>
            
            <div class="data-table-container">
                <div class="table-header-controls">
                    <h3 style="font-size: 1.25rem; font-weight: 700;">Data Overview</h3>
                    <button class="btn" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add New Record</button>
                </div>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>School Year</th>
                                <th>Grade Level</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mock_academic)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);"><i class="fa-solid fa-folder-open" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>No academic records found.</td></tr>
                            <?php
else: ?>
                            <?php foreach ($mock_academic as $row): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--primary-dark);"><?php echo $row["student_id"]; ?></td>
                                <td><?php echo $row["student_name"] ?? 'Unknown'; ?></td>
                                <td><?php echo $row["school_year"]; ?></td>
                                <td><?php echo $row["grade_level"]; ?></td>
                                <td><?php echo $row["section"]; ?></td>
                                <td><span class="status-badge <?php echo $row['academic_status'] == 'Completed' ? 'status-completed' : 'status-ongoing'; ?>"><?php echo $row["academic_status"]; ?></span></td>
                                <td style="text-align: right;">
                                    <button class="btn-view" onclick="viewDetails('<?php echo $row['id']; ?>')">
                                        <i class="fa-solid fa-eye"></i> View Grades
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
                <div>
                    <h2 style="font-size: 1.6rem;">New Academic Record</h2>
                    <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">Complete each section to build a full scholastic history.</p>
                </div>
                <button class="btn-ghost" style="padding: 10px 14px; border-radius: 50%;" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="api/add_record.php" method="POST" id="academicForm">
                <input type="hidden" name="action" value="add_academic">

                <!-- Step 1: Student Link -->
                <div class="step-label"><span class="step-num">1</span><span>Student Profile Link</span></div>
                <div class="form-grid" style="margin-bottom: 2.5rem;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Select Student</label>
                        <select name="student_id" required>
                            <option value="">Choose Student...</option>
                            <?php foreach ($mock_info as $student): ?>
                            <option value="<?php echo $student['student_id']; ?>"><?php echo $student['student_id'] . " — " . $student['student_name']; ?></option>
                            <?php
endforeach; ?>
                        </select>
                        <small>Pick the student whose academic record you are encoding.</small>
                    </div>
                </div>

                <!-- Step 2: Header Information -->
                <div class="step-label"><span class="step-num">2</span><span>Header Information</span></div>
                <div class="form-grid" style="margin-bottom: 2rem;">
                    <div class="form-group">
                        <label>School Year</label>
                        <input type="text" name="school_year" placeholder="e.g. 2024-2025" required>
                        <small>The specific academic period (e.g., 2024-2025).</small>
                    </div>
                    <div class="form-group">
                        <label>Grade Level / Year</label>
                        <select name="grade_level" required>
                            <option value="">Select level...</option>
                            <option value="Grade 7">Grade 7</option>
                            <option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option>
                            <option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                            <option value="1st Year">1st Year College</option>
                            <option value="2nd Year">2nd Year College</option>
                            <option value="3rd Year">3rd Year College</option>
                            <option value="4th Year">4th Year College</option>
                        </select>
                        <small>The level they were enrolled in (e.g., Grade 11 or 1st Year).</small>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <input type="text" name="section" placeholder="e.g. 1-A" required>
                        <small>The specific class group.</small>
                    </div>
                    <div class="form-group">
                        <label>Program / Strand</label>
                        <input type="text" name="program" placeholder="e.g. STEM or BSIT" required>
                        <small>The official course or strand (e.g., STEM, BSIT).</small>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Entry Type</label>
                        <div class="transferee-option">
                            <input type="checkbox" name="is_transferee" id="isTrans" onchange="toggleTransferee(this)">
                            <label for="isTrans" style="margin: 0; font-weight: 500;">This student is a <strong>Transferee</strong> &mdash; grades came from a different school</label>
                        </div>
                        <small>Check this only if the grades below were from another institution.</small>
                    </div>
                    <div id="transfereeFields" style="display: none; grid-column: span 2;">
                        <div class="form-group">
                            <label>Previous School Name</label>
                            <input type="text" name="prev_school" placeholder="e.g. Lincoln High School">
                        </div>
                    </div>
                </div>

                <!-- Step 3: Subjects & Grades -->
                <div class="step-label"><span class="step-num" style="background: var(--secondary-color);">3</span><span>Encode Subjects &amp; Final Grades</span></div>
                <div class="subject-encoding">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 40px; gap: 1rem; padding: 0 4px; margin-bottom: 8px;">
                        <small style="font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Subject Name (full official name)</small>
                        <small style="font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Final Grade</small>
                        <span></span>
                    </div>
                    <div id="subjectContainer">
                        <div class="subject-row">
                            <input type="text" name="subjects[]" placeholder="e.g. Introduction to Computing" required>
                            <input type="number" name="grades[]" placeholder="e.g. 95" step="0.01" min="0" max="100" class="grade-input" onchange="calculateAverage()" required>
                            <span></span>
                        </div>
                    </div>
                    <button type="button" class="btn-add-subject" onclick="addSubjectRow()"><i class="fa-solid fa-plus"></i> Add Another Subject</button>

                    <div style="margin-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.07); padding-top: 1.2rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; font-size: 0.9rem;">System-Calculated Average (GPA):</span>
                        <span id="avgDisplay" style="font-size: 1.8rem; font-weight: 800; color: var(--primary-color);">0.00</span>
                        <input type="hidden" name="gpa" id="gpaInput" value="0">
                    </div>
                </div>

                <!-- Step 4: Final Status -->
                <div class="step-label" style="margin-top: 2rem;"><span class="step-num" style="background: #f43f5e;">4</span><span>Final Status &amp; Remarks</span></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Academic Status</label>
                        <select name="academic_status">
                            <option value="Ongoing">🟡 Ongoing — Still studying this year</option>
                            <option value="Completed">🟢 Completed — Passed the year/level</option>
                            <option value="Dropped">🔴 Dropped — Left mid-year</option>
                        </select>
                        <small>Select the final result for this school year.</small>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <input type="text" name="remarks" placeholder="e.g. Promoted to Grade 12">
                        <small>Brief note like "Promoted" or "Transferred from [School Name]."</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn-modal"><i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i>Save Academic Record</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal: View Grades -->

    <div class="modal-overlay" id="viewModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Academic Performance Details</h2>
                <button class="btn-ghost" style="padding: 10px; border-radius: 50%;" onclick="closeModal('viewModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div id="viewContent">
                <!-- Dynamically filled -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-ghost" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <script>
        const academicData = <?php echo json_encode($mock_academic); ?>;

        function openAddModal() { document.getElementById('addModal').classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        function toggleTransferee(el) {
            document.getElementById('transfereeFields').style.display = el.checked ? 'block' : 'none';
        }

        function addSubjectRow() {
            const row = document.createElement('div');
            row.className = 'subject-row';
            row.innerHTML = `
                <input type="text" name="subjects[]" placeholder="Subject Name" required>
                <input type="number" name="grades[]" placeholder="Grade" step="0.01" class="grade-input" onchange="calculateAverage()" required>
                <button type="button" class="btn-remove" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button>
            `;
            document.getElementById('subjectContainer').appendChild(row);
        }

        function removeRow(btn) {
            btn.parentElement.remove();
            calculateAverage();
        }

        function calculateAverage() {
            const inputs = document.querySelectorAll('.grade-input');
            let sum = 0;
            let count = 0;
            inputs.forEach(input => {
                if (input.value) {
                    sum += parseFloat(input.value);
                    count++;
                }
            });
            const avg = count > 0 ? (sum / count).toFixed(2) : "0.00";
            document.getElementById('avgDisplay').innerText = avg;
            document.getElementById('gpaInput').value = avg;
        }

        function viewDetails(id) {
            const record = academicData.find(r => r.id == id);
            if (record) {
                let html = `
                    <div style="background: #f8fafc; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; border: 1px solid #e2e8f0;">
                         <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div><label style="display:block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Student</label><strong>${record.student_name} (${record.student_id})</strong></div>
                            <div><label style="display:block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">School Year</label><strong>${record.school_year}</strong></div>
                            <div><label style="display:block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Grade Level</label><strong>Grade ${record.grade_level}</strong></div>
                            <div><label style="display:block; font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Average GPA</label><strong style="color: var(--primary-color); font-size: 1.2rem;">${record.gpa}</strong></div>
                         </div>
                    </div>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                                <th style="padding: 1rem;">Subject</th>
                                <th style="padding: 1rem;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                if (record.subjects && record.subjects.length > 0) {
                    record.subjects.forEach(s => {
                        html += `
                            <tr style="border-bottom: 1px solid #f8fafc;">
                                <td style="padding: 1rem; font-weight: 600;">${s.subject_name}</td>
                                <td style="padding: 1rem;">${s.grade}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `<tr><td colspan="2" style="padding: 2rem; text-align: center; color: #94a3b8;">No detailed grades encoded.</td></tr>`;
                }

                html += `</tbody></table>`;
                document.getElementById('viewContent').innerHTML = html;
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