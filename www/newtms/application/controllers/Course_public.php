<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * This is the controller class for course listing based  on different  parameters. 
 */

class Course_public extends CI_Controller {

    public function __construct() {

        parent::__construct();
         
        $this->load->model('course_public_model','course_model');
        $this->load->model('Trainee_model','traineemodel');
        $this->load->helper('metavalues_helper', 'common');

        $this->load->model('meta_values');
        $this->load->model('manage_tenant_model', 'manage_tenant');
        $host=$_SERVER['HTTP_HOST'];
        if($host != 'biipmi.co'){
            $tenent_details = $this->course_model->get_tenant_details();
        }
        $this->session->set_userdata('public_tenant_details', $tenent_details);

        $data = $this->session->userdata('userDetails');    
    }

    /*
     * This function loads the initial list view page for courses.
     */
    public function index() {
       
        ////////////added by shubhranshu to show the landing page for all tenants////////////

        $host=$_SERVER['HTTP_HOST'];
        if($host == 'biipmi.co'){
            $data['page_title'] = 'BIIPMI Training Management Portal';
            $data['tenants'] = $this->manage_tenant->list_all_tenants_for_landing_page();
            $this->load->view('landing_page', $data);
            exit;
        }
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////
        $search_value = $this->input->get('search_value');

        $arg3 = $this->uri->segment(3);

        if (empty($search_value) && !empty($arg3)) {

            $pageno = $arg3;

            $search_value = $this->uri->segment(2);
        }

        $records_per_page = RECORDS_IN_MAIN_PAGE;

        $this->load->helper('pagination');

        $data['page_title'] = 'Welcome to Training Management System';



        if (!empty($search_value)) {

            $search_value = trim($search_value);

            $search_value = htmlspecialchars($search_value, ENT_QUOTES, 'UTF-8');

            $baseurl = base_url() . "course_public/$search_value/";

            $pageno = (empty($pageno)) ? $this->uri->segment(2) : $pageno;
        } else {

            $search_value = NULL;

            $baseurl = base_url() . 'course_public/';

            $pageno = $this->uri->segment(2);
        }



        if (empty($pageno)) {

            $pageno = 1;
        }


        $totalrows = count($this->course_model->get_course_list_home($search_value));

        $offset = ($pageno * $records_per_page);



        $data['course_list'] = $this->course_model->get_course_list_home($records_per_page, $offset, $search_value);



        //foreach ($data['course_list'] as $cl) {
        //$data['course_class_count'][$cl->course_id] = $this->course_model->get_course_count($cl->course_id);
        //}

        $data['controllerurl'] = 'course_public/';

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $search_value);



        $data['notifications'] = $this->course_model->get_notifications();

        $data['search_value'] = $search_value;

        $data['main_content'] = 'course_public/main_course_list';

        $this->load->view('layout_public', $data);
    }

    // skm code start for iframe

    public function com_page() {
        ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $data['course_list'] = $this->course_model->get_course_list1();

        foreach ($data['course_list'] as $cl) {

            $data['course_class_count'][$cl->course_id] = $this->course_model->get_course_count($cl->course_id);
        }

        $data['main_content'] = 'course_public/main_course_list1';

        $this->load->view('layout1', $data);
    }

    // skm code start for iframe





    /*



     * function to return the list of course class schedule

     */



    public function course_class_schedule($course_id = NULL) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $data['page_title'] = 'Course Schedule';
        $tenant_id = TENANT_ID;
        $field = $this->input->get('f');
        $order_by = $this->input->get('o');
        $baseurl = base_url() . 'course_public/course_class_schedule/' . $course_id . '/';
        $data['tabledata'] = $this->course_model->get_course_class_list($course_id, $tenant_id, $records_per_page, $offset, $field, $order_by);
        $data['crid'] = $course_id;
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'course_public/course_class_schedule/' . $course_id . '/';
        $meta_result = fetch_all_metavalues();
        $values = $meta_result[Meta_Values::LANGUAGE];
        $meta_map = $this->meta_values->get_param_map();
        $class_values = $meta_result[Meta_Values::CLASSROOM_LOCATION];
        foreach ($values as $value) {

            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_values as $value) {

            $status_lookup_location[$value['parameter_id']] = $value['category_name'];
        }
        
        foreach ($data['tabledata'] as $key => $cl) {
            
            $data['class_count'][$cl['class_id']] = $booked = $this->course_model->get_course_class_count($course_id, $cl['class_id']);
            $available = $cl['total_seats'] - $booked;
            $available = ($available < 0) ? 0 : $available;
            $data['tabledata'][$key]['available'] = $available;
           
        }
       
        foreach ($data['tabledata'] as $key => $cl) {
            $data['tabledata'][$key]['crse_manager'] = $this->course_model->get_managers($cl['training_aide']);
        }
        foreach ($data['tabledata'] as $key => $cl) {
            $data['tabledata'][$key]['classroom_trainer'] = $this->course_model->get_trainers($cl['classroom_trainer']);
        }
        $data['status_lookup_language'] = $status_lookup_language;
        
        $data['status_lookup_location'] = $status_lookup_location;
        $data['course_name'] = $this->course_model->get_course_name($course_id);
        $data['main_content'] = 'course_public/course_class_schedule';
        $this->load->view('layout_public', $data);
    }

    /* This function show the total booking in a specifc corse and class */

    public function course_class_booked_seat($course_id = NULL) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $data['page_title'] = ' Course Booking';
        $tenant_id = TENANT_ID;
        $field = $this->input->get('f');
        $order_by = $this->input->get('o');
        $baseurl = base_url() . 'course_public/course_class_booked_seat/' . $course_id . '/';
        $data['tabledata'] = $this->course_model->get_course_class_list($course_id, $tenant_id, $records_per_page, $offset, $field, $order_by);
        $data['crid'] = $course_id;
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'course_public/course_class_booked_seat/' . $course_id . '/';
        $meta_result = fetch_all_metavalues();
        $values = $meta_result[Meta_Values::LANGUAGE];
        $meta_map = $this->meta_values->get_param_map();
        $class_values = $meta_result[Meta_Values::CLASSROOM_LOCATION];
        foreach ($values as $value) {

            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_values as $value) {

            $status_lookup_location[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($data['tabledata'] as $key => $cl) {
            $data['class_count'][$cl['class_id']] = $booked = $this->course_model->get_course_class_count($course_id, $cl['class_id']);
            $available = $cl['total_seats'] - $booked;
            $available = ($available < 0) ? 0 : $available;
            $data['tabledata'][$key]['available'] = $available;
            $data['tabledata'][$key]['booked'] = $booked; //skm
        }
        foreach ($data['tabledata'] as $key => $cl) {
            $data['tabledata'][$key]['crse_manager'] = $this->course_model->get_managers($cl['training_aide']);
        }
        foreach ($data['tabledata'] as $key => $cl) {
            $data['tabledata'][$key]['classroom_trainer'] = $this->course_model->get_trainers($cl['classroom_trainer']);
        }
        $data['status_lookup_language'] = $status_lookup_language;
        $data['status_lookup_location'] = $status_lookup_location;
        $data['course_name'] = $this->course_model->get_course_name($course_id);
        $data['main_content'] = 'course_public/course_class_booking';
        $this->load->view('layout_public', $data);
    }

    /*



     * function to return the list of course class schedule based on selected date

     */

    public function classes_list_by_date($class_date = NULL) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $tenant_id = TENANT_ID;

        $data['page_title'] = 'Course Schedule';



        $field = $this->input->get('f');

        $order_by = $this->input->get('o');



        $baseurl = base_url() . 'course_public/classes_list_by_date/' . $class_date . '/';







        $data['tabledata'] = $this->course_model->get_classes_by_date($class_date, $tenant_id, $records_per_page, $offset, $field, $order_by);

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'course_public/classes_list_by_date/' . $class_date . '/';



        $meta_result = fetch_all_metavalues();

        $meta_map = $this->meta_values->get_param_map();

        $values = $meta_result[Meta_Values::LANGUAGE];

        $class_values = $meta_result[Meta_Values::CLASSROOM_LOCATION];

        foreach ($values as $value) {

            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }

        foreach ($class_values as $value) {

            $status_lookup_location[$value['parameter_id']] = $value['category_name'];
        }

        foreach ($data['tabledata'] as $key => $cl) {



            $data['class_count'][$cl['class_id']] = $booked = $this->course_model->get_course_class_count($cl['course_id'], $cl['class_id']);



            $available = $cl['total_seats'] - $booked;

            $available = ($available < 0) ? 0 : $available;

            $data['tabledata'][$key]['available'] = $available;
        }



        foreach ($data['tabledata'] as $key => $cl) {



            $data['tabledata'][$key]['crse_manager'] = $this->course_model->get_managers($cl['training_aide']);
        }

        foreach ($data['tabledata'] as $key => $cl) {

            $data['tabledata'][$key]['classroom_trainer'] = $this->course_model->get_trainers($cl['classroom_trainer']);
        }



        $data['status_lookup_language'] = $status_lookup_language;

        $data['status_lookup_location'] = $status_lookup_location;

        $data['course_date'] = $class_date;

        $data['main_content'] = 'course_public/course_class_schedule_date';

        $this->load->view('layout_public', $data);
    }

    /*



     * Function  to  check tha class available  on the selected  date 

     */

    public function check_course_class_schedule() {
        
        extract($_POST);

        $date = trim(($date));

        $tenant_id = TENANT_ID;

        $exists = $this->course_model->get_classes_by_date_count($date, $tenant_id);

        if ($exists > 0) {

            echo 1;
        } else {

            echo 0;
        }
    }

    /*



     * function to return the list of course for autocomplete text box

     */

    public function get_course_list_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $result = $this->course_model->course_list_autocomplete($query_string);

        print json_encode($result);
    }

    /**



     * This function user to display course list

     */
    public function course_list() {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $data['page_title'] = 'Course List';
        $search_value = $this->input->get('search_value');
        $arg3 = $this->uri->segment(3);
        if (empty($search_value) && !empty($arg3)) {
            $pageno = $arg3;
            // $search_value = $this->uri->segment(2);
        }
        $records_per_page = RECORDS_IN_MAIN_PAGE;
        if (!empty($search_value)) {
            $search_value = trim($search_value);
            $search_value = htmlspecialchars($search_value, ENT_QUOTES, 'UTF-8');
            $baseurl = base_url() . "course_public/course_list/$search_value/";
            $pageno = (empty($pageno)) ? $this->uri->segment(3) : $pageno;
        } else {
            $search_value = NULL;
            $baseurl = base_url() . 'course_public/course_list/';
            $pageno = $this->uri->segment(3);
        }
        $totalrows = $this->course_model->course_count($search_value);
        $field = $this->input->get('f');
        $order_by = $this->input->get('o');
        $records_per_page = 15;
        $baseurl = base_url() . 'course_public/course_list/';
        $pageno = $this->uri->segment(3);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $tenant_id = TENANT_ID;
        $data['tabledata'] = $this->course_model->get_active_course_class_list($tenant_id, $records_per_page, $offset, $field, $order_by, $search_value);

        $data['course_data'] = $this->course_model->available_course($tenant_id, $records_per_page, $offset, $field, $order_by, $search_value);
        $data1 = array();
        foreach ($data['course_data'] as $value) {
            $data1[] = $value->course_id;
        }
        $data['course_id'] = $data1;

        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'course_public/course_list/';
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by, $search_value);
        //$values = $meta_result[Meta_Values::CLASS_LANGUAGE];
        $meta_result = fetch_all_metavalues();
        $values = $meta_result[Meta_Values::LANGUAGE];
        $values1 = $meta_result[Meta_Values::COURSE_TYPE];
        $meta_map = $this->meta_values->get_param_map();
        foreach ($values as $value) {
            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($values1 as $value) {

            $status_lookup_course_type[$value['parameter_id']] = $value['category_name'];
        }

        $course_name_list = $this->course_model->fetch_course_names_list($search_value);

        foreach ($course_name_list as $value) {

            $status_lookup_course_name[$value['course_id']] = $value['crse_name'];
        }



        $data['status_lookup_language'] = $status_lookup_language;
        $data['status_lookup_course_type'] = $status_lookup_course_type;
        $data['status_lookup_course_name'] = $status_lookup_course_name;

        $data['main_content'] = 'course_public/course_list';

        $this->load->view('layout_public', $data);
    }

