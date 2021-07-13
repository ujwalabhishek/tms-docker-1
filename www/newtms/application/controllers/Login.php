<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Login extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('login_model', 'login');
        $this->load->model('acl_model', 'acl');
        $this->load->library('bcrypt');
        $this->load->model('manage_tenant_model', 'manage_tenant');
    }
    /**
     * default load
     */
    public function index() {
        
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        //echo $user_role;exit;
        if ($user_role == 'SADMN') {
            redirect('manage_tenant/');
        }

        redirect('course_public/'); ///// added by shubhranshu

        
        
    }
    
    public function administrator(){
        if (isset($this->session->userdata('userDetails')->user_id)) {
            $this->load->model('dashboard_model', 'dashboard');
            $data['page_title'] = 'Admin Home Page';
            $data['main_content'] = 'dashboard/dashboard_page';
            $data['sideMenuData'] = fetch_non_main_page_content();
           // print_r($data);exit;
//            $userid = $this->session->userdata('userDetails')->user_id;
//            $tenant_id = $this->session->userdata('userDetails')->tenant_id;
//            $data['user_details'] = $this->login->get_user_details($userid,$tenant_id);
                if($this->session->userdata('userDetails')->role_id == 'SADMN') {  ///added by shubhranshu to redirect to isvadmin
                    redirect('manage_tenant/');
                }
            $data['role_id'] = 'ADMN';
            $this->load->view('layout', $data);
            } else {
//                if(isset($this->session->userdata('captcha_key'))){
                   $this->session->unset_userdata('captcha_key');//added by shubhranshu
                    unlink(FCPATH .'captcha/'.$this->session->userdata('captcha_file')); // added by shubhranshu to delete the captcha file 
//                }
                $data['captcha']=$this->generateCaptcha();
                $data['page_title'] = 'Login page';
                $this->load->view('layout_1', $data);
            }
    }
    /// below function was added by shubhranshu for captcha validation
    private function generateCaptcha(){
        $this->load->helper('captcha');
        $vals = array(
                'word'          => '',
                'img_path'      => FCPATH.'captcha/',
                'img_url'       => base_url().'captcha/',
                'font_path'     => FCPATH .'assets/fonts/ATBramley-Medium.ttf',
                'img_width'     => '127',
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
        $this->session->set_userdata('captcha_file', $cap['filename']);
        $this->session->set_userdata('captcha_key', $cap['word']);
        return $cap['image'];
    }/////////////////////////end ssp///////////////////////
    
    /* load tms dashboard */
    public function dashboard() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        //print_r($data);exit;
        $user_role = $this->session->userdata('userDetails')->role_id;
        if ($user_role == 'SADMN') {
            redirect('manage_tenant/');
        }
        if (isset($this->session->userdata('userDetails')->user_id)) {
            $this->load->model('dashboard_model', 'dashboard');
            $data['page_title'] = 'Admin Home Page';
            $data['main_content'] = 'dashboard/adminhomepage';
            $data['classes_start_this_week'] = $this->dashboard->classes_start_this_week();
            $this->load->model('class_model', 'class');
            foreach($data['classes_start_this_week'] as $id=>$class){
                $data['classes_start_this_week'][$id]->trainer_name = $this->class->get_trainer_names($class->classroom_trainer);
            }
            $data['pending_account_activation'] = $this->dashboard->pending_account_activation();
            $data['notifications'] = $this->dashboard->notifications();
            $data['pending_class'] = $this->dashboard->pending_class();
            $sales_commission_due = $this->dashboard->sales_commission_due();
            $year_arr = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 =>
                'June', 7 => 'July', 8 => 'August', 9 => 'September',
                10 => 'October', 11 => 'November', 12 => 'December');
            $salesdata = array();
            foreach ($sales_commission_due as $row) {
                if ($row->pymnt_status != 'PAID') {
                    $salesdata[$row->sales_exec_id]['name'] = $row->first_name . ' ' . $row->last_name;
                    $salesdata[$row->sales_exec_id]['comm_amount'][] = $row->comm_amount;
                    $salesdata[$row->sales_exec_id]['due_period'][$row->comm_period_yr][$row->comm_period_mth] = $year_arr[$row->comm_period_mth];
                }
            }
            $data['sales_commission_due'] = $salesdata;
            $data['role_id'] = 'ADMN';
            $this->load->view('layout', $data);
        } else {
            $data['page_title'] = 'Login page';
            $this->load->view('layout_1', $data);
        }
    }
    /**
     * This function check weather login account is active along with proper credentials
     */
    public function validate_user() {
        $user_name = $this->input->post('username');
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
            redirect('login/administrator');
        }
        //////above block of code added by shubhranshu for google captcha
        
