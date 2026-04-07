<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    die("Akses ditolak.");
}

$reservation_id = $_POST['reservation_id'] ?? null;
if (!$reservation_id || !isset($_FILES['bukti'])) {
    die("Data tidak lengkap.");
}

$uploadDir = "uploads/";
$filename = uniqid() . "_" . basename($_FILES['bukti']['name']);
$targetFile = $uploadDir . $filename;

if (move_uploaded_file($_FILES['bukti']['tmp_name'], $targetFile)) {
    $pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil room_id dan user_id dari reservasi
    $stmt = $pdo->prepare("SELECT room_id, user_id FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $stmt = $pdo->prepare("
            UPDATE pembayaran 
            SET bukti_transfer = ?, status = 'Menunggu Konfirmasi'
            WHERE user_id = ? AND room_id = ?
        ");
        $stmt->execute([$filename, $row['user_id'], $row['room_id']]);
    }

    header("Location: dashboard_penyewa.php?upload=waiting");
    exit();
} else {
    echo "Upload gagal.";
}
