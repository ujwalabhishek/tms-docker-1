<?php

/*
 * This is the Model class for Internal Users
 */

class Internal_User_Model extends CI_Model {
    /*
     * This method gets count for the internal user list for a tenant (Used in pagination)
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('bcrypt');
        $this->load->helper('common');
    }
    /**
     * Get internal user count
     * @param type $tenant_id
     * @return int
     */
    public function get_internal_user_count_by_tenant_id($tenant_id) {
        if (empty($tenant_id)) {
            return 0;
        }
        $search_radio = $this->input->get('search_radio');
         $user_role = ($this->input->get('user_role')) ? $this->input->get('user_role') : '';
        $first_last_name = ($this->input->get('first_last_name')) ? $this->input->get('first_last_name') : '';
        $filter_status = ($this->input->get('filter_status')) ? $this->input->get('filter_status') : '';
       $this->db->select('count(*) as totalrows');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('internal_user_role irole', 'usr.user_id = irole.user_id');
        $this->db->join('tms_roles role', 'irole.tenant_id = role.tenant_id '
                . 'AND irole.role_id = role.role_id');
        $this->db->where('usr.tenant_id', $tenant_id);
        $this->db->where('usr.account_type', 'INTUSR');
       if ($user_role != '' && $search_radio == 'user_role_radio') {
            $this->db->like('role.role_id', $user_role);
        }
        if ($first_last_name != '' && $search_radio == 'first_last_name_radio') {
            $tax_codes = explode('(', $first_last_name);
            $tax_codes = explode(')', $tax_codes[1]);
            $this->db->like('usr.tax_code', $tax_codes[0], 'both');
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('usr.account_status', $filter_status);
        }
        $this->db->group_by("usr.user_id"); 
        $result = $this->db->get();
        return $result->num_rows;
    }

