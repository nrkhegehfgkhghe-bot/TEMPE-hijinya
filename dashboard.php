<?php
// File: admin/dashboard.php

// Atur judul halaman sebelum memanggil header
$page_title = 'Dashboard Admin';

// Panggil template header. Header akan menangani otentikasi.
require_once __DIR__ . '/../templates/header.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
// Fungsi ini tersedia karena kita sudah memanggil header.php (yang memanggil auth_guard.php)
checkRole('admin');
?>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Menu Utama Admin</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-blue-100 p-6 rounded-lg hover:shadow-xl transition-shadow duration-300">
            <i class="fas fa-users text-blue-500 text-4xl mb-4"></i>
            <h3 class="font-bold text-xl text-blue-800">Manajemen Pengguna</h3>
            <p class="text-blue-600 mt-2">Tambah, ubah, atau hapus data pengguna sistem.</p>
            <a href="manage_users.php" class="inline-block mt-4 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700">Kelola</a>
        </div>

        <div class="bg-purple-100 p-6 rounded-lg hover:shadow-xl transition-shadow duration-300">
            <i class="fas fa-box-open text-purple-500 text-4xl mb-4"></i>
            <h3 class="font-bold text-xl text-purple-800">Manajemen Produk</h3>
            <p class="text-purple-600 mt-2">Tambah, lihat, atau kelola daftar produk dan stok.</p>
            <a href="manage_products.php" class="inline-block mt-4 bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-purple-700">Kelola</a>
        </div>

        <div class="bg-green-100 p-6 rounded-lg hover:shadow-xl transition-shadow duration-300">
            <i class="fas fa-chart-bar text-green-500 text-4xl mb-4"></i>
            <h3 class="font-bold text-xl text-green-800">Laporan Penjualan</h3>
            <p class="text-green-600 mt-2">Lihat rekapitulasi dan detail penjualan.</p>
            <a href="reports_sales.php" class="inline-block mt-4 bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700">Lihat Laporan</a>
        </div>

        <div class="bg-yellow-100 p-6 rounded-lg hover:shadow-xl transition-shadow duration-300">
            <i class="fas fa-industry text-yellow-500 text-4xl mb-4"></i>
            <h3 class="font-bold text-xl text-yellow-800">Laporan Produksi</h3>
            <p class="text-yellow-600 mt-2">Lihat rekapitulasi dan detail produksi.</p>
            <a href="reports_production.php" class="inline-block mt-4 bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-yellow-700">Lihat Laporan</a>
        </div>

    </div>
</div>

<?php
// Memuat bagian footer dari template
require_once __DIR__ . '/../templates/footer.php';
?>