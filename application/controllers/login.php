<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends Login_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('db_user');
    }

    public function index() {
        $data = array();

        $this->stencil->title('Sign in');
        $this->stencil->meta(
                array('description' => 'Sign in to your HAUGS online application account')
        );

        // activate email
        switch ($this->input->get('action')) {
            case 'activate':
                $email = $this->input->get('email');
                $key = $this->input->get('key');
                $data['activate'] = $this->_db_activate(urldecode($email), $key);
                break;
            case 'delete':
                $data['delete'] = true;
        }

        $this->stencil->paint('login_view', $data);
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|callback_fv_account_valid'
            ),
        );

        $this->form_validation->output_errors($rules);

        // set the user's session
        if (($data = $this->db_user->get(null, null, $this->id))) {
            $this->_sess_login($data);
        }

        // set the setinterval cookie (to close all browser sessions when logged out
        // Mr. Joel Canlas' advice
        set_cookie(array(
            'name' => 'ci_setinterval',
            'value' => 'true',
            'expire' => 604800
        ));

        // if "Remember me" is ticked, create a refresh token
        // from http://stackoverflow.com/questions/3984313/how-to-create-remember-me-checkbox-using-codeigniter-session-library
        if ($this->input->post('remember') == 1) {
            $this->_db_insert_refresh_token();
        }

        exit('true');
    }

    // activate the account by changing its value to an empty string
    private function _db_activate($email, $key) {
        $where = array(
            'user_email' => $email,
            'user_activation_key' => $key,
        );

        // check if the details are correct
        
        if (!($user_id = $this->db_user->get_col('user_id', $where)))
            return false;

        // remove the activation code, effectively activating the account
        $data = array(
            'user_activation_key' => '',
        );

        return $this->db_user->update($data, null, $user_id);
    }

    protected function _db_insert_refresh_token() {
        $token_val = random_string('alnum', 60);

        // save the token as a cookie on the client browser
        // token value has unique identifiers
        $token_obj = array(
            'id' => $this->id,
            'token_val' => $token_val,
            'ip_address' => $this->input->ip_address(), // make sure it's the same IP address we're extending
            'user_agent' => $this->agent->agent_string(), // make sure it's the same browser being used
        );

        if ($this->db_user->insert_meta($token_obj, $this->refresh_token_name, $this->id)) {
            set_cookie(array(
                'name' => $this->refresh_token_name,
                'value' => json_encode($token_obj),
                'expire' => 604800
            ));
        }
    }

    public function fv_account_valid() {
        $where = array(
            'user_email' => $this->input->post('email'),
            'user_pass' => sha1($this->input->post('password')),
        );

        $data = $this->db_user->get('user_id, user_activation_key, user_status', $where);

        if (!$data) {
            $this->form_validation->set_message(__FUNCTION__, 'Login details are incorrect.');
            return false;
        }

        // set the ID
        $this->id = $data->user_id;

        // proceed with other validations
        if ($data->user_activation_key != '') {
            $this->form_validation->set_message(__FUNCTION__, 'Your account is not yet activated.');
            return false;
        }

        if ($data->user_status > 0) {
            $this->form_validation->set_message(__FUNCTION__, 'Your account is currently suspended.');
            return false;
        }
    }

}