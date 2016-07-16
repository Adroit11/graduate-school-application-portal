<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Smtp extends Developer_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var(array(
            'module' => 'smtp',
        ));
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_options->get('smtp');
        $this->stencil->title('SMTP Settings');
        $this->stencil->meta(
                array('description' => 'Edit SMTP settings')
        );
        $this->stencil->paint('developer/emails/smtp_view', $data);
    }

    public function submit() {
        if (!$this->input->post()) {
            $this->output_error('Could not transmit data to the server.');
        }

        $rules = array(
            array(
                'field' => 'from_name',
                'label' => 'From name',
                'rules' => 'trim|required|max_length[50]',
            ),
            array(
                'field' => 'from_email',
                'label' => 'From email',
                'rules' => 'trim|required|valid_email|max_length[255]',
            ),
            array(
                'field' => 'reply_to_name',
                'label' => 'Reply-to name',
                'rules' => 'trim|max_length[35]',
            ),
            array(
                'field' => 'reply_to_email',
                'label' => 'Reply-to email',
                'rules' => 'trim|valid_email|max_length[255]',
            ),
            array(
                'field' => 'host',
                'label' => 'Hostname',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'port',
                'label' => 'Port number',
                'rules' => 'trim|required|numeric'
            ),
            array(
                'field' => 'authentication',
                'label' => 'Authentication',
                'rules' => 'trim|required|numeric|min_value[0]|max_value[1]'
            ),
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim',
            ),
            array(
                'field' => 'subject_prefix',
                'label' => 'Subject line prefix',
                'rules' => 'trim|max_length[20]',
            ),
            array(
                'field' => 'cc',
                'label' => 'CC',
                'rules' => 'trim',
            ),
            array(
                'field' => 'bcc',
                'label' => 'BCC',
                'rules' => 'trim',
            ),
            array(
                'field' => 'timeout',
                'label' => 'Email timeout',
                'rules' => 'trim|numeric|min_value[0]|max_value[10]',
            ),
        );

        $this->form_validation->output_errors($rules);

        // set the meta value container
        $mv = array();
        foreach ($rules as $rule) {
            // set the meta key
            $field = $rule['field'];
            switch ($field) {
                case 'authentication':
                    $mv[$field] = (int) abs($this->input->post($field));
                    break;
                case 'cc':
                case 'bcc':
                    $emails = explode('\n', $this->input->post($field));
                    foreach ($emails as $email) {
                        $mv[$field][] = trim($email);
                    }
                    break;
                default:
                    $mv[$field] = $this->input->post($field);
            }
        }

        if (!$this->db_options->save($mv)) {
            $this->errors[] = 'An error occurred while saving SMTP settings';
        }

        $this->output_errors('SMTP settings has been saved.', 'success');
    }

}