<?php

/*

 * Model class for Course List   

 */



class User_Model extends CI_Model {

    



    /**

     * Validate the email Id and DOB and get the username and password

     * @param type $forgot_param

     * @param type $email_id

     * @param type $dob  

     */

    

    public function __construct() {

        parent::__construct();       

        $this->load->library('bcrypt');

        $this->load->helper('common');
       


    }

    function configurationfunction($uname){
        $tenentName = "";
        $domain = str_replace("www.", "", $_SERVER ["HTTP_HOST"]);
        $exploded = explode('.', $domain);
        $mainDomain = str_replace($exploded[0], "", $_SERVER ["HTTP_HOST"]);
        $gd = count($exploded);
       
        $segmentCount = '2';
       
        // collect tenant name from url
        $tenentName =  $gd  > $segmentCount ? $exploded[0] : DEFAULT_TENANT;
//        print_r($this->db->database);
        // fetch tenent id based on tenant name
        ///////////below code added by shubhranshu to check if the trainee is created by testadmin
        $this->db->select('tenant_id');
        $this->db->from('tms_users');
        $this->db->where('user_name', $uname);
        $this->db->limit(1);
        $res = $this->db->get()->row()->tenant_id;////////////////////////////
         //echo $this->db->last_query();exit;
        if($res!= 'T01'){   /////////////////////added by shubhranshu for if the trainee is not created by testadmin
            $this->db->select('tenant_id');
            $this->db->from('tenant_master');
            $this->db->where('tenant_short_name', $tenentName);
            $this->db->limit(1);
    //        print_r($this->db->get()->row());exit;
            $res = $this->db->get()->row()->tenant_id;
        }////////////////////////ssp///////////////////////////////////////
        
//        echo $this->db->last_query();exit;
        // if the tenent name doesnot exist in db redirect to default tenant        
        if(empty($res))
           //redirect(DEFAULT_TENANT);
	  // redirect ("http://www.biipmi.com");
           redirect ("/");
        
        //rename cookie according to tenant so that session data are domain specific
        $this->config->set_item('cookie_prefix', $res);
        
        // define tenant id 
        
        define('TENANT_ID_PUB', $res);
 
    }

    public function check_public_user_valid() {

        extract($_POST);

//        print_r(extract($_POST));

//        exit;

         ///$uname = mysql_real_escape_string($username);
        // $pwd = mysql_real_escape_string($password);
        $uname = trim($username);
        $pwd = trim($password);
   
       // $this->configurationfunction($uname);// added by shubhranshu for dynamic teanant_id
        

        $this->db->select('usr.password, pers.first_name, pers.last_name, pers.tenant_id, usr.user_id, ten.logo, ten.copyrighttext, '

                . 'ten.currency, ten.country,ten.applicationname');

        $this->db->from('tms_users_pers pers');

        $this->db->join('tms_users usr', 'usr.tenant_id = pers.tenant_id and usr.user_id = pers.user_id');

        $this->db->join('tenant_master ten', 'usr.tenant_id=ten.tenant_id');

        $this->db->where('usr.account_type', TRAINE);

        $this->db->where('usr.user_name', $uname);

//        $this->db->where('user.password', $pwd);

        $this->db->where('usr.account_status', ACTIVE);

        $this->db->where('usr.tenant_id', TENANT_ID);

        $result = $this->db->get()->row();

//       echo $this->db->last_query();exit;

//        

//        $res = $this->bcrypt->check_password($password, $result->password);

//        echo $res; echo "<br/>";

//        print_r($res); "<br/>";

//        echo $result->password;

//         exit;       

        if ($this->bcrypt->check_password($password, $result->password)) {

            unset($result->password);

            return $result;

        } else {

            return FALSE;

        }

    }

    

    /* skm -> login automatically when user slecet course and class and register and enroll in class successfully*/

    public function register_login_process($uid) {
	


        //$uid = is_numeric($uid);

 

        $this->db->select('pers.first_name, pers.last_name, pers.tenant_id, usr.user_id, ten.logo, ten.copyrighttext, '

                . 'ten.currency, ten.country,ten.applicationname');

        $this->db->from('tms_users_pers pers');

        $this->db->join('tms_users usr', 'usr.tenant_id = pers.tenant_id and usr.user_id = pers.user_id');

        $this->db->join('tenant_master ten', 'usr.tenant_id=ten.tenant_id');

        $this->db->where('usr.user_id', $uid);

        $this->db->where('usr.account_type', TRAINE);

        $this->db->where('usr.account_status', ACTIVE);

        $this->db->where('usr.tenant_id', TENANT_ID);

        $result = $this->db->get()->row();

        

//        

        return $result;

    }//end


    public function friend_name($user_id,$frnd_id,$class_id){

        $this->db->select('ce.friend_id,tu.user_id,tup.first_name');
        $this->db->from('class_enrol ce');
        $this->db->join('tms_users tu', 'tu.tenant_id = ce.tenant_id and ce.friend_id = tu.user_id');
        $this->db->join('tms_users_pers tup', 'tup.tenant_id = tu.tenant_id and tup.user_id = tu.user_id');
        $this->db->where('ce.user_id',$user_id);
        $this->db->where('ce.friend_id',$frnd_id);
        $this->db->where('ce.class_id',$class_id);
        $this->db->where('ce.tenant_id',TENANT_ID);
        $this->db->where('tu.tenant_id',TENANT_ID);
        $result = $this->db->get()->row();
        return $result->first_name;
    }
    
