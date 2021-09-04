<?php

/*
 * This is the Model class for Internal Users
 */

class Activity_Log_Model extends CI_Model {
    /*
     * This method gets count for the internal user list for a tenant (Used in pagination)
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('bcrypt');
        $this->load->helper('common');
    }
    
    /* this function Select all data from activity log skm start */
//    public function get_activity_log_count_by_tenant_id($tenant_id)
//    {
//       // $this->db->select('count(*) as totalrows');
//        $this->db->select('*');
//        $this->db->from('activity_tracking');
//        $this->db->where('tenant_id', $tenant_id);
//        $query = $this->db->get();
//        return $query->num_rows;
//    }
//    
//    public function get_activity($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL)
//    {
//        $module = ($this->input->get('module')) ? $this->input->get('module') : '';
//        $user_id = ($this->input->get('user_id')) ? $this->input->get('user_id') : '';
//        $com_id = ($this->input->get('com_id')) ? $this->input->get('com_id') : '';
//        $invid = ($this->input->get('invid')) ? $this->input->get('invid') : '';
//        $crs = ($this->input->get('crs')) ? $this->input->get('crs') : '';
//        $cls_id = ($this->input->get('cls_id')) ? $this->input->get('cls_id') : '';
//        $cls_name = ($this->input->get('cls_name')) ? $this->input->get('cls_name') : '';
//        $account_type = ($this->input->get('account_type')) ? $this->input->get('account_type') : '';
//        $inv = ($this->input->get('inv')) ? $this->input->get('inv') : '';
// 
//        $this->db->select('*');
//        $this->db->from('activity_tracking at');
//
//        $this->db->join('tms_users_pers tup', 'tup.user_id = at.act_by');
//
//        $this->db->where('at.tenant_id',$tenant_id);
//
//
//            if ($module!=''){
//                 $this->db->where('at.module_id',$module);
//            }
//
//            if ($user_id!=''){
//                $this->db->where('at.act_on',$user_id);
//            }
//            else if($account_type!='')
//            {
//                $this->db->where('at.account_type',$account_type);
//            }
//            else if ($com_id !=''){
//                $this->db->where('at.act_on',$com_id);
//            }
//            else if ($invid !=''){
//                $this->db->where('at.act_on',$invid);
//            }
//            else if($crs!='' && $cls_name!='')
//            {
//                $this->db->where('at.act_on',$cls_name);
//            }
//            else if($crs!='' && $cls_name!='')
//            {
//                $this->db->where('at.act_on',$cls_name);
//            }
//            else if($inv!='')
//            {
//                $this->db->where('at.act_on',$inv);
//            }
//           
//
//            else if ($course_options !=''){
//                $this->db->where('at.act_on',$course_options);
//            }
//            
//            if ($sort_by) {
//                $this->db->order_by($sort_by, $sort_order);
//            } else {
//                $this->db->order_by('at.trigger_datetime', 'DESC');
//            }
//            if ($limit == $offset) {
//                $this->db->limit($offset);
//            } 
//            else if ($limit > 0) {
//                $limitvalue = $offset - $limit;
//                $this->db->limit($limit, $limitvalue);
//            }
//            $query = $this->db->get();
////            echo"<br/>";echo"<br/>";echo"<br/>";
//            echo $this->db->last_query();
//            return $query->result_array();
//
//    }
    
