<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['reservation_id'])) {
    die("ID pemesanan tidak ditemukan.");
}

$reservation_id = $_GET['reservation_id'];

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
$stmt->execute([$reservation_id]);
$pemesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pemesanan) {
    die("Data pemesanan tidak valid.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_code'])) {
    $file = $_FILES['qr_code'];
    $fileName = uniqid() . "_" . $file['name'];
    $uploadPath = 'uploads/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
      
        $insert = $pdo->prepare("INSERT INTO pembayaran (user_id, room_id, qr_code, status) VALUES (?, ?, ?, 'Belum Dibayar')");
        $insert->execute([$pemesanan['user_id'], $pemesanan['room_id'], $fileName]);

       
        $update = $pdo->prepare("UPDATE reservations SET status = 'Diterima' WHERE id = ?");
        $update->execute([$reservation_id]);

        echo "<script>alert('QR Code berhasil dikirim.'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal mengunggah file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h3>Upload QR Code untuk Pembayaran</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Unggah Gambar QR:</label>
            <input type="file" name="qr_code" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Kirim QR & Konfirmasi</button>
        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>
