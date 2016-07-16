<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Apply extends Page_Controller {

    private $index;
    private $token;
    private $userdata = 'studapp_recommendations';

    public function __construct() {
        parent::__construct();
        $this->load->library('db_util');
        $this->load->library('form_validation');

        $this->stencil->css(array(
            'jquery.loadmask'
        ));
        $this->stencil->js(array(
            'jquery.loadmask.min'
        ));
    }

    public function index() {
        echo redirect(uri_string() . '/recommendations');
    }

    public function recommendations($id = 0, $index = 0, $token = '') {
        $data = array();

        $this->stencil->title('Instructor Recommendations');
        $this->stencil->meta(array(
            'description' => 'Use this form to submit a recommendation for your student.',
        ));

        $this->id = $id;
        $this->index = $index;
        $this->token = $token;

        if (!($db_data = $this->_db_get_student_instructor_data())) {
            $data['invalid'] = true;
        } else {
            // set the instructor session data if it's not set up yet
            if (!$this->session->userdata($this->userdata))
                $this->session->set_userdata($this->userdata, $db_data);

            $data['student'] = $db_data->student;
            $data['instructor'] = $db_data->instructor;
        }

        $this->stencil->paint('secure/apply/recommendations_view', $data);
    }

    public function recommendations_submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        if (!($data = $this->session->userdata($this->userdata))) {
            $this->errors[] = 'Your recommendation has already been received.';
        }

        $this->output_errors();
        
        $rules = array(
            array(
                'field' => 'recommendation',
                'label' => 'Recommendation',
                'rules' => 'trim|required|min_length[1]|max_length[2000]',
            ),
        );
        
        $this->form_validation->output_errors($rules);

        $recommendation = $this->input->post('recommendation');
        
        if (!$this->_db_save_recommendation($data, $recommendation)) {
            $this->errors[] = 'Failed to save your recommendation. Please try again.';
        } else {
            // remove the userdata object once the recommendation has been saved
            $this->session->unset_userdata($this->userdata);
        }
        
        $this->output_errors('Your recommendation has been saved successfully', 'success');
    }

    private function _db_save_recommendation($data, $recommendation) {
        // we need to build the meta value again
        $meta_value = array();
        foreach ($data->instructor as $k => $v) {
            if ($k === 'recommendation') {
                $meta_value['recommendation'] = $recommendation;
            } else {
                $meta_value[$k] = $v;
            }
        }

        $where = array(
            'user_id' => $data->student->id,
        );
        
        return $this->db_util->save_meta($this->tbl->users_meta, $data->meta->meta_key, $meta_value, $where);
    }

    public function _db_get_student_instructor_data() {
        $u = $this->tbl->users;
        $um = $this->tbl->users_meta;

        $where = array(
            "$um.user_id" => $this->id,
            "$um.meta_key" => 'studapp_recommendations_' . $this->index,
        );

        // merge student information and instructor meta_value
        $this->db->select('*');
        $this->db->from($u);
        $this->db->join($um, "$u.user_id = $um.user_id", 'left');
        $this->db->where($where);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();

            $data = array();

            $instructor_meta = json_decode($row->meta_value, true);
            foreach ($instructor_meta as $k => $v) {
                $data['instructor'][$k] = $v;
            }
            unset($row->meta_value);

            // if the tokens do not match or there is already a recommendation, exit immediately.
            if ($data['instructor']['token'] !== $this->token || $data['instructor']['recommendation'] !== '')
                return false;

            // create a multidimensional array that has keys for (a)instructor (b)student and (c)meta information
            $student_rgx = '/^(user)_+(.*)/';
            foreach ($row as $k => $v) {
                if (preg_match($student_rgx, $k, $m)) {
                    $data['student'][$m[2]] = $v;
                } else {
                    $data['meta'][$k] = $v;
                }
            }

            // cast the array to an object
            return array_to_object($data);
        }

        return false;
    }

}