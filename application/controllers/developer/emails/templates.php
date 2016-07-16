<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Templates extends Developer_Controller {

    private $_template_list = array(
        array(
            'group' => 'general',
            'title' => 'General emails',
            'emails' => array(
                'welcome' => 'Welcome Email',
            ),
        ),
        array(
            'group' => 'account',
            'title' => 'Account emails',
            'emails' => array(
                'account_deleted' => 'Account Deleted',
            ),
        ),
    );

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var(array(
            'module' => 'email_template'
        ));
    }

    public function index() {
        $data = array();
        $data['options'] = $this->_template_list_form();
        $this->stencil->title('Email Templates');
        $this->stencil->meta(
                array('description' => 'Edit email templates')
        );
        $this->stencil->paint('developer/emails/templates_view', $data);
    }

    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'subject',
                'label' => 'Subject',
                'rules' => 'trim|required|max_length[60]'
            ),
            array(
                'field' => 'message',
                'label' => 'Message',
                'rules' => 'trim|required|max_length[2000]'
            ),
        );

        $this->form_validation->output_errors($rules);

        // infer the template name from the post data
        $template_name = $this->input->post('template_name');

        // set the record var on db_user so it completes our meta_key() return value
        $this->db_options->set_var('record', $template_name);
        
        // set the meta value from post data
        $mv = array(
            'subject' => $this->input->post('subject'),
            'message' => $this->input->post('message'),
        );

        if (!$this->db_options->save($mv)) {
            $this->errors[] = 'Unable to save the template.';
        }
        
        $this->output_errors('Template successfully saved.', 'success');
    }

    public function data() {
        if (!$this->input->get())
            $this->output_errors('Unable to fetch data to the server.');

        // infer the template name from the post data
        $template_name = $this->input->get('template_name');

        // set the record var on db_user so it completes our meta_key() return value
        $this->db_options->set_var('record', $template_name);

        // get the meta value
        $data = $this->db_options->get();

        // if there's data for that template, return it
        if ($data) {
            exit(json_encode($data));
        }

        $this->output_errors('There\'s no data right for this template yet; I suggest adding one.', 'success');
    }

    private function _template_list_form() {
        $d = array(); // the return array
        foreach ($this->_template_list as $item) {
            $d[$item['title']] = $item['emails'];
        }
        return $d;
    }

}