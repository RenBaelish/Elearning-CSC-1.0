<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if already logged in
if (isLoggedIn()) {
    if (isMentor()) {
        header("Location: dashboard-mentor.php");
    } else {
        header("Location: dashboard-anggota.php");
    }
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($username) || empty($password) || empty($role)) {
        showAlert('Username, password, dan role harus diisi!', 'danger');
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $user['role'] === $role && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['divisi'] = $user['divisi'];

            // Redirect based on role
            if ($role === 'mentor') {
                header("Location: dashboard-mentor.php");
            } else {
                header("Location: dashboard-anggota.php");
            }
            exit;
        } else {
            showAlert('Username atau password salah!', 'danger');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Students Club</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <i class="fas fa-laptop-code"></i>
                <h1>Computer Students Club</h1>
                <p>Login to access your dashboard</p>
            </div>

            <?php displayAlert(); ?>

            <form id="loginForm" method="POST" action="">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-user-tag"></i>
                    <select id="role" name="role">
                        <option value="mentor">Mentor</option>
                        <option value="anggota">Anggota</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
    </div>
</body>

</html>
