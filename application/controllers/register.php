<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends Login_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('db_user');
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            redirect(site_url($role));
        }

        $this->stencil->title('Registration');
        $this->stencil->meta(
                array('description' => 'Sign up for an HAUGS account')
        );
        $this->stencil->paint('register_view');
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'email',
                'label' => 'Email address',
                'rules' => 'trim|required|valid_email|callback_fv_email_available',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[6]|max_length[20]|matches[passwordconf]',
            ),
            array(
                'field' => 'passwordconf',
                'label' => 'Confirm password',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'fname',
                'label' => 'First name',
                'rules' => 'trim|required|max_length[128]',
            ),
            array(
                'field' => 'lname',
                'label' => 'Last name',
                'rules' => 'trim|required|max_length[128]',
            ),
            array(
                'field' => 'tos',
                'label' => 'Terms of service',
                'rules' => 'required|max_length[1]|callback_fv_tos',
            ),
        );

        $this->form_validation->output_errors($rules);


        $activation_key = random_string('alnum', 60);
        $data = array(
            'user_email' => $this->input->post('email'),
            'user_pass' => sha1($this->input->post('password')),
            'user_fname' => $this->input->post('fname'),
            'user_lname' => $this->input->post('lname'),
            'user_role' => 1, // make sure this is ALWAYS 1
            'user_status' => 0,
            'user_activation_key' => $activation_key,
            'user_udate' => date_mysql(),
            'user_cdate' => date_mysql(),
        );

        if (($insert_id = $this->db_user->insert($data))) {
            $activation_link = anchor('login?action=activate&key=' . $activation_key . '&email=' . urlencode($this->input->post('email')), 'Click to confirm your email.');
            $data = array(
                'to' => $this->input->post('email'),
                'subject' => 'Activate your account',
                'message' => <<<EOT
Dear {$data['user_fname']},

To activate your account, click the link below or copy it to your browser's location bar:

$activation_link
        
Warm regards,
JP Caparas
Lead Developer & Designer
EOT
            );
            if (!$this->email->dispatch($data)) {
                $this->db_user->delete($insert_id);
                $this->errors[] = 'Failed to register your account. Please try again.';
            } else {
                // send the welcome email
                $this->db_options->set_var(array(
                    'module' => 'email_template',
                    'record' => 'welcome',
                ));
                if (($welcome_email = $this->db_options->get())) {
                    $data = array(
                        'to' => $this->input->post('email'),
                        'subject' => $welcome_email->subject,
                        'message' => $welcome_email->message,
                    );
                    $this->email->dispatch($data);
                }
            }
        }
        $this->output_errors('You have been successfully registered, but you need to confirm ownership of this email by clicking the activation link sent to your inbox.', 'success');
    }

    public function fv_tos($tos) {
        if ($tos != 1) {
            $this->form_validation->set_message(__FUNCTION__, 'You must agree to the terms of service.');
            return false;
        }
    }

}