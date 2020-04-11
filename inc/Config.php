<?php

class Config {
  private static $file = "config.json";

  public static function get() {
    return json_decode(file_get_contents(dirname(__DIR__) . '/' . self::$file));
  }

  // Get filtered config array by method
  public static function get_by_method($method) {
    $config_list = self::get();
    return array_filter(
      $config_list,
      function ($item) use ($method) { return $item->method === $method; }
    );
  }

  // Get config object by Google client ID
  public static function get_by_client_id($client_id) {
    $config_list = self::get();
    $index = array_search($client_id, array_column($config_list, 'Oauth2ClientID'));
    return $config_list[$index];
  }
}
