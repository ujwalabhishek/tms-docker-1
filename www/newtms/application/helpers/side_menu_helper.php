<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 function fetch_non_main_page_content() {
        $CI =& get_instance();
        $user = $CI->session->userdata('userDetails');
        if (empty($user->user_id)) {
        $data['login_link'] = TRUE;
        return $CI->load->view('common/error', $data);
        }
        if (!isset($user->role_id)) {
            $data['login_link'] = TRUE;
            return $CI->load->view('common/error', $data);
        }
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
function logged_in_user_side_menu() {
    $CI = & get_instance();
    $user = $CI->session->userdata('userDetails');
    $result = array();
    if ($user->user_id && $user->role_id && $user->tenant_id) {
        $CI->load->helper('metavalues_helper');
        $CI->load->model('acl_model');
        $CI->load->model('meta_values');
        $categories = fetch_metavalues_by_category_id(Meta_values::USER_ROLES);
        foreach ($categories as $category) {
            $menu_links[$category['parameter_id']] = $category['category_name'];
        }
        
        $side_menu = $CI->acl_model->getAccessRights($user->tenant_id,$user->user_id,$user->role_id);
        if (is_file(APPPATH.'config/tms_routes.php')) {
            include(APPPATH.'config/tms_routes.php');
        }
        $tms_routes = (!isset($tms_route) OR ! is_array($tms_route)) ? array() : $tms_route;
        //print_r($tms_routes);exit;
        unset($tms_route);
        foreach ($menu_links as $category_machine_name => $category_user_name) {
            $links = get_menu_links_by_category($category_user_name, $category_machine_name, $side_menu[$category_machine_name], $tms_routes);
            if ($links) {
                $output[$category_machine_name] = $links;
            }
        }
    } 
    //print_r($output);exit;
    return $output;
}
/**
     * fetch all the links of a category
     * @param string $category_user_name display name
     * @param string $category_machine_name
     * @param array $category
     * @param array $tms_routes
     * @return string a list of links
     */
    function get_menu_links_by_category($category_user_name, $category_machine_name, $category, $tms_routes) {
        static $i = 1;
        
        if (empty($category) || $category_machine_name == 'DASHBRD') {
            return;
        }
        $output = array();///added by shubhranshu
        $tms_route = $tms_routes[$category_machine_name]['ops'];
        $flag = TRUE;
        $CI = & get_instance();
        //$CI->load->library('Uri'); //commentted by shubhranshu
        
        $controller = $CI->uri->segment(1);
        $method = $CI->uri->segment(2);
        foreach ($category as $key => $value) {
            if($key == 'DEACT' || $key == 'STTGS') {
                continue;
            }
            if($flag) {
                if ((empty($method) || stripos($method, 'view', 0) === 0 || is_numeric($method)) 
                        && $controller == $tms_routes[$category_machine_name]['controller_name']) {
                    setcookie('cnameIndex', $i); 
                    $output[$category_machine_name] = "<a class='active' href=". site_url() . $tms_routes[$category_machine_name]['controller_name'] .">". $category_user_name ."</a>";
                } else {
                    $output[$category_machine_name] = "<a href=". site_url() . $tms_routes[$category_machine_name]['controller_name'] .">". $category_user_name ."</a>";
                }
                $flag = FALSE;
            }  
            if (empty($tms_route[$key])) {
                $link = FALSE;
            }else {
                $url = site_url() . $tms_routes[$category_machine_name]['controller_name'] . '/' . $tms_route[$key];
                if ($controller == $tms_routes[$category_machine_name]['controller_name'] AND $method == $tms_route[$key]) {
                    setcookie('cnameIndex', $i); 
                    $link = "<a class='active' href=". $url .">". $value ."</a>";
                }else {
                    $link = "<a href=". $url .">". $value ."</a>";
                }
            }
            
            $output[$key] = $link;
        }
        $i++;
        //print_r($output);exit;
        return $output;
        
}
/**
 * fetch the details of tenant
 * @param string $tenant_id
 * @return boolean
 */
function fetch_tenant_details($tenant_id = NULL) {
    $CI =& get_instance();
    if (empty($tenant_id)) {
        return FALSE;
    }
    $CI->db->select('ten.tenant_id, ten.logo, ten.copyrighttext, ten.currency, '
            . 'ten.country,ten.applicationname, ten.tenant_name, ten.website_url, ten.tenant_email_id, ten.tenant_contact_num, ten.comp_reg_no, ten.corp_pass_id'); // ten.website_url added by dummy for changing home link on Nov 27 2014.
    $CI->db->from('tenant_master ten');
    $CI->db->where('ten.tenant_id', $tenant_id);
    $CI->db->limit(1);
    
    return $CI->db->get()->row();
}
////added by shubhranshu/////////////////////
function database_connection_check_url(){
    $host=$_SERVER['HTTP_HOST'];
    if(preg_match('/xprienz2/', $host)){
        $db_name=connect_primary('biipmico_tms');
    }else if(preg_match('/focus/', $host)){
        $db_name=connect_primary('biipmico_tms');
    }else if(preg_match('/carrie/', $host)){
        $db_name=connect_primary('biipmico_tms');
    }else if(preg_match('/xprienz/', $host)){
        $db_name=connect_primary('biipmico_tms');
    }else{
        $db_name=connect_primary('biipmico_tms_masterdata');
    }
    return $db_name;
}

/////added by shubhranshu for Data variable to be access session object
function store_session_data(){
    $CI =& get_instance();
    $data['user'] = $CI->session->userdata('userDetails');
    $CI->data = $data;
    return;
}

function connect_primary($url_str){
   // $conn = new mysqli('localhost', 'root', '','db_parameters');
    //$sql = "SELECT db_name FROM db_parameters WHERE url_string='$url_str'";
    //$result = $conn->query($sql);
    //$row= $result->fetch_assoc();
    //mysqli_close($conn); 
    //return $row['db_name'];
    return $url_str;
}
////added by shubhranshu/////////////////////