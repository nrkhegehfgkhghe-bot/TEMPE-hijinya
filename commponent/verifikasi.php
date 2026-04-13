<?php
// Koneksi ke database
$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Gagal terhubung ke database: " . $e->getMessage());
}

// Ambil email dan token dari URL
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (empty($email) || empty($token)) {
    die("Permintaan tidak valid.");
}

// Cari user berdasarkan email dan token
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND verification_token = :token AND is_verified = 0");
$stmt->execute([
    'email' => $email,
    'token' => $token
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Update is_verified menjadi 1
    $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = :id");
    $update->execute(['id' => $user['id']]);

    echo "<script>
        alert('Verifikasi berhasil! Silakan login.');
        window.location.href = 'index.php';
    </script>";
} else {
    echo "<script>
        alert('Token tidak valid atau akun sudah diverifikasi.');
        window.location.href = 'index.php';
    </script>";
}
?>
