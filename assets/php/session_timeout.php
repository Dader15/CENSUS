<?php
session_start();
include_once '../../connection/config.php';

$timeout_duration = 18000; // 30 minutes in seconds

if (isset($_SESSION['loggedin_time']) && isset($_SESSION['UID'])) {
    $UID = $_SESSION['UID'];
    $session_id = $_SESSION['session_id'];
    
    // Check login history for active session
    $sql = "SELECT ID, session_id, last_activity FROM loginhistory_tbl WHERE UID = ? AND session_id = ? AND is_active = 1 LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("is", $UID, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // No active login history record found - force logout
        $stmt->close();
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'logout']);
        exit;
    }
    
    $history_row = $result->fetch_assoc();
    $stmt->close();

    // Calculate time difference between now and the last activity
    $last_active = strtotime($history_row['last_activity']);
    $current_time = time();
    $time_diff = $current_time - $last_active;

    if ($time_diff >= $timeout_duration) {
        // Deactivate login history record
        $update_sql = "UPDATE loginhistory_tbl SET is_active = 0, updated = NOW() WHERE ID = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("i", $history_row['ID']);
        $update_stmt->execute();
        $update_stmt->close();

        session_unset();
        session_destroy();
        echo json_encode(['status' => 'logout']);
        exit;
    } else {
        $_SESSION['loggedin_time'] = time();
        $now = date('Y-m-d H:i:s');
        
        // Update last_activity in loginhistory_tbl
        $update_sql = "UPDATE loginhistory_tbl SET last_activity = ?, updated = ? WHERE ID = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("ssi", $now, $now, $history_row['ID']);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Also update updatedat in user_tbl for general tracking
        $update_user_sql = "UPDATE user_tbl SET updatedat = ? WHERE id = ?";
        $update_user_stmt = $con->prepare($update_user_sql);
        $update_user_stmt->bind_param("si", $now, $UID);
        $update_user_stmt->execute();
        $update_user_stmt->close();
        
        echo json_encode(['status' => 'active']);
        exit;
    }
} else {
    echo json_encode(['status' => 'logout']);
    exit;
}
?>