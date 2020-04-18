<?php
// php.ini
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Setup
define('__ROOT__', dirname(__FILE__));
require_once __ROOT__ . '/vendor/autoload.php';
require_once __ROOT__ . '/inc/Router.php';
date_default_timezone_set('Europe/Berlin');
session_start();
header("Access-Control-Allow-Origin: *");
$router = new Router();

// require controllers
require_once __ROOT__ . '/controller/AuthCtrl.php';
require_once __ROOT__ . '/controller/StatusCtrl.php';


// Get route
// $url = parse_url($_SERVER['REQUEST_URI']);
// $route = explode('/', trim($url['path'], ' /'));
// if (array_key_exists('query', $url)) parse_str($url['query'], $output);
// $_SESSION['query'] = isset($output) ? $output : [];


$router->get('',                             'getRoot');
$router->get('api',                          'getApi', 'json');
$router->get('api/v1',                       'getApiV1', 'json');
$router->get('api/v1/auth',                  'AuthCtrl::getStatus', 'json');
$router->get('api/v1/auth/callback',         'AuthCtrl::getCallback', 'json');
$router->get('api/v1/status',                'StatusCtrl::getStatus', 'json');
$router->get('api/v1/status/check',          'StatusCtrl::checkStatus', 'json');
$router->get('api/v1/status/toggle-manual',  'StatusCtrl::toggleManualStatus', 'json', true);
$router->notFound();

exit();


function getRoot () {
  return header('Location: /ui/');
}

function getApi () {
  require __ROOT__ . '/api/index.json';
}

function getApiV1 () {
  require __ROOT__ . '/api/v1.json';
}


// Router
// switch (array_shift($route)) {
//   case '':
//     return require __DIR__ . '/info.php';
//   case 'api':
//     return routeApi($route);
//   default:
//     return route404();
// }


// Router Functions
// function routeApi ($route) {
//   header('Content-Type: application/json');

//   switch ($route) {
//     case ['v1']:
//       return require __DIR__ . '/api/v1/index.php';
//     case ['v1', 'check']:
//       return require __DIR__ . '/check.php';
//     case ['v2']:
//       return require __DIR__ . '/api/v2/index.json';
//     case ['v2', 'auth']:
//       return require __DIR__ . '/api/v2/auth.php';
//     case ['v2', 'auth', 'callback']:
//       return require __DIR__ . '/api/v2/auth_callback.php';
//     case ['v2', 'check']:
//       return require __DIR__ . '/api/v2/check.php';
//     case ['v2', 'status']:
//       return require __DIR__ . '/api/v2/status.php';
//     default:
//       return require __DIR__ . '/api/index.json';
//   }
// }

// function route404 () {
//   http_response_code(404);
//   print("404");
// }
