<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Basic extends User_Apply_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var('record', 'basic');
    }

    public function index() {
        $data = array();
        $this->stencil->title('Basic Information');
        $this->stencil->meta(
                array('description' => 'Enter your basic information (e.g. name, address, phone number)')
        );
        $data['record'] = $this->db_user->get_meta(null, null, null, true, true);
        $this->stencil->paint('user/apply/basic_view', $data);
    }

    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'trim|required|max_length[3]|callback_fv_valid_title'
            ),
            array(
                'field' => 'fname',
                'label' => 'First name',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'lname',
                'label' => 'Last name',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'mname',
                'label' => 'Middle name',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'suffix',
                'label' => 'Suffix',
                'rules' => 'trim|max_length[35]'
            ),
            array(
                'field' => 'profession',
                'label' => 'Profession',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'bdate',
                'label' => 'Birth date',
                'rules' => 'trim|required|max_length[10]|callback_fv_valid_date'
            ),
            array(
                'field' => 'citizenship',
                'label' => 'Citizenship',
                'rules' => 'trim|required|max_length[35]|callback_fv_valid_citizenship'
            ),
            array(
                'field' => 'phone',
                'label' => 'Phone',
                'rules' => 'trim|max_length[20]|callback_fv_valid_phone'
            ),
            array(
                'field' => 'address',
                'label' => 'Address',
                'rules' => 'trim|required|max_length[60]'
            ),
            array(
                'field' => 'address_2',
                'label' => 'Address 2',
                'rules' => 'trim|max_length[60]'
            ),
            array(
                'field' => 'city',
                'label' => 'City/Town/Municipality',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'state',
                'label' => 'State/Province',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'zip',
                'label' => 'ZIP',
                'rules' => 'trim|required|max_length[10]'
            ),
            array(
                'field' => 'country',
                'label' => 'Country',
                'rules' => 'trim|max_length[35]'
            ),
        );

        $this->form_validation->output_errors($rules);

        // sanitize the post data/make changes
        $meta = array();
        foreach ($rules as $item) {
            $mkey = $item['field'];
            $mval = $this->input->post($mkey);
            switch ($mkey) {
                case 'bdate':
                    $mval = date_mysql($mval);
                    break;
            }
            $meta[$mkey] = $mval;
        }
        
        if (!$this->db_user->save_meta_batch($meta)) {
            $this->errors[] = 'There was an error saving your information to the database';
        }

        $this->output_errors('Your basic information has been saved.', 'success');
    }

    public function fv_valid_title($str) {
        $rgx = '/mr|ms|mrs/';
        if (!preg_match($rgx, $str)) {
            $this->form_validation->set_message(__FUNCTION__, 'The %s field has an incorrect value.');
            return false;
        }
    }

    public function fv_valid_citizenship($str) {
        $rgx = '/filipino|foreign|naturalized/';
        if (!preg_match($rgx, $str)) {
            $this->form_validation->set_message(__FUNCTION__, 'The %s field has an incorrect value.');
            return false;
        }
    }

}