<?php
session_start();

// Hanya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Halaman ini hanya untuk admin.");
}

// Konfigurasi database
$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';

// Lokasi mysqldump (ganti sesuai lokasi XAMPP kamu jika berbeda)
$mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; // jalur lengkap

// Lokasi & nama file hasil backup
$timestamp = date("Ymd_His");
$filename = "backup_$timestamp.sql";
$filepath = __DIR__ . "/backup/$filename";

// Jalankan perintah backup
$command = "\"$mysqldumpPath\" --user=$username --password=$password --host=$host $dbname > \"$filepath\"";
system($command, $result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Backup Database - KosMen.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #121212; color: #fff; padding: 40px; }
        .container { max-width: 600px; margin: auto; }
    </style>
</head>
<body>
<div class="container text-center">
    <h2>🔄 Backup Database KosMen.com</h2>
    <hr>
    <?php if ($result === 0): ?>
        <div class="alert alert-success">
            ✅ Backup berhasil!<br>
            File disimpan: <code>backup/<?= $filename ?></code>
        </div>
        <a href="backup/<?= $filename ?>" class="btn btn-success mt-2">⬇️ Download Backup</a>
    <?php else: ?>
        <div class="alert alert-danger">
            ❌ Backup gagal!<br>
            Periksa apakah file <code>mysqldump.exe</code> ada di:<br>
            <code><?= $mysqldumpPath ?></code><br>
            dan folder <code>/backup</code> dapat ditulis.
        </div>
    <?php endif; ?>
    <div class="mt-4">
        <a href="admin_dashboard.php" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>