    /* this function Select all data from activity log skm start */
    public function get_activity_log_count_by_tenant_id($tenant_id, $module = '', $user_id = '', $com_id = '', $invid='', $inv_taxcode = '', $crs = '', $cls_id = '', $cls_name = '', $account_type = '',$pass = '') 
    {
        
        $this->db->select('*');
        $this->db->from('activity_tracking at');

        $this->db->join('tms_users_pers tup', 'tup.user_id = at.act_by');

        $this->db->where('at.tenant_id',$tenant_id);


            if ($module!=''){ // when search according to module
                 $this->db->where('at.module_id',$module);
            }

            if ($user_id!=''){ // when search for internal staff
                $this->db->where('at.act_on',$user_id);
            }
            else if($account_type!='' && $invid == '' && $com_id == '') // when we search only with account type
            {
                $this->db->where('at.account_type',$account_type);
            }
             else if ($com_id !='' && $account_type !=''){ // when we search with account type and company 
                 $this->db->where('at.account_type',$account_type);
                 $this->db->where('at.act_on',$com_id);
            }
            else if ($com_id !=''){ // when we search only for company
                $this->db->where('at.act_on',$com_id);
            }
            else if (!empty($account_type) && !empty($invid)){ // when we search with account type and individual id
                $this->db->where('at.account_type',$account_type);
                $this->db->where('at.act_on',$invid);
            }
             else if ($invid !=''){ // when we search only for individual
                $this->db->where('at.act_on',$invid);
            }
            else if($crs!='' && $cls_name!=''){ // when search for class based on course
                $this->db->where('at.act_on',$cls_name);
            }
            else if($pass!=''){ // when search for reset password
                $this->db->where('at.act_on',$pass);
            }
            $query = $this->db->get();
//            echo $this->db->last_query();
            return $query->num_rows();

    }
    
//    public function get_activity($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL)
    public function get_activity_list_by_tenant_id($tenant_id, $limit = NULL, $offset = NULL, $sort_by = 'at.trigger_datetime', $sort_order = 'DESC', $module = '', $user_id = '', $com_id = '', $invid = '', $inv_taxcode = '', $crs = '', $cls_id = '', $cls_name = '', $account_type = '',$pass = '',$crse_id='') 
    {
        $this->db->select('*');
        $this->db->from('activity_tracking at');

        $this->db->join('tms_users_pers tup', 'tup.user_id = at.act_by');

        $this->db->where('at.tenant_id',$tenant_id);


            if ($module!=''){ // when search according to module
                 $this->db->where('at.module_id',$module);
            }

            if ($user_id!=''){ // when search for internal staff
                $this->db->where('at.act_on',$user_id);
            }
            else if($account_type!='' && $invid == '' && $com_id == '') // when we search only with account type
            {
                $this->db->where('at.account_type',$account_type);
            }
             else if ($com_id !='' && $account_type !=''){ // when we search with account type and company 
                 $this->db->where('at.account_type',$account_type);
                 $this->db->where('at.act_on',$com_id);
            }
            else if ($com_id !=''){ // when we search only for company
                $this->db->where('at.act_on',$com_id);
            }
            else if (!empty($account_type) && !empty($invid)){ // when we search with account type and individual id
                $this->db->where('at.account_type',$account_type);
                $this->db->where('at.act_on',$invid);
            }
             else if ($invid !=''){ // when we search only for individual
                $this->db->where('at.act_on',$invid);
            }
            else if($crs!='' && $cls_name!=''){ // when search for class based on course
                $this->db->where('at.act_on',$cls_name);
            }
            else if($pass!=''){ // when search for reset password
                $this->db->where('at.act_on',$pass);
            }else if($crse_id !=''){
                $this->db->where('at.act_on',$crse_id);
            }
            
            
            

            if ($sort_by) {
                $this->db->order_by($sort_by, $sort_order);
            } else {
                $this->db->order_by('at.trigger_datetime', 'DESC');
            }
            if ($limit == $offset) {
                $this->db->limit($offset);
            } 
            else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
            $query = $this->db->get();
            //echo $this->db->last_query();exit;
            return $query->result_array();

    }
    
    
    public function get_module()
    {
        $this->db->select('*');
        $this->db->from('activity_module_id');
        $sql = $this->db->get();
        return $sql->result_array();
    }
    
    public function get_users($tenant_id,$acc_type)
    {
        $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
        $this->db->where('tu.account_type',$acc_type);
        $this->db->where('tu.tenant_id',$tenant_id);
        $sql= $this->db->get();
        return $sql->result_array();
    }
    
    public function get_company_list($tenant_id)
    {
        $this->db->select('cm.*');
        $this->db->from('company_master cm');
        $this->db->join('tenant_company tc','tc.company_id = cm.company_id');
        $this->db->where('cm.company_status','ACTIVE');
        $this->db->where('tc.tenant_id',$tenant_id);
        $sql = $this->db->get();
        return $sql->result_array();
        
//      $this->db->select('cm.company_id, cm.company_name');
//      $this->db->from('company_master cm');
//      $this->db->join('tenant_company tc', "cm.company_id = tc.company_id and tc.comp_status='ACTIVE'");  
//      $this->db->where('tc.tenant_id', $tenant_id);
//      $this->db->order_by('cm.company_name');
//      $query = $this->db->get();
//      return $query->result_array();
        
        
    }
    
