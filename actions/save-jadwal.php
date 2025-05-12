<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not mentor
requireMentor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $divisi = $_POST['divisi'] ?? '';
    $hari = $_POST['hari'] ?? '';
    $waktu = $_POST['waktu'] ?? '';
    $tempat = $_POST['tempat'] ?? '';

    if (empty($divisi) || empty($hari) || empty($waktu) || empty($tempat)) {
        showAlert('Semua field harus diisi!', 'danger');
        header("Location: ../dashboard-mentor.php?section=jadwal");
        exit;
    }

    try {
        if (!empty($id)) {
            // Update existing jadwal
            $stmt = $conn->prepare("UPDATE schedule SET divisi = ?, hari = ?, waktu = ?, tempat = ? WHERE id = ?");
            $stmt->execute([$divisi, $hari, $waktu, $tempat, $id]);
            showAlert('Jadwal berhasil diperbarui!');
        } else {
            // Add new jadwal
            $stmt = $conn->prepare("INSERT INTO schedule (divisi, hari, waktu, tempat) VALUES (?, ?, ?, ?)");
            $stmt->execute([$divisi, $hari, $waktu, $tempat]);
            showAlert('Jadwal berhasil ditambahkan!');
        }
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }

    header("Location: ../dashboard-mentor.php?section=jadwal");
    exit;
} else {
    header("Location: ../dashboard-mentor.php?section=jadwal");
    exit;
}
?>
