<?php

/*
 * Use: Add  trainee,checking  of  existing username/email/taxcode
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {
//    class User extends Course_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('metavalues_helper');
        $this->load->helper('common_helper');
        $this->load->model('meta_values');
         $this->load->model('meta_values_model');
        $this->load->model('user_model');
        $this->load->model('course_public_model');
         $this->load->model('courses_model');
        $this->load->model('payments_model');
        $this->load->library('bcrypt');
        
            
        $tenent_details = $this->course_public_model->get_tenant_details();
        $this->session->set_userdata('public_tenant_details', $tenent_details);
        $this->meta_data = $this->meta_values->get_param_map();
    }
    
    /**
     * skm-> This function logout application and redirects to login page
     */
    public function validate_public_user() {
        
        //$this->output->enable_profiler(true);
//        $cid = $this->input->get('cid');
         $course_id = $this->input->post('course_id');
         $class_id = $this->input->post('class_id');
        $year = time() + 31536000;
        setcookie('remember_me', $_POST['username'], $year);
       
        $captcha = $this->input->post('captcha');
        //////below block of code added by shubhranshu for google captcha
        $google_response = $this->input->post('g-recaptcha-response');
        $google_api_url = 'https://www.google.com/recaptcha/api/siteverify?response='.$google_response.'&secret='.GOOGLE_CAPTCHA_SECRETKEY.'';
        $response = file_get_contents($google_api_url);
        $response = json_decode($response);
        if($response->success != 1){
            if(count($response->{'error-codes'}) > 0){
                if($response->{'error-codes'}[0] == 'timeout-or-duplicate'){
                    $this->session->set_flashdata('invalid_captcha', 'Google Captcha Timeout');
                }else if($response->{'error-codes'}[0] == 'bad-request'){
                     $this->session->set_flashdata('invalid_captcha', 'Bad Request for Google Captcha');
                }else if($response->{'error-codes'}[0] == 'invalid-input-response'){
                     $this->session->set_flashdata('invalid_captcha', 'Google Captcha Invalid Response');
                }else if($response->{'error-codes'}[0] == 'missing-input-response'){
                     $this->session->set_flashdata('invalid_captcha', 'Kindly Verify Google Captcha');
                }else if($response->{'error-codes'}[0] == 'invalid-input-secret'){
                     $this->session->set_flashdata('invalid_captcha', 'Google Captcha Invalid Secret');
                }else if($response->{'error-codes'}[0] == 'missing-input-secret'){
                     $this->session->set_flashdata('invalid_captcha', 'Google Captcha Missing Secret');
                }
            }
            redirect('course_public/class_member_check');
        }
//        if(strtolower($captcha) != strtolower($this->session->userdata('public_captcha_key'))){//added by shubhranshu
//            $this->session->set_flashdata('invalid_captcha', 'Invalid captcha code');//added by shubhranshu
//            redirect('course_public/class_member_check');//added by shubhranshu
//             
//        }//added by shubhranshu
        
        $resp = $this->user_model->check_public_user_valid();

        if (empty($resp)) {
            $this->session->set_flashdata('invalid', 'Invalid credentials/ User account inactive.<br/>'
                    . 'Please try again or get in touch with your Administrator.');
            if(!empty($course_id) && is_numeric($course_id) && !empty($class_id) && is_numeric($class_id))
            {
                redirect("course_public/class_member_check/$course_id/$class_id");
            }else { redirect('course_public/class_member_check');}
            
        } else {
            
                $this->session->unset_userdata('public_captcha_key');//added by shubhranshu
                unlink(FCPATH .'captcha/'.$this->session->userdata('public_captcha_file')); // added by shubhranshu to delete the captcha file 
               
                $this->session->set_userdata('userDetails', $resp);
                if (!empty($course_id) && is_numeric($course_id)) {
                    
                $user_id = $resp->user_id; // retrive user_id when user loggedin successfully
                $result = $this->course_public_model->is_user_enrolled1($user_id,$class_id,$course_id);// check user already enrolled in selected course & class.
       
                    if($result == 0)
                    {
                         redirect("course_public/class_enroll1/$course_id/$class_id");

                    }else{

                        $res = $this->course_public_model->class_name($course_id,$class_id); // get the name for class based on courseid and class id
                        $error = 'You Are Already Enrolled In - '. "' $res->class_name '".' Class';
                        $this->session->set_flashdata('error', $error);
                        redirect("course_public/course_class_schedule/$course_id");
                    }
                
                } else {
                    //redirect('user/dashboard');
                    redirect("course_public");
                }
        }
    }
    
    /**
    * skm-> This function logout application and redirects to login page
    */
    public function logout() {
        $this->session->sess_destroy();
        redirect('course_public');
    }
    
     /**
    * skm-> This function redirects and updating  user to profile page.
    */
      public function myprofile() {
         if($this->session->userdata('userDetails')->user_id==""){
            redirect("course_public");
        }

        $data['profile'] = $this->user_model->get_my_profile();
        $data['page_title'] = 'My Profile';
     
        $details = $data['profile']['userdetails'];
        if (!empty($details['personal_address_state'])) {
            $data['state_name'] = $this->courses_model->get_param_value($details['personal_address_state']);
        }
        if ($details['country_of_residence'] == IND) {
            $data['pan_number'] = $details['tax_code'];
        }
        if ($details['country_of_residence'] == SGP) {
            $data['nric_number'] = $details['tax_code'];
            $data['tax_code_type'] = $details['tax_code_type'];
            $data['other_identi_type'] = $details['other_identi_type'];
        }
        if ($details['country_of_residence'] == USA) {
            $data['ssn_number'] = $details['tax_code'];
        }
        //for unique checking of tax_code and email_id.
        $this->session->set_userdata('registered_email_id_edit', $details['registered_email_id']);
        $this->session->set_userdata('tax_code_edit', $details['tax_code']);
        // for updating current profile data.
        if ($_POST) {
            $valid = TRUE;
            if ($this->input->post('country_of_residence') == SGP) {
                // validating nric code.
                $NRIC = $this->input->post('NRIC');
                $NRIC_ID = $this->input->post('NRIC_ID');
                $valid = validate_nric_code($NRIC, $NRIC_ID);
                if ($valid == FALSE) {
                    $this->session->set_flashdata('Error', 'Invalid NRIC Code');
                    redirect('user/myprofile');
                }
            }
            //Server side validation starts.
            $validation_status = $this->server_validation();
            //Server side validation end.
            if ($validation_status == TRUE && $valid == TRUE) {
                $delete_image = $this->input->post('deleteimage') ? $this->input->post('deleteimage') : 'no';
                // updating profile details.
                $uid = $this->user_model->update_my_profile();
                if ($uid == FALSE) {
                    $this->session->set_flashdata('error', 'Unable to update Trainee.Please try again later.');
                } else {
                     
                    // uploading profile images.                    
                    $upload_status = $this->upload_profile_image($uid, $delete_image);
                    if ($upload_status == FALSE) {
                        
                        $this->session->set_flashdata('Success', 'Profile has been updated successfully.<br>Image is not uploaded.');
                    } else {
                        
                        $this->session->set_flashdata('Success', 'Profile has been updated successfully');
                    }
                }
                redirect('user/myprofile');
            }
        }
        $data['main_content'] = 'user/myprofile';   
        $this->load->view('layout_public', $data);
    }
      /*
      Function to verify current password and change password.
      Author:Pritam Choudhary
     */

    public function change_password() {
        $data['page_title'] = 'Change Password';
        $data['main_content'] = 'user/changepassword';
        //server  side validation
        $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            extract($_POST);
            $encrypted_password = $this->bcrypt->hash_password($new_password);
            $result = $this->user_model->update_password($encrypted_password);
            if ($result == TRUE) {
                $this->session->set_flashdata('success', 'Your password has been updated successfully!.');
            } else {
                $this->session->set_flashdata('error', 'Unable to update your password.Please try again later.');
            }
            redirect('user/change_password');
        } else {
            $this->load->view('layout_public', $data);
        }
    }
     public function register_enroll($course_id=null,$class_id=null)
    {
            $data['page_title'] = 'Enrollment'; 
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                $course_id = $this->input->post('course_id');
                $class_id = $this->input->post('class_id');
    //  
                $res = $this->course_public_model->save_imp_trainee();

                echo $uid = $res['user_id'];
                $tax_code = $res['tax_code'];
                $friend_id = $res['friend_id']; //skm
       
                
                  $this->reg($course_id,$class_id,$tax_code);    
             
            } else {

                $data['course_id'] = $course_id;
                $data['class_id'] =  $class_id;
                $data['main_content'] = 'register_enroll';
                $this->load->view('layout_public', $data);
            }
        
        
    }
    // check existing refreal user data start skm
    public function check_fields_match($username,$argument,$radio)
    {
         $username = $this->input->post('username');
         $argument = $this->input->post('argument');
         $radio = $this->input->post('radio');
         $res = $this->course_public_model->check_match_found($username,$argument,$radio);
     
    }
    
    public function check_fields_match1($username,$argument,$radio)
    {
         $username = $this->input->post('username');
         $argument = $this->input->post('argument');
         $radio = $this->input->post('radio');
         
         $this->course_public_model->check_match_found1($username,$argument,$radio);
         return;    
     
    }
    //end
    
    // CHECK EXISTING USER DATA MATCH OR NOT START SKM
    public function check_existing_details($username,$argument,$radio)
    {
         $nric = $this->input->post('nric');
         $argument = $this->input->post('argument');
         $radio = $this->input->post('radio');
         if($radio == 2)
         {
              $this->course_public_model->check_existing_emailDetails($nric,$argument,$radio);

         }elseif($radio == 3) {
              $this->course_public_model->check_existing_contactDetails($nric,$argument,$radio);
         }  else {
             $this->course_public_model->check_existing_dobDetails($nric,$argument,$radio);
         }
        
         return;    
     
    }//END
    
    public function check_user_enrollment($user_id='',$course_id='',$class_id='')
    {
         $user_id = $this->input->post('user_id');
         $course_id = $this->input->post('course_id');
         $class_id = $this->input->post('class_id');
         
         $res = $this->course_public_model->check_user_enrollment($user_id,$course_id,$class_id);
         
    }
    
   


    /*  
     * This function used to add trainee
     */

