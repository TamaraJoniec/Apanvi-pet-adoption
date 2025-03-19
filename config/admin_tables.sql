-- Create admin_users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin_login_logs table
CREATE TABLE IF NOT EXISTS admin_login_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    username VARCHAR(50),
    action ENUM('login', 'logout', 'failed_login') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    status ENUM('success', 'failed') DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: Admin123!)
INSERT INTO admin_users (username, email, password) VALUES 
('admin', 'admin@example.com', '$2y$10$8KzQ.ROCxE7.0TjtZYJ7K.vcTtT.0TjtZYJ7K.vcT'); 