<?php
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
    </style>
</head>
<body>

    <div class="hero-section">
        <div id="particles-js"></div>
        <div class="hero-content">
            <h2>📷 Galeri Foto - Kamar <?= htmlspecialchars($kamar['number']) ?></h2>
            <p>Lihat dan kelola foto kamar Anda dengan tampilan keren!</p>
        </div>
    </div>

    <div class="container py-4">
        <a href="pesan.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

        <?php if (count($fotos) > 0): ?>
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="swiper-slide">
                            <div class="foto-card">
                                <img src="uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Foto Kamar">
                                <div class="card-body">
                                    <p><?= htmlspecialchars($foto['deskripsi'] ?? 'Tidak ada deskripsi.') ?></p>
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
    </script>
</body>
</html>
