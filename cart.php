<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Member') {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['userID'];
$error = '';
$success = '';

if (isset($_GET['remove'])) {
    $cartID = $_GET['remove'];
    
    $deleteStmt = $conn->prepare("DELETE FROM cart WHERE cartID = ? AND userID = ?");
    $deleteStmt->bind_param("ii", $cartID, $userID);
    
    if ($deleteStmt->execute()) {
        $success = 'Item removed from cart';
    } else {
        $error = 'Failed to remove item';
    }
    $deleteStmt->close();
}

if (isset($_POST['checkout'])) {
    $cartQuery = "SELECT c.*, p.price FROM cart c 
                  INNER JOIN products p ON c.productID = p.productID 
                  WHERE c.userID = ?";
    $cartStmt = $conn->prepare($cartQuery);
    $cartStmt->bind_param("i", $userID);
    $cartStmt->execute();
    $cartItems = $cartStmt->get_result();
    
    if ($cartItems->num_rows > 0) {
        $totalPrice = 0;
        $items = [];
        
        while ($item = $cartItems->fetch_assoc()) {
            $subtotal = $item['price'] * $item['quantity'];
            $totalPrice += $subtotal;
            $items[] = $item;
        }
        
        $transactionDate = date('Y-m-d');
        $insertTransaction = $conn->prepare("INSERT INTO transactions (userID, totalPrice, transactionDate) VALUES (?, ?, ?)");
        $insertTransaction->bind_param("ids", $userID, $totalPrice, $transactionDate);
        
        if ($insertTransaction->execute()) {
            $transactionID = $conn->insert_id;
            
            foreach ($items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $insertDetail = $conn->prepare("INSERT INTO transaction_details (transactionID, productID, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $insertDetail->bind_param("iiii", $transactionID, $item['productID'], $item['quantity'], $subtotal);
                $insertDetail->execute();
                $insertDetail->close();
            }
            
            $clearCart = $conn->prepare("DELETE FROM cart WHERE userID = ?");
            $clearCart->bind_param("i", $userID);
            $clearCart->execute();
            $clearCart->close();
            
            header('Location: history.php');
            exit();
        } else {
            $error = 'Checkout failed. Please try again';
        }
        $insertTransaction->close();
    } else {
        $error = 'Your cart is empty';
    }
    $cartStmt->close();
}

$query = "SELECT c.*, p.productName, p.price FROM cart c 
          INNER JOIN products p ON c.productID = p.productID 
          WHERE c.userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$cartItems = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Furniland</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo">Furniland</a>
                <a href="index.php" class="nav-link">Home</a>
                <a href="catalog.php" class="nav-link">Catalog</a>
            </div>
            <div class="nav-right">
                <a href="cart.php" class="nav-link active">Cart</a>
                <a href="history.php" class="nav-link">History</a>
                <a href="profile.php" class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <h1 class="cart-title">Your Cart</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($cartItems->num_rows > 0): ?>
            <div class="cart-table-wrapper">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = $cartItems->fetch_assoc()): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['productName']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                <td>
                                    <a href="cart.php?remove=<?php echo $item['cartID']; ?>" class="btn-remove" onclick="return confirm('Remove this item from cart?')">Remove</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <form method="POST" action="cart.php" class="checkout-form">
                <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
            </form>
        <?php else: ?>
            <p class="empty-cart">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
