<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends User_Apply_Controller {

    protected $token_meta_key;
    protected $program = array();

    public function __construct() {
        parent::__construct();

        // build the token
        $this->token_meta_key = sprintf('%1$s_token', $this->db_user->get_var('module'));
    }

    public function index() {
        $data = array();
        $this->load->library('db_programs');

        // fetch the program list
        $data['programs'] = $this->db_programs->get_for_forms();
        
        // get the last email sent before account went into "revision" status
        $this->db_user->set_var('record', 'last_email');
        $data['last_email'] = $this->db_user->get_meta();
        
        $this->stencil->title('Dashboard');
        $this->stencil->meta(
                array('description' => 'The dashboard is where pending application tasks are laid out')
        );
        $this->stencil->paint('user/apply/home_view', $data);
    }

    public function submit() {
        if (!($this->input->post()))
            $this->output_errors('Unable to transmit data to the server.');

        $rules = array(
            array(
                'field' => 'token',
                'label' => 'Token',
                'rules' => 'trim|required|max_length[60]|callback_fv_token_valid'
            ),
            array(
                'field' => 'program',
                'label' => 'Program',
                'rules' => 'trim|required|callback_fv_program_format_valid'
            ),
        );

        $this->form_validation->output_errors($rules);

        // save the (a) application status, (b) inclusive dates, and (c) program choices
        // from this point forward
        
        
        // save app status
        $this->db_user->set_var('record', 'status');
        if (!$this->db_user->save_meta('review')) {
            $this->output_errors('Could not change application status to "in review."');
        }

        // save app date
        $this->db_user->set_var('record', 'date');
        if (!$this->db_user->save_meta(date_mysql())) {
            $this->output_errors('Could not set the application date.');
        }
        
        // save program choices
        $this->db_user->set_var('record', 'program');
        if (!$this->db_user->save_meta_batch($this->program)) {
            $this->output_errors('Could not set the program applied for.');
        }

        // lastly, delete the submission token
        $this->db_user->set_var('record', 'token');
        if (!$this->db_user->delete_meta()) {
            $this->output_errors('Could not delete the submission token.');
        }

        exit('true');
    }

    public function progress() {
        // used both for rgx and db "like" clause
        $prefix = 'studapp_';

        // get all records that have the meta key prefix "studapp_"
        $where = array(
            'user_id' => $this->id,
        );
        $this->db->select('meta_key, meta_value');
        $this->db->like('meta_key', $prefix, 'after');
        $query = $this->db->get_where($this->tbl->users_meta, $where);

        if ($query->num_rows() <= 0) {
            return;
        }

        $rows = $query->result();

        // "data" is the variable returned
        $data = array();

        // set the required modules (each one has its own accomplishment criteria)
        $meta_required = array('basic', 'education', 'documents', 'essay', 'recommendations');

        // create the regular expressions
        $mkey_rgx = "/^([$prefix]+)_(.*)/";
        $basic_rgx = '/^(basic)_(.*)$/';
        $educ_rgx = '/^(education)_(.*)$/';
        $docs_rgx = '/^(documents)_(.*)$/';
        $rec_rgx = '/^(recommendations)_(1|2)+$/';

        // create the counters
        $rec_counter = $docs_counter = $educ_counter = 0;

        for ($i = 0; $i < count($rows); $i++) {
            $mkey = $rows[$i]->meta_key;

            // split the meta_key into two pieces
            preg_match($mkey_rgx, $mkey, $m);
            $mkey = $m[2];

            // explode the meta value
            $mval = json_decode($rows[$i]->meta_value);
            
            // check for basic matches
            // at least 1 will do
            if (preg_match($basic_rgx, $mkey, $mm)) {
                $data[$mm[1]] = true;
            }

            // check for education matches
            // at least 1 will do
            if (preg_match($educ_rgx, $mkey, $mm)) {
                $data[$mm[1]] = true;
            }

            // check for docs matches
            // at least two (birth, tor) must be present
            if (preg_match($docs_rgx, $mkey, $mm)) {
                if (in_array($mm[2], array('birth', 'tor'))) {
                    $docs_counter++;
                }
                if ($docs_counter >= 2) {
                    $data[$mm[1]] = true;
                }
            }

            // check for recommendations matches
            // the recommendaton meta value should be present
            if (preg_match($rec_rgx, $mkey, $mm)) {
                if (in_array($mm[2], array(1, 2))) {
                    if ($mval->recommendation != '') {
                        $rec_counter++ . '<br />';
                    }
                }
                if ($rec_counter === 2) {
                    $data[$mm[1]] = true;
                }
            }

            // all else
            if (in_array($mkey, $meta_required)) {
                $data[$mkey] = true;
            }
        }

        // check if all requirements have been met
        $req_count = 0;
        $req_total = count($meta_required);
        foreach ($meta_required as $req) {
            if (array_key_exists($req, $data) && $data[$req] === true) {
                $req_count++;
            }
        }

        if ($req_count === $req_total) {
            // create the token if it does not exist yet
            if (($token = $this->_db_get_token())) {
                $data['token'] = object_pop($token);
            }
        }

        exit(json_encode($data));
    }

    private function _db_get_token() {
        // set the meta key
        if (!$this->db_user->meta_key_exists($this->token_meta_key)) {
            // create the token if it does not exist
            if (($token = $this->_db_create_token())) {
                return $token;
            }
        } else {
            // fetch the existing token otherwise
            if (($token = $this->db_user->get_meta($this->token_meta_key))) {
                return $token;
            }
        }
        return false;
    }

    private function _db_create_token() {
        $token = random_string('alnum', 60);
        if ($this->db_user->insert_meta($token, $this->token_meta_key)) {
            return $token;
        }
        return false;
    }

    public function fv_token_valid($token) {
        if (!($obj = $this->db_user->get_meta($this->token_meta_key))) {
            $this->form_validation->set_message(__FUNCTION__, 'You don\'t have a validation token. Please reload this page.');
            return false;
        }
        $db_token = object_pop($obj);
        if ($token !== $db_token) {
            $this->form_validation->set_message(__FUNCTION__, 'The validation token you are using is invalid.');
            return false;
        }
    }

    public function fv_program_format_valid($str) {
        $rgx = '/(\d+)\|\|(\d+)\|\|(.+)/';
        preg_match_all($rgx, $str, $m);

        if (count($m) !== 4) {
            $this->form_validation->set_message(__FUNCTION__, 'The program value is invalid.');
            return false;
        }

        // assign each part as a class variable
        $this->program = array(
            'type' => $m[1][0],
            'parent' => $m[2][0],
            'title' => $m[3][0],
        );

        // do validation for each part (except the title)
        // the parent should exist
        $where = array(
            'program_id' => $this->program['parent'],
        );
        $query = $this->db->get_where($this->tbl->programs, $where);
        if ($query->num_rows() <= 0) {
            $this->form_validation->set_message(__FUNCTION__, 'The program parent does not exist.');
            return false;
        }

        // type should only be either (1) master's or (2) doctoral
        if (!in_array((int) abs($this->program['type']), array(1, 2))) {
            $this->form_validation->set_message(__FUNCTION__, 'The program type is invalid.');
            return false;
        }
    }

}