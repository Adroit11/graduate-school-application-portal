<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Essay extends User_Apply_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->db_user->set_var('record', 'essay');
    }

    public function index() {
        $this->stencil->title('Essay');
        $this->stencil->meta(
                array('description' => 'Submit your application essay')
        );
        $this->stencil->css(array(
        ));
        $this->stencil->js(array(
        ));

        $data['record'] = $this->db_user->get_meta();
        $this->stencil->paint('user/apply/essay_view', $data);
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'essay',
                'label' => 'Essay',
                'rules' => 'trim|required|min_length[200]|max_length[2000]'
            ),
        );

        $this->form_validation->output_errors($rules);

        // insert post data to db
        $meta_value = $this->input->post('essay');
        if (!$this->db_user->save_meta($meta_value)) {
            $this->errors[] = sprintf('Your essay was not saved.', $k);
        }

        $this->output_errors('Your essay has been saved.', 'success');
    }

}