    public function get_course_list($tenant_id)
    {
        $this->db->select('*');
        $this->db->from('course');
        $this->db->where('tenant_id',$tenant_id);
//        $this->db->where('crse_status','ACTIVE');
        $sql = $this->db->get();
        return $sql->result_array();
    }
    
    // This function retrive specific record from activity table skm start
    public function get_activity_details($id,$module,$act_on)
    {
        $this->db->select('*');
        $this->db->from('activity_tracking');
        $this->db->where('id',$id);
        $this->db->where('module_id',$module);
        $this->db->where('act_on',$act_on);
        //return $this->db->get()->row();
        $sql = $this->db->get();
//        echo $this->db->last_query();
        return $sql->row();
        
    }
    
    /* this function get activity on name skm start */
    public function get_act_name($module_id,$act_on,$account_type=null)
    {    
        if($module_id == 1 || $module_id == 3 || $module_id == 11 || $module_id == 12 || $module_id == 13 || $module_id == 18){
            return $res = $this->user_details($act_on);
        }else if($module_id == 2 || $module_id == 17 && $account_type == 2){
            return $res = $this->company_name($act_on);
        }else if($module_id == 4){
            return $res = $this->course_name($act_on);
        }else if($module_id == 5 || $module_id == 6){
            return $res = $this->class_name($act_on);
        }elseif($module_id == 10 && $account_type==1 || $module_id == 17 && $account_type == 1){
            return $res = $this->trainee_name($act_on);
        }elseif($module_id == 10 && $account_type==2){
            return $res = $this->company_name($act_on);
        } else if($module_id == 16){
            return $res = $this->user_password_details($act_on);
        }
    }
    
    public function user_password_details($act_on){
        $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
        $this->db->where('tu.user_name',$act_on);
       return $this->db->get()->row();
        
    }
    
     public function user_details($act_on)
    { 
        $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
        $this->db->where('tu.user_id',$act_on);
       return $this->db->get()->row();
        //echo $this->db->last_query();
    }
    
    public function course_name($act_on)
    {
        $this->db->select('*');
        $this->db->from('course');
        $this->db->where('course_id',$act_on);
        return $this->db->get()->row();
    }
    
    public function company_name($act_on)
    {
        $this->db->select('*');
        $this->db->from('company_master');
        $this->db->where('company_id',$act_on);
        return $this->db->get()->row();
    }
    
    public function trainee_name($act_on)
    {
        $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
        $this->db->where('tu.user_id',$act_on);
        return $this->db->get()->row();
    }
    
    public function class_name($act_on)
    {
        $this->db->select('*');
        $this->db->from('course_class');
        $this->db->where('class_id',$act_on);
        return $this->db->get()->row();
    }
    
    public function get_personal_details($user_id,$tenant_id)
    {
        $this->db->select('first_name,gender,dob,alternate_email_id,contact_number,alternate_contact_number');
        $this->db->from('tms_users_pers');
        $this->db->where('user_id',$user_id);
        $this->db->where('tenant_id',$tenant_id);
        return $this->db->get()->row();
    }
    
    
    public function internalstaff_name_list_autocomplete($query = NULL) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
        $this->db->where('tu.account_tyPE','INTUSR');
        $this->db->where('tup.tenant_id',$tenant_id);
        $this->db->like('tup.first_name',$string);
        $sql = $this->db->get();
        foreach($sql->result() as $row)
        {
             $matches[] = array(
                                'label' => $row->first_name ,
                                'key' => $row->user_id
                                );
        }
        
        
        return $matches;
    }
    
    public function company_name_list_autocomplete($query = NULL) {
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('company_master');
        $this->db->like('company_name',$string);
        $sql = $this->db->get();
        foreach($sql->result() as $row)
        {
             $matches[] = array(
                                'label' => $row->company_name,
                                'key' => $row->company_id
                                );
        }
        
        
        return $matches;
    }
    
    public function course_name_list_autocomplete($query = NULL) {
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('course');
        $this->db->like('crse_name',$string);
        $sql = $this->db->get();
        foreach($sql->result() as $row)
        {
             $matches[] = array(
                                'label' => $row->crse_name,
                                'key' => $row->course_id
                                );
        }
        
        
        return $matches;
    }
    
    public function get_course_name($course_id){
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $this->db->select('crse_name');
        $this->db->from('course');
        $this->db->where('course_id',$course_id);
        $this->db->where('tenant_id',$tenant_id);
        return $this->db->get()->row();
    }
    
    /**
     * function to get classroom location for others
     */
    public function get_classroom_location($venue, $other) {
        if ($venue == 'OTH') {
            return 'Others (' . $other . ')';
        } else {
            $this->load->model('course_model');
            return $this->course_model->get_metadata_on_parameter_id($venue);
        }
    }
    
