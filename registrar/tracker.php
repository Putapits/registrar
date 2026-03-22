<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../index.php");
    exit();
}
require_once 'api/db_connection.php';

// Fetch Filters from GET
$filter_sy = $_GET['sy'] ?? '';
$filter_grade = $_GET['grade'] ?? '';
$filter_status = $_GET['status'] ?? '';

// Build Query for Stats (Unfiltered total counts)
$stats = [
    'total' => 0,
    'active' => 0,
    'graduated' => 0,
    'dropped' => 0,
    'transferred' => 0
];

$res_stats = $conn->query("SELECT status, COUNT(*) as count FROM personal_info GROUP BY status");
if ($res_stats) {
    while ($row = $res_stats->fetch_assoc()) {
        $stats['total'] += $row['count'];
        $s = strtolower($row['status']);
        if ($s == 'active' || $s == 'enrolled') $stats['active'] += $row['count'];
        else if ($s == 'graduated') $stats['graduated'] += $row['count'];
        else if ($s == 'dropped') $stats['dropped'] += $row['count'];
        else if ($s == 'transferred') $stats['transferred'] += $row['count'];
    }
}

// Build Query for Table
$sql = "SELECT * FROM personal_info WHERE 1=1";
if ($filter_sy) $sql .= " AND school_year = '" . $conn->real_escape_string($filter_sy) . "'";
if ($filter_grade) $sql .= " AND grade_level = '" . $conn->real_escape_string($filter_grade) . "'";
if ($filter_status) $sql .= " AND status = '" . $conn->real_escape_string($filter_status) . "'";
$sql .= " ORDER BY student_id DESC";

