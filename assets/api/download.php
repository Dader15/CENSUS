<?php
include_once 'Api.class.php';

$client_id = $_GET['client_id'] ?? '';
$client_secret = $_GET['client_secret'] ?? '';
$dept_shortcut = $_GET['dept'] ?? '';
$filename = $_GET['file'] ?? '';

$api = new API();
if ($client_id != $api->get_client_id() || $client_secret != $api->get_client_secret()) {
    http_response_code(401);
    die('Unauthorized');
}

$fileContent = $api->get_file($filename, $dept_shortcut);

if ($fileContent === false) {
    http_response_code(404);
    die('File not found');
}

// Decode base64
$fileContent = base64_decode($fileContent);

// Parse original filename from temp_name (format: uniqid_filename)
$parts = explode('_', $filename, 2);
$originalFilename = isset($parts[1]) ? $parts[1] : $filename;

// Determine mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_buffer($finfo, $fileContent);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $originalFilename . '"');
echo $fileContent;
?>