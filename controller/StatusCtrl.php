<?php
require_once(__ROOT__ . '/inc/Config.php');
require_once(__ROOT__ . '/inc/Check.php');
require_once(__ROOT__ . '/inc/Store.php');
require_once(__ROOT__ . '/inc/helpers.php');

class StatusCtrl {
  public static function getStatus () {
    $store = new Store(Store::STATUS);
    return generateStatusReport($store);
  }

  public static function checkStatus () {
    $config_list = Config::get();
    $store = new Store(Store::STATUS);
    $store->clear();
    $results = [];

    foreach ($config_list as $config) {
      // perform apropriate check per method
      $result = Check::__($config);

      // store result
      $store->set($config->name, $result);
    }

    return generateStatusReport($store);
  }

  public static function toggleManualStatus () {
    $store = new Store(Store::STATUS);
    $store->set('Manual Stream', (bool) !$store->getValue('Manual Stream'));
    return generateStatusReport($store);
  }
}


