<?php
require_once(__ROOT__ . '/inc/Store.php');

function generateStatusReport (Store $store) {
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
  $report = [
    'streams' => $streams,
    'live' => count(array_filter($store->getAll(), 'extractValue')) > 0
  ];
  return json_encode($report);
}

function extractValue (Value $value) {
  return $value->value;
}
