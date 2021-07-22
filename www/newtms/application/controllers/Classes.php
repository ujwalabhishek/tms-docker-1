<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Class Use case features. 
 */

class Classes extends CI_Controller {
    
    private $user;

    public function __construct() {
        parent::__construct();
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $this->load->model('class_trainee_model', 'classTraineeModel');
        $this->load->model('class_model', 'classmodel');
        $this->load->model('course_model', 'coursemodel');
        $this->load->model('tpg_api_model', 'tpgModel');
        $this->load->model('meta_values');
        $this->user = $this->session->userdata('userDetails');
       
    }

    /**
     * function to check class unique name
     */
    public function check_classname_unique() {
        $tenant_id = $this->tenant_id;
        $exists = $this->classmodel->check_classname_unique($tenant_id);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        exit();
    }

    /**
     * generate payment received details
     */
    function payment_received() {
        $tenantId = $this->tenant_id;
        $userId = $this->session->userdata('userDetails')->user_id;
        $trainee_id = $this->input->post('user_id');
        $class_id = $this->input->post('class_id');
        $result = $this->classmodel->get_payment_received($tenantId, $class_id, $trainee_id);
        $result['recd_on'] = date('F d Y, l', strtotime($result['recd_on']));
        $result['mode_of_pymnt'] = rtrim($this->coursemodel->get_metadata_on_parameter_id($result['mode_of_pymnt']), ', ');
        $result['total_inv_discnt'] = number_format(($result['class_fees'] / 100) * $result['class_discount'], 2);
        $result['amount_recd'] = number_format(($result['total_gst'] + $result['total_inv_amount']), 2); //$result['total_inv_subsdy'] + 
        $result['class_fees'] = number_format($result['class_fees'], 2);
        $result['class_discount'] = number_format($result['class_discount'], 2);
        $result['total_inv_subsdy'] = number_format($result['total_inv_subsdy'], 2);
        $result['total_inv_amount'] = number_format($result['total_inv_amount'], 2);
        $result['total_gst'] = number_format($result['total_gst'], 2);
        $result['gst_rate'] = number_format($result['gst_rate'], 2);
        $result['class_id'] = $class_id;
        $result['user_id'] = $trainee_id;
        echo json_encode($result);
        exit;
    }

    /*
     * This function loads the initial list view page for class.
     */
    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Class';
        $tenantId = $this->tenant_id;
        $userId = $this->user->user_id;
        $courseId = $this->input->get('course_id');
        $data['courses'] = $this->coursemodel->get_active_course_list_by_tenant($tenantId);
        $data['classes'] = array();
        $data['courseDetails'] = array();
        if ($courseId) {
            $export_url = '?';
            foreach ($_GET as $k => $v) {
                $export_url .="$k=$v&";
            }
            $export_url = rtrim($export_url, '&');
            $data['export_url'] = $export_url;
            $data['sort_link'] = $sort_link = "course_id=" . $this->input->get('course_id') . "&class_id=" . $this->input->get('class_id') . "&class_status=" . $this->input->get('class_status');
            $this->load->model('settings_model', 'settingsmodel');
            $data['classes'] = $this->classmodel->get_course_class($tenantId, $courseId, '', 1);
            $data['courseRunId'] = $this->classmodel->get_course_Run_id($tenantId, $courseId, '', 1);
            $data['courseDetails'] = $coursedetails = $this->coursemodel->get_course_detailse($courseId);
            $data['coursePreReq'] = $this->coursemodel->get_pre_requisite($coursedetails->pre_requisite);
            $data['courseLang'] = $this->coursemodel->get_metadata_on_parameter_id($coursedetails->language);
            $data['courseType'] = $this->coursemodel->get_metadata_on_parameter_id($coursedetails->crse_type);
            $data['certiLevel'] = $this->coursemodel->get_metadata_on_parameter_id($coursedetails->certi_level);
            $data['courseClassType'] = $this->coursemodel->get_metadata_on_parameter_id($coursedetails->class_type);
            $gst_rates = $this->settingsmodel->get_active_gst_rates($tenantId);
            $data['GstRates'] = ($gst_rates->gst_rate != false) ? number_format($gst_rates->gst_rate, 2, '.', '') . "%" : "GST-Not Defined";
            $data['courseManager'] = $this->coursemodel->get_managers($coursedetails->crse_manager);
            $data['SalesExec'] = $this->coursemodel->get_sales_exec_detailse($courseId);
            $course_id = ($this->input->get('course_id')) ? $this->input->get('course_id') : '';
            $class_id = ($this->input->get('class_id')) ? $this->input->get('class_id') : '';
            $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'last_modified_on'; //class_id
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $totalrows = $this->classmodel->get_all_class_count_by_tenant_id($tenantId, $course_id, $class_id, $class_status);
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'classes/';
            $pageno = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;
            $offset = ($pageno * $records_per_page);
            $data['tabledata'] = $this->classmodel->list_all_class_by_tenant_id($tenantId, $records_per_page, $offset, $field, $order_by, $course_id, $class_id, $class_status);
            $data['sort_order'] = $order_by;
            $data['controllerurl'] = 'classes/';
            $this->load->helper('pagination');
            $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        }
        $data['main_content'] = 'class/classlist';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /*
     * This function modified by shubhranshu to chech with SSG system and sync the data with TMS 09.05.21
     */
    public function add_new_class() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $data['courses'] = $this->coursemodel->get_active_course_list_by_tenant($tenant_id);
        $data['trainer'] = $this->classmodel->get_tenant_users_by_role($tenant_id, 'TRAINER');
        $data['course_manager'] = $this->classmodel->get_tenant_users_by_role($tenant_id, 'CRSEMGR'); 
        $data['role'] = $this->user->role_id;
        $data['page_title'] = 'Class';
        $this->load->library('form_validation');
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('class_course', 'Class Course', 'required');
            $this->form_validation->set_rules('class_name', 'Class Name', 'max_length[50]');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            $this->form_validation->set_rules('end_date', 'End Date', 'required');
            $this->form_validation->set_rules('total_seats', 'Total Seats', 'required|max_length[10]|integer');
            $this->form_validation->set_rules('minimum_students', 'Minimum Students', 'max_length[10]|integer');
            $this->form_validation->set_rules('fees', 'Fees', 'required|numeric');
            $this->form_validation->set_rules('class_discount', 'Class Discount', 'numeric');
            $this->form_validation->set_rules('display_class', 'Check box', 'trim');
            $this->form_validation->set_rules('languages', 'Languages', 'required');
            $this->form_validation->set_rules('modeoftraining', 'modeoftraining', 'required');
            $this->form_validation->set_rules('sessions_perday', 'Radio', 'trim');
            $this->form_validation->set_rules('payment_details', 'Radio', 'trim');
            $this->form_validation->set_rules('survey_language', 'survey_language', 'required|alpha');
            $this->form_validation->set_rules('cls_venue', 'Classroom Venue', 'required');
            $this->form_validation->set_rules('control_5[]', 'Class Room Trainer', 'trim|numeric|required');///modified by shubhranshu for trainer validation
            $this->form_validation->set_rules('crs_admin_email', 'Course Admin Email', 'trim|required|valid_email|max_length[30]');
            $this->form_validation->set_rules('venue_block', 'Venue Block', 'required|max_length[30]|alpha_numeric_spaces');
            $this->form_validation->set_rules('venue_street', 'Venue Street', 'required|max_length[30]|alpha_numeric_spaces');
            $this->form_validation->set_rules('venue_building', 'Venue Building', 'required|max_length[30]|alpha_numeric_spaces');
            $this->form_validation->set_rules('venue_floor', 'Venue Floor', 'required|max_length[30]|numeric');
            $this->form_validation->set_rules('venue_unit', 'Venue Unit', 'required|max_length[30]|alpha_numeric_spaces');
            $this->form_validation->set_rules('venue_postalcode', 'Venue Postal Code', 'required|max_length[30]|numeric');
            $this->form_validation->set_rules('crse_ref_no', 'Course Reference No', 'required');
             
             
             
