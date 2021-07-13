<?php
class Dashboard_Model extends CI_Model {

    private $user;

    public function __construct() {
        parent::__construct();
        $this->load->helper('common');
        $this->user = $this->session->userdata('userDetails');
    }
    /*
     * This function used for classes_start_this_week list in dash board.
     */
    public function classes_start_this_week(){
        $week_start = date("Y-m-d", strtotime('last Sunday'));
        $week_end = date("Y-m-d", strtotime('saturday this week'));
        $query = "select cls.class_id, cls.class_start_datetime , cls.class_name, cls.classroom_location, 
		  cls.class_pymnt_enrol, cls.classroom_trainer, cls.classroom_venue_oth
		  from course_class cls
		  where DATE(cls.class_start_datetime) >='$week_start'
		  and DATE(cls.class_start_datetime) <= '$week_end'
		  and cls.class_status != 'INACTIV' and cls.tenant_id = '".$this->user->tenant_id."'
		  order by cls.class_start_datetime DESC";
	
        $res = $this->db->query($query);
        return $res->result();
    }
    /**
     * Ckasses starting this week
     * @return type
     */
    public function classes_start_this_week_bk() {
        $week_start = date("Y-m-d", strtotime('last Sunday'));
        $week_end = date("Y-m-d", strtotime('saturday this week'));

        $this->db->select("cls.class_id, cls.class_start_datetime , cls.class_name , GROUP_CONCAT(`usr`.`first_name`) as first_name, GROUP_CONCAT(`usr`.`last_name`) as last_name ,"
                . " cls.classroom_location, cls.class_pymnt_enrol");
        $this->db->from('course_class cls');
        $this->db->join('tms_users_pers usr', 'FIND_IN_SET(usr.user_id , cls.classroom_trainer)',"left");
        $this->db->where('DATE(cls.class_start_datetime) >=', $week_start);
        $this->db->where('DATE(cls.class_start_datetime) <=', $week_end);
        $this->db->where('cls.class_status !=', 'INACTIV');
        $this->db->where('cls.tenant_id', $this->user->tenant_id);
        $this->db->group_by('cls.class_id'); 
        $this->db->order_by('cls.class_start_datetime', 'DESC');
        $res = $this->db->get();        
        $result = $res->result();        
        return $result;
    }
    /*
     * This function used for pending_account_activation list in dash board.
     */
    public function pending_account_activation(){
        $query = "select usr.user_id, usr.account_type, pers.first_name, pers.last_name
                    from tms_users usr
                    inner join tms_users_pers pers on pers.user_id=usr.user_id and pers.tenant_id=usr.tenant_id
                    where usr.tenant_id='".$this->user->tenant_id."' and usr.account_status='PENDACT'
                    group by usr.user_id
                    order by usr.account_type DESC";
        $res = $this->db->query($query);
        return $res->result();
    }
    /**
     * Pending a/c activcation 
     * @return type
     */
    public function pending_account_activation_bk() {
        $this->db->select("usr.user_id, usr.account_type, pers.first_name, pers.last_name, rl.role_id,role.role_name, "
                . "cusr.company_id, cmst.company_name");
        $this->db->from("tms_users usr");
        $this->db->join("tms_users_pers pers", "usr.user_id = pers.user_id");
        $this->db->join('internal_user_role rl', "rl.user_id=pers.user_id", "left");
        $this->db->join("tms_roles role", "rl.role_id =role.role_id and rl.tenant_id=role.tenant_id", "left");
        $this->db->join("tenant_company_users cusr", "pers.user_id =cusr.user_id", "left");
        $this->db->join("company_master cmst", "cusr.company_id =cmst.company_id", "left");
        $this->db->where("usr.tenant_id", $this->user->tenant_id);
        $this->db->where("usr.account_status", 'PENDACT');
        $this->db->group_by('usr.user_id');
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where("cusr.company_id", $this->user->company_id);
        }        
        $this->db->order_by('usr.account_type', 'DESC');
        $result = $this->db->get();
        return $result->result();
    }
    /*
     * This function used for notifications list in dash board.
     */
    public function notifications() {
        $role_array = array('ADMN', 'CRSEMGR', 'SLEXEC', 'TRAINER');
        $my_role = $this->user->role_id;
        $noti_type_array = array();
        if (in_array($my_role, $role_array)) {
            $noti_type_array = array('ALLEBDDB', 'LNDEMALDB', 'INTEMLDD');
        }
        if ($my_role == 'COMPACT') {
            $noti_type_array = array('ALLEBDDB', 'LNDEMALDB');
        }
        
        if (!empty($noti_type_array)) {
            $this->db->select('noti.*');
            $this->db->from("custom_notifications noti");
            $this->db->where('noti.tenant_id', $this->user->tenant_id);
            $this->db->where_in('noti.noti_type', $noti_type_array);
            $this->db->where('noti.broadcast_from <=', date('Y-m-d'));
            $where = "(noti.broadcast_to >='" . date('Y-m-d') . "' OR noti.broadcast_to is NULL )";
            $this->db->where($where);
            $this->db->order_by('noti.created_on', 'DESC');
            $result = $this->db->get();
            return $result->result();
        } else {
            return FALSE;
        }
    }

    /*
     * This function used for pending_class list in dash board.
     */
    public function pending_class(){
        $tenant_id = $this->user->tenant_id;
        $extra_where = '';
        $trainer_where='';
        $crse_where='';
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = " AND FIND_IN_SET(".$this->user->user_id.",cls.classroom_trainer)";
        } elseif ($this->user->role_id == 'CRSEMGR') {
            $crse_where = " AND FIND_IN_SET(".$this->user->user_id.",crse.crse_manager)";
        } elseif ($this->user->role_id == 'COMPACT') {
            $extra_where .= " AND enrol.company_id='".$this->user->company_id."'";
        } elseif ($this->user->role_id == 'SLEXEC') {
            $extra_where .= " AND enrol.sales_executive_id='".$this->user->user_id."'";
        }
        $query = "SELECT DISTINCT inv.invoice_id, inv.inv_type, inv.total_inv_amount, inv.total_inv_amount,
                inv.company_id, enrol.user_id, enrol.enrolment_type, enrol.enrolled_on, crse.crse_name, 
                cls.class_id, cls.class_name, cls.class_start_datetime,
                usr.first_name, usr.last_name, tu.tax_code
                FROM class_enrol enrol
                INNER JOIN enrol_invoice inv ON enrol.pymnt_due_id = inv.pymnt_due_id
                INNER JOIN course crse ON enrol.course_id = crse.course_id $crse_where
                INNER JOIN course_class cls ON enrol.class_id = cls.class_id AND crse.course_id = cls.course_id $trainer_where
                INNER JOIN tms_users_pers usr ON enrol.user_id = usr.user_id
                INNER JOIN tms_users tu ON tu.user_id = enrol.user_id
                WHERE enrol.tenant_id='$tenant_id' AND enrol.payment_status IN ('NOTPAID','PARTPAID')
                $extra_where
                GROUP BY inv.invoice_id
                ORDER BY enrol.enrolled_on DESC";

        $res = $this->db->query($query);
        return $res->result();
    }
    /**
     * pending class
     * @return type
     */
    public function pending_class_bk() {
        if ($this->user->role_id == 'TRAINER') {                                   
            $trainer_where = 'AND FIND_IN_SET('.$this->user->user_id.',cls.classroom_trainer)';
        }
        if ($this->user->role_id == 'CRSEMGR') {                                   
            $crsemgr_where = 'AND FIND_IN_SET('.$this->user->user_id.',crse.crse_manager)';
        }
        $this->db->distinct();
        $this->db->select("inv.invoice_id, inv.inv_type, inv.total_inv_amount, inv.total_inv_amount,"
                . "inv.company_id, enrol.user_id, enrol.enrolment_type, enrol.enrolled_on, crse.crse_name, "
                . "cls.class_id, cls.class_name, comp.company_name, cls.class_start_datetime,"
                . "comp.comp_regist_num, usr.first_name, usr.last_name, tu.tax_code");
        $this->db->from("class_enrol enrol");
        $this->db->join("enrol_invoice inv", "enrol.pymnt_due_id = inv.pymnt_due_id");
        $this->db->join("course crse", "enrol.course_id = crse.course_id ".$crsemgr_where);
        $this->db->join("course_class cls", "enrol.class_id = cls.class_id AND crse.course_id = cls.course_id ".$trainer_where);
        $this->db->join("company_master comp", "comp.company_id = enrol.company_id", "left");
        $this->db->join("tms_users_pers usr", "enrol.user_id = usr.user_id");
        $this->db->join("tms_users tu", "tu.user_id = enrol.user_id");
        $this->db->where("enrol.tenant_id", $this->user->tenant_id);
        $this->db->where_in("enrol.payment_status", array("NOTPAID", "PARTPAID"));
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where("enrol.company_id", $this->user->company_id);
        }        
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }        
        $this->db->group_by("inv.invoice_id");
        $this->db->order_by('enrol.enrolled_on', 'DESC');
        $res = $this->db->get();        
        $result = $res->result();
        return $result;
    }
    /*
     * This function used for sales_commission_due list in dash board.
     */
    public function sales_commission_due() {
        $this->db->select("tup.first_name, tup.last_name, scd.pymnt_status, scd.sales_exec_id, "
                . "scd.comm_period_mth, scd.comm_period_yr, scd.comm_amount");
        $this->db->from("sales_comm_due scd");
        $this->db->join("tms_users_pers tup", "tup.user_id = scd.sales_exec_id");
        $this->db->where("scd.tenant_id", $this->user->tenant_id);
        $this->db->order_by('tup.first_name');
        $result = $this->db->get();
        return $result->result();
    }

}
