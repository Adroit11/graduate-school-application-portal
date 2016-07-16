<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Admin_Account_Controller {

    protected $new_email;
    protected $old_email;
    protected $new_pass;
    protected $old_pass;
    protected $activation_key;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = array();
        
        // get misc. data
        $user = $this->db_user->get('user_email, user_fname, user_lname');
        $phone = $this->db_user->get_meta('account_phone');
        $record = array(
            'email' => $user->user_email,
            'fname' => $user->user_fname,
            'lname' => $user->user_lname,
            'phone' => $phone,
        );
        $data['record'] = array_to_object($record);
        
        $this->stencil->title('Account');
        $this->stencil->meta(
                array('description' => 'General account settings')
        );
        $this->stencil->paint('admin/account/home_view', $data);
    }

    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');

        $this->old_email = $this->db_user->get_email();
        $this->new_email = $this->input->post('email');
        $this->old_pass = $this->input->post('password');
        $this->new_pass = $this->input->post('newpassword');
        
        $rules = array(
            array(
                'field' => 'email',
                'label' => 'Email address',
                'rules' => 'trim|required|valid_email|callback_fv_email_available',
            ),
            array(
                'field' => 'fname',
                'label' => 'First name',
                'rules' => 'trim|required|max_length[35]',
            ),
            array(
                'field' => 'lname',
                'label' => 'Last name',
                'rules' => 'trim|required|max_length[35]',
            ),
            array(
                'field' => 'phone',
                'label' => 'Mobile number',
                'rules' => 'trim|max_length[35]|callback_fv_valid_phone',
            ),
            array(
                'field' => 'newpassword',
                'label' => 'New password',
                'rules' => 'trim|min_length[6]|max_length[20]|matches[newpasswordconf]|callback_fv_same_pass',
            ),
            array(
                'field' => 'newpasswordconf',
                'label' => 'Confirm new password',
                'rules' => 'trim|min_length[6]|max_length[20]',
            ),
            array(
                'field' => 'password',
                'label' => 'Current password',
                'rules' => 'trim|required|callback_fv_correct_pass',
            ),
        );

        $this->form_validation->output_errors($rules);
        
        $changes = array();
        
        // save first name and last name ahead of the rest
        $data = array(
            'user_fname' => $this->input->post('fname'),
            'user_lname' => $this->input->post('lname')
        );
        if ($this->db_user->save($data)) {
            $changes[] = 'Your name has been updated.';
        }
        
        // save phone number
        if ($this->db_user->save_meta($this->input->post('phone'), 'account_phone')) {
            $changes[] = 'Your phone number has been updated.';
        }
        
        // change email if it's not the same with the old one
        if ($this->old_email != $this->new_email) {
            // create a new activation key
            $this->activation_key = random_string('alnum', 60);

            // set the new email and activation key
            $data = array(
                'user_email' => $this->new_email,
                'user_activation_key' => $this->activation_key,
            );

            if ($this->db_user->update($data)) {
                // if the email was changed, email the user about the change.
                if ($this->_email_revert_email()) {
                    $changes[] = 'Your email has been updated successfully.';
                } else {
                    // revert to the old email if unsuccessful
                    $data = array(
                        'user_email' => $this->old_email,
                    );

                    $this->db_user->update($data);
                }
            }
        }

        // change the pass if it isn't blank
        if ($this->new_pass) {
            $data = array(
                'user_pass' => sha1($this->new_pass),
            );

            if ($this->db_user->update($data)) {
                $changes[] = 'Your password has been changed successfully.';
            }
        }

        // if there were changes output the results
        if (!empty($changes))
            $this->output_errors($changes, 'success');
        else
        // otherwise, just show a success message // even though nothing was changed
            $this->output_errors('Some settings were not saved.');
    }

    private function _email_revert_email() {
        $data = array(
            'to' => $this->new_email,
            'subject' => 'Activate your account',
            'message' => sprintf('<p>Please reactivate your account by clicking %1$s.</p>', anchor('login?action=activate&key=' . $this->activation_key . '&email=' . urlencode($this->new_email), 'this link')),
        );
        if ($this->email->dispatch($data)) {
            return true;
        }
        return false;
    }

}