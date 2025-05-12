<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $taskId = $_POST['task_id'] ?? '';
    $jawaban = $_POST['jawaban'] ?? '';

    if (empty($taskId) || empty($jawaban)) {
        showAlert('Jawaban harus diisi!', 'danger');
        header("Location: ../dashboard-anggota.php?section=tugas");
        exit;
    }

    try {
        // Check if task exists and deadline not passed
        $stmt = $conn->prepare("SELECT deadline FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch();

        if (!$task) {
            showAlert('Tugas tidak ditemukan!', 'danger');
            header("Location: ../dashboard-anggota.php?section=tugas");
            exit;
        }

        if (isDeadlinePassed($task['deadline'])) {
            showAlert('Deadline tugas sudah berakhir!', 'danger');
            header("Location: ../dashboard-anggota.php?section=tugas");
            exit;
        }

        // Check if already submitted
        $stmt = $conn->prepare("SELECT COUNT(*) FROM task_submissions WHERE task_id = ? AND user_id = ?");
        $stmt->execute([$taskId, $userId]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            showAlert('Anda sudah mengumpulkan tugas ini!', 'warning');
        } else {
            // Submit task
            $stmt = $conn->prepare("INSERT INTO task_submissions (task_id, user_id, jawaban) VALUES (?, ?, ?)");
            $stmt->execute([$taskId, $userId, $jawaban]);

            showAlert('Tugas berhasil dikumpulkan!');
        }
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }

    header("Location: ../dashboard-anggota.php?section=tugas");
    exit;
} else {
    header("Location: ../dashboard-anggota.php?section=tugas");
    exit;
}
?>
