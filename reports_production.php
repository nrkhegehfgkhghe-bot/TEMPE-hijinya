<?php
// File: admin/reports_production.php

$page_title = 'Laporan Produksi'; // Judul untuk header
require_once __DIR__ . '/../templates/header.php'; // Memuat template header

checkRole('admin'); // Memastikan hanya admin yang bisa akses
?>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">📈 Laporan Produksi</h2>

    <form id="productionReportFilterForm" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label for="startDate" class="block text-gray-700 text-sm font-semibold mb-2">Dari Tanggal</label>
            <input type="date" id="startDate" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label for="endDate" class="block text-gray-700 text-sm font-semibold mb-2">Sampai Tanggal</label>
            <input type="date" id="endDate" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                Tampilkan Laporan
            </button>
        </div>
    </form>

    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <p class="text-gray-700 text-lg font-semibold">Total Produksi (Kg): <span id="totalProductionKg" class="text-blue-600">0</span></p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-200">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID Produksi</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Jumlah (Kg)</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Catatan</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody id="productionReportTableBody" class="text-gray-700">
                <tr>
                    <td colspan="5" class="text-center p-4">Silakan pilih rentang tanggal dan klik "Tampilkan Laporan".</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="message" class="mt-4 text-center text-sm font-medium"></div>
</div>

<?php
require_once __DIR__ . '/../templates/footer.php'; // Memuat template footer
?>