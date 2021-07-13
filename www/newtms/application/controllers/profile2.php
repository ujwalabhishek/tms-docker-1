<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for profile page. 
 */
class Profile extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('internal_user_model', 'internaluser');
        $this->load->helper('common');
        $this->load->helper('metavalues');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('bcrypt');
    }
    /*
     * This methord for loading the profile page.
     */
    public function index() {
        $data['page_title'] = 'Profile';
        $data['main_content'] = 'profile/profile';
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $user_list_values = $this->internaluser->get_user_details($tenant_id, $user_id);
        $data['user_list_values'] = $user_list_values;
        $country_of_residence = get_param_value($user_list_values->country_of_residence);
        $data['country_of_residence'] = $country_of_residence;
        if ($user_list_values->country_of_residence == 'IND') {
            $data['pan_number'] = $user_list_values->tax_code;
        }
        if ($user_list_values->country_of_residence == 'SGP') {
            $data['nric_number'] = $user_list_values->tax_code;
            $data['tax_code_type'] = $user_list_values->tax_code_type;
            $data['other_identi_type'] = $user_list_values->other_identi_type;
        }
        if ($user_list_values->country_of_residence == 'USA') {
            $data['ssn_number'] = $user_list_values->tax_code;
        }
        $user_personal_country = get_param_value($user_list_values->personal_address_country);
        $data['user_personal_country'] = $user_personal_country;
        $user_personal_state = get_param_value($user_list_values->personal_address_state);
        $data['user_personal_state'] = $user_personal_state;
        $user_office_designation = get_param_value($user_list_values->designation);
        $data['user_office_designation'] = $user_office_designation;
        $user_office_country = get_param_value($user_list_values->off_address_country);
        $data['user_office_country'] = $user_office_country;
        $user_office_state = get_param_value($user_list_values->off_address_state);
        $data['user_office_state'] = $user_office_state;
        $user_role_name = $this->internaluser->get_user_role_name($user_list_values->role_id);
        $data['user_role_name'] = $user_role_name;
        $data['roles'] = $this->internaluser->get_user_role($tenant_id);
        $this->load->view('layout', $data);
    }
   /*
     * duplicate function to get states from internal user for profile controller
     */
    public function get_states_json() {
        $country_param = $this->input->post('country_param');
        $states = $this->internaluser->get_states($country_param);
        $states_arr = array();
        foreach ($states as $item) {
            $states_arr[] = $item;
        }
        echo json_encode($states_arr);
        exit;
    }
    /*
     * This methord for Updating the profile data.(here using the same model of internal userd)
     */
    public function update_profile() {
        $edit_user_id = $this->session->userdata('userDetails')->user_id;
        $data['edit_user_id'] = $edit_user_id;
        $user_list_values = $this->internaluser->get_user_details($this->session->userdata('userDetails')->tenant_id, $edit_user_id);
        if ($user_list_values == false) {
            $this->session->set_flashdata("error", "Unable to edit profile. Please try again later.");
        } else {
            $this->session->set_userdata('registered_email_id_edit', $user_list_values->registered_email_id);
            $this->session->set_userdata('user_name_edit', $user_list_values->user_name);
            $this->session->set_userdata('tax_code_edit', $user_list_values->tax_code);
            $country_of_residence = $this->input->post('country_of_residence');
            if ($country_of_residence == 'IND') {
                $this->form_validation->set_rules('PAN', 'PANNumber', 'required|max_length[15]|callback_check_unique_usertaxcode_edit');
            }
            $valid = TRUE; 
            if ($country_of_residence == 'SGP') {
                if ($this->data['user']->role_id != 'ADMN'){//Check role != ADMIN
                    $NRIC = $this->input->post('NRIC');
                    $NRIC_OTHER = $this->input->post("NRIC_OTHER");
                    $NRIC_ID = $this->input->post('NRIC_ID');
                    $this->form_validation->set_rules('NRIC', 'NRIC Type', 'required');                    
                    if($NRIC_OTHER != "NOTAXCODE" ){
                        $this->form_validation->set_rules('NRIC_ID', 'NRIC Number', 'required|max_length[50]|callback_check_unique_usertaxcode');
                        if(!empty($NRIC) && $NRIC != "SNG_3") {
                            $valid = validate_nric_code($NRIC, $NRIC_ID);
                            if ($valid == FALSE) {                                                   
                                $this->session->set_flashdata('error_message', 'Invalid NRIC Code');                                
                            }
                        }
                    }
                }                
            }
            if ($country_of_residence == 'USA') {
                $this->form_validation->set_rules('SSN', 'SSNNumber', 'required|max_length[15]|callback_check_unique_usertaxcode_edit');
            }
            $this->form_validation->set_rules('user_registered_email', 'UserEmail', 'required|max_length[50]|callback_check_unique_useremail_edit');
            $this->form_validation->set_rules('pers_first_name', 'First Name', 'required');
            if ($this->form_validation->run() == TRUE && $valid==TRUE) {
                $delete_image = $this->input->post('deleteimage') ? $this->input->post('deleteimage') : 'no';
                $uid = $this->internaluser->update_user_data();
                $this->load->helper('upload_helper');
                if (!empty($_FILES['userfile']['name']) && $uid && $delete_image == 'no') {
                    $image_data = upload_image('uploads/images/internal_user', $uid);
                    if ($image_data['status']) {
                        $image_path = $image_data['image']['system_path'] . '/' .
                                $image_data['image']['raw_name'] . '_thumb' . $image_data['image']['file_ext'];
                        $previous_thumb_path = fetch_image_path_by_uid($uid);
                        remove_previous_image($previous_thumb_path);
                        save_image_path($uid, $image_path);
                    } 
                } else if ($uid && $delete_image == 'no') {
                    $previous_thumb_path = fetch_image_path_by_uid($uid);
                    remove_previous_image($previous_thumb_path);
                    save_image_path($uid);
                }
                if ($uid == FALSE){
                    $this->session->set_flashdata('error_message', 'Unable to update your account information. Please try again later');
                }else{
                    $previous_data = json_encode($user_list_values);
                    user_activity(12,$edit_user_id,$previous_data);
                    $this->session->set_flashdata('success_message', 'Your profile has been updated successfully');
                }
                redirect('profile');
            }else {
                redirect("profile");
            }
        }
    }
    /*
     * Method for checking if pan id is already exists in add Interanl user.
     */
    public function check_pan($pan) { 
        extract($_POST);        
        if($country_of_residence == "SGP" && $this->data['user']->role_id != 'ADMN') {
            $valid = validate_nric_code($nric, $pan_id);            
            if($valid == FALSE) {
                echo 2;
                return;
            }
        }
        $exists = $this->internaluser->check_pan(); 
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
        return TRUE;
    }
    /*
     * This methord used for checking the unique taxcode.
     */
    public function check_unique_usertaxcode_edit() {
        $country_of_residence = $this->input->post('country_of_residence');
        if ($country_of_residence == "IND") {
            $tax_code = $this->input->post('PAN');
        }
        if ($country_of_residence == "SGP") {
            $tax_code = $this->input->post('NRIC');
        }
        if ($country_of_residence == "USA") {
            $tax_code = $this->input->post('SSN');
        }
        if ($tax_code) {
            $exists = $this->internaluser->check_duplicate_user_taxcode_edit($tax_code, $this->session->userdata('tax_code_edit'));
            if (!$exists) {
                $this->form_validation->set_message('check_unique_usertaxcode_edit', "Taxcode exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * This methord used for checking th taxcode unique.
     */
    public function check_unique_useremail_edit() {
        if ($user_email = $this->input->post('user_registered_email')) {
            $exists = $this->internaluser->check_duplicate_user_email_edit($user_email, $this->session->userdata('registered_email_id_edit'));
            if (!$exists) {
                $this->form_validation->set_message('check_unique_useremail_edit', "Email ID exists.");
                return FALSE;
            }
            return TRUE;
        }
    }
    /*
     * This function for changing password.
     */
    public function change_password() {
        $data['page_title'] = 'Change Password';
        $data['main_content'] = 'profile/changepassword';
        $this->load->view('layout', $data);
    }
    /* This function for updating the new pwd.
     */
    public function update_password() {
        $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
        $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|matches[new_password_confirm]');
        $this->form_validation->set_rules('new_password_confirm', 'Confirm Password', 'trim|required');
        if ($this->form_validation->run() == TRUE) {
            extract($_POST);
            $encrypted_password = $this->bcrypt->hash_password($new_password);
            $data = array('old_password' => $old_password,
                'encrypted_password' => $encrypted_password);
            $result = $this->internaluser->update_password($data);
            if ($result == TRUE) {
                    $user_id = $this->session->userdata('userDetails')->user_id;
                    $first_name = $this->session->userdata('userDetails')->first_name;
                    $date_time = date('Y-m-d h:i:s');
                    $old_password = $this->input->post('old_password');
                    $data = array('user_id'=>$user_id,
                                    'first_name'=>$first_name,
                                    'old_password'=>$old_password,
                                    'date_time'=>$date_time);
                    $previous_details = json_encode($data);
                    user_activity(16, $user_id, $previous_details,1);
                $this->session->set_flashdata('success', 'Your password has been updated successfully');
            } else {
                $this->session->set_flashdata('error', 'Unable to update your password.Please try again later.');
            }
            redirect('profile/change_password');
        } else {
            $this->change_password();
        }
    }
}
