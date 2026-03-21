<?php
require_once 'registrar/api/db_connection.php';

$student_id = '2026-0001';
$username = 'student';
$password = password_hash('123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("DELETE FROM student_accounts WHERE username = ? OR student_id = ?");
$stmt->bind_param("ss", $username, $student_id);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO student_accounts (student_id, username, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $student_id, $username, $password);
if ($stmt->execute()) {
    echo "Student account reset successfully.\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}
?>
