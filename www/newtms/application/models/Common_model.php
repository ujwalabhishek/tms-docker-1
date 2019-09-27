<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Use: A Model class for Common functions helper.
 */

class Common_Model extends CI_Model{
    public function is_taxcode_exist($tax_code, $tenant_id){
        $sql = 'select * from tms_users where tax_code="'.$tax_code.'" AND tenant_id="'.$tenant_id.'"';
        $result = $this->db->query($sql);         
        if ($result->num_rows == 0) {
            return FALSE; 
        }
        else {
            return TRUE; 
        }
        
    }
    public function is_username_exist($user_name, $tenant_id){
        $sql = 'select * from tms_users where user_name="'.$user_name.'" AND tenant_id="'.$tenant_id.'"';
        $result = $this->db->query($sql);       
        if ($result->num_rows == 0){
            return FALSE; 
        }
        else {
            return TRUE; 
        }
        
    }  
    public function is_registration_number_exist($reg_num,$tenant_id='') {
         $user = $this->session->userdata('userDetails');
        $tenant_id = $user->tenant_id;
        $sql = 'SELECT * FROM company_master cm join tenant_company tc on tc.company_id=cm.company_id WHERE cm.comp_regist_num="'.$reg_num.'" and tc.tenant_id="'.$tenant_id.'"';
        $result = $this->db->query($sql);
        if ($result->num_rows == 0) {
            return FALSE; 
        }
        else {
            return TRUE; 
        }
    }
    /**
     * This function used for duplicate enrollment check.
    * @param type $user_id
     * @param type $course_id
     * @param type $class_id
     * @param type $tenant_id
     * @return boolean
     */
    public function is_user_enrolled($user_id, $course_id, $class_id, $tenant_id){
        $sql = 'SELECT * FROM class_enrol WHERE user_id="'.$user_id.'" AND course_id="'.$course_id.'" AND class_id="'.$class_id.'" AND tenant_id="'.$tenant_id.'"';
        $result = $this->db->query($sql);        
        if ($result->num_rows == 0) {
            return FALSE; 
        }
        else {
            return TRUE; 
        }
    }
    
    public function fetch_compnies(){
      $user = $this->session->userdata('userDetails');      
      
      $this->db->select('cm.company_id, cm.company_name');
      $this->db->from('company_master cm');
      $this->db->join('tenant_company tc', "cm.company_id = tc.company_id and tc.comp_status='ACTIVE'");  
      $this->db->where('tc.tenant_id', $user->tenant_id);
      
 
      if($user->company_id != '') {
          $this->db->where('tc.company_id', $user->company_id);
      }
      
      
      $this->db->order_by('cm.company_name');
      $query = $this->db->get();
      return $query->result_array();
    }

    public function fetch_courses(){
        
      $this->db->select('course_id, crse_name');
      $this->db->from('course');     
      $query = $this->db->get();
      $data = array();

      return $query->result_array();
    }
    
    public function fetch_classes_by_couseid($couseid){
        
      $this->db->select('class_id, class_name');
      $this->db->from('course_class');    
      $this->db->where('course_id', $couseid);
      $query = $this->db->get();

      return $query->result_array();
    }
    
    public function get_param_value($param_id){
            return $this->db->select('category_name')->where('parameter_id',$param_id)->get('metadata_values')->row();
        }
    public function get_companyname($company_id){
      $this->db->select('company_name');
      $this->db->from('company_master'); 
      $this->db->where('company_id', $company_id);
      $query = $this->db->get();
      return $query->result_array();
    }
        
}

