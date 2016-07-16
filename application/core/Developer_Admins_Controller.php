<?php

class Developer_Admins_Controller extends Developer_Controller {

    public function __construct() {
        parent::__construct();

        $this->stencil->layout('default');
    }

}