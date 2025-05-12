<?php
// WARNING: This is for testing only! Remove this file after testing!
require_once 'includes/functions.php';
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_GET['username'] ?? '';
$role = $_GET['role'] ?? '';

if (empty($username) || empty($role)) {
    die("Please provide username and role parameters");
}

// Find the user
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
$stmt->execute([$username, $role]);
$user = $stmt->fetch();

if ($user) {
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['divisi'] = $user['divisi'];

    echo "Login successful!<br>";
    echo "Name: " . $user['name'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Divisi: " . formatDivisiName($user['divisi']) . "<br><br>";

    // Provide links to dashboards
    echo "<a href='dashboard-mentor.php'>Go to Mentor Dashboard</a><br>";
    echo "<a href='dashboard-anggota.php'>Go to Anggota Dashboard</a>";
} else {
    echo "User not found!";

    // Debug information
    echo "<br><br>Debug info:<br>";
    echo "Searching for username: " . htmlspecialchars($username) . "<br>";
    echo "Role: " . htmlspecialchars($role) . "<br><br>";

    // List available users
    $stmt = $conn->query("SELECT username, role FROM users LIMIT 10");
    $users = $stmt->fetchAll();

    echo "Available users in database:<br>";
    echo "<ul>";
    foreach ($users as $u) {
        echo "<li>" . htmlspecialchars($u['username']) . " (" . htmlspecialchars($u['role']) . ")</li>";
    }
    echo "</ul>";
}
?>
