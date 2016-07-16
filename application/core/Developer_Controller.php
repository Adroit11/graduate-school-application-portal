<?php

class Developer_Controller extends Auth_Controller {

    public function __construct() {
        parent::__construct();

        if ($this->session->userdata('role') != 'developer') {
            show_404();
        }
    }

}