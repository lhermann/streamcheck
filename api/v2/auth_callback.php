<?php
require_once(__ROOT__ . '/inc/constants.php');
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Google_API.php');

if (isset($_GET['code'])) {
  // find config
  $config_list = Config::get_by_method(METHODS::YOUTUBE);
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
    die('The session state did not match.');
  }
}
