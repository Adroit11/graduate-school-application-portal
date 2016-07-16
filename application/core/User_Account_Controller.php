<?php

class User_Account_Controller extends User_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var('module', 'account');

        $this->stencil->layout('user/account');

        $this->stencil->slice(array(
            'account_nav' => 'user/account_nav',
        ));
    }

}