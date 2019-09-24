<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Meta_Values extends CI_Model {

    const COUNTRIES = 'CAT05';
    const USER_ROLES = 'CAT09';
    const GENDER = 'CAT11';
    const BUSINESS_TYPE = 'CAT13';
    const BUSINESS_SIZE = 'CAT17';
    const LANGUAGE = 'CAT23';
    const LOCATION = 'CAT25'; 
    const CLASS_STATUS = 'CAT15'; 
    const COPY_REASON = 'CAT30'; 
    const RESCH_REASON = 'CAT37'; 
    const REFUND_REASON = 'CAT44'; 
    const CLASS_TRAINEE_FILTER = 'CAT54'; 
    const USER_REACTIVATE_REASONS = 'CAT55'; 
    const COMPANY_REACTIVATE_REASONS = 'CAT56'; 
    const TRAINEE_REACTIVATE_REASONS = 'CAT57'; 
    const COURSE_TYPE = 'CAT22';
    const CLASS_TYPE = 'CAT24';
    const RACE = 'CAT12';
    const SAL_RANGE = 'CAT18';
    const EMAIL_ACT = 'CAT04';
    
    const CLASS_LANGUAGE = 'CAT23_01';
    const CLASSROOM_LOCATION = 'CAT25';
    const FEEDBACK='CAT32';
    
    const DESIGNATION = 'CAT48';
    
    const STATUS = 'CAT02';
    const DEACTIVATE_REASONS = 'CAT08';
    const NOTIFICATION_TYPES = 'CAT28'; 
    const BROADCAST_USER_TYPES = 'CAT28_01'; 
    const COURSE_DEACTIVATE_REASONS = 'CAT31'; 
    const CERTIFICATE_CODE = 'CAT29'; 
    const COPY_REASONS = 'CAT30'; 
    const COMPANY_DEACTIVATE_REASONS = 'CAT35';
    const NRIC = 'CAT16';
    const NRIC_OTHER = 'CAT07';
    const NATIONALITY = 'CAT47';
    const OCCUPATION ='CAT48';
    const HIGHEST_EDUC_LEVEL = 'CAT49';
    const COURSE_STATUS = 'CAT53'; 
    const CLASS_DEACTIVATE_REASONS = 'CAT50';
    const CURRENCIES = 'CAT14'; 
    /**
     * function to get the information about the categories
     * @param type $category_list
     * @param type $category_id
     * @return type
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
     * @param type $category_id
     * @return null
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
     * Get the name of the category
     * @param type $category_id
     * @return null
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
     * @return type
     */
    public function get_param_map() {
		//Added Apr 08 2015
        $this->db = $this->load->database('default', TRUE);
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
        $query = $this->db->select('*')->get('metadata_values');
        $original_result = array();
        $original_result = $query->result_array();
        $result = $original_result;
        foreach($result as $row) {
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
       foreach($all_categories as $category) {
           if ($category['category_id'] == $child_category_id) {
                $childrens[] = $category;
           }
       } 
      
       return $childrens;
    }

}
