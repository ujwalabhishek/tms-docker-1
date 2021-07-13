<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



/*

 * This is the controller class for Internal user Use case all features. 

 */

class Activity_log extends CI_Controller {

        private $user;

    public function __construct() {



        parent::__construct();

        //$this->load->model('Internal_User_Model', 'internaluser');

        $this->load->model('activity_log_model', 'activitylog');

        $this->load->model('meta_values');

        $this->load->model('common_model', 'commonmodel');

        $this->load->helper('common');

        $this->load->helper('metavalues_helper');

        $this->load->helper('url');

        $this->load->helper('pagination');

        $this->load->library('bcrypt');

         $this->user = $this->session->userdata('userDetails');

        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;    

    }

    

    /* This function get all the record of activity log skm start */

       public function index(){

        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->tenant_id;

        extract($_GET);

        $data['course_list'] = $course_list = $this->activitylog->get_course_list($tenant_id);// get all course

        if($crs){

            $course_classes = $this->activitylog->get_course_class($tenant_id, $crs);

            $data['classes'] = $course_classes;

        }

        $export_url = '';

        $sort_url = '';

        if (!empty($_GET)) {

            $export_url = '?';

            foreach ($_GET as $k => $v) {

                if (!empty($v)) {

                    $export_url .="$k=$v&";

                }

                if ($k != 'f' && $k != 'o') {

                    if (!empty($v)) {

                        $sort_url .="$k=$v&";

                    }

                }

            }

        }

        $export_url = rtrim($export_url, '&');

        $sort_url = rtrim($sort_url, '&');

        $data['export_url'] = $export_url;

        $data['sort_url'] = '?' . $sort_url;

        $module = ($this->input->get('module')) ? $this->input->get('module') : '';

        $user_id = ($this->input->get('user_id')) ? $this->input->get('user_id') : '';

        $com_id = ($this->input->get('com_id')) ? $this->input->get('com_id') : '';

        $invid = ($this->input->get('invid')) ? $this->input->get('invid') : '';

        $inv_taxcode = ($this->input->get('inv_taxcode')) ? $this->input->get('inv_taxcode') : '';

        $crs = ($this->input->get('crs')) ? $this->input->get('crs') : '';

        $cls_id = ($this->input->get('cls_id')) ? $this->input->get('cls_id') : '';

        $cls_name = ($this->input->get('cls_name')) ? $this->input->get('cls_name') : '';

        $account_type = ($this->input->get('account_type')) ? $this->input->get('account_type') : '';

        $pass = ($this->input->get('pass')) ? $this->input->get('pass') : '';

        $field = ($this->input->get('f')) ? $this->input->get('f') : 'at.trigger_datetime';

        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'desc';

        

        $records_per_page = RECORDS_PER_PAGE;

        $baseurl = base_url() . 'activity_log/';

         $pageno = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;

        

        $offset = ($pageno * $records_per_page);

        $data['tenant'] = $tenant_id;

        $this->db->cache_on();

        $tabledata = $this->activitylog->get_activity_list_by_tenant_id($tenant_id,$records_per_page, $offset, $field, $order_by,$module, $user_id,$com_id,$invid,$inv_taxcode,$crs,$cls_id,$cls_name,$account_type,$pass);



        //$tabledata = $this->classtraineemodel->list_all_classtrainee_by_tenant_id($tenant_id, $records_per_page, $offset, $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);

        $totalrows = $this->activitylog->get_activity_log_count_by_tenant_id($tenant_id,$module, $user_id,$com_id,$invid,$user_id,$inv_taxcode,$crs,$cls_id,$cls_name,$account_type,$pass);

        $this->db->cache_off();

        $data['tabledata'] = $tabledata;

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'activity_log/';

        $this->load->helper('pagination');

        if ($sort_url) 

        {

            $pag_sort = $order_by . '&' . $sort_url;

        } 

        else 

        {

            $pag_sort = $order_by;

        }

        $data['activity_module'] = $this->activitylog->get_module($tenant_id);// get all module name with activity

        $data['company_list'] = $this->activitylog->get_company_list($tenant_id);// get all company

        

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $pag_sort);

        $data['page_title'] = 'Activity Log';

        $data['main_content'] = 'internaluser/activity';

