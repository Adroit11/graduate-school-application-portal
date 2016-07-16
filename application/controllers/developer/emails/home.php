<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Developer_Controller {

    public function index() {
        $this->stencil->title('Email Dashboard');
        $this->stencil->meta(
                array('description' => 'Manage email settings')
        );
        $this->stencil->paint('developer/emails/home_view');
    }

}