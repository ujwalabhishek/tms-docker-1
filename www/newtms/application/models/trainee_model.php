<?php

/*
 * This is the Model class for Trainee
 */

class Trainee_Model extends CI_Model {

    private $user;
    
    public function __construct() {
        parent::__construct();
        $this->load->library('bcrypt');
        $this->load->helper('common');
        $this->user = $this->session->userdata('userDetails');
        
    }

    /**
     * This method give the trainee count according to the search criteria.
     * @return type numeric
     */
    public function record_count() {
        $tenant_id = $this->user->tenant_id;
        $extra_query = '';
        $extra_where = '';
        $extra_group_by = '';
        $company_id = 0;
        if ($this->user->company_id != '') {
            $company_id = $this->user->company_id;
        }
        $company_post = $this->input->get('off_company_name');
        if ($company_post) {
            $company_id = $company_post;
        }
        if ($company_id) {
            $extra_query .= " inner join tenant_company_users com on usr.tenant_id = com.tenant_id AND usr.user_id = com.user_id
                        inner join company_master com_mst on com.company_id = com_mst.company_id";
            $extra_where .= " and com.company_id='$company_id'";
            $extra_where .= " and com_mst.company_status='ACTIVE'";
        }
        if ($this->user->role_id == 'SLEXEC') {
            $extra_query .=" inner join class_enrol ce on ce.tenant_id = pers.tenant_id and ce.user_id = pers.user_id";
            $extra_where .= " and ce.sales_executive_id='" . $this->user->user_id . "'";
            $extra_group_by .= " group by ce.user_id";
        } elseif ($this->user->role_id == 'TRAINER') {
            $extra_query .=" inner join course_class ccl on ccl.tenant_id = pers.tenant_id and FIND_IN_SET(" . $this->user->user_id . ",ccl.classroom_trainer)
                       inner join class_enrol ce on ce.tenant_id = pers.tenant_id and ce.user_id = pers.user_id and
                       ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id";
            $extra_group_by .= " group by ce.user_id";
        }
        $search_radio = $this->input->get('search_radio');
        if ($search_radio == "pers_radio" || $search_radio == "tax_radio") {
             $user_id = $this->input->get('user_id');
            if(!empty($user_id)){
                $extra_where .=" and usr.user_id='$user_id'";
            }
        }
            $search_status = $this->input->get('status');
            if ($search_status) {
            $extra_where .= " and usr.account_status='$search_status'";
        }
        $query = "select count(*) as count
                from tms_users usr
                inner join tms_users_pers pers on pers.user_id = usr.user_id and pers.tenant_id = usr.tenant_id
                $extra_query
                where usr.tenant_id='$tenant_id' and usr.account_type='TRAINE' 
                $extra_where
                $extra_group_by";

        $res = $this->db->query($query);
        return $res->row('count');
    }
    /**
     * Get record count
     * @return type
     */
    public function record_count_bk() {
        $company_id = '';
        if ($this->user->company_id != '') {
            $company_id = $this->user->company_id;
        }
        if ($this->input->get('off_company_name')) {
            $company_id = $this->input->get('off_company_name');
        }

        $search_radio = $this->input->get('search_radio');
        $this->db->select('usr.tenant_id');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('tenant_company_users com', 'usr.tenant_id = com.tenant_id '
                . 'AND usr.user_id = com.user_id', "left");
        $this->db->join('company_master com_mst', 'com.company_id = com_mst.company_id', "left");
        $this->db->where('usr.tenant_id', $this->user->tenant_id);
        $this->db->where('usr.account_type', 'TRAINE');
        $search_pers_first_name_arr = explode('(', $this->input->get('pers_first_name'));
        $search_taxcode_arr = explode('(', $this->input->get('tax_code'));
        $search_status = $this->input->get('status');

        if ($search_radio == "pers_radio") {
            $pers_first_name_extracted = $search_pers_first_name_arr[0];
            $tax_code_extracted = trim($search_pers_first_name_arr[1], ')');
            if ($tax_code_extracted != '') {//tax code
                $this->db->where('usr.tax_code', $tax_code_extracted);
            } else if ($pers_first_name_extracted) {//trainee first name
                 $this->db->where('pers.first_name', $pers_first_name_extracted);
            }
        }

        if ($search_radio == "tax_radio") {
            $tax_code_extracted = $search_taxcode_arr[0];
             if ($tax_code_extracted) {
                $this->db->where('usr.tax_code', $tax_code_extracted);
            }
        }

        if ($search_status != '') {
            $this->db->where('usr.account_status', $this->input->get('status'));
        }

        if ($company_id) {
            $this->db->where('com.company_id', $company_id);
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->traineelist_querychange();
        }
        if ($this->user->role_id == 'TRAINER') {
            $this->trainer_traineelist_querychange();
        }
         $result = $this->db->get()->num_rows();
        return $result;
    }

    /**
     * Function to get list of trainees 
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @return type array     
     */
    public function get_trainee_list($tenant_id, $limit, $offset, $sort_by, $sort_order) 
    {
        $extra_select = '';
        $extra_query = '';
        $extra_where = '';
        $extra_order_by = '';
        $extra_group_by = '';
        $extra_limit = '';
        $company_id = 0;
        if ($this->user->company_id != '') 
        {
            $company_id = $this->user->company_id;
        }
        $company_post = $this->input->get('off_company_name');
        if ($company_post) 
        {
            $company_id = $company_post;
        }
        if ($company_id) 
        {
            $extra_select .=",com.company_id";
            $extra_query .= " inner join tenant_company_users com on usr.tenant_id = com.tenant_id AND usr.user_id = com.user_id
                        inner join company_master com_mst on com.company_id = com_mst.company_id";
            $extra_where .= " and com.company_id='$company_id'";
            $extra_where .= " and com_mst.company_status='ACTIVE'";
        }
        if ($this->user->role_id == 'SLEXEC') 
        {
            $extra_query .=" inner join class_enrol ce on ce.tenant_id = pers.tenant_id and ce.user_id = pers.user_id";
            $extra_where .= " and ce.sales_executive_id='" . $this->user->user_id . "'";
            $extra_group_by .= " group by ce.user_id";
        } 
        elseif ($this->user->role_id == 'TRAINER') 
        {
            $extra_query .=" inner join course_class ccl on ccl.tenant_id = pers.tenant_id and FIND_IN_SET(" . $this->user->user_id . ",ccl.classroom_trainer)
                       inner join class_enrol ce on ce.tenant_id = pers.tenant_id and ce.user_id = pers.user_id and
                       ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id";
            $extra_group_by .= " group by ce.user_id";
        }
        $search_radio = $this->input->get('search_radio');
        if ($search_radio == "pers_radio" || $search_radio == "tax_radio") {
             $user_id = $this->input->get('user_id');
            if(!empty($user_id)){
             $extra_where .=" and usr.user_id='$user_id'";
            }
        }
        $search_status = $this->input->get('status');
        if ($search_status) {
            $extra_where .= " and usr.account_status='$search_status'";
        }
        if ($sort_by) {
            $extra_order_by .= " order by $sort_by $sort_order";
        } else {
            $extra_order_by .= " order by usr.last_modified_on DESC";
        }
        if ($limit == $offset) {
            $extra_limit .=" limit $limit";
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $extra_limit .=" limit $limitvalue,$limit";
        }
        $query = "select usr.user_id, usr.tax_code,usr.country_of_residence, usr.registration_date,
                pers.first_name traineename, pers.last_name, pers.dob, usr.account_type, usr.account_status
                $extra_select
                from tms_users usr
                inner join tms_users_pers pers on pers.user_id = usr.user_id and pers.tenant_id = usr.tenant_id
                $extra_query
                where usr.tenant_id='$tenant_id' and usr.account_type='TRAINE' 
                $extra_where
                $extra_group_by
                $extra_order_by
                $extra_limit";

        $res = $this->db->query($query);
        return $res->result_array();
    }

