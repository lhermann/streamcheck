<?php
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Check.php');
require_once(__ROOT__ . '/inc/Store.php');
require_once(__ROOT__ . '/inc/helpers.php');

class StatusCtrl {
  public static function get () {
    $store = new Store(Store::STATUS);
    return generateStatusReport($store);
  }

  public static function check () {
    $config_list = Config::get();
    $store = new Store(Store::STATUS);

    foreach ($config_list as $config) {
      // perform apropriate check per method
      $result = Check::__($config, $store->getValue($config->name));

      // store result
      $store->set($config->name, $result);
    }

    return self::_generateStatusReport($store);
  }

  public static function remove () {
    $name = $_GET['name'];
    if (!$name) throw new Exception('Missing argument: name');
    $store = new Store(Store::STATUS);
    $store->remove($name);
    return self::_generateStatusReport($store);
  }

  public static function toggle () {
    $name = $_GET['name'];
    if (!$name) throw new Exception('Missing argument: name');
    $store = new Store(Store::STATUS);
    $store->set($name, (bool) !$store->getValue($name));
    return self::_generateStatusReport($store);
  }

  private static function _generateStatusReport (Store $store) {
    $all_statuses = $store->getAll();
    // $report = array_map('extractValue', $all_statuses);
    $streams = [];
    foreach ($store->getAll() as $key => $value) {
      $streams[] = [
        'name' => $key,
        'live' => $value->value,
        'updated' => $value->updated
      ];
    }
    return [
      'streams' => $streams,
      'live' => count(array_filter($store->getAll(), 'self::_extractValue')) > 0
    ];
  }

  private static function _extractValue (Value $value) {
    return $value->value;
  }
}


