<?php

/*
 * Training Controller 
 * Author : Blessy Paul
 * use: Information about completed trainings,feedback etc
 */

class Trainings extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('courses_model');
        $this->load->model('meta_values_model');
        $this->load->helper('metavalues_helper');
    }

    /*
      Function to list completed tainings.
      Author: Blessy Paul
     */

    public function index() {
        if($this->session->userdata('userDetails')->user_id==""){
            redirect("course_public");
        }

        $data['page_title'] = 'Trainings Completed';
        $course_id = NULL;
        $totalrows = $this->courses_model->get_completed_class_list_count();
        $field = $this->input->get('f');
        $order_by = $this->input->get('o');
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'trainings/';
        $pageno = $this->uri->segment(2);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'trainings/';
        $class_lists = $this->courses_model->get_completed_class_list($records_per_page, $offset, $field, $order_by);

        $data['class_list'] = $class_lists;
        $this->load->helper('pagination');

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);
        $course_name_list = $this->courses_model->get_course_name($course_id);
        foreach ($course_name_list as $value) {
            $status_lookup_course_name[$value['course_id']] = $value['crse_name'];
        }
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

        $data['feedback_status'] = $this->courses_model->get_feedback_status();
        $data['status_lookup_course_name'] = $status_lookup_course_name;
        $data['status_lookup_language'] = $status_lookup_language;
        $data['status_lookup_location'] = $status_lookup_location;
        $data['status_lookup_class_status'] = $status_lookup_class_status;
        $data['main_content'] = 'training/trainingscompleted';
        $this->load->view('layout_public', $data);
    }
    
    /* Get the trainner feedback skm start*/
    public function view_trainer_feedback() {

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

//        $course_id = $this->input->get('course_id');
//
//        $class_id = $this->input->get('class_id');
//
//        $user_id = $this->input->get('user_id');
        
        $course_id = $this->input->post('course_id');

        $class_id = $this->input->post('class_id');

        $user_id = $this->input->post('user_id');

        $data['heading'] = 'Trainer Feedback';

//        $data['trainee_name'] = $this->input->get('trainee_name');

        $data['tabledata'] = $this->courses_model->get_trainer_feedback_by_user_id( $course_id, $class_id, $user_id);

        $data['course_id'] = $course_id;

        $data['class_id'] = $class_id;

        $data['user_id'] = $user_id;

        $this->load->view('training/view_trainer_feedback', $data);

    }//end
    
    /* Print Trainer Feedback skm start*/
    public function print_trainer_feedback() {
         
        $this->load->helper('pdf_reports_helper');

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        $course_id = $this->input->get('course_id');

        $class_id = $this->input->get('class_id');

        $user_id = $this->input->get('user_id');

        $user_details = $this->courses_model->get_trainee_details($user_id);
        //print_r($user_details);

        $class_details = $this->courses_model->get_class_details_feedback( $class_id);

        $user_details->class_title = $class_details->class_name;

        $user_details->class_end_datetime = $class_details->class_end_datetime;

        $user_details->course_title = $this->courses_model->get_course_detailse($course_id)->crse_name;

        $feedbackData = $this->courses_model->get_trainer_feedback_by_user_id( $course_id, $class_id, $user_id);

        $feedbackData['CERTCOLDT']['feedback_answer'] = empty($feedbackData['CERTCOLDT']['feedback_answer']) ? '--' : $feedbackData['CERTCOLDT']['feedback_answer'];

        $feedbackData['SATSRATE']['feedback_answer'] = empty($feedbackData['SATSRATE']['feedback_answer']) ? '--' : $feedbackData['SATSRATE']['feedback_answer'];

        $feedbackData['CERTCOM1']['feedback_answer'] = empty($feedbackData['CERTCOM1']['feedback_answer']) ? '--' : $feedbackData['CERTCOM1']['feedback_answer'];

        $feedbackData['APPKNLSKL']['feedback_answer'] = empty($feedbackData['APPKNLSKL']['feedback_answer']) ? '--' : $feedbackData['APPKNLSKL']['feedback_answer'];

        $feedbackData['EXPJOBSCP']['feedback_answer'] = empty($feedbackData['EXPJOBSCP']['feedback_answer']) ? '--' : $feedbackData['EXPJOBSCP']['feedback_answer'];

        $feedbackData['RT3MNTHS']['feedback_answer'] = empty($feedbackData['RT3MNTHS']['feedback_answer']) ? '--' : $feedbackData['RT3MNTHS']['feedback_answer'];

        $feedbackData['DTCOMMEMP']['feedback_answer'] = empty($feedbackData['DTCOMMEMP']['feedback_answer']) ? '--' : $feedbackData['DTCOMMEMP']['feedback_answer'];

        $feedbackData['COMYTCOM']['feedback_answer'] = empty($feedbackData['COMYTCOM']['feedback_answer']) ? '--' : $feedbackData['COMYTCOM']['feedback_answer'];

        $feedbackData['COMMNTS']['feedback_answer'] = empty($feedbackData['COMMNTS']['feedback_answer']) ? '--' : $feedbackData['COMMNTS']['feedback_answer'];

        $tenant_details = $this->courses_model->get_tenant_details();

        $tenant_details->tenant_country = rtrim($this->courses_model->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');

        return traner_feedback_pdf($feedbackData, $tenant_details,$user_details);

    }//end

    /*
     * Added by  Blessy
     * function  for  loading feedbackform
     */

    public function give_feedback() {
        $data['class_id'] = $this->input->post('class_id');
        $data['course_id'] = $this->input->post('course_id');
        $user_id = $this->session->userdata('userDetails')->user_id;
        $data['feedback'] = $this->courses_model->get_trainee_feedback_by_user_id(TENANT_ID, $data['course_id'], $data['class_id'], $user_id);
        $this->load->view('training/give_feedback', $data);
    }

    /*
     * Added by  Blessy
     * function  for  saving feedbackform
     */

    public function save_feedback() {
        $class_id = $this->input->post('class_id');
        $course_id = $this->input->post('course_id');
        $data = $this->courses_model->save_feedback($class_id, $course_id);
        if ($data == true) {
            $this->session->set_flashdata('success', 'Your  feedback  is successfully submitted');
        } else {
            $this->session->set_flashdata('error', 'Sorry Try Again !');
        }
        redirect('trainings');
    }

    /*
     * Added by  Blessy
     * function  for  view feedbackform
     */

    public function view_feedback() {
        $class_id = $this->input->post('class_id');
        $course_id = $this->input->post('course_id');
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $data['tabledata'] = $this->courses_model->get_trainee_feedback_by_user_id($tenant_id, $course_id, $class_id, $user_id);
        $data['classdata'] = $this->courses_model->get_feedback_values($tenant_id, $user_id, $course_id, $class_id);
        $this->load->view('training/feedback', $data);
    }

    /*
     * Added by  Blessy
     * function  for  print feedbackform
     */

    public function print_feedback_form() {
        $this->load->helper('public_pdf_reports_helper');
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        return generate_feedback_pdf($tenant_id, $user_id, $course_id, $class_id);
    }

    /*

     * Added by Blessy
     * function  to load   certificate  modal */

    public function certi_collection() {
        $data['class_id'] = $this->input->post('class_id');
        $certificate_collectedon = $this->courses_model->get_certificate_coll_on($data['class_id']);
        $data['certificate_coll_on'] = $certificate_collectedon[0]->certificate_coll_on;
        $this->load->view('training/certification', $data);
    }

    /* Added by Blessy
     * function  to save  certificate collection date
     */

    public function save_certi_colln_date() {
        $data = $this->courses_model->save_certi_colln_date();
        if ($data == true) {
            $this->session->set_flashdata('success', 'Certificate collected date has been updated Successfully.');
        } else {
            $this->session->set_flashdata('error', 'Sorry Try Again !');
        }
        redirect('trainings');
    }

    /*  Function  for  loc pdf download
     *  Author:Blessy
        Date:13/11/14 */

    public function generate_certificate($class_id) {
        $user_id = $this->session->userdata('userDetails')->user_id;
        $tenant_id = TENANT_ID;
        $loc_details = $this->courses_model->get_loc_details($tenant_id, $class_id, $user_id);
        $this->load->helper('pdf_reports_helper');
        generate_loc_pdf($loc_details);
    }

}
