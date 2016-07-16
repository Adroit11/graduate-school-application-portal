<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends Developer_Admins_Controller {

    public function index($offset = 0) {
        $data = array();

        $limit = 5;
        $data['users'] = $this->_get_users($offset, $limit, $calc_rows);
        $data['params'] = $this->input->get();
        $data['total_rows'] = $calc_rows;

        // build the pagination
        $config = array(
            'base_url' => site_url('developer/admins/index'),
            'total_rows' => $calc_rows,
            'per_page' => $limit,
            'num_links' => 4,
            'uri_segment' => 4,
            'full_tag_open' => '<ul class="pagination pagination-lg">',
            'full_tag_close' => '</ul>',
        );

        if (($qs = $_SERVER['QUERY_STRING']) != '') {
            $config['suffix'] = '?' . $qs;
        }

        $this->load->library('pagination', $config);
        $data['pagination'] = $this->pagination->create_links();

        $this->stencil->title('Admins');
        $this->stencil->meta(
                array('description' => 'Manage admins')
        );
        $this->stencil->paint('developer/admins/home_view', $data);
    }

    private function _get_users($offset = 0, $limit = 0, &$calc_rows = 0) {
        $data = array();

        $u = $this->tbl->users;

        if (($name_param = $this->input->get('name')) && $name_param != '') {
            $name_filter = sprintf('AND u.user_fname LIKE "%%%1$s%%" OR u.user_lname LIKE "%%%1$s%%"', $this->db->escape_like_str($name_param));
        } else {
            $name_filter = null;
        }

        $order_filter = $this->_build_order_filter();

        $result_filter = sprintf('LIMIT %1$s, %2$s', (int) abs($offset), (int) abs($limit));

        $sql = <<<EOT
                SELECT
                    SQL_CALC_FOUND_ROWS *,
                    CONCAT (u.user_fname, ' ', u.user_lname) AS name,
                    u.user_id AS id,
                    u.user_email AS email,
                    u.user_status AS status,
                    u.user_cdate AS cdate,
                    u.user_udate AS udate
                FROM
                $u u
                WHERE
                    u.user_role = 2
                $name_filter
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
                            $data[$i][$k] = (int) $data[$i][$k] === 0 ? 'Active' : 'Deactivated';
                            break;
                        case 'udate':
                        case 'cdate':
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
        $status = $this->db_user->get_status($this->input->get('id'));
        if ($status === NULL) {
            exit('false');
        } else {
            exit($status);
        }
    }

    public function update_status() {
        if (!$this->input->post()) {
            $this->output_errors('There was an error saving data to the server.');
        }

        // mandatory fields
        $rules = array(
            array(
                'field' => 'id',
                'label' => 'User ID',
                'rules' => 'trim|required|callback_fv_valid_account',
            ),
            array(
                'field' => 'status',
                'label' => 'User status',
                'rules' => 'trim|required|min_value[0]|max_value[1]',
            ),
        );

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

        // change the user status
        $data = array(
            'user_status' => $msg_vars['status'],
        );
        if (!($this->db_user->update($data, null, $msg_vars['id']))) {
            $this->errors[] = 'Could not update the admin status at this time.';
        }

        if (!empty($this->errors)) {
            $this->output_errors();
        } else {
            exit('true');
        }
    }

    private function _build_order_filter() {
        // sort results
        if (($order_by = $this->input->get('order_by'))) {
            $order_by = strtolower($order_by);
            $valid_cols = array(
                'id' => 'u.user_id',
                'email' => 'u.user_email',
                'name' => 'u.user_lname',
                'status' => 'u.user_status',
                'udate' => 'u.user_udate',
                'cdate' => 'u.user_cdate'
            );
            if (array_key_exists($order_by, $valid_cols)) {
                $order = $this->input->get('order');
                return sprintf('ORDER BY %1$s %2$s', $valid_cols[$order_by], ($order && strtolower($order) === 'asc') ? 'ASC' : 'DESC');
            }
        } else {
            return 'ORDER BY u.user_udate DESC, u.user_cdate DESC, u.user_lname DESC, u.user_status ASC';
        }

        return null;
    }

    public function fv_valid_account($id) {
        // get the pertinent data
        $userdata = $this->db_user->get('user_role', null, $id);

        // check if the user actually exists
        if (!$userdata) {
            $this->form_validation->set_message(__FUNCTION__, 'The user you\'re trying to modify does not exist.');
            return false;
        }

        // check if the role is a user
        if ($userdata->user_role != 2) {
            $this->form_validation->set_message(__FUNCTION__, 'The user you\'re trying to modify is not an admin.');
            return false;
        }

        return true;
    }

}