<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Manage Tenants Subsidy
 */
class Manage_Subsidy extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('manage_subsidy_model', 'manage_subsidy');        
        $this->view_folder = 'tenant/';
        $this->controller_name = 'manage_subsidy/';
    }
    /**
     * This function to list tenant subsidy
     */
    public function index() {    
        $data['sideMenuData'] = fetch_non_main_page_content(); // added by shubhranshu
        $records_per_page = RECORDS_PER_PAGE;        
        $pageno = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;        
        $offset = ($pageno * $records_per_page);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ts.last_modified_on';
        $tenant_id = ($this->input->get('tenant_id')) ? $this->input->get('tenant_id') : 0;
        $data['sort_order'] = $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';        
        $totalrows = $this->manage_subsidy->list_all_subsidy($tenant_id)->num_rows();
        $data['tabledata'] = $this->manage_subsidy->list_all_subsidy($tenant_id, $records_per_page, $offset, $field, $order_by)->result_array();
        $data['tenant_details'] = $this->manage_subsidy->get_tenant_details()->result_array();        
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "tenant_name=" . $this->input->get('tenant_name') ."&tenant_id=" . $this->input->get('tenant_id');
        $data['pagination'] = get_pagination($records_per_page, $pageno, base_url() . $this->controller_name, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['controllerurl'] = $this->controller_name;        
        $data['page_title'] = 'Manage Tenants';
        $data['main_content'] = $this->view_folder . 'subsidylist';
        $this->load->view('layout', $data);
    }
    /**
     * This Function add tenant subsidy.
     */
    public function new_subsidy() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if ($this->input->server('REQUEST_METHOD') === 'POST') {            
            $subsidy_type = $this->input->post('subsidy_type');
            $subsidy_amount = $this->input->post('subsidy_amount');           
            $tenant_id = $this->input->post('tenant_id');           
            $result = $this->manage_subsidy->add_new_subsidy($tenant_id, $subsidy_type, $subsidy_amount);
            if ($result == TRUE) {
                $this->session->set_flashdata("success", "Subsidy type created successfully.");
            } else {
                $this->session->set_flashdata("error", "Unable to create subsidy type. Please try again later.");
            }            
            redirect($this->controller_name);
        }
        $data['controllerurl'] = $this->controller_name."new_subsidy";
        $data['tenant_details'] = $this->manage_subsidy->get_tenant_details()->result_array();  
        $data['page_title'] = 'Add Subsidy Type';
        $data['type'] = 'Add';
        $data['action'] = 'Save';
        $data['main_content'] = $this->view_folder . 'add_subsidy';
        $this->load->view('layout', $data);
    }
    /**
     * This function edit the tenant subsidy
     * @param type $subsidy_id
     */
    public function edit_subsidy($subsidy_id=0) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if ($this->input->server('REQUEST_METHOD') === 'POST') {            
            $subsidy_type = $this->input->post('subsidy_type');
            $subsidy_id = $this->input->post('subsidy_id');
            $subsidy_amount = $this->input->post('subsidy_amount');           
            $tenant_id = $this->input->post('tenant_id');           
            $result = $this->manage_subsidy->edit_subsidy($tenant_id, $subsidy_id, $subsidy_type, $subsidy_amount);
            if ($result == TRUE) {
                $this->session->set_flashdata("success", "Subsidy type updated successfully.");
            } else {
                $this->session->set_flashdata("error", "Unable to update subsidy type. Please try again later.");
            }            
            redirect($this->controller_name);
        }
        $data['tenant_subsidy'] = $this->manage_subsidy->get_tenant_subsidy($subsidy_id)->row();
        $data['tenant_details'] = $this->manage_subsidy->get_tenant_details()->result_array();
        $data['controllerurl'] = $this->controller_name."edit_subsidy";
        $data['subsidy_id'] = $subsidy_id;
        $data['page_title'] = 'Edit Subsidy Type';
        $data['type'] = 'Edit';
        $data['action'] = 'Update';
        $data['main_content'] = $this->view_folder . 'add_subsidy';
        $this->load->view('layout', $data);
    }
    /**
     * Autocomplete to get all tenant name by tenant id
     */
    public function get_all_tenant() {
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');              
        $result = $this->manage_subsidy->get_alltenant($query_string);        
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->tenant_id,
                    'label' => $row->tenant_name . ' (' . $row->tenant_id . ')',
                    'value' => $row->tenant_name
                );
            }
        }
        echo json_encode($matches);
        exit();
    }
}
