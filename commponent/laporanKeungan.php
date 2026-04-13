<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

$pemilik_id = $_SESSION['user_id'];
$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Ambil data pembayaran yang sudah dikonfirmasi
$stmt = $pdo->prepare("SELECT p.tanggal_mulai, p.status, r.number AS no_kamar, r.price, u.username AS penyewa
    FROM pembayaran p
    JOIN rooms r ON p.room_id = r.id
    JOIN users u ON p.user_id = u.id
    WHERE p.status = 'Sudah Dibayar' AND r.user_id = ?
    AND MONTH(p.tanggal_mulai) = ? AND YEAR(p.tanggal_mulai) = ?
    ORDER BY p.tanggal_mulai DESC");
$stmt->execute([$pemilik_id, $bulan, $tahun]);
$transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total
$total = array_reduce($transaksi, fn($sum, $t) => $sum + $t['price'], 0);

// Data untuk pie chart
$penyewaChart = [];
foreach ($transaksi as $t) {
    $penyewa = $t['penyewa'];
    $penyewaChart[$penyewa] = ($penyewaChart[$penyewa] ?? 0) + $t['price'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f9fbfc;
            font-family: 'Segoe UI', sans-serif;
        }

        .laporan-container {
            max-width: 960px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        h2 {
            font-weight: 700;
        }

        table th, table td {
            vertical-align: middle;
        }

        .form-select {
            border-radius: 10px;
        }

        canvas {
            background: #fff;
            padding: 10px;
            border-radius: 12px;
        }

        .chart-section {
            margin-top: 40px;
        }

        .total-row {
            font-weight: bold;
            background: #d1e7dd;
        }

        .btn-primary {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="laporan-container">
    <h2 class="mb-4 text-primary">📊 Laporan Keuangan Kosan</h2>
    <?php include "layout/navbar.html"; ?>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-select">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>>
                        <?= date('F', mktime(0,0,0,$i,1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">Tampilkan</button>
        </div>
    </form>

    <?php if (count($transaksi) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Kamar</th>
                    <th>Penyewa</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transaksi as $t): ?>
                    <tr>
                        <td><?= $t['tanggal_mulai'] ?></td>
                        <td><?= htmlspecialchars($t['no_kamar']) ?></td>
                        <td><?= htmlspecialchars($t['penyewa']) ?></td>
                        <td>Rp<?= number_format($t['price']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3">Total Pemasukan</td>
                    <td>Rp<?= number_format($total) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="row chart-section">
            <div class="col-md-6">
                <h5 class="text-center">📦 Pemasukan per Kamar</h5>
                <canvas id="keuanganChart" height="250"></canvas>
            </div>
            <div class="col-md-6">
                <h5 class="text-center">🧑 Kontribusi Penyewa</h5>
                <canvas id="penyewaPie" height="250"></canvas>
            </div>
        </div>

        <script>
            const ctxBar = document.getElementById('keuanganChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($transaksi, 'no_kamar')) ?>,
                    datasets: [{
                        label: 'Jumlah (Rp)',
                        data: <?= json_encode(array_column($transaksi, 'price')) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            const ctxPie = document.getElementById('penyewaPie').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_keys($penyewaChart)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($penyewaChart)) ?>,
                        backgroundColor: [
                            '#6f42c1', '#0d6efd', '#20c997', '#ffc107', '#dc3545', '#198754'
                        ],
                        hoverOffset: 10
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Rp' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        </script>
    <?php else: ?>
        <div class="alert alert-warning text-center">Tidak ada transaksi di bulan ini.</div>
    <?php endif; ?>
</div>
</body>
</html>
