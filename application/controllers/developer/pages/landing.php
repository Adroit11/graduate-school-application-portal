<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Landing extends Developer_Pages_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var('record', 'landing');
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_options->get();

        $this->stencil->title('Landing Page');
        $this->stencil->meta(
                array('description' => 'Edit the landing page')
        );
        $this->stencil->paint('developer/pages/landing_view', $data);
    }

    public function submit() {
        if (!$this->input->post()) {
            $this->output_error('Could not transmit data to the server.');
        }

        $rules = array(
            array(
                'field' => 'jumbotron_lead',
                'label' => 'Jumbotron lead text',
                'rules' => 'trim',
            ),
            array(
                'field' => 'jumbotron_cta',
                'label' => 'Jumbotron call-to-action',
                'rules' => 'trim',
            ),
            array(
                'field' => 'jumbotron_btn',
                'label' => 'Jumbotron button text',
                'rules' => 'trim',
            ),
            array(
                'field' => 'main_title',
                'label' => 'Main title',
                'rules' => 'trim',
            ),
            array(
                'field' => 'main_content',
                'label' => 'Main content',
                'rules' => 'trim',
            ),
            array(
                'field' => 'sidebar_title',
                'label' => 'Jumbotron button text',
                'rules' => 'trim',
            ),
            array(
                'field' => 'sidebar_gcal_url',
                'label' => 'Sidebar Google Calendar URL',
                'rules' => 'trim|callback_fv_valid_url',
            ),
        );

        for ($i = 1; $i <= 3; $i++) {
            $rules[] = array(
                'field' => 'trifecta_title_' . $i,
                'label' => 'Triecta item ' . $i . ' title',
                'rules' => 'trim',
            );
            $rules[] = array(
                'field' => 'trifecta_content_' . $i,
                'label' => 'Triecta item ' . $i . ' content',
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

        $this->output_errors('Landing page data has been saved.', 'success');
    }

}