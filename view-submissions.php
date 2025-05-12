<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not mentor
requireMentor();

$taskId = $_GET['task_id'] ?? 0;

// Get task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$taskId]);
$task = $stmt->fetch();

if (!$task) {
    showAlert('Tugas tidak ditemukan!', 'danger');
    header("Location: dashboard-mentor.php?section=tugas");
    exit;
}

// Get submissions
$stmt = $conn->prepare("
    SELECT s.*, u.name, u.username
    FROM task_submissions s
    JOIN users u ON s.user_id = u.id
    WHERE s.task_id = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$taskId]);
$submissions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumpulan Tugas | Computer Students Club</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="card" style="max-width: 800px; margin: 2rem auto;">
            <div class="card-header">
                <h3>Pengumpulan Tugas: <?= htmlspecialchars($task['judul']) ?></h3>
                <p>Divisi: <?= formatDivisiName($task['divisi']) ?> | Deadline: <?= formatDate($task['deadline']) ?></p>
            </div>

            <div class="mt-3">
                <a href="dashboard-mentor.php?section=tugas" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (count($submissions) > 0): ?>
            <div class="table-container mt-3">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jawaban</th>
                            <th>Waktu Pengumpulan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?= htmlspecialchars($submission['name']) ?></td>
                            <td><?= nl2br(htmlspecialchars($submission['jawaban'])) ?></td>
                            <td><?= formatDate($submission['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> Belum ada pengumpulan untuk tugas ini.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Check for dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
    </script>
</body>

</html>
