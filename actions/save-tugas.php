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
    $deadline = $_POST['deadline'] ?? '';

    if (empty($divisi) || empty($judul) || empty($deskripsi) || empty($deadline)) {
        showAlert('Semua field harus diisi!', 'danger');
        header("Location: ../dashboard-mentor.php?section=tugas");
        exit;
    }

    try {
        if (!empty($id)) {
            // Update existing tugas
            $stmt = $conn->prepare("UPDATE tasks SET divisi = ?, judul = ?, deskripsi = ?, deadline = ? WHERE id = ?");
            $stmt->execute([$divisi, $judul, $deskripsi, $deadline, $id]);
            showAlert('Tugas berhasil diperbarui!');
        } else {
            // Add new tugas
            $stmt = $conn->prepare("INSERT INTO tasks (divisi, judul, deskripsi, deadline) VALUES (?, ?, ?, ?)");
            $stmt->execute([$divisi, $judul, $deskripsi, $deadline]);
            showAlert('Tugas berhasil ditambahkan!');
        }
    } catch (PDOException $e) {
        showAlert('Error: ' . $e->getMessage(), 'danger');
    }

    header("Location: ../dashboard-mentor.php?section=tugas");
    exit;
} else {
    header("Location: ../dashboard-mentor.php?section=tugas");
    exit;
}
?>
