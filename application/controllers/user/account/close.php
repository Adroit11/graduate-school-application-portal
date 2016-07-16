<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Close extends User_Account_Controller {

    public function index() {
        $this->stencil->title('Close Account');
        $this->stencil->meta(
                array('description' => 'Delete your account')
        );
        $this->stencil->paint('user/account/close_view');
    }

    public function submit() {
        if (!$this->input->post()) {
            show_404();
        }

        $rules = array(
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

        if (!$this->_db_delete_user()) {
            $this->errors[] = 'Your account could not be deleted at this time.';
        }

        $this->output_errors('Your account has been successfully deleted.', 'success');
    }

    private function _db_delete_user() {
        // get the email before it gets deleted
        $email = $this->db_user->get_email();

        $tbls = array(
            $this->tbl->users,
            $this->tbl->users_meta,
        );

        $where = array(
            'user_id' => $this->id,
        );

        $this->db->delete($tbls, $where);

        if ($this->db->affected_rows() > 0) {
            // send the goodbye email
            $this->db_options->set_var(array(
                'module' => 'email_template',
                'record' => 'account_deleted',
            ));
            if (($deletion_email = $this->db_options->get())) {
                $data = array(
                    'to' => $email,
                    'subject' => $deletion_email->subject,
                    'message' => $deletion_email->message,
                );
                $this->email->dispatch($data);
            }

            // delete the session
            $this->_sess_delete();
            return true;
        }

        return false;
    }

}