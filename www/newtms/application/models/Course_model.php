<?php

/*

 * This is the Model class for Course

 */

class Course_Model extends CI_Model {

    private $user;

    public function __construct() {
        parent::__construct();
        $this->user = $this->session->userdata('userDetails');
    }

    /*

     * This method gets the Active  Course list for a tenant

     */

    public function get_active_course_list_all_non_tpg($tenantId, $classTrainee = 0) {

        $this->db->select('c.course_id, c.crse_name, c.crse_manager');
        $this->db->from('course c');
        $this->db->where('c.tenant_id', $tenantId);
///added by shubhranshu due to deactivate class are coming on 0/12/2018//
        //if ($this->user->role_id != 'ADMN') { 
        $this->db->where('c.crse_status', 'ACTIVE');
        $this->db->where('c.tpg_crse', '0');
        //}/////////////////////////////////////////////////////////////////////

        if ($this->user->role_id == 'SLEXEC' && (string) $classTrainee == 'classTrainee') {
            $this->traineelist_querychange();
        }

        if ($this->user->role_id == 'CRSEMGR' && (string) $classTrainee == 'discount') {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        $this->db->order_by("c.crse_name");
        $query = $this->db->get();
        //echo $this->db->last_query();exit;

        $tenant_active_courses = array();
        $logged_in_user_id = $this->user->user_id;

        foreach ($query->result() as $item) {
            $tenant_active_courses[$item->course_id] = $item->crse_name;
        }
        return $tenant_active_courses;
    }
            
    /*

     * This method gets the Active  Course list for a tenant

     */

    public function get_active_course_list_by_tenant($tenantId, $classTrainee = 0) {

        $this->db->select('c.course_id, c.crse_name, c.crse_manager');
        $this->db->from('course c');
        $this->db->where('c.tenant_id', $tenantId);
///added by shubhranshu due to deactivate class are coming on 0/12/2018//
        //if ($this->user->role_id != 'ADMN') { 
        $this->db->where('c.crse_status', 'ACTIVE');
        //$this->db->where('c.tpg_crse', '0');
        //}/////////////////////////////////////////////////////////////////////

        if ($this->user->role_id == 'SLEXEC' && (string) $classTrainee == 'classTrainee') {
            $this->traineelist_querychange();
        }

        if ($this->user->role_id == 'CRSEMGR' && (string) $classTrainee == 'discount') {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        $this->db->order_by("c.crse_name");
        $query = $this->db->get();
        //echo $this->db->last_query();exit;

        $tenant_active_courses = array();
        $logged_in_user_id = $this->user->user_id;

        foreach ($query->result() as $item) {
            $tenant_active_courses[$item->course_id] = $item->crse_name;
        }
        return $tenant_active_courses;
    }
    
    public function get_active_course_list_all_tpg($tenantId, $classTrainee = 0) {

        $this->db->select('c.course_id, c.crse_name, c.crse_manager');
        $this->db->from('course c');
        $this->db->where('c.tenant_id', $tenantId);
///added by shubhranshu due to deactivate class are coming on 0/12/2018//
        //if ($this->user->role_id != 'ADMN') { 
        $this->db->where('c.crse_status', 'ACTIVE');
        $this->db->where('c.tpg_crse', '1');
        //}/////////////////////////////////////////////////////////////////////

        if ($this->user->role_id == 'SLEXEC' && (string) $classTrainee == 'classTrainee') {
            $this->traineelist_querychange();
        }

        if ($this->user->role_id == 'CRSEMGR' && (string) $classTrainee == 'discount') {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        $this->db->order_by("c.crse_name");
        $query = $this->db->get();
        //echo $this->db->last_query();exit;

        $tenant_active_courses = array();
        $logged_in_user_id = $this->user->user_id;

        foreach ($query->result() as $item) {
            $tenant_active_courses[$item->course_id] = $item->crse_name;
        }
        return $tenant_active_courses;
    }

    /**

     * Get active class courses

     * @param type $tenantId

     * @return type

     */
    public function get_active_classcourse_list_by_tenant($tenantId) {

        $cur_date = date('Y-m-d');

        $this->db->select('c.course_id, c.crse_name');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->where('cc.tenant_id', $tenantId);

        $this->db->where('c.crse_status', 'ACTIVE');

        $this->db->where('date(cc.class_start_datetime) >', $cur_date);

        $this->db->group_by('cc.course_id');

        $query = $this->db->get();

        $tenant_active_courses = array();

        foreach ($query->result() as $item) {

            $tenant_active_courses[$item->course_id] = $item->crse_name;
        }

        return $tenant_active_courses;
    }

    /**

     * For getting the total class enrol.

     * @param type $course_id

     * @return type

     * 

     */
    public function get_enrol_count($course_id) {

        $this->db->select('course_id');

        $this->db->from('class_enrol');

        $this->db->where('course_id', $course_id);

        $result = $this->db->get()->num_rows();

        return $result;
    }

    /**

     * This method gets the Course list for a tenant

     * @param type $tenant_id

     * @param type $is_inactive

     * @return type

     */
    public function get_course_list_by_tenant($tenant_id, $is_inactive = 0) {

        if ($this->user->role_id == 'TRAINER') {

            $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, "
                    . "c.language, c.pre_requisite, c.certi_level, c.crse_manager");

            $this->db->from("course c");

            $this->db->where("c.tenant_id", $tenant_id);

            $this->db->where("c.crse_status", 'ACTIVE');

            $this->db->join('course_class ccl', 'ccl.course_id = c.course_id'
                    . ' AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');

            $result = $this->db->get();

            $courses = $result->result();

            foreach ($courses as $item) {

                $tenant_courses[$item->course_id] = $item->crse_name;
            }
        } else {

            $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, "
                    . "c.language, c.pre_requisite, c.certi_level, c.crse_manager");

            $this->db->from("course c");

            $this->db->where("c.tenant_id", $tenant_id);

            if (empty($is_inactive)) {

                $this->db->where("c.crse_status", 'ACTIVE');
            }

            $this->db->order_by("c.crse_name");

            if ($this->user->role_id == 'SLEXEC') {

                $this->traineelist_querychange();
            }

            $result = $this->db->get();

            $courses = $result->result();

            $tenant_courses = array();

            $current_page = $this->uri->segment(2);

            $filter_array = array('certificates', 'attendance', 'soa_report');

            if ($this->user->role_id == 'CRSEMGR' && in_array($current_page, $filter_array)) {

                $logged_in_user_id = $this->user->user_id;

                foreach ($courses as $item) {

                    $crse_manager_arr = explode(',', $item->crse_manager);

                    if (in_array($logged_in_user_id, $crse_manager_arr)) {

                        $tenant_courses[$item->course_id] = $item->crse_name;
                    }
                }
            } else {

                foreach ($courses as $item) {

                    $tenant_courses[$item->course_id] = $item->crse_name;
                }
            }
        }

        return $tenant_courses;
    }

    /**

     * This method gets the internal user list for a tenant by their role

     * @param type $tenant_id

     * @param type $role_id

     * @return type

     */
    public function get_tenant_users_by_role($tenant_id, $role_id) {

        $this->db->select("pers.user_id, pers.first_name, pers.last_name, rl.role_id");

        $this->db->from("tms_users_pers pers");

        $this->db->join("internal_user_role rl", "pers.tenant_id = rl.tenant_id and pers.user_id = rl.user_id");

        $this->db->where("pers.tenant_id", $tenant_id);



        $this->db->join("tms_users tu", "tu.user_id=pers.user_id");

        $this->db->where("tu.account_status", "ACTIVE");



        $this->db->where("rl.role_id", $role_id);

        $this->db->order_by("pers.first_name");

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('pers.user_id', $this->user->user_id);
        }

        $result = $this->db->get();

        $tenant_users = array();

        foreach ($result->result() as $item) {

            $tenant_users[$item->user_id] = $item->first_name;
        }

        return $tenant_users;
    }