//    public function add_trainee($course_id=null,$class_id=null) { 
//   
//            $data['page_title'] = 'Trainee Register';
//            $registration = '';
//        if ($this->input->server('REQUEST_METHOD') === 'POST') {
//           $course_id = $this->input->post('course_id');
//           $class_id = $this->input->post('class_id');
//           $registration = $this->input->post('registration');
//            $res = $this->course_public_model->save_imp_trainee();
//         
//            $uid = $res['user_id'];
//            $tax_code = $res['tax_code'];
//            $friend_id = $res['friend_id']; 
//            
//            if($this->input->post('course_id')!='')
//            {
//              $this->reg($course_id,$class_id,$tax_code,$registration);    
//            }
////             echo $course_id; echo"<br/>";
////             echo $class_id; echo"<br/>";
////             echo $uid; echo"<br/>";
//            // my code when user data exist when comes from home page
////            if($course_id!='' && $class_id!='' && $uid!='' && $friend_id=='')
////            {   echo "base_url().'course/enroll_friend/'.$uid.'/'.$friend_id";
//////                redirect(base_url().'course/enroll_friend/'.$uid.'/'.$friend_id);
////            }
////            else{
////                echo "problem";
////            }
//             
//            //end
////          exit;
//            if($this->input->post('course_id')=='')
//            { 
////                echo "inner";
//                if (!$uid) {
//                        $data['message'] = "fail";
//                    } else {
//                        $data['message'] = "success";
//                        /* session start when user select course &class & register and then enroll in class successfully*/ 
//                        $user_session_id = $this->session->userdata('userDetails')->user_id;
//                        if(empty($user_session_id))
//                        {   $this->load->model('user_model');
//                            $resp = $this->user_model->register_login_process($uid);
//                            if(!empty($resp))
//                            {
//                                $this->session->set_userdata('userDetails', $resp);
//                            }
//                        }//end
//                    }
//                /*
//                 * when user comes from enrollsome one then if condition work and show course list
//                * when user comes from registrain page then else condition work and show success msg
//                */
//                //skm start
//                if($uid!='' && $friend_id!=''&& $course_id==''&& $class_id=='')
//                {   
//                    $relation = $this->input->post('relationship');
////                    redirect(base_url().'course/get_course_class_list/'.$uid.'/'.$friend_id);
//                    redirect(base_url().'course/get_course_class_list/'.$uid.'/'.$friend_id.'/'.$relation);
//                }
////                 if($uid!='' && $course_id=='' && $class_id=='')
////                { 
////                    redirect(base_url().'course/get_course_class_list/'.$uid.'/'.$friend_id);
////                }
//                 
//                
//               // exit;
//                // skm end
//            $data['tenant_details'] = $this->user_model->get_tenant_details();
//            $data['main_content'] = 'common/error';
//            $this->load->view('layout_public', $data);
//           }
//        } else {
//                $data['course_id'] = $course_id;
//                $data['class_id'] = $class_id;
//           
//            $data['main_content'] = 'addtrainee';
//            $this->load->view('layout_public', $data);
//        }
//    }
    
    
    /* This Method is Use to Register the Trainee skm*/
    public function reg_trainee() { 
        extract($_POST);
        $data['page_title'] = 'Trainee Register';
        $registration = '';
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            if(strtolower($captcha) != strtolower($this->session->userdata('public_captcha_key'))){//added by shubhranshu
                $this->session->set_flashdata('invalid_captcha', 'Invalid captcha code');//added by shubhranshu
                redirect('course_public/register');//added by shubhranshu

            }//added by shubhranshu
            $res = $this->course_public_model->save_reg_trainee();
        
           $user_id = $res;
            if ($user_id=='') {
                
                            $error = 'Unable to Register, Please try again !';
                            $this->session->set_flashdata('error', $error);
                            return redirect('course_public/register');
                        }
                        else
                        {
                                
                                                      
                            $this->load->model('user_model');

                            $resp = $this->user_model->register_login_process($user_id);

                            if(!empty($resp))
                            {
                                $this->session->set_userdata('userDetails', $resp);
                            }
                            $success = 'Thanks for the registration';
                            $this->session->set_flashdata('success', $success);
                            return redirect('course_public');
                                
                            
                        }  
        }  
    } //end
    
     /*  
     * This function used to add trainee when user loggedin (Enroll For Someone)
     */   
    public function add_trainee($course_id=null,$class_id=null) { 
   
            $data['page_title'] = 'Trainee Register';
            $registration = '';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            $course_id = $this->input->post('course_id');
            $class_id = $this->input->post('class_id');
            $registration = $this->input->post('registration');
            $relation = $this->input->post('relationship');

            $res = $this->course_public_model->loggedin_enroll_someone(); 
            if($res['user_id']!=0)
            {
           
                $uid = $res['user_id'];
                $tax_code = $res['tax_code'];
                $friend_id = $res['friend_id']; 
                $user_password = $res['user_password'];
            

                $this->create_classenroll1($uid,$user_password,$course_id,$class_id,$tax_code,$registration,$friend_id,$relation);
        
            }else{
                $error = 'We are not able to complete the enrollment in the class. Please try again later or contact your Administrator.';                             
                $this->session->set_flashdata('error', $error);
                return redirect('course_public/register_enroll/'.$course_id.'/'.$class_id); 
            }
              
            }
    }
    
    public function create_classenroll1($user_id,$user_password,$course_id,$class_id,$tax_code,$registration=null,$friend_id=null,$relation=null) 
    {

        $taxcode = $tax_code;
        $res_found1 = $this->input->post('res_found1');

            $taxcode_details = $this->course_public_model->validate_taxcode_data(trim($taxcode));
            
            $data['class_details'] = $class_details = $this->course_public_model->get_class_details($class_id);
            $data['course_details'] = $course_details = $this->course_public_model->course_basic_details($class_details->course_id);

           $is_enrolled = $this->course_public_model->is_user_enrolled1($taxcode_details->user_id, $class_id, $course_id);
            
            $data['user_id'] = $user_id; 
            $data['refer_id'] = $friend_id; 
            $data['relation'] = $relation;
            $data['trainee_data'] = $taxcode_details;
            $data['course_id'] = $course_id;
            $data['class_id'] = $class_id;
            $data['class_name'] = $class_details->class_name;
            $data['class_fees'] = $class_details->class_fees;
//            $data['discount_rate'] = $class_details->dis  count_rate;
//            $data['discount_amount'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
            /* calculate discount type and amount based on class and individual discount skm start*/
            $discount_total=0;
            
            $data['discount_type']='DISINDVI';
            $data['discount_rate'] = $class_details->class_discount;
            $data['indv_class_details'] = $indv_class_details = $this->course_public_model->get_indv_class_details($course_id,$user_id);
            $data['discount_amount'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
            $feesdue=$class_details->class_fees;
            if($discount_total>0)
            {
                $data['discount_type']='DISCLASS';  
                $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total); 
            }
            
            if($discount_total==0)
            {
                if($indv_class_details->discount_amount==0 && $indv_class_details->discount_percent!=0)
                {
                    $data['discount_amount'] = $discount_total = 
                       round(($indv_class_details->discount_percent / 100) * $class_details->class_fees, 2);
                    $data['discount_type']='DISINDVI';
                    $data['discount_rate']=$indv_class_details->discount_percent;
                     $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total); 
                    
                }
                if($indv_class_details->discount_amount!=0 && $indv_class_details->discount_percent==0){
                    $data['discount_amount'] = $discount_total = $indv_class_details->discount_amount;
                    $data['discount_type']='DISINDVI';
                    $data['discount_rate']= round(($indv_class_details->discount_amount / $class_details->class_fees) * 100 , 2);
                     $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total); 
                }
            }
            
