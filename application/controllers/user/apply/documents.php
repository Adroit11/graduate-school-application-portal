<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Documents extends User_Apply_Controller {

    private $fields = array('birth', 'tor', 'employer');

    public function __construct() {
        parent::__construct();
        $this->db_user->set_var('record', 'documents');
    }

    public function index() {
        $data = array();
        $data['fields'] = $this->fields;
        $data['records'] = $this->_db_get_file_data();
        $data['alerts'] = array();
        foreach ($this->fields as $field) {
            // check if there's flash data
            if (($fdata = $this->session->flashdata($field))) {
                $data['alerts'][$field] = $fdata;
            }
        }

        $this->stencil->title('Electronic Documents');
        $this->stencil->meta(
                array('description' => 'Upload required documents')
        );
        $this->stencil->paint('user/apply/documents_view', $data);
    }

    public function submit() {
        // build the uploads directory
        $path = DIR_APPLICANT_UPLOADS;
//        show_error(octal_permissions(fileperms($path)));
//        exit();
        // check if dir exists
        if (!realpath($path)) {
            if (!mkdir($path, 0777, true)) {
                show_error('Error accessing upload directory');
            }
        } else {
            // check if permissions are public
            if (octal_permissions(fileperms($path)) != 777) {
                if (!chmod($path, 0777)) {
                    show_error('Error setting permissions for upload directory');
                }
            }
        }

        // set the constraints
        $config = array(
            'upload_path' => $path,
            'allowed_types' => 'doc|docx|odf|pdf',
            'max_size' => '2048',
            'encrypt_name' => true,
        );

        $this->load->library('upload', $config);

        foreach ($_FILES as $field => $data) {
            d($_FILES);
            // check if that field contains a file
            if ($data['name']) {
                // upload the file
                if ($this->upload->do_upload($field)) {
                    $upload_data = $this->upload->data();
                    if ($this->_db_save_file_data($field, $upload_data))
                        $this->session->set_flashdata($field, array('file' => $data['name'], 'data' => sprintf('The file <em>%1$s</em> has been successfully uploaded.', $data['name'])));
                    else {
                        // delete the file if it didn't save to the database
                        unset($upload_data['full_path']);
                        $this->session->set_flashdata($field, array('file' => $data['name'], 'data' => array('Failed to save the file to the database')));
                    }
                } else {
                    $this->session->set_flashdata($field, array('file' => $data['name'], 'data' => $this->upload->errors_array()));
                }
            }
            $this->upload->reset_errors_array();
        }

        redirect(sprintf('user/apply/documents'));
    }

    private function _db_get_file_data() {
        $ret = array();

        foreach ($this->fields as $field) {
            $meta_key = $this->db_user->meta_key($field);

            $data = $this->db_user->get_meta($meta_key);

            if (!empty($data)) {
                $ret[$field] = $data;
            }
        }

        return $ret;
    }

    private function _db_save_file_data($prefix, $meta_value) {
        // check if there's already a file with the same name as the upload
        $meta_key = $this->db_user->meta_key($prefix);

        $data = $this->db_user->get_meta($meta_key);
        if (!empty($data)) {
            if ($data->file_name === $meta_value['file_name']) {
                return false;
            }
        }

        $where = array(
            'user_id' => $this->id,
        );

        if ($this->db_util->save_meta($this->tbl->users_meta, $meta_key, $meta_value, $where)) {
            return true;
        }

        return false;
    }

    public function download($field = null) {
        if ($field == '' || !in_array($field, $this->fields)) {
            show_404();
        }

        $this->load->helper('download');

        $meta_key = $this->db_user->meta_key($field);

        // check if file has a record
        if (($data = $this->db_user->get_meta($meta_key))) {
            $full_path = realpath(DIR_APPLICANT_UPLOADS . '/' . $data->file_name);

            // if file doesn't exist, delete entry from database
            if (!$full_path) {
                if ($this->db_user->delete_meta($meta_key)) {
                    $this->session->set_flashdata($field, array('file' => $field, 'data' => array('File does not exist.')));
                    redirect('user/apply/documents');
                }
            } else {
                // download file
                force_download($data->orig_name, file_get_contents($full_path));
            }
        } else {
            $this->session->set_flashdata($field, array('file' => $field, 'data' => array('File does not exist.')));
            redirect('user/apply/documents');
        }
    }

    public function delete($field = '') {
        if ($field == '' || !in_array($field, $this->fields)) {
            show_404();
        }

        $meta_key = $this->db_user->meta_key($field);

        // check first if a DB record exists
        if (!($data = $this->db_user->get_meta($meta_key))) {
            $this->session->set_flashdata($field, array('data' => array('There is nothing to delete.')));
        } else {
            // delete the file
            if ($this->db_user->delete_meta($meta_key)) {
                $full_path = realpath(DIR_APPLICANT_UPLOADS . '/' . $data->file_name);
                unlink($full_path);
                $this->session->set_flashdata($field, array('data' => sprintf('The file <em>%1$s</em> has been successfully deleted.', $data->orig_name)));
            } else {
                $this->session->set_flashdata($field, array('data' => array('Failed to delete the file. Plesae try again.')));
            }
        }

        redirect(sprintf('user/apply/documents'));
    }

}