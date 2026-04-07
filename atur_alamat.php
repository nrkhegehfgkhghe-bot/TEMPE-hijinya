<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT nama_kosan, alamat_kosan, latitude, longitude FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kosan = $_POST['nama_kosan'];
    $alamat_kosan = $_POST['alamat_kosan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $update = $pdo->prepare("UPDATE users SET nama_kosan = ?, alamat_kosan = ?, latitude = ?, longitude = ? WHERE id = ?");
    $update->execute([$nama_kosan, $alamat_kosan, $latitude, $longitude, $user_id]);

    echo "<script>alert('Alamat berhasil diperbarui!'); location.href='atur_alamat.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Atur Alamat Kosan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            background: #f7f9fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-card {
            max-width: 750px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        h2 {
            font-weight: 700;
            font-size: 24px;
        }

        #map {
            height: 320px;
            border-radius: 12px;
            margin-top: 10px;
            border: 2px solid #dee2e6;
        }

        .btn-secondary {
            background-color: #495057;
            border: none;
        }

        .badge {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .loading {
            display: none;
            font-size: 14px;
            color: #888;
        }

        .lokasi-info {
            font-size: 0.9rem;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include "layout/navbar.html"; ?>
        <div class="form-card">
            <h2>📍 Atur Lokasi & Alamat Kosan Anda</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="nama_kosan" class="form-label">Nama Kosan</label>
                    <input type="text" name="nama_kosan" id="nama_kosan" class="form-control" value="<?= htmlspecialchars($data['nama_kosan']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="alamat_kosan" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat_kosan" id="alamat_kosan" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat_kosan']) ?></textarea>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <button type="button" class="btn btn-secondary" onclick="cariAlamat()">🔍 Cari Lokasi Otomatis</button>
                    <span class="loading" id="loadingMsg">⏳ Mencari lokasi...</span>
                </div>
                <div class="mb-2 lokasi-info">
                    <strong>Lokasi Terkini:</strong> <span id="lokasi_ditemukan">Belum ditemukan</span>
                </div>
                <div id="map"></div>
                <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($data['latitude'] ?? '-6.2') ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($data['longitude'] ?? '106.8') ?>">
                <button type="submit" class="btn btn-primary mt-4 w-100">💾 Simpan Lokasi</button>
            </form>
        </div>
    </div>

<script>
    const latInput = document.getElementById("latitude");
    const lonInput = document.getElementById("longitude");
    const lokasiSpan = document.getElementById("lokasi_ditemukan");
    const loadingMsg = document.getElementById("loadingMsg");

    const map = L.map('map').setView([parseFloat(latInput.value), parseFloat(lonInput.value)], 15);
    const marker = L.marker([latInput.value, lonInput.value], { draggable: true }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        latInput.value = pos.lat;
        lonInput.value = pos.lng;
        lokasiSpan.innerText = `Manual: ${pos.lat.toFixed(5)}, ${pos.lng.toFixed(5)}`;
    });

    function cariAlamat() {
        const alamat = document.getElementById("alamat_kosan").value.trim();
        if (!alamat) return alert("Silakan isi alamat lengkap terlebih dahulu.");
        loadingMsg.style.display = "inline";

        fetch("https://nominatim.openstreetmap.org/search?format=json&q=" + encodeURIComponent(alamat))
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    const { lat, lon, display_name } = data[0];
                    latInput.value = lat;
                    lonInput.value = lon;
                    lokasiSpan.innerText = display_name;
                    marker.setLatLng([lat, lon]);
                    map.setView([lat, lon], 17);
                } else {
                    alert(" Lokasi tidak ditemukan. Coba perjelas alamatnya.");
                }
            })
            .catch(() => alert("Gagal mengakses layanan pencarian alamat."))
            .finally(() => loadingMsg.style.display = "none");
    }
</script>
</body>
</html>
