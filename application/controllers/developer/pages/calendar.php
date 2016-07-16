<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Calendar extends Developer_Pages_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var('record', 'calendar');
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_options->get();

        $this->stencil->title('Event Calendar Page');
        $this->stencil->meta(
                array('description' => 'Edit the Calendar page')
        );
        $this->stencil->paint('developer/pages/calendar_view', $data);
    }

    public function submit() {
        if (!$this->input->post()) {
            $this->output_error('Could not transmit data to the server.');
        }

        $rules = array(
            array(
                'field' => 'gcal_url',
                'label' => 'Google Calendar public URL',
                'rules' => 'trim|callback_fv_valid_url',
            ),
            array(
                'field' => 'fallback',
                'label' => '"Calendar not loaded" placeholder text',
                'rules' => 'trim|required',
            ),
        );

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

        $this->output_errors('Calendar page data has been saved.', 'success');
    }

}