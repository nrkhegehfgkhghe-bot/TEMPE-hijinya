<?php
// File: pengunjung/dashboard.php - Dashboard untuk Pengunjung (pembeli)

session_start();

$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$username = $isLoggedIn ? $_SESSION['username'] : 'Pengunjung';
$role = $isLoggedIn ? $_SESSION['role'] : 'pengunjung';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung - Pabrik Tempe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-leaf text-2xl"></i>
                    <span class="text-xl font-bold">Pabrik Tempe</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Halo, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <a href="../api/logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        
        <!-- Produk Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-box text-green-600 mr-2"></i>Daftar Produk
            </h2>
            
            <div id="productsLoading" class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-2xl text-green-600"></i>
                <p class="text-gray-500 mt-2">Memuat produk...</p>
            </div>
            
            <div id="productsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" style="display:none;">
            </div>
            
            <div id="productsError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            </div>

        <!-- Pemesanan Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-shopping-cart text-green-600 mr-2"></i>Pemesanan Produk
            </h2>
            
            <form id="orderForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Produk</label>
                        <select id="productSelect" name="product_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Pilih Produk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Jumlah (Kg)</label>
                        <input type="number" id="quantityKg" name="quantity_kg" step="0.1" min="0.1" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Masukkan jumlah dalam Kg">
                    </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Pemesan</label>
                        <input type="text" id="customerName" name="customer_name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Nama lengkap Anda">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP</label>
                        <input type="tel" id="customerPhone" name="customer_phone" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Nomor WhatsApp">
                    </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat Pengiriman</label>
                    <textarea id="customerAddress" name="customer_address" rows="3" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Alamat lengkap untuk pengiriman"></textarea>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pesan</label>
                    <input type="date" id="saleDate" name="sale_date" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-shopping-cart mr-2"></i>Pesan Sekarang
                </button>
            </form>
            
            <div id="orderMessage" class="mt-4 text-center text-sm font-medium"></div>

        <!-- Riwayat Pesanan Section -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                <i class="fas fa-history text-green-600 mr-2"></i>Riwayat Pesanan Saya
            </h2>
            
            <div id="ordersLoading" class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-2xl text-green-600"></i>
                <p class="text-gray-500 mt-2">Memuat riwayat pesanan...</p>
            </div>
            
            <div class="overflow-x-auto">
                <table id="ordersTable" class="w-full text-left border-collapse" style="display:none;">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border-b">ID</th>
                            <th class="p-3 border-b">Tanggal</th>
                            <th class="p-3 border-b">Produk</th>
                            <th class="p-3 border-b">Jumlah (Kg)</th>
                            <th class="p-3 border-b">Alamat</th>
                            <th class="p-3 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                    </tbody>
                </table>
            </div>
            
            <div id="noOrdersMessage" class="hidden text-center py-4 text-gray-500">
                Anda belum memiliki pesanan.
            </div>

    </div>

    <script>
        document.getElementById('saleDate').valueAsDate = new Date();

        async function loadProducts() {
            try {
                const response = await fetch('../api/get_products_stock.php');
                const data = await response.json();
                
                document.getElementById('productsLoading').style.display = 'none';
                
                if (data.status === 'success' && data.data.length > 0) {
                    const container = document.getElementById('productsContainer');
                    const select = document.getElementById('productSelect');
                    
                    data.data.forEach(product => {
                        const card = document.createElement('div');
                        card.className = 'bg-white rounded-lg shadow-md p-4 border border-gray-200';
                        card.innerHTML = '<h3 class="font-bold text-lg text-gray-800">' + escapeHTML(product.product_name) + '</h3>' +
                            '<div class="mt-2"><span class="text-2xl font-bold text-green-600">' + parseFloat(product.current_stock_kg).toFixed(1) + '</span>' +
                            '<span class="text-gray-500">Kg tersedia</span></div>' +
                            '<p class="text-xs text-gray-400 mt-2">Update: ' + new Date(product.last_updated).toLocaleDateString() + '</p>';
                        container.appendChild(card);
                        
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = product.product_name + ' (Stok: ' + parseFloat(product.current_stock_kg).toFixed(1) + ' Kg)';
                        option.dataset.stock = product.current_stock_kg;
                        select.appendChild(option);
                    });
                    
                    container.style.display = 'grid';
                } else {
                    document.getElementById('productsError').textContent = 'Belum ada produk tersedia.';
                    document.getElementById('productsError').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading products:', error);
                document.getElementById('productsLoading').style.display = 'none';
                document.getElementById('productsError').textContent = 'Gagal memuat produk. Silakan coba lagi.';
                document.getElementById('productsError').style.display = 'block';
            }
        }

        async function loadOrders() {
            try {
                const response = await fetch('../api/get_sales.php');
                const data = await response.json();
                
                document.getElementById('ordersLoading').style.display = 'none';
                
                if (data.status === 'success' && data.data.length > 0) {
                    const tbody = document.getElementById('ordersTableBody');
                    
                    data.data.forEach(order => {
                        const row = document.createElement('tr');
                        row.className = 'border-b hover:bg-gray-50';
                        row.innerHTML = '<td class="p-3">#' + order.id + '</td>' +
                            '<td class="p-3">' + order.sale_date + '</td>' +
                            '<td class="p-3">' + escapeHTML(order.product_name || 'Tempe') + '</td>' +
                            '<td class="p-3">' + parseFloat(order.quantity_kg).toFixed(1) + ' Kg</td>' +
                            '<td class="p-3">' + escapeHTML(order.customer_address || '-') + '</td>' +
                            '<td class="p-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Diterima</span></td>';
                        tbody.appendChild(row);
                    });
                    
                    document.getElementById('ordersTable').style.display = 'table';
                } else {
                    document.getElementById('noOrdersMessage').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                document.getElementById('ordersLoading').style.display = 'none';
                document.getElementById('noOrdersMessage').textContent = 'Gagal memuat riwayat pesanan.';
                document.getElementById('noOrdersMessage').style.display = 'block';
            }
        }

        document.getElementById('orderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            var messageDiv = document.getElementById('orderMessage');
            var submitBtn = e.target.querySelector('button[type="submit"]');
            
            messageDiv.textContent = 'Memproses pesanan...';
            messageDiv.className = 'mt-4 text-center text-sm font-medium text-gray-600';
            submitBtn.disabled = true;
            
            var formData = new FormData(e.target);
            
            try {
                var response = await fetch('../api/record_sale.php', {
                    method: 'POST',
                    body: formData
                });
                
                var data = await response.json();
                
                messageDiv.textContent = data.message;
                if (data.status === 'success') {
                    messageDiv.className = 'mt-4 text-center text-sm font-medium text-green-600';
                    e.target.reset();
                    document.getElementById('saleDate').valueAsDate = new Date();
                    loadProducts();
                    loadOrders();
                } else {
                    messageDiv.className = 'mt-4 text-center text-sm font-medium text-red-600';
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.textContent = 'Gagal mengirim pesanan. Periksa koneksi Anda.';
                messageDiv.className = 'mt-4 text-center text-sm font-medium text-red-600';
            }
            
            submitBtn.disabled = false;
        });

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            var map = {
                '&': '&amp;',
                '<': '<',
                '>': '>',
                "'": '&#39;',
                '"': '"'
            };
            return str.replace(/[&<>'"]/g, function(m) { return map[m]; });
        }

        loadProducts();
        loadOrders();
    </script>
</body>
</html>
