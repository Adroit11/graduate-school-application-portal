<?php

/*
 * Boostrap (getboostrap.com) convenience functions
 */

function bs_glyph($name = null, $text = '', $glyph_before = true) {
    if (empty($name))
        return;
    return sprintf('%1$s<span class="glyphicon glyphicon-%2$s"></span>%3$s', !empty($text) && !$glyph_before ? $text . ' ' : '', $name, !empty($text) && $glyph_before ? ' ' . $text : '');
}

// Font-Awesome
function bs_fa($name = '', $text = '', $size = 'lg', $icon_before = true) {
    if (empty($name))
        return;
    return sprintf('%1$s<i class="fa fa-%2$s fa-%3$s"></i>%4$s', !empty($text) && !$icon_before ? $text . ' ' : '', $name, $size, !empty($text) && $icon_before ? ' ' . $text : '');
}

function bs_thumbnail($type = 'circle', $w = 0, $h = 0, $alt = '', $holder = '') {
    $w = abs((int) $w);
    $w = (!$w) ? 140 : $w;
    $h = abs((int) $h);
    $h = (!$h) ? 140 : $h;
    $holder = (empty($holder)) ? base_url() . 'js/holder.js' : $holder;

    $data = sprintf('class="img-%1$s" data-src="%2$s/%3$sx%4$s"', $type, $holder, $w, $h);

    return sprintf('<img %1$s alt="%2$s">', $data, $alt);
}

