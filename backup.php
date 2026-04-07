<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup dan Restore Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212; /* Latar belakang hitam */
            color: white; /* Teks putih */
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(0, 0, 0, 0.8); /* Kontainer dengan latar belakang sedikit transparan */
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #f1c40f; /* Warna kuning untuk judul */
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
        }

        h4 {
            color: #3498db; /* Warna biru untuk subjudul */
            font-size: 1.5rem;
            margin-top: 20px;
        }

        .back-btn {
            font-size: 1.1rem;
            background: #17a2b8;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #138496;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .form-label {
            color: #ecf0f1; /* Warna terang untuk label input */
        }

        .form-control {
            background-color: #2c3e50; /* Latar belakang gelap untuk input */
            color: white;
            border: 1px solid #34495e;
        }

        .form-control:focus {
            border-color: #3498db;
            background-color: #34495e;
        }

        .mb-4 {
            margin-bottom: 30px;
        }

        .text-end {
            margin-bottom: 20px;
        }

        hr {
            border: 1px solid #444;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Fitur Backup dan Restore Data</h1>
        
        <div class="text-end mb-4">
            <a href="admin_dashboard.php" class="back-btn">Kembali ke Dashboard</a>
        </div>

        <!-- Form Backup -->
        <form method="POST" class="mb-4">
            <button type="submit" name="backup" class="btn btn-success">Backup Data</button>
        </form>

        <hr>

        <!-- Form Restore -->
        <h4>Restore Data dari File SQL</h4>
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="backup_file" class="form-label">Pilih File SQL untuk Restore</label>
                <input type="file" name="backup_file" class="form-control" id="backup_file" accept=".sql" required>
            </div>
            <button type="submit" name="restore" class="btn btn-primary">Restore Data</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
