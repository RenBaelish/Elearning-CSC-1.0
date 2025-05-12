<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not mentor
requireMentor();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);

        showAlert('Tugas berhasil dihapus!');
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }
}

header("Location: ../dashboard-mentor.php?section=tugas");
exit;
?>
