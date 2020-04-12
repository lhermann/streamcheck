<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');


// Setup
define('__ROOT__', dirname(__FILE__));
require_once __ROOT__ . '/vendor/autoload.php';
session_start();


// Get route
$url = parse_url($_SERVER['REQUEST_URI']);
$route = explode('/', trim($url['path'], ' /'));
if (array_key_exists('query', $url)) parse_str($url['query'], $output);
$_SESSION['query'] = isset($output) ? $output : [];


// Router
switch (array_shift($route)) {
  case '':
    return require __DIR__ . '/info.php';
  case 'api':
    return routeApi($route);
  default:
    return route404();
}


// Router Functions
function routeApi ($route) {
  header('Content-Type: application/json');

  switch ($route) {
    case ['v1']:
      return require __DIR__ . '/api/v1/index.php';
    case ['v1', 'check']:
      return require __DIR__ . '/check.php';
    case ['v2']:
      return require __DIR__ . '/api/v2/index.json';
    case ['v2', 'auth']:
      return require __DIR__ . '/api/v2/auth.php';
    case ['v2', 'auth', 'callback']:
      return require __DIR__ . '/api/v2/auth_callback.php';
    case ['v2', 'check']:
      return require __DIR__ . '/api/v2/check.php';
    case ['v2', 'status']:
      return require __DIR__ . '/api/v2/status.php';
    default:
      return require __DIR__ . '/api/index.json';
  }
}

function route404 () {
  http_response_code(404);
  print("404");
}
