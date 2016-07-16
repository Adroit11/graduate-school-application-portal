<?php

class Admin_Applications_Emails_Controller extends Admin_Applications_Controller {

    public function __construct() {
        parent::__construct();

        $this->stencil->layout('admin/applications_emails');

        $this->stencil->slice(array(
            'applications_emails_nav' => 'admin/applications_emails_nav',
        ));
    }

}