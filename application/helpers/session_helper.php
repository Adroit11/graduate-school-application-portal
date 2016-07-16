<?php

if (!function_exists('user_id')) {

    function user_id($id = '') {
        if (!abs((int) $id))
            return;

        $ci = & get_instance();

        return $ci->session->userdata('id');
    }

}

if (!function_exists('user_role')) {

    function user_role($role = '') {
        $role = trim($role);
        if (empty($role))
            return;

        $ci = & get_instance();

        return $ci->session->userdata('role');
    }

}

if (!function_exists('is_admin')) {

    function is_admin() {
        $admin_val = 'admin';

        $ci = & get_instance();

        return $ci->session->userdata('role') == $admin_val ? true : false;
    }

}

if (!function_exists('logged_in')) {

    function logged_in() {

        $ci = & get_instance();

        return $ci->session->userdata('logged_in') === true ? true : false;
    }

}