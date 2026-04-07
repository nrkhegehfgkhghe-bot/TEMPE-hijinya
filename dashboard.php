<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Gagal terhubung ke database: " . $e->getMessage());
}

// Dapatkan user_id dari session
$user_id = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalRooms = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE status = 'Ditempati' AND user_id = ?");
$stmt->execute([$user_id]);
$occupiedRooms = $stmt->fetchColumn();

$availableRooms = $totalRooms - $occupiedRooms;

// Data pengingat pembayaran berdasarkan user_id
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS count 
    FROM rooms
    WHERE status = 'Ditempati'
    AND user_id = ?
    AND DATEDIFF(DATE_ADD(tenant_date, INTERVAL 1 MONTH), NOW()) BETWEEN 0 AND 3
");
$stmt->execute([$user_id]);
$dueSoon = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) AS count 
    FROM rooms
    WHERE status = 'Ditempati'
    AND user_id = ?
    AND DATEDIFF(DATE_ADD(tenant_date, INTERVAL 1 MONTH), NOW()) < 0
");
$stmt->execute([$user_id]);
$overdue = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("
    SELECT r.*, u.username AS penyewa, rm.number AS no_kamar 
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN rooms rm ON r.room_id = rm.id
    WHERE r.pemilik_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Aplikasi Pengelolaan Kosan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <style>
        :root {
            --bg-light: #fafafa;
            --bg-dark: #181818;
            --text-light: #333;
            --text-dark: #f9f9f9;
            --card-light: #ffffff;
            --card-dark: #333333;
            --navbar-light: #f8f9fa;
            --navbar-dark: #222222;
            --gradient-light: linear-gradient(to right, #ff7e5f, #feb47b);
            --gradient-dark: linear-gradient(to right, #2c3e50, #34495e);
        }

        body.light-mode {
            background-color: var(--bg-light);
            color: var(--text-light);
        }

        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .navbar {
            background-color: var(--navbar-light);
        }

        body.dark-mode .navbar {
            background-color: var(--navbar-dark);
        }

        .banner {
            padding: 40px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            background: var(--gradient-light);
            color: white;
        }

        body.dark-mode .banner {
            background: var(--gradient-dark);
        }

        .card {
            border: 1px solid var(--bg-light);
            background-color: var(--card-light);
            transition: background-color 0.3s ease, border-color 0.3s ease;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        body.dark-mode .card {
            border-color: var(--bg-dark);
            background-color: var(--card-dark);
        }

        .card .icon,
        .card h2,
        .card p {
            color: var(--text-light);
        }

        body.dark-mode .card .icon,
        body.dark-mode .card h2,
        body.dark-mode .card p {
            color: var(--text-dark);
        }

        .footer {
            text-align: center;
            padding: 15px;
            margin-top: 30px;
            background-color: var(--bg-light);
        }

        body.dark-mode .footer {
            background-color: var(--bg-dark);
        }

        .mode-toggle {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1.5rem;
        }

        body.dark-mode .mode-toggle {
            color: var(--text-dark);
        }

        /* Responsive grid untuk 5 kartu dalam satu baris */
        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            justify-items: center;
        }

        .stats .card {
            width: 100%;
            max-width: 250px;
        }

        /* Efek animasi bintang */
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .stars span {
            position: absolute;
            display: block;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
            animation: twinkling 5s infinite ease-in-out;
        }

        body.dark-mode .stars span {
            background: #f8f8f8;
        }

        @keyframes twinkling {
            0% {
                opacity: 0.2;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.5);
            }
            100% {
                opacity: 0.2;
                transform: scale(1);
            }
        }
    </style>
</head>
<body class="light-mode">
    <button class="mode-toggle" id="toggleMode">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Bintang Gemerlap -->
    <div class="stars">
        <?php for ($i = 0; $i < 100; $i++): ?>
            <span style="top: <?= rand(0, 100) ?>%; left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 5) ?>s;"></span>
        <?php endfor; ?>
    </div>

    <div class="container mt-4">
        <?php include "layout/navbar1.html"; ?>

        <!-- Banner -->
        <div class="banner">
            <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
            <p>Kelola kamar kosan Anda dengan mudah dan cepat.</p>
        </div>

        <!-- Statistik Kosan -->
        <div class="stats">
            
            <div class="card">
                <i class="fas fa-door-open icon"></i>
                <h2><?= $totalRooms ?></h2>
                <p>Total Kamar</p>
            </div>
            <div class="card">
                <i class="fas fa-bed icon"></i>
                <h2><?= $availableRooms ?></h2>
                <p>Kamar Tersedia</p>
            </div>
            <div class="card">
                <i class="fas fa-user-check icon"></i>
                <h2><?= $occupiedRooms ?></h2>
                <p>Kamar Ditempati</p>
            </div>
            <div class="card">
                <i class="fas fa-hourglass-half icon"></i>
                <h2><?= $dueSoon ?></h2>
                <p>Mendekati Jatuh Tempo</p>
            </div>
            <div class="card">
                <i class="fas fa-exclamation-triangle icon"></i>
                <h2><?= $overdue ?></h2>
                <p>Telah Melewati Jatuh Tempo</p>
            </div>
        </div>

        <!-- Notifikasi Pemesanan Baru -->
<div class="mt-5">
    <h3>Notifikasi Pemesanan Kamar</h3>
    <?php if (count($pesanan) > 0): ?>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Tanggal Pesan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesanan as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['penyewa']) ?></td>
                        <td><?= htmlspecialchars($p['no_kamar']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal_pesan']) ?></td>
                        <td><?= $p['status'] ?? 'Menunggu' ?></td>
                        <td>
                               <?php if (($p['status'] ?? 'Menunggu') === 'Menunggu'): ?>
                                <form method="GET" action="upload_qr.php" class="d-inline">
                                <input type="hidden" name="reservation_id" value="<?= $p['id'] ?>">
                                <button class="btn btn-success btn-sm">Terima</button>
                                    
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($p['status']) ?>
                            <?php endif; ?>

                            <?php if (($p['status'] ?? 'Menunggu') === 'Menunggu'): ?>
                                <form method="POST" action="konfirmasi_pesan.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button name="aksi" value="ditolak" class="btn btn-danger btn-sm">Tolak</button>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($p['status']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada pemesanan baru saat ini.</p>
    <?php endif; ?>
</div>


        <!-- Footer -->
        <?php include "fotter.php"; ?>

    <script>
        const toggleButton = document.getElementById('toggleMode');
        toggleButton.addEventListener('click', () => {
            const body = document.body;
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                toggleButton.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                toggleButton.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
    </script>
</body>
</html>
