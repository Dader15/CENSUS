<?php
session_start();
include_once '../../connection/config.php';

$response = [
    'status' => 400,
    'message' => 'Invalid request',
    'data' => null
];

$UID = isset($_SESSION['UID']) ? $_SESSION['UID'] : null;

if (!$UID) {
    $response['message'] = 'User not logged in';
} else {
    $stmt = $con->prepare("SELECT id, sname, fname, middleinitial, suffix, username, usertype, brgy, position, delete_status 
                          FROM user_tbl WHERE id = ? LIMIT 1");
    
    if (!$stmt) {
        $response['message'] = 'Prepare failed: ' . $con->error;
    } else {
        $stmt->bind_param("i", $UID);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $row['full_name'] = $row['sname'] . ', ' . $row['fname'] . ' ' . $row['middleinitial'] . ' ' . $row['suffix'];
            $response['status'] = 200;
            $response['message'] = 'User retrieved successfully';
            $response['data'] = $row;
        } else {
            $response['message'] = 'User not found';
        }
        $stmt->close();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
