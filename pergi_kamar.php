<?php
session_start();

// Hanya penyewa yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: login.php");
    exit;
}

$reservation_id = $_GET['reservation_id'] ?? null;

if (!$reservation_id) {
    die("ID reservasi tidak ditemukan.");
}

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ambil room_id dari reservasi
$stmt = $pdo->prepare("SELECT room_id FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user_id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Reservasi tidak valid atau bukan milik Anda.");
}

$room_id = $data['room_id'];

// 1. Kosongkan kamar
$pdo->prepare("UPDATE rooms SET status = 'Kosong' WHERE id = ?")->execute([$room_id]);

// 2. Hapus data pembayaran terkait
$pdo->prepare("DELETE FROM pembayaran WHERE user_id = ? AND room_id = ?")->execute([$_SESSION['user_id'], $room_id]);

// 3. Hapus reservasi
$pdo->prepare("DELETE FROM reservations WHERE id = ?")->execute([$reservation_id]);

// Redirect kembali ke dashboard
header("Location: dashboard_penyewa.php?keluar=success");
exit();
