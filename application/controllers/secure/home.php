<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Page_Controller {

    public function index() {
        echo redirect(uri_string() . '/apply/recommendations');
    }

}