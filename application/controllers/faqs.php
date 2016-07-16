<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Faqs extends Page_Controller {

    public function index() {
        $data = $d = array();
        
        // format into an easy-to-use array
        $this->db_options->set_var('record', 'faqs');
        if (($d = $this->db_options->get())) {
            $d = object_to_array($d);
            $rgx = '/(.*)_(.*)_(\d+)/';
            foreach($d as $k => $v) {
                preg_match($rgx, $k, $m);
                $data['content'][$m[3]][$m[2]] = $v;
            }
           $data['content'] = array_to_object($data['content']);
        }
        
        $this->stencil->title('Frequently Asked Questions (FAQs)');
        $this->stencil->meta(array(
            'description' => 'Lost? Find answers to commonly asked questions here.'
        ));
        $this->stencil->data(array(
        ));
        $this->stencil->paint('faqs_view', $data);
    }

}