//  skm start

    public function enrol_for_someone() {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Enroll For Someone';

        $data['main_content'] = 'enrol_someone';

        $this->load->view('layout_public', $data);
    }

//end
//  skm code for registration form when user comes from home page start

    public function register_enroll($course_id = null, $class_id = null) {
        
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Enrollment';

        $data['course_id'] = $course_id;

        $data['class_id'] = $class_id;

        $data['user_id'] = $this->session->userdata('userDetails')->user_id;
        /* course class complete details */
        $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);
        $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
        $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
        $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
        $data['net_due'] = $net_due = $feesdue + $totalgst;
        $meta_result = $this->meta_values->get_param_map();
        $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
        $data['class_type'] = $meta_result[$course_details->class_type];
        $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
        //end


        $data['main_content'] = 'register_enroll';

        $this->load->view('layout_public', $data);
    }
    
    

//end
//  skm code for check nric is present in db or not start    

    public function check_nric_no() {

        extract($_POST);

        $taxcode = trim(($taxcode_nric));

        $res = $this->course_model->check_taxcode_exists($taxcode);
    }

//end

    public function check_nric_no_cc() {
        extract($_POST);

        $taxcode = trim(($taxcode_nric));
        $course_id = trim(($course_id));
        $class_id = trim(($class_id));

        $res = $this->course_model->check_taxcode_exists_cc($taxcode, $course_id, $class_id);
    }

//end
    //  skm code for check nric is present in db or not start    
    public function check_referal_nric() {
        extract($_POST);
        $taxcode = trim(($taxcode_nric));
        $res = $this->course_model->check_referal_taxcode_exists($taxcode);
    }

//end


    /*



     * skm Function to view  the page  to  enter  the credentials for enrol someone    



     */

    public function referral_credentials1($course_id=0, $class_id=0) {
        extract($_POST);
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        unlink(FCPATH .'captcha/'.$this->session->userdata('captcha_file'));// added by shubhranshu to delete the captcha file
        $session_user_id = $this->session->userdata('userDetails')->user_id;
    
        if (!empty($session_user_id)) {
            redirect('register_enroll/' . $course_id . '/' . $class_id);
        } else {

            $data['page_title'] = 'Enrol For Someone';
            $flag = 0;
            $data['post'] = 0;
            $data['show_error_form'] = 0;
            if ($this->input->server('REQUEST_METHOD') === 'POST') {
                $course_id = $this->input->post('course_id');
                $class_id = $this->input->post('class_id');
                $registration = $this->input->post('registration');
                $relation = $this->input->post('relationship');



                $data['post'] = 1;
                $submit = $this->input->post('submit');
                $enrolment = $this->input->post('enrolment');
                $this->load->library('form_validation');
                
                $this->_refer_friend_server_validation(1, 1);
                
                

                if ($this->form_validation->run() == TRUE) {
                    $this->session->unset_userdata('captcha_key');
                    $this->load->model('user_model');
                        unlink(FCPATH .'captcha/'.$this->session->userdata('captcha_file')); // added by shubhranshu to delete the captcha file
//                    $this->db->trans_start();
                    $res = $this->course_model->save_imp_trainee_skm();
                    if ($res['user_id'] != 0) {
                        if ($res['status'] != FALSE) {
                            $uid = $res['user_id'];
                            $tax_code = $res['tax_code'];
                            $friend_id = $res['friend_id'];
                            $user_password = $res['user_pass'];
                            $friend_password = $res['friend_pass'];
                            //                        
                            $this->create_classenroll2($uid, $user_password, $course_id, $class_id, $tax_code, $registration, $friend_id, $friend_password, $relation);
                            $flag = 1;
                        } else {

                            echo "error page show";
                        }
                    } else {
                        $error = '<center>We are not able to complete the enrollment in this class. Please try again later or contact to Administrator.</center>';
                        $this->session->set_flashdata('error', $error);
                        return redirect('course_public/referral_credentials1/' . $course_id . '/' . $class_id);
                    }

//                    $this->db->trans_complete();
//                    if ($this->db->trans_status() === FALSE)
//                    {
//                        $status = FALSE;
//                        // rediect to view page
//                    }
                }
            }
            
            if ($flag == 0) {
                $data['course_id'] = $course_id;
                $data['class_id'] = $class_id;
                /* course class complete details */
                $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);
                $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);
//print_r($course_details);

                $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
                $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
                $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
                $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
                $data['net_due'] = $net_due = $feesdue + $totalgst;
                $meta_result = $this->meta_values->get_param_map();
                $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
                $data['class_type'] = $meta_result[$course_details->class_type];
                $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
                //end
                $data['captcha'] = $this->_create_captcha();
                $data['main_content'] = 'enrol_someone';
                $this->load->view('layout_public', $data);
            }
        }
    }

    public function create_classenroll2($uid, $user_password = null, $course_id, $class_id, $tax_code, $registration = null, $friend_id = null, $friend_password = null, $relation = null) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $action = $this->input->get('action');
        $error = '';
        $checkbox = $this->input->post('checkbox');
        $taxcode = $tax_code;
