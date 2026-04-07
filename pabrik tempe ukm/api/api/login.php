<?php
header('Content-Type: application/json');

try {
    require_once 'db_connect.php';

    $response = array('status' => 'error', 'message' => 'Terjadi kesalahan.');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        throw new Exception('Username dan password harus diisi.');
    }

    $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE nama = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $response['status'] = 'success';
            $response['user'] = array(
                'id' => $user['id'],
                'nama' => $user['nama'],
                'role' => $user['role']
            );
            // In production, set session here: session_start(); $_SESSION['user_id'] = $user['id'];
        } else {
            throw new Exception('Password salah.');
        }
    } else {
        throw new Exception('Username atau email tidak ditemukan.');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>