//    public function get_user_course_name($course_id,$userid,$tenant_id){
//        $this->db->select('crse_name');
//        $this->db->from('tms_users_discount');
//        $this->db->where('course_id',$course_id);
//        $this->db->where('user_id',$userid);
//        $this->db->where('tenant_id',$tenant_id);
//        return $this->db->get()->row();
//    }
    
    public function get_company_details($user_id,$tenant_id) 
    {
        $this->db->select('cm.company_name , cm.company_id');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('company_master cm', 'cm.company_id=tcu.company_id');
        $this->db->where('tcu.user_id', $user_id);
        $this->db->where("tcu.tenant_id", $tenant_id);
//        $this->db->where("cm.company_status", 'ACTIVE');
//        $this->db->where("tcu.user_acct_status", 'ACTIVE');
        return $this->db->get()->row_array();
    }
    
    public function invtaxcode_list_autocomplete($query = NULL) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id');
        $this->db->where('tup.tenant_id',$tenant_id);
        $this->db->where('tu.account_type','TRAINE');
        //$this->db->like('tup.first_name',$string);
         $this->db->like('tu.tax_code',$string);
        $sql = $this->db->get();
//        echo $this->db->last_query();
        foreach($sql->result() as $row)
        {
             $matches[] = array(
                                'label' =>$row->first_name.'('.$row->tax_code.')',
                                'key' => $row->user_id
                                );
        }
        
        
        return $matches;
    }
    
      public function password_list_autocomplete($query = NULL){
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('activity_tracking');
        $this->db->where('tenant_id',$tenant_id);
        $this->db->where('module_id',16);
        $this->db->like('act_on',$string);
        $this->db->group_by('act_on');
        $sql = $this->db->get();
        //echo $this->db->last_query();
        foreach($sql->result() as $row){
            $matches[] = array(
                                'label' =>$row->act_on,
                                'key' => $row->act_on
                                );
        }
         return $matches;
        
    }
    
    public function inv_list_autocomplete($query = NULL){
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('activity_tracking');
        $this->db->where('tenant_id',$tenant_id);
        $this->db->where('module_id',14);
        $this->db->like('act_on',$string);
        $this->db->group_by('act_on');
        $sql = $this->db->get();
        //echo $this->db->last_query();
        foreach($sql->result() as $row){
            $matches[] = array(
                                'label' =>$row->act_on,
                                'key' => $row->act_on
                                );
        }
         return $matches;
        
    }
    
    
    public function course_class_list_autocomplete($query = NULL) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $string =  TRIM($query);
        $this->db->select('*');
        $this->db->from('course c');
        $this->db->join('course_class cc','cc.course_id = c.course_id');
        $this->db->where('c.tenant_id',$tenant_id);
        
        $this->db->like('cc.class_name',$string);
        $sql = $this->db->get();
//        echo $this->db->last_query();
//        exit();
        foreach($sql->result() as $row)
        {
             $matches[] = array(
                                'label' => $row->class_name,
                                'key' => $row->class_id
                                );
        }
        
        
        return $matches;
    }
    public function get_course_class($tenantId, $courseId) {
        $this->db->select('class_id,class_name');
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenantId);
        $this->db->where('course_id', $courseId);
       
        $this->db->order_by("DATE(class_start_datetime)", "DESC"); // added for class start date based sorting on Nov 24 2014.
        $query = $this->db->get();    
        $result = array();
        foreach ($query->result() as $row) {
            $result[$row->class_id] = $row->class_name;
        }
        return $result;
    }
    
    
    
    
    
}