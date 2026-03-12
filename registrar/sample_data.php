<?php
/**
 * Sample Raw Data for Registrar SIS Modules
 * You can include this file in your submodules to test the UI using:
 * include 'sample_data.php';
 */

// 1. info.php (Personal Info DB)
$mock_info = [
    ['student_id' => '2023-0001', 'student_name' => 'Doe, John', 'gender' => 'Male', 'dob' => '2005-04-12', 'address' => '123 Main St', 'program' => 'Grade 11 STEM', 'section' => 'A', 'status' => 'Active'],
    ['student_id' => '2023-0002', 'student_name' => 'Smith, Jane', 'gender' => 'Female', 'dob' => '2006-08-22', 'address' => '456 Oak Avenue', 'program' => 'Grade 11 ABM', 'section' => 'B', 'status' => 'Active'],
    ['student_id' => '2023-0003', 'student_name' => 'Wilson, Mark', 'gender' => 'Male', 'dob' => '2004-11-05', 'address' => '789 Pine Road', 'program' => 'Grade 12 HUMSS', 'section' => 'A', 'status' => 'Inactive']
];

// 2. contacts.php (Guardian & Emergency Contact)
$mock_contacts = [
    ['student_id' => '2023-0001', 'guardian_name' => 'Michael Doe', 'relationship' => 'Father', 'contact_number' => '0917-123-4567', 'email' => 'michael.doe@email.com', 'address' => '123 Main St, Springfield'],
    ['student_id' => '2023-0002', 'guardian_name' => 'Sarah Smith', 'relationship' => 'Mother', 'contact_number' => '0918-987-6543', 'email' => 'sarah.smith@email.com', 'address' => '456 Oak Avenue, Springfield'],
    ['student_id' => '2023-0003', 'guardian_name' => 'Robert Wilson', 'relationship' => 'Uncle', 'contact_number' => '0919-555-8888', 'email' => 'robert.wilson@email.com', 'address' => '789 Pine Road, Springfield']
];

// 3. academic.php (Academic History)
$mock_academic = [
    ['student_id' => '2023-0001', 'prev_school' => 'Lincoln High School', 'year_completed' => '2022', 'gpa' => '88.5', 'remarks' => 'Good Moral Character'],
    ['student_id' => '2023-0002', 'prev_school' => 'Washington Academy', 'year_completed' => '2022', 'gpa' => '92.0', 'remarks' => 'With Honors'],
    ['student_id' => '2023-0003', 'prev_school' => 'Roosevelt High', 'year_completed' => '2021', 'gpa' => '84.0', 'remarks' => 'Transferee']
];

// 4. health.php (Health Record Log)
$mock_health = [
    ['student_id' => '2023-0001', 'blood_type' => 'O+', 'allergies' => 'Peanuts', 'medical_conditions' => 'Asthma', 'emergency_med' => 'Inhaler'],
    ['student_id' => '2023-0002', 'blood_type' => 'A-', 'allergies' => 'None', 'medical_conditions' => 'None', 'emergency_med' => 'None'],
    ['student_id' => '2023-0003', 'blood_type' => 'B+', 'allergies' => 'Dust', 'medical_conditions' => 'Migraines', 'emergency_med' => 'Painkillers']
];

// 5. rfid.php (RFID Log / ID Scanning)
$mock_rfid_logs = [
    ['log_id' => 1, 'rfid_uid' => '5A:4B:3C:2D', 'student_id' => '2023-0001', 'name' => 'John Doe', 'timestamp' => '2026-03-08 07:30:15', 'type' => 'Time In'],
    ['log_id' => 2, 'rfid_uid' => '9F:8E:7D:6C', 'student_id' => '2023-0002', 'name' => 'Jane Smith', 'timestamp' => '2026-03-08 07:35:42', 'type' => 'Time In'],
    ['log_id' => 3, 'rfid_uid' => '11:22:33:44', 'student_id' => '2023-0003', 'name' => 'Mark Wilson', 'timestamp' => '2026-03-08 08:05:10', 'type' => 'Late In']
];

// 6. id_generator.php (ID Generation requests)
$mock_id_queue = [
    ['request_id' => 101, 'student_id' => '2023-0001', 'name' => 'John Doe', 'request_date' => '2026-03-06', 'status' => 'Printed'],
    ['request_id' => 102, 'student_id' => '2023-0002', 'name' => 'Jane Smith', 'request_date' => '2026-03-07', 'status' => 'Pending'],
    ['request_id' => 103, 'student_id' => '2023-0003', 'name' => 'Mark Wilson', 'request_date' => '2026-03-08', 'status' => 'Processing']
];

// 7. docu.php (Document Requests)
$mock_documents = [
    ['request_id' => 501, 'student_id' => '2023-0001', 'document_type' => 'Form 137', 'purpose' => 'Transfer', 'request_date' => '2026-03-05', 'status' => 'Ready for Pickup'],
    ['request_id' => 502, 'student_id' => '2023-0002', 'document_type' => 'Good Moral', 'purpose' => 'Scholarship', 'request_date' => '2026-03-08', 'status' => 'Pending'],
    ['request_id' => 503, 'student_id' => '2023-0001', 'document_type' => 'Transcript of Records', 'purpose' => 'Employment', 'request_date' => '2026-03-07', 'status' => 'Processing']
];

// 8. tracker.php (Status Tracker)
$mock_tracker = [
    ['student_id' => '2023-0001', 'name' => 'John Doe', 'academic_status' => 'Regular', 'enrollment_status' => 'Enrolled', 'clearance' => 'Cleared'],
    ['student_id' => '2023-0002', 'name' => 'Jane Smith', 'academic_status' => 'Regular', 'enrollment_status' => 'Enrolled', 'clearance' => 'Cleared'],
    ['student_id' => '2023-0003', 'name' => 'Mark Wilson', 'academic_status' => 'Irregular', 'enrollment_status' => 'Dropped', 'clearance' => 'Pending']
];

// 9. storage.php (Digital File Storage)
$mock_storage = [
    ['file_id' => 1, 'student_id' => '2023-0001', 'filename' => 'birth_certificate_john.pdf', 'file_type' => 'Birth Certificate', 'upload_date' => '2023-08-15', 'size' => '1.2MB'],
    ['file_id' => 2, 'student_id' => '2023-0002', 'filename' => 'form138_jane.jpg', 'file_type' => 'Form 138', 'upload_date' => '2023-08-16', 'size' => '850KB'],
    ['file_id' => 3, 'student_id' => '2023-0003', 'filename' => 'medical_clearance_mark.pdf', 'file_type' => 'Medical Clearance', 'upload_date' => '2023-08-20', 'size' => '2.4MB']
];

// 10. masterlist.php (Masterlist Generator)
$mock_masterlist = [
    ['no' => 1, 'student_id' => '2023-0001', 'name' => 'Doe, John A.', 'course_grade' => 'Grade 11', 'section' => 'A', 'gender' => 'M'],
    ['no' => 2, 'student_id' => '2023-0002', 'name' => 'Smith, Jane B.', 'course_grade' => 'Grade 11', 'section' => 'A', 'gender' => 'F'],
    ['no' => 3, 'student_id' => '2023-0003', 'name' => 'Wilson, Mark C.', 'course_grade' => 'Grade 12', 'section' => 'B', 'gender' => 'M']
];
?>
