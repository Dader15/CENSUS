<?php
session_start();

if (!isset($_SESSION['UID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

// Save form data into session keyed by user ID so multiple users don't conflict
$uid = $_SESSION['UID'];
$_SESSION['form_draft'][$uid] = [
    'householdData'        => $data['householdData']        ?? [],
    'memberData'           => $data['memberData']           ?? [],
    'householdQuestionsData' => $data['householdQuestionsData'] ?? [],
    'currentStep'          => $data['currentStep']          ?? 1,
    'memberCount'          => $data['memberCount']          ?? 1,
    'savedAt'              => date('Y-m-d H:i:s'),
];

echo json_encode(['status' => 'success']);
