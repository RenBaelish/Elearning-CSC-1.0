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
<!-- Rest of your HTML code remains the same -->
