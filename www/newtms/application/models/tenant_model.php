<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tenant_Model extends CI_Model
{
    //public $db;
    /**
     * Get tenant details by tenant id
     * @param type $tenant_id
     * @return type
     */
    public function get_tenant_details($tenant_id)
    {
        
        $this->db->select('*');
        $this->db->from('tenant_master ten');

        $this->db->where('ten.tenant_id', $tenant_id);

        $query = $this->db->get();

        $results = $query->result();

        return count($results) > 0 ? $results[0] : null;
    }
}
