<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$days = []; $registrations = []; $reservations = []; $payments = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $days[] = $date;

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role='penyewa' AND DATE(created_at)=?");
    $stmt->execute([$date]);
    $registrations[] = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE DATE(created_at)=?");
    $stmt->execute([$date]);
    $reservations[] = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pembayaran WHERE status='sukses' AND DATE(created_at)=?");
    $stmt->execute([$date]);
    $payments[] = $stmt->fetchColumn();
}

// Pendapatan bulan ini
$stmt = $pdo->query("SELECT SUM(jumlah) FROM pembayaran WHERE status='sukses' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())");
$totalBulanan = $stmt->fetchColumn() ?: 0;

// Pendapatan tahun ini
$stmt = $pdo->query("SELECT SUM(jumlah) FROM pembayaran WHERE status='sukses' AND YEAR(created_at)=YEAR(CURDATE())");
$totalTahunan = $stmt->fetchColumn() ?: 0;


$bulanLabels = []; $bulanJumlah = [];
for ($i = 1; $i <= 12; $i++) {
    $stmt = $pdo->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE status='sukses' AND MONTH(created_at)=? AND YEAR(created_at)=YEAR(CURDATE())");
    $stmt->execute([$i]);
    $bulanLabels[] = date('F', mktime(0, 0, 0, $i, 10));
    $bulanJumlah[] = $stmt->fetchColumn() ?: 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistik Admin - KosMen.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #121212;
            color: white;
        }
        .container {
            margin-top: 40px;
        }
        h2 {
            color: #f1c40f;
        }
        .card {
            background-color: #1e1e1e;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Statistik Aktivitas & Keuangan</h2>
        <a href="admin_dashboard.php" class="btn btn-outline-light">← Kembali ke Dashboard</a>
    </div>

    <!-- Ringkasan Keuangan -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white p-3">
                <h5>Total Pendapatan Bulan Ini:</h5>
                <h3 class="text-success">Rp <?= number_format($totalBulanan, 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white p-3">
                <h5>Total Pendapatan Tahun Ini:</h5>
                <h3 class="text-info">Rp <?= number_format($totalTahunan, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <!-- Grafik Aktivitas Mingguan -->
    <div class="card text-white p-4 mb-4">
        <h5 class="mb-3">Aktivitas Harian (7 Hari Terakhir)</h5>
        <canvas id="activityChart"></canvas>
    </div>

    <!-- Grafik Pendapatan Bulanan -->
    <div class="card text-white p-4 mb-5">
        <h5 class="mb-3">Grafik Pemasukan per Bulan (Tahun Ini)</h5>
        <canvas id="incomeChart"></canvas>
    </div>
</div>

<script>
const labels = <?= json_encode($days) ?>;
const activityChart = new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Pendaftar Penyewa',
                data: <?= json_encode($registrations) ?>,
                borderColor: '#f1c40f',
                backgroundColor: '#f1c40f88',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Pemesanan',
                data: <?= json_encode($reservations) ?>,
                borderColor: '#3498db',
                backgroundColor: '#3498db88',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Pembayaran Sukses',
                data: <?= json_encode($payments) ?>,
                borderColor: '#2ecc71',
                backgroundColor: '#2ecc7188',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        scales: {
            y: {
                ticks: { color: 'white' },
                grid: { color: '#333' }
            },
            x: {
                ticks: { color: 'white' },
                grid: { color: '#333' }
            }
        },
        plugins: {
            legend: {
                labels: { color: 'white' }
            }
        }
    }
});

const incomeChart = new Chart(document.getElementById('incomeChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($bulanLabels) ?>,
        datasets: [{
            label: 'Pemasukan (Rp)',
            data: <?= json_encode($bulanJumlah) ?>,
            backgroundColor: '#9b59b6'
        }]
    },
    options: {
        scales: {
            y: {
                ticks: { color: 'white' },
                grid: { color: '#333' }
            },
            x: {
                ticks: { color: 'white' },
                grid: { color: '#333' }
            }
        },
        plugins: {
            legend: {
                labels: { color: 'white' }
            }
        }
    }
});
</script>
</body>
</html>
