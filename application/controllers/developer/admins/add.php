<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Add extends Developer_Admins_Controller {

    public function index() {
        $this->stencil->title('Add Administrator');
        $this->stencil->meta(
                array('description' => 'Add an administrator to manage users and applications')
        );
        $this->stencil->paint('developer/admins/add_view');
    }

    public function submit() {
        if (!$this->input->post())
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
        );
        
        $this->form_validation->output_errors($rules);

        $post_data = array();
        foreach ($rules as $data) {
            $key = $data['field'];
            $val = $this->input->post($key);
            $post_data[$key] = $val;
        }
        
        $data = array(
            'user_email' => $post_data['email'],
            'user_pass' => sha1($post_data['password']),
            'user_fname' => $post_data['fname'],
            'user_lname' => $post_data['lname'],
            'user_role' => 2, // make sure this is ALWAYS 2
            'user_status' => 0,
            'user_udate' => date_mysql(),
            'user_cdate' => date_mysql(),
        );

        if (($insert_id = $this->db_user->insert($data))) {
            $login_url = site_url('login');
            $data = array(
                'to' => $post_data['email'],
                'subject' => 'Your administrator account details',
                'message' => <<<EOT
Dear {$post_data['fname']},

Congratulations! You are now a site administrator.

To access your account, log in to $login_url with the following credentials:

Username: {$post_data['email']}
Password: {$post_data['password']} (do not share this with anyone)
        
Warm regards,
JP Caparas
Lead Developer & Designer
EOT
            );
            if (!$this->email->dispatch($data)) {
                $this->db_user->delete($insert_id);
                $this->errors[] = 'Failed to register your account. Please try again.';
            }
        }
        $this->output_errors(sprintf('%1$s has been successfully added as an administrator.', $post_data['fname']), 'success');
    }

}