<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Checkpoint extends User_Apply_Controller {

    public function index() {
        $data = array();
        $this->stencil->layout('default');
        $this->stencil->title('Checkpoint');
        $this->stencil->meta(
                array('description' => 'You are probably here because your application is pending review')
        );

        $data['status'] = object_pop($this->db_user->get_meta('studapp_status'));
        $data['last_email'] = object_pop($this->db_user->get_meta('studapp_last_email'));
        $this->stencil->paint('user/apply/checkpoint_view', $data);
    }

}