<?php

class Login_Controller extends Page_Controller {

    public function __construct() {
        parent::__construct();

        $this->stencil->css(array(
            'jquery.loadmask'
        ));
        $this->stencil->js(array(
            'jquery.loadmask.min'
        ));

        // log the user to the proper role
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            redirect(site_url($role));
        }
    }

}