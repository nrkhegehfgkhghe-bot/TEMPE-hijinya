<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

$pembayaran_id = $_POST['pembayaran_id'] ?? null;
$aksi = $_POST['aksi'] ?? null;

$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($pembayaran_id && $aksi === 'terima') {
    // Ambil user_id dan room_id dari pembayaran
    $stmt = $pdo->prepare("SELECT user_id, room_id FROM pembayaran WHERE id = ?");
    $stmt->execute([$pembayaran_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // 1. Tandai pembayaran disetujui
        $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'Sudah Dibayar', tanggal_mulai = CURDATE() WHERE id = ?");
        $stmt->execute([$pembayaran_id]);

        // 2. Tandai kamar sebagai 'Ditempati'
        $stmt = $pdo->prepare("UPDATE rooms SET status = 'Ditempati' WHERE id = ?");
        $stmt->execute([$data['room_id']]);
    }

} elseif ($pembayaran_id && $aksi === 'tolak') {
    $stmt = $pdo->prepare("UPDATE pembayaran SET status = 'Ditolak' WHERE id = ?");
    $stmt->execute([$pembayaran_id]);
}

header("Location: monitor_pembayaran.php");
exit();
