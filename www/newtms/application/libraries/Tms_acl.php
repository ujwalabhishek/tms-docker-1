<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Tms_acl {
    var $tms_routes = array();
    private $uri;
    function __construct() {
        $this->uri =& load_class('URI', 'core'); 
       $CI =& get_instance();
       //$CI->db->query('SET OPTION SQL_BIG_SELECTS=1'); 
       $CI->db->query('SET SQL_BIG_SELECTS=1'); 
    }
    /**
     * function to check whether user has an access against the feature
     * @return boolean
     */
    public function has_permission() {
        $uri = implode('/', $this->uri->segments);
        $uri = explode('/', $uri);
        if (empty($uri[0])) {
            return;
        }
        $arg_first = strtolower($uri[0]);
        if ($arg_first == 'login' || $arg_first == 'activate_user' || $arg_first == 'scheduler') {
            return;
        }
        $CI =& get_instance();
        if($CI->input->is_cli_request()) return TRUE; # run from command line
        $session_data = $CI->session->all_userdata();
        $role_id = $session_data['userDetails']->role_id;
        $tenant_id = $session_data['userDetails']->tenant_id;
        if (empty($session_data['userDetails']->user_id)) {
           $data['login_link'] = TRUE;
           return $CI->load->view('common/error', $data);
        }
        if (!isset($role_id)) {
            $data['login_link'] = TRUE;
            return $CI->load->view('common/error', $data);
        }
        if ($role_id == 'ADMN') {
            return TRUE;
        }
        if (is_file(APPPATH.'config/tms_routes.php')) {
            include(APPPATH.'config/tms_routes.php');
	}
        $this->tms_routes = (!isset($tms_route) OR ! is_array($tms_route)) ? array() : $tms_route;
	unset($tms_route);
        $common_controllers = $this->tms_routes['COMMON_CONTROLLERS'];
        if (in_array($arg_first, $common_controllers)) {
            return TRUE;
        }
        $arg1 = strtolower($uri[1]);
        foreach ($this->tms_routes as $key => $route) {
            if ($route['controller_name'] == $arg_first) {
                $feature = $key;
                $ops = array_flip($route['ops']);
                $permission = $ops[$arg1];
                break;
            }
        }
        if (is_numeric($arg1) && $feature != 'SETTG') {
            $permission = 'LST_SRCH';
        }
        if (empty($feature)) {
            $data['login_link'] = FALSE;
            return $CI->load->view('common/error', $data);
        }
        if (!empty($feature) && empty($permission)) {
           if ($feature == 'SETTG') {
                $permission = 'STTGS';
           } else if ($feature == 'RPTS') {
                $permission = 'ATTDN';
           } else {
                $permission = 'LST_SRCH';
           }
        }
        if ($this->check_feature_permission($tenant_id, $role_id, $feature, $permission)) {
            return TRUE;
        }
        $data['login_link'] = FALSE;
        return $CI->load->view('common/error', $data);
    }
    /**
     * function to check if a user a permission for a specific feature
     * @param int $tenant_id
     * @param string $role_id
     * @param string $feature
     * @param string $permission
     * @return boolean
     */
    public function check_feature_permission($tenant_id = NULL, $role_id = NULL, $feature = NULL, $permission = NULL) {
        if (empty($tenant_id) && empty($role_id) && empty($feature) && empty($permission)) {
            return FALSE;
        }
        $CI =& get_instance();
        $CI->db->select('feature_id');
        $CI->db->from('role_features_access_rights');
        $CI->db->where('tenant_id', $tenant_id);
        $CI->db->where('role_id', $role_id);
        $CI->db->where('feature_id', $feature);
        $CI->db->where('access_right_id', $permission);
        return $CI->db->get()->num_rows();
    }
}