//        if ($this->input->server('REQUEST_METHOD') === 'POST') {
        $submit = $this->input->post('submit');
        $taxcode_details = $this->course_model->validate_taxcode_data(trim($taxcode));

        $param['course_id'] = $course_id;
        $param['class_id'] = $class_id;
        $param['user_id'] = $uid;
        $param['refer_id'] = $friend_id;


        $param['class_details'] = $class_details = $this->course_model->get_class_details($class_id);

        $param['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);
        $is_enrolled = $this->course_model->is_user_enrolled1($taxcode_details->user_id, $class_id, $course_id);
        $param['relation'] = $this->input->post('relationship');
        $param['trainee_data'] = $taxcode_details;

        $param['class_name'] = $class_details->class_name;
        $param['class_fees'] = $class_details->class_fees;
//            $param['discount_rate'] = $class_details->discount_rate;
        /* calculate discount type and amount based on class and individual discount skm start */

        $param['discount_rate'] = $class_details->class_discount;
        $param['indv_class_details'] = $indv_class_details = $this->course_model->get_indv_class_details($course_id, $uid);
        $param['discount_amount'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
        if ($discount_total > 0) {
            $param['discount_type'] = 'DISCLASS';
            $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        }

        if ($discount_total == 0) {
            if ($indv_class_details->discount_amount == 0 && $indv_class_details->discount_percent != 0) {
                $param['discount_amount'] = $discount_total = round(($indv_class_details->discount_percent / 100) * $class_details->class_fees, 2);
                $param['discount_type'] = 'DISINDVI';
                $param['discount_rate'] = $indv_class_details->discount_percent;
                $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
            }
            if ($indv_class_details->discount_amount != 0 && $indv_class_details->discount_percent == 0) {
                $param['discount_amount'] = $discount_total = $indv_class_details->discount_amount;
                $param['discount_type'] = 'DISINDVI';
                $param['discount_rate'] = round(($indv_class_details->discount_amount / $class_details->class_fees) * 100, 2);
                $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
            }
        }

        if ($indv_class_details == 0 && $discount_total == 0) {
            $param['discount_type'] = 'DISINDVI';
            $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        }
        /* end */
        $param['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
//            $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);           
        $param['gst_amount'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
        $param['net_due'] = $net_due = $feesdue + $totalgst;
        $meta_result = $this->meta_values->get_param_map();
        $param['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
        $param['class_type'] = $meta_result[$course_details->class_type];
        $param['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
        $param['gst_onoff'] = $course_details->gst_on_off;
        $param['gst_rule'] = $course_details->subsidy_after_before;

        if ($is_enrolled == 0) {

//                    print_r($param);
            //Update additional information- Added for CR03
            $result1 = $this->course_model->update_additional_information($param);

            $param['enrol_status'] = 'ENRLBKD';
            $param['pay_status'] = 'NOTPAID';
            $result = $this->course_model->create_new_enroll2($param);

            if ($result["status"] == FALSE) {
                $error = 'We were not able to complete the enrollment in the class. Please try again later or contact your Administrator.
                                Click <a href="' . base_url() . 'course_public/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';
                $this->session->set_flashdata('error', $error);
                return redirect('course_public/referral_credentials1/' . $class_details->course_id . '/' . $class_details->class_id);
            } else {

                /* session start when user select course &class & register and then enroll in class successfully */
//                              This is comment because user can not login from enroll for some 30-5-2016
//                              $this->load->model('user_model');
//                            $resp = $this->user_model->register_login_process($param['user_id']);
//                            if(!empty($resp))
//                            {
//                                $this->session->set_userdata('userDetails', $resp);
//                            }//end

                $message = 'You have successfully booked a seat. Click <a href="' . base_url() . 'course_public/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';
                $data['success_message'] = $message;

                $user_mailer_details = array('username' => $this->input->post('user_name'),
                    'email' => $this->input->post('frnd_registered_email'),
                    'password' => $user_password,
                    'firstname' => strtoupper($this->input->post('pers_first_name')),
                    'lastname' => strtoupper($this->input->post('pers_second_name')),
                    'gender' => $this->input->post('pers_gender'));


                $res = $this->user_model->r_userDetails($friend_id);

                /* it check referal nric found or not start */
                $r_res_one = $this->input->post('r_res_one');
                $r_res_zero = $this->input->post('r_res_zero');
                if (!empty($r_res_one)) {
                    $r_res = $r_res_one;
                } else if (!empty($r_res_zero)) {
                    $r_res = $r_res_zero;
                }//end

                $res_found1 = $this->input->post('res_found1');
                if ($uid != '' && $checkbox == '' && $friend_id == '' && $res_found1 == 0) { //cond 1  //echo "1";
                    $r_someone = array('firstname' => strtoupper($this->input->post('r_name1')), 'email' => $this->input->post('r_email1'));

                    $this->course_model->send_reg_someone_referance_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance
                    $this->course_model->send_reg_someone_referal_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance referal
                    // $this->send_tenant_mail($r_user_details, 'BPEMAC'); // tenent email
                }//end
                else if ($uid != '' && $checkbox == 1 && $friend_id != '' && $res_found1 == 0 && $r_res == 1) { //cond 2
                    //echo "2";
                    $r_someone = array('firstname' => strtoupper($res->first_name), 'email' => $res->registered_email_id);
                    $this->course_model->send_reg_someone_referance_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance
                    $this->course_model->send_reg_someone_referal_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance referal
                } else if ($uid != '' && $checkbox == 1 && $friend_id != '' && $res_found1 == 0 && $r_res == 0) { //cond3
                    $r_user_details = array('username' => $this->input->post('r_nric'),
                        'email' => $this->input->post('r_email'),
                        'password' => $friend_password,
                        'firstname' => strtoupper($this->input->post('r_name')));
                    $r_someone = array('firstname' => strtoupper($this->input->post('r_name')), 'email' => $this->input->post('r_email'));


                    $this->course_model->send_reg_someone_referance_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance
                    $this->course_model->send_trainee_email($r_user_details, 'BPEMAC'); // referal
                    $this->course_model->send_reg_someone_referal_email($r_someone, $user_mailer_details, 'BPEMAC'); // referance referal
                    //$this->send_tenant_mail($user_details, 'BPEMAC'); // tenent for referance email
                    //$this->send_tenant_mail($r_user_details, 'BPEMAC'); // tenent for referal email
                } else if ($uid != '' && $checkbox == 1 && $friend_id != '' && $r_res == 0 && $res_found1 == 1) {    //echo "6";
                    $r_user_details = array('username' => $this->input->post('r_nric'),
                        'email' => $this->input->post('r_email'),
                        'password' => $friend_password,
                        'firstname' => strtoupper($this->input->post('r_name')));
                    $this->course_model->send_trainee_email($r_user_details, 'BPEMAC'); // referal
                }



                $course_name = $this->user_model->course_name($param['course_id']);
                $user_result = $this->user_model->r_userDetails($param['user_id']);

                $r_name1 = $this->input->post('r_name1');
                $r_email1 = $this->input->post('r_email1');


                if (!empty($friend_id)) {
                    $user_details = array(
                        'email' => $user_result->registered_email_id,
                        'firstname' => strtoupper($user_result->first_name),
                        'lastname' => strtoupper($user_result->last_name),
                        'gender' => $user_result->gender,
                        'course_name' => $course_name,
                        'class_name' => $class_details->class_name,
                        'class_start' => $class_details->class_start_datetime,
                        'class_end' => $class_details->class_end_datetime,
                        'class_venue' => $param['classloc'],
                        'r_firstname' => strtoupper($res->first_name),
                        'r_email' => $res->registered_email_id
                    );
                } else {

                    $user_details = array(
                        'email' => $user_result->registered_email_id,
                        'firstname' => strtoupper($user_result->first_name),
                        'lastname' => strtoupper($user_result->last_name),
                        'gender' => $user_result->gender,
                        'course_name' => $course_name,
                        'class_name' => $class_details->class_name,
                        'class_start' => $class_details->class_start_datetime,
                        'class_end' => $class_details->class_end_datetime,
                        'class_venue' => $param['classloc'],
                        'r_firstname' => $r_name1,
                        'r_email' => $r_email1
                    );
                }

                $this->course_model->send_referance_email_someone($user_details, 'BPEMAC');
                $this->course_model->send_referal_email_someone($user_details, 'BPEMAC');

                //end




                $_POST = array(
                    'user_id' => $param['user_id'],
                    'class_id' => $param['class_id'],
                );
                $data['booking_ack'] = $this->_get_booking_ack_data();
                $data['page_title'] = 'Enrol';
                $data['main_content'] = 'enrol/message_status';
                $this->load->view('layout_public', $data);
            }
        } else {
//                            This is comment because user can not login from enroll for some when udser details found
//                             but referance already enrolled in particular course and class 30-5-2016
//                            $this->load->model('user_model');
//                            $resp = $this->user_model->register_login_process($param['user_id']);
//                            if(!empty($resp))
//                            {
//                                $this->session->set_userdata('userDetails', $resp);
//                            }//end

            $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. Click <a href="' . base_url() . 'course_public/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';

            $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'.';
            //$error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '>';
            $this->session->set_flashdata('error', $error);
            //return redirect('course_public/class_enroll/' . $class_details->course_id . '/' . $class_details->class_id);
            return redirect('course_public/course_class_schedule/' . $class_details->course_id);
        }

//        }
//        $data['page_title'] = 'Enrol';
//        $data['main_content'] = 'enrol/message_status';
//        $this->load->view('layout_public', $data);
    }

    /*

     *  function which is use enroll some one it is same copy of user contoller function

     */

    public function reg($course_id, $class_id, $tax_code, $registration = null) {

         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $this->load->helper('metavalues_helper', 'common');

        $this->load->model('meta_values');

        $taxcode = $tax_code;



        if (!empty($taxcode)) {

            $taxcode_details = $this->course_model->validate_taxcode_data(trim($taxcode));
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



                $tenant_master_result_set = $this->course_model->get_tenant_name(trim($taxcode_details->tenant_id));



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

            if ($registration == 1) {

                return redirect(base_url() . 'course_public/register_enroll');
            } else {
                return redirect(current_url());
            }
        } else {



            $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);



            $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

            $is_enrolled = $this->course_model->is_user_enrolled1($taxcode_details->user_id, $class_id, $course_id);





            if ($is_enrolled != 0) {

//                            echo "error msg if";

                $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. '
                        . 'Please click <a href="' . base_url() . 'course_public/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';

                $this->session->set_flashdata('error', $error);

                $this->session->set_userdata('prev_tax_code', $taxcode);

//                              

                if ($registration == 1) {

                    return redirect(base_url() . 'course_public/register_enroll');
                } else {
                    return redirect(current_url());
                }
            } else {

//                             echo "error msg else";



                $gender = ($taxcode_details->gender == 'FEMALE') ? 'Ms. ' : 'Mr. ';

                $user_name = $gender . $taxcode_details->first_name . ' ' . $taxcode_details->last_name;

                $user_name = rtrim($user_name, ' ');

                $data['success'] = 'Welcome ' . $user_name . ', please proceed with enrollment (or) click <a href="' . base_url() . 'course_public/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';

                $data['user_id'] = $taxcode_details->user_id;

                //Added for CR03
                $data['additional_remarks'] = $taxcode_details->additional_remarks;

                $data['trainee_data'] = $taxcode_details;

                $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);

                $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);

                $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();

                $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;

                $data['net_due'] = $net_due = $feesdue + $totalgst;

                $meta_result = $this->meta_values->get_param_map();

                $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';

                $data['class_type'] = $meta_result[$course_details->class_type];

                $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];

                $data['main_content'] = 'course_public/payment_details';

                return $this->load->view('layout_public', $data);
            }
        }
    }

    /*



     * Function to view  the page  to  enter  the credentials for refer friend    



     */

    public function referral_credentials() {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Refer a Friend';

        $data['post'] = 0;

        $data['show_error_form'] = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {



            $taxcode = $this->input->post('taxcode');

            $user_data = $this->course_model->get_user_id_from_taxcode($taxcode);

            if ($this->input->post('yes_no') == 0) {

                if (!empty($user_data)) {

                    $meta_map = $this->meta_values->get_param_map();

                    $user_data->personal_address_country = $meta_map[$user_data->personal_address_country];

                    $user_data->personal_address_state = $meta_map[$user_data->personal_address_state];

                    $gender = ($user_data->gender == 'MALE') ? 'Mr. ' : 'Ms. ';

                    if (!empty($user_data->personal_address_bldg)) {

                        $contact = $user_data->personal_address_bldg . ', ';
                    }

                    if (!empty($user_data->personal_address_city)) {

                        $contact .= $user_data->personal_address_city . ', ';
                    }

                    if (!empty($user_data->personal_address_state)) {

                        $contact .= $user_data->personal_address_state . ', ';
                    }

                    if (!empty($user_data->personal_address_country)) {

                        $contact .= $user_data->personal_address_country . ', ';
                    }

                    $contact = rtrim($contact, ', ');

                    $suffix = '';

                    if (!empty($contact)) {

                        $suffix = ' from ' . $contact;
                    }

                    $data['yes_no_message'] = 'Your Credentials have been found in system. Are you ' . $gender . $user_data->first_name . ' ' . $user_data->last_name . $suffix . ' ?';
                } else {

                    $data['yes_no_message'] = '';
                }
            }

            $data['post'] = 1;

            $submit = $this->input->post('submit');



            $this->load->library('form_validation');

            if ($submit == 'add_trainee') {

                $data['show_error_form'] = 1;

                $this->_refer_friend_server_validation(1, 1);

                if ($this->form_validation->run() == TRUE) {

                    $this->session->unset_userdata('captcha_key');

                    $this->load->model('user_model');

                    $result = $this->course_model->save_imp_trainee();

                    if ($result != FALSE) {

                        $data['user_id'] = $user_id = $result;

                        $data['user_data'] = $this->course_model->get_user_data($user_id);

                        $_POST = array();

                        $data['success_message'] = 'Your account has been created, now you can refer your friend.';

                        $data['main_content'] = 'refer_friend';

                        return $this->load->view('layout_public', $data);
                    } else {

                        $data['error_message'] = 'Unable to create your account. Please try again or get in get in touch with our Administrator.';
                    }
                }
            } else {

                $this->_refer_friend_server_validation(2);

                if ($this->form_validation->run() == TRUE) {

                    $this->session->unset_userdata('captcha_key');

                    $data['user_data'] = $user_data;

                    $data['user_id'] = $user_data->user_id;



                    $data['main_content'] = 'refer_friend';

                    return $this->load->view('layout_public', $data);
                }
            }
        }



        $this->session->unset_userdata('refer_user_id');

        $this->session->unset_userdata('refer_friend_id');

        $this->session->unset_userdata('submit_status');

        $data['captcha'] = $this->_create_captcha();

        $data['main_content'] = 'refer_login';

        $this->load->view('layout_public', $data);
    }

    /**



     * method to refer a friend    



     */
    public function refer() {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Refer a Friend';

        $data['user_id'] = $user_id = $this->input->post('user_id');

        $data['user_data'] = $this->course_model->get_user_data($user_id);

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if ($this->input->post('submit_but')) {

                $data['submit'] = $submit = $this->input->post('submit_but');
            } else {

                $data['submit'] = $submit = $this->input->post('submit');
            }

            if ($submit == 'save_and_exit') {



                redirect('course');
            } elseif ($submit == 'save_and_continue') {



                $data['main_content'] = 'refer_friend';

                return $this->load->view('layout_public', $data);
            } elseif ($submit == 'continue' || $submit == 'exit') {



                $this->load->library('form_validation');

                $_POST['validate_tenant'] = $data['user_data']->tenant_id;

                $result = $this->course_model->save_imp_trainee();

                if ($result == FALSE) {

                    $data['error_message'] = 'Unable to create your friends account. Please try again or get in touch with our administrator';

                    $data['main_content'] = 'refer_friend';

                    $this->load->view('layout_public', $data);
                } else {

                    if ($submit == 'continue') {

                        $message = 'You have successfully registered your friend.Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register  another friend OR Use the Class List shown below to Enrol your friend to a class.';

                        $data['submit'] = 'save_and_continue';
                    } else {

                        $message = 'You have successfully registered your friend. Please click  <a href="' . base_url() . '">here</a> to go back to home page OR Use the Class List shown below to Enrol your friend to a class.';

                        $data['submit'] = 'save_and_exit';
                    }

                    $this->session->set_flashdata('success', $message);

                    $this->session->set_userdata('refer_user_id', $data['user_id']);

                    $this->session->set_userdata('refer_friend_id', $result);

                    $this->session->set_userdata('submit_status', $data['submit']);

                    redirect('course_public/get_course_class_list');
                }
            }
        } else {

            redirect('course_public/referral_credentials');
        }
    }

    /**



     * This method returns course_class_list



     */
