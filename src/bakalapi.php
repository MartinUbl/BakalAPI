<?php

namespace Martinubl\Bakalapi;

require "baka_errors.php";

class BakalAPI {

    /** @var \GuzzleHttp\Client */
    private $client = null;

    /** @var string */
    private $accessToken = "";

    /** @var int */
    private $accessTokenExpiresAt = 0;

    /** @var string */
    private $urlSuffix = "";
    
    /** @var bool */
    private $unauthorized = false;

    /** @var Baka_UserInfo */
    private $userInfo = null;

    /** @var bool */
    private $storedInSession = false;

    public function __construct($url, $urlSuffix = "") {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $url,
            'timeout'  => 2.0,
        ]);

        $this->urlSuffix = $urlSuffix;
    }

    public function login(string $username, string $password) : int {

        $response = $this->client->request('POST', $this->urlSuffix.'/api/login', [
            'form_params' => [
                'client_id' => 'ANDR',
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password
            ],
            'http_errors' => false
        ]);

        if ($response->getStatusCode() != 200) {
            return Baka_LoginError::INVALID;
        }

        $parsed = json_decode($response->getBody(), true);
        if (!$parsed || empty($parsed)) { 
            return Baka_LoginError::SERVER_ERROR;
        }

        if (!isset($parsed['access_token'])) {
            return Baka_LoginError::INVALID;
        }

        $this->accessToken = $parsed['access_token'];
        $this->accessTokenExpiresAt = time() + $parsed['expires_in'];
        $this->unauthorized = false;

        return Baka_LoginError::OK;
    }

    public function hasToken() {
        return !empty($this->accessToken) && $this->accessTokenExpiresAt > time();
    }

    public function useToken(string $accessToken, int $expiresIn) {
        $this->accessToken = $accessToken;
        $this->accessTokenExpiresAt = time() + $expiresIn;
    }

    public function getToken() : string {
        return $this->accessToken;
    }

    protected function startSessionIfNeeded() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function saveToSession() : bool {
        $this->startSessionIfNeeded();

        $_SESSION['BakalAPI_access_token'] = $this->accessToken;
        $_SESSION['BakalAPI_access_token_expiry'] = $this->accessTokenExpiresAt;
        $this->storedInSession = true;
        return true;
    }

    public function restoreFromSession() : bool {
        $this->startSessionIfNeeded();

        if (!isset($_SESSION['BakalAPI_access_token']) || empty($_SESSION['BakalAPI_access_token'])) {
            return false;
        }

        if (!isset($_SESSION['BakalAPI_access_token_expiry']) || empty($_SESSION['BakalAPI_access_token_expiry']) || $_SESSION['BakalAPI_access_token_expiry'] < time()) {
            return false;
        }

        $this->accessToken = $_SESSION['BakalAPI_access_token'];
        $this->accessTokenExpiresAt = $_SESSION['BakalAPI_access_token_expiry'];
        $this->unauthorized = false;
        $this->storedInSession = true;
        return true;
    }

    private function request($method, $endpoint, $parameters = []) {
        if (!$this->hasToken()) {
            $this->unauthorized = true;
            throw new Baka_SessionExpiredException("The session has expired");
        }

        $response = $this->client->request($method, $this->urlSuffix.$endpoint, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken
            ],
            'form_params' => $parameters,
            'http_errors' => false
        ]);

        if ($response->getStatusCode() == 401) {
            $this->unauthorized = true;
            $this->accessToken = "";
            if ($this->storedInSession) {
                $this->saveToSession();
                $this->storedInSession = false;
            }
            throw new Baka_SessionExpiredException("The session has expired");
        }

        $this->unauthorized = false;

        return $response;
    }

    public function getUserInfo($force = false) : Baka_UserInfo | null {

        if ($this->userInfo !== null && !$force) {
            return $this->userInfo;
        }

        if (!$this->hasToken()) {
            return null;
        }

        $response = $this->request('GET', '/api/3/user');

        if (!$response || $response->getStatusCode() != 200) {
            return null;
        }

        $data = json_decode((string) $response->getBody());

        foreach ($data as $key => $value) {
            echo $key.' => '.json_encode($value).' <br/>';
        }

        $this->userInfo = new Baka_UserInfo();
        
        $this->userInfo->uid = $data['UserUID'];
        $this->userInfo->fullName = $data['FullName'];
        
        if ($data['UserType'] == "teacher")
            $this->userInfo->role = Baka_UserType::TEACHER;

        $this->userInfo->studyClass = $data["Class"];

        return $this->userInfo;
    }
};
