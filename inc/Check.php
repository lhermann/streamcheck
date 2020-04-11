<?php
require_once('constants.php');
require_once('Log.php');
require_once('Google_API.php');

class Check {
  public static function __($config) {
    switch ($config->method) {
      case METHODS::MANUAL: return self::manual($config);
      case METHODS::CURL: return self::curl($config);
      case METHODS::YOUTUBE: return self::youtube($config);
    }
  }

  public static function manual ($config) {
    return (bool) $config->live;
  }

  public static function curl ($config) {
    $exec_str = "timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i $config->url 2>&1";
    Log::write($exec_str);
    $exec = shell_exec($exec_str);
    if($exec[0] !== '{') Log::write($exec);
    $json = json_decode($exec);
    return is_object($json) && isset($json->streams);
  }

  public static function youtube ($config) {
    $client = new Google_API($config);
    $api = $client->youtube_api();
    $response = $api->liveBroadcasts->listLiveBroadcasts(
      'id,snippet,status',
      ['broadcastStatus' => 'active']
    );
    return count($response->items) > 0;
  }
}
