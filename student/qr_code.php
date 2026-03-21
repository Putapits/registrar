<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// QR Code API URL
$qr_data = $student_id;
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $qr_data;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Digital ID - Student Portal</title>
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

        .main-content { flex-grow: 1; margin-left: var(--sidebar-width); display: flex; align-items: center; justify-content: center; padding: 3rem; }
        .id-card { background: white; padding: 3rem; border-radius: 24px; box-shadow: 0 15px 50px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; border: 4px solid #17388A; }
        .qr-wrapper { background: #f8fafc; padding: 20px; border-radius: 20px; margin: 2rem 0; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); }
        .qr-wrapper img { width: 100%; height: auto; border-radius: 10px; }
        .id-details h2 { font-size: 1.5rem; color: #17388A; margin-bottom: 0.5rem; }
        .id-details p { color: #64748b; font-weight: 600; letter-spacing: 1px; }
        .btn-download { background: #17388A; color: white; padding: 1rem 1.5rem; border-radius: 12px; text-decoration: none; display: block; margin-top: 1.5rem; font-weight: 700; transition: transform 0.3s; }
        .btn-download:hover { transform: translateY(-3px); background: #1e4bb8; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="id-card">
            <div class="header-logo"><i class="fa-solid fa-graduation-cap" style="font-size: 3rem; color: #17388A;"></i></div>
            <div class="id-details" style="margin-top: 1.5rem;">
                <h2><?php echo htmlspecialchars($student_name); ?></h2>
                <p>STUDENT ID: <?php echo $student_id; ?></p>
            </div>
            <div class="qr-wrapper">
                <img src="<?php echo $qr_url; ?>" alt="Student QR Code">
            </div>
            <p style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">Please scan this code at the terminal for entrance and attendance verification.</p>
            <a href="<?php echo $qr_url; ?>" download="My_QR_Code.png" class="btn-download"><i class="fa-solid fa-download"></i> Download QR Code</a>
        </div>
    </main>
</body>
</html>
