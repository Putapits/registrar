<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_doc'])) {
    $doc_type = $_POST['document_type'];
    $purpose = $_POST['purpose'];
    $copies = (int)$_POST['copies'];
    $now = date('Y-m-d');
    
    // In the real system, copies might be separate field. 
    // I'll just append it to purpose or handle it if there's a column.
    // Database schema: request_id, student_id, document_type, purpose, request_date, status
    
    $stmt = $conn->prepare("INSERT INTO document_requests (student_id, document_type, purpose, request_date, status) VALUES (?, ?, ?, ?, 'Pending')");
    $full_purpose = $purpose . " (" . $copies . " copies)";
    $stmt->bind_param("ssss", $student_id, $doc_type, $full_purpose, $now);
    
    if ($stmt->execute()) {
        $message = "Request submitted successfully!";
    } else {
        $message = "Error submitting request.";
    }
}

$q_list = $conn->prepare("SELECT * FROM document_requests WHERE student_id = ? ORDER BY request_date DESC");
$q_list->bind_param("s", $student_id);
$q_list->execute();
$requests = $q_list->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Document - Student Portal</title>
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
        .form-card, .table-card { background: white; padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.05); margin-bottom: 3rem; }
        .input-group { margin-bottom: 1.5rem; }
        .input-group label { display: block; margin-bottom: 0.5rem; color: #64748b; font-weight: 600; font-size: 0.9rem; }
        .input-group input, .input-group select, .input-group textarea { width: 100%; padding: 0.8rem 1.2rem; border-radius: 12px; border: 1px solid #e2e8f0; font-size: 1rem; }
        .btn-submit { background: #17388A; color: white; border: none; padding: 1rem 2rem; border-radius: 12px; cursor: pointer; font-weight: 700; width: 100%; transition: transform 0.3s; }
        .btn-submit:hover { transform: translateY(-3px); background: #1e4bb8; }
        table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        th { text-align: left; padding: 1.2rem; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.85rem; }
        td { padding: 1.2rem; border-bottom: 1px solid #f1f5f9; font-weight: 500; font-size: 0.95rem; }
        .status { padding: 6px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 700; }
        .status-pending { background: #fee2e2; color: #ef4444; }
        .status-processing { background: #fff7ed; color: #f97316; }
        .status-ready { background: #ecfdf5; color: #10b981; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <h1 style="margin-bottom: 2.5rem;">Document Request Portal</h1>
        
        <?php if($message): ?>
            <div style="background: <?php echo strpos($message, 'Error') === false ? '#ecfdf5' : '#fee2e2'; ?>; color: <?php echo strpos($message, 'Error') === false ? '#10b981' : '#ef4444'; ?>; padding: 1.2rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
                <i class="fa-solid <?php echo strpos($message, 'Error') === false ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-file-circle-plus"></i> New Request</h3>
            <form action="" method="POST">
                <div class="input-group">
                    <label>Document Type</label>
                    <select name="document_type" required>
                        <option value="">-- Select Document --</option>
                        <option value="Form 137">Form 137 (Permanent Record)</option>
                        <option value="Good Moral Certificate">Good Moral Certificate</option>
                        <option value="Transcript of Records">Transcript of Records (TOR)</option>
                        <option value="Certificate of Enrollment">Certificate of Enrollment</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Purpose</label>
                    <input type="text" name="purpose" placeholder="e.g. Job Application, Transfer, Scholarship" required>
                </div>
                <div class="input-group">
                    <label>Number of Copies</label>
                    <input type="number" name="copies" min="1" max="5" value="1" required>
                </div>
                <button type="submit" name="request_doc" class="btn-submit">Submit Request</button>
            </form>
        </div>

        <div class="table-card">
            <h3 style="margin-bottom: 1rem;"><i class="fa-solid fa-clock-rotate-left"></i> My Request History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Document</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($requests->num_rows > 0): ?>
                        <?php while($row = $requests->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['request_date'])); ?></td>
                            <td style="color: #17388A; font-weight: 700;"><?php echo $row['document_type']; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                            <td>
                                <?php 
                                $s_class = "status-pending";
                                if($row['status'] == 'Processing') $s_class = "status-processing";
                                if($row['status'] == 'Ready for Pickup') $s_class = "status-ready";
                                ?>
                                <span class="status <?php echo $s_class; ?>"><?php echo $row['status']; ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem;">No requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
