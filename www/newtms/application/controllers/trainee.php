<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Trainee use case all features. 
 */
class Trainee extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('trainee_model', 'traineemodel');
        $this->load->model('manage_tenant_model', 'manage_tenant');
        $this->load->model('course_model', 'course');
        $this->load->model('common_model', 'commonmodel');
        $this->load->model('class_trainee_model', 'classtraineemodel');
        $this->load->helper('common');
        $this->load->model('user_model');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values', 'meta');
        $this->load->model('acl_model', 'acl');
        $this->load->library('bcrypt');
        $this->user = $this->session->userdata('userDetails');
    }
    /*
     * This function loads the initial list view page for trainees.
     */
    public function index() 
    {  
       // $this->output->enable_profiler();
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->user->tenant_id;
        $data['page_title'] = 'Trainee';
        $data['user'] = $this->user;
        $export_url = '?';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $data['sort_link'] = $sort_link = "off_company_name=" . $this->input->get('off_company_name') . "&pers_first_name=" . $this->input->get('pers_first_name') . "&search_radio=" . $this->input->get('search_radio') .
                "&tax_code=" . $this->input->get('tax_code') . "&status=" . $this->input->get('status')."&user_id=".$this->input->get('user_id');
        $totalrows = $this->traineemodel->record_count();
        $totalrows = empty($totalrows)? 0: $totalrows;
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'usr.last_modified_on';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'trainee/';
        $pageno = $this->uri->segment(2);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $data['tabledata'] = $this->traineemodel->get_trainee_list($tenant_id, $records_per_page, $offset, $field, $order_by);
       
        if(! isset($data['tabledata'][0]['company_name']))
        {
            foreach($data['tabledata'] as $k=>$tabledata)
            {
                  $tabledata['user_id'];
               
                $company_details = $this->traineemodel->get_company_details($tabledata['user_id']);
                if($company_details->num_rows())
                {
                    $company_data = $company_details->row();
                    $data['tabledata'][$k]['company_name'] = $company_data->company_name;
                    $data['tabledata'][$k]['company_id'] = $company_data->company_id;
                }
            }
        }
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'trainee/';
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['main_content'] = 'trainee/traineelist';
        $data['filter_status'] = fetch_metavalues_by_category_id(Meta_Values::STATUS);
        $values = fetch_metavalues_by_category_id(Meta_Values::STATUS);
        $status_lookup = array();
        foreach ($values as $value) {
            $status_lookup[$value['parameter_id']] = $value['category_name'];
        }
        if (!empty($status_lookup)) {
            $data['status_lookup'] = $status_lookup;
        }
        $this->load->view('layout', $data);
    }
    /*
     * This function loads the empty Add trainee
     */
    public function add_new_trainee() 
    {
		$tenant_id=$this->user->tenant_id;
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['user'] = $user;
        $data['privilage'] = $this->manage_tenant->get_privilage();//added by shubhranshu
        $data['courses'] = $this->course->get_active_course_list_by_tenant($tenant_id,'discount');        
        $data['page_title'] = 'Add Trainee';
        $valid = TRUE;
        if ($this->input->server('REQUEST_METHOD') === 'POST') 
        {
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');
            $country_of_residence = $this->input->post('country_of_residence');
            if ($country_of_residence == 'IND') 
            {
                $this->form_validation->set_rules('PAN', 'PANNumber', 'required|max_length[50]|callback_check_unique_usertaxcode');
                $tax_code = $this->input->post("PAN");
            }
            if ($country_of_residence == 'SGP') 
            {                
                $nric = $this->input->post("NRIC"); 
                if($nric == "SNG_4"){
                   $nric = "SNG_3"; 
                }
                $nric_other = $this->input->post("NRIC_OTHER");
                $nric_id = $this->input->post('NRIC_ID');
                $tax_code = $nric_id;
                $this->form_validation->set_rules('NRIC', ' NRIC Type', 'required');
                if($nric != "SNG_3" )
                {
                    $this->form_validation->set_rules('NRIC_ID', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
                    if(!empty($nric)) {
                        $valid = validate_nric_code($nric, $nric_id);
                        if ($valid == FALSE) {                       
                            $data['tax_error'] = 'Invalid NRIC Code.'; 
                        }
                    }
                }                
            }            
            if ($country_of_residence == 'USA') {
                $tax_code = $this->input->post("SSN");
                $this->form_validation->set_rules('SSN', 'SSNNumber', 'required|max_length[50]|callback_check_unique_usertaxcode');
            }
            if ($this->input->post('bypassemail') == 'EMACRQ') {
                $this->form_validation->set_rules('user_registered_email', 'Email', 'required|max_length[50]|valid_email');
            }
            $this->form_validation->set_rules('user_name', 'Username', 'required|max_length[15]|callback_check_unique_username');
            $this->form_validation->set_rules('pers_first_name', 'Firstname', 'required|max_length[100]');
            $this->form_validation->set_rules('pers_second_name', 'Secondname', 'max_length[100]');
            $this->form_validation->set_rules('pers_contact_number', 'Contact Number', 'required|exact_length[8]');
            $this->form_validation->set_rules('pers_alternate_email', 'Email', 'valid_email');
            $this->form_validation->set_rules('pers_alternate_contact_number', 'Alternater Contact Number', 'max_length[50]');
            $this->form_validation->set_rules('pers_gender', 'Gender', 'required');
			$this->form_validation->set_rules('pers_dob', 'Date of Birth', 'required|max_length[10]');
			if($tenant_id =='T24'){								
				$this->form_validation->set_rules('pers_country', 'Country', 'required');
				$this->form_validation->set_rules('pers_states', 'State', 'required');
				$this->form_validation->set_rules('pers_city', 'City', 'required|max_length[50]');
				$this->form_validation->set_rules('pers_zip_code', 'Postal Code', 'required|max_length[10]');
				$this->form_validation->set_rules('pers_personal_address_bldg', 'Address', 'required|max_length[255]');
			} else {
				$this->form_validation->set_rules('pers_personal_address_bldg', 'Address', 'max_length[255]');
				$this->form_validation->set_rules('pers_city', 'City', 'max_length[50]');
			}
            $this->form_validation->set_rules('highest_educ_level', 'Highest Educ Level', 'required');
            $this->form_validation->set_rules('nationality', 'Nationality', 'required');
            if ($valid && $this->form_validation->run() == TRUE) {
                  $taxcodeStatus = $this->commonmodel->is_taxcode_exist($tax_code, $tenant_id);
                  if(!$taxcodeStatus){
                       $user_name = $this->input->post("user_name");
                       $usernameStatus = $this->commonmodel->is_username_exist($user_name, $tenant_id);
                       if( $usernameStatus){
                          $failure_msg = 'Duplicate Username. Please change the user name.';
                       }
                  }
                  else{
                      $failure_msg = 'Duplicate Tax Code. Please change the tax code.';
                  }
            }
            if ( ($valid) && ($this->form_validation->run() == TRUE) && (!$taxcodeStatus) && (!$usernameStatus) ) {
                $uid = $this->traineemodel->save_user_data();                
                if (!empty($_FILES['userfile']['name']) && $uid != FALSE) {
                    $this->load->helper('upload_helper');
                    $image_data = upload_image('uploads/images/trainee', $uid);
                    if ($image_data['status']) {
                        $image_path = $image_data['image']['system_path'] . '/' .
                                $image_data['image']['raw_name'] . '_thumb' . $image_data['image']['file_ext'];
                        save_image_path($uid, $image_path);
                    } 
                }
                if ($uid == FALSE) {
                    $this->session->set_flashdata("error_message", "Unable to create trainee. Please try again later.");
                } else {                    
                    $activate_user = $this->input->post("activate_user");
                    $bypassemail = $this->input->post("bypassemail");
                    if($activate_user == "ACTIVE" && $bypassemail== 'BPEMAC') {
                        $this->session->set_userdata('new_trainee_user_id', $uid);
                        redirect("trainee/trainee_enroll_now");
                    }  else {
                        $this->session->set_flashdata("success_message", "Trainee has been created successfully");
                    }                   
                }
                redirect("trainee");
            } else {                
                $data['tax_error'] = ($data['tax_error'])?$data['tax_error']:$failure_msg;
                $data['main_content'] = 'trainee/addnewtrainee';
                $this->load->view('layout', $data);
            }
        } else {
            $data['main_content'] = 'trainee/addnewtrainee';
            $this->load->view('layout', $data);
        }
    }
    
    function check_unique_usertaxcode() {
        $country_of_residence = $this->input->post('country_of_residence');
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
                $this->form_validation->set_message('check_unique_usertaxcode', "Duplicate Tax Code. Please change the tax code.");
                return FALSE;
            }
            return TRUE;
        }
    }
    
    /**
     * This method for redirect trainees to enroll new page if trainee status is active.
     */
    public function trainee_enroll_now() {   
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['success_message'] = 'Trainee has been created successfully';
        $tenant_id = $this->user->tenant_id;        
        $data['page_title'] = 'Enroll New';
        $data['main_content'] = 'trainee/enroll_trainee';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $data['sort_link'] = $sort_link = "off_company_name=" . $this->input->get('off_company_name') . "&pers_first_name=" . $this->input->get('pers_first_name') . "&search_radio=" . $this->input->get('search_radio') .
                "&tax_code=" . $this->input->get('tax_code') . "&status=" . $this->input->get('status');
        $totalrows_array = $this->classtraineemodel->get_course_class_list('count',$tenant_id);
        $totalrows = count($this->remove_overbooked($totalrows_array));
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'cls.class_start_datetime';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'trainee/trainee_enroll_now/';
        $pageno = $this->uri->segment(3);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);              
        $tabledata = $this->classtraineemodel->get_course_class_list('data',$tenant_id, $records_per_page, $offset, $field, $order_by);
        $data['tabledata'] = $this->remove_overbooked($tabledata);
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'trainee/trainee_enroll_now/';
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $this->load->view('layout', $data);
    }
    /**
     * This function removes over booking in pay during enrolment.
     * @param type $tabledata
     * @return type
     */
    private function remove_overbooked($tabledata) {
        foreach ($tabledata as $key => $data) {
            if($data->class_pymnt_enrol == "PDENROL") {
                if($data->total_seats <= $data->total_enrolled) {                    
                    unset($tabledata[$key]);
                }
            }
        }
        return $tabledata;
    }
    /*
     * This function loads the Edit trainee form. and for updating and deactivating the trainee details.
     */
    public function edit_trainee() 
    {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['user'] = $user;
        $data['privilage'] = $this->manage_tenant->get_privilage();//added by shubhranshu
        $this->load->library('form_validation');
        $data['page_title'] = 'Edit Trainee';
        $data['main_content'] = 'trainee/edittrainee';
        $code = '';
        if ($this->input->post('trainee_name')) {
            $taxcode = $this->input->post('trainee_name');
            $taxcode = explode('(', $taxcode);
            $code = rtrim($taxcode[1], ')');
        } else if ($this->input->post('taxcode')) {
            $taxcode = $this->input->post('taxcode');
            $taxcode = explode('(', $taxcode);            
            $code = $taxcode[0];
        }        
        if ($code) { 
            $user_id = $this->input->post('user_id');
            $user_id = ($user_id)? $user_id : $this->input->post("userid");            
            $data['trainee'] = $this->traineemodel->get_trainee_taxcode($user_id, $this->user->tenant_id); 
            $data['payment_status'] = $this->traineemodel->payment_status($data['trainee'][userdetails][user_id],$tenant_id);            
        }        
        if ($this->input->post('task') == 'update') 
        {
            $data['edit_tax_code'] = $code;
            $valid = TRUE;
            $country_of_residence = $this->input->post('country_of_residence');
            $this->form_validation->set_rules('pers_first_name', 'Firstname', 'required|max_length[100]');
            $this->form_validation->set_rules('pers_contact_phone', 'Contact Number', 'required|exact_length[8]');
            if ($country_of_residence == 'IND') {
                $tax_code = $this->input->post("PAN");
                $this->form_validation->set_rules('PAN', 'PAN Number', 'required|max_length[15]');
            }            
            if ($country_of_residence == 'SGP') {
                $this->form_validation->set_rules('NRIC', 'NRIC Type', 'required|max_length[15]');
                $NRIC = $this->input->post('NRIC');
                $NRIC_OTHER = $this->input->post("NRIC_OTHER");
                $NRIC_ID = $this->input->post('NRIC_ID');
                $NRIC_ID_MATCH = $this->input->post('NRIC_ID_MATCH'); // addded by shubhranshu for NRIC ID
                $tax_code = $NRIC_ID;
                
                if($NRIC != "SNG_3"){
                    if($NRIC_ID != $NRIC_ID_MATCH){ //added by shubhranshu for check NRIC if it does not match
                        $this->form_validation->set_rules('NRIC_ID', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode');   
                        if(!empty($NRIC)) {
                        $valid = validate_nric_code($NRIC, $NRIC_ID);
                            if ($valid == FALSE) {
                                $data['tax_error'] = 'Invalid NRIC Code.'; //Added By dummy for Edit issue (Nov 10 2014)
                            }
                        }
                        
                    }
                                                       
                    
                }
            }                       
            if ($country_of_residence == 'USA') {
                $tax_code = $this->input->post('SSN');
                $this->form_validation->set_rules('SSN', 'SSNNumber', 'required|max_length[15]');
            }              
            if ($valid && $this->form_validation->run() == TRUE && $data['trainee'][userdetails]['tax_code']!=$tax_code ) {
                $taxcodeStatus = $this->commonmodel->is_taxcode_exist($tax_code, $tenant_id);
                if($taxcodeStatus) {                    
                    $failure_msg = 'Duplicate Tax Code. Please change the tax code.';
                }
            }
            if ( ($valid) && ($this->form_validation->run() == TRUE) && (!$taxcodeStatus)) {
               $delete_image = $this->input->post('deleteimage') ? $this->input->post('deleteimage') : 'no';
               
                $user_id = $this->input->post('userid');
                $result = $this->traineemodel->get_trainee_details($user_id);              
                $previous_data = json_encode($result);
                
                $uid = $this->traineemodel->update_trainee();
                $this->load->helper('upload_helper');
                if (!empty($_FILES['userfile']['name']) && $uid != FALSE && $delete_image == 'no') {
                    $image_data = upload_image('uploads/images/trainee', $uid);
                    if ($image_data['status']) {
                        $image_path = $image_data['image']['system_path'] . '/' .
                                $image_data['image']['raw_name'] . '_thumb' . $image_data['image']['file_ext'];
                        $previous_thumb_path = fetch_image_path_by_uid($uid);
                        remove_previous_image($previous_thumb_path);
                        save_image_path($uid, $image_path);
                    } 
                } else if ($uid != FALSE && $delete_image == 'no') {
                    $previous_thumb_path = fetch_image_path_by_uid($uid);
                    remove_previous_image($previous_thumb_path);
                    save_image_path($uid);
                }
                if ($uid == FALSE) {
                    
                    $this->session->set_flashdata('error_message', 'Unable to update Trainee.Please try again later.');
                } else {
                     user_activity(3,$user_id,$previous_data);
                    $this->session->set_flashdata('success_message', 'Trainee has been updated successfully');
                }
                redirect('trainee/');
            }
            else {
                $data['tax_error'] = ($data['tax_error'])?$data['tax_error']:$failure_msg;
                $this->load->view('layout', $data);
                return;
            }
        } else if ($this->input->post('task') == 'deactivate') { 
            $user_id = $this->input->post('userid');
            $res = $this->traineemodel->get_complete_trainee_details($user_id);
            $previous_trainee_data = json_encode($res);
            $update = $this->traineemodel->deactivate_trainee();
            if ($update == FALSE) {
                $this->session->set_flashdata('error_message', 'Oops! Sorry, it looks like something went wrong.Please try again');
            } else {
                 user_activity(3,$user_id,$previous_trainee_data);
                $this->session->set_flashdata('success_message', 'Trainee has been deactivated successfully');
            }
            redirect('trainee/');
        }
        $this->load->view('layout', $data);
    }   
    /*
     * This function call model function to save user data.
     */
    public function check_select_default_value($post_string) {
        return $post_string == '_none' ? FALSE : TRUE;
    }
    /**
     * check unique user name
     * @return boolean
     */
    function check_unique_username() {
        if ($user_name = $this->input->post('user_name')) {
            $exists = $this->traineemodel->check_duplicate_user_name($user_name);
            if (!$exists) {
                $this->form_validation->set_message('check_unique_username', "Username $user_name already exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * This function loads the View trainee form.
     */
    public function view_trainee() 
    {
        $data['sideMenuData'] = fetch_non_main_page_content();
        //$this->output->enable_profiler(TRUE);
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'View Trainee';
        $userid = $this->uri->segment(3);
        $data['trainee'] = $trainee = $this->traineemodel->get_trainee($userid);        
        $this->load->model('internal_user_model', 'internaluser');
        if ($trainee[userdetails]['account_status'] == 'INACTIV') 
        {
            $data['deactivated_by'] = $this->internaluser->get_user_details($this->session->userdata('userDetails')->tenant_id, $trainee[userdetails]['deacti_by']);
        }
        $totalrows = $this->traineemodel->get_training_details_count($userid);
        $export_url = '?';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $field = ($this->input->get('f')) ? $this->input->get('f') : '';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : '';
        $records_per_page = RECORDS_PER_PAGE;
        $pageno = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;
        $offset = ($pageno * $records_per_page);
        $data['controllerurl'] = "trainee/view_trainee/$userid";
        $data['training_details'] = $this->traineemodel->get_training_details_new($userid, $records_per_page, $offset, $field, $order_by);
       
        $assessment_links = array();
        foreach($data['training_details'] as $k => $v){
            //$data['training_details'][$k]->att_status = get_common_attendance_status($tenant_id, $v->user_id, $v->course_id, $v->class_id);
			// Added due to trainee status on 16/10/2018 at 2:29PM
            $data['training_details'][$k]->att_status = get_common_attendance_status_new($tenant_id, $v->user_id, $v->course_id, $v->class_id,$v->attn_stats);
            
            $assessment_links[$v->class_id] = $this->traineemodel->get_trainee_assessment_forms($v->user_id,$v->course_id, $v->class_id);            
        }
         
         $data['training_history'] = $this->traineemodel->get_training_history($trainee[userdetails]['tax_code']);
         $data['sort_order'] = $order_by;
        $data['user_id'] = $userid;
        $this->load->helper('pagination');
        $baseurl = base_url() . "trainee/view_trainee/$userid/";
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);
        $data['main_content'] = 'trainee/viewtrainee';
        
        /* For DMS - get the trainee assessment forms */
        $data['assmnt_links'] = $assessment_links;        
        $this->load->view('layout', $data);
    }
    /**
     * export trainee data
     */
    public function export_trainee_page() {
        
        set_time_limit(0);
        ini_set("memory_limit","-1");
        $result = $this->traineemodel->get_trainee_list_export();
        $this->load->helper('export_helper');
        export_trainee_page($result);
    }
    /**
     * export all fields trainee data
     */
    public function export_trainee_full() {
        set_time_limit(0);
        ini_set("memory_limit","-1");
        $query = $this->traineemodel->get_trainee_list_export();
        $this->load->helper('export_helper');
        export_trainee_full($query);
    }
    /**
     * get states
     * @param type $country_param
     * @return type
     */
    private function get_states($country_param) {
        if (empty($country_param)) {
            return;
        }
        $this->load->model('internal_user_model', 'internaluser');
        $states = $this->internaluser->get_states($country_param);
        $states_arr = array();
        foreach ($states as $item) {
            $states_arr[] = $item->parameter_id;
        }
        return $states_arr;
    }
    /*
     * This method for getting all the metavalues for bulk regitration validation
     */
    private function get_metavalues_array() {
        $metavalues = array();
        $countries = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
        foreach ($countries as $item):
            $metavalues['country'][] = $item['parameter_id'];
        endforeach;
        $nrics = fetch_metavalues_by_category_id(Meta_Values::NRIC);
        foreach ($nrics as $item):
            
            $metavalues['nric'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;
        ///////////////////added by shubhranshu for nric other on 12/03/2018////////////////////////////////
        $nricso = fetch_metavalues_by_category_id(Meta_Values::NRIC_OTHER);
        foreach ($nricso as $item):
            
            $metavalues['nric'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;
        ///////////////////////////////////////////////////////////////////////////////////////////////////
        $nationality = fetch_metavalues_by_category_id(Meta_Values::NATIONALITY);
        foreach ($nationality as $item):
           
            $metavalues['nationality'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;
        $gender = fetch_metavalues_by_category_id(Meta_Values::GENDER);
        foreach ($gender as $item):
            $metavalues['gender'][] = $item['parameter_id'];
        endforeach;
        $occupation = fetch_metavalues_by_category_id(Meta_Values::OCCUPATION);
        foreach ($occupation as $item):
            
            $metavalues['occupation'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;
        $education = fetch_metavalues_by_category_id(Meta_Values::HIGHEST_EDUC_LEVEL);
        foreach ($education as $item):
           
            $metavalues['education'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;
        $compnies = getcompnies();
        foreach ($compnies as $item):
            $metavalues['compnies_id'][] = $item['company_id'];
            $metavalues['compnies_name'][$item['company_id']] = $item['company_name'];
        endforeach;
        $sal_range = fetch_metavalues_by_category_id(Meta_Values::SAL_RANGE);
        foreach ($sal_range as $item):
            
            $metavalues['salary'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;        
        $race = fetch_metavalues_by_category_id(Meta_Values::RACE);
        foreach ($race as $item):
           
            $metavalues['race'][trim(strtolower($item['category_name']))] = $item['parameter_id'];
        endforeach;
        return $metavalues;
    }
    /**
     * date validation
     * @param type $date
     * @return type
     */
    private function validateDate_1($date) {
        $d = DateTime::createFromFormat('d-m-Y', $date);
        return $d && $d->format('d-m-Y') == $date;
    }
    /*
     * This method for validating each excel field.
     */
      private function validate_excel($trainee, $i) {
        $tenant_id=$this->user->tenant_id;
        $metavalues = array();
        $metavalues = $this->get_metavalues_array();
        $is_taxcode_unique = $this->is_taxcode_unique($trainee[$i][taxcode]);
        $is_emailid_unique = $this->is_emailid_unique($trainee[$i][EmailId]);
        /////added by shubhranshu to check the restriction
        //$is_nric_restriction = $this->traineemodel->check_nric_restriction($trainee[$i][taxcode]); // added by shubhranshu for client requirement 22/03/2019 for prevent restriction
        ///////////////////////////////////////////////////
        if ($trainee[$i][countryofresidence] == 'IND') {
            $trainee[$i][taxcodetype] = 'PAN';
        } else if ($trainee[$i][countryofresidence] == 'USA') {
            $trainee[$i][taxcodetype] = 'SSN';
        } else {
            $trainee[$i][taxcodetype] = $trainee[$i][countryofresidence];
        }
        $country_state = $this->get_states($trainee[$i][Country]);
        if (($trainee[$i][countryofresidence] == '') || ($trainee[$i][nrictype] == '') || ($trainee[$i][nationality] == '') || ($trainee[$i][education] == '') || ($trainee[$i][firstname] == '') || ($trainee[$i][gender] == '') || ($trainee[$i][ContactNumber] == '')) {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Mandatory Check Fail.';
        }//// added by shubhranshu for notax code problem on 03/12/2018////////////////
        
        if($trainee[$i][taxcode] !=''){
            if (preg_match('/\s/',$trainee[$i][taxcode])){
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'White Space does not allowed in NRIC.';
            }
        }
        if($trainee[$i][taxcode] == '' && $trainee[$i][nrictypeOthers] != 'NO TAX CODE'){
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Mandatory Check Fail.';
        }//// added by shubhranshu
        if($trainee[$i][nrictype] == 'NRIC' && $trainee[$i][nrictypeOthers] != ''){
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'if others should be blank.';
        }
        if($trainee[$i][nrictype] == 'FIN' && $trainee[$i][nrictypeOthers] != ''){
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'if others should be blank.';
        }
        if($trainee[$i][nrictype] == 'Others' && $trainee[$i][nrictypeOthers] == ''){
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'if others field required.';
        }
        if($trainee[$i][nrictypeOthers] == 'NO TAX CODE' && $trainee[$i][taxcode] != ''){
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Taxcode should be empty.';
        }
        if($trainee[$i][nrictypeOthers] == 'NO TAX CODE' && $trainee[$i][nrictype] != 'Others'){
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Nric Type should be Others.';
        }///////////////////////////////////////////////////////////////////////////////
        $trainee[$i][CompanyName] = $metavalues['compnies_name'][$trainee[$i][CompanyCode]];
        if (!in_array($trainee[$i][countryofresidence], $metavalues['country']) && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid Country of Residence.';
        }
        if ($trainee[$i][countryofresidence] == 'SGP' && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][nrictype] = $metavalues['nric'][strtolower($trainee[$i][nrictype])];
            $trainee[$i][nrictypeOthers] = $metavalues['nric'][strtolower($trainee[$i][nrictypeOthers])];//added by shubhranshu to get the parameter id on 12/03/2018
            if (!in_array($trainee[$i][nrictype], $metavalues['nric'])) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid NRIC Type.';
            } else if ($trainee[$i][nrictypeOthers] != 'NOTAXCODE' && validate_nric_code($trainee[$i][nrictype], $trainee[$i][taxcode]) == FALSE) { // added by shubhranshu for notaxcode
                //echo 'no';exit;
                //if(validate_nric_code($trainee[$i][nrictype], $trainee[$i][taxcode]) == FALSE){// validating nric type. 
                    $trainee[$i][rowstatus] = 'fail';
                    $trainee[$i]['failure_reason'] = 'Invalid Taxcode.';
               // }
            } else if ($is_taxcode_unique > 0) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Taxcode already exists.';
            } else { ///  added by shubhranshu to prevent nric type notax code//////////
                if($trainee[$i][nrictype] == 'SNG_3'){
                    $trainee[$i][taxcodetype] = $trainee[$i][nrictype];
                    $trainee[$i][other_identi_type] = $trainee[$i][nrictypeOthers];
                    $trainee[$i][other_identi_code] = $trainee[$i][taxcode];
                }else{//echo 'ok';exit;
                    $trainee[$i][taxcodetype] = $trainee[$i][nrictype];
                    $trainee[$i][other_identi_type] = NULL;
                    $trainee[$i][other_identi_code] = $trainee[$i][taxcode];
                }///////////////////////////////////////////////////////////////////////
            }
        }
        if ($is_taxcode_unique > 0 && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Taxcode already exists.';
        }
        $trainee[$i][nationality] = $metavalues['nationality'][strtolower($trainee[$i][nationality])];
        if (!in_array($trainee[$i][nationality], $metavalues['nationality']) && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid Nationality.';
        }        
        $trainee[$i][education] = $metavalues['education'][strtolower($trainee[$i][education])];
        if (!in_array($trainee[$i][education], $metavalues['education']) && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid Highest Education Level code.';
        }
        if (empty($trainee[$i][firstname]) && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid First Name.';
        }
        if (!empty($trainee[$i][dob]) && ($timestamp = strtotime($trainee[$i][dob])) === false) {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid DOB.';
        }
        if (!in_array($trainee[$i][gender], $metavalues['gender']) && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid Gender.';
        }
        if (!(preg_match('/^[1-9][0-9]*$/', $trainee[$i][ContactNumber])) && $trainee[$i][rowstatus] == 'success') {
            $trainee[$i][rowstatus] = 'fail';
            $trainee[$i]['failure_reason'] = 'Invalid Contact Number.';
        }
        if ($trainee[$i][CompanyCode] && $trainee[$i][rowstatus] == 'success') {
            if (!in_array($trainee[$i][CompanyCode], $metavalues['compnies_id'])) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid Company Code.';
            } else if ($trainee[$i][SalaryRangeCode] == '') {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Salary Range Code Required.';
            } else if ($trainee[$i][occupation] == '') {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Occupation Code Required.';
            }
        }
        if ($trainee[$i][EmailId] && $trainee[$i][rowstatus] == 'success') {
            if ((preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $trainee[$i][EmailId]))) {
                if ($is_emailid_unique > 0) {
                    $trainee[$i][rowstatus] = 'fail';
                    $trainee[$i]['failure_reason'] = 'Email Id already exists.';
                }
            } else {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid Email Id.';
            }
        }
        if ($trainee[$i][City] && $trainee[$i][rowstatus] == 'success') {
            if ((preg_match('/[^a-z\s-]/i', $trainee[$i][City]))) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid City.';
            }
        }
        if ($trainee[$i][Country] && $trainee[$i][rowstatus] == 'success') {
            if (!in_array($trainee[$i][Country], $metavalues['country'])) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid Country.';
            }
        }
        if ($trainee[$i][State] && $trainee[$i][rowstatus] == 'success') {
            if (!in_array($trainee[$i][State], $country_state)) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid State.';
            }
        }
        if ($trainee[$i][ZipCode] && $trainee[$i][rowstatus] == 'success') {
            if ((preg_match('/[^a-z_\-0-9]/i', $trainee[$i][ZipCode]))) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid ZipCode.';
            }
        }
        
        $trainee[$i][SalaryRangeCode] = urldecode(str_replace('%A0','+',urlencode(strtolower($trainee[$i][SalaryRangeCode]))));
        $trainee[$i][SalaryRangeCode] = $metavalues['salary'][$trainee[$i][SalaryRangeCode]];
        if ($trainee[$i][SalaryRangeCode] && $trainee[$i][rowstatus] == 'success') {
            if (!in_array($trainee[$i][SalaryRangeCode], $metavalues['salary'])) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid Salary Range Code.';
            }
        }
        $trainee[$i][occupation] = $metavalues['occupation'][strtolower($trainee[$i][occupation])];
        if ($trainee[$i][occupation] && $trainee[$i][rowstatus] == 'success') {
            if (!in_array($trainee[$i][occupation], $metavalues['occupation'])) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid Occupation Code.';
            }
        }
        $trainee[$i][RaceCode] = $metavalues['race'][strtolower($trainee[$i][RaceCode])];
        if ($trainee[$i][RaceCode] && $trainee[$i][rowstatus] == 'success') {
            if (!in_array($trainee[$i][RaceCode], $metavalues['race'])) {
                $trainee[$i][rowstatus] = 'fail';
                $trainee[$i]['failure_reason'] = 'Invalid Race Code.';
            }
        } 
        /* added by shubhranshu to prevent restriction list nric on 22/03/2019*/
