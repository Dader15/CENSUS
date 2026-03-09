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
        // Soft delete - set delete_status to 1 (inactive)
        $delete_stmt = $con->prepare("UPDATE user_tbl SET delete_status = 1, updatedat = NOW() WHERE id = ?");
        
        if (!$delete_stmt) {
            $response['message'] = 'Prepare failed: ' . $con->error;
        } else {
            $delete_stmt->bind_param("i", $id);

            if ($delete_stmt->execute()) {
                $response['status'] = 200;
                $response['message'] = 'User deleted successfully';
            } else {
                $response['message'] = 'Error deleting user: ' . $delete_stmt->error;
            }
            $delete_stmt->close();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>