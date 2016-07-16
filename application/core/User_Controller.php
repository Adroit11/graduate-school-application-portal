<?php

class User_Controller extends Auth_Controller {

    public function __construct() {
        parent::__construct();

        if ($this->session->userdata('role') != 'user') {
            show_404();
        }
        
        if ($this->db_options->get('maintenance')) {
            redirect('maintenance');
        }
        
    }

}