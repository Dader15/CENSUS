<?php
session_start();
include_once '../../connection/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usr = mysqli_real_escape_string($con, $_POST["usr"]);
    $pss = mysqli_real_escape_string($con, $_POST["pss"]);

    // Fetch user details by username
    $sql = "SELECT * FROM user_tbl WHERE username = ? and delete_status = 0";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $usr);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if user has an active login session via loginhistory_tbl
        $check_sql = "SELECT ID, last_activity FROM loginhistory_tbl WHERE UID = ? AND is_active = 1 ORDER BY last_activity DESC LIMIT 1";
        $check_stmt = $con->prepare($check_sql);
        $check_stmt->bind_param("i", $row['id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $active_row = $check_result->fetch_assoc();
            $last_active = strtotime($active_row['last_activity']);
            $current_time = time();
            $time_diff = $current_time - $last_active;

            if ($time_diff < 300) { // 5 minutes in seconds
                $check_stmt->close();
                echo json_encode(['status' => 'error', 'message' => 'User is already logged in from another device']);
                exit();
            } else {
                // Deactivate stale session
                $deactivate_sql = "UPDATE loginhistory_tbl SET is_active = 0, updated = NOW() WHERE ID = ?";
                $deactivate_stmt = $con->prepare($deactivate_sql);
                $deactivate_stmt->bind_param("i", $active_row['ID']);
                $deactivate_stmt->execute();
                $deactivate_stmt->close();
            }
        }
        $check_stmt->close();

        // Verify the password using password_verify()
        if (password_verify($pss, $row['password'])) {
            // Regenerate session ID to ensure uniqueness
            session_regenerate_id(true);
            $session_id = session_id(); // Get new session ID

            // Set session variables
            $_SESSION['UID'] = $row['id'];
            $_SESSION['sname'] = $row['sname'];
            $_SESSION['fname'] = $row['fname'];
            $_SESSION['mi'] = $row['middleinitial'];
            $_SESSION['suffix'] = $row['suffix'];
            $_SESSION['full_name'] = $row['sname'] . ', ' . $row['fname'] . ' ' . $row['middleinitial'] . ' ' . $row['suffix'];
            $_SESSION['loggedin_time'] = time();
            $_SESSION['session_id'] = $session_id;
            $_SESSION['changedpassword'] = $row['changedpassword'];

            // Update the updatedat timestamp in user_tbl
            $updatedat = date('Y-m-d H:i:s');
            $update_sql = "UPDATE user_tbl SET updatedat = ? WHERE id = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("si", $updatedat, $row['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Insert login history record
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $device_used = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $now = date('Y-m-d H:i:s');
            $history_sql = "INSERT INTO loginhistory_tbl (UID, session_id, ip_address, device_used, created, updated, last_activity, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
            $history_stmt = $con->prepare($history_sql);
            $history_stmt->bind_param("issssss", $row['id'], $session_id, $ip_address, $device_used, $now, $now, $now);
            $history_stmt->execute();
            $history_stmt->close();

            // Determine redirect URL based on usertype
            $redirect_url = '../CENSUS/Dashboard/index.php';

            // Respond with success message and redirect URL
            $response = array('status' => 'success', 'redirect_url' => $redirect_url, 'message' => 'Login successful');
            echo json_encode($response);
        } else {
            // Invalid password
            echo json_encode(array('status' => 'error', 'message' => 'Invalid username or password'));
        }
    } else {
        // User not found
        echo json_encode(array('status' => 'error', 'message' => 'Invalid username or password'));
    }

    // Close database connections
    $stmt->close();
    $con->close();
}
?>