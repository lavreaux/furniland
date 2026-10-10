<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendorName = $_POST['vendor_name'];
    $location = $_POST['location'];
    
    if (empty($vendorName)) {
        $error = 'Vendor name is required';
    } elseif (strlen($vendorName) > 20) {
        $error = 'Vendor name must not exceed 20 characters';
    } elseif (empty($location)) {
        $error = 'Location is required';
    } elseif (strlen($location) > 100) {
        $error = 'Location must not exceed 100 characters';
    } else {
        $insertQuery = "INSERT INTO vendors (vendorName, location) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ss", $vendorName, $location);
        
        if ($stmt->execute()) {
            header('Location: manage-vendors.php');
            exit();
        } else {
            $error = 'Failed to add vendor';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vendor - Furniland</title>
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
            <h2 class="form-title">Add Vendor</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="add-vendor.php" class="admin-form">
                <div class="form-group">
                    <label for="vendor_name">Vendor Name</label>
                    <input type="text" id="vendor_name" name="vendor_name" class="form-input" value="<?php echo isset($_POST['vendor_name']) ? htmlspecialchars($_POST['vendor_name']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-input" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required>
                </div>
                <button type="submit" class="btn-submit">Add Vendor</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
