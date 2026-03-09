<?php

class API{
    private $client_id='ItD0!';
    private $client_secret = 'NvQ#7$Qn&zRGLGqfM65#(+YT[P%49wc@';

    public function get_client_id(){
        return $this->client_id;
    }
    
    public function get_client_secret(){
        return $this->client_secret;
    }

    public function upload_file($file_b64, $filename, $dept_shortcut){
        // Determine the base path based on department shortcut
        $basePath = '../DigitalMemo/' . $dept_shortcut . '/';
        
        // Create the directory if it doesn't exist
        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }

        // Decode base64 data
        $decodedData = base64_decode($file_b64);

        // Save the decoded data to a file
        $result = file_put_contents($basePath . $filename, $decodedData);

        if ($result !== false) {
            return true;
        } else {
            $error = error_get_last();
            return "Error saving file: " . $error["message"];
        }
    }

    public function get_file($filename, $dept_shortcut){
        // Determine the base path based on department shortcut
        $basePath = '../DigitalMemo/' . $dept_shortcut . '/';
        
        $filePath = $basePath . $filename;
        if (!file_exists($filePath)) {
            return false;
        }

        $file = file_get_contents($filePath);
        return base64_encode($file); // return base64 for API
    }
}
?>