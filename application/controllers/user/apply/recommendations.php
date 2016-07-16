<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//TODO
// create a form validation callback that prevents the form from accepting the user's own email
class Recommendations extends User_Apply_Controller {

    public $min_index = 1;
    public $max_index = 2;

    public function __construct() {
        parent::__construct();

        $this->db_user->set_var(array(
            'record' => 'recommendations',
            'min_index' => $this->min_index,
            'max_index' => $this->max_index,
        ));
    }

    public function index() {
        $data = array();
        $data['records'] = $this->db_user->get_meta();

        $this->stencil->title('Recommendations');
        $this->stencil->meta(
                array('description' => 'Valid recommendations from past professors ensure that you are fit for the program')
        );
        $this->stencil->js(
                array(
                    'jquery-ui-1.10.3.custom.min',
                )
        );

        $this->stencil->paint('user/apply/recommendations_view', $data);
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'index',
                'label' => 'Index',
                'rules' => 'trim|required|callback_fv_valid_index',
            ),
            array(
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'trim|required|max_length[60]'
            ),
            array(
                'field' => 'position',
                'label' => 'Position',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'institution',
                'label' => 'Institution',
                'rules' => 'trim|required|max_length[35]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email|max_length[255]'
            ),
            array(
                'field' => 'phone',
                'label' => 'Phone',
                'rules' => 'trim|max_length[20]|callback_fv_valid_phone'
            ),
        );

        $this->form_validation->output_errors($rules);

        $meta_value = array();
        foreach ($rules as $item) {
            $key = $item['field'];
            $meta_value[$key] = $this->input->post($key);
        }

        // get the old meta value (if there exists any)
        $this->db_user->set_var('index', $this->input->post('index'));

        $old_meta_value = $this->db_user->get_meta();
        
        // create token / recommendation keys if not yet set
        $meta_value['token'] = $tmp_token = $old_meta_value && isset($old_meta_value->token) ? $old_meta_value->token : random_string('alnum', 60);
        $meta_value['recommendation'] = $old_meta_value && isset($old_meta_value->recommendation) ? $old_meta_value->recommendation : '';

        // the first condition checks if the value returned is an object
        // the second condition check is there is a previous record 
        // the third condition checks if the newly email is not the same as the old email
        // the fourth condition checks if the newly-entered email is different from the current email
        // any of this conditions is satisfied, the new email token is generated and the new user is emailed
        if ($old_meta_value) {
            if (
                    (!isset($old_meta_value->token) || $old_meta_value->token == '') ||
                    $old_meta_value->email != $meta_value['email'] || $meta_value['recommendation'] != ''
            ) {
                // generate a new token
                $meta_value['token'] = random_string('alnum', 60);
                // try to send the email
                if (!$this->_email_request_recommendation(array_to_object($meta_value))) {
                    // if email fails to send, revert to the old recommendation token (held by tmp_token)
                    $meta_value['token'] = '';
                    $this->errors[] = 'The email failed to send. Please click the "Save" button again.';
                }
            }
        } else {
            // for new inserts
            if (!$this->_email_request_recommendation(array_to_object($meta_value))) {
                $meta_value['token'] = '';
                $this->errors[] = 'The email failed to send. Please click the "Save" button again.';
            }
        }

        // insert JSON object to db, use the post index as a unique identifier of the record
        if (!$this->db_user->save_meta($meta_value)) {
            $this->errors[] = 'There was an error saving your professor\'s information to the database';
        }

        $this->output_errors('Your professor\'s information has been saved.', 'success');
    }

    protected function _email_request_recommendation($instructor) {
        $this->load->library('email');

        $user = $this->db_user->get();

        $url = $this->_url_recommendation($user->user_id, $this->db_user->get_var('index'), $instructor->token);

        $data = array(
            'to' => $instructor->email,
            'subject' => sprintf('%1$s, %2$s %3$s needs your recommendation for HAU Graduate School', $instructor->name, $user->user_fname, $user->user_lname),
            'message' => sprintf('<p>%1$s %2$s is applying at HAU Graduate School and has listed you as a former instructor. Please submit your recommendation by clicking %3$s.</p>', $user->user_fname, $user->user_lname, anchor($url, 'this link')),
        );

        if ($this->email->dispatch($data))
            return true;

        return false;
    }

    protected function _url_recommendation($id = 0, $index = 0, $token = null) {
        return site_url(sprintf('secure/apply/recommendations/%1$s/%2$s/%3$s', $id, $index, $token));
    }

}