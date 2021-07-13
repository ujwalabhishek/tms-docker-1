<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the helper class for all common functions used in the applications. 
 */
//// below function added by shubhranshu for sleaning of special characracters from a string
function clean($string) {
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function generateEncryptedPwd() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return base64_encode(implode($pass)); //turn the array into a string
}

/*
 * This method returns the decrypted password
 */

function getDecryptedPwd($password) {
    return base64_decode($password);
}

/**
 * Generic method for sending mails
 * @param type $to_email_id
 * @param type $cc_email_id
 * @param type $mail_subject
 * @param type $mail_body
 * @return boolean TRUE if success Else Returns FALSE on failure
 */
function send_mail($to_email_id, $cc_email_id, $mail_subject, $mail_body) {
    if(TENANT_ID == 'T20' || TENANT_ID == 'T17'){////dont sent mail for wab and Everest
        return false;
    }else{
        $CI = & get_instance();
        $CI->load->library('email');
        $CI->email->from(FROM_EMAIL_ID, INBOX_MAIL_NAME);
        $CI->email->to($to_email_id);
        $CI->email->cc($cc_email_id);
        $CI->email->subject($mail_subject);
        $CI->email->message($mail_body);
        if ($CI->email->send()) {
            return true;
        } else {
            return false;
        }
    }
    
}

/**
 * get companies
 * @return type
 */
function getcompnies() {
    $ci = & get_instance();
    $ci->load->model('common_model', 'cfmodel');
    $compnies = $ci->cfmodel->fetch_compnies();
    return $compnies;
}

/**
 * get courses
 * @return type
 */
function getcourses() {
    $ci = & get_instance();
    $ci->load->model('common_model', 'cfmodel');
    $courses = $ci->cfmodel->fetch_courses();
    return $courses;
}

function getclasses_by_courseid($courseid) {

    $ci = & get_instance();
    $ci->load->model('common_model', 'cfmodel');
    $classes = $ci->cfmodel->fetch_classes_by_couseid($courseid);
    return $classes;
}

/*
 * function to get category name by param id
 */

function get_catname_by_parm($parmid) {
    $category_name = '';
    $parmid = trim($parmid);
    if (!empty($parmid)) {
        $ci = & get_instance();
        $ci->load->model('common_model', 'cfmodel');
        $paramvalue = $ci->cfmodel->get_param_value($parmid);
        $category_name = $paramvalue->category_name;
    }
    return $category_name;
}

/*
 * common function to get company name by company id 
 */

function get_companyname($company_id) {
    $ci = & get_instance();
    $ci->load->model('common_model', 'cfmodel');
    $company = $ci->cfmodel->get_companyname($company_id);
    return $company[0]['company_name'];
}

function company_details_by_userid($userid) {
    $ci = & get_instance();
    $ci->load->model('trainee_model', 'trainee');
    $res = $ci->trainee->get_company_details_allstatus($userid)->row();
    return $res;
}

/*
 * This method will format the date both db and display purpose.
 */

function formated_date($date, $delimiter) {
    if ($date == '') {
        return FALSE;
    } else {
        $date_array = explode($delimiter, $date);
        $formated_date = '';
        if (count($date_array) > 0) {
            $formated_date = $date_array[2] . '/' . $date_array[1] . '/' . $date_array[0];
        } else {
            return FALSE;
        }
        if ($formated_date == '')
            return FALSE;
        else
            return $formated_date;
    }
}

/**
 * Converts date from Singapore format to MySQL format
 * @param $date  - date string in format "d/m/Y"
 * @return date string in mysql format ('Y-m-d') or NULL
 */
function date_format_mysql($date) {
    $phpdate = date_create_from_format("d/m/Y", $date);
    if ($phpdate === FALSE)
        return NULL;
    else
        return date('Y-m-d', $phpdate->getTimestamp());
}

/**
 * Converts MySQL date to Singapore format
 * @param $date - MySQL date string
 * @return formatted date string in Singapore format
 */
function date_format_singapore($date) {
    $phpdate = date_create_from_format("Y-m-d", $date);
    if ($phpdate === FALSE)
        $phpdate = date_create_from_format("Y-m-d H:i:s", $date);

    if ($phpdate === FALSE)
        return "";
    else
        return date("d/m/Y", $phpdate->getTimestamp());
}

/**
 * Converts MySQL date to Singapore format in array for some field
 * @param $array - array with date field
 * @param $field - name of the date field for format
 * @return array with formatted date
 */
function date_format_singapore_for_array($array, $field) {
    foreach ($array as $key => $res) {
        $date = $array[$key][$field];
        $formatted = date_format_singapore($date);
        $array[$key][$field] = $formatted;
    }
    return $array;
}

