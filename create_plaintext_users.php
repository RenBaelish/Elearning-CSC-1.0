<?php
// WARNING: This is for testing only! Not secure for production!
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';
$dbname = 'computer_students_club';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Clear existing users with specific usernames
    $conn->query("DELETE FROM users WHERE username LIKE 'plain_%'");

    // Create users with plain text passwords
    $stmt = $conn->prepare("INSERT INTO users (username, password, name, role, divisi) VALUES (?, ?, ?, ?, ?)");

    // Insert mentors
    $stmt->execute(['plain_mentor_cs', 'admin123', 'Plain Mentor CS', 'mentor', 'cyber_security']);
    $stmt->execute(['plain_mentor_sd', 'admin123', 'Plain Mentor SD', 'mentor', 'software_dev']);

    // Insert anggota
    $stmt->execute(['plain_anggota_cs', 'admin123', 'Plain Anggota CS', 'anggota', 'cyber_security']);
    $stmt->execute(['plain_anggota_sd', 'admin123', 'Plain Anggota SD', 'anggota', 'software_dev']);
    $stmt->execute(['plain_anggota_ex', 'admin123', 'Plain Anggota EX', 'anggota', 'explore']);

    echo "<h1>Plain Text Users Created</h1>";
    echo "<p>These users have plain text passwords (not hashed):</p>";
    echo "<ul>";
    echo "<li><strong>plain_mentor_cs</strong> - Password: admin123 - Role: mentor</li>";
    echo "<li><strong>plain_mentor_sd</strong> - Password: admin123 - Role: mentor</li>";
    echo "<li><strong>plain_anggota_cs</strong> - Password: admin123 - Role: anggota</li>";
    echo "<li><strong>plain_anggota_sd</strong> - Password: admin123 - Role: anggota</li>";
    echo "<li><strong>plain_anggota_ex</strong> - Password: admin123 - Role: anggota</li>";
    echo "</ul>";

    echo "<p><strong>WARNING:</strong> These users have plain text passwords and are NOT secure. Use only for testing!</p>";

    // Modify index.php login code
    echo "<h2>Login Code Modification</h2>";
    echo "<p>You need to modify your login verification in index.php to handle both hashed and plain text passwords:</p>";

    echo "<pre>";
    echo htmlspecialchars('
// Inside your login verification code in index.php:
if ($user && $user["role"] === $role) {
    // Try password_verify first (for hashed passwords)
    $passwordCorrect = password_verify($password, $user["password"]);

    // If that fails, try direct comparison (for plain text passwords)
    if (!$passwordCorrect) {
        $passwordCorrect = ($password === $user["password"]);
    }

    if ($passwordCorrect) {
        // Login successful
        $_SESSION["user_id"] = $user["id"];
        // ... rest of your session code ...
    }
}
');
    echo "</pre>";

} catch(PDOException $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
