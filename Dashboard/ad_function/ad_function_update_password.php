<?php
session_start();
include_once '../../connection/config.php';

$response = [
    'status' => 400,
    'message' => 'Invalid request',
    'data' => null
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uid = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;
    $currentPassword = isset($_POST["currentPassword"]) ? trim($_POST["currentPassword"]) : '';
    $newPassword = isset($_POST["newPassword"]) ? trim($_POST["newPassword"]) : '';
    
    if (!$uid) {
        $response['message'] = 'User not logged in';
    } elseif (empty($newPassword)) {
        $response['message'] = 'New password is required';
    } else {
        // Get current user's password and changedpassword status
        $check_stmt = $con->prepare("SELECT password, changedpassword FROM user_tbl WHERE id = ? LIMIT 1");
        
        if (!$check_stmt) {
            $response['message'] = 'Prepare failed: ' . $con->error;
        } else {
            $check_stmt->bind_param("i", $uid);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($user_row = $check_result->fetch_assoc()) {
                $userChangedPassword = intval($user_row['changedpassword']);
                $userPasswordHash = $user_row['password'];
                $passwordVerified = false;
                
                // If changedpassword is 0 (initial password change), skip current password verification
                // If changedpassword is 1 (regular change), require current password verification
                if ($userChangedPassword == 0) {
                    // Initial password change - no current password verification needed
                    $passwordVerified = true;
                } else {
                    // Regular password change - verify current password
                    if (empty($currentPassword)) {
                        $response['message'] = 'Current password is required';
                    } elseif (!password_verify($currentPassword, $userPasswordHash)) {
                        $response['status'] = 401;
                        $response['message'] = 'Current password is incorrect';
                    } else {
                        $passwordVerified = true;
                    }
                }
                
                // If password verification passed or was not needed, validate and update new password
                if ($passwordVerified) {
                    if (strlen($newPassword) < 12) {
                        $response['message'] = 'Password must be at least 12 characters long';
                    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
                        $response['message'] = 'Password must contain at least 1 uppercase letter';
                    } elseif (!preg_match('/[a-z]/', $newPassword)) {
                        $response['message'] = 'Password must contain at least 1 lowercase letter';
                    } elseif (!preg_match('/[0-9]/', $newPassword)) {
                        $response['message'] = 'Password must contain at least 1 number';
                    } elseif (!preg_match('/[^a-zA-Z0-9]/', $newPassword)) {
                        $response['message'] = 'Password must contain at least 1 symbol';
                    } else {
                        // Update password and set changedpassword to 1
                        $hashed_password = password_hash($newPassword, PASSWORD_BCRYPT);
                        $update_stmt = $con->prepare("UPDATE user_tbl SET password = ?, changedpassword = 1, updatedat = NOW() WHERE id = ?");
                        
                        if (!$update_stmt) {
                            $response['message'] = 'Prepare failed: ' . $con->error;
                        } else {
                            $update_stmt->bind_param("si", $hashed_password, $uid);
                            
                            if ($update_stmt->execute()) {
                                $response['status'] = 200;
                                $response['message'] = 'Password updated successfully';
                            } else {
                                $response['message'] = 'Error updating password: ' . $update_stmt->error;
                            }
                            $update_stmt->close();
                        }
                    }
                }
            } else {
                $response['message'] = 'User not found';
            }
            $check_stmt->close();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
