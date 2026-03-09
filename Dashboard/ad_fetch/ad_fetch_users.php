<?php
include_once '../../connection/config.php';

$response = array();

try {
    // Use prepared statements for better security
    $stmt = $con->prepare("SELECT id, full_name, username, usertype, brgy, position, delete_status, `date created`, updatedat, changedpassword
                        FROM user_tbl
                        ORDER BY id DESC");
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $con->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $user = array(
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'username' => $row['username'],
                'usertype' => $row['usertype'],
                'brgy' => $row['brgy'],
                'position' => $row['position'],
                'delete_status' => $row['delete_status'],
                'date created' => $row['date created'],
                'updatedat' => $row['updatedat'],
                'changedpassword' => $row['changedpassword']
            );
            $users[] = $user;
        }
        $response['success'] = true;
        $response['users'] = $users;
    } else {
        throw new Exception('Error executing query: ' . $con->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
