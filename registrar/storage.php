<?php session_start(); if (!isset($_SESSION["admin_id"])) { header("Location: ../index.php"); exit(); } include "api/data_loader.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar SIS - Digital File Storage</title>
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
        .page-title { margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; }
        .page-title h1 { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
        .page-title p { color: var(--text-muted); margin-top: 0.5rem; }

        .btn-upload { background: var(--primary-color); color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
        .btn-upload:hover { transform: translateY(-2px); background: var(--primary-dark); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); }

        .data-table-container { background: var(--surface-color); backdrop-filter: blur(10px); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.8); padding: 1.5rem; }
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; text-align: left; }
        .data-table th, .data-table td { padding: 1.2rem 1.5rem; vertical-align: middle; }
        .data-table th { font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem; }
        .data-table tbody tr { transition: all 0.3s; background: white; }
        .data-table tbody tr:hover { transform: scale(1.008); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        .file-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(99, 102, 241, 0.1); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-right: 15px; }

        .btn-action {
            background: rgba(99, 102, 241, 0.08); color: var(--primary-color);
            border: none; padding: 0.5rem 1rem; border-radius: 8px;
            font-weight: 700; cursor: pointer; transition: all 0.3s;
            font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-action:hover { background: var(--primary-color); color: white; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 1000; display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s; }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-card { background: white; width: 100%; max-width: 500px; border-radius: 24px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .modal-header h2 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; }
        .close-btn { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: flex; align-items: center; justify-content: center; }

        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 1.5rem; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .form-group input, .form-group select { padding: 0.8rem 1rem; border-radius: 12px; border: 1.5px solid #e2e8f0; font-family: inherit; }

        .file-upload-zone {
            border: 2px dashed #e2e8f0; border-radius: 16px; padding: 2rem;
            text-align: center; cursor: pointer; transition: all 0.3s;
            background: #f8fafc; color: var(--text-muted); margin-bottom: 1.5rem;
        }
        .file-upload-zone:hover { border-color: var(--primary-color); background: rgba(99, 102, 241, 0.02); }
        .file-upload-zone i { font-size: 2.5rem; color: var(--primary-light); margin-bottom: 1rem; }

        .modal-footer { margin-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem; }
        .btn-submit { background: var(--primary-color); color: white; border: none; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 700; cursor: pointer; }
        .btn-cancel { background: transparent; border: 1px solid #e2e8f0; padding: 0.8rem 2rem; border-radius: 12px; font-weight: 600; cursor: pointer; }

        /* Dark Mode */
        :root[data-theme="dark"] { --bg-color: #0f172a; --surface-color: rgba(30, 41, 59, 0.85); --text-main: #f8fafc; --text-muted: #94a3b8; }
        :root[data-theme="dark"] body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        :root[data-theme="dark"] .sidebar { background: #17388A; border-right: 1px solid rgba(255, 255, 255, 0.05); }
        :root[data-theme="dark"] .user-profile, :root[data-theme="dark"] .data-table tbody tr, :root[data-theme="dark"] .modal-card, :root[data-theme="dark"] .file-upload-zone { background: #1e293b; border-color: rgba(255,255,255,0.05); color: var(--text-main); }
        :root[data-theme="dark"] .form-group input, :root[data-theme="dark"] .form-group select { background: #0f172a; border-color: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-graduation-cap"></i><h2>Registrar SIS<span>Management Portal</span></h2></div>
        <ul class="nav-list">
            <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="Personal_info.php" class="nav-link"><i class="fa-solid fa-users"></i><span>Personal Info</span></a></li>
            <li class="nav-item"><a href="Guardian&Emergency_Contact.php" class="nav-link"><i class="fa-solid fa-hands-holding-child"></i><span>Guardian & Contact</span></a></li>
            <li class="nav-item"><a href="Academic_history.php" class="nav-link"><i class="fa-solid fa-book-open"></i><span>Academic History</span></a></li>
            <li class="nav-item"><a href="Health_Record_log.php" class="nav-link"><i class="fa-solid fa-truck-medical"></i><span>Health Record Log</span></a></li>
            <li class="nav-item"><a href="id_generator.php" class="nav-link"><i class="fa-solid fa-id-card"></i><span>ID Processing</span></a></li>
            <li class="nav-item"><a href="rfid.php" class="nav-link"><i class="fa-solid fa-wifi"></i><span>RFID / QR Module</span></a></li>
            <li class="nav-item"><a href="docu.php" class="nav-link"><i class="fa-solid fa-file-invoice"></i><span>Document Requests</span></a></li>
            <li class="nav-item"><a href="tracker.php" class="nav-link"><i class="fa-solid fa-chart-line"></i><span>Status Tracker</span></a></li>
            <li class="nav-item"><a href="storage.php" class="nav-link active"><i class="fa-solid fa-folder-tree"></i><span>Digital File Storage</span></a></li>
            <li class="nav-item"><a href="masterlist.php" class="nav-link"><i class="fa-solid fa-list-ol"></i><span>Student Masterlist</span></a></li>
            <li class="nav-item"><a href="api/logout.php" class="nav-link" style="margin-top: 2rem; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i> <span style="margin-left: 10px; font-size: 0.9rem;">Search files or students...</span></div>
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
                    <h1><i class="fa-solid fa-folder-tree" style="color: var(--primary-color); margin-right: 15px;"></i>Digital File Storage</h1>
                    <p>Manage and archive digital copies of student records.</p>
                </div>
                <button class="btn-upload" onclick="openUploadModal()"><i class="fa-solid fa-cloud-arrow-up"></i> Upload New File</button>
            </div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>File ID</th>
                            <th>Student</th>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Upload Date</th>
                            <th>Uploaded By</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mock_storage as $row): ?>
                        <tr>
                            <td><span style="font-weight: 700; color: var(--text-muted);"><?php echo str_pad($row['file_id'], 3, '0', STR_PAD_LEFT); ?></span></td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700;"><?php echo $row["student_name"]; ?></span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $row["student_id"]; ?></span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="file-icon"><i class="fa-solid <?php 
                                        $ext = pathinfo($row['filename'], PATHINFO_EXTENSION);
                                        echo ($ext === 'pdf') ? 'fa-file-pdf' : 'fa-file-image'; 
                                    ?>"></i></div>
                                    <span style="font-weight: 600;"><?php echo $row["filename"]; ?></span>
                                </div>
                            </td>
                            <td><span class="status-badge" style="background:rgba(99,102,241,0.05); color:var(--primary-dark);"><?php echo $row["file_type"]; ?></span></td>
                            <td style="font-size: 0.9rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($row["upload_date"])); ?></td>
                            <td><span style="font-weight: 600; font-size: 0.9rem;"><?php echo $row["uploaded_by"]; ?></span></td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button class="btn-action" onclick="alert('Viewing: <?php echo $row['filename']; ?>')"><i class="fa-solid fa-eye"></i> View</button>
                                    <button class="btn-action" onclick="alert('Downloading: <?php echo $row['filename']; ?>')"><i class="fa-solid fa-download"></i></button>
                                    <button class="btn-action" style="background: rgba(239, 68, 68, 0.08); color: #ef4444;" onclick="deleteDocument(<?php echo $row['file_id']; ?>)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mock_storage)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 4rem; color:var(--text-muted);">No documents stored. Start by uploading a file.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function deleteDocument(id) {
            if (confirm("Are you sure you want to permanently delete this file? This action cannot be undone.")) {
                fetch('api/delete_record.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete_storage&file_id=${id}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }
    </script>

    <!-- Modal: Upload File -->
    <div class="modal-overlay" id="uploadModal">
        <div class="modal-card">
            <div class="modal-header">
                <h2>Upload Student File</h2>
                <button class="close-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="api/add_record.php" method="POST" id="uploadForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_storage">
                <input type="hidden" name="upload_date" value="<?php echo date('Y-m-d'); ?>">
                
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
                    <select name="file_type" required>
                        <option value="Birth Certificate">Birth Certificate</option>
                        <option value="Form 137">Form 137</option>
                        <option value="Transcript of Records">Transcript of Records</option>
                        <option value="Good Moral">Good Moral</option>
                        <option value="Medical Record">Medical Record</option>
                        <option value="ID Picture">ID Picture</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div class="file-upload-zone" onclick="document.getElementById('fileInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <h4 id="fileStatus">Click to select file</h4>
                    <p>PDF, PNG, or JPG (Max 10MB)</p>
                    <input type="file" id="fileInput" name="dummy_file" style="display:none;" onchange="updateFileStatus(this)">
                    <input type="hidden" name="filename" id="hiddenFilename">
                    <input type="hidden" name="size" id="hiddenSize">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Upload & Link</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal() { document.getElementById('uploadModal').classList.add('active'); }
        function closeModal() { document.getElementById('uploadModal').classList.remove('active'); }

        function updateFileStatus(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('fileStatus').innerText = file.name;
                document.getElementById('hiddenFilename').value = file.name;
                document.getElementById('hiddenSize').value = (file.size / 1024 / 1024).toFixed(2) + " MB";
            }
        }

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