<?php
require_once('Store.php');

class Google_API {
  private $client, $store, $api;

  public function __construct ($config) {
    // Init Client
    $this->client = new Google_Client();
    $this->client->setApplicationName('Joel Streamcheck');
    $this->client->setDeveloperKey($config->apiKey);
    $this->client->setAuthConfig(__ROOT__.'/'.$config->OAuthCredentialsFile);
    $this->client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/api/v2/auth/callback');
    $this->client->setScopes(['https://www.googleapis.com/auth/youtube']);
    $this->client->prepareScopes();
    $this->client->setAccessType('offline');
    $this->client->setApprovalPrompt('force');

    // Get token from store
    $this->store = new Store(Store::TOKENS);
    $token = $this->store->getValue($this->client->getClientId());
    if ($token) $this->client->setAccessToken($token);

    // renew token if necessary
    if ($this->client->isAccessTokenExpired()) $this->renewToken();
  }

  public function getToken () {
    return $this->client->getAccessToken();
  }

  public function getClientId () {
    return $this->client->getClientId();
  }

  public function getState () {
    return sha1($this->client->getClientId());
  }

  public function getAuthUrl () {
    $this->client->setState($this->getState());
    return $this->client->createAuthUrl();
  }

  public function authenticate ($code) {
    $token = $this->client->fetchAccessTokenWithAuthCode($code);
    $token['refresh_token'] = $this->client->getRefreshToken();
    $this->store->set($this->client->getClientId(), $token);
    return $token;
  }

  public function authenticated () {
    return !$this->client->isAccessTokenExpired();
  }

  public function renewToken () {
    $token = $this->store->getValue($this->client->getClientId());
    if ($token) {
      $new_token = $this->client->fetchAccessTokenWithRefreshToken($token['refresh_token']);
      $this->store->set($this->client->getClientId(), $new_token);
    }
  }

  public function youtube_api () {
    $this->client->authorize();
    return new Google_Service_YouTube($this->client);
  }
}
