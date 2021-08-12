<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*

 * This is the controller class for Course Use case features. 

 */

class Course extends CI_Controller {

    private $user;

    public function __construct() {

        parent::__construct();

        $this->user = $this->session->userdata('userDetails');
        $this->load->helper('metavalues_helper');
        $this->load->helper('common');
        $this->load->model('meta_values');
        $this->load->model('course_model', 'course');
        $this->load->model('class_model', 'classmodel');
        $this->load->library('form_validation');
        $this->user = $this->session->userdata('userDetails');
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
    }

    /*

     * This function loads the initial list view page for course.

     */

    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->user->tenant_id;

        $data['page_title'] = 'Course';

        $data['main_content'] = 'course/courselist';

        $export_url = '?';

        foreach ($_GET as $k => $v) {

            $export_url .="$k=$v&";
        }

        $export_url = rtrim($export_url, '&');

        $data['export_url'] = $export_url;

        $data['sort_link'] = $sort_link = "course_name=" . $this->input->get('course_name') . "&course_code=" . $this->input->get('course_code') . "&filter_status=" . $this->input->get('filter_status') . "&search_radio=" . $this->input->get('search_radio');

        $field = ($this->input->get('f')) ? $this->input->get('f') : 'last_modified_on';

        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $course_name = '';

        $course_code = '';

        $filter_status = '';

        if ($this->input->get('course_name'))
            $course_name = $this->input->get('course_name');

        if ($this->input->get('course_code'))
            $course_code = $this->input->get('course_code');

        if ($this->input->get('filter_status'))
            $filter_status = $this->input->get('filter_status');

        $totalrows = $this->course->get_all_course_count_by_tenant_id($tenant_id, $course_name, $course_code, $filter_status);

        $records_per_page = RECORDS_PER_PAGE;

        $baseurl = base_url() . 'course/';

        $pageno = $this->uri->segment(2);

        if (empty($pageno)) {

            $pageno = 1;
        }

        $offset = ($pageno * $records_per_page);

        $data['tabledata'] = $this->course->list_all_course_by_tenant($tenant_id, $records_per_page, $offset, $field, $order_by, $course_name, $course_code, $filter_status);

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'course/';

