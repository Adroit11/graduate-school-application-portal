<?php

if (!function_exists('d')) {

    function d($e) {
        echo '<pre>';
        print_r($e);
        echo '</pre>';
    }

}

if (!function_exists('dummytext')) {

    function dummytext($rep = 1) {
        for ($i = 1; $i <= $rep; $i++) {
            echo <<<EOT
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla enim ipsum, consectetur vel massa sed, consequat pellentesque tellus. Aliquam ac ante elementum, commodo turpis sed, vehicula diam. Quisque eleifend tortor mauris, eget egestas augue blandit eu. Aliquam vitae ornare quam, sed interdum eros. Curabitur faucibus velit ut sapien porta laoreet. Nulla vulputate suscipit rutrum. Nullam adipiscing tempus risus. Aliquam vitae odio neque. Donec at dui ante. Vestibulum feugiat est fermentum feugiat faucibus. Nulla vel urna aliquam, tincidunt nisi auctor, egestas metus. Vestibulum non ligula in nunc rutrum vulputate in et nulla. Vestibulum ac orci felis. Nunc tristique massa mauris, et mollis magna egestas ac. Phasellus tempus urna ac risus vehicula hendrerit. Pellentesque eu cursus nulla, convallis eleifend ipsum.</p>
EOT;
        }
    }

}

if (!function_exists('hr')) {

    function hr($atts = array()) {
        if (!is_array($atts))
            return;
        echo sprintf('<hr%1$s />', !empty($atts) ? ' ' . arr2atts($atts) : '' );
    }

}

if (!function_exists('autop')) {

    function autop($str = '') {
        $ci = & get_instance();
        $ci->load->library('typography');
        return $ci->typography->auto_typography($str);
    }

}

if (!function_exists('substr_more')) {

    function substr_more($str = '', $max = 140, $more = '...') {
        $str = trim($str);
        $more = trim($more);

        if (empty($str))
            return;

        if (strlen($str) > $max) {
            $str = substr($str, 0, 140);
            if (!empty($more)) {
                $str .= ' ' . $more;
            }
            return $str;
        }

        return $str;
    }

}

if (!function_exists('google_calendar')) {

    function google_calendar($rss_url = '', $max_results = 3) {
        try {
            if (empty($rss_url)) {
                throw new Exception(sprintf('Enter a valid URL for the <strong>%1$s</strong> function.', __FUNCTION__));
            }
        } catch (Exception $e) {
            show_error($e->getMessage());
        }

        $context = stream_context_create(array('https' => array('header' => 'Accept: application/xml')));

        try {
            if (!($xml = @file_get_contents($rss_url . '?max-results=' . (int) $max_results, false, $context))) {
                throw new Exception(sprintf('Enter a working URL for the <strong>%1$s</strong> function.', __FUNCTION__));
            }
        } catch (Exception $e) {
            show_error($e->getMessage());
        }

        $cal = simplexml_load_string($xml);
        $cal_ns = $cal->getNameSpaces(true);

        $ret = array();

        $ret['name'] = (string) $cal->author->name;

        $opensearch = $cal->children($cal_ns['openSearch']);
        $ret['results'] = (int) $opensearch->totalResults;
        $ret['start'] = (int) $opensearch->startIndex;
        $ret['ipp'] = (int) $opensearch->itemsPerPage;

        $gcal = $cal->children($cal_ns['gCal']);
        $gcal_atts = $gcal->timezone->attributes();
        $ret['tz'] = $gcal_atts['value'];

        $i = 0;
        foreach ($cal->entry as $entry) {
            $gd = $entry->children($cal_ns['gd']);
            $when_attr = $gd->when[0]->attributes();
            $ent[$i]['start'] = (string) $when_attr['startTime'];
            $ent[$i]['end'] = (string) $when_attr['endTime'];
            $ent[$i]['title'] = (string) $entry->title;
            $ent[$i]['content'] = (string) $entry->content;

            $i++;
        }

        $ret['entries'] = $ent;

        return $ret;
    }

}

if (!function_exists('get_param')) {

    function get_param($key = '') {
        if (empty($key))
            return;
        $ci = & get_instance();
        $ci->load->library('input');

        return $ci->input->get($key);
    }

}

if (!function_exists('strike')) {

    function strike($str = '', $class = '') {
        if (empty($str))
            return;

        return sprintf('<strike%1$s>%2$s</strike>', !empty($class) ? sprintf(' class="%1$s', $class) : '', $str);
    }

}

if (!function_exists('ul_ext')) {

    function ul_ext($list_items = array(), $list_properties = array()) {
        $ret = '';

        return $ret;
    }

}

