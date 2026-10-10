<?php
session_start();
require_once 'database/database.php';

$productID = $_GET['id'] ?? 0;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['userID'])) {
        header('Location: login.php');
        exit();
    }
    
    $quantity = $_POST['quantity'] ?? 1;
    $userID = $_SESSION['userID'];
    
    if ($quantity < 1) {
        $error = 'Quantity must be at least 1';
    } else {
        $checkCart = $conn->prepare("SELECT cartID, quantity FROM cart WHERE userID = ? AND productID = ?");
        $checkCart->bind_param("ii", $userID, $productID);
        $checkCart->execute();
        $cartResult = $checkCart->get_result();
        
        if ($cartResult->num_rows > 0) {
            $cartItem = $cartResult->fetch_assoc();
            $newQuantity = $cartItem['quantity'] + $quantity;
            $updateCart = $conn->prepare("UPDATE cart SET quantity = ? WHERE cartID = ?");
            $updateCart->bind_param("ii", $newQuantity, $cartItem['cartID']);
            $updateCart->execute();
            $updateCart->close();
        } else {
            $insertCart = $conn->prepare("INSERT INTO cart (userID, productID, quantity) VALUES (?, ?, ?)");
            $insertCart->bind_param("iii", $userID, $productID, $quantity);
            $insertCart->execute();
            $insertCart->close();
        }
        
        $success = 'Product added to cart successfully';
        $checkCart->close();
    }
}

$query = "SELECT p.*, v.vendorName, v.location FROM products p 
          INNER JOIN vendors v ON p.vendorID = v.vendorID 
          WHERE p.productID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: catalog.php');
    exit();
}

$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['productName']); ?> - Furniland</title>
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
                    <a href="catalog.php" class="nav-link">Catalog</a>
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

    <div class="detail-container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="detail-content">
            <div class="detail-image">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
            </div>
            
            <div class="detail-info">
                <h1 class="detail-title"><?php echo htmlspecialchars($product['productName']); ?></h1>
                <p class="detail-description"><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="detail-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                
                <div class="vendor-info">
                    <p><strong>Vendor:</strong> <?php echo htmlspecialchars($product['vendorName']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($product['location']); ?></p>
                </div>
                
                <form method="POST" action="detail.php?id=<?php echo $productID; ?>" class="cart-form">
                    <div class="quantity-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" class="quantity-input">
                    </div>
                    <button type="submit" class="btn-add-cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
