<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];

// Get Student Info
$q_info = $conn->prepare("SELECT student_name, student_id FROM personal_info WHERE student_id = ?");
$q_info->bind_param("s", $student_id);
$q_info->execute();
$student_info = $q_info->get_result()->fetch_assoc();

// Get Files with filter
$file_type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$sql = "SELECT * FROM digital_storage WHERE student_id = ?";
if ($file_type_filter) {
    $sql .= " AND file_type = '" . $conn->real_escape_string($file_type_filter) . "'";
}
$sql .= " ORDER BY upload_date DESC";

$q_files = $conn->prepare($sql);
$q_files->bind_param("s", $student_id);
$q_files->execute();
$files = $q_files->get_result();

// Get unique types for filter
$q_types = $conn->prepare("SELECT DISTINCT file_type FROM digital_storage WHERE student_id = ?");
$q_types->bind_param("s", $student_id);
$q_types->execute();
$types = $q_types->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Files - Student Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1; --primary-dark: #4f46e5; --secondary: #10b981;
            --bg: #f8fafc; --surface: #ffffff; --text: #1e293b; --text-muted: #64748b;
            --sidebar-width: 280px; --radius: 16px; --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Outfit',sans-serif; }
        body { background: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }

        /* Sidebar Styles */
        .sidebar { width: var(--sidebar-width); background: #17388A; backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-right: none; color: white; position: fixed; height: 100vh; left: 0; top: 0; padding-top: 2rem; z-index: 100; transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-brand { padding: 0 2rem 2rem 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 15px; }
        .sidebar-brand i { font-size: 2rem; color: var(--primary); background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 12px; }
        .sidebar-brand h2 { font-size: 1.3rem; font-weight: 700; letter-spacing: -0.5px; line-height: 1.2; color: white; }
        .sidebar-brand h2 span { display: block; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); font-weight: 500; margin-top: 4px; }
        
        .nav-list { list-style: none; padding: 0 1.2rem; }
        .nav-item { margin-bottom: 0.4rem; }
        .nav-link { display: flex; align-items: center; height: 50px; padding: 1rem 1.4rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: var(--radius); transition: all 0.3s; gap: 14px; font-weight: 500; }
        .nav-link i { width: 22px; text-align: center; font-size: 1.25rem; color: rgba(255, 255, 255, 0.7); }
        .nav-link:hover { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25); }
        .nav-link.active i { color: white; }

        .main-content { flex: 1; margin-left: var(--sidebar-width); padding: 2.5rem 3rem; }
        
        .storage-header { background: linear-gradient(135deg, #17388A 0%, #2563eb 100%); color: white; padding: 2.5rem; border-radius: var(--radius); margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow); position: relative; overflow: hidden; }
        .header-bg { position: absolute; right: -20px; bottom: -20px; font-size: 150px; opacity: 0.1; transform: rotate(-15deg); }
        .student-meta h1 { font-size: 2rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 5px; }
        .student-meta p { opacity: 0.8; font-weight: 500; }

        .controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .filter-wrap { display: flex; align-items: center; gap: 12px; }
        .filter-wrap label { font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        .filter-wrap select { padding: 0.6rem 1.2rem; border-radius: 10px; border: 1.5px solid #e2e8f0; background: white; font-weight: 600; outline: none; }
        
        .storage-card { background: var(--surface); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1.25rem 1.5rem; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; background: #f8fafc; }
        td { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f8fafc; font-size: 0.95rem; vertical-align: middle; }
        tr:hover { background: #fcfdfe; }

        .file-info { display: flex; align-items: center; gap: 12px; }
        .file-icon { width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--text-muted); }
        .file-info .name { font-weight: 700; color: var(--text); }
        .file-info .size { font-size: 0.75rem; color: var(--text-muted); display: block; }

        .status-pill { padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-available { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-verified { background: rgba(16, 185, 129, 0.1); color: #10b981; }

        .btn-action { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: var(--text-muted); text-decoration: none; border: 1px solid #e2e8f0; transition: 0.2s; }
        .btn-action:hover { background: var(--primary); color: white; border-color: var(--primary); }
        
        /* Modal for Preview */
        .modal { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 2rem; }
        .modal.active { display: flex; }
        .modal-content { background: white; width: 100%; max-width: 900px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; }
        .modal-header { padding: 1rem 1.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { height: 70vh; background: #f1f5f9; position: relative; }
        .close-modal { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8; }
        
        .preview-placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #94a3b8; }
        .preview-placeholder i { font-size: 5rem; margin-bottom: 1rem; opacity: 0.3; }

        .btn-upload { background: var(--primary); color: white; border: none; padding: 0.7rem 1.5rem; border-radius: 10px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); }
        .btn-upload:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .student-info h1 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="storage-header">
            <div class="header-bg"><i class="fa-solid fa-folder-open"></i></div>
            <div class="student-meta">
                <p>Welcome back,</p>
                <h1><?php echo htmlspecialchars($student_info['student_name'] ?? 'Student'); ?></h1>
                <p><i class="fa-solid fa-id-badge" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($student_info['student_id'] ?? $student_id); ?></p>
            </div>
            <button class="btn-upload" onclick="alert('Upload feature requires administrative approval.')"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Supporting File</button>
        </header>

        <section class="controls">
            <div class="filter-wrap">
                <label>Filter By Type</label>
                <select name="type" onchange="location.href='storage.php?type=' + this.value">
                    <option value="">All Documents</option>
                    <?php while($t = $types->fetch_assoc()): ?>
                        <option value="<?php echo $t['file_type']; ?>" <?php echo $file_type_filter == $t['file_type'] ? 'selected' : ''; ?>><?php echo $t['file_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">
                Showing <?php echo $files->num_rows; ?> total documents
            </div>
        </section>

        <div class="storage-card">
            <table>
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Type</th>
                        <th>Upload Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($files->num_rows > 0): ?>
                        <?php while ($row = $files->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="file-info">
                                        <div class="file-icon">
                                            <?php 
                                            $ext = pathinfo($row['filename'], PATHINFO_EXTENSION);
                                            if (in_array($ext, ['jpg', 'jpeg', 'png'])) echo '<i class="fa-solid fa-file-image"></i>';
                                            else echo '<i class="fa-solid fa-file-pdf"></i>';
                                            ?>
                                        </div>
                                        <div>
                                            <span class="name"><?php echo $row['filename']; ?></span>
                                            <span class="size"><?php echo $row['size']; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span style="font-weight: 600; font-size: 0.85rem; color: #475569;"><?php echo $row['file_type']; ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($row['upload_date'])); ?></td>
                                <td>
                                    <?php 
                                    $st = strtolower($row['status'] ?? 'available');
                                    echo "<span class='status-pill status-$st'>$st</span>";
                                    ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn-action" onclick="openPreview('<?php echo $row['filename']; ?>')" title="Preview"><i class="fa-solid fa-eye"></i></button>
                                        <a href="#" class="btn-action" title="Download" onclick="alert('Digital copies are for viewing. Contact registrar for official transcripts.')"><i class="fa-solid fa-download"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">No documents found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Preview Modal -->
    <div class="modal" id="previewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="previewTitle">Document Preview</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="previewBody">
                <!-- Content will be injected by JS -->
            </div>
        </div>
    </div>

    <script>
        function openPreview(name) {
            const previewTitle = document.getElementById('previewTitle');
            const previewBody = document.getElementById('previewBody');
            const ext = name.split('.').pop().toLowerCase();
            
            previewTitle.innerText = 'Viewing: ' + name;
            previewBody.innerHTML = ''; // Clear previous

            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                // Show Image
                previewBody.innerHTML = `
                    <div style="display:flex; align-items:center; justify-content:center; height:100%; padding:20px;">
                        <img src="../registrar/uploads/${name}" alt="${name}" style="max-width:100%; max-height:100%; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.1);" 
                             onerror="this.src='https://via.placeholder.com/600x400?text=File+Not+Yet+Indexed'; this.style.opacity='0.5';">
                    </div>`;
            } else if (ext === 'pdf') {
                // Show PDF (Placeholder or Iframe)
                previewBody.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i>
                        <p style="font-weight:700; color:var(--text); margin-top:1rem;">${name}</p>
                        <p style="font-size: 0.8rem; margin-top: 10px; color:var(--text-muted);">PDF previews are optimized for download.</p>
                        <a href="../registrar/uploads/${name}" target="_blank" class="btn-upload" style="margin-top:20px; text-decoration:none; background:var(--secondary);">
                            <i class="fa-solid fa-external-link"></i> Open Full Document
                        </a>
                    </div>`;
            } else {
                // Generic placeholder
                previewBody.innerHTML = `
                    <div class="preview-placeholder">
                        <i class="fa-solid fa-file"></i>
                        <p>${name}</p>
                        <p style="font-size: 0.8rem; margin-top: 10px;">[ Preview is read-only. Close to exit. ]</p>
                    </div>`;
            }

            document.getElementById('previewModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('previewModal').classList.remove('active');
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal')) closeModal();
        };
    </script>
</body>
</html>
