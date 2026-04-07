<?php
// File: api/db_connect.php (copy from pabrik-tempe-web/api)
// Konfigurasi Database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'pabrik_tempe';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    // API-friendly JSON error instead of die()
    header('Content-Type: application/json', true);
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]);
    exit;
}

$conn->set_charset("utf8mb4");
?>
