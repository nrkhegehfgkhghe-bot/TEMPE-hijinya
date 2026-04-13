<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: login.php");
    exit();
}

$reservation_id = $_GET['reservation_id'] ?? null;
if (!$reservation_id) {
    die("ID reservasi tidak ditemukan.");
}

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ambil detail pesanan
$stmt = $pdo->prepare("
    SELECT r.id AS reservation_id, r.status, rm.number AS no_kamar, rm.price, u.username AS pemilik,
           p.status AS status_bayar, p.qr_code, p.bukti_transfer
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.id
    JOIN users u ON r.pemilik_id = u.id
    LEFT JOIN pembayaran p ON p.user_id = r.user_id AND p.room_id = r.room_id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$reservation_id, $_SESSION['user_id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan atau akses ditolak.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pembayaran Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Pembayaran untuk Kamar <?= htmlspecialchars($data['no_kamar']) ?></h2>
    <p><strong>Pemilik:</strong> <?= htmlspecialchars($data['pemilik']) ?></p>
    <p><strong>Harga:</strong> Rp<?= number_format($data['price']) ?></p>

    <hr>

    <h5>Status Pembayaran: 
        <span class="badge bg-<?= $data['status_bayar'] === 'Sudah Dibayar' ? 'success' : 'warning' ?>">
            <?= $data['status_bayar'] ?? 'Belum Dibayar' ?>
        </span>
    </h5>

    <?php if ($data['status_bayar'] === 'Sudah Dibayar'): ?>
        <p><strong>Bukti Transfer:</strong></p>
        <?php if ($data['bukti_transfer']): ?>
            <img src="uploads/<?= htmlspecialchars($data['bukti_transfer']) ?>" width="300" class="img-thumbnail">
        <?php else: ?>
            <em>(Tidak tersedia)</em>
        <?php endif; ?>
    <?php else: ?>
        <div class="mt-4">
            <p>Silakan lakukan pembayaran dengan scan QR GoPay berikut:</p>
            <img src="uploads/<?= htmlspecialchars($data['qr_code']) ?>" width="200"><br><br>

            <form action="upload_bukti.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="reservation_id" value="<?= $data['reservation_id'] ?>">
                <label for="bukti">Upload Bukti Transfer (gambar):</label>
                <input type="file" name="bukti" accept="image/*" class="form-control" required>
                <button type="submit" class="btn btn-success mt-2">Kirim Bukti</button>
            </form>
        </div>
    <?php endif; ?>

    <a href="dashboard_penyewa.php" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>
</body>
</html>
