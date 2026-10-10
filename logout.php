<?php
session_start();

if (isset($_SESSION['userID'])) {
    session_destroy();
}

if (isset($_COOKIE['userID'])) {
    setcookie('userID', '', time() - 3600, '/');
}

header('Location: index.php');
exit();
?>
