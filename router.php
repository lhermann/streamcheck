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
require_once __ROOT__ . '/controller/StreamsCtrl.php';
require_once __ROOT__ . '/controller/AuthCtrl.php';
require_once __ROOT__ . '/controller/StatusCtrl.php';


// Get route
// $url = parse_url($_SERVER['REQUEST_URI']);
// $route = explode('/', trim($url['path'], ' /'));
// if (array_key_exists('query', $url)) parse_str($url['query'], $output);
// $_SESSION['query'] = isset($output) ? $output : [];


$router->get('',                        'getRoot');
$router->get('api',                     'getApi', 'json');
$router->get('api/v1',                  'getApiV1', 'json');
$router->get('api/v1/streams',          'StreamsCtrl::getAll', 'json');
$router->get('api/v1/streams/:id',      'StreamsCtrl::get', 'json');
$router->get('api/v1/auth',             'AuthCtrl::get', 'json');
$router->get('api/v1/auth/callback',    'AuthCtrl::callback', 'json');
$router->get('api/v1/status',           'StatusCtrl::get', 'json');
$router->get('api/v1/status/check',     'StatusCtrl::checkAll', 'json');
$router->get('api/v1/status/check/:id', 'StatusCtrl::check', 'json');
$router->post('api/v1/status/toggle/:id', 'StatusCtrl::toggle', 'json', true);
$router->delete('api/v1/status/remove/:id', 'StatusCtrl::remove', 'json', true);
$router->notFound();

exit();


function getRoot () {
  return header('Location: /ui/');
}

function getApi () {
  require __ROOT__ . '/inc/index.json';
}

function getApiV1 () {
  require __ROOT__ . '/inc/v1.json';
}
