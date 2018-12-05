<?php
require_once("inc/Store.php");

$query = explode('/', $_SERVER['QUERY_STRING']);
$streamid = end($query);

if(Store::file_exists($streamid)) {
    header('Content-Type: application/json');
    print(file_get_contents(Store::file($streamid)));
} else {
    http_response_code(404);
    print("404");
}
