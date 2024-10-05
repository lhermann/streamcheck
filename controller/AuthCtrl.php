<?php
require_once(__ROOT__ . '/inc/constants.php');
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Google_API.php');

class AuthCtrl {
  public static function get () {
    $config = Config::getStreamByMethod(METHODS::YOUTUBE);
    $client = new Google_API($config);

    $token = $client->getToken();
    $expires = null;
    if ($token !== null && is_array($token)) {
      if (isset($token['created']) && isset($token['expires_in'])) {
        $expires = date('c', $token['created'] + $token['expires_in']);
      }
    } else {
      error_log('Token is null or not an array in AuthCtrl::get()');
    }

    return [
      'id' => $config->id,
      'name' => $config->name,
      'authenticated' => $client->authenticated(),
      'expires' => $expires,
      'client_id' => $client->getClientId(),
      'auth_url' => $client->getAuthUrl(),
    ];
  }

  public static function callback () {
    $code = array_key_exists('code', $_GET) ? $_GET['code'] : NULL;
    $state = array_key_exists('state', $_GET) ? $_GET['state'] : NULL;
    if (!$code || !$state) throw new Exception("'code' and 'state' query parameters required");

    // find config
    $config = Config::getStreamByMethod(METHODS::YOUTUBE);
    $client = new Google_API($config);
    $client->authenticate($code);
    header('Location: //' . $_SERVER['HTTP_HOST'] . '/ui/');
  }

  public static function check () {
    $config = Config::getStreamByMethod(METHODS::YOUTUBE);

    // If $config is an array, take the first item
    if (is_array($config)) {
      if (empty($config)) {
        throw new Exception('No YouTube streams configured');
      }
      $config = $config[0];
    } elseif (!is_object($config)) {
      throw new Exception('Invalid configuration returned');
    }

    $client = new Google_API($config);
    $token = $client->getToken();

    $status = [
      'id' => $config->id,
      'name' => $config->name,
      'authenticated' => $client->authenticated(),
      'expires' => null,
      'valid' => false
    ];

    if ($token !== null && is_array($token)) {
      if (isset($token['created']) && isset($token['expires_in'])) {
        $expirationTime = $token['created'] + $token['expires_in'];
        $status['expires'] = date('c', $expirationTime);
        $status['valid'] = time() < $expirationTime;
      }
    }

    if (!$status['authenticated'] || !$status['valid']) {
      $status['auth_url'] = $client->getAuthUrl();
    }

    return $status;
  }
}