//            if($indv_class_details == 0 && $discount_total == 0)
//            {
//                $data['discount_type']='DISINDVI';
//                 $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total); 
//            }
            /* end*/
            
            $data['gst_rate'] = $gst_rate = $this->course_public_model->get_gst_current();
            
            
            //$data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);           
            $data['gst_amount'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
            
            $data['net_due'] = $net_due = $feesdue + $totalgst;
            $meta_result = $this->meta_values->get_param_map();
            $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
            $data['class_type'] = $meta_result[$course_details->class_type];
            $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
            $data['gst_onoff'] = $course_details->gst_on_off;
            $data['gst_rule'] =  $course_details->subsidy_after_before;

            if ($is_enrolled == 0) { 

                        $data['enrol_status'] = 'ENRLBKD';
                        $data['pay_status'] = 'NOTPAID';
//                        echo "<br/>"; print_r($data);
                        $result = $this->course_public_model->create_new_enroll($data);
//                        exit();
                        if ($result["status"] == FALSE) {
                            $error = 'We were not able to complete the enrollment in the class. Please try again later or contact your Administrator.';
//                                Click <a href="' . base_url() . 'course/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';
                            $this->session->set_flashdata('error', $error);
                            //return redirect('course/class_enroll/' . $class_details->course_id . '/' . $class_details->class_id);
                            return redirect('course_public/course_class_schedule/' . $class_details->course_id);
                            
                        } else {
//                            echo "else part"; echo "<br/>";
                            /* session start when user select course &class & register and then enroll in class successfully*/ 
                            
                            
                            $this->load->model('user_model');
                            $session_user_id = $this->session->userdata('userDetails')->user_id;                            
                            if(empty($session_user_id)){
                                $resp = $this->user_model->register_login_process($data['user_id']);
                                if(!empty($resp))
                                {
                                    $this->session->set_userdata('userDetails', $resp);
                                }//end
                            }
                            
                           
                             $message = 'You have successfully booked a seat. Click <a href="' . base_url() . 'course_public/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';
                             $data['success_message'] = $message;
                            
                            // skm start fetch user details when register with course aand class
                            
                            $course_name = $this->user_model->course_name($data['course_id']);
                            $user_result = $this->user_model->r_userDetails($data['user_id']);

                            $user_mailer_details = array('username' => $this->input->post('user_name'),
                                                'email' => $this->input->post('frnd_registered_email'), 
                                                'password' => $user_password,
                                                'firstname' => strtoupper($this->input->post('pers_first_name')),
                                                'lastname' => strtoupper($this->input->post('pers_second_name')),
                                                'gender' => $this->input->post('pers_gender'));
                            
                             /* when user loggedin and enroll for some */
                            $res = $this->user_model->r_userDetails($friend_id);
                            $loggedin = $this->input->post('loggedin');
                            if( $loggedin == 1 && !empty($this->session->userdata('userDetails')->user_id) && $res_found1 == '')
                            {
                                    $user_data = $this->user_model->r_userDetails($r_user_id);
                                    $r_someone = array(
                                                        'firstname' => strtoupper($res->first_name),
                                                        'lastname' => strtoupper($res->last_name),
                                                        'email' => $res->registered_email_id
                                                      );
                                    $this->course_public_model->send_reg_someone_referance_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance
                                    $this->course_public_model->send_reg_someone_referal_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance referal
                                    $this->course_public_model->send_tenant_mail($user_details, 'BPEMAC'); // tenent email
                                  
                            }
                            else
                            {
                                $this->course_public_model->send_trainee_email($user_mailer_details, 'BPEMAC');
                            }
                            
                            if(!empty($friend_id))
                            {    
                                $user_details = array(
                                                        'email' => $user_result->registered_email_id,
                                                        'firstname' => strtoupper($user_result->first_name),
                                                        'lastname' => strtoupper($user_result->last_name),
                                                        'gender' => $user_result->gender,
                                                        'course_name' => $course_name,
                                                        'class_name' => $class_details->class_name,
                                                        'class_start' => $class_details->class_start_datetime,
                                                        'class_end' => $class_details->class_end_datetime,
                                                        'class_venue' => $data['classloc'],
                                                        'r_firstname' => strtoupper($res->first_name),
                                                        'r_email' => $res->registered_email_id
                                                      );
                                    $this->course_public_model->send_referance_email_someone($user_details, 'BPEMAC'); 
                                    $this->course_public_model->send_referal_email_someone($user_details, 'BPEMAC'); 
                            }  else {
                                $user_details = array(
                                              'email' => $user_result->registered_email_id,
                                              'firstname' => strtoupper($user_result->first_name),
                                              'lastname' => strtoupper($user_result->last_name),
                                              'gender' => $user_result->gender,
                                              'course_name' => $course_name,
                                              'class_name' => $class_details->class_name,
                                              'class_start' => $class_details->class_start_datetime,
                                              'class_end' => $class_details->class_end_datetime,
                                              'class_venue' => $data['classloc'],
                                            );
                            
                                
                                 $this->course_public_model->send_reg_enroll($user_details, 'BPEMAC'); 
                                  
                            }
//                      
                            $_POST = array(
                                'user_id' => $data['user_id'],
                                'class_id' => $data['class_id'],
                            );
                            $data['booking_ack'] = $this->_get_booking_ack_data();
                            
                            $data['page_title'] = 'Enrol';
                            $data['main_content'] = 'enrol/message_status';
                            $this->load->view('layout_public', $data);
                            
                        }
              
                

            }else{
                    $session_user_id = $this->session->userdata('userDetails')->user_id;                            
                    if(empty($session_user_id)){
                        $resp = $this->user_model->register_login_process($data['user_id']);
                        if(!empty($resp))
                        {
                            $this->session->set_userdata('userDetails', $resp);
                        }
                    }
                
                $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'.';
                $this->session->set_flashdata('error', $error);
                //return redirect('course/class_enroll/' . $class_details->course_id . '/' . $class_details->class_id);
                return redirect('course_public/course_class_schedule/' . $class_details->course_id);
                
            }
  }
    
    private function _get_booking_ack_data() {
        $tenant_id = TENANT_ID;
        $trainee_id = $this->input->post('user_id');
        $class_id = $this->input->post('class_id');
        $trainee_det = $this->course_public_model->get_user_data($trainee_id);
        $trainee_name = $trainee_det->first_name . ' ' . $trainee_det->last_name;
        $trainee = ($trainee_det->gender == 'MALE') ? 'Mr. ' . $trainee_name : 'Ms. ' . $trainee_name;
        $class_details = $this->course_public_model->get_class_details($class_id);
        $meta_map = $this->meta_values->get_param_map();
        $ClassLoc = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_map[$class_details->classroom_location];
        $course_details = $this->course_public_model->course_basic_details($class_details->course_id);
        $course_manager = $this->course_public_model->get_managers($course_details->crse_manager);
        $length = stripos($course_manager, ', ');
        $coursemanager = (!empty($length)) ? substr($course_manager, 0, $length) : $course_manager;
        $tenant_details = $this->course_public_model->get_tenant_masters($tenant_id);

        $tenant_details->tenant_country = $meta_map[$tenant_details->tenant_country];
        $courseLevel = $meta_map[$course_details->certi_level];

        $contact_details .= $tenant_details->contact_name;



        if (!empty($tenant_details->tenant_contact_num)) {
            $contact_details .='(Phone: ' . $tenant_details->tenant_contact_num . ', ';
        }
        if (!empty($tenant_details->tenant_email_id)) {
            $contact_details .='Email Id: ' . $tenant_details->tenant_email_id . ')';
        }
        $contact_details = rtrim($contact_details, ', ');
        $message = 'A seat has been temporarily booked. Please pay the class fees on or before the class start date.
            Temporary booking for <strong>' . $trainee . '</strong> for \'Course: ' . $course_details->crse_name . ', Class: ' . $class_details->class_name . ', Certificate Code: ' . $courseLevel . '\'.';
        $booking_details = $this->course_public_model->get_paydue_invoice($trainee_id, $class_id);

        $message2 = '<p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD \'<b>' . number_format($booking_details->total_inv_amount, 2, '.', '') . '</b>\' from <b>\'' . $trainee . '\' </b> for  <b>\'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'</b>. Mode of payment:<b>' . ONLINE . '</b></p>'; 
        
        /* skm code start for remark.
            reporting time skm start bcoz of sam request for AOP(67) on 18-may-17*/
        $time = strtotime($class_details->class_start_datetime);
        $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
            if($class_details->course_id == 67 || $class_details->course_id == 121)
            {
                 $li = "Report at center at $reporting_time to register for class";
            }else{
                 $li = "Report at center at 8:30 AM to register for class";
            }
        /* end */
            $message3 = '<strong>Remark *: </strong>
             <ol>
                            <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>'.$li.'</li>
                        </ol>';
            
            if(TENANT_ID == 'T20'){////added by shubhranshu due to points fow wablab
                $message3 = '<strong>Remark *: </strong>
             <ol>
                        
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>'.$li.'</li>
                        </ol>';
            }
          
        /* skm end */
        if ($booking_details) {
            $booking_no = date('Y', strtotime($booking_details->inv_date)) . ' ' . $booking_details->invoice_id;
            $booking_date = date('d/m/Y', strtotime($booking_details->inv_date));
        } else {
            $booking_no = date('Y') . ' ' . $trainee_id;
            $booking_date = date('d/m/Y');
        }
        return array(
            'trainee_id' => $trainee_id,
            'class_id' => $class_id,
            'message' => $message,
            'message2' => $message2, 
            'message3' => $message3,//skm1
            'loc' => $ClassLoc,
            'start_date' => date('M d, Y h:i A', strtotime($class_details->class_start_datetime)),
            'contact' => $contact_details,
            'book_no' => $booking_no,
            'book_date' => $booking_date,
            'tenant_det' => $tenant_details
        );
    }
    
    
    public function reg($course_id,$class_id,$tax_code,$registration=null)
    {   
//        echo "<br/>";
//        echo current_url();echo"<br/>";
//        echo base_url().'/course/register_enroll';
//        echo "<br/>";
        
                $this->load->helper('metavalues_helper', 'common');
                $this->load->model('meta_values');
                $taxcode = $tax_code;

                if (!empty($taxcode)) {
                   $taxcode_details = $this->course_public_model->validate_taxcode_data(trim($taxcode));
                }
//                print_r($taxcode_details);
                $error = '';
            

                if (empty($taxcode_details)) {    

                    $error = 'We have not been able to find your credentials in our system. '
                            . 'Kindly click <a href="' . base_url() . '/user/add_trainee">here</a> to register your account.';
                } else {

                    if (trim($taxcode_details->account_type) != 'TRAINE') {
                        $error = 'We have  been able to find your credentials in our system. '
                                . 'But you  are not registered as trainee. '
                                . 'Kindly get in touch with your administrator to register you as a Trainee, before proceeding class enrollments.';
                    } elseif (trim($taxcode_details->account_status) != 'ACTIVE') {

                        $error = 'We have found your credentials. But your account is not ACTIVE. '
                                . ' Kindly get in touch with your administrator to activate your account before proceeding with enrollment.';
                    } elseif (trim($taxcode_details->tenant_id) != TENANT_ID) {

                        $tenant_master_result_set = $this->course_public_model->get_tenant_name(trim($taxcode_details->tenant_id));

                        if (empty($tenant_master_result_set)) {
                            $error = 'Your details have been found linked to an invalid training institute.'
                                    . ' Kindly get in touch with your administrator to validate your registration details.';
                        } else {
                            $tenant_details = $this->session->userdata('public_tenant_details');
                            $user_name = $taxcode_details->first_name;
                            if (!empty($taxcode_details->last_name))
                                $user_name .= $taxcode_details->last_name;
                            $error = 'Welcome ' . $user_name . ', we have found your credentials in our system.'
                                    . ' But you are currently registered with the training institute "' . $tenant_master_result_set->tenant_name . '". '
                                    . 'This portal belongs to the training institute "' . $tenant_details->tenant_name . '". '
                                    . 'Kindly get in touch with your administrator to enroll in classes for this training institute OR please access your training institute portal.';
                            }
                        }

                    }
                    
//                echo $error; echo "<br/>";   

                    if (!empty($error)) { 
//                        echo "error msg";
                        $this->session->set_flashdata('error', $error);
                        $this->session->set_userdata('prev_tax_code', $taxcode);
                       if($registration == 1){
                            return redirect(base_url().'course/register_enroll'); 
                        }else{ return redirect(current_url());  } 
                        
                    } else {
                      
                        $data['class_details'] = $class_details = $this->course_public_model->get_class_details($class_id);
                      
                        $data['course_details'] = $course_details = $this->course_public_model->course_basic_details($class_details->course_id);
                         $is_enrolled = $this->course_public_model->is_user_enrolled1($taxcode_details->user_id, $class_id, $course_id);
                        
                        
                        if ($is_enrolled!= 0) {
//                            echo "error msg if";
                             $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. '
                                    . 'Please click <a href="' . base_url() . 'course/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';
                            $this->session->set_flashdata('error', $error);
                            $this->session->set_userdata('prev_tax_code', $taxcode);
//                              
                                if($registration == 1){
                                   return redirect(base_url().'course/register_enroll'); 
                                }else{ return redirect(current_url());  }
                               
                        } else {
//                             echo "error msg else";
                             
                            $gender = ($taxcode_details->gender == 'FEMALE') ? 'Ms. ' : 'Mr. ';
                            $user_name = $gender . $taxcode_details->first_name . ' ' . $taxcode_details->last_name;
                            $user_name = rtrim($user_name, ' ');
                            $data['success'] = 'Welcome ' . $user_name . ', please proceed with enrollment (or) click <a href="' . base_url() . 'course/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';
                            $data['user_id'] = $taxcode_details->user_id;
                            //Added for CR03
                            $data['additional_remarks'] = $taxcode_details->additional_remarks;
                            $data['trainee_data'] = $taxcode_details;
                            $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
                            $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
                            $data['gst_rate'] = $gst_rate = $this->course_public_model->get_gst_current();
                            $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
                            $data['net_due'] = $net_due = $feesdue + $totalgst;
                            $meta_result = $this->meta_values->get_param_map();
                            $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
                            $data['class_type'] = $meta_result[$course_details->class_type];
                            $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
                            $data['main_content'] = 'course/payment_details';
                            return $this->load->view('layout_public', $data);

                        }

                    }

               
        
        
    }

    /*
     *  This Method for checks if user name already exists.
     */

    public function check_username() {
        extract($_POST);
        $user_name = trim(($username));
        $exists = $this->user_model->check_username($user_name);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        return;
    }

    /*
     * This Method for checks if email already exists.
     */

    public function check_email() {
        extract($_POST);
        $email_add = trim(($email));
        $exists = $this->user_model->check_email($email_add);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        return;
    }

    /*
     * This Method for checks if tax code already exists.    
     */

    public function check_taxcode() {
        extract($_POST);
        $tax_code = trim(($tax_code));
        if ($country_of_residence == "SGP") {
            $this->load->helper('common');
            $valid = validate_nric_code($nric, $tax_code);
            if ($valid == FALSE) {
                echo 2;
                return;
            }
        }
       
        $exists = $this->user_model->check_taxcode($tax_code);
       
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /*    
     * This Method for checks if tax code already exists.    
     */

    public function check_taxcode_with_data() {
        extract($_POST);
        $tax_code = trim(($tax_code));
        $tenant_id = TENANT_ID; 
        if ($country_of_residence == "SGP") {
            $this->load->helper('common');
            $valid = validate_nric_code($nric, $tax_code);
            if ($valid == FALSE) {
                $val = 2;
                return;
            }
        }
        $exists = $this->user_model->check_taxcode($tax_code); 
       
        if ($exists) {
            $val = 1;
        } else {
            $val = 0;
        }
        $value = array();
        if ($val == 1) {
            $this->load->model('course_public_model');
            $value = $this->course_public_model->get_user_id_from_taxcode($tax_code);
            $this->load->model('meta_values');
            $meta_map = $this->meta_values->get_param_map();
            $value->personal_address_country = $meta_map[$value->personal_address_country];
            $value->personal_address_state = $meta_map[$value->personal_address_state];
            if (!empty($value->personal_address_bldg)) {
                $contact = $value->personal_address_bldg . ', ';
            }
            if (!empty($value->personal_address_city)) {
                $contact .= $value->personal_address_city . ', ';
            }
            if (!empty($value->personal_address_state)) {
                $contact .= $value->personal_address_state . ', ';
            }
            if (!empty($value->personal_address_country)) {
                $contact .= $value->personal_address_country . ', ';
            }
            $contact = rtrim($contact, ', ');
            $value->suffix = '';
            if (!empty($contact)) {
                $value->suffix = ' from ' . $contact;
            }
        }
        echo json_encode(array('data' => $value, 'val' => $val));
        exit();
    }

    /*    
     * Function  to  check  the  uniqueness of  taxcode in server side validation   
    */

    function check_unique_usertaxcode() {
        $country_of_residence = trim($this->input->post('country_of_residence'));
        if ($country_of_residence == "IND") {

            $tax_code = $this->input->post('PAN');
        }
        if ($country_of_residence == "SGP") {

            $tax_code = $this->input->post('NRIC');
        }
        if ($country_of_residence == "USA") {

            $tax_code = $this->input->post('SSN');
        }
        if ($tax_code) {
            $exists = $this->user_model->check_duplicate_user_taxcode($tax_code);
            if (!$exists) {
                $this->form_validation->set_message('check_unique_usertaxcode', "Taxcode " . $tax_code . " exists.");
                return FALSE;
            }
            return TRUE;
        }
    }

    /*
     * Function  to get the states list based on  country parameter     
     */

    public function get_states_json() {
        $country_param = trim($this->input->post('country_param'));
        if (!empty($country_param)) {
            $states = $this->user_model->get_states($country_param);
        }
        $states_arr = array();
        foreach ($states as $item) {
            $states_arr[] = $item;
        }
        echo json_encode($states_arr);
        exit;
    }

    /*
     * Function  to  check the unique username-used in  server  side  validation
     */

    function check_unique_username() {
        $user_name = trim($this->input->post('user_name'));
        if ($user_name) {

            $exists = (!preg_match('/^[a-zA-Z0-9]+$/', $user_name)) ? FALSE : TRUE;
            if (!$exists) {
                $this->form_validation->set_message('check_unique_username', "Invalid Username");
                return FALSE;
            } else {
                $exist = $this->user_model->check_duplicate_user_name($user_name);
                if (!$exist) {
                    $this->form_validation->set_message('check_unique_username', "Username $user_name already exists.");
                    return FALSE;
                }
                return TRUE;
            }
        }
    }

    /*
     * Function  for  adding  rows in  register page 
     */

    public function addmore($add) {

        $this->load->helper('metavalues_helper');
        $this->load->helper('form');
        $this->load->model('meta_values');
        $add = $this->input->post('add');
        $rowno = $this->input->post('rowno') + 1;

        if (!$add || $add == '') {
            echo "Current row must have a value.";
            exit;
        }

        if ($add == 'addedu') {

            $edulevel = fetch_metavalues_by_category_id(Meta_Values::EDU_LEVEL);
            $edulevel_options[''] = 'Select';
            foreach ($edulevel as $item):
                $edulevel_options[$item['parameter_id']] = $item['category_name'];
            endforeach;

            $edu_level = form_dropdown('edu[' . $rowno . '][edu_level]', $edulevel_options, '', ' rowno="' . $rowno . '" class="edu_level"');

            $score_grade = array(
                'name' => 'edu[' . $rowno . '][score_grade]',
                'value' => set_value('score_grade')
            );
            $year_of_comp = array(
                'name' => 'edu[' . $rowno . '][year_of_comp]',
                'id' => 'year_of_comp',
                'value' => set_value('year_of_comp')
            );
            $edu_remarks = array(
                'name' => 'edu[' . $rowno . '][edu_remarks]',
                'value' => set_value('edu_remarks')
            );

            $res = '<tr>
                            <td>' . $edu_level . '
                            </td>
                            <td>' . form_input($year_of_comp) . '</td>
                            <td>' . form_input($score_grade) . '</td>
                            <td>' . form_input($edu_remarks) . ' </td>
                            <td><span class="pull-right remove_img"></span> </td>
                          </tr>';
        }
        else if ($add == 'addother') {

            $certi_name = array(
                'name' => 'other[' . $rowno . '][certi_name]',
                'id' => 'certi_name',
                'class' => 'certi_name',
                'rowno' => $rowno,
                'value' => set_value('certi_name')
            );
            $year_of_certi = array(
                'name' => 'other[' . $rowno . '][year_of_certi]',
                'id' => 'year_of_certi',
                'value' => set_value('year_of_certi')
            );
            $validity = array(
                'name' => 'other[' . $rowno . '][validity]',
                'id' => 'validity_' . $rowno,
                'value' => set_value('validity')
            );
            $other_remarks = array(
                'name' => 'other[' . $rowno . '][other_remarks]',
                'id' => 'other_remarks',
                'value' => set_value('other_remarks')
            );
            $res = ' <tr>
                        <td>' . form_input($certi_name) . '</td>
                        <td>' . form_input($year_of_certi) . '</td>
                        <td>' . form_input($validity) . '</td>
                        <td>' . form_input($other_remarks) . '</td>
                        <td><span class="pull-right remove_img"></span> </td>
                        
                      </tr>';
        } else if ($add == 'addwork') {

            $org_name = array(
                'name' => 'work[' . $rowno . '][org_name]',
                'id' => 'org_name',
                'class' => 'org_name',
                'rowno' => $rowno,
                'value' => set_value('org_name')
            );

            $empfrom = array(
                'name' => 'work[' . $rowno . '][empfrom]',
                'id' => 'empfrom_datetimepicker_' . $rowno,
                'value' => set_value('empfrom')
            );
            $empto = array(
                'name' => 'work[' . $rowno . '][empto]',
                'id' => 'empto_datetimepicker_' . $rowno,
                'value' => set_value('empto')
            );
            $designation = fetch_metavalues_by_category_id(Meta_Values::DESIGNATION);
            $designation_options[''] = 'Select';
            foreach ($designation as $item):
                $designation_options[$item['parameter_id']] = $item['category_name'];
            endforeach;
            $attr_designation = 'id="designation';
            $designation = form_dropdown('work[' . $rowno . '][designation]', $designation_options, set_value('designation'), $attr_designation);

            $res = '<tr>
                        <td>' . form_input($org_name) . '</td>
                        <td>' . form_input($empfrom) . '</td>
                        <td>' . form_input($empto) . '</td>
                        <td>' . $designation . '</td>
                        <td><span class="pull-right remove_img"></span> </td>
                      </tr>';
        }

        echo $res;
    }

    /* 
     * Function  to  save the  refer  trainee data 
     */

    public function update_refer_trainee() {
        
        if ($_POST) {
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $country_of_residence = $this->input->post('country_of_residence');
            if ($country_of_residence == 'IND') {
                $this->form_validation->set_rules('PAN', 'PAN Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
            }
            if ($country_of_residence == 'SGP') {
                $this->form_validation->set_rules('NRIC', 'NRIC/ FIN No. Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
                $NRIC = $this->input->post('NRIC');
                $NRIC_ID = trim($this->input->post('NRIC_ID'));
                $valid = validate_nric_code($NRIC, $NRIC_ID);
                if ($valid == FALSE) {
                    $data['error_message'] = 'Invalid NRIC Code';
                    $data['main_content'] = 'addtrainee';
                    $this->load->view('layout_public', $data);
                    return;
                }
            }
            if ($country_of_residence == 'USA') {
                $this->form_validation->set_rules('SSN', 'SSN Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
            }
            $this->form_validation->set_rules('user_name', 'Username', 'required|max_length[15]|callback_check_unique_username');
            $this->form_validation->set_rules('pers_first_name', 'Firstname', 'required|max_length[50]');
            $this->form_validation->set_rules('pers_gender', 'Gender', 'required');
            $this->form_validation->set_rules('pers_contact_number', 'Contact Number', 'required|max_length[50]');
            if ($this->form_validation->run() == TRUE) {
                $uid = $this->user_model->save_trainee();
            }
        }
    }
     /*
     * This method for updating the refer trainee profile.
     * Author : Bineesh.
     * Date : 30 Oct 2014.
     */

    public function update_refer_trainee1() {
        //Server side validation starts.
        $validation_status = $this->server_validation();
        //Server side validation end.
        if ($validation_status == TRUE) {
            $delete_image = $this->input->post('deleteimage') ? $this->input->post('deleteimage') : 'no';
            // updating profile details.
            $uid = $this->user_model->update_my_profile();
            if ($uid == FALSE) {
                $this->session->set_flashdata('error', 'Unable to update Trainee.Please try again later.');
            } else {
                // uploading profile image.                    
                $upload_status = $this->upload_profile_image($uid, $delete_image);
                if ($upload_status == FALSE) {
                    $this->session->set_flashdata('Success', 'Trainee has been updated successfully.<br>Image is not uploaded.');
                } else {
                    $this->session->set_flashdata('Success', 'Trainee has been updated successfully');
                }
            }
            redirect('user/referral_list');
        } else {
            $refer_user_id = $this->input->post('userid');
            $this->edit_refer_trainee($refer_user_id);
        }
    }

    /**
     * This method used for landing page display     
     */
    public function landing_page() {
        $data['page_title'] = 'Landing Page';        
        $data['main_content'] = 'public_landing';
        $this->load->view('landing_page_layout', $data);        
    }
     /**
     * Author : Pritam     
     * This method for server side validation in myprofile and refer_trainee.
     * Date : 30 may 2016.
     */
    private function server_validation() {
//        $country_of_residence = $this->input->post('country_of_residence');
//        if ($country_of_residence == IND) {
//            $this->form_validation->set_rules('PAN', 'PAN Number', 'required|max_length[50]|callback_check_unique_usertaxcode_edit');
//        }
//        if ($country_of_residence == SGP) {
//            $this->form_validation->set_rules('NRIC', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode_edit');
//        }
//        if ($country_of_residence == USA) {
//            $this->form_validation->set_rules('SSN', 'SSN Number', 'required|max_length[50]|callback_check_unique_usertaxcode_edit');
//        }
        $this->form_validation->set_rules('pers_first_name', 'Firstname', 'required|max_length[50]');
        $this->form_validation->set_rules('pers_second_name', 'Secondname', 'max_length[50]');
        $this->form_validation->set_rules('pers_gender', 'Gender', 'required');
//        $this->form_validation->set_rules('pers_dob', 'Date of Birth', 'max_length[10]');
//        $this->form_validation->set_rules('pers_contact_number', 'Contact Number', 'required|max_length[50]');
        if ($this->input->post('pers_alternate_contact_number')) {
            $this->form_validation->set_rules('pers_alternate_contact_number', 'Alternate Contact Number', 'required|max_length[50]');
        }
//        $this->form_validation->set_rules('user_registered_email', 'Email', 'valid_email|matches[pers_conf_email]');
//        $this->form_validation->set_rules('frnd_registered_email', 'Email', 'valid_email|matches[frnd_conf_email]');
//        if ($this->input->post('pers_conf_email')) {
//            $this->form_validation->set_rules('pers_conf_email', 'Confirm Email', 'valid_email');
//        }
        if ($this->input->post('pers_alternate_email')) {
            $this->form_validation->set_rules('pers_alternate_email', 'Alternate Email', 'valid_email|max_length[50]');
        }
        $this->form_validation->set_rules('nationality', 'Nationality', 'required|max_length[10]');
        $this->form_validation->set_rules('occupation', 'Occupation', 'required|max_length[10]');
        $this->form_validation->set_rules('highest_educ_level', 'Highest Education Level', 'required|max_length[10]');
        $this->form_validation->set_rules('pers_personal_address_bldg', 'Address', 'max_length[255]');
        $this->form_validation->set_rules('pers_city', 'City', 'max_length[50]');
        // unsetting the old email id and tax code.
        $array_items = array('registered_email_id_edit' => '', 'tax_code_edit' => '');
        $this->session->unset_userdata($array_items);
        if ($this->form_validation->run() == TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
     /**
     * Author : Bineesh     
     * This method for uploading trainee images to admin folder.
     * Date : 30 Oct 2014.
     */
    private function upload_profile_image($uid, $delete_image) {
        $this->load->helper('upload_helper');
        if (!empty($_FILES['userfile']['name']) && $uid != FALSE && $delete_image == 'no') {
            $image_data = upload_image('uploads/images/trainee', $uid);
            if ($image_data['status']) {
                $image_path = $image_data['image']['system_path'] . '/' .
                        $image_data['image']['raw_name'] . '_thumb' . $image_data['image']['file_ext'];
                //remove previous image
                $previous_thumb_path = fetch_image_path_by_uid($uid);
                remove_previous_image($previous_thumb_path);
                save_image_path($uid, $image_path);
            } else {
                return FALSE;
            }
        } else if ($uid != FALSE && $delete_image == 'no') {
            //remove previous image
            $previous_thumb_path = fetch_image_path_by_uid($uid);
            remove_previous_image($previous_thumb_path);
            save_image_path($uid);
        }
        return TRUE;
    }
    /**
     
     * Modified By:Pritam
     * 37/06/16
     * This function to enroll the user to a class.
     */
    public function enroll_now() {
        
        $data['page_title'] = 'Enrol';
        $data['pay'] = $this->input->get('pay');
        $action = $_REQUEST['action'];
        $parm['class_id'] = $this->input->get('cls');
        $parm['course_id'] = $this->input->get('crs');
        $parm['enrol_to'] = $this->input->get('enrolto');
        if (empty($parm['enrol_to'])) {
            $parm['enrol_to'] = $this->session->userdata('userDetails')->user_id;
        }
       
        $data['details'] = $this->courses_model->get_class_details($parm);
       
//        $data['ack_data'] = $this->courses_model->get_ack_data($parm); ## commented by sankar added atlast
        $data['discount'] = $this->calculate_discount($data['details']);
         
        $fee_details = $this->calculate_fee($data['details'], $data['discount']);
       
        $parm['discount_amount'] = $fee_details['discount_amount']; ### added by sankar
        
        $parm['amount'] = $fee_details['net_fee'];
        $parm['discount'] = $fee_details['discount'];
        $parm['discount_type'] = $fee_details['discount_type'];
        $parm['class_fee'] = $fee_details['class_fee'];
        $parm['payment_status'] = NOT_PAID; ### changed by sankar
        $parm['gstrate'] = $fee_details['gstrate']; ### added by sankar
        $parm['gstamount'] = $fee_details['gstamount'];
        $data['fee'] = $fee_details;
 
        if ($action == 'enroll') {
           
            $exists = $this->courses_model->check_user_enrol_exists($parm);
            if (!$exists) {
                $this->db->trans_start();
                $enrolled = $this->courses_model->insert_class_enroll($parm);
                $this->db->trans_complete();
                if ($enrolled) {
                    $data['enrolled']['type'] = PAY_AFTER_ENROL;
                    $data['enrolled']['enrolled_to'] = $this->get_username_by_userid($parm['enrol_to']);
                    $data['msg'] = 'You have enrolled successfully.';
                } else {
                    $data['error'] = 'Error!! While enrollment.';
                }
            } else {
                $data['error'] = 'You have already enrolled for this class.';
            }
        } else if ($action == 'success') {

            if ($this->session->userdata('enrol_to')) {
                $parm['enrol_to'] = $this->session->userdata('enrol_to');
                $pay = $this->session->userdata('payment_update');
                $parm['payment_status'] = PAID;
                $this->db->trans_start();
                if ($pay) {
                    $parm['invoiceId'] = $this->courses_model->get_invoice_id($parm);
                    $this->courses_model->update_payment_status($parm);
                } else {
                    $parm['invoiceId'] = $this->courses_model->insert_class_enroll($parm);
                }
                $parm['mode_of_pymnt'] = 'ONLINE';
                $this->courses_model->insert_invoice_record($parm);
                $this->db->trans_complete();
                $data['enrolled']['type'] = PAY_DURING_ENROL;
                $data['enrolled']['enrolled_to'] = $this->get_username_by_userid($parm['enrol_to']);
                if ($pay) {
                    $data['msg'] = 'Payment updated successfully.';
                } else {
                    $data['msg'] = 'Your transaction is successful. You have enrolled successfully.';
                }
            } else {
                $data['error'] = 'Error!! While enrollment.';
            }
        } else if ($action == 'cancel') {
            $data['error'] = 'Alert!! Transaction Cancelled.';
        } else if ($action == 'ipn') {
            $this->load->library('paypal_class');
            $p = new paypal_class(); // paypal class
       
            if ($p->validate_ipn()) {
                $parm['enrol_to'] = $this->session->userdata('enrol_to');
                $this->courses_model->insert_class_enroll($parm);
                $data['msg'] = 'Payment done. You have enrolled successfully.';
            } else {
                $data['error'] = 'Instant Payment Failed.';
            }
        }
        if ($this->input->get('exists') == 1) {
            $data['error'] = 'You have already enrolled for this class.';
        }
        //code modification by sankar, on 17/03/2015
        $data['enrl_det'] = '';
        $data['ack_data'] = $this->payments_model->get_acknowledgement_data($parm['class_id'],$parm['course_id'],$parm['enrol_to']);
        $data['details']['class']['classroom_location'] = $this->meta_data[$data['details']['class']['classroom_location']];
        $data['main_content'] = 'courseviews/enrol';
        $this->load->view('layout_public', $data);
    }
    /* Function  to calculate discount amount,discount type
     * Author:Pritam */

    public function calculate_discount($details) {

        $userid = $this->input->get('enrolto');
        if (!$userid) {
            $userid = $this->session->userdata('userDetails')->user_id;
        }

        // get the individual discount
        $discount['discount'] = $this->courses_model->get_individual_discount($userid, $details['course']['id']);
        $discount['discount_type'] = 'DISINDVI';
        if (!$discount['discount']) {
            $discount['discount'] = $details['class'][class_discount];
            $discount['discount_type'] = 'DISCLASS';
        }

        if ($discount['discount']) {
            return $discount;
        } else {
            return array(
                'discount' => 0,
                'discount_type' => 'DISCLASS'
            );
        }
    }
    /**
    
     * Author: pritam
     * 07/06/16
     * calculate fee
     * */
    public function calculate_fee($details, $discount) {

        if ($discount['discount'] > 0) {
            $discount_amount = ($details['class']['class_fees'] / 100) * $discount['discount'];
            $feeafterdiscount = $details['class']['class_fees'] - $discount_amount;
        } else {
            $feeafterdiscount = $details['class']['class_fees'];
        }

        $isgst = $details['course']['isgst'];
        if ($isgst) {

            $gstamount = ($feeafterdiscount / 100 ) * $details['gst'];
            $fee['gstamount'] = number_format($gstamount, 2, '.', '');
            $fee['gstrate'] = $details['gst'];
            $fee['net_fee'] = $feeafterdiscount + $fee['gstamount'];
        } else {

            $fee['net_fee'] = $feeafterdiscount;
        }

        $fee['gstrate'] = number_format($details['gst'], 2, '.', '');
        $fee['net_fee'] = number_format($fee['net_fee'], 2, '.', '');
        $fee['discount'] = number_format($discount['discount'], 2, '.', '');
        $fee['class_fee'] = $details['class']['class_fees'];
        $fee['discount_amount'] = $discount_amount;
        $fee['feeafter_discount'] = $feeafterdiscount;
        $fee['discount_type'] = $discount['discount_type'];

        return $fee;
    }
      /* Function  to  get  username
      Author:pritam */

    public function get_username_by_userid($userid) {
        if (empty($userid)) {
            return FALSE;
        }
        return $this->courses_model->get_username_by_userid($userid);
    }
    
    /*
     * This method for forgot password screen loading.
     * Date : 08 June 2016.
     * Author : Pritam
     */

    public function forgot_password() {
        $this->session->unset_userdata('public_captcha_key');///added by shubhranshu
        unlink(FCPATH .'captcha/'.$this->session->userdata('public_captcha_file')); // added by shubhranshu to delete the captcha file
        $data['captcha']=$this->generateCaptcha(); // added by shubhranshu for capctha entry
        $data['page_title'] = 'Forgot Password';
        $this->load->view('forgot_password', $data);
    }
    /*
     * This method for generating mail in forgot password.
   
     */
    /// below function was added by shubhranshu for captcha validation
    public function generateCaptcha(){
        $this->load->helper('captcha');
        $vals = array(
                'word'          => '',
                'img_path'      => FCPATH.'captcha/',
                'img_url'       => base_url().'captcha/',
                'font_path'     => FCPATH .'assets/fonts/ATBramley-Medium.ttf',
                'img_width'     => '131',
                'img_height'    => 30,
                'expiration'    => 7200,
                'word_length'   => 6,
                'font_size'     => 16,
                'img_id'        => 'Imageid',
                'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

                // White background and border, black text and red grid
                'colors'        => array(
                        'background' => array(255, 255, 255),
                        'border' => array(255, 255, 255),
                        'text' => array(0, 0, 0),
                        'grid' => array(255, 40, 40)
                )
        );
        
        $cap = create_captcha($vals);
        $this->session->set_userdata('public_captcha_file', $cap['filename']);
        $this->session->set_userdata('public_captcha_key', $cap['word']);
        
        return $cap['image'];
    }/////////////////////////end ssp///////////////////////
    public function get_forgot_password() {
        // get the email id and DOB from the form and the forgot param i.e. username or password
        extract($_POST);
        if(strtolower($captcha) != strtolower($this->session->userdata('public_captcha_key'))){//added by shubhranshu
            $this->session->set_flashdata('invalid_captcha', 'Invalid captcha code');//added by shubhranshu
            redirect('user/forgot_password');//added by shubhranshu
             
        }//added by shubhranshu
        $password = random_key_generation();
        $encrypted_password = $this->bcrypt->hash_password($password);
        $result = $this->user_model->validate_forgot_pwd($forgot, $email, $dob, $encrypted_password, $password);
        if ($result == 'database_error') {
            $this->session->set_flashdata('error', 'Oops! Sorry, it looks like something went wrong.Please try again');
        } else if ($result == 'mail_sent') {
            $this->session->set_flashdata("success", "$forgot has been mailed to your email Id.");
        } else {
            $this->session->set_flashdata("error", "Failed to send mail. Please try again later or get in touch with your Administrator.");
        }
        redirect("user/forgot_password");
    }
  /*
     * This method for loading the dashboard after login.
     
     */

    public function referral_list() {
        if($this->session->userdata('userDetails')->user_id==""){
            redirect("course_public");
        }

        $data['page_title'] = 'Referral List';
       // $data['notifications'] = $this->user_model->get_notifications();
        $data['referrals'] = $this->user_model->get_referral_list();
        $data['referral1'] = $this->user_model->get_registered_list();
        $data['user'] = $this->user_model->get_dashboard_user_info();
        
       $metaresult = fetch_all_metavalues();
        $values = $metaresult[Meta_Values_Model::CLASS_LANGUAGE];
        $class_values = $metaresult[Meta_Values_Model::CLASSROOM_LOCATION];
        $class_status = $metaresult[Meta_Values_Model::CLASSROOM_STATUS];
        foreach ($values as $value) {
            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_values as $value) {
            $status_lookup_location[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_status as $value) {
            $status_lookup_class_status[$value['parameter_id']] = $value['category_name'];
        }
        $data['status_lookup_language'] = $status_lookup_language;
        $data['status_lookup_location'] = $status_lookup_location;
        $data['status_lookup_class_status'] = $status_lookup_class_status;
        
        
        $data['main_content'] = 'student_dashboard/dashboard';
        $this->load->view('layout_public', $data);
    }
    
     //code added by sankar, date: 05/03/2015, reason: view trainee, author: Sankar
    public function view_trainee($refer_user_id = NULL) {
        //getting the personal details.
        
        $data['profile'] = $this->user_model->get_my_profile($refer_user_id);
        $data['page_title'] = 'View Refer Trainee';
        $details = $data['profile']['userdetails'];
        if (!empty($details['personal_address_state'])) {
            $data['state_name'] = $this->courses_model->get_param_value($details['personal_address_state']);
        }
        if ($details['country_of_residence'] == IND) {
            $data['pan_number'] = $details['tax_code'];
        }
        if ($details['country_of_residence'] == SGP) {
            $data['nric_number'] = $details['tax_code'];
            $data['tax_code_type'] = $details['tax_code_type'];
            $data['other_identi_type'] = $details['other_identi_type'];
        }
        if ($details['country_of_residence'] == USA) {
            $data['ssn_number'] = $details['tax_code'];
        }
        
        $data['main_content'] = 'user/view_trainee';
        $this->load->view('layout_public', $data);
    }
    /*
     * This method for editing the refer trainees.
   
     */

    public function edit_refer_trainee($refer_user_id = NULL) {
        $t = $this->input->get('t');
        if (!empty($t)) {
            $refer_user_id = $t;
        }
        //getting the personal details.
        $data['profile'] = $this->user_model->get_my_profile($refer_user_id);
        $data['page_title'] = 'Edit Refer Trainee';
        $details = $data['profile']['userdetails'];
        if (!empty($details['personal_address_state'])) {
            $data['state_name'] = $this->courses_model->get_param_value($details['personal_address_state']);
        }
        if ($details['country_of_residence'] == IND) {
            $data['pan_number'] = $details['tax_code'];
        }
        if ($details['country_of_residence'] == SGP) {
            $data['nric_number'] = $details['tax_code'];
            $data['tax_code_type'] = $details['tax_code_type'];
            $data['other_identi_type'] = $details['other_identi_type'];
        }
        if ($details['country_of_residence'] == USA) {
            $data['ssn_number'] = $details['tax_code'];
        }
        // for unique checking of tax_code and email_id. after submiting.
        $this->session->set_userdata('registered_email_id_edit', $details['registered_email_id']);
        $this->session->set_userdata('tax_code_edit', $details['tax_code']);
        $data['main_content'] = 'user/edit_refer_trainee';
        $this->load->view('layout_public', $data);
    }
    /*
     * This method for loading refer trainee screen.
     * Author : pritam.
     * Date : 09 06 2016.
     */

    public function refer_trainee() {
         if($this->session->userdata('userDetails')->user_id==""){
            redirect("course");
        }
        $data['page_title'] = 'Refer a friend or family';
        $data['main_content'] = 'user/refer_trainee';
        $this->load->view('layout_public', $data);
    }
    /*
     * This function used to add refer trainee( difference with add_trainee: validation false page loading).
     * Modified by : pritam.
     * Modified date : 09 June 2016.
     */

    public function add_refer_trainee() {

        
        if($this->session->userdata('userDetails')->user_id==""){
            redirect("course_public/class_member_check");
        }
        $data['page_title'] = 'Trainee Register';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $country_of_residence = $this->input->post('country_of_residence');
            if ($country_of_residence == 'SGP') {
                $this->form_validation->set_rules('NRIC', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
                // validating nric code.
                $NRIC = $this->input->post('NRIC');
                $NRIC_ID = trim($this->input->post('NRIC_ID'));
                $valid = validate_nric_code($NRIC, $NRIC_ID);
                if ($valid == FALSE) {
                    $data['error_message'] = 'Invalid NRIC Code';
                    $data['main_content'] = 'user/refer_trainee';
                    $this->load->view('layout_public', $data);
                    return;
                }
            }
            $validation_status = $this->server_validation();
            if ($validation_status == TRUE) {
                $uid = $this->user_model->save_trainee();
                $upload_status = $this->upload_profile_image($uid, 'no');
                $relation = $this->input->post('relationship');
                if ($uid == FALSE) {
                    $data['message'] = "fail";
                } else {
                    $data['message'] = "success";
                }
                $data['tenant_details'] = $this->user_model->get_tenant_details();
                $data['main_content'] = 'includes/friend_registration_status';
                $this->load->view('layout_public', $data);
                return;
            }
        }
        $data['main_content'] = 'user/refer_trainee';
        $this->load->view('layout_public', $data);
    }
    
    
    ///// added by shubhranshu for add new trainee from public portal
    public function add_new_trainee($course_id=null,$class_id=null) { 
   
            $data['page_title'] = 'Trainee Register';
            $registration = '';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            $course_id = $this->input->post('course_id');
            $class_id = $this->input->post('class_id');
            $registration = $this->input->post('registration');
            $relation = $this->input->post('relationship');

            $res = $this->course_public_model->loggedin_enroll_someone(); 
                if($res['user_id']!=0)
                {
                   
                   $error= "<div style='color:green;font-weight: bold;text-align:center;padding: 9px;'>Congratulation! Trainee Registration Successful</div>";
                            
                        
                    $uid = $res["user_id"];
                    $tax_code = $res['tax_code'];
                    $friend_id = $res['friend_id']; 
                    $user_password = $res['user_password'];
                    $this->session->set_flashdata('error', $error);
                    return redirect('course_public/class_member_check_elearning'); 
                }else{
                      $error= "<div style='color:red;font-weight: bold;text-align:center;padding: 9px;'>Oops!!. Please try again later or contact your Administrator</div>";
                                          
                    $this->session->set_flashdata('error', $error);
                    return redirect('course_public/class_member_check_elearning'); 
                }
              
            }
    }

    



}