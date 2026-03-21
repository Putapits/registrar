<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_id = $_SESSION['student_id'];

// 1. Fetch Student Info
$q_info = $conn->prepare("SELECT student_name, student_id, program, section FROM personal_info WHERE student_id = ?");
$q_info->bind_param("s", $student_id);
$q_info->execute();
$student = $q_info->get_result()->fetch_assoc();

// 2. Fetch Health Profile
$q_profile = $conn->prepare("SELECT * FROM health_records WHERE student_id = ?");
$q_profile->bind_param("s", $student_id);
$q_profile->execute();
$profile = $q_profile->get_result()->fetch_assoc();

// 3. Fetch Health Logs
$q_logs = $conn->prepare("SELECT * FROM health_logs WHERE student_id = ? ORDER BY log_date DESC");
$q_logs->bind_param("s", $student_id);
$q_logs->execute();
$logs = $q_logs->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Record - Student Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #a5b4fc;
            --primary-dark: #4338ca;
            --secondary-color: #10b981;
            --health-color: #f43f5e;
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
        
        /* Header Card */
        .header-card { background: white; padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.03); margin-bottom: 2rem; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 2rem; }
        .header-icon { width: 80px; height: 80px; background: rgba(244,63,94,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--health-color); }
        .header-meta h2 { font-size: 1.8rem; font-weight: 800; color: #17388A; }
        .header-meta p { color: var(--text-muted); font-weight: 500; }

        /* Grid Layout */
        .content-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        
        .panel { background: white; padding: 2rem; border-radius: var(--radius-lg); box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; }
        .panel h3 { margin-bottom: 1.5rem; font-size: 1.15rem; display: flex; align-items: center; gap: 10px; }
        
        /* Profile Details */
        .profile-item { margin-bottom: 1.5rem; }
        .profile-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .profile-value { font-size: 1rem; font-weight: 600; color: var(--text-main); }
        .blood-type { display: inline-block; padding: 4px 12px; border-radius: 8px; background: #fee2e2; color: #ef4444; font-weight: 800; }

        /* Timeline Table */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1rem; border-bottom: 2px solid #f1f5f9; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
        td { padding: 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
        .date-col { font-weight: 700; color: var(--primary-color); }
        .comp-col { font-weight: 700; }
        .action-tag { display: inline-block; padding: 4px 10px; border-radius: 6px; background: #ecfdf5; color: #10b981; font-weight: 700; font-size: 0.75rem; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="header-card">
            <div class="header-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <div class="header-meta">
                <p>Health Information for</p>
                <h2><?php echo htmlspecialchars($student['student_name'] ?? 'Incomplete Profile'); ?></h2>
                <p>Student ID: <strong><?php echo $student['student_id'] ?? $_SESSION['student_id']; ?></strong> | <?php echo $student['program'] ?? 'N/A'; ?> - <?php echo $student['section'] ?? 'N/A'; ?></p>
            </div>
        </div>

        <div class="content-grid">
            <!-- Health Profile -->
            <div class="panel">
                <h3><i class="fa-solid fa-stethoscope" style="color: var(--health-color)"></i> Health Profile</h3>
                
                <div class="profile-item">
                    <div class="profile-label">Blood Type</div>
                    <div class="profile-value"><span class="blood-type"><?php echo $profile['blood_type'] ?? 'N/A'; ?></span></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label">Allergies</div>
                    <div class="profile-value"><?php echo ($profile['allergies'] ?? '') ?: 'None'; ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label">Existing Conditions</div>
                    <div class="profile-value"><?php echo ($profile['medical_conditions'] ?? '') ?: 'No recorded conditions'; ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label">Emergency Medications</div>
                    <div class="profile-value"><?php echo ($profile['emergency_med'] ?? '') ?: 'No specific medications'; ?></div>
                </div>

                <div class="profile-item">
                    <div class="profile-label">Medical Remarks</div>
                    <div class="profile-value" style="color: var(--text-muted); font-style: italic; font-size: 0.9rem;"><?php echo ($profile['emergency_notes'] ?? '') ?: 'No additional remarks.'; ?></div>
                </div>
            </div>

            <!-- Health Logs / Clinic Visits -->
            <div class="panel">
                <h3><i class="fa-solid fa-notes-medical" style="color: #10b981"></i> Clinic Visit History</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Complaint</th>
                            <th>Diagnosis</th>
                            <th>Action Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logs)): ?>
                            <tr><td colspan="4" style="text-align: center; padding: 3rem; color: #94a3b8;">No medical visits recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach($logs as $log): ?>
                            <tr>
                                <td class="date-col"><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                <td class="comp-col"><?php echo htmlspecialchars($log['complaint']); ?></td>
                                <td><?php echo htmlspecialchars($log['diagnosis']); ?></td>
                                <td><span class="action-tag"><?php echo htmlspecialchars($log['treatment']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 2rem; background: #fff7ed; border-left: 4px solid #f97316; padding: 1.2rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem;">
            <i class="fa-solid fa-circle-info" style="color: #f97316; font-size: 1.5rem;"></i>
            <p style="font-size: 0.9rem; color: #c2410c; font-weight: 500;">Note: Health records are managed by the school clinic. If you need to update your medical information, please visit the clinic office with supporting medical documents.</p>
        </div>
    </main>
</body>
</html>