function bs_nav_dropdown($uri = null, $title = null, $inside_contents = null) {
    if (empty($uri))
        return;
    $ci = & get_instance();
    $ci->load->library('uri');
    $segment_count = substr_count($uri, '/');
    $pieced_uri = '';
    for ($i = 1; $i <= ($segment_count + 1); $i++) {
        $pieced_uri .= $ci->uri->segment($i) . '/';
    }
    $pieced_uri = substr($pieced_uri, 0, strripos($pieced_uri, '/'));
    $class = $uri == $pieced_uri ? ' active' : '';
    $anchor = anchor($uri, $title . ' <b class="caret"></b>', array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'));
    return sprintf('<li class="dropdown%1$s">%2$s<ul class="dropdown-menu">%3$s</ul></li>', $class, $anchor, $inside_contents);
}

function bs_nav_anchor($uri = null, $title = null, $class = null, $args = array()) {
    if (empty($uri))
        return;
    $ci = & get_instance();
    $ci->load->library('uri');
    $uri_string = $ci->uri->uri_string();
    if (is_array($uri)) {
        $r = '';
        for ($i = 0; $i < count($uri); $i++) {
            $r .= bs_nav_anchor($uri[$i]['uri'], $uri[$i]['title'], isset($uri[$i]['class']) ? $uri[$i]['class'] : null, isset($uri[$i]['class']) ? $uri[$i]['class'] : array());
        }
        return $r;
    }
    if ($uri_string == $uri) {
        $class .= ' active';
    }
    $class = trim($class);
    return sprintf('<li%1$s>%2$s</li>', !empty($class) ? ' class="' . $class . '"' : '', anchor($uri, !empty($title) ? $title : 'Untitled', $args));
}

function bs_panel_open($accordion = false) {
    return sprintf('<div class="panel-group"%1$s>', $accordion ? ' id="accordion"' : '');
}

function bs_panel_item(&$idx = 1, $title = null, $content = null) {
    $content =
            '<div class="panel panel-default">' .
            '<div class="panel-heading">' .
            '<h4 class="panel-title">' .
            sprintf('<a data-toggle="collapse" data-parent="#accordion" href="#collapse-%1$s">%2$s</a>', $idx, $title) .
            '</h4>' .
            '</div>' .
            sprintf('<div id="collapse-%1$s" class="panel-collapse collapse%2$s">', $idx, $idx == 1 ? ' in' : '') .
            sprintf('<div class="panel-body">%1$s</div>', $content) .
            '</div>' .
            '</div>';
    $idx++;
    return $content;
}

function bs_panel_close(&$idx = 1) {
    $idx = 1;
    return '</div>';
}

if (!function_exists('bs_modal')) {

    function bs_modal($id = '', $title = '', $body = '', $close_text = '', $footer = '') {
        if (empty($id)) {
            return;
        }

        $m = '';

        $title = empty($title) ? false : sprintf('<h4 class="modal-title" id="myModalLabel">%1$s</h4>', $title);
        $body = empty($body) ? 'No content specified.' : autop($body);
        $close = empty($close_text) ? 'Close' : $close_text;
        $footer = empty($footer) ? sprintf('<button type="button" class="btn btn-default" data-dismiss="modal">%1$s</button>', $close) : $footer;

        $m .= <<<EOT
<div class="modal fade" id="$id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
EOT;

        if ($title) {
            $m .= <<<EOT
<div class="modal-header">
<button type = "button" class = "close" data-dismiss = "modal" aria-hidden = "true">&times;
</button>
$title
</div>
EOT;
        }

        $m .= <<<EOT
<div class = "modal-body">
$body
</div>
<div class = "modal-footer">
$footer
</div>
</div><!--/.modal-content-->
</div><!--/.modal-dialog-->
</div><!--/.modal-->
EOT;
        return $m;
    }

}

if (!function_exists('bs_form_fields')) {

    function bs_form_fields($item = array(), $hide_labels = false) {
        // Get CI instance & load relevant classes

        $ret = '';

        if (!array_key_exists('type', $item)) {
            foreach ($item as $sub_item) {
                $ret .= call_user_func(__FUNCTION__, $sub_item, $hide_labels);
            }
            return $ret;
        }

        $ci = & get_instance();
        $ci->load->helper('form');
        $ci->load->library('input');

        if (!array_key_exists('data', $item)) {
            $item['data'] = array();
        }

        // Add appropriate classes
        // Do not add the form-control class to checkboxes
        $exclude = array('checkbox', 'upload');
        $item['data']['class'] = array_remove($item['data'], 'class');

        if (!in_array($item['type'], $exclude)) {
            switch ($item['type']) {
                case 'submit':
                case 'button':
                    $item['data']['class'] .= ' btn';
                    if (!preg_match('/btn-default/', $item['data']['class'])) {
                        $item['data']['class'] .= ' btn-default';
                    }
                    break;
                default:
                    $item['data']['class'] .= ' form-control';
            }
            $item['data']['class'] = trim($item['data']['class']);
        }

        // Form-group class wrapper
        $ret .= '<div class="form-group">';

        // Labels
        // exclude checkboxes: they have a different label implementation

        if ((isset($item['label']) && !empty($item['label'])) && $item['type'] != 'checkbox') {
            $ret .= sprintf('<label%1$s%2$s>%3$s%4$s</label>', $hide_labels ? ' class="sr-only"' : '', isset($item['data']['id']) ? ' for="' . trim(html_escape($item['data']['id'])) . '"' : '', $item['label'], isset($item['required']) && $item['required'] ? '&nbsp;<span class="text-danger required-field">*</span>' : '');
            $ret .= in_array($item['type'], array('select', 'dropdown')) ? '&nbsp;&nbsp;' : '';
        }

        // Build actual input fields

        $type = array_remove($item, 'type');
        $data = array_remove($item, 'data');
        $label = array_remove($item, 'label');

        switch (strtolower($type)) {
            case 'phone':
                $ret .= sprintf('<input type="text" class="form-control bfh-phone"%1$s>', ($atts = arr2atts($data)) && !empty($atts) ? ' ' . $atts : '' );
                break;
            case 'date':
                $ret .= sprintf('<div class="bfh-datepicker"%1$s></div>', ($atts = arr2atts($data)) && !empty($atts) ? ' ' . $atts : '' );
                break;
            case 'country':
                $ret .= sprintf('<select class="form-control bfh-countries"%1$s></select>', ($atts = arr2atts($data)) && !empty($atts) ? ' ' . $atts : '' );
                break;
            case 'upload':
                $value = array_remove($data, 'value');
                $ret .= form_upload($data, $value);
                break;
            case 'checkbox':
                unset($data['class']);
                $ret .= '<div class="checkbox">';
                $ret .= $label ? '<label>' : '';
                $ret .= form_checkbox($data);
                $ret .= $label ? sprintf('<span>%1$s</span>', !empty($label) ? ' ' . $label : '') : '';
                $ret .= $label ? '</label>' : '';
                $ret .= '</div>';
                break;
            case 'select':
                $name = array_remove($data, 'name');
                $options = array_remove($data, 'options');
                $selected = array_remove($data, 'selected');
                $ret .= form_dropdown($name, $options, $selected, arr2atts($data));
                break;
            case 'textarea':
                $data['name'] = array_key_exists('name', $data) ? $data['name'] : '';
                $ret .= form_textarea($data);
                break;
            case 'submit':
                $value = array_remove($data, 'value');
                $ret .= form_submit($data, $value);
                break;
            case 'button':
                $value = array_remove($data, 'value');
                $ret .= form_button($data, $value);
                break;
            case 'text':
            case 'password':
                $value = array_remove($data, 'value');
                $type = ($type == 'text') ? 'input' : $type;
                $ret .= call_user_func(sprintf('form_%1$s', $type), $data, $value);
                break;
            default:
                $ret .= '<div class="alert alert-danger">Invalid input type</div>';
        }

        $ret .= '</div>';

        return $ret;
    }

}

if (!function_exists('bs_button_dropdown')) {

    function bs_button_dropdown($btn_text = '', $items = array(), $btn_props = array(), $block = false) {
        if (empty($btn_text) || !is_array($items) || !is_array($btn_props) || empty($items))
            return;

        $btn_class = sprintf('btn dropdown-toggle%1$s ', $block ? ' btn-block' : null) . array_remove($btn_props, 'class');
        $btn_class = trim($btn_class);
        $btn_data_toggle = 'dropdown ' . array_remove($btn_props, 'data-toggle');
        $btn_data_toggle = trim($btn_data_toggle);

        $r = sprintf('<div class="btn-group%1$s">', $block ? ' btn-block' : null);
        $r .= sprintf('<button type="button" class="%1$s" data-toggle="%2$s" %3$s>%4$s</button>', $btn_class, $btn_data_toggle, !empty($text_props) ? ' ' . arr2atts($btn_props) : null, $btn_text . '&nbsp;&nbsp;<span class="caret"></span>');
        $r .= sprintf('<ul class="dropdown-menu%1$s" role="menu">', $block ? ' btn-block' : null);
        foreach ($items as $item)
            if (is_array($item))
                $r.= sprintf('<li>%1$s</li>', anchor(array_key_exists('href', $item) ? $item['href'] : '#', array_key_exists('text', $item) ? $item['text'] : 'Missing text', array_key_exists('data', $item) ? $item['data'] : false));
        $r .= '</ul>';
        $r .= '</div>';

        return $r;
    }

}

if (!function_exists('bs_value')) {

    function bs_value($key = '', $arr = array(), $default_val = '', $callback = false, $callback_default = false) {
        $val = ($val = element($key, $arr)) ? $val : $default_val;
        if ($val != $default_val && $callback) {
            $val = call_user_func($callback, $val);
        } else if ($val == $default_val && $callback_default) {
            $val = call_user_func($callback_default, $val);
        }
        return $val;
    }

}