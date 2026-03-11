<?php
session_start();
include_once '../../connection/config.php';

// Function to generate secure password
function generateSecurePassword() {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    // Ensure at least one character from each required set
    $password = '';
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $numbers[rand(0, strlen($numbers) - 1)];
    $password .= $symbols[rand(0, strlen($symbols) - 1)];
    
    // Fill remaining 8 characters from all sets combined
    $all_chars = $uppercase . $lowercase . $numbers . $symbols;
    for ($i = 0; $i < 8; $i++) {
        $password .= $all_chars[rand(0, strlen($all_chars) - 1)];
    }
    
    // Shuffle the password
    $password_array = str_split($password);
    shuffle($password_array);
    return implode('', $password_array);
}

$response = [
    'status' => 400,
    'message' => 'Invalid request',
    'data' => null
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sname = isset($_POST["sname"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["sname"]))) : '';
    $fname = isset($_POST["fname"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["fname"]))) : '';
    $middleinitial = isset($_POST["middleinitial"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["middleinitial"]))) : '';
    $suffix = isset($_POST["suffix"]) ? mysqli_real_escape_string($con, strtoupper(trim($_POST["suffix"]))) : '';
    $username = isset($_POST["username"]) ? mysqli_real_escape_string($con, strtolower(trim($_POST["username"]))) : '';
    $usertype = isset($_POST["usertype"]) ? mysqli_real_escape_string($con, trim($_POST["usertype"])) : '';
    $brgy = isset($_POST["brgy"]) ? mysqli_real_escape_string($con, trim($_POST["brgy"])) : '';
    $position = isset($_POST["position"]) ? mysqli_real_escape_string($con, trim($_POST["position"])) : '';

    if (empty($sname) || empty($fname) || empty($username) || empty($usertype)) {
        $response['message'] = 'Missing required fields';
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
                // Generate random password
                $generated_password = generateSecurePassword();
                $hashed_password = password_hash($generated_password, PASSWORD_BCRYPT);
                
                $insert_stmt = $con->prepare("INSERT INTO user_tbl (sname, fname, middleinitial, suffix, username, password, usertype, brgy, position, delete_status, `date created`, changedpassword) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), 0)");
                
                if (!$insert_stmt) {
                    $response['message'] = 'Prepare failed: ' . $con->error;
                } else {
                    $insert_stmt->bind_param("sssssssss", $sname, $fname, $middleinitial, $suffix, $username, $hashed_password, $usertype, $brgy, $position);
                    
                    if ($insert_stmt->execute()) {
                        $response['status'] = 200;
                        $response['message'] = 'User added successfully';
                        $response['data'] = [
                            'id' => $insert_stmt->insert_id,
                            'generated_password' => $generated_password
                        ];
                    } else {
                        $response['message'] = 'Error adding user: ' . $insert_stmt->error;
                    }
                    $insert_stmt->close();
                }
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