/**
 * parse time from MySQL datetime field
 * @param $date - MySQL datetime string
 * @return formatted time string
 */
function time_format_singapore($date) {
    $phpdate = date_create_from_format("Y-m-d H:i:s", $date);
    if ($phpdate === FALSE)
        return "";
    else
        return date("H:i", $phpdate->getTimestamp());
}

/**
 * Compares to date excluding time
 * @param $date1 - DateTime
 * @param $date2 - DateTime
 * @return true if dates equals
 */
function compare_dates_without_time(DateTime $date1, DateTime $date2) {
    $date_str1 = date("y-m-d", $date1->getTimestamp());
    $date_str2 = date("y-m-d", $date2->getTimestamp());
    return $date_str1 == $date_str2;
}

/**
 * Parse date for format
 * @param $date_str - data in string format
 * @param $format - date format for parsing
 * @return DateTime parsed date on success or false on failure.
 */
function parse_date($date_str, $format) {
    $date = date_create_from_format($format, $date_str);
    return $date; 
}

/**
 * fetch all the links of a category
 * @param string $category_user_name display name
 * @param string $category_machine_name
 * @param array $category
 * @return string a list of links
 */
function get_menu_links_by_category1($category_user_name, $category_machine_name, $category) {
    if (empty($category) || $category_machine_name == 'DASHBRD') {
        return;
    }
    if (is_file(APPPATH . 'config/tms_routes.php')) {
        include(APPPATH . 'config/tms_routes.php');
    }
    $tms_routes = (!isset($tms_route) OR ! is_array($tms_route)) ? array() : $tms_route;
    unset($tms_route);
    $output = '';
    $tms_route = $tms_routes[$category_machine_name]['ops'];
    $flag = TRUE;
    foreach ($category as $key => $value) {
        if ($flag) {
            if ($_SERVER['PATH_INFO'] == '/' . $tms_routes[$category_machine_name]['controller_name']) {
                $css = 'active';
            } else {
                $css = '';
            }
            $output .= "<span><a class=\"$css\" href=" . site_url() . $tms_routes[$category_machine_name]['controller_name'] . ">" . $category_user_name . "</a></span>";
            $output .= "<ul>";
            $flag = FALSE;
        }

        if ($key == 'EXP_XLS' || $key == 'LST_SRCH' || is_numeric($key) || $key == 'DEACT' || $key == 'STTGS') {
            continue;
        }
        if ($key) {
            $url = site_url() . $tms_routes[$category_machine_name]['controller_name'] . '/' . $tms_route[$key];
            $output .= "<li><span><a href=" . $url . ">" . $value . "</a></span></li>";
        }
    }
    if ($output) {
        $output .= '</ul>';
    }

    return $output;
}

/**
 * function to return the random 8 digit password
 * @return string
 */
function random_key_generation() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

/*
 * for getting mail footer data
 */

function get_mail_footer($tenant_id = NULL) {
    $CI = & get_instance();
    if (empty($tenant_id)) {
        return FALSE;
    }
    $tenant_details = $CI->fetch_tenant_details($tenant_id);
    $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);
    $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);
    return $footer_data;
}

/*
 * This method for validating nric code for singapore.
 */

function validate_nric_code($NRIC, $NRIC_ID) {
    $validityStatus = TRUE;
    $NRIC_TYPE=$NRIC;
    if (empty($NRIC) || empty($NRIC_ID)) {
        $validityStatus = FALSE;
    } else {
        $NRIC_ID = strtoupper($NRIC_ID);
        $CI = & get_instance();
        $CI->load->library('nric');
        if ($NRIC != NRIC_OTHERS) {
             $validityStatus = $CI->nric->validatenric($NRIC_ID,$NRIC_TYPE);
        }
    }
    return $validityStatus;
}

/**
 * This function gets and sets the maximum lookup value  // to be synchronized by dummy later - Dec 09 2014
 * @param type $table_name
 */
function get_max_lookup($table_name) {
    $CI = & get_instance();
    $CI->db->select('max_id');
    $CI->db->from('index_lookup');
    $CI->db->where('table_name', $table_name);
    $result_set = $CI->db->get();
    $max_id = $result_set->row('max_id');
    $vad = $max_id + 1;
    $max_array = array(
        'max_id' => $vad,
    );
    $CI->db->where('table_name', $table_name);
    $CI->db->update("index_lookup", $max_array);
    return $max_id;
}

/**
 * This function added to get company autocomplete list
 */
