<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class About extends Developer_Pages_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var('record', 'about');
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_options->get();

        $this->stencil->title('About Page');
        $this->stencil->meta(
                array('description' => 'Edit the About page')
        );
        $this->stencil->paint('developer/pages/about_view', $data);
    }

    public function submit() {
        if (!$this->input->post()) {
            $this->output_error('Could not transmit data to the server.');
        }

        $rules = array();

        for ($i = 1; $i <= 3; $i++) {
            $rules[] = array(
                'field' => 'title_' . $i,
                'label' => 'Tab ' . $i . ' title',
                'rules' => 'trim',
            );

            $rules[] = array(
                'field' => 'content_' . $i,
                'label' => 'Tab ' . $i . ' content',
                'rules' => 'trim',
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

        $this->output_errors('About page data has been saved.', 'success');
    }

}