//    public function get_course_class_list() {

    public function get_course_class_list($uid = null, $friend_id = null, $relation = null) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Available Classlist';

        //STEP1 ST

        $data['user_id'] = $uid;

        $data['friend_id'] = $friend_id;

        $data['relation'] = $relation;

        // STEP1 ED

        $field = $this->input->get('f');

        $order_by = $this->input->get('o');

        $tenant_id = TENANT_ID;

        $data['tabledata'] = $this->course_model->get_course_class_list($id = NULL, $tenant_id, 100, 100, $field, $order_by);

        foreach ($data['tabledata'] as $key => $cl) {

            $set_flag = 1;



            $count = $this->course_model->get_course_class_count($cl['course_id'], $cl['class_id']);

            if ($cl['total_seats'] <= $count) {

                unset($data['tabledata'][$key]);

                $set_flag = 0;
            }



            if ($set_flag) {

                $data['tabledata'][$key]['class_status'] = $this->course_model->get_class_status($cl['class_id']);

                $data['tabledata'][$key]['crse_manager'] = $this->course_model->get_managers($cl['crse_manager']);
            }
        }

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'course_public/get_course_class_list/';

        $meta_result = fetch_all_metavalues();

        $values = $meta_result[Meta_Values::LANGUAGE];

        $class_values = $meta_result[Meta_Values::CLASSROOM_LOCATION];

        foreach ($values as $value_array) {

            $status_lookup_language[$value_array['parameter_id']] = $value_array['category_name'];
        }

        foreach ($class_values as $value_array) {

            $status_lookup_location[$value_array['parameter_id']] = $value_array['category_name'];
        }

        $data['status_lookup_language'] = $status_lookup_language;

        $data['status_lookup_location'] = $status_lookup_location;

        $data['main_content'] = 'enrol/courselist';

        $this->load->view('layout_public', $data);
    }

    /**



     * This method used to enroll friend.



     * @param type $class_id



     * @param type $data



     * @return type



     */
    public function enrol_friend($class_id = NULL, $data = array()) {

         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        if ($this->input->server('REQUEST_METHOD') === 'POST' || $class_id) {

            $data['page_title'] = 'Enrol a Friend';

            if (empty($class_id)) {

                $class_id = $this->input->post('class_id');
            }

            $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);

//            $user_id = $this->session->userdata('refer_user_id');
//            $trainee_id = $this->session->userdata('refer_friend_id');
            // STEP1 ST

            if ($this->input->post('user_id') != '' || $this->input->post('friend_id')) {//skm when comes from enroll some one start

                $user_id = $this->input->post('friend_id'); // referal

                $trainee_id = $this->input->post('user_id'); // referance

                $relation = $this->input->post('relation'); // friendship relation
            }// end
            else {

                $user_id = $this->session->userdata('refer_user_id');

                $trainee_id = $this->session->userdata('refer_friend_id');
            }

            // STEP1 ED

            if (empty($trainee_id) || empty($user_id) || empty($class_id) || empty($class_details)) {



                return redirect('course_public/referral_credentials');
            }



            $data['user_data'] = $this->course_model->get_user_data($user_id);



            $data['trainee_data'] = $this->course_model->get_user_data($trainee_id);



            $data['relation'] = $relation; // friendship relation



            $total_booked = $this->course_model->get_course_class_count($class_details->course_id, $class_id);

            if ($class_details->total_seats <= $total_booked && $class_details->class_pymnt_enrol == PAY_D_ENROL) {



                return redirect('course_public/referral_credentials');
            }

            $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

            $is_enrolled = $this->course_model->is_user_enrolled1($trainee_id, $class_id, $class_details->course_id);

            if ($is_enrolled != '0') {

                $this->session->set_flashdata('error', 'Your friend is already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. Please choose another class.');

                if ($this->input->post('user_id') && $this->input->post('friend_id') != '') {

                    return redirect('course_public/get_course_class_list/' . $trainee_id . '/' . $user_id);
                } else {

                    return redirect('course_public/get_course_class_list');
                }
            }







            $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);

            $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);

            $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();



            $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;



            $data['net_due'] = $net_due = $feesdue + $totalgst;

            //Added for CR03

            $data['additional_remarks'] = $data[trainee_data]->additional_remarks;



            $meta_result = $this->meta_values->get_param_map();

            $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';

            $data['class_type'] = $meta_result[$course_details->class_type];

            $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];

            $data['main_content'] = 'enrol/payment_details';

            $this->load->view('layout_public', $data);
        }
    }

    /**



     * paypal create enroll



     * @return type



     */
    public function create_enroll() {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $this->load->helper('common');

        $this->load->library('paypal_class');

        $p = new paypal_class();

        $action = $this->input->get('action');

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $submit = $this->input->post('submit');

            $param['user_id'] = $this->input->post('user_id');

            //Added for CR03

            $param['additional_remarks'] = $this->input->post('additional_remarks');



            $param['refer_id'] = $this->input->post('refer_id');

            $param['course_id'] = $this->input->post('course_id');

            $param['class_id'] = $this->input->post('class_id');

            $param['class_fees'] = $this->input->post('class_fees');

            $param['discount_rate'] = $this->input->post('discount_rate');

            $param['discount_amount'] = $this->input->post('discount_amount');

            $param['gst_rate'] = $this->input->post('gst_rate');

            $param['gst_amount'] = $this->input->post('gst_amount');

            $param['gst_onoff'] = $this->input->post('gst_onoff');

            $param['gst_rule'] = $this->input->post('gst_rule');

            $param['net_due'] = $this->input->post('net_due');

            $param['relation'] = $this->input->post('relation');



            $data['class_details'] = $class_details = $this->course_model->get_class_details($param['class_id']);

            $is_enrolled = $this->course_model->is_user_enrolled1($param['user_id'], $param['class_id'], $class_details->course_id);



            if ($is_enrolled == 0) {



                if ($submit == 'book_now') {

                    $param['enrol_status'] = 'ENRLBKD';

                    $param['pay_status'] = 'NOTPAID';

                    $result = $this->course_model->create_new_enroll($param);

                    //Update additional information- Added for CR03

                    $result1 = $this->course_model->update_additional_information($param);



                    if ($result["status"] == FALSE) {

                        $error_message = 'We were not able to complete the enrollment in the class. Please try again later or contact your Administrator.

                                          Please click  <a href="' . base_url() . '"> here </a> to go back to home page.';

                        $data['error_message'] = $error_message;
                    } else {

                        /* skm-> Fetch Details of refral and refrance for email start */

                        $this->load->model('user_model');

                        $course_name = $this->user_model->course_name($class_details->course_id);

                        $user_result = $this->user_model->r_userDetails($param['user_id']); // referance user details

                        $r_user_result = $this->user_model->r_userDetails($param['refer_id']); // // referal user details

                        $user_details = array(
                            'email' => $user_result->registered_email_id,
                            'firstname' => strtoupper($user_result->first_name),
                            'lastname' => strtoupper($user_result->last_name),
                            'gender' => $user_result->gender,
                            'course_name' => $course_name,
                            'class_name' => $class_details->class_name,
                            'class_start' => $class_details->class_start_datetime,
                            'class_end' => $class_details->class_end_datetime,
                            'class_venu' => $class_details->classroom_venue_oth,
                            'r_email' => $r_user_result->registered_email_id,
                            'r_firstname' => strtoupper($r_user_result->first_name),
                            'r_lastname' => strtoupper($r_user_result->last_name),
                        );





                        $this->course_model->send_referance_email_someone($user_details, 'BPEMAC');

                        $this->course_model->send_referal_email_someone($user_details, 'BPEMAC');

//                      //end



                        $message = 'You have successfully booked a seat.';



                        if ($this->session->userdata('submit_status') == 'save_and_continue') {

                            $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click  <a href="' . base_url() . '"> here </a> to go back to the home page.';
                        } else {

                            $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
                        }

                        $data['success_message'] = $message;

                        $data['booking_ack'] = $this->_get_booking_ack_data();
                    }
                }

                if ($submit == 'pay_now') {

                    //Update additional information- Added for CR03

                    $result1 = $this->course_model->update_additional_information($param);



                    $tenant_details = $this->course_model->get_tenant_masters(TENANT_ID);

                    $paypal_email = $tenant_details->paypal_email_id;

                    $p->admin_mail = $tenant_details->tenant_email_id;



                    $this_script = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];







                    if (!empty($_SERVER['HTTPS'])) {



                        $this_script = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    }

                    $p->add_field('business', $paypal_email);



                    $p->add_field('cmd', $this->input->post('cmd'));



                    $p->add_field('upload', '1');

                    $p->add_field('return', $this_script . '?action=success');

                    $p->add_field('cancel_return', $this_script . '?action=cancel');



                    $p->add_field('notify_url', $this_script . '?action=ipn');



                    $p->add_field('currency_code', $tenant_details->Currency);

                    $p->add_field('country', $tenant_details->Country);

                    $p->add_field('item_name', $this->input->post('class_name'));

                    $p->add_field('quantity', 1);

                    $p->add_field('amount', $this->input->post('net_due'));



                    $p->add_field('no_shipping', 1);



                    $enroll_data = $param;

                    $enroll_data['enrol_status'] = 'ENRLBKD';

                    $enroll_data['pay_status'] = 'NOTPAID';

                    $enrol_result = $this->course_model->create_new_enroll($enroll_data);

                    $param['invoice_id'] = $enrol_result['invoice_id'];



                    $param['payment_due_id'] = $enrol_result['payment_due_id'];



                    $pid = $this->course_model->store_paypal_enrol_details($param, $param['invoice_id']);

                    $p->add_field('invoice', $param['invoice_id']);



                    $this->session->set_userdata('paypal_data', $param);



                    $this->session->set_userdata('classId', $this->input->post('class_id'));



                    if ($enrol_result["status"]) {

                        return $p->submit_paypal_post();
                    } else {

                        $error = 'We have not been able to enrol you to the class. Please try again or get in touch with your administrator.';

                        $this->session->set_flashdata('error', $error);

                        return redirect('course_public/class_enroll/' . $class_details->course_id . '/' . $class_details->class_id);
                    }
                }
            } else {



                $course_details = $this->course_model->course_basic_details($class_details->course_id);

                $this->session->set_flashdata('error', 'Your friend is already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. Please select another class.');



                //return redirect('course_public/get_course_class_list');

                if ($param['user_id'] != '' && $param['refer_id'] != '') {

                    return redirect('course_public/get_course_class_list/' . $param['user_id'] . '/' . $param['refer_id']);
                } else {

                    return redirect('course_public/get_course_class_list');
                }
            }
        }



        if ($action == 'cancel') {



            $message = 'The payment has been cancelled, no enrollment has been done.';

            if ($this->session->userdata('submit_status') == 'save_and_continue') {

                $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click <a href="' . base_url() . '">here</a> to go back to the home page.';
            } else {

                $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
            }

            $class_id = $this->session->userdata('classId');

            $data['error_message'] = $message;

            $this->enrol_friend($class_id, $data);

            return;
        }

        if ($action == 'success') {

            $tran_id = $this->input->post('txn_id');



            $invoice_id = $this->input->post('invoice');

            $param = $this->session->userdata('paypal_data');

            $this->session->unset_userdata('paypal_data');

            $param['enrol_status'] = 'ENRLACT';

            $param['pay_status'] = 'PAID';



            if ($tran_id) {



                $exists = $this->course_model->check_paypal_payment_details_exists($tran_id);



                if ($exists) {



                    $is_enrolled = $this->course_model->is_user_paid($param['invoice_id']);



                    if ($is_enrolled->num_rows() == 0) {



                        $result = $this->course_model->update_enroll_payment($param);



                        if (!$result["status"]) {



                            $error_message = 'We have not been able to update your payment status.'
                                    . ' You have however been enrolled for the class. Please contact your Administrator with the transction Id shown here, for your payment update. Paypal Transcation Id: <label style="font-weight:bold;color:red;">' . $tran_id . '</label>';

                            $data['error_message'] = $error_message;
                        } else {



                            $message = 'The payment was successful and enrollment has been done in the class.';

                            if ($this->session->userdata('submit_status') == 'save_and_continue') {

                                $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click <a href="' . base_url() . '">here</a> to go back to the home page.';
                            } else {

                                $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
                            }

                            $data['success_message'] = $message;

                            $data['payment_receipt'] = $this->_get_payment_receipt($param['user_id'], $param['class_id']);
                        }
                    } else {



                        $message = 'The payment was successful and enrollment has been done in the class.';

                        if ($this->session->userdata('submit_status') == 'save_and_continue') {

                            $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click <a href="' . base_url() . '">here</a> to go back to the home page.';
                        } else {

                            $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
                        }

                        $data['success_message'] = $message;

                        $data['payment_receipt'] = $this->_get_payment_receipt($param['user_id'], $param['class_id']);
                    }
                } else {



                    $result = $this->course_model->update_enroll_payment($param);



                    $paypal_details = array(
                        'payer_id' => $this->input->post('payer_id'),
                        'payer_email' => $this->input->post('payer_email'),
                        'txn_id' => $this->input->post('txn_id'),
                        'mc_gross' => $this->input->post('mc_gross'),
                        'tenant_id' => TENANT_ID,
                        'invoice_id' => $result['invoice_id'],
                        'enrol_details' => NULL
                    );



                    $this->course_model->update_paypal_enrol_details($invoice_id, $paypal_details);



                    if (!$result["status"]) {



                        $error_message = 'We were not able to complete enrollment in the class. We have however received the payment for the class. Please contact your Administrator with the transction Id shown here, for your enrollment. Paypal Transcation Id: <label style="font-weight:bold;color:red;">' . $tran_id . '</label>';

                        $data['error_message'] = $error_message;
                    } else {



                        $message = 'The payment was successful and enrollment has been done in the class..';

                        if ($this->session->userdata('submit_status') == 'save_and_continue') {

                            $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click <a href="' . base_url() . '">here</a> to go back to the home page.';
                        } else {

                            $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
                        }

                        $data['success_message'] = $message;

                        $data['payment_receipt'] = $this->_get_payment_receipt($param['user_id'], $param['class_id']);
                    }
                }
            } else {



                $is_enrolled = $this->course_model->is_user_enrolled($param['user_id'], $param['class_id'], $param['course_id']);

                if ($is_enrolled->num_rows() == 0) {



                    $error_message = 'We havenot received the Paypal transaction ID, as a result, we have not been able to update your payment status. But you have been enrolled to the class. Please get in touch with your Administrator with your Paypal Transaction details to complete your payment status update.';

                    $data['error_message'] = $error_message;
                } else {

                    $message = 'The payment was successful and enrollment has been done in the class.';

                    if ($this->session->userdata('submit_status') == 'save_and_continue') {

                        $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click <a href="' . base_url() . '">here</a> to go back to the home page.';
                    } else {

                        $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
                    }

                    $data['success_message'] = $message;

                    $data['payment_receipt'] = $this->_get_payment_receipt($param['user_id'], $param['class_id']);
                }
            }
        }

        if ($action == 'ipn') {

            if ($p->validate_ipn()) {



                $invoice_id = $this->input->post('invoice');

                $param = $this->course_model->get_paypal_enrol_details($pid);

                $param['enrol_status'] = 'ENRLACT';

                $param['pay_status'] = 'PAID';



                $result = $this->course_model->update_enroll_payment($param);







                $paypal_details = array(
                    'payer_id' => $this->input->post('payer_id'),
                    'payer_email' => $this->input->post('payer_email'),
                    'txn_id' => $this->input->post('txn_id'),
                    'mc_gross' => $this->input->post('mc_gross'),
                    'tenant_id' => TENANT_ID,
                    'invoice_id' => $result['invoice_id'],
                    'enrol_details' => NULL
                );



                $this->course_model->update_paypal_enrol_details($invoice_id, $paypal_details);
            }
        }

        $data['page_title'] = 'Enrol';

        $data['main_content'] = 'enrol/message_status';

        $this->load->view('layout_public', $data);
    }

    public function create_enroll_self_loggedin($course_id, $class_id) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////
        $this->load->model('user_model');
        $user_id = $this->session->userdata('userDetails')->user_id;
        $user_result = $this->user_model->r_userDetails($user_id);

        $taxcode = $user_result->tax_code;

        $taxcode_details = $this->course_model->validate_taxcode_data(trim($taxcode));

        $param['user_id'] = $user_id;
        $param['class_details'] = $class_details = $this->course_model->get_class_details($class_id);
        $param['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $param['course_id'] = $course_id;
        $param['class_id'] = $class_id;
        $param['class_name'] = $class_details->class_name;
        $param['class_fees'] = $class_details->class_fees;
        $param['discount_rate'] = $class_details->discount_rate;
        $param['discount_amount'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
        $param['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
        $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        $param['gst_amount'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
        $param['net_due'] = $net_due = $feesdue + $totalgst;
        //$meta_result = $this->meta_values->get_param_map();
        $param['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
        $param['class_type'] = $meta_result[$course_details->class_type];
        $param['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
        $param['gst_onoff'] = $course_details->gst_on_off;
        $param['gst_rule'] = $course_details->subsidy_after_before;
        //$is_enrollerd = $this->course_model->is_user_enrolled1($param['user_id'], $param['class_id'], $class_details->course_id);
        $data['class_details'] = $class_details = $this->course_model->get_class_details($param['class_id']);
        $is_enrolled = $this->course_model->is_user_enrolled1($taxcode_details->user_id, $class_id, $course_id);
        if ($is_enrolled == 0) {
            //if ($submit == 'book_now') {

            $param['enrol_status'] = 'ENRLBKD';
            $param['pay_status'] = 'NOTPAID';

            $result = $this->course_model->create_new_enroll($param);
            //Update additional information- Added for CR03
            $result1 = $this->course_model->update_additional_information($param);




            if ($result["status"] == FALSE) {
                $error_message = 'We were not able to complete the enrollment in the class. Please try again later or contact your Administrator.
                                          Please click  <a href="' . base_url() . '"> here </a> to go back to home page.';
                $data['error_message'] = $error_message;
            } else {

                /* skm-> Fetch Details of refral and refrance for email start */

                $course_name = $this->user_model->course_name($class_details->course_id);

                $user_details = array(
                    'email' => $user_result->registered_email_id,
                    'firstname' => strtoupper($user_result->first_name),
                    'lastname' => strtoupper($user_result->last_name),
                    'gender' => $user_result->gender,
                    'course_name' => $course_name,
                    'class_name' => $class_details->class_name,
                    'class_start' => $class_details->class_start_datetime,
                    'class_end' => $class_details->class_end_datetime,
                    'class_venu' => $class_details->classroom_venue_oth,
                    'r_email' => $r_user_result->registered_email_id,
                    'r_firstname' => strtoupper($r_user_result->first_name),
                    'r_lastname' => strtoupper($r_user_result->last_name),
                );


                $this->course_model->send_reg_enroll($user_details, 'BPEMAC');

//                      $this->send_referance_someone($user_details, 'BPEMAC');
//                       exit;
                //end 

                $message = 'You have successfully booked a seat.';

//                            if ($this->session->userdata('submit_status') == 'save_and_continue') {
//                                $message .= ' Please  click <a href="javascript:;" id="back_to_refer">here</a> to  register a new friend OR click  <a href="' . base_url() . '"> here </a> to go back to the home page.';
//                            } else {
//                                $message .= ' Please click <a href="' . base_url() . '">here</a> to go back to home page.';
//                            }
                $_POST = array(
                    'user_id' => $param['user_id'],
                    'class_id' => $param['class_id'],
                );
                $data['success_message'] = $message;
                $data['booking_ack'] = $this->_get_booking_ack_data();
                $data['page_title'] = 'Enrol';
                $data['main_content'] = 'enrol/message_status';
                $this->load->view('layout_public', $data);
            }

        
        } else {

            $course_details = $this->course_model->course_basic_details($class_details->course_id);
            $this->session->set_flashdata('error', 'Your friend is already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. Please select another class.');

            //return redirect('course_public/get_course_class_list');
            if ($param['user_id'] != '' && $param['refer_id'] != '') {
                return redirect('course_public/get_course_class_list/' . $param['user_id'] . '/' . $param['refer_id']);
            } else {
                return redirect('course_public/get_course_class_list');
            }
        }

       
    }

    /**



     * Function for class enroll db insert    



     * @return type

     */
    public function create_classenroll() {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $this->load->library('paypal_class');

        $p = new paypal_class();

        $action = $this->input->get('action');

        $error = '';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $submit = $this->input->post('submit');

            $param['user_id'] = $this->input->post('user_id');

            //Added for CR03

            $param['additional_remarks'] = $this->input->post('additional_remarks');

            $param['course_id'] = $this->input->post('course_id');

            $param['class_id'] = $this->input->post('class_id');

            $param['class_fees'] = $this->input->post('class_fees');

//            $param['discount_rate'] = $this->input->post('discount_rate');
//
//            $param['discount_amount'] = $this->input->post('discount_amount');
//            $param['gst_rate'] = $this->input->post('gst_rate');
//
//            $param['gst_amount'] = $this->input->post('gst_amount');

            $param['gst_onoff'] = $this->input->post('gst_onoff');

            $param['gst_rule'] = $this->input->post('gst_rule');

            // $param['net_due'] = $this->input->post('net_due');



            $data['class_details'] = $class_details = $this->course_model->get_class_details($param['class_id']);

            $course_details = $this->course_model->course_basic_details($class_details->course_id);

            /* check discount of class and individual user */
            $discount_total = 0;
            $param['discount_type'] = 'DISINDVI';
            $param['discount_rate'] = $class_details->class_discount;
            $param['indv_class_details'] = $indv_class_details = $this->course_model->get_indv_class_details($param['course_id'], $param['user_id']);
            $param['discount_amount'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
            if ($discount_total > 0) {
                $param['discount_type'] = 'DISCLASS';
                $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
            }

            if ($discount_total == 0) {
                if ($indv_class_details->discount_amount == 0 && $indv_class_details->discount_percent != 0) {
                    $param['discount_amount'] = $discount_total = round(($indv_class_details->discount_percent / 100) * $class_details->class_fees, 2);
                    $param['discount_type'] = 'DISINDVI';
                    $param['discount_rate'] = $indv_class_details->discount_percent;
                    $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
                }
                if ($indv_class_details->discount_amount != 0 && $indv_class_details->discount_percent == 0) {
                    $param['discount_amount'] = $discount_total = $indv_class_details->discount_amount;
                    $param['discount_type'] = 'DISINDVI';
                    $param['discount_rate'] = round(($indv_class_details->discount_amount / $class_details->class_fees) * 100, 2);
                    $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
                }
            }

            if ($indv_class_details == 0 && $discount_total == 0) {
                $param['discount_type'] = 'DISINDVI';
                $param['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
            }
            /* end */

            //$this->input->post('gst_amount');
            $param['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
            $param['gst_amount'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
            $param['net_due'] = $net_due = $feesdue + $totalgst;


            $meta_result = $this->meta_values->get_param_map();
            $param['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
            $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];

            $is_enrolled = $this->course_model->is_user_enrolled($param['user_id'], $param['class_id'], $class_details->course_id);





            if ($is_enrolled->num_rows() == 0) {



                if ($submit == 'book_now') {



                    //Update additional information- Added for CR03

                    $result1 = $this->course_model->update_additional_information($param);



                    $param['enrol_status'] = 'ENRLBKD';

                    $param['pay_status'] = 'NOTPAID';

                    $result = $this->course_model->create_new_enroll($param);

                    if ($result["status"] == FALSE) {

                        $error = 'We were not able to complete the enrollment in the class. Please try again later or contact your Administrator.';

                        $this->session->set_flashdata('error', $error);

                        return redirect('course_public/course_class_schedule/' . $class_details->course_id . '/' . $class_details->class_id);
                    } else {

                        $message = 'You have successfully booked a seat. Click <a href="' . base_url() . 'course_public/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';

                        $data['success_message'] = $message;



                        /* session start when user select course &class & register and then enroll in class successfully */
                        $this->load->model('user_model');


                        $resp = $this->user_model->register_login_process($param['user_id']);


                        if (!empty($resp)) {

                            $this->session->set_userdata('userDetails', $resp);
                        }//end



                        $message = 'You have successfully booked a seat. Click <a href="' . base_url() . 'course_public/course_class_schedule/' . $class_details->course_id . '">here</a> to go back to the Course-Class list.';

                        $data['success_message'] = $message;



                        // skm start fetch user details when register with course aand class
                        //$this->load->model('user_model');

                        $course_name = $this->user_model->course_name($param['course_id']);

                        $user_result = $this->user_model->r_userDetails($param['user_id']);

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

                        $this->course_model->send_reg_enroll($user_details, 'BPEMAC');

                        //end



                        $_POST = array(
                            'user_id' => $param['user_id'],
                            'class_id' => $param['class_id'],
                        );

                        $data['booking_ack'] = $this->_get_booking_ack_data();
                    }
                }
            } else {



                $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name;

                $this->session->set_flashdata('error', $error);

                return redirect('course_public/course_class_schedule/' . $class_details->course_id . '/' . $class_details->class_id);
            }
        }



        $data['page_title'] = 'Enrol';

        $data['main_content'] = 'enrol/message_status';

        $this->load->view('layout_public', $data);
    }

    /**



     * This method used to generate payment receipt pdf



     * @param type $user_id



     * @param type $class_id



     */
    public function payment_receipt_pdf($user_id, $class_id) {

        $payment_receipt_data = $this->_get_payment_receipt($user_id, $class_id);

        $this->load->helper('pdf_reports_helper');

        generate_payment_receipt($payment_receipt_data);
    }

    /**



     * This method used for payment receipt



     * @param type $user_id



     * @param type $class_id



     * @return type



     */
    private function _get_payment_receipt($user_id, $class_id) {

        $_POST = array('user_id' => $user_id, 'class_id' => $class_id);

        return $this->_get_booking_ack_data();
    }

    /**



     * This method used for booking ack



     * @return type



     */
    private function _get_booking_ack_data() {
        

        $tenant_id = TENANT_ID;

        $trainee_id = $this->input->post('user_id');

        $class_id = $this->input->post('class_id');

        $trainee_det = $this->course_model->get_user_data($trainee_id);

        $trainee_name = $trainee_det->first_name . ' ' . $trainee_det->last_name;

        $trainee = ($trainee_det->gender == 'MALE') ? 'Mr. ' . $trainee_name : 'Ms. ' . $trainee_name;

        $class_details = $this->course_model->get_class_details($class_id);

        $meta_map = $this->meta_values->get_param_map();

        $ClassLoc = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_map[$class_details->classroom_location];

        $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $course_manager = $this->course_model->get_managers($course_details->crse_manager);

        $length = stripos($course_manager, ', ');

        $coursemanager = (!empty($length)) ? substr($course_manager, 0, $length) : $course_manager;

        $tenant_details = $this->course_model->get_tenant_masters($tenant_id);



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

        $booking_details = $this->course_model->get_paydue_invoice($trainee_id, $class_id);



        $message2 = '<p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD \'<b>' . number_format($booking_details->total_inv_amount, 2, '.', '') . '</b>\' from <b>\'' . $trainee . '\' </b> for  <b>\'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'</b>. Mode of payment:<b>' . ONLINE . '</b></p>';

        /* skm code start for remark.
          reporting time skm start bcoz of sam request for AOP(67) on 18-may-17 */
        $time = strtotime($class_details->class_start_datetime);
        $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
        if ($class_details->course_id == 67 || $class_details->course_id == 121) {
            $li = "Report at center at $reporting_time to register for class";
        } else {
            $li = "Report at center at 8:30 AM to register for class";
        }
        /* end */
        $message3 = '<strong>Remark *: </strong>
             <ol>
                            <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>' . $li . '</li>
                        </ol>';
        if(TENANT_ID == 'T20'){////added by shubhranshu due to points fow wablab
                $message3 = '<strong>Remark *: </strong>
             <ol>
                        
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>'.$li.'</li>
                        </ol>';
            }

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
            'message3' => $message3, //skm
            'loc' => $ClassLoc,
            'start_date' => date('M d, Y h:i A', strtotime($class_details->class_start_datetime)),
            'contact' => $contact_details,
            'book_no' => $booking_no,
            'book_date' => $booking_date,
            'tenant_det' => $tenant_details
        );
    }

    /**



     * This method used for booking acknowledge pdf



     * @param type $trainee_id



     * @param type $class_id



     */
    public function booking_acknowledge_pdf($trainee_id, $class_id) {

        $tenant_id = TENANT_ID;

        $trainee_det = $this->course_model->get_user_data($trainee_id);

        $trainee_name = $trainee_det->first_name . ' ' . $trainee_det->last_name;

        $trainee = ($trainee_det->gender == 'MALE') ? 'Mr. ' . $trainee_name : 'Ms. ' . $trainee_name;

        $class_details = $this->course_model->get_class_details($class_id);

        $meta_map = $this->meta_values->get_param_map();

        $ClassLoc = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_map[$class_details->classroom_location];

        $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $course_manager = $this->course_model->get_managers($course_details->crse_manager);

        $length = stripos($course_manager, ', ');

        $coursemanager = (!empty($length)) ? substr($course_manager, 0, $length) : $course_manager;







        $tenant_details = $this->course_model->get_tenant_masters($tenant_id);



        $tenant_details->tenant_country = $meta_map[$tenant_details->tenant_country];

        $courseLevel = $meta_map[$course_details->certi_level];

        $contact_details .= $tenant_details->contact_name;


        /* skm code start for remark.
          reporting time skm start bcoz of sam request for AOP(67) on 18-may-17 */
        $time = strtotime($class_details->class_start_datetime);
        $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
        if ($class_details->course_id == 67 || $class_details->course_id == 121) {
            $li = "Report at center at $reporting_time to register for class";
        } else {
            $li = "Report at center at 8:30 AM to register for class";
        }
        /* end */
        $message3 = '
             <ol>
                            <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>' . $li . '</li>
                        </ol>';


        if(TENANT_ID == 'T20'){////added by shubhranshu due to points fow wablab
                $message3 = '<strong>Remark *: </strong>
             <ol>
                        
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>'.$li.'</li>
                        </ol>';
            }

        if (!empty($tenant_details->tenant_contact_num)) {

            $contact_details .='(Phone: ' . $tenant_details->tenant_contact_num . ', ';
        }

        if (!empty($tenant_details->tenant_email_id)) {

            $contact_details .='Email Id: ' . $tenant_details->tenant_email_id . ')';
        }

        $contact_details = rtrim($contact_details, ', ');

        $data = '<br><table><tr><td>A seat has been temporarily booked. Please pay the class fees on or before the class start date.

            Temporary booking for <strong>' . $trainee . '</strong> for \'Course: ' . $course_details->crse_name . ', Class: ' . $class_details->class_name . ', Certificate Code: ' . $courseLevel . '\'.<br><br>

            <strong>Class Start Date:</strong>

            ' . date('M d, Y h:i A', strtotime($class_details->class_start_datetime)) . '

            <br><br>

            <strong>Location:</strong>

            ' . $ClassLoc . '<br><br>

            <strong>Contact Details: </strong>

            ' . $contact_details . '
                
              <br><br>

            <strong>Remark: </strong>

            ' . $message3 . '

            </td></tr></table>';

        $booking_details = $this->course_model->get_paydue_invoice($trainee_id, $class_id);

        if ($booking_details) {

            $booking_no = date('Y', strtotime($booking_details->inv_date)) . ' ' . $booking_details->invoice_id;

            $booking_date = date('d/m/Y', strtotime($booking_details->inv_date));
        } else {

            $booking_no = date('Y') . ' ' . $trainee_id;

            $booking_date = date('d/m/Y');
        }

        $this->load->helper('pdf_reports_helper');

        generate_booking_acknowledge_pdf($data, $tenant_details, $booking_no, $booking_date);
    }

    /**



     * method to generate random key for captcha

     */
    private function _get_random_code() {

        $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';



        $shuffle = substr(str_shuffle($str), 0, 5);



        return $shuffle;
    }

    /**



     * method for form validation taxcode check   



     */
    public function taxcode_check($taxcode) {

        if ($taxcode) {

            $this->load->model('user_model');

            $exists = $this->user_model->check_taxcode($taxcode);

            if (!$exists) {

                $this->form_validation->set_message('taxcode_check', "NRIC/ FIN No. doesnot exists.");

                return FALSE;
            }

            return TRUE;
        }
    }

    /**



     * method for form validation taxcode check   



     */
    public function captcha_match($captcha) {

        if ($captcha) {

            $captcha_key = $this->session->userdata('captcha_key');

            if ($captcha_key != $captcha) {

                $this->form_validation->set_message('captcha_match', "[Incorrect text. Please try again.]");

                return FALSE;
            }

            return TRUE;
        }
    }

    /**



     * method for captcha match for enroll someone



     */
    public function captcha_match1($captcha=0) {

        $captcha = $this->input->post('captcha');

//        exit;

        if ($captcha) {

            $captcha_key = $this->session->userdata('captcha_key');

            if ($captcha_key != $captcha) {

                //$this->form_validation->set_message('captcha_match', "[Incorrect text. Please try again.]");

                echo 0;
            } else {

                echo 1;
            }
        }exit();
    }

    /**

     * Method to check valid taxcode



     */
    public function valid_taxcode() {



        $NRIC = $this->input->post('NRIC');

        $NRIC_ID = trim($this->input->post('NRIC_ID'));

        $valid = validate_nric_code($NRIC, $NRIC_ID);

        if ($valid == FALSE) {

            $this->form_validation->set_message('valid_taxcode', "Invalid NRIC/ FIN No. Code");

            return FALSE;
        }

        return TRUE;
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



            $tax_code = $this->input->post('NRIC_ID');
        }

        if ($country_of_residence == "USA") {



            $tax_code = $this->input->post('SSN');
        }

        if ($tax_code) {

            $this->load->model('user_model');

            $exists = $this->user_model->check_duplicate_user_taxcode($tax_code);

            if (!$exists) {

                $this->form_validation->set_message('check_unique_usertaxcode', "NRIC/ FIN No. " . $tax_code . " exists.");

                return FALSE;
            }

            return TRUE;
        }
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

                $this->load->model('user_model');

                $exist = $this->user_model->check_duplicate_user_name($user_name);

                if (!$exist) {

                    $this->form_validation->set_message('check_unique_username', "Username $user_name already exists.");

                    return FALSE;
                }

                return TRUE;
            }
        }
    }

    /**



     * Refer a friend validation    



     */
    private function _refer_friend_server_validation($key, $captcha = 0) {



        if ($key == 2) {



            $this->form_validation->set_rules('captcha', 'captcha', 'required|max_length[10]|callback_captcha_match');
        }

        if ($captcha == 1) {

            $this->form_validation->set_rules('captcha2', 'Captcha', 'required|callback_captcha_match');
        }
    }

    /**



     * This function to create captcha    



     */
    private function _create_captcha() {



        $this->load->helper('captcha');

        $captcha_data = array(
            'word' => $this->_get_random_code(),
            'img_path' => FCPATH .'captcha/',
            'img_url' => base_url() . 'captcha/',
            'img_width' => '114',
            'img_height' => '40',
            'font_path' => FCPATH .'assets/fonts/ATBramley-Medium.ttf',
            'expiration' => 7200
        );
//        print_r($captcha_data);
        $captcha = create_captcha($captcha_data);
//        print_r($captcha);exit;
        $this->session->set_userdata('captcha_file', $captcha['filename']);
        $this->session->set_userdata('captcha_key', $captcha['word']);

        return $captcha['image'];
    }

    public function class_member_check($course_id=0, $class_id=0) {
        
        unlink(FCPATH .'captcha/'.$this->session->userdata('public_captcha_file')); // added by shubhranshu to delete the captcha file 
        //$this->session->sess_destroy();
        $this->session->unset_userdata('userDetails');
        $data['page_title'] = 'Sign In';

        $data['course_id'] = $course_id;

        $data['class_id'] = $class_id;

        //if ($this->session->userdata('userDetails') != '') {

            //$data['main_content'] = 'course_public/payment_details';

          //  redirect(base_url() . 'course_public/enrol_friend');

//            redirect(base_url().'course_public/class_enroll');
        //} else {
            $data['captcha']=$this->generateCaptcha(); // added by shubhranshu for capctha entry
            $data['main_content'] = 'course_public/class_member_new'; // added by shubhranshu for new look of login

            // redirect(base_url().'course_public/enrol_friend');
       // }



        $this->load->view('layout_public', $data);
    }
    
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
    
    
    
    //public function register($class_id, $course_id, $data) {
    public function register() {
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        $this->session->unset_userdata('public_captcha_key');
        unlink(FCPATH .'captcha/'.$this->session->userdata('public_captcha_file')); // added by shubhranshu to delete the captcha file 
        $data['captcha'] = $this->generateCaptcha();
        $data['page_title'] = 'Add Trainee';
        $data['main_content'] = 'addtrainee';
        $this->load->view('layout_public', $data);
    }

    /**



     * function for class enrollment     



     * @param type $course_id

     * @param type $class_id

     * @return type

     */
    public function class_enroll($course_id, $class_id, $data = NULL) {

        $data['page_title'] = 'Class Enrollment';

        $this->load->library('form_validation');

        if (empty($course_id) || !is_numeric($course_id) || empty($class_id) || !is_numeric($class_id)) {

            redirect('course');
        }



        if ($this->input->server('REQUEST_METHOD') === 'POST') {

        ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
               $user_role = $this->session->userdata('userDetails')->role_id ?? '';
               if($user_role != ''){
                   if($user_role != 'TRAINE'){
                       redirect('login/administrator/'); ///// added by shubhranshu
                   }
               }
               ////////////////////////////////////////////////////////////////////////////////////////

            $submit = $this->input->post('submit');

            $this->_refer_friend_server_validation($key = 2);



            if ($this->form_validation->run() == TRUE) {



                $this->session->unset_userdata('captcha_key');

                $taxcode = $this->input->post('taxcode');

//                 $taxcode = 'QWERTY';

                if (!empty($taxcode)) {

                    $taxcode_details = $this->course_model->validate_taxcode_data(trim($taxcode));
                }

                $error = '';



                if (empty($taxcode_details)) {



                    $error = 'We have not been able to find your credentials in our system. '
                            . 'Kindly click <a href="' . base_url() . 'user/add_trainee/' . $course_id . '/' . $class_id . '">here</a> to register your account.';
                } else {



                    if (trim($taxcode_details->account_type) != 'TRAINE') {

                        $error = 'We have  been able to find your credentials in our system. '
                                . 'But you  are not registered as trainee. '
                                . 'Kindly get in touch with your administrator to register you as a Trainee, before proceeding class enrollments.';
                    } elseif (trim($taxcode_details->account_status) != 'ACTIVE') {



                        $error = 'We have found your credentials. But your account is not ACTIVE. '
                                . ' Kindly get in touch with your administrator to activate your account before proceeding with enrollment.';
                    } elseif (trim($taxcode_details->tenant_id) != TENANT_ID) {



                        $tenant_master_result_set = $this->course_model->get_tenant_name(trim($taxcode_details->tenant_id));



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



                if (!empty($error)) {



                    $this->session->set_flashdata('error', $error);

                    $this->session->set_userdata('prev_tax_code', $taxcode);

                    return redirect(current_url());
                } else {



                    $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);



                    $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

                    $is_enrolled = $this->course_model->is_user_enrolled($taxcode_details->user_id, $class_id, $course_id);

                    if ($is_enrolled->num_rows() > 0) {

                        $error = 'You are already enrolled in this class \'' . $course_details->crse_name . ' - ' . $class_details->class_name . '\'. '
                                . 'Please click <a href="' . base_url() . 'course_public/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';

                        $this->session->set_flashdata('error', $error);

                        $this->session->set_userdata('prev_tax_code', $taxcode);

                        return redirect(current_url());
                    } else {

                        $gender = ($taxcode_details->gender == 'FEMALE') ? 'Ms. ' : 'Mr. ';

                        $user_name = $gender . $taxcode_details->first_name . ' ' . $taxcode_details->last_name;

                        $user_name = rtrim($user_name, ' ');

                        $data['success'] = 'Welcome ' . $user_name . ', please proceed with enrollment (or) click <a href="' . base_url() . 'course_public/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';

                        $data['user_id'] = $taxcode_details->user_id;

                        //Added for CR03

                        $data['additional_remarks'] = $taxcode_details->additional_remarks;

                        $data['trainee_data'] = $taxcode_details;

                        $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);

                        $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);

                        $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();

                        $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;

                        $data['net_due'] = $net_due = $feesdue + $totalgst;

                        $meta_result = $this->meta_values->get_param_map();

                        $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';

                        $data['class_type'] = $meta_result[$course_details->class_type];

                        $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];

                        $data['main_content'] = 'course_public/payment_details';

                        return $this->load->view('layout_public', $data);
                    }
                }
            }
        }

//        echo $data['course_id'] = $course_id;
//        echo $data['class_id'] = $class_id;
//        print_r($data);

        $data['captcha'] = $this->_create_captcha();

        $data['main_content'] = 'course_public/class_enroll';

        $this->load->view('layout_public', $data);
    }

    public function class_enroll1($course_id, $class_id, $data = NULL) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Class Enrollment';

        $this->session->userdata('userDetails');

        $user_id = $this->session->userdata('userDetails')->user_id;
       
        $taxcode = $this->course_model->get_loggedin_taxcode($user_id);
        




        if (empty($course_id) || !is_numeric($course_id) || empty($class_id) || !is_numeric($class_id)) {

            redirect('course_public');
        }



        if (!empty($taxcode)) {

            $taxcode_details = $this->course_model->validate_taxcode_data(trim($taxcode));
        }

        $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);

        $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $gender = ($taxcode_details->gender == 'FEMALE') ? 'Ms. ' : 'Mr. ';

        $user_name = $gender . $taxcode_details->first_name . ' ' . $taxcode_details->last_name;

        $user_name = rtrim($user_name, ' ');

        $data['success'] = 'Welcome ' . $user_name . ', please proceed with enrollment (or) click <a href="' . base_url() . 'course_public/course_class_schedule/' . $course_id . '">here</a> to go back to the Course-Class list.';

        $data['user_id'] = $taxcode_details->user_id;

        //Added for CR03

        $data['additional_remarks'] = $taxcode_details->additional_remarks;

        $data['trainee_data'] = $taxcode_details;

        /* calculate discount based on class or individual skm start */
        // here we use discount total rather then discount amount
        $discount_total = 0;

        $data['discount_type'] = 'DISINDVI';

        $data['indv_class_details'] = $indv_class_details = $this->course_model->get_indv_class_details($course_id, $taxcode_details->user_id);

        $data['discount_rate'] = $class_details->class_discount;
        $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
        $feesdue = $class_details->class_fees;

        if ($discount_total > 0) {
            $data['discount_type'] = 'DISCLASS';
            $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        }

        if ($discount_total == 0) {
            if ($indv_class_details->discount_amount == 0 && $indv_class_details->discount_percent != 0) {
                $data['discount_total'] = $discount_total = round(($indv_class_details->discount_percent / 100) * $class_details->class_fees, 2);
                $data['discount_type'] = 'DISINDVI';
                $data['discount_rate'] = $indv_class_details->discount_percent;
                $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
            }
            if ($indv_class_details->discount_amount != 0 && $indv_class_details->discount_percent == 0) {
                $data['discount_total'] = $discount_total = $indv_class_details->discount_amount;
                $data['discount_type'] = 'DISINDVI';
                $data['discount_rate'] = round(($indv_class_details->discount_amount / $class_details->class_fees) * 100, 2);
                $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
            }
        }
        /* End */



//                            $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);

        $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();

        $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;

        $data['net_due'] = $net_due = $feesdue + $totalgst;

        $meta_result = $this->meta_values->get_param_map();

        $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';

        $data['class_type'] = $meta_result[$course_details->class_type];

        $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];

        $data['main_content'] = 'course_public/payment_details';

        return $this->load->view('layout_public', $data);





