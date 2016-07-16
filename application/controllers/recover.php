<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recover extends Login_Controller {

    private $reset_meta_key = 'pass_reset_key';
    private $user;

    public function __construct() {
        parent::__construct();
        // load the db user library
        $this->load->library('db_user');
    }

    public function index() {
        $data = array();

        $this->stencil->title('Recover Account');
        $this->stencil->meta(
                array('description' => 'Recover your HAUGS online application account')
        );

        // handle GET parameters
        switch ($this->input->get('action')) {
            case 'reset':
                // check if the reset key is valid
                $email = urldecode($this->input->get('email'));
                $reset_key = $this->input->get('key');
                if ($this->db_user->get_meta($this->reset_meta_key, null, $reset_key)) {
                    // show the form
                    $data['reset']['key'] = $reset_key;
                    $data['reset']['email'] = $email;
                } else {
                    // show an error message
                    $data['reset'] = false;
                }
                break;
        }

        $this->stencil->paint('recover_view', $data);
    }

    // user submits a request
    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email|callback_fv_email_exists'
            ),
        );

        $this->form_validation->output_errors($rules);
        
        // if validation passed, it means a user ID has been set by the callback function
        $this->db_user->set_var('id', $this->user->user_id);
        
        // check if account is activated first
        if (!empty($this->user->user_activation_key)) {
            $activation_link = anchor('login?action=activate&email=' . urlencode($this->user->user_email) . '&key=' . $this->user->user_activation_key, 'Click this link to confirm your email.');
            // send the activation key if it isn't
            $data = array(
                'to' => $this->user->user_email,
                'subject' => 'Activate your account',
                'message' => <<<EOT
Dear {$this->user->user_fname},

You recently requested your password to be reset. However, your account needs to be activated first.
    
To confirm your email, click the link below:

$activation_link
        
If you did not request such an action, kindly ignore this message or simply delete it from your inbox.
        
- The HAU Graduate School
EOT
                );
            if ($this->email->dispatch($data)) {
                $this->output_errors('Your account needs to be activated before you can reset your password. We have sent an email to your inbox.', 'success');
            }
            $this->output_errors('Your account is not yet activated. However, we failed to send you an activation key.');
        }
        
        // proceed with the reset if no errors are found
        // generate the reset key
        $reset_key = random_string('alnum', 60);
        // save the meta key
        if ($this->db_user->save_meta($reset_key, $this->reset_meta_key)) {
            $reset_link = anchor('recover?action=reset&key=' . $reset_key . '&email=' . $this->user->user_email, 'Click this link to reset your password.');
            $data = array(
                'to' => $this->user->user_email,
                'subject' => 'Reset your password',
                'message' => <<<EOT
Dear {$this->user->user_fname},

You recently requested your password to be reset. To start the process, click the link below:

$reset_link
        
If you did not request such an action, kindly ignore this message or simply delete it from your inbox.
        
- The HAU Graduate School
EOT
                );
            // email the reset key
            if (!$this->email->dispatch($data)) {
                $this->errors[] = 'We failed to send you an email, please try again.';
            }
        } else {
        }
        $this->output_errors('A reset password link has been sent to your email address.', 'success');
    }

    // the actual reset process (assuming user enters correct key)
    public function reset() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');
        
        $rules = array(
            array(
                'field' => 'user_email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email|callback_fv_email_exists',
            ),
            array(
                'field' => 'reset_key',
                'label' => 'Reset key',
                'rules' => 'trim|required|callback_fv_reset_key_valid',
            ),
            array(
                'field' => 'password',
                'label' => 'New password',
                'rules' => 'trim|required|min_length[6]|max_length[20]|matches[passwordconf]',
            ),
            array(
                'field' => 'passwordconf',
                'label' => 'Confirm new password',
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->output_errors($rules);
        
        $this->db_user->set_var('id', $this->user->user_id);
        
        $data = array(
            // hash the new password
            'user_pass' => sha1($this->input->post('password')),
        );
        // try to reset the password
        if (!$this->db_user->update($data)) {
            $this->errors[] = 'Password reset failed. Please try again.';
        } else {
            // delete the meta key
            $this->db_user->delete_meta($this->reset_meta_key);
        }

        $this->output_errors('Password successfully changed.', 'success');
    }

    public function fv_email_exists($email) {
        if (!($this->user = $this->db_user->get('user_id, user_email, user_fname, user_activation_key', array('user_email' => $email)))) {
            $this->form_validation->set_message(__FUNCTION__, 'That email does not exist.');
            return false;
        }
    }
    
    public function fv_reset_key_valid($key) {
        if (!$this->db_user->get_meta($this->reset_meta_key, $this->user->user_id, $key)) {
            $this->form_validation->set_message(__FUNCTION__, 'The password reset key is invalid.');
            return false;
        }
    }

}