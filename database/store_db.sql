CREATE DATABASE IF NOT EXISTS store_db;
USE store_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Makanan',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, category, price, stock) VALUES
('Nasi Goreng Special Adara', 'Makanan', 28000, 20),
('Kopi Susu Aren Adara', 'Minuman', 22000, 35),
('Croissant Butter', 'Pastry', 18000, 4);