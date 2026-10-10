<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

$productID = $_GET['id'] ?? 0;
$error = '';

$query = "SELECT * FROM products WHERE productID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: manage-products.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

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
    } else {
        $imageName = $product['image'];
        
        if ($image['error'] != UPLOAD_ERR_NO_FILE) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $fileExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
            
            if (!in_array($image['type'], $allowedTypes) || !in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                $error = 'Only .jpg and .png images are allowed';
            } else {
                $newImageName = time() . '_' . basename($image['name']);
                $targetPath = 'images/' . $newImageName;
                
                if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                    if (file_exists('images/' . $product['image'])) {
                        unlink('images/' . $product['image']);
                    }
                    $imageName = $newImageName;
                } else {
                    $error = 'Failed to upload image';
                }
            }
        }
        
        if (empty($error)) {
            $updateQuery = "UPDATE products SET productName = ?, description = ?, price = ?, image = ?, vendorID = ? WHERE productID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("ssdsii", $productName, $description, $price, $imageName, $vendorID, $productID);
            
            if ($updateStmt->execute()) {
                header('Location: manage-products.php');
                exit();
            } else {
                $error = 'Failed to update product';
            }
            $updateStmt->close();
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
    <title>Edit Product - Furniland</title>
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
            <h2 class="form-title">Edit Product</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="edit-product.php?id=<?php echo $productID; ?>" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" class="form-input" value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : htmlspecialchars($product['productName']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" class="form-input" step="0.01" min="0.01" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : htmlspecialchars($product['price']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="vendor">Vendor</label>
                    <select id="vendor" name="vendor" class="form-input" required>
                        <?php while ($vendor = $vendors->fetch_assoc()): ?>
                            <option value="<?php echo $vendor['vendorID']; ?>" <?php echo (isset($_POST['vendor']) ? $_POST['vendor'] == $vendor['vendorID'] : $product['vendorID'] == $vendor['vendorID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($vendor['vendorName']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image (leave blank to keep current)</label>
                    <input type="file" id="image" name="image" class="form-input-file" accept=".jpg,.jpeg,.png">
                    <p class="image-note">Current image: <?php echo htmlspecialchars($product['image']); ?></p>
                </div>
                
                <button type="submit" class="btn-submit">Update Product</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
