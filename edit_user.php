<?php
session_start();

// Cek login dan role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Koneksi database
$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil ID user
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_dashboard.php");
    exit();
}

// Proses update jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $nama_kosan = $_POST['nama_kosan'] ?? '';
    $no_gopay = $_POST['no_gopay'] ?? '';
    $alamat_kosan = $_POST['alamat_kosan'] ?? '';

    // Validasi sederhana
    if (empty($username) || empty($email) || empty($role)) {
        $error = "Username, Email, dan Role wajib diisi.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, nama_kosan=?, no_gopay=?, alamat_kosan=? WHERE id=?");
        $stmt->execute([$username, $email, $role, $nama_kosan, $no_gopay, $alamat_kosan, $id]);

        header("Location: admin_dashboard.php");
        exit();
    }
}

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    echo "User tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Akun Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">✏️ Edit Akun Pengguna</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username *</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role *</label>
            <select name="role" class="form-select" required>
                <option value="penyewa" <?= $user['role'] === 'penyewa' ? 'selected' : '' ?>>Penyewa</option>
                <option value="pemilik" <?= $user['role'] === 'pemilik' ? 'selected' : '' ?>>Pemilik</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Kosan</label>
            <input type="text" name="nama_kosan" value="<?= htmlspecialchars($user['nama_kosan']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">No GoPay</label>
            <input type="text" name="no_gopay" value="<?= htmlspecialchars($user['no_gopay']) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat Kosan</label>
            <textarea name="alamat_kosan" class="form-control"><?= htmlspecialchars($user['alamat_kosan']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">🔙 Kembali</a>
    </form>
</div>
</body>
</html>
