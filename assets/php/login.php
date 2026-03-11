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

        // Check if there is an active session_id already set
        if (!empty($row['session_id'])) {
            $last_active = strtotime($row['updatedat']);
            $current_time = time();
            $time_diff = $current_time - $last_active;

            if ($time_diff < 300) { // 5 minutes in seconds
                echo json_encode(['status' => 'error', 'message' => 'User is already logged in from another device']);
                exit();
            }
        }

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

            // Update the session_id and updatedat in the database
            $updatedat = date('Y-m-d H:i:s');
            $update_sql = "UPDATE user_tbl SET session_id = ?, updatedat = ? WHERE id = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("ssi", $session_id, $updatedat, $row['id']);
            $update_stmt->execute();
            $update_stmt->close(); 

            // Determine redirect URL based on usertype
            // if ($row['usertype'] == ADMIN) {
                $redirect_url = '../CENSUS/Dashboard/index.php';
            // } else {
            //     $redirect_url = '../CENSUS/Dashboard/index.php';
            // }

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