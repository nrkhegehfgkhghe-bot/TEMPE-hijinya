<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'db_connect.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

$nama = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? '');

if (empty($nama) || empty($email) || empty($password) || empty($role)) {
    $response['message'] = 'Semua field wajib diisi';
    echo json_encode($response);
    exit;
}

$allowed_roles = ['admin', 'operator', 'seller', 'pengunjung'];
if (!in_array($role, $allowed_roles)) {
    $response['message'] = 'Role tidak valid';
    echo json_encode($response);
    exit;
}

// Check duplicate nama
$stmt = $conn->prepare("SELECT id FROM users WHERE nama = ?");
$stmt->bind_param("s", $nama);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $response['message'] = 'Nama sudah digunakan';
    echo json_encode($response);
    exit;
}

// Check duplicate email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $response['message'] = 'Email sudah terdaftar';
    echo json_encode($response);
    exit;
}

// Hash password & insert
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);

if ($stmt->execute()) {
    $response = [
        'status' => 'success', 
        'message' => 'Registrasi berhasil! Silakan login.'
    ];
} else {
    $response['message'] = 'Gagal simpan: ' . $conn->error;
}

echo json_encode($response);
?>

