<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is mentor
function isMentor() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'mentor';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit;
    }
}

// Redirect if not mentor
function requireMentor() {
    requireLogin();
    if (!isMentor()) {
        header("Location: dashboard-anggota.php");
        exit;
    }
}

// Format divisi name
function formatDivisiName($divisi) {
    $divisiMap = [
        "cyber_security" => "Cyber Security",
        "software_dev" => "Software Development",
        "explore" => "Explore"
    ];
    return isset($divisiMap[$divisi]) ? $divisiMap[$divisi] : $divisi;
}

// Format date
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d F Y', $timestamp);
}

// Display alert message
function showAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Display alert if exists and clear it
function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alertType = $_SESSION['alert']['type'];
        $alertMessage = $_SESSION['alert']['message'];

        echo "<div class='alert alert-{$alertType}'>{$alertMessage}</div>";

        // Clear the alert
        unset($_SESSION['alert']);
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Check if deadline is passed
function isDeadlinePassed($deadline) {
    $deadlineDate = new DateTime($deadline);
    $today = new DateTime();
    return $deadlineDate < $today;
}
?>