    /* To get the non sales executive  Date: 19-07-2016 By Prit */

    public function get_tenant_non_sales_exe($tenant_id, $role_id) {
        $this->db->distinct();
        $this->db->select("ce.sales_executive_id,pers.first_name,pers.last_name,GROUP_CONCAT(distinct iur.role_id) as roles");
        $this->db->from("class_enrol ce");
        $this->db->join("internal_user_role iur", "iur.tenant_id=ce.tenant_id and iur.user_id=ce.sales_executive_id");
        $this->db->join("tms_users_pers pers ", "pers.tenant_id=ce.tenant_id and pers.user_id=ce.sales_executive_id");
        $this->db->where("ce.tenant_id", $tenant_id);
        $this->db->join("tms_users tu", "tu.user_id=ce.sales_executive_id");
        $this->db->where("tu.account_status", "ACTIVE");
        // $this->db->where_not_in("iur.role_id",$role_id);
        $this->db->order_by("pers.first_name");
        $this->db->group_by('ce.sales_executive_id');
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where("pers.user_id", $this->data['user_id']->user_id);
        }
        $result = $this->db->get();
        $tennant_users = array();
        foreach ($result->result() as $item) {
            $arr = explode(',', $item->roles);
            if (in_array("SLEXEC", $arr)) {
                
            } else {
                $tenant_users[$item->sales_executive_id] = $item->first_name;
            }
        }
        return $tenant_users;
    }

    /**

     * For filtering the Multi select box

     * @param type $item_array

     * @return array

     */
    private function course_filter($item_array) {

        $new_array = array();

        foreach ($item_array as $item):

            if ($item != 'multiselect-all') {

                array_push($new_array, $item);
            }

        endforeach;

        return $new_array;
    }

    /**

     * This method creates a new course for the tenant

     * @param type $tenantId

     * @param type $user_id

     */
    public function create_new_course_by_tenant($tenant_id, $user_id) {

        extract($_POST);

        $pre_requisites_value = '';

        $language_value = '';

        $crse_manager_value = '';

        if (count($pre_requisites) > 0) {

            $pre_requisites = $this->course_filter($pre_requisites);

            $pre_requisites_value = implode(",", $pre_requisites);
        }

        if (count($languages) > 0) {

            $languages = $this->course_filter($languages);

            $language_value = implode(",", $languages);
        }

        if (count($course_manager) > 0) {

            $course_manager = $this->course_filter($course_manager);

            $crse_manager_value = implode(",", $course_manager);
        }

        $data = array('tenant_id' => $tenant_id, 'crse_name' => strtoupper($course_name),
            'pre_requisite' => $pre_requisites_value, 'language' => $language_value,
            'crse_type' => $course_types, 'class_type' => $class_types, 'tpg_crse' => $tpg_crse,
            'crse_duration' => $course_duration, 'reference_num' => strtoupper($course_reference_num), 'external_reference_number' => strtoupper($external_reference_number),
            'default_commission_rate' => $default_commission_rate, 'default_commission_rate' => ($default_commission_rate),
            'competency_code' => strtoupper($course_competency_code), 'certi_level' => $certification_code,
            'crse_manager' => $crse_manager_value, 'description' => $course_description, 'crse_admin_email' => $crse_admin_email,
            'crse_cert_validity' => $validity, 'display_on_portal' => ($display_in_landing_page) ? $display_in_landing_page : 0,
            'crse_status' => 'ACTIVE', 'acti_date_time' => date('Y-m-d H:i:s'),
            'created_by' => $user_id, 'created_on' => date('Y-m-d H:i:s'),
            'gst_on_off' => $gst_rules, 'subsidy_after_before' => $subsidy);

        $this->db->trans_start();

        $this->db->insert('course', $data);

        $course_id = $this->db->insert_id();

        if ($course_id) {

            for ($i = 0; $i < count($sales_executives); $i++) {

                $exec_data = array('tenant_id' => $tenant_id, 'course_id' => $course_id,
                    'user_id' => $sales_executives[$i], 'commission_rate' => number_format($sales_exec_commission_rates[$i], 2, '.', ''),
                    'status' => 'ACTIVE', 'acti_date_time' => date('Y-m-d H:i:s'),
                    'assigned_on' => date('Y-m-d H:i:s'), 'assigned_by' => $user_id);

                $this->db->insert('course_sales_exec', $exec_data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {

                return FALSE;
            }

            return $course_id;
        } else {

            return FALSE;
        }
    }

    /**

     * This method lists all the courses offered by a tenant

     * @param type $tenant_id

     * @param type $limit

     * @param type $offset

     * @param type $sort_by

     * @param type $sort_order

     * @param type $course_name

     * @param type $course_code

     * @param type $filter_status

     * @return type

     */
    public function list_all_course_by_tenant($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL, $course_name = '', $course_code = '', $filter_status = '') {

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }

        $this->db->select("c.course_id, c.crse_name, c.crse_manager, c.crse_type, c.class_type, c.certi_level, "
                . "c.language, c.pre_requisite,c.crse_status");

        $this->db->from('course c');

        $this->db->where('c.tenant_id', $tenant_id);

        if ($course_name != "") {

            $this->db->like('c.crse_name', $course_name);
        }

        if ($course_code != "") {

            $this->db->where('c.course_id', $course_code);
        }

        if ($filter_status != "") {

            $this->db->where('c.crse_status', $filter_status);
        }

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by('c.course_id', 'DESC');
        }

        if ($limit == $offset) {

            $this->db->limit($offset);
        } else if ($limit > 0) {

            $limitvalue = $offset - $limit;

            $this->db->limit($limit, $limitvalue);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('c.crse_status !=', 'INACTIV');
        }

        $result = $this->db->get()->result_array();

        return $result;
    }

    /**

     * This function is used to export all the course list for a tenant displayed in the list view

     * @param type $tenant_id

     * @return type

     */
    public function get_course_list_export($tenant_id) {

        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'course_id';

        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $course_name = ($this->input->get('course_name')) ? $this->input->get('course_name') : '';

        $course_code = ($this->input->get('course_code')) ? $this->input->get('course_code') : '';

        $filter_status = ($this->input->get('filter_status')) ? $this->input->get('filter_status') : '';



        $this->db->select("*");

        $this->db->from("course");

        if ($course_name != "") {

            $this->db->like('crse_name', $course_name);
        }

        if ($course_code != "") {

            $this->db->where('course_id', $course_code);
        }

        if ($filter_status != "") {

            $this->db->where('crse_status', $filter_status);
        }

        $this->db->where("tenant_id", $tenant_id);

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by("course_id", "desc");
        }

        return $this->db->get();
    }

    /**

     * This function is used to export all the internal users for a tenant displayed in the list view

     * @param type $tenant_id

     * @return type

     */
    public function get_sales_rate_export($tenant_id) {

        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'course_id';

        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $course_name = ($this->input->get('course_name')) ? $this->input->get('course_name') : '';

        $sales_executives = ($this->input->get('sales_executives')) ? $this->input->get('sales_executives') : '';



        $this->db->select("crse.course_id, crse.crse_name, usr.user_id, usr.first_name, usr.last_name , sales.commission_rate,sales.user_id as sales_user_id");

        $this->db->from("course crse");

        $this->db->join("course_sales_exec sales", "crse.tenant_id = sales.tenant_id"
                . " AND crse.course_id = sales.course_id");

        $this->db->join("tms_users_pers usr", "sales.tenant_id = usr.tenant_id"
                . " AND sales.user_id = usr.user_id");

        $this->db->where('usr.tenant_id', $tenant_id);

        if ($course_name != "" && $sales_executives != "") {

            $this->db->where("crse.course_id", $course_name);

            $this->db->where("usr.user_id", $sales_executives);
        } else if ($course_name != "") {

            $this->db->where("crse.course_id", $course_name);
        } else if ($sales_executives != "") {

            $this->db->where("usr.user_id", $sales_executives);
        }

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by("crse.course_id", "desc");
        }

        $result = $this->db->get();

        return $result->result();
    }

    /**

     * This Method for course name search

     * @param type $tenant_id

     * @return boolean

     */
    public function get_course_list($tenant_id) {

        if ($tenant_id == "")
            return FALSE;

        else {

            $this->db->distinct();

            $this->db->select("c.crse_name, c.crse_manager");

            $this->db->from("course c");

            $this->db->where("c.tenant_id", $tenant_id);

            $this->db->order_by("c.crse_name");



            if ($this->user->role_id == 'COMPACT') {

                $this->db->where('c.crse_status !=', 'INACTIV');
            }

            $query = $this->db->get();

            $result = $query->result();

            return $result;
        }
    }

    /**

     * This Method for getting the managers names 

     * @param type $manager_ids

     * @param type $gender_check

     * @return type

     */
    public function get_managers($manager_ids, $gender_check = 0) {

        $mgr_name = "";

        if ($manager_ids == "") {

            $mgr_name = "";
        } else {

            $manager_ids_array = explode(",", $manager_ids);

            $this->db->select("first_name,last_name, gender");

            $this->db->from("tms_users_pers");

            $this->db->where_in("user_id", $manager_ids_array);

            $result = $this->db->get()->result();

            foreach ($result as $row) {

                if ($gender_check == 1) {

                    $mgr_name .=($row->gender == 'MALE') ? 'Mr. ' : 'Ms. ';
                }

                $mgr_name .=$row->first_name . " " . $row->last_name . ', ';
            }
        }

        $mgr_name = rtrim($mgr_name, ', ');

        return $mgr_name;
    }

    /**

     * This Method for getting the Pre- names 

     * @param type $get_pre_requisite_id

     * @return type

     */
    public function get_pre_requisite($get_pre_requisite_id) {

        $pre_requisites_name = "";

        if ($get_pre_requisite_id == "") {

            $pre_requisites_name = "";
        } else {

            $get_pre_requisite_id_array = explode(",", $get_pre_requisite_id);

            $this->db->distinct();

            $this->db->select("crse_name");

            $this->db->from("course");

            $this->db->where_in('course_id', $get_pre_requisite_id_array);

            $reuslt = $this->db->get()->result();

            foreach ($reuslt as $row) {

                $pre_requisites_name .=$row->crse_name . ', ';
            }
        }

        $pre_requisites_name = rtrim($pre_requisites_name, ', ');

        return $pre_requisites_name;
    }

    /**

     * This Method for getting values from metadata_values 

     * @param type $course_type

     * @return type

     */
    public function get_metadata_on_parameter_id($course_type) {

        $category_names = "";

        $course_type = trim($course_type);

        if (empty($course_type)) {

            $category_names = "";
        } else {

            $metaparams = explode(',', $course_type);

            for ($i = 0; $i < count($metaparams); $i++) {

                $parameter_id = trim($metaparams[$i]);

                $this->db->select("category_name");

                $this->db->from("metadata_values");

                $this->db->where("parameter_id", $parameter_id);

                $res = $this->db->get();

                if ($res->num_rows()) {

                    $category_name = $res->row('category_name');
                } else {

                    $category_name = '';
                }

                $category_names .=$category_name . ", ";
            }
        }

        $category_names = rtrim($category_names, ', ');

        return $category_names;
    }

    /**

     * This MEthod for getting the total count of course for course pagination

     * @param type $tenant_id

     * @param type $course_name

     * @param type $course_code

     * @param type $filter_status

     * @return int

     */
    public function get_all_course_count_by_tenant_id($tenant_id, $course_name, $course_code, $filter_status) {

        if (empty($tenant_id)) {

            return 0;
        }

        $this->db->select('c.crse_manager');

        $this->db->from('course c');

        $this->db->where('c.tenant_id', $tenant_id);



        if ($course_name != "") {

            $this->db->like('c.crse_name', $course_name);
        }

        if ($course_code != "") {

            $this->db->where('c.course_id', $course_code);
        }

        if ($filter_status != "" && $filter_status != 'Select' && $filter_status != 'All') {

            $this->db->where('c.crse_status', $filter_status);
        }



        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('c.crse_status !=', 'INACTIV');
        }

        $result = $this->db->get()->result_array();

        if ($this->user->role_id == 'ADMN' || $this->user->role_id == 'CRSEMGR') {

            return count($result);
        }

        if ($this->user->role_id == 'SLEXEC') {

            return count($result);
        }

        if ($this->user->role_id == 'TRAINER') {

            return count($result);
        }

        if ($this->user->role_id == 'COMPACT') {

            return count($result);
        }
    }

    /**

     * This Method For getting the Course details for View Couses.

     * @param type $id

     * @return boolean

     */
    public function get_course_detailse($id) {

        if ($id != '') {

            $this->db->select('*');

            $this->db->from('course');

            $this->db->where('course_id', $id);

            $result = $this->db->get()->row();

            return $result;
        } else {

            return FALSE;
        }
    }

    /**

     * This method for getting running class in edit course.

     * @param type $id

     * @return boolean

     */
    public function get_running_class($id) {

        date_default_timezone_set("Asia/Kolkata");

        if ($id != '') {

            $this->db->select('*');

            $this->db->from('course_class');

            $this->db->where('course_id', $id);

            $this->db->where('DATE(class_end_datetime) >=', date('Y-m-d'));

            $this->db->where('DATE(class_start_datetime) <=', date('Y-m-d'));

            $result = $this->db->get()->num_rows();

            return $result;
        } else {

            return FALSE;
        }
    }

    /**

     * This method for getting abort to start class in edit course.

     * @param type $id

     * @return boolean

     */
    public function get_yet_to_class($id) {

        date_default_timezone_set("Asia/Kolkata");

        if ($id != '') {

            $this->db->select('*');

            $this->db->from('course_class');

            $this->db->where('course_id', $id);

            $this->db->where('DATE(class_end_datetime) >', date('Y-m-d'));

            $this->db->where('DATE(class_start_datetime) >', date('Y-m-d'));

            $result = $this->db->get()->num_rows();

            return $result;
        } else {

            return FALSE;
        }
    }

    /**

     * This Method For getting the sales executive names(commition) for View Couses.

     * @param type $course_id

     * @return boolean

     */
    public function get_sales_exec_detailse($course_id) {

        if ($course_id != '') {

            $this->db->select('*');

            $this->db->from('course_sales_exec');

            $this->db->where('course_id', $course_id);

            if ($this->user->role_id == 'SLEXEC') {

                $this->db->where('user_id', $this->user->user_id);
            }

            $result = $this->db->get()->result();

            if ($result) {

                $sales_exec_name = '';

                foreach ($result as $row) {

                    $sales_name = $this->get_managers($row->user_id);

                    $sales_name = rtrim($sales_name, ', ');

                    $sales_exec_name .=$sales_name . '(' . number_format($row->commission_rate, 2, '.', '') . '%), ';
                }

                $sales_exec_name = rtrim($sales_exec_name, ', ');
            }

            return $sales_exec_name;
        } else {

            return FALSE;
        }
    }

    /**

     * This Method For getting the sales executive names(commition) for View Couses.

     * @param type $course_id

     * @return boolean

     */
    public function get_sales_exec_detailse_obj($course_id) {

        if ($course_id != '') {

            $this->db->select('*');

            $this->db->from('course_sales_exec');

            $this->db->where('course_id', $course_id);

            $result = $this->db->get()->result();

            if ($result) {

                $sales_exec_name = array();

                for ($i = 0; $i < count($result); $i++) {

                    $sales_name = $this->get_managers($result[$i]->user_id);

                    $sales_name = rtrim($sales_name, ', ');

                    $sales_exec_name[$i][0] = $sales_name;

                    $sales_exec_name[$i][1] = $result[$i]->commission_rate;

                    $sales_exec_name[$i][2] = $result[$i]->user_id;
                }

                return $sales_exec_name;
            }
        } else {

            return FALSE;
        }
    }

    /**

     * This Method for course code auto complete in course list

     * @param type $search_course_code

     * @return type

     */
    public function course_list_autocomplete($search_course_code = NULL) {

        $matches = array();

        if (!empty($search_course_code)) {

            $user = $this->session->userdata('userDetails');

            $tenant_id = $user->tenant_id;

            $this->db->select('c.course_id');

            $this->db->from('course c');

            $this->db->where('c.tenant_id', $tenant_id);

            if ($this->user->role_id == 'COMPACT') {

                $this->db->where('c.crse_status !=', 'INACTIV');
            }

            $this->db->like('c.course_id', $search_course_code, 'both');

            $results = $this->db->get()->result();

            $i = 0;

            foreach ($results as $result) {

                $matches[$i] = $result->course_id;

                $i++;
            }
        }

        return $matches;
    }

    /**

     * This Method for course name auto complete in copy course

     * @param type $search_course_code

     * @param type $type

     * @return string

     */
    public function course_name_list_autocomplete($search_course_code = NULL, $type = NULL) {

        $matches = array();

        if (!empty($search_course_code)) {

            $user = $this->session->userdata('userDetails');

            $tenant_id = $user->tenant_id;

            $this->db->select('crse_name,course_id');

            $this->db->from('course');

            $this->db->where('tenant_id', $tenant_id);

            if ($type == 'course') {

                $this->db->where('crse_status !=', 'INACTIV');
            }

            if ($this->user->role_id == 'CRSEMGR') {

                $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse_manager) !=", 0);
            }

            $this->db->like('crse_name', $search_course_code, 'both');

            $results = $this->db->get()->result();

            $i = 0;

            foreach ($results as $result) {

                $matches[$i] = $result->crse_name . ' (' . $result->course_id . ')';

                $i++;
            }
        }

        return $matches;
    }

    /**

     * This Method for course name auto complete in duplicate course

     * @param type $search_course_code

     * @return type

     */
    public function course_name_autocomplete($search_course_code = NULL) {

        $matches = array();

        if (!empty($search_course_code)) {

            $user = $this->session->userdata('userDetails');

            $tenant_id = $user->tenant_id;

            $this->db->select('crse_name,course_id');

            $this->db->from('course');

            $this->db->where('tenant_id', $tenant_id);

            $this->db->where_not_in('crse_status', 'INACTIV');

            $this->db->like('crse_name', $search_course_code, 'both');

            $results = $this->db->get()->result();

            $i = 0;

            foreach ($results as $result) {

                $matches[$i] = $result->crse_name;

                $i++;
            }
        }

        return $matches;
    }

    /**

     * Course Name auto-fill help for copy

     * @param type $search_course_code

     * @return type

     */
    public function course_name_copy_autocomplete($search_course_code = NULL) {

        $matches = array();

        if (!empty($search_course_code)) {

            $user = $this->session->userdata('userDetails');

            $tenant_id = $user->tenant_id;

            $this->db->select('crse_name,course_id');

            $this->db->from('course');

            $this->db->where('tenant_id', $tenant_id);

            $this->db->like('crse_name', $search_course_code, 'both');

            $results = $this->db->get()->result();

            $i = 0;

            foreach ($results as $result) {

                $matches[$i] = $result->crse_name;

                $i++;
            }
        }

        return $matches;
    }

    /**

     * Get course by Id

     * @param type $course_id

     * @return type

     */
    public function get_course_by_id($course_id) {

        $this->db->select('*');

        $this->db->from('course');

        $this->db->where('course_id', $course_id);

        $query = $this->db->get();

        $results = $query->result();

        return count($results) > 0 ? $results[0] : null;
    }

    /**

     * This function for duplicating the course

     * @return boolean

     */
    public function duplicate_course() {

        extract($_POST);

        $couse_name = strtoupper($couse_name);

        if ($reason_copy_course == 'OTHERS') {

            $reason_copy_course = $reason_copy_course . ',' . strtoupper($other_reason_copy_course);
        }

        $course_details = $this->get_course_by_id($course_id);

        if ($course_details) {

            $user = $this->session->userdata('userDetails');

            $tenant_id = $user->tenant_id;

            $course_data = array('tenant_id' => $course_details->tenant_id, 'crse_name' => $couse_name
                , 'crse_type' => $course_details->crse_type, 'class_type' => $course_details->class_type
                , 'crse_duration' => $course_details->crse_duration, 'competency_code' => $course_details->competency_code
                , 'reference_num' => $course_details->reference_num, 'certi_level' => $course_details->certi_level
                , 'description' => $course_details->description, 'crse_cert_validity' => $course_details->crse_cert_validity
                , 'display_on_portal' => $course_details->display_on_portal, 'crse_content_path' => $course_details->crse_content_path
                , 'crse_icon' => $course_details->crse_icon, 'pre_requisite' => $course_details->pre_requisite
                , 'language' => $course_details->language, 'crse_manager' => $course_details->crse_manager
                , 'crse_status' => 'ACTIVE', 'acti_date_time' => $course_details->acti_date_time
                , 'deacti_date_time' => NULL, 'deacti_reason' => NULL
                , 'deacti_reason_oth' => NULL, 'deacti_by' => NULL
                , 'created_by' => $course_details->created_by, 'created_on' => $course_details->created_on
                , 'last_modified_by' => $course_details->last_modified_by, 'last_modified_on' => $course_details->last_modified_on
                , 'subsidy_after_before' => $course_details->subsidy_after_before, 'gst_on_off' => $course_details->gst_on_off
                , 'copy_reason' => $reason_copy_course, 'copied_from_id' => $course_id
                , 'copied_on' => date('Y-m-d H:i:s'), 'copied_by' => $user->user_id
                , 'gst_on_off' => $course_details->gst_on_off, 'subsidy_after_before' => $course_details->subsidy_after_before);

            $this->db->trans_start();

            $couser_result = $this->db->insert('course', $course_data);

            $new_course_id = $this->db->insert_id();

            if ($couser_result) {

                $this->db->select('*');

                $this->db->from('course_sales_exec');

                $this->db->where('course_id', $course_id);

                $sales_exec_result = $this->db->get()->result();

                if ($sales_exec_result) {

                    foreach ($sales_exec_result as $result) {

                        $course_sales_exec_data = array('tenant_id' => $result->tenant_id, 'course_id' => $new_course_id
                            , 'user_id' => $result->user_id, 'commission_rate' => $result->commission_rate
                            , 'status' => $result->status, 'acti_date_time' => $result->acti_date_time
                            , 'deacti_date_time' => $result->deacti_date_time, 'deacti_reason' => $result->deacti_reason
                            , 'deacti_reason_oth' => $result->deacti_reason_oth, 'deacti_by' => $result->deacti_by
                            , 'assigned_on' => $result->assigned_on, 'assigned_by' => $result->assigned_by
                            , 'last_modified_by' => $result->last_modified_by, 'last_modified_on' => $result->last_modified_on);

                        $sales_exec_insert = $this->db->insert('course_sales_exec', $course_sales_exec_data);
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {

                        return FALSE;
                    } else if ($sales_exec_insert) {

                        return TRUE;
                    } else {

                        return FALSE;
                    }
                } else {

                    return FALSE;
                }
            } else {

                return FALSE;
            }
        } else {

            return FALSE;
        }
    }

    /* This function get the enrire row from course table skm start */

    public function get_course_details($course_id, $tenant_id) {
        $this->db->select('*');
        $this->db->from('course');
        $this->db->where('course_id', $course_id);
        $this->db->where('tenant_id', $tenant_id);
        $sql = $this->db->get();
        $data = $sql->row_array();
        $data['sales_exe_id'] = $this->get_course_sales_executive($course_id, $tenant_id);
        return $data;
    }

    public function get_course_sales_executive($course_id, $tenant_id) {
        $this->db->select('user_id,commission_rate');
        $this->db->from('course_sales_exec');
        $this->db->where('course_id', $course_id);
        $this->db->where('tenant_id', $tenant_id);
        $sql = $this->db->get();
        if ($sql->num_rows() > 0) {
            foreach ($sql->result_array() as $row) {
                $data[] = array('user_id' => $row['user_id'] . '_' . $row['commission_rate']);
            }
            return $data;
        } else {
            return 0;
        }
    }

    /* end */

    /**

     * This function will check unique couse name.

     * @param type $tenant_id

     * @return type

     */
    public function check_course_name($tenant_id) {

        extract($_POST);

        $course_name = trim($course_name);

        $course_name = strtoupper($course_name);

        $this->db->select('course_id');

        $this->db->from('course');

        $this->db->where('crse_name', $course_name);

        $this->db->where('tenant_id', $tenant_id);

        if ($course_id != '') {

            $this->db->where('course_id !=', $course_id);
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**

     * This method edit a  course for the tenant

     * @param type $tenant_id

     * @param type $user_id

     * @return boolean

     */
    public function edit_course_by_tenant($tenant_id, $user_id) {

        extract($_POST);

        if ($course_id) {

            $pre_requisites_value = '';

            $language_value = '';

            $crse_manager_value = '';

            if (count($pre_requisites) > 0) {

                $pre_requisites = $this->course_filter($pre_requisites);

                $pre_requisites_value = implode(",", $pre_requisites);
            }

            if (count($languages) > 0) {

                $languages = $this->course_filter($languages);

                $language_value = implode(",", $languages);
            }

            if (count($course_manager) > 0) {

                $course_manager = $this->course_filter($course_manager);

                $crse_manager_value = implode(",", $course_manager);
            }

            if ($validity_period == 'No') {

                $validity = 0;
            }

            $new_array = array();

            if ($enrol_count == 0) {

                $new_array = array('pre_requisite' => $pre_requisites_value, 'crse_type' => $course_types,
                    'crse_duration' => $course_duration,
                    'certi_level' => $certification_code);
            }

            $data = array('tenant_id' => $tenant_id, 'crse_name' => strtoupper($course_name),
                'language' => $language_value, 'reference_num' => strtoupper($course_reference_num), 'external_reference_number' => strtoupper($external_reference_number),
                'competency_code' => strtoupper($course_competency_code), 'crse_manager' => $crse_manager_value,
                'default_commission_rate' => $default_commission_rate, 'default_commission_rate' => $default_commission_rate,
                'description' => $course_description, 'display_on_portal' => $display_in_landing_page, 'crse_admin_email' => $crse_admin_email,
                'last_modified_by' => $user_id, 'last_modified_on' => date('Y-m-d H:i:s'),
                'gst_on_off' => $gst_rules, 'subsidy_after_before' => $subsidy,
                'crse_cert_validity' => $validity, 'class_type' => $class_types) + $new_array;



            $this->db->where('tenant_id', $tenant_id);

            $this->db->where('course_id', $course_id);

            $this->db->trans_start();

            $update_result = $this->db->update('course', $data);

            if ($update_result) {

                $this->db->where('tenant_id', $tenant_id);

                $this->db->where('course_id', $course_id);

                $delete_result = $this->db->delete('course_sales_exec');

                if ($delete_result) {

                    for ($i = 0; $i < count($sales_executives); $i++) {

                        $exec_data = array('tenant_id' => $tenant_id, 'course_id' => $course_id,
                            'user_id' => $sales_executives[$i], 'commission_rate' => number_format($sales_exec_commission_rates[$i], 2, '.', ''),
                            'status' => 'ACTIVE', 'acti_date_time' => date('Y-m-d H:i:s'),
                            'assigned_on' => date('Y-m-d H:i:s'), 'assigned_by' => $user_id);

                        $this->db->insert('course_sales_exec', $exec_data);
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {

                        return FALSE;
                    }

                    return $course_id;
                } else {

                    return FALSE;
                }
            } else {

                return FALSE;
            }
        } else {

            return FALSE;
        }
    }

    /**

     * Sales exec for a course

     * @param type $course_id

     * @return type

     */
    public function get_sales_exec_for_course($course_id) {

        $this->db->select("up.user_id, up.first_name as name, cse.commission_rate", FALSE);

        $this->db->from("course_sales_exec cse");

        $this->db->join("tms_users_pers up", "cse.user_id=up.user_id");

        $this->db->where("cse.course_id", $course_id);

        return $this->db->get();
    }

    /**

     * Get classes linked with a course

     * @param type $courseId

     * @return type

     */
    public function get_classes_for_course($courseId) {

        $this->db->select("*");

        $this->db->from("course_class");

        $this->db->where("course_id", $courseId);

        return $this->db->get();
    }

    /**

     * function to get certificate reports

     * @param type $courseId

     * @return type

     */
    public function get_completed_classes_active_enrollment_for_courses($courseId) {

        $cur_date = date('Y-m-d', strtotime(date("Y-m-d")));

        $this->db->select("cc.*");

        $this->db->from('class_enrol ce');

        $this->db->join('course_class cc', 'cc.class_id=ce.class_id');

        $this->db->where('date(cc.class_end_datetime) <', $cur_date);

        $this->db->where('ce.course_id', $courseId);

        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $this->db->group_by('cc.class_id');

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->like('cc.sales_executive', $this->user->user_id, 'both');
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",classroom_trainer) !=", 0);
        }

        $this->db->order_by("DATE(cc.class_start_datetime)", "DESC");

        return $this->db->get();
    }

    /**

     * function to get certificate reports

     * @param type $courseId

     * @return type

     */
    public function get_classes_active_enrollment_certificate_courses($courseId) {

        $cur_date = date('Y-m-d', strtotime(date("Y-m-d")));

        $this->db->select("cc.*");

        $this->db->from('class_enrol ce');

        $this->db->join('course_class cc', 'cc.class_id=ce.class_id');

        $this->db->where('date(cc.class_end_datetime) <=', $cur_date);

        $this->db->where('ce.course_id', $courseId);

        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $this->db->group_by('cc.class_id');

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->like('cc.sales_executive', $this->user->user_id, 'both');
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",classroom_trainer) !=", 0);
        }

        $this->db->order_by("DATE(cc.class_start_datetime)", "DESC");

        return $this->db->get();
    }

    /**

     * Get active enrolments

     * @param type $courseId

     * @return type

     */
    public function get_classes_active_enrollment_courses($courseId) {

        $this->db->select("cc.*");

        $this->db->from('class_enrol ce');

        $this->db->join('course_class cc', 'cc.class_id=ce.class_id');

        $this->db->where('ce.course_id', $courseId);

        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $this->db->group_by('cc.class_id');

        $this->db->order_by("DATE(cc.class_start_datetime)", "DESC");

        return $this->db->get();
    }

    /**

     * This function deactivates the course selectd.

     * @param type $course_id

     * @return boolean

     */
    public function deactivate_course($course_id) {

        $this->load->helper('common');

        foreach ($this->input->post() as $key => $value) {

            $$key = $value;
        }

        if ($reason_for_deactivation != 'OTHERS') {

            $other_reason_for_deactivation = '';
        }

        $user = $this->session->userdata('userDetails');



        $data = array(
            'crse_status' => 'INACTIV',
            'deacti_date_time' => date('Y-m-d H:i:s'),
            'deacti_reason' => $reason_for_deactivation,
            'deacti_reason_oth' => $other_reason_for_deactivation,
            'deacti_by' => $user->user_id,
        );

        $this->db->where('tenant_id', $user->tenant_id);

        $this->db->where('course_id', $course_id);

        $this->db->trans_start();

        $this->db->update('course', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return FALSE;
        }



        return TRUE;
    }

    /**

     * This function for getting sales commission rate list count.

     * @param type $tenant_id

     * @return type

     */
    public function get_sales_commission_list_count($tenant_id) {

        $this->db->select("crse.course_id, crse.crse_name, usr.user_id, usr.first_name, usr.last_name , sales.commission_rate");

        $this->db->from("course crse");

        $this->db->join("course_sales_exec sales", "crse.tenant_id = sales.tenant_id"
                . " AND crse.course_id = sales.course_id");

        $this->db->join("tms_users_pers usr", "sales.tenant_id = usr.tenant_id"
                . " AND sales.user_id = usr.user_id");

        $this->db->where('usr.tenant_id', $tenant_id);

        if ($this->input->server('REQUEST_METHOD')) {

            extract($_GET);

            if ($course_name != "" && $sales_executives != "") {

                $this->db->where("crse.course_id", $course_name);

                $this->db->where("usr.user_id", $sales_executives);
            } else if ($course_name != "") {

                $this->db->where("crse.course_id", $course_name);
            } else if ($sales_executives != "") {

                $this->db->where("usr.user_id", $sales_executives);
            }
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**

     * This function for getting sales commission rate list

     * @param type $tenant_id

     * @param type $limit

     * @param type $offset

     * @param type $sort_by

     * @param type $sort_order

     * @return type

     */
    public function get_sales_commission_list($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }

        $this->db->select("crse.course_id, crse.crse_name, crse.crse_status, usr.user_id, usr.first_name, usr.last_name , sales.commission_rate,sales.user_id as sales_user_id");

        $this->db->from("course crse");

        $this->db->join("course_sales_exec sales", "crse.tenant_id = sales.tenant_id"
                . " AND crse.course_id = sales.course_id");

        $this->db->join("tms_users_pers usr", "sales.tenant_id = usr.tenant_id"
                . " AND sales.user_id = usr.user_id");

        $this->db->where('usr.tenant_id', $tenant_id);

        if ($this->input->server('REQUEST_METHOD')) {

            extract($_GET);

            if ($course_name != "" && $sales_executives != "") {

                $this->db->where("crse.course_id", $course_name);

                $this->db->where("usr.user_id", $sales_executives);
            } else if ($course_name != "") {

                $this->db->where("crse.course_id", $course_name);
            } else if ($sales_executives != "") {

                $this->db->where("usr.user_id", $sales_executives);
            }
        }

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by('course_id', 'DESC');
        }

        if ($limit == $offset) {

            $this->db->limit($offset);
        } else if ($limit > 0) {

            $limitvalue = $offset - $limit;

            $this->db->limit($limit, $limitvalue);
        }

        $query = $this->db->get();



        return $query->result();
    }

    /**

     * This function for updating the sales executive commition.

     * @param type $tenant_id

     * @return boolean

     */
    public function update_sales_exec_rate($tenant_id) {

        extract($_POST);

        if ($new_sales_commition_rate != "" && $course_id != "" && $sales_exec_id != "") {

            $data = array('commission_rate' => number_format($new_sales_commition_rate, 2, '.', ''));

            $this->db->where('course_id', $course_id);

            $this->db->where('user_id', $sales_exec_id);

            $this->db->where('tenant_id', $tenant_id);

            $this->db->trans_start();

            $result = $this->db->update('course_sales_exec', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {

                return FALSE;
            }

            return TRUE;
        } else {

            return FALSE;
        }
    }

    /**

     * This Function for getting the sales exec based on course_id

     * @param type $tenant_id

     * @return type

     */
    public function get_sales_exec_detailse_array($tenant_id) {

        extract($_POST);

        if ($course_id) {

            $query = $this->get_sales_exec_for_course($course_id);

            $result = $query->result();

            if ($result) {

                $new_sales_exec = array();

                foreach ($result as $row) {

                    $new_sales_exec[] = array('key' => $row->user_id, 'value' => $row->name);
                }

                return $new_sales_exec;
            }
        } else {

            $user = $this->session->userdata('userDetails');

            $result = $this->get_tenant_users_by_role($user->tenant_id, 'SLEXEC');

            if ($result) {

                $new_sales_exec = array();

                foreach ($result as $key => $value) {

                    $new_sales_exec[] = array('key' => $key, 'value' => $value);
                }

                return $new_sales_exec;
            }
        }
    }

    /**

     * save the path of the course file

     * @param type $course_id

     * @param type $file_path

     * @param type $field

     * @return boolean

     */
    public function save_course_file_path($course_id = NULL, $file_path = NULL, $field = NULL) {

        if (empty($course_id)) {

            return FALSE;
        }

        $data = array(
            "$field" => $file_path
        );



        $this->db->where('course_id', $course_id);

        $this->db->update('course', $data);
    }

    /**

     * function to fetch the icon path

     * @param type $course_id

     * @param type $field

     * @return type

     */
    public function fetch_image_path_by_course_id($course_id = NULL, $field = NULL) {

        if (empty($course_id)) {

            return;
        }

        return $this->db->select($field)->from("course")->
                        where_in("course_id", $course_id)->get()->row()->$field;
    }

    /**

     * function to remove the icon path

     * @param type $path

     * @return type

     */
    public function remove_previous_icon($path = NULL) {

        if (empty($path)) {

            return;
        }

        $previous_original_path = str_ireplace("_thumb", "", $path);

        unlink($path);

        unlink($previous_original_path);

        return;
    }

    /**

     * Check enrolled details

     * @return type

     */
    public function check_enrolled() {

        $sales_id = $this->input->post('sales_id');

        $course_id = $this->input->post('course_id');

        $this->db->select('course_id');

        $this->db->from('class_enrol');

        $this->db->where('sales_executive_id', $sales_id);

        $this->db->where('course_id', $course_id);

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**

     * get class langauge

     * @param type $course_id

     * @return type

     */
    public function get_class_language($course_id) {

        $this->db->distinct();

        $this->db->select("class_language");

        $this->db->from("course_class");

        $this->db->where("course_id", $course_id);

        $result = $this->db->get();

        return $result->result_array();
    }

    /**

     * function to get class courses

     * @param type $tenant_id

     * @return type

     */
    public function get_class_courses($tenant_id) {

        if ($this->user->role_id == 'TRAINER') {

            $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, "
                    . "c.language, c.pre_requisite, c.certi_level, c.crse_manager");

            $this->db->from("course c");

            $this->db->where("c.tenant_id", $tenant_id);

            $this->db->where("c.crse_status", 'ACTIVE');

            $this->db->join('course_class ccl', 'ccl.course_id = c.course_id'
                    . ' AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');

            $result = $this->db->get();

            $courses = $result->result();

            foreach ($courses as $item) {

                $tenant_courses[$item->course_id] = $item->crse_name;
            }
        } else {

            $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, c.language, c.pre_requisite, c.certi_level, c.crse_manager");

            $this->db->from('course_class cl');

            $this->db->join("course c", "c.course_id = cl.course_id");

            $this->db->where("cl.tenant_id", $tenant_id);

            $cur_page = $this->uri->segment(2);

            $filter_arr = array('attendance', 'mark_attendance');

            if (!in_array($cur_page, $filter_arr)) {

                $this->db->where('DATE(cl.class_start_datetime) <=', date('Y-m-d'));
            }

            $this->db->where('cl.class_status !=', 'INACTIV');

            $this->db->where('c.crse_status', 'ACTIVE');

            $this->db->order_by("c.crse_name");

            $this->db->group_by("c.course_id");

            if ($this->user->role_id == 'SLEXEC') {

                $this->traineelist_querychange();
            }

            $result = $this->db->get();

            $courses = $result->result();

            $tenant_courses = array();

            $current_page = $this->uri->segment(2);

            $filter_array = array('certificates', 'attendance', 'mark_attendance');

            if ($this->user->role_id == 'CRSEMGR' && in_array($current_page, $filter_array)) {

                $logged_in_user_id = $this->user->user_id;

                foreach ($courses as $item) {

                    $crse_manager_arr = explode(',', $item->crse_manager);

                    if (in_array($logged_in_user_id, $crse_manager_arr)) {

                        $tenant_courses[$item->course_id] = $item->crse_name;
                    }
                }
            } else {

                foreach ($courses as $item) {

                    $tenant_courses[$item->course_id] = $item->crse_name;
                }
            }
        }

        return $tenant_courses;
    }

    /**

     * function to get class courses

     * @param type $tenant_id

     * @return type

     */
    public function get_class_courses_all($tenant_id) {

        if ($this->user->role_id == 'TRAINER') {

            $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, "
                    . "c.language, c.pre_requisite, c.certi_level, c.crse_manager");

            $this->db->from("course c");

            $this->db->where("c.tenant_id", $tenant_id);

            $this->db->where("c.crse_status", 'ACTIVE');

            $this->db->join('course_class ccl', 'ccl.course_id = c.course_id'
                    . ' AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');

            $result = $this->db->get();

            $courses = $result->result();

            foreach ($courses as $item) {

                $tenant_courses[$item->course_id] = $item->crse_name;
            }
        } else {

            $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, c.language, c.pre_requisite, c.certi_level, c.crse_manager");

            $this->db->from('course_class cl');

            $this->db->join("course c", "c.course_id = cl.course_id");

            $this->db->where("cl.tenant_id", $tenant_id);

            $cur_page = $this->uri->segment(2);

            $filter_arr = array('attendance', 'mark_attendance');

            if (!in_array($cur_page, $filter_arr)) {

                $this->db->where('DATE(cl.class_start_datetime) <=', date('Y-m-d'));
            }

            $this->db->where('cl.class_status !=', 'INACTIV');

            $this->db->order_by("c.crse_name");

            $this->db->group_by("c.course_id");

            if ($this->user->role_id == 'SLEXEC') {

                $this->traineelist_querychange();
            }

            $result = $this->db->get();

            $courses = $result->result();

            $tenant_courses = array();

            $current_page = $this->uri->segment(2);

            $filter_array = array('certificates', 'attendance', 'mark_attendance');

            if ($this->user->role_id == 'CRSEMGR' && in_array($current_page, $filter_array)) {

                $logged_in_user_id = $this->user->user_id;

                foreach ($courses as $item) {

                    $crse_manager_arr = explode(',', $item->crse_manager);

                    if (in_array($logged_in_user_id, $crse_manager_arr)) {

                        $tenant_courses[$item->course_id] = $item->crse_name;
                    }
                }
            } else {

                foreach ($courses as $item) {

                    $tenant_courses[$item->course_id] = $item->crse_name;
                }
            }
        }

        return $tenant_courses;
    }

    /**

     * role based access for salesexec

     */
    private function traineelist_querychange() {

        $this->db->join('course_sales_exec cse', 'cse.tenant_id = c.tenant_id AND cse.course_id = c.course_id');

        $this->db->where('cse.user_id', $this->user->user_id);
    }

    /**

     * for getting copeied from course name in edit and view mode of course.

     * @param type $course_id

     * @return type

     */
    public function course_name($course_id) {

        $this->db->select("crse_name");

        $this->db->from("course");

        $this->db->where("course_id", $course_id);

        return $this->db->get()->row("crse_name");
    }

    /**
     * DMS - This method returns the list of templates available for the course
     * @param type $tenant_id
     * @param type $course_id
     * @param type $filter_option
     * @param type $records_per_page
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @param type $opType
     * @return type
     */
    public function get_template_list($tenant_id, $course_id, $filter_option, $records_per_page, $offset, $sort_by, $sort_order, $opType) {

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }
        $this->db->select("temp.*, pers.first_name");
        $this->db->from("dms_crse_assmnt_template temp");
        $this->db->join('tms_users_pers pers', "temp.template_last_modified_by = pers.user_id");
        $this->db->join('course c', 'temp.course_id = c.course_id');
        $this->db->where("temp.course_id", $course_id);
        $this->db->where("temp.tenant_id", $tenant_id);

        if (strlen(trim($filter_option)) != 0) {
            if (strtoupper($filter_option) == 'INACTIVE') {
                //Set selection criteria for Inactive templates
                $this->db->where("temp.template_status", "INACTIVE");
            } else if (strtoupper($filter_option) == 'ACTIVE') {
                //Set selection criteria for active templates
                $this->db->where("temp.template_status", "ACTIVE");
            }
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by("temp.template_id");
        }



        if ($opType == 'rowcount') {

            $query = $this->db->get();
            //echo $this->db->last_query();die();
            $result = $query->result_array();
            return count($result);
        } else {

            if ($offset != null && $records_per_page != null) {
                if ($records_per_page == $offset) {
                    $this->db->limit($offset);
                } else if ($records_per_page > 0) {
                    $limitvalue = $offset - $records_per_page;
                    $this->db->limit($records_per_page, $limitvalue);
                }
            }
            $query = $this->db->get();
            //echo '$sort_by: '.$sort_by.' $sort_order: '.$sort_order.'      \n';
            //echo $this->db->last_query();die();
            return $query->result_array();
        }
    }

    /**
     * DMS - gets the list of available active course for the tenant
     * @param type $tenant_id
     * @return boolean
     */
    public function get_course_listbytenant($tenant_id) {
        if ($tenant_id == "") {
            return FALSE;
        } else {
            $this->db->select("*");
            $this->db->from("course c");
            $this->db->where("c.tenant_id", $tenant_id);
            $this->db->order_by("c.crse_name");
            $query = $this->db->get();
            $courselist = $query->result();
            return $courselist;
        }
    }

    /**
     *  This method inserts the assessment template into the database
     */
    public function insert_assmnt_template($tenant_id, $user_id) {

        extract($_POST);

        //insert data into table dms_crse_assmnt_template and save the uploaded file to the file system
        // Mark it as version 1 ans set it to Active
        $this->db->trans_start();

        $sel_up_template_id = get_max_lookup(ASSMNT_TEMAPLTE_ID);
        $template_data = array('template_id' => $sel_up_template_id,
            'course_id' => $assmnt_course_name,
            'template_title' => $template_name,
            'tenant_id' => $tenant_id,
            'template_status' => 'ACTIVE',
            'template_last_modified_by' => $user_id,
            'templates_version_id' => 1,
            'template_last_modified_on' => date('Y-m-d H:i:s'));

        $course_result = $this->db->insert('dms_crse_assmnt_template', $template_data);

        $this->db->trans_complete();

        // Copy the file over - Hardcoing the path as that seems to be the preferred approach
        $file_name = "uploads/files/assessment_templates/template_" . $sel_up_template_id . "_1.pdf";
        move_uploaded_file($_FILES['assmnt_upload']['tmp_name'], $file_name);

        return true;
    }

    /**
     * This method is used to change the current PDF file to inactive and insert the new PDF version
     *  It will also set the PDF as active 
     */
    public function change_assmnt_template($tenant_id, $user_id) {

        extract($_POST);

        // Check if a file upload was also done, if not only update existing record, no version change
        if (strlen(trim($_FILES['up_assmnt_upload']['name'])) != 0) {

            $this->db->trans_start();

            // Set the current one inactive
            $template_data = array('template_status' => 'INACTIVE',
                'template_deactivated_on' => date('Y-m-d H:i:s'),
                'template_deactivated_by' => $user_id);
            $this->db->where('template_id', $sel_up_template_id);
            $update_result = $this->db->update('dms_crse_assmnt_template', $template_data);

            // Get the last version id
            $this->db->select_max("templates_version_id");
            $this->db->from("dms_crse_assmnt_template");
            $this->db->where("template_id", $sel_up_template_id);
            $result_set = $this->db->get();

            $result = $result_set->result();
            $last_version = $result[0]->templates_version_id;

            $last_version += 1;

            // Get the other data elements
            $this->db->select("tenant_id,course_id");
            $this->db->from("dms_crse_assmnt_template");
            $this->db->where("template_id", $sel_up_template_id);
            $result_set = $this->db->get();
            $result = $result_set->result();

            //insert data into table dms_crse_assmnt_template and save the uploaded file to the file system    
            $template_data = array('template_id' => $sel_up_template_id,
                'course_id' => $result[0]->course_id,
                'template_title' => $up_template_name,
                'tenant_id' => $result[0]->tenant_id,
                'template_status' => 'ACTIVE',
                'template_last_modified_by' => $user_id,
                'templates_version_id' => $last_version,
                'template_last_modified_on' => date('Y-m-d H:i:s'));

            $couser_result = $this->db->insert('dms_crse_assmnt_template', $template_data);

            $this->db->trans_complete();

            // Copy the file over - Hardcoing the path as that seems to be the preferred approach
            $file_name = "uploads/files/assessment_templates/template_" . $sel_up_template_id . "_" . $last_version . ".pdf";
            move_uploaded_file($_FILES['up_assmnt_upload']['tmp_name'], $file_name);
        } else {

            // Update the existing record
            $template_data = array('template_last_modified_by' => $user_id,
                'template_last_modified_on' => date('Y-m-d H:i:s'),
                'template_title' => $up_template_name);

            $this->db->where('template_id', $sel_up_template_id);
            $update_result = $this->db->update('dms_crse_assmnt_template', $template_data);
        }

        return true;
    }

    /**
     * This method is used change the PDF staus to inactive
     */
    public function remove_assmnt_template($user_id) {

        extract($_POST);

        $template_data = array('template_status' => 'INACTIVE',
            'template_deactivated_by' => $user_id,
            'template_deactivated_on' => date('Y-m-d H:i:s'));

        $this->db->where('template_id', $sel_template_id);
        $update_result = $this->db->update('dms_crse_assmnt_template', $template_data);

        return true;
    }

    public function get_course_name($tenant_id, $course_id) {
        return $this->db->select('crse_name')->from('course')->where('tenant_id', $tenant_id)->where('course_id', $course_id)->get()->row()->crse_name;
    }

    public function get_course_by_reference_no($tenant_id, $reference_no) {
        return $this->db->select('crse_name')->from('course')->where('tenant_id', $tenant_id)->where('reference_num', $reference_no)->get()->row()->crse_name;
    }

}
