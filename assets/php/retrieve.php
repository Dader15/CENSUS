
<?php
include_once '../connection/config.php';
$UID = $_SESSION['UID'];

$query = "
        SELECT user_tbl.id, user_tbl.username, user_tbl.usertype, user_tbl.position, user_tbl.full_name, user_tbl.delete_status, user_tbl.changedpassword, user_tbl.brgy
        FROM user_tbl
        WHERE user_tbl.id = ?
        ";

$stmt = $con->prepare($query);
$stmt->bind_param("s", $UID);
$stmt->execute();
$stmt->bind_result($UID, $usr, $usertype, $position, $name, $delete_stat, $changedpass, $brgy);

// Fetch the result
if ($stmt->fetch()) {
    $_SESSION['UID'] = $UID;
    $_SESSION['usr'] = $usr;
    $_SESSION['username'] = $usr;
    $_SESSION['usertype'] = $usertype;
    $_SESSION['position'] = $position;
    $_SESSION['full_name'] = $name;
    $_SESSION['name'] = $name;
    $_SESSION['delete_status'] = $delete_stat;
    $_SESSION['changedpassword'] = $changedpass;
    $_SESSION['brgy'] = $brgy;
} else {
    
}

$stmt->close();
$con->close();
?>
