<?php
require_once(__ROOT__ . '/inc/Store.php');

function generateStatusReport (Store $store) {
  $all_statuses = $store->getAll();
  $report = array_map('extractValue', $all_statuses);
  $report['any'] = count(array_filter($all_statuses, 'extractValue')) > 0;
  return json_encode($report);
}

function extractValue (Value $value) {
  return $value->value;
}
