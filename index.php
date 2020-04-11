<?php
// require_once("inc/Store.php");

// $query = explode('/', $_SERVER['QUERY_STRING']);
// $streamid = end($query);

// if(Store::file_exists($streamid)) {
//     header('Content-Type: application/json');
//     print(file_get_contents(Store::file($streamid)));
// } elseif(!$streamid) {
//     header('Content-Type: application/json');
//     print(json_encode(Store::read_all()));
// } else {
//     http_response_code(404);
//     print("404");
// }

$request = $_SERVER['REQUEST_URI'];

// var_dump($request, preg_match('/^\/api/', $request));

switch ($request) {
  case '/':
    require __DIR__ . '/info.php';
    break;
  case preg_match('/^\/api\/v1/', $request) > 0:
    header('Content-Type: application/json');
    require __DIR__ . '/api/v1.php';
    break;
  case preg_match('/^\/api\/v2/', $request) > 0:
    header('Content-Type: application/json');
    require __DIR__ . '/api/v2.php';
    break;
  case preg_match('/^\/api/', $request) > 0:
    header('Content-Type: application/json');
    require __DIR__ . '/api/main.json';
    break;
  default:
    http_response_code(404);
    print("404");
    break;
}
