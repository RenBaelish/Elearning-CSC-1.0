<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection
$host = 'localhost';
$dbname = 'computer_students_club';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Debug info
    echo "<!-- Attempting login with: $username, role: $role -->";

    if (empty($username) || empty($password) || empty($role)) {
        $error = 'All fields are required';
    } else {
        // Get user from database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug info
        echo "<!-- User found: " . ($user ? 'Yes' : 'No') . " -->";
        if ($user) {
            echo "<!-- User role: " . $user['role'] . ", Requested role: $role -->";
            echo "<!-- Password hash in DB: " . $user['password'] . " -->";
        }

        // Check password with password_verify
        if ($user && $user['role'] === $role && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['divisi'] = $user['divisi'];

            $success = "Login successful! Redirecting...";

            // Redirect after 2 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = '" . ($role === 'mentor' ? 'dashboard-mentor.php' : 'dashboard-anggota.php') . "';
                }, 2000);
            </script>";
        } else {
            // Try direct password comparison (not secure, just for testing)
            if ($user) {
                echo "<!-- Direct password comparison: " . ($password === $user['password'] ? 'Match' : 'No match') . " -->";
            }

            $error = 'Invalid username, password, or role';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Login - Computer Students Club</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input,
    select,
    button {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }

    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    .error {
        color: red;
        margin-bottom: 15px;
    }

    .success {
        color: green;
        margin-bottom: 15px;
    }
    </style>
</head>

<body>
    <h1>Simple Login</h1>

    <?php if ($error): ?>
    <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="mentor">Mentor</option>
                <option value="anggota" selected>Anggota</option>
            </select>
        </div>

        <button type="submit">Login</button>
    </form>

    <hr>

    <h3>Test Accounts</h3>
    <p>All accounts use password: <strong>admin123</strong></p>

    <h4>Anggota Accounts:</h4>
    <ul>
        <li>anggota_cs1 (Cyber Security)</li>
        <li>anggota_sd1 (Software Dev)</li>
        <li>anggota_ex1 (Explore)</li>
    </ul>

    <h4>Mentor Accounts:</h4>
    <ul>
        <li>mentor_cs (Cyber Security)</li>
        <li>mentor_sd (Software Dev)</li>
        <li>mentor_ex (Explore)</li>
    </ul>
</body>

</html>
