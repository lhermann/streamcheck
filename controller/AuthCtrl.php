<?php
require_once(__ROOT__ . '/inc/constants.php');
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Google_API.php');

class AuthCtrl {
  public static function get () {
    return array_map(
      'self::_mapTokens',
      Config::getStreamByMethod(METHODS::YOUTUBE)
    );
  }

  public static function callback () {
    $code = $_GET['code'];
    $state = $_GET['state'];
    if (!$code || !$state) throw new Exception('code and state required');

    // find config
    $config_list = Config::getStreamByMethod(METHODS::YOUTUBE);
    $filtered_list = array_filter($config_list, function ($item) {
      return sha1($item->id) === $_GET['state'];
    });

    // authenticate
    if (count($filtered_list)) {
      $config = $filtered_list[0];
      $client = new Google_API($config);
      $client->authenticate($_GET['code']);
      header('Location: //' . $_SERVER['HTTP_HOST'] . '/ui/');
    } else {
      throw new Exception('The session state did not match.');
    }
  }

  private static function _mapTokens ($config) {
    $client = new Google_API($config);
    $token = $client->getToken();
    return [
      'id' => $config->id,
      'name' => $config->name,
      'authenticated' => $client->authenticated(),
      'expires' => date('c', $token['created'] + $token['expires_in']),
      'auth_url' => $client->getAuthUrl(),
    ];
  }
}

