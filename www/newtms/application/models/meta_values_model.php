<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Meta_Values_Model extends CI_Model {

    const COUNTRIES = 'CAT05';
    const USER_ROLES = 'CAT09';
    const GENDER = 'CAT11';
    const BUSINESS_TYPE = 'CAT13';
    const BUSINESS_SIZE = 'CAT17';
    const COURSE_TYPE = 'CAT22';
    const CLASS_TYPE = 'CAT24';
    const RACE = 'CAT12';
    const SAL_RANGE = 'CAT18';
    const EMAIL_ACT = 'CAT04';
    const DESIGNATION = 'CAT48';
    const DESIGNATIONS = 'CAT20';
    const STATUS = 'CAT02';
    const DEACTIVATE_REASONS = 'CAT08';
    const CLASS_LANGUAGE = 'CAT23';
    const CLASSROOM_LOCATION = 'CAT25';
    const CLASSROOM_STATUS = 'CAT15'; ### ADDED BY SANKAR
    const FEEDBACK = 'CAT32';
    const NRIC = 'CAT16';
    const NRIC_OTHER = 'CAT07';
    const NATIONALITY = 'CAT47';
    const OCCUPATION = 'CAT48';
    const HIGHEST_EDUC_LEVEL = 'CAT49';

    /**
     * function to get the information about the categories
     * @param array() $category_list
     * @param varchar $category_id
     *
     * @return array meta_data information of all the categories
     */
    public function get_metavalues($category_list = array(), $category_id = NULL) {
        $category_name = '';
        $meta_data = array();
        if (empty($category_list) && empty($category_id)) {
            return array();
        } else if (empty($category_list) && $category_id) {
            $category_list = array($category_id);
            $category_name = 'result';
        }
        $this->db->select('category_id, child_category_id, parameter_id,
                        category_name, description');
        $this->db->from('metadata_values');
        $this->db->where_in('category_id', $category_list);
        if ($category_id != self::USER_ROLES) {
            $this->db->order_by('category_name');
        }
        $query = $this->db->get();
        if (!$category_name) {
            $category_name = $this->get_main_category_name($category_id);
        }
        foreach ($query->result() as $row) {
            $meta_data[$row->parameter_id] = array('parameter_id' => $row->parameter_id,
                'child_category_id' => $row->child_category_id,
                'category_id' => $row->category_id,
                'category_name' => $row->category_name,
                'description' => $row->description);
        }

        return $meta_data;
    }

    /**
     * fetch the child category from the parent category
     * @param varchar $category_id
     *
     * @return varchar child_category_id
     */
    public function fetch_child_category_by_category_id($category_id = NULL) {
        if (empty($category_id)) {
            return NULL;
        }
        $this->db->select('child_category_id, category_name');
        $this->db->from('metadata_values');
        $this->db->where('category_id', $category_id);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();
        $child_category_id = NULL;
        if ($result) {
            $child_category_id = $result[0]->child_category_id;
        }
        return $child_category_id;
    }

    /**
     * get the name of the category
     * @param int category_id
     * 
     * return string category_name
     */
    public function get_main_category_name($category_id = NULL) {
        if (empty($category_id)) {
            return NULL;
        }
        $this->db = $this->load->database('default', TRUE);
        $this->db->select('category_name');
        $this->db->from('metadata_values');
        $this->db->where('category_id', $category_id);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();
        $category_name = NULL;
        if ($result) {
            $category_name = $result[0]->category_name;
        }
        return $category_name;
    }

    /**
     * get all meta values as map
     */
    public function get_param_map() {
        $query = $this->db->select('category_name, parameter_id')->get('metadata_values');
        $result = array();
        foreach ($query->result() as $row) {
            if (!empty($row->parameter_id)) {
                $result[$row->parameter_id] = $row->category_name;
            }
        }
        return $result;
    }

    /*
     * fetch all the metadata
     * 
     */

    public function fetch_metadata_categories() {
        $this->db->distinct();
        //$this->db->where('child_category_id !=', '');
        $query = $this->db->select('*')->get('metadata_values');
        $original_result = array();
        $original_result = $query->result_array();
        $result = $original_result;
        foreach ($result as $row) {
            $childrens = $this->search_for_children($original_result, $row['category_id'], $row['child_category_id']);
            if ($childrens) {
                $data[$row['category_id']] = $childrens;
            }
        }
        return $data;
    }

    /**
     * function to return the children categories
     * @param type $all_categories
     * @param type $category_id
     * @param type $child_category_id
     * @return array
     */
    public function search_for_children($all_categories, $category_id, $child_category_id) {
        $childrens = array();
        foreach ($all_categories as $category) {
            if ($category['category_id'] == $child_category_id) {
                $childrens[] = $category;
            }
        }
        array_multisort($childrens, SORT_ASC);
        return $childrens;
    }

}

/*End of the file meta_values_model.php*/