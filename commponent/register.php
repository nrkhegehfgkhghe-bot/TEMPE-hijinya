<?php
// Koneksi ke database
$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Gagal terhubung ke database: " . $e->getMessage());
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $nama_kosan = $_POST['nama_kosan'] ?? null;
    $no_gopay = $_POST['no_gopay'] ?? null;
    $alamat_kosan = $_POST['alamat_kosan'] ?? null;

    if ($password !== $confirm_password) {
        echo "<script>alert('Password dan konfirmasi tidak cocok!'); window.location.href = 'register.php';</script>";
        exit;
    }

    if (!in_array($role, ['pemilik', 'penyewa'])) {
        echo "<script>alert('Role tidak valid!'); window.location.href = 'register.php';</script>";
        exit;
    }

    if ($role === 'pemilik' && (empty($nama_kosan) || empty($no_gopay) || empty($alamat_kosan))) {
        echo "<script>alert('Data kosan wajib diisi untuk pemilik!'); window.location.href = 'register.php';</script>";
        exit;
    }

    $token = generateToken();

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, nama_kosan, no_gopay, alamat_kosan, is_verified, verification_token) 
                           VALUES (:username, :email, :password, :role, :nama_kosan, :no_gopay, :alamat_kosan, 0, :token)");

    try {
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'nama_kosan' => $nama_kosan,
            'no_gopay' => $no_gopay,
            'alamat_kosan' => $alamat_kosan,
            'token' => $token
        ]);

        // Kirim email menggunakan PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'asuka2505l@gmail.com'; // ganti dengan emailmu
            $mail->Password = 'wmej ymzh yido yfdj'; // ganti dengan App Password dari Google
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('emailkamu@gmail.com', 'KosMen.com');
            $mail->addAddress($email, $username);
            $mail->Subject = 'Verifikasi Akun Anda';
            $verificationLink = "http://localhost/KosMen.com/verifikasi.php?email=" . urlencode($email) . "&token=" . urlencode($token);

$mail->Body = "Halo $username,\n\nKlik link berikut untuk verifikasi akun Anda:\n$verificationLink";

            $mail->send();
            echo "<script>alert('Registrasi berhasil! Silakan cek email untuk verifikasi akun.'); window.location.href = 'index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Registrasi berhasil, tapi gagal kirim email verifikasi: " . $mail->ErrorInfo . "'); window.location.href = 'index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Gagal registrasi: " . $e->getMessage() . "'); window.location.href = 'register.php';</script>";
    }
}
?>

<!-- HTML form tetap sama, tidak perlu diubah -->


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script>
   function toggleNamaKosan() {
  const role = document.getElementById('role').value;
  const namaKosanDiv = document.getElementById('nama_kosan_div');
  const noGopayDiv = document.getElementById('no_gopay_div');
  const alamatKosanDiv = document.getElementById('alamat_kosan_div');

  if (role === 'pemilik') {
    namaKosanDiv.style.display = 'block';
    noGopayDiv.style.display = 'block';
    alamatKosanDiv.style.display = 'block';
  } else {
    namaKosanDiv.style.display = 'none';
    noGopayDiv.style.display = 'none';
    alamatKosanDiv.style.display = 'none';
  }
}

  </script>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 20rem;">
      <h2 class="text-center mb-4">Register</h2>
      <form action="register.php" method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" id="username" name="username" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" id="email" name="email" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" id="password" name="password" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password:</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Daftar Sebagai:</label>
          <select name="role" id="role" class="form-select" onchange="toggleNamaKosan()" required>
            <option value="">Sebagai</option>
            <option value="pemilik">Pemilik</option>
            <option value="penyewa">Penyewa</option>
          </select>
        </div>
        <div class="mb-3" id="nama_kosan_div" style="display: none;">
          <label for="nama_kosan" class="form-label">Nama Kosan:</label>
          <input type="text" id="nama_kosan" name="nama_kosan" class="form-control"/>
        </div>
        <div class="mb-3" id="alamat_kosan_div" style="display: none;">
        <label for="alamat_kosan" class="form-label">Alamat Kosan:</label>
        <textarea name="alamat_kosan" id="alamat_kosan" class="form-control" rows="2" placeholder="Masukkan alamat kosan lengkap"></textarea>
        </div>

        <div class="mb-3" id="no_gopay_div" style="display: none;">
          <label for="no_gopay" class="form-label">Nomor GoPay:</label>
          <input type="text" id="no_gopay" name="no_gopay" class="form-control" pattern="[0-9]{10,15}" placeholder="08xxxxxxxxxx"/>
        </div>
        <button type="submit" class="btn btn-success w-100">Register</button>
      </form>
      <p class="text-center mt-3">
        Sudah punya akun? <a href="index.php">Login</a>
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
