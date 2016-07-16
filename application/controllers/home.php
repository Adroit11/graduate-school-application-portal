<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Page_Controller {

    private $_content;

    public function __construct() {
        parent::__construct();
        $this->db_options->set_var('record', 'landing');
        $this->_content = $this->db_options->get();
    }

    public function index() {
        $data = array();
        $data['content'] = $this->_content;
        $this->stencil->title('Welcome');
        $this->stencil->meta(array(
            'description' => 'Broaden your career horizons by applying at Holy Angel University Graduate School'
        ));
        $this->stencil->paint('home_view', $data);
    }

    public function calendar() {
        if (isset($this->_content->sidebar_gcal_url) && $this->_content->sidebar_gcal_url != '') {
            $url = $this->_content->sidebar_gcal_url;
        } else {
            $url = '';
        }
        $context = stream_context_create(array('https' => array('header' => 'Accept: application/xml')));
        $resp = @file_get_contents($url, false, $context);
        if ($resp === false)
            exit('error');
        header('Content-type: application/xml');
        echo $resp;
    }

}