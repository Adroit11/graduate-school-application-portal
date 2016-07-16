<?php

class Admin_Account_Controller extends Admin_Controller {

    public function __construct() {
        parent::__construct();

        $this->db_user->set_var('module', 'account');
        
        $this->stencil->layout('admin/account');

        $this->stencil->slice(array(
            'account_nav' => 'admin/account_nav',
        ));
    }

}