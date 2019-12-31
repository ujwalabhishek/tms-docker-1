<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Manage Tenants 
 */
class Manage_tenant extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('manage_tenant_model', 'manage_tenant');
        $this->load->model('meta_values', 'meta');
        
        $this->load->model('tenant_model', 'tenantModel');
        $this->load->model('course_model', 'courseModel');
        
        $this->meta_map = $this->meta->get_param_map();
        $this->view_folder = 'tenant/';
        $this->controller_name = 'manage_tenant/';
    }
    /*
     * This function to list all the tenant users
     */
    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $records_per_page = RECORDS_PER_PAGE;
        $pageno = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;
        $offset = ($pageno * $records_per_page);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'account_created_on';
        $tenant_id = ($this->input->get('tenant_id')) ? $this->input->get('tenant_id') : 0;
        $data['sort_order'] = $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $totalrows = $this->manage_tenant->list_all_tenants($tenant_id)->num_rows();
        $data['tabledata'] = $this->manage_tenant->list_all_tenants($tenant_id, $records_per_page, $offset, $field, $order_by)->result_array();
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "tenant_name=" . $this->input->get('tenant_name') . "&tenant_id=" . $this->input->get('tenant_id');
        $data['pagination'] = get_pagination($records_per_page, $pageno, base_url() . $this->controller_name, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        $data['page_title'] = 'Manage Tenants';
        $data['main_content'] = $this->view_folder . 'tenantlist';
        $this->load->view('layout', $data);
    }
    /**
     * This function to view individual tenant details
     */
    public function view($tenant_id) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if (empty($tenant_id)) {
            return show_404();
        }
        $data['tenant'] = $this->manage_tenant->get_tenant_details($tenant_id)->row();
        if (empty($data['tenant'])) {
            return show_404();
        }
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        $data['page_title'] = 'View Tenant';
        $data['main_content'] = $this->view_folder . 'viewtenant';
        $this->load->view('layout', $data);
    }
    /**
     * This function to add new tenant
     */
    public function add_new_tenant() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $this->load->helper('metavalues_helper');
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        $data['page_title'] = 'Add New Tenant';
        $data['countries'] = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
        $data['currencies'] = fetch_metavalues_by_category_id(Meta_Values::CURRENCIES);
        $data['states'] = array();
        $country = $this->input->post('country');
        if (!empty($country)) {
            $data['states'] = $this->manage_tenant->get_states($country);
        }
        $data['inv_names'] = array('' => 'Select', 'Tax Invoice' => 'Tax Invoice', 'Training Bill' => 'Training Bill');
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('userfile', 'Logo', 'callback_logo_validation');
            $this->form_validation->set_rules('tenant_name', 'Tenant Name', 'required');
            $this->form_validation->set_rules('address', 'Address', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('country', 'Country', 'required');
            $this->form_validation->set_rules('contact_num', 'Contact No.', 'required');
            $this->form_validation->set_rules('acti_start_date', 'Activation Start Date', 'required');
            $this->form_validation->set_rules('inv_name', 'Invoice Name', 'required');
            $this->form_validation->set_rules('currency', 'Currency', 'required');
            $this->form_validation->set_rules('country_use', 'Country of Use', 'required');
            $this->form_validation->set_rules('director_name', 'Director Name', 'required');
            $this->form_validation->set_rules('contact_name', 'Contact Name', 'required');
            $this->form_validation->set_rules('copyright', 'Copyright Text', 'required');
            if ($this->form_validation->run() == FALSE) {
                $data['main_content'] = $this->view_folder . 'addnew';
                return $this->load->view('layout', $data);
            } else {
                $logo = $this->logo_upload();
                if (is_array($logo)) {
                    $_POST['logo'] = $logo['file_name'];
                } 
                $result = $this->manage_tenant->create_tenant();
                if ($result == TRUE) {
                    $this->session->set_flashdata("success", "Tenant created successfully.");
                } else {
                    $this->session->set_flashdata("error", "Unable to create tenant. Please try again later.");
                }
                return redirect($this->controller_name);
            }
        }
        $data['main_content'] = $this->view_folder . 'addnew';
        $this->load->view('layout', $data);
    }
    /**
     * This function to update tenant
     */
    public function update_tenant() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        $tenant_id = $this->input->post('tenant_id');
        $data['tenant'] = $tenant_data = $this->manage_tenant->get_tenant_details($tenant_id)->row();
        $data['countries'] = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
        $data['currencies'] = fetch_metavalues_by_category_id(Meta_Values::CURRENCIES);
        $data['states'] = array();
        $country = $this->input->post('country');
        if (!empty($country)) {
            $data['states'] = $this->manage_tenant->get_states($country);
        }
        $data['inv_names'] = array('' => 'Select', 'Tax Invoice' => 'Tax Invoice', 'Training Bill' => 'Training Bill');
        $data['page_title'] = 'Edit Tenant';
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');
            if (!empty($_FILES['userfile']['name'])) {
                $this->form_validation->set_rules('userfile', 'Logo', 'callback_logo_validation');
            }
            $this->form_validation->set_rules('tenant_name', 'Tenant Name', 'required');
            $this->form_validation->set_rules('address', 'Address', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('country', 'Country', 'required');
            $this->form_validation->set_rules('director_name', 'Director Name', 'required');
            $this->form_validation->set_rules('contact_name', 'Contact Name', 'required');
            $this->form_validation->set_rules('contact_num', 'Contact No.', 'required');
            $this->form_validation->set_rules('acti_start_date', 'Activation Start Date', 'required');
            $this->form_validation->set_rules('inv_name', 'Invoice Name', 'required');
            $this->form_validation->set_rules('currency', 'Currency', 'required');
            $this->form_validation->set_rules('country_use', 'Country of Use', 'required');
            $this->form_validation->set_rules('copyright', 'Copyright Text', 'required');
            if ($this->form_validation->run() == FALSE) {
                $data['main_content'] = $this->view_folder . 'edit';
                return $this->load->view('layout', $data);
            } else {
                $acc_acti_end_date = ($this->input->post('acti_end_date')) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('acti_end_date')))) : NULL;
                if (!empty($_FILES['userfile']['name'])) {
                    $logo = $this->logo_upload();
                    if (is_array($logo)) {
                        $logo_img = $logo['file_name'];
                        if (file_exists('./logos/' . $tenant_data->Logo) && !empty($tenant_data->Logo)) {
                            unlink('./logos/' . $tenant_data->Logo);
                        }
                    }
                }
                $data = array(
                    'tenant_name' => strtoupper($this->input->post('tenant_name')),
                    'tenant_address' => strtoupper($this->input->post('address')),
                    'tenant_city' => strtoupper($this->input->post('city')),
                    'tenant_state' => $this->input->post('state'),
                    'tenant_country' => $this->input->post('country'),
                    'tenant_email_id' => $this->input->post('email'),
                    'tenant_contact_num' => $this->input->post('contact_num'),
                    'account_activation_start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('acti_start_date'))),
                    'account_activation_end_date' => $acc_acti_end_date,
                    'CopyRightText' => $this->input->post('copyright'),
                    'Currency' => $this->input->post('currency'),
                    'Country' => $this->input->post('country_use'),
                    'paypal_email_id' => $this->input->post('paypal_email'),
                    'invoice_name' => $this->input->post('inv_name'),
                    'invoice_footer_text' => $this->input->post('inv_footer'),
                    'website_url' => $this->input->post('website'),
                    'comp_reg_no' => strtoupper($this->input->post('company_no')),
                    'gst_reg_no' => strtoupper($this->input->post('gst_no')),
                    'director_name' => strtoupper($this->input->post('director_name')),
                    'contact_name' => strtoupper($this->input->post('contact_name')),
                );                
                if (!empty($logo_img)) {
                    $data['Logo'] = $logo_img;
                }
                $where = array(
                    'tenant_id' => $this->input->post('tenant_id')
                );                
                $result = $this->manage_tenant->update_tenant_master($data, $where);
                if ($result == TRUE) {
                    $this->session->set_flashdata("success", "Tenant updated successfully.");
                } else {
                    $this->session->set_flashdata("error", "Unable to update tenant. Please try again later.");
                }
                return redirect($this->controller_name);
            }
        }
    }
    
    
    /**
     * This function to edit tenant
     */
    public function edit_tenant() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['controllerurl'] = $this->controller_name;
        $data['meta_map'] = $this->meta_map;
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $tenant_id = $this->input->post('tenant_id');
            if (empty($tenant_id)) {
                return show_404();
            }
            $data['tenant'] = $tenant_data = $this->manage_tenant->get_tenant_details($tenant_id)->row();
            if (empty($data['tenant'])) {
                return show_404();
            }
            $acc_acti_end_date = ($tenant_data->account_activation_end_date == NULL || $tenant_data->account_activation_end_date == '0000-00-00 00:00:00') ? '' : date('d-m-Y', strtotime($tenant_data->account_activation_end_date));
            $_POST = array(
                'tenant_name' => $tenant_data->tenant_name,
                'tenant_id' => $tenant_data->tenant_id,
                'address' => $tenant_data->tenant_address,
                'city' => $tenant_data->tenant_city,
                'state' => $tenant_data->tenant_state,
                'country' => $tenant_data->tenant_country,
                'email' => $tenant_data->tenant_email_id,
                'contact_num' => $tenant_data->tenant_contact_num,
                'logo' => $tenant_data->Logo,
                'copyright' => $tenant_data->CopyRightText,
                'currency' => $tenant_data->Currency,
                'country_use' => $tenant_data->Country,
                'inv_name' => $tenant_data->invoice_name,
                'inv_footer' => $tenant_data->invoice_footer_text,
                'website' => $tenant_data->website_url,
                'company_no' => $tenant_data->comp_reg_no,
                'gst_no' => $tenant_data->gst_reg_no,
                'acti_start_date' => date('d-m-Y', strtotime($tenant_data->account_activation_start_date)),
                'acti_end_date' => $acc_acti_end_date,
                'search_tenant_name' => $this->input->post('search_tenant_name'),
                'paypal_email_id' => $tenant_data->paypal_email_id,
                'director_name' => $tenant_data->director_name,
                'contact_name' => $tenant_data->contact_name,
            );
            $data['countries'] = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
            $data['currencies'] = fetch_metavalues_by_category_id(Meta_Values::CURRENCIES);
            $data['states'] = array();
            $country = $this->input->post('country');
            if (!empty($country)) {
                $data['states'] = $this->manage_tenant->get_states($country);
            }
            $data['inv_names'] = array('' => 'Select', 'Tax Invoice' => 'Tax Invoice', 'Training Bill' => 'Training Bill');
        }
        $data['page_title'] = 'Edit Tenant';
        $data['main_content'] = $this->view_folder . 'edit';
        $this->load->view('layout', $data);
    }
    /**
     * This function to deactivate tenant users
     */
    public function deactivate() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $reason_for_deactivation = $this->input->post('reason_for_deactivation');
            $other_reason_for_deactivation = $this->input->post('other_reason_for_deactivation');
            $tenant_id = $this->input->post('tenant_id');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('reason_for_deactivation', 'reason for deactivation', 'required');
            if ($reason_for_deactivation == 'OTHERS') {
                $this->form_validation->set_rules('other_reason_for_deactivation', 'other reason for deactivation', 'required');
            }
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'account_status' => 'INACTIV',
                    'account_deactivation_reason' => $reason_for_deactivation,
                    'account_deactivation_reason_oth' => strtoupper($other_reason_for_deactivation),
                    'account_activation_end_date' => date('Y-m-d H:i:s')
                );
                $where = array(
                    'tenant_id' => $tenant_id
                );
                $result = $this->manage_tenant->update_tenant_master($data, $where);
                if ($result) {
                    $this->session->set_flashdata('success', 'Tenant has been deactivated successfully');
                } else {
                    $this->session->set_flashdata("error", "Unable to deactivate tenant. Please try again later.");
                }
                redirect($this->controller_name);
            }
        }
    }
    /**
     * Autocomplete to get all tenant name by tenant id
     */
    public function get_all_tenant() {
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $active = $this->input->post('active');
        $result = $this->manage_tenant->get_alltenant($query_string, $active);
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
    /**
     * upload logo validate size
     * @return boolean
     */
    public function logo_validation() {
        if (empty($_FILES['userfile']['name'])) {
            $this->form_validation->set_message('logo_validation', "[required]");
            return FALSE;
        } else {
            $image_info = getimagesize($_FILES["userfile"]["tmp_name"]);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            if ($image_width > 180 || $image_height > 180) {
                
                $this->form_validation->set_message('logo_validation', "[The image you are attempting to upload exceeds the maximum height or width.]");
                return FALSE;
            }
        }
    }
    /**
     * upload logo
     * @return type
     */
    public function logo_upload() {
        $config['upload_path'] = './logos/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '1000';
        $config['max_width'] = '180';
        $config['max_height'] = '180';
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload()) {
            return $this->upload->display_errors();
        } else {
            return $this->upload->data();
        }
    }
    /**
     * get states based on country
     */
    public function get_states() {
        $country = $this->input->post('country');
        $states = $this->manage_tenant->get_states($country);
        echo json_encode($states);
        exit;
    }
    /**
     * check email id
     */
    public function check_email() {
        $email = trim($this->input->post('email'));
        $tenant_id = $this->input->post('tenant_id');
        echo $this->manage_tenant->check_email($email, $tenant_id);
        exit;
    }
    /**
     * get tenant name
     */
    public function check_tenant_name() {
        $name = trim($this->input->post('name'));
        $tenant_id = $this->input->post('tenant_id');
        echo $this->manage_tenant->check_tenant_name($name, $tenant_id);
        exit;
    }
    
    /**
     * monthly enrollment count
     */
    public function enrollment_count() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data = array();

        $data['year_arr'] = array(''=>'Select year',2015 => 2015, 2016 => 2016, 2017 => 2017, 2018 => 2018, 2019 => 2019, 2020 => 2020);        
        
        $data['month_array'] = array(''=>'Select Month','01'=>Jan,'02'=>Feb,'03'=>Mar,'04'=>Apr,'05'=>May,'06'=>Jun,'07'=>Jul,'08'=>Aug,'09'=>Sep,'10'=>Oct,'11'=>Nov,'12'=>Dec);
        
        $data['tenant_array'] = $this->manage_tenant->get_all_tenant()->result_array();
        
        $tenant_id = $this->input->get('tenant_id');
        $fmonth = $this->input->get('fmonth');
        $fyear = $this->input->get('fyear'); 
        $lmonth = $this->input->get('lmonth');
        $lyear = $this->input->get('lyear');    

        if($tenant_id == '')
        {
            $data['tenant_tabledata'] = $this->manage_tenant->get_all_records()->result();
            
        }
        else
        {

            $data['tabledata'] = $this->manage_tenant->get_monthly_tenant_enrollment_count($tenant_id,$fyear,$fmonth,$lyear,$lmonth)->result_array();

        }

        $data['controllerurl'] = 'manage_tenant/manage_tenant';

        $data['page_title'] = 'General Reports - Monthly Enrollment Count';

        $data['main_content'] = 'tenant/enrollment_count_page';

        $this->load->view('layout', $data);

    }
    
    public function monthly_tenant_enrollment_count_pdf() {

        $this->load->helper('pdf_reports_helper');

        $tenant_id = $this->input->get('tenant_id');
        $fmonth = $this->input->get('fmonth');
        $fyear = $this->input->get('fyear'); 
        $lmonth = $this->input->get('lmonth');
        $lyear = $this->input->get('lyear');    

        $tenant_details = $this->tenantModel->get_tenant_details($tenant_id);

        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');

        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');

        $tabledata = $this->manage_tenant->get_monthly_tenant_enrollment_count($tenant_id,$fyear,$fmonth,$lyear,$lmonth)->result_array();
        
        $pdf_heading = 'General Reports - Monthly Enrollment Count Report for the tenant - '.$tenant_details->tenant_name;        

        return generate_monthly_tenant_enrollment_count_pdf($pdf_heading, $tabledata, $tenant_details);

    }
    
    public function monthly_tenant_enrollment_count_xls() {

        $this->load->helper('export_helper');

        $tenant_id = $this->input->get('tenant_id');
        $fmonth = $this->input->get('fmonth');
        $fyear = $this->input->get('fyear'); 
        $lmonth = $this->input->get('lmonth');
        $lyear = $this->input->get('lyear');
        
        $tenant_details = $this->tenantModel->get_tenant_details($tenant_id);

        $tabledata =$this->manage_tenant->get_monthly_tenant_enrollment_count($tenant_id,$fyear,$fmonth,$lyear,$lmonth)->result_array();

        $count_tabledata = count($tabledata);

        $excel_titles = array('Tenant','Month', 'Year', 'Total Enrollments');

        $excel_data = array();
        
          usort($tabledata, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });

        for ($i = 0; $i < $count_tabledata; $i++) {
            
            $excel_data[$i][] = $tabledata[$i]['tenant_name'];

            $excel_data[$i][] = $tabledata[$i]['month'];

            $excel_data[$i][] = $tabledata[$i]['year'];

            $excel_data[$i][] = $tabledata[$i]['count'];

        }

        $excel_filename = 'Monthly Enrollment Count ' . $tenant_details->tenant_name . '.xls';

        $excel_sheetname = 'Monthly Enrollment Count Report';

        $excel_main_heading = 'General Reports - Monthly Enrollment Count Report for the tenant - '.$tenant_details->tenant_name;

        return export__tenant_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);

    }
    
    public function total_tenant_enrollment_count_pdf() {

        $this->load->helper('pdf_reports_helper');

        $tenant_tabledata = $this->manage_tenant->get_all_records()->result_array();

        $pdf_heading = 'General Reports - Total Enrollment Count Report';        

        return generate_total_tenant_enrollment_count_pdf($pdf_heading, $tenant_tabledata);

    }
    
    public function total_tenant_enrollment_count_xls() {

        $this->load->helper('export_helper');
        $tenant_tabledata = $this->manage_tenant->get_all_records()->result_array();

        $count_tabledata = count($tenant_tabledata);

        $excel_titles = array('Tenant Name', 'Total Enrollments');

        $excel_data = array();

        for ($i = 0; $i < $count_tabledata; $i++)
        {

            $excel_data[$i][] = $tenant_tabledata[$i]['tenant_name'];

            $excel_data[$i][] = $tenant_tabledata[$i]['total'];

        }

        $excel_filename = 'Tenant Enrollment Count.xls';

        $excel_sheetname = 'Tenant Enrollment Count Report';

        $excel_main_heading = 'General Reports - Tenant Enrollment Count Report';

        return export__total_tenant_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);

    }
    
    
}
