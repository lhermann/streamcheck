<?php
require_once('Config.php');
require_once('Log.php');

class Router {

  private $route = [], $method = '', $params = [];

  public function __construct () {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->route = $this->_explodeRoute($url['path']);
    header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS, HEAD');
    header('Access-Control-Allow-Origin: *');

    if ($this->method === 'OPTIONS') exit();
    if ($this->method === 'HEAD') exit();
  }

  public function notFound () {
    Log::write('404 Not Found', Log::ERROR);
    Log::write($_SERVER, Log::ERROR);
    http_response_code(404);
    print("404 Not Found");
  }

  public function get ($route, $callback = null, $content_type = null, $auth = false){
    if ($this->method !== 'GET') return;
    if ($this->_compareRoute($route)) {
      if ($auth) $this->_requireAuth();
      $this->_serveCallback($callback, $content_type);
      exit();
    }
  }

  public function post ($route, $callback = null, $content_type = null, $auth = false){
    if ($this->method !== 'POST') return;
    if ($this->_compareRoute($route)) {
      if ($auth) $this->_requireAuth();
      $this->_serveCallback($callback, $content_type);
      exit();
    }
  }

  public function delete ($route, $callback = null, $content_type = null, $auth = false){
    if ($this->method !== 'DELETE') return;
    if ($this->_compareRoute($route)) {
      if ($auth) $this->_requireAuth();
      $this->_serveCallback($callback, $content_type);
      exit();
    }
  }

  public function param ($key) {
    return $this->params[$key];
  }

  private function _explodeRoute ($route) {
    return explode('/', trim($route, ' /'));
  }

  private function _compareRoute ($route) {
    foreach ($this->_explodeRoute($route) as $key => $str) {
      if ($str && $str[0] === ':') {
        $this->params[trim($str, ':')] = $this->route[$key];
        continue;
      }
      if ($str === '*') return true;
      if ($this->route[$key] !== $str) return false;
    }
    return count($this->_explodeRoute($route)) === count($this->route);
  }

  private function _serveCallback ($callback, $content_type = null) {
    if (!$callback) echo "Missing callback for route $route";
    try {
      $response = call_user_func($callback, $this);
      if ($content_type === 'json') {
        header('Content-Type: application/json');
        echo $response ? json_encode($response) : '';
      } else {
        echo $response;
      }
    } catch (Exception $e) {
      $message = $e->getMessage();
      $code = 400;
      if (json_decode($message) && property_exists(json_decode($message), 'error')) {
        $error = json_decode($message)->error;
        $message = $error->message;
        $code = $error->code;
      }
      http_response_code($code);
      echo $message;
    }
  }

  private function _requireAuth() {
    $auth = Config::get('auth');
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    if ($_SERVER['HTTP_APP_PASSWORD'] !== $auth->password) {
      http_response_code(401);
      exit;
    }

    // Basic Auth
    // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
    // $is_not_authenticated = (
    //   !$has_supplied_credentials ||
    //   $_SERVER['PHP_AUTH_USER'] != $auth->user ||
    //   $_SERVER['PHP_AUTH_PW']   != $auth->password
    // );
    // if ($is_not_authenticated) {
    //   http_response_code(401);
    //   header('WWW-Authenticate: Basic realm="Streamcheck Access"');
    //   exit;
    // }
  }
}
