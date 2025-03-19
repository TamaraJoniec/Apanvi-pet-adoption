<?php
require_once '../config/database.php';

try {
    // Check if is_admin column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    if ($stmt->rowCount() == 0) {
        // Add is_admin column if it doesn't exist
        $conn->exec("ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE");
    }

    // Create or update admin user
    $email = 'admin@apanvi.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $username = 'admin';

    // Check if admin user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin) {
        // Update existing user to admin
        $stmt = $conn->prepare("UPDATE users SET is_admin = TRUE WHERE id = ?");
        $stmt->execute([$admin['id']]);
        echo "Existing user updated to admin successfully.\n";
    } else {
        // Create new admin user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, TRUE)");
        $stmt->execute([$username, $email, $password]);
        echo "Admin user created successfully.\n";
    }

    echo "Admin setup completed. You can now login with:\n";
    echo "Email: admin@apanvi.com\n";
    echo "Password: admin123\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 