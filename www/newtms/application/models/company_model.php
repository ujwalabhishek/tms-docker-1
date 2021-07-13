<?php

/*
 * This is the Model class for Company
 */

class Company_Model extends CI_Model {
    
     private $user;

    public function __construct() {
        parent::__construct();
        $this->load->library('bcrypt');
        $this->load->helper('common');
        $this->user = $this->session->userdata('userDetails');
    }

    /*
     * This method gets count for the company list for a tenant
     */

    public function get_company_count_by_tenant_id($tenant_id) {

        if (empty($tenant_id)) {
            return 0;
        }
        $this->db->select('count(*) as totalrows');
        $this->db->from('tenant_company tc');
        $this->db->join('company_master cm', 'cm.company_id=tc.company_id');
        $this->db->where('tc.tenant_id', $tenant_id);
        $business_type = $this->input->get('business_type');
        $filter_status = $this->input->get('filter_status');
         $company_id = $this->input->get('list_search_company_id');
        if (!empty($company_id)) {
            $this->db->where('cm.company_id', $company_id);
        }
        if ($business_type != '') {
            $this->db->like('cm.business_type', $business_type, 'after');
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('tc.comp_status', $filter_status);
        }
 
        $result = $this->db->get()->result();

        return $result[0]->totalrows;
    }