    public function refferal_friend_name($user_id,$class_id){
        $this->db->select('ce.referral_details');
        $this->db->from('class_enrol ce');
        $this->db->where('ce.user_id',$user_id);
        $this->db->where('ce.class_id',$class_id);
        $result = $this->db->get()->row();
        $name = json_decode($result->referral_details);
        return $name->name;  
    } 


    

     /**

     * This method generates the mail body for sending user name

     * @param type $user_name

     * @param type $first_name

     * @param type $last_name

     * @param type $gender

     */

    private function get_mail_body($data, $first_name, $last_name, $gender, $forgot_param) {

        if ($gender == 'MALE') {

            $mail_body = "Dear Mr." . $first_name;

        } else if ($gender == 'FEMALE') {

            $mail_body = "Dear Ms." . $first_name;

        }else {

            $mail_body = "Dear " . $first_name;

        }

        $mail_body.= "<br/>Your $forgot_param for TMS login is: " . $data . "<br/> Thanks <br/> TMS Administrator";

        return $mail_body;

    }

    public function get_user_details($taxcode)
    {   
       $this->db->select('*');
        $this->db->from('tms_users tu');
        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');
        $this->db->where('tu.tax_code',$taxcode);
        $this->db->where('tu.tenant_id',TENANT_ID);
       return $sql = $this->db->get()->row();
        // $this->db->last_query();
       
    }

    /*

     * This function is used to populate the state list.    

     */



    public function get_states($country_param) {

        $sql = $this->db->where('parameter_id', $country_param)->get('metadata_values')->row();

        if ($sql->child_category_id) {

            $query = $this->db->where('category_id', $sql->child_category_id)->get('metadata_values');

            return $query->result();

        } else {

            $querys = $this->db->where('parameter_id', $country_param)->get('metadata_values');

            return $querys->result();

        }

    }



    /*

     * This function will check the username exists on the db or not.    

     */



    public function check_username($user_name) {

        $this->db->select('user_id');

        $this->db->from('tms_users');

        $this->db->where('user_name', $user_name);

        $query = $this->db->get();

        return $query->num_rows();

    }



    /*

     * This function will check the email exists on the db or not.  

     */



    public function check_email($email) {

        $this->db->select('user_id');

        $this->db->from('tms_users');

        $this->db->where('registered_email_id', $email);

        $query = $this->db->get();
//        echo $this->db->last_query();
        return $query->num_rows();

    }

 

    /*

     * This function will check the taxcode exists on the db or not.

     */



    public function check_taxcode($tax_code) {

        $this->db->select('user_id');

        $this->db->from('tms_users');

        $this->db->where('tax_code', $tax_code);

         $this->db->where('tenant_id',TENANT_ID);

         $query = $this->db->get();

        return $query->num_rows();

    }



    

     public function validate_taxcode($tax_code) { 

        $this->db->select('user_id, tenant_id');

        $this->db->from('tms_users');

        $this->db->where('tax_code', $tax_code);

        $this->db->where('tenant_id',TENANT_ID); 

        $query = $this->db->get();

        return $query->result();

    }

    

    /*

     * checks if user tax code already exists (ADD user)

     */

    public function check_duplicate_user_taxcode($tax_code) {

        $exists = $this->db->select('tax_code')->get_where('tms_users', array('tax_code' => $tax_code, 'tenant_id' =>TENANT_ID ), 1)->num_rows();

        if ($exists) {

            return FALSE;

        }

        return TRUE;

    }

    /**

     * This method check duplicate user_name

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

    

    // skm course_name start

    public function course_name($course_id)

    {   

        $this->db->select('crse_name');

        $this->db->from('course');

        $this->db->where('course_id',$course_id);

        $this->db->where('tenant_id',TENANT_ID);

         $sql = $this->db->get()->row();  

//         echo $this->db->last_query();

//         echo $sql->course_name;

        return $sql->crse_name;

    }//end
    
     public function check_tmsuser_taxcode($taxcode)
    {
        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

        $this->db->where('tu.tax_code',$taxcode);
        
        //$this->db->where('tup.tenant_id',TENANT_ID);

        $sql = $this->db->get()->row();

        return $sql->user_id;

    }
    
    /* Fetch user details skm start*/
    public function existing_userDetails($user_id)
    {   
        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

        $this->db->where('tu.user_id',$user_id);

        $this->db->where('tup.tenant_id',TENANT_ID);

        return $sql = $this->db->get()->row();


    }//end



   

//  skm functions for save trainee for enroll some one start

    

    public function r_userDetails($userId)

    {   

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

        $this->db->where('tu.user_id',$userId);

        $this->db->where('tup.tenant_id',TENANT_ID);

        return $sql = $this->db->get()->row();



       

    }

    

    public function check_tmsuser_email($username,$col)

    {

        $this->db->select('user_name,registered_email_id,user_id');

        $this->db->from('tms_users');

        $this->db->where('user_name',$username);

        $this->db->where('registered_email_id',$col);

        $sql = $this->db->get()->row();

        return $sql->user_id;

        

    }



        

    public function check_tmsuser_name($username,$col)

    {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

        $this->db->where('tu.user_name',$username);

        $this->db->where('tup.first_name',$col);

        $sql = $this->db->get()->row();

//        echo $this->db->last_query();

        return $sql->user_id;

        

    }

    

    public function check_tms_contact_no($username,$col)

