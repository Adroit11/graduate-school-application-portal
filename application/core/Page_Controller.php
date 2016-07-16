<?php

class Page_Controller extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // load db_options library
        $this->load->library('db_options', array(
            'module' => 'page',
        ));
        /*
         * Begin Stencil params
         */
        $this->stencil->layout('default');
        switch ($this->session->userdata('role')) {
            case 'user':
                $role_nav = 'user/role_nav';
                break;
            case 'admin':
                $role_nav = 'admin/role_nav';
                break;
            case 'developer':
                $role_nav = 'developer/role_nav';
                break;
            default:
                $role_nav = 'role_nav';
        }
        $this->stencil->slice(array(
            'footer' => 'footer',
            'top_nav' => 'top_nav',
            'role_nav' => $role_nav,
        ));
        $this->stencil->title('Untitled page');
        $this->stencil->meta(array(
            'author' => 'JP Caparas',
            'google-site-verification' => 'ofUiYbZSiLnB1fFPvJg13I159K5P51aTHkzrOINITJU',
        ));
        $this->stencil->css(array(
            'bootstrap.min',
            'font-awesome-4.0.3/css/font-awesome.css',
            'style',
        ));
        $this->stencil->js(array(
            'jquery',
            'jquery.json-2.4.min',
            'jquery-cookie/jquery.cookie',
            'jquery.scrollTo.min',
            'bootstrap.min',
            'holder',
            'custom.functions',
            'custom.hooks',
        ));
    }

}

?>