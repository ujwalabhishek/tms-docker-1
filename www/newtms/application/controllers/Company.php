<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Company Use case all features. 
 */

class Company extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('company_model', 'companymodel');
        $this->load->model('internal_user_model', 'internaluser');
        $this->load->model('common_model', 'commonmodel');
        $this->load->model('meta_values');
        $this->load->helper('metavalues');
        $this->load->helper('common');
        $this->load->helper('side_menu');
    }

    /*
     * This function loads the initial list view page for company.
     */

    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Company';
        $export_url = '?';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $data['query_string'] = $query_string = 'search_company_name=' . $this->input->get('search_company_name') . '&business_type=' . $this->input->get('business_type') . '&filter_status=' . $this->input->get('filter_status');
        $totalrows = $this->companymodel->get_company_count_by_tenant_id($tenant_id);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'cm.last_modified_on';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'company/';
        $pageno = $this->uri->segment(2);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $tabledata = $this->companymodel->get_company_list($tenant_id, $records_per_page, $offset, $field, $order_by);
        for ($i = 0; $i < count($tabledata); $i++) {
            $tabledata[$i]['num_registered_trainees'] = $this->companymodel->get_company_registered_trainees_num($tenant_id, $tabledata[$i]['company_id']);
            $tabledata[$i]['num_active_trainees'] = $this->companymodel->get_company_active_trainees_num($tenant_id, $tabledata[$i]['company_id']);
            $tabledata[$i]['comp_status_param_id'] = $tabledata[$i]['comp_status'];
            $comp_status = get_param_value($tabledata[$i]['comp_status']);
            $tabledata[$i]['comp_status'] = $comp_status->category_name;
            if ($tabledata[$i]['comp_state'] != '') {
                $comp_state = get_param_value($tabledata[$i]['comp_state']);
                $tabledata[$i]['comp_state'] = $comp_state->category_name;
            }
            if ($tabledata[$i]['comp_cntry'] != '') {
                $comp_cntry = get_param_value($tabledata[$i]['comp_cntry']);
                $tabledata[$i]['comp_cntry'] = $comp_cntry->category_name;
            }
            $tabledata[$i]['last_activity_details'] = '';
            $last_modified_by = $this->internaluser->get_user_details($tenant_id, $tabledata[$i]['last_modified_by']);
            if ($last_modified_by->user_name) {
                $tabledata[$i]['last_activity_details'] = $last_modified_by->first_name . ' ' . $last_modified_by->last_name . ', ' . $tabledata[$i]['last_modified_on'];
            }

            $tabledata[$i]['company_discount'] = $this->companymodel->get_company_discount($tenant_id, $tabledata[$i]['company_id'], "all");
        }
        $data['tabledata'] = $tabledata;
        $data['sort_order'] = $this->input->get('o');
        $data['controllerurl'] = 'company/' . $pageno;
        $data['controllerurl_link'] = 'company/' . $pageno;
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $query_string);
        $data['filter_status'] = fetch_metavalues_by_category_id(Meta_Values::STATUS);
        $data['main_content'] = 'company/companylist';
        $this->load->view('layout', $data);
    }

    /*
     * This function loads the empty Add company form.
     */

    public function add_new_company() {
        //$this->output->enable_profiler(true);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $this->load->library('form_validation');
        $data['page_title'] = 'Add New Company';
        $this->load->model('course_model', 'course');
        $regno = $this->input->post('regno');
        $data['courses'] = $this->course->get_active_course_list_by_tenant($tenant_id, 'discount');
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            extract($_POST);
            $this->form_validation->set_rules('company_name', 'Company Name', 'required|max_length[250]');
            //Added by Abdulla
            if ($tenant_id != 'T02') {
                $this->form_validation->set_rules('regno', 'Registration Number', 'required|callback_check_registration_number[' . $regno . ']');
            } else {
                $this->form_validation->set_rules('regno', 'Registration Number', 'required');
            }
            $this->form_validation->set_rules('business_type', 'Business Type', 'required');
            $this->form_validation->set_rules('business_s', 'Business Size', 'required');
            $this->form_validation->set_rules('phoneno', 'Phone Number', 'required');
            $this->form_validation->set_rules('country_of_residence', 'Country', 'required');
            $this->form_validation->set_rules('comp_attn', 'Company Attn.', 'required');
            $this->form_validation->set_rules('comp_email', 'Company Email', 'required');
            $fname = $data['fname'] = $this->input->post('fname');
            $lname = $data['lname'] = $this->input->post('lname');
            $gender = $data['gender'] = $this->input->post('gender');
            $contactno = $data['contactno'] = $this->input->post('contactno');
            $mobileno = $data['mobileno'] = $this->input->post('mobileno');
            $mobile_p = $data['mobile_p'] = $this->input->post('mobile_p');
            $email_01 = $data['email_01'] = $this->input->post('email_01');
            $email_02 = $data['email_02'] = $this->input->post('email_02');
            $username = $data['username'] = $this->input->post('username');
            for ($i = 0; $i < count($fname); $i++) {
                $this->form_validation->set_rules('fname[' . $i . ']', 'Firstname', 'required');
            }
            for ($i = 0; $i < count($contactno); $i++) {
                $this->form_validation->set_rules('contactno[' . $i . ']', 'Contact No.', 'required');
            }
            for ($i = 0; $i < count($email_01); $i++) {
                $this->form_validation->set_rules('email_01[' . $i . ']', 'Email Id', 'required'); //|callback_check_email[' . $email_01[$i] . '] unique check removed, author: dummy, date: 16/03/2015
            }
            for ($i = 0; $i < count($username); $i++) {
                $this->form_validation->set_rules('username[' . $i . ']', 'Username', 'required|callback_check_username[' . $username[$i] . ']');
            }
            if ($this->form_validation->run() == TRUE) {
                $regno = $this->input->post('regno');
                $regnoStatus = $this->commonmodel->is_registration_number_exist($regno);
                if ($tenant_id == 'T02' && $regnoStatus) {
                    $regnoStatus = FALSE;
                }
                if ($regnoStatus) {
                    $failure_msg = 'Duplicate Registration No. Please change Registration No.';
                }
            }
            if ($this->form_validation->run() == TRUE && (!$regnoStatus)) {
                $this->companymodel->save_company_details();
                $this->session->set_flashdata('companyadded', 'Company has been added successfully!');
                redirect('company');
            } else {
                $data['tax_error'] = ($data['tax_error']) ? $data['tax_error'] : $failure_msg;
            }
        }

        $data['main_content'] = 'company/addnewcompany';

        $this->load->view('layout', $data);
    }

    /*
     * This function loads the Edit company form.
     */

    public function edit_company() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['tenant_id'] = $tenant_id;
        $data['page_title'] = 'Edit Company';
        $this->load->library('form_validation');
        $form_style_attr = ' style="display:none;"';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $edit_search_company_name = $this->input->post('edit_search_company_name');
            $company_id = $this->input->post('edit_search_company_id');
            $data['company_id'] = $company_id;

            if ($company_id != '') {
                $form_style_attr = ' style="display:inline;"';
                $data['no_company_found'] = '';
            } else {
                $data['no_company_found'] = "No company found!";
            }
            if ($this->input->post('deactivate_user_id') != '') {
                $deactivate_user_id = $this->input->post('deactivate_user_id');
                $deactivate_reason = $this->input->post('deactivate_reason');
                $deactivate_other_reason = $this->input->post('deactivate_other_reason');
                $this->companymodel->deactivate_company_contact($tenant_id, $company_id, $deactivate_user_id, $deactivate_reason, $deactivate_other_reason);
                $this->session->set_userdata('contact_deactivated_success', 'Company contact has been deactivated successfully!');
            }
            if ($this->input->post('submit_button') != '') {
                extract($_POST);
                $this->form_validation->set_rules('company_name', 'Company Name', 'required|max_length[250]');
                if ($tenant_id != 'T02') {
                    $this->form_validation->set_rules('regno', 'Registration Number', 'required|callback_check_registration_number_edit[' . $regno_current . ']');
                } else {
                    $this->form_validation->set_rules('regno', 'Registration Number', 'required');
                }                
                $this->form_validation->set_rules('business_type', 'Business Type', 'required');
                $this->form_validation->set_rules('business_s', 'Business Size', 'required');
                $this->form_validation->set_rules('phoneno', 'Phone Number', 'required');
                $this->form_validation->set_rules('country_of_residence', 'Company Country', 'required');
                $this->form_validation->set_rules('comp_attn', 'Company Attn.', 'required');
                $this->form_validation->set_rules('comp_email', 'Company Email', 'required');
                $fname = $data['fname'] = $this->input->post('fname');
                $lname = $data['lname'] = $this->input->post('lname');
                $gender = $data['gender'] = $this->input->post('gender');
                $contactno = $data['contactno'] = $this->input->post('contactno');
                $mobileno = $data['mobileno'] = $this->input->post('mobileno');
                $mobile_p = $data['mobile_p'] = $this->input->post('mobile_p');
                $email_01 = $data['email_01'] = $this->input->post('email_01');
                $email_02 = $data['email_02'] = $this->input->post('email_02');
                $username = $data['username'] = $this->input->post('username');
                for ($i = 0; $i < count($fname); $i++) {
                    $this->form_validation->set_rules('fname[' . $i . ']', 'Firstname', 'required');
                }
                for ($i = 0; $i < count($contactno); $i++) {
                    $this->form_validation->set_rules('contactno[' . $i . ']', 'Contact No.', 'required');
                }
                for ($i = 0; $i < count($email_01); $i++) {
                    $this->form_validation->set_rules('email_01[' . $i . ']', 'Email Id', 'required'); //|callback_check_email_edit[' . $email_01_current[$i] . '] unique email check removed, author: dummy, date: 16/03/2015
                }
                for ($i = 0; $i < count($username); $i++) {
                    $this->form_validation->set_rules('username[' . $i . ']', 'Username', 'required|callback_check_username_edit[' . $username_current[$i] . ']');
                }
                if ($this->form_validation->run() == TRUE) {
                    $regno = $this->input->post('regno');
                    $regnoStatus = $this->commonmodel->is_registration_number_exist($regno);
                    if ($tenant_id == 'T02' && $regnoStatus) {
                        $regnoStatus = FALSE;
                    }
                    if ($regnoStatus) {
                        $failure_msg = 'Duplicate Registration No. Please change Registration No.';
                    }
                }
                if ($this->form_validation->run() == TRUE) {
                    $result = $this->companymodel->get_company_status_info($company_id);
                    $previous_data = json_encode($result);
                    $this->companymodel->update_company_details($company_id);
                    user_activity(2, $company_id, $previous_data);
                    $this->session->set_flashdata('companyupdated', 'Company has been updated successfully!');
                    redirect('company');
                } else {
                    $data['tax_error'] = ($data['tax_error']) ? $data['tax_error'] : $failure_msg;
                }
            }
        }
        $data['form_style_attr'] = $form_style_attr;
        $company_users_details = $this->companymodel->get_company_users_details($tenant_id, $company_id);
        $active_company_contacts_num = 0;
        foreach ($company_users_details as $contcat) {
            if ($contcat->user_acct_status == 'ACTIVE') {
                $active_company_contacts_num++;
            }
        }
        $data['active_company_contacts_num'] = $active_company_contacts_num;
        $data['company_users_details'] = $company_users_details;
        $company_info = $this->companymodel->get_company_details($tenant_id, $company_id);
        $data['company_info'] = $company_info[0];
        $data['company_discount'] = $this->companymodel->get_company_discount($tenant_id, $company_id);
        $data['number_trainees_payment_pending'] = $this->companymodel->number_trainees_payment_pending($company_id);
        $data['main_content'] = 'company/editcompany';
        $this->load->view('layout', $data);
    }

    /**
     * This function loads the View company form.
     * @param type $company_id
     */
    public function view_company($company_id) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['tenant_id'] = $tenant_id;
        $data['page_title'] = 'View Company';

        $data['controllerurl'] = 'company/';
        $data['company_id'] = $company_id;

        $data['company_users_details'] = $this->companymodel->get_company_users_details($tenant_id, $company_id);
        $data['company_info'] = $company_info = $this->companymodel->get_company_details($tenant_id, $company_id);
        $this->load->model('course_model', 'coursemodel');
        $data['sme_nonsme'] = $this->coursemodel->get_metadata_on_parameter_id($company_info[0]->sme_nonsme);

        $trainees_count = $this->companymodel->get_company_trainees_count($tenant_id, $company_id);
        $data['trainees_count'] = $trainees_count['totalrows'];
        $data['company_discount'] = $this->companymodel->get_company_discount($tenant_id, $company_id, "all");
        $data['main_content'] = 'company/viewcompany';

        $this->load->view('layout', $data);
    }

    /**
     * checks if user name already exists
     * @param type $arg_username
     * @return boolean
     */
    public function check_username($arg_username = '') {
        extract($_POST);
        $user_name = trim($username);
        if ($arg_username != '') {
            $user_name = $arg_username;
        }
        $num_rows = $this->companymodel->check_username($user_name);
        if ($arg_username != '') {
            if ($num_rows >= 1) {
                $this->form_validation->set_message('check_username', "Username exists!");
                return false;
            } else {
                return true;
            }
        }
        if ($num_rows >= 1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * checks if user name already exists
     * @param type $arg_username
     * @param type $arg_curr_username
     * @return boolean
     */
    public function check_username_edit($arg_username = '', $arg_curr_username = '') {
        extract($_POST);
        $user_name = trim(($username));
        $curr_user_name = trim(($curr_username));
        if ($arg_username != '') {
            $user_name = $arg_username;
            $curr_user_name = $arg_curr_username;
        }
        $num_rows = $this->companymodel->check_username_edit($user_name, $curr_user_name);
        if ($arg_username != '') {
            if ($num_rows >= 1) {
                $this->form_validation->set_message('check_username_edit', "Username exists!");
                return false;
            } else {
                return true;
            }
        }
        if ($num_rows >= 1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * checks if email id is already exists (Add company)
     * @param type $arg_email
     * @return boolean
     */
    public function check_email($arg_email = '') {
        extract($_POST);
        $email_id = trim($email);
        if ($arg_email != '') {
            $email_id = $arg_email;
        }
        $num_rows = $this->companymodel->check_email($email_id);
        if ($arg_email != '') {
            if ($num_rows >= 1) {
                $this->form_validation->set_message('check_email', "Email Id exists!");
                return false;
            } else {
                return true;
            }
        }
        if ($num_rows >= 1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * checks if email id is already exists (Edit company)
     * @param type $arg_email
     * @param type $arg_curr_email
     * @return boolean
     */
    public function check_email_edit($arg_email = '', $arg_curr_email = '') {
        extract($_POST);
        $email_id = trim($email);
        $curr_email_id = trim($curr_email);
        if ($arg_email != '') {
            $email_id = $arg_email;
            $curr_email_id = $arg_curr_email;
        }
        $num_rows = $this->companymodel->check_email_edit($email_id, $curr_email_id);
        if ($arg_email != '') {
            if ($num_rows >= 1) {
                $this->form_validation->set_message('check_email_edit', "Email Id exists!");
                return false;
            } else {
                return true;
            }
        }
        if ($num_rows >= 1) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * Checks if registration number is already exists (Add company)
     * @param type $arg_reg_num
     * @return boolean
     */
    public function check_registration_number($arg_reg_num = '') {
        echo "aaa"; exit;
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        extract($_POST);
        $reg_num = trim($reg_num);
        if ($arg_reg_num != '') {
            $reg_num = $arg_reg_num;
        }
        $num_rows = $this->companymodel->check_registration_number($reg_num, $tenant_id);
        if ($arg_reg_num != '') {
            if ($num_rows >= 1) {
                $this->form_validation->set_message('check_registration_number', "Registration Number exists!");
                if ($tenant_id == 'T02') {
                    return true;
                }
                return false;
            } else {
                return true;
            }
        }
        if ($num_rows >= 1) {
            echo 1;
            if ($tenant_id == 'T02') {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    /**
     * Checks if registration number is already exists (Edit company)
     * @param type $arg_reg_num
     * @param type $arg_curr_reg_num
     * @return boolean
     */
    public function check_registration_number_edit($arg_reg_num = '', $arg_curr_reg_num = '') {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        extract($_POST);
        $reg_num = trim($reg_num);
        $curr_reg_num = trim($curr_reg_num);
        if ($arg_reg_num != '') {
            $reg_num = $arg_reg_num;
            $curr_reg_num = $arg_curr_reg_num;
        }
        $num_rows = $this->companymodel->check_registration_number_edit($reg_num, $curr_reg_num, $tenant_id);
        if ($arg_reg_num != '') {
            if ($num_rows >= 1) {
                $this->form_validation->set_message('check_registration_number_edit', "Registration Number exists!");
                if ($tenant_id == 'T02') {
                    return true;
                }
                return false;
            } else {
                return true;
            }
        }
        if ($num_rows >= 1) {
            echo 1;
            if ($tenant_id == 'T02') {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    /**
     * get company list used by auto-complete
     */
    public function get_company_list_autocomplete() {
        $name_startsWith = htmlspecialchars($_GET['name_startsWith'], ENT_QUOTES, 'UTF-8');
        $name_startsWith = trim($name_startsWith);
        $mode = '';
        $mode = htmlspecialchars($_GET['mode'], ENT_QUOTES, 'UTF-8');
        $result = $this->companymodel->internal_company_list_autocomplete($name_startsWith, $mode);
        print json_encode($result);
        exit;
    }

    /**
     * get trainee list auto-complete
     */
    public function get_company_trainee_list_autocomplete() {
        $name_startsWith = htmlspecialchars($_GET['name_startsWith'], ENT_QUOTES, 'UTF-8');
        $name_startsWith = trim($name_startsWith);
        $company_id = htmlspecialchars($_GET['company_id'], ENT_QUOTES, 'UTF-8');
        $result = $this->companymodel->internal_company_trainee_list_autocomplete($name_startsWith, $company_id);
        print json_encode($result);
        exit;
    }

    /**
     * get trainee tax code auto-complete
     */
    public function get_company_trainee_taxcode_autocomplete() {
        $name_startsWith = htmlspecialchars($_GET['name_startsWith'], ENT_QUOTES, 'UTF-8');
        $name_startsWith = trim($name_startsWith);
        $company_id = htmlspecialchars($_GET['company_id'], ENT_QUOTES, 'UTF-8');
        $result = $this->companymodel->internal_company_trainee_taxcode_autocomplete($name_startsWith, $company_id);
        print json_encode($result);
        exit;
    }

    /**
     * deactivate company
     */
    public function deactivate_company() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $company_id = $this->input->post('deactivate_company_id');
        $data['page_title'] = 'Company';
        $data['company_id'] = $company_id;
        $data['not_authorized_error'] = '';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            extract($_POST);
            $res = $this->companymodel->get_company_status_info($company_id);
            $previous_data = json_encode($res);
            $this->companymodel->company_deactivate($tenant_id, $company_id, $company_reason_for_deactivation, $company_other_reason_for_deactivation);
            user_activity(2, $company_id, $previous_data);
            $this->session->set_flashdata('company_deactivated', 'Company has been deactivated successfully!');
            redirect('company');
        }
    }

    /**
     * get trainees by company id
     * @param type $company_id
     */
    public function trainees($company_id) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Company';
        $data['company_id'] = $company_id;
        $data['not_authorized_error'] = '';
        $export_url = '?';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $data['query_string'] = $query_string = 'search_by=' . $this->input->get('search_by') . '&search_company_trainee_name=' . $this->input->get('search_company_trainee_name') . '&search_company_trainee_taxcode=' . $this->input->get('search_company_trainee_taxcode');
        $trainees_count = $this->companymodel->get_company_trainees_count($tenant_id, $company_id);
        $totalrows = $trainees_count['totalrows'];
        $totalrows_no_search = $trainees_count['totalrows_no_search'];
        $data['totalrows_no_search'] = $totalrows_no_search;
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'tu.last_modified_on';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'company/trainees/' . $company_id . '/';
        $pageno = $this->uri->segment(4);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $tabledata = $this->companymodel->get_company_trainees_list($tenant_id, $company_id, $records_per_page, $offset, $field, $order_by);
        $data['tabledata'] = $tabledata;
        $data['sort_order'] = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $data['controllerurl'] = 'company/trainees/' . $company_id . '/';
        $data['controllerurl_link'] = 'company/trainees/' . $company_id . '/';
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $query_string);
        $data['main_content'] = 'company/trainees';
        $this->load->view('layout', $data);
    }

    /**
     * export company data to xls
     */
    public function export_company_page_fields() {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $this->db->select('cm.company_id');
        $this->db->select('cm.company_name');
        $this->db->select('cm.comp_scn SCN');
        $this->db->select('tc.comp_status');
        $this->db->select('cm.last_modified_by, cm.last_modified_on');
        $this->db->select('cm.comp_address, cm.comp_city, cm.comp_state, cm.comp_cntry, cm.comp_zip, cm.comp_phone');
        $this->db->from('tenant_company tc');
        $this->db->join('company_master cm', 'cm.company_id=tc.company_id');
        $this->db->where('tc.tenant_id', $tenant_id);
        $search_company_name = trim($this->input->get('search_company_name'));
        $search_company_name_arr = explode(' (', $search_company_name);
        $business_type = $this->input->get('business_type');
        $filter_status = $this->input->get('filter_status');
        if (count($search_company_name_arr) > 0)
            $indexPos = count($search_company_name_arr) - 1;
        else
            $indexPos = 0;
        if ($search_company_name_arr[$indexPos] != '') {
            $this->db->like('cm.comp_regist_num', trim($search_company_name_arr[$indexPos], ')'), 'after');
        }
        if ($business_type != '') {
            $this->db->like('cm.business_type', $business_type, 'after');
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('tc.comp_status', $filter_status);
        }
        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'cm.last_modified_on';
        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('cm.last_modified_on', 'DESC');
        }
        $data = $this->db->get()->result();
        $excel_titles = array('Company Name', 'Last Activity', 'Contact Details', 'Registered Trainees', 'Active Trainees', 'SCN', 'Status');
        $excel_data = array();
        $data_count = count($data); // added by dummy for reducing for loop execution time on Nov 25 2014.
        for ($i = 0; $i < $data_count; $i++) {
            $excel_data[$i][] = $data[$i]->company_name;
            $last_modified_by = $this->internaluser->get_user_details($tenant_id, $data[$i]->last_modified_by);
            if ($last_modified_by->user_name) {
                $excel_data[$i][] = $last_modified_by->first_name . ' ' . $last_modified_by->last_name . ', ' . $data[$i]->last_modified_on;
            } else {
                $excel_data[$i][] = 'NIL';
            }
            $comp_state = get_param_value($data[$i]->comp_state);
            $comp_cntry = get_param_value($data[$i]->comp_cntry);
            $contact_details = '';
            if ($data[$i]->comp_address != '') {
                $contact_details .= $data[$i]->comp_address . ', ';
            }
            if ($data[$i]->comp_city != '') {
                $contact_details .= $data[$i]->comp_city . ", ";
            }
            if ($data[$i]->comp_state != '') {
                $contact_details .= $comp_state->category_name . ", ";
            }
            if ($data[$i]->comp_cntry != '') {
                $contact_details .= $comp_cntry->category_name . ", ";
            }
            if ($data[$i]->comp_zip != '') {
                $contact_details .= $data[$i]->comp_zip . ", ";
            }
            if ($data[$i]->comp_phone != '') {
                $contact_details .= "Phone: " . $data[$i]->comp_phone;
            }
            $excel_data[$i][] = $contact_details;
            $excel_data[$i][] = $this->companymodel->get_company_registered_trainees_num($tenant_id, $data[$i]->company_id);
            $excel_data[$i][] = $this->companymodel->get_company_active_trainees_num($tenant_id, $data[$i]->company_id);
            $excel_data[$i][] = $data[$i]->SCN;
            $comp_status = get_param_value($data[$i]->comp_status);
            $excel_data[$i][] = $comp_status->category_name;
        }
        $excel_filename = 'company_list_page_fields.xls';
        $excel_sheetname = 'Company List';
        $heading = 'List of all companies registered with ' . $this->data['tenant_details']->tenant_name . ' as on ' . date('F d Y l');
        $this->load->helper('export_helper');
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $heading);
    }

    /**
     * export all fields
     */
    public function export_company_all_fields() {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $this->db->select('cm.company_id');
        $this->db->select('cm.sme_nonsme');
        $this->db->select('cm.comp_attn');
        $this->db->select('cm.comp_email');
        $this->db->select('cm.company_name');
        $this->db->select('cm.comp_scn SCN');
        $this->db->select('tc.comp_status');
        $this->db->select('cm.last_modified_by, cm.last_modified_on');
        $this->db->select('cm.comp_address, cm.comp_city, cm.comp_state, cm.comp_cntry, cm.comp_zip, cm.comp_phone, cm.comp_fax, cm.business_type, cm.business_size, cm.comp_regist_num');
        $this->db->select('tc.comp_discount, tc.comp_frgn_discnt');
        $this->db->from('tenant_company tc');
        $this->db->join('company_master cm', 'cm.company_id=tc.company_id');
        $this->db->where('tc.tenant_id', $tenant_id);
        $search_company_name = trim($this->input->get('search_company_name'));
        $search_company_name_arr = explode(' (', $search_company_name);
        $business_type = $this->input->get('business_type');
        $filter_status = $this->input->get('filter_status');
        if (count($search_company_name_arr) > 0)
            $indexPos = count($search_company_name_arr) - 1;
        else
            $indexPos = 0;
        if ($search_company_name_arr[$indexPos] != '') {
            $this->db->like('cm.comp_regist_num', trim($search_company_name_arr[$indexPos], ')'), 'after');
        }
        if ($business_type != '') {
            $this->db->like('cm.business_type', $business_type, 'after');
        }
        if ($filter_status != '' && $filter_status != 'All') {
            $this->db->where('tc.comp_status', $filter_status);
        }
        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'cm.last_modified_on';
        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('cm.last_modified_on', 'DESC');
        }
        $query = $this->db->get_compiled_select(); // changed by shubhranshu for CI update
        $data = $this->db->query($query)->result();
        $excel_titles = array('Company Name', 'Registration No.', 'Business Type', 'Size', 'Country', 'Address', 'Phone Number', 'Fax Number', 'Local Discount', 'Foreign Discount', 'SME Type', 'Company Attn.', 'Company Email', 'Total Registered Trainees', 'Total Active Trainees', 'SCN', 'Last Activity', 'Company Status');
        $excel_data = array();
        $excel_sub_titles = array('First Name', 'Last Name', 'Gender', 'Phone Number', 'Mobile Number', 'Email ID', 'Username', 'Contact Status');
        $excel_sub_data = array();
        $this->load->model('course_model', 'coursemodel');
        $data_count = count($data); // added by dummy for reducing for loop execution time on Nov 25 2014.
        for ($i = 0; $i < count($data); $i++) {
            $comp_state = get_param_value($data[$i]->comp_state);
            $comp_cntry = ($data[$i]->comp_cntry) ? get_param_value($data[$i]->comp_cntry) : '';
            $excel_data[$i][] = $data[$i]->company_name;
            $excel_data[$i][] = $data[$i]->comp_regist_num;
            if ($data[$i]->business_type != '') {
                $business_type = get_param_value($data[$i]->business_type);
            }
            $excel_data[$i][] = $business_type->category_name;
            if ($data[$i]->business_size != '') {
                $business_size = get_param_value($data[$i]->business_size);
            }
            $excel_data[$i][] = $business_size->category_name;
            $excel_data[$i][] = $comp_cntry->category_name;
            $address = '';
            if ($data[$i]->comp_address != '') {
                $address .= $data[$i]->comp_address . ', ';
            }
            if ($data[$i]->comp_city != '') {
                $address .= $data[$i]->comp_city . ', ';
            }
            if ($data[$i]->comp_state != '') {
                $address .= $comp_state->category_name . ', ';
            }
            if ($data[$i]->comp_cntry != '') {
                $address .= $comp_cntry->category_name . ', ';
            }
            if ($data[$i]->comp_zip != '') {
                $address .= $data[$i]->comp_zip . ', ';
            }
            if ($data[$i]->comp_phone != '') {
                $address .= "Phone: " . $data[$i]->comp_phone;
            }
            $address = rtrim($address, ', ');
            $excel_data[$i][] = $address;
            $excel_data[$i][] = $data[$i]->comp_phone;
            $excel_data[$i][] = $data[$i]->comp_fax;
            $excel_data[$i][] = number_format($data[$i]->comp_discount, 2);
            $excel_data[$i][] = number_format($data[$i]->comp_frgn_discnt, 2);
            $excel_data[$i][] = $this->coursemodel->get_metadata_on_parameter_id($data[$i]->sme_nonsme);
            $excel_data[$i][] = $data[$i]->comp_attn;
            $excel_data[$i][] = $data[$i]->comp_email;
            $excel_data[$i][] = $this->companymodel->get_company_registered_trainees_num($tenant_id, $data[$i]->company_id);
            $excel_data[$i][] = $this->companymodel->get_company_active_trainees_num($tenant_id, $data[$i]->company_id);
            $excel_data[$i][] = $data[$i]->SCN;
            $last_modified_by = $this->internaluser->get_user_details($tenant_id, $data[$i]->last_modified_by);
            if ($last_modified_by->user_name) {
                $excel_data[$i][] = $last_modified_by->first_name . ' ' . $last_modified_by->last_name . ', ' . $data[$i]->last_modified_on;
            } else {
                $excel_data[$i][] = 'NIL';
            }
            $comp_status = get_param_value($data[$i]->comp_status);
            $excel_data[$i][] = $comp_status->category_name;
            $contact_details = $this->companymodel->get_company_users_details($tenant_id, $data[$i]->company_id);
            $contact_details_count = count($contact_details);
            $c = 0;
            for ($j = 0; $j < $contact_details_count; $j++) {
                if ($contact_details[$j]->gender != '') {
                    $contact_gender = get_param_value($contact_details[$j]->gender);
                }
                $contact_status = get_param_value($contact_details[$j]->user_acct_status);
                $conctact_array = array(
                    $contact_details[$j]->first_name,
                    $contact_details[$j]->last_name,
                    $contact_gender->category_name,
                    $contact_details[$j]->contact_number,
                    $contact_details[$j]->alternate_contact_number,
                    $contact_details[$j]->registered_email_id,
                    $contact_details[$j]->user_name,
                    $contact_status->category_name
                );
                $excel_sub_data[$i][] = $conctact_array;
            }
        }
        $excel_filename = 'company_list_all_fields.xls';
        $excel_sheetname = 'Company List';
        $heading = 'List of all companies registered [Full Detail] with ' . $this->data['tenant_details']->tenant_name . ' as on ' . date('F d Y l');
        $this->load->helper('export_helper');
        export_all_fields($excel_titles, $excel_sub_titles, $excel_data, $excel_sub_data, $excel_filename, $excel_sheetname, $heading);
    }

    /**
     * export trainee data
     * @param type $company_id
     */
    public function export_company_trainee_page($company_id) {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $this->db->select('tu.user_id, DATE(tu.acct_acti_date_time) AS acct_acti_date_time, DATE(tu.registration_date) AS registration_date, tu.acc_activation_type, tup.first_name, tup.last_name, tu.country_of_residence, tu.tax_code, tu.tax_code_type, tu.other_identi_type, tu.other_identi_code, tup.dob, tu.registered_email_id, tup.contact_number, tup.personal_address_bldg, tup.personal_address_city, tup.personal_address_state, tup.personal_address_country, tup.personal_address_zip');
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
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'tu.last_modified_on';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $this->db->order_by($field, $order_by);
        $export_company_trainee_page_query = $this->db->return_query();
        $data = $this->db->query($export_company_trainee_page_query)->result();
        $excel_titles = array('Trainee Name', 'Nationality', 'Tax Code/NRIC', 'Registration Date', 'Date of Birth', 'Contact Details');
        $excel_data = array();
        for ($i = 0; $i < count($data); $i++) {
            $excel_data[$i][] = $data[$i]->first_name . ' ' . $data[$i]->last_name;
            $country = get_param_value($data[$i]->country_of_residence);
            $excel_data[$i][] = $country->category_name;
            if ($data[$i]->tax_code_type && $data[$i]->tax_code) {
                if ($data[$i]->tax_code_type != 'OTHERS') {
                    $type = get_param_value($data[$i]->tax_code_type);
                    $tax_code_nric = $type->category_name . ' - ' . $this->mask_format($data[$i]->tax_code);
                }
            }
            if ($data[$i]->other_identi_type && $data[$i]->other_identi_code) {
                $tax_code_type = get_param_value($data[$i]->tax_code_type);
                $type = get_param_value($data[$i]->other_identi_type);
                $tax_code_nric = $tax_code_type->category_name . ' - ' . $type->category_name . ' - ' . $this->mask_format($data[$i]->other_identi_code);
            }
            $excel_data[$i][] = $tax_code_nric;
            if ($data[$i]->acc_activation_type == 'BPEMAC') {
                $datetime = DateTime::createFromFormat('Y-m-d', $data[$i]->registration_date);
                $excel_data[$i][] = $datetime->format('d/m/Y');
            } else {
                $datetime = DateTime::createFromFormat('Y-m-d', $data[$i]->acct_acti_date_time);
                $excel_data[$i][] = $datetime->format('d/m/Y');
            }
            if (!empty($data[$i]->dob)) {
                $datetime = DateTime::createFromFormat('Y-m-d', $data[$i]->dob);
                $excel_data[$i][] = $datetime->format('d/m/Y');
            } else {
                $excel_data[$i][] = '';
            }
            $contact_details = '';
            if (!empty($data[$i]->personal_address_bldg)) {
                $contact_details .= $data[$i]->personal_address_bldg . ', ';
            }
            if ($data[$i]->personal_address_city != '') {
                $contact_details .= $data[$i]->personal_address_city . ', ';
            }
            if ($data[$i]->personal_address_state != '' && $data[$i]->personal_address_state != 0) {
                $state = get_param_value($data[$i]->personal_address_state);
                $contact_details .= $state->category_name . ', ';
            }
            if ($data[$i]->personal_address_country != '') {
                $country = get_param_value($data[$i]->personal_address_country);
                $contact_details .= $country->category_name . ', ';
            }
            if ($data[$i]->personal_address_zip != '') {
                $contact_details .= $data[$i]->personal_address_zip . ', ';
            }
            if (!empty($data[$i]->contact_number)) {
                $contact_details .= "Phone: " . $data[$i]->contact_number . ', ';
            }
            if (!empty($data[$i]->registered_email_id)) {
                $contact_details .= "Email: " . $data[$i]->registered_email_id . ', ';
            }
            $contact_details = rtrim($contact_details, ', ');
            $excel_data[$i][] = $contact_details;
        }
        $excel_filename = 'company_trainees_list.xls';
        $excel_sheetname = 'Company Trainee List';
        $heading = 'List of all trainees registered with ' . $this->data['tenant_details']->tenant_name . ' as on ' . date('F d Y l');
        $this->load->helper('export_helper');
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $heading);
    }

    /**
     * method to reactivate company
     */
    /* shubhranshu  start: replace nric first 5 character with mask */
    function mask_format($nric) {
        if (is_numeric($nric) == 1) {
            return $nric;
        } else {
            $new_nric = substr_replace($nric, 'XXXXX', 0, 5);
            //$new_nric = substr_replace($nric,'XXXX',5);        
            return $new_nric;
        }
    }

    /* shubhranshu end */

    public function reactivate_company() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $reason_for_reactivation = $this->input->post('reason_for_reactivation');
            $other_reason_for_reactivation = $this->input->post('other_reason_for_reactivation');
            $company_id = $this->input->post('company_id');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('reason_for_reactivation', 'reason for reactivation', 'required');
            if ($reason_for_reactivation == 'OTHERS') {
                $this->form_validation->set_rules('other_reason_for_reactivation', 'other reason for reactivation', 'required');
            }
            if ($this->form_validation->run() == TRUE) {
                $res = $this->companymodel->get_company_status_info($company_id);
                $previous_data = json_encode($res);
                $result = $this->companymodel->reactivate_company($company_id, $reason_for_reactivation, $other_reason_for_reactivation);
                if ($result == TRUE) {
                    user_activity(2, $company_id, $previous_data);
                    $this->session->set_flashdata('company_deactivated', 'Company has been reactivated successfully');
                } else {
                    $this->session->set_flashdata('company_db_error', 'Oops! Sorry, it looks like something went wrong.Please try again!.');
                }
            }
        }
        redirect('company');
    }

    /**
     * save company discount
     */
    public function update_companydiscount() {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $result = $this->companymodel->update_companydiscount($tenant_id);
        echo ($result) ? 1 : 0;
        exit();
    }

    /**
     * reset password
     * @param type $user_id
     * @param type $company_id
     */
    public function reset_password($user_id, $company_id) {
        if (empty($user_id) || empty($company_id)) {
            $this->session->set_flashdata("companyadded", "<span class='error1'>Unable to reset password. Try again.</span>");
            redirect('company');
        } else {
            $status = common_reset_password($user_id);
            if ($status) {
                $user = $this->session->userdata('userDetails');
                $tenant_id = $user->tenant_id;
                $res = $this->companymodel->get_username($user_id, $tenant_id);
                $date_time = date('Y-m-d h:i:s');
                $data = array('user_id' => $user_id,
                    'company_id' => $company_id,
//                                'old_password'=>$old_password,
                    'date_time' => $date_time);
                $previous_details = json_encode($data);

                user_activity(16, $res->user_name, $previous_details, 2);
//                $this->session->set_flashdata("success", "Password has been reset successfully.");
                $comp_user_email_id = company_user_email_id($user_id);
                $this->session->set_flashdata("success", "Password has been reset successfully. An email has sent on " . $comp_user_email_id . " and password is :" . $status);
            } else {
                $this->session->set_flashdata("error", "Unable to reset password. Try again.");
            }
            redirect('company/view_company/' . $company_id);
        }
    }

    ///////function created by shubhranshu to test email
    public function sendnewmail() {
        $this->load->library('email');

        $this->email->from('biipmisg2020@gmail.com');
        $this->email->to('sspklo@mailinator.com');
        //$this->email->cc('another@another-example.com');
        //$this->email->bcc('them@their-example.com');

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');

        if ($this->email->send()) {
            echo "mail sent";
            exit;
        } else {
            echo $this->email->print_debugger();
        }
    }

}
