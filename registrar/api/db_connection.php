<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registrar_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists (in case the user hasn't run the SQL script)
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Function to fetch all rows from a table
function fetchAll($conn, $table) {
    $data = [];
    try {
        $sql = "SELECT * FROM $table";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
    } catch (Exception $e) {
        // Log error or handle missing table
        // For now, we return empty to prevent fatal crashes
        // error_log($e->getMessage());
    }
    return $data;
}
?>
