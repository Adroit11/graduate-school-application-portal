<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Maintenance extends Developer_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var(array(
            'module' => 'maintenance'
        ));
    }

    public function index() {
        $data = array();

        $maintenance = $this->db_options->get('maintenance');
        $data['maintenance'] = $maintenance ? 1 : 0;

        $this->stencil->title('Website Maintenance');
        $this->stencil->meta(
                array('description' => 'Put the website under maintenance')
        );
        $this->stencil->css(array(
            'bootstrap-switch.min',
        ));
        $this->stencil->js(array(
            'bootstrap-switch.min',
        ));
        $this->stencil->paint('developer/maintenance_view', $data);
    }
    
    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');
        
        $rules = array(
            array(
                'field' => 'maintenance',
                'label' => 'Maintenance',
                'rules' => 'trim|required|min_value[0]|max_value[1]'
            ),
        );
        
        $this->form_validation->output_errors($rules);

        // save the maintenance status
        $maintenance = $this->input->post('maintenance');
        if (!$this->db_options->save($maintenance)) {
            $this->errors[] = 'Unable to set the maintenance status.';
        }
        
        $this->output_errors('Maintenance mode preference saved.', 'success');
    }

}