    /**
     * Get the list of internal users
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @return type
     */
    public function get_internal_user_list($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        $search_radio = $this->input->get('search_radio');
        $user_role = ($this->input->get('user_role')) ? $this->input->get('user_role') : '';
        $first_last_name = ($this->input->get('first_last_name')) ? $this->input->get('first_last_name') : '';
        $user_id = trim($this->input->get('user_id'));
        $filter_status = ($this->input->get('filter_status')) ? $this->input->get('filter_status') : '';
        if ($offset <= 0 || empty($tenant_id)) {
            return;
        }
        $this->db->select('usr.user_id, usr.tax_code, usr.tax_code_type, usr.other_identi_type, usr.other_identi_code, usr.account_status, pers.first_name, '
                . 'pers.last_name, GROUP_CONCAT(role.role_name SEPARATOR ", ") as role_name', FALSE ); 
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->join('internal_user_role irole', 'usr.user_id = irole.user_id');
        $this->db->join('tms_roles role', 'irole.tenant_id = role.tenant_id '
                . 'AND irole.role_id = role.role_id');
        $this->db->where('usr.tenant_id', $tenant_id);
        $this->db->where('usr.account_type', 'INTUSR');
        if ($user_role != '' && $search_radio == 'user_role_radio') {
            $this->db->like('role.role_id', $user_role);
        }
        if(!empty($user_id)){
            $this->db->where('usr.user_id',$user_id);
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('usr.account_status', $filter_status);
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
        $this->db->group_by("usr.user_id"); 
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /* Add data into activity tracking skm start*/
    public function user_activity_log($data)
    {
        $this->db->insert('activity_tracking',$data);
        if($this->db->affected_rows() == 1)
        {
            return TRUE;
        }
        
    }
    
    /* This function get the details of internal staff skm */
    public function get_interuser_details($user_id){
        $this->db->select('*');
        $this->db->from('tms_users');
        $this->db->where('user_id', $user_id);
        //$this->db->where('tenant_id', $tenant_id);
        $sql = $this->db->get();
        $data = $sql->row_array();
        $data['role_id'] = $this->user_role_id($user_id);
        return $data;
    }
    /* This function get all the role id of specific internal user skm start */
    public function user_role_id($user_id)
    {
        $this->db->select('*');
        $this->db->from('internal_user_role');
        $this->db->where('user_id', $user_id);
        $sql = $this->db->get();
        if($sql->num_rows()>0){
            foreach($sql->result_array() as $row)
            {
                $data[] = array('role_id'=>$row['role_id']);
            }
            
            return $data;
            
        }else{
            return 0;
        }
        
    }

    /**
     * This function is used to export all the internal users for a tenant displayed in the list view
     * @param type $tenantId
     * @return type
     */
    public function get_internal_user_list_export($tenantId) {
        $search_radio = $this->input->get('search_radio');
        $user_role = ($this->input->get('user_role')) ? $this->input->get('user_role') : '';
        $first_last_name = ($this->input->get('first_last_name')) ? $this->input->get('first_last_name') : '';
        $filter_status = ($this->input->get('filter_status')) ? $this->input->get('filter_status') : '';
        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'usr.last_modified_on';
        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $this->db->select('usr.*,pers.*,internal_user_emp_detail.*,GROUP_CONCAT(role.role_id SEPARATOR ",") as role_id,'
                . 'GROUP_CONCAT(rl.role_name SEPARATOR ",") as role_name', FALSE);  
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id and usr.user_id = pers.user_id');
        $this->db->join('internal_user_role role', 'usr.tenant_id = role.tenant_id and usr.user_id = role.user_id');
        $this->db->join('tms_roles rl', 'role.role_id = rl.role_id and role.tenant_id = rl.tenant_id');
        $this->db->join('internal_user_emp_detail', 'usr.user_id = internal_user_emp_detail.user_id');
        $this->db->where('usr.account_type', 'INTUSR');
        $this->db->where('usr.tenant_id', $tenantId);
        if ($user_role != '' && $search_radio == 'user_role_radio') {
            $this->db->like('role.role_id', $user_role);
        }
        if ($first_last_name != '' && $search_radio == 'first_last_name_radio') {
            $tax_codes = explode('(', $first_last_name);
            $tax_codes = explode(')', $tax_codes[1]);
            $this->db->like('usr.tax_code', $tax_codes[0], 'both');
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('usr.account_status', $filter_status);
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('usr.last_modified_on', 'DESC');
        }
        $this->db->group_by("usr.user_id"); 
        $result = $this->db->get();                
        return $result;
    }

    /**
     * This function is used to get the user role for a tenant.
     * @param type $tenant_id
     * @return type
     */
    public function get_user_role($tenant_id) {
        return $this->db->where('tenant_id', $tenant_id)->get('tms_roles')->result();
    }
    /**
     * function to save the user data
     * @param type $user
     * @return boolean
     */
    public function save_user_data($user) {
        foreach ($this->input->post() as $key => $value) {
            $$key = $value;
        }

        $dateTime = date('Y-m-d H:i:s');
        $other_identi_type = NULL;
        $other_identi_code = NULL;
        if ($country_of_residence == 'IND') {
            $tax_code_type = 'PAN';
            $tax_code = $PAN;
        }
        if ($country_of_residence == 'SGP') {
            $tax_code_type = $NRIC;
            $tax_code = $NRIC_ID;
            if ($NRIC == "SNG_3") {
                $other_identi_type = $NRIC_OTHER;
                $other_identi_code = $tax_code;
            }
        }
        if ($country_of_residence == 'USA') {
            $tax_code_type = 'SSN';
            $tax_code = $SSN;
        }
        $password = NULL;
        $encrypted_password = NULL;
        if ($activate_user == 'ACTIVE') {
            $password = random_key_generation();
            $encrypted_password = $this->bcrypt->hash_password($password);
            $acct_acti_date_time = $dateTime;
        } else {
            $acct_acti_date_time = '0000-00-00 00:00';
        }
        $pers_dob = ($pers_dob) ? formated_date($pers_dob, '/'):'';
        $emp_doj = ($emp_doj) ? formated_date($emp_doj, '/'):'';


        $tms_users_data = array(
            'tenant_id' => $user->tenant_id,
            'account_type' => 'INTUSR',
            'registration_mode' => 'INTUSR',
            'friend_id' => NULL,
            'registration_date' => $dateTime,
            'user_name' => $user_name,
            'tenant_org_id' => '',
            'password' => $encrypted_password,
            'acc_activation_type' => 'BPEMAC',
            'activation_key' => NULL,
            'registered_email_id' => $user_registered_email,
            'country_of_residence' => $country_of_residence,
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '',
            'acct_acti_date_time' => $acct_acti_date_time,
            'acct_deacti_date_time' => NULL,
            'account_status' => $activate_user,
            'deacti_reason' => NULL,
            'deacti_reason_oth' => NULL,
            'deacti_by' => NULL,
            'created_by' => $user->user_id,
            'created_on' => $dateTime,
            'last_modified_by' => NULL,
            'last_modified_on' => $dateTime,
            'last_login_date_time' => NULL,
            'last_ip_used' => NULL,
            'pwd_last_chgd_on' => NULL
        );
        $this->db->trans_start();
        $this->db->insert('tms_users', $tms_users_data);
        $user_id = $this->db->insert_id();
        if($user_id && $NRIC_OTHER == 'NOTAXCODE' && $country_of_residence == 'SGP' && $NRIC == "SNG_3") {
            $data = array('tax_code' => $user_id,
                'other_identi_code' => $user_id);
            $this->db->where("user_id",$user_id);
            $this->db->update("tms_users",$data);
        }
        $tms_users_pers_data = array(
            'tenant_id' => $user->tenant_id,
            'user_id' => $user_id,
            'first_name' => strtoupper($pers_first_name),
            'gender' => $pers_gender,
            'dob' => $pers_dob,
            'alternate_email_id' => $pers_alternate_email,
            'contact_number' => $pers_contact_number,
            'alternate_contact_number' => $pers_alternate_contact_number,
            'race' => NULL,
            'salary_range' => $sal_range,
            'personal_address_bldg' => strtoupper($pers_personal_address_bldg),
            'personal_address_city' => strtoupper($pers_city),
            'personal_address_state' => $pers_states,
            'personal_address_country' => $pers_country,
            'personal_address_zip' => strtoupper($pers_zip),
            'highest_educ_level	' => $highest_educ_level,
            'photo_upload_path' => NULL,
            'individual_discount' => NULL,
            'certificate_pick_pref' => NULL,
            'indi_setting_list_size' => NULL
        );
        $this->db->insert('tms_users_pers', $tms_users_pers_data);
        $emp_designation_others = '';
        if($emp_designation == 'OTHERS') {
            $emp_designation_others = $this->input->post('emp_designation_others'); 
        }
        $internal_user_emp_details_data = array(
            'tenant_id' => $user->tenant_id,
            'user_id' => $user_id,
            'company_name' => strtoupper($emp_company_name),
            'doj' => $emp_doj,
            'designation' => strtoupper($emp_designation),
            'designation_others'=>strtoupper($emp_designation_others),
            'off_email_id' => $emp_email,
            'off_contact_number' => $emp_contact_number,
            'off_address_bldg' => strtoupper($emp_address),
            'off_address_city' => strtoupper($emp_city),
            'off_address_state' => $emp_states,
            'off_address_country' => $emp_country,
            'off_address_zip' => strtoupper($emp_zip)
        );
        $this->db->insert('internal_user_emp_detail', $internal_user_emp_details_data);
        if(!empty($user_role)) {
            $internal_user_role = array();
            foreach ($user_role as $role) {
                $internal_user_role[] = array(
                    'tenant_id' => $user->tenant_id,
                    'user_id' => $user_id, 
                    'role_id' => $role);
            }
        }
        if(!empty($internal_user_role)) {
            $status = $this->db->insert_batch('internal_user_role', $internal_user_role);
        }
        if ($activate_user == 'ACTIVE' && $status) {    
            $user_details = array('username' => $user_name,
                'email' => $emp_email, 'password' => $password,
                'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_last_name),
                'gender' => $pers_gender);
            $this->internal_user_send_mail($user_details);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $user_id;
    }

    /**
     * send mail to the internal user containing password.
     * @param type $user
     * @return boolean
     */
    public function internal_user_send_mail($user) {
        if ($user['username'] && $user['password'] && $user['email']) {
            $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
            $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);
            $footer_data=str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);
            $subject = 'Your Account Creation Acknowledgment';
            $body = NULL;
            if ($user['gender'] == 'MALE') {
                $body = "Dear Mr." . $user['firstname'].',';
            } elseif ($user['gender'] == 'FEMALE')  {
                $body = "Dear Ms." . $user['firstname'].',';
            }else{
                $body = "Dear " . $user['firstname'].',';
            }
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
     * This function is used to populate the data.
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
     * This method gets the user details for a user based on the tenant
     * @param type $tenant_id
     * @param type $user_id
     * @return boolean
     */
    public function get_user_details($tenant_id, $user_id) {        
        $this->db->select('usr.*,pers.*,emp.*,GROUP_CONCAT(role.role_id SEPARATOR ",") as role_id,'
                . 'GROUP_CONCAT(tms_roles.role_name SEPARATOR ",") as role_name', FALSE); 
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id and usr.tenant_id = pers.tenant_id');
        $this->db->join('internal_user_emp_detail emp', 'usr.user_id = emp.user_id and usr.tenant_id = emp.tenant_id');
        $this->db->join('internal_user_role role', 'usr.user_id = role.user_id and usr.tenant_id = role.tenant_id');
        $this->db->join('tms_roles', 'role.tenant_id = tms_roles.tenant_id and tms_roles.role_id=role.role_id');
        $this->db->where('usr.user_id', $user_id);
        $this->db->where('usr.tenant_id', $tenant_id);
        $this->db->group_by("usr.user_id"); // Added by dummy for multiple roles on Nov 21 2014.
        $qry = $this->db->get();        
        if ($qry->num_rows() > 0) {
            return $qry->row();
        } else {
            return false;
        }
    }
    /**
     * This method for checking user_role in course. Used In internal staff Deactivation.
     * @param type $edit_user_id
     * @param type $tenant_id
     * @return boolean
     */
    public function user_role_check($edit_user_id, $tenant_id) {
        if(empty($edit_user_id) || empty($tenant_id)) {
            return FALSE;
        }
        $this->db->select("crse_name as course_class_name");
        $this->db->from("course");
        $this->db->where("crse_status","ACTIVE");
        $this->db->where("tenant_id",$tenant_id);
        $this->db->where("FIND_IN_SET(" . $edit_user_id . ",crse_manager) !=", 0);        
        $result = $this->db->get();        
        if ($result->num_rows == 0) {            
            $this->db->select("class_name as course_class_name");
            $this->db->from("course_class");
            $this->db->where("tenant_id",$tenant_id);
            $this->db->where("FIND_IN_SET(" . $edit_user_id . ",classroom_trainer) !=", 0);
            $this->db->or_where("FIND_IN_SET(" . $edit_user_id . ",lab_trainer) !=", 0);
            $this->db->or_where("FIND_IN_SET(" . $edit_user_id . ",assessor) !=", 0);
            $this->db->or_where("FIND_IN_SET(" . $edit_user_id . ",training_aide) !=", 0);
            $this->db->or_where("FIND_IN_SET(" . $edit_user_id . ",sales_executive) !=", 0);
            $result = $this->db->get();            
        }        
        return $result->result();
    }

   /**
    * This function is used to get the role name based on the role ID
    * @param type $role_id
    * @return type
    */
    public function check_sales_exec($userid,$roleid) {
      $this->db->select('*');
      $this->db->from('internal_user_role'); 
      $this->db->where('user_id', $userid);
      $this->db->where('role_id', $roleid);
      $result = $this->db->get()->result();
      return $result;
    }
     /**
    * This function is used to get the role name based on the role ID
    * @param type $role_id
    * @return type
    */
    public function check_sales_exec1($userid) {
      $this->db->select('*');
      $this->db->from('internal_user_role'); 
      $this->db->where('user_id', $userid);
      $result = $this->db->get()->row();
       $this->db->last_query();
      return $result;
    }
    
       /**
    * This function is used to get the role name based on the role ID
    * @param type $role_id
    * @return type
    */
    public function get_user_role_name($role_id) {
        return $this->db->select('role_name')->where('role_id', $role_id)->get('tms_roles')->row();
    }
    /**
     * Internal user auto-fill help
     * @param type $search_firstname
     * @return type
     */
    public function internal_user_list_autocomplete($search_firstname = NULL) {
        $matches = array();
        if (!empty($search_firstname)) {
            $user = $this->session->userdata('userDetails');
            $tenant_id = $user->tenant_id;
            $this->db->select('usr.tax_code,pers.user_id, pers.first_name, pers.last_name');
            $this->db->from('tms_users_pers pers');
            $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');
            $this->db->where('usr.account_type', 'INTUSR');
            $this->db->where('usr.tenant_id', $tenant_id);
            $this->db->where('usr.user_id !=', $user->user_id);
            $this->db->where_not_in('usr.account_status', 'INACTIV');
            $this->db->like('pers.first_name', $search_firstname, 'both');
            $results = $this->db->get()->result();

            foreach ($results as $result) {
                 $matches[$result->user_id] = array(
                    'label'=>$result->first_name . ' ' . $result->last_name . '  NRIC/FIN: ' . $result->tax_code . '(' . $result->user_id . ')',
                    'key'=>$result->user_id
                    );
            }
        }
        return $matches;
    }

    /*
     * This function is used to update internal data
     */
    public function update_user_data() {
        foreach ($this->input->post() as $key => $value) {
            $$key = $value;
        }
        $user = $this->session->userdata('userDetails');

        $dateTime = date('Y-m-d H:i:s');
        $other_identi_type = NULL;
        $other_identi_code = NULL;

        if ($country_of_residence == 'IND') {
            $tax_code_type = 'PAN';
            $tax_code = $PAN;
        }
        if ($country_of_residence == 'SGP') {

            $tax_code_type = $NRIC;
            $tax_code = $NRIC_ID;
            if ($NRIC == "SNG_3") {
                $other_identi_type = $NRIC_OTHER;
                $other_identi_code = $tax_code;
            }
        }
        if ($country_of_residence == 'USA') {
            $tax_code_type = 'SSN';
            $tax_code = $SSN;
        }

        $tms_users_data = array(
            'registered_email_id' => $user_registered_email,
            'country_of_residence' => $country_of_residence,
            'tax_code_type' => $tax_code_type,
            'tax_code' => strtoupper($tax_code),
            'other_identi_type' => $other_identi_type,
            'other_identi_code' => strtoupper($other_identi_code),
            'other_identi_upload' => '');

        if ($activate_user == 'ACTIVE') {
            $tms_users_data['acct_acti_date_time'] = $dateTime;
            $tms_users_data['account_status'] = $activate_user;
        } elseif ($activate_user == 'PENDACT') {
            $tms_users_data['account_status'] = $activate_user;
        }
        $password = NULL;
        $encrypted_password = NULL;
        if ($activate_user != NULL && $activate_user == 'ACTIVE') { // this means user was in pending activation and during edit status was changed to active
            $password = random_key_generation();
            $encrypted_password = $this->bcrypt->hash_password($password);
            $tms_users_data['password'] = $encrypted_password;
        }

        $tms_users_data['acct_deacti_date_time'] = NULL;
        $tms_users_data['deacti_reason'] = NULL;
        $tms_users_data['deacti_reason_oth'] = NULL;
        $tms_users_data['deacti_by'] = NULL;
        $tms_users_data['last_modified_by'] = $user->user_id;
        $tms_users_data['last_modified_on'] = $dateTime;

        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('user_id', $edit_user_id);
        $this->db->trans_start();
        $this->db->update('tms_users', $tms_users_data);
        if($edit_user_id && $NRIC_OTHER == 'NOTAXCODE' && $country_of_residence == 'SGP' && $NRIC == "SNG_3") {
            $data = array('tax_code' => $edit_user_id,
                'other_identi_code' => $edit_user_id);
            $this->db->where("user_id",$edit_user_id);
            $this->db->where('tenant_id', $user->tenant_id);
            $this->db->update("tms_users",$data);
        }
        $pers_dob = ($pers_dob)?formated_date($pers_dob, '/'):'';
        ////added by shubhranshu due to paranthesis in search issue on 04/12/2018/////
        $pers_first_name=str_replace(array( '(', ')' ), '', $pers_first_name);////////
        /////////////////////////////////////////////////////////////////////////////
        $tms_users_pers_data = array(
            'first_name' => strtoupper($pers_first_name),
            'last_name' => "",
            'gender' => $pers_gender,
            'dob' => $pers_dob,
            'alternate_email_id' => $pers_alternate_email,
            'contact_number' => $pers_contact_number,
            'alternate_contact_number' => $pers_alternate_contact_number,
            'personal_address_bldg' => strtoupper($pers_personal_address_bldg),
            'personal_address_city' => strtoupper($pers_city),
            'personal_address_state' => $pers_states,
            'personal_address_country' => $pers_country,
            'personal_address_zip' => strtoupper($pers_zip),
            'highest_educ_level' => $highest_educ_level,
            'salary_range' => $sal_range
        );
        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('user_id', $edit_user_id);
        $this->db->update('tms_users_pers', $tms_users_pers_data);
        $emp_doj = ($emp_doj)?formated_date($emp_doj, '/'):'';
        $emp_designation_others = '';
        if($emp_designation == 'OTHERS') {
            $emp_designation_others = $this->input->post('emp_designation_others'); 
        }
        $internal_user_emp_details_data = array(
            'company_name' => strtoupper($emp_company_name),
            'doj' => $emp_doj,
            'designation' => strtoupper($emp_designation),
            'designation_others'=>strtoupper($emp_designation_others),
            //'off_email_id' => $emp_email,
            'off_contact_number' => $emp_contact_number,
            'off_address_bldg' => strtoupper($emp_address),
            'off_address_city' => strtoupper($emp_city),
            'off_address_state' => $emp_states,
            'off_address_country' => $emp_country,
            'off_address_zip' => strtoupper($emp_zip)
        );
        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('user_id', $edit_user_id);
        $this->db->update('internal_user_emp_detail', $internal_user_emp_details_data);
        if(!empty($user_role)) {
            $internal_user_role = array();
            foreach ($user_role as $role) {
                $internal_user_role[] = array(
                    'tenant_id' => $user->tenant_id,
                    'user_id' => $edit_user_id, 
                    'role_id' => $role);
            }
        }        
        if(!empty($internal_user_role)) {
            $this->db->where('user_id', $edit_user_id);
            $this->db->where('tenant_id', $user->tenant_id);
            $this->db->delete('internal_user_role');
             $this->db->insert_batch('internal_user_role', $internal_user_role);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        if ($activate_user == 'ACTIVE') {
            $user_details = array('username' => $user_name,
                'email' => $emp_email, 'password' => $password,
                'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_last_name),
                'gender' => $pers_gender);
            $this->internal_user_send_mail($user_details);
        }
        return $edit_user_id;
    }

    /**
     * checks if user name already exists
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
    * checks if user name already exists 
    * @param type $username
    * @param type $username_edit
    * @return boolean
    */
    public function check_duplicate_user_name_edit($username, $username_edit) {
        $exists = $this->db->select('user_id')->where('user_name !=', $username_edit)->get_where('tms_users', array('user_name' => $username), 1)->num_rows();
        if ($exists) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     *  checks if user name already exists
     * @param type $useremail
     * @return boolean
     */
    public function check_duplicate_user_email($useremail) {
        $exists = $this->db->select('user_id')->get_where('tms_users', array('registered_email_id' => $useremail), 1)->num_rows();
        if ($exists) {
            return TRUE;
        }
        return FALSE;
    }
    //added by shubhranshu to validate company email
    public function check_duplicate_user_email_company($offemail) {
        $exists = $this->db->select('user_id')->get_where('internal_user_emp_detail', array('off_email_id' => $offemail), 1)->num_rows();
        if ($exists) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * checks if user name already exists 
     * @param type $useremail
     * @param type $useremail_edit
     * @return boolean
     */
    public function check_duplicate_user_email_edit($useremail, $useremail_edit) {
        $exists = $this->db->select('user_id')->where('registered_email_id !=', $useremail_edit)->get_where('tms_users', array('registered_email_id' => $useremail), 1)->num_rows();
        if ($exists) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * checks if user tax code already exists 
     * @param type $tax_code
     * @return boolean
     */
    public function check_duplicate_user_taxcode($tax_code) {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $exists = $this->db->select('tax_code')->get_where('tms_users', array('tax_code' => $tax_code, 'tenant_id'=>$tenant_id), 1)->num_rows();
        if ($exists) {
            return FALSE;
        }
        return TRUE;
    }

   /**
    * checks if user tax code already exists
    * @param type $tax_code
    * @param type $tax_code_edit
    * @return boolean
    */
    public function check_duplicate_user_taxcode_edit($tax_code, $tax_code_edit) {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $exists = $this->db->select('tax_code')->where('tax_code !=', $tax_code_edit)->get_where('tms_users', array('tax_code' => $tax_code, 'tenant_id'=>$tenant_id), 1)->num_rows();
        if ($exists) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * This function deactivates the user selectd.
     * @param type $user_id
     * @return boolean
     */
    public function deactivate_user($user_id) {
        foreach ($this->input->post() as $key => $value) {
            $$key = $value;
        }

        if ($reason_for_deactivation != 'OTHERS') {
            $other_reason_for_deactivation = '';
        }
        $user = $this->session->userdata('userDetails');
        $tms_users_data = array(
            'account_status' => 'INACTIV',
            'acct_deacti_date_time' => date('Y-m-d H:i:s'),
            'deacti_reason' => $reason_for_deactivation,
            'deacti_reason_oth' => strtoupper($other_reason_for_deactivation),
            'deacti_by' => $user->user_id,
        );
        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('user_id', $user_id);
        $this->db->trans_start();
        $this->db->update('tms_users', $tms_users_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

   /**
    * This function will check the email id exists
    * @param type $email_id
    * @return type
    */
    public function check_email($email_id) {
        $this->db->select('user_id');
        $this->db->from('tms_users');
        $this->db->where('registered_email_id', $email_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * This function will check the pan exists
     * @return type
     */
    
    ////// added by shubhranshu for valid email(internal user view page) on 4/12/2018///////////////////
    public function check_email_status($email_id) {
        $this->db->select('user_id');
        $this->db->from('internal_user_emp_detail');
        $this->db->where('off_email_id', $email_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
     public function update_email_status($email_id,$usrid) {
       $data = array(
           'off_email_id' => $email_id
       );
        $this->db->where('user_id', $usrid);
        $status =$this->db->update('internal_user_emp_detail',$data);
        return $status;
    }/////////////////////////////////////////////////////////////////////////////////
    public function check_pan() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        extract($_POST);
        $pan_id = trim($pan_id);
        $this->db->select('usr.user_id, usr.tenant_id, usr.account_type, usr.account_status, usr.registered_email_id,'
                . 'usr_pers.first_name, usr_pers.last_name, usr_pers.nationality, usr_pers.gender, usr_pers.dob,'
                . 'usr_pers.contact_number, usr_pers.personal_address_bldg, usr_pers.personal_address_city,'
                . 'usr_pers.personal_address_state, usr_pers.personal_address_country, usr_pers.personal_address_zip');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers usr_pers',"usr.user_id = usr_pers.user_id");
        $this->db->where('usr.tax_code', $pan_id);
        if ($edit_user_id != '') {
            $this->db->where('usr.user_id !=', $edit_user_id);
        }
        $this->db->where('usr.tenant_id',$tenant_id);
        $result = $this->db->get();
        return $result->row();
    }

    /**
     * This function will check the username exists
     * @param type $user_name
     * @return type
     */
    public function check_username($user_name) {
        $this->db->select('user_id');
        $this->db->from('tms_users');
        $this->db->where('user_name', $user_name);
        $query = $this->db->get();
        return $query->num_rows();
    }

  /**
   * This Method for Internal user name auto complete
   * @param type $query
   * @return string
   */
    public function internal_user_autocomplete($query = NULL) {
        $matches = array();
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $strMatchString = $query;
        if (!empty($strMatchString)) {
            $strQry = 'SELECT usr.user_id, usr.tax_code, pers.first_name, pers.last_name'
                    . ' FROM tms_users usr, tms_users_pers pers';
            
            $strQry .= ' WHERE usr.user_id=pers.user_id AND usr.tenant_id=pers.tenant_id ';
            
            $strQry .= ' AND usr.tenant_id="' . $tenant_id . '"'
                    . ' AND usr.account_type = "INTUSR"'
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

    /**
     * Get internal user by account type
     * @param type $types
     * @param type $tenant_id
     * @return type
     */
    public function get_users_by_types($types, $tenant_id) {
        return $this->db->
                        select("user_id, registered_email_id")->
                        from("tms_users")->
                        where("account_status", 'ACTIVE')->
                        where("tenant_id", $tenant_id)->
                        where_in("account_type", $types)->
                        get();
    }
    /**
     * Get internal User By Id
     * @param type $user_ids
     * @return type
     */
    public function get_users_by_ids($user_ids) {
        return $this->db->
                        select("user_id, registered_email_id")->
                        from("tms_users")->
                        where("account_status", 'ACTIVE')->
                        where_in("user_id", $user_ids)->
                        get();
    }

    /**
     * This function for updating the password.
     * @param type $data
     * @return boolean
     */
    public function update_password($data) {
        $result = $this->match_old_pwd($data['old_password']);
        if ($result == TRUE) {
            $user = $this->session->userdata('userDetails');
            $update_array = array('password' => $data['encrypted_password']);
            $this->db->where("user_id", $user->user_id);
            $this->db->where("tenant_id", $user->tenant_id);
            $this->db->trans_start();
            $result = $this->db->update("tms_users", $update_array);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

   /**
    * This method for checking the old password
    * @param type $old_password
    * @return boolean
    */
    private function match_old_pwd($old_password) {
        $user = $this->session->userdata('userDetails');
        $this->db->select("password");
        $this->db->from("tms_users");
        $this->db->where("user_id", $user->user_id);
        $this->db->where("tenant_id", $user->tenant_id);
        $result = $this->db->get()->row();
        if ($this->bcrypt->check_password($old_password, $result->password)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * This method for changing the role of trainee to Internal Staff
     * @return boolean
     */
    public function trainee_role_change() {        
        $user = $this->session->userdata('userDetails');
        $trainee_tax_code = trim($this->input->post('trainee_tax_code'));
        $trainee_user_id = $this->input->post('trainee_user_id');
        $trainee_status = $this->input->post('trainee_status');
         $new_array = array();
        if ($trainee_status == 'PENDACT') {
            $password = random_key_generation();
            $encrypted_password = $this->bcrypt->hash_password($password);            
            $new_array = array("password"=>$encrypted_password,"acct_acti_date_time"=>date('Y-m-d H:i:s'));
        }
        $tms_users_array = array("account_type" => "INTUSR",
            "account_status" => "ACTIVE",
            "upgrade_internal_staff_on" => date('Y-m-d H:i:s'),
            "staff_upgrade_done_by" => $user->user_id,
            "last_modified_on" => date('Y-m-d H:i:s'),
            "last_modified_by" => $user->user_id) + $new_array;
        $this->db->where("user_id",$trainee_user_id);        
        $this->db->where("tenant_id", $user->tenant_id);
        $this->db->update("tms_users",$tms_users_array);
        $tenant_company_users = array(
            'user_acct_status' => 'INACTIV'
        );
        $this->db->where("user_id",$trainee_user_id);        
        $this->db->where("tenant_id", $user->tenant_id);
        $this->db->update("tenant_company_users",$tenant_company_users);
        $trainee_designation_others = '';
        $trainee_company_name = $this->input->post('trainee_company_name');
        $trainee_doj = $this->input->post('trainee_doj');        
        $trainee_doj = formated_date($trainee_doj, '/');        
        $trainee_designation = $this->input->post('trainee_designation');
        if($trainee_designation == 'OTHERS') {
            $trainee_designation_others = $this->input->post('trainee_designation_others'); 
        }
        $trainee_email = $this->input->post('trainee_email');
        $trainee_contact_number = $this->input->post('trainee_contact_number');
        $trainee_city = $this->input->post('trainee_city');
        $trainee_country = $this->input->post('trainee_country');
        $trainee_states = $this->input->post('trainee_states');
        $trainee_address = $this->input->post('trainee_address');
        $trainee_zip = $this->input->post('trainee_zip');
        $internal_user_emp_detail_array = array ("tenant_id"=>$user->tenant_id,
            "user_id"=>$trainee_user_id,
            "company_name" => strtoupper($trainee_company_name),"doj" => $trainee_doj,
            "designation" =>strtoupper($trainee_designation),"off_email_id"=>$trainee_email,
            "designation_others" => strtoupper($trainee_designation_others),
            "off_contact_number"=>$trainee_contact_number,"off_address_bldg"=>strtoupper($trainee_address),
            "off_address_city"=>strtoupper($trainee_city),"off_address_state"=>$trainee_states,
            "off_address_country"=>$trainee_country,"off_address_zip"=>strtoupper($trainee_zip));
        $this->db->insert("internal_user_emp_detail",$internal_user_emp_detail_array);
        $trainee_role = $this->input->post("trainee_role");
        if(!empty($trainee_role)) {
            $internal_user_role = array();
            foreach ($trainee_role as $role) {
                $internal_user_role[] = array(
                    'tenant_id' => $user->tenant_id,
                    'user_id' => $trainee_user_id, 
                    'role_id' => $role);
            }
        }        
        if(!empty($internal_user_role)) {
            $status = $this->db->insert_batch('internal_user_role', $internal_user_role);
        }
        if($status) {            
            if ($trainee_status == 'PENDACT') {
                $trainee_details = $this->get_trainee_details($trainee_user_id);                
                $user_details = array('username' => $trainee_details->user_name,
                    'email' => $trainee_details->registered_email_id, 'password' => $password,
                    'firstname' => $trainee_details->first_name, 'lastname' => $trainee_details->last_name,
                    'gender' => $trainee_details->gender);                
                $this->internal_user_send_mail($user_details);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * This methord for getting trainee details for sending email to trainee.
     * @param type $trainee_user_id
     * @return type
     */
    private function get_trainee_details($trainee_user_id){
        $this->db->select("usr.user_name, usr.registered_email_id, pers.first_name, pers.last_name, pers.gender");
        $this->db->from("tms_users usr");
        $this->db->join("tms_users_pers pers","usr.user_id=pers.user_id");
        $this->db->where("usr.user_id",$trainee_user_id);
        $result = $this->db->get();        
        return $result->row();
    }
    /**
     * This method for getting discount details in internal staff
     * @param type $tenant_id
     * @param type $user_id
     * @return type
     */
    public function get_discount_details($tenant_id, $user_id) {
        $this->db->select('c.crse_name, c.course_id');
        $this->db->select(' tud.discount_percent');
        $this->db->from('course c');
        $this->db->join('tms_users_discount tud', 'tud.course_id = c.course_id and tud.user_id="' . $user_id . '"', 'LEFT');
        $this->db->where('c.tenant_id', $tenantid);
        $this->db->where('c.crse_status', 'ACTIVE');
        $query = $this->db->get();
        return $query->result_array();
    }
 /**
     * function to reactivate user
     * @param type $user_id
     * @param type $reactivate_reason
     * @param type $other_reason
     * @return boolean
     */
    public function reactivate_user($user_id = NULL, $reactivate_reason, $other_reason) {
        if(empty($user_id)){
            return FALSE;
        }
        $other_reason = ($reactivate_reason == 'OTHERS') ? $other_reason : '';
        $user = $this->session->userdata('userDetails');
        $cur_date = date('Y-m-d H:i:s');
        $tms_users_data = array(
            'account_status' => 'ACTIVE',
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $cur_date,
            'reactivation_date_time' => $cur_date,
            'reactivated_by' => $user->user_id,
            'reactivation_reason_id' => $reactivate_reason,
            'reactivation_reason_others' => strtoupper($other_reason),
        );
        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('tms_users', $tms_users_data);
        return TRUE;
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

}

