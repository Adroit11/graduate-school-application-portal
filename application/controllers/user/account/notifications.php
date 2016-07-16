<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notifications extends User_Account_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var('record', 'notifications');
    }

    public function index() {
        $data = array();
        $data['record'] = $this->db_user->get_meta();

        $this->stencil->title('Notifications');
        $this->stencil->meta(
                array('description' => 'Edit your email notification preferences')
        );
        $this->stencil->css(array(
            'bootstrap-switch.min',
        ));
        $this->stencil->js(array(
            'bootstrap-switch.min',
        ));
        $this->stencil->paint('user/account/notifications_view', $data);
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'notifications',
                'label' => 'Notifications',
                'rules' => 'callback_fv_valid_checkbox_ids_values'
            ),
        );

        $this->form_validation->output_errors($rules);

        $data = $this->input->post('notifications');
        $meta_value = json_encode($data);

        if (!$this->db_user->save_meta($meta_value)) {
            $this->errors[] = 'There was an error saving your information to the database';
        }

        $this->output_errors('Your preferences have been saved.', 'success');
    }

    public function fv_valid_checkbox_ids_values() {
        $valid_ids = array('general', 'admin', 'account', 'newsletters');
        foreach ($this->input->post('notifications') as $k => $v) {
            if (!in_array($k, $valid_ids)) {
                $this->form_validation->set_message(__FUNCTION__, 'The of selected options uses an invalid ID.');
                return false;
            }
            if ((int) abs($v) < 0) {
                $this->form_validation->set_message(__FUNCTION__, 'One of the selected options uses an invalid value.');
                return false;
            }
        }
    }

}