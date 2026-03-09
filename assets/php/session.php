<?php
// Include central security headers for all admin pages before any output
if (file_exists(__DIR__ . '/security_headers.php')) {
    include_once __DIR__ . '/security_headers.php';
}

session_start();
if (!isset($_SESSION['UID'])) {
    header("Location: ../index.php");
    exit();
}
?>
