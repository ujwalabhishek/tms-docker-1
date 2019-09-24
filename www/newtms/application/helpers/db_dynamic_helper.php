
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function switch_db_dynamic($name_db)
{
    $config_app['dsn'] = '';
    $config_app['hostname'] = '172.18.0.2';
    $config_app['username'] = 'biipmico_tms_master';
    $config_app['password'] = 'ksj784382*879#';
    $config_app['database'] = $name_db;
    $config_app['dbdriver'] = 'mysqli';
    $config_app['dbprefix'] = '';
    $config_app['pconnect'] = FALSE;
    $config_app['db_debug'] = (ENVIRONMENT !== 'production');
    $config_app['cache_on'] = FALSE;
    $config_app['cachedir'] = '';
    $config_app['char_set'] = 'utf8';
    $config_app['dbcollat'] = 'utf8_general_ci';
    $config_app['swap_pre'] = '';
    $config_app['encrypt'] = FALSE;
    $config_app['compress'] = FALSE;
    $config_app['stricton'] = FALSE;
    $config_app['failover'] = array();
    $config_app['save_queries'] = TRUE;
    
    return $config_app;
}

function fetch_dynamic_db_details(){
    $host=$_SERVER['HTTP_HOST'];
    if($host == 'xprienz.net'){
        $object = new stdClass();
        $object->tenant_db_name = 'biipmico_tms_masterdata';
        $object->tenant_db_user = 'biipmico_tms_master';
        $object->tenant_db_password = 'ksj784382*879#';
        
        return $object;
    }
    $config_app = switch_db_dynamic('biipmico_tms_masterdata');
    $CI =& get_instance();
    $CI->dbs = $CI->load->database($config_app,TRUE);
    //print_r($CI->dbs);exit;
    $CI->dbs->select('*');
        $CI->dbs->from('tenant_master');
        $CI->dbs->where('tenant_url',$host);
        return $CI->dbs->get()->row();
        //print_r( $CI->dbs->get()->row());exit;
}

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
                'applicationname' => 'TMS1.png',
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
            . 'ten.country,ten.applicationname, ten.tenant_name, ten.website_url, ten.tenant_email_id'); // ten.website_url added by dummy for changing home link on Nov 27 2014.
    $CI->db->from('tenant_master ten');
    $CI->db->where('ten.tenant_id', $tenant_id);
    $CI->db->limit(1);
    
    return $CI->db->get()->row();
}
