<?php

class Developer_Pages_Controller extends Developer_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var(array(
            'module' => 'page'
        ));

        $this->stencil->layout('developer/pages');

        $this->stencil->slice(array(
            'developer_pages_nav' => 'developer/developer_pages_nav',
        ));
    }

}