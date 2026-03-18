<?php
session_start();

if (!isset($_SESSION['UID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$uid = $_SESSION['UID'];
$draft = $_SESSION['form_draft'][$uid] ?? null;

echo json_encode(['status' => 'success', 'draft' => $draft]);
