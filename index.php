<?php
/**
 * DUMMY
 */
$path = explode('/', $_SERVER['QUERY_STRING']);
$streamid = end($path);

class Check {
    public $streamid;
    public $last_check;
    public $live = false;

    function __construct($streamid = "all") {
        $this->streamid = $streamid;
        $this->last_check = time();
    }
}

$return = new Check($streamid);

switch ($streamid) {
    case 'joelmedia':
        $return->live = true;
        break;
    default:
        # code...
        break;
}

header('Content-Type: application/json');
print( json_encode($return) );


// $exec = "timeout 10s /usr/local/bin/ffprobe -v quiet -print_format json -show_format -show_streams -i " . $stream . " 2>&1";
// $temp = shell_exec ( $exec );
// $json = json_decode( $temp );

// timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i https://streamer1.streamhost.org:443/salive/joelmediatv/playlist.m3u8
// timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i rtmp://streamer1.streamhost.org/salive/joelmediatv
// timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i rtmp://live.stream.joelmediatv.de:1935/live/joelmedia
// timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i https://streamer1.streamhost.org/salive/lctvde/playlist.m3u8
// timeout 10s ffprobe -v quiet -print_format json -show_format -show_streams -i rtmp://streamer1.streamhost.org/salive/lctvde
