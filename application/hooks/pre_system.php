<?php

define('HOSTNAME', sprintf('http%1$s://%2$s%3$s', ($_SERVER['SERVER_PORT'] == 443) ? 's' : '', $_SERVER['HTTP_HOST'], ($_SERVER['SERVER_PORT']) != 80 ? ':' . $_SERVER['SERVER_PORT'] : ''));
define('URI', $_SERVER['REQUEST_URI']);

class URLFixes {

    public function removeIndexDotPHP() {
        $rgx = '/(?=.*)\/index\.(php|html)+$/';
        if (preg_match($rgx, URI)) {
            header('Location: ' . substr(HOSTNAME . URI, 0, strlen('index.php') * -1));
        }
    }

}

class HouseKeeping {

    public function setTimezone() {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('Asia/Manila');
        }
    }

}