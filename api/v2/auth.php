<?php
require_once(__ROOT__ . '/inc/constants.php');
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Google_API.php');


if (isset($_SESSION['query']['client_id'])) {
  $config = Config::get_by_client_id($_SESSION['query']['client_id']);
  $client = new Google_API($config);
  var_dump($client, $_SERVER['REQUEST_URI']);
} else {
  echo json_encode(array_map(
    'mapTokens',
    Config::get_by_method(METHODS::YOUTUBE)
  ));
}

function mapTokens ($config) {
  $client = new Google_API($config);
  $token = $client->getToken();
  return [
    'Oauth2ClientID' => $client->getClientId(),
    'authenticated' => $client->authenticated(),
    'token' => $token,
    'expires' => date('c', $token->created),
    'auth_url' => $client->getAuthUrl()
  ];
}
