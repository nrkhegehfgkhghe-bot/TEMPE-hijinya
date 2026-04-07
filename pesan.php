<?php
session_start();

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: login.php");
    exit();
}

$penyewa_id = $_SESSION['user_id'];
$pemilik_id = $_POST['pemilik_id'] ?? $_GET['pemilik_id'] ?? null;

if (!$pemilik_id) {
    die("<div class='alert alert-danger'>Data pemilik tidak ditemukan.</div>");
}

// Koneksi ke database
$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmtPemilik = $pdo->prepare("SELECT username, nama_kosan FROM users WHERE id = ? AND role = 'pemilik'");
$stmtPemilik->execute([$pemilik_id]);
$pemilik = $stmtPemilik->fetch(PDO::FETCH_ASSOC);

if (!$pemilik) {
    die("<div class='alert alert-danger'>Pemilik tidak ditemukan.</div>");
}


$stmtKamar = $pdo->prepare("SELECT * FROM rooms WHERE user_id = ? AND status = 'Kosong'");
$stmtKamar->execute([$pemilik_id]);
$kamarKosong = $stmtKamar->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pesan Kamar - <?= htmlspecialchars($pemilik['nama_kosan']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .card-img-top:hover {
            transform: scale(1.05);
        }
        .card-title {
            font-weight: bold;
        }
        .btn-pesan {
            width: 100%;
        }
        .badge-harga {
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>🛏️ Kamar di <strong><?= htmlspecialchars($pemilik['nama_kosan'] ?? 'Kosan Tanpa Nama') ?></strong></h2>
            <p class="text-muted mb-0">Pemilik: <?= htmlspecialchars($pemilik['username']) ?></p>
        </div>
        <a href="list_kosan.php" class="btn btn-secondary">← Kembali </a>
    </div>

    <?php if (count($kamarKosong) > 0): ?>
        <div class="row">
            <?php foreach ($kamarKosong as $kamar): ?>
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <?php if (!empty($kamar['photo'])): ?>
                            <a href="lihat_foto_kamar.php?id_kamar=<?= $kamar['id'] ?>">
                                <img src="uploads/<?= htmlspecialchars($kamar['photo']) ?>" class="card-img-top" alt="Foto Kamar">
                            </a>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">Kamar <?= htmlspecialchars($kamar['number']) ?></h5>
                            <p class="mb-2">
                                <span class="badge text-bg-success badge-harga">Rp<?= number_format($kamar['price']) ?>/bulan</span>
                            </p>
                            <p class="text-muted" style="min-height: 40px"><?= nl2br(htmlspecialchars($kamar['facilities'])) ?></p>
                            <form action="proses_pesan.php" method="POST">
                                <input type="hidden" name="room_id" value="<?= $kamar['id'] ?>">
                                <input type="hidden" name="pemilik_id" value="<?= $pemilik_id ?>">
                                <button type="submit" class="btn btn-primary btn-pesan">Pesan Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Tidak ada kamar kosong tersedia dari pemilik ini.</div>
    <?php endif; ?>

</body>
</html>
