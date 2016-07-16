<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class About extends Page_Controller {

    public function index() {
        $data = array();
        
        $this->db_options->set_var('record', 'about');
        $data['content']  = $this->db_options->get();
        
        $this->stencil->title('About the Website');
        $this->stencil->meta(array(
            'description' => 'More information about the online application portal'
        ));
        $this->stencil->paint('about_view', $data);
    }

}