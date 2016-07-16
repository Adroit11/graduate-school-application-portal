<?php

class Admin_Applications_Controller extends Admin_Controller {

    public function __construct() {
        parent::__construct();

        $this->stencil->layout('admin/applications');

        $this->stencil->slice(array(
            'applicants_nav' => 'admin/applications_nav',
        ));
    }

}