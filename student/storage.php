<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];
$q = $conn->prepare("SELECT * FROM digital_storage WHERE student_id = ? ORDER BY upload_date DESC");
$q->bind_param("s", $student_id);
$q->execute();
$files = $q->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Files - Student Portal</title>
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
        .file-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem; }
        .file-card { background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); text-align: center; transition: transform 0.3s; }
        .file-card:hover { transform: translateY(-5px); }
        .file-icon { font-size: 3rem; color: #17388A; margin-bottom: 1rem; }
        .file-name { font-weight: 700; margin-bottom: 0.5rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .file-meta { font-size: 0.8rem; color: #94a3b8; margin-bottom: 1.5rem; }
        .btn-download { background: #f1f5f9; color: #17388A; padding: 0.8rem 1.2rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; }
        .btn-download:hover { background: #17388A; color: white; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <h1 style="margin-bottom: 2.5rem;">Digital File Storage</h1>
        <div class="file-grid">
            <?php if($files->num_rows > 0): ?>
                <?php while($row = $files->fetch_assoc()): ?>
                <div class="file-card">
                    <div class="file-icon"><i class="fa-solid fa-file-pdf"></i></div>
                    <span class="file-name" title="<?php echo $row['filename']; ?>"><?php echo $row['filename']; ?></span>
                    <div class="file-meta"><?php echo $row['file_type']; ?> &bull; <?php echo $row['size']; ?></div>
                    <a href="#" class="btn-download"><i class="fa-solid fa-download"></i> Download</a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; color: #94a3b8; padding: 5rem;">No digital records available yet.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
