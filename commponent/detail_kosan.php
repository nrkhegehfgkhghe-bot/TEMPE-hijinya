<?php
session_start();

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'penyewa') {
    header("Location: login.php");
    exit();
}

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

// Ambil daftar kamar kosong
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE status = 'Kosong'");
$stmt->execute();
$rooms = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Kamar Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h1 class="mb-4 text-success fw-bold">📝 Form Pemesanan Kamar Kos</h1>
    <?php include "layout/navbar.html"; ?>

    <?php if (empty($rooms)): ?>
        <div class="alert alert-warning">Tidak ada kamar kosong yang tersedia saat ini.</div>
    <?php else: ?>
        <form method="POST" action="proses_pesan.php" class="p-4 bg-light rounded shadow-sm">
            <div class="mb-3">
                <label for="room_id" class="form-label">Pilih Kamar Kosong</label>
                <select class="form-select" id="room_id" name="room_id" required>
                    <option value="">-- Pilih Kamar --</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['id'] ?>">
                            Kamar <?= htmlspecialchars($room['number']) ?> - Rp<?= number_format($room['price']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                🛒 Pesan Sekarang
            </button>
        </form>
    <?php endif; ?>

</body>
</html>
