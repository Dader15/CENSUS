<?php

$prod = strpos($_SERVER['SERVER_NAME'], 'localhost') === false;
// $prod = true;
// if ($prod) {
//     define('_HOST', '192.168.20.6:3307');
//     define('_USER', 'DigitalMemo_prod_rw');
//     define('_PASS', 'ct74gHZj&6Ea');
//     define('_DB', 'ctmsgr_db');
//     define('BASE_URL', 'http://192.168.20.6/CENSUS/');
//     define('SITE_URL', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "CENSUS/");
// } else {
    define('_HOST', 'localhost');
    define('_USER', 'root');
    define('_PASS', '');
    define('_DB', 'census_db');
    define('BASE_URL', 'http://localhost/CENSUS/');
    define('SITE_URL', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "CENSUS/");
// }

$con = mysqli_connect(_HOST, _USER, _PASS, _DB);
$con->set_charset("utf8");

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>