    {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

        $this->db->where('tu.user_name',$username);

        $this->db->where('tup.contact_number',$col);

        $sql = $this->db->get()->row();

//        echo $this->db->last_query();

        return $sql->user_id;

        

    }

    

    public function check_tmsuser_dob($taxcode_nric,$col)

    {

//        $this->db->select('*');

//        $this->db->from('tms_users tu');

//        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

//        $this->db->where('tu.tax_code',$taxcode_nric);

//        $this->db->where('tup.dob',$col);

//        $sql = $this->db->get();

//        echo $this->db->last_query();

//        return $sql->num_rows();

        $this->db->select('*');

        $this->db->from('tms_users');

        $this->db->where('tax_code',$taxcode_nric);

        $sql = $this->db->get()->row();

//        echo $this->db->last_query();

        $result = $this->dob_referance($sql->user_id,$col);

        if($result == 1)

        {

             return $sql->user_id;

        }

        else {

            return FALSE;

        }

       

       

    }

    public function dob_referance($user_id,$dob)

    {

       $old_dob = explode("-",$dob);

       $new_dob = $old_dob[2].'-'. $old_dob[1].'-'.$old_dob[0];

       $this->db->select('*');

       $this->db->from('tms_users_pers');

       $this->db->where('user_id',$user_id);

       $this->db->like('dob',$new_dob);

       $sql=$this->db->get();

       return $sql->num_rows();

    }

    

    public function check_tmsuser_eemail($taxcode_nric,$col)

    {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->where('tu.tax_code',$taxcode_nric);

        $this->db->where('tu.registered_email_id',$col);

        $sql = $this->db->get()->row();

//        echo $this->db->last_query();

        return $sql->user_id;

    }

    

    public function check_tmsuser_econtact($taxcode_nric,$col)

    {

        $this->db->select('*');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup','tu.user_id = tup.user_id');

        $this->db->where('tu.tax_code',$taxcode_nric);

        $this->db->where('tup.contact_number',$col);

        $sql = $this->db->get()->row();

        return $sql->user_id;

        

    }



    

//  end  

    

    



    /*

     * function is used to save traninee registered data.  

     */

