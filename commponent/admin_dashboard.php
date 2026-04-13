<?php
session_start();

// Hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Koneksi DB
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


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("UPDATE users SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php");
    exit();
}


if (isset($_GET['restore'])) {
    $id = $_GET['restore'];
    $stmt = $pdo->prepare("UPDATE users SET is_deleted = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php");
    exit();
}

// Ambil user aktif
$stmtAktif = $pdo->prepare("SELECT * FROM users WHERE (role = 'penyewa' OR role = 'pemilik') AND is_deleted = 0");
$stmtAktif->execute();
$usersAktif = $stmtAktif->fetchAll();

// Ambil user nonaktif
$stmtNonaktif = $pdo->prepare("SELECT * FROM users WHERE (role = 'penyewa' OR role = 'pemilik') AND is_deleted = 1");
$stmtNonaktif->execute();
$usersNonaktif = $stmtNonaktif->fetchAll();

// Hitung jumlah penyewa & pemilik aktif
$penyewaCount = 0;
$pemilikCount = 0;
foreach ($usersAktif as $user) {
    if ($user['role'] === 'penyewa') $penyewaCount++;
    if ($user['role'] === 'pemilik') $pemilikCount++;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - KosMen.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        .table {
            color: #ffffff;
        }
        .badge.bg-primary {
            background-color: #3498db !important;
        }
        .badge.bg-secondary {
            background-color: #7f8c8d !important;
        }
        .container {
            margin-top: 30px;
        }
        h2 {
            color: #f1c40f;
        }
        canvas {
            background: #1f1f1f;
            border-radius: 10px;
            padding: 10px;
        }
        .btn-logout {
            border-radius: 30px;
            padding: 8px 20px;
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            border: none;
            color: white;
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #ff4b2b, #ff416c);
        }

    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Statistik Pengguna Aktif</h2>
    <canvas id="userChart" height="100"></canvas>
        <div class="text-end mb-4">
    <a href="admin_analytics.php" class="btn btn-info">📊 Lihat Statistik & Keuangan</a>
     <a href="backup_data.php" target="_blank" class="btn btn-info">🔄 Backup Data</a>
    <a href="index.php" class="btn btn-logout">LOGOUT</a>
    
</div>
 
   



    <h2 class="mt-5 mb-3">Pengguna Aktif</h2>
    <table class="table table-dark table-bordered table-striped">
        <thead class="table-success text-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Nama Kosan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usersAktif as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge bg-primary"><?= $user['role'] ?></span></td>
                <td><?= htmlspecialchars($user['nama_kosan']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="admin_dashboard.php?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <h2 class="mt-5 mb-3"> Pengguna Nonaktif</h2>
    <table class="table table-dark table-bordered table-striped">
        <thead class="table-secondary text-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Nama Kosan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usersNonaktif as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><span class="badge bg-secondary"><?= $user['role'] ?></span></td>
                <td><?= htmlspecialchars($user['nama_kosan']) ?></td>
                <td>
                    <a href="admin_dashboard.php?restore=<?= $user['id'] ?>" class="btn btn-success btn-sm">Aktifkan Kembali</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('userChart').getContext('2d');
const userChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Penyewa', 'Pemilik'],
        datasets: [{
            label: 'Jumlah Pengguna Aktif',
            data: [<?= $penyewaCount ?>, <?= $pemilikCount ?>],
            borderColor: '#4bc0c0',
            backgroundColor: '#4bc0c088',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'white',
            pointBorderColor: '#4bc0c0',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: 'white'
                },
                grid: {
                    color: '#444'
                }
            },
            x: {
                ticks: {
                    color: 'white'
                },
                grid: {
                    color: '#444'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            },
            tooltip: {
                backgroundColor: '#333',
                titleColor: 'white',
                bodyColor: 'white'
            }
        }
    }
});
</script>

</body>
</html>
