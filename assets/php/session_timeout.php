<?php
session_start();
include_once '../../connection/config.php';

$timeout_duration = 18000; // 30 minutes in seconds

if (isset($_SESSION['loggedin_time']) && isset($_SESSION['UID'])) {
    $UID = $_SESSION['UID'];
    $session_id = $_SESSION['session_id'];
    $sql = "SELECT session_id, updatedat FROM user_tbl WHERE UID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $UID);
    $stmt->execute();
    $stmt->bind_result($db_session_id, $db_updatedat);
    $stmt->fetch();
    $stmt->close();

    // Calculate time difference between now and the last updated time
    $last_active = strtotime($db_updatedat);
    $current_time = time();
    $time_diff = $current_time - $last_active;

    if ($session_id !== $db_session_id || $time_diff >= $timeout_duration) {
        // Remove session ID from database
        $update_sql = "UPDATE user_tbl SET session_id = NULL WHERE UID = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("i", $UID);
        $update_stmt->execute();
        $update_stmt->close();

        session_unset();
        session_destroy();
        echo json_encode(['status' => 'logout']);
        exit;
    } else {
        $_SESSION['loggedin_time'] = time();
        $updatedat = date('Y-m-d H:i:s');
        $update_sql = "UPDATE user_tbl SET updatedat = ? WHERE UID = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("si", $updatedat, $UID);
        $update_stmt->execute();
        $update_stmt->close();
        echo json_encode(['status' => 'active']);
        exit;
    }
} else {
    echo json_encode(['status' => 'logout']);
    exit;
}
?>