if (!function_exists('arr2atts')) {

    function arr2atts($arr = array(), $prefix = '', $escape = true) {
        if (empty($arr) && !is_array($arr))
            return;
        $r = '';
        foreach ($arr as $k => $v) {
            if ($escape) {
                $k = html_escape($k);
                $v = html_escape($v);
            }
            if (is_numeric($k)) {
                $r .= sprintf('%1$s%2$s ', $prefix, $v);
            } else {
                $r .= sprintf('%1$s%2$s="%3$s" ', $prefix, $k, $v);
            }
        }
        return trim($r);
    }

}

if (!function_exists('uri_string')) {

    function uri_string($part = 0) {
        $ci = & get_instance();
        $ci->load->library('uri');
        if ((int) abs($part))
            return $ci->uri->segment($part);
        return $ci->uri->uri_string;
    }

}

if (!function_exists('array_remove')) {

    function array_remove(array &$arr, $key) {
        if (!is_array($arr) || empty($arr))
            return;
        if (array_key_exists($key, $arr)) {
            $val = $arr[$key];
            unset($arr[$key]);
            return $val;
        }
        return null;
    }

}

if (!function_exists('object_to_array')) {

    function object_to_array($d) {
        if (is_object($d))
            $d = get_object_vars($d);

        return is_array($d) ? array_map(__FUNCTION__, $d) : $d;
    }

}

if (!function_exists('array_to_object')) {

    function array_to_object($d) {
        return is_array($d) ? (object) array_map(__FUNCTION__, $d) : $d;
    }

}

// convert date helper value to database-friendly value
if (!function_exists('date_mysql')) {

    function date_mysql($date = null) {
        if ($date == '')
            return date('Y-m-d H:i:s');

        $old_format = 'm/d/Y';
        $parsed = date_create_from_format($old_format, $date);
        $new_format = 'Y-m-d H:i:s';
        return date_format($parsed, $new_format);
    }

}

// convert db value to date helper-friendly value (opposite of bs_date_db)
if (!function_exists('date_nice')) {

    function date_nice($date = null) {
        if ($date == '')
            return date('m/d/Y');

        $old_format = 'Y-m-d H:i:s';
        $parsed = date_create_from_format($old_format, $date);
        $new_format = 'm/d/Y';
        return date_format($parsed, $new_format);
    }

}

if (!function_exists('prop')) {

    function prop($prop, $class, $default = null, $callback = null, $default_callback = null) {
        if (gettype($class) !== 'object' || !property_exists($class, $prop)) {
            if ($default == '') {
                return null;
            } else {
                if ($default_callback) {
                    return call_user_func($default_callback, $default);
                }
                return $default;
            }
        }
        if ($callback) {
            return call_user_func($callback, $class->$prop);
        }
        return $class->$prop;
    }

}

if (!function_exists('object_pop')) {

    function object_pop($d) {
        $d = (array) ($d);
        $d = array_pop($d);
        return array_to_object($d);
    }

}

if (!function_exists('order_by')) {

    function order_by($col, $order = 'desc') {
        $url = current_url();
        $qs = '?' . $_SERVER['QUERY_STRING'];

        // append order_by if it doesn't exist, otherwise, modify
        $rgx1 = '/(.*)(order_by)+(?:\=)([^&]*)(.*)/i';
        if (preg_match($rgx1, $qs, $m)) {
            $qs = preg_replace($rgx1, '$1$2=' . urlencode($col) . '$4', $qs);
        } else {
            $qs .= sprintf('&order_by=%1$s', urlencode($col));
        }

        // append order_status if it doesn't exist, otherwise, modify
        $rgx2 = '/(.*)(order(?!=_by))+(?:\=)([^&]*)(.*)/i';
        if (preg_match($rgx2, $qs, $m)) {
            $qs = preg_replace($rgx2, '$1$2=' . $order . '$4', $qs);
        } else {
            $qs .= sprintf('&order=%1$s', urlencode($order));
        }

        // final sanitization
        $qs = str_replace('?&', '?', $qs);

        return sprintf('%1$s%2$s', $url, $qs);
    }

}

// alternative to CI's native download helper
// from http://taggedzi.com/articles/display/forcing-downloads-through-codeigniter
if (!function_exists('push_file')) {

    function push_file($path, $name) {
        $ci = & get_instance();
        // make sure it's a file before doing anything!
        if (is_file($path)) {
            // required for IE
            if (ini_get('zlib.output_compression')) {
                ini_set('zlib.output_compression', 'Off');
            }

            // get the file mime type using the file extension
            $ci->load->helper('file');

            $mime = get_mime_by_extension($path);

            // Build the headers to push out the file properly.
            header('Pragma: public');     // required
            header('Expires: 0');         // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
            header('Cache-Control: private', false);
            header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
            header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($path)); // provide file size
            header('Connection: close');
            readfile($path); // push it out
            exit();
        }
    }

}

// this is a callback function
// http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value/2699159#2699159
if (!function_exists('db_query_sort')) {

    function db_query_sort($a, $b) {
        
    }
}