function common_companies_autocomplete($company) {
    $ci = & get_instance();
    $ci->load->model('company_model', 'companymodel');
    $result = $ci->companymodel->internal_company_list_autocomplete($company, 'edit');
    $output = $result;
    $output = array();
    foreach ($result as $k => $v) {
        $output[] = array(
            'key' => $v->company_id,
            'value' => $v->company_name,
            'label' => $v->company_name . '(' . $v->comp_regist_num . ')',
        );
    }
    if (stripos($ci->data['tenant_details']->tenant_name, $company) !== false) {
        $tenant_details = $ci->data['tenant_details'];
        $output[] = array(
            'key' => $tenant_details->tenant_id,
            'value' => $tenant_details->tenant_name,
            'label' => $tenant_details->tenant_name . '(' . $tenant_details->tenant_id . ')',
        );
    }
    return $output;
}

/**
 * function to get user in a courseclass is present, absent or Data Not present
 */
function get_common_attendance_status($tenant_id, $user_id, $course_id, $class_id) {
    $CI = & get_instance();
    $CI->db->select('user_id');
    $CI->db->from('class_attendance');
    $where = array(
        'tenant_id' => $tenant_id, 'course_id' => $course_id, 'class_id' => $class_id
    );
    $CI->db->where($where);
    $class_data = $CI->db->get()->num_rows();
    if ($class_data) {
        $CI->db->select('user_id');
        $CI->db->from('class_attendance');
        $CI->db->where($where);
        $present_where = "(session_01 = 1 OR session_02 = 1)";
        $CI->db->where('user_id', $user_id);
        $CI->db->where($present_where);
        $class_present = $CI->db->get()->num_rows();
        if ($class_present) {
            return 'P';
        }
        return 'AB';
    }
    return 'DNP';
}

///   Added on 16/10/2018 AT 2:29PM  by shubhranshu for view_trainee status
function get_common_attendance_status_new($tenant_id, $user_id, $course_id, $class_id,$atn_status) {
    //echo $atn_status;exit;
    $CI = & get_instance();
    $CI->db->select('user_id');
    $CI->db->from('class_attendance');
    $where = array(
        'tenant_id' => $tenant_id, 'course_id' => $course_id, 'class_id' => $class_id
    );
    $CI->db->where($where);
    $class_data = $CI->db->get()->num_rows();
    if ($class_data) { 
        if ($atn_status == 1) {
            return 'P';
        }
        return 'AB';
    }
    return 'DNP';
}

/**
 * function to reset password
 */
function common_reset_password($user_id, $pass = '') {
    $CI = & get_instance();
    $CI->load->library('bcrypt');
    if ($pass == 'TAXCODE') {
//        $pwd = $CI->db->select('tax_code')->from('tms_users')->where('user_id', $user_id)->get()->row('tax_code');
//        $pwd = strtolower(substr($pwd, 0, 10));
         $pwd=  random_key_generation();
    } else {
       // $pwd = RESETPWD;
        $pwd=  random_key_generation();
    }
    //$pwd ='Pangchoon@#1956';
    $encrypted_password = $CI->bcrypt->hash_password($pwd);
    $password_data = array('password' => $encrypted_password);
    $CI->db->trans_start();
    $CI->db->where('user_id', $user_id);
    $CI->db->update('tms_users', $password_data);
    $CI->db->trans_complete();
    if ($CI->db->trans_status() === FALSE) {
        return FALSE;
    } else {
        $cc_email_to = '';
        $data = $pwd;
        $CI->db->select("usr.user_id,pers.first_name, pers.last_name, pers.gender");
        $CI->db->from("tms_users usr");
        $CI->db->join("tms_users_pers pers", " usr.user_id=pers.user_id");
        $CI->db->where("usr.user_id", $user_id);
        $qry1 = $CI->db->get();
        $mail_subject = "Your New TMS Password";
        $mail_subject_admin = "New TMS Password FOR ".$qry1->row('first_name');
        $mail_body = get_mail_body($data, $qry1->row('first_name'), $qry1->row('gender'));
        $mail_body_admin = get_mail_body_admin_pwreset($data, $qry1->row('first_name'), $qry1->row('gender'));

        $to_email_id = $CI->db->select('registered_email_id')->from('tms_users')->where('user_id', $user_id)->get()->row('registered_email_id');

        $user_role = $CI->session->userdata('userDetails')->role_id;
        if ($user_role == "ADMN") {
            $cc_email_to_admin = $CI->session->userdata('userDetails')->registered_email_id;
        }
        if(!empty($to_email_id)){
        send_mail($to_email_id, $cc_email_to, $mail_subject, $mail_body);
        }
        if(!empty($cc_email_to_admin)){
        send_mail($cc_email_to_admin, $cc_email_to, $mail_subject_admin, $mail_body_admin);
        }
//        return TRUE; //commented by skm on 3-july-17 because we want to show password on message.
        return $pwd;
    }
}


