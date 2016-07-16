<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tos extends Page_Controller {

    public function index() {
        $data = array();
        
        $this->db_options->set_var('record', 'tos');
        $data['content']  = $this->db_options->get();
        
        $this->stencil->title('Terms of Use');
        $this->stencil->meta(array(
            'description' => 'Learn the extent of data we gather from from users'
        ));
        $this->stencil->data(array(
        ));
        $this->stencil->paint('tos_view', $data);
    }

}