//            }
//        }
    }

    /**



     * This method used for class enroll payment



     * @param type $class_id



     * @param type $course_id



     * @param type $data



     * @return type



     */
    public function class_enroll_payment($class_id, $course_id, $data) {
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $paypal_data = $this->session->userdata('paypal_data');

        $taxcode_details = $this->course_model->get_user_data($paypal_data['user_id']);



        $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);

        $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $gender = ($taxcode_details->gender == 'FEMALE') ? 'Ms. ' : 'Mr. ';

        $user_name = $gender . $taxcode_details->first_name . ' ' . $taxcode_details->last_name;

        $user_name = rtrim($user_name, ' ');

        $data['user_id'] = $taxcode_details->user_id;

        $data['trainee_data'] = $taxcode_details;

        $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);

        $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);

        $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();

        $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;

        $data['net_due'] = $net_due = $feesdue + $totalgst;

        $meta_result = $this->meta_values->get_param_map();

        $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';

        $data['class_type'] = $meta_result[$course_details->class_type];

        $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];

        $data['main_content'] = 'course_public/payment_details';

        return $this->load->view('layout_public', $data);
    }
    
    public function all_course_class()
    {
        $data['page_title'] = 'Course Schedule List';
        $tenant_id = TENANT_ID;
        $field = $this->input->get('f');
        $order_by = $this->input->get('o');
        $baseurl = base_url() . 'course_public/all_course_class/';
        
        $records_per_page = RECORDS_IN_MAIN_PAGE;
//        $records_per_page = '5';
        $pageno = $this->uri->segment(3);

        if (empty($pageno)) {
            $pageno = 1;
        }
         $offset = ($pageno * $records_per_page);
        
        
        $data['tabledata'] = $this->course_model->get_all_course_class_list($tenant_id, $records_per_page, $offset, $field, $order_by);

        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'course_public/all_course_class/';
        $totalrows = $this->course_model->get_all_course_class_list_count($tenant_id, $records_per_page, $offset, $field, $order_by); 
         
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by,$search_value);
        
        
        $meta_result = fetch_all_metavalues();
        $values = $meta_result[Meta_Values::LANGUAGE];
        $meta_map = $this->meta_values->get_param_map();
        $class_values = $meta_result[Meta_Values::CLASSROOM_LOCATION];
        foreach ($values as $value) {

            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_values as $value) {

            $status_lookup_location[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($data['tabledata'] as $key=>$cl) {
            $data['class_count'][$cl['class_id']] = $booked = $this->course_model->get_course_class_count($cl['course_id'], $cl['class_id']);
            $available = $cl['total_seats'] - $booked;
            $available = ($available < 0) ? 0 : $available;
            $data['tabledata'][$key]['available'] = $available;
        }
        foreach ($data['tabledata'] as $key => $cl) {
               $data['tabledata'][$key]['crse_manager'] = $this->course_model->get_managers($cl['training_aide']);
        }
        foreach ($data['tabledata'] as $key => $cl) {
            $data['tabledata'][$key]['classroom_trainer'] = $this->course_model->get_trainers($cl['classroom_trainer']);
        }
        $data['status_lookup_language'] = $status_lookup_language;
        $data['status_lookup_location'] = $status_lookup_location;
        $data['course_name'] = $this->course_model->get_course_name($course_id);
        $data['main_content'] = 'course_public/all_course_class_schedule';
        $this->load->view('layout_public', $data);
    }
    ///////////added by shubhranshu for new requirement for elearning
    
    public function class_member_check_elearning($course_id = null, $class_id = null) {
       $SGPTIME = date('H');
       $SGPTIME =9;
       if ($SGPTIME >= 8 && $SGPTIME < 10) {  /////site will be only available during 8 to 10am
        
         $data['page_title'] = 'Enrollment';

         $data['course_id'] = $course_id;

         $data['class_id'] = $class_id;

         $data['user_id'] = $this->session->userdata('userDetails')->user_id;
         /* course class complete details */
         $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);
         $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

         $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
         $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
         $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
         $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
         $data['net_due'] = $net_due = $feesdue + $totalgst;
         $meta_result = $this->meta_values->get_param_map();
         $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
         $data['class_type'] = $meta_result[$course_details->class_type];
         $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
         //end


         $data['main_content'] = 'register_enroll_elearning';

         $this->load->view('layout_public_new', $data);
       }else{
            echo "<div style='font-size: 24px;border: 5px solid grey;padding: 100px;text-align: center;color: red;'>Sorry ! This page is only Available During 8:00AM to 10:00 AM Only.<div style='padding:18px;color:black'>".date('Y-m-d H:i:s')."SGT</div></div><br>";exit;
       }
    }
    
    
    public function check_nric_no_public() {
        extract($_POST);

        $taxcode = trim(($taxcode_nric));
        $course_id = trim(($course_id));
        $class_id = trim(($class_id));

        $res = $this->course_model->check_taxcode_exists_public($taxcode, $course_id, $class_id);
    }
    
    
    public function confirm_trainee_details($course_id = null, $class_id = null,$user_id_popup=null) {
        
         ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Enrollment';

        $data['course_id'] = $course_id = $this->input->post('course_id');

        $data['class_id'] = $class_id = $this->input->post('class_id');

        $data['user_id'] = $this->session->userdata('userDetails')->user_id;
        /* course class complete details */
        $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);
        $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);
        
        $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
        $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
        $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
        $data['net_due'] = $net_due = $feesdue + $totalgst;
        $meta_result = $this->meta_values->get_param_map();
        $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
        $data['class_type'] = $meta_result[$course_details->class_type];
        $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
        //end
        

        
        
        //$data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['user'] = $user;
        $data['privilage'] = $this->manage_tenant->get_privilage();//added by shubhranshu
        $this->load->library('form_validation');
        $data['page_title'] = 'Edit Trainee';
        

        $user_id=$this->input->post('user_id_popup');
        if ($user_id) { 
                       
            $data['trainee'] = $this->traineemodel->get_trainee_taxcode($user_id, TENANT_ID); 
            //$data['payment_status'] = $this->traineemodel->payment_status($data['trainee'][userdetails][user_id],$tenant_id);            
        }        
        if ($this->input->post('task') == 'update') 
        {
            $data['edit_tax_code'] = $code;
            $valid = TRUE;
            $country_of_residence = $this->input->post('country_of_residence');
            $this->form_validation->set_rules('pers_first_name', 'Firstname', 'required|max_length[100]');
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
//            if ($valid && $this->form_validation->run() == TRUE && $data['trainee'][userdetails]['tax_code']!=$tax_code ) {
//                $taxcodeStatus = $this->commonmodel->is_taxcode_exist($tax_code, $tenant_id);
//                if($taxcodeStatus){                    
//                    $failure_msg = 'Duplicate Tax Code. Please change the tax code.';
//                }
//            }
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
                     //user_activity(3,$user_id,$previous_data);
                    $this->session->set_flashdata('success_message', 'Trainee has been updated successfully');
                }
                redirect('course_public/enrolnow_elearning/'.$course_id.'/'.$class_id.'/'.$user_id.'/'.$NRIC_ID_MATCH);
            }else {
                $data['main_content'] = 'course_public/edit_trainee_details';
                $data['tax_error'] = ($data['tax_error'])?$data['tax_error']:$failure_msg;
                $this->load->view('layout_public', $data);
                return;
            }
        } 
     
        $data['main_content'] = 'course_public/edit_trainee_details';
        $this->load->view('layout_public', $data);
    }
    
    public function enrolnow_elearning($course_id = null, $class_id = null, $user_id=NULL,$nric=NULL){
          ////////////added by shubhranshu to move to admin page if the user is not a trainee////////////
        $user_role = $this->session->userdata('userDetails')->role_id ?? '';
        if($user_role != ''){
            if($user_role != 'TRAINE'){
                redirect('login/administrator/'); ///// added by shubhranshu
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////

        $data['page_title'] = 'Enrollment';

        $data['course_id'] = $course_id;

        $data['class_id'] = $class_id;

        $data['r_user_id'] =  $this->session->userdata('userDetails')->user_id;
        $data['nric'] = $nric;
        /* course class complete details */
        $data['class_details'] = $class_details = $this->course_model->get_class_details($class_id);
        $data['course_details'] = $course_details = $this->course_model->course_basic_details($class_details->course_id);

        $data['discount_total'] = $discount_total = round(($class_details->class_discount / 100) * $class_details->class_fees, 2);
        $data['feesdue'] = $feesdue = $class_details->class_fees - ($discount_total);
        $data['gst_rate'] = $gst_rate = $this->course_model->get_gst_current();
        $data['totalgst'] = $totalgst = ($course_details->gst_on_off == 1) ? round(($feesdue * $gst_rate) / 100, 2) : 0;
        $data['net_due'] = $net_due = $feesdue + $totalgst;
        $meta_result = $this->meta_values->get_param_map();
        $data['gst_label'] = $gst_label = ($course_details->gst_on_off == 1) ? 'GST applicable ' . '(' . number_format($data['gst_rate'], 2, '.', '') . '%)' : 'GST not applicable';
        $data['class_type'] = $meta_result[$course_details->class_type];
        $data['classloc'] = ($class_details->classroom_location == 'OTH') ? 'Others (' . $class_details->classroom_venue_oth . ')' : $meta_result[$class_details->classroom_location];
        //end
        $data['main_content'] = 'course_public/enrolnow_elearning';
        $this->load->view('layout_public', $data);
    }
    
    public function register_trainee($course_id = null, $class_id = null) {
        $nric_tax=$this->input->post('taxcode_nric');
        if($nric_tax == ''){
            redirect('course_public/class_member_check_elearning');
        }
        $SGPTIME = date('H');
        $SGPTIME =9;
        if ($SGPTIME >= 8 && $SGPTIME < 10) {  /////site will be only available during 8 to 10am
            $data['page_title'] = 'Trainee registration';

            $data['main_content'] = 'course_public/register_new';

            $this->load->view('layout_public_new', $data);
        }else{
            redirect('course_public/class_member_check_elearning');
        }
    }
    
    
    public function confirm_trainee_detail($course_id = null, $class_id = null,$user_id_popup=null) {
        $user_id=$this->input->post('user_id_popup') ?? $this->input->post('task');
        if($user_id == ''){
            redirect('course_public/class_member_check_elearning');
        }
         $SGPTIME = date('H');
         $SGPTIME =9;
        if ($SGPTIME >= 8 && $SGPTIME < 10) {  /////site will be only available during 8 to 10am
            $data['page_title'] = 'Update Trainee';

            $data['user_id'] = $this->session->userdata('userDetails')->user_id;
            /* course class complete details */
            //$data['sideMenuData'] = fetch_non_main_page_content();
            $user = $this->session->userdata('userDetails');
            $tenant_id = $user->tenant_id;
            $data['user'] = $user;
            $data['privilage'] = $this->manage_tenant->get_privilage();//added by shubhranshu
            $this->load->library('form_validation');
            $data['page_title'] = 'Edit Trainee';


            
            if ($user_id) { 

                $data['trainee'] = $this->traineemodel->get_trainee_taxcode($user_id, TENANT_ID); 
                //$data['payment_status'] = $this->traineemodel->payment_status($data['trainee'][userdetails][user_id],$tenant_id);            
            }        
            if ($this->input->post('task') == 'update') 
            {
                $data['edit_tax_code'] = $code;
                $valid = TRUE;
                $country_of_residence = $this->input->post('country_of_residence');
                $this->form_validation->set_rules('pers_first_name', 'Firstname', 'required|max_length[100]');
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
    //            if ($valid && $this->form_validation->run() == TRUE && $data['trainee'][userdetails]['tax_code']!=$tax_code ) {
    //                $taxcodeStatus = $this->commonmodel->is_taxcode_exist($tax_code, $tenant_id);
    //                if($taxcodeStatus){                    
    //                    $failure_msg = 'Duplicate Tax Code. Please change the tax code.';
    //                }
    //            }
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
                        $error= "<div style='color:red;font-weight: bold;text-align:center;padding: 9px;'>Unable to update Trainee.Please try again later</div>";
                        $this->session->set_flashdata('error', $error);
                    } else {
                         //user_activity(3,$user_id,$previous_data);
                        $error= "<div style='color:green;font-weight: bold;text-align:center;padding: 9px;'>Trainee has been updated successfully</div>";
                        $this->session->set_flashdata('error', $error);
                    }



                    redirect('course_public/class_member_check_elearning');
                }else {
                    $data['main_content'] = 'course_public/edit_trainee_details';
                    $data['tax_error'] = ($data['tax_error'])?$data['tax_error']:$failure_msg;
                    $this->load->view('layout_public_new', $data);
                    return;
                }
            } 

            $data['main_content'] = 'course_public/edit_trainee_details';
            $this->load->view('layout_public_new', $data);
        }else{
            redirect('course_public/class_member_check_elearning');
        }
    }
}

    