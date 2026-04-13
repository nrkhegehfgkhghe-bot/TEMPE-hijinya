<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

// Dapatkan user_id dari session
$user_id = $_SESSION['user_id'];

// Statistik kamar berdasarkan user_id
$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalRooms = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE status = 'Ditempati' AND user_id = ?");
$stmt->execute([$user_id]);
$occupiedRooms = $stmt->fetchColumn();

$availableRooms = $totalRooms - $occupiedRooms;

// Data pengingat pembayaran berdasarkan user_id
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS count 
    FROM rooms
    WHERE status = 'Ditempati'
    AND user_id = ?
    AND DATEDIFF(DATE_ADD(tenant_date, INTERVAL 1 MONTH), NOW()) BETWEEN 0 AND 3
");
$stmt->execute([$user_id]);
$dueSoon = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) AS count 
    FROM rooms
    WHERE status = 'Ditempati'
    AND user_id = ?
    AND DATEDIFF(DATE_ADD(tenant_date, INTERVAL 1 MONTH), NOW()) < 0
");
$stmt->execute([$user_id]);
$overdue = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("
    SELECT r.*, u.username AS penyewa, rm.number AS no_kamar 
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN rooms rm ON r.room_id = rm.id
    WHERE r.pemilik_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

  <!-- Notifikasi Pemesanan Baru -->
<div class="mt-5">
    <h3>Notifikasi Pemesanan Kamar</h3>
    <?php if (count($pesanan) > 0): ?>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Tanggal Pesan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pesanan as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['penyewa']) ?></td>
                        <td><?= htmlspecialchars($p['no_kamar']) ?></td>
                        <td><?= htmlspecialchars($p['tanggal_pesan']) ?></td>
                        <td><?= $p['status'] ?? 'Menunggu' ?></td>
                        <td>
                            <?php if (($p['status'] ?? 'Menunggu') === 'Menunggu'): ?>
                                <form method="POST" action="konfirmasi_pesan.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button name="aksi" value="diterima" class="btn btn-success btn-sm">Terima</button>
                                    <button name="aksi" value="ditolak" class="btn btn-danger btn-sm">Tolak</button>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($p['status']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada pemesanan baru saat ini.</p>
    <?php endif; ?>
</div>

