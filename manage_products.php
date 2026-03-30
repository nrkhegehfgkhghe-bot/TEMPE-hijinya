<?php
// File: admin/manage_products.php

$page_title = 'Manajemen Produk'; // Judul untuk header
require_once __DIR__ . '/../templates/header.php'; // Memuat template header

checkRole('admin'); // Memastikan hanya admin yang bisa akses
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-lg h-full">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">➕ Tambah Produk Baru</h2>
            <form id="addProductForm">
                <div class="mb-4">
                    <label for="product_name" class="block text-gray-700 text-sm font-semibold mb-2">Nama Produk</label>
                    <input type="text" id="product_name" name="product_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Tempe Kemasan 500g">
                </div>
                <div class="mb-6">
                    <label for="initial_stock_kg" class="block text-gray-700 text-sm font-semibold mb-2">Stok Awal (Kg)</label>
                    <input type="number" step="0.01" id="initial_stock_kg" name="initial_stock_kg" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 100.00">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700">
                    Tambahkan Produk
                </button>
            </form>
            <div id="messageAddProduct" class="mt-4 text-center text-sm font-medium"></div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-bold text-gray-800 mb-6">📦 Daftar Produk dan Stok</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">ID</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Produk</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Stok (Kg)</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Terakhir Diperbarui</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody" class="text-gray-700">
                        <tr>
                            <td colspan="5" class="text-center p-4">Memuat data produk...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="messageProductList" class="mt-4 text-center text-sm font-medium"></div>
        </div>
    </div>
</div>

<div id="deliveryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">🚚 Kirim Produk <span id="modalProductName" class="text-blue-600"></span></h2>
        <form id="directDeliveryForm">
            <input type="hidden" id="deliveryProductId" name="product_id">
            <input type="hidden" id="deliveryProductName" name="product_name_for_api">

            <div class="mb-4">
                <label for="deliveryQuantity" class="block text-gray-700 text-sm font-semibold mb-2">Jumlah Dikirim (Kg)</label>
                <input type="number" step="0.01" id="deliveryQuantity" name="quantity_kg" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Jumlah produk yang akan dikirim">
            </div>
            <div class="mb-4">
                <label for="deliveryCustomerName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Penerima</label>
                <input type="text" id="deliveryCustomerName" name="customer_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Nama pelanggan/penerima">
            </div>
            <div class="mb-4">
                <label for="deliveryAddress" class="block text-gray-700 text-sm font-semibold mb-2">Alamat Pengiriman</label>
                <textarea id="deliveryAddress" name="delivery_address" rows="2" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Alamat lengkap pengiriman"></textarea>
            </div>
            <div class="mb-4">
                <label for="deliveryPhone" class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Penerima</label>
                <input type="text" id="deliveryPhone" name="delivery_phone" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Nomor HP penerima">
            </div>
            <div class="mb-6">
                <label for="deliveryNotes" class="block text-gray-700 text-sm font-semibold mb-2">Catatan (Opsional)</label>
                <textarea id="deliveryNotes" name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Catatan tambahan untuk pengiriman"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="closeDeliveryModal" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-400">Batal</button>
                <button type="submit" class="bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700">Jadwalkan Pengiriman</button>
            </div>
            <div id="messageDirectDelivery" class="mt-4 text-center text-sm font-medium"></div>
        </form>
    </div>
</div>


<?php
require_once __DIR__ . '/../templates/footer.php'; // Memuat template footer
?>