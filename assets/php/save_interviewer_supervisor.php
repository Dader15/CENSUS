<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['UID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Save interviewer data to session
if (isset($input['interviewer'])) {
    $_SESSION['interviewer_surname']   = trim($input['interviewer']['surname'] ?? '');
    $_SESSION['interviewer_firstname'] = trim($input['interviewer']['firstname'] ?? '');
    $_SESSION['interviewer_mi']        = trim($input['interviewer']['mi'] ?? '');
    $_SESSION['interviewer_suffix']    = trim($input['interviewer']['suffix'] ?? '');
}

// Save supervisor data to session
if (isset($input['supervisor'])) {
    $_SESSION['supervisor_surname']   = trim($input['supervisor']['surname'] ?? '');
    $_SESSION['supervisor_firstname'] = trim($input['supervisor']['firstname'] ?? '');
    $_SESSION['supervisor_mi']        = trim($input['supervisor']['mi'] ?? '');
    $_SESSION['supervisor_suffix']    = trim($input['supervisor']['suffix'] ?? '');
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Interviewer/Supervisor saved to session']);
exit;
?>
