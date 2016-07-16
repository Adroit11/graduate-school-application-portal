<?php

class Auth_Controller extends Page_Controller {

    public function __construct() {
        parent::__construct();
        // load the db user convenience class
        $this->load->library('db_user');

        $this->_test_authentication();

        $this->id = $this->session->userdata('id');
        $this->db_user->set_var('id', $this->id);

        $this->stencil->meta(array(
            // the two meta tags below are to prevent Google from indexing the site
            // from https://support.google.com/webmasters/answer/93710
            'robots' => 'noindex',
            'googlebot' => 'noindex',
        ));

        $this->stencil->css(array(
            'jquery.loadmask',
            'bootstrap-formhelpers.min',
        ));
        $this->stencil->js(array(
            'jquery.loadmask.min',
            'bootstrap-formhelpers.min',
            'cms.hooks',
            'cms.functions',
        ));
    }

    private function _test_authentication() {
        // prevent unauthorized users from accessing private pages
        if ($this->session->userdata('logged_in')) {
            return;
        }
        // check if there is an refresh token from the client browser
        // if there is, keep the user logged in by creating a new session
        // check if the refresh token has a database match
        if (($refresh_token = get_cookie($this->refresh_token_name))) {
            $match = $this->db_user->get_meta($this->refresh_token_name, $refresh_token);
            if ($match && isset($match->id) && $match->id) {
                // preserve session
                $data = $this->db_user->get(null, null, $match->id);
                $this->_sess_login($data);
                return;
            }
        }
        redirect(sprintf('/login?goto=%1$s', urlencode(current_url())));
    }

}