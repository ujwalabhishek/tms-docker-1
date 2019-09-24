<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /*
  * This is the controller class for Trainee use case all features. 
  */
 
class Activate_user extends CI_Controller {
   
    public function __construct() {        
        parent::__construct();
        $this->load->model('trainee_model');        
    }
    /*
     * for checking the activation of user.
     */   
    public function index() {     
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user_id = $this->uri->segment(3);        
        $activation_key = $this->uri->segment(4);         
        $is_valid = FALSE;
        $message="";        
        $is_valid = $this->trainee_model->verify_trainee_user($user_id, $activation_key);                
        if ($is_valid) {            
            $data['message'] = 'Y';
        }else{
            $data['message'] = 'N';
        }        
        $data['page_title'] = 'TMS Activation  page';
        $this->load->view('common/activation', $data);
    }
}