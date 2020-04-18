<?php

/**
 * Simple value object
 */
class Value {
  public $value, $created, $updated;

  public function __construct(
    $value = null,
    $created = null,
    $updated = null
  ) {
    $this->value = $value;
    $this->created = $created ?: date('c');
    $this->updated = $updated ?: date('c');
  }

  public function set ($value) {
    $this->value = $value;
    $this->updated = date('c');
  }

  public function get () {
    return $this;
  }
}

/**
 * Simple key-value file storrage
 */
class Store {
  private const DIR = __DIR__ . '/../store/';

  public const TOKENS = 'tokens';
  public const STATUS = 'status';
  public const MISC = 'misc';

  private $namespace, $store = null;

  public function __construct($namespace = self::MISC) {
    $this->namespace = $namespace;
    $this->read();
  }

  /*
   * Public Functions
   */
  public function getAll () {
    return $this->store;
  }

  public function get($key) {
    if (isset($this->store[$key])) {
      return $this->store[$key];
    } else {
      $value = new Value();
      $this->store[$key] = $value;
      return $value;
    }
  }

  public function getValue($key) {
    return $this->get($key)->value;
  }

  public function set($key, $value) {
    $instance = $this->get($key);
    $instance->set($value);
    $this->write();
  }

  public function remove ($key) {
    unset($this->store[$key]);
    $this->write();
  }

  public function clear () {
    $this->store = [];
  }

  /*
   * Private Functions
   */
  private function read() {
    $file = $this->filename();
    if(file_exists($file)) {
      $entries = json_decode(file_get_contents($file), true);
      $this->store = array_map(function ($item) {
        return new Value($item['value'], $item['created'], $item['updated']);
      }, $entries);
    } else {
      $this->store = [];
      $this->write();
    }
  }

  private function write() {
    return file_put_contents(
      $this->filename(),
      json_encode($this->store)
    );
  }

  private function filename() {
    return self::DIR . $this->namespace . '.json';
  }
}
