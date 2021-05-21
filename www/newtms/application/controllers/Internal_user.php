<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Internal user Use case all features. 
 */
class Internal_user extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('internal_user_model', 'internaluser');
        $this->load->model('meta_values');
        $this->load->model('common_model', 'commonmodel');
        $this->load->helper('common');
        $this->load->helper('metavalues_helper');
        $this->load->helper('url');
        $this->load->library('bcrypt');
    }
    /*
     * This function loads the initial list view page for internal users.
     */
    public function index() {
        //$this->output->enable_profiler(true);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Internal User';
        $export_url = '?';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
       
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $data['sort_link'] = $sort_link = "user_role=" . $this->input->get('user_role') . "&first_last_name=" . $this->input->get('first_last_name') . "&filter_status=" . $this->input->get('filter_status') . "&search_radio=" . $this->input->get('search_radio').'&user_id='.$this->input->get('user_id');
        
        $totalrows = $this->internaluser->get_internal_user_count_by_tenant_id($tenant_id);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'usr.last_modified_on';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $role_id = ($this->input->get('user_role')) ? $this->input->get('user_role') : '';
        $status = ($this->input->get('filter_status')) ? $this->input->get('filter_status') : '';
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'internal_user/';
        $pageno = $this->uri->segment(2);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $data['tabledata'] = $this->internaluser->get_internal_user_list($tenant_id, $records_per_page, $offset, $field, $order_by);
        //print_r($data);exit;
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'internal_user/';
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
       
        $data['main_content'] = 'internaluser/userlist';
        $data['roles'] = $this->internaluser->get_user_role($tenant_id);
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
     * This method is used to export all internal user data for a tenant to an XLS
     */
    public function exportnow() {
        $sqlquery = $this->internaluser->get_internal_user_list_export($tenantId);
        $this->load->helper('export_helper');
    }
    /**
     * export internal staff data
     */
    public function export_users_page() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $query = $this->internaluser->get_internal_user_list_export($tenant_id);
        $this->load->helper('export_helper');
        export_users_page($query);
    }
    /**
     * export full internal staff data
     */
    public function export_users_full() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $query = $this->internaluser->get_internal_user_list_export($tenant_id);
        $this->load->helper('export_helper');
        export_users_full($query);
    }    
    /*
     * This function loads the Add Internal user form and allows the user to save the data entered into the form.
     */
    public function add_user() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Add User';
        $data['roles'] = $this->internaluser->get_user_role($user->tenant_id);
        $valid = TRUE;
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');            
            $country_of_residence = $this->input->post('country_of_residence'); 
            $this->form_validation->set_rules('country_of_residence', 'Country of Residence', 'required');
            $this->form_validation->set_rules('pers_contact_number', 'Contact Number', 'required|max_length[12]');
            if ($country_of_residence == 'IND') {
                $this->form_validation->set_rules('PAN', 'PANNumber', 'required|max_length[15]|callback_check_unique_usertaxcode');
                $tax_code = $this->input->post("PAN");
            }
            if ($country_of_residence == 'SGP') {
                $NRIC = $this->input->post('NRIC');
                $NRIC_OTHER = $this->input->post("NRIC_OTHER");
                $NRIC_ID = $this->input->post('NRIC_ID');
                $tax_code = $NRIC_ID;
                $this->form_validation->set_rules('NRIC', 'NRIC Type', 'required');
                if($NRIC_OTHER != "NOTAXCODE" ){
                    $this->form_validation->set_rules('NRIC_ID', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
                    if(!empty($NRIC) && $NRIC != "SNG_3") {
                        $valid = validate_nric_code($NRIC, $NRIC_ID);
                        if ($valid == FALSE) {                                                   
                            $data['tax_error'] = 'Invalid NRIC Code.';
                            $data['main_content'] = 'internaluser/adduser';
                            $this->load->view('layout', $data);
                            return;
                        }
                    }
                }                
            }
            if ($country_of_residence == 'USA') {
                $tax_code = $this->input->post("SSN");
                $this->form_validation->set_rules('SSN', 'SSNNumber', 'required|max_length[15]|callback_check_unique_usertaxcode');
            }
            $this->form_validation->set_rules('user_registered_email', 'UserEmail', 'max_length[50]|callback_check_unique_useremail');
            $this->form_validation->set_rules('user_name', 'Username', 'required|max_length[15]|callback_check_unique_username');
            $this->form_validation->set_rules('emp_email', 'Employee Email', 'required|callback_check_unique_useremail_emp');
            if ($this->form_validation->run() == TRUE) {
                $taxcodeStatus = $this->commonmodel->is_taxcode_exist($tax_code, $tenant_id);                
                if(!$taxcodeStatus){
                     $user_name = $this->input->post("user_name");                     
                     $usernameStatus = $this->commonmodel->is_username_exist($user_name, $tenant_id);
                     if($usernameStatus){
                          $failure_msg = 'Duplicate Username. Please change the user name.';
                     }
                }else{
                    $failure_msg = 'Duplicate Tax Code. Please change the tax code.';
                }
            }            
            if( ($valid) && $this->form_validation->run() == TRUE && (!$taxcodeStatus) && (!$usernameStatus)){
                $uid = $this->internaluser->save_user_data($user);
                if (!empty($_FILES['userfile']['name']) && $uid) {
                    $this->load->helper('upload_helper');
                    $image_data = upload_image('uploads/images/internal_user', $uid);
                    if ($image_data['status']) {
                        $image_path = $image_data['image']['system_path'] . '/' .
                                $image_data['image']['raw_name'] . '_thumb' . $image_data['image']['file_ext'];
                        save_image_path($uid, $image_path);
                    } 
                }
                if ($uid == FALSE)
                    $this->session->set_flashdata('error_message', 'Unable to create user account. Please try again later.');
                else
                    $this->session->set_flashdata('success_message', 'Internal user account has been created successfully');
                redirect('internal_user');
            } else {                
                $data['tax_error'] = ($data['tax_error'])?$data['tax_error']:$failure_msg;
                $data['main_content'] = 'internaluser/adduser';
                $this->load->view('layout', $data);          
            }
        }
        else {
            $data['main_content'] = 'internaluser/adduser';
            $this->load->view('layout', $data);
        }
    }
    /*
     * This function is used to retrieve the states based on the country selection in Internal User => Add User page
     */
    public function get_states_json() {
        $country_param = $this->input->post('country_param');
        $states = $this->internaluser->get_states($country_param);
        $states_arr = array();
        foreach ($states as $item) {
            $states_arr[] = $item;
        }
        echo json_encode($states_arr);
        exit;
    }
    /*
     * This function loads the Edit Internal user form.
     */
    public function edit_user() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Edit User';
        $form_style_attr = ' style="display:none;"';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $user_details = explode('(', $this->input->post('search_user_firstname'));
            $edit_user_id = $this->input->post('search_user_id');
            $data['edit_user_id'] = $edit_user_id;			
            $form_style_attr = ' style="display: ;"';
            if ($this->input->post('edit_user_form_btn') != '') {
                $this->load->library('form_validation');
                $edit_user_id = $this->input->post('edit_user_id');
                $user_list_values = $this->internaluser->get_user_details($tenant_id, $edit_user_id);                
                if ($user_list_values == false) {
                    redirect('internal_user/edit_user');
                }
                $this->session->set_userdata('registered_email_id_edit', $user_list_values->registered_email_id);
                $this->session->set_userdata('user_name_edit', $user_list_values->user_name);
                $this->session->set_userdata('tax_code_edit', $user_list_values->tax_code);
                $this->form_validation->set_rules('country_of_residence', 'Country of Residence', 'required');
                $this->form_validation->set_rules('pers_contact_number', 'Contact Number', 'required|max_length[12]');
                $country_of_residence = $this->input->post('country_of_residence');
                $valid = TRUE; //Added By dummy for Edit issue (Nov 10 2014)
                if ($country_of_residence == 'IND') {
                    $tax_code = $this->input->post("PAN");
                    $this->form_validation->set_rules('PAN', 'PANNumber', 'required|max_length[15]|callback_check_unique_usertaxcode_edit');
                }                
                if ($country_of_residence == 'SGP') {
                    $NRIC = $this->input->post('NRIC');
                    $NRIC_OTHER = $this->input->post("NRIC_OTHER");
                    $NRIC_ID = $this->input->post('NRIC_ID');
                    $tax_code = $NRIC_ID;
                    $this->form_validation->set_rules('NRIC', 'NRIC Type', 'required');                    
                    if($NRIC_OTHER != "NOTAXCODE" ){
                        $this->form_validation->set_rules('NRIC_ID', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
                        if(!empty($NRIC) && $NRIC != "SNG_3") {
                            $valid = validate_nric_code($NRIC, $NRIC_ID);
                            if ($valid == FALSE) {                                                   
                                $data['tax_error'] = 'Invalid NRIC Code.';                                
                            }
                        }
                    }                
                }
                if ($country_of_residence == 'USA') {
                    $tax_code = $this->input->post('SSN');
                    $this->form_validation->set_rules('SSN', 'SSNNumber', 'required|max_length[15]|callback_check_unique_usertaxcode_edit');
                } //added by shubhranshu to validate company email
                $this->form_validation->set_rules('user_registered_email', 'UserEmail', 'max_length[50]|callback_check_unique_useremail_edit');
                //$this->form_validation->set_rules('emp_email', 'Employee Email', 'required|callback_check_unique_useremail_emp');
                if ($valid && $this->form_validation->run() == TRUE && $user_list_values->tax_code != $tax_code) {
                    $taxcodeStatus = $this->commonmodel->is_taxcode_exist($tax_code, $tenant_id); 
                    if($taxcodeStatus) {                       
                       $failure_msg = 'Duplicate Tax Code. Please change the tax code.'; 
                    }
                }            
                if ($valid && $this->form_validation->run() == TRUE && (!$taxcodeStatus)) {
                    $delete_image = $this->input->post('deleteimage') ? $this->input->post('deleteimage') : 'no';
                    
                    $result = $this->internaluser->get_interuser_details($edit_user_id);// skm1
                    $previous_data = json_encode($result);
                    
                    $uid = $this->internaluser->update_user_data();
                    $this->load->helper('upload_helper');
                    if (!empty($_FILES['userfile']['name']) && $uid && $delete_image == 'no') {
                        $image_data = upload_image('uploads/images/internal_user', $uid);//echo "image upload :";print_r($image_data);exit;
                        if ($image_data['status']) {
                            $image_path = $image_data['image']['system_path'] . '/' .
                                    $image_data['image']['raw_name'] . '_thumb' . $image_data['image']['file_ext'];
                            $previous_thumb_path = fetch_image_path_by_uid($uid);
                            remove_previous_image($previous_thumb_path);
                            save_image_path($uid, $image_path);
                        } 
                    } else if ($uid && $delete_image == 'no') {
                        $previous_thumb_path = fetch_image_path_by_uid($uid);
                        remove_previous_image($previous_thumb_path);
                        save_image_path($uid);
                    }
                    if ($uid == FALSE){
                        $this->session->set_flashdata('error_message', 'Unable to update user account. Please try again later');
                    }else{
                         user_activity(1,$edit_user_id,$previous_data);
                        $this->session->set_flashdata('success_message', 'Internal user account has been updated successfully');
                    }redirect('internal_user');
                } else {
                    $data['tax_error'] = ($data['tax_error'])?$data['tax_error']:$failure_msg;
                }
            }
        }
        $user_list_values = $this->internaluser->get_user_details($tenant_id, $edit_user_id);
				
        $data['user_list_values'] = $user_list_values;
        $country_of_residence = get_param_value($user_list_values->country_of_residence);
        $data['country_of_residence'] = $country_of_residence;
        if ($user_list_values->country_of_residence == 'IND') {
            $data['pan_number'] = $user_list_values->tax_code;
        }
        if ($user_list_values->country_of_residence == 'SGP') {
            $data['nric_number'] = $user_list_values->tax_code;
            $data['tax_code_type'] = $user_list_values->tax_code_type;
            $data['other_identi_type'] = $user_list_values->other_identi_type;
        }
        if ($user_list_values->country_of_residence == 'USA') {
            $data['ssn_number'] = $user_list_values->tax_code;
        }
        $user_personal_country = get_param_value($user_list_values->personal_address_country);
        $data['user_personal_country'] = $user_personal_country;
        $user_personal_state = get_param_value($user_list_values->personal_address_state);
        $data['user_personal_state'] = $user_personal_state;
        $user_office_designation = get_param_value($user_list_values->designation);
        $data['user_office_designation'] = $user_office_designation;
        $user_office_country = get_param_value($user_list_values->off_address_country);
        $data['user_office_country'] = $user_office_country;
        $user_office_state = get_param_value($user_list_values->off_address_state);
        $data['user_office_state'] = $user_office_state;
        $user_role_name = $this->internaluser->get_user_role_name($user_list_values->role_id);
        $data['user_role_name'] = $user_role_name;
        $data['roles'] = $this->internaluser->get_user_role($tenant_id);
        $user_role_check = $this->internaluser->user_role_check($edit_user_id, $tenant_id);
        $data['user_role_check'] = $user_role_check;
        $data['form_style_attr'] = $form_style_attr;
        $data['main_content'] = 'internaluser/edituser';
        $this->load->view('layout', $data);
    }
    /*
     * This function loads the View Internal user form.
     */
    public function view_user($user_id) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'View User';
        $user_list_values = $this->internaluser->get_user_details($tenant_id, $user_id);
        $data['user_list_values'] = $user_list_values;
        $data['discountdetails'] = $this->internaluser->get_discount_details($tenant_id, $user_id);
        $this->load->model('trainee_model', 'traineemodel');
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
        $data['controllerurl'] = "internal_user/view_user/$user_id";
        $data['training_details'] = $this->traineemodel->get_training_details_new($user_id, $records_per_page, $offset, $field, $order_by);
        foreach($data['training_details'] as $k => $v){
            $data['training_details'][$k]->att_status = get_common_attendance_status_new($tenant_id, $v->user_id, $v->course_id, $v->class_id,$v->attn_stats);
        }
        $totalrows = $this->traineemodel->get_training_details_count($user_id);
        $data['sort_order'] = $order_by;
        $data['user_id'] = $user_id;
        $this->load->helper('pagination');
        $baseurl = base_url() . "internal_user/view_user/$user_id/";
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);
        if ($user_list_values->other_identi_type != NULL && $user_list_values->other_identi_type != '') {
            $other_identi_type = get_param_value($user_list_values->other_identi_type);
            $data['other_identi_type'] = " (" . $other_identi_type->category_name . " )";
        }
        $country_of_residence = ($user_list_values->country_of_residence) ? get_param_value($user_list_values->country_of_residence) : '';
        $data['country_of_residence'] = $country_of_residence;
        $user_personal_country = ($user_list_values->personal_address_country) ? get_param_value($user_list_values->personal_address_country) : '';
        $data['user_personal_country'] = $user_personal_country;
        $user_personal_state = ($user_list_values->personal_address_state) ? get_param_value($user_list_values->personal_address_state) : '';
        $data['user_personal_state'] = $user_personal_state;
        $user_office_designation = ($user_list_values->designation) ? get_param_value($user_list_values->designation) : '';
        $data['user_office_designation'] = $user_office_designation;
        $user_office_country = ($user_list_values->off_address_country) ? get_param_value($user_list_values->off_address_country) : '';
        $data['user_office_country'] = $user_office_country;
        $user_office_state = ($user_list_values->off_address_state) ? get_param_value($user_list_values->off_address_state) : '';
        $data['user_office_state'] = $user_office_state;
        $data['main_content'] = 'internaluser/viewuser';
        $this->load->view('layout', $data);
    }
    /**
     * get internal staff auto-complete
     */
    public function get_user_list_autocomplete() {
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $result = $this->internaluser->internal_user_list_autocomplete($query_string);
        print json_encode($result);
        exit;
    }
    /*
     * This function deactivates the user selectd.
     */
    public function deactivate_user() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            foreach ($this->input->post() as $key => $value) {
                $$key = $value;
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules('reason_for_deactivation', 'reason for deactivation', 'required');
            if ($reason_for_deactivation == 'OTHERS') {
                $this->form_validation->set_rules('other_reason_for_deactivation', 'other reason for deactivation', 'required');
            }
            
            $deactive_data = $this->internaluser->get_interuser_details($user_id);
            $previous_data = json_encode($deactive_data);
            
            if ($this->form_validation->run() == TRUE) {
                $result = $this->internaluser->deactivate_user($user_id);
                if ($result == TRUE){
                    user_activity(1,$user_id,$previous_data);
                    $this->session->set_flashdata('success_message', 'Internal user account has been deactivated successfully');
                }else{
                    $this->session->set_flashdata('error_message', 'Oops! Sorry, it looks like something went wrong.Please try again!.');
                }
                    redirect('internal_user');
            }
        }
        $data['main_content'] = 'internaluser/deactivateuser';
        $data['sideMenuData'] = $this->sideMenu;
        $data['user_id'] = $user_id;
        $this->load->view('layout', $data);
    }
    /*
     * This function is used to check whether the username already exists
     */
     function check_unique_username() {
        if ($user_name = $this->input->post('user_name')) {
            $exists = $this->internaluser->check_duplicate_user_name($user_name);
            if (!$exists) {
                $this->form_validation->set_message('check_unique_username', "Username exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * Checks if user name already exists (Edit user)
     */
    function check_unique_username_edit() {
        if ($user_name = $this->input->post('user_name')) {
            $exists = $this->internaluser->check_duplicate_user_name_edit($user_name, $this->session->userdata('user_name_edit'));
            if (!$exists) {
                $this->form_validation->set_message('check_unique_username_edit', "Username exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * Checks if user email already exists (Add user)
     */
    function check_unique_useremail() {
        if ($user_email = $this->input->post('user_registered_email')) {
            $exists = $this->internaluser->check_duplicate_user_email($user_email);
            if ($exists) {
                $this->form_validation->set_message('check_unique_useremail', "Email ID exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    ///// below function was added by shubhranshu to check client side duplicates email
    function check_unique_useremail_client() {
        if ($emp_email = $this->input->post('emp_email')) {
            $exists = $this->internaluser->check_duplicate_user_email_company($emp_email);
            if ($exists) {
                echo '1';
            }else{
                echo '0';
            }
            
        }
    }
    
    function check_unique_useremail_emp() {
        if ($emp_email = $this->input->post('emp_email')) {
            $exists = $this->internaluser->check_duplicate_user_email_company($emp_email);
            if ($exists) {
                $this->form_validation->set_message('check_unique_useremail_emp', "Employee Email ID Already exists!!.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * Checks if user email already exists (Edit user)
     */
    function check_unique_useremail_edit() {
        if ($user_email = $this->input->post('user_registered_email')) {
            $exists = $this->internaluser->check_duplicate_user_email_edit($user_email, $this->session->userdata('registered_email_id_edit'));
            if (!$exists) {
                $this->form_validation->set_message('check_unique_useremail_edit', "Email ID exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * Checks if user taxcode already exists (Add user)
     */
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
            $exists = $this->internaluser->check_duplicate_user_taxcode($tax_code);            
            if (!$exists) {
                $this->form_validation->set_message('check_unique_usertaxcode', "Duplicate Tax Code. Please change the tax code.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * Checks if user taxcode already exists (Edit user)
     */
    function check_unique_usertaxcode_edit() {
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
            $exists = $this->internaluser->check_duplicate_user_taxcode_edit($tax_code, $this->session->userdata('tax_code_edit'));
            if (!$exists) {
                $this->form_validation->set_message('check_unique_usertaxcode_edit', "Taxcode exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * Method for checking if email id is already exists in add Interanl user.
     */
    public function check_email_id($email='') {
        extract($_POST);
        $email_id = trim($email);
        $exists = $this->internaluser->check_email($email_id);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        return;
    }
    ////// added by shubhranshu for valid email (internal user view page) on 4/12/2018///////////////////
    public function check_email_status($email='') {
        extract($_POST);
        $email_id = trim($email);
        $exists = $this->internaluser->check_email_status($email_id,$usrid);
        if ($exists) {
            echo 1;
        } else {
            $this->internaluser->update_email_status($email_id,$usrid);
            echo 0;
        }
        return;
    }//////////////////////////////////////////////////////////////////////////
    /*
     * Method for checking if pan id is already exists in add Interanl user.
     */
    public function check_pan($pan='') { /// modified by shubhranshu
        extract($_POST);        
        if($country_of_residence == "SGP") {
            $valid = validate_nric_code($nric, $pan_id);            
            if($valid == FALSE) {
                $exists->status = '2';
                echo json_encode($exists);
                return;
            }
        }
        $exists = $this->internaluser->check_pan();                
        if (!empty($exists)) {
            $exists->nationality = ($exists->nationality)?get_catname_by_parm($exists->nationality):'';
            $exists->gender = ($exists->gender)?get_catname_by_parm($exists->gender):'';
            $exists->personal_address_country = ($exists->personal_address_country)?get_catname_by_parm($exists->personal_address_country):'';
            $exists->personal_address_state = ($exists->personal_address_state)?get_catname_by_parm($exists->personal_address_state):'';
            $exists->dob = ($exists->dob)?date('d-m-Y',  strtotime($exists->dob)):'';
            $exists->status = '1';
            echo json_encode($exists);
        } else {            
            echo json_encode('0');
        }
        return TRUE;
    }
    /*
     * This Method for checks if user name already exists in add Internal user.
     */
    public function check_username() {
        extract($_POST);
        $user_name = trim(($username));
        $exists = $this->internaluser->check_username($username);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        return;
    }
    /*
     * This Method for internal user auto complete
     */
    public function get_internal_user_autocomplete() {
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $result = $this->internaluser->internal_user_autocomplete($query_string);
        print json_encode($result);
        exit;
    }
    /*
     * This method for changing the role of trainee to Internal Staff. used in Internal Staff.
     */
    public function trainee_role_change() {
        $status = $this->internaluser->trainee_role_change();
        if($status == TRUE) {
            $this->session->set_flashdata('success_message','Trainee has been successfully upgraded to Internal Staff');
        } else {
            $this->session->set_flashdata('error_message','Unable to upgrade your account. Please try again later');
        }
        redirect("internal_user/");
    }
    /**
     * Method to reactivate users
     */
    public function reactivate_user() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $user_id = $this->input->post('user_id');
            $reason_for_reactivation = $this->input->post('reason_for_reactivation');
            $other_reason_for_reactivation = $this->input->post('other_reason_for_reactivation');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('reason_for_reactivation', 'reason for reactivation', 'required');
            if ($reason_for_reactivation == 'OTHERS') {
                $this->form_validation->set_rules('other_reason_for_reactivation', 'other reason for reactivation', 'required');
            }
            if ($this->form_validation->run() == TRUE) {
                $res = $this->internaluser->get_interuser_details($user_id);
                $previous_data = json_encode($res);
                $result = $this->internaluser->reactivate_user($user_id, $reason_for_reactivation, $other_reason_for_reactivation);
                if ($result == TRUE){
                    user_activity(1,$user_id,$previous_data);
                    $this->session->set_flashdata('success_message', 'Internal user account has been reactivated successfully');
                }else{
                    $this->session->set_flashdata('error_message', 'Oops! Sorry, it looks like something went wrong.Please try again!.');
            }}
        }
        redirect('internal_user');
    }    
    /**
     * reset password
     * @param type $user_id
     */
    public function reset_password($user_id){
        if(empty($user_id)){
            $this->session->set_flashdata("error_message", "Unable to reset password. Try again.");
            redirect('internal_user');
        }else{
            $status = common_reset_password($user_id, 'TAXCODE');
            if($status){
                $user = $this->session->userdata('userDetails');
                $tenant_id = $user->tenant_id;
                $res = $this->internaluser->get_username($user_id,$tenant_id);
                
                $date_time = date('Y-m-d h:i:s');
                $data = array('user_id'=>$user_id,
//                                'first_name'=>$first_name,
//                                'old_password'=>$old_password,
                                'date_time'=>$date_time);
                $previous_details = json_encode($data);
                user_activity(16, $res->user_name, $previous_details,1);
                $this->session->set_flashdata("success", "Password has been reset successfully.");
            }else{
                $this->session->set_flashdata("error", "Unable to reset password. Try again.");
            }
            redirect('internal_user/view_user/'.$user_id);
        }
    }
    
     public function internal_user_reset_password($user_id){
        if(empty($user_id)){
            $this->session->set_flashdata("error_message", "Unable to reset password. Try again.");
            redirect('internal_user');
        }else{
            $user = $this->session->userdata('userDetails');
            $tenant_id = $user->tenant_id;
            $status = internal_staff_reset_password($user_id, 'TAXCODE');
            if($status){
                $res = $this->internaluser->get_username($user_id,$tenant_id);
                $date_time = date('Y-m-d h:i:s');
                $data = array('user_id'=>$user_id,
                                'date_time'=>$date_time);
                $previous_details = json_encode($data);
                user_activity(16, $res->user_name, $previous_details,1);
                
                $official_email_id = official_email_id($user_id);
                
                
//                $this->session->set_flashdata("success", "Password has been reset successfully. An email has sent on ".$official_email_id);
                $this->session->set_flashdata("success", "Password has been reset successfully. An email has sent on ".$official_email_id." and password is : ".$status);
            }else{
                $this->session->set_flashdata("error", "Unable to reset password. Try again.");
            }
            redirect('internal_user/view_user/'.$user_id);
        }
    }
}
