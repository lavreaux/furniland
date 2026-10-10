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
    $vendorID = $_GET['delete'];
    
    $checkProducts = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE vendorID = ?");
    $checkProducts->bind_param("i", $vendorID);
    $checkProducts->execute();
    $result = $checkProducts->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $error = 'Cannot delete vendor. It is referenced in products.';
    } else {
        $deleteStmt = $conn->prepare("DELETE FROM vendors WHERE vendorID = ?");
        $deleteStmt->bind_param("i", $vendorID);
        
        if ($deleteStmt->execute()) {
            $success = 'Vendor deleted successfully';
        } else {
            $error = 'Failed to delete vendor';
        }
        $deleteStmt->close();
    }
    $checkProducts->close();
}

$query = "SELECT * FROM vendors ORDER BY vendorID";
$vendors = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendors - Furniland</title>
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
            <h1 class="manage-title">Manage Vendors</h1>
            <a href="add-vendor.php" class="btn-add">+ Add Vendor</a>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Vendor Name</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($vendors->num_rows > 0): ?>
                        <?php while ($vendor = $vendors->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vendor['vendorName']); ?></td>
                                <td><?php echo htmlspecialchars($vendor['location']); ?></td>
                                <td class="actions-cell">
                                    <a href="edit-vendor.php?id=<?php echo $vendor['vendorID']; ?>" class="btn-edit">Edit</a>
                                    <a href="manage-vendors.php?delete=<?php echo $vendor['vendorID']; ?>" class="btn-delete-small" onclick="return confirm('Are you sure you want to delete this vendor?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="no-data">No vendors found</td>
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
