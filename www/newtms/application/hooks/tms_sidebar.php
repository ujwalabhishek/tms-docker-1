<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Tms_sidebar {
    /**
     * function to get the page complimentary content
     * @return boolean
     */
    public function fetch_non_main_page_content() {
        $CI =& get_instance();
        $user = $CI->session->userdata('userDetails');
        if (empty($user->tenant_id) || empty($user->user_id)) {
            return;
        }
        $CI->load->helper('side_menu');
        $data = array();
        $data['user'] = $user;
        if ($CI->session->userdata('userDetails')->tenant_id == 'ISV01') {
            $tenant_details = array(
                'tenant_id' => 'ISV01',
                'logo' => 't01.png',
                'copyrighttext' => 'Copyright 2015',
                'currency' => '',
                'country' => '',
                'applicationname' => 'tms1.png',
                'tenant_name' => $CI->session->userdata('userDetails')->user_name,
                'website_url' => '',
                'tenant_email_id' => $CI->session->userdata('userDetails')->registered_email_id
            );
            $data['tenant_details'] = (object) $tenant_details;
        } else {
            $data['tenant_details'] = fetch_tenant_details($user->tenant_id);
        }
        $data['left_side_menu'] = logged_in_user_side_menu();
        $CI->data = $data;
        return;
    }

}