        $this->load->view('layout', $data);

         

    }

    

    public function index2()

    { 

        

        $x = extract($_GET);

        $user = $this->session->userdata('userDetails');

        $tenant_id = $user->tenant_id;

        $data['page_title'] = 'Activity Log';

        $export_url = '?';

        foreach ($_GET as $k => $v) {

            $export_url .="$k=$v&";

        }

        $export_url = rtrim($export_url, '&');

        $data['export_url'] = $export_url;

      

        $data['sort_link'] = $sort_link = "module=" .$this->input->get('module') .

                "&staff_id=" . $this->input->get('staff_id'). "&internal_staff=" . $this->input->get('internal_staff').

                "&com_id" . $this->input->get('com_id')."&company" . $this->input->get('company') .

                "&invid=" . $this->input->get('invid')."&inv_taxcode=" . $this->input->get('inv_taxcode').

                "&crse_id=" . $this->input->get('crse_id')."&course=" . $this->input->get('course').

                //"&crse_name=" . $this->input->get('crse_name')."&cls_id=" . $this->input->get('cls_id')."&cls_name=" . $this->input->get('cls_name');

                "&crs=" . $this->input->get('crs')."&cls_name=" . $this->input->get('cls_name').

                "&account_type=".$this->input->post('account_type').

                "&inv=".$this->input->post('inv');



        $totalrows = $this->activitylog->get_activity_log_count_by_tenant_id($tenant_id); 

       

        $field = ($this->input->get('f')) ? $this->input->get('f') : 'at.trigger_datetime';

        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';

        $records_per_page = RECORDS_PER_PAGE; 

        $baseurl = base_url() . 'activity_log/'; 

        $pageno = $this->uri->segment(2); 

      

        if (empty($pageno)) {

            $pageno = 1;

        }

         $offset = ($pageno * $records_per_page);



        $data['tabledata'] = $this->activitylog->get_activity($tenant_id,$records_per_page, $offset, $field, $order_by);

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'activity_log/';



        

        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);

        

        $data['activity_module'] = $this->activitylog->get_module($tenant_id);// get all module name with activity

//        $data['tms_users'] = $this->activitylog->get_users($tenant_id,'INTUSR');// get all users

        $data['company_list'] = $this->activitylog->get_company_list($tenant_id);// get all company

       

//        $data['trainee_list'] = $this->internaluser->get_users('TRAINE',$type);// get all trainee list

        $data['course_list'] = $course_list = $this->activitylog->get_course_list($tenant_id);// get all course

        if($crs){

            $course_classes = $this->activitylog->get_course_class($tenant_id, $crs);

            $data['classes'] = $course_classes;

        }

      

        $data['main_content'] = 'internaluser/activity';

        $this->load->view('layout', $data);

    

    }

    

    /* This function retrive all data for activity list */

    public function activity_log_view($id,$module,$act_on)

    {

        $data['sideMenuData'] = fetch_non_main_page_content();
        
        $res = $this->activitylog->get_activity_details($id,$module,$act_on);

        $data['res'] = $res;
	
	$data['page_title'] = 'Activity Log View';

        $data['main_content'] = 'internaluser/activity_log_view';

        $this->load->view('layout', $data);

    }

        

    public function get_internalstaff_name_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->internalstaff_name_list_autocomplete($query_string);

        print json_encode($result);

        exit;

    }

    

    public function get_companyname_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->company_name_list_autocomplete($query_string);

        print json_encode($result);

        exit;

    }

    

    

    public function get_coursename_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->course_name_list_autocomplete($query_string);

        print json_encode($result);

        exit;

    }

    

    public function get_invtaxcode_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->invtaxcode_list_autocomplete($query_string);

        print json_encode($result);

        exit;

    }

    

    public function get_inv_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->inv_list_autocomplete($query_string);

        print json_encode($result);

        exit;

    }

    

    public function get_password_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->password_list_autocomplete($query_string);

        print json_encode($result);

        exit;

    }

    

    public function get_course_class_name_autocomplete() 

    {

        $query_string = htmlspecialchars($_GET['name_startsWith'], ENT_QUOTES, 'UTF-8'); 

        $query_string = trim($query_string);

        $result = $this->activitylog->course_class_list_autocomplete($query_string);

        

        print json_encode($result);

        exit;

    }

    

    public function get_course_class_name_json($course_id){

        $user = $this->session->userdata('userDetails');

        $tenantId = $user->tenant_id;

        $courseId = $this->input->post('course_id');

       

        $course_classes = $this->activitylog->get_course_class($tenantId, $courseId);

        $classes_arr = array();

        foreach ($course_classes as $k => $v) {

            $classes_arr[] = array('key' => $k, 'value' => $v);

        }

        echo json_encode($classes_arr);

        exit;

    }

    

}



