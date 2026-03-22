<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    $school_year = $conn->real_escape_string($_POST['school_year']);
    $grade_level = $conn->real_escape_string($_POST['grade_level']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update personal_info
        $query = "UPDATE personal_info SET status = '$new_status', school_year = '$school_year', grade_level = '$grade_level' WHERE student_id = '$student_id'";
        if (!$conn->query($query)) {
            throw new Exception("Error updating status: " . $conn->error);
        }

        // Add to history
        $history_query = "INSERT INTO student_status_history (student_id, status, school_year, grade_level) VALUES ('$student_id', '$new_status', '$school_year', '$grade_level')";
        if (!$conn->query($history_query)) {
            throw new Exception("Error recording history: " . $conn->error);
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
