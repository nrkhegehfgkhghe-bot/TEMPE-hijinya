<?php
session_start();

// Pastikan user adalah penyewa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: login.php");
    exit();
}

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

$id_kamar = isset($_GET['id_kamar']) ? (int)$_GET['id_kamar'] : 0;
if ($id_kamar <= 0) {
    die("<div class='alert alert-danger'>ID kamar tidak valid.</div>");
}


$stmtKamar = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmtKamar->execute([$id_kamar]);
$kamar = $stmtKamar->fetch(PDO::FETCH_ASSOC);

if (!$kamar) {
    die("<div class='alert alert-danger'>Kamar tidak ditemukan.</div>");
}


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
    background: linear-gradient(-45deg, #e3f2fd, #bbdefb, #e1f5fe, #e3f2fd);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

        .hero-section {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(to right,rgb(106, 169, 232),rgb(237, 239, 239));
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
            text-shadow: 0 0 8px rgba(0,0,0,0.3);
        }

        .hero-content p {
            font-size: 1rem;
            color: #f0f0f0;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
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
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            color: #555;
        }
    </style>
</head>
<body>

    <div class="hero-section">
        <div id="particles-js"></div>
        <div class="hero-content">
            <h2> Galeri Foto - Kamar <?= htmlspecialchars($kamar['number']) ?></h2>
            <p>Detail kamar kosan yang ingin Anda pesan.</p>
        </div>
    </div>

    <div class="container py-4">
        <a href="pesan.php?pemilik_id=<?= $kamar['user_id'] ?>" class="btn btn-secondary mb-3">← Kembali ke Daftar Kamar</a>

        <?php if (count($fotos) > 0): ?>
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="swiper-slide">
                            <div class="foto-card">
                                <img src="uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Foto Kamar">
                                <div class="card-body">
                                    <p><?= htmlspecialchars($foto['deskripsi'] ?? '-') ?></p>
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
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.3 },
                "size": { "value": 3 },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.3, "width": 1 },
                "move": { "enable": true, "speed": 2 }
            },
            "interactivity": {
                "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" } },
                "modes": { "grab": { "distance": 200, "line_linked": { "opacity": 0.4 } } }
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