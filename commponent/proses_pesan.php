<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: login.php");
    exit();
}

$room_id = $_POST['room_id'] ?? null;
$pemilik_id = $_POST['pemilik_id'] ?? null;
$penyewa_id = $_SESSION['user_id'];

if (!$room_id || !$pemilik_id) {
    die("Data tidak lengkap.");
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert ke tabel reservations
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, room_id, pemilik_id, status) VALUES (?, ?, ?, 'Menunggu')");
    $stmt->execute([$penyewa_id, $room_id, $pemilik_id]);

    header("Location: dashboard_penyewa.php?berhasil=1");
    exit();
} catch (PDOException $e) {
    die("Gagal memesan: " . $e->getMessage());
}
