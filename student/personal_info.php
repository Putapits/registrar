<?php
session_start();
if (!isset($_SESSION['student_db_id'])) { header('Location: ../index.php'); exit(); }
require_once '../registrar/api/db_connection.php';

$student_db_id = $_SESSION['student_db_id'];
$message = "";

// 1. Fetch current info (Join personal_info and student_contacts)
$q = $conn->prepare("SELECT s.student_id, p.*, c.guardian_name, c.relationship as g_rel, c.contact_number as g_contact, c.email as g_email, c.guardian_occupation, c.address as g_address, c.emergency_name, c.emergency_relationship, c.emergency_contact, c.emergency_address 
                     FROM student_accounts s 
                     LEFT JOIN personal_info p ON s.student_id = p.student_id 
                     LEFT JOIN student_contacts c ON s.student_id = c.student_id
                     WHERE s.id = ?");
$q->bind_param("i", $student_db_id);
$q->execute();
$data = $q->get_result()->fetch_assoc();

// 2. Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_profile'])) {
    // Student Info
    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $suffix = $_POST['suffix'];
    $dob = $_POST['dob'];
    $pob = $_POST['pob'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'];
    $contact = $_POST['contact_number'];
    $civil_status = $_POST['civil_status'];
    $present_addr = $_POST['present_address'];
    $permanent_addr = $_POST['permanent_address'];
    $personal_email = $_POST['personal_email'];
    $full_name = "$last, $first $middle";

    // Guardian Info
    $g_name = $_POST['guardian_name'];
    $g_rel = $_POST['guardian_rel'];
    $g_contact = $_POST['guardian_contact'];
    $g_email = $_POST['guardian_email'];
    $g_occ = $_POST['guardian_occ'];
    $g_addr = $_POST['guardian_addr'];

    // Emergency Contact
    $e_name = $_POST['emergency_name'];
    $e_rel = $_POST['emergency_rel'];
    $e_contact = $_POST['emergency_contact'];
    $e_addr = $_POST['emergency_addr'];

    $student_id = $data['student_id'];
    
    if (empty($student_id)) {
        // Create new Student record
        $student_id = "2026-" . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $it = $conn->prepare("INSERT INTO personal_info (student_id, first_name, middle_name, last_name, suffix, student_name, dob, pob, gender, civil_status, nationality, contact_number, present_address, permanent_address, email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
        $it->bind_param("sssssssssssssss", $student_id, $first, $middle, $last, $suffix, $full_name, $dob, $pob, $gender, $civil_status, $nationality, $contact, $present_addr, $permanent_addr, $personal_email);
        $it->execute();

        // Update link in accounts
        $stmt_link = $conn->prepare("UPDATE student_accounts SET student_id = ? WHERE id = ?");
        $stmt_link->bind_param("si", $student_id, $student_db_id);
        $stmt_link->execute();
        $_SESSION['student_id'] = $student_id;
    } else {
        // Update existing Student record
        $ut = $conn->prepare("UPDATE personal_info SET first_name=?, middle_name=?, last_name=?, suffix=?, student_name=?, dob=?, pob=?, gender=?, civil_status=?, nationality=?, contact_number=?, present_address=?, permanent_address=?, email=? WHERE student_id=?");
        $ut->bind_param("sssssssssssssss", $first, $middle, $last, $suffix, $full_name, $dob, $pob, $gender, $civil_status, $nationality, $contact, $present_addr, $permanent_addr, $personal_email, $student_id);
        $ut->execute();
    }

    // Upsert Guardian & Emergency Contacts
    $chk = $conn->query("SELECT id FROM student_contacts WHERE student_id = '$student_id'");
    if ($chk->num_rows > 0) {
        $uc = $conn->prepare("UPDATE student_contacts SET guardian_name=?, relationship=?, contact_number=?, email=?, guardian_occupation=?, address=?, emergency_name=?, emergency_relationship=?, emergency_contact=?, emergency_address=? WHERE student_id=?");
        $uc->bind_param("sssssssssss", $g_name, $g_rel, $g_contact, $g_email, $g_occ, $g_addr, $e_name, $e_rel, $e_contact, $e_addr, $student_id);
        $uc->execute();
    } else {
        $ic = $conn->prepare("INSERT INTO student_contacts (student_id, guardian_name, relationship, contact_number, email, guardian_occupation, address, emergency_name, emergency_relationship, emergency_contact, emergency_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ic->bind_param("sssssssssss", $student_id, $g_name, $g_rel, $g_contact, $g_email, $g_occ, $g_addr, $e_name, $e_rel, $e_contact, $e_addr);
        $ic->execute();
    }

    $message = "Your profile has been fully updated!";
    $_SESSION['student_name'] = $full_name;

    // Refresh $data
    $q->execute();
    $data = $q->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Student Information System</title>
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
        body { background: linear-gradient(135deg, #f4f7fe 0%, #edf2f7 100%); color: var(--text-main); display:flex; min-height:100vh; }
        
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
        .page-title { margin-bottom: 2rem; }
        .profile-container { max-width: 1000px; }
        .section-card { background: white; border-radius:24px; padding: 2.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 2rem; border: 1px solid #f1f5f9; }
        .section-header { border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem; margin-bottom: 2rem; display:flex; align-items:center; gap:10px; }
        .section-header h2 { font-size: 1.25rem; font-weight: 700; color: #17388A; }
        .section-tag { padding: 4px 12px; background: rgba(99,102,241,0.1); color: var(--primary-color); border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem; }
        .form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem; }
        .input-group { display:flex; flex-direction:column; gap:8px; }
        .input-group label { font-size: 0.85rem; font-weight: 600; color: #64748b; margin-left: 4px; }
        .input-group input, .input-group select { padding: 0.8rem 1.1rem; border-radius:12px; border: 1.5px solid #e2e8f0; font-size: 0.95rem; }
        .input-group input:focus { outline:none; border-color: var(--s); box-shadow: 0 0 0 4px rgba(99,102,241,0.1); }
        
        .btn-save { background: var(--primary-color); color: white; border:none; padding: 1rem 2rem; border-radius:14px; font-weight:700; cursor:pointer; width:100%; font-size: 1.1rem; transition: all 0.3s; margin-top:1rem; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); }
        .btn-save:hover { background: var(--primary-dark); transform: translateY(-3px); }
        .alert { background: #ecfdf5; border: 1px solid #10b981; color:#10b981; padding: 1.2rem; border-radius:14px; margin-bottom:2rem; font-weight:600; }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-title">
            <h1>Complete Your Profile</h1>
            <p style="color:#64748b;">Fill in all fields to finalize your student registration.</p>
        </div>

        <?php if($message): ?><div class="alert"><i class="fa-solid fa-circle-check"></i> <?php echo $message; ?></div><?php endif; ?>

        <form action="" method="POST" class="profile-container">
            <!-- Student Information Section -->
            <div class="section-card">
                <div class="section-header">
                    <span class="section-tag">Primary Info</span>
                    <h2>Student Information</h2>
                </div>
                <div class="form-grid">
                    <div class="input-group"><label>First Name</label><input type="text" name="first_name" value="<?php echo $data['first_name']??''; ?>" required></div>
                    <div class="input-group"><label>Middle Name</label><input type="text" name="middle_name" value="<?php echo $data['middle_name']??''; ?>"></div>
                    <div class="input-group"><label>Last Name</label><input type="text" name="last_name" value="<?php echo $data['last_name']??''; ?>" required></div>
                </div>
                <div class="form-grid">
                    <div class="input-group"><label>Suffix (Optional)</label><input type="text" name="suffix" value="<?php echo $data['suffix']??''; ?>" placeholder="Jr., III"></div>
                    <div class="input-group"><label>Date of Birth</label><input type="date" name="dob" value="<?php echo $data['dob']??''; ?>" required></div>
                    <div class="input-group"><label>Place of Birth</label><input type="text" name="pob" value="<?php echo $data['pob']??''; ?>" required></div>
                </div>
                <div class="form-grid">
                    <div class="input-group"><label>Gender</label>
                        <select name="gender" required>
                            <option value="Male" <?php if(($data['gender']??'')=='Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if(($data['gender']??'')=='Female') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>
                    <div class="input-group"><label>Civil Status</label>
                        <select name="civil_status" required>
                            <option value="Single" <?php if(($data['civil_status']??'')=='Single') echo 'selected'; ?>>Single</option>
                            <option value="Married" <?php if(($data['civil_status']??'')=='Married') echo 'selected'; ?>>Married</option>
                        </select>
                    </div>
                    <div class="input-group"><label>Nationality</label><input type="text" name="nationality" value="<?php echo $data['nationality']??'Filipino'; ?>" required></div>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Contact Number</label><input type="text" name="contact_number" value="<?php echo $data['contact_number']??''; ?>" required></div>
                    <div class="input-group"><label>Personal Email Address</label><input type="email" name="personal_email" value="<?php echo $data['email']??''; ?>" required></div>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Present Address</label><input type="text" name="present_address" value="<?php echo $data['present_address']??''; ?>" required></div>
                    <div class="input-group"><label>Permanent Address</label><input type="text" name="permanent_address" value="<?php echo $data['permanent_address']??''; ?>" required></div>
                </div>
            </div>

            <!-- Guardian Information Section -->
            <div class="section-card">
                <div class="section-header">
                    <span class="section-tag" style="background:rgba(16,185,129,0.1); color:#059669;">Family</span>
                    <h2>Guardian Information</h2>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Guardian Full Name</label><input type="text" name="guardian_name" value="<?php echo $data['guardian_name']??''; ?>" required></div>
                    <div class="input-group"><label>Relationship</label><input type="text" name="guardian_rel" value="<?php echo $data['g_rel']??''; ?>" required></div>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Contact Number</label><input type="text" name="guardian_contact" value="<?php echo $data['g_contact']??''; ?>" required></div>
                    <div class="input-group"><label>Email Address</label><input type="email" name="guardian_email" value="<?php echo $data['g_email']??''; ?>"></div>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Occupation</label><input type="text" name="guardian_occ" value="<?php echo $data['guardian_occupation']??''; ?>"></div>
                    <div class="input-group"><label>Address</label><input type="text" name="guardian_addr" value="<?php echo $data['g_address']??''; ?>" required></div>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="section-card">
                <div class="section-header">
                    <span class="section-tag" style="background:rgba(239,68,68,0.1); color:#ef4444;">Safety</span>
                    <h2>Emergency Contact</h2>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Contact Person Full Name</label><input type="text" name="emergency_name" value="<?php echo $data['emergency_name']??''; ?>" required></div>
                    <div class="input-group"><label>Relationship</label><input type="text" name="emergency_rel" value="<?php echo $data['emergency_relationship']??''; ?>" required></div>
                </div>
                <div class="form-grid-2">
                    <div class="input-group"><label>Contact Number</label><input type="text" name="emergency_contact" value="<?php echo $data['emergency_contact']??''; ?>" required></div>
                    <div class="input-group"><label>Address</label><input type="text" name="emergency_addr" value="<?php echo $data['emergency_address']??''; ?>" required></div>
                </div>
            </div>

            <button type="submit" name="save_profile" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save & Submit All Details</button>
        </form>
    </main>
</body>
</html>