            if ($this->form_validation->run() == FALSE) {
                $data['display'] = 'display:block;';
                $data['main_content'] = 'class/addnewclass';
                $this->load->view('layout', $data);
            } else {
                if($this->user->role_id != 'ADMN'){
                    $start_date_timestamp = strtotime($this->input->post('start_date'));        
                    $today_date = strtotime(date('Y-m-d'));                                            
                    if($start_date_timestamp < $today_date){                       
                        $data['main_content'] = 'class/addnewclass';
                        $data['tax_error'] = "You donot have permission to create class with start date lesser than today's date. Please get in touch with your Administratot to make this change.";
                        $data['tax_error_status'] = 1;
                        $this->load->view('layout', $data); 
                        return;
                    }                                     
                }
                $tenant = $this->classTraineeModel->get_tenant_masters($tenantId);
                $tpg_response = $this->tpgModel->create_courserun_tpg($tenantId,$tenant->comp_reg_no);
                
                if($tpg_response->status == 200){
                    $tpg_course_run_id = $tpg_response->data->runs[0]->id;
                    $result = $this->classmodel->create_class($tenant_id, $user_id,$tpg_course_run_id);
                    if($result['status'] == TRUE) {
                        $ssg_data = $this->tpgModel->getCourseByRunId($tpg_course_run_id);//to get qr code details
                        $st = $this->tpgModel->updateSsgData($result['classid'],$tpg_course_run_id,$ssg_data->data->course->run);
                        //print_r($tpg_response);exit;
                        if($st == TRUE){
                           $this->session->set_flashdata("success", "Class created successfully With Course Run ID: ".$tpg_course_run_id); 
                        }else{
                            $this->session->set_flashdata("success", "Class created successfully Without Course Run ID");
                        }
                        
                        
                    } else {
                        $this->session->set_flashdata("error", "Unable to create class. Please try again later.");
                    }
                    redirect('classes?course_id=' . $this->input->post('class_course'));
                }else{
                    if($tpg_response->status == 400){
                        $this->session->set_flashdata('error',"Oops! Bad request!");
                    }elseif($tpg_response->status == 403){
                        $this->session->set_flashdata('error',"Oops! Forbidden. Authorization information is missing or invalid.");
                    }elseif($tpg_response->status == 404){
                        $this->session->set_flashdata('error',"Oops! Not Found!");
                    }elseif($tpg_response->status == 500){
                        $this->session->set_flashdata('error',"Oops! Internal Error!!");
                    }else{
                        $this->session->set_flashdata('error',"TPG is not responding. Please, check back again."); 
                    }
                    $data['error'] = $tpg_response->error->details;
                    $data['display'] = 'display:block;';
                    $data['main_content'] = 'class/addnewclass';
                    $this->load->view('layout', $data);
                }
                
                
            }
        } else {
            $data['display'] = 'display:none;';
            $data['main_content'] = 'class/addnewclass';
            $this->load->view('layout', $data);
        }
    }

    /**
     * this function to deactivate class
     */
    function deactivate_class() {
         $tenant_id = $this->tenant_id;
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            foreach ($this->input->post() as $key => $value) {
                $$key = $value;
            }

            $this->load->library('form_validation');
            $this->form_validation->set_rules('deactivation_date', 'deactivation date', 'required');
            $this->form_validation->set_rules('reason_for_deactivation', 'reason for deactivation', 'required');
            if ($reason_for_deactivation == 'OTHERS') {
                $this->form_validation->set_rules('other_reason_for_deactivation', 'other reason for deactivation', 'required');
            }
            if ($this->form_validation->run() == TRUE) {
                $res = $this->classmodel->get_class_info($class_id_deactive);
                $crs = $this->coursemodel->get_course_detailse($res[course_id]);
                //print_r($crs);exit;
                $tenant = $this->classTraineeModel->get_tenant_masters($tenant_id);
                $tpg_response = $this->tpgModel->delete_courserun_tpg($crs->reference_num,$tenant->comp_reg_no,$res[tpg_course_run_id]);///callling to delete the courserun FROM SSG system
                if($tpg_response->status == 200){
                     $result = $this->classmodel->deactivate_class($class_id_deactive);
                    if ($result == TRUE) {
                        $current_class_data = json_encode($res);
                        user_activity( 5, $class_id_deactive, $current_class_data);
                        $this->session->set_flashdata('success', 'Class has been deleted successfully With SSG');
                    } else {
                        $this->session->set_flashdata("error", "Unable to delete class. Please try again later.");
                    }
                }else{
                    if($tpg_response->status == 400){
                        $this->session->set_flashdata('error',"Oops! Bad request!");
                    }elseif($tpg_response->status == 403){
                        $this->session->set_flashdata('error',"Oops! Forbidden. Authorization information is missing or invalid.");
                    }elseif($tpg_response->status == 404){
                        $this->session->set_flashdata('error',"Oops! Not Found!");
                    }elseif($tpg_response->status == 500){
                        $this->session->set_flashdata('error',"Oops! Internal Error!!");
                    }else{
                        $this->session->set_flashdata('error',"TPG is not responding. Please, check back again."); 
                    }
                     $this->session->set_flashdata('resp_error',$tpg_response->error->details);
                }
                redirect('classes');
            }
        }
    }
