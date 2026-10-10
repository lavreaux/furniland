<?php
session_start();
require_once 'database/database.php';

$search = $_GET['search'] ?? '';
$vendorFilter = $_GET['vendor'] ?? '';
$sortBy = $_GET['sort'] ?? '';

$query = "SELECT p.*, v.vendorName FROM products p 
          INNER JOIN vendors v ON p.vendorID = v.vendorID 
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND p.productName LIKE ?";
}

if (!empty($vendorFilter)) {
    $query .= " AND p.vendorID = ?";
}

if ($sortBy == 'name_asc') {
    $query .= " ORDER BY p.productName ASC";
} elseif ($sortBy == 'name_desc') {
    $query .= " ORDER BY p.productName DESC";
} elseif ($sortBy == 'price_asc') {
    $query .= " ORDER BY p.price ASC";
} elseif ($sortBy == 'price_desc') {
    $query .= " ORDER BY p.price DESC";
}

$stmt = $conn->prepare($query);

if (!empty($search) && !empty($vendorFilter)) {
    $searchParam = "%$search%";
    $stmt->bind_param("si", $searchParam, $vendorFilter);
} elseif (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bind_param("s", $searchParam);
} elseif (!empty($vendorFilter)) {
    $stmt->bind_param("i", $vendorFilter);
}

$stmt->execute();
$products = $stmt->get_result();

$vendorsQuery = "SELECT * FROM vendors";
$vendorsResult = $conn->query($vendorsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog - Furniland</title>
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
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="catalog.php" class="nav-link active">Catalog</a>
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

    <div class="catalog-container">
        <h1 class="catalog-title">Product Catalog</h1>
        <p class="catalog-subtitle">Browse, filter, and sort furnitures</p>
        
        <div class="catalog-filters">
            <form method="GET" action="catalog.php" class="filters-form">
                <select name="vendor" class="filter-select">
                    <option value="">All Vendors</option>
                    <?php while ($vendor = $vendorsResult->fetch_assoc()): ?>
                        <option value="<?php echo $vendor['vendorID']; ?>" <?php echo $vendorFilter == $vendor['vendorID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($vendor['vendorName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="sort" class="filter-select">
                    <option value="">Sort By</option>
                    <option value="name_asc" <?php echo $sortBy == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    <option value="name_desc" <?php echo $sortBy == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                    <option value="price_asc" <?php echo $sortBy == 'price_asc' ? 'selected' : ''; ?>>Price (Low-High)</option>
                    <option value="price_desc" <?php echo $sortBy == 'price_desc' ? 'selected' : ''; ?>>Price (High-Low)</option>
                </select>
                
                <div class="search-box">
                    <input type="text" name="search" placeholder="Search products" class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-apply">Apply</button>
                </div>
            </form>
        </div>

        <div class="products-grid">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['productName']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-vendor">Vendor: <?php echo htmlspecialchars($product['vendorName']); ?></p>
                            <p class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <a href="detail.php?id=<?php echo $product['productID']; ?>" class="btn-view-detail">View Detail</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-products">No products found</p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
