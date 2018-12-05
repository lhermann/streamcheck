<?php

class Log {
    private static $dir = "log/";

    public static function file($name = "check") {
        return self::$dir . $name . ".log";
    }

    public static function write($string) {
        return file_put_contents(
            self::file(),
            sprintf( "[%s] %s\n",
                date("Y-m-d H:i:s"),
                is_string($string) ? str_replace("\n", "", $string) : print_r($string, true)
            ),
            FILE_APPEND
        );
    }
}
