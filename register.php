<?php
session_start();
require_once 'database/database.php';

if (isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'];
    
    if (empty($username)) {
        $error = 'Username is required';
    } elseif (strlen($username) < 4 || strlen($username) > 20) {
        $error = 'Username must be 4-20 characters';
    } elseif (!str_ends_with($email, '@gmail.com')) {
        $error = 'Email must end with @gmail.com';
    } elseif (empty($password)) {
        $error = 'Password is required';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (empty($gender)) {
        $error = 'Please select a gender';
    } elseif (empty($dob)) {
        $error = 'Date of birth is required';
    } elseif (strtotime($dob) >= time()) {
        $error = 'Date of birth must be in the past';
    } else {
        $checkUsername = $conn->prepare("SELECT userID FROM users WHERE username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $resultUsername = $checkUsername->get_result();
        
        if ($resultUsername->num_rows > 0) {
            $error = 'Username already exists';
        } else {
            $checkEmail = $conn->prepare("SELECT userID FROM users WHERE email = ?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $resultEmail = $checkEmail->get_result();
            
            if ($resultEmail->num_rows > 0) {
                $error = 'Email already exists';
            } else {
                $role = 'Member';
                $insertQuery = "INSERT INTO users (username, email, password, gender, dob, role) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("ssssss", $username, $email, $password, $gender, $dob, $role);
                
                if ($stmt->execute()) {
                    header('Location: login.php');
                    exit();
                } else {
                    $error = 'Registration failed. Please try again';
                }
                $stmt->close();
            }
            $checkEmail->close();
        }
        $checkUsername->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Furniland</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php" class="logo">Furniland</a>
                <a href="index.php" class="nav-link">Home</a>
            </div>
            <div class="nav-right">
                <a href="login.php" class="nav-link">Login</a>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Register</h2>
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="register.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Re-enter Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'checked' : ''; ?>>
                            <span>Male</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'checked' : ''; ?>>
                            <span>Female</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-input" placeholder="dd/mm/yyyy" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>" required>
                </div>
                <button type="submit" class="btn-login">Register</button>
                <div class="register-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Furniland. All rights reserved.</p>
        <p>Contact us at <a href="mailto:furniland.support@gmail.com">furniland.support@gmail.com</a></p>
    </footer>
</body>
</html>
