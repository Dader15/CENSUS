<?php
header("Access-Control-Allow-Origin: *"); // Change * to specific origin if needed
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Api-Function, Client-Id, Client-Secret, Dept-Shortcut"); // Add Dept-Shortcut header

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

$api_headers = getallheaders();

$method = isset($api_headers['Api-Function']) ? $api_headers['Api-Function'] : '';
$client_id = isset($api_headers['Client-Id']) ? $api_headers['Client-Id'] : '';
$client_secret = isset($api_headers['Client-Secret']) ? $api_headers['Client-Secret'] : '';

include_once 'Api.class.php';
$api = new API();
if ($client_id != $api->get_client_id() ||
    $client_secret != $api->get_client_secret()) {
    echo json_encode([
        'status' => 400, 'message' => 'Unauthorized', 'data' => $_POST
    ]);
    die;
}

if ($method === 'upload_file_to_nas') {
    $datas = file_get_contents('php://input');
    $datas = json_decode($datas);
    $datas = $datas ? (array)$datas : $_POST;
    if (isset($datas["fileAttachmentB64"]) &&
        isset($datas["filename"]) &&
        isset($datas["dept_shortcut"])) {

        $base64data = $datas["fileAttachmentB64"];
        $res = $api->upload_file($base64data, $datas["filename"], $datas["dept_shortcut"]);
        if ($res === true) {
            echo json_encode([
                'status' => 200, 'message' => 'File uploaded successfully.',
            ]);
        } else {
            echo json_encode([
                'status' => 400, 'message' => $res,
            ]);
        }
    } else {
        echo json_encode([
            'status' => 400, 'message' => 'Invalid Parameters', 'data' => $datas
        ]);
    }
} else if ($method === 'get_file_from_nas') {
    $datas = file_get_contents('php://input');
    $datas = json_decode($datas);
    $datas = $datas ? (array)$datas : $_POST;
    if (isset($datas["filename"]) && isset($datas["dept_shortcut"])) {
        $dept_shortcut = $datas["dept_shortcut"];
        $filename = $datas["filename"];
        $url = ($dept_shortcut === 'HRMDO') ? 'https://192.168.20.25/DigitalMemo/download.php' : 'https://192.168.20.57/DigitalMemo/download.php';
        $url .= '?client_id=ItD0!&client_secret=NvQ#7$Qn&zRGLGqfM65#(+YT[P%49wc@&dept=' . urlencode($dept_shortcut) . '&file=' . urlencode($filename);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(['success' => false, 'error' => 'Curl error: ' . curl_error($ch)]);
            curl_close($ch);
            die;
        }
        curl_close($ch);
        $base64 = base64_encode($response);
        echo json_encode(['success' => true, 'data' => $base64]);
    } else {
        echo json_encode([
            'status' => 400, 'message' => 'Invalid Parameters', 'data' => $datas
        ]);
    }
} else {
    echo json_encode([
        'status' => 400, 'message' => 'Invalid Request',
        'headers' => $api_headers,
        'server' => $_SERVER,
    ]);
}
?>