<?php

class Log {
  private static $dir = __ROOT__ . "/log/";
  private $name;

  public const ERROR = 'error';
  public const CHECK = 'check';

  public function __construct($name = self::ERROR) {
    // $this->file = self::file($file);
    $this->name = $name;
  }

  public function log ($string) {
    self::write($string, $this->name);
  }

  /* Static Functions */

  public static function file($name = self::ERROR) {
    return self::$dir . $name . ".log";
  }

  public static function write($string, $name = self::ERROR) {
    return file_put_contents(
      self::file($name),
      sprintf( "[%s] %s\n",
        date("Y-m-d H:i:s"),
        is_string($string) ? $string : print_r($string, true)
      ),
      FILE_APPEND
    );
  }
}
