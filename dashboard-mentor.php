<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in or not mentor
requireMentor();

// Get user data
$userId = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Default section
$section = $_GET['section'] ?? 'materi';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mentor | Computer Students Club</title>
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
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3><?= htmlspecialchars($name) ?></h3>
                <p>Mentor</p>
            </div>
            <nav class="menu">
                <a href="?section=materi" class="menu-item <?= $section === 'materi' ? 'active' : '' ?>">
                    <i class="fas fa-book"></i> Kelola Materi
                </a>
                <a href="?section=tugas" class="menu-item <?= $section === 'tugas' ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> Kelola Tugas
                </a>
                <a href="?section=jadwal" class="menu-item <?= $section === 'jadwal' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Jadwal Eskul
                </a>
                <a href="?section=anggota" class="menu-item <?= $section === 'anggota' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Kelola Anggota
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="content">
            <header class="content-header">
                <h2>Dashboard Mentor</h2>
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
                    <h3>Kelola Materi</h3>
                    <p>Tambahkan materi baru atau kelola materi yang sudah ada.</p>

                    <form method="POST" action="actions/save-materi.php" class="mt-3">
                        <div class="form-group">
                            <label for="divisi">Pilih Divisi:</label>
                            <select id="divisi" name="divisi" class="form-control" required>
                                <option value="">-- Pilih Divisi --</option>
                                <option value="cyber_security">Cyber Security</option>
                                <option value="software_dev">Software Development</option>
                                <option value="explore">Explore</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="judul">Judul Materi:</label>
                            <input type="text" id="judul" name="judul" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi Materi:</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="5" required></textarea>
                        </div>

                        <input type="hidden" name="id" id="materiId">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Materi
                        </button>
                        <a href="?section=materi" class="btn-secondary" id="cancelEditBtn" style="display: none;">
                            <i class="fas fa-times"></i> Batal Edit
                        </a>
                    </form>
                </div>

                <div class="card">
                    <h3>Daftar Materi</h3>

                    <div class="form-group">
                        <label for="filterDivisi">Filter Divisi:</label>
                        <select id="filterDivisi" name="filterDivisi" class="form-control"
                            onchange="window.location='?section=materi&divisi='+this.value">
                            <option value="all"
                                <?= (!isset($_GET['divisi']) || $_GET['divisi'] === 'all') ? 'selected' : '' ?>>Semua
                                Divisi</option>
                            <option value="cyber_security"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'cyber_security') ? 'selected' : '' ?>>
                                Cyber Security</option>
                            <option value="software_dev"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'software_dev') ? 'selected' : '' ?>>
                                Software Development</option>
                            <option value="explore"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'explore') ? 'selected' : '' ?>>
                                Explore</option>
                        </select>
                    </div>

                    <?php
                        // Get materi based on filter
                        $filterDivisi = $_GET['divisi'] ?? 'all';

                        if ($filterDivisi === 'all') {
                            $stmt = $conn->query("SELECT * FROM materials ORDER BY created_at DESC");
                        } else {
                            $stmt = $conn->prepare("SELECT * FROM materials WHERE divisi = ? ORDER BY created_at DESC");
                            $stmt->execute([$filterDivisi]);
                        }

                        $materials = $stmt->fetchAll();

                        if (count($materials) > 0):
                            foreach ($materials as $material):
                        ?>
                    <div class="card">
                        <div class="card-header">
                            <h4><?= htmlspecialchars($material['judul']) ?></h4>
                            <span class="badge badge-primary"><?= formatDivisiName($material['divisi']) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($material['deskripsi'])) ?></p>
                        <div class="mt-3">
                            <small>Ditambahkan pada: <?= formatDate($material['created_at']) ?></small>
                            <?php if ($material['updated_at'] != $material['created_at']): ?>
                            <small> | Diperbarui pada: <?= formatDate($material['updated_at']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="action-buttons mt-3">
                            <a href="?section=materi&edit=<?= $material['id'] ?>" class="btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="actions/delete-materi.php?id=<?= $material['id'] ?>" class="btn-danger btn-sm"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                    <?php
                            endforeach;
                        else:
                        ?>
                    <div class="card">
                        <p>Belum ada materi
                            <?= $filterDivisi !== 'all' ? 'di divisi ' . formatDivisiName($filterDivisi) : '' ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php
                    // Handle edit materi
                    if (isset($_GET['edit'])) {
                        $editId = $_GET['edit'];
                        $stmt = $conn->prepare("SELECT * FROM materials WHERE id = ?");
                        $stmt->execute([$editId]);
                        $editMateri = $stmt->fetch();

                        if ($editMateri) {
                            echo "<script>
                                document.getElementById('divisi').value = '{$editMateri['divisi']}';
                                document.getElementById('judul').value = '" . addslashes($editMateri['judul']) . "';
                                document.getElementById('deskripsi').value = '" . addslashes($editMateri['deskripsi']) . "';
                                document.getElementById('materiId').value = '{$editMateri['id']}';
                                document.querySelector('button[type=\"submit\"]').innerHTML = '<i class=\"fas fa-save\"></i> Update Materi';
                                document.getElementById('cancelEditBtn').style.display = 'inline-block';
                                document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
                            </script>";
                        }
                    }
                    ?>

                <?php elseif ($section === 'tugas'): ?>
                <!-- Tugas Section -->
                <div class="card">
                    <h3>Kelola Tugas</h3>
                    <p>Tambahkan tugas baru atau kelola tugas yang sudah ada.</p>

                    <form method="POST" action="actions/save-tugas.php" class="mt-3">
                        <div class="form-group">
                            <label for="divisiTugas">Pilih Divisi:</label>
                            <select id="divisiTugas" name="divisi" class="form-control" required>
                                <option value="">-- Pilih Divisi --</option>
                                <option value="cyber_security">Cyber Security</option>
                                <option value="software_dev">Software Development</option>
                                <option value="explore">Explore</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="judulTugas">Judul Tugas:</label>
                            <input type="text" id="judulTugas" name="judul" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="deskripsiTugas">Deskripsi Tugas:</label>
                            <textarea id="deskripsiTugas" name="deskripsi" class="form-control" rows="5"
                                required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="deadline">Deadline:</label>
                            <input type="date" id="deadline" name="deadline" class="form-control" required
                                min="<?= date('Y-m-d') ?>">
                        </div>

                        <input type="hidden" name="id" id="tugasId">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Tugas
                        </button>
                        <a href="?section=tugas" class="btn-secondary" id="cancelEditTugasBtn" style="display: none;">
                            <i class="fas fa-times"></i> Batal Edit
                        </a>
                    </form>
                </div>

                <div class="card">
                    <h3>Daftar Tugas</h3>

                    <div class="form-group">
                        <label for="filterDivisiTugas">Filter Divisi:</label>
                        <select id="filterDivisiTugas" name="filterDivisiTugas" class="form-control"
                            onchange="window.location='?section=tugas&divisi='+this.value">
                            <option value="all"
                                <?= (!isset($_GET['divisi']) || $_GET['divisi'] === 'all') ? 'selected' : '' ?>>Semua
                                Divisi</option>
                            <option value="cyber_security"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'cyber_security') ? 'selected' : '' ?>>
                                Cyber Security</option>
                            <option value="software_dev"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'software_dev') ? 'selected' : '' ?>>
                                Software Development</option>
                            <option value="explore"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'explore') ? 'selected' : '' ?>>
                                Explore</option>
                        </select>
                    </div>

                    <?php
                        // Get tugas based on filter
                        $filterDivisi = $_GET['divisi'] ?? 'all';

                        if ($filterDivisi === 'all') {
                            $stmt = $conn->query("SELECT * FROM tasks ORDER BY deadline ASC");
                        } else {
                            $stmt = $conn->prepare("SELECT * FROM tasks WHERE divisi = ? ORDER BY deadline ASC");
                            $stmt->execute([$filterDivisi]);
                        }

                        $tasks = $stmt->fetchAll();

                        if (count($tasks) > 0):
                            foreach ($tasks as $task):
                                $isExpired = isDeadlinePassed($task['deadline']);

                                // Count submissions
                                $stmt = $conn->prepare("SELECT COUNT(*) FROM task_submissions WHERE task_id = ?");
                                $stmt->execute([$task['id']]);
                                $submissionCount = $stmt->fetchColumn();
                        ?>
                    <div class="card <?= $isExpired ? 'expired-card' : '' ?>">
                        <div class="card-header">
                            <h4><?= htmlspecialchars($task['judul']) ?></h4>
                            <span class="badge badge-primary"><?= formatDivisiName($task['divisi']) ?></span>
                            <span
                                class="badge <?= $isExpired ? 'badge-danger' : 'badge-warning' ?>"><?= $isExpired ? 'Expired' : 'Active' ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($task['deskripsi'])) ?></p>
                        <div class="mt-3">
                            <small>Deadline: <?= formatDate($task['deadline']) ?></small>
                            <small> | Ditambahkan pada: <?= formatDate($task['created_at']) ?></small>
                            <?php if ($task['updated_at'] != $task['created_at']): ?>
                            <small> | Diperbarui pada: <?= formatDate($task['updated_at']) ?></small>
                            <?php endif; ?>
                        </div>

                        <div class="action-buttons mt-3">
                            <a href="?section=tugas&edit=<?= $task['id'] ?>" class="btn-secondary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="actions/delete-tugas.php?id=<?= $task['id'] ?>" class="btn-danger btn-sm"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                            <a href="view-submissions.php?task_id=<?= $task['id'] ?>" class="btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Lihat Pengumpulan (<?= $submissionCount ?>)
                            </a>
                        </div>
                    </div>
                    <?php
                            endforeach;
                        else:
                        ?>
                    <div class="card">
                        <p>Belum ada tugas
                            <?= $filterDivisi !== 'all' ? 'di divisi ' . formatDivisiName($filterDivisi) : '' ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php
                    // Handle edit tugas
                    if (isset($_GET['edit'])) {
                        $editId = $_GET['edit'];
                        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
                        $stmt->execute([$editId]);
                        $editTugas = $stmt->fetch();

                        if ($editTugas) {
                            echo "<script>
                                document.getElementById('divisiTugas').value = '{$editTugas['divisi']}';
                                document.getElementById('judulTugas').value = '" . addslashes($editTugas['judul']) . "';
                                document.getElementById('deskripsiTugas').value = '" . addslashes($editTugas['deskripsi']) . "';
                                document.getElementById('deadline').value = '{$editTugas['deadline']}';
                                document.getElementById('tugasId').value = '{$editTugas['id']}';
                                document.querySelector('button[type=\"submit\"]').innerHTML = '<i class=\"fas fa-save\"></i> Update Tugas';
                                document.getElementById('cancelEditTugasBtn').style.display = 'inline-block';
                                document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
                            </script>";
                        }
                    }
                    ?>

                <?php elseif ($section === 'jadwal'): ?>
                <!-- Jadwal Section -->
                <div class="card">
                    <h3>Kelola Jadwal Ekstrakurikuler</h3>
                    <p>Atur jadwal kegiatan ekstrakurikuler Computer Students Club.</p>

                    <form method="POST" action="actions/save-jadwal.php" class="mt-3">
                        <div class="form-group">
                            <label for="divisiJadwal">Pilih Divisi:</label>
                            <select id="divisiJadwal" name="divisi" class="form-control" required>
                                <option value="">-- Pilih Divisi --</option>
                                <option value="cyber_security">Cyber Security</option>
                                <option value="software_dev">Software Development</option>
                                <option value="explore">Explore</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="hari">Hari:</label>
                            <select id="hari" name="hari" class="form-control" required>
                                <option value="">-- Pilih Hari --</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="waktu">Waktu:</label>
                            <input type="text" id="waktu" name="waktu" class="form-control"
                                placeholder="contoh: 15:30 - 17:00" required>
                        </div>

                        <div class="form-group">
                            <label for="tempat">Tempat:</label>
                            <input type="text" id="tempat" name="tempat" class="form-control" required>
                        </div>

                        <input type="hidden" name="id" id="jadwalId">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Jadwal
                        </button>
                        <a href="?section=jadwal" class="btn-secondary" id="cancelEditJadwalBtn" style="display: none;">
                            <i class="fas fa-times"></i> Batal Edit
                        </a>
                    </form>
                </div>

                <div class="card">
                    <h3>Daftar Jadwal</h3>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Divisi</th>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Tempat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Get schedule
                                    $stmt = $conn->query("SELECT * FROM schedule ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')");
                                    $schedules = $stmt->fetchAll();

                                    if (count($schedules) > 0):
                                        foreach ($schedules as $schedule):
                                    ?>
                                <tr>
                                    <td><?= formatDivisiName($schedule['divisi']) ?></td>
                                    <td><?= $schedule['hari'] ?></td>
                                    <td><?= $schedule['waktu'] ?></td>
                                    <td><?= htmlspecialchars($schedule['tempat']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?section=jadwal&edit=<?= $schedule['id'] ?>"
                                                class="btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="actions/delete-jadwal.php?id=<?= $schedule['id'] ?>"
                                                class="btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                        endforeach;
                                    else:
                                    ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada jadwal</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php
                    // Handle edit jadwal
                    if (isset($_GET['edit'])) {
                        $editId = $_GET['edit'];
                        $stmt = $conn->prepare("SELECT * FROM schedule WHERE id = ?");
                        $stmt->execute([$editId]);
                        $editJadwal = $stmt->fetch();

                        if ($editJadwal) {
                            echo "<script>
                                document.getElementById('divisiJadwal').value = '{$editJadwal['divisi']}';
                                document.getElementById('hari').value = '{$editJadwal['hari']}';
                                document.getElementById('waktu').value = '{$editJadwal['waktu']}';
                                document.getElementById('tempat').value = '" . addslashes($editJadwal['tempat']) . "';
                                document.getElementById('jadwalId').value = '{$editJadwal['id']}';
                                document.querySelector('button[type=\"submit\"]').innerHTML = '<i class=\"fas fa-save\"></i> Update Jadwal';
                                document.getElementById('cancelEditJadwalBtn').style.display = 'inline-block';
                                document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
                            </script>";
                        }
                    }
                    ?>

                <?php elseif ($section === 'anggota'): ?>
                <!-- Anggota Section -->
                <div class="card">
                    <h3>Kelola Anggota</h3>
                    <p>Tambahkan anggota baru atau kelola anggota yang sudah ada.</p>

                    <form method="POST" action="actions/save-anggota.php" class="mt-3">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="name">Nama Lengkap:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control"
                                <?= isset($_GET['edit']) ? '' : 'required' ?>>
                            <?php if (isset($_GET['edit'])): ?>
                            <small class="form-text">Biarkan kosong jika tidak ingin mengubah password</small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="divisiAnggota">Divisi:</label>
                            <select id="divisiAnggota" name="divisi" class="form-control" required>
                                <option value="">-- Pilih Divisi --</option>
                                <option value="cyber_security">Cyber Security</option>
                                <option value="software_dev">Software Development</option>
                                <option value="explore">Explore</option>
                            </select>
                        </div>

                        <input type="hidden" name="id" id="anggotaId">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Anggota
                        </button>
                        <a href="?section=anggota" class="btn-secondary" id="cancelEditAnggotaBtn"
                            style="display: none;">
                            <i class="fas fa-times"></i> Batal Edit
                        </a>
                    </form>
                </div>

                <div class="card">
                    <h3>Daftar Anggota</h3>

                    <div class="form-group">
                        <label for="filterDivisiAnggota">Filter Divisi:</label>
                        <select id="filterDivisiAnggota" name="filterDivisiAnggota" class="form-control"
                            onchange="window.location='?section=anggota&divisi='+this.value">
                            <option value="all"
                                <?= (!isset($_GET['divisi']) || $_GET['divisi'] === 'all') ? 'selected' : '' ?>>Semua
                                Divisi</option>
                            <option value="cyber_security"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'cyber_security') ? 'selected' : '' ?>>
                                Cyber Security</option>
                            <option value="software_dev"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'software_dev') ? 'selected' : '' ?>>
                                Software Development</option>
                            <option value="explore"
                                <?= (isset($_GET['divisi']) && $_GET['divisi'] === 'explore') ? 'selected' : '' ?>>
                                Explore</option>
                        </select>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Divisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Get anggota based on filter
                                    $filterDivisi = $_GET['divisi'] ?? 'all';

                                    if ($filterDivisi === 'all') {
                                        $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'anggota' ORDER BY name ASC");
                                        $stmt->execute();
                                    } else {
                                        $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'anggota' AND divisi = ? ORDER BY name ASC");
                                        $stmt->execute([$filterDivisi]);
                                    }

                                    $anggota = $stmt->fetchAll();

                                    if (count($anggota) > 0):
                                        foreach ($anggota as $member):
                                    ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['username']) ?></td>
                                    <td><?= htmlspecialchars($member['name']) ?></td>
                                    <td><?= formatDivisiName($member['divisi']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?section=anggota&edit=<?= $member['id'] ?>"
                                                class="btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="actions/delete-anggota.php?id=<?= $member['id'] ?>"
                                                class="btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                        endforeach;
                                    else:
                                    ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada anggota
                                        <?= $filterDivisi !== 'all' ? 'di divisi ' . formatDivisiName($filterDivisi) : '' ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php
                    // Handle edit anggota
                    if (isset($_GET['edit'])) {
                        $editId = $_GET['edit'];
                        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'anggota'");
                        $stmt->execute([$editId]);
                        $editAnggota = $stmt->fetch();

                        if ($editAnggota) {
                            echo "<script>
                                document.getElementById('username').value = '" . addslashes($editAnggota['username']) . "';
                                document.getElementById('name').value = '" . addslashes($editAnggota['name']) . "';
                                document.getElementById('divisiAnggota').value = '{$editAnggota['divisi']}';
                                document.getElementById('anggotaId').value = '{$editAnggota['id']}';
                                document.querySelector('button[type=\"submit\"]').innerHTML = '<i class=\"fas fa-save\"></i> Update Anggota';
                                document.getElementById('cancelEditAnggotaBtn').style.display = 'inline-block';
                                document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
                            </script>";
                        }
                    }
                    ?>
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
