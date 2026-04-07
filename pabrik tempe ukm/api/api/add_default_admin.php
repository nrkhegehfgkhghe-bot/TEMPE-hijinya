<?php
// One-time script: Add default admin (admin/admin123)
require_once 'db_connect.php';

$admin_name = 'admin';
$admin_email = 'admin@pabrik-tempe.com';
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$admin_role = 'admin';

// Check if exists
$stmt = $conn->prepare("SELECT id FROM users WHERE nama = ?");
$stmt->bind_param("s", $admin_name);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "Admin already exists.\n";
} else {
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role, is_verified) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("ssss", $admin_name, $admin_email, $admin_pass, $admin_role);
    if ($stmt->execute()) {
        echo "✅ Default admin created: admin / admin123\n";
    } else {
        echo "❌ Failed: " . $conn->error . "\n";
    }
}
$conn->close();
?>
