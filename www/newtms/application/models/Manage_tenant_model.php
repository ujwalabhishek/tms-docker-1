<?php

/*
 * This is the Model class for Manage Tenants
 */

class Manage_Tenant_Model extends CI_Model {
    ///// added by shubhranshu to show all tenants on the landing page///////////////
    public $dbs;
    
    public function list_all_tenants_for_landing_page() {
        //$this->load->helper('db_dynamic_helper');
        //$config_app = switch_db_dynamic('biipmico_tms_masterdata');
        //$this->dbs = $this->load->database($config_app,TRUE);
        $this->db->select('*');
        $this->db->from('tenant_master');
        $this->db->where('tenant_id !=','T01');
        return $this->db->get()->result_array();
    }

   /**
    * This function to get all the tenants
    * @param type $tenant_id
    * @param type $limit
    * @param type $offset
    * @param type $sort_by
    * @param type $sort_order
    * @return type
    */
    
    public function list_all_tenants($tenant_id = 0, $limit = NULL, $offset = NULL, $sort_by = 'account_created_on', $sort_order = 'DESC') {
        $this->db->select('tenant_name, tenant_address, tenant_city, tenant_state, tenant_country,
                tenant_email_id, tenant_contact_num, account_status, tenant_id');
        $this->db->from('tenant_master');
        $this->db->order_by($sort_by, $sort_order);
        $this->db->where('tenant_id <>', 'ISV01');
        $this->db->where('tenant_id <>', 'T01');
        if (!empty($tenant_id)) {
            $this->db->where('tenant_id', $tenant_id);
        }
        if (!empty($offset)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        return $this->db->get();
    }
    
    ///added by shubhranshu for blocked nric
    public function list_blocked_nric($tenant_id = 0, $limit = NULL, $offset = NULL, $sort_by = 'id', $sort_order = 'DESC') {
        $this->db->select('*');
        $this->db->from('nric_restriction_list');
        $this->db->order_by($sort_by, $sort_order);
//        $this->db->where('tenant_id <>', 'ISV01');
//        $this->db->where('tenant_id <>', 'T01');
//        if (!empty($tenant_id)) {
//            $this->db->where('tenant_id', $tenant_id);
//        }
        if (!empty($offset)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }//$this->db->get();
        //echo $this->db->last_query();exit;
        return $this->db->get();
    }
/**
 * This function to get the tenant details
 * @param type $tenant_id
 * @return type
 */
    public function get_tenant_details($tenant_id) {
        $this->db->select('*')->from('tenant_master')->where('tenant_id <>', 'ISV01')->where('tenant_id', $tenant_id);
        return $this->db->get();
    }
   /**
     * This function to create new tenant
     */
    public function create_tenant() {
        $this->load->helper('common');
        $this->load->library('bcrypt'); 

        $tenant_id = get_max_lookup(TENANT_ID);
        $acc_acti_end_date = ($this->input->post('acti_end_date')) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('acti_end_date')))) : NULL;
        $cur_date = date('Y-m-d H:i:s');
        
        $this->input->post('tenant_name');
        $short_name = explode(' ',$this->input->post('tenant_name'));
        $tenant_short_name = strtolower($short_name[0]);
        
