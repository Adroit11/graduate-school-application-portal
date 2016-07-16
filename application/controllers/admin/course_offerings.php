<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Course_Offerings extends Admin_Controller {

    private $data = array();

    public function index() {
        $data = array();
        $this->load->library('db_programs');

        $data['records'] = $this->db_programs->get();
        $this->stencil->title('Course Offerings');
        $this->stencil->meta(array(
            'description' => 'Manage program offerings'
        ));
        $this->stencil->paint('admin/course_offerings_view', $data);
    }

    public function submit() {
        if (!$this->input->post())
            $this->output_errors('Cannot connect to the database');

        $rules = array(
            array(
                'field' => 'orig_program_title',
                'label' => 'Original Program Title',
                'rules' => 'trim|max_length[100]|callback_fv_orig_title_exists'
            ),
            array(
                'field' => 'program_title',
                'label' => 'Program Title',
                'rules' => 'trim|required|max_length[100]|callback_fv_title_exists'
            ),
            array(
                'field' => 'program_type',
                'label' => 'Program Type',
                'rules' => 'trim|required|min_value[1]|max_value[2]'
            ),
            array(
                'field' => 'program_parent',
                'label' => 'Program Parent',
                'rules' => 'trim|required|callback_fv_parent_valid'
            ),
        );

        $this->form_validation->output_errors($rules);

        foreach ($rules as $rule) {
            $field = $rule['field'];
            $this->data[$field] = $this->input->post($field);
        }
        $this->data = array_to_object($this->data);

        // if it's blank, insert
        if (($this->data->orig_program_title == '')) {
            if (!$this->_db_insert_course()) {
                $this->errors[] = 'Could not add the course. Please try again.';
            }
        } else {
            // if not, update
            if (!$this->_db_update_course()) {
                $this->errors[] = 'Course was not modified. Please try again.';
            }
        }

        $this->output_errors('Course details successfully saved.', 'success');
    }

    public function delete() {
        if (!$this->input->post())
            exit('Your request is invalid');
        
        $where = array(
            'program_title' => $this->input->post('program_title'),
        );
        
        
        // if there was no data passed to begin with, exit immediately
        if (!$this->input->post('program_title') || $this->input->post('program_title') == '') {
            exit('success');
        }
        // also delete the record  if there was no record in the database to begin with
        $query_exists = $this->db->get($this->tbl->programs, $where);
        if ($query_exists->num_rows() <= 0) {
            exit('success');
        }

        // delete the record if it does exist
        $this->db->delete($this->tbl->programs, $where);

        if ($this->db->affected_rows() > 0) {
            exit('success');
        }

        exit('Failed to delete the record. Please try again.');
    }

    private function _db_update_course() {
        $where = array(
            'program_title' => $this->data->orig_program_title,
        );
        $data = array(
            'program_title' => $this->data->program_title,
            'program_type' => $this->data->program_type,
            'program_parent' => $this->data->program_parent,
            'program_udate' => date_mysql(),
        );

        $this->db->update($this->tbl->programs, $data, $where);

        if ($this->db->affected_rows() > 0)
            return true;

        return false;
    }

    private function _db_insert_course() {
        $data = array(
            'program_title' => $this->data->program_title,
            'program_type' => $this->data->program_type,
            'program_parent' => $this->data->program_parent,
            'program_cdate' => date_mysql(),
            'program_udate' => date_mysql(),
        );

        $this->db->insert($this->tbl->programs, $data);

        if ($this->db->insert_id() > 0)
            return true;

        return false;
    }

    public function fv_parent_valid($parent) {
        // check the value first
        if ((int) abs($parent) <= 0) {
            $this->form_validation->set_message(__FUNCTION__, 'This course offering should be assigned to a program.');
            return false;
        }

        // check if the parent ID meets our criteria
        $where = array(
            'program_id' => $parent,
            'program_parent' => 0,
            'program_active' => 1,
        );

        $this->db->select('program_id');
        $query = $this->db->get_where($this->tbl->programs, $where);
        if ($query->num_rows() <= 0) {
            $this->form_validation->set_message(__FUNCTION__, 'This program you are assigning this course to does not exist.');
            return false;
        }
    }

    public function fv_title_exists($title) {
        // allow if the old title is the same as the new title
        if ($this->input->post('orig_program_title') == $title)
            return;

        // check if the title doesn't exist in the database yet
        $where = array(
            'program_title' => $title,
        );
        $query = $this->db->get_where($this->tbl->programs, $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message(__FUNCTION__, 'There is already another course offering with that name.');
            return false;
        }
    }

    public function fv_orig_title_exists($title) {
        // if it's blank, it means we're doing an insert, so let it pass
        if ($title == '')
            return;

        // check if the original title doesn't exist in the database yet
        $where = array(
            'program_title' => $title,
        );
        $query = $this->db->get_where($this->tbl->programs, $where);
        if ($query->num_rows() <= 0) {
            $this->form_validation->set_message(__FUNCTION__, 'The course offering you are trying to update does not exist yet.');
            return false;
        }
    }

}