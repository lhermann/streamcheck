<?php
require_once("inc/Log.php");
require_once("inc/Store.php");
require_once("inc/Config.php");

foreach (Config::get() as $streamid => $streams) {
    $store = new Store($streamid);

    $i = 0;
    foreach ($streams as $key => $url) {
        $store->stream_key[$i] = $key;
        $store->stream_url[$i] = $url;
        Log::write("timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i $url 2>&1");
        $exec = shell_exec("timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i $url 2>&1");
        if($exec[0] !== '{') Log::write($exec);
        $json = json_decode( $exec );
        $store->stream_live[$i] = is_object($json) && isset($json->streams);
        $i++;
    }

    $store->live = in_array(true, $store->stream_live);
    $store->write();
}
