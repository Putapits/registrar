<?php
session_start();
require_once 'db_connection.php';

// Try to create the admins table if it doesn't exist
$table_sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_sql);

// Check if there are any admins, if not, create a default one
$check_sql = "SELECT id FROM admins LIMIT 1";
$result = $conn->query($check_sql);
if ($result->num_rows == 0) {
    $default_user = 'admin';
    $default_pass = password_hash('admin123', PASSWORD_DEFAULT);
    $default_name = 'admin';
    $insert_sql = "INSERT INTO admins (username, password, full_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sss", $default_user, $default_pass, $default_name);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Check Admin Table
    $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_user'] = $user['full_name'];
            header("Location: ../dashboard.php");
            exit();
        }
    }

    // 2. Check Student Accounts Table
    $stmt = $conn->prepare("SELECT s.id, s.username, s.password, s.student_id, s.email, p.student_name, p.status 
                             FROM student_accounts s 
                             LEFT JOIN personal_info p ON s.student_id = p.student_id 
                             WHERE s.username = ? OR s.email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($student = $result->fetch_assoc()) {
        if (password_verify($password, $student['password'])) {
            $_SESSION['student_db_id'] = $student['id'];
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['student_name'] ?? $student['username'];
            $_SESSION['student_status'] = $student['status'] ?? 'Pending Link';
            header("Location: ../../student/dashboard.php");
            exit();
        }
    }
    
    header("Location: ../../index.php?error=invalid_credentials");
    exit();
}
?>
