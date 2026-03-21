<?php
require_once 'registrar/api/db_connection.php';

$student_id = '2023-0001';
$username = 'student';
$password = password_hash('123', PASSWORD_DEFAULT);

$sql = "INSERT IGNORE INTO student_accounts (student_id, username, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $student_id, $username, $password);
if ($stmt->execute()) {
    echo "Student account created successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?>