    public function get_trainee_list_bk($tenant_id, $limit, $offset, $sort_by, $sort_order) {
        $company_id = '';
        if ($this->user->company_id != '') {
            $company_id = $this->user->company_id;
        }
        if ($this->input->get('off_company_name')) {
            $company_id = $this->input->get('off_company_name');
        }
        $search_radio = $this->input->get('search_radio');
        if ($offset <= 0 OR empty($tenant_id)) {
            return;
        }
        if ($this->input->get('off_company_name') == '') {
            $user_status = 'usr.account_status';
        } else {
            $user_status = 'com.user_acct_status';
        }

        $this->db->select('usr.user_id, usr.tax_code,usr.country_of_residence, usr.registration_date, '
                . 'pers.first_name traineename, pers.last_name, pers.dob, usr.account_type, ' . $user_status . ' as account_status,'
                . 'com.company_id,com_mst.company_name');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('tenant_company_users com', 'usr.tenant_id = com.tenant_id '
                . 'AND usr.user_id = com.user_id', "left");
        $this->db->join('company_master com_mst', 'com.company_id = com_mst.company_id', "left");
        $this->db->where('usr.tenant_id', $tenant_id);
        $this->db->where('usr.account_type', 'TRAINE');
        $search_pers_first_name_arr = explode('(', $this->input->get('pers_first_name'));
        $search_taxcode_arr = explode('(', $this->input->get('tax_code'));
        $search_status = $this->input->get('status');

        if ($search_radio == "pers_radio") {
            $pers_first_name_extracted = $search_pers_first_name_arr[0];
            $tax_code_extracted = trim($search_pers_first_name_arr[1], ')');

            if ($tax_code_extracted != '') {
               $this->db->where('usr.tax_code', $tax_code_extracted);
            } else if ($pers_first_name_extracted) {
                $this->db->where('pers.first_name', $pers_first_name_extracted);
            }
        }

        if ($search_radio == "tax_radio") {
            $tax_code_extracted = $search_taxcode_arr[0];
            if ($tax_code_extracted) {
                $this->db->where('usr.tax_code', $tax_code_extracted);
            }
        }

        if ($search_status != '') {
            $this->db->where('usr.account_status', $this->input->get('status'));
        }

        if ($company_id) {
            $this->db->where('com.company_id', $company_id);
        }
       if ($this->user->role_id == 'SLEXEC') {
            $this->traineelist_querychange();
        }
         if ($this->user->role_id == 'TRAINER') {
            $this->trainer_traineelist_querychange();
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('usr.last_modified_on', 'DESC');
        }
        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * This method for exporting page fields based on search criteria.
     * @return string
      */
    public function get_trainee_list_export() 
    {
        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'usr.last_modified_on';
        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tenant_id = $this->user->tenant_id;
        $extra_select = '';
        $extra_query = '';
        $extra_where = '';
        $extra_order_by = '';
        $extra_group_by = '';
        $extra_limit = '';
        $company_id = 0;
        if ($this->user->company_id != '') 
        {
            $company_id = $this->user->company_id;
        }
        $company_post = $this->input->get('off_company_name');
        if ($company_post) {
            $company_id = $company_post;
        }
        if ($company_id) 
        {
            $extra_select .=",com.company_id,com_mst.company_name";
            $extra_query .= " inner join tenant_company_users com on usr.tenant_id = com.tenant_id AND usr.user_id = com.user_id
                        inner join company_master com_mst on com.company_id = com_mst.company_id";
            $extra_where .= " and com.company_id='$company_id'";
            $extra_where .= " and com_mst.company_status='ACTIVE'";
        }
        if ($this->user->role_id == 'SLEXEC') {
            $extra_query .=" inner join class_enrol ce on ce.tenant_id = pers.tenant_id and ce.user_id = pers.user_id";
            $extra_where .= " and ce.sales_executive_id='" . $this->user->user_id . "'";
            $extra_group_by .= " group by ce.user_id";
        } elseif ($this->user->role_id == 'TRAINER') {
            $extra_query .=" inner join course_class ccl on ccl.tenant_id = pers.tenant_id and FIND_IN_SET(" . $this->user->user_id . ",ccl.classroom_trainer)
                       inner join class_enrol ce on ce.tenant_id = pers.tenant_id and ce.user_id = pers.user_id and
                       ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id";
            $extra_group_by .= " group by ce.user_id";
        }
        $search_radio = $this->input->get('search_radio');
        if ($search_radio == "pers_radio") {
            $search_pers_first_name_arr = explode('(', $this->input->get('pers_first_name'));
            $pers_first_name_extracted = $search_pers_first_name_arr[0];
            $tax_code_extracted = trim($search_pers_first_name_arr[1], ')');

            if ($tax_code_extracted != '') {
                $extra_where .= " and usr.tax_code='$tax_code_extracted'";
            } else if ($pers_first_name_extracted) {
                $extra_where .= " and pers.first_name='$pers_first_name_extracted'";
            }
        } else if ($search_radio == "tax_radio") {
            $search_taxcode_arr = explode('(', $this->input->get('tax_code'));
            $tax_code_extracted = $search_taxcode_arr[0];
            if ($tax_code_extracted) {
                $extra_where .= " and usr.tax_code='$tax_code_extracted'";
            }
        }
        $search_status = $this->input->get('status');
        if ($search_status) {
            $extra_where .= " and usr.account_status='$search_status'";
        }
        if ($sort_by) {
            $extra_order_by .= " order by $sort_by $sort_order";
        } else {
            $extra_order_by .= " order by usr.last_modified_on DESC";
        }

        $query = "select usr.user_id, usr.user_name, usr.tax_code_type, usr.tax_code,usr.country_of_residence, 
                usr.registration_date, usr.account_type, usr.account_status, usr.registered_email_id, 
                pers.first_name, pers.last_name, pers.dob, pers.gender,pers.race, pers.salary_range,
                pers.occupation_code, pers.certificate_pick_pref, usr.acc_activation_type,
                pers.personal_address_bldg, pers.personal_address_country, pers.personal_address_state, 
                pers.personal_address_city, pers.personal_address_zip, pers.nationality,pers.highest_educ_level
                from tms_users usr
                inner join tms_users_pers pers on pers.user_id = usr.user_id and pers.tenant_id = usr.tenant_id
                $extra_query
                where usr.tenant_id='$tenant_id' and usr.account_type='TRAINE' 
                $extra_where
                $extra_group_by
                $extra_order_by
                $extra_limit";

        return $this->db->query($query);
    }
    /**
     * Get Trainee list
     * @return type
     */
    public function get_trainee_list_export_bk() {
        $company_id = '';
        if ($this->user->company_id != '') {
            $company_id = $this->user->company_id;
        }
        if ($this->input->get('off_company_name')) {
            $company_id = $this->input->get('off_company_name');
        }

        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'usr.last_modified_on';
        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $search_radio = $this->input->get('search_radio');
         if ($this->input->get('off_company_name') == '') {
            $user_status = 'usr.account_status';
        } else {
            $user_status = 'com.user_acct_status';
        }
        $this->db->select('usr.user_id, usr.user_name, usr.tax_code_type, usr.tax_code,usr.country_of_residence, usr.registration_date, '
                . 'pers.first_name , pers.last_name, pers.dob, pers.gender,pers.race,pers.salary_range,pers.occupation_code, pers.certificate_pick_pref, usr.acc_activation_type,'
                . ' usr.account_type, ' . $user_status . ' as account_status,usr.registered_email_id,pers.personal_address_bldg,'
                . 'pers.personal_address_country, pers.personal_address_state, pers.personal_address_city, pers.personal_address_zip,'
                . 'pers.nationality,pers.highest_educ_level,'
                . 'com.company_id,com_mst.company_name');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('tenant_company_users com', 'usr.tenant_id = com.tenant_id '
                . 'AND usr.user_id = com.user_id', "left");
        $this->db->join('company_master com_mst', 'com.company_id = com_mst.company_id', "left");
        $this->db->where('usr.tenant_id', $this->user->tenant_id);
        $this->db->where('usr.account_type', 'TRAINE');
        $search_pers_first_name_arr = explode('(', $this->input->get('pers_first_name'));
        $search_taxcode_arr = explode('(', $this->input->get('tax_code'));
        $search_status = $this->input->get('status');

        if ($search_radio == "pers_radio") {
            $pers_first_name_extracted = $search_pers_first_name_arr[0];
            $tax_code_extracted = trim($search_pers_first_name_arr[1], ')');

            if ($tax_code_extracted != '') {
                $this->db->like('usr.tax_code', $tax_code_extracted, 'after');
            } else {
                $this->db->like('pers.first_name', $pers_first_name_extracted, 'after');
            }
        }

        if ($search_radio == "tax_radio") {
            $tax_code_extracted = $search_taxcode_arr[0];
            $this->db->like('usr.tax_code', $tax_code_extracted, 'after');
        }

        if ($search_status != '') {
            $this->db->where('usr.account_status', $this->input->get('status'));
        }

        if ($company_id) {
            $this->db->where('com.company_id', $company_id);
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->traineelist_querychange();
        }
         if ($this->user->role_id == 'TRAINER') {
            $this->trainer_traineelist_querychange();
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('usr.last_modified_on', 'DESC');
        }
        $result = $this->db->get();
        return $result;
    }

    /**
     * function to check duplicate username 
     * @param type $username
     * @return boolean
     */
    public function check_duplicate_user_name($username) {
        $exists = $this->db->select('user_id')->get_where('tms_users', array('user_name' => $username), 1)->num_rows();
        if ($exists) {
            return FALSE;
        }
        return TRUE;
    }
    /**
     * This method gets the Trainee Details view
     * @param type $user_id
     * @return type array
     */
    public function get_trainee($user_id) 
    {
        $tenantid = $this->user->tenant_id;
        $data = array();
        $this->db->select('usr.*,  pers.*,cm.company_name,cm.company_id');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to','left');
        $this->db->where('usr.user_id', $user_id);
        $this->db->where('usr.tenant_id', $tenantid);
        $query = $this->db->get();
        $result = $query->result_array();
        $data[userdetails] = $result[0];
        
        $company_details = $this->get_company_details($user_id);
        $data['company']['company_name'] = 'NA';
        $data['company']['company_id'] = '';
        if ($company_details->num_rows()) 
        {
            
            $data['company']['company_name'] = $company_details->row('company_name');
            $data['company']['company_id'] = $company_details->row('company_id');
        }
        
        $this->db->select('educ_id, educ_level, educ_yr_completion, educ_score, educ_remarks');
        $this->db->from('tms_users_educ');
        $this->db->where('tenant_id', $tenantid);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $data[edudetails] = $query->result_array();
        $this->db->select('othr_cert_id, cert_name, yr_completion, valid_till,oth_remarks');
        $this->db->from('tms_users_othr_cert');
        $this->db->where('tenant_id', $tenantid);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $data[otherdetails] = $query->result_array();
        $this->db->select('wrk_exp_id, org_name, emp_from_date, emp_to_date, designation');
        $this->db->from('tms_users_wrk_exp');
        $this->db->where('tenant_id', $tenantid);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $data[workdetails] = $query->result_array();
        $this->db->select('c.crse_name, c.course_id');
        $this->db->select(' tud.discount_percent, tud.discount_amount');
        $this->db->from('course c');
        $this->db->join('tms_users_discount tud', 'tud.course_id = c.course_id and tud.user_id="' . $user_id . '"', 'LEFT');
        $this->db->where('c.tenant_id', $tenantid);
        $this->db->where('c.crse_status', 'ACTIVE');
        $this->db->order_by("c.crse_name");  
        $query = $this->db->get();
        $data[discountdetails] = $query->result_array();

        return $data;
    }
    /**
     * This method for getting the training deatils of the trainee.
     * @param type $user_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @return boolean
     */
/// added by shubhranshu on 19/11/2018 at 08:32AM
public function get_training_details_new($user_id = NULL, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        if ($offset <= 0) {
            return;
        }
        if ($user_id == NULL) {
            return FALSE;
        } else {
            $trainer_condition = '';
             if ($this->user->role_id == 'TRAINER') {
                $trainer_condition = "AND FIND_IN_SET(" . $this->user->user_id . ",cls.classroom_trainer)";
            }
            $this->db->select("enrol.enrol_status,epd.att_status as attn_stats, enrol.user_id,enrol.pymnt_due_id,enrol.tenant_id, enrol.course_id, enrol.class_id, "
                    . "enrol.enrolled_on, enrol.enrolment_mode,"
                    . "enrol.pymnt_due_id, enrol.payment_status,enrol.training_score, crse.crse_name,cls.class_name,cls.class_start_datetime,cls.class_end_datetime,crse.crse_cert_validity,crse.crse_manager, "
                    . "com_mst.company_name, ei.invoice_id, com_mst.company_name,enrol.company_id");
            $this->db->from('class_enrol  enrol');
            $this->db->join("course crse", "crse.course_id=enrol.course_id and crse.tenant_id=enrol.tenant_id");
            $this->db->join("course_class cls", "cls.course_id=enrol.course_id "
                    . "and cls.class_id=enrol.class_id and cls.tenant_id=enrol.tenant_id $trainer_condition");
            $this->db->where('enrol.user_id', $user_id);
            $this->db->where('enrol.tenant_id', $this->user->tenant_id);
            $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id = enrol.pymnt_due_id', 'left');
            $this->db->join('enrol_pymnt_due epd','epd.pymnt_due_id = ei.pymnt_due_id and epd.user_id = enrol.user_id'); //added by shubhranshu on 23/11/2018
            $this->db->join('company_master com_mst', 'enrol.company_id = com_mst.company_id', "left");
            $this->db->where_in('enrol.enrol_status', array('ENRLBKD', 'ENRLACT'));
            $this->db->group_by('ei.invoice_id'); 
           if ($sort_by) {
                $this->db->order_by($sort_by, $sort_order);
            } else {
                $this->db->order_by('enrol.enrolled_on', 'DESC');
            }
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
           $result = $this->db->get();
           
            return $result->result();
        }
    }
    
    


public function get_training_details($user_id = NULL, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        if ($offset <= 0) {
            return;
        }
        if ($user_id == NULL) {
            return FALSE;
        } else {
            $trainer_condition = '';
             if ($this->user->role_id == 'TRAINER') {
                $trainer_condition = "AND FIND_IN_SET(" . $this->user->user_id . ",cls.classroom_trainer)";
            }
            $this->db->select("enrol.enrol_status, enrol.user_id,enrol.pymnt_due_id,enrol.tenant_id, enrol.course_id, enrol.class_id, "
                    . "enrol.enrolled_on, enrol.enrolment_mode,"
                    . "enrol.pymnt_due_id, enrol.payment_status,enrol.training_score, crse.crse_name,cls.class_name,cls.class_start_datetime,cls.class_end_datetime,crse.crse_cert_validity,crse.crse_manager, "
                    . "com_mst.company_name, ei.invoice_id, com_mst.company_name,enrol.company_id");
            $this->db->from('class_enrol  enrol');
            $this->db->join("course crse", "crse.course_id=enrol.course_id and crse.tenant_id=enrol.tenant_id");
            $this->db->join("course_class cls", "cls.course_id=enrol.course_id "
                    . "and cls.class_id=enrol.class_id and cls.tenant_id=enrol.tenant_id $trainer_condition");
            $this->db->where('enrol.user_id', $user_id);
            $this->db->where('enrol.tenant_id', $this->user->tenant_id);
            $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id = enrol.pymnt_due_id', 'left');
            $this->db->join('company_master com_mst', 'enrol.company_id = com_mst.company_id', "left");
            $this->db->where_in('enrol.enrol_status', array('ENRLBKD', 'ENRLACT'));
           if ($sort_by) {
                $this->db->order_by($sort_by, $sort_order);
            } else {
                $this->db->order_by('enrol.enrolled_on', 'DESC');
            }
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
           $result = $this->db->get();
//           echo $this->db->last_query();
            return $result->result();
        }
    }
    
/**
 * This method for getting training details of the user in export to excel full.
 * @param type $user_id
 * @return boolean
 */
    public function get_excel_all_training_details($user_id = NULL) {
        if ($user_id == NULL) {
            return FALSE;
        } else {
            $trainer_condition = '';
            if ($this->user->role_id == 'TRAINER') {
                $trainer_condition = "AND FIND_IN_SET(" . $this->user->user_id . ",cls.classroom_trainer)";
            }
            $this->db->select("enrol.enrol_status, enrol.user_id, enrol.course_id, enrol.class_id, enrol.enrolled_on, enrol.enrolment_type,"
                    . "enrol.pymnt_due_id, enrol.payment_status,enrol.trainee_feedback_rating, crse.crse_name,cls.class_name,"
                    . "cls.class_start_datetime,cls.class_end_datetime,crse.crse_cert_validity,cls.classroom_trainer,"
                    . "ei.subsidy_amount");
            $this->db->from('class_enrol  enrol');
            $this->db->join("course crse", "crse.course_id=enrol.course_id and crse.tenant_id=enrol.tenant_id");
            $this->db->join("course_class cls", "cls.course_id=enrol.course_id and cls.class_id=enrol.class_id and cls.tenant_id=enrol.tenant_id $trainer_condition");
            $this->db->where('enrol.user_id', $user_id);
            $this->db->where('enrol.tenant_id', $this->user->tenant_id);
            $this->db->join('enrol_pymnt_due ei', 'ei.pymnt_due_id = enrol.pymnt_due_id', "left");
            $this->db->where_in('enrol.enrol_status', array('ENRLBKD', 'ENRLACT'));
            $this->db->order_by('enrol.enrolled_on', 'DESC');
            $this->db->group_by('enrol.class_id');
            if ($this->user->role_id == 'SLEXEC') {
                $this->db->where('enrol.sales_executive_id', $this->user->user_id);
            }
            $result = $this->db->get();
            return $result->result_array();
        }
    }
   /**
    * This method for getting the total count of training details of trainee
    * @param type $user_id
    * @return boolean
    */
    public function get_training_details_count($user_id = NULL) {
        if ($user_id == NULL) {
            return FALSE;
        } else {
            $this->db->select("enrol.enrolled_on, enrol.enrolment_mode, crse.crse_name,cls.class_name,cls.class_start_datetime,cls.class_end_datetime,crse.crse_cert_validity");
            $this->db->from('class_enrol  enrol');
            $this->db->join("course crse", "crse.course_id=enrol.course_id and crse.tenant_id=enrol.tenant_id");
            $this->db->join("course_class cls", "cls.course_id=enrol.course_id and cls.class_id=enrol.class_id and cls.tenant_id=enrol.tenant_id");
            $this->db->where('enrol.user_id', $user_id);
            $this->db->where('enrol.tenant_id', $this->user->tenant_id);
            $result = $this->db->get();
            return $result->num_rows();
        }
    }

    /**
     * function to get trainee details by tax code.
     * @param type $user_id
     * @param type $tenantid
     * @return type
     */
    function get_trainee_taxcode($user_id, $tenantid) 
    {        
        //$this->output->enable_profiler(TRUE);
        $data = array();

        $this->db->select('usr.*,  pers.*,cm.company_name as comp_name,cm.company_id');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to','left');
        $this->db->where('usr.user_id', $user_id);
        $this->db->where('usr.tenant_id', $tenantid);
        $query = $this->db->get();
        $result = $query->result_array();
        $data[userdetails] = $result[0];
        $userid = $data[userdetails][user_id];

        $company_details = $this->get_company_details($userid);
        $data['company']['company_name'] = 'NA';
        $data['company']['company_id'] = '';
        if ($company_details->num_rows()) 
        {
            $data['company']['company_name'] = $company_details->row('company_name');
            $data['company']['company_id'] = $company_details->row('company_id');
        }


        $this->db->select('educ_id, educ_level, educ_yr_completion, educ_score, educ_remarks');
        $this->db->from('tms_users_educ');
        $this->db->where('tenant_id', $tenantid);
        $this->db->where('user_id', $userid);
        $query = $this->db->get();
        $data[edudetails] = $query->result_array();

        $this->db->select('othr_cert_id, cert_name, yr_completion, valid_till,oth_remarks');
        $this->db->from('tms_users_othr_cert');
        $this->db->where('tenant_id', $tenantid);
        $this->db->where('user_id', $userid);
        $query = $this->db->get();
        $data[otherdetails] = $query->result_array();

        $this->db->select('wrk_exp_id, org_name, emp_from_date, emp_to_date, designation');
        $this->db->from('tms_users_wrk_exp');
        $this->db->where('tenant_id', $tenantid);
        $this->db->where('user_id', $userid);
        $query = $this->db->get();
        $data[workdetails] = $query->result_array();

        $this->db->select('c.crse_name, c.course_id');
        $this->db->select(' tud.discount_percent,tud.discount_amount');
        $this->db->from('course c');
        $this->db->join('tms_users_discount tud', 'tud.course_id = c.course_id and tud.user_id="' . $userid . '"', 'LEFT');
        $this->db->where('c.tenant_id', $tenantid);
        
        if ($this->user->role_id == 'CRSEMGR')
        {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }
        $this->db->where('c.crse_status', 'ACTIVE');
        $this->db->order_by("c.crse_name");  
        $query = $this->db->get();
        $data[discountdetails] = $query->result_array();

        return $data;
    }

    /**
     * This method creates a Trainee account
     * @return boolean
     */
     public function save_user_data() 
    {
        $tenant_id = $this->user->tenant_id;
        
        foreach ($this->input->post() as $key => $value)
        {
            if(!is_array($value)) 
            {
                $$key = trim($value);
            } 
            else 
            {
                $$key = $value;
            }
        }
        if($NRIC == "SNG_4") /////added by shubhranshu for client requirement on 16/12/2019
        {
            $NRIC = 'SNG_3';
        }
        if($this->user->role_id == 'COMPACT') 
        {
          echo  $assign_company = $this->user->company_id;
        }        
        $dateTime = date('Y-m-d H:i:s');
        $other_identi_type = NULL;
        $other_identi_code = NULL;
        $taxcode_prefix = '';
        
        if ($country_of_residence == 'IND') 
        {
            $tax_code_type = 'PAN';
            $tax_code = $PAN;
            
        }
        if ($country_of_residence == 'SGP') 
        {
            $tax_code_type = $NRIC;
            $tax_code = $NRIC_ID;
            if ($NRIC == "SNG_3") 
            {
                $other_identi_type = $NRIC_OTHER;
                $other_identi_code = $tax_code;
            }
           
            if( $this->user->tenant_id=='T02')
            {
                    $taxcode_prefix = 'XP';
            }
            if($this->user->tenant_id=='T03')
           {
                    $taxcode_prefix = 'CAI';
            }
            if($this->user->tenant_id=='T04')
            {
                    $taxcode_prefix = 'FL';
            }
            if($this->user->tenant_id=='T22')
            {
                    $taxcode_prefix = 'CD';
            }
            if($this->user->tenant_id=='T20')
            {
                    $taxcode_prefix = 'WB';
            }
            if($this->user->tenant_id=='T12')
            {
                    $taxcode_prefix = 'AR';
            }
            if($this->user->tenant_id=='T17')
            {
                    $taxcode_prefix = 'EV';
            }
        }
        
        if ($country_of_residence == 'USA') 
        {
            $tax_code_type = 'SSN';
            $tax_code = $SSN;
        }
        $password = NULL;
        $encrypted_password = NULL;
        $activation_key = NULL;
        $activate_user_status = NULL;
        $acct_acti_date_time = '0000-00-00 00:00:00';

        if ($activate_user == 'ACTIVE' && $bypassemail == "EMACRQ") 
        {
            $activation_key = random_key_generation();
            $activate_user_status = 'PENDACT';
        } 
        else 
        {
            $activate_user_status = $activate_user;
        }
        if ($activate_user == 'ACTIVE' && $bypassemail == "BPEMAC") 
            
        {
            $acct_acti_date_time = $dateTime;
        }
        if ($activate_user == 'ACTIVE') 
        {
            $password = random_key_generation();
            $encrypted_password = $this->bcrypt->hash_password($password);
        }
        $tms_users_data = array(
            'tenant_id' => $this->user->tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'INTUSR',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'password' => $encrypted_password,
            'acc_activation_type' => $bypassemail,
            'activation_key' => $activation_key,
            'registered_email_id' => $user_registered_email,
            'country_of_residence' => $country_of_residence,
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $acct_acti_date_time,
            'acct_deacti_date_time' => NULL,
            'account_status' => $activate_user_status,
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => $this->user->user_id,
            'created_on' => $dateTime,
            'last_modified_by' => $this->user->user_id,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL
        );
        $this->db->trans_start();
        $this->db->insert('tms_users', $tms_users_data);
        $user_id = $this->db->insert_id();
        if ($other_identi_type == 'NOTAXCODE' &&  $tax_code_type == "SNG_3")
        {
            $data = array('tax_code' => $taxcode_prefix.$user_id,
                'other_identi_code' => $taxcode_prefix.$user_id);
            $this->db->where("user_id", $user_id);
            $this->db->update("tms_users", $data);
        }
      
        if ($assign_company != '') 
        {
            
            $assign_company1 =explode("/",$assign_company);
//            print_r($assign_company1);exit;
            $assign_company=$assign_company1[0];
            $assign_company2=$assign_company1[1];
            $company_data = array(
                'company_id' => $assign_company,
                'tenant_id' => $this->user->tenant_id,
                'user_id' => $user_id,
                'user_acct_status' => $activate_user_status,
                'acct_acti_date_time' => $acct_acti_date_time,
                'acct_deacti_date_time' => '',
                'deacti_reason' => '',
                'deacti_reason_oth' => '',
                'deacti_by' => '',
                'assigned_by' => $this->user->user_id,
                'assigned_on' => $dateTime,
                'last_modified_by' => $this->user->user_id,
                'last_modified_on' => $dateTime
            );
//            print_r($company_data);exit;
            $this->db->insert('tenant_company_users', $company_data);
        }
       
        $dob = (empty($pers_dob)) ? NULL : date('Y-m-d', strtotime($pers_dob));
       
       
        $cert_sent_to =explode("/",$cert_sent_to);
        $cert_sent_to=$cert_sent_to[0];
       
        if($assign_company2=="OTHERS"){
             $cert_sent_to=$cert_sent_to_others;
        }
        
        
            $tms_users_pers_data = array(
            'cert_sent_to' => $cert_sent_to,
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($pers_first_name),
            'gender' => $pers_gender,
            'dob' => $dob, 
            'alternate_email_id' => $pers_alternate_email,
            'contact_number' => $pers_contact_number,
            'alternate_contact_number' => $pers_alternate_contact_number,
            'race' => $race,
            'salary_range' => $sal_range,
            'personal_address_bldg' => strtoupper($pers_personal_address_bldg),
            'personal_address_city' => strtoupper($pers_city),
            'personal_address_state' => $pers_states,
            'personal_address_country' => $pers_country,
            'personal_address_zip' => strtoupper($pers_zip_code),
            'photo_upload_path' => NULL,
            'individual_discount' => $individual_discount,
            'certificate_pick_pref' => $certificate_pick_pref,
            'indi_setting_list_size' => NULL,
            'highest_educ_level' => $highest_educ_level,
            'occupation_code' => $occupation,
            'nationality' => $nationality
        );
            
        $this->db->insert('tms_users_pers', $tms_users_pers_data);
       for ($i = 0; $i < count($edu_level); $i++) {
            if ($edu_level[$i]) {
                $edu = array(
                    'tenant_id' => $this->user->tenant_id,
                    'user_id' => $user_id,
                    'educ_id' => '',
                    'educ_level' => $edu_level[$i],
                    'educ_yr_completion' => $edu_year_of_comp[$i],
                    'educ_score' => $edu_score_grade[$i],
                    'educ_remarks' => strtoupper($edu_remarks[$i]),
                );
                $this->db->insert('tms_users_educ', $edu);
            }
        }
       for ($i = 0; $i < count($oth_certi_name); $i++) {
            if ($oth_certi_name[$i]) {
                $other = array(
                    'tenant_id' => $this->user->tenant_id,
                    'user_id' => $user_id,
                    'othr_cert_id' => '',
                    'cert_name' => strtoupper($oth_certi_name[$i]),
                    'yr_completion' => $oth_year_of_certi[$i],
                    'valid_till' => ($oth_validity[$i]) ? date('Y-m-d', strtotime($oth_validity[$i])) : NULL,
                    'oth_remarks' => strtoupper($oth_remarks[$i]),
                );
                $this->db->insert('tms_users_othr_cert', $other);
            }
        }
        for ($i = 0; $i < count($work_org_name); $i++) {
            if ($work_org_name[$i]) {
                $other = array(
                    'tenant_id' => $this->user->tenant_id,
                    'user_id' => $user_id,
                    'wrk_exp_id' => '', 
                    'org_name' => strtoupper($work_org_name[$i]),
                    'emp_from_date' => ($work_empfrom[$i]) ? date('Y-m-d', strtotime($work_empfrom[$i])) : NULL,
                    'emp_to_date' => ($work_empto[$i]) ? date('Y-m-d', strtotime($work_empto[$i])) : NULL,
                    'designation' => $work_designation[$i],
                );
                $this->db->insert('tms_users_wrk_exp', $other);
            }
        }
        foreach ($indi_disc as $k => $row) {
            $indi_data = array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'course_id' => $k,
                'discount_percent' => $row,
                'discount_amount' => $indi_disc_amt[$k] 
            );
            $this->db->insert('tms_users_discount', $indi_data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
         /* get details of tenent for makeing url for public portal skm start*/
        $this->db->select('*');
        $this->db->from('tenant_master');
        $this->db->where('tenant_id',$this->user->tenant_id);
        $x= $this->db->get()->row();
        
        $base_url=base_url();
        $y = explode('.',$base_url);
        $header = 'http://';
        $url = $header.$x->tenant_short_name.'.'.$y[1].'.'.$y[2];
        /* end*/
        
        if($this->user->tenant_id !='T03')
        {
            if ($activate_user == 'ACTIVE')
            {
                $user_details = array('username' => $user_name,
                    'email' => $user_registered_email, 'password' => $password,
                    'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),
                    'gender' => $pers_gender,'url'=>$url);
                if ($bypassemail == 'BPEMAC') { 	                    
                    $this->send_trainy_email($user_details, $bypassemail);
                }
                if ($bypassemail == 'EMACRQ') {                                                           
                    $user_details['link'] = base_url() . 'activate_user/index/' . $user_id . '/' . md5($activation_key);
                    $this->send_trainy_email($user_details, $bypassemail);
                }
            }
        }
        
    
        return $user_id;
    }
/**
 * This method for sending account activation and creation mail to the trainee
 * @param type $user
 * @param type $bypassemail
 * @return type
 */
    public function send_trainy_email($user, $bypassemail) {
        $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);
        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);
        $subject = NULL;
        $body = NULL;
        if ($user['gender'] == 'MALE') {
           $body = "Dear Mr. " . $user['firstname'] . ',';
        } elseif ($user['gender'] == 'FEMALE') {
            $body .="Dear Ms. " . $user['firstname'] . ',';
        } else {
            $body .="Dear " . $user['firstname'] . ',';
        }
        if ($bypassemail == 'BPEMAC') { 	                    
            $subject = 'Your Account Creation Acknowledgment';
            $body .= "<br/><br/>Thank you for registering with us at <b>'" . $tenant_details->tenant_name . "'</b>. Your Training Account has been successfully created.<br/><br/>";
            $body .= "With this Training Account, you will be able to track all the courses that you take with us at <b>'" . $tenant_details->tenant_name . "'</b> as well as enroll for new courses with us.<br/><br/>";
        }
        if ($bypassemail == 'EMACRQ') { 
            $subject = 'Your Account Activation Acknowledgment';
            $body .= "<br/><br/>Thank you for registering with us at <b>'" . $tenant_details->tenant_name . "'</b>. Your Training Account has been successfully created.<br/>";
            $body .= "With this Training Account, you will be able to track all the courses that you take with us at <b>'" . $tenant_details->tenant_name . "'</b> as well as enroll for new courses with us.<br/><br/>";
            if ($user['link']) {
                $body .= 'Please click here or copy the URL <br/> ';
                $body .= '<a target="_blank" href="' . $user['link'] . '">' . $user['link'] . '</a> <br/> to access your Training Account.<br/><br/>';
            }
        }
        $body .= "<strong>Your Username:</strong> " . $user['username'] . "<br/>";
        $body .= "<strong>Your Password:</strong> " . $user['password'] . "<br/><br/>";
       // $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" . base_url() . "</a></strong><br/><br/>"; comented due to public portal
       $body .= "<strong>To acces your public portal account please <a href='" . $user['url'] . "'>Click Here</a></strong><br/><br/>";
        $body .= $footer_data;
        return send_mail($user['email'], '', $subject, $body);
    }
    ///////added by shubhranshu to prevent enrollment for paid company invoice on 05/12/2018////////////////
    public function check_company_invoice_status($comp_id,$course_id,$class_id){
       $this->db->select('payment_status');
       $this->db->from('class_enrol');
       $this->db->where('course_id',$course_id);
       $this->db->where('class_id',$class_id);
       $this->db->where('company_id',$comp_id);
       $this->db->where('enrolment_mode','COMPSPON');
       $res=$this->db->get()->row();
       //echo $this->db->last_query();exit;
       if(!empty($res)){
           return $res;
       }else{
            $res['payment_status']='NULL';
           return $res;
       }
       
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
   /**
    *  for activating trainee
    * @param type $user_id
    * @param type $activation_key
    * @return boolean
    */
    public function verify_trainee_user($user_id, $activation_key) {
        if ($user_id && $activation_key) {
            $this->db->select('activation_key');
            $this->db->from('tms_users');
            $this->db->where('user_id', $user_id);
            $db_activation_key = md5($this->db->get()->row('activation_key'));
            if ($db_activation_key == $activation_key) {
                $user_data = array('account_status' => 'ACTIVE');
                $this->db->where('user_id', $user_id);
                $status = $this->db->update('tms_users', $user_data);
                if ($status) {
                    $company_data = array('user_acct_status' => 'ACTIVE');
                    $this->db->where('user_id', $user_id);
                    $this->db->update('tenant_company_users', $company_data);
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
    }
    /**
     * function to save trainee data from bulk file
     * @param type $data
     * @return boolean
     */
    public function save_bulk_user_data($data) {
        //print_r($data);exit;
        $status = array();
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        $dateTime = date('Y-m-d H:i:s');
        $activation_key = NULL;
        $activate_user = 'ACTIVE';
        $acct_acti_date_time = $dateTime;
        $bypassemail = 'BPEMAC';
        $password = random_key_generation();
        $encrypted_password = $this->bcrypt->hash_password($password);
        // added by shubhranshu to generate user name while notax code on 03/12/2018//////
        if($other_identi_type == 'NOTAXCODE'){
            $user_notax=random_key_generation().date('is');
            $taxcode = strtoupper($user_notax);
        }else{
             $taxcode = strtoupper($taxcode);
        }
       // /////////////////////////////////////////////////////////////
        
        if($this->user->tenant_id == 'T02') {
            $user_name = "XPZ".$taxcode;
        } else if($this->user->tenant_id == 'T03'){
            $user_name = "CAI".$taxcode;
        } else if($this->user->tenant_id == 'T04'){
            $user_name = "FL".$taxcode;
        }else if($this->user->tenant_id == 'T22'){
            $user_name = "CD".$taxcode;
        }else if($this->user->tenant_id == 'T20'){
            $user_name = "WB".$taxcode;
        }else if($this->user->tenant_id == 'T17'){
            $user_name = "EV".$taxcode;
        }else if($this->user->tenant_id == 'T12'){
            $user_name = "AR".$taxcode;
        } else {
            $user_name = $taxcode;
        }
        /////////below code added by shubhranshu to check & unique NRIC/////
        if(strlen($user_name) > 13){
            $user_name = substr($user_name,0,8);
        }
        
        $check_username_unique = $this->is_username_unique($user_name);
        
        if($check_username_unique >0){
            $user_name = $user_name.date('is');
        }////////////////////////////////////ssp end///////////////////////////////////
        $tms_users_data = array(
            'tenant_id' => $this->user->tenant_id,
            'account_type' => 'TRAINE',
            'registration_mode' => 'INTUSR',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'password' => $encrypted_password,
            'acc_activation_type' => $bypassemail,
            'activation_key' => $activation_key,
            'registered_email_id' => $EmailId,
            'country_of_residence' => $countryofresidence,
            'tax_code_type' => $taxcodetype,
            'tax_code' => strtoupper($taxcode),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $acct_acti_date_time,
            'acct_deacti_date_time' => NULL,
            'account_status' => $activate_user,
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => $this->user->user_id,
            'created_on' => $dateTime,
            'last_modified_by' => $this->user->user_id,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL
        );
        $this->db->trans_start();
        $this->db->insert('tms_users', $tms_users_data);
        $user_id = $this->db->insert_id();
       if (!empty($this->user->company_id)) {
            $CompanyCode = $this->user->company_id;
        }
        if ($CompanyCode) {
            $company_data = array(
                'company_id' => $CompanyCode,
                'tenant_id' => $this->user->tenant_id,
                'user_id' => $user_id,
                'user_acct_status' => $activate_user,
                'acct_acti_date_time' => $acct_acti_date_time,
                'acct_deacti_date_time' => '',
                'deacti_reason' => '',
                'deacti_reason_oth' => '',
                'deacti_by' => '',
                'assigned_by' => $this->user->user_id,
                'assigned_on' => $dateTime,
                'last_modified_by' => $this->user->user_id,
                'last_modified_on' => $dateTime
            );
            $this->db->insert('tenant_company_users', $company_data);
        }
        $occupation = empty($occupation) ? NULL : $occupation;
        $dob_input = empty($dob)? NULL: date('Y-m-d',  strtotime($dob));
        $tms_users_pers_data = array(
            'tenant_id' => $this->user->tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($firstname),
            'last_name' => strtoupper($lastname),
            'gender' => $gender,
            'dob' => $dob_input,
            'alternate_email_id' => '',
            'contact_number' => $ContactNumber,
            'alternate_contact_number' => '',
            'race' => $RaceCode,
            'salary_range' => $SalaryRangeCode,
            'personal_address_bldg' => strtoupper($address),
            'personal_address_city' => strtoupper($City),
            'personal_address_state' => $State,
            'personal_address_country' => $Country,
            'personal_address_zip' => strtoupper($ZipCode),
            'photo_upload_path' => NULL,
            'individual_discount' => number_format($Discount, 2, '.', ''),
            'certificate_pick_pref' => "cerit_self",
            'indi_setting_list_size' => NULL,
            'occupation_code' => $occupation,
            'highest_educ_level' => $education,
            'nationality' => $nationality,
        );
        $this->db->insert('tms_users_pers', $tms_users_pers_data);
        //// added by shubhranshu to update the tax code and other identi code as taxcode on 03/12/2018//////
        if($this->user->tenant_id == 'T02') {
            $no_tax_tax_code = "XPZ".$user_id;
        } else if($this->user->tenant_id == 'T03'){
            $no_tax_tax_code = "CAI".$user_id;
        } else if($this->user->tenant_id == 'T04'){
            $no_tax_tax_code = "FL".$user_id;
        }else if($this->user->tenant_id == 'T22'){
            $no_tax_tax_code = "CD".$user_id;
        }else if($this->user->tenant_id == 'T20'){
            $no_tax_tax_code = "WB".$user_id;
        }else if($this->user->tenant_id == 'T17'){
            $no_tax_tax_code = "EV".$user_id;
        }else if($this->user->tenant_id == 'T12'){
            $no_tax_tax_code = "AR".$user_id;
        }else {
            $no_tax_tax_code = $user_id;
        }
        
        if($other_identi_type == 'NOTAXCODE' && $taxcodetype == "SNG_3"){
            $notaxcode_data = array(
                'other_identi_code' => $no_tax_tax_code,
                'tax_code' => $no_tax_tax_code
            );
            $this->db->where('user_id', $user_id);
            $this->db->update('tms_users', $notaxcode_data);
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $status['status'] = FALSE;
            return $status;
        }
        
         $user_details = array('username' => $user_name,
            'email' => $EmailId, 'password' => $password,
            'firstname' => strtoupper($firstname), 'lastname' => strtoupper($lastname),
            'gender' => $gender);
         if(!empty($EmailId)) {
            $this->send_trainy_email($user_details, $bypassemail);
        }
        $status['userid_for_notax'] = $no_tax_tax_code;// added userid by shubhranshu to fetch the user_id if notaxcode
        $status['status'] = TRUE;
        $status['password'] = $password;
        $status['username'] = $user_name;
        return $status;
    }
    
    public function is_username_unique($username) {
        return $this->db->select('user_name')->get_where('tms_users', array('user_name' => $username), 1)->num_rows();
    }
    /**
     * function to deactivate trainee
     * @return boolean
     */
    public function deactivate_trainee() {
        
        $tenant_id = $this->user->tenant_id;
        $user_id = $this->input->post('userid');
        $reason_for_deactivation = $this->input->post('reason_for_deactivation');
        $other_reason_for_deactivation = $this->input->post('other_reason_for_deactivation');
        $company_id = $this->input->post('hiddencompanyid');
        //$this->db->trans_start();
        if (!empty($company_id)) {
            $deactive = array(
                'acct_deacti_date_time' => date('Y-m-d H:i:s'),
                'deacti_reason' => $reason_for_deactivation,
                'deacti_reason_oth' => strtoupper($other_reason_for_deactivation),
                'user_acct_status' => 'INACTIV',
                'deacti_by' => $this->user->user_id
            );
            $this->db->where('user_id', $user_id);
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where('company_id', $company_id);
            $this->db->update('tenant_company_users', $deactive);
        } else {
            $deactive = array(
                'acct_deacti_date_time' => date('Y-m-d H:i:s'),
                'deacti_reason' => $reason_for_deactivation,
                'deacti_reason_oth' => strtoupper($other_reason_for_deactivation),
                'account_status' => 'INACTIV',
                'deacti_by' => $this->user->user_id
            );
            $this->db->where('user_id', $user_id);
            $this->db->where('tenant_id', $tenant_id);
            $this->db->update('tms_users', $deactive);
        }
        //$this->db->trans_complete();
        //echo $this->db->last_query();exit;
        //if ($this->db->trans_status() === FALSE) {
            //return FALSE;
       // } else {
            return TRUE;
        //}
    }

    /**
     * This method for checking email change
     * @param type $new_email_id
     * @param type $user_id
     * @return boolean
     */
    private function check_registered_email_change($new_email_id, $user_id) {
        $this->db->select('registered_email_id,user_name,acc_activation_type,account_status');
        $this->db->from('tms_users');
        $this->db->where('tenant_id', $this->user->tenant_id);
        $this->db->where('user_id', $user_id);
        $result = $this->db->get();
        $user_registered_email = $result->row('registered_email_id');
        if (trim($user_registered_email) != trim($new_email_id)) {
            $result_array = array('status' => TRUE, 'user_name' => $result->row('user_name'),
                'acc_activation_type' => $result->row('acc_activation_type'), 'account_status' => $result->row('account_status'));
            return $result_array;
        } else {
            return $result_array = array('status' => FALSE, 'user_name' => $result->row('user_name'),
                'acc_activation_type' => $result->row('acc_activation_type'), 'account_status' => $result->row('account_status'));
        }
    }
    /**
     * Get Tenant company user detail
     * @param type $user_id
     * @return type
     */
    private function get_tenant_company_users($user_id) {
        $this->db->select('company_id');
        $this->db->from('tenant_company_users');
        $this->db->where('user_id', $user_id);
        $this->db->where('tenant_id', $this->user->tenant_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * function to update trainee data
     * @return boolean
     */
     public function update_trainee() 
    {
        $tenant_id = $this->user->tenant_id;
        foreach ($this->input->post() as $key => $value) 
        {
            if(!is_array($value)) 
            {
                $$key = trim($value);
            } 
            else 
            {
                $$key = $value;
            }
        }
        $dateTime = date('Y-m-d H:i:s');
        $other_identi_type = NULL;
        $other_identi_code = NULL;
        $taxcode_prefix='';
        if ($country_of_residence == 'IND') 
        {
            $tax_code_type = 'PAN';
            $tax_code = $PAN;
        }
        if ($country_of_residence == 'SGP') 
        {
            $tax_code_type = $NRIC;
            $tax_code = $NRIC_ID;
            if ($NRIC == "SNG_3") 
            {
                $other_identi_type = $NRIC_OTHER;
                $other_identi_code = $tax_code;
            }
            if( $this->user->tenant_id=='T02')
            {
                    $taxcode_prefix = 'XP';
            }
            if($this->user->tenant_id=='T03')
            {
                    $taxcode_prefix = 'CAI';
            }
            if($this->user->tenant_id=='T04'){
                    $taxcode_prefix = 'FL';
            }
            if($this->user->tenant_id=='T22'){
                    $taxcode_prefix = 'CD';
            }
            if($this->user->tenant_id=='T20'){
                    $taxcode_prefix = 'WB';
            }
            if($this->user->tenant_id=='T17'){
                    $taxcode_prefix = 'EV';
            }
            if($this->user->tenant_id=='T12'){
                    $taxcode_prefix = 'AR';
            }
        }
        if ($country_of_residence == 'USA')
        {
            $tax_code_type = 'SSN';
            $tax_code = $SSN;
        }
        $new_array = array();
        $result_array = $this->check_registered_email_change($user_registered_email, $this->input->post('userid'));
        if ($result_array['acc_activation_type'] == "BPEMAC" && $result_array['account_status'] == 'ACTIVE') 
        {
            $new_array = array('account_status' => 'ACTIVE', 'acc_activation_type' => 'BPEMAC');
        }
        else if ($result_array['acc_activation_type'] == "BPEMAC" && $result_array['account_status'] == 'PENDACT') 
        {
            if ($bypassemail == 'EMACRQ')
            {
                $activation_key = random_key_generation();
                $password = random_key_generation();
                $encrypted_password = $this->bcrypt->hash_password($password);
                $new_array = array('activation_key' => $activation_key, 'password' => $encrypted_password,
                    'account_status' => 'PENDACT', 'acc_activation_type' => 'EMACRQ');
            }
            else if ($bypassemail == 'BPEMAC') 
            {
                if ($activate_user == 'ACTIVE') 
                {
                    $password = random_key_generation();
                    $encrypted_password = $this->bcrypt->hash_password($password);
                    $new_array = array('password' => $encrypted_password, 'account_status' => 'ACTIVE',
                        'acct_acti_date_time' => $dateTime, 'acc_activation_type' => 'BPEMAC');
                }
                else if ($activate_user == 'PENDACT')
                {
                    $new_array = array('account_status' => 'PENDACT', 'acc_activation_type' => 'BPEMAC');
                }
            }
        }
        else if ($result_array['acc_activation_type'] == "EMACRQ" && $result_array['account_status'] == 'PENDACT') 
        {
            if ($bypassemail == 'BPEMAC') 
           {
                if ($activate_user == 'ACTIVE') 
                {
                    $password = random_key_generation();
                    $encrypted_password = $this->bcrypt->hash_password($password);
                    $new_array = array('password' => $encrypted_password, 'account_status' => 'ACTIVE',
                        'acct_acti_date_time' => $dateTime, 'acc_activation_type' => 'BPEMAC',
                        'activation_key' => ""
                    );
                } 
                else 
                {
                    $new_array = array('password' => "", 'account_status' => 'PENDACT',
                        'acct_acti_date_time' => "", 'acc_activation_type' => 'BPEMAC',
                        'activation_key' => ""
                    );
                }
            }
            else
            {
                if ($result_array['status'] == TRUE) 
                {
                    $activation_key = random_key_generation();
                    $password = random_key_generation();
                    $encrypted_password = $this->bcrypt->hash_password($password);
                    $new_array = array('activation_key' => $activation_key, 'password' => $encrypted_password,
                        'account_status' => 'PENDACT', 'acc_activation_type' => 'EMACRQ');
                } 
                else 
                {
                    $new_array = array('account_status' => 'PENDACT', 'acc_activation_type' => 'EMACRQ');
                }
            }
        }
        else if ($result_array['acc_activation_type'] == "EMACRQ" && $result_array['account_status'] == 'ACTIVE') 
        {
            $new_array = array('account_status' => 'ACTIVE', 'acc_activation_type' => 'EMACRQ');
        }
        $date = date('Y-m-d H:i:s');
        
        $data = array(
            'country_of_residence' => $country_of_residence,
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'registered_email_id' => $user_registered_email,
            'other_identi_upload' => '',
            'last_modified_by' => $this->user->user_id,
            'last_modified_on' => $date,
                ) + $new_array;
        //print_r($data);exit;
        $this->db->trans_start();
        $user_id = $this->input->post('userid');
        $this->db->where('user_id', $user_id);
        $this->db->update('tms_users', $data);
       if ($other_identi_type == 'NOTAXCODE' && $tax_code_type == "SNG_3")
       {
            $data = array('tax_code' => $taxcode_prefix.$user_id,
                'other_identi_code' => $taxcode_prefix.$user_id);
            $this->db->where("user_id", $user_id);
            $this->db->update("tms_users", $data);
        }
        $pers_dob = $this->input->post('personal_dob');
        $dob = (empty($pers_dob)) ? NULL : date('Y-m-d', strtotime($pers_dob));
        $assign_company1 =explode("/",$assign_company);
         $assign_company=$assign_company1[0];
      
         $assign_company_name=$assign_company1[1];
       
        if($assign_company_name=="INDIVIDUAL")
        {
            $cert_sent_to =explode("/",$cert_sent_to);
            $cert_sent_to=$cert_sent_to[0];
            
           
        }
        else
        {
            $cert_sent_to="";
            
        }
       
        if($assign_company_name=="OTHERS")
        {
           $cert_sent_to=   $cert_sent_to_others;
        }
        
        $pers = array(
            'cert_sent_to' => $cert_sent_to,
            'first_name' => trim(strtoupper($this->input->post('pers_first_name'))),
            'last_name' => "",
            'gender' => $this->input->post('gender'),
            'dob' => $dob, 
            'alternate_email_id' => $this->input->post('pers_alt_email'),
            'contact_number' => $this->input->post('pers_contact_phone'),
            'alternate_contact_number' => $pers_contact_mobile,
            'race' => $race,
            'salary_range' => $sal_range,
            'personal_address_bldg' => strtoupper($pers_personal_address),
            'personal_address_city' => strtoupper($pers_city),
            'personal_address_state' => $pers_states,
            'personal_address_country' => $pers_country,
            'personal_address_zip' => strtoupper($personal_address_zip),
            'individual_discount' => number_format($individual_discount, 2, '.', ''),
            'certificate_pick_pref' => $certificate_pick_pref,
            'indi_setting_list_size' => NULL,
            'occupation_code' => $occupation,
            'highest_educ_level' => $highest_educ_level,
            'nationality' => $nationality
        );

        $this->db->where('user_id', $user_id);
        $this->db->update('tms_users_pers', $pers);
         
       
        if ($assign_company) {
            $company_data = array(
                'company_id' => $assign_company,
                'tenant_id' => $this->user->tenant_id,
                'user_id' => $user_id,
                'user_acct_status' => $new_array['account_status'],
                'acct_acti_date_time' => $dateTime,
                'acct_deacti_date_time' => '',
                'deacti_reason' => '',
                'deacti_reason_oth' => '',
                'deacti_by' => '',
                'assigned_by' => $this->user->user_id,
                'assigned_on' => $dateTime,
                'last_modified_by' => $this->user->user_id,
                'last_modified_on' => $date
            );
            $tenant_company_users = $this->get_tenant_company_users($user_id);
            if ($tenant_company_users > 0) 
            {
                $this->db->where('user_id', $user_id);
                $this->db->update('tenant_company_users', $company_data);
            } 
            else 
            {
                $this->db->insert('tenant_company_users', $company_data);
            }
        } else {
            $this->db->where('user_id', $user_id);
            $this->db->where('tenant_id', $this->user->tenant_id);
            $this->db->delete('tenant_company_users');
        }
        $this->db->where('user_id', $user_id);
        $this->db->delete('tms_users_educ');

        $this->db->where('user_id', $user_id);
        $this->db->delete('tms_users_othr_cert');

        $this->db->where('user_id', $user_id);
        $this->db->delete('tms_users_wrk_exp');
       for ($i = 0; $i < count($edu_level); $i++) {
            if ($edu_level[$i]) {
                $edu = array(
                    'tenant_id' => $this->user->tenant_id,
                    'user_id' => $user_id,
                    'educ_id' => '',
                    'educ_level' => $edu_level[$i],
                    'educ_yr_completion' => ($edu_year_of_comp[$i]) ? $edu_year_of_comp[$i] : NULL,
                    'educ_score' => $edu_score_grade[$i],
                    'educ_remarks' => strtoupper($edu_remarks[$i]),
                );
                $this->db->insert('tms_users_educ', $edu);
            }
        }

        for ($i = 0; $i < count($oth_certi_name); $i++) {
            if ($oth_certi_name[$i]) {
                $other = array(
                    'tenant_id' => $this->user->tenant_id,
                    'user_id' => $user_id,
                    'othr_cert_id' => '',
                    'cert_name' => strtoupper($oth_certi_name[$i]),
                    'yr_completion' => $oth_year_of_certi[$i],
                    'valid_till' => ($oth_validity[$i]) ? date('Y-m-d', strtotime($oth_validity[$i])) : NULL,
                    'oth_remarks' => strtoupper($oth_remarks[$i]),
                );
                $this->db->insert('tms_users_othr_cert', $other);
            }
        }

        for ($i = 0; $i < count($work_org_name); $i++) {
            if ($work_org_name[$i]) {
                $other = array(
                    'tenant_id' => $this->user->tenant_id,
                    'user_id' => $user_id,
                    'wrk_exp_id' => '',
                    'org_name' => strtoupper($work_org_name[$i]),
                    'emp_from_date' => ($work_empfrom[$i]) ? date('Y-m-d', strtotime($work_empfrom[$i])) : NULL,
                    'emp_to_date' => ($work_empto[$i]) ? date('Y-m-d', strtotime($work_empto[$i])) : NULL,
                    'designation' => $work_designation[$i],
                );
                $this->db->insert('tms_users_wrk_exp', $other);
            }
        }
         $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            if ($result_array['account_status'] == 'PENDACT') {
                if ($bypassemail == 'EMACRQ') {
                    if (($result_array['acc_activation_type'] == "EMACRQ" && $result_array['status'] == TRUE) || ($result_array['acc_activation_type'] == "BPEMAC")) {
                        $user_details = array('username' => $result_array['user_name'],
                            'email' => $user_registered_email, 'password' => $password,
                            'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),
                            'gender' => $gender);
                        $user_details['link'] = base_url() . 'activate_user/index/' . $user_id . '/' . md5($activation_key);
                        $this->send_trainy_email($user_details, $bypassemail);
                    }
                }
                if ($bypassemail == 'BPEMAC' && $activate_user == 'ACTIVE') { //If user selects ?By Pass Email Activation? THEN
                    $user_details = array('username' => $result_array['user_name'],
                        'email' => $user_registered_email, 'password' => $password,
                        'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),
                        'gender' => $pers_gender);
                    $this->send_trainy_email($user_details, $bypassemail);
                }
            }
        }
        return $user_id;
    }

    /**
     * function to get trainee list autocomplete - search by Trainee Name
     * @param type $query
     * @param type $company_id
     * @return string
     */
    public function trainee_user_list_autocomplete($query = NULL, $company_id = NULL) {
        $matches = array();
        $strMatchString = $query;
        if (!empty($strMatchString)) {
            $strQry = 'SELECT usr.user_id, usr.tax_code, pers.first_name, pers.last_name'
                    . ' FROM tms_users usr, tms_users_pers pers';
            if (!empty($company_id)) {
                $strQry .= ', tenant_company_users com';
            }
            $strQry .= ' WHERE usr.user_id=pers.user_id AND usr.tenant_id=pers.tenant_id ';
            if (!empty($company_id)) {
                $strQry .= ' AND usr.user_id = com.user_id  AND usr.tenant_id = com.tenant_id AND com.company_id ="' . $company_id . '"';
            }
            $strQry .= ' AND usr.tenant_id="' . $this->user->tenant_id . '"'
                    . ' AND usr.account_type = "TRAINE"'
                    . ' AND TRIM(pers.first_name) LIKE "%' . TRIM($strMatchString) . '%"';
            $results = $this->db->query($strQry.' LIMIT 200')->result();
            foreach ($results as $result) {
                $matches[] = array(
                    'label' => $result->first_name . ' ' . $result->last_name . '(' . $result->tax_code . ')',
                    'key' => $result->user_id
                );
            }
      }
        return $matches;
    }
    
     /* This function get all details of trainee skm start */
    public function get_trainee_details($user_id)
    {
     $this->db->select('*');
     $this->db->from('tms_users tu');
     $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
     $this->db->where('tu.user_id',$user_id);
     $sql = $this->db->get();
     $data = $sql->row_array();
     $data['individual_discount'] = $this->trainee_discount($user_id);
     $data['trainee_type'] = $this->get_company_details_activity_log($user_id);
     return $data;
    }
    
    public function trainee_discount($user_id){
        $zero = 0;
        $this->db->select('*');
        $this->db->from('tms_users_discount');
        $this->db->where('user_id',$user_id);
        $where = '(discount_percent!=0 or discount_amount!=0)';
//        $this->db->where('discount_percent !=',$zero);
//        $this->db->where('discount_amount !=',$zero);
        $this->db->where($where);
        $sql = $this->db->get();
        if($sql->num_rows()>0){
            foreach($sql->result_array() as $row){
                $data[] = array('discount' => $row['course_id'].'_'.$row['discount_percent'].'#'.$row['discount_amount']);
            }
             return $data;
        }else{
            return $sql = 0;
        }
       
        
    }
    /* get trainee details for activity log skm start */
    public function get_complete_trainee_details($user_id){
    $this->db->select('*');
    $this->db->from('tms_users tu');
    $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
    $this->db->where('tu.user_id',$user_id);
    $sql = $this->db->get();
    $data = $sql->row_array();
    $data['trainee_type'] = $this->get_company_details_activity_log($user_id);
    return $data;
    }
    /* skm end */
    
    /**
     * Get company details
     * @param type $user_id
     * @return type
     */
    public function get_company_details_activity_log($user_id) 
    {
        $this->db->select('cm.company_name , cm.company_id');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('company_master cm', 'cm.company_id=tcu.company_id');
        $this->db->where('tcu.user_id', $user_id);
        $this->db->where("tcu.tenant_id", $this->user->tenant_id);
//        $this->db->where("cm.company_status", 'ACTIVE');
//        $this->db->where("tcu.user_acct_status", 'ACTIVE');
            $sql = $this->db->get();
            if($sql->num_rows()>0)
            {
                return $sql->row()->company_name;
            }
            else
            {
                return false;
            }
    }
    /* end */
    
/**
 * This method is used by Trainee
 * @param type $tax_code
 * @return type
 */
    public function trainee_traineelist_by_name_autocomplete($tax_code = NULL) {
        $matches = array();
        if (!empty($tax_code)) {
            $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code');
            $this->db->from('tms_users usr');
            $this->db->join('tms_users_pers pers', 'usr.user_id=pers.user_id and pers.tenant_id=usr.tenant_id');
            $this->db->where('usr.account_type', 'TRAINE');
            $this->db->where("usr.account_status != 'INACTIV'");
            $this->db->where('usr.tenant_id', $this->user->tenant_id);
            $this->db->like('pers.first_name', $tax_code, 'both');
             if ($this->user->role_id == 'SLEXEC') { 
                $this->traineelist_querychange();
                $this->db->limit(200);
                $this->db->get();
                $query1 = $this->db->last_query();
                $this->db->query("select pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code "
                        . "from tms_users_pers pers , tms_users usr where usr.user_id=pers.user_id and "
                        . "usr.account_type='TRAINE' and usr.account_status != 'INACTIV' and usr.tenant_id='" . $this->user->tenant_id . "' "
                        . "and usr.created_by=" . $this->user->user_id . " and pers.first_name like '%" . $tax_code . "%' limit 200");
                $query2 = $this->db->last_query();
                $query = $this->db->query("(" . $query1 . ") UNION (" . $query2 . ")");
                $results = $query->result();
             } else if ($this->user->role_id == 'CRSEMGR') {
                $this->crsemgr_traineelist_querychange();
                $this->db->limit(200);
                $this->db->get();
                $query1 = $this->db->last_query();
                $this->db->query("select pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code "
                        . "from tms_users_pers pers , tms_users usr where usr.user_id=pers.user_id and "
                        . "usr.account_type='TRAINE' and usr.account_status != 'INACTIV' and usr.tenant_id='" . $this->user->tenant_id . "' "
                        . "and usr.created_by=" . $this->user->user_id . " and pers.first_name like '%" . $tax_code . "%' limit 200");
                $query2 = $this->db->last_query();
                $query = $this->db->query("(" . $query1 . ") UNION (" . $query2 . ") order by first_name");
                $results = $query->result();
            } else {
                $this->db->limit(200);
                $results = $this->db->get()->result();
            }
            foreach ($results as $result) {
                $matches[] = array(
                        'label'=>$result->first_name . ' ' . $result->last_name . '(' . $result->tax_code . ')',
                        'key'=>$result->user_id
                    );
                    
            }
        }
        return $matches;
    }

    /**
     * function to get trainee autocomplete by tax code
     * @param type $query
     * @param type $company_id
     * @return string
     */
    public function trainee_user_list_by_taxcode($query = NULL, $company_id = NULL) {
        
        $matches = array();
        $strMatchString = $query;
        if (!empty($strMatchString)) {
            $strQry = 'SELECT usr.user_id, usr.tax_code, pers.first_name, pers.last_name'
                    . ' FROM tms_users usr, tms_users_pers pers';
            if (!empty($company_id)) {
                $strQry .= ', tenant_company_users com';
            }
            $strQry .= ' WHERE usr.user_id=pers.user_id AND usr.tenant_id=pers.tenant_id ';
            if (!empty($company_id)) {
                $strQry .= ' AND usr.user_id = com.user_id  AND usr.tenant_id = com.tenant_id AND com.company_id ="' . $company_id . '"';
            }
            $strQry .= ' AND usr.tenant_id="' . $this->user->tenant_id . '"'
                    . ' AND usr.account_type = "TRAINE"'
                    . ' AND TRIM(usr.tax_code) LIKE "%' . TRIM($strMatchString) . '%"';
            $results = $this->db->query($strQry.' LIMIT 200')->result();
        }
            foreach ($results as $result) {
                $matches[] = array(
                    'label' => $result->tax_code . '(' . $result->first_name . ' ' . $result->last_name . ')',
                    'key' => $result->user_id
                );
            }
            return $matches;
    }
/**
 * function to check for duplicate taxcode
 * @param type $taxcode
 * @param type $user_id
 * @return type
 */
    public function check_taxcode($taxcode, $user_id = '') {
        $tenant_id=$this->user->tenant_id;
        $this->db->select('tax_code');
        $this->db->from('tms_users');
        $this->db->where('tax_code', $taxcode);
        if ($user_id != '') {
            $this->db->where('user_id !=', $user_id);
        }
        $this->db->where('tenant_id', $tenant_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    /**
     * Search by trainee name
     * @param type $name
     * @return type
     */
    public function search_trainee_by_name($name) {
        $sql = "select usr.user_id, first_name, last_name
        from tms_users usr, tms_users_pers pers
        where usr.tenant_id = pers.tenant_id
        AND usr.user_id = pers.user_id
        AND usr.account_type='TRAINE'
        AND (pers.first_name like ? OR pers.last_name like ?)";
        return $this->db->query($sql, array($name . '%', $name . '%'));
    }
    /**
     * Search by Trainee Id
     * @param type $ids
     * @return type
     */
    public function search_trainee_by_ids($ids) {
        $sql = "select usr.user_id, first_name, last_name
        from tms_users usr, tms_users_pers pers
        where usr.tenant_id = pers.tenant_id
        AND usr.user_id = pers.user_id
        AND usr.user_id in (" . implode(",", $ids) . ")";
        return $this->db->query($sql);
    }

    /**
     * function for upload excel file
     * @return type
     */
    public function excelfile_upload() {
        $config['upload_path'] = './tmp/';
        $config['allowed_types'] = '*';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            $error = array('error' => $this->upload->display_errors());
        } else {
            $out = array();
            $data = array('upload_data' => $this->upload->data());
            $out['path'] = $data['upload_data']['full_path'];
            $out['name'] = $data['upload_data']['file_name'];
            return $out;
        }
    }

    /**
     * This function used for getting trainer feed back in view trainee
     * @param type $course
     * @param type $class
     * @param type $user
     * @return int|boolean
     */
//    public function get_trainer_feedback($course = NULL, $class = NULL, $user = NULL,$payment) 
//    {
//  
//        
//        $payment1=$payment;
//        if ($course == NULL || $class == NULL || $user == NULL || $payment1= NULL) {
//           
//            return FALSE;
//        } 
//        else  if($payment1 == 0)
//        {
//            $this->db->select("feedback_question_id,feedback_answer");
//            $this->db->from("trainer_feedback");
//            $this->db->where("tenant_id", $this->user->tenant_id);
//            $this->db->where("user_id", $user);
//            $this->db->where("course_id", $course);
//            $this->db->where("class_id", $class);
//            $query =$this->db->get();
//            $rowcount=$query->num_rows();
//            if($rowcount>0){
//                $this->db->select("tf.feedback_question_id,tf.feedback_answer,ce.training_score");
//                $this->db->from("trainer_feedback tf");
//                $this->db->join("class_enrol ce",'ce.user_id=ce.user_id');
//                $this->db->where("tf.tenant_id", $this->user->tenant_id);
//                $this->db->where("tf.user_id", $user);
//                $this->db->where("tf.course_id", $course);
//                $this->db->where("tf.class_id", $class);
//                $this->db->where("ce.user_id",$user);
//               }
//            else{
//                $this->db->select("ce.training_score");
//                $this->db->from("class_enrol ce");
//                $this->db->where("ce.tenant_id", $this->user->tenant_id);
//                $this->db->where("ce.course_id", $course);
//                $this->db->where("ce.class_id",$class);
//                $this->db->where("ce.user_id",$user);
//            }
//            $result['trainer'] = $this->db->get()->result();
//            $this->db->select("*");
//            $this->db->from("trainee_feedback tf");
//            $this->db->join("class_enrol e", "tf.tenant_id=e.tenant_id and tf.user_id = e.user_id "
//                    . "and tf.course_id = e.course_id and tf.class_id = e.class_id");
//            $this->db->where("tf.tenant_id", $this->user->tenant_id);
//            $this->db->where("tf.user_id", $user);
//            $this->db->where("tf.course_id", $course);
//            $this->db->where("tf.class_id", $class);
//            $result['trainee'] = $this->db->get()->result();
//            if(empty($result['trainee'])){
//                $result['trainee'] = 0;
//            }
//            return $result;
//        }
//        else 
//        {
//            $this->db->select("feedback_question_id,feedback_answer");
//            $this->db->from("trainer_feedback");
//            $this->db->where("tenant_id", $this->user->tenant_id);
//            $this->db->where("user_id", $user);
//            $this->db->where("course_id", $course);
//            $this->db->where("class_id", $class);
//            $query =$this->db->get();
//            $rowcount=$query->num_rows();
//            if($rowcount>0){
//                $this->db->select("tf.feedback_question_id,tf.feedback_answer,epd.att_status,ce.training_score");
//                $this->db->from("trainer_feedback tf");
//                $this->db->join("enrol_pymnt_due epd",'epd.user_id=tf.user_id');
//                $this->db->join("class_enrol ce",'ce.user_id=ce.user_id');
//                $this->db->where("tf.tenant_id", $this->user->tenant_id);
//                $this->db->where("tf.user_id", $user);
//                $this->db->where("tf.course_id", $course);
//                $this->db->where("tf.class_id", $class);
//                $this->db->where("epd.pymnt_due_id",$payment);
//                $this->db->where("ce.pymnt_due_id",$payment);
//                $this->db->where("epd.user_id",$user);
//                $this->db->where("ce.user_id",$user);
//               }
//            else{
//                $this->db->select("epd.att_status,ce.training_score");
//                $this->db->from("enrol_pymnt_due epd");
//                $this->db->join('class_enrol ce','ce.pymnt_due_id=epd.pymnt_due_id');
//                $this->db->where("epd.pymnt_due_id", $payment);
//                $this->db->where("ce.pymnt_due_id", $payment);
//                $this->db->where("epd.user_id",$user);
//                $this->db->where("ce.user_id",$user);
//            }
//           
//            $result['trainer'] = $this->db->get()->result();
//             $this->db->last_query();
//           
//            $this->db->select("*");
//            $this->db->from("trainee_feedback tf");
//            $this->db->join("class_enrol e", "tf.tenant_id=e.tenant_id and tf.user_id = e.user_id "
//                    . "and tf.course_id = e.course_id and tf.class_id = e.class_id");
//            $this->db->where("tf.tenant_id", $this->user->tenant_id);
//            $this->db->where("tf.user_id", $user);
//            $this->db->where("tf.course_id", $course);
//            $this->db->where("tf.class_id", $class);
//            $result['trainee'] = $this->db->get()->result();
//            if(empty($result['trainee'])){
//                $result['trainee'] = 0;
//            }
//            return $result;
//        }
//    }
    public function get_trainer_feedback($course = NULL, $class = NULL, $user = NULL,$payment) 
    {
  
        
        $payment1=$payment;
        if ($course == NULL || $class == NULL || $user == NULL || $payment1= NULL) {
           
            return FALSE;
        } 
        else  if($payment1 == 0)
        {
            $this->db->select("feedback_question_id,feedback_answer");
            $this->db->from("trainer_feedback");
            $this->db->where("tenant_id", $this->user->tenant_id);
            $this->db->where("user_id", $user);
            $this->db->where("course_id", $course);
            $this->db->where("class_id", $class);
            $query =$this->db->get();
            $rowcount=$query->num_rows();
            if($rowcount>0){
                $this->db->select("tf.feedback_question_id,tf.feedback_answer,ce.training_score");
                $this->db->from("trainer_feedback tf");
                $this->db->join("class_enrol ce",'ce.user_id=tf.user_id and ce.class_id=tf.class_id');
                $this->db->where("tf.tenant_id", $this->user->tenant_id);
                $this->db->where("tf.user_id", $user);
                $this->db->where("tf.course_id", $course);
                $this->db->where("tf.class_id", $class);
                $this->db->where("ce.course_id", $course);
                $this->db->where("ce.class_id", $class);
                $this->db->where("ce.user_id",$user);
               }
            else{
                $this->db->select("ce.training_score");
                $this->db->from("class_enrol ce");
                $this->db->where("ce.tenant_id", $this->user->tenant_id);
                $this->db->where("ce.course_id", $course);
                $this->db->where("ce.class_id",$class);
                $this->db->where("ce.user_id",$user);
            }
            $result['trainer'] = $this->db->get()->result();
            $this->db->select("*");
            $this->db->select("lock_status");
            $this->db->from("course_class");
            $this->db->where("tenant_id", $this->user->tenant_id);
            $this->db->where("course_id", $course);
            $this->db->where("class_id",$class);
            $result['class_lock'] = $this->db->get()->result();
            $this->db->from("trainee_feedback tf");
            $this->db->join("class_enrol e", "tf.tenant_id=e.tenant_id and tf.user_id = e.user_id "
                    . "and tf.course_id = e.course_id and tf.class_id = e.class_id");
            $this->db->where("tf.tenant_id", $this->user->tenant_id);
            $this->db->where("tf.user_id", $user);
            $this->db->where("tf.course_id", $course);
            $this->db->where("tf.class_id", $class);
            $result['trainee'] = $this->db->get()->result();
            if(empty($result['trainee'])){
                $result['trainee'] = 0;
            }
            //////below code was added by shubhranshu for xp for attrition option start-----
            if(TENANT_ID=='T02'){
                $qr =$this->db->query("select att.user_id as user_id,SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) / (select count(cs.class_id) 
                    from class_schld cs where cs.course_id='$course' and cs.class_id='$class' and cs.tenant_id='T02' and (cs.session_type_id='S1' or cs.session_type_id='S2')) as attendence
                    from class_attendance att
                    join course_class cc on cc.class_id='$class' and cc.course_id='$course' and cc.tenant_id='T02'
                    where att.class_id='$class' and att.course_id='$course' and att.user_id='$user'
                    group by att.user_id,att.class_id");
                    //having attendence <= 0.50");
                $result['att_percentage'] =$qr->result_array()[0][attendence];
            }
            //////below code was added by shubhranshu for xp for attrition option end-----
            //echo $this->db->last_query();print_r($result);exit;
            return $result;
        }
        else 
        {
            $this->db->select("feedback_question_id,feedback_answer");
            $this->db->from("trainer_feedback");
            $this->db->where("tenant_id", $this->user->tenant_id);
            $this->db->where("user_id", $user);
            $this->db->where("course_id", $course);
            $this->db->where("class_id", $class);
            $query =$this->db->get();
            $rowcount=$query->num_rows();
            if($rowcount>0){
                $this->db->select("tf.feedback_question_id,tf.feedback_answer,epd.att_status,ce.training_score");
                $this->db->from("trainer_feedback tf");
                $this->db->join("enrol_pymnt_due epd",'epd.user_id=tf.user_id');
                $this->db->join("class_enrol ce",'ce.user_id=tf.user_id and ce.class_id=tf.class_id');
                $this->db->where("tf.tenant_id", $this->user->tenant_id);
                $this->db->where("tf.user_id", $user);
                $this->db->where("tf.course_id", $course);
                $this->db->where("tf.class_id", $class);
                 $this->db->where("ce.course_id", $course);
                $this->db->where("ce.class_id", $class);
                $this->db->where("epd.pymnt_due_id",$payment);
                $this->db->where("ce.pymnt_due_id",$payment);
                $this->db->where("epd.user_id",$user);
                $this->db->where("ce.user_id",$user);
               }
            else{
                $this->db->select("epd.att_status,ce.training_score");
                $this->db->from("enrol_pymnt_due epd");
                $this->db->join('class_enrol ce','ce.pymnt_due_id=epd.pymnt_due_id');
                $this->db->where("epd.pymnt_due_id", $payment);
                $this->db->where("ce.pymnt_due_id", $payment);
                $this->db->where("epd.user_id",$user);
                $this->db->where("ce.user_id",$user);
            }
           
            $result['trainer'] = $this->db->get()->result();
           
            $this->db->select("lock_status");
            $this->db->from("course_class");
            $this->db->where("tenant_id", $this->user->tenant_id);
            $this->db->where("course_id", $course);
            $this->db->where("class_id",$class);
            
            $result['class_lock'] = $this->db->get()->result();
           
            $this->db->select("*");
            $this->db->from("trainee_feedback tf");
            $this->db->join("class_enrol e", "tf.tenant_id=e.tenant_id and tf.user_id = e.user_id "
                    . "and tf.course_id = e.course_id and tf.class_id = e.class_id");
            $this->db->where("tf.tenant_id", $this->user->tenant_id);
            $this->db->where("tf.user_id", $user);
            $this->db->where("tf.course_id", $course);
            $this->db->where("tf.class_id", $class);
            $result['trainee'] = $this->db->get()->result();
            if(empty($result['trainee'])){
                $result['trainee'] = 0;
            }
            
            //////below code was added by shubhranshu for xp2 for attrition option start-----
            if(TENANT_ID=='T02'){
                $qr =$this->db->query("select att.user_id as user_id,SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) / (select count(cs.class_id) 
                    from class_schld cs where cs.course_id='$course' and cs.class_id='$class' and cs.tenant_id='T02' and (cs.session_type_id='S1' or cs.session_type_id='S2')) as attendence
                    from class_attendance att
                    join course_class cc on cc.class_id='$class' and cc.course_id='$course' and cc.tenant_id='T02'
                    where att.class_id='$class' and att.course_id='$course' and att.user_id='$user'
                    group by att.user_id,att.class_id
                    having attendence <= 0.50");
                $result['att_percentage'] =$qr->result_array()[0][attendence];
            }
            //////below code was added by shubhranshu for xp2 for attrition option end-----
            
            return $result;
        }
    }
    /**
     * Remove trainer feedback
     * @param type $user_id
     * @param type $course_id
     * @param type $class_id
     * @return boolean
     */
    private function delete_trainer_feedback($user_id, $course_id, $class_id) {
        $this->db->where("tenant_id", $this->user->tenant_id);
        $this->db->where("user_id", $user_id);
        $this->db->where("course_id", $course_id);
        $this->db->where("class_id", $class_id);
        $this->db->trans_start();
        $result = $this->db->delete("trainer_feedback");
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else if ($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * This method for inserting trainer feed back.
     * @param type $user_id
     * @param type $course_id
     * @param type $class_id
     * @return boolean
     */
   public function trainer_feedback($user_id, $course_id, $class_id) 
    {
        $data = $this->db->select('lock_status')
                        ->from('course_class')->where('class_id', $class_id)->where('course_id', $course_id)->get()->row(0);

        $lock_status = $data->lock_status;
        $this->db->trans_start();
        if($lock_status==0)
        {
            
            $result = $this->delete_trainer_feedback($user_id, $course_id, $class_id);
            foreach ($this->input->post() as $key => $value) 
            {
                if ($result == TRUE) 
                {
                    $data = array('tenant_id' => $this->user->tenant_id,
                        'course_id' => $course_id,
                        'class_id' => $class_id,
                        'user_id' => $user_id,
                        'feedback_question_id' => $key,
                        'feedback_answer' => strtoupper($value));
                        $insert_result = $this->db->insert("trainer_feedback", $data);

                    if ($key == 'SATSRATE') {
                        $feedback_data = array(
                            'trainee_feedback_rating' => $value,
                        );
                        $this->db->where('tenant_id', $this->user->tenant_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('class_id', $class_id);
                        $this->db->where('user_id', $user_id);
                        $update_result = $this->db->update("class_enrol", $feedback_data);
                    }
                    if ($key == 'COMYTCOM') {
                        $feedback_data = array(
                            'training_score' => $value,
                            'trainer_fdbck_by' => $this->user->user_id,
                            'trainer_fdbck_on' => date('Y-m-d H:i:s'),
                        );
                        $this->db->where('tenant_id', $this->user->tenant_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('class_id', $class_id);
                        $this->db->where('user_id', $user_id);
                        $update_result = $this->db->update("class_enrol", $feedback_data);
                    }
                    if ($key == 'FSCORE') {
                        $feedback_data = array(
                            'feedback_score' => $value,
                            
                        );
                        $this->db->where('tenant_id', $this->user->tenant_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('class_id', $class_id);
                        $this->db->where('user_id', $user_id);
                        $update_result = $this->db->update("class_enrol", $feedback_data);
                    }
                    if ($key == 'FGRADE') {
                        $feedback_data = array(
                            'feedback_grade' => $value,
                            
                        );
                        $this->db->where('tenant_id', $this->user->tenant_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('class_id', $class_id);
                        $this->db->where('user_id', $user_id);
                        $update_result = $this->db->update("class_enrol", $feedback_data);
                    }
                    if ($key == 'CERTCOLDT') {
                        $certi_coll_date = (empty($value)) ? NULL : date('Y-m-d H:i:s', strtotime($value));
                        $enrol_data = array(
                            'certificate_coll_on' => $certi_coll_date,
                        );
                        $this->db->where('tenant_id', $this->user->tenant_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('class_id', $class_id);
                        $this->db->where('user_id', $user_id);
                        $update_result = $this->db->update("class_enrol", $enrol_data);
                    }
                }
            }
             
        }
        else
        {
            $cert_cll_date= $this->input->post('CERTCOLDT');
            $comments= $this->input->post('COMMNTS');
            $this->db->select('*');
            $this->db->from('trainer_feedback');
            $this->db->where('course_id', $course_id);
            $this->db->where('user_id', $user_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('feedback_question_id', 'CERTCOLDT');
            $sql = $this->db->get();
            if($sql->num_rows()>0)
            {
                $this->db->where('course_id', $course_id);
                $this->db->where('user_id', $user_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('feedback_question_id', 'CERTCOLDT');
                $this->db->delete('trainer_feedback');
            }
            $this->db->select('*');
            $this->db->from('trainer_feedback');
            $this->db->where('course_id', $course_id);
            $this->db->where('user_id', $user_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('feedback_question_id', 'COMMNTS');
            $sql1 = $this->db->get();
            if($sql1->num_rows()>0)
            {
                $this->db->where('course_id', $course_id);
                $this->db->where('user_id', $user_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('feedback_question_id', 'COMMNTS');
                $this->db->delete('trainer_feedback');
            }
            foreach ($this->input->post() as $key => $value) 
            {
                if($key=="COMMNTS" || $key=="CERTCOLDT")
                {
                    $data = array('tenant_id' => $this->user->tenant_id,
                        'course_id' => $course_id,
                        'class_id' => $class_id,
                        'user_id' => $user_id,
                        'feedback_question_id' => $key,
                        'feedback_answer' => strtoupper($value));
                        $insert_result = $this->db->insert("trainer_feedback", $data);
                }
            }
            /*$data = array(
                        'tenant_id'=>$this->user->tenant_id,
                        'course_id'=>$course_id,
                        'class_id'=>$class_id,
                        'user_id'=>$user_id,
                        'feedback_question_id'=>'CERTCOLDT',
                        'feedback_answer'=> $cert_cll_date
                        );
                $this->db->insert('trainer_feedback',$data);
                */
               $certi_coll_date = date('Y-m-d H:i:s', strtotime($cert_cll_date));
                        $enrol_data = array(
                            'certificate_coll_on' => $certi_coll_date,
                        );
                $this->db->where('tenant_id', $this->user->tenant_id);
                $this->db->where('course_id', $course_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('user_id', $user_id);
                $update_result = $this->db->update("class_enrol", $enrol_data);
            
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * function to get loc details
     * @param type $tenant_id
     * @param type $class
     * @param type $user
     * @return type
     */
    public function get_loc_details($tenant_id, $class, $user) {
        $result = $this->db->select('cc.class_name, c.crse_name, tup.first_name, tup.last_name, tup.gender,
                        cc.class_end_datetime, c.competency_code, tu.tax_code, tm.*')
                        ->from('class_enrol ce')
                        ->join('course_class cc', 'cc.class_id=ce.class_id')
                        ->join('course c', 'c.course_id=ce.course_id')
                        ->join('tms_users tu', 'tu.user_id=ce.user_id')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id')
                        ->where('ce.user_id', $user)->where('ce.class_id', $class)
                        ->where('ce.tenant_id', $tenant_id)
                        ->get()->row();
        return $result;
    }
    
      public function get_wsq_loc_details($tenant_id, $class_id, $user_id)
    {
        $this->db->select('cc.class_name,cc.classroom_trainer,cc.class_start_datetime,cc.class_end_datetime,c.crse_name,c.competency_code,'
                . 'c.crse_duration,c.reference_num, tup.first_name, tup.last_name,tu.tax_code,tm.*');
        $this->db->from('class_enrol ce');
        $this->db->join('course_class cc', 'cc.class_id=ce.class_id');
        $this->db->join('course c', 'c.course_id=ce.course_id');
        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');
        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
        
        $this->db->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id');
        $this->db->where('ce.user_id', $user_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.tenant_id', $tenant_id);
        $query = $this->db->get();
//        echo $this->db->last_query();
        return $query->row();
        
    }
    
   /**
     * this function to get trainer names
     */    
    public function get_trainer_details($trainer_id) 
    {        
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_id);
        if (!empty($tids)) 
        {
            $trainer_name = '';
            foreach ($tids as $tid) 
            {    
                $this->db->select('tu.user_id,tu.registered_email_id,tup.first_name,tup.contact_number');
                $this->db->from('tms_users tu');
                $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id and tup.tenant_id = tu.tenant_id');
                $this->db->join('internal_user_role iur','iur.user_id = tu.user_id  and iur.tenant_id = tu.tenant_id');
                $this->db->where('tup.tenant_id',$tenantId);
                $this->db->where('iur.role_id','TRAINER');
                $this->db->where('iur.user_id',$tid);
                $query = $this->db->get();
                $data = $query->row(0);
                $trainer = $data->first_name . ' ' . $data->last_name;

                $trainer_name .="$trainer,";
            }
            return rtrim($trainer_name, ',');
        }
    }


    /**
     * Get assessment date
     * @param type $class_id
     * @param type $user_id
     * @return type
     */
    public function get_assessment_date($class_id, $user_id) {
        $result = $this->db->select('assmnt_date')->from('class_assmnt_schld')
                ->where('class_id', $class_id)
                ->where('assmnt_type', 'DEFAULT')
                ->get();
        if ($result->num_rows() == 1) {
            return $result->row()->assmnt_date;
        } else {
            $result = $this->db->select('cas.assmnt_date')
                            ->from('class_assmnt_trainee cat')
                            ->join('class_assmnt_schld cas', 'cas.assmnt_id=cat.assmnt_id and cas.class_id=cat.class_id')
                            ->where('cat.class_id', $class_id)
                            ->where('cat.user_id', $user_id)
                            ->where('cas.assmnt_type', 'CUSTOM')->get()->row()->assmnt_date;
            return $result;
        }
    }
    /**
     * This method for getting full name of the user in export to excel full.
     * @param type $user_id
     * @return string|boolean
     */
    public function get_full_name($user_id = NULL) {
        if ($user_id == NULL) {
            return FALSE;
        } else {
            $this->db->select("first_name,last_name");
            $this->db->from("tms_users_pers");
            $this->db->where("user_id", $user_id);
            $result = $this->db->get()->row();
            $full_name = $result->first_name . " " . $result->last_name;
            return $full_name;
        }
    }
/**
 * This method for getting states based on country-- duplicated from internal user.
 * @param type $country_param
 * @return type
 */
    public function get_states($country_param) {
        $sql = $this->db->where('parameter_id', $country_param)->get('metadata_values')->row();
        if ($sql->child_category_id) {
            $query = $this->db->where('category_id', $sql->child_category_id)->order_by('category_name')->get('metadata_values');
            return $query->result();
        } else {
            $querys = $this->db->where('parameter_id', $country_param)->get('metadata_values');
            return $querys->result();
        }
    }

    /**
     * role based access for salesexec    
     */
    private function traineelist_querychange() {
        $this->db->join('class_enrol ce', 'ce.tenant_id = pers.tenant_id AND ce.user_id = pers.user_id');
        $this->db->where('ce.sales_executive_id', $this->user->user_id);
        $this->db->group_by('ce.user_id');
    }
   /**
    * role based access for trainer.
    */
    private function trainer_traineelist_querychange() {
        $this->db->join('course_class ccl', 'ccl.tenant_id = pers.tenant_id AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');
        $this->db->join('class_enrol ce', 'ce.tenant_id = pers.tenant_id AND ce.user_id = pers.user_id'
                . ' AND ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id');
        $this->db->group_by('ce.user_id');
    }
/**
 * role based access for crsemgr.
 */
    private function crsemgr_traineelist_querychange() {
        $this->db->join('course crse', 'crse.tenant_id = pers.tenant_id  AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)');
        $this->db->join('course_class ccl', 'ccl.tenant_id = crse.tenant_id AND ccl.course_id = crse.course_id');
        $this->db->join('class_enrol ce', 'ce.tenant_id = pers.tenant_id AND ce.user_id = pers.user_id'
                . ' AND ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id');
        $this->db->group_by('ce.user_id');
    }
/**
 * function to reactivate trainee
 * @param type $user_id
 * @param type $company_id
 * @param type $reactivate_reason
 * @param type $other_reason
 * @return boolean
 */
    public function reactivate_trainee($user_id = NULL, $company_id, $reactivate_reason, $other_reason) {
        if (empty($user_id)) {
            return FALSE;
        }
        $user = $this->user;
        $other_reason = ($reactivate_reason == 'OTHERS') ? $other_reason : '';
        $cur_date = date('Y-m-d H:i:s');
        $common_data = array(
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $cur_date,
            'reactivation_date_time' => $cur_date,
            'reactivated_by' => $user->user_id,
            'reactivation_reason_id' => $reactivate_reason,
            'reactivation_reason_others' => strtoupper($other_reason),
        );
        $tms_users_data = $common_data;
        $tms_users_data['account_status'] = 'ACTIVE';

        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('user_id', $user_id);

        
        $this->db->trans_start();
        $this->db->update('tms_users', $tms_users_data);
        if (!empty($company_id)) {
            $company_data = $common_data;
            $company_data['user_acct_status'] = 'ACTIVE';
            $this->db->where('user_id', $user_id);
            $this->db->where('tenant_id', $user->tenant_id);
            $this->db->where('company_id', $company_id);
            $this->db->update('tenant_company_users', $company_data);
        }
       
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
/**
 * This method for getting trainee info
 * @param type $user_id
 * @param type $tenant_id
 * @return type
 */
    public function trainee_detais($user_id, $tenant_id) {
        $this->db->select("pers.first_name,pers.last_name, pers.gender, coalesce(cusr.company_id,0) as company_id", FALSE);
        $this->db->from("tms_users_pers pers");
        $this->db->join("tenant_company_users cusr", "pers.user_id = cusr.user_id AND pers.tenant_id = cusr.tenant_id", "left");
        $this->db->where("pers.user_id", $user_id);
        $this->db->where("pers.tenant_id", $tenant_id);
        $result = $this->db->get();
        return $result->row();
    }

    /**
     * Update paymeny status
     * @param type $user_id
     * @param type $tenant_id
     * @return type
     */
    public function payment_status($user_id, $tenant_id) {
        $this->db->select("count(user_id) as count");
        $this->db->from("class_enrol");
        $this->db->where("user_id", $user_id);
        $this->db->where("tenant_id", $tenant_id);
        $this->db->where("payment_status", "NOTPAID");
        $result = $this->db->get();
        return $result->row('count');
    }

    /**
     * This methor used to get the traing historical data for a trainee.
     * @param type $tax_code
     * @return type
     */
    public function get_training_history($tax_code) {
        $this->db->select("th.*,cm.company_name");
        $this->db->from("tms_users_training_history th");
        $this->db->join("company_master cm", "cm.company_id = th.company_id", "left");
        $this->db->where("th.tax_code", $tax_code);
        $this->db->where("th.tenant_id", $this->user->tenant_id);
         $this->db->order_by("th.course_name");
        $result = $this->db->get();
        return $result->result();
    }
    /**
     * Get company details
     * @param type $user_id
     * @return type
     */
    public function get_company_details($user_id) 
    {
        $this->db->select('cm.company_name , cm.company_id');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('company_master cm', 'cm.company_id=tcu.company_id');
        $this->db->where('tcu.user_id', $user_id);
        $this->db->where("tcu.tenant_id", $this->user->tenant_id);
        $this->db->where("cm.company_status", 'ACTIVE');
        $this->db->where("tcu.user_acct_status", 'ACTIVE');
        return $this->db->get();
    }

    /**
     * Get company details across status
     * @param type $user_id
     * @return type
     */
    public function get_company_details_allstatus($user_id) {
        $this->db->select('cm.company_name, cm.company_id');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('company_master cm', 'cm.company_id=tcu.company_id');
        $this->db->where('tcu.user_id', $user_id);
        $this->db->where("tcu.tenant_id", $this->user->tenant_id);
        return $this->db->get();
    }

    /**
     * Get Trainee discount
     * @param type $tenant_id
     * @return boolean
     */
    public function update_traineediscount($tenant_id){
        $userid = $this->input->post('userid');
         /* activity log start skm */
        $individual = array();
        $individual ['individual_id'] =  $userid;
        $individual ['individual_discount'] = $this->trainee_discount($userid);
        if( $individual ['individual_discount']!=0){
        $previous_indv_discount = json_encode($individual);}
        /* skm end */
        $disc_perc = $this->input->post('disc_perc');
        $disc_amt = $this->input->post('disc_amt');
        $data=array();
        
        $this->db->trans_start();
        
        if ($this->user->role_id == 'CRSEMGR') {
            $delete_courses = array();
            foreach($disc_perc as $k=>$row){
                if(!empty($row) || !empty($disc_amt[$k])){
                    $delete_courses[]=$k;
                }
            }
            if(!empty($delete_courses)){
                $this->db->where('user_id', $userid);
                $this->db->where_in('course_id',$delete_courses);
                $this->db->delete('tms_users_discount');
            }
        }else{
            $this->db->where('user_id', $userid);
            $this->db->delete('tms_users_discount');            
        }
        
        foreach ($disc_perc as $k => $row) {
            if(!empty($row) || !empty($disc_amt[$k])){
                $data[] = array(
                    'tenant_id' => $tenant_id,
                    'user_id' => $userid,
                    'course_id' => $k,
                    'discount_percent' => $row,
                    'discount_amount' => $disc_amt[$k]
                );
            }
        }
        $this->db->insert_batch('tms_users_discount', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
             if( $individual ['individual_discount']!=0){
             user_activity(17,$userid,$previous_indv_discount,1);}
            return TRUE;
        }
    }
    /**
     * Function used for taxcode check without tenant id.
     * @param type $taxcode
     * @param type $user_id
     * @return type
     */
    public function check_taxcode_without_tenant_id($taxcode, $user_id='') {        
        $this->db->select('tenant_id, user_id');
        $this->db->from('tms_users');
        $this->db->where('tax_code', $taxcode);
        if (!empty($user_id)) {
            $this->db->where('user_id !=', $user_id);
        }
        $this->db->order_by("last_modified_on","DESC");
        $query = $this->db->get();
        return $query->row();
    }
    
    /** Method added for DMS */
    public function get_trainee_assessment_forms($trainee_id, $course_id, $class_id) {                                             
        
        $this->db->select("dms_crse_assmnt_trainee_template.template_id,"
                . "dms_crse_assmnt_trainee_template.active_version_id,dms_crse_assmnt_template.template_title");
        $this->db->from("dms_crse_assmnt_trainee_template");
        $this->db->join("dms_crse_assmnt_template","dms_crse_assmnt_template.template_id=dms_crse_assmnt_trainee_template.template_id","inner");
        $this->db->where("dms_crse_assmnt_trainee_template.trainee_id", $trainee_id);
        $this->db->where("dms_crse_assmnt_trainee_template.class_id", $class_id);
        $this->db->where("dms_crse_assmnt_trainee_template.course_id", $course_id);
        $this->db->where("dms_crse_assmnt_template.template_status", "ACTIVE");
   
        $result_set = $this->db->get();
                
        $forms = array(array());
        $count = 0;
       
        foreach ($result_set->result() as $row) {
    
            $forms[$count][0] = $row->template_id;
            $forms[$count][1] = $row->active_version_id;
            $forms[$count][2] = $row->template_title;
            
            $count+=1;
        }                       
               
        return $forms;
    }
    
      /*  this method get the username */
    public function get_username($user_id,$tenant_id)
    {
        $this->db->select('user_name');
        $this->db->from('tms_users');
        $this->db->where('user_id',$user_id);
        $this->db->where('tenant_id',$tenant_id);
        $sql = $this->db->get();
        return $sql->row();
    }
    
  /*  added by shubhranshu for client requirement on 21/03/2019 */
    public function check_nric_restriction($taxcode,$operation) {
        //$tenant_id=$this->user->tenant_id;
        $this->db->select('nric');
        $this->db->from('nric_restriction_list');
        $this->db->where('nric', $taxcode);
//        if ($user_id != '') {
//            $this->db->where('user_id !=', $user_id);
//        }
        //$this->db->where('tenant_id', $tenant_id);
        $row = $this->db->get()->num_rows();
        $data = array(
            'tenant_id' => $this->user->tenant_id,
            'enrolled_by_user_id' => $this->user->user_id,
            'role_id' => $this->user->role_id,
	    'operation' => $operation,
            'enrolled_by_user_name' => $this->user->user_name,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name ? $this->user->last_name : '',
            'nric_taxcode' => strtoupper($taxcode),
            'trigger_datetime' => date('Y-m-d H:i:s')
        );
        if($row > 0){
            $res = $this->db->insert('nric_restriction_logs',$data);
        }
        return $row;
    }/*  added by shubhranshu for client requirement on 21/03/2019 */
}