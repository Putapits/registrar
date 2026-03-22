<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION["admin_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php_input'), true);
if (!$data) $data = $_POST;

if (isset($data['action']) && $data['action'] == 'delete_storage') {
    $file_id = $conn->real_escape_string($data['file_id']);
    
    // First, get the filename to delete actually from disk
    $q = $conn->query("SELECT filename FROM digital_storage WHERE file_id = '$file_id'");
    if ($q && $q->num_rows > 0) {
        $row = $q->fetch_assoc();
        $filename = $row['filename'];
        $filepath = "../uploads/" . $filename;
        
        // Delete record from DB
        if ($conn->query("DELETE FROM digital_storage WHERE file_id = '$file_id'")) {
            // Unlink from disk if exists
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Record not found']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid Request']);
?>