   public function save_trainee() {

        $user_id= $this->session->userdata('userDetails')->user_id;

         $user_registered_email = $this->input->post('frnd_registered_email');

        $tenant_id = TENANT_ID;

        foreach ($this->input->post() as $key => $value) {

            $$key = $value;

        }

        $dateTime = date('Y-m-d H:i:s');



        $other_identi_type = NULL;

        $other_identi_code = NULL;

        $friend_id = NULL;

        $friend_relation = NULL;

        $acc_activation_type = 'EMACT_RQ';

        $account_status = PENDACT;

        $created_by = '';

        $acct_acti_date_time = '';



        if ($country_of_residence == IND) {

            $tax_code_type = PAN;

            $tax_code = $PAN;

        }

        if ($country_of_residence == SGP) {

            $tax_code_type = $NRIC;

            $tax_code = $NRIC_ID;

           // if ($NRIC == OTHERS) { commented by sushil on 08-nov-16, beacause of it if we choose others then others value not going to the table.
            if ($NRIC == SNG_3) {

                $other_identi_type = $NRIC_OTHER;

                $other_identi_code = $tax_code;

            }

        }

        if ($country_of_residence == USA) {

            $tax_code_type = SSN;

            $tax_code = $SSN;

        }



        $password = random_key_generation();

        $encrypted_password = $this->bcrypt->hash_password($password);

        $activation_key = random_key_generation();

        // for  Refer a Friend or Family Registration.

        if (!empty($relationship)) {

            $friend_id = $this->session->userdata('userDetails')->user_id;

            $friend_relation = $relationship;

            $acc_activation_type = BPEMAC;

            $activation_key = '';

            $account_status = ACTIVE;

            $created_by = $friend_id;

            $acct_acti_date_time = $dateTime;

        }



        $tms_users_data = array(

            'tenant_id' => $tenant_id,

            'account_type' => TRAINE,

            'registration_mode' => TRAINE,

            'friend_id' => $friend_id,

            'registration_date' => $dateTime,

            'user_name' => $user_name,

            'tenant_org_id' => "",

            'password' => $encrypted_password,

            'acc_activation_type' => $acc_activation_type,

            'activation_key' => $activation_key,

            'registered_email_id' => trim($user_registered_email),

            'country_of_residence' => trim($country_of_residence),

            'tax_code_type' => $tax_code_type,

            'tax_code' => strtoupper($tax_code),

            'other_identi_type' => $other_identi_type,

            'other_identi_code' => strtoupper($other_identi_code),

            'other_identi_upload' => '',

            'acct_acti_date_time' => $acct_acti_date_time,

            'acct_deacti_date_time' => NULL,

            'account_status' => $account_status,

            'deacti_reason' => NULL,

            'deacti_reason_oth' => NULL,

            'deacti_by' => NULL,

            'created_by' => $created_by,

            'created_on' => $dateTime,

            'last_modified_by' => $created_by,

            'last_modified_on' => $dateTime,

            'last_login_date_time' => NULL,

            'last_ip_used' => NULL,

            'pwd_last_chgd_on' => NULL,

            'friend_relation' => $friend_relation,

        );

        $this->db->insert('tms_users', $tms_users_data);

        $user_id = $this->db->insert_id();

        $dob_date = ($pers_dob) ? date('Y-m-d', strtotime($pers_dob)) : NULL;

        $tms_users_pers_data = array(

            'tenant_id' => $tenant_id,

            'user_id' => $user_id,

            'first_name' => strtoupper($pers_first_name),

            'last_name' => strtoupper($pers_second_name),

            'gender' => $pers_gender,

            'dob' => $dob_date,

            'alternate_email_id' => trim($pers_alternate_email),

            'contact_number' => trim($pers_contact_number),

            'alternate_contact_number' => trim($pers_alternate_contact_number),

            'race' => trim($race),

            'salary_range' => trim($sal_range),

            'personal_address_bldg' => strtoupper(trim($pers_personal_address_bldg)),

            'personal_address_city' => strtoupper(trim($pers_city)),

            'personal_address_state' => trim($pers_states),

            'personal_address_country' => trim($pers_country),

            'personal_address_zip' => strtoupper(trim($pers_zip_code)),

            'photo_upload_path' => '',

            'individual_discount' => NULL,

            'certificate_pick_pref' => $certificate_pick_pref,

            'indi_setting_list_size' => NULL,

            'occupation_code' => $occupation,

            'highest_educ_level' => $highest_educ_level,

            'nationality' => $nationality,

        );

        $this->db->insert('tms_users_pers', $tms_users_pers_data);

        //Education Level   

        $edu = array();

        for ($i = 0; $i < count($edu_level); $i++) {

            if ($edu_level[$i]) {

                $edu[] = array(

                    'tenant_id' => $tenant_id,

                    'user_id' => $user_id,

                    'educ_id' => '', // auto increment.

                    'educ_level' => trim($edu_level[$i]),

                    'educ_yr_completion' => trim($edu_year_of_comp[$i]),

                    'educ_score' => trim($edu_score_grade[$i]),

                    'educ_remarks' => trim(strtoupper($edu_remarks[$i])),

                );

            }

        }

        if (!empty($edu)) {

            $this->db->insert_batch('tms_users_educ', $edu);

        }



        //Certification        

        $other = array();

        for ($i = 0; $i < count($oth_certi_name); $i++) {

            if ($oth_certi_name[$i]) {

                $other[] = array(

                    'tenant_id' => $tenant_id,

                    'user_id' => $user_id,

                    'othr_cert_id' => '', // auto increment.

                    'cert_name' => trim(strtoupper($oth_certi_name[$i])),

                    'yr_completion' => trim($oth_year_of_certi[$i]),

                    'valid_till' => ($oth_validity[$i]) ? date('Y-m-d', strtotime($oth_validity[$i])) : NULL,

                    'oth_remarks' => trim(strtoupper($oth_remarks[$i])),

                );

            }

        }

        if (!empty($other)) {

            $this->db->insert_batch('tms_users_othr_cert', $other);

        }

        //Work Experience 

        $work = array();

        for ($i = 0; $i < count($work_org_name); $i++) {

            if ($work_org_name[$i]) {

                $work[] = array(

                    'tenant_id' => $tenant_id,

                    'user_id' => $user_id,

                    'wrk_exp_id' => '', // auto increment.

                    'org_name' => trim(strtoupper($work_org_name[$i])),

                    'emp_from_date' => ($work_empfrom[$i]) ? date('Y-m-d', strtotime($work_empfrom[$i])) : NULL,

                    'emp_to_date' => ($work_empto[$i]) ? date('Y-m-d', strtotime($work_empto[$i])) : NULL,

                    'designation' => trim($work_designation[$i]),

                );

            }

        }

        if (!empty($work)) {

            $this->db->insert_batch('tms_users_wrk_exp', $work);

        }

        if (!empty($user_registered_email)) {

            $user_details = array('username' => $user_name,

                'email' => $user_registered_email, 'password' => $password,

                'firstname' => strtoupper($pers_first_name), 'lastname' => strtoupper($pers_second_name),

                'gender' => $pers_gender);

            if ($acc_activation_type == 'EMACT_RQ') {

                $user_details['link'] = admin_url() . '/tmspublic/activate_user/index/' . $user_id . '/' . md5($activation_key);

            }
            
            /* send email when refer a friend start skm */
           // $this->send_trainee_email($user_details, $acc_activation_type);
                $this->load->model('course_model');
               $sessin_user_id = $this->session->userdata('userDetails')->user_id;
                $user_data = $this->r_userDetails($sessin_user_id);
                
                
                $r_someone = array(
                                    'firstname' => strtoupper($user_data->first_name),
                                    'lastname' => strtoupper($user_data->last_name),
                                    'email' => $user_data->registered_email_id
                                  );
              
                //$this->course_model->send_reg_someone_referance_email($r_someone, $user_details, 'BPEMAC'); // referance
                //$this->course_model->send_reg_someone_referal_email($r_someone, $user_details, 'BPEMAC'); // referance referal
                //end
            $this->send_trainee_email($r_someone, $user_details, $acc_activation_type);
            //$this->send_trainee_email1($r_someone, $user_details, $acc_activation_type);
            $this->load->model('course_public_model');
            $this->course_public_model->send_reg_someone_referal_email($r_someone, $user_details, $acc_activation_type);


        }

        return $user_id;

    } 

    /*

     * This methord for sending the email to the user.     

     */

    private function send_trainee_email($r_someone, $user, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);        

        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($user['gender'] == 'MALE'){

            $body = "Dear Mr. ".$user['firstname'].',';

        }else if ($user['gender'] == 'FEMALE'){

            $body .="Dear Ms. ".$user['firstname'].',';            

        }  else{

            $body .="Dear ".$user['firstname'].','; 

        }         

