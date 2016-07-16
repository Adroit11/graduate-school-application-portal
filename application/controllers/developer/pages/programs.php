<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Programs extends Developer_Pages_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var('record', 'programs');
    }

    public function index() {
        $data = array();
        $data['records'] = $this->db_options->get();
        
        $this->stencil->title('Programs Page');
        $this->stencil->meta(
                array('description' => 'Edit the Programs page')
        );
        $this->stencil->paint('developer/pages/programs_view', $data);
    }

    public function submit() {
        if (!$this->input->post()) {
            $this->output_errors('Could not transmit data to the server.');
        }

        // since we're dealing with a variable number of data, use an array
        $rules = array();

        // get only the array keys, we don't need the value
        $faqs = array_keys($this->input->post());
        foreach ($faqs as $field) {
            $label = ucfirst(preg_replace('/_/', ' ', $field));
            $label = preg_replace('/\d/', '', $label);
            $rules[] = array(
                'field' => $field,
                'label' => $label,
                'rules' => 'trim|required',
            );
        }

        $this->form_validation->output_errors($rules);

        // set the meta value container
        $mv = array();
        foreach ($rules as $rule) {
            // set the meta key
            $field = $rule['field'];
            switch ($field) {
                default:
                    $mv[$field] = $this->input->post($field);
            }
        }

        if (!$this->db_options->save($mv)) {
            $this->errors[] = 'An error occurred while saving data.';
        }

        $this->output_errors('Programs page data has been saved.', 'success');
    }

}