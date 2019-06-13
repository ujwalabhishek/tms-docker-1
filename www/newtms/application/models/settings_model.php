<?php

/*
  * This is the Model class for Settings
  */

class Settings_Model extends CI_Model {
  /**
   * get GST rate
   * @param type $tenant_id
   * @param type $limit
   * @param type $offset
   * @param type $sort_by
   * @param type $sort_order
   * @return boolean
   */
    public function get_gst_rates($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL){        
        $this->db->select("gst.gst_id, gst.gst_rate, gst.updated_on, gst.updated_by, "
                . "gst.is_current, usr.first_name, usr.last_name");        
        $this->db->from("gst_rates gst, tms_users_pers usr");
       $this->db->where("gst.updated_by = usr.user_id");
        $this->db->where("is_current !=",1);
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('gst.gst_id', 'DESC');
        }        
        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $result = $this->db->get();        
        if($result->num_rows()>0) {
            return $result->result();
        }
        else {
            return false;
        }    

    }
    /**
     * This gets the total GST entries in the table gst_rates
     * @return type
     */
    public function get_gst_count(){      
        $this->db->select("count(*) as total");        
        $this->db->from("gst_rates gst, tms_users_pers usr");
        $this->db->where("gst.tenant_id = usr.tenant_id");
        $this->db->where("gst.updated_by = usr.user_id");
        $this->db->order_by('gst.gst_id', 'DESC');        
        $result = $this->db->get();                 
        return $result->result();        
    }
    /**
     * This method inserts the new gst rate and set it to active
     * @param type $tenant_id
     * @param type $user_id
     * @param type $gst_rate
     * @return boolean
     */
    private  function insert_new_gst_rate($tenant_id,$user_id,$gst_rate) {                      
        $data = array('tenant_id'=>$tenant_id,'gst_rate'=>$gst_rate,'updated_on'=>date('Y-m-d'),'updated_by'=>$user_id,'is_current'=>1);
        $this->db->trans_start();
        $result=$this->db->insert('gst_rates', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
   /**
    * This method gets the active gst rate  for a tenant
    * @return boolean
    */
    public function get_active_gst_rates(){        
        $this->db->select("gst_rate,gst_id");
        $this->db->from("gst_rates");
        $this->db->where("is_current",1);        
        $result = $this->db->get();        
        if($result->num_rows()>0){
            return $result->row();
        }
        else
            return false;
        
    }
    /**
     * This method updates existing active gst rate to in-active and inserts the new gst rate
     * @param type $tenant_id
     * @param type $user_id
     * @return boolean
     */
    public function update_gst_rate($tenant_id,$user_id){
        extract($_POST);
        $data=array('is_current'=>0);
        $this->db->where('is_current',1);
        $this->db->trans_start();
        $this->db->update('gst_rates',$data);        
        $result=$this->insert_new_gst_rate($tenant_id,$user_id,$gst_rate);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }else {
            return $result;
        }
    }
    /**
     * This function for getting the tenant mater details in settings page.
     * @param type $tenant_id
     * @return boolean
     */
    public function get_tenent_master($tenant_id = NULL){
        if($tenant_id== NULL){
            return FALSE;
        }
        $this->db->select('*');
        $this->db->from('tenant_master');
        $this->db->where('tenant_id',$tenant_id);
        $result=$this->db->get();
        if($result) {
            return $result->row();
        }else {
            return FALSE;
        }
    }
   /**
    * This Methord for updating the paypal email id of the tenant.
    * @param type $tenant_id
    * @return boolean
    */ 
    public function update_paypal_email_id($tenant_id) {
        $paypal_email_id=$this->input->post('paypal_email_id');        
        $data= array('paypal_email_id'=>$paypal_email_id);
        $this->db->where('tenant_id',$tenant_id);
        $this->db->trans_start();
        $result=$this->db->update('tenant_master',$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else{
            return TRUE;
        }
    }
   /**
    * This Methord for updating the Invoice name of the tenant.
    * @param type $tenant_id
    * @return boolean
    */
    public function update_invoice_name($tenant_id) {
        $invoice_name=$this->input->post('invoice_name');      
        $data= array('invoice_name'=>$invoice_name);
        $this->db->where('tenant_id',$tenant_id);
        $this->db->trans_start();
        $result=$this->db->update('tenant_master',$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }else {
            return TRUE;
        }
    }
   /**
    * This Methord for updating the Invoice name of the tenant.
    * @param type $tenant_id
    * @return boolean
    */
    public function update_invoice_footer_text($tenant_id) {
        $invoice_footer_text=$this->input->post('invoice_footer_text');      
        $data= array('invoice_footer_text'=>$invoice_footer_text);
        $this->db->where('tenant_id',$tenant_id);
        $this->db->trans_start();
        $result=$this->db->update('tenant_master',$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

 }   
   