//        if($is_nric_restriction > 0){
//            $flag = 'true';
//            $trainee['flag'] = $flag;
//            ///$trainee[$i][rowstatus] = 'fail';
//            //$trainee[$i]['failure_reason'] = 'NRIC Restricted.'; 
//        }/*-------------------------------------------------------------------*/
        return $trainee[$i];
    }
    
    
//    private function validate_excel($trainee, $i) {
//        $metavalues = array();
//        $metavalues = $this->get_metavalues_array();
//        $is_taxcode_unique = $this->is_taxcode_unique($trainee[$i][taxcode]);
//        $is_emailid_unique = $this->is_emailid_unique($trainee[$i][EmailId]);
//        if ($trainee[$i][countryofresidence] == 'IND') {
//            $trainee[$i][taxcodetype] = 'PAN';
//        } else if ($trainee[$i][countryofresidence] == 'USA') {
//            $trainee[$i][taxcodetype] = 'SSN';
//        } else {
//            $trainee[$i][taxcodetype] = $trainee[$i][countryofresidence];
//        }
//        $country_state = $this->get_states($trainee[$i][Country]);
//        if (($trainee[$i][countryofresidence] == '') || ($trainee[$i][taxcode] == '') || ($trainee[$i][nationality] == '') || ($trainee[$i][education] == '') || ($trainee[$i][firstname] == '') || ($trainee[$i][gender] == '') || ($trainee[$i][ContactNumber] == '')) {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Mandatory Check Fail.';
//        }
//        $trainee[$i][CompanyName] = $metavalues['compnies_name'][$trainee[$i][CompanyCode]];
//        if (!in_array($trainee[$i][countryofresidence], $metavalues['country']) && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid Country of Residence.';
//        }
//        if ($trainee[$i][countryofresidence] == 'SGP' && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][nrictype] = $metavalues['nric'][strtolower($trainee[$i][nrictype])];
//            if (!in_array($trainee[$i][nrictype], $metavalues['nric'])) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid NRIC Type.';
//            } else if (validate_nric_code($trainee[$i][nrictype], $trainee[$i][taxcode]) == FALSE) { // validating nric type.
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Taxcode.';
//            } else if ($is_taxcode_unique > 0) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Taxcode already exists.';
//            } else {
//                $trainee[$i][taxcodetype] = $trainee[$i][nrictype];
//                $trainee[$i][other_identi_type] = $trainee[$i][nrictype];
//                $trainee[$i][other_identi_code] = $trainee[$i][taxcode];
//            }
//        }
//        if ($is_taxcode_unique > 0 && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Taxcode already exists.';
//        }
//        $trainee[$i][nationality] = $metavalues['nationality'][strtolower($trainee[$i][nationality])];
//        if (!in_array($trainee[$i][nationality], $metavalues['nationality']) && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid Nationality.';
//        }        
//        $trainee[$i][education] = $metavalues['education'][strtolower($trainee[$i][education])];
//        if (!in_array($trainee[$i][education], $metavalues['education']) && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid Highest Education Level code.';
//        }
//        if (empty($trainee[$i][firstname]) && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid First Name.';
//        }
//        if (!empty($trainee[$i][dob]) && ($timestamp = strtotime($trainee[$i][dob])) === false) {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid DOB.';
//        }
//        if (!in_array($trainee[$i][gender], $metavalues['gender']) && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid Gender.';
//        }
//        if (!(preg_match('/^[1-9][0-9]*$/', $trainee[$i][ContactNumber])) && $trainee[$i][rowstatus] == 'success') {
//            $trainee[$i][rowstatus] = 'fail';
//            $trainee[$i]['failure_reason'] = 'Invalid Contact Number.';
//        }
//        if ($trainee[$i][CompanyCode] && $trainee[$i][rowstatus] == 'success') {
//            if (!in_array($trainee[$i][CompanyCode], $metavalues['compnies_id'])) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Company Code.';
//            } else if ($trainee[$i][SalaryRangeCode] == '') {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Salary Range Code Required.';
//            } else if ($trainee[$i][occupation] == '') {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Occupation Code Required.';
//            }
//        }
//        if ($trainee[$i][EmailId] && $trainee[$i][rowstatus] == 'success') {
//            if ((preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $trainee[$i][EmailId]))) {
//                if ($is_emailid_unique > 0) {
//                    $trainee[$i][rowstatus] = 'fail';
//                    $trainee[$i]['failure_reason'] = 'Email Id already exists.';
//                }
//            } else {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Email Id.';
//            }
//        }
//        if ($trainee[$i][City] && $trainee[$i][rowstatus] == 'success') {
//            if ((preg_match('/[^a-z\s-]/i', $trainee[$i][City]))) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid City.';
//            }
//        }
//        if ($trainee[$i][Country] && $trainee[$i][rowstatus] == 'success') {
//            if (!in_array($trainee[$i][Country], $metavalues['country'])) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Country.';
//            }
//        }
//        if ($trainee[$i][State] && $trainee[$i][rowstatus] == 'success') {
//            if (!in_array($trainee[$i][State], $country_state)) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid State.';
//            }
//        }
//        if ($trainee[$i][ZipCode] && $trainee[$i][rowstatus] == 'success') {
//            if ((preg_match('/[^a-z_\-0-9]/i', $trainee[$i][ZipCode]))) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid ZipCode.';
//            }
//        }
//        
//        $trainee[$i][SalaryRangeCode] = urldecode(str_replace('%A0','+',urlencode(strtolower($trainee[$i][SalaryRangeCode]))));
//        $trainee[$i][SalaryRangeCode] = $metavalues['salary'][$trainee[$i][SalaryRangeCode]];
//        if ($trainee[$i][SalaryRangeCode] && $trainee[$i][rowstatus] == 'success') {
//            if (!in_array($trainee[$i][SalaryRangeCode], $metavalues['salary'])) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Salary Range Code.';
//            }
//        }
//        $trainee[$i][occupation] = $metavalues['occupation'][strtolower($trainee[$i][occupation])];
//        if ($trainee[$i][occupation] && $trainee[$i][rowstatus] == 'success') {
//            if (!in_array($trainee[$i][occupation], $metavalues['occupation'])) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Occupation Code.';
//            }
//        }
//        $trainee[$i][RaceCode] = $metavalues['race'][strtolower($trainee[$i][RaceCode])];
//        if ($trainee[$i][RaceCode] && $trainee[$i][rowstatus] == 'success') {
//            if (!in_array($trainee[$i][RaceCode], $metavalues['race'])) {
//                $trainee[$i][rowstatus] = 'fail';
//                $trainee[$i]['failure_reason'] = 'Invalid Race Code.';
//            }
//        }        
//        return $trainee[$i];
//    }
    /*
      validation for bulk registration - trainee bulk registration
     */
