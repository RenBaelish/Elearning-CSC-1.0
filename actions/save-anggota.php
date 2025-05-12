<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not mentor
requireMentor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'] ?? '';
    $name = $_POST['name'] ?? '';
    $password = $_POST['password'] ?? '';
    $divisi = $_POST['divisi'] ?? '';

    if (empty($username) || empty($name) || empty($divisi) || (empty($id) && empty($password))) {
        showAlert('Semua field harus diisi!', 'danger');
        header("Location: ../dashboard-mentor.php?section=anggota");
        exit;
    }

    try {
        // Check if username already exists (for new users or when changing username)
        if (empty($id) || $username != getUsernameById($conn, $id)) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                showAlert('Username sudah digunakan!', 'danger');
                header("Location: ../dashboard-mentor.php?section=anggota");
                exit;
            }
        }

        if (!empty($id)) {
            // Update existing anggota
            if (!empty($password)) {
                // Update with new password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, password = ?, divisi = ? WHERE id = ?");
                $stmt->execute([$username, $name, $hashedPassword, $divisi, $id]);
            } else {
                // Update without changing password
                $stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, divisi = ? WHERE id = ?");
                $stmt->execute([$username, $name, $divisi, $id]);
            }
            showAlert('Anggota berhasil diperbarui!');
        } else {
            // Add new anggota
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, name, role, divisi) VALUES (?, ?, ?, 'anggota', ?)");
            $stmt->execute([$username, $hashedPassword, $name, $divisi]);
            showAlert('Anggota berhasil ditambahkan!');
        }
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }

    header("Location: ../dashboard-mentor.php?section=anggota");
    exit;
} else {
    header("Location: ../dashboard-mentor.php?section=anggota");
    exit;
}

// Helper function to get username by id
function getUsernameById($conn, $id) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
}
?>
