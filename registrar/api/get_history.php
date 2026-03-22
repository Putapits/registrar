<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_GET['student_id'])) {
    $student_id = $conn->real_escape_string($_GET['student_id']);
    $query = "SELECT * FROM student_status_history WHERE student_id = '$student_id' ORDER BY updated_at DESC";
    $result = $conn->query($query);
    $history = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
    }
    echo json_encode(['success' => true, 'history' => $history]);
} else {
    echo json_encode(['success' => false, 'message' => 'Missing student ID']);
}
?>
