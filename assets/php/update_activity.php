<?php
session_start();
include_once '../../connection/config.php';

if (isset($_SESSION['UID'])) {
    $UID = $_SESSION['UID'];
    $updatedat = date('Y-m-d H:i:s');

    $update_sql = "UPDATE user_tbl SET updatedat = ? WHERE UID = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("si", $updatedat, $UID);
    $update_stmt->execute();
    $update_stmt->close();

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}
$con->close();
?>
