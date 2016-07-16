<?php

/* Warning: This class has a dependency on Db_util.php */

class Db_options {

    protected $id;
    protected $ci;
    protected $tbl;
    protected $db;
    protected $module;
    protected $record;
    protected $max_index;
    protected $min_index;
    protected $index;

    public function __construct($params = null) {
        $this->set_var($params);
        $this->ci = & get_instance();
        $this->ci->load->library('db_util');
        $this->db_util = $this->ci->db_util;
        $this->db = $this->ci->db;
        $this->tbl = $this->ci->tbl->options;
    }

    public function meta_key($field = null, $override = false) {
        if ($override)
            return $field;

        $str = '';
        $str .= isset($this->module) ? $this->module : '';
        $str .= isset($this->record) ? '_' . $this->record : '';
        if ($field != '')
            $str .= '_' . $field;
        else
        if (isset($this->index))
            $str .= '_' . $this->index;

        return $str;
    }

    public function set_var($var = null, $val = null) {
        if (is_array($var)) {
            foreach ($var as $k => $v) {
                $this->set_var($k, $v);
            }
        } else {
            if (array_key_exists($var, get_object_vars($this)))
                $this->$var = $val;
        }
    }

    public function get_var($var = null) {
        if ($var) {
            return array_key_exists($var, get_object_vars($this)) ? $this->$var : false;
        }
    }

    public function insert($meta_value, $meta_key = null) {
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        return $this->db_util->insert_meta($this->tbl, $meta_key, $meta_value);
    }

    public function delete($meta_key = null) {
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        $where = array(
            'meta_key' => $meta_key,
        );

        return $this->db_util->delete_meta($this->tbl, $meta_key, $where);
    }

    public function update($meta_value, $meta_key = null) {
        return $this->db_util->update_meta($this->tbl, $meta_key, $meta_value);
    }

    public function save($meta_value, $meta_key = null) {
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        return $this->db_util->save_meta($this->tbl, $meta_key, $meta_value);
    }

    public function get($meta_key = null, $meta_value = null) {
        $this->db->select('meta_value, meta_key');

        // if we entered our own meta key, use that
        if ($meta_key) {
            $this->db->where('meta_key', $meta_key);
        } else {
            // otherwise, use the one generated by the variables set
            $meta_key = $this->meta_key();
            // setting an index means you're looking for a specific record
            if (!$this->index && ($this->min_index && $this->max_index)) {
                $this->db->like('meta_key', $meta_key . '_', 'after');
            } else {
                $this->db->where('meta_key', $meta_key);
            }
        }

        // add a meta value constraint
        if ($meta_value) {
            $this->db->where('meta_value', $meta_value);
        }

        $query = $this->db->get($this->tbl);

        if (($num_rows = $query->num_rows()) > 0) {
            // if the last part of the meta key is a number, we're dealing with indexes
            // indexes should use the last part of the meta key as the array/object index
            // rather than have PHP do the sorting for us.
            // also, NEVER use array_pop if the meta key has an index
            // 
            // set the regex
            $rgx = '/^(.*)(?:_)+(\d)+$/';

            $row = $query->row();
            // if we're not dealing with an indexed meta key, proceed to do a normal operation
            // setting an index means we're only looking for one record
            if ($this->index || !preg_match($rgx, $row->meta_key)) {
                // pop the object if it's only one result
                if ($num_rows === 1) {
                    $mv = $row->meta_value;
                    // json decode when necessary
                    if ($this->ci->is_json($mv)) {
                        return json_decode($mv);
                    }
                    return $mv;
                }
                // if there are multiple results, return only the meta keys
                $d = array(); // return object
                $mv_obj = $query->result();

                foreach ($mv_obj as $k => $row) {
                    $mv = $row->meta_value;
                    // check if the meta value is in json
                    if ($this->ci->is_json($mv)) {
                        $d[$k] = json_decode($mv);
                    } else {
                        $d[$k] = $mv;
                    }
                }

                return $d;
            } else {
                // this assumes that we're now dealing with indexed meta key/s
                $d = array(); // data holder (to be populated and used as return value later)
                foreach ($query->result_array() as $m => $v) {
                    $mk = $v['meta_key'];
                    $mv = $v['meta_value'];

                    // if the value is a JSON string, turn into an array/object first
                    if ($this->ci->is_json($mv)) {
                        $mv = json_decode($mv);
                    }

                    preg_match($rgx, $mk, $m);
                    // use the index as the array index
                    $key = count($m) === 3 ? $m[2] : 0;
                    $d[$key] = $mv;
                }
                return array_to_object($d);
            }
        }
        return null;
    }

}