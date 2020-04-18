<?php
require_once(__ROOT__ . '/inc/constants.php');
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Google_API.php');

class AuthCtrl {
  public static function getStatus () {
    return array_map(
      'self::_mapTokens',
      Config::getStreamByMethod(METHODS::YOUTUBE)
    );
  }

  public static function getCallback () {
    $code = $_GET['code'];
    $state = $_GET['state'];
    if (!$code || !$state) return new Error('code and state required');

    // find config
    $config_list = Config::getStreamByMethod(METHODS::YOUTUBE);
    $filtered_list = array_filter($config_list, function ($item) {
      return sha1($item->Oauth2ClientID) === $_GET['state'];
    });

    // authenticate
    if (count($filtered_list)) {
      $config = $filtered_list[0];
      $client = new Google_API($config);
      $client->authenticate($_GET['code']);
      header('Location: //' . $_SERVER['HTTP_HOST'] . '/api/v2/auth');
    } else {
      return new Error('The session state did not match.');
    }
  }

  private static function _mapTokens ($config) {
    $client = new Google_API($config);
    $token = $client->getToken();
    return [
      'name' => $config->name,
      'Oauth2ClientID' => $client->getClientId(),
      'authenticated' => $client->authenticated(),
      // 'token' => $token,
      'expires' => date('c', $token['created'] + $token['expires_in']),
      'auth_url' => $client->getAuthUrl()
    ];
  }
}

