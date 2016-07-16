<?php

/* Warning: This class has a dependency on Db_util.php */

class Db_user {

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
        $this->tbl = $this->ci->tbl;
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

    public function exists($where = array(), $id = null) {
        $default_where = array(
            'user_id' => $id != '' ? $id : $this->id,
        );
        $where = array_merge($default_where, $where);

        $query = $this->db->get_where($this->tbl->users, $where);

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get($select = null, $where = array(), $id = null) {
        // do this if the ID is set (assuming this class is being used when the user is logged in)
        if ($id) {
            $where['user_id'] = $id;
        } else {
            if ($this->id) {
                $where['user_id'] = $this->id;
            }
        }

        $select = $select ? $select : '*';
        $this->db->select($select);
        $query = $this->db->get_where($this->tbl->users, $where);

        if (($row_count = $query->num_rows()) > 0) {
            if ($row_count === 1) {
                // if it's just a single record
                return $query->row();
            }
            else
            // if there are multiple results
                return $query->result();
        }

        return null;
    }

    public function get_col($col = null, $where = array(), $id = null) {
        // get user id if no column has been specified
        $col = $col ? $col : 'user_id';

        // check if the column/field name exists
        if (!$this->db->field_exists($col, $this->tbl->users))
            return null;

        if ($id) {
            $where['user_id'] = $id;
        } else {
            if ($this->id) {
                $where['user_id'] = $this->id;
            }
        }

        $this->db->select($col);
        $query = $this->db->get_where($this->tbl->users, $where);

        if ($query->num_rows() > 0) {
            return array_pop($query->row_array());
        }

        return null;
    }

    // begin convenience functions

    public function get_email($id = null) {
        return $this->get_col('user_email', null, $id);
    }

    public function get_active($id = null) {
        return $this->get_col('user_active', null, $id) == 1 ? true : false;
    }
    
    public function get_status($id = null) {
        return $this->get_col('user_status', null, $id);
    }

    public function get_role($id = null) {
        switch ($this->get_col('user_role', null, $id)) {
            case 0:
                return 'developer';
                break;
            case 2:
                return 'admin';
                break;
            case 1:
            default:
                return 'user';
        }
    }

    // end convenience functions

    public function insert($data = array()) {
        $first_elem = array_shift(array_values($data));

        if (is_array($first_elem)) {
            $this->db->insert_batch($this->tbl->users, $data);
        } else {
            $this->db->insert($this->tbl->users, $data);
        }

        if (($id = $this->db->insert_id()) > 0) {
            return $id;
        }

        return false;
    }

    public function update($data = array(), $where = array(), $id = null) {
        $default_where = array(
            'user_id' => $id ? $id : $this->id,
        );

        if (!is_array($where)) {
            $where = $default_where;
        } else {
            $where = array_merge($where, $default_where);
        }

        // using array_values will prevent any array element from being removed
        // good for "peeking" into the first element of an array without modifying the original
        $first_elem = array_shift(array_values($data));

        if (is_array($first_elem)) {
            $this->db->update_batch($this->tbl->users, $data, $where);
        } else {
            $this->db->update($this->tbl->users, $data, $where);
        }

        if (($this->db->affected_rows() > 0)) {
            return true;
        }

        return false;
    }

    public function save($data = array(), $where = array(), $id = null) {
        // check if an ID is set, or if one is already saved as field
        if ($id) {
            $id = $id;
        } else {
            if ($this->id) {
                $id = $this->id;
            }
        }

        // if there's no ID set, insert
        if (!$id) {
            if (($insert_id = $this->insert($data))) {
                return $insert_id;
            }
        } else {
            // try to see if a record exists before tying to update
            if ($this->get('user_id', null, $id)) {
                if ($this->update($data, $where, $id)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function delete($id = null, $where = array()) {
        $default_where = array(
            'user_id' => $id ? $id : $this->id,
        );

        $where = array_merge($default_where, $where);

        $this->db->delete($this->tbl->users, $where);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function meta_key_exists($meta_key = null, $user_id = null) {
        $user_id = $user_id ? $user_id : $this->id;

        $where = array(
            'meta_key' => $meta_key ? $meta_key : $this->meta_key(),
            'user_id' => $user_id ? $user_id : $this->id,
        );

        $query = $this->db->get_where($this->tbl->users_meta, $where);

        if ($query->num_rows() > 0) {
            return true;
        }

        return false;
    }

    public function insert_meta($meta_value, $meta_key = null, $user_id = null) {
        $data = array(
            'user_id' => $user_id ? $user_id : $this->id,
        );

        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        return $this->db_util->insert_meta($this->tbl->users_meta, $meta_key, $meta_value, $data);
    }

    public function insert_meta_batch($assoc = array(), $meta_key = null, $user_id = null) {
        // assoc must be a collection of meta keys/meta values
        if (!is_array($assoc)) {
            return false;
        }

        $batch = array();

        $user_id = $user_id ? $user_id : $this->id;
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        foreach ($assoc as $data) {
            $batch[] = array(
                'user_id' => $user_id,
                'meta_key' => $meta_key . '_' . $data['meta_key'],
                'meta_value' => $data['meta_value'],
            );
        }

        $this->udate_update(null, $user_id);

        $this->db->insert_batch($this->tbl->users_meta, $batch);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function update_meta_batch($assoc = array(), $meta_key = null, $user_id = null) {
        // the default update_batch method is flawed, so we'll have to loop through each key-value pair
        // assoc must be a collection of meta keys/meta values
        if (!is_array($assoc)) {
            return false;
        }

        $user_id = $user_id ? $user_id : $this->id;
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        foreach ($assoc as $item) {
            $data = array(
                'meta_value' => $item['meta_value']
            );

            $where = array(
                'user_id' => $user_id,
                'meta_key' => $meta_key . '_' . $item['meta_key'],
            );

            // check if there's an existing record, so we can skip those
            $this->db->select('user_id');
            $query = $this->db->get_where($this->tbl->users_meta, array_merge($data, $where));

            if ($query->num_rows() > 0) {
                continue;
            } else {
                $this->db->update($this->tbl->users_meta, $data, $where);
            }
        }

        $this->udate_update(null, $user_id);

        return true;
    }

    public function save_meta_batch($assoc = array(), $meta_key = null, $user_id = null) {
        // assoc must be a collection of meta keys/meta values
        if (!is_array($assoc)) {
            return false;
        }

        $batch_to_insert = array();
        $batch_to_update = array();

        $user_id = $user_id ? $user_id : $this->id;
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        foreach ($assoc as $k => $v) {
            $this->db->select('user_id');
            $where = array(
                'user_id' => $user_id,
                'meta_key' => $meta_key . '_' . $k,
            );
            $query = $this->db->get_where($this->tbl->users_meta, $where);
            if ($query->num_rows() <= 0) {
                // add to the batch insert array
                $batch_to_insert[] = array(
                    'user_id' => $user_id,
                    'meta_key' => $k,
                    'meta_value' => $v,
                );
            } else {
                // add to the batch update array
                $batch_to_update[] = array(
                    'meta_key' => $k,
                    'meta_value' => $v,
                );
            }
        }

        $op_count = 0;
        $op_tally = 0;

        if (!empty($batch_to_insert)) {
            $op_count++;
            if ($this->insert_meta_batch($batch_to_insert, $meta_key, $user_id)) {
                $op_tally++;
            }
        }

        if (!empty($batch_to_update)) {
            $op_count++;
            if ($this->update_meta_batch($batch_to_update, $meta_key, $user_id)) {
                $op_tally++;
            }
        }

        if ($op_tally === $op_count) {
            return true;
        }

        return false;
    }

    public function delete_meta($meta_key = null, $user_id = false) {
        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        $where = array(
            'meta_key' => $meta_key,
            'user_id' => $user_id ? $user_id : $this->id,
        );

        return $this->db_util->delete_meta($this->tbl->users_meta, $meta_key, $where);
    }

    public function update_meta($meta_value, $meta_key = null, $user_id = null) {
        $where = array(
            'user_id' => $user_id ? $user_id : $this->id,
        );

        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        // update the user
        $this->udate_update(null, $where['user_id']);

        return $this->db_util->update_meta($this->tbl->users_meta, $meta_key, $meta_value, $where);
    }

    public function save_meta($meta_value, $meta_key = null, $user_id = null) {
        $where = array(
            'user_id' => $user_id ? $user_id : $this->id,
        );

        $meta_key = $meta_key ? $meta_key : $this->meta_key();

        // update the user
        $this->udate_update(null, $where['user_id']);

        return $this->db_util->save_meta($this->tbl->users_meta, $meta_key, $meta_value, $where);
    }

    public function get_meta($meta_key = null, $user_id = null, $meta_value = null, $multiple = false, $use_meta_key_as_index = false) {
        $this->db->select('meta_value, meta_key');

        // if we entered our own meta key, use that
        if ($meta_key) {
            $this->db->where('meta_key', $meta_key);
        } else {
            // otherwise, use the one generated by the variables set
            $meta_key = $this->meta_key();
            // setting an index means you're looking for a specific record
            if ($multiple || (!$this->index && ($this->min_index && $this->max_index))) {
                $this->db->like('meta_key', $meta_key . '_', 'after');
            } else {
                $this->db->where('meta_key', $meta_key);
            }
        }

        // add a user id constraint
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        } else {
            if ($this->id) {
                $this->db->where('user_id', $this->id);
            }
        }

        // add a meta value constraint
        if ($meta_value) {
            $this->db->where('meta_value', $meta_value);
        }

        $query = $this->db->get($this->tbl->users_meta);

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
            if (!$this->index && !preg_match($rgx, $row->meta_key)) {
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
                    $mk = $row->meta_key;
                    if ($use_meta_key_as_index) {
                        $d[$mk] = $mv;
                    } else {
                        // check if the meta value is in json
                        if ($this->ci->is_json($mv)) {
                            $d[$k] = json_decode($mv);
                        } else {
                            $d[$k] = $mv;
                        }
                    }
                }
                if ($use_meta_key_as_index) {
                    return array_to_object($d);
                } else {
                    return $d;
                }
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

    public function udate_update($date = null, $id = null) {
        $date = $date ? $date : date_mysql();
        $id = $id ? $id : $this->id;

        $data = array(
            'user_udate' => $date,
        );

        $where = array(
            'user_id' => $id,
        );

        $this->db->update($this->tbl->users, $data, $where);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

}