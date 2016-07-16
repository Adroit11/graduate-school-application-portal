<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class View extends Admin_Applications_Controller {

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var(array(
            'module' => 'studapp',
        ));
        $this->load->helper('country_code');
        $this->stencil->layout('default');
        $this->stencil->title('Applications');
        $this->stencil->meta(array(
            'description' => 'Manage user applications'
        ));
    }

    public function index($id = null) {
        $id = (int) abs($id);
        if (!$id) {
            redirect('admin/applications');
        }
        
        $this->db_user->set_var(array(
            'id' => $id,
        ));

        $data = array();

        if (!($data['user'] = $this->db_user->get())) {
            $this->_show_output('That user does not exist.');
            return;
        }

        if ($data['user']->user_role != 1) {
            $this->_show_output('That user is not an applicant.');
            return;
        }

        if (!($this->_get_meta('basic', $data, true, true))) {
            $this->_show_output('The applicant is lacking basic information.');
            return;
        }
        
        if (!($this->_get_meta('essay', $data))) {
            $this->_show_output('The applicant is lacking an essay.');
            return;
        }
        
        if (!($this->_get_meta('documents_birth', $data))) {
            $this->_show_output('The applicant is lacking a birth certificate.');
            return;
        }
        
        if (!($this->_get_meta('documents_tor', $data))) {
            $this->_show_output('The applicant is lacking a transcript of records.');
            return;
        }
        
        // (optional, employer recommendation)
        $this->_get_meta('documents_employer', $data);
        
        if (!($this->_get_meta('recommendations_1', $data))) {
            $this->_show_output('The applicant is lacking the first professor recommendation.');
            return;
        }
        
        if (!($this->_get_meta('recommendations_2', $data))) {
            $this->_show_output('The applicant is lacking the second professor recommendation.');
            return;
        }
        
        if (!($this->_get_meta('education', $data, true, true))) {
            $this->_show_output('The applicant is lacking educational information.');
            return;
        }
        
        $this->_show_output($data, false);
    }
    
    public function document($id = 0, $file = null) {
        $id = (int) abs($id);
        if (!$id) {
            show_404();
        }
        
        // check if the document type is specified
        if (!$file) {
            show_404();
        }
        
        // check if the user exists
        if (!($user = $this->db_user->get(null, null, $id))) {
            show_404();
        }
            
        // set the ID for fetching meta key
        $this->db_user->set_var(array(
            'id' => $user->user_id,
            'record' => 'documents_' . $file,
        ));

        // get the document_data
        if (!($data = $this->db_user->get_meta())) {
            show_404();
        }
        
        if(!($full_path = realpath(DIR_APPLICANT_UPLOADS . '/' . $data->file_name))){
            show_404();
        }
        
        // create a friendly filename
        switch ($file) {
            case 'tor':
                $friendly  = 'Transcript of Records';
                break;
            case 'birth':
                $friendly = 'Birth Certificate';
                break;
            case 'employer':
                $friendly = 'Employer Recommendation';
        }
        
        // build filename
        $new_filename = sprintf('%1$s - %2$s %3$s - %4$s', $friendly, $user->user_fname, $user->user_lname, date('Y-m-d'));

        // download file
        push_file($full_path, $new_filename);
    }

    private function _show_output($d = array(), $is_error = true) {
        $data = array();

        if ($is_error === true) {
            $data['error'] = $d;
        } else {
            $data = $d;
        }
        
        $this->stencil->paint('admin/applications/view_view', $data);
    }

    private function _get_meta($record, &$data, $multiple = false, $use_index_as_array_key = false) {
        $this->db_user->set_var('record', $record);
        return ($data[$record] = $this->db_user->get_meta(null, null, null, $multiple, $use_index_as_array_key));
    }

}