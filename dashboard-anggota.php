<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in or not anggota
requireLogin();
if (isMentor()) {
    header("Location: dashboard-mentor.php");
    exit;
}

// Get user data
$userId = $_SESSION['user_id'];
$name = $_SESSION['name'];
$divisi = $_SESSION['divisi'];

// Default section
$section = $_GET['section'] ?? 'materi';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota | Computer Students Club</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-laptop-code"></i>
                <h2>CSC</h2>
            </div>
            <div class="user-info">
                <div class="avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h3><?= htmlspecialchars($name) ?></h3>
                <p>Anggota</p>
            </div>
            <nav class="menu">
                <a href="?section=materi" class="menu-item <?= $section === 'materi' ? 'active' : '' ?>">
                    <i class="fas fa-book"></i> Materi
                </a>
                <a href="?section=absen" class="menu-item <?= $section === 'absen' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-check"></i> Absensi
                </a>
                <a href="?section=jadwal" class="menu-item <?= $section === 'jadwal' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Jadwal
                </a>
                <a href="?section=tugas" class="menu-item <?= $section === 'tugas' ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> Tugas
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="content">
            <header class="content-header">
                <h2>Dashboard Anggota</h2>
                <div class="user-controls">
                    <span><?= date('l, d F Y') ?></span>
                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <section class="content-body">
                <?php displayAlert(); ?>

                <?php if ($section === 'materi'): ?>
                <!-- Materi Section -->
                <div class="card">
                    <h3>Materi Divisi <?= formatDivisiName($divisi) ?></h3>
                    <p>Berikut adalah materi yang tersedia untuk divisi Anda.</p>
                </div>

                <?php
                    // Get materi for user's divisi
                    $stmt = $conn->prepare("SELECT * FROM materials WHERE divisi = ? ORDER BY created_at DESC");
                    $stmt->execute([$divisi]);
                    $materials = $stmt->fetchAll();

                    if (count($materials) > 0):
                        foreach ($materials as $material):
                    ?>
                <div class="card">
                    <h3><?= htmlspecialchars($material['judul']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($material['deskripsi'])) ?></p>
                    <div class="mt-3">
                        <small>Ditambahkan pada: <?= formatDate($material['created_at']) ?></small>
                        <?php if ($material['updated_at'] != $material['created_at']): ?>
                        <small> | Diperbarui pada: <?= formatDate($material['updated_at']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                        endforeach;
                    else:
                    ?>
                <div class="card">
                    <p>Belum ada materi di divisi ini.</p>
                </div>
                <?php endif; ?>

                <?php elseif ($section === 'absen'): ?>
                <!-- Absensi Section -->
                <div class="card">
                    <h3>Absensi Harian</h3>
                    <p>Silakan isi absensi untuk hari ini: <?= date('d F Y') ?></p>

                    <?php
                        // Check if user already filled attendance today
                        $today = date('Y-m-d');
                        $stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND tanggal = ?");
                        $stmt->execute([$userId, $today]);
                        $attendance = $stmt->fetch();

                        if ($attendance):
                        ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Anda sudah mengisi absensi hari ini dengan status:
                        <?= ucfirst($attendance['status']) ?>
                    </div>
                    <?php else: ?>
                    <form method="POST" action="actions/submit-attendance.php">
                        <div class="form-group">
                            <label for="keterangan">Keterangan:</label>
                            <select id="keterangan" name="keterangan" class="form-control" required>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>
                        <div class="form-group" id="alasanGroup" style="display: none;">
                            <label for="alasan">Alasan:</label>
                            <textarea id="alasan" name="alasan" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Absensi
                        </button>
                    </form>

                    <script>
                    // Show/hide alasan field based on keterangan
                    document.getElementById('keterangan').addEventListener('change', function() {
                        const alasanGroup = document.getElementById('alasanGroup');
                        if (this.value === 'izin' || this.value === 'sakit') {
                            alasanGroup.style.display = 'block';
                        } else {
                            alasanGroup.style.display = 'none';
                        }
                    });
                    </script>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <h3>Riwayat Absensi</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Get attendance history
                                    $stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? ORDER BY tanggal DESC");
                                    $stmt->execute([$userId]);
                                    $attendanceHistory = $stmt->fetchAll();

                                    if (count($attendanceHistory) > 0):
                                        foreach ($attendanceHistory as $record):
                                            $statusClass = '';
                                            switch ($record['status']) {
                                                case 'hadir':
                                                    $statusClass = 'badge-success';
                                                    break;
                                                case 'izin':
                                                    $statusClass = 'badge-warning';
                                                    break;
                                                case 'sakit':
                                                    $statusClass = 'badge-danger';
                                                    break;
                                            }
                                    ?>
                                <tr>
                                    <td><?= formatDate($record['tanggal']) ?></td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($record['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= $record['alasan'] ? htmlspecialchars($record['alasan']) : '-' ?></td>
                                </tr>
                                <?php
                                        endforeach;
                                    else:
                                    ?>
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data absensi</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($section === 'jadwal'): ?>
                <!-- Jadwal Section -->
                <div class="card">
                    <h3>Jadwal Ekstrakurikuler</h3>
                    <p>Berikut adalah jadwal kegiatan ekstrakurikuler Computer Students Club.</p>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Divisi</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Tempat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Get schedule
                                    $stmt = $conn->query("SELECT * FROM schedule ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')");
                                    $schedules = $stmt->fetchAll();

                                    foreach ($schedules as $schedule):
                                    ?>
                                <tr>
                                    <td><?= formatDivisiName($schedule['divisi']) ?></td>
                                    <td><?= $schedule['hari'] ?></td>
                                    <td><?= $schedule['waktu'] ?></td>
                                    <td><?= htmlspecialchars($schedule['tempat']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($section === 'tugas'): ?>
                <!-- Tugas Section -->
                <div class="card">
                    <h3>Daftar Tugas</h3>
                    <p>Berikut adalah tugas-tugas yang perlu Anda kerjakan.</p>
                </div>

                <?php
                    // Get tasks for user's divisi
                    $stmt = $conn->prepare("SELECT * FROM tasks WHERE divisi = ? ORDER BY deadline ASC");
                    $stmt->execute([$divisi]);
                    $tasks = $stmt->fetchAll();

                    if (count($tasks) > 0):
                        foreach ($tasks as $task):
                            // Check if user has submitted this task
                            $stmt = $conn->prepare("SELECT * FROM task_submissions WHERE task_id = ? AND user_id = ?");
                            $stmt->execute([$task['id'], $userId]);
                            $submission = $stmt->fetch();

                            $isExpired = isDeadlinePassed($task['deadline']);
                    ?>
                <div class="card <?= $isExpired ? 'expired-card' : '' ?>">
                    <h3><?= htmlspecialchars($task['judul']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($task['deskripsi'])) ?></p>
                    <div class="mt-3">
                        <small>Deadline: <?= formatDate($task['deadline']) ?></small>
                        <?php if ($isExpired): ?>
                        <span class="badge badge-danger">Expired</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($submission): ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> Tugas sudah dikumpulkan pada
                        <?= formatDate($submission['created_at']) ?>
                    </div>
                    <div class="mt-3">
                        <h4>Jawaban Anda:</h4>
                        <p><?= nl2br(htmlspecialchars($submission['jawaban'])) ?></p>
                    </div>
                    <?php elseif (!$isExpired): ?>
                    <form method="POST" action="actions/submit-task.php" class="mt-3">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <div class="form-group">
                            <label for="jawaban-<?= $task['id'] ?>">Jawaban:</label>
                            <textarea id="jawaban-<?= $task['id'] ?>" name="jawaban" class="form-control" rows="3"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Kumpulkan Tugas
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-circle"></i> Deadline telah berakhir, Anda tidak dapat mengumpulkan
                        tugas ini.
                    </div>
                    <?php endif; ?>
                </div>
                <?php
                        endforeach;
                    else:
                    ?>
                <div class="card">
                    <p>Belum ada tugas di divisi ini.</p>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
    // Toggle dark mode
    document.getElementById('themeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');

        const themeIcon = this.querySelector('i');
        if (document.body.classList.contains('dark-mode')) {
            themeIcon.className = 'fas fa-sun';
            localStorage.setItem('darkMode', 'enabled');
        } else {
            themeIcon.className = 'fas fa-moon';
            localStorage.setItem('darkMode', 'disabled');
        }
    });

    // Check for dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        document.querySelector('.theme-toggle i').className = 'fas fa-sun';
    }
    </script>
</body>

</html>
