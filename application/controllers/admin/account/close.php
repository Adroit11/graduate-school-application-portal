<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Close extends Admin_Account_Controller {

    public function index() {
        $this->stencil->title('Close Account');
        $this->stencil->meta(
                array('description' => 'Request your account to be deleted')
        );
        $this->stencil->paint('admin/account/close_view');
    }

    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'reason',
                'label' => 'Reason',
                'rules' => 'required|trim|max_length[2000]',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|trim|matches[passwordconf]|callback_fv_correct_pass',
            ),
            array(
                'field' => 'passwordconf',
                'label' => 'Confirm password',
                'rules' => 'required|trim|matches[passwordconf]',
            ),
        );

        $this->form_validation->output_errors($rules);
        
        // if there are no errors, proceed sending the email

        $user = $this->db_user->get('user_email, user_fname, user_lname');
        $admin_name = $user->user_fname . ' ' . $user->user_lname;
        $subject = 'Account deletion request';
        $reason = $this->input->post('reason');
        $msg = <<<EOT
<strong>$admin_name</strong> is requesting deletion of his/her account.
                
<strong>Reason:</strong>
$reason
EOT;
        // build the data
        $data = array(
            'from' => $user->user_email,
            'to' => 'caparas.jp@gmail.com',
            'subject' => $subject,
            'message' => auto_typography($msg),
        );

        // send the email
        if (!$this->email->dispatch($data)) {
            $this->errors[] = 'Your request was not sent. Please try again.';
        }

        $this->output_errors('Your request has been sent. The developer will get back to you soon.', 'success');
    }

}