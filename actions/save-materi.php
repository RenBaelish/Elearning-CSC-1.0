<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not mentor
requireMentor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $divisi = $_POST['divisi'] ?? '';
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    if (empty($divisi) || empty($judul) || empty($deskripsi)) {
        showAlert('Semua field harus diisi!', 'danger');
        header("Location: ../dashboard-mentor.php?section=materi");
        exit;
    }

    try {
        if (!empty($id)) {
            // Update existing materi
            $stmt = $conn->prepare("UPDATE materials SET divisi = ?, judul = ?, deskripsi = ? WHERE id = ?");
            $stmt->execute([$divisi, $judul, $deskripsi, $id]);
            showAlert('Materi berhasil diperbarui!');
        } else {
            // Add new materi
            $stmt = $conn->prepare("INSERT INTO materials (divisi, judul, deskripsi) VALUES (?, ?, ?)");
            $stmt->execute([$divisi, $judul, $deskripsi]);
            showAlert('Materi berhasil ditambahkan!');
        }
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }

    header("Location: ../dashboard-mentor.php?section=materi");
    exit;
} else {
    header("Location: ../dashboard-mentor.php");
    exit;
}
?>
