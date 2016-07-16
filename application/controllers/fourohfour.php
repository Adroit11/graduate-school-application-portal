<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FourOhFour extends Page_Controller {

    public function index() {
        header('HTTP/1.1 404 Not Found');
        $this->stencil->title(sprintf('404 Error %1$s Page does not exist', config_item('title_sep')));
        $this->stencil->meta(array(
            'description' => 'The page you requested does not exist'
        ));
        $this->stencil->data(array(
        ));
        $this->stencil->paint('fourohfour_view');
    }

}