        if ($bypassemail == 'EMACT_RQ'){

            $body .=  "<br/><br/>Thank you for registering with us at <b>'".$tenant_details->tenant_name."'</b>. Your Training Account has been successfully created by ".$r_someone['firstname'].' '.$r_someone['lastname']."<br/><br/>";

            $body .= "With this Training Account, you will be able to track all the courses that you take with us at <b>'" . $tenant_details->tenant_name . "'</b> as well as enroll for new courses with us.<br/><br/>";

              if ($user['link']) {

                  $body .= 'Please click here or copy the URL ';

                  $body .= '<br/><a target="_blank" href="'. $user['link'] . '">'.$user['link'].'</a> <br/>to access your Training Account.<br/><br/>';

              }

        }

        $subject = 'Your TMS Account Credentials';
        
        $body .= "<br/>";
        
        $body .=  "<br/><br/>Thank you for registering with us at <b>'".$tenant_details->tenant_name."'</b>. Your Training Account has been successfully created by ".$r_someone['firstname'].' '.$r_someone['lastname']."<br/><br/>";
        
        $body .= "<strong>Your Username:</strong> ". $user['username'] ."<br/>";

        $body .= "<strong>Your Password:</strong> ". $user['password']."<br/>";

        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" .  base_url() . "</a></strong>";
        
        $body .= "<br/><br/>";
        
        $body .= $footer_data;        

