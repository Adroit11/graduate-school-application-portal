<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Admin_Controller {

    public function index() {
        $this->stencil->title('Admin Dashboard');
        $this->stencil->meta(
                array('description' => 'Select an admin operation to perform')
        );
        $this->stencil->paint('admin/home_view');
    }

}