    /**
     * Function will retrieve the company list
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @param type $gettotal
     * @return type
     */
    public function get_company_list($tenant_id, $limit, $offset, $sort_by, $sort_order, $gettotal = NULL) {

        if ($offset <= 0 || empty($tenant_id)) {
            return;
        }

        $this->db->select('cm.company_id');
        $this->db->select('cm.company_name');
        $this->db->select('cm.comp_scn SCN');
        $this->db->select('tc.comp_status');
        $this->db->select('cm.last_modified_by, cm.last_modified_on');
        $this->db->select('cm.comp_address, cm.comp_city, cm.comp_state, cm.comp_cntry, cm.comp_zip, cm.comp_phone');
        $this->db->from('tenant_company tc');
        $this->db->join('company_master cm', 'cm.company_id=tc.company_id');
        $this->db->where('tc.tenant_id', $tenant_id);
        $business_type = $this->input->get('business_type');
        $filter_status = $this->input->get('filter_status');
       $company_id = $this->input->get('list_search_company_id');
        if (!empty($company_id)) {
            $this->db->where('cm.company_id', $company_id);
        }
         if ($business_type != '') {
            $this->db->like('cm.business_type', $business_type, 'after');
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('tc.comp_status', $filter_status);
        }
      if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('cm.last_modified_on', 'DESC');
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

    /*
     * Function used to store company datas
     */
    public function save_company_details() {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        extract($_POST);

        $curr_date_time = date('Y-m-d H:i:s');
        $acct_activation_date_time = '0000-00-00 00:00:00';
        if ($activate_company == 'ACTIVE') {
            $acct_activation_date_time = $curr_date_time;
        }


        if ($country_of_residence != 'SGP') {
            $comp_scn = '';
        }
       $companydata = array(
            'company_name' => $company_name,
            'comp_regist_num' => $regno,
            'business_type' => $business_type,
            'business_size' => $business_s,
            'comp_phone' => $phoneno,
            'comp_fax' => strtoupper($faxno),
            'comp_address' => $street,
            'comp_city' => $city,
            'comp_state' => $pers_states,
            'comp_cntry' => $company_country,
            'comp_zip' => strtoupper($zipcode),
            'company_status' => $activate_company,
            'remarks' => $comments,
            'created_by' => $user->user_id,
            'created_on' => $curr_date_time,
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $curr_date_time,
            'comp_scn' => $comp_scn,
            'comp_cntry_scn' => $country_of_residence,
            'sme_nonsme' => $sme_type,
            'comp_attn' => strtoupper($comp_attn),
            'comp_email' => $comp_email,
        );
        if ($activate_company == 'ACTIVE') {
            $companydata['acct_activation_date_time'] = $acct_activation_date_time;
        }
        $this->db->trans_start();
        $this->db->insert('company_master', $companydata);
        $comp_id = $this->db->insert_id();
        $tenantcompany = array(
            'tenant_id' => $tenant_id,
            'company_id' => $comp_id,
            'comp_status' => $activate_company,
            'assigned_by' => $user->user_id,
            'assigned_on' => $curr_date_time,
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $curr_date_time,
        );
        if ($activate_company == 'ACTIVE') {
            $tenantcompany['acct_acti_date_time'] = $acct_activation_date_time;
        }
        $this->db->insert('tenant_company', $tenantcompany);
        foreach ($comp_disc_perc as $k => $row) {
            $indi_data = array(
                'Tenant_ID' => $tenant_id,
                'Company_ID' => $comp_id,
                'Course_ID' => $k,
                'Discount_Percent' => $row,
                'Discount_Amount' => $comp_disc_amt[$k],
            );
            $this->db->insert('company_discount', $indi_data);
        }
        $account_type = 'COMUSR';
        $registration_date = $curr_date_time;
        $registration_mode = 'INTUSR';
        $acc_activation_type = 'BPEMAC';

        for ($i = 0; $i < count($username); $i++) {
            $encrypted_password = NULL;
            if ($activate_company == 'ACTIVE') {
                $password = random_key_generation();
                $encrypted_password = $this->bcrypt->hash_password($password);
            }
            $tms_users_data = array(
                'tenant_id' => $tenant_id,
                'account_type' => $account_type,
                'registration_mode' => $registration_mode,
                'password' => $encrypted_password,
                'registration_date' => $registration_date,
                'user_name' => $username[$i],
                'registered_email_id' => $email_01[$i],
                'account_status' => $activate_company,
                'created_by' => $user->user_id,
                'created_on' => $curr_date_time,
                'last_modified_by' => $user->user_id,
                'last_modified_on' => $curr_date_time,
                'acc_activation_type' => 'BPEMAC',
            );
            if ($activate_company == 'ACTIVE') {
                $tms_users_data['acct_acti_date_time'] = $acct_activation_date_time;
            }
            $this->db->insert('tms_users', $tms_users_data);
             $user_id = $this->db->insert_id();
            $tms_users_pers_data = array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'first_name' => $fname[$i],
                 'gender' => $gender[$i],
                'contact_number' => $contactno[$i],
                'alternate_contact_number' => $mobileno[$i],
                'alternate_email_id' => $email_02[$i],
            );
            $this->db->insert('tms_users_pers', $tms_users_pers_data);
            $internal_user_role_data = array(
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'role_id' => 'COMPACT'
            );
            $this->db->insert('internal_user_role', $internal_user_role_data);
            $tenant_Comp_users = array(
                'company_id' => $comp_id,
                'tenant_id' => $tenant_id,
                'user_id' => $user_id,
                'user_acct_status' => $activate_company,
                'assigned_by' => $user->user_id,
                'assigned_on' => $curr_date_time,
                'last_modified_by' => $user->user_id,
                'last_modified_on' => $curr_date_time,
            );
            if ($activate_company == 'ACTIVE') {
                $tenant_Comp_users['acct_acti_date_time'] = $acct_activation_date_time;
            }
            $insert_result = $this->db->insert('tenant_company_users', $tenant_Comp_users);
            if ($activate_company == 'ACTIVE' && $insert_result) {
                $user_details = array('username' => $username[$i],
                    'email' => $email_01[$i], 'password' => $password,
                    'firstname' => $fname[$i], 'lastname' => $lname[$i],
                    'gender' => $gender[$i]);
                $this->compnay_user_send_mail($user_details,$company_name);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('company_db_error', 'Oops! Sorry, it looks like something went wrong and an error has occurred');
            redirect('company');
        }
        return;
    }

    /**
     * This function is used to Send Company Account Activation Mail
     * @param type $user_details
     * @param type $company_name
     * @return boolean
     */
    public function compnay_user_send_mail($user_details,$company_name) {
        if ($user_details['username'] && $user_details['password'] && $user_details['email']) {
            $user = $this->session->userdata('userDetails');
            $tenant_details = fetch_tenant_details($user->tenant_id);
           $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);
            $footer_data=str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);
            $subject = 'Your Account Creation Acknowledgment';
            $body = NULL;
            if ($user_details['gender'] == 'MALE') {
                $body = "Dear Mr." . $user_details['firstname'].',';
            } else if ($user_details['gender'] == 'FEMALE') {
                $body = "Dear Ms." . $user_details['firstname'].',';
            } else {
                $body = "Dear " . $user_details['firstname'].',';
            }
            $body .= '<br/><br/>Thank you for registering with us. Your account has been successfully created in "'.$company_name.'".<br/><br/>';
            $body .= 'With this Training Management System, you will able to do the following:<br/>
                    - enroll your company staff for our courses<br/>
                    - view your staff attentance<br/>
                    - view your staff Certificate<br/>
                    - check the payment details<br/>
                    - and other features!
                    <br/><br/>';
            /*                  - edit their details and training schedules<br/>*/
            $body .= "<strong>Your username:</strong>&nbsp;&nbsp;" . $user_details['username'] . "<br/>";
            $body .= "<strong>Your password:</strong>&nbsp;&nbsp;" . $user_details['password'] . "<br/><br/>";
            $body .= '<strong>You can access the TMS portal at <a href="' .  base_url() . '">' .  base_url() . '</a></strong><br/><br/>';
            $body .= $footer_data;         
            return send_mail($user_details['email'], '', $subject, $body);
        }
        return FALSE;
    }
    /*
     * This function will check the username exists on the db or not. (Add company)
     */
    public function check_username($user_name) {
        $this->db->select('user_id');
        $this->db->from('tms_users');
        $this->db->where('user_name', $user_name);
        $query = $this->db->get();
        return $query->num_rows();
    }
    /*
     * This function will check the username exists on the db or not. (Edit company)
     */
    public function check_username_edit($user_name, $curr_user_name) {
        $this->db->select('user_id');
        $this->db->from('tms_users');
        $this->db->where('user_name', $user_name);
        $this->db->where('user_name !=', $curr_user_name);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * This function will check the email id exists on the db or not.
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
     * This function will check the email id exists on the db or not.
     * @param type $email_id
     * @param type $curr_email_id
     * @return type
     */
    public function check_email_edit($email_id, $curr_email_id) {
        $this->db->select('user_id');
        $this->db->from('tms_users');
        $this->db->where('registered_email_id', $email_id);
        $this->db->where('registered_email_id !=', $curr_email_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    /**
     * This function will check the registration number exists on the db or not.
     * @param type $reg_num
     * @return type
     */
     public function check_registration_number($reg_num,$tenant_id) {
        $this->db->select('cm.company_id');
        $this->db->from('company_master cm');
        $this->db->join('tenant_company tc', 'tc.company_id=cm.company_id'); 
        $this->db->where('cm.comp_regist_num', $reg_num);
        $this->db->where('tc.tenant_id', $tenant_id);
        $query = $this->db->get();
         $this->db->last_query();
        return $query->num_rows();
    }
    /**
     * This function will check the registration number exists on the db or not
     * @param type $reg_num
     * @param type $curr_reg_num
     * @return type
     */
    public function check_registration_number_edit($reg_num, $curr_reg_num,$tenant_id) {
        $this->db->select('cm.company_id');
        $this->db->from('company_master cm');
        $this->db->join('tenant_company tc', 'tc.company_id=cm.company_id'); 
        $this->db->where('cm.comp_regist_num', $reg_num);
        $this->db->where('cm.comp_regist_num !=', $curr_reg_num);
          $this->db->where('tc.tenant_id', $tenant_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    /*
     * This method gets the company details
     * @param - Company Id
     * @param - Tenant Id
     */
    public function get_company_users($tenant_id, $company_id) {
        if ((empty($tenant_id)) && (empty($company_id))) {
            return 0;
        }
        $this->db->select('tecomusr.user_id');
        $this->db->from('tenant_company_users tecomusr');
        $this->db->join('tms_users usr', 'usr.user_id = tecomusr.user_id');
        $this->db->where('usr.account_type', 'COMUSR');
       $this->db->where('tecomusr.tenant_id', $tenant_id);
        $this->db->where('tecomusr.company_id', $company_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $comp_user_deatails = array();
            foreach ($query->result() as $row) {
                $comp_user_deatails[] = $row->user_id;
            }
        }
        return $comp_user_deatails;
    }

    /**
     * This method gets the company details
     * @param type $tenant_id
     * @param type $company_id
     * @return type
     */
    public function get_company_users_details($tenant_id, $company_id) {
        $this->db->select('usr.user_id,usr.registered_email_id,usr.user_name,usr.account_type, tecomusr.user_acct_status, tecomusr.acct_deacti_date_time, tecomusr.deacti_reason, tecomusr.deacti_reason_oth, tecomusr.deacti_by, pers.first_name, pers.last_name, pers.gender, pers.contact_number, pers.alternate_contact_number, pers.alternate_email_id');
        $this->db->from('tenant_company_users tecomusr');
        $this->db->join('tms_users_pers pers', 'pers.user_id = tecomusr.user_id');
        $this->db->join('tms_users usr', 'usr.user_id = tecomusr.user_id');
        $this->db->where('usr.account_type', 'COMUSR');
        $this->db->where('tecomusr.tenant_id', $tenant_id);
        $this->db->where('tecomusr.company_id', $company_id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * company discount
     * @param type $tenant_id
     * @param type $company_id
     * @return type
     */
    public function get_company_discount($tenant_id, $company_id, $type = '') {
        $this->db->select('c.crse_name, c.course_id');
        $this->db->select('cd.Discount_Percent, cd.Discount_Amount');
        $this->db->from('course c');
        $this->db->join('company_discount cd', 'cd.Course_ID = c.course_id and cd.Tenant_ID = c.tenant_id and cd.Company_ID="' . $company_id . '"', 'LEFT');
        $this->db->where('c.tenant_id', $tenant_id);
        if ($this->user->role_id == 'CRSEMGR' && empty($type)) {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }
        $this->db->where('c.crse_status', 'ACTIVE');
        //code modified on 07-04-2015
        $this->db->order_by("c.crse_name");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * This method gets the company details
     * @param type $tenant_id
     * @param type $company_id
     * @return type
     */
    //////added by shubhranshu for bulk enrollment company discount issue on 11-02-2020
     public function get_company_details_discount($tenant_id, $company_id,$course) {

        $this->db->select('*');
        $this->db->from('tenant_company company');
        $this->db->join('company_master companymaster', 'company.company_id = companymaster.company_id');
        if($course > 0 && $course !=''){
             $this->db->join('company_discount cd', 'company.company_id = cd.company_id');
            $this->db->where('cd.Course_ID', $course);
        }
        $this->db->where('company.tenant_id', $tenant_id);
        $this->db->where('company.company_id', $company_id);
        
        $qry = $this->db->get();

        if ($qry->num_rows() > 0) {
            $comp_details = array();
            foreach ($qry->result() as $row) {
                $comp_details[] = $row;
            }
        }
        return $comp_details;
    }
    
    
    public function get_company_details($tenant_id, $company_id) {

        $this->db->select('*');
        $this->db->from('tenant_company company');
        $this->db->join('company_master companymaster', 'company.company_id = companymaster.company_id');
        $this->db->where('company.tenant_id', $tenant_id);
        $this->db->where('company.company_id', $company_id);
        $qry = $this->db->get();
        if ($qry->num_rows() > 0) {
            $comp_details = array();
            foreach ($qry->result() as $row) {
                $comp_details[] = $row;
            }
        }
        return $comp_details;
    }
    /**
     * Internal Company Trainee List Auto-fill help
     * @param type $name_startsWith
     * @param type $mode
     * @return type
     */
    public function internal_company_list_autocomplete($name_startsWith, $mode) {
        $name_startsWith = trim($name_startsWith);
        $user = $this->session->userdata('userDetails');
        $results = array();
        if (!empty($name_startsWith)) {
            $this->db->select('cm.company_id, cm.company_name, cm.comp_regist_num');
            $this->db->from('company_master cm');
            $this->db->join('tenant_company tc', 'tc.company_id=cm.company_id');
            $this->db->where('tc.tenant_id', $user->tenant_id);

            if ($mode == 'edit') {
                $this->db->where('tc.comp_status !=', 'INACTIV');
            }

            $this->db->like('cm.company_name', $name_startsWith, 'both');
            $this->db->order_by('cm.company_name', 'ASC');
            $this->db->limit(200);
            $results = $this->db->get()->result();
            //echo $this->db->last_query();exit;
        }

        return $results;
    }
    /**
     * Internal Company Trainee list
     * @param type $name_startsWith
     * @param type $company_id
     * @return type
     */
    public function internal_company_trainee_list_autocomplete($name_startsWith, $company_id) {
        $user = $this->session->userdata('userDetails');
        $results = array();
        if (!empty($name_startsWith)) {
            $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code_type, tu.tax_code, tu.other_identi_type, tu.other_identi_code');
            $this->db->from('tenant_company_users tcu');
            $this->db->join('tms_users tu', 'tu.user_id=tcu.user_id');
            $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
            $this->db->where('tup.tenant_id', $user->tenant_id);
            $this->db->where('tu.account_type', 'TRAINE');
            $this->db->where('tcu.company_id', $company_id);
            $this->db->like('tup.first_name', $name_startsWith, 'after');
            $this->db->order_by('tup.first_name', 'ASC');
            $results = $this->db->get()->result();
        }
        return $results;
    }

    public function internal_company_trainee_taxcode_autocomplete($name_startsWith, $company_id) {
        $user = $this->session->userdata('userDetails');
        $results = array();
        if (!empty($name_startsWith)) {
            $this->db->select('tu.tax_code, tup.first_name, tup.last_name');
            $this->db->from('tenant_company_users tcu');
            $this->db->join('tms_users tu', 'tu.user_id=tcu.user_id');
            $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
            $this->db->where('tup.tenant_id', $user->tenant_id);
            $this->db->where('tu.account_type', 'TRAINE');
            $this->db->where('tcu.company_id', $company_id);
            $this->db->like('tu.tax_code', $name_startsWith, 'after');
            $this->db->order_by('tu.tax_code', 'ASC');
            $results = $this->db->get()->result();
            return $results;
        }
        return $results;
    }

    /**
     * This function is used to get the number of registered users based on tenant id and company id
     * @param type $tenant_id
     * @param type $company_id
     * @return type
     */
    public function get_company_registered_trainees_num($tenant_id, $company_id) {
        $this->db->select('COUNT(*) AS registered_trainees_num');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tms_users tu', 'tu.user_id=tcu.user_id');
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tcu.company_id', $company_id);
        $this->db->where('tu.account_type', 'TRAINE');
        $result = $this->db->get()->result();
        return $result[0]->registered_trainees_num;
    }

    /**
     * This function is used to get the number of active users based on tenant id and company id 
     * @param type $tenant_id
     * @param type $company_id
     * @return type
     */
    public function get_company_active_trainees_num($tenant_id, $company_id) {
        $this->db->select('COUNT(*) AS active_trainees_num');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tms_users tu', 'tu.user_id=tcu.user_id');
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tcu.company_id', $company_id);
        $this->db->where('tu.account_type', 'TRAINE');
        $this->db->where('tu.account_status', 'ACTIVE');
        $result = $this->db->get()->result();
        return $result[0]->active_trainees_num;
    }

    /**
     * unction used to update company datas
     * @param type $company_id
     * @return type
     */
    public function update_company_details($company_id) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        extract($_POST);

        $curr_date_time = date('Y-m-d H:i:s');
        $acct_activation_date_time = '0000-00-00 00:00:00';
        if ($activate_company == 'ACTIVE') {
            $acct_activation_date_time = $curr_date_time;
        }

        if ($country_of_residence != 'SGP') {
            $comp_scn = '';
        }
       $companydata = array(
            'company_name' => $company_name,
            'comp_regist_num' => $regno,
            'business_type' => $business_type,
            'business_size' => $business_s,
            'comp_phone' => $phoneno,
            'comp_fax' => strtoupper($faxno),
            'comp_address' => $street,
            'comp_city' => $city,
            'comp_state' => $pers_states,
            'comp_cntry' => $company_country,
            'comp_zip' => strtoupper($zipcode),
            'remarks' => $comments,
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $curr_date_time,
            'comp_scn' => $comp_scn,
            'comp_cntry_scn' => $country_of_residence,
            'sme_nonsme' => $sme_type,
            'comp_attn' => strtoupper($comp_attn),
            'comp_email' => $comp_email,
        );
        if ($activate_company == 'ACTIVE' || $activate_company == 'PENDACT') {
            $companydata['company_status'] = $activate_company;
        }
        if ($activate_company == 'ACTIVE') {
            $companydata['acct_activation_date_time'] = $acct_activation_date_time;
        }
        $this->db->trans_start();
        $this->db->where('company_id', $company_id);
        $this->db->update('company_master', $companydata);

        $tenantcompany = array(
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $curr_date_time,
        );
        if ($activate_company == 'ACTIVE' || $activate_company == 'PENDACT') {
            $tenantcompany['comp_status'] = $activate_company;
        }
        if ($activate_company == 'ACTIVE') {
            $tenantcompany['acct_acti_date_time'] = $acct_activation_date_time;
        }
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('company_id', $company_id);
        $this->db->update('tenant_company', $tenantcompany);
 
        $account_type = 'COMUSR';
        $registration_date = $curr_date_time;
        $registration_mode = 'INTUSR';

        $acc_activation_type = 'BPEMAC';

        for ($i = 0; $i < count($username); $i++) {
            if ($username_userids[$i] != '') {
                $tms_users_data = array(
                    'registered_email_id' => $email_01[$i],
                    'last_modified_by' => $user->user_id,
                    'last_modified_on' => $curr_date_time,
                );
                if ($activate_company == 'ACTIVE' || $activate_company == 'PENDACT') {
                    $tms_users_data['account_status'] = $activate_company;
                }
                if ($activate_company == 'ACTIVE') {
                    $tms_users_data['acct_acti_date_time'] = $acct_activation_date_time;
                }
                $encrypted_password = NULL;
                $password = NULL;
                $emailId_change = FALSE;
                if ($activate_company == 'ACTIVE'){
                    if ($username_status[$i] == '' || $username_status[$i] == 'PENDACT') {  
                        $password = random_key_generation();
                        $encrypted_password = $this->bcrypt->hash_password($password);
                        $tms_users_data['password'] = $encrypted_password; 
                    }
                    if ( $username_status[$i] == 'ACTIVE'){
                        if($email_01_hidden[$i] != $email_01[$i])
                            $emailId_change = TRUE;
                        
                        $password = random_key_generation();
                        $encrypted_password = $this->bcrypt->hash_password($password);
                        $tms_users_data['password'] = $encrypted_password; 
                    }
                }

                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('user_id', $username_userids[$i]);
                $this->db->update('tms_users', $tms_users_data);
                $tms_users_pers_data = array(
                    'first_name' => $fname[$i],
                    'last_name' => "",
                    'gender' => $gender[$i],
                    'contact_number' => $contactno[$i],
                    'alternate_contact_number' => $mobileno[$i],
                    'alternate_email_id' => $email_02[$i],
                );
                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('user_id', $username_userids[$i]);
                $this->db->update('tms_users_pers', $tms_users_pers_data);
               $tenant_Comp_users = array(
                    'user_acct_status' => $activate_company,
                    'last_modified_by' => $user->user_id,
                    'last_modified_on' => $curr_date_time,
                );
                if ($activate_company == 'ACTIVE' || $activate_company == 'PENDACT') {
                    $tenant_Comp_users['user_acct_status'] = $activate_company;
                }
                if ($activate_company == 'ACTIVE') {
                    $tenant_Comp_users['acct_acti_date_time'] = $acct_activation_date_time;
                }
                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('user_id', $username_userids[$i]);
                $this->db->where('company_id', $company_id);
                $this->db->update('tenant_company_users', $tenant_Comp_users);
                if ($activate_company == 'ACTIVE' && ($username_status[$i] == '' 
                        || $username_status[$i] == 'PENDACT' 
                        || $emailId_change)) {  
                        $user_details = array('username' => $username[$i],
                        'email' => $email_01[$i], 'password' => $password,
                        'firstname' => $fname[$i], 'lastname' => $lname[$i],
                        'gender' => $gender[$i]);
                    $this->compnay_user_send_mail($user_details,$company_name);
                }
            }
        }
        for ($i = 0; $i < count($username); $i++) {
            if ($username_userids[$i] == '') {
                $tms_users_data = array(
                    'tenant_id' => $tenant_id,
                    'account_type' => $account_type,
                    'registration_mode' => $registration_mode,
                    'registration_date' => $registration_date,
                    'user_name' => $username[$i],
                    'registered_email_id' => $email_01[$i],
                    'account_status' => $activate_company,
                    'created_by' => $user->user_id,
                    'created_on' => $curr_date_time,
                    'last_modified_by' => $user->user_id,
                    'last_modified_on' => $curr_date_time,
                    'acc_activation_type' => 'BPEMAC',
                );
                if ($activate_company == 'ACTIVE') {
                    $tms_users_data['acct_acti_date_time'] = $acct_activation_date_time;
                }
                $encrypted_password = NULL;
                if ($activate_company == 'ACTIVE' ) { 
                    if ( $username_status[$i] == '' || $username_status[$i] == 'PENDACT'){
                       $password = random_key_generation();
                       $encrypted_password = $this->bcrypt->hash_password($password);
                       $tms_users_data['password'] = $encrypted_password; 
                    }
                }

                $this->db->insert('tms_users', $tms_users_data);
                $user_id = $this->db->insert_id();
               $tms_users_pers_data = array(
                    'tenant_id' => $tenant_id,
                    'user_id' => $user_id,
                    'first_name' => $fname[$i],
                    'last_name' => $lname[$i],
                    'gender' => $gender[$i],
                    'contact_number' => $contactno[$i],
                    'alternate_contact_number' => $mobileno[$i],
                    'alternate_email_id' => $email_02[$i],
                );
                $this->db->insert('tms_users_pers', $tms_users_pers_data);
               $internal_user_role_data = array(
                    'tenant_id' => $tenant_id,
                    'user_id' => $user_id,
                    'role_id' => 'COMPACT'
                );
                $this->db->insert('internal_user_role', $internal_user_role_data);
               $tenant_Comp_users = array(
                    'company_id' => $company_id,
                    'tenant_id' => $tenant_id,
                    'user_id' => $user_id,
                    'user_acct_status' => $activate_company,
                    'assigned_by' => $user->user_id,
                    'assigned_on' => $curr_date_time,
                    'last_modified_by' => $user->user_id,
                    'last_modified_on' => $curr_date_time,
                );
                if ($activate_company == 'ACTIVE') {
                    $tenant_Comp_users['acct_acti_date_time'] = $acct_activation_date_time;
                }
                $this->db->insert('tenant_company_users', $tenant_Comp_users);
               if ($activate_company == 'ACTIVE' && ($username_status[$i] == '' || $username_status[$i] == 'PENDACT')) {   // Was pending activation has now been changed to ACTIVE
                    //Send mail with Username and password.
                    $user_details = array('username' => $username[$i],
                        'email' => $email_01[$i], 'password' => $password,
                        'firstname' => $fname[$i], 'lastname' => $lname[$i],
                        'gender' => $gender[$i]);
                    $this->compnay_user_send_mail($user_details,$company_name);
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('company_db_error', 'Oops! Sorry, it looks like something went wrong and an error has occurred');
            redirect('company');
        }
       return;
    }
     public function company_discount($company_id)
    {   $zero = 0;
        $this->db->select('*');
        $this->db->from('company_discount');
        $this->db->where('Company_ID',$company_id);
         $where = '(Discount_percent!=0 or Discount_Amount!=0)';
//        $this->db->where('Discount_percent !=',$zero);
//        $this->db->where('Discount_Amount !=',$zero);
         $this->db->where($where);
        $sql = $this->db->get();
//        echo $this->db->last_query();
        if($sql->num_rows()>0){
            
            foreach($sql->result_array() as $row){
//                echo $row['Discount_Percent']; echo "<br/>";
                $data[] = array('discount'=>$row['Course_ID'].'_'.$row['Discount_Percent'].'#'.$row['Discount_Amount']);
            }

            return $data;
            
        }  else {
        return 0;    
        }
        
    }
    /**
     * Deactivate company contact
     * @param type $tenant_id
     * @param type $company_id
     * @param type $deactivate_user_id
     * @param type $deactivate_reason
     * @param type $deactivate_other_reason
     */
    public function deactivate_company_contact($tenant_id, $company_id, $deactivate_user_id, $deactivate_reason, $deactivate_other_reason) {
        $user = $this->session->userdata('userDetails');
        $curr_date_time = date('Y-m-d H:i:s');
       $tms_users_deactivate_data = array(
            'account_status' => 'INACTIV',
            'deacti_by' => $user->user_id,
            'deacti_reason' => $deactivate_reason,
            'acct_deacti_date_time' => $curr_date_time
        );
        if ($deactivate_reason == 'OTHERS') {
            $tms_users_deactivate_data['deacti_reason_oth'] = strtoupper($deactivate_other_reason);
        }
        $this->db->trans_start();
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('user_id', $deactivate_user_id);
        $this->db->update('tms_users', $tms_users_deactivate_data);

        $tenant_comp_users_deactivate = array(
            'user_acct_status' => 'INACTIV',
            'deacti_by' => $user->user_id,
            'acct_deacti_date_time' => $curr_date_time,
            'deacti_reason' => $deactivate_reason,
        );
        if ($deactivate_reason == 'OTHERS') {
            $tenant_comp_users_deactivate['deacti_reason_oth'] = strtoupper($deactivate_other_reason);
        }
        $this->db->where('company_id', $company_id);
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('user_id', $deactivate_user_id);
        $this->db->update('tenant_company_users', $tenant_comp_users_deactivate);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('company_db_error', 'Oops! Sorry, it looks like something went wrong and an error has occurred');
            redirect('company');
        }
    }
    
    /* this funcation get the all details of company for activity log skm start*/
    public function get_company_status_info($comp_id){
       
        $this->db->select('*');
        $this->db->from('company_master cm');
        $this->db->join('tenant_company tm','tm.company_id = cm.company_id');
        $this->db->where('cm.company_id',$comp_id);
        $sql = $this->db->get();
        $company_details = $sql->row_array();
        $company_details['user_details'] = $this->get_company_users_details($company_details['tenant_id'],$company_details['company_id']);
        return $company_details;
        
    }
    /* end */
    
    /**
     * deactivate company contact
     * @param type $tenant_id
     * @param type $company_id
     * @param type $reason_for_deactivation
     * @param string $other_reason_for_deactivation
     */
    public function company_deactivate($tenant_id, $company_id, $reason_for_deactivation, $other_reason_for_deactivation) {
        $user = $this->session->userdata('userDetails');
        $logged_in_user_id = $user->user_id;
        $deactivation_date = date('Y-m-d H:i:s');

        if ($reason_for_deactivation != 'OTHERS') {
            $other_reason_for_deactivation = '';
        }

        $tenant_company_deactivate = array(
            'comp_status' => 'INACTIV',
            'acct_deacti_date_time' => $deactivation_date,
            'deacti_reason' => $reason_for_deactivation,
            'deacti_by' => $logged_in_user_id,
            'deacti_reason_oth' => strtoupper($other_reason_for_deactivation),
            'last_modified_by' =>$logged_in_user_id, 
            'last_modified_on' =>$deactivation_date
        );
        $this->db->trans_start();
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('company_id', $company_id);
        $this->db->update('tenant_company', $tenant_company_deactivate);
       $company_master_array = array(
            'last_modified_by' =>$logged_in_user_id,
            'last_modified_on' =>$deactivation_date
        );
        $this->db->where('company_id', $company_id);
        $this->db->update('company_master', $company_master_array);
        $tenant_company_users_deactivate = array(
            'user_acct_status' => 'INACTIV',
            'acct_deacti_date_time' => $deactivation_date,
            'deacti_reason' => $reason_for_deactivation,
            'deacti_by' => $logged_in_user_id,
            'deacti_reason_oth' => strtoupper($other_reason_for_deactivation),
            'last_modified_by' =>$logged_in_user_id, 
            'last_modified_on' =>$deactivation_date
        );
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('company_id', $company_id);
        $this->db->update('tenant_company_users', $tenant_company_users_deactivate);
       $sql = "UPDATE tms_users tu, tenant_company_users tcu  
                SET tu.account_status='INACTIV',
                tu.acct_deacti_date_time='$deactivation_date',
                tu.deacti_reason = '$reason_for_deactivation',
                tu.deacti_reason_oth = '$other_reason_for_deactivation',
                tu.deacti_by='$logged_in_user_id'                 
                 WHERE  tcu.tenant_id='$tenant_id' 
                AND tcu.company_id='$company_id' 
                AND tu.user_id=tcu.user_id 
                AND tu.account_type != 'TRAINE'";

        $this->db->query($sql);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('company_db_error', 'Oops! Sorry, it looks like something went wrong and an error has occurred');
            redirect('company');
        }
    }

    /**
     * To get company trainees count
     * @param type $tenant_id
     * @param type $company_id
     * @return int
     */
    public function get_company_trainees_count($tenant_id, $company_id) {

        if (empty($tenant_id)) {
            return 0;
        }
        $this->db->select('count(*) as totalrows');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tms_users tu', "tu.user_id=tcu.user_id AND tu.account_type='TRAINE'");
        $this->db->join('tms_users_pers tup', "tup.user_id=tu.user_id");
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tcu.company_id', $company_id);
       //$query = $this->db->return_query(); // commented by shubhranshu for page loading issue for view company
        $query = $this->db->get_compiled_select();// added by shubhranshu for page loading issue for view company
        $rs = $this->db->query($query)->result();
        
        //--------------------added by shubhranshu on 22/07/19 to fix the generate query issue------------------------------
        $this->db->select('count(*) as totalrows');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tms_users tu', "tu.user_id=tcu.user_id AND tu.account_type='TRAINE'");
        $this->db->join('tms_users_pers tup', "tup.user_id=tu.user_id");
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tcu.company_id', $company_id);
        //----------------------added by shubhranshu on 22/07/19----------------------------
        
        $search_by = $this->input->get('search_by');
        $search_company_trainee_name = trim($this->input->get('search_company_trainee_name'));
        $search_company_trainee_name_arr = explode(' (', $search_company_trainee_name);
       $search_company_trainee_taxcode = trim($this->input->get('search_company_trainee_taxcode'));
        $search_company_trainee_taxcode_arr = explode(' (', $search_company_trainee_taxcode);
       if ($search_by == 'trainee_name') {
            $search_trainee_full_name = $search_company_trainee_name_arr[0];
            $search_trainee_tax_code = trim($search_company_trainee_name_arr[1], ')');
          if ($search_trainee_tax_code != '') {
                $this->db->like('tu.tax_code', $search_trainee_tax_code);
            }
          if ($search_trainee_tax_code == '') {
                $this->db->like('tup.first_name', $search_trainee_full_name, 'after');
            }
        }
       if ($search_by == 'tax_code') {
            if ($search_company_trainee_taxcode_arr[0] != '') {
                $this->db->like('tu.tax_code', $search_company_trainee_taxcode_arr[0]);
            }
        }
        //$result = $this->db->get_compiled_select();echo $result;exit;
         $result = $this->db->get()->result();
       return array('totalrows' => $result[0]->totalrows, 'totalrows_no_search' => $rs[0]->totalrows);
    }

    /**
     * To get company trainees list
     * @param type $tenant_id
     * @param type $company_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @param type $gettotal
     * @return type
     */
    public function get_company_trainees_list($tenant_id, $company_id, $limit, $offset, $sort_by, $sort_order, $gettotal = NULL) {
       if ($offset <= 0 || empty($tenant_id)) {
            return;
        }
       $this->db->select('tu.user_id, DATE(tu.acct_acti_date_time) AS acct_acti_date_time, DATE(tu.registration_date) AS registration_date, tu.acc_activation_type, tu.country_of_residence, tu.tax_code, tu.tax_code_type, tu.other_identi_type, tu.other_identi_code, tup.first_name, tup.last_name, tup.dob, tu.registered_email_id, tup.contact_number, tup.personal_address_bldg, tup.personal_address_city, tup.personal_address_state, tup.personal_address_country, tup.personal_address_zip');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tms_users tu', "tu.user_id=tcu.user_id AND tu.account_type='TRAINE'");
        $this->db->join('tms_users_pers tup', "tup.user_id=tu.user_id");
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tcu.company_id', $company_id);
       $search_by = $this->input->get('search_by');
        $search_company_trainee_name = trim($this->input->get('search_company_trainee_name'));
        $search_company_trainee_name_arr = explode(' (', $search_company_trainee_name);
       $search_company_trainee_taxcode = trim($this->input->get('search_company_trainee_taxcode'));
        $search_company_trainee_taxcode_arr = explode(' (', $search_company_trainee_taxcode);
        if ($search_by == 'trainee_name') {
            $search_trainee_full_name = $search_company_trainee_name_arr[0];
            $search_trainee_tax_code = trim($search_company_trainee_name_arr[1], ')');
          if ($search_trainee_tax_code != '') {
                $this->db->like('tu.tax_code', $search_trainee_tax_code);
            }
           if ($search_trainee_tax_code == '') {
                $this->db->like('tup.first_name', $search_trainee_full_name, 'after');
            }
        }
       if ($search_by == 'tax_code') {
            if ($search_company_trainee_taxcode_arr[0] != '') {
                $this->db->like('tu.tax_code', $search_company_trainee_taxcode_arr[0]);
            }
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('tu.last_modified_on', 'DESC');
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
     * get company list who is having active users
     * @param type $tenant_id
     * @param type $active_enrollment
     * @return type
     */
    public function get_activeuser_companies_for_tenant($tenant_id, $active_enrollment = 0) {
        $this->db->cache_on();
        $this->db->select('cm.company_id');
        $this->db->select('cm.company_name');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tenant_company tc', 'tc.company_id=tcu.company_id');
        $this->db->join('company_master cm', 'cm.company_id=tc.company_id');
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tc.tenant_id', $tenant_id);
        $this->db->where('tcu.user_acct_status', 'ACTIVE');
        $this->db->where('tc.comp_status', 'ACTIVE');
        if ($active_enrollment == 1) {
            $this->db->join('class_enrol ce', 'ce.company_id=cm.company_id');
        }
        if ($this->user->company_id != '') {
            $this->db->where('cm.company_id', $this->user->company_id);
        }
        $this->db->group_by('tcu.company_id');
        $this->db->order_by("cm.company_name"); 
        return $this->db->get()->result();
    }

    /**
     * get company list for tenant id
     * @param type $tenant_id
     * @return type
     */
    public function get_companies_for_tenant($tenant_id) {
        $this->db->select('cm.company_id');
        $this->db->select('cm.company_name');
        $this->db->from('tenant_company tc');
        $this->db->join('company_master cm', 'cm.company_id=tc.company_id');
        $this->db->where('tc.tenant_id', $tenant_id);
        $this->db->where('tc.comp_status', 'ACTIVE');
        return $this->db->get()->result();
    }

    /**
     * function to get company trainees with removing class
     * @param type $tenant_id
     * @param type $company_id
     * @param type $class
     * @return type
     */
    public function get_company_trainees($tenant_id, $company_id, $class) {
        if ($class) {
            $this->db->select('user_id');
            $this->db->from('class_enrol');
            $this->db->where('class_id', $class);
            $class_have = $this->db->get()->result();
        }
        $class_have_arr = array();
        foreach ($class_have as $row) {
            $class_have_arr[] = $row->user_id;
        }
        $this->db->select('tu.user_id, tup.first_name, tup.last_name, tu.tax_code');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('tms_users tu', "tu.user_id=tcu.user_id AND tu.account_type='TRAINE'");
        $this->db->join('tms_users_pers tup', "tup.user_id=tu.user_id");
        $this->db->where('tcu.tenant_id', $tenant_id);
        $this->db->where('tcu.user_acct_status', 'ACTIVE');
        $this->db->where('tu.account_status', 'ACTIVE');
        $this->db->where('tcu.company_id', $company_id);
        $this->db->order_by('tup.first_name','ASC');
        if (!empty($class_have_arr)) {
            $this->db->where_not_in('tu.user_id', $class_have_arr);
        }
       return $this->db->get()->result();
    }
    /**
     * function to get company users with removing class
     * @param type $tenant_id
     * @param type $company_id
     * @param type $class
     * @return type
     */
    public function get_tenent_active_users($tenant_id, $company_id, $class) {
        if ($class) {
            $this->db->select('user_id');
            $this->db->from('class_enrol');
            $this->db->where('class_id', $class);
            $class_have = $this->db->get()->result();
        }
        $class_have_arr = array();
        foreach ($class_have as $row) {
            $class_have_arr[] = $row->user_id;
        }        
        $this->db->select('tu.user_id, tup.first_name, tup.last_name, tu.tax_code');
        $this->db->from('tms_users tu');        
        $this->db->join('tms_users_pers tup', "tup.user_id=tu.user_id");
        $this->db->where('tu.tenant_id', $tenant_id);        
        $this->db->where('tu.account_status', 'ACTIVE');        
        $this->db->where('tu.account_type', 'INTUSR');       
        $this->db->order_by('tup.first_name','ASC');
         if (!empty($class_have_arr)) {
            $this->db->where_not_in('tu.user_id', $class_have_arr);
        }

        return $this->db->get()->result();
    }
   /**
    * This method for checking number_trainees_payment_pending for company deactivation.
    * @param type $company_id
    * @return boolean
    */
    public function number_trainees_payment_pending($company_id) {
        if ($company_id) {
            $this->db->select("count(user_id) as pending");
            $this->db->from("class_enrol");
            $this->db->where("company_id", $company_id);
            $this->db->where_in("payment_status", array("NOTPAID", "PARTPAID"));
            $this->db->where_in("enrol_status", array("ENRLBKD", "ENRLACT"));
            $result = $this->db->get();
            return $result->row("pending");
        } else {
            return FALSE;
        }
    }

    /**
     * method to reactivate company
     * @param type $company_id
     * @param type $reactivate_reason
     * @param type $other_reason
     * @return boolean
     */
    public function reactivate_company($company_id, $reactivate_reason, $other_reason) {
        $user = $this->session->userdata('userDetails');
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
        $tenant_company_data = $common_data;
        $tenant_company_data['comp_status'] = 'ACTIVE';
        $this->db->trans_start();
        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('company_id', $company_id);
        $this->db->update('tenant_company', $tenant_company_data);
         $company_master_array = array(         
            'last_modified_by' => $user->user_id,
            'last_modified_on' => $cur_date,  
        );
        $this->db->where('company_id', $company_id);
        $this->db->update('company_master', $company_master_array);
         $tenant_company_users_data = $common_data;
        $tenant_company_users_data['user_acct_status'] = 'ACTIVE';
        $this->db->where('tenant_id', $user->tenant_id);
        $this->db->where('company_id', $company_id);
        $this->db->update('tenant_company_users', $tenant_company_users_data);
        $tms_users_data = array(
            'tms_users.last_modified_by' => $user->user_id,
            'tms_users.last_modified_on' => $cur_date,
            'tms_users.reactivation_date_time' => $cur_date,
            'tms_users.reactivated_by' => $user->user_id,
            'tms_users.reactivation_reason_id' => $reactivate_reason,
            'tms_users.reactivation_reason_others' => strtoupper($other_reason),
            'tms_users.account_status'=>'ACTIVE'
        );
        $this->db->set($tms_users_data);
        $this->db->where('tenant_company_users.tenant_id', $user->tenant_id);
        $this->db->where('tenant_company_users.company_id', $company_id);
        $this->db->where('tms_users.account_type !=', 'TRAINE');
        $this->db->update('tms_users JOIN tenant_company_users ON tms_users.user_id = tenant_company_users.user_id');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * update company discount
     * @param type $tenant_id
     * @return boolean
     */
    public function update_companydiscount($tenant_id){
        $companyid = $this->input->post('companyid');
    
       /* activity log start skm */
        $comp = array();
        $comp ['company_id'] =  $companyid;
        $comp ['company_discount'] = $this->company_discount($companyid);
        if($comp ['company_discount']!=0){
        $previous_comp_discount = json_encode($comp);}
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
                $this->db->where('Company_ID', $companyid);
                $this->db->where_in('Course_ID',$delete_courses);
                $this->db->delete('company_discount');
            }
        }else{
            $this->db->where('Company_ID', $companyid);
            $this->db->delete('company_discount');
        }
        foreach ($disc_perc as $k => $row) {
            if(!empty($row) || !empty($disc_amt[$k])){
                $data[] = array(
                    'Tenant_ID' => $tenant_id,
                    'Company_ID' => $companyid,
                    'Course_ID' => $k,
                    'Discount_Percent' => $row,
                    'Discount_Amount' => $disc_amt[$k]
                );
            }
        }
        $this->db->insert_batch('company_discount', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            if($comp ['company_discount']!=0){
            user_activity(17,$companyid,$previous_comp_discount,2);}
            return TRUE;
        }
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
    
    public function get_company_user($tenant_id, $company_id){
        $this->db->select('tup.first_name');
        $this->db->from('tenant_company company');
        $this->db->join('company_master cm', 'company.company_id = cm.company_id');
        $this->db->join('tenant_company_users tcu', 'tcu.company_id = cm.company_id');
        $this->db->join('tms_users tu','tu.user_id = tcu.user_id'); 
        $this->db->join('tms_users_pers tup','tup.user_id = tu.user_id'); 
        $this->db->where('tu.account_type','COMUSR'); 
        $this->db->where('company.tenant_id', $tenant_id); 
        $this->db->where('company.company_id', $company_id); 
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result();
        
        
        
    }

}

