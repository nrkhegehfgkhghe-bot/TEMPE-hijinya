<?php
require_once 'db_connect.php';

echo "=== STATUS DATABASE Pabrik Tempe ===\n";
echo "Koneksi DB OK.\n\n";

echo "=== CEK TABEL USERS ===\n";
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "✅ Tabel 'users' ADA.\n";
    
    $r = $conn->query("SELECT COUNT(*) as total FROM users");
    $row = $r->fetch_assoc();
    echo "📊 Jumlah user: " . $row['total'] . "\n";
    
    echo "\n--- DAFTAR USER TERBARU (10 terakhir) ---\n";
    $r = $conn->query("SELECT id, nama, email, role, created_at FROM users ORDER BY id DESC LIMIT 10");
    while ($row = $r->fetch_assoc()) {
        echo sprintf("ID %3d | %-15s | %-25s | %-10s | %s\n", 
            $row['id'], $row['nama'], $row['email'], $row['role'], $row['created_at']);
    }
} else {
    echo "❌ Tabel 'users' TIDAK ADA! Jalankan api/setup_database.sql di phpMyAdmin.\n";
}

echo "\n=== TEST SELESAI ===\n";
$conn->close();
?>

