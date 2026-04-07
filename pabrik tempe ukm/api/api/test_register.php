<?php
// Test registration - hardcoded data
header('Content-Type: application/json');

require_once 'db_connect.php';

$test_username = 'testuser';
$test_email = 'test@example.com';
$test_password = '123456';
$test_role = 'pengunjung';

echo "Testing registration...\n";

try {
    // Check exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE nama = ? OR email = ?");
    $stmt->bind_param("ss", $test_username, $test_email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "User already exists.\n";
    } else {
        $hashed = password_hash($test_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $test_username, $test_email, $hashed, $test_role);
        if ($stmt->execute()) {
            echo "✅ SUCCESS! Test user '$test_username' tersimpan di DB.\n";
            echo "ID: " . $conn->insert_id . "\n";
        } else {
            echo "❌ INSERT FAILED: " . $stmt->error . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
echo "Test selesai.\n";
?>
