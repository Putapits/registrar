<?php
session_start();
if (!isset($_SESSION['student_db_id'])) {
    header('Location: ../index.php');
    exit();
}
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$student_status = $_SESSION['student_status'];

// Fetch some quick stats if needed, or just student details
$q_student = $conn->prepare("SELECT * FROM personal_info WHERE student_id = ?");
$q_student->bind_param("s", $student_id);
$q_student->execute();
$student_data = $q_student->get_result()->fetch_assoc();

$q_requests = $conn->prepare("SELECT COUNT(*) as c FROM document_requests WHERE student_id = ? AND status != 'Released'");
$q_requests->bind_param("s", $student_id);
$q_requests->execute();
$pending_requests = $q_requests->get_result()->fetch_assoc()['c'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Dashboard</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

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
        .nav-link.active { background: var(--primary-color); color: white; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25); }
        .nav-link.active i { color: white; }

        /* Main Content */
        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .header { height: var(--header-height); background: transparent; display: flex; align-items: center; justify-content: space-between; padding: 0 3rem; position: sticky; top: 0; z-index: 50; backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; padding: 8px 16px; background: white; border-radius: 99px; border: 1px solid rgba(0,0,0,0.05); transition: all 0.3s; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .dashboard-body { padding: 3rem; flex-grow: 1; }
        .page-title { margin-bottom: 2.5rem; color: var(--text-main); }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; }

        /* Student Info Card */
        .student-portal-info {
            background: linear-gradient(135deg, #17388A 0%, #1e4bb8 100%);
            color: white;
            padding: 2.5rem;
            border-radius: var(--radius-lg);
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 15px 35px rgba(23, 56, 138, 0.2);
            position: relative;
            overflow: hidden;
        }
        .student-portal-info::after {
            content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;
        }

        .student-main-details h2 { font-size: 2rem; margin-bottom: 0.5rem; }
        .student-main-details p { opacity: 0.8; font-size: 1.1rem; }
        .student-status-badge { background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 99px; font-weight: 600; font-size: 0.9rem; align-self: flex-start; margin-top: 10px; display: inline-block; }

        .quick-actions { display: flex; gap: 1rem; }
        .btn-action {
            background: white; color: #17388A; padding: 0.8rem 1.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 10px; transition: all 0.3s;
        }
        .btn-action:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.8rem; margin-bottom: 3rem; }
        .stat-card { background-color: var(--surface-color); backdrop-filter: blur(10px); padding: 1.8rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); display: flex; align-items: center; gap: 1.5rem; transition: all 0.4s ease; border: 1px solid rgba(255,255,255,0.8); }
        .stat-icon { width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-green { background: #ecfdf5; color: #10b981; }
        
        /* Modules Grid */
        .section-header { margin-bottom: 1.8rem; font-size: 1.3rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px; }
        .modules-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.8rem; }
        .module-card {
            background-color: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-soft); text-decoration: none; color: var(--text-main); border: 1px solid rgba(255,255,255,0.8); position: relative; overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .module-card:hover { transform: translateY(-8px) scale(1.02); box-shadow: var(--shadow-hover); background-color: white; border-color: white; }
        .module-icon { width: 55px; height: 55px; border-radius: 16px; background: #f1f5f9; color: var(--text-main); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1.5rem; transition: all 0.4s ease; }
        .module-card:hover .module-icon { background: var(--primary-color); color: white; border-radius: 50%; transform: rotate(5deg) scale(1.1); }
        .module-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.6rem; letter-spacing: -0.5px; }
        .module-desc { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; }

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div style="font-weight: 700; color: var(--text-muted); font-size: 0.9rem;"><i class="fa-solid fa-calendar-day"></i> <?php echo date('F d, Y'); ?></div>
            <div style="display: flex; align-items: center;">
                <div class="user-profile">
                    <div class="user-info" style="text-align: right;"><h4>Student</h4><p><?php echo htmlspecialchars($student_name); ?></p></div>
                    <div class="avatar"><?php echo strtoupper(substr($student_name, 0, 1)); ?></div>
                </div>
            </div>
        </header>

        <div class="dashboard-body">
            <div class="student-portal-info">
                <div class="student-main-details">
                    <p>Welcome back,</p>
                    <h2><?php echo htmlspecialchars($student_name); ?></h2>
                    <p>Student ID: <strong><?php echo $student_id; ?></strong></p>
                    <div class="student-status-badge">Status: <?php echo $student_status; ?></div>
                </div>
                <div class="quick-actions">
                    <a href="personal_info.php" class="btn-action"><i class="fa-solid fa-user-gear"></i> View Profile</a>
                    <a href="document_request.php" class="btn-action"><i class="fa-solid fa-file-circle-plus"></i> Request Document</a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fa-solid fa-file-signature"></i></div>
                    <div class="stat-details"><h3><?php echo $pending_requests; ?></h3><p>Pending Requests</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fa-solid fa-check-double"></i></div>
                    <div class="stat-details"><h3><?php echo $student_data['program'] ?? 'N/A'; ?></h3><p>Current Grade/Program</p></div>
                </div>
            </div>

            <h2 class="section-header"><i class="fa-solid fa-cubes"></i> My Modules</h2>
            <div class="modules-grid">
                <a href="personal_info.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-address-card"></i></div>
                    <h3 class="module-title">Personal Info</h3>
                    <p class="module-desc">View your personal details, address, and profile.</p>
                </a>
                <a href="academic_history.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-award"></i></div>
                    <h3 class="module-title">Academic History</h3>
                    <p class="module-desc">Check your grades and previous school records.</p>
                </a>
                <a href="health_record.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-notes-medical"></i></div>
                    <h3 class="module-title">Health Record</h3>
                    <p class="module-desc">View your health profile and clinic logs.</p>
                </a>
                <a href="qr_code.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-qrcode"></i></div>
                    <h3 class="module-title">RFID / QR Code</h3>
                    <p class="module-desc">Access your digital ID for gate scanning.</p>
                </a>
                <a href="document_request.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-file-signature"></i></div>
                    <h3 class="module-title">Document Requests</h3>
                    <p class="module-desc">Request Form 137, Good Moral, or TOR.</p>
                </a>
                <a href="storage.php" class="module-card">
                    <div class="module-icon"><i class="fa-solid fa-folder-tree"></i></div>
                    <h3 class="module-title">Digital Files</h3>
                    <p class="module-desc">View or download your submitted documents.</p>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
