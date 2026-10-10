<?php
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$userID = $_SESSION['userID'];
$error = '';
$success = '';

$query = "SELECT * FROM users WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        
        if (empty($username)) {
            $error = 'Username is required';
        } elseif (strlen($username) < 4 || strlen($username) > 20) {
            $error = 'Username must be 4-20 characters';
        } elseif (!str_ends_with($email, '@gmail.com')) {
            $error = 'Email must end with @gmail.com';
        } elseif (empty($gender)) {
            $error = 'Please select a gender';
        } elseif (empty($dob)) {
            $error = 'Date of birth is required';
        } elseif (strtotime($dob) >= time()) {
            $error = 'Date of birth must be in the past';
        } else {
            $checkUsername = $conn->prepare("SELECT userID FROM users WHERE username = ? AND userID != ?");
            $checkUsername->bind_param("si", $username, $userID);
            $checkUsername->execute();
            $resultUsername = $checkUsername->get_result();
            
            if ($resultUsername->num_rows > 0) {
                $error = 'Username already exists';
            } else {
                $checkEmail = $conn->prepare("SELECT userID FROM users WHERE email = ? AND userID != ?");
                $checkEmail->bind_param("si", $email, $userID);
                $checkEmail->execute();
                $resultEmail = $checkEmail->get_result();
                
                if ($resultEmail->num_rows > 0) {
                    $error = 'Email already exists';
                } else {
                    $updateQuery = "UPDATE users SET username = ?, email = ?, gender = ?, dob = ? WHERE userID = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("ssssi", $username, $email, $gender, $dob, $userID);
                    
                    if ($updateStmt->execute()) {
                        $_SESSION['username'] = $username;
                        $user['username'] = $username;
                        $user['email'] = $email;
                        $user['gender'] = $gender;
                        $user['dob'] = $dob;
                        $success = 'Profile updated successfully';
                    } else {
                        $error = 'Failed to update profile';
                    }
                    $updateStmt->close();
                }
                $checkEmail->close();
            }
            $checkUsername->close();
        }
    }
    
    if (isset($_POST['update_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (empty($currentPassword)) {
            $error = 'Current password is required';
        } elseif ($currentPassword !== $user['password']) {
            $error = 'Current password is incorrect';
        } elseif (empty($newPassword)) {
            $error = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Passwords do not match';
        } else {
            $updatePwdQuery = "UPDATE users SET password = ? WHERE userID = ?";
            $updatePwdStmt = $conn->prepare($updatePwdQuery);
            $updatePwdStmt->bind_param("si", $newPassword, $userID);
            
            if ($updatePwdStmt->execute()) {
                $user['password'] = $newPassword;
                $success = 'Password updated successfully';
            } else {
                $error = 'Failed to update password';
            }
            $updatePwdStmt->close();
        }
    }
    
    if (isset($_POST['delete_account'])) {
        $confirmPassword = $_POST['confirm_delete_password'];
        
        if (empty($confirmPassword)) {
            $error = 'Please confirm your password';
        } elseif ($confirmPassword !== $user['password']) {
            $error = 'Password is incorrect';
        } else {
            $deleteQuery = "DELETE FROM users WHERE userID = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $userID);
            
            if ($deleteStmt->execute()) {
                $deleteStmt->close();
                session_destroy();
                setcookie('userID', '', time() - 3600, '/');
                header('Location: index.php');
                exit();
            } else {
                $error = 'Failed to delete account';
            }
            $deleteStmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Furniland</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo">Furniland</a>
                <?php if ($_SESSION['role'] == 'Admin'): ?>
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                <?php else: ?>
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="catalog.php" class="nav-link">Catalog</a>
                <?php endif; ?>
            </div>
            <div class="nav-right">
                <?php if ($_SESSION['role'] == 'Admin'): ?>
                    <a href="profile.php" class="nav-link active">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <?php else: ?>
                    <a href="cart.php" class="nav-link">Cart</a>
                    <a href="history.php" class="nav-link">History</a>
                    <a href="profile.php" class="nav-link active">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <?php endif; ?>
                <a href="logout.php" class="nav-link logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <h1 class="profile-title">Your Profile</h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="profile-section">
            <h2 class="section-title">Update Profile Information</h2>
            <form method="POST" action="profile.php" class="profile-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-input" required>
                        <option value="Male" <?php echo $user['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $user['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-input" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
            </form>
        </div>

        <div class="profile-section">
            <h2 class="section-title">Update Password</h2>
            <form method="POST" action="profile.php" class="profile-form">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                </div>
                <button type="submit" name="update_password" class="btn-save">Update Password</button>
            </form>
        </div>

        <div class="profile-section delete-section">
            <h2 class="section-title-danger">Delete Account</h2>
            <p class="warning-text">Warning: Once you delete your account, it cannot be recovered.</p>
            <form method="POST" action="profile.php" class="profile-form">
                <div class="form-group">
                    <label for="confirm_delete_password">Confirm Password</label>
                    <input type="password" id="confirm_delete_password" name="confirm_delete_password" class="form-input" required>
                </div>
                <button type="submit" name="delete_account" class="btn-delete" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">Delete Account</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
