<?php

class MY_Controller extends CI_Controller {

    protected $id;
    protected $dbutil;
    protected $refresh_token_name = 'ci_refresh_token';
    public $tbl;
    public $errors = array();

    public function __construct() {
        parent::__construct();

        $this->_set_tbl();

        if (get_config('maintenance') === true) {
            $maintenance_uri = 'maintenance';
            if ($this->uri->uri_string() != $maintenance_uri) {
                redirect($maintenance_uri);
            }
        }
    }

    protected function _object_to_array($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        return is_array($d) ? array_map(__METHOD, $d) : $d;
    }

    protected function _array_to_object($d) {
        return is_array($d) ? (object) array_map(__METHOD__, $d) : $d;
    }

    private function _set_tbl() {
        $tbls = array(
            'emails' => 'emails',
            'options' => 'options',
            'pages' => 'pages',
            'pages_meta' => 'pages_meta',
            'programs' => 'programs',
            'studapp' => 'studapp',
            'studapp_meta' => 'studapp_meta',
            'users' => 'users',
            'users_meta' => 'users_meta',
        );
        $this->tbl = new stdClass();
        foreach ($tbls as $k => $v) {
            $this->tbl->$k = $v;
        }
    }

    public function output_errors($data = null, $type = 'error') {
        // if there are errors, output those first
        if (!empty($this->errors)) {
            $data = array(
                'type' => 'error',
                'data' => $this->errors
            );
            exit(json_encode($data));
        }
        // output if data is not empty
        if ($data != '') {
            $data = array(
                'type' => $type,
                'data' => is_array($data) ? $data : array($data),
            );
            exit(json_encode($data));
        }
    }

    public function is_json($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function array_json_decode($str, $as_array = false) {
        if ($this->is_json($str)) {
            return json_decode($str, $as_array);
        }
        return $str;
    }

    public function array_json($arr) {
        if (is_array($arr) || is_object($arr)) {
            return json_encode($arr);
        }
        return $arr;
    }

    protected function _sess_login($d) {
        switch ($d->user_role) {
            case '0':
                $role = 'developer';
                break;
            case '2':
                $role = 'admin';
                break;
            default:
            case '1':
                $role = 'user';
        }
        $data = array(
            'logged_in' => true,
            'id' => $d->user_id,
            'fname' => $d->user_fname,
            'lname' => $d->user_lname,
            'role' => $role,
        );
        // save the new session
        $this->session->set_userdata($data);
    }

    protected function _sess_delete() {
        // delete the refresh token
        if (($id = $this->session->userdata('id'))) {
            $this->load->library('db_user');
            $this->db_user->delete_meta($this->refresh_token_name, $id);
        }
        $this->session->sess_destroy();
        // delete the token saved in the database
        delete_cookie($this->refresh_token_name);
        delete_cookie('ci_setinterval');
    }

    public function fv_valid_url($url) {
        if ($url != '' && filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $this->form_validation->set_message(__FUNCTION__, sprintf('The %s field uses an invalid URL: %1$s', $url));
            return false;
        }
    }

    public function fv_email_available($email) {
        // for account email change only
        // allow the email if it's the same as the old one
        if (isset($this->old_email)) {
            if ($this->old_email == $email) {
                return;
            }
        }

        // check availability of entered email
        $data = array(
            'user_email' => $email,
        );
        $query = $this->db->get_where('users', $data);

        // check for any other matches
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message(__FUNCTION__, sprintf('The email %1$s has already been taken.', $email));
            return false;
        }
    }

    public function fv_valid_phone($num) {
        if ($num == '')
            return;

        $rgx = '/\++\d{2}\s\(\d{3}\)\s\d{3}\-\d{4}/';
        if (!preg_match($rgx, $num)) {
            $this->form_validation->set_message(__FUNCTION__, 'The phone number you entered uses an incorrect format.');
            return false;
        }
    }

    public function fv_valid_date($date) {
        if ($date == '')
            return;

        $rgx = '|\d{2}/\d{2}/\d{1,4}|';
        if (!preg_match($rgx, $date)) {
            $this->form_validation->set_message(__FUNCTION__, 'The date you entered uses an incorrect format.');
            return false;
        }
    }

    public function fv_correct_pass($pass) {
        $where = array(
            'user_id' => $this->id,
            'user_pass' => sha1($pass),
        );
        $this->db->select('user_id');
        $query = $this->db->get_where($this->tbl->users, $where);

        if ($query->num_rows() <= 0) {
            $this->form_validation->set_message(__FUNCTION__, 'The password you entered is incorrect.');
            return false;
        }
    }

    public function fv_same_pass($pass) {
        $where = array(
            'user_id' => $this->id,
            'user_pass' => sha1($pass),
        );
        $this->db->select('user_id');
        $query = $this->db->get_where($this->tbl->users, $where);

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message(__FUNCTION__, 'The new password you entered is the same as your current one.');
            return false;
        }
    }

}