/* end class*/
    public function end_class(){
        $tenant_id=$this->tenant_id;
        $class_id=$this->input->get('end_class');
        if($class_id){
        $result= $this->classmodel->end_class($tenant_id,$class_id);
        $this->session->set_flashdata("success", "Class Status is Changed.");
           
       }
        redirect("class_trainee");
    }
    /*
     * This function loads the Edit class form.
     */
   public function edit_class($tax_error ="") {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            extract($_POST);
            if ($this->classmodel->get_class_status($class_id) == 'Inactive') {
                $this->session->set_flashdata("error", "You cannot Edit/Deactivate an 'INACTIVE' class.");
                return redirect("classes");
            }else if ($this->classmodel->get_class_status($class_id) == 'DELETED') {
                $this->session->set_flashdata("error", "You cannot Edit a 'Deleted' class.");
                return redirect("classes");
            } else {
                $data['classid'] = $class_id;
                $data['coursename'] = $this->coursemodel->course_name($course_id);
                $data['classes'] = $this->classmodel->get_course_class_for_edit($tenant_id, $course_id);
                $languages_arr = $this->classmodel->get_course_language($tenant_id, $course_id);
                $course_salesexec = $this->classmodel->get_course_salesexec($tenant_id, $course_id);
                $salesexec_arr = array();
                foreach ($course_salesexec as $val) {
                    $name = $val->first_name . ' ' . $val->last_name;
                    $salesexec_arr[$val->user_id] = $name;
                }
                $data['languages'] = $languages_arr;
                $data['salesexec'] = $salesexec_arr;
                $trainee_enrolled = $this->classmodel->traineeenrolled_class($tenant_id, $class_id);
                $data['trainee_enrolled'] = $trainee_enrolled;
                $data['trainer'] = $this->classmodel->get_tenant_users_by_role($tenant_id, 'TRAINER');
                $data['course_manager'] = $this->classmodel->get_tenant_users_by_role($tenant_id, 'CRSEMGR'); 
                $data['class'] = $class = $this->classmodel->get_class_details_assmnts($tenant_id, $class_id);
                $totalbooked = $this->classmodel->get_class_booked($course_id, $class_id, $tenant_id);
                $cur_date = strtotime(date('Y-m-d'));
                if ($totalbooked == 0) {
                    $label = array('start' => '', 'fees' => '', 'language' => '', 'discount' => '', 'deactivate' => '');
                } else {
                    if (strtotime(date('Y-m-d', strtotime($class->class_start_datetime))) > $cur_date && strtotime(date('Y-m-d', strtotime($class->class_end_datetime))) > $cur_date) {
                        $label = array('start' => '', 'fees' => 'label', 'language' => 'label', 'discount' => 'label', 'deactivate' => 'label');
                    } else if (strtotime(date('Y-m-d', strtotime($class->class_start_datetime))) <= $cur_date && strtotime(date('Y-m-d', strtotime($class->class_end_datetime))) >= $cur_date) {
                        $label = array('start' => 'label', 'fees' => 'label', 'language' => 'label', 'discount' => 'label', 'deactivate' => 'label');
                    } else if(strtotime(date('Y-m-d', strtotime($class->class_start_datetime))) < $cur_date && strtotime(date('Y-m-d', strtotime($class->class_end_datetime))) <$cur_date){
                        $label = array('fees' => 'label', 'discount' => 'label');
                        $data['js_class_status']='completed';
                    }
                    if($this->user->role_id == 'ADMN'){
                        $label['fees'] = '';
                    }
                }
                $data['label'] = $label;
                $data['course_duration'] = $this->coursemodel->get_course_detailse($course_id)->crse_duration;
                $data['class_schedule'] = $this->classmodel->get_all_class_schedule($tenant_id, $class_id);
                $def_assmnt = $this->classmodel->get_def_assessment($tenant_id, $class_id, $class->assmnt_type);
                if ($class->assmnt_type == 'DEFAULT') {
                    $data['DefAssLoc'] = ($def_assmnt->assmnt_venue == 'OTH') ? 'Others (' . $def_assmnt->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($def_assmnt->assmnt_venue);
                    $data['DefAssId'] = $this->classmodel->get_trainer_names($def_assmnt->assessor_id);
                    $cdef_assmnt = $def_assmnt;
                } else {
                    foreach ($def_assmnt as $k => $row) {
                        $def_assmnt[$k]->DefAssLoc = ($row->assmnt_venue == 'OTH') ? 'Others (' . $row->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($row->assmnt_venue);
                        $def_assmnt[$k]->DefAssId = $this->classmodel->get_trainer_names($row->assessor_id);
                    }
                    $assmnt = array();
                    foreach ($def_assmnt as $row) {
                        $assmnt[$row->assmnt_id]['DefAssLoc'] = ($row->assmnt_venue == 'OTH') ? 'Others (' . $row->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($row->assmnt_venue);
                        $assmnt[$row->assmnt_id]['DefAssId'] = $this->classmodel->get_trainer_names($row->assessor_id);
                        $assmnt[$row->assmnt_id]['assessor_id'] = $row->assessor_id;
                        $assmnt[$row->assmnt_id]['assmnt_id'] = $row->assmnt_id;
                        $assmnt[$row->assmnt_id]['assmnt_date'] = $row->assmnt_date;
                        $assmnt[$row->assmnt_id]['assmnt_venue'] = $row->assmnt_venue;
                        $assmnt[$row->assmnt_id]['assmnt_venue_oth'] = $row->assmnt_venue_oth;
                        $assmnt[$row->assmnt_id]['assmnt_date'] = $row->assmnt_date;
                        $assmnt[$row->assmnt_id]['trainee'][] = $row->first_name . ' ' . $row->last_name;
                        $assmnt[$row->assmnt_id]['trainee_id'][] = $row->user_id;
                        $assmnt[$row->assmnt_id]['assmnt_start_time'] = $row->assmnt_start_time;
                        $assmnt[$row->assmnt_id]['assmnt_end_time'] = $row->assmnt_end_time;
                    }
                    $cdef_assmnt = $assmnt;
                }
                $data['def_assessment'] = $cdef_assmnt;
            }
            
        }
       
        $data['tax_error'] = $tax_error;
        $data['page_title'] = 'Class';
        $data['main_content'] = 'class/editclass';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /*
     * This function loads the View class form.
     */
    public function view_class($class_id) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if (empty($class_id)) {
            return show_404();
        }
        $tenant_id = $this->tenant_id;
        $user_id = $this->user->user_id;
        $data['class'] = $class = $this->classmodel->get_class_details_assmnts($tenant_id, $class_id);
        $coursedetails = $this->coursemodel->get_course_detailse($class->course_id);
        $status = $this->classmodel->get_class_status($class_id, '');
        if ($status == 'Inactive') {
            $data['deactivate_reason'] = ($class->deacti_reason != 'OTHERS') ? $this->coursemodel->get_metadata_on_parameter_id($class->deacti_reason) : 'Others (' . $class->deacti_reason_oth . ')';
        }
        if (!empty($class->class_copied_from)) {
            $copied_user_details = $this->classmodel->get_user_details($tenant_id, $class->copied_by);
            $data['copied_user'] = $copied_user_details->first_name . ' ' . $copied_user_details->last_name;
            $data['copy_reason'] = ($class->copied_reason != 'OTHERS') ? $this->coursemodel->get_metadata_on_parameter_id($class->copied_reason) : 'Others (' . $class->copied_reason_oth . ')';
        }
        if ($status == 'Yet to Start')
            $status_label = '<font color="green">Yet to Start</font>';
        elseif ($status == 'Inactive')
            $status_label = '<font color="red">Inactive</font>';
        else if ($status == 'Completed')
            $status_label = '<font color="red">Completed</font>';
        else if ($status == 'In-Progress')
            $status_label = '<font color="blue">In-Progress</font>';
        else
            $status_label = 'Unknown';
        $data['class_status'] = $status_label;
        $data['ClassPay'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_pymnt_enrol);
        $data['ClassLang'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_language);
        $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
        $data['LabLoc'] = $this->get_classroom_location($class->lab_location, $class->lab_venue_oth);
        $data['ClassTrainer'] = $this->classmodel->get_trainer_names($class->classroom_trainer);
        $data['LabTrainer'] = $this->classmodel->get_trainer_names($class->lab_trainer);
        $data['Assessor'] = $this->classmodel->get_trainer_names($class->assessor);
        $data['TrainingAide'] = $this->classmodel->get_course_manager_names($class->training_aide);
        $data['SalesExec'] = $this->classmodel->get_class_salesexec($tenant_id, $class->course_id, $class->sales_executive);
        $data['class_schedule'] = $this->classmodel->get_all_class_schedule($tenant_id, $class_id);
        $def_assmnt = $this->classmodel->get_def_assessment_new($tenant_id, $class_id, $class->assmnt_type);
        if ($class->assmnt_type == 'DEFAULT') {
            $data['DefAssLoc'] = ($def_assmnt->assmnt_venue == 'OTH') ? 'Others (' . $def_assmnt->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($def_assmnt->assmnt_venue);
            $data['DefAssId'] = $this->classmodel->get_trainer_names($def_assmnt->assessor_id);
            $cdef_assmnt = $def_assmnt;
        } else {
            foreach ($def_assmnt as $k => $row) {
                $def_assmnt[$k]->DefAssLoc = ($row->assmnt_venue == 'OTH') ? 'Others (' . $row->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($row->assmnt_venue);
                $def_assmnt[$k]->DefAssId = $this->classmodel->get_trainer_names($row->assessor_id);
            }
            $assmnt = array();
            foreach ($def_assmnt as $row) {
                $assmnt[$row->assmnt_id]['DefAssLoc'] = ($row->assmnt_venue == 'OTH') ? 'Others (' . $row->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($row->assmnt_venue);
                $assmnt[$row->assmnt_id]['DefAssId'] = $this->classmodel->get_trainer_names($row->assessor_id);
                $assmnt[$row->assmnt_id]['assessor_id'] = $row->assessor_id;
                $assmnt[$row->assmnt_id]['assmnt_id'] = $row->assmnt_id;
                $assmnt[$row->assmnt_id]['assmnt_date'] = $row->assmnt_date;
                $assmnt[$row->assmnt_id]['assmnt_venue'] = $row->assmnt_venue;
                $assmnt[$row->assmnt_id]['assmnt_venue_oth'] = $row->assmnt_venue_oth;
                $assmnt[$row->assmnt_id]['assmnt_date'] = $row->assmnt_date;
                $assmnt[$row->assmnt_id]['trainee'][] = $row->first_name . ' ' . $row->last_name;
                $assmnt[$row->assmnt_id]['trainee_id'][] = $row->user_id;
                $assmnt[$row->assmnt_id]['assmnt_start_time'] = $row->assmnt_start_time;
                $assmnt[$row->assmnt_id]['assmnt_end_time'] = $row->assmnt_end_time;
            }
            $cdef_assmnt = $assmnt;
        }
        $data['def_assessment'] = $cdef_assmnt;
        $data['page_title'] = 'Class';
        $data['classid'] = $class_id;
        $data['main_content'] = 'class/viewclass';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /**
     * this function to update the edited class
     */
    public function update_class() { 
        $data['sideMenuData'] = fetch_non_main_page_content();// added by shubhranshu
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $role_id = $this->session->userdata('userDetails')->role_id;// added by shubhranshu for fetching role id
        $this->load->library('form_validation');
        $this->form_validation->set_rules('class_name', 'Class Name', 'max_length[50]');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('total_seats', 'Total Seats', 'required|max_length[10]|integer');
        $this->form_validation->set_rules('minimum_students', 'Minimum Students', 'max_length[10]|integer');
        $this->form_validation->set_rules('fees', 'Fees', 'required|numeric');
        $this->form_validation->set_rules('class_discount', 'Class Discount', 'numeric');
        $this->form_validation->set_rules('display_class', 'Check box', 'trim');
        $this->form_validation->set_rules('languages', 'Languages', 'required');
        $this->form_validation->set_rules('sessions_perday', 'Radio', 'trim');
        $this->form_validation->set_rules('tpg_course_run_id', 'TPG Course Run ID', 'trim|numeric');
        $this->form_validation->set_rules('payment_details', 'Radio', 'trim');
        $this->form_validation->set_rules('cls_venue', 'Classroom Venue', 'required');
        $this->form_validation->set_rules('control_5[]', 'Class Room Trainer', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['page_title'] = 'Class';
            $data['main_content'] = 'class/editclass';
            //$data['sideMenuData'] = $this->sideMenu;
            $data['sideMenuData'] = fetch_non_main_page_content();
            $this->load->view('layout', $data);
            
        } else {
            if($role_id != 'ADMN'){ // changed by shubhranshu for userdata issue
                $start_date_timestamp = strtotime($this->input->post('start_date'));        
                $start_date_timestamp_hidden = strtotime($this->input->post('start_date_hidden'));        
                $today_date = strtotime(date('Y-m-d'));                                            
                if($start_date_timestamp < $today_date && $start_date_timestamp != $start_date_timestamp_hidden){
                    $tax_error = "You donot have permission to update class with start date lesser than today's date. Please get in touch with your Administrator to make this change.";                    
                    $this->edit_class($tax_error);
                    return;
                }                                     
            }   
            
            $class_id = $this->input->post('class_hid');
            $result = $this->classmodel->get_class_info($class_id);          
            $previous_data = json_encode($result);
            
            $result = $this->classmodel->update_class($tenant_id, $user_id);
            if ($result == TRUE) {
                 user_activity(5,$class_id,$previous_data);
                $this->session->set_flashdata("success", "Class updated successfully.");
            } else {
                $this->session->set_flashdata("error", "Unable to update class. Please try again later.");
            }
        }
        
        redirect("classes?course_id=" . $this->input->post('course_id'));
    }

    /**
     * function to export booked seats
     */
    public function export_booked_seats($id) {
        $tenant_id = $this->tenant_id;
        $o = $this->input->get('o');
        $f = $this->input->get('f');
        $result = $this->classmodel->export_all_booked_seats($tenant_id, $id, $o, $f);
        if ($result) {
            $class = $this->classmodel->get_class_by_classid($tenant_id, $id);
            $course = $this->coursemodel->get_course_detailse($class['course_id']);
            $totalbooked = $this->classmodel->get_class_booked($class['course_id'], $id, $tenant_id);
            $this->load->helper('export_helper');
            export_booked_seats($result, $class, $course, $totalbooked, $tenant_id);
        } else {
            $this->session->set_flashdata("error", "There are no data to export in XLS");
            redirect('classes');
        }
    }

    /**
     * function to display booked seats
     */
    public function seats_booked($id) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if (empty($id)) {
            return show_404();
        }
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;

        $data['class'] = $class = $this->classmodel->get_class_by_classid($tenant_id, $id);
        $data['course'] = $course = $this->coursemodel->get_course_detailse($class['course_id']);
        $data['total_booked'] = $totalrows = $this->classmodel->get_class_booked_count($class['course_id'], $id, $tenant_id);
        if ($this->user->role_id == 'SLEXEC') {
            $data['sales_total_booked'] = $totalrows = $this->classmodel->get_class_booked_by_salesexec($class['course_id'], $id, $tenant_id);
        }
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'class_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'classes/seats_booked/' . $id . '/';
        $pageno = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;
        $offset = ($pageno * $records_per_page);
        $data['tabledata'] = $this->classmodel->list_all_booked_seats($tenant_id, $records_per_page, $offset, $field, $order_by, $id);
        $data['sort_order'] = $order_by;
        $data['sort_field'] = $field;
        $data['controllerurl'] = 'classes/seats_booked/' . $id . '/';
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);

        $data['tenant_id'] = $tenant_id;
        $data['page_title'] = 'Booked Seats';
        $data['main_content'] = 'class/bookedseats';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /**
     * function to generate class PDF
     */
    public function print_class($class_id) {
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $data['class'] = $class = $this->classmodel->get_class_details_assmnts($tenant_id, $class_id);
        $data['course_name'] = $this->coursemodel->get_course_detailse($class->course_id);
        $this->load->model('class_trainee_model', 'classtraineemodel');
        $data['tenant_details'] = $this->classtraineemodel->get_tenant_masters($tenant_id);
        $data['tenant_details']->tenant_state = rtrim($this->coursemodel->get_metadata_on_parameter_id($data['tenant_details']->tenant_state), ', ');
        $data['tenant_details']->tenant_country = rtrim($this->coursemodel->get_metadata_on_parameter_id($data['tenant_details']->tenant_country), ', ');
        $status = $this->classmodel->get_class_status($class_id, '');
        if ($status == 'Yet to Start')
            $status_label = '<font color="green">Yet to Start</font>';
        elseif ($status == 'Inactive')
            $status_label = '<font color="blue">Inactive</font>';
        else if ($status == 'Completed')
            $status_label = '<font color="red">Completed</font>';
        else
            $status_label = '<font color="blue">In-Progress</font>';
        $data['class_status'] = $status_label;
        $data['ClassPay'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_pymnt_enrol);
        $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
        $data['LabLoc'] = $this->get_classroom_location($class->lab_location, $class->lab_venue_oth);
        $data['ClassTrainer'] = $this->classmodel->get_trainer_names($class->classroom_trainer);
        $data['LabTrainer'] = $this->classmodel->get_trainer_names($class->lab_trainer);
        $data['Assessor'] = $this->classmodel->get_trainer_names($class->assessor);
        $data['TrainingAide'] = $this->classmodel->get_course_manager_names($class->training_aide);
        $data['SalesExec'] = $this->classmodel->get_class_salesexec($tenant_id, $class->course_id, $class->sales_executive);
        $data['class_schedule'] = $this->classmodel->get_all_class_schedule($tenant_id, $class_id);
        $def_assmnt = $this->classmodel->get_def_assessment($tenant_id, $class_id, $class->assmnt_type);
        if ($class->assmnt_type == 'DEFAULT') {
            $def_assmnt->DefAssLoc = ($def_assmnt->assmnt_venue == 'OTH') ? 'Others (' . $def_assmnt->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($def_assmnt->assmnt_venue);
            $def_assmnt->DefAssId = $this->classmodel->get_trainer_names($def_assmnt->assessor_id);
            $cdef_assmnt = $def_assmnt;
        } else {
            foreach ($def_assmnt as $k => $row) {
                $def_assmnt[$k]->DefAssLoc = ($row->assmnt_venue == 'OTH') ? 'Others (' . $row->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($row->assmnt_venue);
                $def_assmnt[$k]->DefAssId = $this->classmodel->get_trainer_names($row->assessor_id);
            }
            $assmnt = array();
            foreach ($def_assmnt as $row) {
                $assmnt[$row->assmnt_id]['DefAssLoc'] = ($row->assmnt_venue == 'OTH') ? 'Others (' . $row->assmnt_venue_oth . ')' : $this->coursemodel->get_metadata_on_parameter_id($row->assmnt_venue);
                $assmnt[$row->assmnt_id]['DefAssId'] = $this->classmodel->get_trainer_names($row->assessor_id);
                $assmnt[$row->assmnt_id]['assessor_id'] = $row->assessor_id;
                $assmnt[$row->assmnt_id]['assmnt_id'] = $row->assmnt_id;
                $assmnt[$row->assmnt_id]['assmnt_date'] = $row->assmnt_date;
                $assmnt[$row->assmnt_id]['assmnt_venue'] = $row->assmnt_venue;
                $assmnt[$row->assmnt_id]['assmnt_venue_oth'] = $row->assmnt_venue_oth;
                $assmnt[$row->assmnt_id]['assmnt_date'] = $row->assmnt_date;
                $assmnt[$row->assmnt_id]['trainee'][] = $row->first_name . ' ' . $row->last_name;
                $assmnt[$row->assmnt_id]['trainee_id'][] = $row->user_id;
                $assmnt[$row->assmnt_id]['assmnt_start_time'] = $row->assmnt_start_time;
                $assmnt[$row->assmnt_id]['assmnt_end_time'] = $row->assmnt_end_time;
            }
            $cdef_assmnt = $assmnt;
        }
        $def_ass_data = $cdef_assmnt;
        if (empty($data['class'])) {
            $this->session->set_flashdata("error", "No Class data to print as pdf.");
            redirect('classes');
        } else {
            $this->load->helper('pdf_reports_helper');
            generate_class($data, $def_ass_data);
        }
    }

    /**
     * function to generate pdf for sales executive commission paid
     */
    public function export_salesexecutive_commission($sales_id) {
        $tenantId = $this->tenant_id;
        $userId = $this->session->userdata('userDetails')->user_id;
        $result = $this->classmodel->get_commission_payment($tenantId, $sales_id);
        $sales_name = $this->classmodel->get_sales_exec_name($tenantId, $sales_id);
        if (empty($result)) {
            $this->session->set_flashdata("error", "No Commission data to print as pdf.");
            redirect('classes');
        } else {
            $this->load->model('class_trainee_model', 'classtrainee');
            $this->load->model('Class_Trainee_Model', 'classtraineemodel');
            $tenant = $this->classtraineemodel->get_tenant_masters($this->tenant_id);
            $tenant->tenant_state = rtrim($this->coursemodel->get_metadata_on_parameter_id($tenant->tenant_state), ', ');
            $tenant->tenant_country = rtrim($this->coursemodel->get_metadata_on_parameter_id($tenant->tenant_country), ', ');
            $this->load->helper('pdf_reports_helper');
            generate_sales_payment_received($result, $sales_name, $tenant);
        }
    }

    /**
     * function to get payment recieved PDF
     */
    public function generate_pdf_payment_recieved() {
        $tenantId = $this->tenant_id;
        $userId = $this->session->userdata('userDetails')->user_id;
        $trainee_id = $this->input->get('user_id');
        $class_id = $this->input->get('class_id');
        $result = $this->classmodel->get_payment_received($tenantId, $class_id, $trainee_id);
        $result['recd_on'] = date('F d Y, l', strtotime($result['recd_on']));
        $result['mode_of_pymnt'] = rtrim($this->coursemodel->get_metadata_on_parameter_id($result['mode_of_pymnt']), ', ');
        $result['total_inv_discnt'] = number_format(($result['class_fees'] / 100) * $result['class_discount'], 2);
        $result['amount_recd'] = number_format(($result['total_gst'] + $result['total_inv_amount']), 2); //$result['total_inv_subsdy'] + 
        $result['class_fees'] = number_format($result['class_fees'], 2);
        $result['class_discount'] = number_format($result['class_discount'], 2);
        $result['total_inv_subsdy'] = number_format($result['total_inv_subsdy'], 2);
        $result['total_inv_amount'] = number_format($result['total_inv_amount'], 2);
        $result['total_gst'] = number_format($result['total_gst'], 2);
        $result['gst_rate'] = number_format($result['gst_rate'], 2);
        $this->load->helper('pdf_reports_helper');
        generate_report_payment_received($result);
    }

    /**
     * this function to get post values and copy the class
     */
    
    public function find_date_diff($date1,$date2){
        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        return $days;
    }
    public function copy_classes() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        extract($_POST);
        if (!empty($class_hid)) {    
            if($this->user->role_id != 'ADMN'){
                $start_date_timestamp = date('Y-m-d', strtotime($this->input->post('start_date')));        
                $today_date = strtotime(date('Y-m-d'));                                            
                if($start_date_timestamp < $today_date){                       
                    $tax_error = "You donot have permission to create class with start date lesser than today's date. Please get in touch with your Administratot to make this change.";
                    $this->copy_class($tax_error); 
                    return;
                }                                     
            } 
            
            ///////////////////////////////

                $data['class'] = $class = $this->classmodel->get_class_details_assmnts($tenant_id, $class_hid);
                $data['course']=$coursedetails = $this->coursemodel->get_course_detailse($class->course_id);
               print_r($data['class']);exit;
                $start_date = date("Y-m-d", strtotime($this->input->post('start_date')));
                $end_date = date("Y-m-d", strtotime($this->input->post('end_date')));

                $old_class_date_diff = $this->find_date_diff($date1,$date2);
                $new_class_date_diff = $this->find_date_diff(date("Y-m-d", strtotime($data['class']->class_start_datetime)),date("Y-m-d", strtotime($data['class']->class_end_datetime)));
                echo $old_class_date_diff.'--'.$new_class_date_diff;exit;
                if($old_class_date_diff != $new_class_date_diff){
                    $this->session->set_flashdata("error", "Unable to Copy! Since Class Date Mismatched!. Enter the correct Date Difference.");
                    redirect('classes?course_id=' . $data['course']->course_id);
                }
               
                $status = $this->classmodel->get_class_status($class_hid, '');
                if ($status == 'Inactive') {
                    $data['deactivate_reason'] = ($class->deacti_reason != 'OTHERS') ? $this->coursemodel->get_metadata_on_parameter_id($class->deacti_reason) : 'Others (' . $class->deacti_reason_oth . ')';
                }
                if (!empty($class->class_copied_from)) {
                    $copied_user_details = $this->classmodel->get_user_details($tenant_id, $class->copied_by);
                    $data['copied_user'] = $copied_user_details->first_name . ' ' . $copied_user_details->last_name;
                    $data['copy_reason'] = ($class->copied_reason != 'OTHERS') ? $this->coursemodel->get_metadata_on_parameter_id($class->copied_reason) : 'Others (' . $class->copied_reason_oth . ')';
                }
                if ($status == 'Yet to Start')
                    $status_label = '<font color="green">Yet to Start</font>';
                elseif ($status == 'Inactive')
                    $status_label = '<font color="red">Inactive</font>';
                else if ($status == 'Completed')
                    $status_label = '<font color="red">Completed</font>';
                else if ($status == 'In-Progress')
                    $status_label = '<font color="blue">In-Progress</font>';
                else
                    $status_label = 'Unknown';
                $data['class_status'] = $status_label;
                $data['ClassPay'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_pymnt_enrol);
                $data['ClassLang'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_language);
                $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
                $data['LabLoc'] = $this->get_classroom_location($class->lab_location, $class->lab_venue_oth);
                $data['ClassTrainer'] = $this->classmodel->get_trainer_names($class->classroom_trainer);
                $data['LabTrainer'] = $this->classmodel->get_trainer_names($class->lab_trainer);
                $data['Assessor'] = $this->classmodel->get_trainer_names($class->assessor);
                $data['TrainingAide'] = $this->classmodel->get_course_manager_names($class->training_aide);
                $data['SalesExec'] = $this->classmodel->get_class_salesexec($tenant_id, $class->course_id, $class->sales_executive);
                $data['class_schedule'] = $this->classmodel->get_all_class_schedules($tenant_id, $class_hid);
                $def_assmnt = $this->classmodel->get_def_assessment_new($tenant_id, $class_hid, $class->assmnt_type);
                
                $data['def_assessment'] = $def_assmnt;
                $data['page_title'] = 'Class';
                $data['classid'] = $class_hid;
               
            
            
                $tenant = $this->classTraineeModel->get_tenant_masters($tenant_id);
                $tpg_response = $this->tpgModel->create_copy_courserun_tpg($tenant_id,$tenant->comp_reg_no,$data);
                if($tpg_response->status == 200){
                    $tpg_course_run_id = $tpg_response->data->runs[0]->id;
                    $result = $this->classmodel->copy_classes($tenant_id, $data['course']->crse_name, $user_id,$data,$tpg_course_run_id);
                    if($result['status'] == TRUE) {
                        $ssg_data = $this->tpgModel->getCourseByRunId($tpg_course_run_id);//to get qr code details
                        $st = $this->tpgModel->updateSsgData($result['classid'],$tpg_course_run_id,$ssg_data->data->course->run);
                        $this->session->set_flashdata("success", "Class Copied successfully With Course Run ID: ".$tpg_course_run_id); 
                    } else {
                        $this->session->set_flashdata("error", "Unable to copy class. Please try again later.");
                    }
                    redirect('classes?course_id=' . $data['course']->course_id);
                }else{
                    if($tpg_response->status == 400){
                        $this->session->set_flashdata('error',"Oops! Bad request!");
                    }elseif($tpg_response->status == 403){
                        $this->session->set_flashdata('error',"Oops! Forbidden. Authorization information is missing or invalid.");
                    }elseif($tpg_response->status == 404){
                        $this->session->set_flashdata('error',"Oops! Not Found!");
                    }elseif($tpg_response->status == 500){
                        $this->session->set_flashdata('error',"Oops! Internal Error!!");
                    }else{
                        $this->session->set_flashdata('error',"TPG is not responding. Please, check back again."); 
                    }
                    $this->session->set_flashdata('resp_error',$tpg_response->error->details);
                    
                }
                
            ///////////////////////////////////        
  
        }
        redirect("classes?course_id=" . $data['course']->course_id);
    }

    /*
     * This function copy's one class to another course.
     */
    public function copy_class($tax_error = "") {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            extract($_POST);
            $data['classes'] = $this->classmodel->get_course_class($tenant_id, $course_id, '', 1);
            $data['class'] = $class = $this->classmodel->get_class_details($tenant_id, $class_id);
            $data['ClassPay'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_pymnt_enrol);
            $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
            $data['LabLoc'] = $this->get_classroom_location($class->lab_location, $class->lab_venue_oth);
            $data['ClassTrainer'] = $this->classmodel->get_trainer_names($class->classroom_trainer);
            $data['ClassLang'] = $this->coursemodel->get_metadata_on_parameter_id($class->class_language);
            $data['LabTrainer'] = $this->classmodel->get_trainer_names($class->lab_trainer);
            $data['Assessor'] = $this->classmodel->get_trainer_names($class->assessor);
            $data['TrainingAide'] = $this->classmodel->get_course_manager_names($class->training_aide);
            $data['SalesExec'] = $this->classmodel->get_class_salesexec($tenant_id, $class->course_id, $class->sales_executive);
            $data['DefAssLoc'] = $this->coursemodel->get_metadata_on_parameter_id($def_assmnt->assmnt_venue);
            $data['DefAssId'] = $this->classmodel->get_trainer_names($def_assmnt->assessor_id);
        }
        $data['tax_error'] = $tax_error;
        $data['page_title'] = 'Class';
        $data['main_content'] = 'class/copyclass';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /**
     * function to post all the update commission values
     */
    public function updates_commission() {
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('payment_type', 'Payment Type', 'required');
            $payment_type = $this->input->get('payment_type');
            if ($payment_type == 'CHQ') {
                $this->form_validation->set_rules('paid_on', 'Paid on', 'required');
                $this->form_validation->set_rules('cheque_number', 'Cheque number', 'required');
                $this->form_validation->set_rules('cheque_amount', 'Cheque amount', 'required');
                $this->form_validation->set_rules('cheque_date', 'Cheque date', 'required');
                $this->form_validation->set_rules('bank_name', 'Bank name', 'required');
            } else if ($payment_type == 'CASH') {
                $this->form_validation->set_rules('cashpaid_on', ' Paid on', 'required');
                $this->form_validation->set_rules('cash_amount', 'Cash Amount', 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $data['main_content'] = 'accounting/update_commission';
                //$data['sideMenuData'] = $this->sideMenu;
                $data['sideMenuData'] = fetch_non_main_page_content();
                $this->load->view('layout', $data);
            } else {
                $result = $this->classmodel->update_commission_post($tenant_id, $user_id);
                if ($result == TRUE) {
                    $this->session->set_flashdata("success", "Sales executive commission updated successfully.");
                } else {
                    $this->session->set_flashdata("error", "Unable to update sales executive commission. Please try again later.");
                }
                redirect('accounting/update_commission');
            }
        }
    }

    /**
     * this function to get courses autocomplete json
     */
    public function get_courses_json() {
        $tenantId = $this->tenant_id;
        $q = trim($this->input->post('q'));
        $result = $this->classmodel->get_course_autocomplete($tenantId, $q);
        echo json_encode($result);
        exit;
    }

    /**
     * function to get class enroll count for a class
     */
    public function get_class_enroll_count() {
        $class_id = $this->input->post('class_id');
        $result = $this->classmodel->get_class_enroll_count($class_id);
        echo $result;
        exit();
    }

    /**
     * this function to get course classes on change json
     */
    public function get_course_classes_edit_json() {
        $tenantId = $this->tenant_id;
        $courseId = $this->input->post('course_id');
        $course_classes = $this->classmodel->get_course_class_for_edit($tenantId, $courseId);
        $classes_arr = array();
        foreach ($course_classes as $k => $v) {
            $classes_arr[] = array('key' => $k, 'value' => $v);
        }
        echo json_encode($classes_arr);
        exit;
    }

    /**
     * this function to get course classes on change json
     */
    public function get_course_classes_json() {
        
        $tenantId = $this->tenant_id;
        $courseId = $this->input->post('course_id');
        $mark_attendance = $this->input->post('mark_attendance');
        $course_classes = $this->classmodel->get_course_class($tenantId, $courseId, $mark_attendance,"ACTIVE","classTrainee");
        $classes_arr = array();
        foreach ($course_classes as $k => $v) {
            $classes_arr[] = array('key' => $k, 'value' => $v);
        }
        echo json_encode($classes_arr);
        exit;
    }

    /**
     * this function to get course classes on change json
     */
    public function get_course_copy_classes_json() {
        $tenantId = $this->tenant_id;
        $courseId = $this->input->post('course_id');
        $mark_attendance = $this->input->post('mark_attendance');
        $course_classes = $this->classmodel->get_course_class($tenantId, $courseId, '', 1);
        $classes_arr = array();
        foreach ($course_classes as $k => $v) {
            $classes_arr[] = array('key' => $k, 'value' => $v);
        }
        echo json_encode($classes_arr);
        exit;
    }

    /**
     * this is to get all the JSON data (sales_executive, languages, course_duration) for add class page Onchange
     */
    public function get_course_related_json() {
        $tenantId = $this->tenant_id;
        $courseId = $this->input->post('course_id');
        $all_data_arr = array();
        $course_languages = $this->classmodel->get_course_language($tenantId, $courseId);
        $languages_arr = array();
        foreach ($course_languages as $k => $v) {
            $languages_arr[] = array('key' => $k, 'value' => $v);
        }
        $course_salesexec = $this->classmodel->get_course_salesexec($tenantId, $courseId);
        $salesexec_arr = array();
        foreach ($course_salesexec as $val) {
            $name = $val->first_name . ' ' . $val->last_name;
            $salesexec_arr[] = array('key' => $val->user_id, 'value' => $name);
        }
        $course_details = $this->coursemodel->get_course_detailse($courseId);        
        $course_duration = $course_details->crse_duration;        
        $course_manager = $course_details->crse_manager;    
        $all_data_arr['crse_admin_email'] = $course_details->crse_admin_email;  
        $all_data_arr['crse_ref_no'] = $course_details->reference_num; 
        $all_data_arr['languages'] = $languages_arr;
        $all_data_arr['salesexec'] = $salesexec_arr;
        $all_data_arr['course_duration'] = $course_duration; 
        $all_data_arr['course_manager'] = explode(",",$course_manager);
        echo json_encode($all_data_arr);
        exit;
    }

    /**
     * this function to generate xls for class page fields
     */
    public function export_class_page() {
        $tenant_id = $this->tenant_id;
        $result = $this->classmodel->get_class_list_export($tenant_id);
        $this->load->helper('export_helper');
        export_class_page($result);
    }

    /**
     * this function to generate xls for class page fields
     */
    public function export_class_full() {
        $tenant_id = $this->tenant_id;
        $result = $this->classmodel->get_class_list_export($tenant_id);
        $this->load->helper('export_helper');
        export_class_full($result);
    }

    /**
     * function to get classroom location for others
     */
    public function get_classroom_location($venue, $other) {
        if ($venue == 'OTH') {
            return 'Others (' . $other . ')';
        } else {
            return $this->coursemodel->get_metadata_on_parameter_id($venue);
        }
    }

    ///added by shubhranshu to fetch the latest ssg sessions
    public function get_ssg_session(){
        $tenant_id = $this->tenant_id;
        $CourseRunId = $this->input->post('crs_run_id');
        $classId = $this->input->post('class_id');
        $tenant = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $class_details = $this->classmodel->get_class_details($tenant_id,$classId);
        $crse_details=$this->coursemodel->get_course_detailse($class_details->course_id);
        $tpg_response = $this->tpgModel->fetch_ssg_session($tenant_id,$CourseRunId,$classId,$tenant->comp_reg_no,$crse_details->crse_ref_no);
        echo $tpg_response;
    }
    ///added by shubhranshu to fetch all trainee asseessments
    public function tpg_assessments(){
        $tenant_id = $this->tenant_id;
        $data['page_title'] = 'TPG Assessments';
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['courses'] = $this->coursemodel->get_active_course_list_by_tenant($tenant_id, 'classTrainee');
        $course = $this->input->get('course');
        $class = $this->input->get('class');
        $userid = $this->input->get('nric');
        $searchAssessment = $this->input->get('searchAssessment');
        if($searchAssessment == 'tpg'){
            $tenant = $this->classTraineeModel->get_tenant_masters($tenant_id);
            $class_details = $this->classmodel->get_class_details($tenant_id,$class);
            $crse_details=$this->coursemodel->get_course_detailse($class_details->course_id);
            $data['classes'] = $this->classmodel->get_course_class($tenant_id, $course, $mark_attendance,"ACTIVE","classTrainee");
            $data['tabledata_tpg'] = $this->tpgModel->search_assessments($tenant_id,$tenant->comp_reg_no,$crse_details->crse_ref_no,$class_details->tpg_course_run_id);
            //print_r($data['tabledata_tpg']);exit;
        }else{
            if (!empty($course) && !empty($class)) {
                $data['tabledata']= $this->getTraineeForAssessments($course,$class,$userid);
                $data['classes'] = $this->classmodel->get_course_class($tenant_id, $course, $mark_attendance,"ACTIVE","classTrainee");
                $data['nric'] = $this->classmodel->get_Trainee_For_Assessments_json($tenant_id,$course,$class);

            }
        }
        $data['main_content'] = 'class/tpg_assessments';
        $this->load->view('layout', $data);
    }
    ///added by shubhranshu to get traine assessments
    private function getTraineeForAssessments($courseID,$classID,$userid){
        $tenant_id = $this->tenant_id;//
        $trainees = $this->classmodel->get_Trainee_For_Assessments($tenant_id,$courseID,$classID,$userid);
        return $trainees;
    }
    ///added by shubhranshu to fetch trainee for assesssment json data
    public function get_Trainee_For_Assessments_json(){
        $courseID = $this->input->post('course_id');
        $classID = $this->input->post('class_id');
        $tenant_id = $this->tenant_id;
        $res = $this->classmodel->get_Trainee_For_Assessments_json($tenant_id,$courseID,$classID);
        $classes_arr = array();
        foreach ($res as $k => $v) {
            $classes_arr[] = array('key' => $k, 'value' => $v);
        }
        echo json_encode($classes_arr);
    }
    
   
}
