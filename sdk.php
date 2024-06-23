<?php

class VauthSDK {
    private $apiBaseUrl = 'https://velvetauth.com/enc/'; 
    private $appId;
    private $secret;
    private $version;

    public function __construct($appId, $secret, $version) {
        $this->appId = $this->encryptData($appId, $secret);
        $this->secret = $secret;
        $this->version = $this->encryptData($version, $secret);
    }

    private function sendRequest($endpoint, $data) {
        $url = $this->apiBaseUrl . $endpoint;
        $data['secret'] = $this->secret;  
        $data['app_id'] = $this->appId;  
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($result === FALSE || $httpcode >= 400) {
            return ['error' => 'Request failed', 'details' => curl_error($ch)];
        }

        curl_close($ch);
        return json_decode($result, true);
    }

    public function init() {
        return $this->sendRequest('index.php', [
            'type' => 'init',
            'version' => $this->version
        ]);
    }

    public function register($username, $password, $licenseKey, $email) {
        return $this->sendRequest('index.php', [
            'type' => 'register',
            'username' => $this->encryptData($username, $this->secret),
            'password' => $this->encryptData($password, $this->secret),
            'license_key' => $this->encryptData($licenseKey, $this->secret),
            'email' => $this->encryptData($email, $this->secret),
            'hwid' => "" // Set HWID as null
        ]);
    }

    public function AuthReg($username, $password, $email) {
        return $this->sendRequest('index.php', [
            'type' => 'keyless',
            'username' => $this->encryptData($username, $this->secret),
            'password' => $this->encryptData($password, $this->secret),
            'email' => $this->encryptData($email, $this->secret),
            'hwid' => "" // Set HWID as null
        ]);
    }

    public function login($username, $password) {
        return $this->sendRequest('index.php', [
            'type' => 'login',
            'username' => $this->encryptData($username, $this->secret),
            'password' => $this->encryptData($password, $this->secret),
            'hwid' => ""  
        ]);
    }

    public function extendExpiry($username, $licenseKey) {
        return $this->sendRequest('index.php', [
            'type' => 'extend_expiry',
            'username' => $this->encryptData($username, $this->secret),
            'license_key' => $this->encryptData($licenseKey, $this->secret)
        ]);
    }

    private function encryptData($data, $keyHex) {
        $key = hex2bin($keyHex); 
        $iv = openssl_random_pseudo_bytes(16); 
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $encryptedData = base64_encode($iv . $encrypted); 
        return $encryptedData;
    }

    private function decryptData($data, $keyHex) {
        $key = hex2bin($keyHex);
        $data = base64_decode($data); 
        $iv = substr($data, 0, 16);
        $encryptedData = substr($data, 16);
        $decrypted = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}
?>