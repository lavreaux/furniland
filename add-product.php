<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $vendorID = $_POST['vendor'];
    $image = $_FILES['image'];
    
    if (empty($productName)) {
        $error = 'Product name is required';
    } elseif (strlen($productName) < 3 || strlen($productName) > 30) {
        $error = 'Product name must be 3-30 characters';
    } elseif (empty($description)) {
        $error = 'Description is required';
    } elseif (empty($price)) {
        $error = 'Price is required';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Price must be a number greater than 0';
    } elseif (empty($vendorID)) {
        $error = 'Please select a vendor';
    } elseif ($image['error'] == UPLOAD_ERR_NO_FILE) {
        $error = 'Product image is required';
    } else {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        
        if (!in_array($image['type'], $allowedTypes) || !in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
            $error = 'Only .jpg and .png images are allowed';
        } else {
            $imageName = time() . '_' . basename($image['name']);
            $targetPath = 'images/' . $imageName;
            
            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                $insertQuery = "INSERT INTO products (productName, description, price, image, vendorID) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssdsi", $productName, $description, $price, $imageName, $vendorID);
                
                if ($stmt->execute()) {
                    header('Location: manage-products.php');
                    exit();
                } else {
                    $error = 'Failed to add product';
                    unlink($targetPath);
                }
                $stmt->close();
            } else {
                $error = 'Failed to upload image';
            }
        }
    }
}

$vendorsQuery = "SELECT * FROM vendors";
$vendors = $conn->query($vendorsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Furniland</title>
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

    <div class="form-page-container">
        <div class="form-box">
            <h2 class="form-title">Add New Product</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="add-product.php" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" class="form-input" value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" class="form-input" step="0.01" min="0.01" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="vendor">Vendor</label>
                    <select id="vendor" name="vendor" class="form-input" required>
                        <option value="">Select Vendor</option>
                        <?php while ($vendor = $vendors->fetch_assoc()): ?>
                            <option value="<?php echo $vendor['vendorID']; ?>" <?php echo (isset($_POST['vendor']) && $_POST['vendor'] == $vendor['vendorID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($vendor['vendorName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" class="form-input-file" accept=".jpg,.jpeg,.png" required>
                </div>
                
                <button type="submit" class="btn-submit">Add Product</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