        return send_mail($user['email'], '', $subject, $body);

    }
    
     private function send_trainee_email1($r_someone, $user, $bypassemail) {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);        

        $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

        $subject = NULL;

        $body = NULL;

        if ($user['gender'] == 'MALE'){

            $body = "Dear Mr. ".$r_someone['firstname'].',';

        }else if ($user['gender'] == 'FEMALE'){

            $body .="Dear Ms. ".$r_someone['firstname'].',';            

        }  else{

            $body .="Dear ".$r_someone['firstname'].','; 

        }         

        if ($bypassemail == 'EMACT_RQ'){

            $body .=  "<br/><br/>Thank you for registering with us at <b>'".$tenant_details->tenant_name."'</b>. Your Training Account has been successfully created by ".$r_someone['firstname'].' '.$r_someone['lastname']."<br/><br/>";

            $body .= "With this Training Account, you will be able to track all the courses that you take with us at <b>'" . $tenant_details->tenant_name . "'</b> as well as enroll for new courses with us.<br/><br/>";

              if ($user['link']) {

                  $body .= 'Please click here or copy the URL ';

                  $body .= '<br/><a target="_blank" href="'. $user['link'] . '">'.$user['link'].'</a> <br/>to access your Training Account.<br/><br/>';

              }

        }

        $subject = 'Your TMS Account Credentials';
        
        $body .= "<br/>";
        
        $body .= "<br/><br/>You Refered ".$user['firstname'].' '.$user['lastname'].''."to <b>". $tenant_details->tenant_name . "</b>";
         
        $body .= "<br/>";
        
        $body .= "<strong> Username:</strong> ". $user['username'] ."<br/>";

        $body .= "<strong> Password:</strong> ". $user['password']."<br/>";
         $body .= "<br/>";

        $body .= "<strong>You can access the TMS portal at <a href='" . base_url() . "'>" .  base_url() . "</a></strong>";

        $body .= $footer_data;        

        return send_mail($r_someone['email'], '', $subject, $body);

    }

        

    /**

     * for activating trainee

     * @param int $user_id

     * @param string $activation_key md5 of the activation code

     * @return boolean

     */

    public function verify_trainee_user($user_id, $activation_key) {

        if($user_id && $activation_key){

            $this->db->select('activation_key');

            $this->db->from('tms_users');

            $this->db->where('user_id',$user_id);

            $db_activation_key=md5($this->db->get()->row('activation_key'));            

            if($db_activation_key == $activation_key){

                $data=array('account_status' => 'ACTIVE');

                $this->db->where('user_id',$user_id);

                $status=$this->db->update('tms_users',$data);

                if($status){

                    return TRUE;

                }else{

                    return FALSE;

                }

            }else{

                return FALSE;

            }

        }else{

            return FALSE;

        }

        

    }

    /*

    * Method for getting tenant information.

    */

    public function get_tenant_details() {

        $tenant_details = fetch_tenant_details(TENANT_ID);

        return $tenant_details;

    }

    

    /* for student profile */

    

    

    public function get_my_profile($refer_user_id = NULL) {

        $result = array();

        $userdetails = array();

        $tid = $this->session->userdata('userDetails')->tenant_id;

        if (!empty($refer_user_id)) {

            $uid = $refer_user_id;

        } else {

            $uid = $this->session->userdata('userDetails')->user_id;

        }

        $this->db->select('usr.*,  pers.*');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '

                . 'AND usr.user_id = pers.user_id');

        $this->db->where('usr.user_id', $uid);

        $this->db->where('usr.tenant_id', $tid);

        $query = $this->db->get();

        $result = $query->result_array();

        $data['userdetails'] = $result[0];

        // educational  details

        $this->db->select('educ_id, educ_level, educ_yr_completion, educ_score, educ_remarks');

        $this->db->from('tms_users_educ');

        $this->db->where('tenant_id', $tid);

        $this->db->where('user_id', $uid);

        $query = $this->db->get();

        $data['edudetails'] = $query->result_array();

        //other  details

        $this->db->select('othr_cert_id, cert_name, yr_completion, valid_till,oth_remarks');

        $this->db->from('tms_users_othr_cert');

        $this->db->where('tenant_id', $tid);

        $this->db->where('user_id', $uid);

        $query = $this->db->get();

        $data['otherdetails'] = $query->result_array();

        //work  details

        $this->db->select('wrk_exp_id, org_name, emp_from_date, emp_to_date, designation');

        $this->db->from('tms_users_wrk_exp');

        $this->db->where('tenant_id', $tid);

        $this->db->where('user_id', $uid);

        $query = $this->db->get();

        $data['workdetails'] = $query->result_array();



        return $data;

    }

     /*

     * Added  by  Pritam

  

     * This  function update  the   student  profile.

     * MOdifiedDate : 30 May 2016.

     */



    public function update_my_profile() {

        if ($this->input->post('userid')) {

            $user_id = $this->input->post('userid');

            $relationship_array = array('friend_relation' => $this->input->post('relationship'));

        } else {

            $user_id = $this->session->userdata('userDetails')->user_id;

            $relationship_array = array();

        }

        $dateTime = date('Y-m-d H:i:s');

        $other_identi_type = NULL;

        $other_identi_code = NULL;

        $country_of_residence = trim($this->input->post('country_of_residence', TRUE));

        if ($country_of_residence == IND) {

            $tax_code_type = PAN;

            $tax_code = trim($this->input->post('PAN', TRUE));

        }

        if ($country_of_residence == SGP) {

            $NRIC = trim($this->input->post('NRIC', TRUE));

            $tax_code_type = $NRIC;

            $tax_code = trim($this->input->post('NRIC_ID', TRUE));

            if ($NRIC == OTHERS) {

                $other_identi_type = trim($this->input->post('NRIC_OTHER', TRUE));

                $other_identi_code = $tax_code;

            }

        }

        if ($country_of_residence == USA) {

            $tax_code_type = SSN;

            $tax_code = trim($this->input->post('SSN', TRUE));

        }


/*none ediatble fields start*/
//        $data = array(
//
//            'registered_email_id' => trim($this->input->post('user_registered_email', TRUE)),
//
//            'country_of_residence' => $country_of_residence,
//
//            'tax_code_type' => $tax_code_type,
//
//            'tax_code' => $tax_code,
//
//            'other_identi_type' => $other_identi_type,
//
//            'other_identi_code' => $other_identi_code, //END
        $data = array(

            'last_modified_on' => $dateTime,

            'last_modified_by' => $user_id,

                ) + $relationship_array;

        $this->db->where('user_id', $user_id);

        $this->db->update('tms_users', $data);

        $pers_dob = $this->input->post('pers_dob');

        $dob_date = (empty($pers_dob)) ? NULL : date('Y-m-d', strtotime($pers_dob));

        $tms_users_pers_data = array(

            'first_name' => trim(strtoupper($this->input->post('pers_first_name', TRUE))),

            'last_name' => trim(strtoupper($this->input->post('pers_second_name', TRUE))),

            'gender' => trim($this->input->post('pers_gender', TRUE)),

            //'dob' => $dob_date,

            'alternate_email_id' => trim($this->input->post('pers_alternate_email', TRUE)),

            //'contact_number' => trim($this->input->post('pers_contact_number', TRUE)),

            'alternate_contact_number' => trim($this->input->post('pers_alternate_contact_number', TRUE)),

            'nationality' => trim($this->input->post('nationality', TRUE)),

            'race' => trim($this->input->post('race', TRUE)),

            'occupation_code' => trim($this->input->post('occupation', TRUE)),

            'salary_range' => trim($this->input->post('sal_range', TRUE)),

            'highest_educ_level' => trim($this->input->post('highest_educ_level', TRUE)),

            'personal_address_bldg' => trim(strtoupper($this->input->post('pers_personal_address_bldg', TRUE))),

            'personal_address_city' => trim(strtoupper($this->input->post('pers_city', TRUE))),

            'personal_address_state' => trim($this->input->post('pers_states', TRUE)),

            'personal_address_country' => trim($this->input->post('pers_country', TRUE)),

            'personal_address_zip' => trim($this->input->post('pers_zip_code', TRUE)),

            'certificate_pick_pref' => trim($this->input->post('certificate_pick_pref', TRUE)),

        );



        $this->db->where('user_id', $user_id);

        $this->db->update('tms_users_pers', $tms_users_pers_data);

        extract($_POST);



        // deleting all the existing datas in tms_users_educ,tms_users_othr_cert,tms_users_wrk_exp.

        $this->db->where('user_id', $user_id);

        $this->db->delete('tms_users_educ');



        $this->db->where('user_id', $user_id);

        $this->db->delete('tms_users_othr_cert');



        $this->db->where('user_id', $user_id);

        $this->db->delete('tms_users_wrk_exp');

        // inserting in to  tms_users_educ, tms_users_othr_cert,tms_users_wrk_exp starts.

        $edu = array();

        for ($i = 0; $i < count($edu_level); $i++) {

            if ($edu_level[$i]) {

                $edu[] = array(

                    'tenant_id' => $this->session->userdata('userDetails')->tenant_id,

                    'user_id' => $user_id,

                    'educ_id' => '',

                    'educ_level' => $edu_level[$i],

                    'educ_yr_completion' => ($edu_year_of_comp[$i]) ? $edu_year_of_comp[$i] : NULL,

                    'educ_score' => $edu_score_grade[$i],

                    'educ_remarks' => strtoupper($edu_remarks[$i]),

                );

            }

        }

        if (!empty($edu)) {

            $this->db->insert_batch('tms_users_educ', $edu);

        }



        $other = array();

        for ($i = 0; $i < count($oth_certi_name); $i++) {

            if ($oth_certi_name[$i]) {

                $other[] = array(

                    'tenant_id' => $this->session->userdata('userDetails')->tenant_id,

                    'user_id' => $user_id,

                    'othr_cert_id' => '',

                    'cert_name' => strtoupper($oth_certi_name[$i]),

                    'yr_completion' => $oth_year_of_certi[$i],

                    'valid_till' => ($oth_validity[$i]) ? date('Y-m-d', strtotime($oth_validity[$i])) : NULL,

                    'oth_remarks' => strtoupper($oth_remarks[$i]),

                );

            }

        }

        if (!empty($other)) {

            $this->db->insert_batch('tms_users_othr_cert', $other);

        }



        $wrk_exp = array();

        for ($i = 0; $i < count($work_org_name); $i++) {

            if ($work_org_name[$i]) {

                $wrk_exp[] = array(

                    'tenant_id' => $this->session->userdata('userDetails')->tenant_id,

                    'user_id' => $user_id,

                    'wrk_exp_id' => '',

                    'org_name' => strtoupper($work_org_name[$i]),

                    'emp_from_date' => ($work_empfrom[$i]) ? date('Y-m-d', strtotime($work_empfrom[$i])) : NULL,

                    'emp_to_date' => ($work_empto[$i]) ? date('Y-m-d', strtotime($work_empto[$i])) : NULL,

                    'designation' => $work_designation[$i],

                );

            }

        }

        if (!empty($wrk_exp)) {

            $this->db->insert_batch('tms_users_wrk_exp', $wrk_exp);

        }

        return $user_id;

    }

     /*

     * This Method for Updating the new password.

     * Author : pritam.

     * Date : 01 June 2016.

     */



    public function update_password() {

        $user = $this->session->userdata('userDetails');

        $new_enc_password = $this->bcrypt->hash_password($this->input->post('new_password'));

        $update_array = array('password' => $new_enc_password, 'pwd_last_chgd_on' => date('Y-m-d H:i:s'));

        $this->db->where("user_id", $user->user_id);

        $this->db->where("tenant_id", $user->tenant_id);

        $result = $this->db->update("tms_users", $update_array);

        if ($result) {

            return TRUE;

        } else {

            return FALSE;

        }

    }

     /*

     * For the old password matching ajax call.

     * Author : Pritam.

     * Date : 01 June 2016.

     */



    public function match_old_pwd($old_password) {

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

    /*

     * Method for forgot password.

    

     */



    public function validate_forgot_pwd($forgot_param, $to_email_id, $dob, $encrypted_password, $password) {

        //$dob1 = date('Y-m-d',strtotime($dob));

       //$dt = DateTime::createFromFormat('d/m/Y', $dob)->format('Y-m-d');

        $this->db->select("usr.user_id, usr.user_name , usr.password,  pers.first_name, pers.last_name, pers.gender");

        $this->db->from("tms_users  usr, tms_users_pers pers");

        $this->db->where("usr.user_id = pers.user_id");

        $this->db->where("usr.tenant_id = pers.tenant_id");

        $this->db->where("usr.registered_email_id", $to_email_id);

      //  $this->db->where("pers.dob", $dt);

        $this->db->where("usr.tenant_id", TENANT_ID);

        $qry = $this->db->get();

        if ($qry->num_rows() > 0) {

            if ($forgot_param == 'Username') {

                $mail_subject = "Your User Name";

                $data = $qry->row('user_name');

            } else {

                $update_array = array('password' => $encrypted_password);

                $this->db->where('user_id', $qry->row('user_id'));

                $update_result = $this->db->update('tms_users', $update_array);

                if (!$update_result) {

                    return 'database_error';

                }

                $mail_subject = "Your New Password";

                $data = $password;

            }

            $mail_body = $this->get_mail_body($data, $qry->row('first_name'), $qry->row('last_name'), $qry->row('gender'), $forgot_param);

            //send mail           

            $cc_email_id = "";

            $mail_result = send_mail($to_email_id, $cc_email_id, $mail_subject, $mail_body);

            if ($mail_result) {

                return 'mail_sent';

            } else {

                return 'mail_not_sent';

            }

        } else {

            return FALSE;

        }

    }

      /*

     * Function to get referral list of current loged in user.

    

     */



    public function get_referral_list() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        $user_id = $this->session->userdata('userDetails')->user_id;

        $this->db->select('u.user_id,u.friend_id,ce.friend_id,ce.friend_relation as user_friend,ce.friend_relation as enrol_friend,'
                . 'up.first_name,up.last_name,ce.class_id,ce.course_id,cc.class_name,'
                . 'cc.class_start_datetime,cc.class_end_datetime,cc.classroom_location,cc.class_language,cc.class_status');

        $this->db->from('tms_users u');
        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id');

        $this->db->join('class_enrol ce','ce.user_id=u.user_id and ce.user_id=up.user_id','left');
        $this->db->join('course_class cc','cc.course_id=ce.course_id and cc.class_id=ce.class_id','left');
        $this->db->where('ce.friend_id', $user_id);
       
        $this->db->where('u.tenant_id', $tenant_id);

        $this->db->order_by("u.created_on", "DESC");

        return $this->db->get()->result_array();

    }
     public function get_registered_list() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        $user_id = $this->session->userdata('userDetails')->user_id;

        $this->db->select('u.user_id,u.friend_id, u.friend_relation as user_friend,up.first_name,up.last_name');
        $this->db->from('tms_users u');
        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id');
      
        $this->db->where('u.friend_id', $user_id);
       
        $this->db->where('u.tenant_id', $tenant_id);

        $this->db->order_by("u.created_on", "DESC");
