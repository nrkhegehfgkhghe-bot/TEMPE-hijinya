<?php
// File: api/register.php - Fixed version

header('Content-Type: application/json');

try {
    require_once 'db_connect.php';

    $response = array('status' => 'error', 'message' => 'Terjadi kesalahan.');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['role'])) {
        throw new Exception('Semua field harus diisi.');
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi
    if (empty($username) || empty($email) || empty($password)) {
        throw new Exception('Semua field harus diisi.');
    }
    if (strlen($username) < 3) {
        throw new Exception('Username minimal 3 karakter.');
    }
    if (strlen($password) < 3) {
        throw new Exception('Password minimal 3 karakter.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid.');
    }

    $allowed_roles = ['admin', 'pengunjung', 'seller', 'operator'];
    if (!in_array($role, $allowed_roles)) {
        throw new Exception('Role tidak valid.');
    }

    // Check username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE nama = ? OR username = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Username sudah digunakan.');
    }

    // Check email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Email sudah digunakan.');
    }

    // Insert
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Registrasi berhasil! Silakan login.';
    } else {
        throw new Exception('Insert gagal: ' . $conn->error);
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (Error $e) {
    $response['message'] = 'Fatal error: ' . $e->getMessage();
}

if (isset($conn)) {
    $conn->close();
}

echo json_encode($response);
?>