//        if(strtolower($captcha) != strtolower($this->session->userdata('captcha_key'))){//added by shubhranshu
//            $this->session->set_flashdata('invalid_captcha', 'Invalid captcha code');//added by shubhranshu
//            redirect('login/administrator');//added by shubhranshu
//             
//        }//added by shubhranshu
        if (empty($user_name)) {
            return FALSE;
        }
        $year = time() + 31536000;
        setcookie('remember_me', $user_name, $year);
        $user = $this->login->check_user_valid();
        if ($user->account_status == 'INACTIV') {
            $this->session->set_flashdata('invalid', 'Your tenant account is inactive. Please get in touch with your Administrator.');
            redirect('login/administrator');
        }
        if (empty($user)) {
            $this->session->set_flashdata('invalid', 'Invalid credentials. '
                    . 'Please try again with valid credentials.');
            redirect('login/administrator');
        } else {
            
            $this->session->unset_userdata('captcha_key');///added by shubhranshu
            unlink(FCPATH .'captcha/'.$this->session->userdata('captcha_file')); // added by shubhranshu to delete the captcha file
            $user = $this->assign_my_role($user);
            $this->session->set_userdata('userDetails', $user);
          
            if (empty($user->role_id)) {
                $this->session->sess_destroy();
                redirect('login/');
            } else {
                redirect('login/');
            }
        }
    }
    /**
     * This method for assigning the role of the loggin user.
     * @param type $user
     * @return type
     */
    private function assign_my_role($user) {
        $my_role_array = explode(", ", $user->role_id);
        if (count($my_role_array) > 1) {
            $role_priority_array = explode(',', ROLE_PRIORITY_ARRAY);
            foreach ($role_priority_array as $key => $value) {
                if (in_array($value, $my_role_array)) {
                    $user->role_id = $role_priority_array[$key];
                    break;
                }
            }
        }
        return $user;
    }
    /**
     * This method is used to logout the user from the application and re-direct to the login page
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('login/');
    }
    /*
     * This method sends the user name or the password to the user
     */
    public function get_forgot_password() {
        extract($_POST);
        if(strtolower($captcha) != strtolower($this->session->userdata('captcha_key'))){//added by shubhranshu
            $this->session->set_flashdata('invalid_captcha', 'Invalid captcha code');//added by shubhranshu
            redirect('login/forgot_password');//added by shubhranshu
             
        }//added by shubhranshu
         $this->session->unset_userdata('captcha_key');///added by shubhranshu
         unlink(FCPATH .'captcha/'.$this->session->userdata('captcha_file')); // added by shubhranshu to delete the captcha file
        $password = random_key_generation();
        $encrypted_password = $this->bcrypt->hash_password($password);
        $result = $this->login->validate_forgot_pwd($email, $username, $encrypted_password, $password);
        if ($result == 'database_error') {
            $data['form_error']='Oops! Sorry, it looks like something went wrong.Please try again';
        } else if ($result == 'mail_sent') {
            $data['form_success'] = "Password has been mailed to your email Id.";
        } else if ($result == 'email_id_not_present') {  // Modified by dummy on 27 Jan 2015. Reason : Message change. starts here
            $data['form_error'] = "Wrong emailId or the emailId is not present. Please contact administrator to reset your password.";
        } else if ($result == 'invalid_username') {
            $data['form_error'] = "Invalid Username. Unable to send mail.";
        } else {
            $data['form_error'] = "Failed to send mail. Email Id / Username does not exist. Please try again later or get in touch with your Administrator.";
        }
        $data['page_title'] = 'Forgot Password';
        $this->load->view('layout_forgot_password', $data);
    }
    /*
     * This Method load the forgot password form.
     */
    public function forgot_password() {
        $this->session->unset_userdata('captcha_key');///added by shubhranshu
        unlink(FCPATH .'captcha/'.$this->session->userdata('captcha_file')); // added by shubhranshu to delete the captcha file
        $data['captcha']=$this->generateCaptcha();
        $data['page_title'] = 'Forgot Password';
        $this->load->view('layout_forgot_password', $data);
    }
    /**
     * custom error page
     */
    public function _404() {
        $session_data = $this->session->all_userdata();
        //Commented by abdulla
		//$tenant_id == $session_data['userDetails']->tenant_id;
		$tenant_id = $this->session->userdata('userDetails')->tenant_id;    
        if ($tenant_id) {
            $data['tenant_details'] = $this->login->fetch_tenant_details($tenant_id);
            $data['session_data'] = $session_data;
            $this->load->view("error_404", $data);
        }
    }
}

