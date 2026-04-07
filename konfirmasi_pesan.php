<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $aksi = $_POST['aksi'] ?? null;

    if (!$id || !$aksi) {
        die("Data tidak lengkap.");
    }

    $pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($aksi === 'diterima') {
        // Update status di tabel reservations
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'Diterima' WHERE id = ?");
        $stmt->execute([$id]);

        // Ambil data reservasi untuk keperluan pembayaran
        $stmtData = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmtData->execute([$id]);
        $data = $stmtData->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            // Simpan ke tabel pembayaran (otomatis status Belum Dibayar)
            $insert = $pdo->prepare("INSERT INTO pembayaran (user_id, room_id, status, qr_code) VALUES (?, ?, 'Belum Dibayar', 'gopay.png')");
            $insert->execute([$data['user_id'], $data['room_id']]);
        }

    } elseif ($aksi === 'ditolak') {
        // Update status ditolak
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'Ditolak' WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: dashboard.php");
    exit();
}
