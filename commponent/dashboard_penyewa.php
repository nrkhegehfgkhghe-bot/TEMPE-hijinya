<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ambil semua pemesanan + pembayaran
$stmt = $pdo->prepare("
    SELECT r.id AS reservation_id, r.tanggal_pesan, r.status AS status_reservasi,
           rm.number AS no_kamar, rm.price,
           u.username AS pemilik,
           p.status AS status_bayar, p.qr_code, p.bukti_transfer, p.tanggal_mulai
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.id
    JOIN users u ON r.pemilik_id = u.id
    LEFT JOIN pembayaran p ON p.user_id = r.user_id AND p.room_id = r.room_id
    WHERE r.user_id = ?
    ORDER BY r.tanggal_pesan DESC
");
$stmt->execute([$user_id]);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penyewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            padding: 80px 20px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section h2 {
            font-size: 2.5rem;
            font-weight: bold;
            animation: popIn 1s ease-in-out forwards;
        }
        .hero-section p {
            font-size: 1.2rem;
            animation: fadeInUp 1.5s ease forwards;
        }
        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); }
        }
        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .card-pesan {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s ease-in-out;
        }
        .card-pesan:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .status-badge {
            padding: 6px 14px;
            font-size: 0.85rem;
            border-radius: 20px;
            margin-right: 5px;
        }
        .badge-menunggu { background-color: #ffc107; color: #212529; }
        .badge-diterima { background-color: #28a745; }
        .badge-ditolak { background-color: #dc3545; }
        .badge-bayar { background-color: #0d6efd; }
        .img-thumbnail {
            max-width: 100px;
            border-radius: 10px;
        }
        .btn-rounded {
            border-radius: 30px;
            padding-left: 20px;
            padding-right: 20px;
        }
        .progress {
            height: 10px;
            background-color: #e9ecef;
        }
        .progress-bar {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <?php include "layout/navbar2.html"; ?>

    <div class="hero-section">
        <h2>👋 Selamat Datang, <?= htmlspecialchars($username) ?>!</h2>
        <p>Kelola semua pemesanan kamar dan pembayaranmu dengan mudah di satu tempat.</p>
    </div>

    <?php if (count($pesanan) > 0): ?>
        <?php foreach ($pesanan as $p): ?>
            <div class="card card-pesan">
                <div class="row g-4 align-items-center">
                    <div class="col-md-8">
                        <h5 class="fw-semibold text-dark">Kamar <span class="text-primary">#<?= htmlspecialchars($p['no_kamar']) ?></span></h5>
                        <p class="mb-1">💰 Harga: <strong class="text-success">Rp<?= number_format($p['price']) ?></strong></p>
                        <p class="mb-1">👤 Pemilik: <strong><?= htmlspecialchars($p['pemilik']) ?></strong></p>
                        <p class="text-muted">📅 Dipesan: <?= date('d M Y', strtotime($p['tanggal_pesan'])) ?></p>
                        <div class="mb-2">
                            <span class="status-badge <?=
                                $p['status_reservasi'] === 'Diterima' ? 'badge-diterima' :
                                ($p['status_reservasi'] === 'Ditolak' ? 'badge-ditolak' : 'badge-menunggu') ?>">
                                <?= $p['status_reservasi'] ?>
                            </span>
                            <?php if ($p['status_bayar']): ?>
                                <span class="status-badge <?=
                                    $p['status_bayar'] === 'Sudah Dibayar' ? 'badge-bayar' : 'badge-menunggu' ?>">
                                    <?= $p['status_bayar'] ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($p['status_bayar'] === 'Sudah Dibayar' && $p['tanggal_mulai']): ?>
                            <?php
                                $mulai = new DateTime($p['tanggal_mulai']);
                                $now = new DateTime();
                                $selisih = $now->diff($mulai)->days;
                                $sisa = max(0, 30 - $selisih);
                                $persen = round(($sisa / 30) * 100);
                            ?>
                            <div class="mt-3">
                                <small class="text-muted">📆 Sisa Waktu Sewa: <strong><?= $sisa ?> hari</strong></small>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-info" style="width: <?= $persen ?>%"></div>
                                </div>
                                <?php if ($sisa == 0): ?>
                                    <div class="mt-3">
                                        <a href="pembayaran.php?reservation_id=<?= $p['reservation_id'] ?>" class="btn btn-warning btn-sm btn-rounded">Bayar Lagi</a>
                                        <a href="pergi_kamar.php?reservation_id=<?= $p['reservation_id'] ?>" class="btn btn-danger btn-sm btn-rounded">Kosongkan Kamar</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($p['status_bayar'] === 'Menunggu Konfirmasi' && $p['bukti_transfer']): ?>
                            <div class="mt-2">
                                <a href="uploads/<?= $p['bukti_transfer'] ?>" class="btn btn-outline-info btn-sm" target="_blank">📄 Lihat Bukti Transfer</a>
                            </div>
                        <?php elseif ($p['status_reservasi'] === 'Diterima' && $p['qr_code']): ?>
                            <div class="mt-2">
                                <img src="uploads/<?= $p['qr_code'] ?>" class="img-thumbnail" alt="QR">
                                <a href="pembayaran.php?reservation_id=<?= $p['reservation_id'] ?>" class="btn btn-primary btn-sm btn-rounded mt-2">Bayar Sekarang</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4 text-end">
                        <?php if ($p['status_reservasi'] === 'Menunggu'): ?>
                            <span class="text-warning fw-semibold">⏳ Menunggu Konfirmasi</span>
                        <?php elseif ($p['status_reservasi'] === 'Ditolak'): ?>
                            <span class="text-danger fw-semibold">❌ Ditolak</span>
                        <?php elseif ($p['status_bayar'] === 'Sudah Dibayar'): ?>
                            <span class="text-success fw-semibold">✅ Aktif</span>
                        <?php endif; ?>

                        <div class="mt-3">
                            <a href="pergi_kamar.php?reservation_id=<?= $p['reservation_id'] ?>"
                               class="btn btn-outline-danger btn-sm btn-rounded"
                               onclick="return confirm('Yakin ingin keluar dari kamar ini?')">
                               Kosongkan Kamar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Belum ada pemesanan kamar.</div>
    <?php endif; ?>

</div>
</body>
</html>