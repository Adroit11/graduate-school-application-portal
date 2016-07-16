<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Contact_Developer extends Admin_Controller {

    public function index() {
        $this->stencil->title('Contact Developer');
        $this->stencil->meta(array(
            'description' => 'Contact the application developer'
        ));
        $this->stencil->paint('admin/contact_developer_view');
    }

    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'subject',
                'label' => 'Subject',
                'rules' => 'trim|required|min_length[5]|max_length[20]',
            ),
            array(
                'field' => 'short_description',
                'label' => 'Short description',
                'rules' => 'trim|required|max_length[100]|',
            ),
            array(
                'field' => 'long_description',
                'label' => 'Long description',
                'rules' => 'trim|required|max_length[2000]'
            ),
        );

        $this->form_validation->output_errors($rules);
        
        // if there are no errors, proceed sending the email

        $user = $this->db_user->get('user_email, user_fname, user_lname');
        
        $sender = $user->user_fname . ' ' . $user->user_lname;
        $subject = $this->input->post('subject');
        $short_d = $this->input->post('short_description');
        $long_d = $this->input->post('long_description');
        $msg = <<<EOT
<strong>Sender:</strong> $sender
                
<strong>Subject:</strong>  $subject
                
<strong>Short Description:</strong>
$short_d
    
<strong>Long Description:</strong>
$long_d
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

        $this->output_errors('Your request has been sent. The average resolution time is one week.', 'success');
    }

}