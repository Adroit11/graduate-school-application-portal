<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends User_Controller {

    public function index() {
        redirect('user/apply');
    }

}