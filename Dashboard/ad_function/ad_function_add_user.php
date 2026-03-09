<?php
session_start();
include_once '../../connection/config.php';

$response = [
    'status' => 400,
    'message' => 'Invalid request',
    'data' => null
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = isset($_POST["full_name"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["full_name"]))) : '';
    $username = isset($_POST["username"]) ? mysqli_real_escape_string($con, strtolower(trim($_POST["username"]))) : '';
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';
    $usertype = isset($_POST["usertype"]) ? mysqli_real_escape_string($con, trim($_POST["usertype"])) : '';
    $brgy = isset($_POST["brgy"]) ? mysqli_real_escape_string($con, trim($_POST["brgy"])) : '';
    $position = isset($_POST["position"]) ? mysqli_real_escape_string($con, trim($_POST["position"])) : '';

    if (empty($full_name) || empty($username) || empty($password) || empty($usertype)) {
        $response['message'] = 'Missing required fields';
    } else {
        // Password strength validation
        if (strlen($password) < 12) {
            $response['message'] = 'Password must be at least 12 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $response['message'] = 'Password must contain at least 1 uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $response['message'] = 'Password must contain at least 1 lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $response['message'] = 'Password must contain at least 1 number';
        } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password)) {
            $response['message'] = 'Password must contain at least 1 symbol (!@#$%^&*() etc)';
        } else {
            // Check if username exists
            $check_stmt = $con->prepare("SELECT id FROM user_tbl WHERE LOWER(username) = ?");
            if (!$check_stmt) {
                $response['message'] = 'Prepare failed: ' . $con->error;
            } else {
                $lower_username = strtolower($username);
                $check_stmt->bind_param("s", $lower_username);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    $response['message'] = 'Username already exists';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $insert_stmt = $con->prepare("INSERT INTO user_tbl (full_name, username, password, usertype, brgy, position, delete_status, `date created`, changedpassword) 
                                 VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), 1)");
                    
                    if (!$insert_stmt) {
                        $response['message'] = 'Prepare failed: ' . $con->error;
                    } else {
                        $insert_stmt->bind_param("ssssss", $full_name, $username, $hashed_password, $usertype, $brgy, $position);
                        
                        if ($insert_stmt->execute()) {
                            $response['status'] = 200;
                            $response['message'] = 'User added successfully';
                            $response['data'] = ['id' => $insert_stmt->insert_id];
                        } else {
                            $response['message'] = 'Error adding user: ' . $insert_stmt->error;
                        }
                        $insert_stmt->close();
                    }
                }
                $check_stmt->close();
            }
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