//    public function validate_bulk_trainee($excel_data) {
//        $trainee = array();
//        $i = 1;
//        $trainee[1]['db_error'] = '';
//        unset($excel_data[1]);
//        foreach ($excel_data as $k => $exceldata) {
//            $trainee[$i][CompanyCode] = trim($exceldata[10]);
//            if ($this->user->role_id == 'COMPACT') {
//                $trainee[$i][CompanyCode] = $this->user->company_id;
//            }
//            $trainee[$i][countryofresidence] = ($exceldata[1])? trim($exceldata[1]): 'SGP';
//            $trainee[$i][nrictype] = trim($exceldata[2]);
//            $trainee[$i][taxcode] = trim($exceldata[3]);
//            $trainee[$i][nationality] = trim($exceldata[4]);
//            $trainee[$i][education] = trim($exceldata[5]);
//            $trainee[$i][firstname] = trim($exceldata[6]);
//            $trainee[$i][gender] = trim($exceldata[7]);
//            $trainee[$i][dob] = trim($exceldata[8]);
//            $trainee[$i][ContactNumber] = trim($exceldata[9]);
//            $trainee[$i][EmailId] = trim($exceldata[11]);
//            $trainee[$i][Discount] = NULL; //trim($exceldata[12]);
//            $trainee[$i][address] = trim($exceldata[12]);
//            $trainee[$i][City] = trim($exceldata[13]);
//            $trainee[$i][Country] = trim($exceldata[14]);
//            $trainee[$i][State] = trim($exceldata[15]);
//            $trainee[$i][ZipCode] = trim($exceldata[16]);
//            $trainee[$i][SalaryRangeCode] = trim($exceldata[17]);
//            $trainee[$i][occupation] = trim($exceldata[18]);
//            $trainee[$i][RaceCode] = trim($exceldata[19]);
//            $trainee[$i][rowstatus] = 'success';
//            $trainee[$i]['failure_reason'] = '';
//            $trainee[$i][password] = '';
//            $trainee[$i][CompanyName] = '';
//            $trainee[$i] = $this->validate_excel($trainee, $i);            
//            if ($trainee[$i][rowstatus] == 'success') {                
//                $status = $this->traineemodel->save_bulk_user_data($trainee[$i]);
//               
//                if ($status['status'] == FALSE) {
//                    $trainee[$i][rowstatus] = 'fail';
//                    $trainee[$i]['failure_reason'] = 'Insertion Failed';
//                    $trainee[1]['db_error'] = 'db_error';
//                } else {
//                    $trainee[$i][password] = $status['password'];
//                    $trainee[$i][username] = $status['username'];
//                    $trainee[1]['db_error'] = '';
//                }
//            }
//            $i++;
//        }
//        return $trainee;
//    }
    
    public function validate_bulk_trainee($excel_data) {
        $tenant_id=$this->user->tenant_id;
        $trainee = array();
        $i = 1;
        $trainee[1]['db_error'] = '';
        unset($excel_data[1]);
        $restrict_flag = 'false'; // added by shubhranshu for restriction array
        foreach ($excel_data as $k => $exceldata) {
            $trainee[$i][CompanyCode] = trim($exceldata[11]);
            if ($this->user->role_id == 'COMPACT') {
                $trainee[$i][CompanyCode] = $this->user->company_id;
            }
            $trainee[$i][countryofresidence] = ($exceldata[1])? trim($exceldata[1]): 'SGP';
            ////addded by shubhranshu for Xprienz requirement
            if($tenant_id =='T02'){
                $trainee[$i][nrictype] = trim($exceldata[2] ? $exceldata[2] : 'Others');
                if($exceldata[2]){////added by shubhranshu
                    $trainee[$i][nrictypeOthers] = $exceldata[3];
                }else{
                   $trainee[$i][nrictypeOthers] = 'NO TAX CODE';  
                }
               
                $trainee[$i][nationality] = ($exceldata[5] ? $exceldata[5] : 'SINGAPORE PR');
                $trainee[$i][education] = ($exceldata[6] ? $exceldata[6] : "At least 1 GCE 'N' Level Pass");
                $trainee[$i][gender] = ($exceldata[8] ? $exceldata[8] : 'MALE');
            }else{
                $trainee[$i][nrictype] = trim($exceldata[2]);
                $trainee[$i][nrictypeOthers] = trim($exceldata[3]);
                $trainee[$i][nationality] = trim($exceldata[5]);
                $trainee[$i][education] = trim($exceldata[6]);
                $trainee[$i][gender] = trim($exceldata[8]);
            }
            $trainee[$i][taxcode] = trim($exceldata[4]);          
            $trainee[$i][firstname] = trim($exceldata[7]);
            $trainee[$i][dob] = trim($exceldata[9]);
            $trainee[$i][ContactNumber] = trim($exceldata[10]);
            $trainee[$i][EmailId] = trim($exceldata[12]);
            $trainee[$i][Discount] = NULL; //trim($exceldata[12]);
            $trainee[$i][address] = trim($exceldata[13]);
            $trainee[$i][City] = trim($exceldata[14]);
            $trainee[$i][Country] = trim($exceldata[15]);
            $trainee[$i][State] = trim($exceldata[16]);
            $trainee[$i][ZipCode] = trim($exceldata[17]);
            $trainee[$i][SalaryRangeCode] = trim($exceldata[18]);
            $trainee[$i][occupation] = trim($exceldata[19]);
            $trainee[$i][RaceCode] = trim($exceldata[20]);
            $trainee[$i][rowstatus] = 'success';
            $trainee[$i]['failure_reason'] = '';
            $trainee[$i][password] = '';
            $trainee[$i][CompanyName] = '';
            $trainee[$i] = $this->validate_excel($trainee, $i);
            /////added by shubhranshu to check the restriction
            $is_nric_restriction = $this->traineemodel->check_nric_restriction($trainee[$i][taxcode],'BULK_REGISTER'); // added by shubhranshu for client requirement 22/03/2019 for prevent restriction
            if($is_nric_restriction > 0){
                $restrict_flag = 'true';
                $trainee['flag'] = $restrict_flag;
            }
            ///////////////////////////////////////////////////
            if ($trainee[$i][rowstatus] == 'success') {               
                $status = $this->traineemodel->save_bulk_user_data($trainee[$i]);
               //print_r($status);exit;
                if ($status['status'] == FALSE) {
                    $trainee[$i][rowstatus] = 'fail';
                    $trainee[$i]['failure_reason'] = 'Insertion Failed';
                    $trainee[1]['db_error'] = 'db_error';
                } else {
                    $trainee[$i][password] = $status['password'];
                    $trainee[$i][username] = $status['username'];
                    //////below code added by shubhranshu for displaying the userid if its notax code
                    $trainee[$i]['userid'] = $status['userid_for_notax'];//below code added by shubhranshu for displaying the userid if its notax code
                    $trainee[1]['db_error'] = '';
                }
            }
            $i++;
        }
        //print_r($trainee);exit;
        return $trainee;
    }
    /*
      function for bulk registration : uploading the bulk data excel file
     */
    public function bulk_registration() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Bulk Registration';
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        if ($this->input->post("upload")) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = '*';
            $config['max_size'] = '2048';
            $config['max_width'] = '1024';
            $config['max_height'] = '768';
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload()) {
                $data['error'] = $this->upload->display_errors();
            } else {
                $data = $this->upload->data();
                $this->load->library('excel_reader');
                $this->excel_reader->setOutputEncoding('CP1251');
                $read_perm = $this->excel_reader->read($data['full_path']);
                if ($read_perm == 'FALSE') {
                    $data['error'] = 'File is not readable.';
                } else {
                    $excel_data = $this->excel_reader->sheets[0][cells];
                    if(count($excel_data[1]) > 0){////added by shubhranshu to prevent if the sheet is blank
                        
                        $trainee = $this->validate_bulk_trainee($excel_data);
                    }else{
                        $this->session->set_flashdata('error_message', 'Oops! Excel Sheet is blank!');
                        
                    }
                    

                    if ($trainee[1]['db_error'] == 'db_error') {
                        $this->session->set_flashdata('error_message', 'Oops! Sorry, it looks like something went wrong with some record.Please check!');
                    }
                    unset($trainee[1]['db_error']);
                    if (empty($trainee[1]))
                        unset($trainee[1]);
                    $this->load->helper('export');
                    $files = write_import_status($trainee, $this->user->user_id);
                    unlink('./uploads/' . $data['file_name']);
                    
                }
            }
        } 
        $data['files'] = $files;
        $data['page_title'] = "Bulk Registration";
        $data['trainee_data'] = $trainee;
        $data['controllerurl'] = '/trainee/';
        $data['privilage'] = $this->manage_tenant->get_privilage();//added by shubhranshu
        $data['main_content'] = 'trainee/bulkregistration';
        $this->load->view('layout', $data);
    }
   /**
    * download smaple excel file.
    * @param type $file_name
    */
    public function download_xls() {
        $tenant_id = $this->user->tenant_id;////added by shubhranshu due to client request
        if($tenant_id =='T02'){
            $file_name = "uploads/Xprienz_Trainee_Bulk_Registration.xls";
            ob_clean();
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment;filename=Xprienz_Trainee_Bulk_Registration.xls");
            readfile(base_url() . $file_name);
            exit();
        }else{
            $file_name = "uploads/Trainee_Bulk_Registration.xls";
            ob_clean();
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment;filename=Trainee_Bulk_Registration.xls");
            readfile(base_url() . $file_name);
            exit();
        }
        
    }
    /**
     * download import xls
     * @param type $file_name
     */
    public function download_import_xls($file_name) {
        ob_clean();
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment;filename=$file_name");
        readfile(base_url() . 'tmp/trainee/import_status/' . $file_name);
        exit();
    }
    /*
     * This function displays the referrals made by a trainee
     */
    public function referrals() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Referrals';
        $data['main_content'] = 'trainee/referrals';
        $this->load->view('layout', $data);
    }
    /**
     * add more check
     * @param type $add
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
    /**
     * check taxcode exists
     */
    public function check_taxcode_existence() {
        $taxcode = $this->input->post('taxcode');
        $exists = $this->db->select('tax_code')->get_where('tms_users', array('tax_code' => $taxcode), 1)->num_rows();
        echo json_encode($exists);
        exit;
    }
    /**
     * check user name exists
     */
    public function check_user_name_existence() {
        $username = $this->input->post('username');
        $exists = $this->db->select('user_name')->get_where('tms_users', array('user_name' => $username), 1)->num_rows();
        echo json_encode($exists);
        exit;
    }
    /**
     * trainee auto-complete help
     */
    public function get_trainees_by_taxcode_autocomplete() 
    {
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $company_id = htmlspecialchars($_GET['company_id'], ENT_QUOTES, 'UTF-8');
        $query_string = trim($query_string);
        $company_id = trim($company_id);
        $result = $this->traineemodel->trainee_user_list_autocomplete($query_string, $company_id);
        print json_encode($result);
        exit;
    }
    /**
     * This method is used by Trainee - Edit/ Deactivate
     */
    public function get_trainees_by_name_autocomplete() {
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $result = $this->traineemodel->trainee_traineelist_by_name_autocomplete($query_string);
        print json_encode($result);
        exit;
    }
    /**
     * get trainee by taxcode
     */
    public function get_trainees_by_taxcode() {
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $company_id = htmlspecialchars($_GET['company_id'], ENT_QUOTES, 'UTF-8');
        $query_string = trim($query_string);
        $company_id = trim($company_id);
        $result = $this->traineemodel->trainee_user_list_by_taxcode($query_string, $company_id);
        print json_encode($result);
        exit;
    }
    /**
     * unique user name check
     * @param type $username
     * @return type
     */
    public function is_username_unique($username) {
        return $this->db->select('user_name')->get_where('tms_users', array('user_name' => $username), 1)->num_rows();
    }
    /**
     * taxcode unique check
     * @param type $taxcode
     * @return type
     */
    public function is_taxcode_unique($taxcode) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        return $this->db->select('tax_code')->get_where('tms_users', array('tax_code' => $taxcode, 'tenant_id'=>$tenant_id), 1)->num_rows();
    }
    /**
     * email id unique check
     * @param type $emailid
     * @return type
     */
    public function is_emailid_unique($emailid) {
        return $this->db->select('registered_email_id')->get_where('tms_users', array('registered_email_id' => $emailid), 1)->num_rows();
    }
    /**
     * check email id
     * @param type $email
     * @return type
     */
   /// added by shubhranshu
    public function check_email_id($email='') {
        extract($_POST);
        $email_id = trim($email);
        $this->load->model('internal_user_model', 'internaluser');
        $exists = $this->internaluser->check_email($email_id);
        if ($exists) {
            echo TRUE;
        } else {
            echo FALSE;
        }
    }
    /**
     * check user name
     * @return type
     */
    public function check_username() {
        extract($_POST);
        $user_name = trim(($username));
        $this->load->model('internal_user_model', 'internaluser');
        $exists = $this->internaluser->check_username($username);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        return;
    }
    /*  added by shubhranshu for client requirement on 21/03/2019 */
    public function check_nric_restriction(){
        extract($_POST);
        $tax_code = trim(($tax_code));
	$operation = trim(($operation));
        $exists = $this->traineemodel->check_nric_restriction($tax_code,$operation);
        if ($exists) {
            echo 1;
        } else { echo 0;}
    }/*  added by shubhranshu for client requirement on 21/03/2019 */

    /**
     * This method validate the NRIC/FIN number     
     * @return type
     */
    public function check_taxcode() {
        extract($_POST);
        $tax_code = trim(($tax_code));
        if ($country_of_residence == "SGP") {
            $valid = validate_nric_code($nric, $tax_code);
            if ($valid == FALSE) {
                echo 2;
                return;
            }
        }
        $exists = $this->traineemodel->check_taxcode($tax_code, $user_id);
        if ($exists) {
            echo 1;
        } else {
            $result = $this->traineemodel->check_taxcode_without_tenant_id($tax_code, $user_id);            
            if(!empty($result->tenant_id)) {
                $trainee_details = $this->traineemodel->get_trainee_taxcode($result->user_id, $result->tenant_id);
                $trainee_details[userdetails]['dob'] = ($trainee_details[userdetails]['dob'])?date('d-m-Y', strtotime($trainee_details[userdetails]['dob'])):'';
                for($i=0;$i<count($trainee_details[otherdetails]);$i++) {
                    $trainee_details[otherdetails][$i]['valid_till'] = date('d-m-Y', strtotime($trainee_details[otherdetails][$i]['valid_till']));
                }
                for($i=0;$i<count($trainee_details[workdetails]);$i++) {
                    $trainee_details[workdetails][$i]['emp_from_date'] = date('d-m-Y', strtotime($trainee_details[workdetails][$i]['emp_from_date']));
                    $trainee_details[workdetails][$i]['emp_to_date'] = date('d-m-Y', strtotime($trainee_details[workdetails][$i]['emp_to_date']));
                }   
                echo json_encode($trainee_details);
            } else {
                echo 0;
            }
        }
        return;
    }
    /*
     * This function used for getting trainer feed back in view trainee
     */
    public function get_trainer_feedback() {
        $course = $this->input->post('course');
        $class = $this->input->post('class');
        $user = $this->input->post('user');
        $payment = $this->input->post('payment');
        $result = $this->traineemodel->get_trainer_feedback($course, $class, $user,$payment);
        echo json_encode($result);
        exit();
    }
    /*
     * This function for inserting the trainer feed back
     */
    public function trainer_feedback($user_id, $course_id, $class_id) {
        $result = $this->traineemodel->trainer_feedback($user_id, $course_id, $class_id);
        if ($result == TRUE) {
            $this->session->set_flashdata("success", "Your feedback has been updated successfully.");
        } else {
            $this->session->set_flashdata("error", "We have not been able to save the feedback.Please try again later or get in touch with your Administrator.");
        }
        if($this->input->post("viewuser")) {
            redirect("internal_user/view_user/$user_id");
        } else {
            redirect("trainee/view_trainee/$user_id");
        }
    }
    /**
     * function to print LOC
     */
    public function print_loc($class_id, $user_id) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        if (empty($class_id) || empty($user_id)) {
            return show_404();
        }
        $this->load->model('course_model', 'coursemodel');
        $loc_details = $this->traineemodel->get_loc_details($tenant_id, $class_id, $user_id);
        $loc_details->tenant_country = $this->coursemodel->get_metadata_on_parameter_id($loc_details->tenant_country);
        $loc_details->tenant_state = $this->coursemodel->get_metadata_on_parameter_id($loc_details->tenant_state);
        $loc_details->exam_date = $this->traineemodel->get_assessment_date($class_id, $user_id);
        $this->load->helper('pdf_reports_helper');
        generate_loc_pdf($loc_details);
    }
    
    public function print_wsq_loc($course_id, $class_id, $user_id) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        if (empty($class_id) || empty($user_id)) {
            return show_404();
        }
            $this->load->helper('pdf_reports_helper');       
            $loc_details = $this->traineemodel->get_wsq_loc_details($tenant_id, $class_id, $user_id);
            $loc_details->trainer_name = $this->traineemodel->get_trainer_details($loc_details->classroom_trainer);
            generate_wsq_loc_pdf($loc_details);

    }
    
    /*
     * This function is used to retrieve the states based on the country selection in Internal User => Add User page
     */
    public function get_states_json() {
        $country_param = $this->input->post('country_param');
        $this->load->model('internal_user_model', 'internaluser');
        $states = $this->internaluser->get_states($country_param);
        $states_arr = array();
        foreach ($states as $item) {
            $states_arr[] = $item;
        }
        echo json_encode($states_arr);
        exit;
    }
    /**
     * Method to reactivate trainee
     */
    public function reactivate_trainee() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $user_id = $this->input->post('user_id');
            $reason_for_reactivation = $this->input->post('reason_for_reactivation');
            $other_reason_for_reactivation = $this->input->post('other_reason_for_reactivation');
            $company_id = $this->input->post('company_id');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('reason_for_reactivation', 'reason for reactivation', 'required');
            if ($reason_for_reactivation == 'OTHERS') {
                $this->form_validation->set_rules('other_reason_for_reactivation', 'other reason for reactivation', 'required');
            }
            if ($this->form_validation->run() == TRUE) {
                
                $trainee_id = $this->input->post('user_id');
                $res = $this->traineemodel->get_complete_trainee_details($trainee_id);
                $previous_trainee_data = json_encode($res);
                
                $result = $this->traineemodel->reactivate_trainee($user_id, $company_id, $reason_for_reactivation, $other_reason_for_reactivation);
                if ($result == TRUE){
                     user_activity(3,$user_id,$previous_trainee_data);
                    $this->session->set_flashdata('success_message', 'Trainee has been reactivated successfully');
                }else{
                    $this->session->set_flashdata('error_message', 'Oops! Sorry, it looks like something went wrong.Please try again!.');
            }}
        }
        redirect('trainee');
    }
    /**
     * Enroll trainee during registration
     */
    public function enroll_trainee() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $this->load->model('internal_user_model');
        $this->load->model('class_model');
        $trainee_id = $this->session->userdata('new_trainee_user_id');
        $tenant_id = $this->user->tenant_id;
        $loggedin_user_id =  $this->user->user_id;
        $data['course_id'] = $course_id = $this->input->post('course_id');
        $data['class_id'] = $class_id = $this->input->post('class_id');
        
        //Check if the sales exec is assigned to class to add as sales exec for comission
        //Changed by - Ujwal
        //Date : 24/07/2015
         //changed by prit on 18/07/2016
        $data['classes'] = $classes = $this->class->get_class_details($tenant_id, $class_id);
        /* get the sales executive name based on course- prit*/
        $course_salesexec = $this->class->get_course_salesexec1($tenant_id, $course);
        $sales=array();
        foreach ($course_salesexec as $value){
             $sales[]=$value->user_id;

        }
        $sales_executive=  implode(',', $sales);
        //$data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $classes->course_id, $classes->sales_executive);
        $data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $classes->course_id, $sales_executive);
        $checkrole = $this->internal_user_model->check_sales_exec($loggedin_user_id,'SLEXEC');
        $role = $this->internal_user_model->check_sales_exec1($loggedin_user_id);
       // $check_salesexe_assigned = $checkrole && in_array($loggedin_user_id , explode(',', $classes->sales_executive)) ? $loggedin_user_id : FALSE ; commented on 18/07/2017 to get sales exe id who enrols trainee
        //$data['salesexec'] = $sales_guy;
        
        if($role->role_id!='ADMN')
        {
            //if ($check_salesexe_assigned || $role->role_id=='CRSEMGR' || $role->role_id=='TRAINER')
            if ($this->user->role_id == 'SLEXEC' || $this->user->role_id=='CRSEMGR' || $this->user->role_id=='TRAINER') 
            {
                   $data['salesexec_check'] = 1;

            }
        }
        //exit('');
        $class_pymnt_enrol = $this->input->post('class_pymnt_enrol');
        $data['class_pymnt_enrol'] = $class_pymnt_enrol;
         $data['tenant_id'] = $this->user->tenant_id;
        $data['result_array'] = $this->classtraineemodel->fees_payable($trainee_id, $tenant_id, $course_id, $class_id,0, $company_id, $loggedin_user_id);
        $data['trainee_data'] = $this->traineemodel->trainee_detais($trainee_id,$tenant_id);
        $data['page_title'] = 'Enroll New';
        $data['main_content'] = 'trainee/enroll_trainee_details';
        $this->load->view('layout', $data);
    }
    ///////added by shubhranshu to prevent enrollment for paid company invoice on 05/12/2018////////////////
    function check_company_invoice_status($course_id='', $class_id='',$company_id=''){
        $course_id = $this->input->post('crs_id');
        $class_id = $this->input->post('cls_id'); 
        $comp_id = $this->input->post('comp_id');
        $result = $this->traineemodel->check_company_invoice_status($comp_id,$course_id,$class_id);
        echo json_encode($result);exit;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * recalculate payment details
     */
    public function re_calc_payment_details(){
        $loggedin_user_id =  $this->user->user_id;
        $tenant_id = $this->user->tenant_id;
        $trainee_id = $this->session->userdata('new_trainee_user_id');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $enrolment_mode = $this->input->post('enrolment_mode');
        $company_id = ($enrolment_mode == 'COMPSPON')?$this->input->post('company'):0;
        $result = $this->classtraineemodel->fees_payable($trainee_id, $tenant_id, $course_id, $class_id,0, $company_id, $loggedin_user_id);
        echo json_encode($result);        
        exit();
    }
    /**
     * update trainee discount
     */
    public function update_userdiscount(){
        $result = $this->traineemodel->update_traineediscount($this->user->tenant_id);
        echo ($result) ? 1 : 0;
        exit();
    }
    
    /**
     * reset password
     * @param type $user_id
     */
    public function reset_password($user_id){
        if(empty($user_id)){
            $this->session->set_flashdata("error_message", "Unable to reset password. Try again.");
            redirect('trainee');
        }else{
            $status = common_reset_password($user_id, 'TAXCODE');
            if($status){
                
                $user = $this->session->userdata('userDetails');
                $tenant_id = $user->tenant_id;
                $res = $this->traineemodel->get_username($user_id,$tenant_id);
                
                 $date_time = date('Y-m-d h:i:s');
                $data = array('user_id'=>$user_id,
//                                'first_name'=>$first_name,
//                                'old_password'=>$old_password,
                                'date_time'=>$date_time);
                $previous_details = json_encode($data);

                user_activity(16, $res->user_name, $previous_details,1);
//                $this->session->set_flashdata("success", "Password has been reset successfully.");
                $trainee_email_id = company_user_email_id($user_id);
                $this->session->set_flashdata("success", "Password has been reset successfully. An email has sent on ".$trainee_email_id." and password is :".$status);
                
            }else{
                $this->session->set_flashdata("error", "Unable to reset password. Try again.");
            }
            redirect('trainee/view_trainee/'.$user_id);
        }
    }
    
    public function test_send_mail(){
  
        $this->load->library('email');
        $this->email->from(FROM_EMAIL_ID, INBOX_MAIL_NAME);
        $this->email->to('abdullah@mailinator.com');
        $this->email->subject('Hello');
        $this->email->message('Good to know you are happy');
        if ($this->email->send()) {
            echo "mail sent successfully";
             echo $this->email->print_debugger();
        
        }else{
            echo "something went wrong";
             echo $this->email->print_debugger();
        }
    }
}

