<?php
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Check.php');
require_once(__ROOT__ . '/inc/Store.php');
require_once(__ROOT__ . '/inc/helpers.php');

class StreamsCtrl {
  public static function getAll ($router) {
    $config = new Config();
    $store = new Store(Store::STATUS);

    $streams = [];
    foreach ($config->getAllStreams() as $stream) {
      $status = $store->get($stream->id);
      $streams[] = [
        'id' => $stream->id,
        'name' => $stream->name,
        'live' => $status->value,
        'updated' => $status->updated,
      ];
    }

    return $streams;
  }

  public static function get ($router) {
    $config = new Config();
    $store = new Store(Store::STATUS);
    $id = $router->param('id');
    $status = $store->get($id);

    return [
      'id' => $id,
      'name' => $config->getStream($id)->name,
      'live' => $status->value,
      'updated' => $status->updated,
    ];
  }
}


