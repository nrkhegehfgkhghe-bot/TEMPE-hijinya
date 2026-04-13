<?php
session_start();

// Koneksi
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

// Ambil data kosan
$query = "SELECT id, username, nama_kosan, alamat_kosan, latitude, longitude FROM users WHERE role = 'pemilik'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$kosanList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Kosan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #d1c4e9, #f8bbd0);
            background-size: 300% 300%;
            animation: gradientBG 10s ease infinite;
            padding: 3rem 1rem;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h2 {
            text-align: center;
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #512da8;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.7s ease forwards;
            margin-bottom: 30px;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(81, 45, 168, 0.3);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-title {
            font-weight: 600;
            color: #512da8;
        }

        .card-text {
            color: #37474f;
        }

        .map {
            height: 250px;
            margin-top: 15px;
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(to right, #7b1fa2, #512da8);
            border: none;
            border-radius: 30px;
            padding: 8px 25px;
            font-weight: 500;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #512da8, #311b92);
        }

        .btn-outline-secondary {
            border-radius: 30px;
            font-weight: 500;
            background: linear-gradient(to right,rgb(134, 131, 135),rgb(10, 10, 11));
            color: white;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🏠 Daftar Kosan Tersedia</h2>
    <a href="dashboard_penyewa.php" class="btn btn-outline-secondary mb-4">← Kembali ke Dashboard</a>

    <?php if (count($kosanList) > 0): ?>
        <?php foreach ($kosanList as $index => $kosan): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($kosan['nama_kosan'] ?? 'Nama Kosan Tidak Ada'); ?></h5>
                    <p class="card-text"><strong>Pemilik:</strong> <?= htmlspecialchars($kosan['username']); ?></p>
                    <p class="card-text"><strong>Alamat:</strong> <?= htmlspecialchars($kosan['alamat_kosan'] ?? 'Belum diisi'); ?></p>

                    <!-- Peta -->
                    <div id="map<?= $index ?>" class="map"></div>

                    <!-- Tombol Pesan -->
                    <form method="POST" action="pesan.php" class="mt-3">
                        <input type="hidden" name="pemilik_id" value="<?= $kosan['id']; ?>">
                        <button type="submit" name="pesan" class="btn btn-primary">Pesan Sekarang</button>
                    </form>
                </div>
            </div>

            <script>
                const lat<?= $index ?> = <?= floatval($kosan['latitude'] ?? 0) ?>;
                const lon<?= $index ?> = <?= floatval($kosan['longitude'] ?? 0) ?>;

                if (lat<?= $index ?> && lon<?= $index ?>) {
                    const map<?= $index ?> = L.map('map<?= $index ?>').setView([lat<?= $index ?>, lon<?= $index ?>], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map<?= $index ?>);
                    L.marker([lat<?= $index ?>, lon<?= $index ?>]).addTo(map<?= $index ?>);
                } else {
                    document.getElementById('map<?= $index ?>').innerHTML = '<p class=\"text-muted\"> Lokasi belum ditentukan.</p>';
                }
            </script>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted text-center">Belum ada kosan yang terdaftar.</p>
    <?php endif; ?>
</div>

</body>
</html>
