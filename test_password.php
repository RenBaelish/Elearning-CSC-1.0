<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test password hashing and verification
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h1>Password Hashing Test</h1>";
echo "<p>Original password: <strong>$password</strong></p>";
echo "<p>Generated hash: <strong>$hash</strong></p>";

// Test verification
$verify = password_verify($password, $hash);
echo "<p>Verification result: <strong>" . ($verify ? 'SUCCESS' : 'FAILED') . "</strong></p>";

// Test with the pre-generated hash
$preHash = '$2y$10$8tPjdlv.K4A/zRmKrEd/9.YBNhCvFTOJx5tXtmKkY0QdVVwKA5pSa';
echo "<p>Pre-generated hash: <strong>$preHash</strong></p>";

$preVerify = password_verify($password, $preHash);
echo "<p>Pre-generated hash verification: <strong>" . ($preVerify ? 'SUCCESS' : 'FAILED') . "</strong></p>";

// Test with the test user hash
$testHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$testPassword = 'password';
echo "<p>Test user password: <strong>$testPassword</strong></p>";
echo "<p>Test user hash: <strong>$testHash</strong></p>";

$testVerify = password_verify($testPassword, $testHash);
echo "<p>Test user verification: <strong>" . ($testVerify ? 'SUCCESS' : 'FAILED') . "</strong></p>";

// Database connection test
echo "<h1>Database Connection Test</h1>";
try {
    $host = 'localhost';
    $dbname = 'computer_students_club';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>Database connection: <strong>SUCCESS</strong></p>";

    // Test query for anggota users
    $stmt = $conn->query("SELECT * FROM users WHERE role = 'anggota' LIMIT 3");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Sample Anggota Users</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Username</th><th>Name</th><th>Role</th><th>Divisi</th><th>Password Hash</th></tr>";

    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['name'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td>" . $user['divisi'] . "</td>";
        echo "<td>" . $user['password'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

} catch(PDOException $e) {
    echo "<p>Database connection: <strong>FAILED</strong></p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
