<?php
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
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

// Fungsi untuk mengonversi data ke format SQL
function exportToSQL($pdo, $tableName) {
    // Mengambil nama kolom tabel
    $columns = [];
    $result = $pdo->query("DESCRIBE $tableName");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }

    // Menyiapkan SQL untuk insert data
    $sql = "INSERT INTO $tableName (" . implode(", ", $columns) . ") VALUES\n";
    $stmt = $pdo->query("SELECT * FROM $tableName");

    $values = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rowValues = [];
        foreach ($columns as $column) {
            $rowValues[] = "'" . addslashes($row[$column]) . "'";
        }
        $values[] = "(" . implode(", ", $rowValues) . ")";
    }

    // Gabungkan semua data ke dalam format SQL
    $sql .= implode(",\n", $values) . ";";

    return $sql;
}

// Mengambil data dari semua tabel yang ingin diekspor
$tables = ['rooms', 'users']; // Daftar tabel yang ingin diekspor
$sqlData = '';

foreach ($tables as $table) {
    $sqlData .= "-- Export for table: $table\n";
    $sqlData .= exportToSQL($pdo, $table);
    $sqlData .= "\n\n";
}

// Mengirimkan file SQL untuk diunduh
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="backup_data_' . date('Y-m-d_H-i-s') . '.sql"');
echo $sqlData;
exit;
?>
