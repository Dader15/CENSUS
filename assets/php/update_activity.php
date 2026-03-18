<?php
session_start();
include_once '../../connection/config.php';

if (isset($_SESSION['UID'])) {
    $UID = $_SESSION['UID'];
    $session_id = $_SESSION['session_id'] ?? '';
    $updatedat = date('Y-m-d H:i:s');

    // Update user_tbl
    $update_sql = "UPDATE user_tbl SET updatedat = ? WHERE id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("si", $updatedat, $UID);
    $update_stmt->execute();
    $update_stmt->close();

    // Update loginhistory_tbl last_activity
    $history_sql = "UPDATE loginhistory_tbl SET last_activity = ?, updated = ? WHERE UID = ? AND session_id = ? AND is_active = 1";
    $history_stmt = $con->prepare($history_sql);
    $history_stmt->bind_param("ssis", $updatedat, $updatedat, $UID, $session_id);
    $history_stmt->execute();
    $history_stmt->close();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}
$con->close();
?>
