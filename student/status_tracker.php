<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];
$q = $conn->prepare("SELECT * FROM status_tracker WHERE student_id = ?");
$q->bind_param("s", $student_id);
$q->execute();
$data = $q->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Tracker - Student Portal</title>
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
            --header-height: 80px;
            --radius-lg: 20px;
            --radius-md: 14px;
            --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.05);
            --shadow-hover: 0 20px 40px -10px rgba(99,102,241,0.15);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Outfit',sans-serif; }
        body { background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%); color: var(--text-main); display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-brand { padding: 0 2rem 2rem 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 15px; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary-color); background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 12px; }
        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }
        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }
        
        .nav-list { list-style: none; padding: 0 1.2rem; }
        .nav-item { margin-bottom: 0.4rem; }
        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: var(--radius-md); transition: all 0.3s; gap: 14px; font-weight: 500; }
        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }
        .nav-link:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link.active { background: var(--primary-color); color: white; box-shadow: 0 8px 16px rgba(99,102,241,0.25); }
        .nav-link.active i { color: white; }

        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); padding: 3rem; }
        .tracker-card { background: white; padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .step { display: flex; align-items: flex-start; gap: 1.5rem; margin-bottom: 2rem; position: relative; }
        .step::after { content: ''; position: absolute; left: 19px; top: 40px; width: 2px; height: calc(100% - 20px); background: #e2e8f0; }
        .step:last-child::after { display: none; }
        .step-icon { width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; z-index: 1; }
        .step.completed .step-icon { background: #10b981; color: white; }
        .step-content h4 { font-size: 1.1rem; margin-bottom: 0.2rem; }
        .step-content p { font-size: 0.9rem; color: #64748b; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <h1 style="margin-bottom: 2.5rem;">Status Tracker</h1>
        <div class="tracker-card">
            <div class="step <?php echo ($data['enrollment_status'] == 'Enrolled' ? 'completed' : ''); ?>">
                <div class="step-icon"><i class="fa-solid fa-check"></i></div>
                <div class="step-content">
                    <h4>Enrollment Status</h4>
                    <p><?php echo $data['enrollment_status'] ?? 'Pending'; ?></p>
                </div>
            </div>
            <div class="step <?php echo ($data['clearance'] == 'Cleared' ? 'completed' : ''); ?>">
                <div class="step-icon"><i class="fa-solid fa-check"></i></div>
                <div class="step-content">
                    <h4>Student Clearance</h4>
                    <p><?php echo $data['clearance'] ?? 'Pending'; ?></p>
                </div>
            </div>
            <div class="step <?php echo ($data['academic_status'] == 'Regular' ? 'completed' : ''); ?>">
                <div class="step-icon"><i class="fa-solid fa-check"></i></div>
                <div class="step-content">
                    <h4>Academic Status</h4>
                    <p><?php echo $data['academic_status'] ?? 'Pending'; ?></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
