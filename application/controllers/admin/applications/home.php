<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Admin_Applications_Controller {

    // applicant variables
    var $user_id;
    var $user_email;
    var $user_status_new;
    var $user_status_old;

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var(array(
            'module' => 'studapp',
            'record' => 'status',
        ));
    }

    public function index($offset = 0) {
        $data = array();
        
        $limit = 5;
        $data['users'] = $this->_get_users($offset, $limit, $calc_rows);
        $data['params'] = $this->input->get();
        $data['total_rows'] = $calc_rows;
        $data['programs'] = $this->_db_get_parent_programs();
        
        // build the pagination
        $config = array(
            'base_url' => site_url('admin/applications/home/index'),
            'total_rows' => $calc_rows,
            'per_page' => $limit,
            'num_links' => 4,
            'uri_segment' => 5,
            'full_tag_open' => '<ul class="pagination pagination-lg">',
            'full_tag_close' => '</ul>',
        );
        
        if (($qs = $_SERVER['QUERY_STRING']) != '') {
            $config['suffix'] = '?' . $qs;
        }

        $this->load->library('pagination', $config);
        $data['pagination'] = $this->pagination->create_links();

        $this->stencil->layout('default');
        $this->stencil->title('Applications');
        $this->stencil->meta(array(
            'description' => 'Manage user applications'
        ));
        $this->stencil->paint('admin/applications/home_view', $data);
    }

    private function _get_users($offset = 0, $limit = 0, &$calc_rows = 0) {
        $data = array();

        $u = $this->tbl->users;
        $um = $this->tbl->users_meta;

        // set up filters
        $valid_statuses = array(
            'review',
            'test',
            'test_reschedule',
            'test_fail',
            'interview',
            'interview_pass',
            'interview_fail',
            'interview_decline',
            'enroll',
            'withdraw',
            'revision',
        );
        
        if (($name_param = $this->input->get('name')) && $name_param != '') {
            $name_filter = sprintf('AND u.user_fname LIKE "%%%1$s%%" OR u.user_lname LIKE "%%%1$s%%"', $this->db->escape_like_str($name_param));
        } else {
            $name_filter = null;
        }
        
        $status_filter = $this->_build_filter('status.meta_value', 'status', $valid_statuses);

        $valid_program_parents = array();
        foreach ($this->_db_get_parent_programs() as $k => $v) {
            $valid_program_parents[] = $v->program_id;
        }
        
        $program_filter = $this->_build_filter('program_parent.meta_value', 'program', $valid_program_parents);

        $type_filter = $this->_build_filter('type.meta_value', 'type', array(1, 2));

        $gender_filter = $this->_build_filter('gender.meta_value', 'gender', array('mr', 'ms'));

        $citizenship_filter = $this->_build_filter('citizenship.meta_value', 'citizenship', array('filipino', 'foreign', 'naturalized'));

        $date_applied_filter = $this->_build_filter_date_applied();

        $country_filter = $this->_build_filter_country();

        $profession_filter = $this->_build_filter_profession();
        
        $order_filter = $this->_build_order_filter();
        
        // $result_filter = sprintf('LIMIT %1$s OFFSET %2$s', (int) abs($limit), (int) abs($offset));
        $result_filter = sprintf('LIMIT %1$s, %2$s', (int) abs($offset), (int) abs($limit));

        $sql = <<<EOT
                SELECT
                    SQL_CALC_FOUND_ROWS *,
                    u.user_id AS id,
                    CONCAT (u.user_fname, ' ', u.user_lname) AS name,
                    u.user_role AS role,
                    status.meta_value AS status,
                    type.meta_value AS type,
                    program.meta_value AS program,
                    date.meta_value AS date
                FROM
                $u u
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_status') AS status
                ON
                    u.user_id = status.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_program_parent') AS program_parent
                ON
                    u.user_id = program_parent.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_program_type') AS type
                ON
                    u.user_id = type.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_program_title') AS program
                ON
                    u.user_id = program.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_date') AS date
                ON
                    u.user_id = date.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_basic_title') AS gender
                ON
                    u.user_id = gender.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_basic_citizenship') AS citizenship
                ON
                    u.user_id = citizenship.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_basic_country') AS country
                ON
                    u.user_id = country.user_id
                INNER JOIN
                    (SELECT meta_value, user_id FROM $um um WHERE meta_key = 'studapp_basic_profession') AS profession
                ON
                    u.user_id = profession.user_id
                WHERE
                    u.user_status = 0
                AND
                    u.user_role = 1
                $name_filter
                $status_filter
                $program_filter
                $type_filter
                $gender_filter
                $citizenship_filter
                $date_applied_filter
                $country_filter
                $profession_filter
                $order_filter
                $result_filter
EOT;

        // execute query
        $query = $this->db->query($sql);
        
        // count the number of rows (disregarding limit and offset)
        $query_calc_rows = $this->db->query('SELECT FOUND_ROWS()');
        $calc_rows = array_pop($query_calc_rows->row_array());

        // sandbox
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            for ($i = 0; $i < count($data); $i++) {
                foreach ($data[$i] as $k => $v) {
                    switch ($k) {
                        case 'status':
                            $data[$i][$k] = $this->get_status_friendly($v);
                            break;
                        case 'date':
//                            $data[$i][$k] = date('d M Y', time($v));
                            $data[$i][$k] = date_nice($v);
                            break;
                    }
                }
            }
            return array_to_object($data);
        }

        return false;
    }

    public function get_status() {
        // get the applicant's status
        if (!($id = $this->input->get('id'))) {
            exit('0');
        }

        // get the meta
        $mval = $this->db_user->get_meta(null, $id);

        // if no value was returned
        if (!$mval) {
            exit('0');
        }

        // return the value
        exit($mval);
    }

    public function get_status_friendly($status = '', $echo = false) {
        // convert status to friendly name
        switch ($status) {
            case 'review':
                $status = 'Pending review';
                break;
            case 'test':
                $status = 'Entrance examination scheduled';
                break;
            case 'test_reschedule':
                $status = 'Entrance examination rescheduled';
                break;
            case 'test_fail':
                $status = 'Entrance examination failed';
                break;
            case 'interview':
                $status = 'Pending interview';
                break;
            case 'interview_reschedule':
                $status = 'Interview rescheduled';
                break;
            case 'interview_fail':
                $status = 'Interview failed';
                break;
            case 'interview_pass':
                $status = 'Interview passed';
                break;
            case 'interview_decline':
                $status = 'Interview declined';
                break;
            case 'enroll':
                $status = 'Already enrolled';
                break;
            case 'withdraw':
                $status = 'Withdrew application';
                break;
            case 'revision':
                $status = 'Awaiting revision';
                break;
            default:
                $status = 'Unknown';
        }

        // useful for ajax requests
        if ($echo)
            echo $status;
        else
            return $status;
    }

    public function update_status() {
        if (!$this->input->post()) {
            $this->output_errors('There was an error saving data to the server.');
        }

        // get the new user status
        if (!($this->user_status_new = $this->input->post('status_new'))) {
            $this->output_errors('An applicant status must be set.');
        }

        // mandatory fields
        $rules = array(
            array(
                'field' => 'id',
                'label' => 'User ID',
                'rules' => 'trim|required|callback_fv_valid_account|callback_fv_valid_application_status',
            ),
            array(
                'field' => 'subject',
                'label' => 'Subject',
                'rules' => 'trim|required',
            ),
            array(
                'field' => 'message',
                'label' => 'Message',
                'rules' => 'trim|required',
            ),
        );

        // case-by-case additional fields
        switch ($this->user_status_new) {
            // interview and reschedule_interview both require date & time fields 
            case 'test':
            case 'test_reschedule':
            case 'interview':
            case 'interview_reschedule':
                $rules[] = array(
                    'field' => 'time',
                    'label' => 'Time',
                    'rules' => 'trim|required',
                );
                $rules[] = array(
                    'field' => 'date',
                    'label' => 'Date',
                    'rules' => 'trim|required',
                );
                break;
        }

        $this->form_validation->output_errors($rules);

        $msg_vars = array();

        foreach ($rules as $rule) {
            // set the meta key
            $field = $rule['field'];
            switch ($field) {
                default:
                    $msg_vars[$field] = $this->input->post($field);
            }
        }

        $msg_vars = array_to_object($msg_vars);

        // load the email library first
        $this->load->library('email');

        // build the email message
        $msg = <<<EOT
<strong>Subject:</strong> $msg_vars->subject

<strong>Message:</strong>
$msg_vars->message
EOT;
        // if time and date are set, append
        if (isset($msg_vars->time) && isset($msg_vars->date)) {
            $msg .= <<<EOT
<br />
<strong>Time:</strong> $msg_vars->time

<strong>Date:</strong> $msg_vars->date
EOT;
        }

        // email config
        $email_data = array(
            'to' => $this->user_email,
            'subject' => $msg_vars->subject,
            'message' => $msg,
        );

        // send the email and update the status
        if ($this->db_user->save_meta($this->user_status_new, null, $this->user_id)) {
            if (!$this->email->dispatch($email_data)) {
                $this->errors[] = 'Could not email user at this time.';
                // if email was not sent, revert to the old status
                if (!$this->db_user->save_meta($this->user_status_old, null, $this->user_id)) {
                    $this->errors[] = 'Could not revert to the old application status';
                }
            } else {
                // save the email message to the database
                $this->db_user->set_var('record', 'last_email');
                if (!$this->db_user->save_meta($email_data['message'], null, $this->user_id)) {
                    $this->errors[] = 'Could not save the email sent to the user.';
                }
            }
        } else {
            $this->errors[] = 'Could not change the user application status.';
        }

        $this->output_errors('Successfully emailed user. The application status has also been updated.', 'success');
    }
    
    private function _build_order_filter() {
        // sort results
        if (($order_by = $this->input->get('order_by'))) {
            $order_by = strtolower($order_by);
            $valid_cols = array(
                'id' => 'u.user_id',
                'name' => 'u.user_lname',
                'role' => 'u.user_role',
                'status' => 'status.meta_value',
                'type' => 'type.meta_value',
                'program' => 'program.meta_value',
                'date' => 'date.meta_value'
            );
            if (array_key_exists($order_by, $valid_cols)) {
                $order = $this->input->get('order');
                return sprintf('ORDER BY %1$s %2$s', $valid_cols[$order_by], ($order && strtolower($order) === 'asc') ? 'ASC' : 'DESC');
            }
        } else {
            return 'ORDER BY date.meta_value DESC, u.user_lname ASC, program.meta_value ASC';
        }
        
        return null;
    }

    private function _build_filter($col, $get_key, $valid) {
        if (($get_filters = $this->input->get($get_key)) && is_array($get_filters)) {
            $get_array = array();
            foreach ($get_filters as $v) {
                if (in_array($v, $valid)) {
                    $get_array[] = $v;
                }
            }
            if (!empty($get_array)) {
                $str = "'" . join("','", $get_array) . "'";
                return "AND $col IN ($str)";
            }
        }
        return null;
    }

    private function _build_filter_date_applied() {
        if (!($date_applied = $this->input->get('date_applied'))) {
            return null;
        }

        switch ($date_applied) {
            case 'week':
                $interval = '7 DAY';
                break;
            case 'month':
                $interval = '1 MONTH';
                break;
            case 'month_3':
                $interval = '3 MONTH';
                break;
            case 'month_6':
                $interval = '6 MONTH';
                break;
            case 'year':
                $interval = '1 YEAR';
                break;
            case 'year_2':
                $interval = '2 YEAR';
                break;
        }

        if (isset($interval)) {
            return sprintf('AND date.meta_value >= DATE_SUB(CURDATE(), INTERVAL %1$s)', $interval);
        }

        return null;
    }

    private function _build_filter_country() {
        if (!($country = $this->input->get('country'))) {
            return null;
        }

        return sprintf('AND country.meta_value = "%1$s"', strtoupper($country));
    }

    private function _build_filter_profession() {
        if (!($profession = $this->input->get('profession'))) {
            return null;
        }

        return sprintf('AND profession.meta_value LIKE "%%%1$s%%"', $profession);
    }

    private function _db_get_parent_programs() {
        $where = array(
            'program_parent' => 0,
        );
        $this->db->select('program_title, program_id');
        $query = $this->db->get_where($this->tbl->programs, $where);
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function fv_valid_application_status($id) {
        $valid_statuses = array(
            'review', // the user is pending review from admin
            'test', // entrance examination has been scheduled
            'test_schedule', // entrance examination has been rescheduled
            'test_fail', // applicant has failed the entrance examination
            'interview', // admin has sent interview request
            'interview_reschedule', // admin has rescheduled interview
            'interview_fail', // applicant has failed interview
            'interview_pass', // applicant has passed interview
            'interview_decline', // admin has declined interview and will not entertain applicant any further
            'enroll', // applicant has already enrolled (paid dues, etc.)
            'withdraw', // applicant has withdrawn application
        );

        // check if the new status being set is valid
        if (!$this->user_status_new && !in_array($this->user_status_new, $valid_statuses)) {
            $this->form_validation->set_message(__FUNCTION__, 'The new status you\'re trying to set is invalid.');
            return false;
        }

        // get the old status
        $this->user_status_old = $this->db_user->get_meta(null, $id);

        // check if the old status is a valid status
        if (!$this->user_status_old && !in_array($this->user_status_old, $valid_statuses)) {
            $this->form_validation->set_message(__FUNCTION__, 'The user you\'re trying trying to modify has an invalid application status.');
            return false;
        }

        // check if the new status we're trying to set is the same as the old status
        // note that we're exiting prematurely
        if ($this->user_status_new === $this->user_status_old) {
            $this->form_validation->set_message(__FUNCTION__, 'The user\'s new application status has already been set.');
            return false;
        }

        return true;
    }

    public function fv_valid_account($id) {
        $this->user_id = $id;
        // get the pertinent data
        $userdata = $this->db_user->get('user_email, user_role, user_status', null, $this->user_id);

        // check if the user actually exists
        if (!$userdata) {
            $this->form_validation->set_message(__FUNCTION__, 'The user you\'re trying to modify does not exist.');
            return false;
        }

        // check if the role is a user
        if ($userdata->user_role != 1) {
            $this->form_validation->set_message(__FUNCTION__, 'The user you\'re trying to modify is not an applicant.');
            return false;
        }

        // check if the user is active
        if ($userdata->user_status != 0) {
            $this->form_validation->set_message(__FUNCTION__, 'The user you\'re trying to modify has some unresolved issues.');
            return false;
        }

        // set the email
        $this->user_email = $userdata->user_email;

        return true;
    }

}