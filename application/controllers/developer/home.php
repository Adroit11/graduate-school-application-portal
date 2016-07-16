<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Developer_Controller {

    public function index() {
        $this->stencil->title('Developer Dashboard');
        $this->stencil->meta(
                array('description' => 'Select an admin operation to perform')
        );
        $this->stencil->paint('developer/home_view');
    }

}