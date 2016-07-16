<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Calendar extends Page_Controller {

    public function index() {
        $data = array();
        
        $this->db_options->set_var('record', 'calendar');
        $data['content'] = $this->db_options->get();
        
        $this->stencil->title('Calendar of activities');
        $this->stencil->meta(
                array('description' => 'Calendar of activities')
        );
        $this->stencil->paint('calendar_view', $data);
    }
}