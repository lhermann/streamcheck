<?php

class Config {
    private static $file = "config.json";

    public static function get($stream = null) {
        $config = json_decode(
            file_get_contents(self::$file)
        );

        if($stream && property_exists($config, $stream)) {
            return $config->$stream;
        } else {
            return $config;
        }
    }
}
