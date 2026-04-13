<?php
$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND reset_token = ?");
        $stmt->execute([$email, $token]);
        $user = $stmt->fetch();

        if ($user) {
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE email = ?");
            $stmt->execute([$new, $email]);
            $success = "Password berhasil diubah. Silakan login.";
        } else {
            $error = "Token tidak valid atau sudah kadaluarsa.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setel Ulang Password - KosMen.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
<div class="container mt-5" style="max-width: 500px;">
    <h3 class="text-center">🔐 Atur Password Baru</h3>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100">Simpan Password</button>
    </form>
</div>
</body>
</html>
