<?php
session_start();

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

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Proses tambah / edit kamar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number = $_POST['roomNumber'];
    $price = $_POST['roomPrice'];
    $status = $_POST['roomStatus'];
    $facilities = $_POST['roomFacilities'];
    $photoName = $_POST['existingPhoto'] ?? null;

    if (!empty($_FILES['roomPhoto']['name'])) {
        $photoName = time() . '_' . basename($_FILES['roomPhoto']['name']);
        $targetDir = "uploads/";
        move_uploaded_file($_FILES['roomPhoto']['tmp_name'], $targetDir . $photoName);
    }

    if (isset($_POST['editRoom'])) {
        $id = $_POST['roomId'];
        $stmt = $pdo->prepare("UPDATE rooms SET number = ?, price = ?, status = ?, facilities = ?, photo = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$number, $price, $status, $facilities, $photoName, $id, $userId]);
        $message = "Kamar berhasil diperbarui.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO rooms (number, price, status, facilities, photo, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$number, $price, $status, $facilities, $photoName, $userId]);
        $message = "Kamar berhasil ditambahkan.";
    }

    header("Location: pengelolaan_kamar.php?msg=" . urlencode($message));
    exit();
}

// Hapus kamar
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    $message = "Kamar berhasil dihapus.";
    header("Location: pengelolaan_kamar.php?msg=" . urlencode($message));
    exit();
}

// Ambil data kamar
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE user_id = ?");
$stmt->execute([$userId]);
$rooms = $stmt->fetchAll();

// Ambil pesan jika ada
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kamar Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .room-card img {
            object-fit: cover;
            height: 160px;
        }
        .room-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
        }
        #photoPreview {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>
<body class="container py-4">

    <h1 class="mb-4 text-primary fw-bold">🛏️ Manajemen Kamar Kos</h1>
    <?php include "layout/navbar.html"; ?>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Tambah/Edit Kamar -->
    <div class="room-form mb-4 shadow">
        <h4 id="formTitle" class="mb-3">➕ Tambah Kamar</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="roomId" id="roomId">
            <input type="hidden" name="existingPhoto" id="existingPhoto">

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="roomNumber" class="form-label">Nomor Kamar</label>
                    <input type="text" class="form-control" id="roomNumber" name="roomNumber" required>
                </div>
                <div class="col-md-3">
                    <label for="roomPrice" class="form-label">Harga Sewa (Rp)</label>
                    <input type="number" class="form-control" id="roomPrice" name="roomPrice" required>
                </div>
                <div class="col-md-3">
                    <label for="roomStatus" class="form-label">Status</label>
                    <select class="form-select" id="roomStatus" name="roomStatus" required>
                        <option value="Kosong">Kosong</option>
                        <option value="Ditempati">Ditempati</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="roomFacilities" class="form-label">Fasilitas</label>
                <textarea class="form-control" id="roomFacilities" name="roomFacilities" rows="2" required></textarea>
            </div>

            <div class="mb-3">
                <label for="roomPhoto" class="form-label">Foto Kamar</label>
                <input type="file" class="form-control" name="roomPhoto" id="roomPhoto" accept="image/*">
                <img id="photoPreview" src="#" alt="Preview" class="img-thumbnail mt-2 d-none">
            </div>

            <button type="submit" name="addRoom" class="btn btn-success" id="submitButton">
                <i class="bi bi-plus-circle"></i> Tambah Kamar
            </button>
        </form>
    </div>

    <!-- Daftar Kamar Kos -->
    <h2 class="mb-3">🗂️ Daftar Kamar</h2>
    <div class="row">
        <?php foreach ($rooms as $room): ?>
            <div class="col-md-4">
                <div class="card room-card mb-4 shadow-sm">
                    <a href="detail_foto.php?id_kamar=<?= $room['id'] ?>">
                        <img src="uploads/<?= htmlspecialchars($room['photo']) ?>" class="card-img-top" alt="Foto Kamar">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">Kamar <?= htmlspecialchars($room['number']) ?></h5>
                        <p class="card-text mb-1">
                            <strong>Harga:</strong> Rp<?= number_format($room['price']) ?><br>
                            <strong>Status:</strong> <?= htmlspecialchars($room['status']) ?><br>
                            <strong>Fasilitas:</strong><br>
                            <?= nl2br(htmlspecialchars($room['facilities'])) ?>
                        </p>
                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-sm btn-warning" onclick='editRoom(<?= json_encode($room) ?>)'>✏️ Edit</button>
                            <a href="?delete=<?= $room['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kamar ini?')">🗑️ Hapus</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editRoom(room) {
            document.getElementById('roomId').value = room.id;
            document.getElementById('roomNumber').value = room.number;
            document.getElementById('roomPrice').value = room.price;
            document.getElementById('roomStatus').value = room.status;
            document.getElementById('roomFacilities').value = room.facilities;
            document.getElementById('existingPhoto').value = room.photo;

            document.getElementById('submitButton').textContent = "💾 Simpan Perubahan";
            document.getElementById('submitButton').name = "editRoom";
            document.getElementById('formTitle').textContent = "✏️ Edit Kamar";

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Preview foto
        document.getElementById('roomPhoto').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('photoPreview');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
