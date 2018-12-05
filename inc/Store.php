<?php

class Store {
    private static $dir = "store/";
    public $id, $updated, $live, $streams;

    public function __construct($id = null) {
        $this->id = $id ?: "misc";
        $obj = $this->read();
        $this->updated = time();
        $this->live = $obj->live ?: false;
        $this->stream_key = $obj->stream_key ?: [];
        $this->stream_url = $obj->stream_url ?: [];
        $this->stream_live = $obj->stream_live ?: [];
    }

    public static function file($name = null) {
        return self::$dir . ($name ?: "misc") . ".json";
    }

    public static function file_exists($name) {
        return file_exists(self::file($name));
    }

    public function write() {
        return file_put_contents(
            self::file($this->id),
            json_encode($this)
        );
    }

    public function read() {
        $file = self::file($this->id);
        if(file_exists($file)) {
            return json_decode(
                file_get_contents(self::file($this->id))
            );
        }
        return null;
    }

    // /*
    //  * Static Functions
    //  */
    // public static function file($name = null) {
    //     return self::$dir . ($name ?: "misc") . ".json";
    // }


    // public static function write($object, $name = null) {
    //     return file_put_contents(
    //         self::file($name),
    //         json_encode($object),
    //         FILE_APPEND
    //     );
    // }

}
