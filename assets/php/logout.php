<?php
session_start();

if (isset($_SESSION['UID'])) {
    // Try to update database if config exists
    if (file_exists('../../connection/config.php')) {
        include_once '../../connection/config.php';
        $UID = $_SESSION['UID'];
        $session_id = $_SESSION['session_id'] ?? '';

        // Deactivate login history record
        $update_sql = "UPDATE loginhistory_tbl SET is_active = 0, updated = NOW() WHERE UID = ? AND session_id = ? AND is_active = 1";
        $update_stmt = $con->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param("is", $UID, $session_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
    
    // Destroy session
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No active session found']);
}
exit;
?>
