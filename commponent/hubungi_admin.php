<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Koneksi database
$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subjek']);
    $pesan = trim($_POST['pesan']);

    if (empty($nama) || empty($email) || empty($pesan)) {
        $error = "Nama, Email, dan Pesan wajib diisi.";
    } else {
        // Simpan ke database
        $stmt = $pdo->prepare("INSERT INTO kontak_admin (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $email, $subjek, $pesan]);

        // Kirim ke email admin
        $mail = new PHPMailer(true);
        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'elachan2505@gmail.com'; // ganti
            $mail->Password = 'ckfj ejxz rdmx xwmi';   // ganti App Password Gmail
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($email, $nama);
            $mail->addAddress('elachan2505@gmail.com', 'Admin KosMen'); // tujuan

            $mail->isHTML(true);
            $mail->Subject = "Pesan dari $nama - $subjek";
            $mail->Body = "
                <h3>Pesan Baru dari $nama</h3>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subjek:</strong> $subjek</p>
                <p><strong>Pesan:</strong><br>" . nl2br(htmlspecialchars($pesan)) . "</p>
                <hr>
                <small>Dikirim dari halaman Hubungi Admin KosMen.com</small>
            ";

            $mail->send();
            $success = "Pesan berhasil dikirim ke admin!";
        } catch (Exception $e) {
            $error = "Gagal mengirim email: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hubungi Admin - KosMen.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f1f4f9;
        }
        .container {
            max-width: 600px;
            margin-top: 60px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4a69bd;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Hubungi Admin</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nama *</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subjek</label>
            <input type="text" name="subjek" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Pesan *</label>
            <textarea name="pesan" rows="5" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Kirim Pesan</button>
    </form>
</div>
 <div class="text-center">
        <a href="index.php">kembali ke halaman login</a>
    </div>
</body>
</html>

