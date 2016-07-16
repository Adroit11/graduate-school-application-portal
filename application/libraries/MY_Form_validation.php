<?php

class MY_Form_validation extends CI_Form_validation {

    public function error_array() {
        return $this->_error_array;
    }

    public function tos($str, $comp) {
        $comp = !$comp ? 1 : $comp;
        if ($str != $comp) {
            $this->set_message('tos', 'You must agree to %s');
            return false;
        }
        return true;
    }

    public function output_errors($rules) {
        $this->set_rules($rules);
        if (!$this->run()) {
            $data = array(
                'type' => 'error',
                'data' => $this->error_array(),
            );
            exit(json_encode($data));
        }
    }

}