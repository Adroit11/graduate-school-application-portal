<?php

class User_Apply_Controller extends User_Controller {

    public $index;
    public $min_index;
    public $max_index;
    public $db_prefix;

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var('module', 'studapp');

        // check if the account is pending review; if yes, redirect to checkpoint
        if (($status = $this->db_user->get_meta('studapp_status'))) {
            // the "revision" status is allowed
            if ($status !== 'revision') {
                $redirect_uri = 'user/apply/checkpoint';
                if (uri_string() !== $redirect_uri)
                    redirect($redirect_uri);
            }
        }

        $this->stencil->layout('user/apply');

        $this->stencil->slice(array(
            'apply_nav' => 'user/apply_nav',
        ));
    }

    public function fv_valid_index() {
        $index = $this->input->post('index');
        $index = (int) abs($index);
        if ($index > $this->max_index || $index < $this->min_index) {
            $this->form_validation->set_message(__FUNCTION__, 'The entry index is invalid.');
            return false;
        }
    }

}