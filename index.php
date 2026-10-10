<?php
session_start();
require_once 'database/database.php';

$query = "SELECT p.*, v.vendorName FROM products p 
          INNER JOIN vendors v ON p.vendorID = v.vendorID 
          ORDER BY RAND() 
          LIMIT 6";
$products = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniland - Furnitures you might love</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo">Furniland</a>
                <?php if (isset($_SESSION['userID']) && $_SESSION['role'] == 'Admin'): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                <?php else: ?>
                    <a href="index.php" class="nav-link active">Home</a>
                    <?php if (isset($_SESSION['userID'])): ?>
                        <a href="catalog.php" class="nav-link">Catalog</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="nav-right">
                <?php if (isset($_SESSION['userID'])): ?>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <a href="profile.php" class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <?php else: ?>
                        <a href="cart.php" class="nav-link">Cart</a>
                        <a href="history.php" class="nav-link">History</a>
                        <a href="profile.php" class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-link logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="home-container">
        <div class="hero-section">
            <h1 class="hero-title">Furniland</h1>
            <p class="hero-subtitle">Furnitures you might love</p>
        </div>

        <div class="home-products">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="home-product-card">
                        <div class="home-product-image">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
                        </div>
                        <div class="home-product-info">
                            <h3 class="home-product-name"><?php echo htmlspecialchars($product['productName']); ?></h3>
                            <p class="home-product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="home-product-vendor">Vendor: <?php echo htmlspecialchars($product['vendorName']); ?></p>
                            <p class="home-product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <a href="detail.php?id=<?php echo $product['productID']; ?>" class="btn-view-detail">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-products">No products available</p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
