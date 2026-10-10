<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Member') {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['userID'];

$query = "SELECT t.* FROM transactions t 
          WHERE t.userID = ? 
          ORDER BY t.transactionDate DESC, t.transactionID DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$transactions = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Furniland</title>
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
                <a href="cart.php" class="nav-link">Cart</a>
                <a href="history.php" class="nav-link active">History</a>
                <a href="profile.php" class="nav-link">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="history-container">
        <h1 class="history-title">Transaction History</h1>
        
        <?php if ($transactions->num_rows > 0): ?>
            <div class="transactions-list">
                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                    <div class="transaction-card">
                        <div class="transaction-header">
                            <div class="transaction-info">
                                <h3 class="transaction-id">Transaction ID: <?php echo $transaction['transactionID']; ?></h3>
                                <p class="transaction-date">Date: <?php echo date('d M Y', strtotime($transaction['transactionDate'])); ?></p>
                            </div>
                            <div class="transaction-total">
                                <span class="total-label">Total:</span>
                                <span class="total-amount">Rp <?php echo number_format($transaction['totalPrice'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        
                        <div class="transaction-items">
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $detailsQuery = "SELECT td.*, p.productName FROM transaction_details td 
                                                     INNER JOIN products p ON td.productID = p.productID 
                                                     WHERE td.transactionID = ?";
                                    $detailsStmt = $conn->prepare($detailsQuery);
                                    $detailsStmt->bind_param("i", $transaction['transactionID']);
                                    $detailsStmt->execute();
                                    $details = $detailsStmt->get_result();
                                    
                                    while ($detail = $details->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detail['productName']); ?></td>
                                            <td><?php echo $detail['quantity']; ?></td>
                                            <td>Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php 
                                    endwhile;
                                    $detailsStmt->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="empty-history">You haven't made any transactions yet.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
