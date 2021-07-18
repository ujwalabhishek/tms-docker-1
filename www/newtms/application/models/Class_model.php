<?php

/*
 * This is the Model class for Class
 */

class Class_Model extends CI_Model {

    private $user;
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('common');
        $this->sess_user = $this->session->userdata('userDetails'); // added by shubhranshu to het the user data
        $this->user = $this->session->userdata('userDetails');
    }

    /**
     * check classname unique
     */
    public function check_classname_unique($tenant_id) {
        extract($_POST);
        $class_name = strtoupper(trim($class_name));

        $this->db->select('class_id');
        $this->db->from('course_class');
        $this->db->where('class_name', $class_name);
        $this->db->where('tenant_id', $tenant_id);
        if (!empty($class_id)) {
            $this->db->where('class_id !=', $class_id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * This method gets the sales executives who can sell this course
     * @param type $tenantId
     * @param type $courseId
     */
    public function get_course_salesexec($tenantId, $courseId) {
        $query = $this->db->query("SELECT sales.user_id, usr.first_name, usr.last_name
          FROM course_sales_exec sales, tms_users_pers usr
          WHERE usr.tenant_id = sales.tenant_id
          AND usr.user_id = sales.user_id
          AND sales.tenant_id = '$tenantId'
          AND sales.course_id ='$courseId'");
        return $query->result();
    }
     /**To gets sales executive based on course lavel*/
     public function get_course_salesexec1($tenantId, $courseId) {
        $query = $this->db->query("SELECT sales.user_id, usr.first_name, usr.last_name
          FROM course_sales_exec sales, tms_users_pers usr
          WHERE usr.tenant_id = sales.tenant_id
          AND usr.user_id = sales.user_id
          AND sales.tenant_id = '$tenantId'
          AND sales.course_id ='$courseId'");
        return $query->result();
    }

    /**
     * function to get commission payment due
     */
    public function get_commission_payment_due($tenant_id, $salesexec_id) {
        $result = $this->db->select('scd.comm_period_mth, scd.comm_period_yr, scd.course_id, scd.comm_amount,
                    scd.comm_detail, scd.pymnt_status, scd.pymnt_due_id, c.crse_name')
                        ->select_sum('scp.amount_paid')
                        ->from('sales_comm_due scd')
                        ->join('course c', 'c.course_id=scd.course_id')
                        ->join('sales_comm_pymnt scp', 'scp.pymnt_due_id=scd.pymnt_due_id', 'LEFT')
                        ->where('scd.tenant_id', $tenant_id)
                        ->where('scd.sales_exec_id', $salesexec_id)
                        ->where_in('scd.pymnt_status', array('NOTPAID', 'PARTPAID'))
                        ->order_by('scd.course_id', 'ASC')
                        ->order_by('scd.comm_period_yr', 'ASC')
                        ->order_by('scd.comm_period_mth', 'ASC')
                        ->group_by('scd.pymnt_due_id')
                        ->get()->result_object();
        //echo $this->db->last_query();exit;
        return $result;
    }

    /**
     * function to post commission values
     */
    public function update_commission_post($tenant_id, $user_id) {
        extract($_POST);
        if (!empty($trainee_selected)) {
            foreach ($trainee_selected as $pdi) {
                if (($trainee[$pdi] == 1) || ($trainee[$pdi] == 2)) {
                    $payment_status = ($amount_paying[$pdi] == $amount_check[$pdi]) ? 'PAID' : 'PARTPAID';
                    if ($payment_type == 'CASH') {
                        $data = array(
                            'pymnt_due_id' => $pdi,
                            'paid_on' => date('Y-m-d H:i:S', strtotime($cashpaid_on)),
                            'mode_of_payment' => $payment_type,
                            'amount_paid' => round($amount_paying[$pdi], 2),
                            'cheque_number' => NULL,
                            'cheque_date' => NULL,
                            'bank_name' => NULL,
                            'updated_by' => $user_id,
                            'updated_on' => date('Y-m-d'),
                            'sales_exec_id' =>$salesexec
                        );
                    } elseif ($payment_type == 'CHQ') {
                        $data = array(
                            'pymnt_due_id' => $pdi,
                            'paid_on' => date('Y-m-d H:i:S', strtotime($paid_on)),
                            'mode_of_payment' => $payment_type,
                            'amount_paid' => round($amount_paying[$pdi], 2),
                            'cheque_number' => $cheque_number,
                            'cheque_date' => date('Y-m-d', strtotime($cheque_date)),
                            'bank_name' => $bank_name,
                            'updated_by' => $user_id,
                            'updated_on' => date('Y-m-d'),
                            'sales_exec_id' =>$salesexec
                        );
                    }
                    $this->db->trans_start();

                    $this->db->insert('sales_comm_pymnt', $data);
                    $data = array('pymnt_status' => $payment_status);
                    $this->db->where('tenant_id', $tenant_id);
                    $this->db->where('sales_exec_id', $salesexec);
                    $this->db->where('pymnt_due_id', $pdi);
                    $this->db->update('sales_comm_due', $data);
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        return FALSE;
                    }
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * function to get commission paid to sales executive
     */
    public function get_commission_payment($tenant_id, $salesexec_id) {
        $result = $this->db->select('c.crse_name as course,d.comm_period_mth, d.comm_period_yr, p.paid_on, m.category_name,p.mode_of_payment, p.cheque_number, p.cheque_date, p.amount_paid')
                        ->from('sales_comm_due d')
                        ->join('sales_comm_pymnt p', 'p.pymnt_due_id=d.pymnt_due_id')
                        ->join('course c', 'c.course_id=d.course_id')
                        ->join('metadata_values m', 'm.parameter_id=p.mode_of_payment')
                        ->where('d.tenant_id', $tenant_id)
                        ->where('d.sales_exec_id', $salesexec_id)->where_in('d.pymnt_status', array('PAID', 'PARTPAID'))
                        ->order_by('p.pymnt_due_id', 'ASC')->get()->result_object();        
        return $result;
    }

    /**
     * function to get all the sales executive
     */
    public function get_all_sales_exec($tenant_id) {
        $result = $this->db->select('prs.user_id, prs.first_name, prs.last_name')
                        ->from('internal_user_role iur')->join('tms_users_pers prs', 'prs.user_id=iur.user_id')
                        ->where('iur.role_id', 'SLEXEC')
                        ->where('iur.tenant_id', $tenant_id)
                        ->order_by('prs.first_name', 'ASC')->get()->result_object();
        return $result;
    }

    /**
     * function to get all the sales executive
     */
    public function get_sales_exec_name($tenant_id, $user_id) {
        $result = $this->db->select('prs.user_id, prs.first_name, prs.last_name')
                        ->from('internal_user_role iur')->join('tms_users_pers prs', 'prs.user_id=iur.user_id')
                        ->where('iur.role_id', 'SLEXEC')
                        ->where('iur.tenant_id', $tenant_id)
                        ->where('iur.user_id', $user_id)
                        ->order_by('prs.first_name', 'ASC')->get()->row();
        return $result->first_name . ' ' . $result->last_name;
    }
    
    /* this function get the class details skm start */
    public function get_class_info($class_id)
    {
        $this->db->select('*');
        $this->db->from('course_class');
        $this->db->where('class_id',$class_id);
        $sql = $this->db->get();
        return $sql->row_array();
    }
    
    /* end */

    /**
     * This method gets only ACTIVE internal users for CLASS - ADD and EDIT
     * @param type $tenant_id
     * @param type $role_id
     * @return string
     */
    public function get_tenant_users_by_role($tenant_id, $role_id) {
        $this->db->select("pers.user_id, pers.first_name, pers.last_name, rl.role_id");
        $this->db->from("tms_users_pers pers");
        $this->db->join("internal_user_role rl", "pers.tenant_id = rl.tenant_id and pers.user_id = rl.user_id");
        $this->db->join("tms_users tu", "tu.user_id=pers.user_id");
        $this->db->where("tu.account_status", "ACTIVE");
        $this->db->where("pers.tenant_id", $tenant_id);
        $this->db->where("rl.role_id", $role_id);
        $this->db->order_by("pers.first_name");
        $result = $this->db->get();
        $tenant_users = array();
        foreach ($result->result() as $item) {
            $tenant_users[$item->user_id] = $item->first_name . ' ' . $item->last_name;
        }
        return $tenant_users;
    }

    /**
     * function to deactivate class
     */
    function deactivate_class($class_id) {
        $this->load->helper('common');
        foreach ($this->input->post() as $key => $value) {
            $$key = $value;
        }
        $deactivate_date = date('Y-m-d');
        $data = array(
            'class_status' => 'INACTIV',
            //'class_status' => 'DELETED',
            'deacti_date_time' => $deactivate_date,
            'deacti_reason' => $reason_for_deactivation,
            'deacti_reason_oth' => strtoupper($other_reason_for_deactivation),
            'deacti_by' => $this->session->userdata('userDetails')->user_id,
        );
        $this->db->where('tenant_id', $this->session->userdata('userDetails')->tenant_id);
        $this->db->where('class_id', $class_id);
        $this->db->trans_start();
        $this->db->update('course_class', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
    /**
     * this function to get all the class details by class_id
     */
    public function get_class_details($tenant_id, $class_id) {
        $this->db->select('*');
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('class_id', $class_id);
        $result = $this->db->get()->row();
        return $result;
    }

    /**
     * function to get class with assmnts
     */
    public function get_class_details_assmnts($tenant_id, $class_id) {
        $this->db->select('cc.*,cas.assmnt_type');
        $this->db->from('course_class cc');
        $this->db->join('class_assmnt_schld cas', 'cc.course_id = cas.course_id and cc.class_id = cas.class_id', 'left');
        $this->db->where('cc.tenant_id', $tenant_id);
        $this->db->where('cc.class_id', $class_id);
        $result = $this->db->get()->row();
        return $result;
    }

    /**
     * this function is to get the total count in class list page
     */
    public function get_all_class_count_by_tenant_id($tenant_id, $course_id, $class_id, $class_status) {
        $cur_date = date('Y-m-d', strtotime(date("Y-m-d")));
        if (empty($tenant_id)) {
            return 0;
        }
        $this->db->select('count(*) as totalrows');
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenant_id);
        if (!empty($course_id)) {
            $this->db->where('course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('class_id', $class_id);
        }
        if (!empty($class_status)) {
            switch ($class_status) {
                case 'IN_PROG':
                    $this->db->where('date(class_start_datetime) <=', $cur_date);
                    $this->db->where('date(class_end_datetime) >=', $cur_date);
                    break;

                case 'COMPLTD':
                    $this->db->where('date(class_end_datetime) <', $cur_date);
                    $this->db->where('date(class_start_datetime) <', $cur_date);
                    break;

                case 'YTOSTRT':
                    $this->db->where('date(class_start_datetime) >', $cur_date);
                    $this->db->where('date(class_end_datetime) >', $cur_date);
                    break;

                case 'INACTIV':
                    $this->db->where('class_status', 'INACTIV');
                    break;

                default:
                    break;
            }
            if ($class_status != 'INACTIV') {
                $this->db->where_not_in('class_status', 'INACTIV');
            }
        }
        $result = $this->db->get()->result();
        return $result[0]->totalrows;
    }

    /**
     * function to get active enrollments booked(please change get_class_booked_count method also.)
     */
    public function get_class_booked($course_id, $class_id, $tenant_id) {
        $this->db->select('count(*) as count');
        $this->db->from('class_enrol');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('course_id', $course_id);
        $this->db->where_in('enrol_status', array('ENRLACT', 'ENRLBKD'));
        return $this->db->get()->row()->count;
    }

    /**
     * function to get active enrollments booked count in view course list.
     */
    public function get_class_booked_count($course_id, $class_id, $tenant_id) {
        $this->db->select('count(*) as count');
        $this->db->from('class_enrol');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('course_id', $course_id);
        $this->db->where_in('enrol_status', array('ENRLACT', 'ENRLBKD'));
        if ($this->session->userdata('userDetails')->role_id == 'COMPACT') {
            $this->db->where("company_id", $this->session->userdata('userDetails')->company_id);
        }
        return $this->db->get()->row()->count;
    }

    /**
     * function to get active enrollments booked
     */
    public function get_class_booked_by_salesexec($course_id, $class_id, $tenant_id) {
        $this->db->select('count(*) as count');
        $this->db->from('class_enrol');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('course_id', $course_id);
        $this->db->where_in('enrol_status', array('ENRLACT', 'ENRLBKD'));
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('sales_executive_id', $this->user->user_id);
        }
        return $this->db->get()->row()->count;
    }

    /**
     * function to export all booked seats
     */
    public function export_all_booked_seats($tenant_id, $class_id, $sort_order, $sort_by) {
        $this->db->select('usrs.tax_code, usrs.registered_email_id,prs.first_name, prs.last_name,prs.contact_number,
            prs.personal_address_bldg, prs.personal_address_city, prs.personal_address_state, prs.personal_address_country, prs.personal_address_zip,
            usrs.account_type,usrs.country_of_residence,enrol.enrolled_on, prs.dob, enrol.payment_status, enrol.enrolment_mode, enrol.company_id');
        $this->db->from('class_enrol enrol');
        $this->db->join('tms_users usrs', 'usrs.user_id=enrol.user_id');
        $this->db->join('tms_users_pers prs', 'prs.user_id=usrs.user_id');
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('enrol.class_id', $class_id);
        $array = array('ENRLACT', 'ENRLBKD');
        $this->db->where_in('enrol.enrol_status', $array);
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('usrs.user_id', 'DESC');
        }
        $query = $this->db->get();
        return $query->result_object();
    }

    /**
     * function to get table data of booked seats
     */
    public function list_all_booked_seats($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL, $class_id) {
        $this->db->select('usrs.tax_code, usrs.registered_email_id, prs.first_name, prs.last_name, prs.contact_number,
            enrol.pymnt_due_id, ei.invoice_id, 
            prs.personal_address_bldg, prs.personal_address_city, prs.personal_address_state, prs.personal_address_country, prs.personal_address_zip,
            usrs.account_type, usrs.country_of_residence, enrol.enrolled_on, prs.dob, enrol.payment_status, usrs.user_id, enrol.enrolment_mode, enrol.company_id');
        $this->db->from('class_enrol enrol');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=enrol.pymnt_due_id', 'left');
        $this->db->join('tms_users usrs', 'usrs.user_id=enrol.user_id');
        $this->db->join('tms_users_pers prs', 'prs.user_id=usrs.user_id');
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('enrol.class_id', $class_id);
        $array = array('ENRLACT', 'ENRLBKD');
        $this->db->where_in('enrol.enrol_status', $array);
        if ($this->session->userdata('userDetails')->role_id == 'COMPACT') {
            $this->db->where("enrol.company_id", $this->session->userdata('userDetails')->company_id);
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('usrs.user_id', 'DESC');
        }
        if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
       $query = $this->db->get();
        return $query->result_object();
    }

    /**
     * This function to get the classses list 
     */
    public function list_all_class_by_tenant_id($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL, $course_id = '', $class_id = '', $class_status = '') 
   {
        $cur_date = date('Y-m-d');
        if ($offset <= 0 || empty($tenant_id)) {
            return;
        }
        $this->db->select("tpg_course_run_id,class_pymnt_enrol, class_id, course_id, class_name, class_start_datetime, "
                . "class_end_datetime, classroom_trainer,training_aide, "
                . "class_language, total_seats, class_status");
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenant_id);
        if (!empty($course_id)) 
        {
            $this->db->where('course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('class_id', $class_id);
        }
        if (!empty($tpg_course_run_id)) {
            $this->db->where('tpg_course_run_id', $tpg_course_run_id);
        }
        if (!empty($class_status)) {
            switch ($class_status) {
                case 'IN_PROG':
                    $this->db->where('date(class_start_datetime) <=', $cur_date);
                    $this->db->where('date(class_end_datetime) >=', $cur_date);
                    break;

                case 'COMPLTD':
                    $this->db->where('date(class_end_datetime) <', $cur_date);
                    $this->db->where('date(class_start_datetime) <', $cur_date);
                    break;

                case 'YTOSTRT':
                    $this->db->where('date(class_start_datetime) >', $cur_date);
                    $this->db->where('date(class_end_datetime) >', $cur_date);
                    break;

                case 'INACTIV':
                    $this->db->where('class_status', 'INACTIV');
                    break;
            }
            if ($class_status != 'INACTIV') {
                $this->db->where_not_in('class_status', 'INACTIV');
            }
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('last_modified_on', 'DESC');
        }
         if ($limit == $offset) {
            $this->db->limit($offset);
        } else if ($limit > 0) {
            $limitvalue = $offset - $limit;
            $this->db->limit($limit, $limitvalue);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    /* End the class */
    public function end_class($tenant_id,$class_id){
        
        $data= array("class_status"=>'COMPLTD');
        //$this->db->trans_start();
        $this->db->where('tenant_id',$tenant_id);
        $this->db->where('class_id',$class_id);
        $this->db->update('course_class',$data);
          return TRUE;
    }
    /**
     * this function to get the class status by its start and end date
     */
 public function get_class_status($cid, $status='') {
        if (!empty($status)) {
            switch ($status) {
                case 'IN_PROG':
                    return 'In-Progress';
                    break;
                case 'COMPLTD':
                    return 'Completed';
                    break;
                case 'YTOSTRT':
                    return 'Yet to Start';
                    break;
            }
        }
        $data = $this->db->select('class_status,class_start_datetime as start,class_end_datetime as end')
                        ->from('course_class')->where('class_id', $cid)->get()->row(0);
        $start = strtotime($data->start);
        $end = strtotime($data->end);
        $cur_date = strtotime(date("Y-m-d H:i:s"));
        if($data->class_status== 'COMPLTD'){
            return 'Completed';
        }
        if ($data->class_status == 'INACTIV') {
            return 'Inactive';
        } elseif ($start > $cur_date && $end > $cur_date) {
            return 'Yet to Start';
        } else if ($start <= $cur_date && $end >= $cur_date) {
            return 'In-Progress';
        } elseif ($end < $cur_date && $start < $cur_date) {
            return 'Completed';
        } elseif ($data->class_status == 'DELETED') {
            return 'DELETED';
        } else {
            return 'Status Unknown!!!!';  
        }
    }
/* class ennd time */
     public function get_end_date($cid) {
        
        $data = $this->db->select('class_status,date(class_start_datetime) as start,date(class_end_datetime) as end')
                        ->from('course_class')->where('class_id', $cid)->get()->row(0);
       
        $end = strtotime($data->end);
        $cur_date = strtotime(date("Y-m-d H:i:s"));
        return $end;
       
    }
    /**
     * this function get autocomplete course name
     */
    function get_course_autocomplete($tenant_id, $search_course_code) {
        if (!empty($search_course_code)) {
            $this->db->select('crse_name as label,course_id as id');
            $this->db->from('course');
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where_not_in('crse_status', 'INACTIV');
            if ($this->user->role_id == 'CRSEMGR') {
                $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse_manager) !=", 0);
            }
            $this->db->like('crse_name', $search_course_code, 'both');
            $results = $this->db->get()->result_object();
            return $results;
        }
    }

    /**
     * this function to export class page fields
     */
    public function get_class_list_export($tenant_id) {
        $cur_date = date('Y-m-d', strtotime(date("Y-m-d")));
        $course_id = $this->input->get('course_id');
        $class_id = $this->input->get('class_id');
        $class_status = $this->input->get('class_status');
        $sort_order = $this->input->get('0');
        $sort_by = $this->input->get('f');
        $this->db->select('*')->from('course_class')->where('tenant_id', $tenant_id);
        if (!empty($course_id)) {
            $this->db->where('course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('class_id', $class_id);
        }
        if (!empty($class_status)) {
            switch ($class_status) {
                case 'IN_PROG':
                    $this->db->where('date(class_start_datetime) <=', $cur_date);
                    $this->db->where('date(class_end_datetime) >=', $cur_date);
                    $this->db->where_not_in('class_status', 'INACTIV');
                    break;

                case 'COMPLTD':
                    $this->db->where('date(class_end_datetime) <', $cur_date);
                    $this->db->where('date(class_start_datetime) <', $cur_date);
                    $this->db->where_not_in('class_status', 'INACTIV');
                    break;

                case 'YTOSTRT':
                    $this->db->where('date(class_start_datetime) >', $cur_date);
                    $this->db->where('date(class_end_datetime) >', $cur_date);
                    $this->db->where_not_in('class_status', 'INACTIV');
                    break;
                
                case 'INACTIV':
                    $this->db->where('class_status', 'INACTIV');
                    break;
                default:
                    break;
            }
        }
        if ($this->user->role_id == 'TRAINER') {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",classroom_trainer) !=", 0);
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('last_modified_on', 'DESC');
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->traineelist_querychange();
        }
        $result = $this->db->get()->result_object();
        return $result;
    }

    /**
     * this function gets the default assessment schedule for class id
     */
    public function get_def_assessment($tenant_id, $class_id, $assmnt_type='') {
        if ($assmnt_type == 'DEFAULT') {
            $result = $this->db->select('*')->from('class_assmnt_schld')->where('tenant_id', $tenant_id)
                            ->where('class_id', $class_id)->get()->row();
        } elseif ($assmnt_type == 'CUSTOM') {
            $this->db->select('assmnt.*, trainee.user_id, usr.first_name, usr.last_name');
            $this->db->from('class_assmnt_schld assmnt');
            $this->db->join('class_assmnt_trainee trainee', 'trainee.assmnt_id=assmnt.assmnt_id AND trainee.course_id=assmnt.course_id AND trainee.class_id=assmnt.class_id', 'LEFT');
            $this->db->join('tms_users_pers usr', 'trainee.user_id = usr.user_id');
            $this->db->where('assmnt.tenant_id', $tenant_id);
            $this->db->where('assmnt.class_id', $class_id);

            $result = $this->db->get()->result();
        }
        return $result;
    }
    
    public function get_def_assessment_new($tenant_id, $class_id, $assmnt_type='') {
        if ($assmnt_type == 'DEFAULT') {
            $result = $this->db->select('*')->from('class_assmnt_schld')->where('tenant_id', $tenant_id)
                            ->where('class_id', $class_id)->get()->row();
        } elseif ($assmnt_type == 'CUSTOM') {
            $this->db->select('*');
            $this->db->from('class_assmnt_schld');
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where('class_id', $class_id);

            $result = $this->db->get()->result();
        }
        return $result;
    }

    /**
     * this function to get all class schedules
     */
    public function get_all_class_schedule($tenant_id, $cid) {
        $result = $this->db->query("select class_date, session_type_id, session_start_time,session_end_time
                from class_schld where tenant_id='$tenant_id' and class_id='$cid'
                order by class_date DESC, session_start_time ASC");
        return $result->result_array();
    }
    
     /* this function get the start date and time of class skm start */
    public function get_class_startdate_time($class_id)
    {
        $this->db->select('class_start_datetime');
        $this->db->from('course_class');
        $this->db->where('class_id',$class_id);
        //$this->db->where('tenant_id',TENANT_ID);
       $sql = $this->db->get()->row();
//       echo $this->db->last_query();
        return $sql->class_start_datetime;
       
    }
    
    /* end */
    

    /**
     * class sales executive details
     */
    function get_class_salesexec($tenant_id, $course_id, $ids) {
        if (empty($ids)) {
            return;
        }
        $ids = explode(',', $ids);
        $this->db->select('pers.user_id, pers.first_name, pers.last_name, sales.commission_rate');
        $this->db->from('tms_users_pers pers');
        $this->db->join('course_sales_exec sales', 'sales.user_id=pers.user_id');
        $this->db->where_in('pers.user_id', $ids);
        $this->db->where('pers.tenant_id', $tenant_id);
        $this->db->where('sales.course_id', $course_id);
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('pers.user_id', $this->user->user_id);
        }
        $result = $this->db->get();
        
        return $result->result_array();
    }
    ///////below function was added by shubhranshu for fetch of course sales executive assisgn
    function get_all_salesexec_course($tenant_id, $course_id) {
       if($this->user->role_id == 'ADMN') { 
            $this->db->select('pers.user_id, pers.first_name, pers.last_name, sales.commission_rate');
            $this->db->from('tms_users_pers pers');
            $this->db->join('course_sales_exec sales', 'sales.user_id=pers.user_id');
            $this->db->where('pers.tenant_id', $tenant_id);
            $this->db->where('sales.course_id', $course_id);
            if ($this->user->role_id == 'SLEXEC') {
                $this->db->where('pers.user_id', $this->user->user_id);
            }
            $result = $this->db->get();
            return $result->result_array();
       }else{
           return;
       }
    }
     /**
     * class sales executive details
     */
      function get_class_salesexec1($tenant_id,$course_id,$class_id,$ids='') 
                {
        if (empty($ids)) {
            return;
        }
        //$ids = explode(',', $ids);
        /*$this->db->select('pers.user_id, pers.first_name, pers.last_name, sales.commission_rate');
        $this->db->from('tms_users_pers pers');
        $this->db->join('course_sales_exec sales', 'sales.user_id=pers.user_id');
        $this->db->where('pers.user_id', $ids);
        $this->db->where('pers.tenant_id', $tenant_id);
        $this->db->where('sales.course_id', $course_id);*/
      /*  $this->db->select('pers.user_id, pers.first_name, pers.last_name');
        $this->db->from('tms_users_pers pers');
        $this->db->join('class_enrol ce', 'ce.sales_executive_id=pers.user_id');
        $this->db->where('pers.user_id', $ids);
        $this->db->where('pers.tenant_id', $tenant_id);
        $this->db->where('ce.course_id', $course_id);
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('pers.user_id', $this->user->user_id);
        }*/
         if (empty($ids)) {
            return;
        }
        $data = $this->db->select('sales_executive_id')
                        ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course_id)->where('class_id', $class_id)
                        ->where('user_id', $ids)->get()->row(0);

        $sales_executive_id = $data->sales_executive_id;

        //$ids = explode(',', $ids);
        //$this->db->select('pers.user_id, pers.first_name, pers.last_name, sales.commission_rate');
        $this->db->select('pers.user_id, pers.first_name, pers.last_name');
        $this->db->from('tms_users_pers pers');
        $this->db->where('pers.user_id', $sales_executive_id);
        $this->db->where('pers.tenant_id', $tenant_id);
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('pers.user_id', $this->user->user_id);
        }
        $result = $this->db->get();
        return $result->result_array();
    }


    /**
     * this function to get trainer names
     */    
    public function get_trainer_names($trainer_id) 
    {        
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_id);
        if (!empty($tids)) 
        {
            $this->load->model('course_model', 'course');
            $trainer_name = '';
            foreach ($tids as $tid) 
            {                
                $sql = "SELECT pers.user_id, pers.first_name, pers.last_name, rl.role_id FROM `tms_users_pers` pers, internal_user_role rl
        WHERE pers.tenant_id = rl.tenant_id AND pers.user_id = rl.user_id AND pers.tenant_id = '$tenantId' AND rl.role_id='TRAINER' AND rl.user_id='$tid'";                
                $query = $this->db->query($sql);

                $data = $query->row(0);
                $trainer = $data->first_name . ' ' . $data->last_name;

                $trainer_name .="$trainer,";
            }
            return rtrim($trainer_name, ',');
        }
    }
    //added by  pritam
     public function get_training_aide($trainer_id) 
    {        
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $tids = explode(',', $trainer_id);
        if (!empty($tids)) 
        {
            $this->load->model('course_model', 'course');
            $trainer_name = '';
            foreach ($tids as $tid) 
            {                
                $sql = "SELECT pers.user_id, pers.first_name, pers.last_name, rl.role_id FROM `tms_users_pers` pers, internal_user_role rl
        WHERE pers.tenant_id = rl.tenant_id AND pers.user_id = rl.user_id AND pers.tenant_id = '$tenantId' AND rl.user_id='$tid'";                
                $query = $this->db->query($sql);

                $data = $query->row(0);
                $trainer = $data->first_name . ' ' . $data->last_name;

                $trainer_name .="$trainer,";
            }
            return rtrim($trainer_name, ',');
        }
    }

    /**
     * fn to get class enroll students count
     */
    public function get_class_enroll_count($class_id) {
        $res = $this->db->select('count(*) as totalrow')->from('class_enrol')->where('class_id', $class_id)
                        ->where_in('enrol_status', array('ENRLACT', 'ENRLBKD'))->get()->row()->totalrow;
        return $res;
    }

    /**
     * this function get classes in a course for edit
     */
    public function get_course_class_for_edit($tenantId, $courseId) {
        $cur_date = date('Y-m-d');
        $this->db->select('class_id,class_name');
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenantId);
        $this->db->where('course_id', $courseId);
        $this->db->where_not_in('class_status', 'INACTIV');
        $this->db->order_by("DATE(class_start_datetime)", "DESC"); // added for class start date based sorting on Nov 24 2014.
        $query = $this->db->get();
        $result = array();
        foreach ($query->result() as $row) {
            $result[$row->class_id] = $row->class_name;
        }
        return $result;
    }

    /**
     * this function get classes in a course
     */
    public function get_course_class($tenantId, $courseId, $mark_attendance = NULL, $is_allclass = 0,$classTrainee=0) {
        
        $this->db->select('cc.class_id,cc.class_name,tpg_course_run_id');
        $this->db->from('course_class cc');
        $this->db->where('cc.tenant_id', $tenantId);
        $this->db->where('cc.course_id', $courseId);
        if (empty($is_allclass)) {
             $this->db->where('cc.class_status !=', 'INACTIV');
        }
        if ($this->sess_user->role_id == 'SLEXEC' && (string)$classTrainee=='classTrainee') {
            $this->traineelist_querychange_copy();
        }
         if ($this->sess_user->role_id == 'TRAINER') {
            $this->db->where("FIND_IN_SET(" . $this->sess_user->user_id . ",cc.classroom_trainer) !=", 0);
        }
        $this->db->order_by("DATE(cc.class_start_datetime)", "DESC"); // added for class start date based sorting on Nov 24 2014.
        $query = $this->db->get();   
        
        $result = array();
        foreach ($query->result() as $row) {
            $result[$row->class_id] = $row->class_name.'('.$row->tpg_course_run_id.')';
        }
        return $result;
    }
    
    ////added by shubhranshu to fetch course run id
    public function get_course_Run_id($tenantId, $courseId, $mark_attendance = NULL, $is_allclass = 0,$classTrainee=0) {
        
        $this->db->select('cc.tpg_course_run_id');
        $this->db->from('course_class cc');
        $this->db->where('cc.tenant_id', $tenantId);
        $this->db->where('cc.course_id', $courseId);
        if (empty($is_allclass)) {
             $this->db->where('cc.class_status !=', 'INACTIV');
        }
        if ($this->sess_user->role_id == 'SLEXEC' && (string)$classTrainee=='classTrainee') {
            $this->traineelist_querychange_copy();
        }
         if ($this->sess_user->role_id == 'TRAINER') {
            $this->db->where("FIND_IN_SET(" . $this->sess_user->user_id . ",cc.classroom_trainer) !=", 0);
        }
        $this->db->order_by("DATE(cc.class_start_datetime)", "DESC"); // added for class start date based sorting on Nov 24 2014.
        $query = $this->db->get();   
        
        $result = array();
        foreach ($query->result() as $row) {
            $result[$row->tpg_course_run_id] = $row->tpg_course_run_id;
        }
      
        return $result;
    }

    /**
     * This method gets the languages in a course
     * @param type $tenantId
     * @param type $courseId
     */
    public function get_course_language($tenantId, $courseId) {
        $this->db->select('language');
        $this->db->from('course');
        $this->db->where('tenant_id', $tenantId);
        $this->db->where('course_id', $courseId);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $languageIds = explode(',', $query->row()->language);
            if (!empty($languageIds)) {
                $course_language = array();
                foreach ($languageIds as $languageId) {
                    $languageId = trim($languageId);
                    $this->db->select('category_name');
                    $this->db->from('metadata_values');
                    $this->db->where('parameter_id', $languageId);
                    $languageText = $this->db->get()->row('category_name');
                    $course_language[$languageId] = $languageText;
                }
                return $course_language;
            }
        }
    }

    /**
     * function to get class by classid, tenantid using in seats booked page
     */
    public function get_class_by_classid($tenant_id, $class_id) {
        $data = $this->db->select('*')->from('course_class')
                        ->where('tenant_id', $tenant_id)->where('class_id', $class_id)->get()->row_array();
        return $data;
    }

    /**
     * This method copy's a class
     * @param type $tenantId
     * @param type $course_name
     * @param type $user_id
     * @return boolean
     */
    public function copy_classes($tenant_id, $course_name, $user_id, $data1){
        
       $class_name = $this->input->post('class_name');
       $start_date = $this->input->post('start_date');
       $start_time = $this->input->post('start_time');
       $end_date = $this->input->post('end_date');
       $end_time = $this->input->post('end_time');
       $copy_reason = $this->input->post('copy_reason');
       $class_id = $this->input->post('class_hid');
       
        $start_date_timestamp = date('Y-m-d H:i:s', strtotime($start_date . ' ' . $start_time . ':00'));
        $end_date_timestamp = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time . ':00'));
       // $data = $this->db->select('*')->from('course_class')
                        //->where('tenant_id', $tenantId)->where('class_id', $class_id)->get()->row_array();
        $cur_date = strtotime(date('Y-m-d'));
        $class_start_date = strtotime($start_date);
        $class_end_date = strtotime($end_date);
        if ($class_start_date > $cur_date && $class_end_date > $cur_date) {
            $class_status = 'YTOSTRT';
        } else if ($class_start_date <= $cur_date && $class_end_date >= $cur_date) {
            $class_status = 'IN_PROG';
        } else {
            $class_status = ' !!!!';  
        }
        $data['class_status'] = $class_status;
        $data['deacti_date_time'] = NULL;
        $data['deacti_reason'] = '';
        $data['deacti_reason_oth'] = '';
        $data['deacti_by'] = '';
        $data['class_id'] = '';
        $data['class_name'] = strtoupper($class_name);
        $data['class_start_datetime'] = $start_date_timestamp;
        $data['class_end_datetime'] = $end_date_timestamp;
        $data['certi_coll_date'] = NULL;
        $data['created_by'] = $user_id;
        $data['class_copied_from'] = $class_hid;
        $data['copied_by'] = $user_id;
        $data['copied_reason'] = $copy_reason;
        $data['copied_reason_oth'] = strtoupper($other_reason);
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['last_modified_by'] = $user_id;
        $data['last_modified_on'] = date('Y-m-d H:i:s');
        print_r($data); print_r($data1);exit;
        $this->db->trans_start();
        $course_class = $this->db->insert('course_class', $data);
        if ($course_class) {
            $class_id = $this->db->insert_id();
            if (empty($class_name)) {
                $class_name = $course_name.'_'.$class_id;                
                $this->db->where('class_id', $class_id);
                $this->db->update('course_class', array('class_name' => $class_name));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function copy_classes_backup($tenantId, $course_name, $user_id) {
        extract($_POST);
        $class_name = $class_name;
        $start_date_timestamp = date('Y-m-d H:i:s', strtotime($start_date . ' ' . $start_time . ':00'));
        $end_date_timestamp = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time . ':00'));
        $data = $this->db->select('*')->from('course_class')
                        ->where('tenant_id', $tenantId)->where('class_id', $class_hid)->get()->row_array();
        $cur_date = strtotime(date('Y-m-d'));
        $class_start_date = strtotime($start_date);
        $class_end_date = strtotime($end_date);
        if ($class_start_date > $cur_date && $class_end_date > $cur_date) {
            $class_status = 'YTOSTRT';
        } else if ($class_start_date <= $cur_date && $class_end_date >= $cur_date) {
            $class_status = 'IN_PROG';
        } else {
            $class_status = ' !!!!';  
        }

        $data['class_status'] = $class_status;
        $data['deacti_date_time'] = NULL;
        $data['deacti_reason'] = '';
        $data['deacti_reason_oth'] = '';
        $data['deacti_by'] = '';
        $data['class_id'] = '';
        $data['class_name'] = strtoupper($class_name);
        $data['class_start_datetime'] = $start_date_timestamp;
        $data['class_end_datetime'] = $end_date_timestamp;
        $data['certi_coll_date'] = NULL;
        $data['created_by'] = $user_id;
        $data['class_copied_from'] = $class_hid;
        $data['copied_by'] = $user_id;
        $data['copied_reason'] = $copy_reason;
        $data['copied_reason_oth'] = strtoupper($other_reason);
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['last_modified_by'] = $user_id;
        $data['last_modified_on'] = date('Y-m-d H:i:s');
        $this->db->trans_start();

        $course_class = $this->db->insert('course_class', $data);
        if ($course_class) {
            $class_id = $this->db->insert_id();
            if (empty($class_name)) {
                $class_name = $course_name.'_'.$class_id;                
                $this->db->where('class_id', $class_id);
                $this->db->update('course_class', array('class_name' => $class_name));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This method updates a class for a course
     * @param type $tenantId
     * @param type $userId
     * @param type $courseId
     */
    public function update_class($tenantId, $userId) {
        $display_class = 0;
        $control_4 = '';
        $control_5 = '';
        $control_6 = '';
        $control_7 = '';
        $control_3 = '';
        extract($_POST);
        $class_id = $class_hid;
        $class_details = $this->get_class_details($tenantId, $class_hid);
        $course_name = $this->db->select('crse_name')->from('course')->where('course_id', $class_details->course_id)->get()->row()->crse_name;
        $start_date_timestamp = date('Y-m-d H:i:s', strtotime($start_date . ' ' . $start_time . ':00'));
        $end_date_timestamp = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time . ':00'));
        if (empty($class_name)) {
            $class_name = $course_name.'-'.$class_id;
        }
        if ($coll_date) {
            $coll_date = date('Y-m-d', strtotime($coll_date));
        } else {
            $coll_date = NULL;
        }
        $description = htmlspecialchars($description, ENT_QUOTES);
        if (!empty($control_4)) {
            $control_4 = implode(",", $control_4);
        }
        if (!empty($control_5)) {
            $control_5 = implode(",", $control_5);
        }
        if (!empty($control_6)) {
            $control_6 = implode(",", $control_6);
        }
        if (!empty($control_7)) {
            $control_7 = implode(",", $control_7);
        }
        if (!empty($control_3)) {
            $control_3 = implode(",", $control_3);
        }
        $class_name = strtoupper($class_name);
        $cur_date = strtotime(date('Y-m-d'));
        $class_start_date = strtotime($start_date);
        $class_end_date = strtotime($end_date);
        if ($class_start_date > $cur_date && $class_end_date > $cur_date) {
            $class_status = 'YTOSTRT';
        } else if ($class_start_date <= $cur_date && $class_end_date >= $cur_date) {
            $class_status = 'IN_PROG';
        } else {
            $class_status = 'COMPLTD';  
        }
        $control_4 = empty($control_4) ? NULL : $control_4;
        $classroom_venue_oth = empty($classroom_venue_oth) ? NULL : strtoupper($classroom_venue_oth);
        $lab_venue_oth = empty($lab_venue_oth) ? NULL : strtoupper($lab_venue_oth);
       
            $data_class = array(
            'tenant_id' => $tenantId,
            'class_name' => strtoupper($class_name),
            'tpg_course_run_id' => $tpg_course_run_id,
            'class_start_datetime' => $start_date_timestamp,
            'class_end_datetime' => $end_date_timestamp,
            'total_seats' => $total_seats,
            'total_classroom_duration' => $cls_duration,
            'total_lab_duration' => $lab_duration,
            'assmnt_duration' => $class_assmnt_duration,
            'class_fees' => $fees,
            'class_discount' => $class_discount,
            'certi_coll_date' => $coll_date,
            'class_session_day' => $sessions_perday,
            'class_pymnt_enrol' => $payment_details,
            'classroom_location' => $cls_venue,
            'lab_location' => $lab_venue,
            'class_language' => $languages,
            'description' => $description,
            'display_class_public' => $display_class,
            'min_reqd_students' => $minimum_students,
            'min_reqd_noti_freq1' => $reminder1,
            'min_reqd_noti_freq2' => $reminder2,
            'min_reqd_noti_freq3' => $reminder3,
            'classroom_trainer' => $control_5,
            'lab_trainer' => $control_6,
            'assessor' => $control_7,
            'training_aide' => $control_3,
            'sales_executive' => $control_4,
            'last_modified_by' => $userId,
            'last_modified_on' => date('Y-m-d H:i:s'),
            'class_status' => $class_status,
            'classroom_venue_oth' => $classroom_venue_oth,
            'lab_venue_oth' => $lab_venue_oth
        );
        
        
        
        $this->db->where('tenant_id', $tenantId);
        $this->db->where('class_id', $class_id);
        $this->db->trans_start();

        $update_result = $this->db->update('course_class', $data_class);
        if ($update_result) {
            $totalbooked = $this->get_class_booked($course_id, $class_id, $tenantId);
            if (!empty($totalbooked)) {
                $content = '';
                $trigger_mail = 0;
                $class_schedule_check = $this->check_class_schedule_update($class_id);
                $assmnt_check = $this->check_class_assmnt_update($class_id);
                $curr_date = strtotime(date('Y-m-d'));
                if(strtotime(date('Y-m-d', strtotime($class_details->class_start_datetime))) < $curr_date && strtotime(date('Y-m-d', strtotime($class_details->class_end_datetime))) <$curr_date){
                    $trigger_mail = 0;
                } else if (($class_details->class_start_datetime != $start_date_timestamp) || ($class_details->class_end_datetime != $end_date_timestamp) || ($class_details->classroom_location != $cls_venue) || ($class_schedule_check == FALSE) || $assmnt_check == 'DEFAULT FAILED' || $assmnt_check == 'CUSTOM FAILED') {
                    $trigger_mail = 1;
                    $content = 'Your <b>' . $course_name . ' - ' . $class_details->class_name . '</b> scheduled for ' . date('F j Y, h:i A', strtotime($class_details->class_start_datetime)) . ' has been modified as follows: <br/><br/>';
                    $content .= '<b>Class Start Date-Time:</b> ' . date('F j Y, h:i A', strtotime($start_date_timestamp)) . '. <br/>';
                    $content .= '<b>Class End Date-Time:</b> ' . date('F j Y, h:i A', strtotime($end_date_timestamp)) . '. <br/>';
                    if ($cls_venue == 'OTH') {
                        $class_venue = $classroom_venue_oth;
                    } else {
                        $class_venue = $this->db->select('category_name')->from('metadata_values')->where('parameter_id', $cls_venue)->get()->row()->category_name;
                    }
                    $content .= '<b>Classroom Venue:</b> ' . $class_venue . '. <br/>';
                    if (($class_schedule_check == FALSE)) {
                        $content .= '<b><u>Classroom schedule changed.</u></b><br/><br/>';
                        $content .= $this->build_class_shedule();
                    }
                    $subject = 'Your TMS Class information has changed - ';
                    $subject_label = '';
                    if (($class_details->class_start_datetime != $start_date_timestamp) || ($class_details->class_end_datetime != $end_date_timestamp)) {
                        $subject_label .= 'Class Timings';
                    }

                    if ($class_details->classroom_location != $cls_venue) {
                        $subject_label .= ', Classroom Venue';
                    }
                    if ($class_schedule_check == FALSE) {
                        $subject_label .= ', Classroom Schedule';
                    }
                    if ($assmnt_check == 'DEFAULT FAILED' || $assmnt_check == 'CUSTOM FAILED') {
                        $subject_label .= ', Assessment Schedule';
                    }
                    $subject = $subject . trim($subject_label, ',');
                }
                if ($trigger_mail == 1) {
                    $tenant_details = fetch_tenant_details($tenantId);
                    $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);
                    $footer_data=str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);
                    $this->db->select('usrs.user_id,usrs.registered_email_id as email, pers.first_name, pers.last_name, pers.gender');
                    $this->db->from('class_enrol enrol');
                    $this->db->join('tms_users usrs', 'usrs.user_id=enrol.user_id');
                    $this->db->join('tms_users_pers pers', 'pers.user_id=enrol.user_id and pers.user_id=usrs.user_id');
                    $this->db->where('enrol.tenant_id', $tenantId);
                    $this->db->where('enrol.class_id', $class_id);
                    $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
                    $emails = $this->db->get()->result_object();
                    $mail_arr = array();
                    foreach ($emails as $row) {
                        $assmnt_content = '';
                        if ($assmnt_check == 'DEFAULT FAILED' || $assmnt_check == 'CUSTOM FAILED') {
                            $assmnt_content = $this->class_assmnt_content($row->user_id, $assmnt_check);
                            if ($assmnt_check == 'DEFAULT FAILED' && empty($assmnt_content)) {
                                $prev_assmnt_date = $this->get_previous_assmnt_date($class_id,$row->user_id);
                                 $assmnt_content = '<br/><br/>Your Assessment Scheduled for \''.$prev_assmnt_date.'\' has been cancelled. Kindly get in touch with your Administrator for the new Assessment Dates.';
                            }
                            if ($assmnt_check == 'CUSTOM FAILED' && empty($assmnt_content)) {
                                $assmnt_content = '<br/><br/><b>Kindly contact your Trainer for the assessment date.</b><br/>';
                            }
                        }
                        $mailcontent = $content . $assmnt_content;
                        $mail_arr[] = array(
                            'email' => $row,
                            'content' => $mailcontent,
                            'subject' => $subject,
                            'footer' => $footer_data
                        );
                    }
                    $def_schded_date = $this->input->post('def_schlded_date');
                    $assmnt_trinee = $this->input->post('assmnt_trainee');
                    if (!empty($def_schded_date)) { //$assmnt_check == 'DEFAULT FAILED' && 
                        $mailcontent = $content . $assmnt_content;
                    } elseif (!empty($assmnt_trinee)) {
                        $msg = '<br/><br/>Custom Assessment dates has changed for this class. Please check the class details in the application.<br/>';
                        $mailcontent = $content . $msg;
                    } else {
                        $msg = '<br/><br/>Assessment dates has changed for this class. Please check the class details in the application.<br/>';
                        $mailcontent = $content . $msg;
                    }
                    $admin = $this->session->userdata('userDetails');
                    $admin->email = $admin->registered_email_id;
                    $mail_arr[] = array(
                        'email' => $admin,
                        'content' => $mailcontent,
                        'subject' => $subject,
                        'footer' => $footer_data
                    );
                }
            }
            $this->db->where('tenant_id', $tenantId);
            $this->db->where('class_id', $class_id);
            $delete_result = $this->db->delete('class_schld');
            if (!empty($schlded_date)) {
                foreach ($schlded_date as $k => $v) {
                    $class_date = date('Y-m-d', strtotime($schlded_date[$k]));
                    $session_start_time = $schlded_start_time[$k] . ':00';
                    $session_end_time = $schlded_end_time[$k] . ':00';
                    $class_schld_data = array(
                        'tenant_id' => $tenantId,
                        'course_id' => $course_id,
                        'class_id' => $class_id,
                        'class_date' => $class_date,
                        'session_type_id' => $schlded_session_type[$k],
                        'session_start_time' => $session_start_time,
                        'session_end_time' => $session_end_time
                    );
                    $this->db->insert('class_schld', $class_schld_data);
                }
            }
            $this->db->where('tenant_id', $tenantId);
            $this->db->where('class_id', $class_id);
            $delete_result = $this->db->delete('class_assmnt_schld');
            $this->db->where('tenant_id', $tenantId);
            $this->db->where('class_id', $class_id);
            $delete_result = $this->db->delete('class_assmnt_trainee');
            if (isset($def_schlded_date)) {
                $assmt_date = date('Y-m-d', strtotime($def_schlded_date));
                $assmt_start_time = $def_schlded_start_time . ':00';
                $assmt_end_time = $def_schlded_end_time . ':00';
                $def_schlded_venue_oth = empty($def_schlded_venue_oth) ? NULL : $def_schlded_venue_oth;
                $class_assmnt_data = array(
                    'tenant_id' => $tenantId,
                    'course_id' => $course_id,
                    'class_id' => $class_id,
                    'assmnt_date' => $assmt_date,
                    'assmnt_start_time' => $assmt_start_time,
                    'assmnt_end_time' => $assmt_end_time,
                    'assessor_id' => rtrim($def_schlded_assessor, ','),
                    'assmnt_venue' => $def_schlded_venue,
                    'assmnt_type' => 'DEFAULT',
                    
                    'assmnt_venue_oth' => strtoupper($def_schlded_venue_oth),
                );
                $this->db->insert('class_assmnt_schld', $class_assmnt_data);
            }

            if (isset($assmnt_date)) {
                foreach ($assmnt_date as $k => $v) {
                    $assmnt_date = date('Y-m-d', strtotime($v));
                    $assmt_start_time = $assmnt_start_time[$k] . ':00';
                    $assmt_end_time = $assmnt_end_time[$k] . ':00';
                    $assm_venue_oth = (empty($ass_venue_oth[$k]))?NULL:$ass_venue_oth[$k];
                    $class_assmnt_data = array(
                        'tenant_id' => $tenantId,
                        'course_id' => $course_id,
                        'class_id' => $class_id,
                        'assmnt_date' => $assmnt_date,
                        'assmnt_start_time' => $assmt_start_time,
                        'assmnt_end_time' => $assmt_end_time,
                        'assessor_id' => rtrim($assmnt_assessor[$k], ','),
                        'assmnt_venue' => $ass_venue[$k],
                        'assmnt_type' => 'CUSTOM',
                       
                        'assmnt_venue_oth' => strtoupper($assm_venue_oth),
                    );
                    $this->db->insert('class_assmnt_schld', $class_assmnt_data);
                    $assmnt_id = $this->db->insert_id();
                    $user_id = rtrim($assmnt_trainee[$k], ',');
                    $uids = explode(',', $user_id);
                    foreach ($uids as $uid) {
                        $class_assmnt_trainee = array(
                            'tenant_id' => $tenantId,
                            'course_id' => $course_id,
                            'class_id' => $class_id,
                            'assmnt_date' => $assmnt_date,
                            'user_id' => $uid,
                            'assmnt_id' => $assmnt_id
                        );
                        $this->db->insert('class_assmnt_trainee', $class_assmnt_trainee);
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            } else {
                if (!empty($mail_arr) && $tenantId!='T02') {
                    foreach ($mail_arr as $mail_row) {
                        $this->class_update_send_email($mail_row['email'], $mail_row['content'], $mail_row['subject'], $mail_row['footer']);
                    }
                }
            }

            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * class assessment
     * @param type $user_id
     * @param type $assmnt_check
     * @param type $is_admin
     * @return string
     */
    public function class_assmnt_content($user_id, $assmnt_check, $is_admin = 0) {
        $def_schlded_date = $this->input->post('def_schlded_date');
        $assmnt_date = $this->input->post('assmnt_date');
        $assmnt_trainee = $this->input->post('assmnt_trainee');
        if (!empty($def_schlded_date)) { //$assmnt_check == 'DEFAULT FAILED'
            $date = date('d/m/Y', strtotime($def_schlded_date));
            return '<b>Your Assessment Date has Changed.</b><br/>Assessmemt Date: <b>' . $date . '</b>';
        } elseif (!empty($assmnt_trainee)) { //$assmnt_check == 'CUSTOM FAILED'
            $dateExist = FALSE;
            foreach ($assmnt_trainee as $k => $row) {
                $ass_user = explode(',', $row);
                if (in_array($user_id, $ass_user)) {
                    $date = date('d/m/Y', strtotime($assmnt_date[$k]));
                    $dateExist = TRUE;
                    return '<b>Your Assessment Date has Changed.</b><br/>Assessmemt Date: <b>' . $date . '</b>';
                }
            }
            if (!$dateExist)
                return '<b>Assessment date is not available.</b><br/>';
        }
    }

    /**
     * user details Copied from internal_user_model
     */
    public function get_user_details($tenant_id, $user_id) {
        $this->db->select('*');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id and usr.tenant_id = pers.tenant_id');
        $this->db->join('internal_user_emp_detail emp', 'usr.user_id = emp.user_id and usr.tenant_id = emp.tenant_id');
        $this->db->join('internal_user_role role', 'usr.user_id = role.user_id and usr.tenant_id = role.tenant_id');
        $this->db->join('tms_roles', 'role.tenant_id = tms_roles.tenant_id and tms_roles.role_id=role.role_id');
        $this->db->where('usr.user_id', $user_id);
        $this->db->where('usr.tenant_id', $tenant_id);
        $qry = $this->db->get();
        if ($qry->num_rows() > 0) {
            return $qry->row();
        } else {
            return false;
        }
    }
/**
 * class asssessment update
 * @param type $class_id
 * @return string
 */
    public function check_class_assmnt_update($class_id) {
        $result = $this->db->select('assmnt_date,assmnt_start_time, assmnt_end_time, assessor_id,
                assmnt_venue, assmnt_type, assmnt_id')
                        ->from('class_assmnt_schld')->where('class_id', $class_id)->get()->result();
        if ($result) {
            $row = $result[0];
            if ($row->assmnt_type == 'DEFAULT') {
                $def_schlded_date = $this->input->post('def_schlded_date');
                $def_schlded_start_time = $this->input->post('def_schlded_start_time');
                $def_schlded_end_time = $this->input->post('def_schlded_end_time');
                $def_schlded_assessor = $this->input->post('def_schlded_assessor');
                $def_schlded_date = $this->input->post('def_schlded_date');
                $def_schlded_venue = $this->input->post('def_schlded_venue');
                $date = date('Y-m-d', strtotime($def_schlded_date));
                $assmnt_entered = $row->assmnt_date . '__' . $row->assmnt_start_time . '__' . $row->assmnt_end_time . '__' . $row->assmnt_venue;
                $assmnt_posted = $date . '__' . $def_schlded_start_time . ':00__' . $def_schlded_end_time . ':00__' . $def_schlded_venue;
                if ($assmnt_entered != $assmnt_posted) {
                    return 'DEFAULT FAILED';
                }
                return 'DEFAULT PASSED';
            } elseif ($row->assmnt_type == 'CUSTOM') {
                $assmnt_date = $this->input->post('assmnt_date');
                $assmnt_start_time = $this->input->post('assmnt_start_time');
                $assmnt_end_time = $this->input->post('assmnt_end_time');
                $assmnt_assessor = $this->input->post('assmnt_assessor');
                $assmnt_trainee = $this->input->post('assmnt_trainee');
                $ass_venue = $this->input->post('ass_venue');
                $db_arr = array();
                if (count($result) == count($assmnt_date)) {
                    foreach ($result as $row) {
                        $trainee = $this->get_assmnt_trainee($row->assmnt_id);
                        $db_arr[] = $row->assmnt_date . '__' . $row->assmnt_start_time . '__' . $row->assmnt_end_time . '__' . $trainee . '__' . $row->assmnt_venue;
                    }
                    $post_arr = array();
                    foreach ($assmnt_date as $k => $row) {
                        $date = date('Y-m-d', strtotime($row));
                        $post_arr[] = $date . '__' . $assmnt_start_time[$k] . ':00__' . $assmnt_end_time[$k] . ':00__' . rtrim($assmnt_trainee[$k], ',') . '__' . $ass_venue[$k];
                    }
                    if (empty($post_arr)) {
                        return 'CUSTOM FAILED';
                    }
                    foreach ($post_arr as $row) {
                        if (!in_array($row, $db_arr)) {
                            return 'CUSTOM FAILED';
                        }
                    }
                    return 'CUSTOM PASSED';
                } else {
                    return 'CUSTOM FAILED';
                }
            }
        } else {
            $def_schlded_date = $this->input->post('def_schlded_date');
            $assmnt_date = $this->input->post('assmnt_date');
            if (!empty($def_schlded_date)) {
                return 'DEFAULT FAILED';
            } elseif (!empty($assmnt_date)) {
                return 'CUSTOM FAILED';
            }
            return 'ZERO RECORDS';
        }
    }
    /**
     * assessmeny trainee list
     * @param type $assmnt_id
     * @return type
     */
    public function get_assmnt_trainee($assmnt_id) {
        $res = $this->db->select('user_id')->from('class_assmnt_trainee')->where('assmnt_id', $assmnt_id)
                        ->get()->result();
        $out = array();
        foreach ($res as $row) {
            $out[] = $row->user_id;
        }
        return implode(',', $out);
    }
    /**
     * class schedule update
     * @param type $class_id
     * @return boolean
     */
    public function check_class_schedule_update($class_id) {
        $schlded_date = $this->input->post('schlded_date');
        $ses_type = $this->input->post('schlded_session_type');
        $schlded_start_time = $this->input->post('schlded_start_time');
        $schlded_end_time = $this->input->post('schlded_end_time');
        $result = $this->db->select('class_date, session_type_id, session_start_time, session_end_time')
                        ->from('class_schld')->where('class_id', $class_id)->get()->result();
        if (empty($result) && empty($schlded_date)) {
            return TRUE;
        }
        if (count($result) == count($schlded_date)) {
            $res_arr = array();
            foreach ($result as $row) {
                $res_arr[] = $row->session_type_id . '__' . $row->class_date . '__' . $row->session_start_time . '__' . $row->session_end_time;
            }
            $schld_arr = array();
            foreach ($schlded_date as $k => $row) {
                $date = date('Y-m-d', strtotime($row));
                $schld_arr[] = $ses_type[$k] . '__' . $date . '__' . trim($schlded_start_time[$k]) . ':00__' . trim($schlded_end_time[$k]) . ':00';
            }
            foreach ($schld_arr as $row) {
                if (!in_array($row, $res_arr)) {
                    return FALSE;
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * build class schedule
     * @return string
     */
    public function build_class_shedule() {

        $schlded_date = $this->input->post('schlded_date');
        $sess_arr = array('S1' => 1, 'BRK' => 2, 'S2' => 3);
        $ses_type = $this->input->post('schlded_session_type');
        $schlded_dat = array();
        foreach ($schlded_date as $k => $v) {
            $schlded_dat[] = array(
                'date' => $v,
                'key' => $k,
                'session' => $sess_arr[$ses_type[$k]]
            );
        }
        usort($schlded_dat, function($a1, $a2) {
            $value1 = strtotime($a1['date']);
            $value2 = strtotime($a2['date']);
            $rdiff = $value1 - $value2;
            if ($rdiff)
                return $rdiff;
            return $a1['session'] - $a2['session'];
        });

        $schlded_start_time = $this->input->post('schlded_start_time');
        $schlded_end_time = $this->input->post('schlded_end_time');
        $output = '<table border="1">
                <tr>
                    <td>Class Date</td>
                    <td>Session Type</td>
                    <td>Start Time</td>
                    <td>End Time</td>
                </tr>';
        if (!empty($schlded_dat)) {
            $sess_arr = array(
                'S1' => 'Session 1',
                'BRK' => 'Break',
                'S2' => 'Session 2'
            );
            foreach ($schlded_dat as $key => $row) {
                $k = $row['key'];
                $date = date('d/m/Y', strtotime($row['date']));
                $start = date('h:i A', strtotime(trim($schlded_start_time[$k])));
                $end = date('h:i A', strtotime(trim($schlded_end_time[$k])));
                $output .= '<tr><td>' . $date . '</td><td>' . $sess_arr[$ses_type[$k]] . '</td><td>' . $start . '</td><td>' . $end . '</td></tr>';
            }
        } else {
            $output .= '<tr><td colspan="4">There are no class schedule available</td></tr>';
        }
        $output .='</table><br/>';
        return $output;
    }

    /**
     * class update mail function 
     */
    public function class_update_send_email($user, $content, $subject, $footer_data) {
        $body = NULL;
        if ($user->gender == 'MALE') {
            $body = "Dear Mr." . $user->first_name.',';
        } else if ($user->gender == 'FEMALE') {
            $body .="Dear Ms." . $user->first_name.',';
        } else {
            $body .="Dear " . $user->first_name.',';
        }
        $body .='<br/><br/>' . $content . '<br/><br/>';
        $body .= $footer_data;
        return send_mail($user->email, '', $subject, $body);
    }

    /**
     * This method creates a class for a course
     * @param type $tenantId
     * @param type $userId
     * @param type $courseId
     */
    public function create_class($tenantId, $userId,$tpg_course_run_id) {
        $display_class = 0;
        $control_4 = '';
        $control_5 = '';
        $control_6 = '';
        $control_7 = '';
        $control_3 = '';
        extract($_POST);
        $start_date_timestamp = date('Y-m-d H:i:s', strtotime($start_date . ' ' . $start_time . ':00'));
        $end_date_timestamp = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time . ':00'));
        if ($coll_date) {
            $coll_date = date('Y-m-d', strtotime($coll_date));
        } else {
            $coll_date = NULL;
        }
        $description = htmlspecialchars($description, ENT_QUOTES);
        if (!empty($control_4)) {
            $control_4 = implode(",", $control_4);
        }
        if (!empty($control_5)) {
            $control_5 = implode(",", $control_5);
        }
        if (!empty($control_6)) {
            $control_6 = implode(",", $control_6);
        }
        if (!empty($control_7)) {
            $control_7 = implode(",", $control_7);
        }
        if (!empty($control_3)) {
            $control_3 = implode(",", $control_3);
        }
        $class_name = strtoupper($class_name);
        $cur_date = strtotime(date('d-m-Y'));
        $class_start_date = strtotime($start_date);
        $class_end_date = strtotime($end_date);        
        
        if ($class_start_date > $cur_date && $class_end_date > $cur_date) {
            $class_status = 'YTOSTRT';
        } else if ($class_start_date <= $cur_date && $class_end_date >= $cur_date) {
            $class_status = 'IN_PROG';
        } else {
            $class_status = 'COMPLTD';  
        }
        $control_4 = empty($control_4) ? NULL : $control_4;
        $classroom_venue_oth = empty($classroom_venue_oth) ? NULL : strtoupper($classroom_venue_oth);
        $lab_venue_oth = empty($lab_venue_oth) ? NULL : strtoupper($lab_venue_oth);     
            $data = array(
            'tenant_id' => $tenantId,
            'course_id' => $class_course,
            'class_name' => strtoupper($class_name),
            //'tpg_course_run_id' => strtoupper($tpg_course_run_id),
            'class_start_datetime' => $start_date_timestamp,
            'class_end_datetime' => $end_date_timestamp,
            'total_seats' => $total_seats,
            'total_classroom_duration' => $cls_duration,
            'total_lab_duration' => $lab_duration,
            'assmnt_duration' => $class_assmnt_duration,
            'class_fees' => $fees,
            'class_discount' => $class_discount,
            'certi_coll_date' => $coll_date,
            'class_session_day' => $sessions_perday,
            'class_pymnt_enrol' => $payment_details,
            'classroom_location' => $cls_venue,
            'lab_location' => $lab_venue,
            'class_language' => $languages,
            'description' => $description,
            'display_class_public' => $display_class,
            'min_reqd_students' => $minimum_students,
            'min_reqd_noti_freq1' => $reminder1,
            'min_reqd_noti_freq2' => $reminder2,
            'min_reqd_noti_freq3' => $reminder3,
            'classroom_trainer' => $control_5,
            'lab_trainer' => $control_6,
            'assessor' => $control_7,
            'training_aide' => $control_3,
            'sales_executive' => $control_4,
            'class_status' => $class_status,
            'created_by' => $userId,
            'created_on' => date('Y-m-d H:i:s'),
            'last_modified_by' => $userId,
            'last_modified_on' => date('Y-m-d H:i:s'),
            'classroom_venue_oth' => $classroom_venue_oth,
            'lab_venue_oth' => $lab_venue_oth,
            'venue_building' => $venue_building,
            'venue_block' => $venue_block,
            'venue_street' => $venue_street,
            'venue_room' => $venue_room,
            'venue_unit' => $venue_unit,
            'venue_postalcode' => $venue_postalcode,
            'venue_floor' => $venue_floor,
            'modeoftraining' => $modeoftraining,
            'survey_language' => $survey_language
            );  
        
        $this->db->trans_start();
        $this->db->insert('course_class', $data);
        $class_id = $this->db->insert_id();
        if ($class_id) {
            if (empty($class_name)) {
                $this->db->select("crse_name");
                $this->db->from("course");
                $this->db->where("course_id",$class_course);
                $course_result = $this->db->get();
                $course_name = $course_result->row("crse_name");
                $course_name = $course_name."_".$class_id;
                $this->db->where('class_id', $class_id);
                $this->db->update('course_class', array('class_name' => $course_name)); //coursename_classid
            }
            if (!empty($schlded_date)) {
                $ct =1;
                foreach ($schlded_date as $k => $v) {
                    if($schlded_session_type[$k] != 'BRK'){
                        $class_date = date('Y-m-d', strtotime($schlded_date[$k]));
                        $session_start_time = $schlded_start_time[$k] . ':00';
                        $session_end_time = $schlded_end_time[$k] . ':00';
                        $class_schld_data = array(
                        'tenant_id' => $tenantId,
                        'course_id' => $class_course,
                        'class_id' => $class_id,
                        'class_date' => $class_date,
                        'session_type_id' => $schlded_session_type[$k],
                        'tpg_session_id' => $crse_ref_no.'-'.$tpg_course_run_id.'-S'.$ct,
                        'mode_of_training' => $mode_of_training[$k],
                        'session_start_time' => $session_start_time,
                        'session_end_time' => $session_end_time
                        );
                         $ct +=1;
                    }else{
                        $class_date = date('Y-m-d', strtotime($schlded_date[$k]));
                        $session_start_time = $schlded_start_time[$k] . ':00';
                        $session_end_time = $schlded_end_time[$k] . ':00';
                        $class_schld_data = array(
                        'tenant_id' => $tenantId,
                        'course_id' => $class_course,
                        'class_id' => $class_id,
                        'class_date' => $class_date,
                        'session_type_id' => $schlded_session_type[$k],
                        'session_start_time' => $session_start_time,
                        'session_end_time' => $session_end_time
                        );
                    }
                    
                    $this->db->insert('class_schld', $class_schld_data);
                   
                }
            }
            if (isset($assmnt_date)) {
                foreach ($assmnt_date as $k => $v) {
                    $assmnt_date = date('Y-m-d', strtotime($v));
                    $assmt_start_time = $assmnt_start_time[$k] . ':00';
                    $assmt_end_time = $assmnt_end_time[$k] . ':00';
                    $assm_venue_oth = (empty($ass_venue_oth[$k]))?NULL:$ass_venue_oth[$k];
                    $class_assmnt_data = array(
                        'tenant_id' => $tenantId,
                        'course_id' => $class_course,
                        'class_id' => $class_id,
                        'assmnt_date' => $assmnt_date,
                         'mode_of_training' => '8',
                        'assmnt_start_time' => $assmt_start_time,
                        'assmnt_end_time' => $assmt_end_time,
                        'assessor_id' => rtrim($assmnt_assessor[$k], ','),
                        'assmnt_venue' => $ass_venues[$k],
                        'assmnt_type' => 'CUSTOM',
                       
                        'assmnt_venue_oth' => strtoupper($assm_venue_oth),
                    );
                    $this->db->insert('class_assmnt_schld', $class_assmnt_data);
                    $assmnt_id = $this->db->insert_id();
                   
                  
                    
                }
            }
            
            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }
            $res = array(
                'status' => TRUE,
                'classid' => $class_id
            );
            return $res;
        } else {
            return FALSE;
        }
    }
    /**
     * this function to get payment details of trainee enrolled
     */
    public function get_payment_received($tenant_id, $class_id, $user_id) {
        $this->db->select('epr.recd_on, prs.first_name, prs.last_name, epr.mode_of_pymnt, cc.class_fees, cc.class_discount, ei.total_inv_discnt,
            ei.total_inv_subsdy, ei.total_gst, ei.gst_rate, ei.total_inv_amount, epr.amount_recd, cc.class_name');
        $this->db->from('class_enrol ce');
        $this->db->join('tms_users_pers prs', 'prs.user_id=ce.user_id');
        $this->db->join('course_class cc', 'cc.class_id=ce.class_id');
        $this->db->join('enrol_pymnt_due epd', 'ce.pymnt_due_id =epd.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id =ce.pymnt_due_id');
        $this->db->join('enrol_paymnt_recd epr', 'epr.invoice_id =ei.invoice_id');
        $this->db->where('ce.tenant_id', $tenant_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.user_id', $user_id);
        $this->db->where('ce.enrol_status', 'ENRLACT');
        return $this->db->get()->row_array();
    }

    /*
     * This method gets the Class list for a courses of a tenant
     */
    public function get_class_list($tenantId, $courseId) {

        $this->db->select('class_id, class_name, class_start_datetime, class_end_datetime');
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenantId);
        $this->db->where('course_id', $courseId);

        $query = $this->db->get();

        $result = $query->result_array();

        $classes = array();
        foreach ($result as $item) {
            $classes[$item['class_id']] = $item['class_name'];
        }

        return $classes;
    }

    /**
     * This method gets the class details
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @return type
     */
    public function get_class_by_id($tenant_id, $course_id, $class_id) {

        $this->db->select('*');
        $this->db->from('course_class');

        $array = array('tenant_id' => $tenant_id, 'course_id' => $course_id, 'class_id' => $class_id);
        $this->db->where($array);

        $query = $this->db->get();

        $results = $query->result();
        return count($results) > 0 ? $results[0] : null;
    }

    /**
     * This method gets class details for pdf reports
     * @param $class_id
     */
    public function get_class_details_for_report($tenant_id, $course_id, $class_id) {
    
        $this->db->select('cc.tenant_id,cc.class_id, cc.class_session_day, cc.course_id, cc.class_name, cc.classroom_trainer, cc.assessor, cc.lab_trainer, c.crse_name, tm.tenant_name as company_name, cc.class_start_datetime, cc.class_end_datetime, c.crse_manager, c.competency_code, cc.total_seats');
        $this->db->select('cc.total_classroom_duration, cc.total_lab_duration, cc.assmnt_duration');
        $this->db->from('course_class cc');

        $this->db->join('course c', 'cc.course_id = c.course_id and cc.tenant_id = c.tenant_id');
        $this->db->join('tenant_master tm', 'tm.tenant_id = cc.tenant_id');

        $array = array('cc.tenant_id' => $tenant_id, 'cc.course_id' => $course_id, 'cc.class_id' => $class_id);
        $this->db->where($array);

        $query = $this->db->get();

//echo $this->db->last_query();exit;
        $results = $query->result();
        $details = count($results) > 0 ? $results[0] : null;
        if ($details != null) {
            $assessor = $details->assessor;
            $assessor_names = "";
            $assessor_ids = explode(",", $assessor);

            $classroom_trainer = $details->classroom_trainer;
            $classroom_trainer_names = "";
            $classroom_trainer_ids = explode(",", $classroom_trainer);

            $crse_manager = $details->crse_manager;
            $crse_manager_names = "";
            $crse_manager_ids = explode(",", $crse_manager);

            $lab_trainer = $details->lab_trainer;
            $lab_trainer_names = "";
            $lab_trainer_ids = explode(",", $lab_trainer);
            $this->db->select("user_id, first_name as name", FALSE);
            $this->db->from('tms_users_pers');
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where_in('user_id', array_merge($assessor_ids, $classroom_trainer_ids, $crse_manager_ids, $lab_trainer_ids));
            $query = $this->db->get();
            $results = $query->result_array();
            foreach ($results as $res) {
                $user_id = $res['user_id'];
                if (in_array($user_id, $assessor_ids)) {
                    $assessor_names .= $res['name'] . ", ";
                }
                if (in_array($user_id, $classroom_trainer_ids)) {
                    $classroom_trainer_names .= $res['name'] . ", ";
                }
                if (in_array($user_id, $crse_manager_ids)) {
                    $crse_manager_names .= $res['name'] . ", ";
                }
                if (in_array($user_id, $lab_trainer_ids)) {
                    $lab_trainer_names .= $res['name'] . ", ";
                }
            }
            $assessor_names = rtrim($assessor_names, ", ");
            $classroom_trainer_names = rtrim($classroom_trainer_names, ", ");
            $lab_trainer_names = rtrim($lab_trainer_names, ", ");
            $crse_manager_names = rtrim($crse_manager_names, ", ");

            $details->assessor = $assessor_names;
            $details->classroom_trainer = $classroom_trainer_names;
            $details->crse_manager = $crse_manager_names;
            $details->lab_trainer = $lab_trainer_names;
        }
        return $details;
    }

    public function get_class_booked_seats_count($class_id) {
        return $this->db->query("select count(*) as cnt from class_enrol where class_id = ?", $class_id)->row()->cnt;
    }

    /**
     * function to get trainee enrolled in a class
     */
    public function traineeenrolled_class($tenant_id, $class_id) {
        $result = $this->db->select('tup.first_name, tup.last_name, ce.user_id')->from('class_enrol ce')
                        ->join('tms_users_pers tup', 'tup.user_id=ce.user_id')->where('ce.tenant_id =', $tenant_id)
                        ->where('ce.class_id', $class_id)->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))->get()->result();
        return $result;
    }

    /**
     * This function for cron job
     */
    public function get_all_active_classes() {
        $this->db->select("crse.course_id, crse.crse_name , crse.crse_manager, cls.class_id, cls.class_name, DATE(cls.class_start_datetime) as class_start_date, cls.min_reqd_noti_freq1, cls.min_reqd_noti_freq2, cls.min_reqd_noti_freq3, cls.class_status");
        $this->db->from("course_class cls");
        $this->db->join("course crse", "crse.course_id = cls.course_id");
        $this->db->where("cls.class_status !=", "INACTIV");
        $this->db->where("DATE(cls.class_start_datetime) > ", date('Y-m-d'));
        $result1 = $this->db->get();
        return $result1->result();
    }

    /*
     * This method for getting the manager details for the course.
     * Author : dummy.
     * DAte :  15 Oct 2014.
     */

    public function get_manager_details($managers) {
        $managers_array = explode(",", $managers);
        $this->db->select("usr_pers.first_name, usr_pers.last_name, usr.registered_email_id");
        $this->db->from("tms_users usr");
        $this->db->join("tms_users_pers usr_pers", "usr.user_id = usr_pers.user_id");
        $this->db->where_in("usr.user_id", $managers_array);
        $result = $this->db->get();
        return $result->result();
    }

    /*
     * This method for getting the total entrollment of a course and class.
     */

    public function class_enrol_count($course, $class) {
        $this->db->select("count(*) as enrol_total");
        $this->db->from("class_enrol");
        $this->db->where("course_id", $course);
        $this->db->where("class_id", $class);
        $this->db->where("enrol_status != ", "RESHLD");
        $result = $this->db->get();
        return $result->row('enrol_total');
    }

    /**
     * role based access for salesexec
     */
    private function traineelist_querychange_copy() {
		$this->db->join("course_sales_exec sales", " cc.course_id=sales.course_id");		
		$this->db->where("sales.user_id", $this->user->user_id);
    }
	
	/**
     * role based access for salesexec
     */
    private function traineelist_querychange() {		
        $this->db->like('sales_executive', $this->user->user_id, 'both');
    }
    
    /**
     * this function to get the previous assessment date.
     */
    private function get_previous_assmnt_date($class_id, $user_id){
        $result = $this->db->select('assmnt_date')->from('class_assmnt_schld')
                ->where('class_id', $class_id)
                ->where('assmnt_type', 'DEFAULT')
                ->get();
        if ($result->num_rows() == 1) {
            $output =  $result->row('assmnt_date');
        } else {
            $output = $this->db->select('cas.assmnt_date, cas.assmnt_venue, cas.assmnt_venue_oth, cas.assessor_id')
                            ->from('class_assmnt_trainee cat')
                            ->join('class_assmnt_schld cas', 'cas.assmnt_id=cat.assmnt_id and cas.class_id=cat.class_id')
                            ->where('cat.class_id', $class_id)
                            ->where('cat.user_id', $user_id)
                            ->where('cas.assmnt_type', 'CUSTOM')->get()->row('assmnt_date');
        }
        return date('d/m/Y', strtotime($output));
    }
    /**
     * Selecting course manager names.
     * @param type $course_manager_id
     * @return type
     */    
    public function get_course_manager_names($course_manager_id) {          
        $tenantId = $this->session->userdata('userDetails')->tenant_id;
        $cids = explode(',', $course_manager_id);           
        if (!empty($cids)) {            
            $crse_mgr_name = '';
            foreach ($cids as $cid) {                  
                $sql = "SELECT pers.user_id, pers.first_name, pers.last_name FROM `tms_users_pers` pers, internal_user_role rl
                WHERE pers.tenant_id = rl.tenant_id AND pers.user_id = rl.user_id AND pers.tenant_id = '$tenantId' AND rl.role_id='CRSEMGR' AND rl.user_id='$cid'";                
                $query = $this->db->query($sql);
                $data = $query->row(0);
                $crse_mgr = $data->first_name . ' ' . $data->last_name;

                $crse_mgr_name .="$crse_mgr,";
            }                
            return rtrim($crse_mgr_name, ',');
        }
    }    

    
    public function get_class_name($tenant_id,$class_id){
    return $this->db->select('class_name')->from('course_class')->where('tenant_id',$tenant_id)->where('class_id',$class_id)->get()->row()->class_name;
    }
    
    public function get_Trainee_For_Assessments($tenant_id,$courseID,$classID,$userid=''){
        $today_date = date('Y-m-d');
        $str='';
        if($userid !=''){
            $str = "AND ce.user_id = '$userid'";
        }
         $sql = "SELECT
                cm.company_name,
                c.reference_num,
                c.course_id,
                cc.tpg_course_run_id,
                tu.tax_code,
                tu.tax_code_type,
                cc.class_id,
                ce.eid_number,
                cas.assmnt_date,
                ce.assessment_reference_No,
                ce.user_id,
                tup.first_name as fullname,
                CURDATE() as assessmentDate,
                c.competency_code as skillCode,
                ce.feedback_score,
                ce.feedback_grade,
                (CASE WHEN ce.training_score ='C' THEN 'Pass' WHEN ce.training_score ='EX' THEN 'Exempt' ELSE 'Fail' END) as 'result',
                cc.class_start_datetime,
                cc.class_end_datetime,
                cc.class_name,
                ce.training_score
                FROM ( course_class cc) 
                JOIN course c ON c.course_id = cc.course_id 
                JOIN class_enrol ce ON ce.class_id = cc.class_id 
                JOIN tms_users tu ON tu.user_id = ce.user_id 
                left join tms_users_pers tup on tup.user_id =ce.user_id 
                left join company_master cm on cm.company_id=ce.company_id
                left join class_assmnt_schld cas on cas.course_id = cc.course_id and cas.class_id = cc.class_id and cas.tenant_id = cc.tenant_id
                WHERE cc . tenant_id = '$tenant_id'
                AND c.course_id = '$courseID'
                AND cc.class_id = '$classID'
                AND ce.feedback_grade !=''
                AND ce.feedback_score !=0
                AND c.competency_code !=''
                AND c.reference_num !=''
                AND ce.training_score !='' $str
                AND date(cc.class_end_datetime) <= '$today_date'";                
                $result = $this->db->query($sql)->result();
                //echo $this->db->last_query();exit;
                return $result;
    }
    
    public function get_Trainee_For_Assessments_json($tenant_id,$courseID,$classID){
        
        $today_date = date('Y-m-d');
    
         $sql = "SELECT             
                tu.user_id,
                tu.tax_code
                FROM ( course_class cc) 
                JOIN course c ON c.course_id = cc.course_id 
                JOIN class_enrol ce ON ce.class_id = cc.class_id 
                JOIN tms_users tu ON tu.user_id = ce.user_id 
                left join tms_users_pers tup on tup.user_id =ce.user_id 
                left join company_master cm on cm.company_id=ce.company_id
                WHERE cc . tenant_id = '$tenant_id'
                AND c.course_id = '$courseID'
                AND cc.class_id = '$classID'
                AND ce.feedback_grade !=''
                AND ce.feedback_score !=0
                AND c.competency_code !=''
                AND c.reference_num !=''
                AND ce.training_score !=''
                AND date(cc.class_end_datetime) <= '$today_date'";                
                $res = $this->db->query($sql)->result();
                //echo $this->db->last_query();exit;
                $result = array();
                foreach ($res as $row) {
                    $result[$row->user_id] = $row->tax_code;
                }
               
                return $result;
    }
    
    public function get_single_trainee_for_assessment($tenant_id,$courseID,$classID,$userid){
        $today_date = date('Y-m-d');
        $str='';
        if($userid !=''){
            $str = "AND ce.user_id = '$userid'";
        }
         $sql = "SELECT
                cm.company_name,
                c.reference_num,
                c.course_id,
                cc.tpg_course_run_id,
                tu.tax_code,
                tu.tax_code_type,
                cc.class_id,
                ce.user_id,
                tup.first_name as fullname,
                CURDATE() as assessmentDate,
                c.competency_code as skillCode,
                ce.feedback_score,
                ce.feedback_grade,
                (CASE WHEN ce.training_score ='C' THEN 'Pass' ELSE 'Fail' END) as 'result',
                cc.class_start_datetime,
                cc.class_end_datetime,
                cc.class_name,
                ce.training_score
                FROM ( course_class cc) 
                JOIN course c ON c.course_id = cc.course_id 
                JOIN class_enrol ce ON ce.class_id = cc.class_id 
                JOIN tms_users tu ON tu.user_id = ce.user_id 
                left join tms_users_pers tup on tup.user_id =ce.user_id 
                left join company_master cm on cm.company_id=ce.company_id
                WHERE cc . tenant_id = '$tenant_id'
                AND c.course_id = '$courseID'
                AND cc.class_id = '$classID'
                AND ce.feedback_grade !=''
                AND ce.feedback_score !=0
                AND c.competency_code !=''
                AND c.reference_num !=''
                AND ce.training_score !='' $str
                AND date(cc.class_end_datetime) <= '$today_date'";                
                $result = $this->db->query($sql)->result();
                //echo $this->db->last_query();exit;
                return $result[0];
    }
    
    public function updateAssessmentRefNo($assment_ref_no,$course_id,$class_id,$user_id,$tenant_id){
        $this->db->trans_start();
        $data = array(
            'assessment_reference_No' => $assment_ref_no,
            'assessment_date' => date('Y-m-d')
            );
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('class_enrol', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    public function updateAssessmentData($score,$assessment_date,$grade,$user_id,$class_id,$course_id,$tenant_id){
        $this->db->trans_start();
        $data = array(
            'feedback_score' => $score,
            'feedback_grade' => $grade,
            'assessment_date' => date('Y-m-d', strtotime($assessment_date))
            );
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('class_enrol', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    public function get_tms_trainee_assessments($assessment_ref_no,$tenant_id){

         $sql = "SELECT
                cm.company_name,
                c.reference_num,
                c.course_id,
                cc.tpg_course_run_id,
                tu.tax_code,
                tu.tax_code_type,
                cc.class_id,
                ce.eid_number,
                ce.assessment_reference_No,
                ce.user_id,
                tup.first_name as fullname,
                CURDATE() as assessmentDate,
                c.competency_code as skillCode,
                ce.feedback_score,
                ce.feedback_grade,
                (CASE WHEN ce.training_score ='C' THEN 'Pass' ELSE 'Fail' END) as 'result',
                cc.class_start_datetime,
                cc.class_end_datetime,
                cc.class_name,
                ce.training_score
                FROM ( course_class cc) 
                JOIN course c ON c.course_id = cc.course_id 
                JOIN class_enrol ce ON ce.class_id = cc.class_id 
                JOIN tms_users tu ON tu.user_id = ce.user_id 
                left join tms_users_pers tup on tup.user_id =ce.user_id 
                left join company_master cm on cm.company_id=ce.company_id
                WHERE cc . tenant_id = '$tenant_id' AND assessment_reference_No='$assessment_ref_no'";             
                $result = $this->db->query($sql)->result();
                
                return $result;
    }


}

?>
