<?php

/*
 * Settings Controller 
 * Author : Balwant Singh
 * Use: User Settings, list and change
 */

class settings_public extends CI_controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper(array('form', 'url', 'common'));
        $this->load->library('bcrypt');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['page_title'] = 'Settings';
        $data['main_content'] = 'settings/mysettings';
        if ($this->input->post()) {
            $limit_val = $this->input->post('limit_val');
            if ($limit_val == "" | $limit_val < 1 || $limit_val > 50) {
                $data['msg'] = 'Field required a value in range of 1-50';
            } else {
                $updated = $this->user_model->update_search_limit($limit_val);
                $this->session->set_userdata('records_per_page', $limit_val);
                $data['msg'] = 'Updated Successfully.';
            }
        }
        $data['details'] = $this->user_model->get_search_limit();

        $this->load->view('layout', $data);
    }

    /*
      Function to verify current password and change password.
      Author: Balwant Singh
     */

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
            $this->load->view('layout', $data);
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