//        $query1 = $this->db->get();
//        echo $join1 = $this->db->last_query();
//        exit();
        return $this->db->get()->result_array();

    }
public function get_referral_list1() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $this->db->select('u.user_id,ce.friend_id, u.friend_relation as user_friend,ce.friend_relation as enrol_friend,up.first_name,up.last_name,ce.class_id,ce.course_id,cc.class_name,'
                . 'cc.class_start_datetime,cc.class_end_datetime,cc.classroom_location,cc.class_language,cc.class_status');
        $this->db->from('tms_users u');
        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id');
        $this->db->join('class_enrol ce','ce.user_id=u.user_id and ce.user_id=up.user_id','left');
        $this->db->join('course_class cc','cc.course_id=ce.course_id and cc.class_id=ce.class_id','left');
        // $this->db->where('u.friend_id', $user_id);
        $this->db->where('ce.friend_id', $user_id);
        $this->db->where('u.tenant_id', $tenant_id);
      //  $this->db->order_by("u.created_on", "DESC");
        $query1 = $this->db->get();
        $join1 = $this->db->last_query();
      //  echo "<br />";
        $this->db->select('u.user_id,u.friend_id, u.friend_relation as user_friend,ce.friend_relation as enrol_friend,up.first_name,up.last_name,ce.class_id,ce.course_id,cc.class_name,'
                . 'cc.class_start_datetime,cc.class_end_datetime,cc.classroom_location,cc.class_language,cc.class_status');
        $this->db->from('tms_users u');
        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id');
        $this->db->join('class_enrol ce','ce.user_id=u.user_id and ce.user_id=up.user_id','left');
        $this->db->join('course_class cc','cc.course_id=ce.course_id and cc.class_id=ce.class_id','left');
        $this->db->where('u.friend_id', $user_id);
        // $this->db->or_where('ce.friend_id', $user_id);
        $this->db->where('u.tenant_id', $tenant_id);
       // $this->db->order_by("u.created_on", "DESC");
        $query2 = $this->db->get();
        $join2 = $this->db->last_query();
     //   echo "<br />";
        $query3 = $this->db->query($join1.' UNION '.$join2);
           // echo  $this->db->last_query();
       return $query3->result_array();
        
