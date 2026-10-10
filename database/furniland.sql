-- Create database
CREATE DATABASE IF NOT EXISTS furniland;
USE furniland;

-- Table users
CREATE TABLE IF NOT EXISTS users (
    userID INT(10) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(40) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender VARCHAR(6) NOT NULL,
    dob DATE NOT NULL,
    role VARCHAR(6) NOT NULL DEFAULT 'Member'
);

-- Table vendors
CREATE TABLE IF NOT EXISTS vendors (
    vendorID INT(10) AUTO_INCREMENT PRIMARY KEY,
    vendorName VARCHAR(20) NOT NULL,
    location VARCHAR(100) NOT NULL
);

-- Table products
CREATE TABLE IF NOT EXISTS products (
    productID INT(10) AUTO_INCREMENT PRIMARY KEY,
    productName VARCHAR(30) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price FLOAT(10) NOT NULL,
    image VARCHAR(100) NOT NULL,
    vendorID INT(10) NOT NULL,
    FOREIGN KEY (vendorID) REFERENCES vendors(vendorID) ON DELETE CASCADE
);

-- Table cart
CREATE TABLE IF NOT EXISTS cart (
    cartID INT(10) AUTO_INCREMENT PRIMARY KEY,
    userID INT(10) NOT NULL,
    productID INT(10) NOT NULL,
    quantity INT(5) NOT NULL,
    FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE,
    FOREIGN KEY (productID) REFERENCES products(productID) ON DELETE CASCADE
);

-- Table transactions
CREATE TABLE IF NOT EXISTS transactions (
    transactionID INT(10) AUTO_INCREMENT PRIMARY KEY,
    userID INT(10) NOT NULL,
    totalPrice FLOAT(10) NOT NULL,
    transactionDate DATE NOT NULL,
    FOREIGN KEY (userID) REFERENCES users(userID) ON DELETE CASCADE
);

-- Table transaction_details
CREATE TABLE IF NOT EXISTS transaction_details (
    detailID INT(10) AUTO_INCREMENT PRIMARY KEY,
    transactionID INT(10) NOT NULL,
    productID INT(10) NOT NULL,
    quantity INT(5) NOT NULL,
    subtotal INT(10) NOT NULL,
    FOREIGN KEY (transactionID) REFERENCES transactions(transactionID) ON DELETE CASCADE,
    FOREIGN KEY (productID) REFERENCES products(productID) ON DELETE CASCADE
);

-- Insert admin
INSERT INTO users (username, email, password, gender, dob, role) VALUES 
('admin', 'admin@gmail.com', 'admin123', 'Male', '1990-01-01', 'Admin');

-- Insert users
INSERT INTO users (username, email, password, gender, dob, role) VALUES 
('steven', 'steven.gunawan010@gmail.com', 'password123', 'Male', '1995-05-15', 'Member'),
('christopher devin', 'kevin@gmail.com', 'password123', 'Male', '1998-08-20', 'Member'),
('joshua', 'joshua.kartadi@gmail.com', 'password123', 'Male', '2005-06-01', 'Member');

-- Insert vendors
INSERT INTO vendors (vendorName, location) VALUES 
('IKEA', 'Stockholm, Sweden'),
('Ashley Furniture', 'Wisconsin, USA'),
('Wayfair', 'Boston, USA'),
('West Elm', 'New York, USA');

-- Insert products
INSERT INTO products (productName, description, price, image, vendorID) VALUES 
('Modern Sofa', 'Comfortable 3-seater sofa with soft cushions', 500000, 'sofa.webp', 1),
('Wooden Dining Table', 'Solid oak dining table for 6 people', 300000, 'table1.webp', 2),
('Office Chair', 'Ergonomic office chair with lumbar support', 150000, 'chair1.webp', 3),
('King Size Bed', 'Luxurious king size bed frame with storage', 800000, 'build-your-bed.jpg', 4),
('Coffee Table', 'Modern glass coffee table with metal frame', 100000, 'table2.webp', 1),
('Bookshelf', '5-tier wooden bookshelf for storage', 200000, 'shelf.jpeg', 2),
('Recliner Chair', 'Leather recliner with footrest', 400000, 'chair2.jpg', 3),
('Nightstand', 'Compact nightstand with 2 drawers', 50000, 'nightstand.webp', 4),
('TV Stand', 'Entertainment center with cable management', 250000, 'tvstand1.jpg', 1),
('Wardrobe', 'Large wardrobe with mirror doors', 600000 , 'wardrobe1.jpg', 2);

-- Insert cart items
INSERT INTO cart (userID, productID, quantity) VALUES 
(2, 1, 1),
(2, 5, 2);

-- Insert transactions
INSERT INTO transactions (userID, totalPrice, transactionDate) VALUES 
(2, 700, '2025-12-01'),
(3, 150, '2025-12-05');

-- Insert transaction details
INSERT INTO transaction_details (transactionID, productID, quantity, subtotal) VALUES 
(1, 1, 1, 500),
(1, 5, 2, 200),
(2, 3, 1, 150);