        $data = array(
            'tenant_id' => 'T' . $tenant_id,
            'tenant_name' => strtoupper($this->input->post('tenant_name')),
            'tenant_address' => strtoupper($this->input->post('address')),
            'tenant_city' => strtoupper($this->input->post('city')),
            'tenant_state' => $this->input->post('state'),
            'tenant_country' => $this->input->post('country'),
            'tenant_email_id' => $this->input->post('email'),
            'tenant_contact_num' => $this->input->post('contact_num'),
            'account_created_on' => $cur_date,
            'account_activation_start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('acti_start_date'))),
            'account_activation_end_date' => $acc_acti_end_date,
            'account_status' => 'ACTIVE',
            'Logo' => $this->input->post('logo'),
            'CopyRightText' => $this->input->post('copyright'),
            'ApplicationName' => 'tms1.png',
            'Currency' => $this->input->post('currency'),
            'Country' => $this->input->post('country_use'),
            'paypal_email_id' => $this->input->post('paypal_email'),
            'invoice_name' => $this->input->post('inv_name'),
            'invoice_footer_text' => $this->input->post('inv_footer'),
            'website_url' => $this->input->post('website'),
            'comp_reg_no' => strtoupper($this->input->post('company_no')),
            'gst_reg_no' => strtoupper($this->input->post('gst_no')),
            'director_name' => strtoupper($this->input->post('director_name')),
            'contact_name' => strtoupper($this->input->post('contact_name')),
            'tenant_short_name'=> $tenant_short_name
        );
        $password = random_key_generation();
        $encrypted_password = $this->bcrypt->hash_password($password);
        $user_data = array(
            'tenant_id' => 'T' . $tenant_id,
            'account_type' => 'INTUSR',
            'registration_mode' => 'INTUSR',
            'registration_date' => $cur_date,
            'user_name' => $tenant_id,
            'password' => $encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'registered_email_id' => $this->input->post('email'),
            'country_of_residence' => $this->input->post('country_use'),
            'tax_code_type' => 'SNG_3',
            'tax_code' => '',
            'other_identi_type' => 'NOTAXCODE',
            'other_identi_code' => '',
            'acct_acti_date_time' => $cur_date,
            'account_status' => 'ACTIVE',
            'created_by' => '1',
            'created_on' => $cur_date,
            'last_modified_by' => '1',
            'last_modified_on' => $cur_date,
        );
        $this->db->trans_start();
        $this->db->insert('tenant_master', $data);
        $tenant_org_id = $this->db->insert_id();//sk1
         
