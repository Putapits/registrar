<?php
/**
 * Registrar SIS Data Loader
 * This file connects to the database and populates the data variables
 * used by all subsystems.
 */

require_once 'db_connection.php';

// Fetch data from database
$mock_info = fetchAll($conn, 'personal_info');
$mock_contacts = [];
$result = $conn->query("SELECT sc.*, pi.student_name FROM student_contacts sc LEFT JOIN personal_info pi ON sc.student_id = pi.student_id");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $mock_contacts[] = $row;
    }
}
$mock_academic = [];
$res_ac = $conn->query("SELECT ah.*, pi.student_name FROM academic_history ah LEFT JOIN personal_info pi ON ah.student_id = pi.student_id ORDER BY ah.school_year DESC");
if ($res_ac && $res_ac->num_rows > 0) {
    while($row = $res_ac->fetch_assoc()) {
        $academic_id = $row['id'];
        $row['subjects'] = [];
        $res_sub = $conn->query("SELECT * FROM academic_subjects WHERE academic_id = $academic_id");
        while($sub = $res_sub->fetch_assoc()) {
            $row['subjects'][] = $sub;
        }
        $mock_academic[] = $row;
    }
}
$mock_health = [];
$res_health = $conn->query("SELECT hr.*, pi.student_name, pi.program, pi.section FROM health_records hr LEFT JOIN personal_info pi ON hr.student_id = pi.student_id");
if ($res_health && $res_health->num_rows > 0) {
    while ($row = $res_health->fetch_assoc()) {
        $sid = $conn->real_escape_string($row['student_id']);
        $row['logs'] = [];
        $res_logs = $conn->query("SELECT * FROM health_logs WHERE student_id = '$sid' ORDER BY log_date DESC");
        if ($res_logs) {
            while ($log = $res_logs->fetch_assoc()) {
                $row['logs'][] = $log;
            }
        }
        $mock_health[] = $row;
    }
}

$mock_rfid_logs = fetchAll($conn, 'rfid_logs');
$mock_id_queue = fetchAll($conn, 'id_requests');
$mock_documents = [];
$res_doc = $conn->query("SELECT dr.*, pi.student_name FROM document_requests dr LEFT JOIN personal_info pi ON dr.student_id = pi.student_id ORDER BY dr.request_date DESC");
if ($res_doc && $res_doc->num_rows > 0) {
    while($row = $res_doc->fetch_assoc()) {
        $mock_documents[] = $row;
    }
}
$mock_tracker = fetchAll($conn, 'status_tracker');
$mock_storage = [];
$res_store = $conn->query("SELECT ds.*, pi.student_name FROM digital_storage ds LEFT JOIN personal_info pi ON ds.student_id = pi.student_id ORDER BY ds.upload_date DESC");
if ($res_store && $res_store->num_rows > 0) {
    while($row = $res_store->fetch_assoc()) {
        $mock_storage[] = $row;
    }
}

// Special case for Masterlist: transform personal_info for the masterlist view
$mock_masterlist = [];
$count = 1;
foreach ($mock_info as $student) {
    // Apply filters
    if (!empty($_GET['grade']) && $student['program'] !== $_GET['grade']) continue;
    if (!empty($_GET['section']) && $student['section'] !== $_GET['section']) continue;
    if (!empty($_GET['status']) && $student['status'] !== $_GET['status']) continue;

    $mock_masterlist[] = [
        'no' => $count++,
        'student_id' => $student['student_id'],
        'name' => $student['student_name'],
        'course_grade' => $student['program'],
        'section' => $student['section'],
        'status' => $student['status'] ?? 'Active'
    ];
}

// Fetch student identity info (QR and RFID assignments)
$mock_identities = [];
$res_id = $conn->query("SELECT * FROM student_ids");
if ($res_id && $res_id->num_rows > 0) {
    while($row = $res_id->fetch_assoc()) {
        $mock_identities[$row['student_id']] = $row;
    }
}
?>

