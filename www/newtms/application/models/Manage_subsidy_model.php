<?php

/*
 * This is the Model class for Manage Tenants Subsidy
 */

class Manage_Subsidy_Model extends CI_Model {
    /**
     * list subsidy
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $sort_order
     * @return type
     */
    public function list_all_subsidy($tenant_id = 0, $limit = NULL, $offset = NULL, $sort_by = 'ts.last_modified_on', $sort_order = 'DESC') {
        $this->db->select('tm.tenant_name, ts.tenant_id, ts.subsidy_id, ts.subsidy_type, ts.subsidy_amount, '
                . 'ts.last_modified_by, ts.last_modified_on');
        $this->db->from('tenant_subsidy ts');
        $this->db->join('tenant_master tm','tm.tenant_id = ts.tenant_id');        
        $this->db->order_by($sort_by, $sort_order);        
        if (!empty($tenant_id)) {
            $this->db->where('ts.tenant_id', $tenant_id);
        }
        if (!empty($offset)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }        
        return $this->db->get();
    }
    /**
     * This function to get the tenant details
     * @param type $tenant_id
     * @return type
     */
    public function get_tenant_details($tenant_id='') {
        $this->db->select('*')->from('tenant_master')->where('tenant_id <>', 'ISV01');
        if(!empty($tenant_id)) {
            $this->db->where('tenant_id', $tenant_id);
        }
        return $this->db->get();
    }

    /**
     * This function to create new tenant subsidy
     * @param type $tenant_id
     * @param type $subsidy_type
     * @param type $subsidy_amount
     * @return type
     */
    public function add_new_subsidy($tenant_id, $subsidy_type, $subsidy_amount) {
        if(empty($subsidy_type) || empty($subsidy_amount)|| empty($tenant_id)) {
            $status = FALSE;
        } else {
            $user_id = $this->session->userdata('userDetails')->user_id;
            $data = array(
                'tenant_id' => $tenant_id,
                'subsidy_type' => $subsidy_type,
                'subsidy_amount' => round($subsidy_amount,2),
                'created_by' => $user_id,
                'created_on' => date('Y-m-d H:i:s'),
                'last_modified_by' => $user_id,
                'last_modified_on' => date('Y-m-d H:i:s'),
                'status' => 'ACTIVE'
                );
            $status = $this->db->insert("tenant_subsidy",$data);
        }
        return $status;
    }
    /**
     * This method edit tenant subsidy
     * @param type $tenant_id
     * @param type $subsidy_id
     * @param type $subsidy_type
     * @param type $subsidy_amount
     * @return type
     */
    public function edit_subsidy($tenant_id, $subsidy_id, $subsidy_type, $subsidy_amount) {
        if(empty($subsidy_type) || empty($subsidy_amount)|| empty($tenant_id)) {
            $status = FALSE;
        } else {
            $user_id = $this->session->userdata('userDetails')->user_id;
            $data = array(
                'tenant_id' => $tenant_id,
                'subsidy_type' => $subsidy_type,
                'subsidy_amount' => round($subsidy_amount,2),                        
                'last_modified_by' => $user_id,
                'last_modified_on' => date('Y-m-d H:i:s'),
                'status' => 'ACTIVE'
                );
            $this->db->where("subsidy_id",$subsidy_id);
            $status = $this->db->update("tenant_subsidy",$data);
        }
        return $status;
    }
    /**
     * This function return tenant subsidy
     * @param type $subsidy_id
     * @return type
     */
    public function get_tenant_subsidy($subsidy_id) {
        $this->db->select('*')->from('tenant_subsidy')->where('subsidy_id', $subsidy_id);
        return $this->db->get();
    }
    /**
     * This function to get all tenant names
     * @param type $tenant_name
     * @return type
     */
    public function get_alltenant($tenant_name) {
        $this->db->select('tenant_name, tenant_id')->from('tenant_master');
        $this->db->where('tenant_id <>', 'ISV01');        
        if (!empty($tenant_name)) {
            $this->db->like('tenant_name', $tenant_name, 'both');
        }
        return $this->db->get()->result();
    }

}

?>
