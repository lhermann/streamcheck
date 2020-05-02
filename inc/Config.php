<?php

class Config {
  private static $file = "config.json";
  private $config;

  public function __construct() {
    $this->config = json_decode(file_get_contents(__ROOT__ . '/' . self::$file));
  }

  public function getAllStreams() {
    return $this->config->streams;
  }

  public function getStream($id) {
    $streams = $this->getAllStreams();
    $index = array_search($id, array_column($streams, 'id'));
    return $index !== false ? $streams[$index] : null;
  }

  public function getAuthCredentials() {
    return $this->config->auth;
  }

  /* Static Functions */

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
