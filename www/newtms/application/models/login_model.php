<?php
/*
  * This is the Model class for Login  
  */

class Login_Model extends CI_Model {
    
    public function __construct() {
        parent::__construct();       
        $this->load->helper('common');
        $this->load->library('bcrypt');
    }
    /* 
     * This method validates the user credentials 
     */
    public function check_user_valid() { 
        $user_name = $this->input->post('username');    
        $password = $this->input->post('password');
        $this->db->select('tenant_id, password, user_id, registered_email_id,user_name')
                ->from('tms_users')->where('user_name',$user_name)
                ->where_in('account_type', array('INTUSR'))
                ->where('account_status', 'ACTIVE');
        $result = $this->db->get()->row();
        if(!empty($result)&& ($result->tenant_id == 'ISV01') ){ 
            if ($this->bcrypt->check_password($password, $result->password)) {
                    unset($result->password);
                    $result->role_id = 'SADMN';
                    $result->first_name = 'ISV Administrator';
                    return $result;
                }else{
                    return FALSE;
                }
        }
        $this->db->select('usr.password, usr.user_id, usr.tenant_id, usr.registered_email_id, '
                . 'GROUP_CONCAT(role.role_id SEPARATOR ", ") as role_id,usr.user_name, pers.first_name, pers.last_name', FALSE);
        $this->db->from('tms_users usr');
        $this->db->join('internal_user_role role', 'usr.user_id = role.user_id AND usr.tenant_id = role.tenant_id');
        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id AND usr.tenant_id = pers.tenant_id');
        $this->db->where('usr.user_name', $user_name);
        $this->db->where_in('usr.account_type', array('INTUSR', 'COMUSR'));
        $this->db->where('usr.account_status', 'ACTIVE');
        $this->db->join('tenant_master tm', 'tm.tenant_id = usr.tenant_id');
        $this->db->select('tm.account_status');
        $this->db->group_by("usr.user_id");
        $result = $this->db->get()->row();
        if ($this->bcrypt->check_password($password, $result->password)) {
            unset($result->password);
            if($result->role_id == 'COMPACT') {
                $company_id = $this->db->select('company_id')->from('tenant_company_users')->where('user_id', $result->user_id)->get()->row();                    
                $result->company_id = $company_id->company_id;                    
            }
            $this->db->delete('ci_sessions',array('user_id' => $result->user_id));
            $session_id = $this->session->userdata('session_id');
            $this->db->where('session_id', $session_id);
            $this->db->update('ci_sessions', array('user_id' => $result->user_id));
            //print_r($result);exit;
            return $result;
        }
        else {
            return FALSE;
        }
    }   
    /**
     * Validate the email Id and DOB and get the username and password
     * @param type $forgot_param
     * @param type $email_id
     * @param type $dob
     */     
    public function validate_forgot_pwd($to_email_id, $username, $encrypted_password, $password) {   
        $this->db->select("usr.user_id, usr.user_name , usr.password");
        $this->db->from("tms_users  usr");
        $this->db->where("usr.registered_email_id",$to_email_id);
        $this->db->where("usr.user_name",$username);
        $qry = $this->db->get();
        if($qry->num_rows()>0) {     
            $this->db->select("usr.user_id,pers.first_name, pers.last_name, pers.gender");
            $this->db->from("tms_users usr");
            $this->db->join("tms_users_pers pers", " usr.user_id=pers.user_id");
            $this->db->where("usr.user_id",$qry->row('user_id'));
            $this->db->where("usr.user_name",$username);
            $qry1 = $this->db->get();        
            if($qry1->num_rows()>0) {       
                    $update_array=array('password'=>$encrypted_password);
                    $this->db->where('user_id', $qry->row('user_id'));
                    $this->db->trans_start();
                    $this->db->update('tms_users',$update_array);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {                    
                        return 'database_error';
                    }
                    $mail_subject= "Your New TMS Password";
                    $data=$password;
                $mail_body =  $this->get_mail_body($data,$qry1->row('first_name'),$qry1->row('gender'));            
                $cc_email_id = "";
                $mail_result = send_mail($to_email_id,$cc_email_id,$mail_subject,$mail_body); 
                if($mail_result) {
                   return 'mail_sent';
                } else {
                   return 'mail_not_sent';
               }
            } else { 
                return 'invalid_username';
            }
        } else {
            return 'email_id_not_present';
        }
    }
     /**
      * This method generates the mail body for sending user name
       * @param type $user_name
      * @param type $first_name
      * @param type $last_name
      * @param type $gender
      */
    private function get_mail_body($data, $first_name, $gender) {         
        if ($gender == 'MALE'){
            $mail_body="Dear Mr.".$first_name.',';
        }elseif($gender == 'FEMALE'){
             $mail_body="Dear Ms.".$first_name.',';            
        }else{
            $mail_body="Dear ".$first_name.',';
        }
        $extra_text = '';
            $extra_text = 'a change of ';
        $mail_body.= "<br/><br/>
                You have requested for $extra_text Password.<br/><br/>
                <strong>Your Password for TMS login is: </strong>".$data."<br/><br/>
                If you have not made this request, please notify us immediately by forwarding this email to admin@biipmi.com <br/><br/>";
        $mail_body .= FORGOT_PWD_MAIL_FOOTER;     
        return $mail_body;
    }
    
    public function fetch_tenant_details($tenant_id = NULL) {
        if (empty($tenant_id)) {
            return FALSE;
        }
        $this->db->select('ten.tenant_id, ten.tenant_email_id, ten.logo, ten.copyrighttext, ten.currency, ten.country,ten.applicationname');
        $this->db->from('tenant_master ten');
        $this->db->where('ten.tenant_id', $tenant_id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }
}   