$result = $conn->query($sql);
$students = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch distinct values for filters
$sys = $conn->query("SELECT DISTINCT school_year FROM personal_info WHERE school_year IS NOT NULL AND school_year != ''");
$grades = $conn->query("SELECT DISTINCT grade_level FROM personal_info WHERE grade_level IS NOT NULL AND grade_level != ''");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Student Status Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1; --primary-dark: #4f46e5; --primary-light: #a5b4fc;
            --secondary: #10b981; --danger: #ef4444; --warning: #f59e0b; --info: #3b82f6;
            --bg: #f8fafc; --surface: #ffffff; --text: #1e293b; --text-light: #64748b;
            --radius: 16px; --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --sidebar-width: 280px;
        }

        [data-theme="dark"] {
            --bg: #0f172a; --surface: #1e293b; --text: #f8fafc; --text-light: #94a3b8;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }

        /* Sidebar Styles */
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-brand { padding: 0 2rem 2rem 2rem; display: flex; align-items: center; gap: 15px; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary); background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 12px; }
        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }
        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }
        .nav-list { list-style: none; padding: 0 1.2rem; }
        .nav-item { margin-bottom: 0.4rem; }
        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: 14px; transition: all 0.3s; gap: 14px; font-weight: 500; }
        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }
        .nav-link:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25); }
        .nav-link.active i { color: white; }

        /* Main Content */
        .main { flex: 1; margin-left: var(--sidebar-width); padding: 2rem 3rem; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .page-title h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; }
        .page-title p { color: var(--text-light); font-size: 0.95rem; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; margin-bottom: 2.5rem; }
        .stat-card { background: var(--surface); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.02); display: flex; align-items: center; gap: 1rem; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .stat-info h3 { font-size: 1.5rem; font-weight: 700; }
        .stat-info p { font-size: 0.85rem; color: var(--text-light); font-weight: 500; }

        .bg-primary { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
        .bg-secondary { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }
        .bg-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .bg-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .bg-info { background: rgba(59, 130, 246, 0.1); color: var(--info); }

        /* Filters */
        .filters-card { background: var(--surface); padding: 1.5rem; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; flex: 1; min-width: 180px; }
        .filter-group label { font-size: 0.85rem; font-weight: 600; color: var(--text-light); }
        .filter-group select, .filter-group input { padding: 0.7rem 1rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); background: var(--bg); color: var(--text); outline: none; transition: 0.3s; }
        .filter-group select:focus { border-color: var(--primary); }
        .btn-filter { background: var(--primary); color: white; border: none; padding: 0.8rem 2rem; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; height: 45px; }
        .btn-filter:hover { background: var(--primary-dark); transform: scale(1.02); }

        /* Table */
        .table-card { background: var(--surface); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        .table-header { padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1.2rem 1.5rem; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; background: rgba(0,0,0,0.01); }
        td { padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.03); font-size: 0.95rem; }
        tr:hover { background: rgba(99, 102, 241, 0.02); }

        .status-pill { padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-active { background: rgba(16, 185, 129, 0.1); color: var(--secondary); }
        .status-graduated { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .status-dropped { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .status-transferred { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .status-promoted { background: rgba(99, 102, 241, 0.1); color: var(--primary); }

        .btn-action { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: var(--text-light); text-decoration: none; transition: 0.2s; border: 1px solid rgba(0,0,0,0.05); }
        .btn-action:hover { background: var(--primary); color: white; border-color: var(--primary); }
        .btn-update { background: #f1f5f9; color: var(--primary); border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; cursor: pointer; }
        .btn-update:hover { background: var(--primary); color: white; }

        /* Modal */
        .modal { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 2rem; opacity: 0; transition: 0.3s; }
        .modal.active { display: flex; opacity: 1; }
        .modal-content { background: var(--surface); width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; transform: translateY(20px); transition: 0.3s; }
        .modal.active .modal-content { transform: translateY(0); }
        .modal-header { padding: 1.5rem 2rem; background: var(--primary); color: white; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 2rem; }
        .modal-footer { padding: 1.5rem 2rem; display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid rgba(0,0,0,0.05); }
        .close-modal { background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem; }

        .form-row { margin-bottom: 1.2rem; }
        .form-row label { display: block; margin-bottom: 6px; font-size: 0.85rem; font-weight: 600; color: var(--text-light); }
        .form-row select, .form-row input { width: 100%; padding: 0.8rem 1rem; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); background: var(--bg); color: var(--text); }

        .btn-save { background: var(--primary); color: white; border: none; padding: 0.8rem 2rem; border-radius: 10px; font-weight: 600; cursor: pointer; }
        .btn-cancel { background: #f1f5f9; color: var(--text-light); border: none; padding: 0.8rem 2rem; border-radius: 10px; font-weight: 600; cursor: pointer; }

        /* Theme Toggle */
        #themeToggle { background: var(--surface); border: 1px solid rgba(0,0,0,0.05); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; }
        #themeToggle:hover { background: var(--primary); color: white; }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .stagger-in > * { opacity: 0; animation: fadeIn 0.5s forwards; }
        .stagger-in > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-in > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-in > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-in > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-in > *:nth-child(5) { animation-delay: 0.5s; }

        /* History Table in Modal */
        .history-list { max-height: 300px; overflow-y: auto; }
        .history-item { padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .history-item:last-child { border-bottom: none; }
        .history-meta { font-size: 0.75rem; color: var(--text-light); }
        .history-status { font-weight: 700; font-size: 0.85rem; }

        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; padding: 1.5rem; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body data-theme="light">
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
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link"><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
            <li class="nav-item"><a href="id_generator.php" class="nav-link"><i class="fa-solid fa-id-card"></i><span>ID Processing</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link"><i class="fa-solid fa-wifi"></i><span>RFID / QR Module</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link"><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link active"><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link"><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link"><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main">
        <header>
            <div class="page-title">
                <h1>Student Status Tracker</h1>
                <p>Monitor and manage academic status transitions</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <button id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <div class="user-badge" style="display: flex; align-items: center; gap: 10px; background: var(--surface); padding: 5px 15px; border-radius: 99px; box-shadow: var(--shadow);">
                    <div style="width: 32px; height: 32px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                        <?php echo strtoupper(substr($_SESSION["admin_user"], 0, 1)); ?>
                    </div>
                    <span style="font-weight: 600; font-size: 0.9rem;"><?php echo $_SESSION["admin_user"]; ?></span>
                </div>
            </div>
        </header>

        <section class="stats-grid stagger-in">
            <div class="stat-card">
                <div class="stat-icon bg-primary"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info"><h3><?php echo $stats['total']; ?></h3><p>Total Students</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-secondary"><i class="fa-solid fa-user-check"></i></div>
                <div class="stat-info"><h3><?php echo $stats['active']; ?></h3><p>Active</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-info"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="stat-info"><h3><?php echo $stats['graduated']; ?></h3><p>Graduated</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-danger"><i class="fa-solid fa-user-xmark"></i></div>
                <div class="stat-info"><h3><?php echo $stats['dropped']; ?></h3><p>Dropped</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning"><i class="fa-solid fa-truck-moving"></i></div>
                <div class="stat-info"><h3><?php echo $stats['transferred']; ?></h3><p>Transferred</p></div>
            </div>
        </section>

        <form action="" method="GET" class="filters-card">
            <div class="filter-group">
                <label>School Year</label>
                <select name="sy">
                    <option value="">All Years</option>
                    <?php while($sy = $sys->fetch_assoc()): ?>
                        <option value="<?php echo $sy['school_year']; ?>" <?php echo $filter_sy == $sy['school_year'] ? 'selected' : ''; ?>><?php echo $sy['school_year']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Grade Level</label>
                <select name="grade">
                    <option value="">All Grades</option>
                    <?php while($grade = $grades->fetch_assoc()): ?>
                        <option value="<?php echo $grade['grade_level']; ?>" <?php echo $filter_grade == $grade['grade_level'] ? 'selected' : ''; ?>>Grade <?php echo $grade['grade_level']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo $filter_status == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Graduated" <?php echo $filter_status == 'Graduated' ? 'selected' : ''; ?>>Graduated</option>
                    <option value="Dropped" <?php echo $filter_status == 'Dropped' ? 'selected' : ''; ?>>Dropped</option>
                    <option value="Transferred" <?php echo $filter_status == 'Transferred' ? 'selected' : ''; ?>>Transferred</option>
                    <option value="Promoted" <?php echo $filter_status == 'Promoted' ? 'selected' : ''; ?>>Promoted</option>
                </select>
            </div>
            <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Apply Filter</button>
            <a href="tracker.php" class="btn-cancel" style="display: flex; align-items: center; justify-content: center; text-decoration: none; padding: 0.8rem 1.5rem;">Reset</a>
        </form>

        <div class="table-card">
            <div class="table-header">
                <h3 style="font-weight: 700;">Student Status Registry</h3>
                <button class="btn-filter" style="background: var(--secondary);" onclick="openBulkModal()"><i class="fa-solid fa-layer-group"></i> Bulk Update</button>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>School Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="7" style="text-align: center; color: var(--text-light); padding: 4rem;">No records found matching your filters.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo $s['student_id']; ?></td>
                                    <td style="font-weight: 500;"><?php echo $s['student_name']; ?></td>
                                    <td>Grade <?php echo $s['grade_level'] ?? 'N/A'; ?></td>
                                    <td><?php echo $s['section'] ?? 'N/A'; ?></td>
                                    <td>
                                        <?php 
                                            $st = strtolower($s['status'] ?? 'active');
                                            $class = "status-$st";
                                            echo "<span class='status-pill $class'>$st</span>";
                                        ?>
                                    </td>
                                    <td><?php echo $s['school_year'] ?? 'N/A'; ?></td>
                                    <td style="display: flex; gap: 8px;">
                                        <button class="btn-update" onclick="openUpdateModal('<?php echo $s['student_id']; ?>', '<?php echo addslashes($s['student_name']); ?>', '<?php echo $s['status']; ?>', '<?php echo $s['grade_level']; ?>', '<?php echo $s['school_year']; ?>')">Update</button>
                                        <button class="btn-action" onclick="viewHistory('<?php echo $s['student_id']; ?>', '<?php echo addslashes($s['student_name']); ?>')" title="View History"><i class="fa-solid fa-clock-rotate-left"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Update Modal -->
    <div class="modal" id="updateModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Student Status</h3>
                <button class="close-modal" onclick="closeModal('updateModal')">&times;</button>
            </div>
            <form id="updateForm">
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="up_student_id">
                    <div class="form-row">
                        <label>Student Name</label>
                        <input type="text" id="up_student_name" disabled style="opacity: 0.7;">
                    </div>
                    <div class="form-row">
                        <label>Academic Status</label>
                        <select name="status" id="up_status" required>
                            <option value="Active">Active</option>
                            <option value="Promoted">Promoted</option>
                            <option value="Graduated">Graduated</option>
                            <option value="Dropped">Dropped</option>
                            <option value="Transferred">Transferred</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Grade Level</label>
                        <input type="text" name="grade_level" id="up_grade" placeholder="e.g. 10" required>
                    </div>
                    <div class="form-row">
                        <label>School Year</label>
                        <input type="text" name="school_year" id="up_sy" placeholder="e.g. 2025-2026" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('updateModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Modal -->
    <div class="modal" id="historyModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Status History</h3>
                <button class="close-modal" onclick="closeModal('historyModal')">&times;</button>
            </div>
            <div class="modal-body">
                <h4 id="hist_student_name" style="margin-bottom: 1rem; color: var(--primary);"></h4>
                <div class="history-list" id="historyList">
                    <!-- History items will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Update Modal (Optional) -->
    <div class="modal" id="bulkModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Bulk Status Update</h3>
                <button class="close-modal" onclick="closeModal('bulkModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 1.5rem;">Update status for all students in a specific grade/year.</p>
                <div class="form-row">
                    <label>Target Grade Level</label>
                    <input type="text" id="bulk_grade" placeholder="e.g. 12">
                </div>
                <div class="form-row">
                    <label>New Status</label>
                    <select id="bulk_status">
                        <option value="Promoted">Promoted</option>
                        <option value="Graduated">Graduated</option>
                        <option value="Active">Active</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('bulkModal')">Cancel</button>
                <button type="button" class="btn-save" style="background: var(--secondary);" onclick="alert('Bulk update functionality is a placeholder for this demo.')">Apply Bulk Update</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        function openUpdateModal(id, name, status, grade, sy) {
            document.getElementById('up_student_id').value = id;
            document.getElementById('up_student_name').value = name;
            document.getElementById('up_status').value = status;
            document.getElementById('up_grade').value = grade;
            document.getElementById('up_sy').value = sy;
            document.getElementById('updateModal').classList.add('active');
        }

        function viewHistory(id, name) {
            document.getElementById('hist_student_name').innerText = name;
            const historyList = document.getElementById('historyList');
            historyList.innerHTML = '<p style="text-align: center; padding: 1rem;">Loading history...</p>';
            
            fetch(`api/get_history.php?student_id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.history.length > 0) {
                        historyList.innerHTML = data.history.map(item => `
                            <div class="history-item">
                                <div>
                                    <div class="history-status">${item.status}</div>
                                    <div class="history-meta">Grade ${item.grade_level} | SY ${item.school_year}</div>
                                </div>
                                <div class="history-meta">${new Date(item.updated_at).toLocaleDateString()}</div>
                            </div>
                        `).join('');
                    } else {
                        historyList.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--text-light);">No history records found.</p>';
                    }
                });
            
            document.getElementById('historyModal').classList.add('active');
        }

        function openBulkModal() {
            document.getElementById('bulkModal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Form Submission
        document.getElementById('updateForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('api/update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        };

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        themeToggle.onclick = () => {
            const body = document.body;
            const isDark = body.getAttribute('data-theme') === 'dark';
            body.setAttribute('data-theme', isDark ? 'light' : 'dark');
            themeToggle.innerHTML = isDark ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun"></i>';
        };

        // Close on overlay click
        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.remove('active');
            }
        };
    </script>
</body>
</html>