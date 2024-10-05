<?php
require_once('Store.php');
require_once('Log.php');

class Google_API {
  private $config;
  private $client;
  private $store;
  private $api;

  public function __construct ($config) {
    $this->config = $config;
    $this->store = new Store(Store::TOKENS);

    // Init Client
    $this->client = new Google\Client();
    $this->client->setApplicationName('Joel Streamcheck');
    $this->client->setAuthConfig(__ROOT__ . '/' . $config->OAuthCredentialsFile);
    $this->client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/api/v1/auth/callback');
    $this->client->setScopes([Google\Service\YouTube::YOUTUBE]);
    $this->client->prepareScopes();
    $this->client->setAccessType('offline');
    $this->client->setApprovalPrompt('force');

    // Get token from store
    $token = $this->store->getValue($this->config->id);
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
    return sha1($this->config->id);
  }

  public function getAuthUrl () {
    $this->client->setState($this->getState());
    return $this->client->createAuthUrl();
  }

  public function authenticate ($code) {
    try {
      $token = $this->client->fetchAccessTokenWithAuthCode($code);
      $token['refresh_token'] = $this->client->getRefreshToken();
      $this->store->set($this->config->id, $token);
      return $token;
    } catch (Exception $e) {
      Log::write($e->getMessage(), Log::ERROR);
      throw $e;
    }
  }

  public function authenticated () {
    return !$this->client->isAccessTokenExpired();
  }

  public function renewToken () {
    try {
      $token = $this->store->getValue($this->config->id);
      if (!$token || !array_key_exists('refresh_token', $token)) return;
      $new_token = $this->client->fetchAccessTokenWithRefreshToken(
        $token['refresh_token']
      );
      $this->store->set($this->config->id, $new_token);
    } catch (Exception $e) {
      Log::write($e->getMessage(), Log::ERROR);
      throw $e;
    }
  }

  public function youtube_api () {
    $this->client->authorize();
    return new Google\Service\YouTube($this->client);
  }
}
