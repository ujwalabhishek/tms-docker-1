<?php

class Tpg_api_Model extends CI_Model {

/**
     * this function to get trainer names
     */    
    public function get_trainer_details($trainer_id) 
    {        
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_id);
        if (!empty($tids)) 
        {
            $this->load->model('course_model', 'course');
            
            foreach ($tids as $tid) 
            {                
                $sql = "SELECT pers.user_id,tu.registered_email_id, pers.first_name, pers.last_name, rl.role_id 
                        FROM `tms_users_pers` pers, internal_user_role rl, tms_users tu
                        WHERE pers.tenant_id = rl.tenant_id 
                        AND pers.user_id = rl.user_id 
                        AND tu.user_id = pers.user_id 
                        AND pers.tenant_id = '$tenantId' 
                        AND rl.role_id='TRAINER' 
                        AND rl.user_id='$tid' ";                
                $query = $this->db->query($sql);

                $data = $query->row(0);
                $trainer[] = $data;

                
            }
            return $trainer;
        }
    }
    
    public function updateCourseRunId($class_id,$crse_run_id){
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
         $data = array(
                    'tpg_course_run_id' => $crse_run_id
                );
        $this->db->trans_start();
        $this->db->where('class_id', $class_id);
        $this->db->where('tenant_id', $tenantId);
        $this->db->update('course_class', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        return true;
    }

}