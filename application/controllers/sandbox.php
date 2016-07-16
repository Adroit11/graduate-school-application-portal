<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sandbox extends Page_Controller {

    public function index() {
        
        $this->stencil->title('Sandbox');
        $this->stencil->meta(
                array('description' => 'Sandbox')
        );
        $this->stencil->paint('sandbox_view');
    }
}