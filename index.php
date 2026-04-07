<?php
session_start();

// Koneksi ke database
$pdo = new PDO("mysql:host=localhost;dbname=users", "root", "");

// Jika form login di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Ambil data pengguna berdasarkan username dan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
       
        if (isset($user['is_deleted']) && $user['is_deleted'] == 1) {
            $error = "Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.";
        }
       
        elseif ($user['role'] !== 'admin' && isset($user['is_verified']) && $user['is_verified'] == 0) {
            $error = "Akun belum diverifikasi. Silakan cek email Anda.";
        }
        
        elseif ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

           
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'pemilik':
                    header("Location: dashboard.php");
                    break;
                case 'penyewa':
                    header("Location: dashboard_penyewa.php");
                    break;
                default:
                    $error = "Role tidak dikenali. Hubungi admin.";
                    break;
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username atau Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KosMen.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.8rem;
            text-align: center;
            color: #4a69bd;
        }
        .btn-primary {
            background: #4a69bd;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Login</h1>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <p class="text-center mt-3">
        Belum punya akun? <a href="register.php" class="btn btn-link">Register</a>
    </p>
    <div class="text-center">
        <a href="request_reset.php">Lupa password?</a>
    </div>
    <br>
    <div class="text-center">
        <a href="hubungi_admin.php">hubungi admin</a>
    </div>

</div>
</body>
</html>
