<?php

class Config {
  private static $file = "config.json";

  public static function get($key = 'streams') {
    $config = json_decode(file_get_contents(dirname(__DIR__) . '/' . self::$file));
    return $config->{$key};
  }

  // Get filtered config array by method
  public static function getStreamByMethod($method) {
    $streams = self::get('streams');
    return array_filter(
      $streams,
      function ($item) use ($method) { return $item->method === $method; }
    );
  }

  // Get config object by Google client ID
  public static function getStreamByClientId($client_id) {
    $streams = self::get('streams');
    $index = array_search($client_id, array_column($streams, 'Oauth2ClientID'));
    return $streams[$index];
  }
}
