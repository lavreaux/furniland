<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

$query = "SELECT t.*, u.username FROM transactions t 
          INNER JOIN users u ON t.userID = u.userID 
          ORDER BY t.transactionDate DESC, t.transactionID DESC";
$transactions = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Transactions - Furniland</title>
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
            <h1 class="manage-title">All Transactions</h1>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User</th>
                        <th>Total Price</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transactions->num_rows > 0): ?>
                        <?php while ($transaction = $transactions->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($transaction['transactionID']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['username']); ?></td>
                                <td>Rp <?php echo number_format($transaction['totalPrice'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y', strtotime($transaction['transactionDate'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">No transactions found</td>
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
