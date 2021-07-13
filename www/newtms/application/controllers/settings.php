<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
  * This is the controller class for Settings Use case features. 
  */
class Settings extends CI_Controller {
    private $user;
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('common');
        $this->load->model('settings_model');
        $this->load->model('notifications_model');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values', 'meta');
        $this->load->library('bcrypt');
        $this->user=$this->session->userdata('userDetails');
    }
    /*
     * This function loads the Settings page.
     */
    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;        
        $data['page_title'] = 'Settings';
        $data['main_content'] = 'settings/settings';
        $data['table_data'] = $this->settings_model->get_tenent_master($tenant_id);
        $this->load->view('layout', $data);
    }
    /*
     * This function loads the GST Rates page.
    */
    public function gst_rates() {   
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Gst Rate';
        $data['main_content'] = 'settings/gstrates';                
        $field = ($this->input->get('f'))?$this->input->get('f'):'gst.gst_id';
        $order_by = ($this->input->get('o'))?$this->input->get('o'):'DESC';
        $result = $this->settings_model->get_gst_count();        
        $totalrows = ($result[0]->total)?$result[0]->total - 1:0;
        $records_per_page = RECORDS_PER_PAGE;        
        $baseurl = base_url().  'settings/gst_rates/';
        $pageno = $this->uri->segment(3);
        if (empty($pageno)) {
            $pageno = 1;
        }        
        $offset = ($pageno * $records_per_page);        
        $data['gst_list'] = $this->settings_model->get_gst_rates($tenant_id, $records_per_page, $offset, $field, $order_by);
        $data['gst_active_value'] = $this->settings_model->get_active_gst_rates(); 
        $data['controllerurl'] =   'settings/gst_rates/';
        $this->load->helper('pagination');
        $data['pagination'] =  get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);        
        $data['sort_order'] = $order_by;
        $this->load->view('layout', $data);
    } 
    /**
     * notifications list page
     */
    public function notifications() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Notifications';
        $data['main_content'] = 'settings/notifications';
        $notifictions = $this->notifications_model->get_notifications();
        $data['notifications'] = $notifictions['query'];
        $data['page_count'] = ceil($notifictions['count']/RECORDS_PER_PAGE);
        $data['notification_types'] = fetch_metavalues_by_category_id(Meta_Values::NOTIFICATION_TYPES);
        $data['broadcast_user_types'] = fetch_metavalues_by_category_id(Meta_Values::BROADCAST_USER_TYPES);
        $data['meta_map'] = $this->meta->get_param_map();
        $this->load->view('layout', $data);
    }
    /**
     * save notifications
     */
    public function update_notification() {
        $this->notifications_model->update_notification();
        $id = $this->input->get_post('notification_id');
        if (!empty($id))
            $this->session->set_flashdata('success_message', 'Notification updated successfully');
        else
            $this->session->set_flashdata('success_message', 'Notification created successfully');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success'=>true));
    }
    /**
     * copy notification
     */
    public function copy_notification() {
        $this->notifications_model->copy_notification();
        $this->session->set_flashdata('success_message', 'Notification created successfully');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success'=>true));
    }
    /**
     * retrieve notification
     */
    public function get_notification() {
        $id = $this->input->get('notification_id');
        $data = $this->notifications_model->get_notification($id)->row();
        $data->broadcast_from = date_format_singapore($data->broadcast_from);
        $data->broadcast_to = date_format_singapore($data->broadcast_to);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
    /**
     * search trainee by name
     */
    public function search_trainees_by_name() {
        $this->load->model('trainee_model');
        $data = array();
        $query = $this->trainee_model->search_trainee_by_name($this->input->get('name'));
        foreach($query->result() as $row)
        {
            $username = $row->first_name.' '.$row->last_name;
            array_push($data, array('id'=>$row->user_id, 'text'=>$username));
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('results'=>$data, 'more'=>false));
    }
    /**
     * search trainee by id
     */
    public function search_trainees_by_ids() {
        $this->load->model('trainee_model');
        $idsString = $this->input->get('ids');
        $ids = explode(',',$idsString);
        $data = array();
        $query = $this->trainee_model->search_trainee_by_ids($ids);
        foreach($query->result() as $row)
        {
            $username = $row->first_name.' '.$row->last_name;
            array_push($data, array('id'=>$row->user_id, 'text'=>$username));
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('results'=>$data, 'more'=>false));
    }
    /*
    * This method used for editing active gst record. 
    */
    public function edit_gst_rate() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $data['page_title'] = 'Edit Gst Rate';
        $data['main_content'] = 'settings/editGSTrate';        
        // common settings ends                
        $data['gst_active_value'] = $this->settings_model->get_active_gst_rates($tenant_id);                                        
        $data['sideMenuData'] = $this->sideMenu;        
        $this->load->view('layout', $data);       
    }
    /* 
    * This method updates existing active gst rate to in-active and inserts the new gst rate 
    */
    public function update_gst_rate() {
        $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $user_id = $user->user_id;
        $data['page_title'] = 'Edit Gst Rate';
        $data['main_content'] = 'settings/editGSTrate';        
        $result=$this->settings_model->update_gst_rate($tenant_id,$user_id);        
        if($result == TRUE){
            $this->session->set_flashdata('success_message', 'GST rate has been successfully updated');
        }else {             
            $this->session->set_flashdata('error_message','Oops! Sorry, it looks like something went wrong.Please try again!.');
        }
        redirect("settings/gst_rates");
    }
    /*
     * This function for updating paypal email id of the tenant.
     */
    function update_paypal_email_id() {
        $result= $this->settings_model->update_paypal_email_id($this->user->tenant_id);
        if($result == TRUE){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /*
     * This function for updating Invoice name of the tenant.
     */
    function update_invoice_name() {
        $result= $this->settings_model->update_invoice_name($this->user->tenant_id);
        if($result == TRUE){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /*
     * This function for update_invoice_footer_text name of the tenant.
     */
    function update_invoice_footer_text() {
        $result= $this->settings_model->update_invoice_footer_text($this->user->tenant_id);
        if($result == TRUE){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function change_password() {
        $data['page_title'] = 'Change Password - Settings';
        $data['main_content'] = 'user/changepassword';
        //server  side validation
        $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            extract($_POST);
            $encrypted_password = $this->bcrypt->hash_password($new_password);
            $result = $this->user_model->update_password($encrypted_password);
            if ($result == TRUE) {
                $this->session->set_flashdata('success', 'Your password has been updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Unable to update your password.Please try again later.');
            }
            redirect('settings/change_password');
        } else {
            $this->load->view('layout_public', $data);
        }
    }

    /*
      Function to verify current password.

     */

    public function password_exist() {
        extract($_POST);
        $oldpassword = trim(($oldpassword));
        $exists = $this->user_model->match_old_pwd($oldpassword);
        echo ($exists) ? '1' : '0';
        return;
    }

}