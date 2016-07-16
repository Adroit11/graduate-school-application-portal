<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Programs extends Page_Controller {

    private $_data;

    public function __construct() {
        parent::__construct();
        $d = array();
        $this->db_options->set_var('record', 'programs');
        if (($data = $this->db_options->get()) && !empty($data)) {
            $rgx = '/^(.*)\_(.*)\_(?:\d+)$/';
            foreach ($data as $k => $v) {
                preg_match($rgx, $k, $m);
                if (count($m) === 3) {
                    $program = $m[1];
                    $type = $m[2];
                    if ($type === 'content') {
                        $d[$program]['content'] = $v;
                    } else if ($type === 'course') {
                        $d[$program]['courses'][] = $v;
                    }
                }
            }
            $this->_data = array_to_object($d);
        }
    }

    public function index() {
        redirect('programs/business');
    }

    public function business() {
        $data = array();
        if (isset($this->_data->business)) {
            $data['content'] = $this->_data->business;
        }
        $this->stencil->title('Business programs');
        $this->stencil->meta(
                array('description' => 'Business programs')
        );
        $this->stencil->paint('programs/business_view', $data);
    }

    public function education() {
        $data = array();
        if (isset($this->_data->education)) {
            $data['content'] = $this->_data->education;
        }
        $this->stencil->title('Education programs');
        $this->stencil->meta(
                array('description' => 'Education programs')
        );
        $this->stencil->paint('programs/education_view', $data);
    }

    public function engineering_it() {
        $data = array();
        if (isset($this->_data->engineering_it)) {
            $data['content'] = $this->_data->engineering_it;
        }
        $this->stencil->title('Engineering & IT programs');
        $this->stencil->meta(
                array('description' => 'Business programs')
        );
        $this->stencil->paint('programs/engineering_it_view', $data);
    }

    public function nursing() {
        $data = array();
        if (isset($this->_data->nursing)) {
            $data['content'] = $this->_data->nursing;
        }
        $this->stencil->title('Nursing programs');
        $this->stencil->meta(
                array('description' => 'Medicine programs')
        );
        $this->stencil->paint('programs/nursing_view', $data);
    }

}