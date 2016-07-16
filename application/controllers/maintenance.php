<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Maintenance extends Page_Controller {

    public function index() {

        $this->stencil->title('Under Maintenance');
        $this->stencil->meta(
                array('description' => 'The developer is performing housekeeping. The website will be back online soon.')
        );

        $this->stencil->paint('maintenance_view');
    }
}