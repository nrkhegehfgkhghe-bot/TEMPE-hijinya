<?php
session_start();
$role = $_SESSION['role'] ?? 'guest'; // role: 'pemilik', 'penyewa', atau 'guest'

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

$id_kamar = isset($_GET['id_kamar']) ? (int)$_GET['id_kamar'] : 0;

// Batasi aksi POST hanya untuk pemilik
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role !== 'pemilik') {
    die("Akses ditolak.");
}

// Hapus foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapusFotoId'])) {
    $fotoId = (int)$_POST['hapusFotoId'];
    $namaFile = $_POST['nama_file'];

    $stmtDelete = $pdo->prepare("DELETE FROM foto_kamar WHERE id = ?");
    $stmtDelete->execute([$fotoId]);

    $filePath = 'uploads/' . $namaFile;
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    header("Location: detail_foto.php?id_kamar=$id_kamar");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_kamar'])) {
    foreach ($_FILES['foto_kamar']['tmp_name'] as $key => $tmpName) {
        $originalName = $_FILES['foto_kamar']['name'][$key];
        $fileName = time() . '_' . basename($originalName);
        $targetPath = 'uploads/' . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $stmt = $pdo->prepare("INSERT INTO foto_kamar (id_kamar, nama_file) VALUES (?, ?)");
            $stmt->execute([$id_kamar, $fileName]);
        }
    }

    header("Location: detail_foto.php?id_kamar=$id_kamar");
    exit;
}

// Ambil data kamar
$stmtKamar = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmtKamar->execute([$id_kamar]);
$kamar = $stmtKamar->fetch(PDO::FETCH_ASSOC);

if (!$kamar) {
    echo "Kamar tidak ditemukan.";
    exit;
}

// Ambil foto
$stmtFoto = $pdo->prepare("SELECT * FROM foto_kamar WHERE id_kamar = ?");
$stmtFoto->execute([$id_kamar]);
$fotos = $stmtFoto->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Foto - Kamar <?= htmlspecialchars($kamar['number']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
        body {
            background-color: #121212;
            font-family: 'Segoe UI', sans-serif;
            color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .hero-section {
            position: relative;
            height: 300px;
            overflow: hidden;
            color: white;
            text-align: center;
            padding-top: 80px;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-content h2 {
            font-size: 2.8rem;
            font-weight: 600;
            text-shadow: 0 0 8px #00d0ff;
        }

        .hero-content p {
            font-size: 1rem;
            color: #ccc;
        }

        .btn-secondary {
            background-color: #333;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #555;
        }

        .form-label {
            color: #ccc;
        }

        .form-control {
            background-color: #1e1e1e;
            color: #fff;
            border: 1px solid #444;
        }

        .btn-primary {
            background-color: #00c3ff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #00a6dd;
        }

        .swiper {
            padding: 40px 0;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
        }

        .foto-card {
            width: 300px;
            background: #1e1e1e;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .foto-card:hover {
            transform: translateY(-5px);
        }

        .foto-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .foto-card .card-body {
            padding: 15px;
            text-align: center;
        }

        .foto-card .card-body p {
            font-size: 0.9rem;
            color: #bbb;
        }

        .editable-deskripsi:focus {
            outline: 2px dashed #00c3ff;
            background: #292929;
        }

        .btn-danger {
            font-size: 0.8rem;
            background-color: #ff4d4d;
            border: none;
        }

        .btn-danger:hover {
            background-color: #e43f3f;
        }
    </style>
</head>
<body>

    <div class="hero-section">
        <div id="particles-js"></div>
        <div class="hero-content">
            <h2>📷 Galeri Foto - Kamar <?= htmlspecialchars($kamar['number']) ?></h2>
            <p><?= $role === 'pemilik' ? 'Lihat dan kelola foto kamar Anda dengan tampilan keren!' : 'Galeri kamar kosan yang ingin Anda pesan.' ?></p>
        </div>
    </div>

    <div class="container py-4">
        <a href="<?= $role === 'pemilik' ? 'pengelolaan_kamar.php' : 'pesan.php' ?>" class="btn btn-secondary mb-3">← Kembali</a>

        <?php if ($role === 'pemilik'): ?>
            <!-- Form Upload -->
            <form method="POST" enctype="multipart/form-data" class="mb-4">
                <div class="mb-3">
                    <label for="foto_kamar" class="form-label">Tambah Foto Kamar (bisa lebih dari satu)</label>
                    <input type="file" name="foto_kamar[]" id="foto_kamar" class="form-control" accept="image/*" multiple required>
                </div>
                <button type="submit" class="btn btn-primary">Upload Foto</button>
            </form>
        <?php endif; ?>

        <!-- Galeri Swipeable -->
        <?php if (count($fotos) > 0): ?>
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="swiper-slide">
                            <div class="foto-card">
                                <img src="uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Foto Kamar">
                                <div class="card-body">
                                    <?php if ($role === 'pemilik'): ?>
                                        <p class="editable-deskripsi" contenteditable="true" data-id="<?= $foto['id'] ?>">
                                            <?= htmlspecialchars($foto['deskripsi'] ?? 'Klik untuk tambah deskripsi...') ?>
                                        </p>
                                        <form method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                            <input type="hidden" name="hapusFotoId" value="<?= $foto['id'] ?>">
                                            <input type="hidden" name="nama_file" value="<?= htmlspecialchars($foto['nama_file']) ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <p><?= htmlspecialchars($foto['deskripsi'] ?? '-') ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination mt-3"></div>
            </div>
        <?php else: ?>
            <p class="text-center">Tidak ada foto untuk kamar ini.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80, "density": { "enable": true, "value_area": 800 }},
                "color": { "value": "#00d0ff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5 },
                "size": { "value": 3 },
                "line_linked": { "enable": true, "distance": 150, "color": "#00c3ff", "opacity": 0.4, "width": 1 },
                "move": { "enable": true, "speed": 2 }
            },
            "interactivity": {
                "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" } },
                "modes": { "grab": { "distance": 200, "line_linked": { "opacity": 0.6 }} }
            }
        });

        const swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            },
            slidesPerView: 1,
            spaceBetween: 20,
            centeredSlides: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                576: { slidesPerView: 1.5 },
                768: { slidesPerView: 2 },
                992: { slidesPerView: 3 },
            }
        });

        
        <?php if ($role === 'pemilik'): ?>
        document.querySelectorAll('.editable-deskripsi').forEach(p => {
            p.addEventListener('blur', function () {
                const id = this.dataset.id;
                const deskripsi = this.innerText.trim();

                fetch('simpan_deskripsi.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id=${encodeURIComponent(id)}&deskripsi=${encodeURIComponent(deskripsi)}`
                }).then(response => response.text()).then(data => {
                    console.log("Deskripsi disimpan:", data);
                });
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
