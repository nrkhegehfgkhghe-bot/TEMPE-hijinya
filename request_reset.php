<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$host = 'localhost';
$dbname = 'users';
$username = 'root';
$password = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

$success = $error = "";

function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = generateToken();
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);

        // Kirim email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'elachan2505@gmail.com'; // ganti
            $mail->Password = 'kzzz onwu zlbj xbxf';  // ganti
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('elachan2505@gmail.com', 'KosMen.com');
            $mail->addAddress($email);
            $mail->Subject = 'Reset Password - KosMen.com';
            $mail->isHTML(true);
            $link = "http://localhost/KosMen.com/reset_password.php?email=$email&token=$token";
            $mail->Body = "
                <p>Halo <strong>{$user['username']}</strong>,</p>
                <p>Klik link di bawah ini untuk reset password Anda:</p>
                <p><a href='$link'>$link</a></p>
            ";

            $mail->send();
            $success = "Link reset telah dikirim ke email Anda.";
        } catch (Exception $e) {
            $error = "Gagal mengirim email: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - KosMen.com</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
<div class="container mt-5" style="max-width: 500px;">
    <h3 class="text-center">🔐 Minta Reset Password</h3>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Email Anda</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button class="btn btn-warning w-100">Kirim Link Reset</button>
    </form>
</div>
</body>
</html>
