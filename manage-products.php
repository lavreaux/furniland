<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if (isset($_GET['delete'])) {
    $productID = $_GET['delete'];
    
    $deleteStmt = $conn->prepare("DELETE FROM products WHERE productID = ?");
    $deleteStmt->bind_param("i", $productID);
    
    if ($deleteStmt->execute()) {
        $success = 'Product deleted successfully';
    } else {
        $error = 'Failed to delete product';
    }
    $deleteStmt->close();
}

$search = $_GET['search'] ?? '';
$vendorFilter = $_GET['vendor'] ?? '';

$query = "SELECT p.*, v.vendorName FROM products p 
          INNER JOIN vendors v ON p.vendorID = v.vendorID 
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND p.productName LIKE ?";
}

if (!empty($vendorFilter)) {
    $query .= " AND p.vendorID = ?";
}

$query .= " ORDER BY p.productID";

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
    <title>Manage Products - Furniland</title>
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
                <a href="profile.php" class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="manage-container">
        <div class="manage-header">
            <h1 class="manage-title">Manage Products</h1>
            <a href="add-product.php" class="btn-add">+ Add Product</a>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="product-filters">
            <form method="GET" action="manage-products.php" class="filter-form">
                <select name="vendor" class="filter-select">
                    <option value="">All Vendors</option>
                    <?php while ($vendor = $vendorsResult->fetch_assoc()): ?>
                        <option value="<?php echo $vendor['vendorID']; ?>" <?php echo $vendorFilter == $vendor['vendorID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($vendor['vendorName']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <input type="text" name="search" placeholder="Search products" class="search-input-small" value="<?php echo htmlspecialchars($search); ?>">
                
                <button type="submit" class="btn-apply-small">Apply</button>
            </form>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Vendor</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>" class="product-thumbnail">
                                </td>
                                <td><?php echo htmlspecialchars($product['productName']); ?></td>
                                <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($product['vendorName']); ?></td>
                                <td class="actions-cell">
                                    <a href="edit-product.php?id=<?php echo $product['productID']; ?>" class="btn-edit">Edit</a>
                                    <a href="manage-products.php?delete=<?php echo $product['productID']; ?>" class="btn-delete-small" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
