<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Education extends User_Apply_Controller {

    public $min_index = 1;
    public $max_index = 5;

    public function __construct() {
        parent::__construct();

        $this->db_user->set_var(array(
            'record' => 'education',
            'min_index' => $this->min_index,
            'max_index' => $this->max_index,
        ));
    }

    public function index() {
        $data = array();

        $data['records'] = $this->db_user->get_meta();
        $data['min_index'] = $this->min_index;
        $data['max_index'] = $this->max_index;

        $this->stencil->title('Education');
        $this->stencil->meta(
                array('description' => 'Enter your educational information')
        );
        $this->stencil->css(
                array(
                    'bootstrap-formhelpers.min',
                )
        );
        $this->stencil->js(
                array(
                    'bootstrap-formhelpers.min',
                )
        );
        $this->stencil->paint('user/apply/education_view', $data);
    }

    public function delete() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        if (!($index = $this->input->post('index'))) {
            exit('false');
        }

        $this->db_user->set_var('index', $index);

        if (!$this->db_user->delete_meta()) {
            exit('false');
        };

        exit('true');
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'concentration',
                'label' => 'Concentration',
                'rules' => 'trim|required|max_length[35]|callback_fv_valid_index'
            ),
            array(
                'field' => 'degree',
                'label' => 'Degree',
                'rules' => 'trim|required|max_length[35]|callback_fv_degree_valid'
            ),
            array(
                'field' => 'institution',
                'label' => 'Institution',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'admitted',
                'label' => 'Date Admitted',
                'rules' => 'trim|required|max_length[10]|callback_fv_valid_date'
            ),
            array(
                'field' => 'graduated',
                'label' => 'Date Graduated',
                'rules' => 'trim|required|max_length[10]|callback_fv_valid_date'
            ),
            array(
                'field' => 'student_id',
                'label' => 'Student ID',
                'rules' => 'trim|max_length[35]'
            ),
            array(
                'field' => 'gpa',
                'label' => 'GPA',
                'rules' => 'trim|max_length[5]'
            ),
            array(
                'field' => 'awards',
                'label' => 'Awards',
                'rules' => 'trim|max_length[255]'
            ),
        );

        $this->form_validation->output_errors($rules);

        $meta_value = array();
        foreach ($rules as $item) {
            $key = $item['field'];
            $val = $this->input->post($key);
            switch ($key) {
                case 'admitted':
                case 'graduated':
                    $val = date_mysql($val);
                    break;
            }
            $meta_value[$key] = $val;
        }

        // insert JSON object to db, use the post index as a unique identifier of the record
        $this->db_user->set_var('index', $this->input->post('index'));
        if (!$this->db_user->save_meta($meta_value)) {
            $this->errors[] = 'There was an error saving your education to the database.';
        }

        $this->output_errors('Your educational information has been saved.', 'success');
    }

    public function fv_degree_valid($str) {
        $valid = array(
            'undergraduate',
            'vocational',
            'master',
            'doctoral',
        );

        if (!in_array($str, $valid)) {
            $this->form_validation->set_message(__FUNCTION__, 'You entered an invalid degree.');
            return false;
        }
    }

}