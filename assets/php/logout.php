<?php
session_start();

if (isset($_SESSION['UID'])) {
    // Try to update database if config exists
    if (file_exists('../../connection/config.php')) {
        include_once '../../connection/config.php';
        $UID = $_SESSION['UID'];
        $update_sql = "UPDATE user_tbl SET session_id = NULL WHERE id = ?";
        $update_stmt = $con->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param("i", $UID);
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