        $this->db->insert('tms_users', $user_data);
        $user_id = $this->db->insert_id();
        $user_name = 'admin' . $user_id;
        $data = array(
            'user_name' => $user_name,
            'tax_code' => $user_id,
            'other_identi_code' => $user_id,
            'tenant_org_id'=> $tenant_org_id
        );
        $this->db->where("user_id", $user_id);
        $this->db->update("tms_users", $data);
        $first_name = strtoupper($this->input->post('tenant_name') . ' Administrator');
        $pers_data = array(
            'tenant_id' => 'T' . $tenant_id,
            'user_id' => $user_id,
            'first_name' => $first_name,
            'last_name' => '',
            'contact_number' => $this->input->post('contact_num'),
        );
        $this->db->insert('tms_users_pers', $pers_data);
        $role_data = array(
            'tenant_id' => 'T' . $tenant_id,
            'user_id' => $user_id,
            'role_id' => 'ADMN'
        );
        $this->db->insert('internal_user_role', $role_data);
        $this->insert_role_access('T' . $tenant_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        $user_details = array(
            'username' => $user_name,
            'email' => $this->input->post('email'),
            'password' => $password,
            'firstname' => $first_name,
            'tenantname' => $this->input->post('tenant_name')
        );
        $this->tenant_send_mail($user_details);
        return TRUE;
    }
/**
 * This function to update tenant_master
 * @param type $data
 * @param type $where
 * @return boolean
 */
    public function update_tenant_master($data, $where) {
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update('tenant_master', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
/**
 * This function to get all tenant names
 * @param type $tenant_name
 * @param type $active
 * @return type
 */
    public function get_alltenant($tenant_name, $active = '') {
        $this->db->select('tenant_name, tenant_id')->from('tenant_master');
        $this->db->where('tenant_id <>', 'ISV01');
        $this->db->where('tenant_id <>', 'T01');
        if ($active == 'active') {
            $this->db->where('account_status', 'ACTIVE');
        }
        if (!empty($tenant_name)) {
            $this->db->like('tenant_name', $tenant_name, 'both');
        }
        return $this->db->get()->result();
    }
    /**
     * Get states
     * @param type $country
     * @return type
     */
    public function get_states($country) {
        $sql = $this->db->where('parameter_id', $country)->get('metadata_values')->row();
        if ($sql->child_category_id) {
            $query = $this->db->where('category_id', $sql->child_category_id)->order_by('category_name')->get('metadata_values');
            return $query->result();
        } else {
            $querys = $this->db->where('parameter_id', $country)->get('metadata_values');
            return $querys->result();
        }
    }
    /**
     * Check email 
     * @param type $email
     * @param type $tenant_id
     * @return type
     */
    public function check_email($email, $tenant_id) {
        $this->db->select('tenant_id');
        $this->db->from('tenant_master');
        $this->db->where('tenant_email_id', $email);
        if (!empty($tenant_id)) {
            $this->db->where('tenant_id !=', $tenant_id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    /**
     * Check tenant name
     * @param type $name
     * @param type $tenant_id
     * @return type
     */
    public function check_tenant_name($name, $tenant_id) {
        $this->db->select('tenant_id');
        $this->db->from('tenant_master');
        $this->db->where('tenant_name', $name);
        if (!empty($tenant_id)) {
            $this->db->where('tenant_id !=', $tenant_id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    /**
     * This function to add role access
     * @param type $tenant_id
     */
    public function insert_role_access($tenant_id) {
        $cur_date = date('Y-m-d H:i:s');
        $this->db->query("INSERT INTO role_features (tenant_id, role_id, feature_id, feature_access_scope_id)
            SELECT '$tenant_id', role_id, feature_id, feature_access_scope_id
            FROM role_features WHERE tenant_id='T01'");
        $this->db->query("INSERT INTO tms_roles (tenant_id, role_id, role_name, role_description, role_status, created_by, created_on)
            SELECT '$tenant_id', role_id, role_name, role_description, role_status, 'ISV01', '$cur_date'
            FROM tms_roles WHERE tenant_id='T01'");
        $this->db->query("INSERT INTO role_features_access_rights (tenant_id, role_id, feature_id, access_right_id)
            SELECT '$tenant_id', role_id, feature_id, access_right_id
            FROM role_features_access_rights WHERE tenant_id='T01'");
    }
/**
 * This function to send tenant_mail
 * @param type $user
 * @return boolean
 */
    public function tenant_send_mail($user) {
        if ($user['username'] && $user['password'] && $user['email'] && $user['tenantname']) {
            $footer_data = str_replace("<Tenant_Company_Name>", $user['tenantname'], MAIL_FOOTER);
            $footer_data=str_replace("<Tenant_Company_Email>", $user['email'], $footer_data);
            $subject = 'Your Account Creation Acknowledgment';
            $body = "Dear " . $user['firstname'] . ',';
            $body .= '<br/><br/>Thank you for registering with us. Your account has been successfully created.<br/><br/>';
            $body .= "<strong>Your User Name:</strong> " . $user['username'] . "<br/>";
            $body .= "<strong>Your Password:</strong> " . $user['password'] . "<br/><br/>";
            $body .= "You may log in at <a href='" . base_url() . "'>".  base_url() . "</a><br/><br/>";
            $body .= $footer_data;
            return send_mail($user['email'], '', $subject, $body);
        }

        return FALSE;
    }
/**
 * This function to get all main meta categories
 * @param type $category
 * @return type
 */
    public function get_meta_categories($category = '') {
        $this->db->from('metadata_values');
        if (empty($category)) {
            $this->db->where('child_category_id IS NOT NULL', null, false);
            $this->db->where('(parameter_id IS NULL OR parameter_id="")', null, false);
        } else {
            $this->db->where('category_id', $category);
        }
        $this->db->order_by('category_name');
        return $this->db->get();
    }
 /**
  *  This function to create metadata
  * @param type $data
  * @param type $child_cat
  * @param type $category
  * @param type $sub_category
  * @return boolean
  */
    public function create_metadata($data, $child_cat, $category, $sub_category) {
        if (!empty($sub_category) || !empty($category)) {
            $data['category_id'] = (!empty($sub_category)) ? $sub_category : $category;
            $max_id = ($child_cat) ? '_' . $this->max_metadata($data['category_id']) : 0;
        } else {
            $this->load->helper('common');
            $cat_id = get_max_lookup(METADATA);
            $data['category_id'] = 'CAT' . $cat_id;
            $max_id = '_01';
        }

        if ($child_cat) {
            $sub_cat = $data['child_category'];
            unset($data['child_category']);
            $sub_cat['category_id'] = $data['child_category_id'] = $data['category_id'] . $max_id;
        }
        $this->db->trans_start();
        $this->db->insert('metadata_values', $data);
        if ($sub_cat) {
            $this->db->insert('metadata_values', $sub_cat);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
 /**
  * This function to get max metadata child category id
  * @param type $child_cat
  * @return type
  */
    public function max_metadata($child_cat) {
        $query = $this->db->query("SELECT SUBSTRING(child_category_id,LENGTH('" . $child_cat . "__'),3) as max_id
        FROM metadata_values 
        WHERE category_id = '$child_cat'
        order by cast(SUBSTRING(child_category_id,LENGTH('" . $child_cat . "__'),3) as SIGNED) DESC limit 0,1");
        $max_id = $query->row('max_id');
        return sprintf("%02s", ++$max_id);
    }
/**
 * This function to update metadata
 * @param type $data
 * @param type $where
 * @return boolean
 */
    public function update_metadata($data, $where) {
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update('metadata_values', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
/*  added by shubhranshu for client requirement on 25/03/2019 for restriction nric*/  
    public function update_blocked_nric($data, $where) {
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update('nric_restriction_list', $data);
        //echo $this->db->last_query();exit;
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
    /*  added by shubhranshu for client requirement on 25/03/2019 for restriction nric*/ 
    public function save_blocked_nric($data) {
       $status=$this->db->insert('nric_restriction_list', $data);
       return $status;
    }
     /*  added by shubhranshu for client requirement on 25/03/2019 for exist of nric in the blocked list*/ 
    public function exist_blocked_nric($data){
        $this->db->select('nric');
        $this->db->from('nric_restriction_list');
        $this->db->where('nric',$data['nric']);
        return $this->db->get(); 
    }
/**
 * This function to check param id exist
 * @param type $cat
 * @param type $param
 * @return type
 */
    public function unique_param_check($cat, $param) {
        $this->db->select('parameter_id')->from('metadata_values')
                ->where('category_id', $cat)->where('parameter_id', $param);
        return $this->db->get();
    }
    
     /* skm */
    public function get_all_tenant(){
        $this->db->select('tenant_id,tenant_name');
        $this->db->from('tenant_master');
        $query = $this->db->get();
        return $query;
    }


    public function get_all_records(){
        $this->db->select('COUNT(ce.user_id) as total,ce.tenant_id,tm.tenant_name');
        $this->db->from('class_enrol ce');
        $this->db->join('tenant_master tm','tm.tenant_id = ce.tenant_id');
        $this->db->group_by('tenant_id');
        $query = $this->db->get();
        return $query;
    }
    
    public function get_monthly_tenant_enrollment_count($tenant_id,$fyear,$fmonth,$lyear,$lmonth){     

        if($fmonth!='' && $fyear!='' && $lmonth=='' && $lyear =='')
        {
            $this->db->select('MONTHNAME(ce.enrolled_on) AS month,YEAR(ce.enrolled_on) AS year,tm.tenant_name, count(*) AS count');
            $this->db->from('class_enrol ce');
            $this->db->join('tenant_master tm','tm.tenant_id = ce.tenant_id');
            $this->db->like('ce.enrolled_on',$fyear.'-'.$fmonth.'-','after'); 
            $this->db->where('ce.tenant_id',$tenant_id);
            $this->db->group_by(array('MONTH(ce.enrolled_on)','ce.tenant_id'));
            $this->db->order_by('MONTH(ce.enrolled_on)','DESC');
            $qry = $this->db->get();
            return $qry;
        }
        
        if($fmonth!='' && $fyear!='' && $lmonth!='' && $lyear!='')
        {
            $start_month_date = $fyear.'-'.$fmonth.'-01';            
            $last_month_date = $lyear.'-'.$lmonth.'-01';
            $last_month_date = date("Y-m-t", strtotime($last_month_date));

              
//            $qry = "SELECT MONTHNAME(enrolled_on) AS month, YEAR(enrolled_on) AS year count(*) AS count FROM (`class_enrol`) WHERE `tenant_id` = '".$tenant_id."' AND
//DATE(enrolled_on)>= '".$start_month_date."' and DATE(enrolled_on) <= '".$last_month_date."' 
//GROUP BY MONTH(enrolled_on), YEAR(enrolled_on) ORDER BY MONTH(enrolled_on) DESC";
            
//            $qry = "SELECT MONTHNAME(enrolled_on) AS month,YEAR(enrolled_on) AS year, count(*) AS count FROM (`class_enrol`)"
//                    . " WHERE `tenant_id` = '".$tenant_id."' AND DATE(enrolled_on)>= '".$start_month_date."' and DATE(enrolled_on) <= '".$last_month_date."' "
//                    . "GROUP BY MONTH(enrolled_on),YEAR(enrolled_on) ORDER BY YEAR(enrolled_on) DESC";
              
            $qry = "SELECT tm.tenant_name,MONTHNAME(ce.enrolled_on) AS month,YEAR(ce.enrolled_on) AS year,DATE_FORMAT(ce.enrolled_on,'%Y%m') AS date, count(*) AS count FROM class_enrol ce"
                    . " LEFT JOIN tenant_master tm on tm.tenant_id = ce.tenant_id "
                    . " WHERE ce.tenant_id = '".$tenant_id."' AND DATE(ce.enrolled_on)>= '".$start_month_date."' and DATE(ce.enrolled_on) <= '".$last_month_date."'"
                    . " GROUP BY MONTH(ce.enrolled_on),YEAR(ce.enrolled_on) ORDER BY MONTH(ce.enrolled_on)  DESC";
    
    return $this->db->query($qry);
         
        }
        
       
    }
    //added by shubhranshu for client requirement on 21/03/2019
    public function get_privilage(){
        $this->db->select('max_id');
        $this->db->from('index_lookup');
        $this->db->where('table_name','privilage_all');
        $res = $this->db->get()->row()->max_id;
        //echo $res;exit;
        return $res;
    }
    //added by shubhranshu for client requirement on 21/03/2019
    public function update_privilage($status){
        $data = array(
                 'max_id'=> $status
         );
        $this->db->where('table_name','privilage_all');
        $this->db->update('index_lookup',$data);
        //echo $this->db->last_query();exit;
        return true;
    }
    
     ///added by shubhranshu for blocked nric logs
    public function list_blocked_nric_logs($tenant_id = 0, $limit = NULL, $offset = NULL, $sort_by = 'id', $sort_order = 'DESC') {
        $this->db->select('nrl.operation,tup.first_name,tup.last_name,nrl.id,nrl.tenant_id,nrl.enrolled_by_user_id,nrl.role_id,nrl.enrolled_by_user_name,nrl.nric_taxcode,nrl.trigger_datetime');
        $this->db->from('nric_restriction_logs nrl');
        $this->db->join('tms_users tu','tu.tax_code=nrl.nric_taxcode AND tu.tenant_id=nrl.tenant_id', 'left');
        $this->db->join('tms_users_pers tup','tup.user_id=tu.user_id','left');
        $this->db->order_by($sort_by, $sort_order);
        
//        $this->db->where('tenant_id <>', 'ISV01');
//        $this->db->where('tenant_id <>', 'T01');
        if (!empty($tenant_id)) {
            $this->db->where('nrl.tenant_id', $tenant_id);
        }else{
            $this->db->where('nrl.tenant_id', TENANT_ID);
        }
        if (!empty($offset)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }//$this->db->get();
        //echo $this->db->last_query();exit;
        return $this->db->get();
    }
    ///added by shubhranshu for blocked nric for company enrol
    public function fetch_nric_code($s){
        $this->db->select('tax_code');
        $this->db->from('tms_users');
        $this->db->where('user_id',$s);
        $taxcode = $this->db->get()->row()->tax_code;
        return $taxcode;
    }

}

?>
