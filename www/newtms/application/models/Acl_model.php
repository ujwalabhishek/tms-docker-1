<?php
/*
  * Model class for ACL  
*/
class Acl_Model extends CI_Model {
   
    public function __construct() {
        parent::__construct();
    }
    /**
     * Get the features that the user has an access to if he has a role other than Admin
    */
    public function getAccessRights($tenant_id,$user_id,$role_id) {
        $side_menu = null;
        $role_features = $this->fetch_role_features($tenant_id, $role_id);
        $side_menu = $this->fetch_access_rights_menu_by_rolefeatures($role_features, $tenant_id, $role_id);
        //print_r($side_menu);exit;
        return $side_menu;
    }
    /**
     * Fetvch access rights
     * @param type $role_features
     * @param type $tenant_id
     * @param type $role_id
     * @return type
     */
    public function fetch_access_rights_menu_by_rolefeatures($role_features, $tenant_id, $role_id) {
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values');   
        $application_menu = fetch_metavalues_by_category_id(Meta_values::USER_ROLES);
        //print_r($application_menu);exit;
        foreach ($application_menu as $menu) {
            $meta_data[$menu['parameter_id']] = $this->get_meta_data_by_category_id($menu['child_category_id']);
        }
        //print_r($meta_data);exit;
        $this->db->select('feature_id, access_right_id');
        $this->db->from('role_features_access_rights');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('role_id', $role_id);
     
        $this->db->where_in('feature_id', $role_features);
        $results = $this->db->get()->result();
        $output = array();//added by shubhranshu
        foreach ($results as $result) {
            $output[$result->feature_id][$result->access_right_id] = $meta_data[$result->feature_id][$result->access_right_id];
        }
        //print_r($output);exit;
        return $output;
    }
    /**
     * function to get the metavalues for the category
     * @param string $category_id
     * @return array
     */
    public function get_meta_data_by_category_id($category_id) {
        $this->db->select('parameter_id, category_name');
        $this->db->from('metadata_values');
        $this->db->where('category_id', $category_id);
        $results = $this->db->get()->result();
        $output = array();
        foreach ($results as $result) {
            $output[$result->parameter_id] = $result->category_name;
        }
        
        return $output;
    }
    
    /*
     * Returns the use cases accessible by the user
     * @input  - session tenant_id and role_id
     * @outout - Array of Features Id
     */
    public function fetch_role_features($tenant_id, $role_id) {
        $this->db->select('feature_id');
        $this->db->from('role_features');
        $this->db->where('tenant_id', $tenant_id);
        ($role_id != 'ADMN') ? $this->db->where('role_id',$role_id ) : '';
        $features = $this->db->get()->result();
        $role_features = array();
        foreach ($features as $feature) {
            $role_features[] = $feature->feature_id;
        }
        //echo $this->db->last_query();exit;
        return $role_features;
    }

}