        $this->load->helper('pagination');

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);

        $data['courses'] = $this->course->get_course_list($tenant_id);

        $data['filter_status'] = fetch_metavalues_by_category_id(Meta_Values::COURSE_STATUS);

        $values = fetch_metavalues_by_category_id(Meta_Values::COURSE_STATUS);

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

     * This function loads the empty Add course form.

     */

    public function add_new_course() {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->user->tenant_id;

        $data['page_title'] = 'Add New Course';

        $data['main_content'] = 'course/addnewcourse';

        $this->load->view('layout', $data);
    }

    /**

     * This method creates a new course for the tenant

     */
    public function create_new_course_by_tenant() {

        $tenant_id = $this->user->tenant_id;

        $user_id = $this->user->user_id;

        $this->form_validation->set_rules('course_name', 'Course Name', 'required');

        $this->form_validation->set_rules('languages[]', 'Language', 'required');

        $this->form_validation->set_rules('course_types', 'Course Type', 'required');

        $this->form_validation->set_rules('class_types', 'Class Type', 'required');

        $this->form_validation->set_rules('gst_rules', 'Gst Rules', 'required');

        $this->form_validation->set_rules('subsidy', 'Subsidy', 'required');

        $this->form_validation->set_rules('course_duration', 'Course Duration', 'required');

        $this->form_validation->set_rules('course_reference_num', 'Course Reference Number', 'required');

        //$this->form_validation->set_rules('external_reference_number', 'External Course Reference Number', 'required');

        //$this->form_validation->set_rules('crse_admin_email', 'course admin email', 'required');

        $this->form_validation->set_rules('course_competency_code', 'Course Competency Code', 'required');

        $this->form_validation->set_rules('certification_code', 'Certification Code', 'required');

        $this->form_validation->set_rules('course_manager[]', 'Course Manager', 'required');
        $this->form_validation->set_rules('default_commission_rate', 'Default Commission Rate', 'required');

        $this->form_validation->set_rules('sales_executives[]', 'Sales Executives', 'required');

        $this->form_validation->set_rules('sales_exec_commission_rates[]', 'Commission Rates', 'required');

        if ($this->form_validation->run() == TRUE) {

            $course_id = $this->course->create_new_course_by_tenant($tenant_id, $user_id);

            if ($course_id) {

                if (!empty($_FILES['course_icon']['name'])) {

                    $this->load->helper('upload_helper');

                    $icon_name = str_ireplace(' ', '_', $this->input->post('course_name'));

                    $icon_name = $icon_name . '_' . $course_id;

                    $icon_data = upload_image('uploads/images/course', $icon_name, 'course_icon');

                    if ($icon_data['status']) {

                        $icon_path = $icon_data['image']['system_path'] . '/' .
                                $icon_path .= $icon_path . $icon_data['image']['raw_name'] . '_thumb' . $icon_data['image']['file_ext'];

                        $this->course->save_course_file_path($course_id, $icon_path, 'crse_icon');
                    } else {

                        $this->session->set_flashdata("error", $icon_data['error']);

                        $this->add_new_course();
                    }
                }

                if (!empty($_FILES['userfile']['name'])) {

                    $this->load->helper('upload_helper');

                    $file_name = str_ireplace(' ', '_', $this->input->post('course_name'));

                    $file_name = $file_name . '_' . $course_id;

                    $file_data = upload_file('uploads/files/course', $file_name);

                    if ($file_data['status']) {

                        $file_path = 'uploads/files/course/' . $file_data['file']['file_name'];

                        $this->course->save_course_file_path($course_id, $file_path, 'crse_content_path');
                    } else {

                        $this->session->set_flashdata("error", $file_data['error']);

                        $this->add_new_course();
                    }
                }

                $this->session->set_flashdata("success", "Course has been created successfully.");
            } else {

                $this->session->set_flashdata("error", "Unable to create course. Please try again later.");
            }

            redirect("course");
        } else {

            $this->add_new_course();
        }
    }

    /**

     * This method exporting excel file for course with page fields

     */
    public function export_course_page() {

        $tenant_id = $this->user->tenant_id;

        $query = $this->course->get_course_list_export($tenant_id);

        $this->load->helper('export_helper');

        export_course_page($query);
    }

    /**

     * This method exporting excel file for course sales executive page.

     */
    public function export_sales_rate_page_filed() {

        $tenant_id = $this->user->tenant_id;

        $data = $this->course->get_sales_rate_export($tenant_id);

        if ($data) {

            $excel_titles = array('Course Id', 'Course Code/ Name', 'Sales Executive Name', 'Commission Rate');

            $excel_data = array();

            for ($i = 0; $i < count($data); $i++) {

                $excel_data[$i][] = $data[$i]->course_id;

                $excel_data[$i][] = $data[$i]->crse_name;

                $excel_data[$i][] = $data[$i]->first_name . " " . $data[$i]->last_name;

                $excel_data[$i][] = $data[$i]->commission_rate . "%";
            }

            $excel_filename = 'sales_commission_rate.xls';

            $excel_sheetname = 'Commission List';

            $heading = 'Sales Commission Rate Page ' . '(As on ' . date('d/m/Y') . ')';

            $this->load->helper('export_helper');

            export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $heading);
        } else {

            $this->sales_commission_rate();
        }
    }

    /**

     * This method exporting excel file for course with All fields

     */
    public function export_course_full() {

        $tenant_id = $this->user->tenant_id;

        $query = $this->course->get_course_list_export($tenant_id);

        $this->load->helper('export_helper');

        export_course_full($query);
    }

    /*

     * This function loads the View course form.

     */

    public function view_course($course_id) {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $data['course_data'] = $course_data = $this->course->get_course_detailse($course_id);

        if ($course_data->copied_from_id) {

            $data['course_data']->copy_reason = (substr(trim($course_data->copy_reason), 0, 6) != 'OTHERS') ? $this->course->get_metadata_on_parameter_id($course_data->copy_reason) : substr($course_data->copy_reason, 7);
        }

        $data['sales_exec'] = $this->course->get_sales_exec_detailse($course_id);

        $tenant_id = $this->user->tenant_id;

        $export_url = '?course_id=' . $course_id . '&';

        foreach ($_GET as $k => $v) {

            $export_url .="$k=$v&";
        }

        $export_url = rtrim($export_url, '&');

        $data['export_url'] = $export_url;

        $data['sort_link'] = $sort_link = "class_status=" . $this->input->get('class_status');

        $field = ($this->input->get('f')) ? $this->input->get('f') : 'class_id';

        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'desc';

        $records_per_page = RECORDS_PER_PAGE;

        $pageno = ($this->uri->segment(4)) ? $this->uri->segment(4) : 1;

        $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';

        $offset = ($pageno * $records_per_page);

        $data['controllerurl'] = "course/view_course/$course_id/";

        $class_id = '';

        $totalrows = $this->classmodel->get_all_class_count_by_tenant_id($tenant_id, $course_id, $class_id, $class_status);

        $data['tabledata'] = $this->classmodel->list_all_class_by_tenant_id($tenant_id, $records_per_page, $offset, $field, $order_by, $course_id, $class_id, $class_status);

        $data['sort_order'] = $order_by;

        $data['course_id'] = $course_id;

        $this->load->helper('pagination');

        $baseurl = base_url() . "course/view_course/$course_id/";

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);

        $data['page_title'] = 'View Course';

        $data['main_content'] = 'course/viewcourse';

        $this->load->view('layout', $data);
    }

    /*

     * This Method for course code auto complete

     */

    public function get_course_list_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $result = $this->course->course_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    /*

     * This Method for course name auto complete in edit course.

     */

    public function get_course_name_list_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $result = $this->course->course_name_list_autocomplete($query_string, 'course');

        print json_encode($result);

        exit;
    }

    /*

     * This Method for course name auto complete in copy course.

     */

    public function get_copy_course_name_list_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $result = $this->course->course_name_list_autocomplete($query_string, 'copy');

        print json_encode($result);

        exit;
    }

    /*

     * This Method for course name auto complete in copy course.(remove once (course id removed))

     */

    public function get_course_name_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $result = $this->course->course_name_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    /**

     * get course name for auto-complete help

     */
    public function get_course_name_copy_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $result = $this->course->course_name_copy_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    /*

     * This function copy's one course to another course.

     */

    public function course_view() {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $data['page_title'] = 'Course';

        $form_style_attr = ' style="display:none;"';

        $data['main_content'] = 'course/copycourse';

        $this->load->view('layout', $data);
    }

    /*

     * This function is use for iframe genration.

     */

    public function wedgit() {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->user->tenant_id;

        $data['page_title'] = 'Widgets';

        $data['main_content'] = 'course/wedgit';


        $this->load->view('layout', $data);
    }

    /*

     * This function lists the sales executives and their commission rates.

     */

    public function sales_commission_rate() {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->user->tenant_id;

        $data['page_title'] = 'Sales Commission';

        $data['main_content'] = 'course/salescommrate';

        $export_url = '?';

        foreach ($_GET as $k => $v) {

            $export_url .="$k=$v&";
        }

        $export_url = rtrim($export_url, '&');

        $data['export_url'] = $export_url;

        $data['sort_link'] = $sort_link = "course_name=" . $this->input->get('course_name') . "&sales_executives=" . $this->input->get('sales_executives');

        $field = ($this->input->get('f')) ? $this->input->get('f') : 'course_id';

        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $records_per_page = RECORDS_PER_PAGE;

        $baseurl = base_url() . 'course/sales_commission_rate/';

        $pageno = $this->uri->segment(3);

        if (empty($pageno)) {

            $pageno = 1;
        }

        $offset = ($pageno * $records_per_page);

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'course/sales_commission_rate/';

        $this->load->helper('pagination');

        $totalrows = $this->course->get_sales_commission_list_count($tenant_id);

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);

        $data['table_data'] = $this->course->get_sales_commission_list($tenant_id, $records_per_page, $offset, $field, $order_by);

        $this->load->view('layout', $data);
    }

    /*

     * This function for viewing the course details in copy course.

     */

    public function copy_course() {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->user->tenant_id;

        $data['page_title'] = 'Copy Course';

        $data['main_content'] = 'course/copycourse';

        $form_style_attr = ' style="display:none;"';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $form_style_attr = ' style="display:inline;"';

            extract($_POST);

            $course_details = explode('(', $copy_course_name);

            $course_id = rtrim(end($course_details), ')');

            $data['course_data'] = $this->course->get_course_detailse($course_id);

            $data['sales_exec_array'] = $this->course->get_sales_exec_detailse_obj($course_id);
        }

        $data['form_style_attr'] = $form_style_attr;

        $this->load->view('layout', $data);
    }

    /*

     * This function for duplicating the course

     */

    public function duplicate_course() {

        extract($_POST);

        if ($course_id != '') {

            $result = $this->course->duplicate_course();

            if ($result == TRUE) {

                $this->session->set_flashdata("success", "Course has been copied successfully.");
            } else {

                $this->session->set_flashdata("error", "Unable to copy course. Please try again later.");
            }
        }

        redirect("course");
    }

    /*

     * Method for checking unique course name.

     */

    public function check_course_name() {

        $tenant_id = $this->user->tenant_id;

        $exists = $this->course->check_course_name($tenant_id);

        if ($exists) {

            echo 1;
        } else {

            echo 0;
        }

        return;
    }

    /*

     * This function for editing the course details.

     */

    public function edit_course($course_id = NULL) {
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->user->tenant_id;

        $data['page_title'] = 'Edit Course';

        $data['main_content'] = 'course/editcourse';

        $form_style_attr = ' style="display:none;"';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $form_style_attr = ' style="display:inline;"';

            extract($_POST);

            if ($course_id == NULL) {

                $course_details = explode('(', $search_course_name);

                $course_id = rtrim(end($course_details), ')');
            }

            $data['course_data'] = $course_data = $this->course->get_course_detailse($course_id);

            if ($course_data->copied_from_id) {

                $data['course_data']->copy_reason = (substr(trim($course_data->copy_reason), 0, 6) != 'OTHERS') ? $this->course->get_metadata_on_parameter_id($course_data->copy_reason) : substr($course_data->copy_reason, 7);
            }

            $data['enrol_count'] = $this->course->get_enrol_count($course_id);

            $class_language = $this->course->get_class_language($course_id);

            foreach ($class_language as $value):

                $data['class_language'][] = $value['class_language'];

            endforeach;

            $data['pre_requisite'] = explode(",", $data['course_data']->pre_requisite);

            $data['language'] = explode(",", $data['course_data']->language);

            $data['course_manager'] = explode(",", $data['course_data']->crse_manager);

            $data['sales_exec_array'] = $this->course->get_sales_exec_detailse_obj($course_id);
        }

        $data['form_style_attr'] = $form_style_attr;

        $this->load->view('layout', $data);
    }

    /*

     * This Methord used to getting the running/yet to start classes in edit course.

     */

    public function get_active_class() {

        $course_id = $this->input->post('course_id');

        $res['running'] = $this->course->get_running_class($course_id);

        $res['yet_to_start'] = $this->course->get_yet_to_class($course_id);

        echo json_encode($res);
    }

    /**

     * This method editing the course for the tenant

     */
    public function edit_course_by_tenant() {

        $data['page_title'] = 'Edit Course';

        $tenant_id = $this->user->tenant_id;

        $user_id = $this->user->user_id;

        $this->form_validation->set_rules('course_name', 'Course Name', 'required');

        $this->form_validation->set_rules('languages[]', 'Language', 'required');

        $this->form_validation->set_rules('gst_rules', 'Gst Rules', 'required');

        $this->form_validation->set_rules('subsidy', 'Subsidy', 'required');

        $this->form_validation->set_rules('course_reference_num', 'Course Reference Number', 'required');

        $this->form_validation->set_rules('external_reference_number', 'External Reference Number', 'required');

        $this->form_validation->set_rules('crse_admin_email', 'Course Admin Email', 'required');

        $this->form_validation->set_rules('course_competency_code', 'Course Competency Code', 'required');

        $this->form_validation->set_rules('course_manager[]', 'Course Manager', 'required');
        $this->form_validation->set_rules('default_commission_rate', 'Default Commission Rate', 'required');
        $this->form_validation->set_rules('sales_executives[]', 'Sales Executives', 'required');

        $this->form_validation->set_rules('sales_exec_commission_rates[]', 'Commission Rates', 'required');

        if ($this->input->post('enrol_count') == 0) {

            $this->form_validation->set_rules('course_types', 'Course Type', 'required');

            $this->form_validation->set_rules('class_types', 'Class Type', 'required');

            $this->form_validation->set_rules('certification_code', 'Certification Code', 'required');
        }

        if ($this->form_validation->run() == TRUE) {

            $course_id = $this->input->post('course_id');
            $res = $this->course->get_course_details($course_id, $tenant_id);
            $previous_course_data = json_encode($res);

            $delete_image = $this->input->post('deleteimage') ? $this->input->post('deleteimage') : 'no';

            $course_id = $this->course->edit_course_by_tenant($tenant_id, $user_id);

            $this->load->helper('upload_helper');

            if (!empty($_FILES['course_icon']['name']) && $course_id && $delete_image == 'no') {

                $icon_name = str_ireplace(' ', '_', $this->input->post('course_name'));

                $icon_name = $icon_name . '_' . $course_id;

                $icon_data = upload_image('uploads/images/course', $icon_name, 'course_icon');

                if ($icon_data['status']) {

                    $icon_path = $icon_data['image']['system_path'] . '/' .
                            $icon_path .= $icon_path . $icon_data['image']['raw_name'] . '_thumb' . $icon_data['image']['file_ext'];

                    $previous_thumb_path = $this->course->fetch_image_path_by_course_id($course_id, 'crse_icon');

                    $this->course->remove_previous_icon($previous_thumb_path);

                    $this->course->save_course_file_path($course_id, $icon_path, 'crse_icon');
                }
            } else if ($course_id && $delete_image == 'no') {

                $previous_thumb_path = $this->course->fetch_image_path_by_course_id($course_id, 'crse_icon');

                $this->course->remove_previous_icon($previous_thumb_path);

                $this->course->save_course_file_path($course_id, $icon_path, 'crse_icon');
            }

            if (!empty($_FILES['userfile']['name'])) {

                $this->load->helper('upload_helper');

                $file_name = str_ireplace(' ', '_', $this->input->post('course_name'));

                $file_name = $file_name . '_' . $course_id;

                $file_data = upload_file('uploads/files/course', $file_name);

                if ($file_data['status']) {

                    $file_path = 'uploads/files/course/' . $file_data['file']['file_name'];

                    $this->course->save_course_file_path($course_id, $file_path, 'crse_content_path');
                } else {

                    $this->session->set_flashdata("error", $file_data['error']);
                }
            }

            if ($course_id) {
                user_activity(4, $course_id, $previous_course_data);
                $this->session->set_flashdata("success", "Course has been updated successfully.");
            } else if (!$course_id) {

                $this->session->set_flashdata("error", "Unable to edit course. Please try again later.");
            }

            redirect("course");
        } else {

            $this->edit_course($this->input->post('course_id'));
        }
    }

    /*

     * This function deactivates the user selectd.

     */

    public function deactivate_course() {

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            foreach ($this->input->post() as $key => $value) {

                $$key = $value;
            }

            $this->load->library('form_validation');

            $this->form_validation->set_rules('reason_for_deactivation', 'reason for deactivation', 'required');

            if ($reason_for_deactivation == 'OTHERS') {

                $this->form_validation->set_rules('other_reason_for_deactivation', 'other reason for deactivation', 'required');
            }

            if ($this->form_validation->run() == TRUE) {

                $result = $this->course->deactivate_course($course_id_deactive);

                if ($result == TRUE) {

                    $tenant_id = $this->user->tenant_id;
                    $res = $this->course->get_course_details($course_id_deactive, $tenant_id);
                    $current_course_data = json_encode($res);
                    user_activity(4, $course_id_deactive, $current_course_data);
                    $this->session->set_flashdata('success', 'Course has been deactivated successfully!');
                } else {

                    $this->session->set_flashdata('error', 'Oops! Sorry, it looks like something went wrong.Please try again!.');
                }

                redirect('course');
            }
        }
    }

    /* This Function for updating sales executive commition rate.

     */

    public function update_sales_exec_rate() {

        $tenant_id = $this->user->tenant_id;

        $result = $this->course->update_sales_exec_rate($tenant_id);

        if ($result == TRUE) {

            $this->session->set_flashdata('success', 'Commission rate has been updated successfully!');
        } else {

            $this->session->set_flashdata("error", "Unable to update Commission rate. Please try again later.");
        }

        redirect('course/sales_commission_rate');
    }

    /* This Function for getting the sales exec based on course_id

     */

    public function get_sales_exec_json() {

        $tenant_id = $this->user->tenant_id;

        $result_array = $this->course->get_sales_exec_detailse_array($tenant_id);

        echo json_encode($result_array);
    }

    /**

     * download course content

     */
    public function download_course_content() {

        if (!$this->input->get('file_path')) {

            $this->course();
        }

        $file_name = str_ireplace(' ', '_', $this->input->get('file_name'));
        $file_name = $file_name . '.zip'; // added by shubhranshu to append zip file extension while download the file
        header("Content-Type: application/octet-stream");

        header("Content-Disposition: attachment;filename=" . $file_name);

        readfile(base_url() . $this->input->get('file_path'));

        exit();
    }

    /**

     * check enrolled status

     * @return type

     */
    public function check_enrolled() {

        $exists = $this->course->check_enrolled();

        if ($exists) {

            echo 1;
        } else {

            echo 0;
        }

        return;
    }

    /**

     * remove uploaded course zip file

     * @return boolean

     */
    public function remove_zip_file() {

        $course_id = $this->input->post('course_id');

        $previous_thumb_path = $this->course->fetch_image_path_by_course_id($course_id, 'crse_content_path');

        $this->course->remove_previous_icon($previous_thumb_path);

        $this->course->save_course_file_path($course_id, '', 'crse_content_path');

        return TRUE;
    }

    /**

     * this function to generate xls for course view page fields

     */
    public function export_course_class_full() {

        $tenant_id = $this->user->tenant_id;

        $result = $this->classmodel->get_class_list_export($tenant_id);

        $this->load->helper('export_helper');

        export_course_class_full($result);
    }

    //Added for DM - June 02 2015
    /**
     * This function loads the assessmenmt templates page
     */
    public function assessment_templates() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        //echo 'in here...';  
        $tenant_id = $this->user->tenant_id;
        $data['courses'] = $this->course->get_course_listbytenant($tenant_id);

        //check if search option  selected or default 
        $course_id = '';
        $filter_option = '';
        if ($this->input->get('course_name'))
            $course_id = $this->input->get('course_name');
        if ($this->input->get('filter_options'))
            $filter_option = $this->input->get('filter_options');

        if (strlen(trim($filter_option)) == 0)
            $filter_option = 'active';
        if (strlen(trim($course_id)) == 0)
            $course_id = $data['courses'][0]->course_id;

        //Pagination and Filter
        $records_per_page = RECORDS_PER_PAGE;
        $sort_by = ($this->input->get('f')) ? $this->input->get('f') : 'temp.template_title';
        $sort_order = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $baseurl = base_url() . 'course/assessment_templates/';
        $pageno = $this->uri->segment(3);


        if (empty($pageno)) {
            $pageno = 1;
        }
        //echo 'Course Id...'.$course_id; 
        $offset = ($pageno * $records_per_page);
        $tabledata = $this->course->get_template_list($tenant_id, $course_id, $filter_option, $records_per_page, $offset, $sort_by, $sort_order, 'loadall');
        $totalrows = $this->course->get_template_list($tenant_id, $course_id, $filter_option, $records_per_page, $offset, $sort_by, $sort_order, 'rowcount');

        //echo 'pageno: '.$pageno.' offset: '.$offset. ' $totalrows: '.$totalrows;

        $sort_link = $_SERVER['QUERY_STRING'];
        $data[sort_link] = $sort_link;
        $data['tabledata'] = $tabledata;
        $data['sort_order'] = $sort_order;
        $data['sort_by'] = $sort_by;
        $data['controllerurl'] = 'course/assessment_templates/' . $pageno;
        $data['controllerurl_link'] = 'course/assessment_templates/' . $pageno;
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $sort_by, $sort_order . '&' . $sort_link);

        $data['page_title'] = 'Course Assessment Templates';
        $data['main_content'] = 'course/assessment_templates';

        $data['sel_course'] = $course_id;
        $data['sel_status'] = $filter_option;

        $this->load->view('layout', $data);
    }

    /**
     *  This method inserts the assessment template into the database
     * Comes here on click of upload
     */
    public function insert_assmnt_template() {

        $result = $this->course->insert_assmnt_template($this->user->tenant_id, $this->user->user_id);
        if ($result == true) {
            $this->session->set_flashdata("success", "Assessment template uploaded successfully.");
        } else {
            $this->session->set_flashdata("error", "Unable to upload assessment template. Please try again later.");
        }

        redirect("course/assessment_templates?course_name=" . $_POST['course_id'] . "&filter_options=" . $_POST['filter_options']);
    }

    /**
     * This method is used to change the current PDF file to inactive and insert the new PDF version
     *  It will also set the PDF as active 
     */
    public function change_assmnt_template() {

        $result = $this->course->change_assmnt_template($this->user->tenant_id, $this->user->user_id);

        if ($result == true) {
            $this->session->set_flashdata("success", "Assessment template updated successfully.");
        } else {
            $this->session->set_flashdata("error", "Unable to update assessment template. Please try again later.");
        }

        redirect("course/assessment_templates?course_name=" . $_POST['course_id'] . "&filter_options=" . $_POST['filter_options']);
    }

    /**
     * This method is used change the PDF staus to inactive
     */
    public function remove_assmnt_template() {

        $result = $this->course->remove_assmnt_template($this->user->user_id);
        $this->session->set_flashdata("success", "Assessment template deactivated successfully.");
        redirect("course/assessment_templates?course_name=" . $_POST['course_id'] . "&filter_options=" . $_POST['filter_options']);
    }

    public function add_new_tpg_course() {

        $tenant_id = $this->tenant_id;
        
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Add New Course(TPG)';
        $data['main_content'] = 'course/addnewcourse_tpg';
        
        $course_reference_no = $this->input->post('course_reference_num');
        
        if($course_reference_no) {
            $result = $this->course->get_course_by_reference_no($tenant_id, $course_reference_no);
            if(isset($result)) {                
                $this->session->set_flashdata('error', "For the Course Reference Number - ".$course_reference_no.", course is already registered on TMS with name ".$result);
                
                redirect('course/add_new_tpg_course');
            }
        }
        
        if($this->input->post('course_reference_num')) {
            
            $crse_ref_no = $this->input->post('course_reference_num');

            $api_version = 'v1';
            $url = "https://" . TPG_URL . "/courses/directory/" . $crse_ref_no;            

            $request = $this->curl_request('GET', $url, "", $api_version);
            $tpg_response = json_decode($request);

            if ($tpg_response->status == 200) {
                //echo "<pre>".print_r($tpg_response, true)."</pre>";
                $data['tpg_response'] = $tpg_response;
                $data['course_name_val'] = $tpg_response->data->courses[0]->title;
                $data['external_reference_number_val'] = $tpg_response->data->courses[0]->externalReferenceNumber;
                $data['course_duration_val'] = $tpg_response->data->courses[0]->totalTrainingDurationHour;
                $data['course_description_val'] = $tpg_response->data->courses[0]->description;
                $data['crse_admin_email_val'] = $tpg_response->data->courses[0]->contactPerson[0]->email->full;
                $data['course_competency_code_val'] = $tpg_response->data->courses[0]->code;
                                                
            } else {
                if ($tpg_response->status == 400) {
                    $this->session->set_flashdata('error', $tpg_response->error->code.' - '.$tpg_response->error->message);
                } elseif ($tpg_response->status == 403) {
                    $this->session->set_flashdata('error', $tpg_response->error->code.' - '.$tpg_response->error->message);
                } elseif ($tpg_response->status == 404) {
                    $this->session->set_flashdata('error', $tpg_response->error->code.' - '.$tpg_response->error->message);
                } elseif ($tpg_response->status == 500) {
                    $this->session->set_flashdata('error', $tpg_response->error->code.' - '.$tpg_response->error->message);
                } else {
                    $this->session->set_flashdata('error', "TPG is not responding. Please, check back again.");
                }
                redirect('course/add_new_tpg_course');
            }
        }
        
        $this->load->view('layout', $data);
    }

    // Modified by abdulla for dynamic pem files.
    public function curl_request($mode, $url, $encrypted_data, $api_version) {

        $tenant_id = $this->tenant_id;

        $pemfile = "/var/www/newtms/assets/certificates/" . $tenant_id . "/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/" . $tenant_id . "/key.pem";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $mode,
            CURLOPT_SSLCERT => $pemfile,
            CURLOPT_SSLCERTTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_POSTFIELDS => $encrypted_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization:  ",
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "x-api-version: $api_version"
            ),
        ));

        $response = curl_exec($curl);
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {

            return $response;
        }
        curl_close($curl);
    }

}
