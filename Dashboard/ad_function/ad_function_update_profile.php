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
    $sname = isset($_POST["sname"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["sname"]))) : '';
    $fname = isset($_POST["fname"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["fname"]))) : '';
    $middleinitial = isset($_POST["middleinitial"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["middleinitial"]))) : '';
    $suffix = isset($_POST["suffix"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["suffix"]))) : '';
    $username = isset($_POST["username"]) ? mysqli_real_escape_string($con, strtolower(trim($_POST["username"]))) : '';
    $brgy = isset($_POST["brgy"]) ? mysqli_real_escape_string($con, trim($_POST["brgy"])) : '';
    $position = isset($_POST["position"]) ? mysqli_real_escape_string($con, trim($_POST["position"])) : '';
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

    if (!$id || empty($sname) || empty($fname) || empty($username)) {
        $response['message'] = 'Missing required fields';
    } else {
        // If password is provided, validate it
        if (!empty($password)) {
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
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $update_stmt = $con->prepare("UPDATE user_tbl SET sname = ?, fname = ?, middleinitial = ?, suffix = ?, username = ?, brgy = ?, position = ?, password = ?, updatedat = NOW() WHERE id = ?");
                
                if (!$update_stmt) {
                    $response['message'] = 'Prepare failed: ' . $con->error;
                } else {
                    $update_stmt->bind_param("ssssssssi", $sname, $fname, $middleinitial, $suffix, $username, $brgy, $position, $hashed_password, $id);
                    
                    if ($update_stmt->execute()) {
                        $response['status'] = 200;
                        $response['message'] = 'Profile updated successfully';
                    } else {
                        $response['message'] = 'Error updating profile: ' . $update_stmt->error;
                    }
                    $update_stmt->close();
                }
            }
        } else {
            // No password provided, keep existing password
            $update_stmt = $con->prepare("UPDATE user_tbl SET sname = ?, fname = ?, middleinitial = ?, suffix = ?, username = ?, brgy = ?, position = ?, updatedat = NOW() WHERE id = ?");
            
            if (!$update_stmt) {
                $response['message'] = 'Prepare failed: ' . $con->error;
            } else {
                $update_stmt->bind_param("ssssssssi", $sname, $fname, $middleinitial, $suffix, $username, $brgy, $position, $id);
                
                if ($update_stmt->execute()) {
                    $response['status'] = 200;
                    $response['message'] = 'Profile updated successfully';
                } else {
                    $response['message'] = 'Error updating profile: ' . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
