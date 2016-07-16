<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logout extends My_Controller {

    public function __construct() {
        parent::__construct();
        $this->_sess_delete();
        redirect(site_url('login'));
    }

}