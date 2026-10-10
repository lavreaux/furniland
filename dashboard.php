<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Furniland</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo">Furniland</a>
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
            </div>
            <div class="nav-right">
                <a href="profile.php" class="nav-link">Hello, <?php echo htmlspecialchars($username); ?></a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1 class="dashboard-title">Admin Dashboard</h1>
        
        <div class="dashboard-cards">
            <a href="manage-products.php" class="dashboard-card">
                <h3 class="card-title">Manage Products</h3>
                <p class="card-description">View, edit, and add furniture products</p>
            </a>
            
            <a href="manage-vendors.php" class="dashboard-card">
                <h3 class="card-title">Manage Vendors</h3>
                <p class="card-description">View and manage furniture vendors</p>
            </a>
            
            <a href="manage-users.php" class="dashboard-card">
                <h3 class="card-title">Manage Users</h3>
                <p class="card-description">View and control user accounts</p>
            </a>
            
            <a href="view-transactions.php" class="dashboard-card">
                <h3 class="card-title">View Transactions</h3>
                <p class="card-description">Monitor purchase history and details</p>
            </a>
        </div>

        <div class="welcome-box">
            <h2 class="welcome-title">Welcome, Admin <?php echo htmlspecialchars($username); ?>!</h2>
            <p class="welcome-text">Use the cards above to manage the platform. Keep track of users, inventory, vendors, and transactions efficiently.</p>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
