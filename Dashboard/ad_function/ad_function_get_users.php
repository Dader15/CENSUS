<?php
session_start();
include_once '../../connection/config.php';

$response = [
    'status' => 200,
    'data' => [],
    'success' => true,
    'users' => []
];

try {
    // Get current user's information
    $userType = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : '';
    $userBrgy = isset($_SESSION['brgy']) ? $_SESSION['brgy'] : '';
    
    if ($userType === 'SUPERADMIN') {
        // SUPERADMIN can see all users from all barangays
        $stmt = $con->prepare("SELECT id, sname, fname, middleinitial, suffix, username, usertype, brgy, position, delete_status, `date created` 
                              FROM user_tbl ORDER BY id DESC");
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $con->error);
        }
        
        $stmt->execute();
    } else {
        // Non-SUPERADMIN can only see users from their own barangay (and not SUPERADMIN)
        $stmt = $con->prepare("SELECT id, sname, fname, middleinitial, suffix, username, usertype, brgy, position, delete_status, `date created` 
                              FROM user_tbl WHERE brgy = ? AND usertype != 'SUPERADMIN' ORDER BY id DESC");
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $con->error);
        }
        
        $stmt->bind_param('s', $userBrgy);
        $stmt->execute();
    }
    
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $row['full_name'] = $row['sname'] . ', ' . $row['fname'] . ' ' . $row['middleinitial'] . ' ' . $row['suffix'];
        $users[] = $row;
    }
    
    $response['data'] = $users;
    $response['users'] = $users;
    $stmt->close();
} catch (Exception $e) {
    $response['status'] = 400;
    $response['success'] = false;
    $response['data'] = [];
    $response['users'] = [];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
