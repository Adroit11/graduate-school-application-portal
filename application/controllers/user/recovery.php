<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recovery extends Page_Controller {

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect(site_url('user'));
        }
        
        $this->load->library('form_validation');

        $stencil_data = array();

        $validation_rules = array(
            array(
                'field' => 'email',
                'label' => 'Email address',
                'rules' => 'trim|required|valid_email'
            ),
        );

        $this->form_validation->set_rules($validation_rules);
        
        if ($this->form_validation->run() == FALSE) {
            $stencil_data['errors'] = $this->form_validation->error_array();
        } else {
            
        }

        $this->stencil->title('Password Recovery');
        $this->stencil->meta(
                array('description' => 'Rewcovery your HAUGS online application password')
        );
        $this->stencil->paint('user/recovery_view', $stencil_data);
    }

}