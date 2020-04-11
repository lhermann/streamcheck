<?php
require_once(__ROOT__ . '/inc/Store.php');
require_once(__ROOT__ . '/inc/helpers.php');

$store = new Store(Store::STATUS);
echo generateStatusReport($store);
