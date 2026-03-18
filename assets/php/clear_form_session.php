<?php
session_start();

if (!isset($_SESSION['UID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$uid = $_SESSION['UID'];
unset($_SESSION['form_draft'][$uid]);

echo json_encode(['status' => 'success']);
