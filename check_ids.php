<?php
require_once 'registrar/api/db_connection.php';
$result = $conn->query("SELECT student_id, student_name FROM personal_info LIMIT 5");
while($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
