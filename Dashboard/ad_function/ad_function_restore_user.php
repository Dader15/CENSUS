<?php
session_start();
include_once '../../connection/config.php';

$response = [
    'status' => 400,
    'message' => 'Invalid request',
    'data' => null
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

    if (!$id) {
        $response['message'] = 'Invalid user ID';
    } else {
        // Restore user - set delete_status to 0 (active)
        $restore_stmt = $con->prepare("UPDATE user_tbl SET delete_status = 0, updatedat = NOW() WHERE id = ?");
        
        if (!$restore_stmt) {
            $response['message'] = 'Prepare failed: ' . $con->error;
        } else {
            $restore_stmt->bind_param("i", $id);

            if ($restore_stmt->execute()) {
                $response['status'] = 200;
                $response['message'] = 'User restored successfully';
            } else {
                $response['message'] = 'Error restoring user: ' . $restore_stmt->error;
            }
            $restore_stmt->close();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
