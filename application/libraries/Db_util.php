<?php

class Db_util {

    public $db;
    public $ci;

    public function __construct() {
        $this->ci = & get_instance();
        $this->db = $this->ci->db;
    }

    public function insert_meta($tbl, $meta_key, $meta_value, $data) {
        $meta_value = $this->ci->array_json($meta_value);

        $default_data = array(
            'meta_key' => $meta_key,
            'meta_value' => $meta_value,
        );

        $data = array_merge($default_data, $data);

        $this->db->insert($tbl, $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function update_meta($tbl, $meta_key, $meta_value, $where = array()) {
        $meta_value = $this->ci->array_json($meta_value);

        $default_where = array(
            'meta_key' => $meta_key,
        );

        $where = array_merge($default_where, $where);

        $data = array(
            'meta_value' => $meta_value,
        );

        $this->db->update($tbl, $data, $where);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function save_meta($tbl, $meta_key, $meta_value, $where = array()) {
        $meta_value = $this->ci->array_json($meta_value);

        $default_where = array(
            'meta_key' => $meta_key,
        );

        $where = array_merge($default_where, $where);

        $this->db->select('meta_value');

        $query = $this->db->get_where($tbl, $where);

        if ($query->num_rows() > 0) {
            $row = $query->row();

            // return true if no value has been changed
            if ($meta_value == $row->meta_value) {
                return true;
            }
            return $this->update_meta($tbl, $meta_key, $meta_value, $where);
        } else {
            return $this->insert_meta($tbl, $meta_key, $meta_value, $where);
        }

        return false;
    }

    public function delete_meta($tbl, $meta_key, $where = array()) {
        $default_where = array(
            'meta_key' => $meta_key,
        );

        $where = array_merge($default_where, $where);

        $this->db->delete($tbl, $where);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

}