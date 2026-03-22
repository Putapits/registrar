<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $redirect = $_SERVER['HTTP_REFERER']; // Automatically return to the calling page

    switch ($action) {
        case 'add_student':
            // Always auto-generate the Student ID
            $year = date("Y");
            $result = $conn->query("SELECT student_id FROM personal_info WHERE student_id LIKE '$year-%' ORDER BY student_id DESC LIMIT 1");
            if ($result && $result->num_rows > 0) {
                $last_id = $result->fetch_assoc()['student_id'];
                $num = intval(substr($last_id, 5)) + 1;
                $student_id = $year . "-" . str_pad($num, 4, '0', STR_PAD_LEFT);
            } else {
                $student_id = $year . "-0001";
            }

            $first  = $_POST['first_name'];
            $middle = $_POST['middle_name'] ?? '';
            $last   = $_POST['last_name'];
            $suffix = $_POST['suffix'] ?? '';

            $m_initial  = !empty($middle) ? strtoupper(substr($middle, 0, 1)) . ". " : "";
            $suffix_val = !empty($suffix) ? " " . $suffix : "";
            $full_name  = trim($first . " " . $m_initial . $last . $suffix_val);

            $stmt = $conn->prepare("INSERT INTO personal_info (student_id, first_name, middle_name, last_name, suffix, student_name, gender, dob, pob, civil_status, permanent_address, present_address, contact_number, email, nationality, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
            $stmt->bind_param("sssssssssssssss",
                $student_id,
                $first,
                $middle,
                $last,
                $suffix,
                $full_name,
                $_POST['gender'],
                $_POST['dob'],
                $_POST['pob'],
                $_POST['civil_status'],
                $_POST['permanent_address'],
                $_POST['present_address'],
                $_POST['contact_number'],
                $_POST['email'],
                $_POST['nationality']
            );

            // Execute immediately and redirect with the generated ID
            if ($stmt->execute()) {
                $stmt->close();
                $base = $redirect;
                $sep  = strpos($base, '?') !== false ? '&' : '?';
                header("Location: " . $base . $sep . "new_id=" . urlencode($student_id));
            } else {
                echo "Error: " . $stmt->error;
            }
            exit();

        case 'add_contact':
            $stmt = $conn->prepare("INSERT INTO student_contacts (student_id, guardian_name, relationship, contact_number, email, address, guardian_occupation, emergency_name, emergency_relationship, emergency_contact, emergency_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssss", 
                $_POST['student_id'], 
                $_POST['guardian_name'], 
                $_POST['relationship'], 
                $_POST['contact_number'], 
                $_POST['email'], 
                $_POST['address'],
                $_POST['guardian_occupation'],
                $_POST['emergency_name'],
                $_POST['emergency_relationship'],
                $_POST['emergency_contact'],
                $_POST['emergency_address']
            );
            break;

        case 'add_academic':
            $is_trans = isset($_POST['is_transferee']) ? 1 : 0;
            $stmt = $conn->prepare("INSERT INTO academic_history (student_id, school_year, grade_level, section, academic_status, is_transferee, program, prev_school, gpa, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssissss", 
                $_POST['student_id'], 
                $_POST['school_year'], 
                $_POST['grade_level'], 
                $_POST['section'], 
                $_POST['academic_status'], 
                $is_trans, 
                $_POST['program'], 
                $_POST['prev_school'], 
                $_POST['gpa'], 
                $_POST['remarks']
            );
            
            if ($stmt->execute()) {
                $academic_id = $conn->insert_id;
                
                // Save subjects if provided
                if (isset($_POST['subjects']) && is_array($_POST['subjects'])) {
                    $sub_stmt = $conn->prepare("INSERT INTO academic_subjects (academic_id, subject_name, grade) VALUES (?, ?, ?)");
                    foreach ($_POST['subjects'] as $index => $sub_name) {
                        if (!empty($sub_name)) {
                            $grade = $_POST['grades'][$index] ?? 0;
                            $sub_stmt->bind_param("isd", $academic_id, $sub_name, $grade);
                            $sub_stmt->execute();
                        }
                    }
                    $sub_stmt->close();
                }
                
                header("Location: " . $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . "success=1");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
            break;

        case 'add_health':
            $stmt = $conn->prepare("INSERT INTO health_records (student_id, blood_type, height, weight, allergies, existing_conditions, medications, emergency_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss",
                $_POST['student_id'],
                $_POST['blood_type'],
                $_POST['height'],
                $_POST['weight'],
                $_POST['allergies'],
                $_POST['existing_conditions'],
                $_POST['medications'],
                $_POST['emergency_notes']
            );
            break;

        case 'add_rfid':
            $stmt = $conn->prepare("INSERT INTO rfid_logs (rfid_uid, student_id, name, type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $_POST['rfid_uid'], $_POST['student_id'], $_POST['name'], $_POST['type']);
            break;

        case 'add_id_request':
            $stmt = $conn->prepare("INSERT INTO id_requests (student_id, name, request_date, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $_POST['student_id'], $_POST['name'], $_POST['request_date'], $_POST['status']);
            break;

        case 'add_tracker':
            $stmt = $conn->prepare("INSERT INTO status_tracker (student_id, academic_status, enrollment_status, clearance) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $_POST['student_id'], $_POST['academic_status'], $_POST['enrollment_status'], $_POST['clearance']);
            break;

        case 'add_storage':
            $student_id = $_POST['student_id'];
            $file_type = $_POST['file_type'];
            $upload_date = $_POST['upload_date'];
            
            // Handle Actual File Upload
            $filename = $_POST['filename']; // Fallback
            $size = $_POST['size']; // Fallback
            
            if (isset($_FILES['dummy_file']) && $_FILES['dummy_file']['error'] == 0) {
                $filename = basename($_FILES['dummy_file']['name']);
                $size = number_format($_FILES['dummy_file']['size'] / 1048576, 2) . " MB";
                $target_dir = "../uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                move_uploaded_file($_FILES['dummy_file']['tmp_name'], $target_dir . $filename);
            }

            $stmt = $conn->prepare("INSERT INTO digital_storage (student_id, filename, file_type, upload_date, size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $student_id, $filename, $file_type, $upload_date, $size, $_SESSION['admin_user']);
            break;

        case 'add_doc_request':
            $stmt = $conn->prepare("INSERT INTO document_requests (student_id, document_type, purpose, request_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $_POST['student_id'], $_POST['document_type'], $_POST['purpose'], $_POST['request_date'], $_POST['status']);
            break;
        case 'update_doc_status':
            $req_id = $_POST['request_id'];
            $status = $_POST['status'];
            $stmt = $conn->prepare("UPDATE document_requests SET status = ? WHERE request_id = ?");
            $stmt->bind_param("si", $status, $req_id);
            break;

        case 'create_account':
            $student_id = $_POST['student_id'];
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            // Link account to the student
            $stmt = $conn->prepare("INSERT INTO student_accounts (student_id, username, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $student_id, $username, $password);
            
            if ($stmt->execute()) {
                header("Location: " . $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . "account_created=1");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            break;

        case 'assign_id':
            $sid = $_POST['student_id'];
            $qr = $_POST['qr_code'] ?? '';
            $rfid = $_POST['rfid_uid'] ?? '';
            
            // Calculate status
            $status = 'Pending';
            if (!empty($qr) && !empty($rfid)) $status = 'Complete';
            elseif (!empty($qr) || !empty($rfid)) $status = 'Partial';
            
            // Upsert logic
            $check = $conn->query("SELECT id FROM student_ids WHERE student_id = '$sid'");
            if ($check->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE student_ids SET qr_code = ?, rfid_uid = ?, status = ? WHERE student_id = ?");
                $stmt->bind_param("ssss", $qr, $rfid, $status, $sid);
            } else {
                $stmt = $conn->prepare("INSERT INTO student_ids (student_id, qr_code, rfid_uid, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $sid, $qr, $rfid, $status);
            }
            break;

        default:
            die("Invalid action.");
    }

    if ($stmt->execute()) {
        header("Location: " . $redirect . (strpos($redirect, '?') !== false ? '&' : '?') . "success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
