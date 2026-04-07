<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

$pemilik_id = $_SESSION['user_id'];
$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("
    SELECT p.*, u.username AS penyewa, r.number AS kamar
    FROM pembayaran p
    JOIN rooms r ON p.room_id = r.id
    JOIN users u ON p.user_id = u.id
    WHERE r.user_id = ?
    ORDER BY p.id DESC
");
$stmt->execute([$pemilik_id]);
$pembayaran = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f8;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        .badge {
            font-size: 0.85rem;
        }
        .table thead {
            background: #f1f1f1;
        }
        h2 {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .icon-check {
            color: green;
        }
        .icon-clock {
            color: orange;
        }
        .icon-times {
            color: red;
        }
    </style>
</head>
<body class="container py-5">
    <?php include "layout/navbar.html"; ?>
    <h2><i class="fas fa-wallet"></i> Konfirmasi Pembayaran Penyewa</h2>

    <?php if (count($pembayaran) > 0): ?>
        <div class="card p-3">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pembayaran as $index => $p): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><i class="fas fa-user text-primary me-1"></i> <?= htmlspecialchars($p['penyewa']) ?></td>
                            <td><i class="fas fa-door-open text-secondary me-1"></i> Kamar <?= htmlspecialchars($p['kamar']) ?></td>
                            <td>
                                <?php if ($p['status'] === 'Sudah Dibayar'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Sudah Dibayar</span>
                                <?php elseif ($p['status'] === 'Menunggu Konfirmasi'): ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Menunggu</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($p['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['bukti_transfer']): ?>
                                    <a href="uploads/<?= htmlspecialchars($p['bukti_transfer']) ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="fas fa-image"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['status'] === 'Menunggu Konfirmasi'): ?>
                                    <form method="POST" action="verifikasi_pembayaran.php" class="d-flex gap-2">
                                        <input type="hidden" name="pembayaran_id" value="<?= $p['id'] ?>">
                                        <button name="aksi" value="terima" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                        <button name="aksi" value="tolak" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">Belum ada pembayaran dari penyewa.</div>
    <?php endif; ?>
</body>
</html>
