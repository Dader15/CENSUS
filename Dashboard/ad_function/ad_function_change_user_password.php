<?php
session_start();
include_once '../../connection/config.php';

$response = [
    'status' => 400,
    'message' => 'Invalid request',
    'data' => null
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = isset($_POST["userId"]) ? intval($_POST["userId"]) : 0;
    $action = isset($_POST["action"]) ? trim($_POST["action"]) : '';
    
    if (!$userId) {
        $response['message'] = 'Invalid user ID';
    } elseif ($action === 'generate') {
        // Generate random password: 12 chars, 1 symbol, 1 uppercase, 1 lowercase, 1 number
        $password = generateSecurePassword();
        
        if (strlen($password) >= 12) {
            $response['status'] = 200;
            $response['message'] = 'Password generated successfully';
            $response['data'] = [
                'password' => $password,
                'message' => 'Click confirm to save this password'
            ];
        } else {
            $response['message'] = 'Failed to generate secure password';
        }
    } elseif ($action === 'save') {
        // Save the generated password
        $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';
        
        if (empty($password)) {
            $response['message'] = 'Password is required';
        } elseif (strlen($password) < 12) {
            $response['message'] = 'Password must be at least 12 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $response['message'] = 'Password must contain at least 1 uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $response['message'] = 'Password must contain at least 1 lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $response['message'] = 'Password must contain at least 1 number';
        } elseif (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $response['message'] = 'Password must contain at least 1 symbol';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Update password and set changedpassword to 0
            $update_stmt = $con->prepare("UPDATE user_tbl SET password = ?, changedpassword = 0, updatedat = NOW() WHERE id = ?");
            
            if (!$update_stmt) {
                $response['message'] = 'Prepare failed: ' . $con->error;
            } else {
                $update_stmt->bind_param("si", $hashed_password, $userId);
                
                if ($update_stmt->execute()) {
                    $response['status'] = 200;
                    $response['message'] = 'Password saved successfully. User will be required to change it on next login.';
                } else {
                    $response['message'] = 'Error updating password: ' . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    } elseif ($action === 'set') {
        // Admin manually sets password
        $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';
        
        if (empty($password)) {
            $response['message'] = 'Password is required';
        } elseif (strlen($password) < 12) {
            $response['message'] = 'Password must be at least 12 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $response['message'] = 'Password must contain at least 1 uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $response['message'] = 'Password must contain at least 1 lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $response['message'] = 'Password must contain at least 1 number';
        } elseif (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $response['message'] = 'Password must contain at least 1 symbol';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Update password and set changedpassword to 0
            $update_stmt = $con->prepare("UPDATE user_tbl SET password = ?, changedpassword = 0, updatedat = NOW() WHERE id = ?");
            
            if (!$update_stmt) {
                $response['message'] = 'Prepare failed: ' . $con->error;
            } else {
                $update_stmt->bind_param("si", $hashed_password, $userId);
                
                if ($update_stmt->execute()) {
                    $response['status'] = 200;
                    $response['message'] = 'Password updated successfully. User will be required to change it on next login';
                } else {
                    $response['message'] = 'Error updating password: ' . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    } else {
        $response['message'] = 'Invalid action';
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);

// Function to generate secure password
function generateSecurePassword($length = 12) {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $symbols = '!@#$%^&*()_+-=[]{}:;<>?,./';
    
    // Ensure at least one of each required character type
    $password = '';
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $numbers[rand(0, strlen($numbers) - 1)];
    $password .= $symbols[rand(0, strlen($symbols) - 1)];
    
    // Fill the rest with random characters from all sets
    $all_chars = $uppercase . $lowercase . $numbers . $symbols;
    for ($i = 4; $i < $length; $i++) {
        $password .= $all_chars[rand(0, strlen($all_chars) - 1)];
    }
    
    // Shuffle the password
    $password = str_shuffle($password);
    
    return $password;
}
?>
