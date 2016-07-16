<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tos extends Developer_Pages_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var('record', 'tos');
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_options->get();

        $this->stencil->title('Terms of Service');
        $this->stencil->meta(
                array('description' => 'Edit the Terms of Service page')
        );
        $this->stencil->paint('developer/pages/tos_view', $data);
    }

    public function submit() {
        if (!$this->input->post()) {
            $this->output_error('Could not transmit data to the server.');
        }

        $rules = array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'trim',
            ),
            array(
                'field' => 'date',
                'label' => 'Effective date',
                'rules' => 'trim|min_length[1]|callback_fv_valid_date',
            ),
            array(
                'field' => 'content',
                'label' => 'Content',
                'rules' => 'trim',
            ),
        );

        $this->form_validation->output_errors($rules);

        // set the meta value container
        $mv = array();
        foreach ($rules as $rule) {
            // set the meta key
            $field = $rule['field'];
            switch ($field) {
                case 'date':
                    $mv[$field] = date_mysql($this->input->post($field));
                    break;
                default:
                    $mv[$field] = $this->input->post($field);
            }
        }

        if (!$this->db_options->save($mv)) {
            $this->errors[] = 'An error occurred while saving data';
        }

        $this->output_errors('Terms of service data has been saved.', 'success');
    }

}