/* function to reset internal staff password skm start */
function internal_staff_reset_password($user_id, $pass = '') {
    $CI = & get_instance();
    $CI->load->library('bcrypt');
    if ($pass == 'TAXCODE') {
//        $pwd = $CI->db->select('tax_code')->from('tms_users')->where('user_id', $user_id)->get()->row('tax_code');
//        $pwd = strtolower(substr($pwd, 0, 10));
         $pwd=  random_key_generation();
    } else {
       // $pwd = RESETPWD;
        $pwd=  random_key_generation();
    }
    $encrypted_password = $CI->bcrypt->hash_password($pwd);
    $password_data = array('password' => $encrypted_password);
    $CI->db->trans_start();
    $CI->db->where('user_id', $user_id);
    $CI->db->update('tms_users', $password_data);
    $CI->db->trans_complete();
    if ($CI->db->trans_status() === FALSE) {
        return FALSE;
    } else {
        $cc_email_to = '';
        $data = $pwd;
        $CI->db->select("usr.user_id,pers.first_name, pers.last_name, pers.gender");
        $CI->db->from("tms_users usr");
        $CI->db->join("tms_users_pers pers", " usr.user_id=pers.user_id");
        //$CI->db->join("internal_user_emp_detail empdetail", " empdetail.user_id=pers.user_id");
        $CI->db->where("usr.user_id", $user_id);
        $qry1 = $CI->db->get();
        $mail_subject = "Your New TMS Password";
        $mail_subject_admin = "New TMS Password FOR ".$qry1->row('first_name');
        $mail_body = get_mail_body($data, $qry1->row('first_name'), $qry1->row('gender'));
        $mail_body_admin = get_mail_body_admin_pwreset($data, $qry1->row('first_name'), $qry1->row('gender'));

        //$to_email_id = $CI->db->select('registered_email_id')->from('tms_users')->where('user_id', $user_id)->get()->row('registered_email_id');
        
        $offcial_internalstaff_email = $CI->db->select('off_email_id')->from('internal_user_emp_detail')->where('user_id', $user_id)->get()->row('off_email_id');
        
        $user_role = $CI->session->userdata('userDetails')->role_id;
        if ($user_role == "ADMN") {
            $cc_email_to_admin = $CI->session->userdata('userDetails')->registered_email_id;
        }
        if(!empty($offcial_internalstaff_email)){
//        send_mail($to_email_id, $cc_email_to, $mail_subject, $mail_body);
        send_mail($offcial_internalstaff_email, $cc_email_to, $mail_subject, $mail_body);
        }
        if(!empty($cc_email_to_admin)){
        send_mail($cc_email_to_admin, $cc_email_to, $mail_subject_admin, $mail_body_admin);
        }
//        return TRUE; //commented by skm on 3-july-17 because we want to show password on message.
        return $pwd;
    }
}


function official_email_id($user_id){
      $CI = & get_instance();
     $offcial_internalstaff_email = $CI->db->select('off_email_id')->from('internal_user_emp_detail')->where('user_id', $user_id)->get()->row('off_email_id'); 
     return $offcial_internalstaff_email;
     
}

function company_user_email_id($user_id){
      $CI = & get_instance();
     $comp_user_email = $CI->db->select('registered_email_id')->from('tms_users')->where('user_id', $user_id)->get()->row('registered_email_id'); 

     return $comp_user_email;
     
}

/* skm end */



/**
 * This method generates the mail body for sending user name
 * @param type $user_name
 * @param type $first_name
 * @param type $last_name
 * @param type $gender
 */
function get_mail_body($data, $first_name, $gender) {
    if ($gender == 'MALE') {
        $mail_body = "Dear Mr." . $first_name . ',';
    } elseif ($gender == 'FEMALE') {
        $mail_body = "Dear Ms." . $first_name . ',';
    } else {
        $mail_body = "Dear " . $first_name . ',';
    }
    $extra_text = '';
    $extra_text = 'a change of ';
    $mail_body.= "<br/><br/>
                You have requested for $extra_text Password.<br/><br/>
                <strong>Your Password for TMS login is: </strong>" . $data . "<br/><br/>
                If you have not made this request, please notify us immediately by forwarding this email to admin@biipmi.com <br/><br/>";
    $mail_body .= FORGOT_PWD_MAIL_FOOTER;
    return $mail_body;
}

/**
 * This method generates the mail body for sending new pwd to admin
 * @param type $user_name
 * @param type $first_name
 * @param type $last_name
 * @param type $gender
 */
