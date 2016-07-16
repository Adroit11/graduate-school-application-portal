<?php

class MY_Upload extends CI_Upload {

    public function errors_array() {
        return $this->error_msg;
    }

    public function reset_errors_array() {
        $this->error_msg = array();
    }

}