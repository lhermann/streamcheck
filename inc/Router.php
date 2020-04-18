<?php
require_once(__ROOT__ . '/inc/Config.php');

class Router {

  private $route = [], $method = '';

  public function __construct () {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->route = $this->_explodeRoute($url['path']);
  }

  public function notFound () {
    http_response_code(404);
    print("<h1>404 Not Found</h1>");
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

  private function _explodeRoute ($route) {
    return explode('/', trim($route, ' /'));
  }

  private function _compareRoute ($route) {
    foreach ($this->_explodeRoute($route) as $key => $value) {
      if ($value === '*') return true;
      if ($this->route[$key] !== $value) return false;
    }
    return count($this->_explodeRoute($route)) === count($this->route);
  }

  private function _serveCallback ($callback, $content_type = null) {
    if (!$callback) echo "Missing callback for route $route";
    try {
      $response = call_user_func($callback);
      if ($content_type === 'json') {
        header('Content-Type: application/json');
        echo $response ? json_encode($response) : '';
      } else {
        echo $response;
      }
    } catch (Exception $e) {
      echo 'Caught exception: ', $e->getMessage();
    }
  }

  private function _requireAuth() {
    $auth = Config::get('auth');
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
    $is_not_authenticated = (
      !$has_supplied_credentials ||
      $_SERVER['PHP_AUTH_USER'] != $auth->user ||
      $_SERVER['PHP_AUTH_PW']   != $auth->password
    );
    if ($is_not_authenticated) {
      header('HTTP/1.1 401 Authorization Required');
      header('WWW-Authenticate: Basic realm="Streamcheck Access"');
      exit;
    }
  }
}
