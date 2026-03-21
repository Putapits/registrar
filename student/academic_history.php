<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];
$student_db_id = $_SESSION['student_db_id'];

// 1. Fetch Student Info
$q_info = $conn->prepare("SELECT student_name, student_id, program, section, status FROM personal_info WHERE student_id = ?");
$q_info->bind_param("s", $student_id);
$q_info->execute();
$student = $q_info->get_result()->fetch_assoc();

// 2. Fetch Academic Summary
$q_summary = $conn->prepare("SELECT * FROM academic_history WHERE student_id = ? ORDER BY school_year DESC");
$q_summary->bind_param("s", $student_id);
$q_summary->execute();
$summary_res = $q_summary->get_result();
$academic_history = [];
while($row = $summary_res->fetch_assoc()) {
    $academic_id = $row['id'];
    // Fetch subjects for this year
    $q_subs = $conn->prepare("SELECT * FROM academic_subjects WHERE academic_id = ?");
    $q_subs->bind_param("i", $academic_id);
    $q_subs->execute();
    $row['subjects'] = $q_subs->get_result()->fetch_all(MYSQLI_ASSOC);
    $academic_history[] = $row;
}

// 3. Fetch Overall Academic Status from status_tracker if exists, else from personal_info
$q_status = $conn->prepare("SELECT academic_status FROM status_tracker WHERE student_id = ?");
$q_status->bind_param("s", $student_id);
$q_status->execute();
$track = $q_status->get_result()->fetch_assoc();
$overall_status = $track['academic_status'] ?? ($student['status'] ?? 'Active');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic History - Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --radius-lg: 20px;
            --radius-md: 14px;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Outfit',sans-serif; }
        body { background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%); color: var(--text-main); display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; }
        .sidebar-brand { padding: 0 2rem 2rem 2rem; display: flex; align-items: center; gap: 15px; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary-color); background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 12px; }
        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }
        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }
        
        .nav-list { list-style: none; padding: 0 1.2rem; }
        .nav-item { margin-bottom: 0.4rem; }
        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: var(--radius-md); transition: all 0.3s; gap: 14px; font-weight: 500; }
        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }
        .nav-link.active { background: var(--primary-color); color: white; box-shadow: 0 8px 16px rgba(99,102,241,0.25); }
        .nav-link.active i { color: white; }

        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); padding: 3rem; }
        
        /* Student Info Header */
        .info-card { background: white; padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.03); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; border: 1px solid #f1f5f9; }
        .student-meta h2 { font-size: 1.8rem; font-weight: 800; color: #17388A; }
        .student-meta p { color: var(--text-muted); font-weight: 500; }
        .status-badge { padding: 8px 16px; border-radius: 99px; font-weight:700; font-size: 0.85rem; text-transform: uppercase; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-graduated { background: #e0e7ff; color: #3730a3; }
        .badge-promoted { background: #fef3c7; color: #92400e; }

        /* Tables */
        .section-card { background: white; padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.03); margin-bottom: 2.5rem; border: 1px solid #f1f5f9; }
        .section-card h3 { margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-size: 1.2rem; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1.2rem; border-bottom: 2px solid #f1f5f9; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 1.2rem; border-bottom: 1px solid #f8fafc; font-weight: 500; font-size: 0.95rem; }
        
        .btn-view { background: #f1f5f9; border:none; padding: 8px 16px; border-radius: 10px; font-weight: 700; color: #17388A; cursor: pointer; transition: all 0.3s; }
        .btn-view:hover { background: #17388A; color: white; }

        /* Modal */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display:none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(5px); }
        .modal.active { display: flex; }
        .modal-content { background: white; width: 90%; max-width: 600px; border-radius: 24px; padding: 2.5rem; position: relative; max-height: 80vh; overflow-y: auto; }
        .close-modal { position: absolute; top: 20px; right: 20px; font-size: 1.5rem; cursor: pointer; color: var(--text-muted); }

        .avg-block { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding: 1rem 1.5rem; background: #f0f7ff; border-radius: 14px; border: 1px solid #e0e7ff; }
        .avg-block h4 { color: #17388A; font-weight: 800; font-size: 1.2rem; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="info-card">
            <div class="student-meta">
                <p>Academic Records for</p>
                <h2><?php echo htmlspecialchars($student['student_name'] ?? 'Incomplete Profile'); ?></h2>
                <p>Student ID: <strong><?php echo $student['student_id'] ?? $_SESSION['student_id']; ?></strong> | <?php echo $student['program'] ?? 'N/A'; ?> - <?php echo $student['section'] ?? 'N/A'; ?></p>
            </div>
            <div class="status-container">
                <span class="status-badge badge-<?php echo strtolower($overall_status); ?>"><?php echo $overall_status; ?></span>
            </div>
        </div>

        <div class="section-card">
            <h3><i class="fa-solid fa-list-check" style="color:var(--primary-color)"></i> Academic Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>School Year</th>
                        <th>Grade Level</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($academic_history)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">No records found.</td></tr>
                    <?php else: ?>
                        <?php foreach($academic_history as $record): ?>
                        <tr>
                            <td><?php echo $record['school_year']; ?></td>
                            <td style="font-weight: 700;"><?php echo $record['grade_level']; ?></td>
                            <td><?php echo $record['section']; ?></td>
                            <td><span style="color: <?php echo $record['academic_status'] == 'Completed' ? '#10b981' : '#f97316'; ?>; font-weight: 700;"><?php echo $record['academic_status']; ?></span></td>
                            <td><button class="btn-view" onclick='viewGrades(<?php echo json_encode($record); ?>)'>View Grades</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Subjects Modal -->
        <div id="gradesModal" class="modal">
            <div class="modal-content">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <div id="modalHeader" style="margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
                    <h2 id="modalTitle" style="color: #17388A;">Grades</h2>
                    <p id="modalSub" style="color: #64748b; font-weight: 500;"></p>
                </div>
                <table id="gradesTable">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th style="text-align: right;">Final Grade</th>
                        </tr>
                    </thead>
                    <tbody id="gradesBody"></tbody>
                </table>
                <div class="avg-block">
                    <div>
                        <p style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Final Average</p>
                        <h4 id="avgDisplay">0.00</h4>
                    </div>
                </div>
                <div style="margin-top: 1.5rem; text-align: right;">
                    <p id="remarksDisplay" style="font-weight: 700; color: #10b981;"></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        function viewGrades(record) {
            document.getElementById('modalTitle').innerText = record.grade_level + " - Grades";
            document.getElementById('modalSub').innerText = "School Year: " + record.school_year + " | Section: " + record.section;
            
            const tbody = document.getElementById('gradesBody');
            tbody.innerHTML = '';
            
            if(record.subjects && record.subjects.length > 0) {
                record.subjects.forEach(sub => {
                    const row = `<tr>
                        <td style="font-weight: 600;">${sub.subject_name}</td>
                        <td style="text-align: right; font-weight: 700; color: #17388A;">${sub.grade}</td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="2" style="text-align:center; padding: 2rem; color: #94a3b8;">No subjects encoded for this year.</td></tr>';
            }
            
            document.getElementById('avgDisplay').innerText = record.gpa || '0.00';
            document.getElementById('remarksDisplay').innerText = "Remarks: " + (record.remarks || 'None');
            
            document.getElementById('gradesModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('gradesModal').classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('gradesModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
