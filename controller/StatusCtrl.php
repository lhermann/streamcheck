<?php
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Check.php');
require_once(__ROOT__ . '/inc/Store.php');
require_once(__ROOT__ . '/inc/helpers.php');

class StatusCtrl {
  public static function get () {
    $store = new Store(Store::STATUS);
    return [
      "live" => count(array_filter($store->getAll(), fn ($value) => $value->value)) > 0
    ];
  }

  public static function checkAll () {
    $config = new Config();
    $store = new Store(Store::STATUS);

    $streams = [];
    foreach ($config->getAllStreams() as $stream) {
      // perform apropriate check per method
      $result = Check::__($stream, $store->getValue($stream->id));

      // store result
      $store->set($stream->id, $result);
    }

    return self::_generateStatusReport($store);
  }

  public static function check ($router) {
    $config = new Config();
    $store = new Store(Store::STATUS);
    $id = $router->param('id');
    $stream = $config->getStream($id);

    // perform apropriate check per method
    $result = Check::__($stream, $store->getValue($id));

    // store result
    $value = $store->set($id, $result);

    return [
      'id' => $id,
      'live' => $value->value,
      'updated' => $value->updated,
    ];
  }

  public static function remove ($router) {
    $id = $router->param('id');
    if (!$id) throw new Exception('Missing argument: id');
    $store = new Store(Store::STATUS);
    $store->remove($id);
    return true;
  }

  public static function toggle ($router) {
    $id = $router->param('id');
    if (!$id) throw new Exception('Missing argument: id');
    $store = new Store(Store::STATUS);
    $new_value = (bool) !$store->getValue($id);
    $value = $store->set($id, $new_value);
    return [
      'id' => $id,
      'live' => $value->value,
      'updated' => $value->updated,
    ];
  }

  private static function _generateStatusReport (Store $store) {
    $streams = [];
    foreach ($store->getAll() as $key => $value) {
      $streams[] = [
        'id' => $key,
        'live' => $value->value,
        'updated' => $value->updated,
      ];
    }
    return $streams;
  }

  private static function _extractValue (Value $value) {
    return $value->value;
  }
}