//        return $this->db->get()->result_array();

    }

    /*

      Function get_dashboard_user_info

      

     */



    public function get_dashboard_user_info() {

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        $user_id = $this->session->userdata('userDetails')->user_id;

        $this->db->select('u.user_id, up.photo_upload_path, up.contact_number, u.registered_email_id, up.first_name, up.last_name'

                . ', up.personal_address_bldg, up.personal_address_city, up.personal_address_state');

        $this->db->from('tms_users u');

        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id');

        $this->db->where('u.user_id', $user_id);

        $this->db->where('u.tenant_id', $tenant_id);

        return $this->db->get()->result_array();

    }

    /*

     * Author:  Blessy

     * This  function   gets  the details  of  the student profile.

     * Date: 27 Oct 2014.

     */



//    public function get_my_profile($refer_user_id = NULL) {
//        $result = array();

//        $userdetails = array();

//        $tid = $this->session->userdata('userDetails')->tenant_id;

//        if (!empty($refer_user_id)) {

//            $uid = $refer_user_id;

//        } else {

//            $uid = $this->session->userdata('userDetails')->user_id;

//        }

//        $this->db->select('usr.*,  pers.*');

//        $this->db->from('tms_users usr');

//        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '

//                . 'AND usr.user_id = pers.user_id');

//        $this->db->where('usr.user_id', $uid);

//        $this->db->where('usr.tenant_id', $tid);

//        $query = $this->db->get();

//        $result = $query->result_array();

//        $data['userdetails'] = $result[0];

//        // educational  details

//        $this->db->select('educ_id, educ_level, educ_yr_completion, educ_score, educ_remarks');

//        $this->db->from('tms_users_educ');

//        $this->db->where('tenant_id', $tid);

//        $this->db->where('user_id', $uid);

//        $query = $this->db->get();

//        $data['edudetails'] = $query->result_array();

//        //other  details

//        $this->db->select('othr_cert_id, cert_name, yr_completion, valid_till,oth_remarks');

//        $this->db->from('tms_users_othr_cert');

//        $this->db->where('tenant_id', $tid);

//        $this->db->where('user_id', $uid);

//        $query = $this->db->get();

//        $data['otherdetails'] = $query->result_array();

//        //work  details

//        $this->db->select('wrk_exp_id, org_name, emp_from_date, emp_to_date, designation');

//        $this->db->from('tms_users_wrk_exp');

//        $this->db->where('tenant_id', $tid);

//        $this->db->where('user_id', $uid);

//        $query = $this->db->get();

//        $data['workdetails'] = $query->result_array();

//

//        return $data;

//    }







}





