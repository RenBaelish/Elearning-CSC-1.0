<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $keterangan = $_POST['keterangan'] ?? '';
    $alasan = $_POST['alasan'] ?? '';
    $tanggal = date('Y-m-d');

    if (empty($keterangan)) {
        showAlert('Keterangan harus diisi!', 'danger');
        header("Location: ../dashboard-anggota.php?section=absen");
        exit;
    }

    // If keterangan is izin or sakit, alasan is required
    if (($keterangan === 'izin' || $keterangan === 'sakit') && empty($alasan)) {
        showAlert('Alasan harus diisi!', 'danger');
        header("Location: ../dashboard-anggota.php?section=absen");
        exit;
    }

    try {
        // Check if already submitted today
        $stmt = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE user_id = ? AND tanggal = ?");
        $stmt->execute([$userId, $tanggal]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            showAlert('Anda sudah mengisi absensi hari ini!', 'warning');
        } else {
            // Submit attendance
            $stmt = $conn->prepare("INSERT INTO attendance (user_id, tanggal, status, alasan) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $tanggal, $keterangan, $alasan]);

            showAlert('Absensi berhasil disimpan!');
        }
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }

    header("Location: ../dashboard-anggota.php?section=absen");
    exit;
} else {
    header("Location: ../dashboard-anggota.php?section=absen");
    exit;
}
?>
