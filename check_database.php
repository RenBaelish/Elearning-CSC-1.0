<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to connect to the database
try {
    require_once 'config/database.php';
    echo "<h2>Database Connection</h2>";
    echo "Connection successful!<br>";
    echo "PHP Version: " . phpversion() . "<br>";
    echo "PDO Driver: " . $conn->getAttribute(PDO::ATTR_DRIVER_NAME) . "<br>";

    // Check if tables exist
    echo "<h2>Tables</h2>";
    $tables = ['users', 'materials', 'tasks', 'task_submissions', 'attendance', 'schedule'];

    foreach ($tables as $table) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "Table '$table': EXISTS ($count records)<br>";
        } catch (PDOException $e) {
            echo "Table '$table': MISSING<br>";
        }
    }

    // Check users table structure
    echo "<h2>Users Table Structure</h2>";
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";

    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // List some users
    echo "<h2>Sample Users</h2>";
    $stmt = $conn->query("SELECT id, username, name, role, divisi FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Role</th><th>Divisi</th></tr>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['divisi'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No users found in the database.";
    }

} catch (PDOException $e) {
    echo "<h2>Database Error</h2>";
    echo "Connection failed: " . $e->getMessage();
}
?>