function get_mail_body_admin_pwreset($data, $first_name, $gender) {
    $mail_body = "Dear Admin,";
    $extra_text = '';
    $extra_text = 'a change of ';
    $mail_body.= "<br/><br/>
                You have requested for $extra_text Password for $first_name. Please find the login details below.<br/><br/>
                <strong>New Password for TMS login is: </strong>" . $data . "<br/><br/>
                If you have not made this request, please notify us immediately by forwarding this email to admin@biipmi.com <br/><br/>";
    $mail_body .= FORGOT_PWD_MAIL_FOOTER;
    return $mail_body;
}
//added by shubhranshu for audittrail autocomplete
function common_invoice_audittrail_autocomplete($tenant_id, $invoice, $paid = 0) {
    $ci = & get_instance();
    $ci->load->model('Class_Trainee_Model', 'classTraineeModel');
    $result = $ci->classTraineeModel->get_all_invoice_audit_trail($tenant_id, $invoice, $paid);
    $output = array();
    foreach ($result as $row) {
        $output[] = array(
            'key' => $row->invoice_id,
            'label' =>
            $row->enrolment_mode == 'SELF' ?
                    $row->invoice_id . ' (Ind: ' . $row->first_name . ' ' . $row->last_name . ', Taxcode: ' . $row->tax_code . ')' :
                    $row->invoice_id . ' (Com: ' . $row->company_name . ', Taxcode: ' . $row->comp_regist_num . ')',
            'value' => $row->invoice_id,
        );
    }
    return $output;
}

/**
 * This function added to get invoice autocomplete list
 * Author: CR02
 * Sate: 01 Apr 2015
 */
function common_invoice_autocomplete($tenant_id, $invoice, $paid = 0) {
    $ci = & get_instance();
    $ci->load->model('Class_Trainee_Model', 'classTraineeModel');
    $result = $ci->classTraineeModel->get_all_invoice($tenant_id, $invoice, $paid);
    $output = array();
    foreach ($result as $row) {
        $output[] = array(
            'key' => $row->invoice_id,
            'label' =>
            $row->enrolment_mode == 'SELF' ?
                    $row->invoice_id . ' (Ind: ' . $row->first_name . ' ' . $row->last_name . ', Taxcode: ' . $row->tax_code . ')' :
                    $row->invoice_id . ' (Com: ' . $row->company_name . ', Taxcode: ' . $row->comp_regist_num . ')',
            'value' => $row->invoice_id,
        );
    }
    return $output;
}
/**
 * This function added to get the attendance is marked or not for the classs
 * Author: Prit
 * Sate: 25 July 2016
 */
function check_attendance_row($tenant_id,$course_id,$class_id)
{
    $ci= & get_instance();
    $ci->load->model('Class_Trainee_Model','classtraineemodel');
    $check_attendance=$ci->classtraineemodel->check_attendance_row($tenant_id,$course_id,$class_id);
   
    return $check_attendance;
}

/* This Function use to insert activity of user skm start*/
function user_activity($module_id,$act_on,$previous_details,$account_type = null)
{   
        $ci= & get_instance();
        $ci->load->model('internal_user_model','internaluser');
        $user = $ci->session->userdata('userDetails');
        $user_id = $ci->session->userdata('userDetails')->user_id;
        $tenant_id = $user->tenant_id;

        
        $data = array(
                        'tenant_id' => $tenant_id,
                        'module_id'=> $module_id, 
                        'account_type' => $account_type,
                        'act_on' => $act_on,
                        'act_by'=> $user_id,                        
                        'previous_details' => $previous_details,
                       
                     );
        $res = $ci->internaluser->user_activity_log($data);
        return $res;
    }
    /* End */
    /////added by shubhranshu for new requirement class schedule 
    function get_course_class_schedule($course_id, $class_id) {
        $ci= & get_instance();
        
        $ci->db->select('class_date,session_start_time,session_end_time');

        $ci->db->from('class_schld');

        $ci->db->where('course_id', $course_id);
        
        $ci->db->where('class_id', $class_id);

        $ci->db->where('tenant_id', TENANT_ID);
        
        $ci->db->group_by('class_date');
        
        $query = $ci->db->get();
        //echo $ci->db->last_query();exit;
        $res = $query->result_array();
        $arr = '';
        foreach($res as $v){
           //$arr .= '<div>'.$v[class_date].'(Start: '.date('d/m/Y , <br>l @ h:i A', strtotime($v[session_start_time])).')</div>';
           $arr .= '<div>'.$v[class_date].',</div>';
        }
        return $arr;
    }