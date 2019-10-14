<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Manage blocked nric 
 * created by shubhranshu
 */
class Manage_Block_Nric extends CI_Controller {
    public function __construct() {
        parent::__construct();
        
        $this->load->model('manage_tenant_model', 'manage_tenant');
        $this->load->model('meta_values', 'meta');
        $this->load->model('trainee_model', 'traineemodel');
        $this->load->model('tenant_model', 'tenantModel');
        $this->load->model('course_model', 'courseModel');
        
        $this->meta_map = $this->meta->get_param_map();
        $this->view_folder = 'tenant/';
        $this->controller_name = 'manage_block_nric/';
    }
    /*
     * /*  to show the blocked nric list //added by shubhranshu for client requirement on 21/03/2019 for restriction nric*/
    
    public function index() {
        // added by shubhranshu prevent controller access for other tenant accept ISV admin////
        if($this->session->userdata('userDetails')->tenant_id != 'ISV01'){
            $this->session->sess_destroy();exit;
        }// end..........////
        $data['sideMenuData'] = fetch_non_main_page_content(); // added by shubhranshu
        
        $records_per_page = RECORDS_PER_PAGE;
        $pageno = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;
        $offset = ($pageno * $records_per_page);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'id';
        $tenant_id = ($this->input->get('tenant_id')) ? $this->input->get('tenant_id') : 0;
        $data['sort_order'] = $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $totalrows = $this->manage_tenant->list_blocked_nric($tenant_id)->num_rows();
        $data['tabledata'] = $this->manage_tenant->list_blocked_nric($tenant_id, $records_per_page, $offset, $field, $order_by)->result_array();
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "tenant_name=" . $this->input->get('tenant_name') . "&tenant_id=" . $this->input->get('tenant_id');
        $data['pagination'] = get_pagination($records_per_page, $pageno, base_url() . $this->controller_name, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        $data['page_title'] = 'Manage Tenants';
        $data['privilage_for_all'] = $this->manage_tenant->get_privilage();
        $data['main_content'] = $this->view_folder . 'block_nric_list';
        $this->load->view('layout', $data);
        
    }
    
    
    /*  added by shubhranshu for client requirement on 21/03/2019 for restriction nric*/
    
    public function update_blocked_nric() {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $input = array();
            $where = array();
            $where['id'] = $this->input->post('nric_id');
            //echo $this->input->post('change_nric').' '.$this->input->post('nric');exit;
            if($this->input->post('nric') == $this->input->post('change_nric')){
                $this->session->set_flashdata("error", "Previous NRIC was same with Current one! Hence no changes required");
                redirect($this->controller_name);
            }
            $input = array(
                    'nric' => $this->input->post('change_nric')
                );
            $result = $this->manage_tenant->update_blocked_nric($input, $where);
            if ($result == TRUE) {
                $this->session->set_flashdata("success", "Blocked NRIC updated successfully.");
            } else {
                $this->session->set_flashdata("error", "Unable to update Blocked NRIC. Please try again later.");
            }
            $extra = '';
            redirect($this->controller_name);
      
        }
    }
   /*  added by shubhranshu for client requirement on 21/03/2019 for restriction nric*********************/ 
    public function add_blocked_nric() {
        $data = array(
                    'nric' => $this->input->post('nric_name')
                );
        $exist = $this->manage_tenant->exist_blocked_nric($data)->num_rows();
        if($exist > 0){
            $this->session->set_flashdata("error", "Oops!! NRIC Already Exist in the Blocked List.");
            redirect($this->controller_name);
        }
        $status = $this->manage_tenant->save_blocked_nric($data);
        if($status){
            $this->session->set_flashdata("success", "Blocked NRIC inserted successfully.");
        }else{
            $this->session->set_flashdata("error", "Unable to insert NRIC. Please try again later.");
        }
        
        redirect($this->controller_name);
    }
    //added by shubhranshu for popup fire privilage
    public function update_privilage(){
        $status =$this->input->post('status');
        $this->manage_tenant->update_privilage($status);
        echo $status;exit;
    }
    
    public function fetch_nric_restriction_log(){
        $data['sideMenuData'] = fetch_non_main_page_content();
        $records_per_page = 50;
        $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $offset = ($pageno * $records_per_page);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'id';
        $tenant_id = ($this->input->get('tenant_id')) ? $this->input->get('tenant_id') : 0;
        $data['sort_order'] = $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $totalrows = $this->manage_tenant->list_blocked_nric_logs($tenant_id)->num_rows();
        $data['tabledata'] = $this->manage_tenant->list_blocked_nric_logs($tenant_id, $records_per_page, $offset, $field, $order_by)->result_array();
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "tenant_name=" . $this->input->get('tenant_name') . "&tenant_id=" . $this->input->get('tenant_id');
        $data['pagination'] = get_pagination($records_per_page, $pageno, base_url() . $this->controller_name.fetch_nric_restriction_log.'/', $totalrows, $field, $order_by . '&' . $sort_link);
        $data['controllerurl'] = $this->controller_name.fetch_nric_restriction_log.'/';
        $data['meta_map'] = $this->meta_map;
        $data['page_title'] = 'Blocked NRIC Logs';
        $data['privilage_for_all'] = $this->manage_tenant->get_privilage();
        $data['main_content'] = $this->view_folder . 'nric_restriction_logs';
        $this->load->view('layout', $data);
    }

    
    public function get_blocked_nric_exist(){
        $nric_list_ids = $this->input->post('nrics');
        $operation = $this->input->post('operation');
        $blocked_nric_found = array();
        foreach ($nric_list_ids as $s){
           $taxcode = $this->manage_tenant->fetch_nric_code($s);
            if($taxcode != ''){
                $rows = $this->traineemodel->check_nric_restriction($taxcode,$operation);
                if($rows > 0){
                     $blocked_nric_found[] = $taxcode;
                }
            }
        }
        if(sizeof($blocked_nric_found) > 0){
            echo 1;
        }else{
           echo 0; 
        }
        
    }
}