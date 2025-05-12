<?php
// Connect to database
require_once 'config/database.php';
require_once 'includes/functions.php';

// Clear existing users (except admin)
$conn->query("DELETE FROM users WHERE username != 'admin'");

// Function to insert a user with properly hashed password
function insertUser($conn, $username, $password, $name, $role, $divisi) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, name, role, divisi) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $name, $role, $divisi]);
    return $conn->lastInsertId();
}

// Common password for all users
$password = 'admin123';

// Insert mentors
insertUser($conn, 'mentor_cs', $password, 'Budi Santoso', 'mentor', 'cyber_security');
insertUser($conn, 'mentor_sd', $password, 'Dewi Lestari', 'mentor', 'software_dev');
insertUser($conn, 'mentor_ex', $password, 'Agus Purnomo', 'mentor', 'explore');

// Insert Cyber Security members
insertUser($conn, 'anggota_cs1', $password, 'Ahmad Rizki', 'anggota', 'cyber_security');
insertUser($conn, 'anggota_cs2', $password, 'Siti Nurhaliza', 'anggota', 'cyber_security');
insertUser($conn, 'anggota_cs3', $password, 'Rudi Hermawan', 'anggota', 'cyber_security');
insertUser($conn, 'anggota_cs4', $password, 'Dina Maulida', 'anggota', 'cyber_security');
insertUser($conn, 'anggota_cs5', $password, 'Farhan Syahputra', 'anggota', 'cyber_security');

// Insert Software Development members
insertUser($conn, 'anggota_sd1', $password, 'Rina Wati', 'anggota', 'software_dev');
insertUser($conn, 'anggota_sd2', $password, 'Bima Sakti', 'anggota', 'software_dev');
insertUser($conn, 'anggota_sd3', $password, 'Anisa Rahma', 'anggota', 'software_dev');
insertUser($conn, 'anggota_sd4', $password, 'Dodi Sudrajat', 'anggota', 'software_dev');
insertUser($conn, 'anggota_sd5', $password, 'Putri Anggraini', 'anggota', 'software_dev');

// Insert Explore members
insertUser($conn, 'anggota_ex1', $password, 'Eko Prasetyo', 'anggota', 'explore');
insertUser($conn, 'anggota_ex2', $password, 'Maya Sari', 'anggota', 'explore');
insertUser($conn, 'anggota_ex3', $password, 'Joko Widodo', 'anggota', 'explore');
insertUser($conn, 'anggota_ex4', $password, 'Lina Marlina', 'anggota', 'explore');
insertUser($conn, 'anggota_ex5', $password, 'Tono Sucipto', 'anggota', 'explore');

// Also insert sample materials
$conn->query("DELETE FROM materials");

// Insert materials for each division
$stmt = $conn->prepare("INSERT INTO materials (divisi, judul, deskripsi) VALUES (?, ?, ?)");

// Cyber Security materials
$stmt->execute(['cyber_security', 'Pengenalan Cyber Security', 'Materi ini membahas dasar-dasar keamanan siber, termasuk konsep dasar, ancaman umum, dan praktik terbaik untuk keamanan online.']);
$stmt->execute(['cyber_security', 'Ethical Hacking Basics', 'Pengenalan tentang ethical hacking, tools yang digunakan, dan metodologi pengujian keamanan sistem.']);
$stmt->execute(['cyber_security', 'Network Security', 'Materi tentang keamanan jaringan, firewall, IDS/IPS, dan praktik terbaik untuk mengamankan infrastruktur jaringan.']);

// Software Development materials
$stmt->execute(['software_dev', 'Dasar Pemrograman Web', 'Pengenalan tentang HTML, CSS, dan JavaScript untuk membangun website interaktif.']);
$stmt->execute(['software_dev', 'Pengembangan Aplikasi Mobile', 'Materi tentang pengembangan aplikasi mobile menggunakan framework Flutter dan React Native.']);
$stmt->execute(['software_dev', 'Database Management', 'Pengenalan tentang database SQL dan NoSQL, serta cara mengelola dan mengoptimalkan database.']);

// Explore materials
$stmt->execute(['explore', 'Pengenalan AI dan Machine Learning', 'Materi dasar tentang kecerdasan buatan dan machine learning, termasuk konsep, algoritma, dan aplikasinya.']);
$stmt->execute(['explore', 'Internet of Things (IoT)', 'Pengenalan tentang IoT, perangkat yang digunakan, dan cara membangun proyek IoT sederhana.']);
$stmt->execute(['explore', 'Cloud Computing', 'Materi tentang komputasi awan, layanan cloud populer, dan cara memanfaatkannya untuk proyek teknologi.']);

// Insert tasks
$conn->query("DELETE FROM task_submissions");
$conn->query("DELETE FROM tasks");

// Insert tasks for each division
$stmt = $conn->prepare("INSERT INTO tasks (divisi, judul, deskripsi, deadline) VALUES (?, ?, ?, ?)");

// Cyber Security tasks
$stmt->execute(['cyber_security', 'Analisis Keamanan Website', 'Lakukan analisis keamanan pada sebuah website dan identifikasi potensi kerentanan. Buatlah laporan yang mencakup temuan dan rekomendasi perbaikan.', date('Y-m-d', strtotime('+7 days'))]);
$stmt->execute(['cyber_security', 'Implementasi Firewall', 'Konfigurasikan firewall pada sistem operasi Linux dan dokumentasikan langkah-langkah serta aturan yang diterapkan.', date('Y-m-d', strtotime('+14 days'))]);

// Software Development tasks
$stmt->execute(['software_dev', 'Membuat Aplikasi To-Do List', 'Buatlah aplikasi to-do list sederhana menggunakan HTML, CSS, dan JavaScript. Aplikasi harus dapat menambah, menghapus, dan menandai tugas sebagai selesai.', date('Y-m-d', strtotime('+10 days'))]);
$stmt->execute(['software_dev', 'Membuat REST API', 'Buatlah REST API sederhana menggunakan PHP dan MySQL untuk mengelola data mahasiswa (CRUD).', date('Y-m-d', strtotime('+21 days'))]);

// Explore tasks
$stmt->execute(['explore', 'Proyek IoT Sederhana', 'Buatlah proyek IoT sederhana menggunakan Arduino atau ESP8266. Dokumentasikan proses pembuatan dan hasilnya.', date('Y-m-d', strtotime('+14 days'))]);
$stmt->execute(['explore', 'Implementasi Model Machine Learning', 'Implementasikan model machine learning sederhana untuk klasifikasi atau regresi menggunakan dataset pilihan Anda.', date('Y-m-d', strtotime('+21 days'))]);

echo "Users, materials, and tasks inserted successfully!";
?>
