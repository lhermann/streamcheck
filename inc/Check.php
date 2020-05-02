<?php
require_once('constants.php');
require_once('Log.php');
require_once('Google_API.php');

class Check {
  public static function __($config, $value = null) {
    switch ($config->method) {
      case METHODS::MANUAL: return self::manual($config, $value);
      case METHODS::CURL: return self::curl($config);
      case METHODS::YOUTUBE: return self::youtube($config);
    }
  }

  public static function manual ($config, $value = null) {
    return (bool) ($value !== null ? $value : false);
  }

  public static function curl ($config) {
    try {
      $exec_str = "timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i $config->url 2>&1";
      Log::write($exec_str, Log::CHECK);
      $json = json_decode(shell_exec($exec_str));
      return is_object($json) && isset($json->streams);
    } catch (Exception $e) {
      Log::write($e->getMessage(), Log::ERROR);
      throw $e;
    }
  }

  public static function youtube ($config) {
    $client = new Google_API($config);
    $api = $client->youtube_api();
    try {
      $response = $api->liveBroadcasts->listLiveBroadcasts(
        'id,snippet,status',
        ['broadcastStatus' => 'active']
      );
      return count($response->items) > 0;
    } catch (Exception $e) {
      Log::write($e->getMessage(), Log::ERROR);
      throw $e;
    }
  }
}
