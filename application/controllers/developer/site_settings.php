<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Site_Settings extends Developer_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var(array(
            'module' => 'settings'
        ));
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_options->get();
        $this->stencil->title('Site Settings');
        $this->stencil->meta(
                array('description' => 'Edit the website settings')
        );
        $this->stencil->paint('developer/site_settings_view', $data);
    }
    
    public function submit() {
        if (!$this->input->post()) {
            $this->output_error('Could not transmit data to the server.');
        }
        
        $rules = array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'title_sep',
                'label' => 'Title Separator',
                'rules' => 'trim|min_length[1]|max_length[1]',
            ),
            array(
                'field' => 'robots',
                'label' => 'Robots.txt entries',
                'rules' => 'trim',
            ),
            array(
                'field' => 'nav_title',
                'label' => 'Navigation title',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'beta',
                'label' => 'In beta',
                'rules' => 'trim|numeric|min_value[0]|max_value[1]',
            ),
            array(
                'field' => 'announcement',
                'label' => 'Announcement',
                'rules' => 'trim'
            ),
            array(
                'field' => 'analytics',
                'label' => 'Google Analytics code',
                'rules' => 'trim'
            ),
        );
        
        $this->form_validation->output_errors($rules);

        // set the meta value container
        $mv = array();
        foreach($rules as $rule) {
            // set the meta key
            $field = $rule['field'];
            switch($field) {
                case 'in_beta':
                    $mv[$field] = (int) abs($this->input->post($field));
                    break;
                default:
                    $mv[$field] = $this->input->post($field);
            }
        }
        
        if (!$this->db_options->save($mv)) {
            $this->errors[] = 'An error occurred while saving settings';
        }
        
        $this->output_errors('Settings has been saved.', 'success');
    }

}