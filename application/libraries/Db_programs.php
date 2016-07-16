<?php

class Db_programs {

    public function __construct() {
        $this->ci = & get_instance();
        $this->db = $this->ci->db;
    }

    public function get($show_courses = true) {
        $where = array(
            'program_active' => 1,
        );

        $this->db->select('program_id, program_title, program_parent, program_type');
        if (!$show_courses) {
            $this->db->where('program_parent', 0);
        }
        $this->db->order_by('program_parent ASC, program_type DESC, program_title ASC, program_udate DESC');
        $query = $this->db->get_where($this->ci->tbl->programs, $where);

        if ($query->num_rows() <= 0)
            return false;

        $data = array();

        $result = $query->result();

        // set the programs
        foreach ($result as $r) {
            if (($parent = $r->program_parent) == 0) {
                $data[] = array('id' => $r->program_id, 'title' => $r->program_title, 'courses' => array());
            } else {
                // set the course under each program
                foreach ($data as $k => $d) {
                    // nest every course to its respective program
                    if ($r->program_parent == $d['id']) {
                        $data[$k]['courses'][] = array('id' => $r->program_id, 'title' => $r->program_title, 'type' => $r->program_type, 'parent' => $r->program_parent);
                    }
                }
            }
        }

        return array_to_object($data);
    }

    public function get_for_forms($hide_empty = true) {
        $programs = $this->get();
        $ret = array();

        foreach ($programs as $data) {
            $ptitle = $data->title;
            $ret[$ptitle] = array();
            foreach ($data->courses as $course_data) {
                $type = $course_data->type;
                $parent = $course_data->parent;
                $title = $course_data->title;
                $title_db = sprintf('%1$s||%2$s||%3$s', $type, $parent, $title);

                $ret[$ptitle][$title_db] = $title;
            }
            if ($hide_empty && empty($ret[$ptitle])) {
                unset($ret[$ptitle]);
            }
        }

        if (!empty($ret)) {
            return $ret;
        }
        return false;
    }

}