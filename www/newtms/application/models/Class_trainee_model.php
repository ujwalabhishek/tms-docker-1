<?php

/*

 * This is the Model class for Class Trainee

 */

class Class_Trainee_Model extends CI_Model {

    private $user;

    public function __construct() {

        parent::__construct();

        $this->load->helper('common');

        $this->user = $this->session->userdata('userDetails');

        $this->load->model('class_model', 'class');
        $this->load->model('course_model', 'course');

        $this->tenant_id = $this->user->tenant_id;
    }

    /**

     * Get Class Trainee List

     * @param type $tenant_id

     * @param type $course_id

     * @param type $class_id

     */
    public function get_class_trainee_list($tenant_id, $course_id, $class_id) {

        $this->db->select('tu.tax_code, tup.first_name, cc.class_session_day, ce.class_id, ce.course_id, ce.user_id');

        $this->db->from('class_enrol ce');

        $this->db->join('tms_users tu', 'ce.user_id = tu.user_id');

        $this->db->join('tms_users_pers tup', 'ce.user_id = tup.user_id');

        $this->db->join('course_class cc', 'cc.class_id = ce.class_id');

        if (!empty($tax_code)) {

            $this->db->where('tu.tax_code', $tax_code);
        }

        if (!empty($trainee_name)) {

            $this->db->where('tup.first_name', $trainee_name);
        }
    }

    ///by shubhranshu for client requirement for declaration data to save
    public function save_declaration_data($tenant_id, $trainee_id, $class_id, $tax_code, $name, $type, $email, $mobile, $condition, $lesson_timing, $overseas) {
        $data_array = array('dec_tenant_id' => $tenant_id,
        'dec_tax_code' => $tax_code,
        'dec_trainee_name' => $name,
        'dec_trainee_email' => $email,
        'dec_trainee_id' => $trainee_id,
        'dec_class_id' => $class_id ?? '0',
        'dec_trainee_mobile' => $mobile,
        'dec_condition' => $condition,
        'dec_lesson_timing' => $lesson_timing,
        'dec_overseas' => $overseas,
        'dec_type' => $type,
        'dec_enrol_by' => $this->session->userdata('userDetails')->user_id ?? '0',
        'dec_trigger_datetime' => date('Y-m-d H:i:s')
        );



        $status = $this->db->insert('tms_declaration_data', $data_array);
        return $status;
    }

    /**
      This method gets the start date of class by class id
     * */
    public function schedule_chck($class_id) {
        $this->db->select('class_id');
        $this->db->from('class_schld');
        $this->db->where('class_id', $class_id);
        $qry = $this->db->get();
        if ($qry->num_rows() > 0) {
            $res = TRUE;
            return $res;
        } else {
            $res = $this->get_class_stdate($class_id);
            return $res;
        }
    }

    /**
     * 
     * @param type $class_id $course_id
     * @return boolean
     */
    public function lock_class_attendance($tenant_id, $course_id, $class_id) {
        $cur_date = date('Y-m-d H:i:s');
        $data = array(
            lock_status => 1,
            lock_datetime => $cur_date
        );
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('lock_status', 0);
        $this->db->update('course_class', $data);
        if ($this->db->affected_rows() > 0) {
            /* skm start : trainee data will capture into the archive table */
            $staff_id = $this->session->userdata('userDetails')->user_id;
            $result = $this->get_all_enrolled_trainee($tenant_id, $course_id, $class_id);
            foreach ($result as $row) {
                $data_array[] = array('tenant_id' => $tenant_id,
                    'trainee_id' => $row->user_id,
                    'taxcode_type' => $row->tax_code_type,
                    'taxcode' => $row->tax_code,
                    'first_name' => $row->first_name,
                    'course_id' => $course_id,
                    'class_id' => $class_id,
                    'enrolment_mode' => $row->enrolment_mode,
                    'company_id' => $row->company_id,
                    'class_fee' => $row->class_fees,
                    'pymnt_due_id' => $row->pymnt_due_id,
                    'subsidy_type_id' => $row->subsidy_type_id,
                    'subsidy_amount' => $row->subsidy_amount,
                    'total_amount_due' => $row->total_amount_due,
                    'invoice_id' => $row->invoice_id,
                    'attendance_locked_by' => $staff_id
                );
            }
            $this->db->insert_batch('class_attendance_archive', $data_array);
            /* skm end */

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* skm start: get all the details of trainee */

    public function get_all_enrolled_trainee($tenant_id, $course_id, $class_id) {
        $this->db->select('ce.user_id,ce.enrolment_mode,ce.company_id,epd.pymnt_due_id,epd.class_fees,'
                . 'epd.total_amount_due,epd.subsidy_type_id,subsidy_amount,epd.subsidy_recd_date,ei.invoice_id,tu.tax_code_type,'
                . 'tu.tax_code,tup.first_name');
        $this->db->from('class_enrol ce');
        $this->db->join('enrol_pymnt_due epd', 'epd.user_id = ce.user_id and epd.pymnt_due_id = ce.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id = epd.pymnt_due_id');
        $this->db->join('tms_users tu', 'tu.user_id = ce.user_id and tu.tenant_id = ce.tenant_id');
        $this->db->join('tms_users_pers tup', 'tup.user_id = tu.user_id and tup.tenant_id = tu.tenant_id');
        $this->db->where('ce.tenant_id', $tenant_id);
        $this->db->where('ce.course_id', $course_id);
        $this->db->where('ce.class_id', $class_id);
        $query = $this->db->get();
        return $query->result();
    }

    /* skm end */

    /**
     * 
     * @param type $class_id $course_id
     * @return boolean
     */
    public function unlock_class_attendance($tenant_id, $course_id, $class_id) {
        $cur_date = date('Y-m-d H:i:s');
        $data = array(
            lock_status => 0,
            unlock_datetime => $cur_date
        );
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('lock_status', 1);
        $this->db->update('course_class', $data);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_attendance_lock_status($tenant, $course_id, $class_id) {
        $this->db->select('*');
        $this->db->from('course_class');
        $this->db->where('tenant_id', $tenant);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $qry = $this->db->get();
        // echo $this->db->last_query();
        return $qry->row();
    }

    public function get_class_stdate($class_id) {
        $this->db->select('class_id,class_start_datetime,class_end_datetime');
        $this->db->from('course_class');
        $this->db->where('class_id', $class_id);
        $sql = $this->db->get();
        $res = $sql->result_array();
        //print_r($res);
        $x = explode(" ", $res[0]['class_start_datetime']);
        $x1 = explode(" ", $res[0]['class_end_datetime']);


        $class_st_date = strtotime($x[0]);
        $class_ed_date = strtotime($x1[0]);


        if ($class_st_date == $class_ed_date) {

            return TRUE;
        } else {
            $today_date = strtotime(CONST_DATE);
            if ($today_date >= $class_st_date) {
                //echo $xxx1 = 1;
                return TRUE;
            } else {
                //echo $xxx1 = 0;
                return FALSE;
            }
        }
    }

    /**

     * Returns Trainee count 

     * @param type $tenantId

     * @param type $courseId

     * @param type $classId

     * @return type

     */
    public function record_count($tenantId, $courseId, $classId) {

        $this->db->select('tu.tax_code, tup.first_name, ca.session_01, ca.session_02, ca.class_attdn_date, cc.class_session_day, ce.class_id, ce.course_id, ce.user_id');

        $this->db->from('class_enrol ce');

        $this->db->join('tms_users tu', 'ce.user_id = tu.user_id');

        $this->db->join('tms_users_pers tup', 'ce.user_id = tup.user_id');

        $this->db->join('course_class cc', 'cc.class_id = ce.class_id');

        $this->db->join('class_attendance ca', 'ca.user_id = ce.user_id', 'left');

        $array = array('ce.tenant_id' => $tenantId, 'ce.course_id' => $courseId, 'ce.class_id' => $classId);

        $this->db->where($array);

        if (!empty($tax_code)) {

            $this->db->where('tu.tax_code', $tax_code);
        }

        if (!empty($trainee_name)) {

            $this->db->where('tup.first_name', $trainee_name);
        }

        $this->db->where("(ca.class_attdn_date >= '$dateFrom' or ca.class_attdn_date is null)");

        $result = $this->db->get()->result();

        return $result[0]->totalrows;
    }

    /**

     * This Method used in Mark attandance report for present absend list.

     * @param type $tenant_id

     * @param type $course_id

     * @param type $class_id

     * @param type $subsidy

     * @param type $from_date

     * @param type $to_date

     * @param type $sort_by

     * @param type $sort_order

     * @param type $attendance_status

     * @param type $user_present

     * @return type

     */
    public function present_absent_attendance_list($tenant_id, $course_id, $class_id, $subsidy, $from_date, $to_date, $sort_by, $sort_order, $attendance_status, $user_present) {

        $date_from = $from_date->format('Y-m-d');

        $date_to = $to_date->format('Y-m-d');

        $this->db->select("tu.tax_code, tu.country_of_residence, tup.first_name as name, ca.session_01, ca.session_02, ca.class_attdn_date, cc.class_session_day, ce.class_id, ce.course_id, ce.user_id", FALSE);

        $this->db->from('class_enrol ce');

        $this->db->join('tms_users tu', 'ce.tenant_id = tu.tenant_id and ce.user_id = tu.user_id');

        $this->db->join('tms_users_pers tup', 'ce.tenant_id = tup.tenant_id and ce.user_id = tup.user_id');

        $this->db->join('course_class cc', 'cc.tenant_id = ce.tenant_id and cc.course_id = ce.course_id and cc.class_id = ce.class_id');
        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id = ce.user_id');
//        if (!empty($subsidy)) {
//
//            $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id = ce.user_id');
//        }

        $this->db->join('class_attendance ca', "ca.tenant_id = ce.tenant_id and ca.course_id = ce.course_id and ca.class_id = ce.class_id and ca.user_id = ce.user_id and ca.class_attdn_date >= '$date_from' and ca.class_attdn_date <= '$date_to'", 'left');

        $array = array('ce.tenant_id' => $tenant_id, 'ce.course_id' => $course_id, 'ce.class_id' => $class_id);

        $this->db->where($array);

        if (!empty($subsidy)) {

            if ($subsidy == 'ws')
                $this->db->where("epd.subsidy_amount >", 0);
            else
                $this->db->where("epd.subsidy_amount", 0);
        }

        $this->db->where_in("ce.enrol_status", array('ENRLBKD', 'ENRLACT'));

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->join('tenant_company_users tcu', 'ce.tenant_id = tu.tenant_id and tu.user_id = tcu.user_id');

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($attendance_status == 'ab') {
            //if (!empty($user_present)) {//commented by shubhranshu since its not needed
            // $this->db->where_not_in('ce.user_id', $user_present);
            $this->db->where('epd.att_status', 0);
            // }//commented by shubhranshu since its not needed
        } else if ($attendance_status == 'pr') {
            // if (!empty($user_present)) {//commented by shubhranshu since its not needed
            //  $this->db->where_in('ce.user_id', $user_present);
            $this->db->where('epd.att_status', 1);
            // } else {//commented by shubhranshu since its not needed
            // $this->db->get();//commented by shubhranshu since its not needed
            //return;//commented by shubhranshu since its not needed
            //} ///commented by shubhranshu since its not needed
        }

        $query = $this->db->get();

        $result = $query->result_array();

        $grouped_by_trainee = array();

        foreach ($result as $res) {

            $trainee = $res['user_id'];

            $date = $res['class_attdn_date'];

            if (!isset($grouped_by_trainee[$trainee])) {

                $grouped_by_trainee[$trainee] = array();

                $grouped_by_trainee[$trainee]['record'] = $res;
            }

            if (!empty($date)) {

                $grouped_by_trainee[$trainee][$date]['session_01'] = $res['session_01'];

                $grouped_by_trainee[$trainee][$date]['session_02'] = $res['session_02'];
            }
        }

        return $grouped_by_trainee;
    }

    /**

     * This method gets class trainee list with attendance grouped by user and date

     * @param $tenant_id

     * @param $course_id

     * @param $class_id

     * @param $from_date

     * @param $to_date

     * @param $sort_by

     * @param $sort_order

     */
    public function get_class_trainee_list_for_attendance($tenant_id, $course_id, $class_id, $subsidy, $from_date, $to_date, $sort_by, $sort_order, $attendance_status = '') {

//       $this->output->enable_profiler(TRUE);
        $date_from = $from_date->format('Y-m-d');

        $date_to = $to_date->format('Y-m-d');

        $this->db->select("tu.tax_code, tu.country_of_residence, tup.nationality ,tup.first_name as name,epd.att_status att, ca.session_01, ca.session_02, ca.class_attdn_date, cc.class_session_day, ce.class_id, ce.course_id, ce.user_id", FALSE);

        $this->db->select('ce.company_id, cm.company_name');

        $this->db->join('company_master cm', 'cm.company_id=ce.company_id', 'LEFT');

        $this->db->from('class_enrol ce');

        $this->db->join('tms_users tu', 'ce.tenant_id = tu.tenant_id and ce.user_id = tu.user_id');

        $this->db->join('tms_users_pers tup', 'ce.tenant_id = tup.tenant_id and ce.user_id = tup.user_id');

        $this->db->join('course_class cc', 'cc.tenant_id = ce.tenant_id and cc.course_id = ce.course_id and cc.class_id = ce.class_id');

        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id = ce.user_id');
//        if (!empty($subsidy)) {
//
//            $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id = ce.user_id');
//        }

        if ($attendance_status == "pr" || $attendance_status == 'ab') {

            $this->db->select('count(*) as user_count');

            $this->db->group_by('ce.user_id');

            $this->db->join('class_attendance ca', "ca.tenant_id = ce.tenant_id and ca.course_id = ce.course_id and ca.class_id = ce.class_id and ca.user_id = ce.user_id and ca.class_attdn_date >= '$date_from' and ca.class_attdn_date <= '$date_to'");

            $where = "(ca.session_01 = 1 OR ca.session_02 = 1)";

            $this->db->where($where);
        } else {

            $this->db->join('class_attendance ca', "ca.tenant_id = ce.tenant_id and ca.course_id = ce.course_id and ca.class_id = ce.class_id and ca.user_id = ce.user_id and ca.class_attdn_date >= '$date_from' and ca.class_attdn_date <= '$date_to'", 'left');
        }

        $array = array('ce.tenant_id' => $tenant_id, 'ce.course_id' => $course_id, 'ce.class_id' => $class_id);



        $this->db->where($array);

//        if (!empty($subsidy)) {
//
//            if ($subsidy == 'ws')
//                $this->db->where("epd.subsidy_amount >", 0);
//            else
//                $this->db->where("epd.subsidy_amount", 0);
//        }

        if (!empty($subsidy)) {
            if ($subsidy == 'ws') {
                $this->db->where("epd.subsidy_amount >", 0);
            }
            /* skm start for foreginer trainee */ else if ($subsidy == 'fr') {
//                $taxcode = 'SNG_2 || SNG_3';
//                $this->db->where('tu.tax_code',$taxcode);
                $this->db->where_in("tu.tax_code_type", array('SNG_2', 'SNG_3'));
            }/* skm end */ else if ($subsidy == 'wts') {
                $this->db->where_in("tu.tax_code_type", 'SNG_1');
                $this->db->where("epd.subsidy_amount", 0);
            }
//            else
//            {
//                $this->db->where("epd.subsidy_amount", 0);
//            }
        }

        $this->db->where_in("ce.enrol_status", array('ENRLBKD', 'ENRLACT'));

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->join('tenant_company_users tcu', 'ce.tenant_id = tu.tenant_id and tu.user_id = tcu.user_id');

            $this->db->where('ce.company_id', $this->user->company_id);
        }
        if ($sort_by == '') {
            $this->db->order_by('name', asc); // show all trainee in asc order
        }
        $query = $this->db->get();


        $result = $query->result_array();

        //echo $this->db->last_query();exit;

        if ($attendance_status == "ab" || $attendance_status == 'pr') {

            return $result;
        }

        $grouped_by_trainee = array();

        foreach ($result as $res) {

            $trainee = $res['user_id'];

            $date = $res['class_attdn_date'];

            if (!isset($grouped_by_trainee[$trainee])) {

                $grouped_by_trainee[$trainee] = array();

                $grouped_by_trainee[$trainee]['record'] = $res;
            }

            if (!empty($date)) {

                $grouped_by_trainee[$trainee][$date]['session_01'] = $res['session_01'];

                $grouped_by_trainee[$trainee][$date]['session_02'] = $res['session_02'];
            }
        }

        return $grouped_by_trainee;
    }
    
    public function get_class_trainee_list_for_assessment($tenant_id, $course_id, $class_id, $subsidy, $from_date, $to_date, $sort_by, $sort_order) {

//       $this->output->enable_profiler(TRUE);
        $date_from = $from_date->format('Y-m-d');

        $date_to = $to_date->format('Y-m-d');

        $this->db->select("tu.tax_code, tu.country_of_residence, tup.nationality ,tup.first_name as name,epd.att_status att, ca.assmnt_attdn, ca.class_assmnt_date, cc.class_session_day, ce.class_id, ce.course_id, ce.user_id", FALSE);
        $this->db->select('ce.company_id, cm.company_name');
        $this->db->join('company_master cm', 'cm.company_id=ce.company_id', 'LEFT');
        $this->db->from('class_enrol ce');
        $this->db->join('tms_users tu', 'ce.tenant_id = tu.tenant_id and ce.user_id = tu.user_id');
        $this->db->join('tms_users_pers tup', 'ce.tenant_id = tup.tenant_id and ce.user_id = tup.user_id');
        $this->db->join('course_class cc', 'cc.tenant_id = ce.tenant_id and cc.course_id = ce.course_id and cc.class_id = ce.class_id');
        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id = ce.user_id');      
        $this->db->join('class_assessment ca', "ca.tenant_id = ce.tenant_id and ca.course_id = ce.course_id and ca.class_id = ce.class_id and ca.user_id = ce.user_id and ca.class_assmnt_date >= '$date_from' and ca.class_assmnt_date <= '$date_to'", 'left');        

        $array = array('ce.tenant_id' => $tenant_id, 'ce.course_id' => $course_id, 'ce.class_id' => $class_id);

        $this->db->where($array);

        $this->db->where_in("ce.enrol_status", array('ENRLBKD', 'ENRLACT'));

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->join('tenant_company_users tcu', 'ce.tenant_id = tu.tenant_id and tu.user_id = tcu.user_id');

            $this->db->where('ce.company_id', $this->user->company_id);
        }
        if ($sort_by == '') {
            $this->db->order_by('name', asc); // show all trainee in asc order
        }
        $query = $this->db->get();

        $result = $query->result_array();

        //echo $this->db->last_query();exit;
        $grouped_by_trainee = array();

        foreach ($result as $res) {

            $trainee = $res['user_id'];

            $date = $res['class_assmnt_date'];

            if (!isset($grouped_by_trainee[$trainee])) {
                $grouped_by_trainee[$trainee] = array();
                $grouped_by_trainee[$trainee]['record'] = $res;
            }
            if (!empty($date)) {
                $grouped_by_trainee[$trainee][$date]['assmnt_attdn'] = $res['assmnt_attdn'];
            }
        }

        return $grouped_by_trainee;
    }
    
    /**

     * Update attendance for some class

     * @param $tenant_id

     * @param $course_id

     * @param $class_id

     * @param $data_table

     */
    public function update_for_mark_attendance($tenant_id, $course_id, $class_id, $data_table, $trainees) {
        $is_updated = false;
        $is_inserted = false;
        $logged_in_user_id = $this->user->user_id;
        //$this->db->trans_start();/// added by shubhranshu to check all the query and return true
        $query = $this->db->query("select * from class_attendance where course_id='$course_id' and class_id='$class_id'");

        if ($query->num_rows() > 0) {
            $marked_trainee = array();
            foreach ($data_table as $trainee_id => $data_row) {
                $marked_trainee[] = $trainee_id;
            }
        }

        if (count($data_table) > 0) {

            $insert_array = array();
            foreach ($data_table as $trainee_id => $data_row) {

                foreach ($data_row as $date => $data) {

                    if (isset($data['session_01']) || isset($data['session_02'])) {

                        $exists = $this->is_attandance_exists($class_id, $trainee_id, $date);
                        $update_data = array();
                        if (isset($data['session_01'])) {

                            $update_data['session_01'] = $data['session_01'];
                        }
                        if (isset($data['session_02'])) {
                            $update_data['session_02'] = $data['session_02'];
                        }
                        $this->db->where('class_id', $class_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('tenant_id', $tenant_id);
                        $this->db->where('user_id', $trainee_id);
                        $this->db->where('class_attdn_date', $date);
                        if ($exists) {
                            $this->db->update('class_attendance', $update_data);
                            $is_updated = true;
                        } else {
                            $insert_array[] = array(
                                'tenant_id' => $tenant_id,
                                'course_id' => $course_id,
                                'class_id' => $class_id,
                                'user_id' => $trainee_id,
                                'class_attdn_date' => $date,
                                'session_01' => $data['session_01'],
                                'session_02' => $data['session_02']
                            );
                        }
                    }
                }
            }
            if (count($insert_array) > 0) {

                $is_inserted = $this->db->insert_batch('class_attendance', $insert_array);
            }
            $query = $this->db->query("select * from class_attendance where course_id='$course_id' and class_id='$class_id'");
            if ($query->num_rows() > 0) {
                $query = $this->db->query("select * from class_schld where course_id='$course_id' and class_id='$class_id' and tenant_id='$tenant_id'");
                $query->num_rows();
                if ($query->num_rows() > 0) {

                    $query = $this->db->query("select att.user_id as user_id,
                            SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) 
                           / (select count(cs.class_id) from class_schld cs where cs.course_id='$course_id' and cs.class_id='$class_id' and cs.tenant_id='$tenant_id' and (cs.session_type_id='S1' or cs.session_type_id='S2')) as attendence
                    from class_attendance att
                    join course_class cc on cc.class_id='$class_id' and cc.course_id='$course_id' and cc.tenant_id='$tenant_id'
                    where att.class_id='$class_id' and att.course_id='$course_id'
                    group by att.user_id,att.class_id
                    having attendence >= 0.75");
                    $this->db->last_query();
                } else {
                    $sql = $this->db->query("select class_start_datetime,class_end_datetime from course_class where course_id='$course_id' and class_id='$class_id'");
                    foreach ($sql->result_array() as $row) {
                        $row['class_start_datetime'];
                        $row['class_start_datetime'];
                    }
                    $query = $this->db->query("select att.user_id as user_id,
                                    SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) 
                                    / (count(att.user_id)* cc.class_session_day) as attendence
                    from class_attendance att
                    join course_class cc on cc.class_id='$class_id' and cc.course_id='$course_id' and cc.tenant_id='$tenant_id'
                    where att.class_id='$class_id' and att.course_id='$course_id'
                    group by att.user_id,att.class_id
                    having attendence >= 0.75");
                    $this->db->last_query();
                }




                $attended_trainee = array();
                foreach ($query->result_array() as $row) {
                    $attended_trainee[] = $row['user_id'];
                }
                $absentee = array_diff($trainees, $attended_trainee);
                foreach ($absentee as $key => $trainee_id) {

                    //////below code was added by shubhranshu for xp for attrition option START-----
                    if (TENANT_ID == 'T02') {
                        $qr = $this->db->query("select att.user_id as user_id,SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) / (select count(cs.class_id) 
                            from class_schld cs where cs.course_id='$course_id' and cs.class_id='$class_id' and cs.tenant_id='T02' and (cs.session_type_id='S1' or cs.session_type_id='S2')) as attendence
                            from class_attendance att
                            join course_class cc on cc.class_id='$class_id' and cc.course_id='$course_id' and cc.tenant_id='T02'
                            where att.class_id='$class_id' and att.course_id='$course_id' and att.user_id='$trainee_id'
                            group by att.user_id,att.class_id");
                        //having attendence <= 0.50");
                        $att_percentage = $qr->result_array()[0][attendence];
                    }
                    //////below code was added by shubhranshu for xp for attrition option end-----


                    $this->db->select('ce.pymnt_due_id,ce.company_id,ei.invoice_id');
                    $this->db->from('class_enrol ce');
                    $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');
                    $this->db->where('ce.tenant_id', $tenant_id);
                    $this->db->where('ce.course_id', $course_id);
                    $this->db->where('ce.class_id', $class_id);
                    $this->db->where('ce.user_id', $trainee_id);
                    $this->db->where('ce.enrolment_mode', 'COMPSPON');
                    $query = $this->db->get();
                    $query->num_rows();

                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $rows) {

                            $payment_due_id = $rows->pymnt_due_id;
                            $invoice_id = $rows->invoice_id;
                            $company_id = $rows->company_id;
                        }
                        $query = $this->db->query("select * from enrol_pymnt_due where pymnt_due_id='$payment_due_id' and user_id='$trainee_id' and att_status='1'");
                        //echo "sss".$query->num_rows();exit;
                        if ($query->num_rows() > 0) {
                            //sk1
                            $status = $this->remove_absentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
                                    , $invoice_id, $payment_due_id, $trainee_id, $att_percentage);
                        }
                    } else {
                        $query = $this->db->select('ce.pymnt_due_id,ei.invoice_id')
                                        ->from('class_enrol ce')
                                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')->where('ce.tenant_id', $tenant_id)->where('ce.course_id', $course_id)
                                        ->where('ce.class_id', $class_id)->where('ce.user_id', $trainee_id)
                                        ->get()->row(0);
                        $this->db->last_query();
                        $payment_due_id = $query->pymnt_due_id;
                        $invoice_id = $query->invoice_id;
                        $query = $this->db->query("select * from enrol_pymnt_due where pymnt_due_id='$payment_due_id' and user_id='$trainee_id' and att_status='1'");
                        if ($query->num_rows() > 0) {

                            $status = $this->remove_ind_absentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $invoice_id, $payment_due_id, $trainee_id, $att_percentage);
                        }
                    }
                }

                $marked_attn = array_intersect($attended_trainee, $marked_trainee);

                foreach ($marked_attn as $key => $trainee_id) {
                    $this->db->select('ce.pymnt_due_id,ce.company_id,ei.invoice_id');
                    $this->db->from('class_enrol ce');
                    $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');
                    $this->db->where('ce.tenant_id', $tenant_id);
                    $this->db->where('ce.course_id', $course_id);
                    $this->db->where('ce.class_id', $class_id);
                    $this->db->where('ce.user_id', $trainee_id);
                    $this->db->where('ce.enrolment_mode', 'COMPSPON');
                    $query = $this->db->get();
                    $query->num_rows();

                    if ($query->num_rows() > 0) {
                        foreach ($query->result() as $rows) {
                            $payment_due_id = $rows->pymnt_due_id;
                            $invoice_id = $rows->invoice_id;
                            $company_id = $rows->company_id;
                        }
                        $query = $this->db->query("select * from enrol_pymnt_due where pymnt_due_id='$payment_due_id' and user_id='$trainee_id' and att_status='0'");

                        if ($query->num_rows() > 0) {
                            //sk2 
                            $status = $this->add_presentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
                                    , $invoice_id, $payment_due_id, $trainee_id);
                        }
                    } else {
                        $query = $this->db->select('ce.pymnt_due_id,ei.invoice_id')
                                        ->from('class_enrol ce')
                                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')->where('ce.tenant_id', $tenant_id)->where('ce.course_id', $course_id)
                                        ->where('ce.class_id', $class_id)->where('ce.user_id', $trainee_id)
                                        ->get()->row(0);

                        $payment_due_id = $query->pymnt_due_id;
                        $invoice_id = $query->invoice_id;
                        $query = $this->db->query("select * from enrol_pymnt_due where pymnt_due_id='$payment_due_id' and user_id='$trainee_id' and att_status='0'");
                        if ($query->num_rows() > 0) {

                            $status = $this->add_ind_absentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $invoice_id, $payment_due_id, $trainee_id);
                        }
                    }
                }
            }

            foreach ($data_table as $trainee_id => $data_row) {

                $query11 = $this->db->select('ce.pymnt_due_id,ei.invoice_id')
                                ->from('class_enrol ce')
                                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')->where('ce.tenant_id', $tenant_id)->where('ce.course_id', $course_id)
                                ->where('ce.class_id', $class_id)->where('ce.user_id', $trainee_id)
                                ->get()->row(0);
                $payment_due_id = $query11->pymnt_due_id;
                //$att_status = $query11->att_status;
                $this->db->set('ce.training_score', 'C');
                $this->db->where('ce.pymnt_due_id', $payment_due_id);
                $this->db->where('epd.pymnt_due_id', $payment_due_id);
                $this->db->where('epd.att_status', 1);
                $this->db->where('ce.user_id', $trainee_id);
                $this->db->where('epd.user_id', $trainee_id);
                $this->db->update('class_enrol ce join enrol_pymnt_due epd ON ce.pymnt_due_id=epd.pymnt_due_id');
                //$this->db->last_query();
                //////below code was added by shubhranshu for xp for attrition option START-----
                if (TENANT_ID == 'T02') {
                    $qr = $this->db->query("select att.user_id as user_id,SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) / (select count(cs.class_id) 
                        from class_schld cs where cs.course_id='$course_id' and cs.class_id='$class_id' and cs.tenant_id='T02' and (cs.session_type_id='S1' or cs.session_type_id='S2')) as attendence
                        from class_attendance att
                        join course_class cc on cc.class_id='$class_id' and cc.course_id='$course_id' and cc.tenant_id='T02'
                        where att.class_id='$class_id' and att.course_id='$course_id' and att.user_id='$trainee_id'
                        group by att.user_id,att.class_id");
                    //having attendence <= 0.50");
                    $att_percentage = $qr->result_array()[0][attendence];
                }


                if ($att_percentage != null && $att_percentage >= 0 && $att_percentage <= 0.50 && TENANT_ID == 'T02') {
                    $this->db->set('ce.training_score', 'ATR');
                    $this->db->where('ce.pymnt_due_id', $payment_due_id);
                    $this->db->where('epd.pymnt_due_id', $payment_due_id);
                    $this->db->where('epd.att_status', 0);
                    $this->db->where('ce.user_id', $trainee_id);
                    $this->db->where('epd.user_id', $trainee_id);
                    $this->db->update('class_enrol ce join enrol_pymnt_due epd ON ce.pymnt_due_id=epd.pymnt_due_id');


                    $data = array('feedback_answer' => 'ATR');
                    $this->db->where('tenant_id', $tenant_id);
                    $this->db->where('course_id', $course_id);
                    $this->db->where('class_id', $class_id);
                    $this->db->where('feedback_question_id', 'COMYTCOM');
                    $this->db->where('user_id', $trainee_id);
                    $this->db->update('trainer_feedback', $data);
                } else if ($att_percentage != null && $att_percentage > 0.50 && $att_percentage < 0.75 && TENANT_ID == 'T02') {
                    $this->db->set('ce.training_score', 'ABS');
                    $this->db->where('ce.pymnt_due_id', $payment_due_id);
                    $this->db->where('epd.pymnt_due_id', $payment_due_id);
                    $this->db->where('epd.att_status', 0);
                    $this->db->where('ce.user_id', $trainee_id);
                    $this->db->where('epd.user_id', $trainee_id);
                    $this->db->update('class_enrol ce join enrol_pymnt_due epd ON ce.pymnt_due_id=epd.pymnt_due_id');


                    $data = array('feedback_answer' => 'ABS');
                    $this->db->where('tenant_id', $tenant_id);
                    $this->db->where('course_id', $course_id);
                    $this->db->where('class_id', $class_id);
                    $this->db->where('feedback_question_id', 'COMYTCOM');
                    $this->db->where('user_id', $trainee_id);
                    $this->db->update('trainer_feedback', $data);
                } else {//////below code was added by shubhranshu for xp for attrition option end-----
                    $this->db->set('ce.training_score', 'ABS');
                    $this->db->where('ce.pymnt_due_id', $payment_due_id);
                    $this->db->where('epd.pymnt_due_id', $payment_due_id);
                    $this->db->where('epd.att_status', 0);
                    $this->db->where('ce.user_id', $trainee_id);
                    $this->db->where('epd.user_id', $trainee_id);
                    $this->db->update('class_enrol ce join enrol_pymnt_due epd ON ce.pymnt_due_id=epd.pymnt_due_id');

                    //////below code was added by shubhranshu for xp for attrition option START-----
                    if (($att_percentage >= 0 && $att_percentage <= 0.50 && TENANT_ID == 'T02') || ($att_percentage == null && TENANT_ID == 'T02')) {                    
                        $data = array('feedback_answer' => '');
                        $this->db->where('tenant_id', $tenant_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('class_id', $class_id);
                        $this->db->where('feedback_question_id', 'COMYTCOM');
                        $this->db->where('user_id', $trainee_id);
                        $this->db->update('trainer_feedback', $data);
                        //////below code was added by shubhranshu for xp for attrition option end-----
                    }
                }

                //$this->db->last_query();
            }
            return $query->result(); // commented by shubhranshu since waste due to some condition does not show msg
        }
        return $is_inserted || $is_updated; // commented by shubhranshu since waste
//        $this->db->trans_complete();/// added by shubhranshu to check all the query and return true strat code/////
//        if($this->db->trans_status() === TRUE){
//            return TRUE;
//        }else{
//            return FALSE;
//        }///////added by shubhranshu end of code////////////////////////////
    }
    
    public function update_for_mark_assessment($tenant_id, $course_id, $class_id, $data_table, $trainees) {
        $is_updated = false;
        $is_inserted = false;
        $query = $this->db->query("select * from class_assessment where course_id='$course_id' and class_id='$class_id'");

        if ($query->num_rows() > 0) {
            $marked_trainee = array();
            foreach ($data_table as $trainee_id => $data_row) {
                $marked_trainee[] = $trainee_id;
            }
        }

        if (count($data_table) > 0) {
            $insert_array = array();
            foreach ($data_table as $trainee_id => $data_row) {

                foreach ($data_row as $date => $data) {

                    if (isset($data['assmnt_attdn'])) {

                        $exists = $this->is_assessment_exists($class_id, $trainee_id, $date);
                        $update_data = array();
                        if (isset($data['assmnt_attdn'])) {

                            $update_data['assmnt_attdn'] = $data['assmnt_attdn'];
                        }                        
                        $this->db->where('class_id', $class_id);
                        $this->db->where('course_id', $course_id);
                        $this->db->where('tenant_id', $tenant_id);
                        $this->db->where('user_id', $trainee_id);
                        $this->db->where('class_assmnt_date', $date);
                        if ($exists) {
                            $this->db->update('class_assessment', $update_data);
                            $is_updated = true;
                        } else {
                            $insert_array[] = array(
                                'tenant_id' => $tenant_id,
                                'course_id' => $course_id,
                                'class_id' => $class_id,
                                'user_id' => $trainee_id,
                                'class_assmnt_date' => $date,
                                'assmnt_attdn' => $data['assmnt_attdn']
                            );
                        }
                    }
                }
            }
            if (count($insert_array) > 0) {

                $is_inserted = $this->db->insert_batch('class_assessment', $insert_array);
            }                                    
        }
        return $is_inserted || $is_updated; 
    }
    

    /*
     * add presentee to invoice ..
     * changed by pritam
     */
    /* Original commnented on 15/11/2016
      public function add_presentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id,$invoice_id,$payment_due_id,$trainee_id)
      {

      $status = TRUE;

      $curr_invoice_details = $this->get_invoice_details($payment_due_id);

      $total_gst_due = 0;

      $total_unit_fees_due = 0;

      $total_net_fees_due = 0;

      $total_discount_due = 0;

      $total_subsidy_amount_due = 0;

      $cur_date = date('Y-m-d');

      $crse_cls_detail = $this->get_class_detail($class_id, $course_id, $tenant_id);

      $str_class_status = $this->get_class_status($crse_cls_detail->class_status, $crse_cls_detail->class_start_datetime, $crse_cls_detail->class_end_datetime);

      $data = $this->get_current_invoice_data($payment_due_id);
      $due_to='Mark as Present';

      $this->db->trans_start();

      $status = $this->enrol_invoice_view($payment_due_id,$data,$logged_in_user_id,$due_to);

      $data = array('att_status'  =>1);
      $this->db->where('pymnt_due_id',$payment_due_id);
      $this->db->where('user_id',$trainee_id);
      $this->db->update('enrol_pymnt_due', $data);

      $data = array('training_score'  =>'C' );
      $this->db->where('pymnt_due_id',$payment_due_id);
      $this->db->where('user_id',$trainee_id);
      $this->db->update('class_enrol', $data);

      $query=$this->db->select('*')->from('trainer_feedback')->where('tenant_id',$tenant_id)->where('course_id',$course_id)
      ->where('class_id',$class_id)->where('user_id',$trainee_id)->get();
      if($query->num_rows()>0)
      {
      $data = array('feedback_answer'  =>'C' );
      $this->db->where('tenant_id',$tenant_id);
      $this->db->where('course_id',$course_id);
      $this->db->where('class_id',$class_id);
      $this->db->where('feedback_question_id','COMYTCOM');
      $this->db->where('user_id',$trainee_id);
      $this->db->update('trainer_feedback', $data);
      }

      $query=$this->db->query("select sum(subsidy_amount) as subsidy_amt from enrol_pymnt_due where att_status=1 and user_id= '$trainee_id' and"
      . " pymnt_due_id=$payment_due_id");
      $row=$query->row_array();
      $this->db->last_query();
      if($row['subsidy_amt']>0){$total_subsidy_amount_due+=$row['subsidy_amt'];}

      $fees_array = $this->fees_payable($trainee_id,$tenant_id,$course_id,$class_id,$total_subsidy_amount_due,$company_id,$logged_in_user_id);
      $discount_rate = $fees_array["discount_rate"];
      $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
      $total_gst_due += $fees_array["gst_amount"];
      $total_unit_fees_due += $fees_array["unit_fees"];
      $total_net_fees_due += $fees_array["net_fees_due"];

      $status = $this->update_invoice_audit_trail($payment_due_id);
      if ($status) {

      $status = $this->remove_invoice($payment_due_id);

      if ($status) {

      list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id,
      ($curr_invoice_details->total_inv_amount + $total_net_fees_due),
      ($curr_invoice_details->total_unit_fees + $total_unit_fees_due),
      ($curr_invoice_details->total_inv_discnt + $total_discount_due),
      ($curr_invoice_details->total_inv_subsdy + $total_subsidy_amount_due),
      ($curr_invoice_details->total_gst + $total_gst_due),
      $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

      if ($status) {
      //paid status
      $query=mysqli_query("select * from enrol_paymnt_recd where invoice_id='$invoice_id1'");
      if($query)
      {
      $invoice_id1=$invoice_id;
      $invoice_id = $new_invoice_id;
      $sql=mysqli_query("update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'");
      $sql1=mysqli_query("update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'");
      }
      //end paid status
      $invoice_id = $new_invoice_id;
      $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
      $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id);
      }
      }
      }

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE) {

      $status = FALSE;
      }



      return $status;
      }     /*
     * add presentee to invoice ..
     * changed by pritam
     */

    /*
     * add presentee to invoice ..
     * changed by pritam
     */

    public function add_presentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $trainee_id) {

        $status = TRUE;
        $curr_invoice_details = $this->get_invoice_details($payment_due_id);
        $total_gst_due = 0;
        $total_unit_fees_due = 0;
        $total_net_fees_due = 0;
        $total_discount_due = 0;
        $total_subsidy_amount_due = 0;
        $cur_date = date('Y-m-d');
        $crse_cls_detail = $this->get_class_detail($class_id, $course_id, $tenant_id);
        $str_class_status = $this->get_class_status($crse_cls_detail->class_status, $crse_cls_detail->class_start_datetime, $crse_cls_detail->class_end_datetime);
        $data = $this->get_current_invoice_data($payment_due_id);
        $due_to = 'Mark as Present';
        $this->db->trans_start();
        $status = $this->enrol_invoice_view($payment_due_id, $data, $logged_in_user_id, $due_to);

        $query1 = $this->db->query("select sum(subsidy_amount) as subsidy_amt,discount_rate from enrol_pymnt_due where user_id= '$trainee_id' and"
                . " pymnt_due_id=$payment_due_id");

        $row1 = $query1->row_array();

        if (empty($row1['discount_rate'])) {
            $query_update = $this->db->query("select discount_rate,class_fees,gst_amount from enrol_pymnt_due where att_status=1 and discount_rate!= 0 and"
                    . " pymnt_due_id=$payment_due_id");

            $row_update = $query_update->row_array();

            if ($query_update->num_rows() > 0) {

                $class_fees = $row_update["class_fees"];
                $discount_rate_update = $row_update["discount_rate"];
                $gst_amount_update = $row_update["gst_amount"];
                $discount_amount = round(((round($row_update["discount_rate"]) * $class_fees) / 100), 4);
                $total_amount_due = $class_fees - $discount_amount + $gst_amount_update;
                $data = array('att_status' => 1,
                    'discount_rate' => $discount_rate_update,
                    'gst_amount' => round($gst_amount_update, 2), //sk1
                    'total_amount_due' => round($total_amount_due, 2)); //sk2
            } else {
                $data = array('att_status' => 1);
            }
        } else if (!empty($row1['discount_rate'])) {
            $query_update = $this->db->query("select discount_rate,class_fees,gst_amount from enrol_pymnt_due where att_status=1 and discount_rate!= 0 and"
                    . " pymnt_due_id=$payment_due_id");

            $query_absent = $this->db->query("select discount_rate,class_fees,gst_amount from enrol_pymnt_due where user_id= '$trainee_id' and"
                    . " pymnt_due_id=$payment_due_id");

            $row_update_pr = $query_update->row_array();

            $row_update_abs = $query_absent->row_array();
            if (!empty($row_update_pr['discount_rate']) && !empty($row_update_abs['discount_rate'])) {
                if ($row_update_pr['discount_rate'] != $row_update_abs['discount_rate']) {
                    $discount_rate_update = $row_update_pr["discount_rate"];
                    $gst_amount_update = $row_update_pr["gst_amount"];
                    $class_fees = $row_update_pr["class_fees"];
                    $discount_amount = round(((round($row_update_pr["discount_rate"]) * $class_fees) / 100), 4);
                    $total_amount_due = $class_fees - $discount_amount + $gst_amount_update;
                    $data = array('att_status' => 1,
                        'discount_rate' => $discount_rate_update,
                        'gst_amount' => round($gst_amount_update, 2), //sk3
                        'total_amount_due' => round($total_amount_due, 2)); //sk4
                } else {
                    $data = array('att_status' => 1);
                }
            } else {
                $data = array('att_status' => 1);
            }
        } else {
            $data = array('att_status' => 1);
        }

        $this->db->where('pymnt_due_id', $payment_due_id);
        $this->db->where('user_id', $trainee_id);
        $this->db->update('enrol_pymnt_due', $data);

        $data = array('training_score' => 'C');
        $this->db->where('pymnt_due_id', $payment_due_id);
        $this->db->where('user_id', $trainee_id);
        $this->db->update('class_enrol', $data);

        $query = $this->db->select('*')->from('trainer_feedback')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                        ->where('class_id', $class_id)->where('user_id', $trainee_id)->get();
        if ($query->num_rows() > 0) {
            $data = array('feedback_answer' => 'C');
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where('course_id', $course_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('feedback_question_id', 'COMYTCOM');
            $this->db->where('user_id', $trainee_id);
            $this->db->update('trainer_feedback', $data);
        }
        $query = $this->db->query("select sum(subsidy_amount) as subsidy_amt from enrol_pymnt_due where att_status=1 and user_id= '$trainee_id' and"
                . " pymnt_due_id=$payment_due_id");

        $row = $query->row_array();

        if ($row['subsidy_amt'] > 0) {
            $total_subsidy_amount_due+=$row['subsidy_amt'];
        }
        $fees_array = $this->fees_payable_add_abs($trainee_id, $payment_due_id, $tenant_id, $course_id, $class_id, $total_subsidy_amount_due, $company_id, $logged_in_user_id);
        $discount_rate = $fees_array["discount_rate"];
        $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
        $total_gst_due += $fees_array["gst_amount"];
        $total_unit_fees_due += $fees_array["unit_fees"];
        $total_net_fees_due += $fees_array["net_fees_due"];
//        echo "discount_rate=".$discount_rate."/ total_discount_due=".$total_discount_due."/ total_gst_due".$total_gst_due.
//                "/ total_gst_due="."/ total_unit_fees_due=".$total_unit_fees_due."/ total_net_fees_due=".$total_net_fees_due;
        $status = $this->update_invoice_audit_trail($payment_due_id);
        $previous_inv_id = $invoice_id;
        if ($status) {
            $status = $this->remove_invoice($payment_due_id);
            if ($status) {
                //sk5
                list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, (round($curr_invoice_details->total_inv_amount, 2) + round($total_net_fees_due, 2)), ($curr_invoice_details->total_unit_fees + $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt + $total_discount_due), ($curr_invoice_details->total_inv_subsdy + $total_subsidy_amount_due), (round($curr_invoice_details->total_gst, 2) + round($total_gst_due, 2)), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');
                if ($status) {
                    //paid status
                    //$query=mysqli_query("select * from enrol_paymnt_recd where invoice_id='$invoice_id'");
                    $this->db->select('*');
                    $this->db->from('enrol_paymnt_recd');
                    $this->db->where('invoice_id', $invoice_id);
                    $query = $this->db->get()->num_rows();

                    if ($query) {
                        $invoice_id1 = $invoice_id;
                        $invoice_id = $new_invoice_id;

                        $updata = array('invoice_id' => $new_invoice_id);
                        $this->db->where('invoice_id', $invoice_id1);
                        $this->db->update('enrol_paymnt_recd', $updata);

                        $this->db->where('invoice_id', $invoice_id1);
                        $this->db->update('enrol_pymnt_brkup_dt', $updata);

                        //$sql=mysqli_query("update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'");
                        //$sql1=mysqli_query("update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'");
                    }

                    $query = mysqli_query("select * from enrol_refund where invoice_id='$previous_inv_id'");
                    if ($query) {
                        $previous_inv_id = $previous_inv_id;
                        $invoice_id = $new_invoice_id;

                        $updata = array('invoice_id' => $new_invoice_id);
                        $this->db->where('invoice_id', $previous_inv_id);
                        $this->db->update('enrol_refund', $updata);

                        $this->db->where('invoice_id', $previous_inv_id);
                        $this->db->update('enrol_refund_brkup_dt', $updata);

                        //$sql=mysqli_query("update enrol_refund set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
                        //$sql1=mysqli_query("update enrol_refund_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
                    }


                    //end paid status
                    $invoice_id = $new_invoice_id;
                    $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                    $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id);
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $status = FALSE;
        }
        return $status;
    }

    public function check_previous_discount() {
        $this->db->select('*');
        $this->db->from('enrol_pymnt_due epd');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
        $this->db->where('epd.pymnt_due_id', $payment_due_id);
        $this->db->where('ce.course_id', $course_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.tenant_id', $tenant_id);
        $this->db->where('epd.att_status', 1);
        $this->db->limit(1);
        $sql = $this->db->get();
    }

    public function fees_payable_add_abs($trainee_id, $payment_due_id, $tenant_id, $course_id, $class_id, $subsidy, $company_id, $loggedin_user_id) {
        $courseDetails = $this->course->get_course_detailse($course_id);
        $classDetails = $this->class->get_class_details($tenant_id, $class_id);
        $unit_fees = $classDetails->class_fees;
        $gst_rule = $courseDetails->subsidy_after_before;
        $gst_rate = $this->get_gst_current();
        $gst_label = ($courseDetails->gst_on_off == 1) ? 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($courseDetails->subsidy_after_before), ', ') : 'GST OFF';
        if ($company_id == 0) {
            $result_array = $this->get_discnt($tenant_id, 0, $course_id, $class_id, $unit_fees, $trainee_id);
        } else {
            //$result_array = $this->get_discnt($tenant_id, $company_id, $course_id, $class_id, $unit_fees, 0);
            $this->db->select('*');
            $this->db->from('enrol_pymnt_due epd');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
            $this->db->where('epd.pymnt_due_id', $payment_due_id);
            $this->db->where('ce.course_id', $course_id);
            $this->db->where('ce.class_id', $class_id);
            $this->db->where('ce.tenant_id', $tenant_id);
            $this->db->where('epd.att_status', 1);
            $this->db->limit(1);
            $sql = $this->db->get();
            if ($sql->num_rows() > 0) {
                $result_array = $sql->row_array();

                $discount_type = $result_array['discount_type'];
                $discount_rate = $result_array['discount_rate'];
                $discount_amount = $unit_fees * ($discount_rate / 100);
            } else {
                $result_array = $this->get_discnt($tenant_id, $company_id, $course_id, $class_id, $unit_fees, 0);

                $discount_type = $result_array['discount_label'];
                $discount_rate = $result_array['discount_rate'];
                $discount_amount = $result_array['discount_amount'];
            }
        }
//            $discount_type =   $result_array['discount_label'];
//            $discount_rate =   $result_array['discount_rate'];
//            $discount_amount = $result_array['discount_amount'];
        if (empty($discount_rate)) {
            $query = $this->db->query("select discount_rate from enrol_pymnt_due where att_status=1 and user_id= $trainee_id and"
                    . " pymnt_due_id=$payment_due_id");
            $row = $query->row_array();
            $discount_rate = $row["discount_rate"];
            $discount_amount = $unit_fees * ($discount_rate / 100);
        }
        $fees_due = $unit_fees - $discount_amount;
        $gst_amount = $this->calculate_gst($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, 0, $gst_rate);
        $net_fees_due = $this->calculate_net_due($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, $subsidy, $gst_rate);
        $result_array = array(
            'unit_fees' => $unit_fees,
            'discount_type' => $discount_type,
            'discount_rate' => $discount_rate,
            'discount_amount' => $discount_amount,
            'gst_amount' => is_null($gst_amount) ? 0 : $gst_amount,
            'gst_rate' => $gst_rate,
            'gst_label' => $gst_label,
            'net_fees_due' => $net_fees_due,
            'gst_rule' => $gst_rule
        );

        return $result_array;
    }

    public function add_ind_absentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $invoice_id, $payment_due_id, $trainee_id) {

        $status = TRUE;

        $curr_invoice_details = $this->get_invoice_details($payment_due_id);

        $total_gst_due = 0;

        $total_unit_fees_due = 0;

        $total_net_fees_due = 0;

        $total_discount_due = 0;

        $total_subsidy_amount_due = 0;

        $cur_date = date('Y-m-d');

        $crse_cls_detail = $this->get_class_detail($class_id, $course_id, $tenant_id);

        $str_class_status = $this->get_class_status($crse_cls_detail->class_status, $crse_cls_detail->class_start_datetime, $crse_cls_detail->class_end_datetime);

        $this->db->trans_start();
        $fees_array = $this->fees_payable($trainee_id, $tenant_id, $course_id, $class_id, 0, $company_id, $logged_in_user_id);

        $data = array('training_score' => 'C');
        $this->db->where('pymnt_due_id', $payment_due_id);
        $this->db->where('user_id', $trainee_id);
        $this->db->update('class_enrol', $data);
        $query = $this->db->select('*')->from('trainer_feedback')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                        ->where('class_id', $class_id)->where('user_id', $trainee_id)->get();

        if ($query->num_rows() > 0) {
            $data = array('feedback_answer' => 'C');
            $this->db->where('tenant_id', $tenant_id);
            $this->db->where('course_id', $course_id);
            $this->db->where('class_id', $class_id);
            $this->db->where('feedback_question_id', 'COMYTCOM');
            $this->db->where('user_id', $trainee_id);
            $this->db->update('trainer_feedback', $data);
        }

        $data = array('att_status' => 1);
        $this->db->where('pymnt_due_id', $payment_due_id);
        $this->db->where('user_id', $trainee_id);
        $this->db->update('enrol_pymnt_due', $data);

        $discount_rate = $fees_array["discount_rate"];
        $total_discount_due = ($fees_array["unit_fees"] * $discount_rate) / 100;
        $total_gst_due = $fees_array["gst_amount"];
        $total_unit_fees_due = $fees_array["unit_fees"];
        $total_net_fees_due = $fees_array["net_fees_due"];

        /* $status = $this->update_invoice_audit_trail($payment_due_id);

          if ($status) {

          $status = $this->remove_invoice($payment_due_id);

          if ($status) {


          list($status, $new_invoice_id) = $this->add_new_invoice($payment_due_id,
          ($curr_invoice_details->total_inv_amount + $total_net_fees_due),
          ($curr_invoice_details->total_unit_fees + $total_unit_fees_due),
          ($curr_invoice_details->total_inv_discnt + $total_discount_due),
          ($curr_invoice_details->total_inv_subsdy + $total_subsidy_amount_due),
          ($curr_invoice_details->total_gst + $total_gst_due),
          $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

          if ($status)
          {

          $invoice_id = $new_invoice_id;

          $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
          }
          }
          }
         */
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }



        return $status;
    }

    /*
     * remove absentee from invoi   ce ..
     * changed by pritam
     */

    public function remove_absentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $trainee_id, $att_percentage) {

        $status = TRUE;


        $curr_invoice_details = $this->get_invoice_details($payment_due_id);

        $total_gst_due = 0;

        $total_unit_fees_due = 0;

        $total_net_fees_due = 0;

        $total_discount_due = 0;

        $total_subsidy_amount_due = 0;

        $this->db->trans_start();

        $user_id = $trainee_id;
        $data = $this->get_current_invoice_data($payment_due_id);

        $due_to = 'Mark as an absent';

        $status = $this->enrol_invoice_view($payment_due_id, $data, $logged_in_user_id, $due_to);


        $payments_result = $this->get_payment_due($payment_due_id, $user_id);

        $discount_rate = $payments_result->discount_rate;

        $total_discount_due += ($payments_result->class_fees * $discount_rate) / 100;

        $total_gst_due += $payments_result->gst_amount;

        $total_unit_fees_due += $payments_result->class_fees;

        $total_net_fees_due += $payments_result->total_amount_due;

        $total_subsidy_amount_due += $payments_result->subsidy_amount;
        //modified by shubhranshu due to attrition starts
        if ($att_percentage <= 0.50 && TENANT_ID == 'T02') {
            $data = array('training_score' => 'ATR');
            $this->db->where('pymnt_due_id', $payment_due_id);
            $this->db->where('user_id', $trainee_id);
            $this->db->update('class_enrol', $data);
        } else {//modified by shubhranshu due to attrition end
            $data = array('training_score' => 'ABS');
            $this->db->where('pymnt_due_id', $payment_due_id);
            $this->db->where('user_id', $trainee_id);
            $this->db->update('class_enrol', $data);
        }


        $query = $this->db->select('*')->from('trainer_feedback')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                        ->where('class_id', $class_id)->where('user_id', $trainee_id)->get();
        if ($query->num_rows() > 0) {//modified by shubhranshu due to attrition starts
            if ($att_percentage <= 0.50 && TENANT_ID == 'T02') {
                $data = array('feedback_answer' => 'ATR');
                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('course_id', $course_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('feedback_question_id', 'COMYTCOM');
                $this->db->where('user_id', $trainee_id);
                $this->db->update('trainer_feedback', $data);
            } else {//modified by shubhranshu due to attrition end
                $data = array('feedback_answer' => 'ABS');
                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('course_id', $course_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('feedback_question_id', 'COMYTCOM');
                $this->db->where('user_id', $trainee_id);
                $this->db->update('trainer_feedback', $data);
            }
        }

        $this->update_payment_due($payment_due_id, $user_id);

        // $this->remove_payment_due($payment_due_id, $user_id);


        $status = $this->update_invoice_audit_trail($curr_invoice_details->pymnt_due_id);
        $previous_inv_id = $invoice_id;
        if ($status) {

            $status = $this->remove_invoice($payment_due_id);

            if ($status) {
                //sk1
                list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, (round($curr_invoice_details->total_inv_amount, 2) - round($total_net_fees_due, 2)), ($curr_invoice_details->total_unit_fees - $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt - $total_discount_due), ($curr_invoice_details->total_inv_subsdy - $total_subsidy_amount_due), (round($curr_invoice_details->total_gst, 2) - round($total_gst_due, 2)), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

                if ($status) {
                    //paid status
                    //$qry="select * from enrol_paymnt_recd where invoice_id='$invoice_id'"; 
                    //$query = mysqli_query($qry);//modified by shubhranshu

                    $this->db->select('*');
                    $this->db->from('enrol_paymnt_recd');
                    $this->db->where('invoice_id', $invoice_id);
                    $query = $this->db->get()->num_rows();


                    if ($query) {
                        $invoice_id1 = $invoice_id;
                        $invoice_id = $new_invoice_id;

                        $updata = array('invoice_id' => $new_invoice_id);
                        $this->db->where('invoice_id', $invoice_id1);
                        $this->db->update('enrol_paymnt_recd', $updata);

                        $this->db->where('invoice_id', $invoice_id1);
                        $this->db->update('enrol_pymnt_brkup_dt', $updata);

                        //$sql=mysqli_query("update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'");
                        //$sql1=mysqli_query("update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'");
                    }
//                  
                    $this->db->select('*');
                    $this->db->from('enrol_refund');
                    $this->db->where('invoice_id', $previous_inv_id);
                    $query1 = $this->db->get();

                    if ($query1->num_rows() > 0) {

                        $previous_inv_id = $previous_inv_id;
                        $invoice_id = $new_invoice_id;

                        $updata = array('invoice_id' => $new_invoice_id);
                        $this->db->where('invoice_id', $previous_inv_id);
                        $this->db->update('enrol_refund', $updata);

                        $this->db->where('invoice_id', $previous_inv_id);
                        $this->db->update('enrol_refund_brkup_dt', $updata);

                        //$sql=mysqli_query("update enrol_refund set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
                        //$sql1=mysqli_query("update enrol_refund_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
                    }
//                     $previous_inv_id = $invoice_id;
//                    $query=mysqli_query("select * from enrol_paymnt_recd where invoice_id='$previous_inv_id'");
//                    if($query)
//                    {
//                      $previous_inv_id = $invoice_id;
//                        $invoice_id = $new_invoice_id;
//                        $sql=mysqli_query("update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
//                        $sql1=mysqli_query("update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
//                    }
                    //end paid status 
                    $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                    $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

/*
     * remove absentee from invoi   ce ..
     * changed by pritam
     */

    public function remove_ind_absentee($tenant_id, $logged_in_user_id, $course_id, $class_id, $invoice_id, $payment_due_id, $trainee_id, $att_percentage) {

        $status = TRUE;
        $curr_invoice_details = $this->get_invoice_details($payment_due_id);

        $total_gst_due = 0;

        $total_unit_fees_due = 0;

        $total_net_fees_due = 0;

        $total_discount_due = 0;

        $total_subsidy_amount_due = 0;

        $this->db->trans_start();

        $user_id = $trainee_id;

        //$status = $this->update_classenrol_audittrail($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);
        //$this->remove_enrollment($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);

        $payments_result = $this->get_payment_due($payment_due_id, $user_id);

        $discount_rate = $payments_result->discount_rate;

        $total_discount_due += ($payments_result->class_fees * $discount_rate) / 100;

        $total_gst_due += $payments_result->gst_amount;

        $total_unit_fees_due += $payments_result->class_fees;

        $total_net_fees_due += $payments_result->total_amount_due;

        $total_subsidy_amount_due += $payments_result->subsidy_amount;
        //modified by shubhranshu due to attrition starts
        if ($att_percentage <= 0.50 && TENANT_ID == 'T02') {

            $data = array('training_score' => 'ATR');
            $this->db->where('pymnt_due_id', $payment_due_id);
            $this->db->where('user_id', $trainee_id);
            $this->db->update('class_enrol', $data);
        } else {//modified by shubhranshu due to attrition end
            //echo $att_percentage."tt";exit;
            $data = array('training_score' => 'ABS');
            $this->db->where('pymnt_due_id', $payment_due_id);
            $this->db->where('user_id', $trainee_id);
            $this->db->update('class_enrol', $data);
        }

        $query = $this->db->select('*')->from('trainer_feedback')->where('tenant_id', $tenant_id)->where('course_id', $course_id)
                        ->where('class_id', $class_id)->where('user_id', $trainee_id)->get();
        if ($query->num_rows() > 0) {/////modified by shubhranshu due to attrition starts
            if ($att_percentage <= 0.50 && TENANT_ID == 'T02') {
                $data = array('feedback_answer' => 'ATR');
                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('course_id', $course_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('feedback_question_id', 'COMYTCOM');
                $this->db->where('user_id', $trainee_id);
                $this->db->update('trainer_feedback', $data);
            } else {//modified by shubhranshu due to attrition end
                $data = array('feedback_answer' => 'ABS');
                $this->db->where('tenant_id', $tenant_id);
                $this->db->where('course_id', $course_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('feedback_question_id', 'COMYTCOM');
                $this->db->where('user_id', $trainee_id);
                $this->db->update('trainer_feedback', $data);
            }
        }
        $this->update_payment_due($payment_due_id, $user_id);
        // $this->remove_payment_due($payment_due_id, $user_id);


        /*  $status = $this->update_invoice_audit_trail($curr_invoice_details->pymnt_due_id);

          if ($status) {

          $status = $this->remove_invoice($payment_due_id);

          if ($status) {

          list($status, $new_invoice_id) = $this->update_invoice($payment_due_id,($curr_invoice_details->total_inv_amount - $total_net_fees_due), ($curr_invoice_details->total_unit_fees - $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt - $total_discount_due), ($curr_invoice_details->total_inv_subsdy - $total_subsidy_amount_due), ($curr_invoice_details->total_gst - $total_gst_due), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

          if ($status) {

          $invoice_id = $new_invoice_id;

          $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
          }
          }
          }
         */
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /* update invoice */

    private function update_invoice($payment_due_id, $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $GSTRule, $GSTRate, $inv_type) {

        $insert_status = TRUE;

        $cur_date = date('Y-m-d H:i:s');

        $invoice_id = $this->generate_invoice_id();

        $enrol_invoice_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $cur_date,
            'inv_type' => $inv_type,
            'company_id' => "",
            'total_inv_amount' => round($total_inv_amount, 4),
            'total_unit_fees' => round($total_unit_fees, 4),
            'total_inv_discnt' => round($total_inv_discnt, 4),
            'total_inv_subsdy' => round($total_inv_subsidy, 4),
            'total_gst' => round($total_gst, 4),
            'gst_rate' => round($GSTRate, 4),
            'gst_rule' => $GSTRule,
            'invoice_generated_on' => $cur_date,
        );

        $this->db->trans_start();

        $this->db->insert('enrol_invoice', $enrol_invoice_data);

        $new_invoice_id = $invoice_id;

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $insert_status = FALSE;
        }

        return array($insert_status, $new_invoice_id);
    }

    /* update invoice for indivisul */

    private function add_new_invoice($payment_due_id, $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $GSTRule, $GSTRate, $inv_type) {

        $insert_status = TRUE;

        $cur_date = date('Y-m-d H:i:s');

        $invoice_id = $this->generate_invoice_id();

        $enrol_invoice_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $cur_date,
            'inv_type' => $inv_type,
            'company_id' => "",
            'total_inv_amount' => round($total_inv_amount, 4),
            'total_unit_fees' => round($total_unit_fees, 4),
            'total_inv_discnt' => round($total_inv_discnt, 4),
            'total_inv_subsdy' => round($total_inv_subsidy, 4),
            'total_gst' => round($total_gst, 4),
            'gst_rate' => round($GSTRate, 4),
            'gst_rule' => $GSTRule,
            'invoice_generated_on' => $cur_date,
        );

        $this->db->trans_start();

        $this->db->insert('enrol_invoice', $enrol_invoice_data);

        $new_invoice_id = $invoice_id;

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $insert_status = FALSE;
        }

        return array($insert_status, $new_invoice_id);
    }

    /*
     * *update payment due
     */

    private function update_payment_due($payment_due_id, $user_id) {
        $data = array(
            'att_status' => 0,
        );
        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->update("enrol_pymnt_due", $data);
    }

    /*
     * *update enroll
     */

    private function update_enrol($payment_due_id, $user_id, $flag) {
        if ($flag == 0) {
            $data = array(
                'training_score' => 'ABS',
            );
        } else {
            $data = array(
                'training_score' => 'C',);
        }
        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->update("enrol_pymnt_due", $data);
        $this->db->last_query();
    }

    /*
     * end remove absentee
     */

    public function is_attandance_exists($class_id, $user_id, $assmnt_date) {

        $cnt = $this->db->query("select count(*) as cnt from class_attendance where class_id = ? and user_id = ? and class_attdn_date = ?", array($class_id, $user_id, $assmnt_date))->row()->cnt;

        return $cnt > 0;
    }
    
    public function is_assessment_exists($class_id, $user_id, $assmnt_date) {

        $cnt = $this->db->query("select count(*) as cnt from class_assessment where class_id = ? and user_id = ? and class_assmnt_date = ?", array($class_id, $user_id, $assmnt_date))->row()->cnt;

        return $cnt > 0;
    }

    public function trainee_user_list_autocomplete($tax_code = NULL) {

        $matches = array();



        if (!empty($tax_code)) {



            $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code');

            $this->db->from('tms_users_pers pers');

            $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

            $this->db->where('usr.account_type', 'INTUSR');

            $this->db->where('usr.tenant_id', $this->session->userdata('userDetails')->tenant_id);

            $this->db->like('usr.tax_code', $tax_code, 'both');

            $results = $this->db->get()->result();



            foreach ($results as $result) {

                $matches[$result->tax_code] = $result->user_name . '(' . $result->tax_code . ')';
            }
        }

        return $matches;
    }

    public function trainee_getAutoCompleteTraineesList($trainee, $tenantID, $courseID = 0, $classID = 0) {

        $matches = array();



        $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code, ce.course_id, ce.class_id');

        $this->db->from('tms_users_pers pers');

        $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

        $this->db->join('class_enrol ce', 'usr.user_id=ce.user_id');

        $this->db->where('usr.account_type', 'TRAINE');

        $this->db->where("usr.account_status != 'INACTIV'");

        $this->db->where('usr.tenant_id', $tenantID);

        if ((int) $courseID > 0) {

            $this->db->where("ce.course_id", $courseID);
        }

        if ((int) $classID > 0) {

            $this->db->where("ce.class_id", $classID);
        }

        $this->db->like('pers.first_name', $trainee, 'both');

        $results = $this->db->get()->result();



        return $results;
    }

    public function trainee_getAutoCompleteTaxcodeList($taxcode, $tenantID, $courseID = 0, $classID = 0) {

        $taxcode = trim($taxcode);

        $matches = array();



        $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code, ce.course_id, ce.class_id');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.user_id=pers.user_id and usr.tenant_id=pers.tenant_id');

        $this->db->join('class_enrol ce', 'usr.user_id=ce.user_id and usr.tenant_id=ce.tenant_id');

        $this->db->where('usr.tenant_id', $tenantID);

        if ((int) $courseID > 0) {

            $this->db->where("ce.course_id", $courseID);
        }

        if ((int) $classID > 0) {

            $this->db->where("ce.class_id", $classID);
        }

        $this->db->like('usr.tax_code', $taxcode, 'both');

        $this->db->group_by('ce.user_id');

        $this->db->limit(200);

        $results = $this->db->get()->result();



        return $results;
    }

    /**

     * This method is used by Reports - Certificate search by Trainee Name

     * @param type $taxcode

     * @param type $tenantID

     * @param type $courseID

     * @param type $classID

     * @return type

     */
    public function trainee_getAutoCompleteTrainee_List($taxcode, $tenantID, $courseID = 0, $classID = 0) {

        $taxcode = trim($taxcode);

        $matches = array();

        $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code, ce.course_id, ce.class_id');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.user_id=pers.user_id and usr.tenant_id=pers.tenant_id');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('course c', 'c.tenant_id = pers.tenant_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');

            $trainer_where = 'AND c.course_id = ce.course_id and c.tenant_id=ce.tenant_id';
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->join('course_class ccl', 'ccl.tenant_id = pers.tenant_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');

            $trainer_where = 'AND ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id and ccl.tenant_id=ce.tenant_id';
        }

        $this->db->join('class_enrol ce', 'usr.user_id=ce.user_id ' . $trainer_where);

        $this->db->where('usr.tenant_id', $tenantID);

        if ((int) $courseID > 0) {

            $this->db->where("ce.course_id", $courseID);
        }

        if ((int) $classID > 0) {

            $this->db->where("ce.class_id", $classID);
        }

        $this->db->like('pers.first_name', $taxcode, 'both');

        $this->db->group_by('ce.user_id');

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        $this->db->limit(200);

        $results = $this->db->get()->result();

        return $results;
    }

    public function get_trainee_details($taxcode) {

        if ($taxcode) {

            $this->db->select('pers.user_id, pers.first_name, pers.last_name, pers.gender, pers.dob, usr.registration_date');

            $this->db->from('tms_users_pers pers');

            $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

            $this->db->where('usr.account_type', 'TRAINE');

            $this->db->where('usr.tenant_id', $this->session->userdata('userDetails')->tenant_id);

            $this->db->where('usr.tax_code', $taxcode);

            $results = $this->db->get()->result_array();

            return $results[0];
        }
    }

    /**

     * function to get trainee details for all trainee ids

     */
    public function get_trainee_details_for_trainee_ids($tenant_id, $trainee_ids) {

        $account_type = array("TRAINE", "INTUSR");

        $this->db->select('usr.tax_code, pers.user_id, pers.first_name, pers.last_name, pers.gender, pers.dob, usr.registration_date');

        $this->db->from('tms_users_pers pers');

        $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

        $this->db->where_in('usr.account_type', $account_type);

        $this->db->where('usr.tenant_id', $tenant_id);

        $this->db->where_in('usr.user_id', $trainee_ids);

        $results = $this->db->get()->result();

        return $results;
    }

    /**

     * Function to get all trainee details

     */
    public function get_all_trainee_details($tenant_id, $taxcode, $class_id, $name = "") {

        $this->db->select('pers.user_id, pers.first_name, pers.last_name, pers.gender, pers.dob, usr.registration_date, usr.tax_code');

        $this->db->from('tms_users_pers pers');

        $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

        if ($taxcode) {

            $this->db->like('usr.tax_code', $taxcode, 'both');
        }

        if ($name) {

            $this->db->like('pers.first_name', $name, 'both');
        }

        if ($class_id) {

            $this->db->join('class_enrol ce', 'ce.user_id=pers.user_id');

            $this->db->where('ce.class_id', $class_id);

            $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        }

        $this->db->where('usr.account_type', 'TRAINE');

        $this->db->where('usr.tenant_id', $tenant_id);

        $results = $this->db->get()->result_object();

        return $results;
    }

    /**

     * Function to get all trainee details

     */
    public function get_all_company_trainee_details($tenant_id, $trainee, $company) {

        $this->db->select('pers.user_id, pers.first_name, pers.last_name, pers.gender, pers.dob, usr.registration_date, usr.tax_code');

        $this->db->from('tms_users_pers pers');

        $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

        if ($trainee) {

            $this->db->like('pers.first_name', $trainee, 'both');
        }

        if ($company) {

            $this->db->join('class_enrol ce', 'ce.user_id=pers.user_id');

            $this->db->where('ce.company_id', $company);

            $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        }

        if ($company[0] == 'T') {

            $this->db->where('usr.account_type', 'INTUSR');
        } else {

            $this->db->where('usr.account_type', 'TRAINE');
        }

        $this->db->where('usr.tenant_id', $tenant_id);

        $this->db->group_by('usr.user_id');

        $this->db->order_by('pers.first_name, pers.last_name');

        $results = $this->db->get()->result_object();

        return $results;
    }

    /**

     * function to get all the class details

     */
    public function get_active_class_enrol($tenant_id, $course_id, $trainee_id) {
        $cur_date = date('Y-m-d');
        $this->db->select('cc.class_name,cc.lock_status,cc.class_id,c.crse_name,c.course_id,cc.class_start_datetime,cc.class_end_datetime, ce.eid_number, ce.tpg_enrolment_status')
                ->from('class_enrol ce')->join('course_class cc', 'cc.class_id=ce.class_id')
                ->join('course c', 'c.course_id=cc.course_id')->where('date(cc.class_end_datetime) >=', $cur_date)
                ->where('ce.tenant_id', $tenant_id)
                ->where('cc.class_status !=', 'INACTIV');
        if (!empty($trainee_id)) {
            $this->db->where('ce.user_id', $trainee_id);
            $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        }
        if (!empty($course_id)) {
            $this->db->where('cc.course_id', $course_id);
        }
        $this->db->order_by("date(cc.class_start_datetime)");
        $result = $this->db->get()->result_object();
        //echo print_r($result); exit;
        return $result;
    }

    public function get_tg_number($tenant_id, $payment_due_id, $user_id) {
        $this->db->select('tg_number');
        $this->db->from('class_enrol');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('pymnt_due_id', $payment_due_id);
        $this->db->where('user_id', $user_id);
        $sql = $this->db->get();
        $tg = $sql->row();
        $tg_number = $tg->tg_number;
        if ($tg_number != '') {
            return $tg_number;
        } else {
            return $sql = 0;
        }
    }

    /**

     * function to get active course with class enroll

     */
    public function get_active_course_classenroll_list_by_tenant($tenantId) {
        $this->db->cache_on();
        $cur_date = date('Y-m-d');

        $this->db->select('c.course_id, c.crse_name');

        $this->db->from('course c');

        $this->db->join('class_enrol ce', 'ce.course_id=c.course_id');

        $this->db->join('course_class cc', 'cc.class_id=ce.class_id');

        $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $this->db->where('c.tenant_id', $tenantId);

        $this->db->where('c.crse_status', 'ACTIVE');

        $this->db->distinct(TRUE);

        $query = $this->db->get();

        $tenant_active_courses = array();

        foreach ($query->result() as $item) {

            $tenant_active_courses[$item->course_id] = $item->crse_name;
        }

        return $tenant_active_courses;
    }

    /**

     * function to get reschedule class details

     */
    public function get_reschedule_class_enrol($tenant_id, $course_ids, $active_ids) {

        $cur_date = date('Y-m-d');

        $this->db->select('cc.class_name,cc.lock_status,cc.class_id,cc.lock_status, c.crse_name, c.course_id, cc.class_start_datetime, 

                cc.class_end_datetime, cc.total_seats, cc.class_pymnt_enrol')
                ->from('course_class cc')
                ->join('course c', 'c.course_id=cc.course_id')
                ->where('date(cc.class_end_datetime) >=', $cur_date)
                ->where('c.tenant_id', $tenant_id)
                ->where('cc.class_status !=', 'INACTIV');

        if (!empty($active_ids)) {

            $this->db->where_not_in('cc.class_id', $active_ids);
        }

        if (!empty($course_ids)) {

            ///$this->db->where('cc.course_id', $course_ids);commented by shubhranshu
            $this->db->where_in('cc.course_id', $course_ids); /////added by shubhranshu to show only the enrolled course id list
        }

        $this->db->order_by("date(cc.class_start_datetime)");

        $result = $this->db->get()->result_object();

        return $result;
    }

    public function get_class_details($classid) {

        if ($classid) {

            $this->db->select('pers.user_id, pers.first_name, pers.last_name, pers.gender, pers.dob, usr.registration_date');

            $this->db->from('tms_users_pers pers');

            $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

            $this->db->where('usr.account_type', 'TRAINE');

            $this->db->where('usr.tenant_id', $this->tenant_id);

            $this->db->where('usr.tax_code', $taxcode);

            $results = $this->db->get()->result_array();

            return $results[0];
        }
    }

    /*

     * This method gets the user details for a user based on the tenant

     */

    public function get_user_details($tenant_id, $user_id) {

        $this->db->select('*');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id and usr.tenant_id=pers.tenant_id');

        $this->db->where_in('usr.user_id', $user_id);

        $this->db->where('usr.tenant_id', $tenant_id);

        $qry = $this->db->get();

        return $qry->row();
    }

    /**

     * function to update subsidy, net amount

     */
    public function update_tgnumber($tenant_id, $payment_due_id, $tg_number, $user_id) {

        $this->db->trans_start();

        if (!empty($tg_number)) {

            $data = array('tg_number' => strtoupper($tg_number));

            $this->db->where('tenant_id', $tenant_id);

            $this->db->where('pymnt_due_id', $payment_due_id);

            $this->db->where('user_id', $user_id);

            $this->db->update('class_enrol', $data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return FALSE;
        }

        return TRUE;
    }

    public function update_eidnumber($tenant_id, $class, $user, $eid_number, $payid) {

        $this->db->trans_start();

        if (!empty($eid_number)) {

            $data = array('eid_number' => strtoupper($eid_number));

            $this->db->where('tenant_id', $tenant_id);

            $this->db->where('pymnt_due_id', $payid);

            $this->db->where('user_id', $user);

            $this->db->where('class_id', $class);

            $this->db->update('class_enrol', $data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return FALSE;
        }

        return TRUE;
    }

    /**

     * function to get  all invoice

     */
    public function get_allinvoice($tenant_id, $payid) {

        $this->db->select('*')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.pymnt_due_id', $payid);

        return $this->db->get()->row();
    }

    /**

     * function to get  all invoice

     */
    public function get_invoice_list($payment_due_id, $invoice_no, $tenant_id, $inv_type) {

        if ($inv_type == 'INVCOMALL') {
            $this->db->select('invoice_id,inv_type,cm.company_name,tup.first_name,tu.tax_code,total_inv_amount,ce.payment_status');
            $this->db->from('enrol_invoice ei');
            $this->db->join('company_master cm', 'cm.company_id=ei.company_id');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id');
        } else {
            $this->db->select('invoice_id,inv_type,tup.first_name,tu.tax_code,total_inv_amount,ce.payment_status');
            $this->db->from('enrol_invoice ei');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id');
        }

        $this->db->join('tms_users_pers tup', 'tup.user_id=ce.user_id and tup.tenant_id=ce.tenant_id');
        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id and tu.tenant_id=ce.tenant_id');
        $this->db->like('ei.invoice_id', $invoice_no, 'both');
        $this->db->where('ei.pymnt_due_id', $payment_due_id);
        $this->db->where('ce.tenant_id', $tenant_id);
        $res = $this->db->get()->result();
        //echo $this->db->last_query();exit;
        //print_r($res);exit;
        return $res;
    }

    public function invoice_list($tenant_id, $invoice_no) {
        $this->db->select('invoice_id,pymnt_due_id,inv_type');
        $this->db->from('enrol_invoice');
        $this->db->like('invoice_id', $invoice_no, 'both');
        $res = $this->db->get()->result();
        return $res;
    }

    public function get_invoice($tenant_id, $invoice, $paid = 0) {

        $this->db->select('ei.invoice_id,tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('course crse', 'crse.course_id=ce.course_id');

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);
        }

        $this->db->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        if (!empty($paid)) {
            if ($paid == "VOID") {
                $this->db->where('ce.payment_status', "VOID");
            } else {
                $this->db->where('ce.payment_status', $paid);
            }
        }

        if (!empty($invoice)) {

            $this->db->like('ei.invoice_id', $invoice, 'both');
        }

        $this->db->order_by('LENGTH(ei.invoice_id)');  //commented by shubhranshu as not required 
        //$this->db->order_by('ei.invoice_id','DESC');////addded by shubhranshu(desc)////
        //$this->db->limit(100);////Added by shubhranshu to optimize the query

        return $this->db->get()->result_object();
    }

    /**

     * function to get not paid invoice

     */
    public function get_notpaid_invoice($tenant_id, $invoice) {

        $this->db->select('ei.invoice_id,tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('course crse', 'crse.course_id=ce.course_id');

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);
        }

        $this->db->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where_in('ce.payment_status', array('PARTPAID', 'NOTPAID'));
        //->order_by('LENGTH(ei.invoice_id)')///commented by shubhranshu
        //->order_by('ei.invoice_id');//commented by shubhranshu

        if ($invoice) {

            $this->db->like('ei.invoice_id', $invoice, 'both');
        }
        $this->db->order_by('ei.invoice_id', 'DESC'); ////addded by shubhranshu(desc)////
        //$this->db->limit(100);////Added by shubhranshu to optimize the query

        return $this->db->get()->result_object();
    }

    /**

     * get payment pending company

     */
    public function check_company_pending_payment($company) {

        $company = $this->db->select('class_id')->from('class_enrol')->where('company_id', $company)
                        ->where_not_in('payment_status', array('PAID'))->get()->num_rows();

        if ($company > 0) {

            return 'pending payments';
        }
    }

    /* get company list whos payment status notpaid */

    public function get_company_list($tenant_id) {

        $this->db->select('cm.company_id,cm.company_name');
        $this->db->from('company_master cm');
        $this->db->join('tenant_company tm', 'tm.company_id = cm.company_id');
        $this->db->join('class_enrol ce', 'ce.company_id = cm.company_id');
//        $this->db->group_by('ce.pymnt_due_id');
//        $this->db->where('ce.payment_status','NOTPAID');
        $this->db->where('tm.tenant_id', $tenant_id);
        $this->db->order_by('cm.company_name');
        $sql = $this->db->get();
        return $sql->result_array();
    }

    /* This function get only notpaid invoice of company */

    public function get_company_notpaid_invoices_list($tenant_id, $company_id) {
        if ($this->user->role_id == 'CRSEMGR') {

            $crsemgr_where = " AND FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=0";
        }
        -
                $result_set = $this->db->query("SELECT
            
        inv.`invoice_id`, inv.`pymnt_due_id`, enrl.payment_status, inv.company_id, 

        enrl.course_id, enrl.class_id, crse.crse_name,        

        cls.class_name, cls.lock_status, comp.company_name, comp.company_id

        FROM enrol_invoice inv, class_enrol enrl, course crse, course_class cls, company_master comp

        WHERE 

        inv.`pymnt_due_id` = enrl.`pymnt_due_id`  AND

        enrl.class_id = cls.class_id AND

        enrl.course_id = cls.course_id AND

        cls.course_id = crse.course_id AND

        inv.company_id = comp.company_id AND

        enrl.enrolment_mode = 'COMPSPON' AND
        
        comp.company_id = '$company_id' AND

        enrl.tenant_id = '$tenant_id'" . $crsemgr_where . "

        $extra

        ORDER BY date(cls.class_start_datetime)");

        $result = $result_set->result();

        foreach ($result as $item) {

            $invoice_array[$item->invoice_id][] = $item;
        }

        foreach ($invoice_array as $invoices) {

            $flag = TRUE;

            $temp = array();


            foreach ($invoices as $invoice) {

                $temp1 = $invoice;


                $temp[] = $invoice->payment_status;
            }

            if (in_array('PAID', $temp)) {

                $flag = FALSE;
            } else if (in_array('PARTPAID', $temp)) {

                $flag = FALSE;
            }

            if ($flag) {

                $final[] = $temp1;
            }
        }


        return $final;
    }

    /**

     * get company only having notpaid invoice

     */
    public function get_notpaid_invoice_company($tenant_id) {

        $invoice = $this->invoice_company_notpaid_invoice($tenant_id, '', '');

        if (!empty($invoice)) {

            $this->db->select('cm.company_id');

            $this->db->select('cm.company_name');

            $this->db->from('enrol_invoice ei');

            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id');

            $this->db->join('tenant_company tc', 'tc.company_id=ce.company_id');

            $this->db->join('company_master cm', 'cm.company_id=tc.company_id');



            $this->db->where_in('ei.invoice_id', $invoice);



            $this->db->where('tc.tenant_id', $tenant_id);

            $this->db->where('tc.comp_status', 'ACTIVE');

            $this->db->group_by('ce.company_id');

            $this->db->order_by('cm.company_name');

            return $this->db->get()->result();
        }
    }

    /**

     * function to get not paid company invoice

     */
    public function invoice_company_notpaid_invoice($tenant_id, $invoice, $company) {

        $this->db->select('ei.invoice_id');

        $this->db->from('class_enrol ce');

        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');

        $this->db->where('ei.inv_type', 'INVCOMALL');

        $this->db->where('ce.enrolment_mode', 'COMPSPON');

        $this->db->where('ce.tenant_id', $tenant_id);

        if ($invoice) {

            $this->db->like('ei.invoice_id', $invoice, 'both');
        }

        $this->db->group_by('ce.pymnt_due_id');

        if ($company) {

            $this->db->where('ce.company_id', $company);
        }

        $this->db->where_in('ce.payment_status', array('NOTPAID', 'PARTPAID'));



        $subQuery = $this->db->get()->result();

        $inv_arr = array();

        foreach ($subQuery as $row) {

            $inv_arr[] = $row->invoice_id;
        }

        return $inv_arr;
    }

    /**

     * sum of invoices paid on company invoices

     */
    public function sumof_company_notpaid_invoice($tenant_id, $invioce, $company) {

        $inv_arr = $this->invoice_company_notpaid_invoice($tenant_id, $invioce, $company);

        if (!empty($inv_arr)) {

            $this->db->select('epr.invoice_id')
                    ->select_sum('epr.amount_recd', 'sum_on')
                    ->from('enrol_paymnt_recd epr')
                    ->join('enrol_invoice ei', 'epr.invoice_id=ei.invoice_id')
                    ->where_in('epr.invoice_id', $inv_arr);



            $this->db->group_by('epr.invoice_id');



            $result = $this->db->get()->result();
        } else {

            $result = array();
        }

        return $result;
    }

    /**

     * Function to get not paid invoice for Company Enrolments

     */
    public function get_company_based_pay_invoice($tenant_id, $invoice, $company, $is_paid, $all_paid = 0, $paid = 0) {

        $this->db->select('crse.crse_name');
        $this->db->select('count(*) as totalrows');
        $this->db->select('class.class_name');

        $this->db->select('inv.invoice_id');

        $this->db->from('class_enrol enrol');

        $this->db->join('enrol_invoice inv', 'enrol.pymnt_due_id = inv.pymnt_due_id');

        $this->db->join('course crse', 'enrol.course_id = crse.course_id');

        $this->db->join('course_class class', 'enrol.class_id =class.class_id and crse.course_id = class.course_id');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);
        }

        if (empty($all_paid)) {

            if (empty($is_paid)) {

                $this->db->where_in('enrol.payment_status', array('PARTPAID', 'NOTPAID'));
            } else {

                $this->db->where_in('enrol.payment_status', array('PARTPAID', 'PAID'));
            }
        }

        if (!empty($paid)) {

            $this->db->where('enrol.payment_status', $paid);
        }

        $this->db->where('enrol.company_id', $company);

        $this->db->where('enrol.tenant_id', $tenant_id);

        if (!empty($invoice)) {

            $this->db->like('inv.invoice_id', $invoice, 'both');
        }

        $this->db->distinct(TRUE);

        $this->db->order_by('LENGTH(invoice_id)');

        $this->db->order_by('invoice_id');

        $this->db->group_by('enrol.pymnt_due_id');

        $result = $this->db->get()->result_object();


        return $result;
    }

    /**

     * function to get not paid invoices for Individual Enrolments

     */
    public function get_paid_invoice($tenant_id, $invoice) {
        $this->db->distinct();
        $this->db->select('epbd.invoice_id,tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id')
                //->join('enrol_paymnt_recd epr', 'epr.invoice_id=ei.invoice_id')
                ->join('enrol_pymnt_brkup_dt epbd', 'ce.user_id=epbd.user_id and epbd.invoice_id=ei.invoice_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('course crse', 'crse.course_id=ce.course_id');

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);
        }

        $this->db->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where('ce.payment_status', 'PAID')
                ->like('ei.invoice_id', $invoice, 'both');

        $this->db->order_by('LENGTH(ei.invoice_id)');

        $this->db->order_by('ei.invoice_id');

        return $this->db->get()->result_object();
    }

    /**

     * function to all indi and company invoice

     */
    public function get_paid_indi_company_invoice($tenant_id, $invoice) {

        $this->db->select('ei.invoice_id,ei.inv_type, tup.first_name, tup.last_name, tu.tax_code, cm.company_name, cm.comp_regist_num')
                ->from('class_enrol ce')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id')
                ->join('enrol_paymnt_recd epr', 'epr.invoice_id=ei.invoice_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('company_master cm', 'cm.company_id=ce.company_id', 'left')
                ->where('ce.tenant_id', $tenant_id)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where('ce.payment_status', 'PAID')
                ->like('ei.invoice_id', $invoice, 'both');

        //$this->db->order_by('LENGTH(ei.invoice_id)');//commented by shubhranshu
        //$this->db->order_by('ei.invoice_id');//commented by shubhranshu
        $this->db->order_by('ei.invoice_id', 'DESC'); ////addded by shubhranshu(desc)////
        $this->db->limit(1500); ////Added by shubhranshu to optimize the query

        return $this->db->get()->result_object();
    }

    /* (*

     * function created to get trainee name

     */

    public function get_notenrol_trainee_name($invoice_id, $taxcode_id, $trainee_id, $tenant_id) {

        $this->db->select('tup.first_name as first, tup.last_name as last, tup.gender, tu.tax_code')
                ->from('tms_users tu')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        if (!empty($invoice_id)) {

            $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');

            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if (!empty($taxcode_id)) {

            if (empty($invoice_id)) {

                $this->db->where('tu.user_id', $taxcode_id);
            } else {

                $this->db->or_where('tu.user_id', $taxcode_id);
            }
        }

        if (!empty($trainee_id)) {

            if (empty($invoice_id) && empty($taxcode_id)) {

                $this->db->where('tu.user_id', $trainee_id);
            } else {

                $this->db->or_where('tu.user_id', $trainee_id);
            }
        }

        return $this->db->get()->row();
    }

    /*

     * function created to get trainee name

     */

    public function get_trainee_name($invoice_id, $taxcode_id, $trainee_id, $tenant_id) {

        $this->db->select('tup.first_name as first, tup.last_name as last, tup.gender, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->where('ce.tenant_id', $tenant_id);

        if (!empty($invoice_id)) {

            $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');

            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if ($taxcode_id) {

            if (empty($invoice_id)) {

                $this->db->where('tu.user_id', $taxcode_id);
            } else {

                $this->db->or_where('tu.user_id', $taxcode_id);
            }
        }

        if ($trainee_id) {

            if (empty($invoice_id) && empty($taxcode_id)) {

                $this->db->where('tu.user_id', $trainee_id);
            } else {

                $this->db->or_where('tu.user_id', $trainee_id);
            }
        }

        return $this->db->get()->row();
    }

    /**

     * function to get current gst

     */
    public function get_gst_current() {

        $result = $this->db->select('gst_rate')->from('gst_rates')->where('is_current', 1)->get()->row()->gst_rate;

        return $result;
    }

    /**

     * This method gets the Individual Discount applicable

     * @param type $trainee_id

     * @param type $class_id

     * @param type $course_id

     * @param type $unit_fees

     * @return type

     */
    public function calculate_discount_enroll($trainee_id, $company_id, $class_id, $course_id, $unit_fees) {

        $tenant_id = $this->user->tenant_id;

        if ($company_id == 0) {

            $result_array = $this->get_discnt($tenant_id, 0, $course_id, $class_id, $unit_fees, $trainee_id);
        } else {

            $result_array = $this->get_discnt($tenant_id, $company_id, $course_id, $class_id, $unit_fees, 0);
        }

        $discount_type = $result_array['discount_label'];

        $discount_rate = $result_array['discount_rate'];

        $discount_amount = $result_array['discount_amount'];


        if ($discount_type == 'DISINDVI') {

            return array('discount_label' => 'Individual', 'discount_rate' => $discount_rate, 'discount_metalabel' => 'DISINDVI');
        } elseif ($discount_type == 'DISCOMP') {

            return array('discount_label' => 'Company', 'discount_rate' => $discount_rate, 'discount_metalabel' => 'DISCOMP');
        } elseif ($discount_type == 'DISCLASS') {

            return array('discount_label' => 'Class', 'discount_rate' => $discount_rate, 'discount_metalabel' => 'DISCLASS');
        } else {

            return array('discount_label' => 'Individual', 'discount_rate' => 0, 'discount_metalabel' => 'DISINDVI');
        }
    }

    /**

     * function to get discount calculation

     */
    public function calculate_discount_for_userid($trainee_id, $payid) {

        if ($trainee_id) {

            $individual = $this->db->select('individual_discount')->from('tms_users_pers')->where('user_id', $trainee_id)
                            ->get()->row()->individual_discount;

            $company = $this->db->select('tc.comp_discount')->from('tenant_company_users tcu')
                            ->join('tenant_company tc', 'tc.company_id=tcu.company_id')->where('tcu.user_id', $trainee_id)
                            ->get()->row()->comp_discount;

            $class = $this->db->select('cc.class_discount')->from('class_enrol ce')
                            ->join('course_class cc', 'cc.class_id=ce.class_id')
                            ->where('ce.user_id', $trainee_id)->where('ce.pymnt_due_id', $payid)
                            ->get()->row()->class_discount;

            if (!empty($individual)) {

                return array('discount_label' => 'Individual', 'discount_rate' => $individual);
            } elseif (!empty($company)) {

                return array('discount_label' => 'Company', 'discount_rate' => $company);
            } elseif (!empty($class)) {

                return array('discount_label' => 'class', 'discount_rate' => $class);
            } else {

                return array('discount_label' => 'Individual', 'discount_rate' => 0);
            }
        }
    }

    /**

     * function to get all individual invoice

     */
    public function get_user($tenant_id, $taxcode, $username, $paid = 0) {

        $taxcode = trim($taxcode);

        $username = trim($username);

        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('enrol_pymnt_due epd', 'epd.user_id=ce.user_id and epd.pymnt_due_id=ce.pymnt_due_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id and tu.tenant_id=ce.tenant_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        if (!empty($paid)) {

            $this->db->where('ce.payment_status', $paid);
        }

        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        $this->db->group_by('tup.user_id');

        $this->db->limit(200);

        return $this->db->get()->result_object();
    }

    /**

     * function to get not paid invoice

     */
    public function get_notpaid_user($tenant_id, $taxcode, $username) {

        $taxcode = trim($taxcode);

        $username = trim($username);

        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id and tu.tenant_id=ce.tenant_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where_in('ce.payment_status', array('PARTPAID', 'NOTPAID'));

        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        $this->db->group_by('tup.user_id');

        $this->db->limit(200);

        return $this->db->get()->result_object();
    }

    /**

     * function to get not paid invoice

     */
    public function get_paid_user($tenant_id, $taxcode, $username) {

        $taxcode = trim($taxcode);

        $username = trim($username);
        $this->db->distinct();
        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                //  ->join('enrol_paymnt_recd epr', 'epr.invoice_id=ei.invoice_id')
                ->join('enrol_pymnt_brkup_dt epbd', 'ce.user_id=epbd.user_id and epbd.invoice_id=ei.invoice_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id and tu.tenant_id=ce.tenant_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where('ce.payment_status', 'PAID');

        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        $this->db->group_by('tup.user_id');

        $this->db->limit(200);

        return $this->db->get()->result_object();
    }

    /**

     * calculate Net Due

     */
    public function calculate_net_due($gst_onoff, $subsidy_after_before, $feesdue, $subsidy, $gst_rate) {

        if ($gst_onoff == 1) {

            if ($subsidy_after_before == 'GSTBSD') {

                return round((($feesdue + ($feesdue * ($gst_rate) / 100)) - $subsidy), 4);
            } else {

                $feesduetemp = ($feesdue - $subsidy);

                return round(( $feesduetemp + ( $feesduetemp * ($gst_rate) / 100)), 4);
            }
        } else {

            return round(($feesdue - $subsidy), 4);
        }
    }

    /**

     * Regenerate Invoice

     */
    public function re_generate_invoice() {

        $cur_date = date('Y-m-d H:i:s');

        $post_invoice = $this->input->post('invoice_hidden_id');

        $select_reinvoice = $this->input->post('select_reinvoice');

        if (in_array(2, $select_reinvoice)) {

            $discount_totalamt = $this->input->post('regen2_form_dis_amt');

            $discount_rate = $this->input->post('regen2_form_dis_perc');

            $discount_type = $this->input->post('regen2_form_dis_type');
        } else {

            $discount_totalamt = $this->input->post('regen2_hid_dis_amt');

            $discount_rate = $this->input->post('regen2_hid_dis_perc');

            $discount_type = $this->input->post('regen2_hid_dis_type');
        }

        $this->db->select('ei.*, epd.*');

        $this->db->from('enrol_invoice ei');

        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ei.pymnt_due_id');

        $this->db->where('ei.invoice_id', $post_invoice);

        $this->db->group_by('ei.invoice_id');

        $res = $this->db->get()->result();

        $input = $res[0];

        if ($this->input->post('inv_class_fee') == 1) {

            $class_id = $this->db->select('class_id')->from('class_enrol')->where('pymnt_due_id', $input->pymnt_due_id)->get()->row('class_id');

            $input->class_fees = $this->db->select('class_fees')->from('course_class')->where('class_id', $class_id)->get()->row('class_fees');
        }

        $trainee = $this->input->post('trainee');

        $subsidy_type_arr = $this->input->post('subsidy_type');

        if (in_array(1, $select_reinvoice)) {

            $amount_paying = $this->input->post('amount_paying');

            $amount_recd_paying = $this->input->post('amount_recd_paying');
        } else {

            $amount_paying = $this->input->post('amount_check');

            $amount_recd_paying = $this->input->post('amount_recd_check');
        }

        $gst_on_off = ($input->total_gst == 0) ? 0 : 1;

        $subsidy_after_before = $input->gst_rule;

        if (!empty($discount_totalamt)) {

            $discount_total = round($discount_totalamt, 4);

            $discount_rate = ($discount_total * 100) / $input->class_fees;

            $discount_rate = round($discount_rate, 4);
        } else {

            $discount_rate = round($discount_rate, 4);

            $discount_total = ( $discount_rate * $input->class_fees) / 100;

            $discount_total = round($discount_total, 4);
        }

        $feesdue = $input->class_fees - ($discount_total);

        $feesdue = round($feesdue, 4);

        $company_net_due = 0;

        $company_discount = 0;

        $company_subsidy = 0;

        $company_total_gst = 0;

        $logged_in_user_id = $this->user->user_id;
        $data = $this->get_current_invoice_data($input->pymnt_due_id);


        if (in_array(1, $select_reinvoice)) {
            $val = 'subsidy';
        } else {
            $val = 'discount';
        }
        $due_to = 'Regenrated Invoice due to changes in ' . $val;
        $status = $this->enrol_invoice_view($input->pymnt_due_id, $data, $logged_in_user_id, $due_to);

        foreach ($trainee as $k => $row) {

            $user_id = $k;

            $subsidy_amount = $amount_paying[$user_id];

            $subsidy_type = $subsidy_type_arr[$user_id];

            $subsidy_recd = $amount_recd_paying[$user_id];

            $subsidy_recd_on = ($subsidy_recd == '') ? '' : date('Y-m-d', strtotime($subsidy_recd));



            $netdue = $this->calculate_net_due($gst_on_off, $subsidy_after_before, $feesdue, $subsidy_amount, $input->gst_rate);

            if ($netdue < 0) {

                $this->session->set_flashdata("error", "The net amount is negative. Unable to regenerate.");

                redirect('accounting/generate_invoice');
            }

            $totalgst = $this->calculate_gst($gst_on_off, $subsidy_after_before, $feesdue, $subsidy_amount, $input->gst_rate);

            $data = array(
                'class_fees' => $input->class_fees,
                'total_amount_due' => round($netdue, 2), //sk1
                'discount_type' => $discount_type,
                'discount_rate' => round($discount_rate, 4),
                'subsidy_type_id' => $subsidy_type,
                'subsidy_amount' => round($subsidy_amount, 4),
                'subsidy_recd_date' => $subsidy_recd_on,
                'gst_amount' => round($totalgst, 2), //sk2
            );

            if ($row == 2) {

                $data['subsidy_modified_on'] = $cur_date;
            }

            $this->db->where('user_id', $user_id);

            $this->db->where('pymnt_due_id', $input->pymnt_due_id);

            $this->db->update('enrol_pymnt_due', $data);

            $company_total_gst = $company_total_gst + round($totalgst, 2); ### added by dummy //sk3

            $company_net_due = $company_net_due + round($netdue, 2); //sk4

            $company_discount = $company_discount + round($discount_total, 4);

            $company_subsidy = $company_subsidy + round($subsidy_amount, 4);
        }

        $invoice_id = $this->generate_invoice_id();



        $data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $input->pymnt_due_id,
            'inv_date' => $cur_date,
            'inv_type' => 'INVCOMALL',
            'company_id' => $input->company_id,
            'total_inv_amount' => round($company_net_due, 2), //sk5
            'total_unit_fees' => round($input->class_fees * count($trainee), 4),
            'total_inv_discnt' => round($company_discount, 4),
            'total_inv_subsdy' => round($company_subsidy, 4),
            'total_gst' => round($company_total_gst, 2), //sk6
            'gst_rate' => round($input->gst_rate, 2), //sk7
            'gst_rule' => $input->gst_rule,
            'invoiced_on' => $input->invoiced_on,
        );

        $this->db->insert('enrol_invoice', $data);
// commented by skm 02-01-17 because of refunded status remark in invoice st       
        /* Skm code if invoice is paid and then refund and after that re-generate due to change in discount start */

//        $previous_inv_id = $input->invoice_id;
//        $new_invoice_id = $invoice_id;
//        $query1="select * from enrol_paymnt_recd where invoice_id='$previous_inv_id'";
//        $query =  mysqli_query($query1);
//                           
//        if($query)
//        { 
//            $sql="update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//            $fi = mysqli_query($sql); 
//            $sql1="update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//            $fi2 = mysqli_query($sql1); 
//
//        }
//
//        $query2="select * from enrol_refund where invoice_id='$previous_inv_id'";
//        $query = mysqli_query($query2);
//
//        if($query)
//        { 
//            $previous_inv_id = $input->invoice_id;
//            $new_invoice_id = $invoice_id;
//            $sql="update enrol_refund set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//            $si = mysqli_query($sql);
//            $sql1="update enrol_refund_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//            $sifi = mysqli_query($sql1);
//
//        }
        /* end */


        $data = array(
            'invoice_id' => $input->invoice_id,
            'pymnt_due_id' => $input->pymnt_due_id,
            'inv_date' => $input->inv_date,
            'inv_type' => 'INVCOMALL',
            'company_id' => $input->company_id,
            'regen_inv_id' => $invoice_id,
            'total_inv_amount' => $input->total_inv_amount,
            'total_unit_fees' => $input->total_unit_fees,
            'total_inv_discnt' => $input->total_inv_discnt,
            'total_inv_subsdy' => $input->total_inv_subsdy,
            'total_gst' => round($input->total_gst, 2), //sk8
            'gst_rate' => round($input->gst_rate, 2), //sk9
            'gst_rule' => $input->gst_rule,
            'invoice_generated_on' => $cur_date,
            'invoiced_on' => $input->invoiced_on,
            'invoice_excess_amt' => $input->invoice_excess_amt
        );

        $this->db->insert('enrol_invoice_audittrail', $data);

        //$this->db->where('invoice_id', $post_invoice);
        //$delete_result = $this->db->delete('enrol_invoice');
        if (!empty($post_invoice)) {
            $this->db->where('invoice_id', $post_invoice);
            $delete_result = $this->db->delete('enrol_invoice');
        }
        $this->set_viewinvoice_newinvoice_num($input->pymnt_due_id, $invoice_id);

        return $invoice_id;
    }

    /**

     * This method updates the DB with Company enrolment details

     * @param type $tenant_id

     * @param type $loggedin_user_id

     * @param type $company_details

     * @param type $discount_changed

     * @return type

     */
    public function company_enrollment_db_update($tenant_id, $loggedin_user_id, $company_details, $discount_changed, $reschedule = 0) {

        $status = TRUE;
        extract($_POST);
        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);
        $invoice_id = $this->generate_invoice_id();
        $company_net_due = 0;
        $company_discount = 0;
        $company_subsidy = 0;
        $company_gst = 0;
        $company_total_unitfees = 0;
        $course = $this->input->post('course');
        $classes = $this->db->select('certi_coll_date,class_fees,course_id,class_id,class_discount')->from('course_class')->where('class_id', $class)->get()->row();
        $courses = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $classes->course_id)->get()->row();
        $dis_label = ($discount_label == 'Class') ? 'DISCLASS' : 'DISCOMP';
        $cal_discount['discount_metalabel'] = $dis_label;
        $total_trainee_count = count($data);
        //print_r($classes);
        if ($discount_changed == 'Y') {
            $temp_ind_discnt_amt = $discount_amount;
            $indv_discount_rate = round((($temp_ind_discnt_amt / $classes->class_fees) * 100), 4);
            $indv_discount_amt = round(($classes->class_fees * ($indv_discount_rate / 100)), 4);
        } else {
            $discount = $this->classtraineemodel->calculate_discount_enroll(0, $company, $classes->class_id, $classes->course_id, $classes->class_fees);
            $indv_discount_rate = $discount['discount_rate'];
            $indv_discount_amt = round(($classes->class_fees * ($indv_discount_rate / 100)), 4);
            if ($indv_discount_amt > $classes->class_fees) {
                $indv_discount_rate = 100;
                $indv_discount_amt = $classes->class_fees;
            }
        }
        $indv_fees_due = round(($classes->class_fees - $indv_discount_amt), 4);
        $gst_rate = $this->get_gst_current();
        $i = 0;
        //$this->db->trans_start();        
        foreach ($data as $row) {
            $user_id = $row['user_id'];
            $check = $this->db->select('*')
                            ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $classes->course_id)
                            ->where('class_id', $class)->where('user_id', $user_id)->get();
            //echo $this->db->last_query();
            $err_arr = array();
            if ($check->num_rows() == 0) {
                $subsidy_recd_on = ($row['subsidy_date'] == '') ? '' : date('Y-m-d', strtotime($row['subsidy_date']));
                $subsidy_amount = $row['subsidy_amount'];
                $ind_net_due = $this->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $indv_fees_due, $subsidy_amount, $gst_rate);

                $ind_net_due = round($ind_net_due, 2); //sk1 new add
                $ind_gst = round($this->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $indv_fees_due, $subsidy_amount, $gst_rate), 2); //sk2
                if (($enrollment_type == 1) && ($payment_retake == 2)) {
                    $pay_status = 'PYNOTREQD';
                    $enrol_status = 'ENRLACT';
                    $payment_due_id = 0;
                } else {
                    $pay_status = 'NOTPAID';
                    $enrol_status = 'ENRLBKD';
                }
                $enrollment_type_text = ($enrollment_type == 1) ? 'RETAKE' : 'FIRST';
                $cur_date = date('Y-m-d H:i:s');
                $tg_number = $row['tg'];                
                $subsidy_type_id = $row['subsidy_type'];
                $class_status = $this->get_class_statustext($class);

                /* sales executive thread */
                if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
                    $salesexec = $this->user->user_id;
                } else {
                    ///$salesexec = empty($salesexec) ? NULL : $salesexec;
                    if (empty($salesexec)) {
                        $salesexec = $this->user->user_id;
                    } else {
                        $salesexec = $salesexec;
                    }
                }

                $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);
                if ($check_attendance > 0) {
                    $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course, $class, $user_id);
                    if ($check_attendance_trainee > 0) {
                        $training_score = 'C';
                        $att_status = 1;
                    } else {
                        $training_score = 'ABS';
                        $att_status = 0;
                    }
                } else {
                    $att_status = 1;
                }
                // this code run on reschedule start   

                if ($reschedule == 1) {
                    $res = $this->company_invoice_exists($classes->course_id, $class, $company);
                    $total_val = count($res);

                    if ($total_val == 0) {
                        //                    echo "no invoice found--".$payment_due_id;
                    } else {
                        //                  echo "invoice found--"; 
                        $selected_trainee = array('0' => $user_id);
                        $res1 = $this->add_to_company_enrollment($tenant_id, $loggedin_user_id, $classes->course_id, $class, $company, $res['invoice_id'], $res['pymnt_due_id'], $selected_trainee);
                        return array('err' => $err_arr, 'invoice' => $res['invoice_id'], 'status' => $status, 'pymnt_due_id' => $res['pymnt_due_id']);
                    }
                }
                // this code run on reschedule end 
                $data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $classes->course_id,
                    'class_id' => $class,
                    'user_id' => $user_id,
                    'enrolment_type' => $enrollment_type_text,
                    'enrolment_mode' => 'COMPSPON',
                    'pymnt_due_id' => $payment_due_id,
                    'company_id' => $company,
                    'enrolled_on' => $cur_date,
                    'enrolled_by' => $loggedin_user_id,
                    'tg_number' => $tg_number,
                    'training_score' => $training_score,
                    'payment_status' => $pay_status,
                    'sales_executive_id' => $salesexec,
                    'class_status' => $class_status,
                    'enrol_status' => $enrol_status
                );

                $this->db->insert('class_enrol', $data);
                //echo $this->db->last_query();

                if (!empty($payment_due_id)) {
                    $data = array(
                        'user_id' => $user_id,
                        'pymnt_due_id' => $payment_due_id,
                        'class_fees' => round($classes->class_fees, 2), //sk3
                        'total_amount_due' => round($ind_net_due, 2), //sk4
                        'discount_type' => $dis_label,
                        'discount_rate' => $indv_discount_rate,
                        'subsidy_type_id' => $subsidy_type_id,
                        'subsidy_amount' => round($subsidy_amount, 2), //sk5
                        'subsidy_recd_date' => $subsidy_recd_on,
                        'subsidy_modified_on' => $cur_date,
                        'gst_amount' => round($ind_gst, 2), //sk6
                        'att_status' => $att_status
                    );
                    $this->db->insert('enrol_pymnt_due', $data);
                    //echo $this->db->last_query();
                }
            } else {
                $err_arr[] = $row['user_id'];
            }
            if ($check_attendance > 0) {
                if ($check_attendance_trainee > 0) {
                    $company_net_due = round(($company_net_due + $ind_net_due), 4);
                    $company_subsidy = round(( $company_subsidy + round($subsidy_amount, 4)), 4);
                    $company_gst = round(( $company_gst + $ind_gst), 2); //sk7
                    $company_total_unitfees = round(($company_total_unitfees + $classes->class_fees), 4);
                    $i++;
                }
            } else {
                $company_net_due = round(($company_net_due + $ind_net_due), 4);
                $company_subsidy = round(( $company_subsidy + round($subsidy_amount, 4)), 4);
                $company_gst = round(( $company_gst + $ind_gst), 2); //sk8
                $company_total_unitfees = round(($company_total_unitfees + $classes->class_fees), 4);
                $i++;
            }
        }
        $total_trainee_count = $i;

        $data = $this->db->select('class_start_datetime as start')
                        ->from('course_class')
                        ->where('class_id', $class)
                        ->where('tenant_id', $tenant_id)
                        ->get()->row(0);
        $start = $data->start;
        //$this->db->last_query();
        $cur_date = date('Y-m-d H:i:s');
        if ($start) {
            $cur_date = $start;
        }
        if (!empty($payment_due_id)) {
            $gst_rule = (empty($courses->gst_on_off)) ? '' : $courses->subsidy_after_before;
            $data = array(
                'invoice_id' => $invoice_id,
                'pymnt_due_id' => $payment_due_id,
                'inv_date' => $cur_date,
                'inv_type' => 'INVCOMALL',
                'company_id' => $company,
                'total_inv_amount' => round($company_net_due, 2), //sk9
                'total_unit_fees' => $company_total_unitfees,
                'total_inv_discnt' => round($indv_discount_amt * $total_trainee_count, 4),
                'total_inv_subsdy' => $company_subsidy,
                'total_gst' => round($company_gst, 2), //sk10
                'gst_rate' => round($gst_rate, 2), //sk11
                'gst_rule' => $gst_rule,
            );
            // }
            $ins_sta = $this->db->insert('enrol_invoice', $data);
            //echo $this->db->last_query();exit;
        } else {
            $invoice_id = '';
        }
        //$this->db->trans_complete();
//        if ($this->db->trans_status() === FALSE) 
        if (!$ins_sta) {
            $status = FALSE;
        }
        return array('err' => $err_arr, 'invoice' => $invoice_id, 'status' => $status, 'pymnt_due_id' => $payment_due_id);
    }

    public function company_enrollment_db_update_backup($tenant_id, $loggedin_user_id, $company_details, $discount_changed, $reschedule = 0) {

        $status = TRUE;
        extract($_POST);
        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);
        $invoice_id = $this->generate_invoice_id();
        $company_net_due = 0;
        $company_discount = 0;
        $company_subsidy = 0;
        $company_gst = 0;
        $company_total_unitfees = 0;
        $course = $this->input->post('course');
        $classes = $this->db->select('certi_coll_date,class_fees,course_id,class_id,class_discount')->from('course_class')->where('class_id', $class)->get()->row();
        $courses = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $classes->course_id)->get()->row();
        $dis_label = ($discount_label == 'Class') ? 'DISCLASS' : 'DISCOMP';
        $cal_discount['discount_metalabel'] = $dis_label;
        //print_r($data);echo count($data);exit;
        $total_trainee_count = count($data);
        if ($discount_changed == 'Y') {
            $temp_ind_discnt_amt = $discount_amount;
            $indv_discount_rate = round((($temp_ind_discnt_amt / $classes->class_fees) * 100), 4);
            $indv_discount_amt = round(($classes->class_fees * ($indv_discount_rate / 100)), 4);
        } else {
            $discount = $this->classtraineemodel->calculate_discount_enroll(0, $company, $classes->class_id, $classes->course_id, $classes->class_fees);
            $indv_discount_rate = $discount['discount_rate'];
            $indv_discount_amt = round(($classes->class_fees * ($indv_discount_rate / 100)), 4);
            if ($indv_discount_amt > $classes->class_fees) {
                $indv_discount_rate = 100;
                $indv_discount_amt = $classes->class_fees;
            }
        }
        $indv_fees_due = round(($classes->class_fees - $indv_discount_amt), 4);
        $gst_rate = $this->get_gst_current();
        $i = 0;
        foreach ($data as $row) {
            $user_id = $row['user_id'];
            $check = $this->db->select('*')
                            ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $classes->course_id)
                            ->where('class_id', $class)->where('user_id', $user_id)->get();
            $err_arr = array();
            if ($check->num_rows() == 0) {
                $subsidy_recd_on = ($row['subsidy_date'] == '') ? '' : date('Y-m-d', strtotime($row['subsidy_date']));
                $subsidy_amount = $row['subsidy_amount'];
                $ind_net_due = $this->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $indv_fees_due, $subsidy_amount, $gst_rate);
                $ind_net_due = round($ind_net_due, 2); //sk1 new add
                $ind_gst = round($this->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $indv_fees_due, $subsidy_amount, $gst_rate), 2); //sk2
                if (($enrollment_type == 1) && ($payment_retake == 2)) {
                    $pay_status = 'PYNOTREQD';
                    $enrol_status = 'ENRLACT';
                    $payment_due_id = 0;
                } else {
                    $pay_status = 'NOTPAID';
                    $enrol_status = 'ENRLBKD';
                }
                $enrollment_type_text = ($enrollment_type == 1) ? 'RETAKE' : 'FIRST';
                $cur_date = date('Y-m-d H:i:s');
                $tg_number = $row['tg'];
                $subsidy_type_id = $row['subsidy_type'];
                $class_status = $this->get_class_statustext($class);

                /* sales executive thread */
                if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
                    $salesexec = $this->user->user_id;
                } else {
                    ///$salesexec = empty($salesexec) ? NULL : $salesexec;
                    if (empty($salesexec)) {
                        $salesexec = $this->user->user_id;
                    } else {
                        $salesexec = $salesexec;
                    }
                }

                $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);
                if ($check_attendance > 0) {
                    $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course, $class, $user_id);
                    if ($check_attendance_trainee > 0) {
                        $training_score = 'C';
                        $att_status = 1;
                    } else {
                        $training_score = 'ABS';
                        $att_status = 0;
                    }
                } else {
                    $att_status = 1;
                }
                // this code run on reschedule start   
                if ($reschedule == 1) {
                    $res = $this->company_invoice_exists($classes->course_id, $class, $company);
                    $total_val = count($res);
                    if ($total_val == 0) {
                        //                    echo "no invoice found--".$payment_due_id;
                    } else {
                        //                  echo "invoice found--"; 
                        $selected_trainee = array('0' => $user_id);
                        $res1 = $this->add_to_company_enrollment($tenant_id, $loggedin_user_id, $classes->course_id, $class, $company, $res['invoice_id'], $res['pymnt_due_id'], $selected_trainee);
                        return array('err' => $err_arr, 'invoice' => $res['invoice_id'], 'status' => $status, 'pymnt_due_id' => $res['pymnt_due_id']);
                    }
                }
                // this code run on reschedule end 
                $data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $classes->course_id,
                    'class_id' => $class,
                    'user_id' => $user_id,
                    'enrolment_type' => $enrollment_type_text,
                    'enrolment_mode' => 'COMPSPON',
                    'pymnt_due_id' => $payment_due_id,
                    'company_id' => $company,
                    'enrolled_on' => $cur_date,
                    'enrolled_by' => $loggedin_user_id,
                    'tg_number' => $tg_number,
                    'training_score' => $training_score,
                    'payment_status' => $pay_status,
                    'sales_executive_id' => $salesexec,
                    'class_status' => $class_status,
                    'enrol_status' => $enrol_status
                );
                //$this->db->trans_start();
                $this->db->trans_begin();
                $this->db->insert('class_enrol', $data);
                //echo $this->db->last_query();

                if (!empty($payment_due_id)) {
                    $data = array(
                    'user_id' => $user_id,
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($classes->class_fees, 2), //sk3
                    'total_amount_due' => round($ind_net_due, 2), //sk4
                    'discount_type' => $dis_label,
                    'discount_rate' => $indv_discount_rate,
                    'subsidy_type_id' => $subsidy_type_id ?? 0,
                    'subsidy_amount' => round($subsidy_amount, 2), //sk5
                    'subsidy_recd_date' => $subsidy_recd_on,
                    'subsidy_modified_on' => $cur_date,
                    'gst_amount' => round($ind_gst, 2), //sk6
                    'att_status' => $att_status
                    );
                    $this->db->insert('enrol_pymnt_due', $data);
                    ///echo $this->db->last_query();
                }
            } else {
                $err_arr[] = $row['user_id'];
            }
            if ($check_attendance > 0) {
                if ($check_attendance_trainee > 0) {
                    $company_net_due = round(($company_net_due + $ind_net_due), 4);
                    $company_subsidy = round(( $company_subsidy + round($subsidy_amount, 4)), 4);
                    $company_gst = round(( $company_gst + $ind_gst), 2); //sk7
                    $company_total_unitfees = round(($company_total_unitfees + $classes->class_fees), 4);
                    $i++;
                }
            } else {
                $company_net_due = round(($company_net_due + $ind_net_due), 4);
                $company_subsidy = round(( $company_subsidy + round($subsidy_amount, 4)), 4);
                $company_gst = round(( $company_gst + $ind_gst), 2); //sk8
                $company_total_unitfees = round(($company_total_unitfees + $classes->class_fees), 4);
                $i++;
            }
        }
        $total_trainee_count = $i;

        $data = $this->db->select('class_start_datetime as start')
                        ->from('course_class')
                        ->where('class_id', $class)
                        ->where('tenant_id', $tenant_id)
                        ->get()->row(0);
        $start = $data->start;
        $this->db->last_query();
        $cur_date = date('Y-m-d H:i:s');
        if ($start) {
            $cur_date = $start;
        }
        if (!empty($payment_due_id)) {
            $gst_rule = (empty($courses->gst_on_off)) ? '' : $courses->subsidy_after_before;
//            if($check_attendance>0)
//            {
//                $data = array(
//                    'invoice_id' => $invoice_id,
//                    'pymnt_due_id' => $payment_due_id,
//                    'inv_date' => $cur_date,
//                    'inv_type' => 'INVCOMALL',
//                    'company_id' => $company,
//                    'total_inv_amount' => 0.0000,
//                    'total_unit_fees' => 0,
//                    'total_inv_discnt' => 0,
//                    'total_inv_subsdy' => 0,
//                    'total_gst' => 0,
//                    'gst_rate' => round($gst_rate, 4),
//                    'gst_rule' => $gst_rule,
//                );
//            }
//            else
//            {
            $data = array(
                'invoice_id' => $invoice_id,
                'pymnt_due_id' => $payment_due_id,
                'inv_date' => $cur_date,
                'inv_type' => 'INVCOMALL',
                'company_id' => $company,
                'total_inv_amount' => round($company_net_due, 2), //sk9
                'total_unit_fees' => $company_total_unitfees,
                'total_inv_discnt' => round($indv_discount_amt * $total_trainee_count, 4),
                'total_inv_subsdy' => $company_subsidy,
                'total_gst' => round($company_gst, 2), //sk10
                'gst_rate' => round($gst_rate, 2), //sk11
                'gst_rule' => $gst_rule,
            );
            // }
            $this->db->insert('enrol_invoice', $data);
            //echo $this->db->last_query();
        } else {
            $invoice_id = '';
        }
        //$this->db->trans_complete();
        //echo $this->db->trans_status().'d';exit;
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback(); ///added by shubhranshu
            $status = FALSE;
        } else {
            $this->db->trans_commit(); ///added by shubhranshu
        }
        return array('err' => $err_arr, 'invoice' => $invoice_id, 'status' => $status, 'pymnt_due_id' => $payment_due_id);
    }

    public function company_invoice_exists($course_id, $class_id, $company_id) {
        $this->db->select('*');
        $this->db->from('class_enrol');
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('company_id', $company_id);
        $sql = $this->db->get();
        //echo $this->db->last_query(); echo"<br/>";
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $result = array('pymnt_due_id' => $row->pymnt_due_id);
            }
            //echo $result['pymnt_due_id'];
            return $res = $this->get_company_invoice($result['pymnt_due_id']);
        }
    }

    public function get_company_invoice($pymnt_due_id) {
        $this->db->select('*');
        $this->db->from('enrol_invoice');
        $this->db->where('pymnt_due_id', $pymnt_due_id);
        $query = $this->db->get()->row(0);
        $array = array('invoice_id' => $query->invoice_id,
            'pymnt_due_id' => $pymnt_due_id);
        return $array;
    }

    /**

     * This method updates the DB with Individual enrolment details

     * @param type $tenant_id

     * @param type $loggedin_user_id

     * @param type $unit_fees

     * @return boolean|string

     */
    public function individual_enrollment_db_update($tenant_id, $loggedin_user_id, $unit_fees) {
        //$this->user = $this->user;
        //print_r($this->data);exit;

        $cur_time = date('H:i:s'); //sk
        $status = TRUE;

        extract($_POST);

        $check = $this->db->select('*')
                        ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course)
                        ->where('class_id', $class)->where('user_id', $user_id)->get();

        if ($check->num_rows() == 0) {

            $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

            $invoice_id = $this->generate_invoice_id();

            $result_array = $this->get_discnt($tenant_id, 0, $course, $class, $unit_fees, $user_id);

            $discount_type = $result_array['discount_label'];

            $discount['discount_rate'] = $disc_rate;

            $discount_amount = $disc_amt;



            if ($subsidy_recd_on) {

                $subsidy_recd_on = date('Y-m-d', strtotime($subsidy_recd_on));
            } else {

                $subsidy_recd_on = '';
            }

            $classes = $this->db->select('certi_coll_date,class_fees')->from('course_class')->where('class_id', $class)->get()->row();

            $courses = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $course)->get()->row();

            $discount_total = round($discount_amount, 4);

            $feesdue = $classes->class_fees - ($discount_total);



            $gst_rate = $this->get_gst_current();

            $netdue = $this->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

            $totalgst = $this->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

            if (($enrollment_type == 1) && ($payment_retake == 2)) {

                $pay_status = 'PYNOTREQD';

                $enrol_status = 'ENRLACT';

                $payment_due_id = 0;
            } else {
                $pay_status = ($mode_of_payment == '') ? 'NOTPAID' : 'PAID';
                $enrol_status = ($mode_of_payment == '') ? 'ENRLBKD' : 'ENRLACT';
            }
            $enrollment_type_text = ($enrollment_type == 1) ? 'RETAKE' : 'FIRST';
            $cur_date = date('Y-m-d H:i:s');
            $class_status = $this->get_class_statustext($class);

            /* sales executive thread */
            if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
                $salesexec = $this->user->user_id;
            } else {
                ///$salesexec = empty($salesexec) ? NULL : $salesexec;
                if (empty($salesexec)) {
                    $salesexec = $this->user->user_id;
                } else {
                    $salesexec = $salesexec;
                }
            }


            $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);

            if ($check_attendance > 0) {
                $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course, $class, $user_id);
                $this->db->last_query();

                if ($check_attendance_trainee > 0) {
                    $training_score = 'C';
                    $att_status = 1;
                } else {
                    $training_score = 'ABS';
                    $att_status = 0;
                }
            } else {
                $att_status = 1;
            }

            $data = array(
                'tenant_id' => $tenant_id,
                'course_id' => $course,
                'class_id' => $class,
                'user_id' => $user_id,
                'enrolment_type' => $enrollment_type_text,
                'enrolment_mode' => 'SELF',
                'pymnt_due_id' => $payment_due_id,
                'company_id' => '',
                'enrolled_on' => date('Y-m-d H:i:s'),
                'enrolled_by' => $loggedin_user_id,
                'tg_number' => $tg_number,
                'training_score' => $training_score,
                'payment_status' => $pay_status,
                'sales_executive_id' => $salesexec,
                'class_status' => $class_status,
                'enrol_status' => $enrol_status
            );
            $this->db->trans_start();
            $this->db->insert('class_enrol', $data);

            if (!empty($payment_due_id)) {
                $data = array(
                    'user_id' => $user_id,
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($classes->class_fees, 4),
                    'total_amount_due' => round($netdue, 4),
                    'discount_type' => $discount_type,
                    'discount_rate' => round($discount['discount_rate'], 4),
                    'subsidy_amount' => round($subsidy_amount, 4),
                    'subsidy_type_id' => $subsidy_type,
                    'subsidy_recd_date' => $subsidy_recd_on,
                    'gst_amount' => round($totalgst, 4),
                    'att_status' => $att_status
                );
                log_message('debug', " Inserted into class_enrol");
                $this->db->insert('enrol_pymnt_due', $data);

                $gst_rule = (empty($courses->gst_on_off)) ? '' : $courses->subsidy_after_before;

                $data = $this->db->select('class_start_datetime as start')
                                ->from('course_class')
                                ->where('class_id', $class)
                                ->where('tenant_id', $tenant_id)
                                ->get()->row(0);
                $start = $data->start;
                $this->db->last_query();
                $cur_date = date('Y-m-d H:i:s');
                if ($start) {
                    $cur_date = $start;
                }
                $data = array(
                    'invoice_id' => $invoice_id,
                    'pymnt_due_id' => $payment_due_id,
                    'inv_date' => $cur_date,
                    'inv_type' => 'INVINDV',
                    'total_inv_amount' => round($netdue, 4),
                    'total_unit_fees' => round($classes->class_fees, 4),
                    'total_inv_discnt' => round($discount_total, 4),
                    'total_inv_subsdy' => round($subsidy_amount, 4),
                    'total_gst' => round($totalgst, 4),
                    'gst_rate' => round($gst_rate, 4),
                    'gst_rule' => $gst_rule,
                );

                log_message('debug', " Inserted into enrol_pymnt_due");
                $this->db->insert('enrol_invoice', $data);
                if (!empty($mode_of_payment)) {
                    if ($mode_of_payment == 'CASH' || $mode_of_payment == 'NETS') {
                        $data = array(
                            'invoice_id' => $invoice_id,
                            'recd_on' => date('Y-m-d ', strtotime($recd_on)) . $cur_time,
                            'mode_of_pymnt' => $mode_of_payment,
                            'amount_recd' => round($amount_rcd, 4),
                            'cheque_number' => NULL,
                            'cheque_date' => NULL,
                            'bank_name' => NULL,
                            'recd_by' => $loggedin_user_id,
                        );
                    } elseif ($mode_of_payment == 'CHQ') {
                        $data = array(
                            'invoice_id' => $invoice_id,
                            'recd_on' => date('Y-m-d ', strtotime($recd_on)) . $cur_time,
                            'mode_of_pymnt' => $mode_of_payment,
                            'amount_recd' => round($amount_rcd, 4),
                            'cheque_number' => $chq_num,
                            'cheque_date' => date('Y-m-d', strtotime($chq_date)),
                            'bank_name' => $bank_name,
                            'recd_by' => $loggedin_user_id,
                        );
                    }
                    $this->db->insert('enrol_paymnt_recd', $data);
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $user_id,
                        'amount_recd' => round($amount_rcd, 4),
                        'recd_on' => date('Y-m-d ', strtotime($recd_on)) . $cur_time
                    );
                    $this->db->insert('enrol_pymnt_brkup_dt', $data);
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $status = FALSE;
            }
        } else {
            $status = FALSE;
        }
        return $status;
    }

    /**

     * create to get pay due and invoice

     */
    public function get_paydue_invoice($trainee_id, $class_id) {

        $result = $this->db->select('*')->from('class_enrol ce')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->where('ce.class_id', $class_id)->where('ce.user_id', $trainee_id)->get()->row();

        return $result;
    }

    /**

     * check enrol status

     */
    public function check_user_enrol_status($user_id, $course, $class, $tenant_id) {

        $result = $this->db->select('*')
                        ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course)
                        ->where('class_id', $class)->where('user_id', $user_id)->get();

        return $result->num_rows();
    }

    /// added by shubhranshu to fetch the company discount 
    public function fetch_compnay_discount($tenant_id, $course, $company_id) {
        $this->db->select('*');
        $this->db->from('company_discount');
        $this->db->where('Tenant_ID', $tenant_id);
        $this->db->where('Company_ID', $company_id);
        $this->db->where('Course_ID', $course);
        $res = $this->db->get()->row();
        return $res;
    }

    /**

     * Insert data into class_enrol,

     */
    public function create_bulk_enrol($tenant_id, $insert_data, $company_id, $course, $salesexec, $class, $class_detail, $curuser_id, $company_details) {

        $cur_date = date('Y-m-d H:i:s');

        $company_net_due = 0;

        $company_discount = 0;

        $company_subsidy = 0;

        $company_gst = 0;

        $company_total_unitfees = 0;


        //// Below code added by shubhranshu to fetch the company discount 


        $comp_discounts_details = $this->fetch_compnay_discount($tenant_id, $course, $company_id);
        if (($comp_discounts_details->Discount_Percent > 0) || ($comp_discounts_details->Discount_Amount > 0)) {

            if ($comp_discounts_details->Discount_Percent > 0) {
                $discount_rate = round($comp_discounts_details->Discount_Percent, 4);
                $discount_total = ( $discount_rate * $class_detail->class_fees) / 100;
            } else {
                $discount_total = $comp_discounts_details->Discount_Amount;
                $discount_rate = round((($discount_total / $class_detail->class_fees) * 100), 4);
            }
            $discount_label = 'DISCOMP';
        } else if ($class_detail->class_discount > 0) {

            $discount_rate = round($class_detail->class_discount, 4);
            $discount_total = ( $discount_rate * $class_detail->class_fees) / 100;
            $discount_label = 'DISCLASS';
        } else {
            $discount_rate = 0;
            $discount_total = 0;
            $discount_label = 'DISCOMP';
        }
        //////end of code by ssp


        $course_detail = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $course)->get()->row();

        $feesdue = $class_detail->class_fees - ($discount_total);

        $gst_rate = $this->get_gst_current();

        ///most important added by shubhranshu
        $check = $this->db->select('*')
                        ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course)
                        ->where('class_id', $class)->where('company_id', $company_id)->get();
        if ($check->num_rows() == 0) {
            $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

            $invoice_id = $this->generate_invoice_id();
        } else {
            $inv_detls = $this->fetch_enrol_invoice_check($tenant_id, $course, $class, $company_id);
            $payment_due_id = $inv_detls->pymnt_due_id;
            $invoice_id = $this->generate_invoice_id();
            $check_regenerate_comp_discount = $this->db->select('*')
                            ->from('enrol_pymnt_due')->where('pymnt_due_id', $inv_detls->pymnt_due_id)->get()->result();
            $regenet_comp_dis_rate = 0;
            foreach ($check_regenerate_comp_discount as $arrays) {
                if ($arrays->discount_rate > $regenet_comp_dis_rate) {
                    $regenet_comp_dis_rate = $arrays->discount_rate;
                }
            }
            if ((!empty($regenet_comp_dis_rate)) && ($regenet_comp_dis_rate != 0)) {
                $discount_total = ($regenet_comp_dis_rate * $class_detail->class_fees) / 100;
                $discount_rate = round($regenet_comp_dis_rate, 4);
                $discount_label = 'DISCOMP';
                $feesdue = $class_detail->class_fees - ($discount_total);
            }
        }



        foreach ($insert_data as $key => $excel) {
            ////////////////////////added by shubhranshu to prevent negative invoice due to subsidy on 4/1/2019////////////
            $k = 4;
            $subsidy_amount = $excel['subsidy_amount'];
            $netdue = $this->calculate_net_due($course_detail->gst_on_off, $course_detail->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

            if ($netdue <= 0) {
                $insert_data[$k]['status'] = 'FAILED';
                $excel['status'] = 'FAILED';
                $insert_data[$k]['failure_reason'] = 'Subsidy amount can not be nagative';
            }
            $k++;
            ////////////////////////added by shubhranshu to prevent negative invoice due to subsidy////////////
            if ($excel['status'] == 'PASSED') {

                //$subsidy_amount = $excel['subsidy_amount'];

                $subsidy_recd_on = $excel['subsidy_recd_on'];

                if ($subsidy_recd_on) {

                    $subsidy_recd_on = date('Y-m-d', strtotime($subsidy_recd_on));
                } else {

                    $subsidy_recd_on = '';
                }

                //$netdue = $this->calculate_net_due($course_detail->gst_on_off, $course_detail->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

                $class_status = $this->get_class_statustext($class);

                $totalgst = $this->calculate_gst($course_detail->gst_on_off, $course_detail->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

                if (($excel['enrollment_type'] == 'RETAKE') && ($excel['enrol_retake_pay_mode'] == 'BYPASS')) {

                    $pay_status = 'PYNOTREQD';

                    $enrol_status = 'ENRLACT';

                    $payment_due_id = '';
                } else {

                    $pay_status = 'NOTPAID';

                    $enrol_status = 'ENRLBKD';
                }

                $tg_number = $excel['tg_number'];

//                    if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'ADMN' || $this->user->role_id == 'CRSEMGR') {
//
//                        $salesexec = $this->user->user_id;
//                    } else {
//
//                        $salesexec = empty($salesexec) ? NULL : $salesexec;
//                    }

                if (empty($salesexec)) {
                    if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'ADMN' || $this->user->role_id == 'CRSEMGR') {
                        $salesexec = $this->user->user_id;
                    } else {
                        $salesexec = NULL;
                    }
                }

                /////////below block was added by shubhranshu for training score to be update for bulk enrol/////
                $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);
                //echo $check_attendance.' -'.$tenant_id.'-'.$course.'-'.$class;exit;
                if ($check_attendance > 0) {
                    $training_score = 'ABS';
                    $att_status = 0;
                } else {
                    $att_status = 1;
                    $training_score = 'C';
                }
                /////////////////////////////end of code by shubhranshu////////////////////////////////



                $data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $course,
                    'class_id' => $class,
                    'user_id' => $excel['user_id'],
                    'enrolment_type' => $excel['enrollment_type'],
                    'enrolment_mode' => 'COMPSPON',
                    'pymnt_due_id' => $payment_due_id,
                    'company_id' => $company_id,
                    'enrolled_on' => $cur_date,
                    'enrolled_by' => $curuser_id,
                    'tg_number' => $tg_number,
                    'training_score' => $training_score, ////added by shubhranshu to by default the score should be present.
                    'payment_status' => $pay_status,
                    'sales_executive_id' => $salesexec,
                    'class_status' => $class_status,
                    'enrol_status' => $enrol_status
                );

                $this->db->insert('class_enrol', $data);

                ////added by shubhranshu if the invoice id re-generated  to fetch the discount
                $fees_array = $this->fees_payable_check_discount($excel['user_id'], $tenant_id, $course, $class, 0, $company_id, $payment_due_id, $this->user->user_id);

                $data = array(
                    'user_id' => $user_id,
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($fees_array["unit_fees"], 4), //sk1
                    'total_amount_due' => round($fees_array["net_fees_due"], 2), //sk2
                    'discount_type' => $fees_array["discount_type"],
                    'discount_rate' => round($fees_array["discount_rate"], 4),
                    'gst_amount' => round($fees_array["gst_amount"], 2), //sk3
                    'subsidy_amount' => 0,
                    'subsidy_recd_date' => '0000-00-00',
                    'att_status' => $att_status
                );
                /////////////////////need to work on this//////////////////



                if ($pay_status != 'PYNOTREQD') {

                    $data = array(
                        'user_id' => $excel['user_id'],
                        'pymnt_due_id' => $payment_due_id,
                        'class_fees' => round($class_detail->class_fees, 4),
                        'total_amount_due' => round($netdue, 4),
                        'discount_type' => $discount_label,
                        'discount_rate' => round($discount_rate, 4),
                        'subsidy_amount' => round($subsidy_amount, 4),
                        'subsidy_recd_date' => $subsidy_recd_on,
                        'subsidy_modified_on' => $cur_date,
                        'gst_amount' => round($totalgst, 4),
                        'att_status' => $att_status ///added by shubhranshu 
                    );

                    $this->db->insert('enrol_pymnt_due', $data);

                    $company_net_due = $company_net_due + round($netdue, 4);

                    $company_discount = $company_discount + round($discount_total, 4);

                    $company_subsidy = $company_subsidy + round($subsidy_amount, 4);

                    $company_gst = $company_gst + round($totalgst, 4);

                    $company_total_unitfees = $company_total_unitfees + $class_detail->class_fees;
                }
            }
        }

        /////////////////////addded by shubhranshu for company invoice which is exist/////////////


        if ($check->num_rows() == 0) {

            if ($company_net_due > 0) {

                $gst_rule = (empty($course_detail->gst_on_off)) ? '' : $course_detail->subsidy_after_before;

                $data = array(
                    'invoice_id' => $invoice_id,
                    'pymnt_due_id' => $payment_due_id,
                    'inv_date' => $cur_date,
                    'inv_type' => 'INVCOMALL',
                    'company_id' => $company_id,
                    'total_inv_amount' => round($company_net_due, 4),
                    'total_unit_fees' => round($company_total_unitfees, 4),
                    'total_inv_discnt' => round($company_discount, 4),
                    'total_inv_subsdy' => round($company_subsidy, 4),
                    'total_gst' => round($company_gst, 4),
                    'gst_rate' => round($gst_rate, 4),
                    'gst_rule' => $gst_rule,
                );
                //print_r($data);exit;
                $this->db->insert('enrol_invoice', $data);

                $insert_data['invoice_id'] = $invoice_id;
            }
        } else {

            if (!empty($inv_detls->pymnt_due_id)) {



                $gst_rule = (empty($course_detail->gst_on_off)) ? '' : $course_detail->subsidy_after_before;

                $company_net_due = $company_net_due + $inv_detls->total_inv_amount;

                $company_discount = $company_discount + $inv_detls->total_inv_discnt;

                $company_subsidy = $company_subsidy + $inv_detls->total_inv_subsdy;

                //$totalgst = $totalgst+$inv_detls->total_gst;

                $company_gst = $company_gst + $inv_detls->total_gst;

                $company_total_unitfees = $company_total_unitfees + $inv_detls->total_unit_fees;

                $data = array(
                    'invoice_id' => $invoice_id,
                    'pymnt_due_id' => $inv_detls->pymnt_due_id,
                    'inv_date' => $cur_date,
                    'inv_type' => 'INVCOMALL',
                    'company_id' => $company_id,
                    'total_inv_amount' => round($company_net_due, 4),
                    'total_unit_fees' => round($company_total_unitfees, 4),
                    'total_inv_discnt' => round(($company_discount), 4),
                    'total_inv_subsdy' => round(($company_subsidy), 4),
                    'total_gst' => round($company_gst, 4),
                    'gst_rate' => round($gst_rate, 4),
                    'gst_rule' => $gst_rule,
                );

                $this->db->where('pymnt_due_id', $inv_detls->pymnt_due_id);

                $this->db->update('enrol_invoice', $data);

                $insert_data['invoice_id'] = $invoice_id;
            }
        }


        return $insert_data;
    }

    public function fetch_enrol_invoice_check($tenant_id, $course_id, $class_id, $comp_id) {

        $result = $this->db->select('ei.*')->from('enrol_invoice ei')
                        ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id')
                        ->where('ce.course_id', $course_id)
                        ->where('ce.class_id', $class_id)
                        ->where('ce.tenant_id', $tenant_id)
                        ->where('ce.company_id', $comp_id)
                        ->get()->row();

        return $result;
    }

    ///////////addded by shubhranshu for company invoice if exist

    /**

     * get id for taxcode

     */
    public function get_id_for_taxcode($taxcode) {

        return $this->db->select('tu.user_id')->from('tms_users tu')->where('tu.tax_code', $taxcode)->where('tu.account_type', 'TRAINE')->get()->row()->user_id;
    }

    public function get_id_for_taxcode_inactive($taxcode, $company_id) {

        return $this->db->select('tu.user_id')
                        ->from('tms_users tu')
                        ->join('tenant_company_users tcu', 'tcu.user_id=tu.user_id')
                        ->where('tcu.company_id', $company_id)
                        ->where('tu.tax_code', $taxcode)
                        ->where('tu.account_type', 'TRAINE')->get()->row()->user_id;
    }

    public function get_id_for_taxcode_company($taxcode, $company_id) {

        return $this->db->select('tu.user_id')
                        ->from('tms_users tu')
                        ->join('tenant_company_users tcu', 'tcu.user_id=tu.user_id')
                        ->where('tcu.user_acct_status', 'ACTIVE')
                        ->where('tu.account_status', 'ACTIVE')
                        ->where('tcu.company_id', $company_id)
                        ->where('tu.tenant_id', $this->tenant_id)
                        ->where('tu.tax_code', $taxcode)->where('tu.account_type', 'TRAINE')->get()->row()->user_id;
    }

    /*

     * This method using in bulk enrollment for internal staff.

     */

    public function get_id_for_taxcode_tenant($taxcode, $company_id) {

        return $this->db->select('tu.user_id')
                        ->from('tms_users tu')
                        ->where('tu.account_status', 'ACTIVE')
                        ->where('tu.tenant_id', $company_id)
                        ->where('tu.tax_code', $taxcode)->where('tu.account_type', 'INTUSR')->get()->row()->user_id;
    }

    public function get_id_for_salestaxcode($taxcode) {

        $res = $this->db->select('tu.user_id')->from('tms_users tu')->where('tu.tax_code', $taxcode)->get()->row()->user_id;

        return $res;
    }

    /**

     * calculate Net Due 

     */
    public function calculate_net_due_subsidy_percentage($gst_onoff, $subsidy_after_before, $feesdue, $subsidy_per, $gst_rate) {

        $subsidy = ($subsidy_per * $feesdue) / 100;

        if ($gst_onoff == 1) {

            if ($subsidy_after_before == 'GSTBSD') {

                return (($feesdue + (($feesdue * $gst_rate) / 100)) - $subsidy);
            } else {

                $feesduetemp = ($feesdue - $subsidy);

                return ( $feesduetemp + (($feesduetemp * $gst_rate) / 100));
            }
        } else {

            return ($feesdue - $subsidy);
        }
    }

    /**

     * calculate Net Due

     */
    public function calculate_after_before_gst($gst_onoff, $subsidy_after_before, $feesdue, $subsidy, $gst_rate) {
//
        if ($gst_onoff == 1) {

            if ($subsidy_after_before == 'GSTBSD') {

                //return ($feesdue + (($feesdue * $gst_rate) / 100));
                $feesduetemp = ($feesdue * $gst_rate) / 100;
                $feesduetemp = round($feesduetemp, 2, PHP_ROUND_HALF_UP);
                return $feesduetemp + $feesdue;
                //return round($feesduetemp, 2, PHP_ROUND_HALF_UP);/////ssp/////
            } else {
                // added by shubhranshu to fixed calculation issue while round off upto 2decimal places on 5/12/2018/////
                $feesduetemp = ($feesdue - $subsidy);
                // return $feesduetemp = ($feesdue - $subsidy);
                return round($feesduetemp, 2, PHP_ROUND_HALF_UP); ///////////////////ssp/////////////
            }
        } else {
            $feesduetemp = ($feesdue - $subsidy);
            //return ($feesdue - $subsidy);
            return round($feesduetemp, 2, PHP_ROUND_HALF_UP);
        }
    }

    /**

     * Get GST Amount

     */
    public function calculate_gst($gst_onoff, $subsidy_after_before, $feesdue, $subsidy, $gst_rate) {

        if ($gst_onoff == 1) {

            if ($subsidy_after_before == 'GSTBSD') {

                return round($feesdue * ($gst_rate ) / 100, 2);
            } else {

                $feesduetemp = ($feesdue - $subsidy);

                return round($feesduetemp * ($gst_rate) / 100, 2);
            }
        }
    }

    /**

     * TENANT MASTER DETAILS

     */
    public function get_tenant_masters($tenant_id) {

        $result = $this->db->select('*')->from('tenant_master')->where('tenant_id', $tenant_id)->get()->row();

        return $result;
    }

    /**

     * get enrol_invoice ##enrollment

     */
    public function get_enroll_invoice($payid) {

        $result = $this->db->select('*')->from('enrol_invoice ei')
                        ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id')
                        ->join('course_class cc', 'cc.class_id=ce.class_id')
                        ->join('course c', 'c.course_id=cc.course_id')
                        ->join('tms_users tu', 'tu.user_id=ce.user_id')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id')
                        ->where('ei.pymnt_due_id', $payid)
                        ->where('epd.att_status', 1)
                        ->get()->row();

        return $result;
    }

    /* This function get the invoice details of company skm */

    public function get_company_enroll_invoice($payid) {

        $result = $this->db->select('*')->from('enrol_invoice ei')
                        ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id')
                        ->join('course_class cc', 'cc.class_id=ce.class_id')
                        ->join('course c', 'c.course_id=cc.course_id')
                        ->join('tms_users tu', 'tu.user_id=ce.user_id')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id')
                        ->where('ei.pymnt_due_id', $payid)
                        ->get()->row();

        return $result;
    }

    /* skm */

    public function get_enroll_individual_invoice($payid) {

        $result = $this->db->select('*')->from('enrol_invoice ei')
                        ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id')
                        ->join('course_class cc', 'cc.class_id=ce.class_id')
                        ->join('course c', 'c.course_id=cc.course_id')
                        ->join('tms_users tu', 'tu.user_id=ce.user_id')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id')
                        ->where('ei.pymnt_due_id', $payid)
                        ->get()->row();

        return $result;
    }

    public function get_discount($pymnt_due_id) {
        $this->db->select('discount_type,discount_rate');
        $this->db->from('enrol_pymnt_due');
        $this->db->where('pymnt_due_id', $pymnt_due_id);
        $this->db->where('att_status', 1);
        $sql = $this->db->get();
        return $sql->row();
    }

    /**

     * get previous invoice number ##enrollment

     */
    public function get_enroll_prev_invoice($invoice_id) {
        $query = 'select invoice_id as previous_inv_id from enrol_invoice_audittrail where regen_inv_id="' . $invoice_id . '"';
        $query = $this->db->query($query);
        return $query->result();
    }

    public function get_enroll_prev_indvoice($payid) {

        $data = $this->db->select('invoice_id')
                        ->from('enrol_invoice')
                        ->where('pymnt_due_id', $payid)
                        ->get()->row(0);

        $invoice_id = $data->invoice_id;
        $query = 'select invoice_id as previous_inv_id from enrol_invoice_audittrail where regen_inv_id="' . $invoice_id . '"';

        $query = $this->db->query($query);
//           sssecho $this->db->last_query();
//            exit();
        return $query->result();
    }

    /**

     * function to get invoice for class and trainee id

     */
    public function get_invoice_for_class_trainee($class_id, $user_id) {

        $data = $this->db->select('ei.invoice_id, ei.pymnt_due_id')->from('class_enrol ce')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->where('ce.class_id', $class_id)
                        ->where('ce.user_id', $user_id)->order_by('ei.invoice_id', 'DESC')->get()->row();

        $invoice_id = $data->invoice_id;

        if (empty($invoice_id)) {

            return;
        }

        $result = $this->get_invoice_paid_detail($invoice_id);

        $result->cheque_date = ($result->cheque_date) ? date('d-m-Y', strtotime($result->cheque_date)) : '';

        $result->pymnt_due_id = $data->pymnt_due_id;

        return $result;
    }

    /**

     * function to get all trainee details in a company by pymnt_due_id

     */
    public function get_company_trainees_by_payid($payid) {

        $this->db->select('tu.tax_code, tup.user_id, tup.first_name as first, tup.last_name as last, cc.class_name,

                epd.total_amount_due, epd.subsidy_amount, epd.subsidy_type_id, epd.subsidy_recd_date,  ce.payment_status')
                ->from('enrol_pymnt_due epd')
                ->join('class_enrol ce', 'ce.pymnt_due_id=epd.pymnt_due_id and ce.user_id=epd.user_id')
                ->join('course_class cc', 'cc.class_id=ce.class_id and cc.course_id=ce.course_id and cc.tenant_id=ce.tenant_id')
                ->join('tms_users_pers tup', 'tup.user_id=epd.user_id')
                ->join('tms_users tu', 'tup.user_id=tu.user_id')
                ->where('epd.pymnt_due_id', $payid)
                ->where('epd.att_status', 1)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $result = $this->db->get()->result();

        return $result;
    }

    public function get_company_trainees_by_payid1($payid) {

        $this->db->select('tu.tax_code, tup.user_id, tup.first_name as first, tup.last_name as last, cc.class_name,

                epd.total_amount_due, epd.subsidy_amount, epd.subsidy_type_id, epd.subsidy_recd_date,  ce.payment_status')
                ->from('enrol_pymnt_due epd')
                ->join('class_enrol ce', 'ce.pymnt_due_id=epd.pymnt_due_id and ce.user_id=epd.user_id')
                ->join('course_class cc', 'cc.class_id=ce.class_id and cc.course_id=ce.course_id and cc.tenant_id=ce.tenant_id')
                ->join('tms_users_pers tup', 'tup.user_id=epd.user_id')
                ->join('tms_users tu', 'tup.user_id=tu.user_id')
                ->where('epd.pymnt_due_id', $payid)
                ->where('epd.att_status', 1)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $result = $this->db->get()->result();

        return $result;
    }

    /**

     * function to get company payment received for individual users

     */
    public function company_payment_recd($invoice) {

        $this->db->select('invoice_id');

        $this->db->select('user_id');

        $this->db->select_sum('brk.amount_recd');

        $this->db->from('enrol_pymnt_brkup_dt brk');

        $this->db->where('invoice_id', $invoice);

        $this->db->group_by('user_id');

        return $this->db->get()->result();
    }

    /**

     * function to get company payment received for individual forgeiner users

     */
    public function company_payment_recd_forgeiner($invoice) {

        $this->db->select('brk.invoice_id');

        $this->db->select('brk.user_id');

        $this->db->select_sum('brk.amount_recd');

        $this->db->from('enrol_pymnt_brkup_dt brk');
        $this->db->join('tms_users tu', 'tu.user_id=brk.user_id');

//        $this->db->not_like('tu.tax_code','S','after');
////      $this->db->or_not_like('tu.tax_code','T','after');

        $this->db->where('invoice_id', $invoice);


        $this->db->group_by('user_id');

        return $this->db->get()->result();
    }

    /**

     * function to get company payment refunded for individual users

     */
    public function company_payment_refund($invoice) {

        $this->db->select('invoice_id');

        $this->db->select('user_id');

        $this->db->select_sum('brk.refund_amount');

        $this->db->from('enrol_refund_brkup_dt brk');

        $this->db->where('invoice_id', $invoice);

        $this->db->group_by('user_id');

        return $this->db->get()->result();
    }

    /**

     * function to get company payment refunded for individual foreigner users

     */
    public function company_payment_refund_foreigner($invoice) {
        $query = $this->db->query("SELECT epd.user_id,tu.tax_code FROM (enrol_pymnt_due epd) 
                                JOIN enrol_invoice ei ON ei.pymnt_due_id=epd.pymnt_due_id JOIN tms_users tu ON tu.user_id=epd.user_id WHERE ei.invoice_id = '$invoice_id' 
                                AND (tu.tax_code LIKE 'S%' AND tu.tax_code LIKE 'T%')");
        $users = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $users[] = $row->user_id;
            }
        }
        $this->db->select('brk.invoice_id');
        $this->db->select('brk.user_id');
        $this->db->select_sum('brk.refund_amount');
        $this->db->from('enrol_refund_brkup_dt brk');
        $this->db->join('tms_users tu', 'tu.user_id=brk.user_id');
        $this->db->where('invoice_id', $invoice);
        $this->db->group_by('user_id');
        return $this->db->get()->result();
    }

    /**

     * This function gets all the active company accounts for a Tenant

     */
    public function get_active_tenant_company($tenant_id) {

        $this->db->select('comp.company_name');

        $this->db->select('comp.company_id');

        $this->db->from('tenant_company tent');

        $this->db->join('company_master comp', 'tent.company_id  = comp.company_id', 'left');

        $this->db->where('tent.tenant_id', $tenant_id);

        $this->db->where('tent.comp_status', 'ACTIVE');

        $this->db->distinct(TRUE);

        $this->db->order_by('comp.company_name');



        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('comp.company_id', $this->user->company_id);
        }



        return $this->db->get()->result();
    }

    /**

     * function to get the company for all invoice

     */
    public function get_company_for_allinvoice($tenant_id) {

        $this->db->select('comp.company_name');

        $this->db->select('comp.company_id');

        $this->db->from('enrol_invoice enrol');

        $this->db->join('tenant_company tent', 'enrol.company_id = tent.company_id', 'left');

        $this->db->join('company_master comp', 'tent.company_id  = comp.company_id', 'left');

        $this->db->where('tent.tenant_id', $tenant_id);

        $this->db->distinct(TRUE);

        $this->db->order_by('comp.company_name');



        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('comp.company_id', $this->user->company_id);
        }



        return $this->db->get()->result();
    }

    /**

     * function to get the company for paid invoice

     */
    public function get_company_for_paidinvoice($tenant_id) {

        $this->db->select('comp.company_name');

        $this->db->select('comp.company_id');

        $this->db->from('enrol_invoice enrol');

        $this->db->join('enrol_paymnt_recd pymnt', 'enrol.invoice_id = pymnt.invoice_id');

        $this->db->join('tenant_company tent', 'enrol.company_id = tent.company_id');

        $this->db->join('company_master comp', 'tent.company_id  = comp.company_id');

        $this->db->where('tent.tenant_id', $tenant_id);

        $this->db->distinct(TRUE);

        $this->db->order_by('comp.company_name');

        return $this->db->get()->result();
    }

    /**

     * function to update invoiced on

     */
    public function update_invoiced_on($invoice_id, $invoice_date) {

        $inv_date = ($invoice_date) ? date('Y-m-d H:i:s', strtotime($invoice_date)) : '0000-00-00 00:00:00';

        $data = array('invoiced_on' => $inv_date);

        $this->db->where('invoice_id', $invoice_id);



        $this->db->update('enrol_invoice', $data);



        return TRUE;
    }

    /**

     * function to get company invoice changes need here

     */
    public function company_invoice($invoice) {

        $this->db->select('ei.pymnt_due_id, c.crse_name, cc.class_name, cc.class_id, ei.invoice_id, ei.inv_date, 

                ei.total_inv_amount, ei.total_unit_fees, ei.pymnt_due_id, ei.invoiced_on, ei.invoice_excess_amt, 

                ei.total_inv_discnt, ei.total_inv_subsdy, ei.total_gst, ei.gst_rate, epd.discount_type, 

                epd.discount_rate, epd.subsidy_amount, ei.gst_rule, ce.company_id')
                ->from('enrol_invoice ei')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id')
                ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id')
                ->join('course_class cc', 'cc.class_id=ce.class_id')
                ->join('course c', 'c.course_id=ce.course_id')
                ->where('ei.invoice_id', $invoice);



        $this->db->group_by('ce.class_id');

        $result = $this->db->get()->row();

        $this->db->select_sum('er.amount_refund', 'amount_refund');

        $this->db->from('enrol_refund er')->where('er.invoice_id', $invoice);

        $this->db->group_by('er.invoice_id');

        $amount_refund = $this->db->get()->row()->amount_refund;

        $result->amount_refund = empty($amount_refund) ? 0 : round($amount_refund, 2);

        $result->amount_recd = $this->check_payment_recived($invoice);

        return $result;
    }

    public function check_payment_recived($invoice) {
        $this->db->select_sum('epr.amount_recd', 'amount_recd');
        $this->db->from('enrol_paymnt_recd epr');
        $this->db->where('epr.invoice_id', $invoice);
        $this->db->group_by('epr.invoice_id');
        $sql = $this->db->get();
        if ($sql->num_rows() > 0) {
            $amount_recd = $sql->row()->amount_recd;
            return $result->$amount_recd = empty($amount_recd) ? 0 : round($amount_recd, 2);
        } else {
            return $result = $sql->num_rows();
        }
    }

    /**

     *  function to get recent recd

     */
    public function get_invoice_paid_detail($invoice_id) {

        $result = $this->db->select('epr.invoice_id, epr.recd_on, epr.mode_of_pymnt,epr.amount_recd, epr.cheque_number, epr.cheque_date,epr.othr_mode_of_payment')
                        ->from('enrol_paymnt_recd epr')
                        ->where('epr.invoice_id', $invoice_id)
                        ->order_by('epr.trigger_date', 'DESC')->get()->row(); //added by shubhranshu

        return $result;
    }

    public function get_invoice_paid_details_indv($invoice_id, $user_id = 0) {
        $mop = array('SFC_ATO', 'SFC_SELF', 'CASH', 'NETS', 'CHQ', 'GIRO', 'ONLINE');
        $this->db->select('epbd.invoice_id, epbd.recd_on, epbd.amount_recd, 
                epr.mode_of_pymnt,epr.othr_mode_of_payment, epr.cheque_number,epr.sfc_claimed,epr.other_amount_recd,epr.cheque_date,
                tup.first_name, tup.last_name, tup.gender');
        $this->db->from('enrol_pymnt_brkup_dt epbd');
        $this->db->join('enrol_paymnt_recd epr', 'epr.invoice_id=epbd.invoice_id and epr.recd_on=epbd.recd_on', 'left');
        $this->db->join('tms_users_pers tup', 'tup.user_id = epbd.user_id', 'left');
        $this->db->where('epbd.invoice_id', $invoice_id);
        $this->db->where_in('epr.mode_of_pymnt', $mop);
        $this->db->order_by('epbd.recd_on', 'DESC');
        $this->db->limit(1);
        if (!empty($user_id)) {
            $this->db->where('epbd.user_id', $user_id);
        }
        $result = $this->db->get()->result_object();
        return $result;
    }

    //// below function was added by shubhranshu to fix the sfc issue while giving the backdate
    public function get_invoice_paid_details_indv_new($invoice_id, $user_id = 0) {
        $mop = array('SFC_ATO', 'SFC_SELF', 'CASH', 'NETS', 'CHQ', 'GIRO', 'ONLINE', 'PSEA');
        $this->db->select('epbd.invoice_id, epbd.recd_on, epbd.amount_recd, 
                epr.mode_of_pymnt,epr.othr_mode_of_payment, epr.cheque_number,epr.sfc_claimed,epr.other_amount_recd,epr.cheque_date,
                tup.first_name, tup.last_name, tup.gender');
        $this->db->from('enrol_pymnt_brkup_dt epbd');
        $this->db->join('enrol_paymnt_recd epr', 'epr.invoice_id=epbd.invoice_id and epr.recd_on=epbd.recd_on', 'left');
        $this->db->join('tms_users_pers tup', 'tup.user_id = epbd.user_id', 'left');
        $this->db->where('epbd.invoice_id', $invoice_id);
        $this->db->where_in('epr.mode_of_pymnt', $mop);
        $this->db->order_by('epbd.trigger_date', 'DESC');
        $this->db->limit(1);
        if (!empty($user_id)) {
            $this->db->where('epbd.user_id', $user_id);
        }
        $result = $this->db->get()->result_object();
        return $result;
    }

    /*

     * function to get trainee paid details

     */

    public function get_invoice_paid_details($invoice_id, $user_id = 0) {

        $mode_of_pymnt = $this->db->select('mode_of_pymnt')->from('enrol_paymnt_recd')->where('invoice_id', $invoice_id)
                        ->order_by('recd_on', 'DESC')
                        ->get()->row()->mode_of_pymnt;
        $mode_of_pymnt;

        if ($mode_of_pymnt == "SFC_ATO" || $mode_of_pymnt == "SFC_SELF") {
            $order = 'ASC';
            //$mop=array('SFC_ATO','SFC_SELF');
            $mop = array('SFC_ATO', 'SFC_SELF', 'CASH', 'NETS', 'CHQ', 'GIRO', 'ONLINE', 'PSEA');
        } else {
            $order = 'DESC';
            $mop = array('SFC_ATO', 'SFC_SELF', 'CASH', 'NETS', 'CHQ', 'GIRO', 'ONLINE', 'PSEA');
        }
        $this->db->select('epbd.invoice_id, epbd.recd_on, epbd.amount_recd, 

                epr.mode_of_pymnt,epr.othr_mode_of_payment, epr.cheque_number,epr.sfc_claimed,epr.other_amount_recd,epr.cheque_date,

                tup.first_name, tup.last_name, tup.gender');

        $this->db->from('enrol_pymnt_brkup_dt epbd');

        $this->db->join('enrol_paymnt_recd epr', 'epr.invoice_id=epbd.invoice_id and epr.recd_on=epbd.recd_on', 'left');

        $this->db->join('tms_users_pers tup', 'tup.user_id = epbd.user_id', 'left');

        $this->db->where('epbd.invoice_id', $invoice_id);
        $this->db->where_in('epr.mode_of_pymnt', $mop);

        $this->db->order_by('epbd.recd_on', $order);

        if (!empty($user_id)) {

            $this->db->where('epbd.user_id', $user_id);
        }

        $result = $this->db->get()->result_object();
        //echo  $this->db->last_query();exit;
        return $result;
    }

    ///// added by shubhranshu if rec on date given backdate issue
    public function get_invoice_paid_details_new($invoice_id, $user_id = 0) {

        $mode_of_pymnt = $this->db->select('mode_of_pymnt')->from('enrol_paymnt_recd')->where('invoice_id', $invoice_id)
                        ->order_by('recd_on', 'DESC')
                        ->get()->row()->mode_of_pymnt;
        $mode_of_pymnt;

        if ($mode_of_pymnt == "SFC_ATO" || $mode_of_pymnt == "SFC_SELF") {
            $order = 'ASC';
            //$mop=array('SFC_ATO','SFC_SELF');
            $mop = array('SFC_ATO', 'SFC_SELF', 'CASH', 'NETS', 'CHQ', 'GIRO', 'ONLINE', 'PSEA');
        } else {
            $order = 'DESC';
            $mop = array('SFC_ATO', 'SFC_SELF', 'CASH', 'NETS', 'CHQ', 'GIRO', 'ONLINE', 'PSEA');
        }
        $this->db->select('epbd.invoice_id, epbd.recd_on, epbd.amount_recd, 

                epr.mode_of_pymnt,epr.othr_mode_of_payment, epr.cheque_number,epr.sfc_claimed,epr.other_amount_recd,epr.cheque_date,

                tup.first_name, tup.last_name, tup.gender');

        $this->db->from('enrol_pymnt_brkup_dt epbd');

        $this->db->join('enrol_paymnt_recd epr', 'epr.invoice_id=epbd.invoice_id and epr.recd_on=epbd.recd_on', 'left');

        $this->db->join('tms_users_pers tup', 'tup.user_id = epbd.user_id', 'left');

        $this->db->where('epbd.invoice_id', $invoice_id);
        $this->db->where_in('epr.mode_of_pymnt', $mop);

        //$this->db->order_by('epbd.recd_on',$order);
        $this->db->order_by('epbd.trigger_date', $order); // added by shubhranshu to fetch the latest record
        if (!empty($user_id)) {

            $this->db->where('epbd.user_id', $user_id);
        }

        $result = $this->db->get()->result_object();

        return $result;
    }

    ////////////////////////


    /*

     * function to get trainee paid details

     */

    public function get_invoice_refund_details($invoice_id) {



        $result = $this->db->select('epbd.invoice_id, epbd.refund_date, epbd.refund_amount,

                tup.first_name, tup.last_name, tup.gender')
                        ->from('enrol_refund_brkup_dt epbd')
                        ->join('tms_users_pers tup', 'tup.user_id = epbd.user_id')
                        ->where('epbd.invoice_id', $invoice_id)->order_by('epbd.refund_date', 'DESC')
                        ->get()->result_object();

        return $result;
    }

    /**

     * function to get trainee paid details

     */
    public function get_refund_paid_details($invoice_id) {

        $result = $this->db->select('er.refund_on, er.mode_of_refund,er.othr_mode_of_refund, er.amount_refund, er.cheque_number,

                er.cheque_date, er.refund_by, er.refnd_reason, er.refnd_reason_ot')
                        ->from('enrol_refund er')
                        ->where('er.invoice_id', $invoice_id)
                        ->order_by('er.trigger_date', 'DESC')->get()->result_object(); /////addded by shubhranshu order by desc to fetch the latest record

        return $result;
    }

    /**

     * function to get trainee id from payment_due_id

     */
    public function get_trainee_by_pymnt_due_id($payment_due_id) {

        $result = $this->db->select('epd.user_id, tup.first_name, tup.last_name, tup.gender')
                ->from('enrol_pymnt_due epd')
                ->join('tms_users_pers tup', 'tup.user_id=epd.user_id')
                ->where('epd.pymnt_due_id', $payment_due_id)->get()
                ->row();

        return $result;
    }

    /**

     * function to get company payment due id

     */
    public function get_company_payment_due_details($payment_due_id) {

        $result = $this->db->select('epd.user_id, tup.first_name, tup.last_name, tup.gender, tu.tax_code,

                 ce.tg_number, ce.payment_status, epd.subsidy_amount, epd.discount_type, epd.discount_rate, 

                 epd.total_amount_due, epd.gst_amount, cc.class_name, ce.payment_status')
                        ->from('enrol_pymnt_due epd')
                        ->join('tms_users_pers tup', 'tup.user_id=epd.user_id')
                        ->join('class_enrol ce', 'ce.user_id=epd.user_id and ce.pymnt_due_id=epd.pymnt_due_id')
                        ->join('course_class cc', 'cc.class_id=ce.class_id and cc.course_id=ce.course_id and cc.tenant_id=ce.tenant_id')
                        ->join('tms_users tu', 'tup.user_id=tu.user_id')
                        ->where('epd.pymnt_due_id', $payment_due_id)
                        ->where('epd.att_status', 1)
                        ->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'))
                        ->get()->result();

        return $result;
    }

    /**

     * function to refund payment for company

     */
    public function refund_company_payment_post($tenant_id, $user_id) {

        extract($_POST);

        $invoice_id = $invoice_hidden_id;

        $trainee_id = $trainee_hidden_id;

        if (!empty($invoice_id)) {

            if ($payment_type == 'CASH' || $payment_type == 'NETS') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                    'refund_type' => 'COMP',
                    'mode_of_refund' => $payment_type,
                    'amount_refund' => round($cash_amount, 2),
                    'cheque_number' => NULL,
                    'cheque_date' => NULL,
                    'bank_name' => NULL,
                    'refnd_reason' => $refund_reason,
                    'refnd_reason_ot' => $other_reason,
                    'refund_by' => $user_id,
                );
            } elseif ($payment_type == 'CHQ') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                    'refund_type' => 'COMP',
                    'mode_of_refund' => $payment_type,
                    'amount_refund' => round($cheque_amount, 2),
                    'cheque_number' => $cheque_number,
                    'cheque_date' => date('Y-m-d', strtotime($cheque_date)),
                    'bank_name' => $bank_name,
                    'refnd_reason' => $refund_reason,
                    'refnd_reason_ot' => $other_reason,
                    'refund_by' => $user_id,
                );
            }



            $this->db->trans_start();



            $this->db->insert('enrol_refund', $data);

            $r_paid = date('Y-m-d H:i:S', strtotime($refund_date));

            foreach ($trainee_selected as $k => $v) {

                if (($trainee[$v] == 1) || ($trainee[$v] == 2)) {

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => $r_paid,
                        'user_id' => $v,
                        'refund_amount' => round($amount_paying[$v], 2),
                        'refund_by' => $user_id,
                    );

                    $this->db->insert('enrol_refund_brkup_dt', $breakup_data);

                    if ($amount_check[$v] == $amount_paying[$v]) {

                        $ce_data = array(
                            'payment_status' => 'NOTPAID'
                        );
                    } else {

                        $ce_data = array(
                            'payment_status' => 'PARTPAID'
                        );
                    }

                    $this->db->where('user_id', $v);

                    $this->db->where('pymnt_due_id', $payment_due_id);

                    $this->db->update('class_enrol', $ce_data);
                }
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

     * function to post refund payment for a trainee

     */
    public function refund_payment_post($tenant_id, $user_id) {

        extract($_POST);

        $invoice_id = $invoice_hidden_id;

        $trainee_id = $trainee_hidden_id;
        $refundable_total;
        $refundable_total = round($refundable_total, 2);

        if (!empty($invoice_id)) {

            if ($payment_type == 'CASH' || $payment_type == 'NETS') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                    'refund_type' => 'INDV',
                    'mode_of_refund' => $payment_type,
                    'amount_refund' => round($cash_amount, 2),
                    'cheque_number' => NULL,
                    'cheque_date' => NULL,
                    'bank_name' => NULL,
                    'refnd_reason' => $refund_reason,
                    'refnd_reason_ot' => strtoupper($other_reason),
                    'refund_by' => $user_id,
                );

                $brk_data = array(
                    'invoice_id' => $invoice_id,
                    'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                    'user_id' => $trainee_id,
                    'refund_amount' => round($cash_amount, 2),
                    'refund_by' => $user_id,
                );



                $refunding_amt = round($cash_amount, 2);
            } else if ($payment_type == 'CHQ') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                    'refund_type' => 'INDV',
                    'mode_of_refund' => $payment_type,
                    'amount_refund' => round($cheque_amount, 2),
                    'cheque_number' => $cheque_number,
                    'cheque_date' => date('Y-m-d', strtotime($cheque_date)),
                    'bank_name' => $bank_name,
                    'refnd_reason' => $refund_reason,
                    'refnd_reason_ot' => $other_reason,
                    'refund_by' => $user_id,
                );

                $brk_data = array(
                    'invoice_id' => $invoice_id,
                    'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                    'user_id' => $trainee_id,
                    'refund_amount' => round($cheque_amount, 2),
                    'refund_by' => $user_id,
                );



                $refunding_amt = round($cheque_amount, 2);
            }
            /* sfc statrs -- 
             * 
             */ else if ($payment_type == 'SFC_SELF') {
                if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') {
                    $net_amount = $sfc_amount + $cash_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'refund_type' => 'INDV',
                        'mode_of_refund' => $payment_type,
                        'othr_mode_of_refund' => $payment_type1,
                        'amount_refund' => round($net_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'other_amount_refund' => round($cash_amount1, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'refnd_reason' => $refund_reason,
                        'refnd_reason_ot' => strtoupper($other_reason),
                        'refund_by' => $user_id,
                    );

                    $brk_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'user_id' => $trainee_id,
                        'refund_amount' => round($net_amount, 2),
                        'refund_by' => $user_id,
                    );
                    $refunding_amt = round($net_amount, 2);
                } else if ($payment_type1 == 'CHQ1') {
                    $net_amount = $sfc_amount + $cheque_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'refund_type' => 'INDV',
                        'mode_of_refund' => $payment_type,
                        'othr_mode_of_refund' => $payment_type1,
                        'amount_refund' => round($net_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'other_amount_refund' => round($cheque_amount1, 2),
                        'cheque_number' => $cheque_number1,
                        'cheque_date' => date('Y-m-d', strtotime($cheque_date1)),
                        'bank_name' => $bank_name1,
                        'refnd_reason' => $refund_reason,
                        'refnd_reason_ot' => $other_reason,
                        'refund_by' => $user_id,
                    );

                    $brk_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'user_id' => $trainee_id,
                        'refund_amount' => round($net_amount, 2),
                        'refund_by' => $user_id,
                    );
                    $refunding_amt = round($net_amount, 2);
                } else {
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'refund_type' => 'INDV',
                        'mode_of_refund' => $payment_type,
                        'amount_refund' => round($sfc_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'refnd_reason' => $refund_reason,
                        'refnd_reason_ot' => strtoupper($other_reason),
                        'refund_by' => $user_id,
                    );

                    $brk_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'user_id' => $trainee_id,
                        'refund_amount' => round($sfc_amount, 2),
                        'refund_by' => $user_id,
                    );
                    $refunding_amt = round($sfc_amount, 2);
                }
            } else if ($payment_type == 'SFC_ATO') {
                if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') {
                    $net_amount = $sfcato_amount + $cash_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'refund_type' => 'INDV',
                        'mode_of_refund' => $payment_type,
                        'othr_mode_of_refund' => $payment_type1,
                        'amount_refund' => round($net_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        'other_amount_refund' => round($cash_amount1, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'refnd_reason' => $refund_reason,
                        'refnd_reason_ot' => strtoupper($other_reason),
                        'refund_by' => $user_id,
                    );

                    $brk_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'user_id' => $trainee_id,
                        'refund_amount' => round($net_amount, 2),
                        'refund_by' => $user_id,
                    );
                    $refunding_amt = round($net_amount, 2);
                } else if ($payment_type1 == 'CHQ1') {
                    $net_amount = $sfcato_amount + $cheque_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'refund_type' => 'INDV',
                        'mode_of_refund' => $payment_type,
                        'othr_mode_of_refund' => $payment_type1,
                        'amount_refund' => round($net_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        'other_amount_refund' => round($cheque_amount1, 2),
                        'cheque_number' => $cheque_number1,
                        'cheque_date' => date('Y-m-d', strtotime($cheque_date1)),
                        'bank_name' => $bank_name1,
                        'refnd_reason' => $refund_reason,
                        'refnd_reason_ot' => $other_reason,
                        'refund_by' => $user_id,
                    );

                    $brk_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'user_id' => $trainee_id,
                        'refund_amount' => round($net_amount, 2),
                        'refund_by' => $user_id,
                    );
                    $refunding_amt = round($net_amount, 2);
                } else {
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'refund_on' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'refund_type' => 'INDV',
                        'mode_of_refund' => $payment_type,
                        'amount_refund' => round($sfcato_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'refnd_reason' => $refund_reason,
                        'refnd_reason_ot' => strtoupper($other_reason),
                        'refund_by' => $user_id,
                    );

                    $brk_data = array(
                        'invoice_id' => $invoice_id,
                        'refund_date' => date('Y-m-d H:i:S', strtotime($refund_date)),
                        'user_id' => $trainee_id,
                        'refund_amount' => round($sfcato_amount, 2),
                        'refund_by' => $user_id,
                    );
                    $refunding_amt = round($sfcato_amount, 2);
                }
            }
            /*
             * sfc ends
             */



            $this->db->trans_start();



            $this->db->insert('enrol_refund', $data);

            $this->db->insert('enrol_refund_brkup_dt', $brk_data);

            $this->db->set('sfc_claimed', 0);
            $this->db->set('othr_mode_of_payment', 0);
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('enrol_paymnt_recd');
            if ($refundable_total == $refunding_amt) {

                $ce_data = array(
                    'payment_status' => 'NOTPAID'
                );

                $this->db->where('user_id', $trainee_id);

                $this->db->where('pymnt_due_id', $payment_due_id);

                $this->db->update('class_enrol', $ce_data);
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

    /* This method get the previous record of payment update for activity log skm start */

    public function get_last_comp_payment_update($invoice_id) {
        $this->db->select('epr.invoice_id,epr.recd_on,epr.sfc_recd_on,epr.mode_of_pymnt,epr.othr_mode_of_payment'
                . ',epr.amount_recd as total_paid_amount,epr.sfc_claimed,epr.other_amount_recd,epr.cheque_number,epr.cheque_date,'
                . 'epr.bank_name,epr.recd_by,epr.trigger_date');
        $this->db->from('enrol_paymnt_recd epr');
        $this->db->join('enrol_pymnt_brkup_dt epbt', 'epr.invoice_id = epbt.invoice_id and epr.recd_on = epbt.recd_on');
        $this->db->where('epr.invoice_id', $invoice_id);
        $this->db->order_by('epr.recd_on', 'desc');
        $this->db->limit(1);
        $res = $this->db->get();
//        echo $this->db->last_query(); echo "<br/>";
        if ($res->num_rows() > 0) {
            $data['company_details'] = $res->row();
            $date = $data['company_details']->recd_on;
            $data['details'] = $this->get_company_breakup_dt($invoice_id, $date);
            return $data;
        } else {
            return $res = 0;
        }
    }

    public function get_company_breakup_dt($invoice_id, $date) {
        $this->db->select('*');
        $this->db->from('enrol_pymnt_brkup_dt');
        $this->db->where('invoice_id', $invoice_id);
        $this->db->where('recd_on', $date);
        $sql = $this->db->get();
//        echo $this->db->last_query();
        $comp_breakup_data = $sql->result();
        return $comp_breakup_data;
    }

    /* skm end */

    /* This method get the previous record of payment refund of company for activity log skm start */

    public function get_last_comp_payment_refund($invoice_id) {
        $this->db->select('er.invoice_id,er.refund_on,er.refund_type,er.mode_of_refund,er.othr_mode_of_refund,er.amount_refund,'
                . 'er.sfc_claimed,er.other_amount_refund,er.cheque_number,er.cheque_date,er.bank_name,er.refnd_reason,er.refnd_reason_ot,'
                . 'er.refund_by,er.trigger_date');
        $this->db->from('enrol_refund er');
        $this->db->where('er.invoice_id', $invoice_id);
        $this->db->order_by('er.trigger_date', 'desc');
        $this->db->limit(1);
        $res = $this->db->get();
//        echo $this->db->last_query();
        // echo $res->num_rows();
//        echo"<br/>";echo"<br/>";
        if ($res->num_rows() > 0) {
            $data['company_details'] = $res->row();
//            $date = $data['company_details']->refund_on;
            $date = $data['company_details']->trigger_date;
            $data['details'] = $this->get_company_refund_breakup_dt($invoice_id, $date);
            return $data;
        } else {
            return $res = 0;
        }
    }

    public function get_company_refund_breakup_dt($invoice_id, $date) {
        $this->db->select('*');
        $this->db->from('enrol_refund_brkup_dt');
        $this->db->where('invoice_id', $invoice_id);
        $this->db->where('trigger_date', $date);
        $sql = $this->db->get();
//        echo $this->db->last_query();
        $comp_breakup_data = $sql->result();
        return $comp_breakup_data;
    }

    /* skm end */

    /* This function get the previous data of individual refund update for activity log skm start */

    public function get_last_payment_refund($invoice_id) {
        $this->db->select('er.*,erbt.invoice_id,erbt.user_id,erbt.refund_amount,erbt.refund_date,erbt.trigger_date as refund_date');
        $this->db->from('enrol_refund er');
        $this->db->join('enrol_refund_brkup_dt erbt', 'er.invoice_id = erbt.invoice_id and er.refund_on = erbt.refund_date');
        $this->db->where('er.invoice_id', $invoice_id);
        $this->db->order_by('er.refund_on', 'desc');
        $this->db->limit(1);
        $sql = $this->db->get();
//        echo $this->db->last_query();

        if ($sql->num_rows() > 0) {
            return $sql->row();
        } else {

            return $sql = 0;
        }
    }

    /* skm end */

    /**

      /**

     * function to update the posted Payment values

     */
    public function update_company_payment_post($tenant_id, $user_id) {

        extract($_POST);

        $invoice_id = $this->db->select('invoice_id')->from('enrol_invoice')->where('pymnt_due_id', $payment_due_id)->get()->row()->invoice_id;

        if (!empty($invoice_id)) {



            $cur_time = date('H:i:s');

            if ($payment_type == 'CASH' || $payment_type == 'NETS') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'recd_on' => date('Y-m-d ', strtotime($cashpaid_on)) . $cur_time,
                    'mode_of_pymnt' => $payment_type,
                    'amount_recd' => round($cash_amount, 2),
                    'cheque_number' => NULL,
                    'cheque_date' => NULL,
                    'bank_name' => NULL,
                    'recd_by' => $user_id,
                );

                $a_paid = round($cash_amount, 2);



                $p_paid = date('Y-m-d ', strtotime($cashpaid_on)) . $cur_time;
            } elseif ($payment_type == 'CHQ') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'recd_on' => date('Y-m-d ', strtotime($paid_on)) . $cur_time,
                    'mode_of_pymnt' => $payment_type,
                    'amount_recd' => round($cheque_amount, 2),
                    'cheque_number' => $cheque_number,
                    'cheque_date' => date('Y-m-d', strtotime($cheque_date)),
                    'bank_name' => $bank_name,
                    'recd_by' => $user_id,
                );

                $a_paid = round($cheque_amount, 2);



                $p_paid = date('Y-m-d ', strtotime($paid_on)) . $cur_time;
            } elseif ($payment_type == 'GIRO') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'recd_on' => date('Y-m-d ', strtotime($transc_on)) . $cur_time,
                    'mode_of_pymnt' => $payment_type,
                    'amount_recd' => round($giro_amount, 2),
                    'cheque_number' => NULL,
                    'cheque_date' => NULL,
                    'bank_name' => $gbank_name,
                    'recd_by' => $user_id,
                );

                $a_paid = round($giro_amount, 2);



                $p_paid = date('Y-m-d ', strtotime($transc_on)) . $cur_time;
            }





            $this->db->trans_start();



            $this->db->insert('enrol_paymnt_recd', $data);

            $t_recd = 0;

            foreach ($trainee_selected as $k => $v) {

                if (($trainee[$v] == 1) || ($trainee[$v] == 2)) {

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $v,
                        'amount_recd' => round($amount_paying[$v], 2),
                        'recd_on' => $p_paid
                    );

                    $this->db->insert('enrol_pymnt_brkup_dt', $breakup_data);

                    $payment_status = ($amount_paying[$v] == $amount_check[$v]) ? 'PAID' : 'PARTPAID';

                    $data = array('payment_status' => $payment_status, 'enrol_status' => 'ENRLACT');

                    $this->db->where('tenant_id', $tenant_id);

                    $this->db->where('enrolment_mode', 'COMPSPON');

                    $this->db->where('pymnt_due_id', $payment_due_id);

                    $this->db->where('user_id', $v);

                    $this->db->update('class_enrol', $data);



                    $t_recd += round($amount_paying[$v], 2);
                }
            }

            if (($a_paid - $t_recd) > 0) {

                $excess_amount = round(($a_paid - $t_recd), 2);

                $data = array('invoice_excess_amt' => $excess_amount);

                $this->db->where('invoice_id', $invoice_id);

                $this->db->update('enrol_invoice', $data);
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

    /* This function get the previous data of individual payment update for activity log skm start */

    public function get_last_payment_update($invoice_id) {
        $this->db->select('epr.*,epbt.invoice_id,epbt.user_id,epbt.amount_recd,epbt.recd_on,epbt.trigger_date');
        $this->db->from('enrol_paymnt_recd epr');
        $this->db->join('enrol_pymnt_brkup_dt epbt', 'epr.invoice_id = epbt.invoice_id and epr.recd_on = epbt.recd_on');
        $this->db->where('epr.invoice_id', $invoice_id);
        $this->db->order_by('epr.recd_on', 'desc');
        $this->db->limit(1);
        $sql = $this->db->get();

        if ($sql->num_rows() > 0) {
            return $sql->row();
        } else {
            return $sql = 0;
        }
    }

    /* skm end */

    /**

     * function to update the posted Payment values

     */
    public function update_payment_post($tenant_id, $user_id) {

        extract($_POST);

        $invoice_id = $this->db->select('invoice_id')->from('enrol_invoice')->where('pymnt_due_id', $payment_due_id)->get()->row()->invoice_id;

        $trainee_id = $this->get_trainee_by_pymnt_due_id($payment_due_id)->user_id;


        if (!empty($invoice_id)) {
            $cur_time = date('H:i:s');
            if ($payment_type == 'CASH' || $payment_type == 'NETS' || $payment_type == 'PSEA') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'recd_on' => date('Y-m-d ', strtotime($cashpaid_on)) . $cur_time,
                    'mode_of_pymnt' => $payment_type,
                    'amount_recd' => round($cash_amount, 2),
                    'cheque_number' => NULL,
                    'cheque_date' => NULL,
                    'bank_name' => NULL,
                    'recd_by' => $user_id,
                );

                $breakup_data = array(
                    'invoice_id' => $invoice_id,
                    'user_id' => $trainee_id,
                    'amount_recd' => round($cash_amount, 2),
                    'recd_on' => date('Y-m-d ', strtotime($cashpaid_on)) . $cur_time
                );
            } elseif ($payment_type == 'CHQ') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'recd_on' => date('Y-m-d ', strtotime($paid_on)) . $cur_time,
                    'mode_of_pymnt' => $payment_type,
                    'amount_recd' => round($cheque_amount, 2),
                    'cheque_number' => $cheque_number,
                    'cheque_date' => date('Y-m-d', strtotime($cheque_date)),
                    'bank_name' => $bank_name,
                    'recd_by' => $user_id,
                );

                $breakup_data = array(
                    'invoice_id' => $invoice_id,
                    'user_id' => $trainee_id,
                    'amount_recd' => round($cheque_amount, 2),
                    'recd_on' => date('Y-m-d ', strtotime($paid_on)) . $cur_time
                );
            } else if ($payment_type == 'GIRO') {

                $data = array(
                    'invoice_id' => $invoice_id,
                    'recd_on' => date('Y-m-d ', strtotime($transc_on)) . $cur_time,
                    'mode_of_pymnt' => $payment_type,
                    'amount_recd' => round($giro_amount, 2),
                    'cheque_number' => NULL,
                    'cheque_date' => NULL,
                    'bank_name' => $gbank_name,
                    'recd_by' => $user_id,
                );

                $breakup_data = array(
                    'invoice_id' => $invoice_id,
                    'user_id' => $trainee_id,
                    'amount_recd' => round($giro_amount, 2),
                    'recd_on' => date('Y-m-d ', strtotime($transc_on)) . $cur_time
                );
            }
            if ($payment_type == 'SFC_ATO') {


                if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') {


                    $sfcclaim_on;
                    date('Y-m-d', strtotime($sfcatoclaim_on));

                    $net_amount = $sfcato_amount + $cash_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($cashpaid_on1)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcatoclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'othr_mode_of_payment' => $payment_type1,
                        'amount_recd' => round($net_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        'other_amount_recd' => round($cash_amount1, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($net_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($cashpaid_on1)) . $cur_time
                    );
                } elseif ($payment_type1 == 'CHQ1') {
                    $net_amount = $sfcato_amount + $cheque_amount1;

                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($paid_on1)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcatoclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'othr_mode_of_payment' => $payment_type1,
                        'amount_recd' => round($net_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        'other_amount_recd' => round($cheque_amount1, 2),
                        'cheque_number' => $cheque_number1,
                        'cheque_date' => date('Y-m-d', strtotime($cheque_date1)),
                        'bank_name' => $bank_name1,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($net_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($paid_on1)) . $cur_time
                    );
                } else if ($payment_type1 == 'GIRO1') {
                    $net_amount = $sfcato_amount + $giro_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($transc_on1)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'othr_mode_of_payment' => $payment_type1,
                        'amount_recd' => round($net_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        //'amount_recd_other'=>round($giro_amount1,2),/// commented by shubhranshu
                        'other_amount_recd' => round($giro_amount1, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => $gbank_name1,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($net_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($transc_on1)) . $cur_time
                    );
                } else {
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($sfcatoclaim_on)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcatoclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'amount_recd' => round($sfcato_amount, 2),
                        'sfc_claimed' => round($sfcato_amount, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($sfcato_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($sfcatoclaim_on)) . $cur_time
                    );
                }
            } else if ($payment_type == 'SFC_SELF') {
                if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') {
                    $sfcclaim_on;
                    date('Y-m-d', strtotime($sfcclaim_on));

                    $net_amount = $sfc_amount + $cash_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($cashpaid_on1)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'othr_mode_of_payment' => $payment_type1,
                        'amount_recd' => round($net_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'other_amount_recd' => round($cash_amount1, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($net_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($cashpaid_on1)) . $cur_time
                    );
                } elseif ($payment_type1 == 'CHQ1') {
                    $net_amount = $sfc_amount + $cheque_amount1;

                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($paid_on1)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'othr_mode_of_payment' => $payment_type1,
                        'amount_recd' => round($net_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'other_amount_recd' => round($cheque_amount1, 2),
                        'cheque_number' => $cheque_number1,
                        'cheque_date' => date('Y-m-d', strtotime($cheque_date1)),
                        'bank_name' => $bank_name1,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($net_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($paid_on1)) . $cur_time
                    );
                } else if ($payment_type1 == 'GIRO1') {
                    $net_amount = $sfc_amount + $giro_amount1;
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($transc_on1)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'othr_mode_of_payment' => $payment_type1,
                        'amount_recd' => round($net_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'other_amount_recd' => round($giro_amount1, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => $gbank_name1,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($net_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($transc_on1)) . $cur_time
                    );
                } else {
                    $data = array(
                        'invoice_id' => $invoice_id,
                        'recd_on' => date('Y-m-d ', strtotime($sfcclaim_on)) . $cur_time,
                        'sfc_recd_on' => date('Y-m-d', strtotime($sfcclaim_on)),
                        'mode_of_pymnt' => $payment_type,
                        'amount_recd' => round($sfc_amount, 2),
                        'sfc_claimed' => round($sfc_amount, 2),
                        'cheque_number' => NULL,
                        'cheque_date' => NULL,
                        'bank_name' => NULL,
                        'recd_by' => $user_id,
                    );

                    $breakup_data = array(
                        'invoice_id' => $invoice_id,
                        'user_id' => $trainee_id,
                        'amount_recd' => round($sfc_amount, 2),
                        'recd_on' => date('Y-m-d ', strtotime($sfcclaim_on)) . $cur_time
                    );
                }
            }


            $this->db->trans_start();
            $this->db->insert('enrol_paymnt_recd', $data);

            $this->db->insert('enrol_pymnt_brkup_dt', $breakup_data);

            /////added by shubhranshu sfc claim id
            if ($payment_type == 'SFC_ATO') {
                $sfc_claim_id = strtoupper($sfc_ato_claim_id);

                $data = array('payment_status' => 'PAID', 'enrol_status' => 'ENRLACT', 'sfc_claim_id' => $sfc_claim_id);
            } else {
                $data = array('payment_status' => 'PAID', 'enrol_status' => 'ENRLACT');
            }
            //////end of the code by ssp


            $this->db->where('tenant_id', $tenant_id);

            $this->db->where('enrolment_mode', 'SELF');

            $this->db->where('pymnt_due_id', $payment_due_id);

            $this->db->update('class_enrol', $data);



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

     * this method to search trainee users

     */
    public function search_trainee_updatepayment($invoice_id, $taxcode_id, $trainee_id, $tenant_id) {

        $this->db->select('ce.pymnt_due_id as payid, tup.first_name as first, tup.last_name as last,

                ei.total_inv_amount as amountdue,tu.tax_code as taxcode, cc.class_name, c.crse_name')
                ->from('class_enrol ce')
                ->join('course_class cc', 'cc.class_id=ce.class_id')
                ->join('course c', 'c.course_id=cc.course_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id and epd.user_id=ce.user_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where_not_in('ce.payment_status', 'PAID');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($invoice_id) {

            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if ($taxcode_id) {

            if (empty($invoice_id)) {

                $this->db->where('tu.user_id', $taxcode_id);
            } else {

                $this->db->or_where('tu.user_id', $taxcode_id);
            }
        }

        if ($trainee_id) {

            if (empty($invoice_id) && empty($taxcode_id)) {

                $this->db->where('tu.user_id', $trainee_id);
            } else {

                $this->db->or_where('tu.user_id', $trainee_id);
            }
        }

        return $this->db->get()->result_object();
    }

    /**

     * 

     */
    public function get_payid_for_class_user($class, $user) {

        $payid = $this->db->select('pymnt_due_id')->from('class_enrol')->where('user_id', $user)
                        ->where('class_id', $class)->get()->row()->pymnt_due_id;

        return $payid;
    }

    //to get eid by shubhranshu
    public function get_eid_for_class_user($class, $user) {

        $eid = $this->db->select('eid_number')->from('class_enrol')->where('user_id', $user)
                        ->where('class_id', $class)->get()->row()->eid_number;

        return $eid;
    }

    /**

     * this method to search trainee invoice

     */
    public function search_trainee_invoice($invoice_id, $taxcode_id, $trainee_id, $tenant_id, $paid = 0) {

        $this->db->select('ei.invoice_id, ce.pymnt_due_id as payid, tup.first_name as first, tup.last_name as last,

                ei.total_inv_amount as amountdue,tu.tax_code as taxcode, cc.class_name, c.crse_name,

                ce.payment_status, tu.country_of_residence')
                ->from('class_enrol ce')
                ->join('course_class cc', 'cc.class_id=ce.class_id')
                ->join('course c', 'c.course_id=cc.course_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if (!empty($paid)) {

            $this->db->where('ce.payment_status', $paid);
        }

        if (!empty($invoice_id)) {

            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if (!empty($taxcode_id)) {

            $this->db->where('tu.user_id', $taxcode_id);
        }

        if (!empty($trainee_id)) {

            $this->db->where('tu.user_id', $trainee_id);
        }

        $invoice_det = $this->db->get()->result();

        foreach ($invoice_det as $k => $det) {

            $amountrecd = $this->db->select_sum('epr.amount_recd', 'amountrecd')->from('enrol_paymnt_recd epr')->where('epr.invoice_id', $det->invoice_id)->group_by('epr.invoice_id')->get()->row('amountrecd');

            $invoice_det[$k]->amountrecd = (empty($amountrecd)) ? 0 : $amountrecd;

            $amount_refund = $this->db->select_sum('er.amount_refund', 'amount_refund')->from('enrol_refund er')->where('er.invoice_id', $det->invoice_id)->group_by('er.invoice_id')->get()->row('amount_refund');

            $invoice_det[$k]->amount_refund = (empty($amount_refund)) ? 0 : $amount_refund;
        }

        return $invoice_det;
    }

    /**

     * this method to search trainee users

     */
    public function search_trainee_refundpayment($invoice_id, $taxcode_id, $trainee_id, $tenant_id) {

        $this->db->select('ei.invoice_id, ce.pymnt_due_id as payid, tup.first_name as first, tup.last_name as last,

                tu.country_of_residence as country_of_residence,

                ei.total_inv_amount as amountdue,tu.tax_code as taxcode, cc.class_name, c.crse_name')
                ->from('class_enrol ce')
                ->join('course_class cc', 'cc.class_id=ce.class_id')
                ->join('course c', 'c.course_id=cc.course_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where('ce.payment_status', 'PAID');

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($invoice_id) {

            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if ($taxcode_id) {

            if (empty($invoice_id)) {

                $this->db->where('tu.user_id', $taxcode_id);
            } else {

                $this->db->or_where('tu.user_id', $taxcode_id);
            }
        }

        if ($trainee_id) {

            if (empty($invoice_id) && empty($taxcode_id)) {

                $this->db->where('tu.user_id', $trainee_id);
            } else {

                $this->db->or_where('tu.user_id', $trainee_id);
            }
        }

        $invoice_det = $this->db->get()->result_object();

        foreach ($invoice_det as $k => $det) {

            $amountrecd = $this->db->select_sum('epr.amount_recd', 'amountrecd')->from('enrol_paymnt_recd epr')->where('epr.invoice_id', $det->invoice_id)->group_by('epr.invoice_id')->get()->row('amountrecd');

            $invoice_det[$k]->amountrecd = (empty($amountrecd)) ? 0 : round($amountrecd, 2);

            $amount_refund = $this->db->select_sum('er.amount_refund', 'amount_refund')->from('enrol_refund er')->where('er.invoice_id', $det->invoice_id)->group_by('er.invoice_id')->get()->row('amount_refund');

            $invoice_det[$k]->amount_refund = (empty($amount_refund)) ? 0 : round($amount_refund, 2);
        }

        return $invoice_det;
    }

    /*

     * This method gets the user details for a user based on the tenant

     */

    public function get_users_details($tenant_id, $user_id) {

        $this->db->select('*');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id and usr.tenant_id=pers.tenant_id');

        $this->db->where_in('usr.user_id', $user_id);

        $this->db->where('usr.tenant_id', $tenant_id);

        $qry = $this->db->get();

        return $qry->result_object();
    }

    /**

     * This method re-schedules the trainee enrollment and recalculates the payment due

     * @global type $temp_array

     * @param type $tenant_id

     * @param type $trainee_id

     * @param type $reschedule_class

     * @param type $active_class

     * @param type $course_id

     * @return type

     */
    public function reschedule_trainee($tenant_id, $trainee_id, $reschedule_class, $active_class, $course_id, $reschedule_reason, $other_reason) {

        global $temp_array;

        $prev_class_id = $active_class;

        $new_class_id = $reschedule_class;

        $data = $this->db->select('*')->from('class_enrol')->where('user_id', $trainee_id)->where('tenant_id', $tenant_id)
                        ->where('class_id', $active_class)->get()->row_array();

        $prev_pymnnt_due_id = $data['pymnt_due_id'];

        $prev_payment_status = $data['payment_status'];

        if ($data['enrolment_mode'] == 'SELF') {

            $str_qry = "SELECT * from enrol_pymnt_due where pymnt_due_id='" . $prev_pymnnt_due_id . "'";

            $result = $this->db->query($str_qry)->row_array();

            $class_prev_fees = $result['class_fees'];

            $str_qry = "SELECT * from course_class cl, course c where cl.course_id='" . $course_id . "' and "
                    . "cl.class_id='" . $reschedule_class . "' and cl.tenant_id='" . $tenant_id . "' and cl.course_id = c.course_id";

            $result = $this->db->query($str_qry)->row_array();

            $class_current_fees = $result['class_fees'];

            if ($class_prev_fees != $class_current_fees) {

                $status = $this->recalculate_fees_payable($prev_class_id, $new_class_id, $course_id, $tenant_id, $trainee_id, $prev_pymnnt_due_id, $class_prev_fees, $class_current_fees, $prev_payment_status);
            } else {

                $status = $this->change_class_enrol($prev_class_id, $new_class_id, $course_id, $tenant_id, $trainee_id, $prev_pymnnt_due_id, $prev_payment_status);
            }
        }



        if ($data['enrolment_mode'] == 'COMPSPON') {

            $status = $this->remove_company_enrollment($tenant_id, $this->user->user_id, $course_id, $prev_class_id, $data['company_id']
                    , $inv_id = 0, $data['pymnt_due_id'], array($trainee_id));

            if ($status) {

                $status = $this->update_rescheduled_reason($tenant_id, $data['pymnt_due_id'], $trainee_id, $course_id, $prev_class_id, $reschedule_reason, $other_reason, $new_class_id);
            }

            if (count($temp_array) == 0 && $status) {

                $res = $this->reschedule_create_new_comp_enroll($tenant_id, $data, $course_id, $new_class_id, $trainee_id);

                $new_invoice_id = $res['invoice'];

                $new_pymnt_due_id = $res['pymnt_due_id'];

                $status = $res['status'];

                $temp_array[$data['company_id']] = array('company_id' => $data['company_id'],
                    'invoice_id' => $new_invoice_id,
                    'paymnt_due_id' => $new_pymnt_due_id);
                /* skm start */

                $update_data = array(
                    "enrolled_by" => $data['enrolled_by'],
                    "sales_executive_id" => $data['sales_executive_id'],
                    "eid_number" => $data['eid_number'],
                    "tpg_enrolment_status" => $data['tpg_enrolment_status'],                    
                );

                $this->db->where("course_id", $course_id);
                $this->db->where("class_id", $new_class_id);
                $this->db->where("user_id", $data['user_id']);
                $this->db->where("tenant_id", $tenant_id);
                $this->db->update('class_enrol', $update_data);

                /* skm end */
            } else {

                if (array_key_exists($data['company_id'], $temp_array)) {

                    $status = $this->reschedule_add_to_company_enrollment($tenant_id, $this->user->user_id, $course_id, $new_class_id, $data['company_id']
                            , $temp_array[$data['company_id']]['invoice_id'], $temp_array[$data['company_id']]['paymnt_due_id'], array($trainee_id));
                } else {

                    $res = $this->reschedule_create_new_comp_enroll($tenant_id, $data, $course_id, $new_class_id, $trainee_id);

                    $new_invoice_id = $res['invoice'];

                    $new_pymnt_due_id = $res['pymnt_due_id'];

                    $status = $res['status'];

                    $temp_array[$data['company_id']] = array('company_id' => $data['company_id'],
                        'invoice_id' => $new_invoice_id,
                        'paymnt_due_id' => $new_pymnt_due_id);
                }
            }
        }



        return $status;
    }

    /**

     * This method recalculates the fees payable

     * @param type $prev_class_id

     * @param type $new_class_id

     * @param type $course_id

     * @param type $tenant_id

     * @param type $trainee_id

     * @param type $prev_pymnnt_due_id

     * @param type $class_prev_fees

     * @param type $class_current_fees

     * @param type $prev_payment_status

     * @return type

     */
    public function recalculate_fees_payable($prev_class_id, $new_class_id, $course_id, $tenant_id, $trainee_id, $prev_pymnnt_due_id, $class_prev_fees, $class_current_fees, $prev_payment_status) {

        if ($prev_payment_status == 'PAID') {

            $current_payment_status = 'NOTPAID';

            if ($class_prev_fees < $class_current_fees) {

                $invoice_excess_amt = 0;
            }
        } else {

            $current_payment_status = $prev_payment_status;
        }

        $fees_array = $this->fees_payable($trainee_id, $tenant_id, $course_id, $new_class_id, 0, $company_id = 0, "");

        $new_invoice_amount = round($fees_array["net_fees_due"], 4);

        $gst_amount = round($fees_array["gst_amount"], 4);

        $discount_amount = round($fees_array["discount_amount"], 4);

        $discount_rate = round($fees_array["discount_rate"], 4);

        $discount_type = $fees_array["discount_type"];

        $unit_fees = round($fees_array["unit_fees"], 4);

        $gst_label = $fees_array["gst_label"];

        $gst_rule = $fees_array["gst_rule"];

        $gst_rate = $fees_array["gst_rate"];



        if ($class_prev_fees > $class_current_fees) {

            $str_qry = "SELECT * from enrol_invoice where pymnt_due_id ='" . $prev_pymnnt_due_id . "'";

            $result = $this->db->query($str_qry)->row_array();

            $prev_invoice_amount = $result['total_inv_amount'];

            $invoice_excess_amt = $prev_invoice_amount - $new_invoice_amount;
        }
        $logged_in_user_id = $this->user->user_id;
        $due_to = 'Regenrated Invoice due to Reschedule ';
        $data1 = $this->get_current_individule_invoice_data($prev_pymnnt_due_id);
        $status = $this->enrol_invoice_view($prev_pymnnt_due_id, $data1, $logged_in_user_id, $due_to);


        $staus = $this->update_enrol_pymnt_due($prev_pymnnt_due_id, $new_invoice_amount, $discount_type, $discount_rate, $gst_amount, $prev_pymnnt_due_id, $class_current_fees);

        $status = $this->update_invoice_audit_trail($prev_pymnnt_due_id);

        $status = $this->remove_invoice($prev_pymnnt_due_id);

        list($status, $new_invoice_id) = $this->create_new_invoice($prev_pymnnt_due_id, '', $new_invoice_amount, $unit_fees, $discount_amount, $subsidy_amount = 0, $gst_amount, $gst_rule, $gst_rate, 'INVINDV');

        if ($status) {

            $status = $this->set_audittrail_newinvoice_num($prev_pymnnt_due_id, $new_invoice_id);
            $status = $this->set_viewinvoice_newinvoice_num($prev_pymnnt_due_id, $new_invoice_id); //s4
        }

        $status = $this->change_class_enrol($prev_class_id, $new_class_id, $course_id, $tenant_id, $trainee_id, $prev_pymnnt_due_id, $current_payment_status);

        return $status;
    }

    /**
     * function to get classroom location for others
     */
    function get_classroom_location($venue, $other) {
        if ($venue == 'OTH') {
            return 'Others (' . $other . ')';
        } else {
            return $this->course->get_metadata_on_parameter_id($venue);
        }
    }

    /**

     * This method moves enrolment data to audit trail and creates a new enrolment

     * @param type $prev_class_id

     * @param type $new_class_id

     * @param type $course_id

     * @param type $tenant_id

     * @param type $trainee_id

     * @param type $pymnnt_due_id

     * @param type $current_payment_status

     * @return type

     */
    public function change_class_enrol($prev_class_id, $new_class_id, $course_id, $tenant_id, $trainee_id, $pymnnt_due_id, $current_payment_status) {

        $status = $this->update_classenrol_audittrail($tenant_id, $pymnnt_due_id, $trainee_id, $course_id, $prev_class_id);



        $cur_date = date('Y-m-d H:i:s');

        $data = array('class_id' => $new_class_id,
            'enrolled_on' => $cur_date,
            'payment_status' => $current_payment_status,
            'class_status' => $this->get_class_statustext($new_class_id));

        $this->db->where("pymnt_due_id", $pymnnt_due_id);

        $this->db->where("user_id", $trainee_id);

        $this->db->where("course_id", $course_id);

        $this->db->where("class_id", $prev_class_id);

        $this->db->where("tenant_id", $tenant_id);

        $status = $this->db->update('class_enrol', $data);

        return $status;
    }

    /**

     * method to do reschedule

     */
    public function create_reschedule() {

        $GLOBALS['temp_array'] = array();

        $status = TRUE;

        extract($_POST);

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;

        if ($type == 1 || $type == 4) {

            $user_id = ($type == 1) ? $taxcode_id : $taxcode_user_id;

            $active_class = $active_class;

            $reschedule_class = $reschedule_class;
        } else if ($type == 3) {

            $user_id = $trainee_id;

            $active_class = $active_class;

            $reschedule_class = $reschedule_class;
        } else {

            $user_id = implode(',', $control_6);

            $active_class = $course_active_class;

            $reschedule_class = $course_reschedule_class;
        }

        if (!empty($user_id)) {

            $user = explode(',', $user_id);
        }

        if (empty($course_id)) {

            $course_id = $this->db->select('course_id')->from('course_class')->where('class_id', $reschedule_class)->get()->row()->course_id;
        }

        foreach ($user as $trainee_id) {

            $status = $this->reschedule_trainee($tenant_id, $trainee_id, $reschedule_class, $active_class, $course_id, $reschedule_reason, $other_reason);

            if ($status) {

                $this->db->where('tenant_id', $tenant_id);

                $this->db->where('course_id', $course_id);

                $this->db->where('class_id', $active_class);

                $this->db->where('user_id', $trainee_id);

                $this->db->delete('class_attendance');

                $user_details = $this->get_user_mail_details($trainee_id);

                $active_details = $this->get_classname($active_class);

                $reschedule_details = $this->get_classname($reschedule_class);

                $mail_content = 'You have been re-scheduled from <b>' . $active_details->class_name . '</b> (Start date: ' . date('F j Y, h:i A', strtotime($active_details->class_start_datetime)) . ', End date: ' . date('F j Y, h:i A', strtotime($active_details->class_end_datetime)) . ') to <b>' . $reschedule_details->class_name . '</b> (Start date: ' . date('F j Y, h:i A', strtotime($reschedule_details->class_start_datetime)) . ', End date: ' . date('F j Y, h:i A', strtotime($reschedule_details->class_end_datetime)) . ').';

                $footer_data = str_replace("<Tenant_Company_Name>", $tenant_details->tenant_name, MAIL_FOOTER);

                $footer_data = str_replace("<Tenant_Company_Email>", $tenant_details->tenant_email_id, $footer_data);

                $this->reschedule_send_email($user_details, $mail_content, $footer_data);
            }
        }

        return $status;
    }

    /**

     * class update mail function 

     */
    public function reschedule_send_email($user, $content, $footer_data) {

        $subject = NULL;

        $body = NULL;

        if ($user->gender == 'MALE') {

            $body = "Dear Mr." . $user->first_name . ',';
        } elseif ($user->gender == 'FEMALE') {

            $body .="Dear Ms." . $user->first_name . ',';
        } else {

            $body .="Dear " . $user->first_name . ',';
        }

        $subject = 'Your Rescheduled Class Acknowledgment';

        $body .='<br/><br/>' . $content . '<br/><br/>';



        $body .= $footer_data;

        return send_mail($user->email, '', $subject, $body);
    }

    /**

     * get class name

     */
    public function get_classname($class_id) {

        $class_name = $this->db->select('class_name, class_start_datetime, class_end_datetime')->from('course_class')
                        ->where('class_id', $class_id)->get()->row();

        return $class_name;
    }

    /**

     * get user details

     */
    public function get_user_mail_details($user_id) {

        $this->db->select('usrs.registered_email_id as email, pers.first_name, pers.last_name, pers.gender');

        $this->db->from('tms_users usrs');

        $this->db->join('tms_users_pers pers', 'pers.user_id=usrs.user_id');

        $this->db->where('usrs.user_id', $user_id);

        return $this->db->get()->row();
    }

    /**

     * only for add new enrol

     */
    public function get_trainee_classes($tenant_id, $course, $trainee_id, $taxcode_id) {

        if ($trainee_id) {

            $class_not_in = $this->db->select('class_id')->from('class_enrol')->where('user_id', $trainee_id)->where('enrol_status !=', 'RESHLD')->get()->result_array();
        }

        if ($taxcode_id) {

            $class_not_in = $this->db->select('class_id')->from('class_enrol')->where('user_id', $taxcode_id)->where('enrol_status !=', 'RESHLD')->get()->result_array();
        }

        $class_arr = array();

        foreach ($class_not_in as $row) {

            $class_arr[] = $row['class_id'];
        }

        $cur_date = date('Y-m-d');

        $this->db->select('cc.total_seats, cc.class_id, cc.class_name,cc.lock_status, cc.course_id, cc.class_pymnt_enrol');
        $this->db->from('course_class cc');

        if (($this->user->role_id == 'SLEXEC')) {
            $this->db->join("course  crse", "crse.course_id = cc.course_id AND crse.tenant_id = cc.tenant_id", "left");
            $this->db->join("course_sales_exec  sales", "sales.course_id = crse.course_id AND sales.tenant_id = crse.tenant_id", "left");
        }
        $this->db->where('cc.tenant_id', $tenant_id);

        if (!empty($course)) {

            $this->db->where('cc.course_id', $course);
        }

        if ($trainee_id && !empty($class_arr)) {

            $this->db->where_not_in('cc.class_id', $class_arr);
        }

        if ($taxcode_id && !empty($class_arr)) {

            $this->db->where_not_in('cc.class_id', $class_arr);
        }

        $this->db->where('cc.class_status !=', 'INACTIV');

        if ($this->user->role_id == 'SLEXEC') {
            $this->db->like('sales.user_id', $this->user->user_id, 'both');
//            if (!empty($course)){
//             $this->db->like('sales.user_id', $this->user->user_id, 'both');
//            }
//            else{
//             $this->db->like('cc.sales_executive', $this->user->user_id, 'both');
//            }
        }
        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",classroom_trainer) !=", 0);
        }

        $this->db->order_by("DATE(cc.class_start_datetime)", "DESC");

        return $this->db->group_by('cc.class_id')->get()->result_object();
    }

    /**

     * get all course with course ids

     */
    public function get_course_by_courseid($course_ids) {

        $this->db->select('c.*');

        $this->db->from('course c');

        if (!empty($course_ids)) {

            $this->db->where_in('c.course_id', $course_ids);
        }

        $this->db->group_by('c.course_id');

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->join('course_sales_exec cse', 'cse.tenant_id = c.tenant_id AND cse.course_id = c.course_id');

            $this->db->where('cse.user_id', $this->user->user_id);
        }
        $this->db->where('c.crse_status', 'ACTIVE'); // added by shubhranshu prevent de-activate course on 6/12/2018
        $query = $this->db->get();



        return $query->result();
    }

    /**

     * 

     */
    public function check_userenroll($user_id, $class) {

        $exists = $this->db->select('class_id')->get_where('class_enrol', array('class_id' => $class, 'user_id' => $user_id), 1)->num_rows();

        if ($exists) {

            return FALSE;
        }

        return TRUE;
    }

    /**

     * 

     */
    public function get_alluser($tenant_id, $username, $taxcode) {

        $username = trim($username);

        $taxcode = trim($taxcode);

        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code')
                ->from('tms_users tu')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id')
                ->where('tu.tenant_id', $tenant_id)
                ->where('tu.account_status', 'ACTIVE')
                ->where('tu.account_type', 'TRAINE');

        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        if ($this->user->role_id == 'SLEXEC') {

            if (!empty($taxcode)) {

                $like = " and tu.tax_code like '%" . $taxcode . "%'";
            } elseif (!empty($username)) {

                $like = " and tup.first_name like '%" . $username . "%'";
            }

            $this->traineelist_querychange();

            $this->db->get();

            $query1 = $this->db->last_query();

            $this->db->query("select tup.user_id, tup.first_name, tup.last_name, tu.tax_code "
                    . "from tms_users_pers tup , tms_users tu where tup.user_id=tu.user_id and "
                    . "tu.account_type='TRAINE' and tu.account_status = 'ACTIVE' and tu.tenant_id='" . $tenant_id . "' "
                    . "and tu.created_by=" . $this->user->user_id . $like . " group by tup.user_id");

            $query2 = $this->db->last_query();

            $query = $this->db->query("(" . $query1 . ") UNION (" . $query2 . ") limit 200");

            return $query->result_object();
        } else {

            $this->db->group_by('tup.user_id');

            $this->db->limit(200);

            return $this->db->get()->result_object();
        }
    }

    /**

     * Role based access for salesexec

     */
    private function traineelist_querychange() {

        $this->db->join('class_enrol ce', 'ce.tenant_id = tup.tenant_id AND ce.user_id = tup.user_id');

        $this->db->where('ce.sales_executive_id', $this->user->user_id);

        $this->db->group_by('ce.user_id');
    }

    /**

     * Used by Class Trainee List View Page

     * @param type $tenant_id

     * @param type $limit

     * @param type $offset

     * @param type $sort_by

     * @param type $sort_order

     * @param type $course_id

     * @param type $class_id

     * @param type $class_status

     * @param type $search_select

     * @param type $taxcode_id

     * @param type $trainee_id

     * @return type

     */
    public function list_all_classtrainee_by_tenant_id($tenant_id, $limit = NULL, $offset = NULL, $sort_by = 'ce.pymnt_due_id', $sort_order = 'DESC', $course_id = '', $class_id = '', $class_status = '', $search_select, $taxcode_id = '', $trainee_id = '', $company_id = 0, $eid = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';
        }

        $cur_date = date('Y-m-d');

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }
        $query2 = $this->list_all_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $limit, $offset, $sort_by, $sort_order, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id, $eid);

        //$this->db->select('cc.*, c.*, ce.*, tu.*, tup.*, tf.feedback_answer, cc.class_status as cc_class_status');
        $this->db->select('c.course_id , c.crse_name, c.tpg_crse,
 cc . class_id, cc. class_name, cc.class_start_datetime,cc.class_end_datetime, cc.certi_coll_date,cc . class_status  as cc_class_status, 
 ce . pymnt_due_id ,ce.enrolment_type, ce.enrolment_mode,ce.referral_details,ce.eid_number, ce.company_id, ce.certificate_coll_on, ce.payment_status,  
 tf.feedback_question_id,tf.feedback_question_id, tf.feedback_answer,
tu . user_id ,tu.tenant_id, tu. account_type, tu.tax_code, tu.account_status,
tup . first_name , tup . last_name, due.att_status, due.total_amount_due,due.subsidy_amount, ce.tg_number,ce.eid_number, ce.sales_executive_id, c.reference_num, c.external_reference_number, cc.tpg_course_run_id, due.class_fees, due.discount_rate, ce.tpg_enrolment_status');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id');

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id=due.pymnt_due_id and ce.user_id = due.user_id');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->join('trainer_feedback tf', 'tf.tenant_id=ce.tenant_id and tf.course_id=ce.course_id and tf.class_id=ce.class_id and tf.user_id=ce.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($eid)) {

            $this->db->where('ce.eid_number', $eid);
        }
        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;
            }

            $this->db->where_not_in('cc.class_status', 'INACTIV');
        }

        if ($user_id) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }


        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }
        //echo $this->db->last_query();  exit;
        //$query = $this->db->get();   
        //$query1 = $this->db->last_query();  exit;
        // $query1 = $this->db->return_query_clear(); //commented by shubhranshu
        $query1 = $this->db->get_compiled_select();  ///added by shubhranshu
        $query1 = str_replace('`', " ", $query1);


        if ($sort_by) {

            $sort_by_arr = explode('.', $sort_by);

            $union_sort_by = end($sort_by_arr);
        } else {

            $union_sort_by = 'class_id';
        }

        if ($limit == $offset) {

            $union_limit = "LIMIT $offset";
        } else if ($limit > 0) {

            $limitvalue = $offset - $limit;

            $union_limit = "LIMIT $limitvalue, $limit";
        }

        $query3 = $this->db->query("(" . $query1 . ") UNION (" . $query2 . ") order by $union_sort_by $sort_order $union_limit");

        return $query3->result_array();
    }

    /* List of public poratl enrolled trainee skm start */

    public function online_list_classtrainee_by_tenant_id($tenant_id, $limit = NULL, $offset = NULL, $sort_by = 'ce.pymnt_due_id', $sort_order = 'DESC', $course_id = '', $class_id = '', $class_status = '', $search_select, $taxcode_id = '', $trainee_id = '', $company_id = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';
        }

        $cur_date = date('Y-m-d');

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }
//        $query2 = $this->list_all_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $limit, $offset, $sort_by, $sort_order, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);
        $query2 = $this->list_all_online_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $limit, $offset, $sort_by, $sort_order, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);
        //$this->db->select('cc.*, c.*, ce.*, tu.*, tup.*, tf.feedback_answer, cc.class_status as cc_class_status');

        $this->db->select('c.course_id , c.crse_name, 
 cc . class_id, cc. class_name, cc.class_start_datetime,cc.class_end_datetime, cc.certi_coll_date,cc . class_status  as cc_class_status, 
 ce . pymnt_due_id, ce.enrolment_mode, ce.enrolment_type, ce.company_id,ce.friend_id,ce.referral_details, ce.certificate_coll_on, ce.payment_status,  
 tf.feedback_question_id,tf.feedback_question_id, tf.feedback_answer,
tu . user_id ,tu.tenant_id, tu. account_type, tu.tax_code, tu.account_status,
tup . first_name , tup . last_name, due.total_amount_due,due.subsidy_amount, ce.tg_number, ce.sales_executive_id');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id');

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id=due.pymnt_due_id and ce.user_id = due.user_id');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->where('ce.enrolment_type', 'PUBLIC');

        $this->db->join('trainer_feedback tf', 'tf.tenant_id=ce.tenant_id and tf.course_id=ce.course_id and tf.class_id=ce.class_id and tf.user_id=ce.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;
            }

            $this->db->where_not_in('cc.class_status', 'INACTIV');
        }

        if ($user_id) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }



        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

//        $query = $this->db->get();   
//        echo $query1 = $this->db->last_query(); 
//        exit();

        $query1 = $this->db->get_compiled_select(); //changed by shubhranshu since the previous funtion is deprecated
        $query1 = str_replace('`', " ", $query1);


        if ($sort_by) {

            $sort_by_arr = explode('.', $sort_by);

            $union_sort_by = end($sort_by_arr);
        } else {

            $union_sort_by = 'class_id';
        }

        if ($limit == $offset) {

            $union_limit = "LIMIT $offset";
        } else if ($limit > 0) {

            $limitvalue = $offset - $limit;

            $union_limit = "LIMIT $limitvalue, $limit";
        }

        $query3 = $this->db->query("(" . $query1 . ") UNION (" . $query2 . ") order by $union_sort_by $sort_order $union_limit");

        return $query3->result_array();
    }

    /* END */

    //Get enrolment mode and company ID of class trainee
    
    public function get_class_trainee_dat($tenant_id, $course_id, $class_id, $user_id) {
        
        $this->db->select('c.course_id , c.crse_name, c.tpg_crse,
 cc . class_id, cc. class_name, cc.class_start_datetime,cc.class_end_datetime, cc.certi_coll_date,cc . class_status  as cc_class_status, 
 ce . pymnt_due_id ,ce.enrolment_type, ce.enrolment_mode,ce.referral_details,ce.eid_number, ce.company_id, ce.certificate_coll_on, ce.payment_status,  
 tf.feedback_question_id,tf.feedback_question_id, tf.feedback_answer,
tu . user_id ,tu.tenant_id, tu. account_type, tu.tax_code, tu.account_status,
tup . first_name , tup . last_name, due.att_status, due.total_amount_due,due.subsidy_amount, ce.tg_number,ce.eid_number, ce.sales_executive_id, c.reference_num, c.external_reference_number, cc.tpg_course_run_id, due.class_fees, due.discount_rate, ce.tpg_enrolment_status');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id');

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id=due.pymnt_due_id and ce.user_id = due.user_id');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->join('trainer_feedback tf', 'tf.tenant_id=ce.tenant_id and tf.course_id=ce.course_id and tf.class_id=ce.class_id and tf.user_id=ce.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));
                
        $this->db->where('cc.course_id', $course_id);           
        $this->db->where('cc.class_id', $class_id);
        $this->db->where('ce.user_id', $user_id);
        
        $results = $this->db->get()->result_array();

        return $results[0];
    }
    
    
    /* This method gets the friend details skm start */

    public function get_friend_details($tenant_id, $course_id, $class_id, $user_id, $friend_id) {

        $this->db->select('tu.user_id,tu.registered_email_id,tup.first_name,tup.contact_number');
        $this->db->from('class_enrol ce');
        $this->db->join('tms_users tu', 'tu.user_id=ce.friend_id');
        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
        $this->db->where('ce.user_id', $user_id);
        $this->db->where('ce.friend_id', $friend_id);
        $this->db->where('ce.course_id', $course_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.tenant_id', $tenant_id);
//        $this->db->get();
        $sql = $this->db->get()->row_array();
//        print_r($sql); echo "<br/>";
        $this->db->select('company_name');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('company_master cm', 'cm.company_id = tcu.company_id');
        $this->db->where('tcu.user_id', $friend_id);
        $this->db->where('tcu.tenant_id', $tenant_id);
        $qry = $this->db->get();
//        echo $this->db->last_query();
//        echo $qry->num_rows();
        if ($qry->num_rows() > 0) {
            $qry = $qry->row_array();
            $sql['company_name'] = $qry['company_name'];
        }

//        echo $this->db->last_query();
//        print_r($sql); echo"<br/>";
        $sql = json_encode($sql);
//        print_r($sql);exit();
        return $sql;
    }

    //end

    public function get_all_online_classtrainee_count_by_tenant_id($tenant_id, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';

            ;
        }

        $cur_date = date('Y-m-d');

        if (empty($tenant_id)) {

            return 0;
        }

        $this->db->select('ce.user_id');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id');

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id=due.pymnt_due_id and ce.user_id = due.user_id');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        $this->db->where('ce.enrolment_type', 'PUBLIC');

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;



                default:

                    break;
            }
        }

        if (!empty($user_id)) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }

        $query = $this->db->get();

        $query1 = $query->num_rows();

        $query2 = $this->get_all_pymt_not_required_onlineclasstrainee_by_tenant_id($tenant_id, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);

        $query3 = $query1 + $query2;

        return $query3;
    }

    public function get_all_pymt_not_required_onlineclasstrainee_by_tenant_id($tenant_id, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';

            ;
        }

        $cur_date = date('Y-m-d');

        if (empty($tenant_id)) {

            return 0;
        }

        $this->db->select('ce.user_id');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id and ce.payment_status="PYNOTREQD" ');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->where('ce.enrolment_type', 'PUBLIC');

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;



                default:

                    break;
            }
        }

        if (!empty($user_id)) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    /* get all class trainee from public portal whos payment not required skm start */

    public function list_all_online_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $limit = NULL, $offset = NULL, $sort_by = 'ce.pymnt_due_id', $sort_order = 'DESC', $course_id = '', $class_id = '', $class_status = '', $search_select, $taxcode_id = '', $trainee_id = '', $company_id = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';
        }

        $cur_date = date('Y-m-d');

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }

        //$this->db->select('cc.*, c.*, ce.*, tu.*, tup.*, tf.feedback_answer, cc.class_status as cc_class_status');
        $this->db->select('c.course_id , c.crse_name,
 cc . class_id, cc. class_name, cc.class_start_datetime,cc.class_end_datetime, cc.certi_coll_date,cc . class_status  as cc_class_status, 
 ce . pymnt_due_id ,ce.enrolment_mode, ce.enrolment_type, ce.company_id,ce.friend_id,ce.referral_details, ce.certificate_coll_on, ce.payment_status,  
 tf.feedback_question_id,tf.feedback_question_id, tf.feedback_answer,
tu . user_id ,tu.tenant_id, tu. account_type, tu.tax_code, tu.account_status,
tup . first_name , tup . last_name, due.total_amount_due,due.subsidy_amount, ce.tg_number,cc.sales_executive');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id and ce.payment_status="PYNOTREQD" ');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->where('ce.enrolment_type', 'PUBLIC');

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id = due.pymnt_due_id', 'LEFT');
        $this->db->join('trainer_feedback tf', 'tf.tenant_id=ce.tenant_id and tf.course_id=ce.course_id and tf.class_id=ce.class_id and tf.user_id=ce.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;
            }

            $this->db->where_not_in('cc.class_status', 'INACTIV');
        }

        if ($user_id) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }



        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        //$query = $this->db->get();        

        $query2 = $this->db->get_compiled_select(); //changed the function by shubhranshu 20/09/2019

        $query2 = str_replace('`', " ", $query2);

        $query2 = str_replace('( course_class  cc)', 'course_class cc', $query2);

        return $query2;
    }

    /* Fetch all trainee name which enrolled from  public portal skm start */

    public function get_online_user_with_class_course($tenant_id, $username, $taxcode, $class, $course) {

        $username = trim($username);

        $taxcode = trim($taxcode);

        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id');

        $this->db->where('tu.tenant_id', $tenant_id);

//        if ($this->user->role_id == 'TRAINER') {
//
//            $this->db->join('course_class ccl', 'ccl.tenant_id = tup.tenant_id '
//                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');
//
//            $trainer_where = 'AND ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id and ccl.tenant_id=ce.tenant_id';
//        }
//
//        if ($this->user->role_id == 'CRSEMGR') {
//
//            $this->db->join('course c', 'c.tenant_id = tup.tenant_id '
//                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');
//
//            $trainer_where = 'AND c.course_id = ce.course_id and c.tenant_id=ce.tenant_id';
//        }

        $this->db->join('class_enrol ce', 'ce.user_id=tup.user_id ' . $trainer_where);

        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));

        $this->db->where('ce.enrolment_type', 'PUBLIC');


        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        if (!empty($course)) {

            $this->db->where('ce.course_id', $course);
        }

        if (!empty($class)) {

            $this->db->where('ce.class_id', $class);
        }


        $this->db->group_by('tup.user_id');

        $this->db->limit(200);

        return $this->db->get()->result_object();
    }

    /* end */

    public function get_total_enrolled_trainee($tenant_id) {
        $this->db->select('*');
        $this->db->from('class_enrol');
        $this->db->where('enrolment_type', 'PUBLIC');
        $this->db->where('tenant_id', $tenant_id);
        $sql = $this->db->get();
        return $sql->num_rows();
    }

    /**

     * Used by Class Trainee - List View Page - Pagination

     * @param type $tenant_id

     * @param type $course_id

     * @param type $class_id

     * @param type $class_status

     * @param type $search_select

     * @param type $taxcode_id

     * @param type $trainee_id

     * @return int

     */
    public function get_all_classtrainee_count_by_tenant_id($tenant_id, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';

            ;
        }

        $cur_date = date('Y-m-d');

        if (empty($tenant_id)) {

            return 0;
        }

        $this->db->select('ce.user_id');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id');

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id=due.pymnt_due_id and ce.user_id = due.user_id');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;



                default:

                    break;
            }
        }

        if (!empty($user_id)) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }

        $query = $this->db->get();

        $query1 = $query->num_rows();

        $query2 = $this->get_all_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);

        $query3 = $query1 + $query2;

        return $query3;
    }

    /* this funcatin get the trainer feedback of previous data for activity log skm start */

    public function get_trainer_feedback($user_id, $course_id, $class_id) {
        $this->db->select('feedback_question_id,feedback_answer');
        $this->db->from('trainer_feedback');
        $this->db->where('user_id', $user_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $sql = $this->db->get();
        if ($sql->num_rows() > 0) {
            $lock = $this->class_lock_status($course_id, $class_id);
            $result['trainer_feedback'] = $sql->result_array();
            $result['details'] = array('user_id' => $user_id, 'course_id' => $course_id, 'class_id' => $class_id, 'class_lock_status' => $lock->lock_status);
//        $res = json_encode($result);
            return $result;
        } else {
            return $sql = 0;
        }
    }

    /* skm end */

    public function class_lock_status($course_id, $class_id) {
        $this->db->select('lock_status');
        $this->db->from('course_class');
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        return $this->db->get()->row();
    }

    /**

     * Used by Class Trainee Search By Taxcode

     * @param type $tenant_id

     * @param type $username

     * @param type $taxcode

     * @param type $class

     * @param type $course

     * @return type

     */
    public function get_alluser_with_class_course($tenant_id, $username, $taxcode, $class, $course) {

        $username = trim($username);

        $taxcode = trim($taxcode);

        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code');

        $this->db->from('tms_users tu');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id');

        $this->db->where('tu.tenant_id', $tenant_id);

        if ($this->user->role_id == 'TRAINER') {

            $this->db->join('course_class ccl', 'ccl.tenant_id = tup.tenant_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',ccl.classroom_trainer)');

            $trainer_where = 'AND ccl.course_id = ce.course_id AND ccl.class_id = ce.class_id and ccl.tenant_id=ce.tenant_id';
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('course c', 'c.tenant_id = tup.tenant_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');

            $trainer_where = 'AND c.course_id = ce.course_id and c.tenant_id=ce.tenant_id';
        }

        $this->db->join('class_enrol ce', 'ce.user_id=tup.user_id ' . $trainer_where);

        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));



        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        if (!empty($course)) {

            $this->db->where('ce.course_id', $course);
        }

        if (!empty($class)) {

            $this->db->where('ce.class_id', $class);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->join('tenant_company_users tcu', 'tcu.user_id = tup.user_id');

            $this->db->where("tcu.company_id", $this->user->company_id);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        $this->db->group_by('tup.user_id');

        $this->db->limit(200);

        return $this->db->get()->result_object();
    }

    /**

     * function to get subsidy , tg_number

     */
    public function get_enrol_payment_due($payid, $user) {

        $this->db->select('epd.subsidy_amount,epd.gst_amount, epd.discount_rate, epd.subsidy_recd_date, epd.class_fees')->from('enrol_pymnt_due epd')
                ->where('epd.pymnt_due_id', $payid)->where('epd.user_id', $user);

        $subsidy_data = $this->db->get()->row();

        $this->db->select('tg_number')->from('class_enrol')->where('pymnt_due_id', $payid)->where('user_id', $user);

        $subsidy_data->tg_number = $this->db->get()->row()->tg_number;

        return $subsidy_data;
    }

    /**

     * This method gets the trainees with no subsidy for a company invoice

     */
    public function get_company_invoice_no_subsidy($invoice_id) {
        // $bind=array(0,NULL);

        $subsidy_amt = NULL;
        $data = $this->db->select('total_inv_amount')
                        ->from('enrol_invoice')->where('invoice_id', $invoice_id)->get()->row(0);

        $total_inv_amount = $data->total_inv_amount;
        if ($total_inv_amount > 0) {
            $query = $this->db->query("SELECT epd.user_id,tu.tax_code FROM (enrol_pymnt_due epd) 
                                JOIN enrol_invoice ei ON ei.pymnt_due_id=epd.pymnt_due_id JOIN tms_users tu ON tu.user_id=epd.user_id WHERE ei.invoice_id = '$invoice_id' 
                                AND (tu.tax_code LIKE 'S%' OR tu.tax_code LIKE 'T%')");
            $users = array();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $users[] = $row->user_id;
                }
            }
            $result = $this->db->select('*');
            $this->db->from('enrol_invoice ei');
            $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id and ce.user_id=epd.user_id');
            $this->db->join('course_class cc', 'cc.class_id=ce.class_id');
            $this->db->join('course c', 'c.course_id=cc.course_id');
            $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');
            $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
            $this->db->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id');
            $this->db->where('ei.invoice_id', $invoice_id);
            $this->db->where_in('epd.user_id', $users);
            $this->db->where('tu.tax_code_type', 'SNG_1');
            $this->db->where("(epd.subsidy_amount=0 or epd.subsidy_amount IS NULL)");
            $this->db->where('epd.att_status', 1);
            $result = $this->db->get()->result();
            return $result;
        } else {
            $query = $this->db->query("SELECT epd.user_id,tu.tax_code FROM (enrol_pymnt_due epd) 
                                JOIN enrol_invoice ei ON ei.pymnt_due_id=epd.pymnt_due_id JOIN tms_users tu ON tu.user_id=epd.user_id WHERE ei.invoice_id = '$invoice_id' 
                                AND (tu.tax_code LIKE 'S%' OR tu.tax_code LIKE 'T%')");
            $users = array();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $users[] = $row->user_id;
                }
            }
            $result = $this->db->select('*');
            $this->db->from('enrol_invoice ei');
            $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id and ce.user_id=epd.user_id');
            $this->db->join('course_class cc', 'cc.class_id=ce.class_id');
            $this->db->join('course c', 'c.course_id=cc.course_id');
            $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');
            $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
            $this->db->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id');
            $this->db->where_in('epd.user_id', $users);

            $this->db->where('ei.invoice_id', $invoice_id);
            $this->db->where("(epd.subsidy_amount=0 or epd.subsidy_amount IS NULL)");
            $result = $this->db->get()->result();
            return $result;
        }
    }

    /**

     * This method gets the trainees with no subsidy for a company invoice

     */
    public function get_company_invoice_foreigner($invoice_id) {
        // $bind=array(0,NULL);

        $subsidy_amt = NULL;
        $data = $this->db->select('total_inv_amount')
                        ->from('enrol_invoice')->where('invoice_id', $invoice_id)->get()->row(0);
        $total_inv_amount = $data->total_inv_amount;
        if ($total_inv_amount > 0) {
            $query = $this->db->query("SELECT epd.user_id,tu.tax_code FROM (enrol_pymnt_due epd) 
                                JOIN enrol_invoice ei ON ei.pymnt_due_id=epd.pymnt_due_id JOIN tms_users tu ON tu.user_id=epd.user_id WHERE ei.invoice_id = '$invoice_id' 
                                AND (tu.tax_code_type!='SNG_1')");
            $users = array();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $users[] = $row->user_id;
                }
            }
            $result = $this->db->select('*');
            $this->db->from('enrol_invoice ei');
            $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id and ce.user_id=epd.user_id');
            $this->db->join('course_class cc', 'cc.class_id=ce.class_id');
            $this->db->join('course c', 'c.course_id=cc.course_id');
            $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');
            $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
            $this->db->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id');
            $this->db->where_in('epd.user_id', $users);
            $this->db->where('ei.invoice_id', $invoice_id);
            $this->db->where("(epd.subsidy_amount=0 or epd.subsidy_amount IS NULL)");
            $this->db->where('epd.att_status', 1);


            $result = $this->db->get()->result();

            return $result;
        } else {
            $query = $this->db->query("SELECT epd.user_id,tu.tax_code FROM (enrol_pymnt_due epd) 
                                JOIN enrol_invoice ei ON ei.pymnt_due_id=epd.pymnt_due_id JOIN tms_users tu ON tu.user_id=epd.user_id WHERE ei.invoice_id = '$invoice_id' 
                                AND  tu.tax_code_type!='SNG_1'");
            $users = array();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $users[] = $row->user_id;
                }
            }
            $result = $this->db->select('*');
            $this->db->from('enrol_invoice ei');
            $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id and ce.user_id=epd.user_id');
            $this->db->join('course_class cc', 'cc.class_id=ce.class_id');
            $this->db->join('course c', 'c.course_id=cc.course_id');
            $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');
            $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');
            $this->db->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id');
            $this->db->where_in('epd.user_id', $users);
            $this->db->where('ei.invoice_id', $invoice_id);
            $this->db->where("(epd.subsidy_amount=0 or epd.subsidy_amount IS NULL)");
            $result = $this->db->get()->result();
            return $result;
        }
    }

    /**

     * This method gets the trainees with subsidy for a company invoice

     */
    public function get_company_invoice_subsidy($invoice_id) {

        $result = $this->db->select('*')->from('enrol_invoice ei')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ei.pymnt_due_id')
                ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id and ce.user_id=epd.user_id')
                ->join('course_class cc', 'cc.class_id=ce.class_id')
                ->join('course c', 'c.course_id=cc.course_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('tenant_master tm', 'tm.tenant_id=ce.tenant_id')->where('ei.invoice_id', $invoice_id)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where('epd.subsidy_amount !=', 0)
                ->where('epd.att_status', 1);

        $result = $this->db->get()->result();

        return $result;
    }

    /**

     * this function to get the class status by its start and end date

     */
    public function get_class_statustext($cid) {

        $cur_date = time();



        $data = $this->db->select('class_status,class_start_datetime as start,class_end_datetime as end')
                        ->from('course_class')->where('class_id', $cid)->get()->row(0);

        $start = strtotime($data->start);

        $end = strtotime($data->end);

        if ($data->class_status == 'INACTIV') {

            return 'INACTIV';
        } elseif ($start > $cur_date) {

            return 'YTOSTRT';
        } elseif ($end < $cur_date) {

            return 'COMPLTD';
        } else {

            return 'IN_PROG';
        }
    }

    /**

     * FUNCTION TO GET TOTAL_ENROLL_INVOICE

     */
    public function total_enrol_invoice() {

        $result = $this->db->select('count(*) as totalrow')->from('enrol_invoice')->get()->row()->totalrow;

        return $result;
    }

    /*

     * This method is for Changing Individual Enrolment Type

     */

    public function get_enrolment_by_tax_code($tax_code) {

        $this->db->select("enrol.course_id, enrol.class_id, enrol.user_id, enrol.pymnt_due_id, ccl.class_name,"
                . " ccl.class_start_datetime, cc.crse_name");

        $this->db->from("class_enrol enrol");

        $this->db->join("tms_users usr", "usr.user_id=enrol.user_id AND usr.tenant_id=enrol.tenant_id");

        $this->db->join("course_class ccl", "enrol.course_id = ccl.course_id AND enrol.class_id=ccl.class_id");

        $this->db->join("course cc", "cc.course_id=ccl.course_id");

        $this->db->where("usr.tax_code", $tax_code);

        $this->db->where("enrol.enrolment_mode", "SELF");

        $this->db->where("enrol.payment_status !=", "PAID");

        $result = $this->db->get();

        return $result->result();
    }

    /*

     * This Method user for getting company name of the user in enrollment change.

     */

    public function get_user_company_name($user_id) {

        if (empty($user_id)) {

            return FALSE;
        } else {

            $this->db->select("cm.company_id, cm.company_name, pers.first_name,pers.last_name");

            $this->db->from("company_master cm");

            $this->db->join("tenant_company_users cusr", "cusr.company_id=cm.company_id");

            $this->db->join("tms_users_pers pers", "pers.user_id=cusr.user_id AND pers.tenant_id=cusr.tenant_id");

            $this->db->where("cusr.user_id", $user_id);

            $result = $this->db->get();

            return $result->row();
        }
    }

    /**

     * Returns back a data set if there is no payment received for the company invoice

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $tenant_id

     * @param type $opt_type

     * @param type $payid

     * @param type $user_id

     * @param type $record

     * @return boolean

     */
    public function get_enroll_invoice_details($course_id, $class_id, $company_id, $tenant_id, $opt_type = 'change', $payid, $user_id = NULL, $record = "first") {

        if (empty($course_id) || empty($class_id) || empty($tenant_id)) {

            return FALSE;
        } else {

            if ($opt_type == "change") {

                $result = $this->check_invoice_present($tenant_id, $user_id, $course_id, $class_id, $record);
            } else if ($opt_type == "remvind") {
                $result_set = $this->enrol_invoice_details_ind($tenant_id, $course_id, $class_id, $payid, $user_id);

                $result = $result_set->row();
            } else {
                $result_set = $this->enrol_invoice_details($tenant_id, $course_id, $class_id, $company_id, $payid);
                $result = $result_set->row();
            }

            return $result;
        }
    }

    /**

     * This method return invoice detais 

     * @param type $tenant_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $query

     * @return type

     */
    private function enrol_invoice_details($tenant_id, $course_id, $class_id, $company_id, $pay_id) {



        if ($pay_id != "" && $pay_id != 0)
            $query = "and enrol.pymnt_due_id='$pay_id'";



        $str_query = "Select inv.*, DATE(inv.inv_date) as inv_date,"
                . "due.discount_type, due.discount_rate, due.subsidy_amount, cc.gst_on_off, 

                    enrol.payment_status

                    from enrol_invoice inv, class_enrol enrol, enrol_pymnt_due due, course cc

                    where enrol.pymnt_due_id = inv.pymnt_due_id and due.pymnt_due_id = enrol.pymnt_due_id and

                    enrol.enrolment_mode = 'COMPSPON' and cc.course_id=enrol.course_id and

                    enrol.course_id ='" . $course_id . "' and enrol.class_id = '" . $class_id . "' "
                . "and enrol.tenant_id='" . $tenant_id . "' and inv.company_id='" . $company_id . "' " . $query;

        return $this->db->query($str_query);
    }

    /*
     * get invice details to remove enrollment
     */

    private function enrol_invoice_details_ind($tenant_id, $course_id, $class_id, $pay_id, $user_id) {
        if ($pay_id != "" && $pay_id != 0)
            $query = "and enrol.pymnt_due_id='$pay_id'";

        $str_query = "Select inv.*,enrol.*,due.*, DATE(inv.inv_date) as inv_date,"
                . " cc.gst_on_off,cc.crse_name,cc.course_id as courseId,cl.class_name,cl.class_id as classId,tu.user_name,tu.tax_code,tu.user_id,

                    enrol.payment_status

                     from enrol_invoice inv, class_enrol enrol, enrol_pymnt_due due, course cc,course_class cl,tms_users tu

                   where enrol.pymnt_due_id = inv.pymnt_due_id and due.pymnt_due_id = enrol.pymnt_due_id and

                    enrol.enrolment_mode = 'SELF' and cc.course_id=enrol.course_id and cc.course_id='" . $course_id . "' "
                . "and cl.class_id='" . $class_id . "' and tu.user_id='" . $user_id . "'

                   and enrol.course_id ='" . $course_id . "' and enrol.class_id = '" . $class_id . "' "
                . "and enrol.tenant_id='" . $tenant_id . "' and enrol.user_id='" . $user_id . "' " . $query;

        $this->db->query($str_query);
        return $this->db->query($str_query);
    }

    /**

     * for checking invoice_present in Change individual enrollment to company enrollment 

     * @param type $tenant_id

     * @param type $user_id

     * @param type $course_id

     * @param type $class_id

     * @return string

     */
    public function check_invoice_present($tenant_id, $user_id, $course_id, $class_id, $record) {

        $stre_qry = "select cm.company_id,cm.company_name from tenant_company_users cusr,company_master cm where cusr.user_id = '" . $user_id . "' "
                . "and cusr.user_acct_status = 'ACTIVE' and cusr.tenant_id='" . $tenant_id . "' "
                . "and cm.company_id= cusr.company_id";



        $result_set = $this->db->query($stre_qry);

        if ($result_set->num_rows() == 0) {

            return array('msg_status' => "not_company_user");
        } else {

            $company_id = $result_set->row('company_id');

            $company_name = $result_set->row('company_name');

            $payment_due_id = $this->get_latest_invoice($tenant_id, $course_id, $class_id, $company_id, $record);

            if ($payment_due_id != 0) {

                $result_set = $this->enrol_invoice_details($tenant_id, $course_id, $class_id, $company_id, $payment_due_id);
            } else {

                return array('msg_status' => "not_found", 'company_name' => $company_name, 'company_id' => $company_id);
            }

            if ($result_set->num_rows() == 0) {

                return array('msg_status' => "not_found", 'company_name' => $company_name, 'company_id' => $company_id);
            } else {

                $status_flag = TRUE;

                foreach ($result_set->result() as $key => $value) {

                    if ($value->payment_status != 'NOTPAID') {

                        $status_flag = FALSE;
                    }
                }

                if ($status_flag) {

                    $result_set->row()->company_name = $company_name;

                    return $result_set->row();
                } else {

                    return array('msg_status' => "cannot_change");
                }
            }
        }
    }

    /**

     *  This method gets the latest invoice for a company+course+class combination

     * @param type $tenant_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $record

     * @return type

     */
    public function get_latest_invoice($tenant_id, $course_id, $class_id, $company_id, $record) {

        $payment_due_id = 0;

        $str_query = "SELECT  pymnt_due_id FROM class_enrol WHERE  tenant_id='" . $tenant_id . "' AND"
                . " course_id  = '" . $course_id . "'  AND class_id = '" . $class_id . "' AND company_id = '" . $company_id . "'"
                . "AND enrolment_mode = 'COMPSPON' order by pymnt_due_id  desc limit 0,2;";

        $result_set = $this->db->query($str_query)->result();

        if ($result_set[0]->pymnt_due_id && $record == "first") {

            $payment_due_id = $result_set[0]->pymnt_due_id;
        } else if ($result_set[1]->pymnt_due_id && $record == "second") {

            $payment_due_id = $result_set[1]->pymnt_due_id;
        }

        return $payment_due_id;
    }

    /*

     * This Method is for getting all trainees details used in Changing Individual Enrolment Type.

     */

    public function get_all_trainees_details($course, $class, $company_id, $pymnt_due_id) {

        if (empty($course) || empty($class) || empty($company_id)) {

            return FALSE;
        } else {

            $this->db->select("usr.tax_code, pers.first_name, pers.last_name, usr.user_id");

            $this->db->from("class_enrol enrol");

            $this->db->join("tms_users usr", "enrol.user_id = usr.user_id");

            $this->db->join("tms_users_pers pers", "usr.user_id=pers.user_id AND usr.tenant_id = pers.tenant_id");

            $this->db->where("enrol.course_id", $course);

            $this->db->where("enrol.class_id", $class);

            $this->db->where("enrol.company_id", $company_id);

            $this->db->where("enrol.pymnt_due_id", $pymnt_due_id);

            $this->db->where("usr.account_type", 'TRAINE');

            $result = $this->db->get();

            return $result->result();
        }
    }

    /*

     * For Change Individual Enrolment Type dropdown on 04 Dec 2014.

     */

    public function get_individual_enrol_trainees($tenant_id, $query = '') {

        if (!empty($query)) {

            $extra = " AND usr.tax_code LIKE '%$query%' ";
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $crsemgr_where = " AND FIND_IN_SET(" . $this->user->user_id . ",crs.crse_manager) !=0";
        }

        $str_query1 = "select usr.user_id, usr.tax_code, pers.first_name,pers.last_name, crs.crse_name, "
                . "crs.course_id, cls.class_name,cls.lock_status,cls.class_id,date(cls.class_start_datetime) as class_start_datetime, "
                . "date(cls.class_end_datetime) as class_end_datetime, enrol.pymnt_due_id, enrol.enrolled_on, "
                . "enrol.company_id,cls.class_fees "
                . "from class_enrol enrol,tms_users usr,tms_users_pers pers,course crs,course_class cls "
                . "where enrol.enrolment_mode = 'SELF' and enrol.payment_status != 'PAID' and enrol.payment_status !='PYNOTREQD'"
                . "AND enrol.user_id=usr.user_id AND usr.user_id=pers.user_id "
                . "AND enrol.class_id=cls.class_id AND enrol.course_id=cls.course_id "
                . "AND cls.course_id=crs.course_id AND enrol.tenant_id='" . $tenant_id . "'" . $crsemgr_where . " $extra ORDER BY date(cls.class_start_datetime)";

        $result_set = $this->db->query($str_query1);

        return $result_set->result();
    }

    /////added by shubhranshu for proper calculation
    public function get_individual_enrol_trainees_subsidy($tenant_id, $pay_id, $user_id) {
        $str_query1 = "select subsidy_amount from enrol_pymnt_due where pymnt_due_id='$pay_id' and user_id='$user_id'";

        $result_set = $this->db->query($str_query1);

        return $result_set->result();
    }

    public function get_remv_individual_enrol_trainees($tenant_id, $query = '') {

        if (!empty($query)) {

            $extra = " AND usr.tax_code LIKE '%$query%' ";
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $crsemgr_where = " AND FIND_IN_SET(" . $this->user->user_id . ",crs.crse_manager) !=0";
        }

        $str_query1 = "select usr.user_id, usr.tax_code, pers.first_name,pers.last_name, crs.crse_name, "
                . "crs.course_id, cls.class_name, cls.class_id,date(cls.class_start_datetime) as class_start_datetime, "
                . "date(cls.class_end_datetime) as class_end_datetime, enrol.pymnt_due_id, enrol.enrolled_on, "
                . "enrol.company_id,cls.class_fees "
                . "from class_enrol enrol,tms_users usr,tms_users_pers pers,course crs,course_class cls "
                . "where enrol.enrolment_mode = 'SELF' and enrol.payment_status != 'PAID' and enrol.payment_status !='PYNOTREQD'"
                . "AND enrol.user_id=usr.user_id AND usr.user_id=pers.user_id "
                . "AND enrol.class_id=cls.class_id AND enrol.course_id=cls.course_id "
                . "AND cls.course_id=crs.course_id AND enrol.tenant_id='" . $tenant_id . "'" . $crsemgr_where . " $extra ORDER BY date(cls.class_start_datetime)";

        $result_set = $this->db->query($str_query1);

        return $result_set->result();
    }

    /**

     * This Method get all company not paid invoices.

     * @param type $tenant_id

     * @return type

     */
    public function get_company_not_paid_invoice($tenant_id, $query = '') {
        $extra = '';
        if (!empty($query)) {

            $extra = " AND comp.company_name LIKE '%$query%' ";
        }
        $crsemgr_where = '';
        if ($this->user->role_id == 'CRSEMGR') {

            $crsemgr_where = " AND FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=0";
        }

        $result_set = $this->db->query("SELECT inv.`invoice_id`, inv.`pymnt_due_id`, enrl.payment_status, inv.inv_date, inv.company_id, 

         enrl.course_id, enrl.class_id, crse.crse_name,

        cls.class_name, cls.class_start_datetime,cls.lock_status,cls.class_end_datetime, cls.class_fees, comp.company_name

        FROM enrol_invoice inv, class_enrol enrl, course crse, course_class cls, company_master comp

        WHERE 

        inv.`pymnt_due_id` = enrl.`pymnt_due_id`  AND

        enrl.class_id = cls.class_id AND

        enrl.course_id = cls.course_id AND

        cls.course_id = crse.course_id AND

        inv.company_id = comp.company_id AND

        enrl.enrolment_mode = 'COMPSPON' AND

        enrl.tenant_id = '$tenant_id'" . $crsemgr_where . "

        $extra

        ORDER BY date(cls.class_start_datetime)");

        $result = $result_set->result();

        foreach ($result as $item) {

            $invoice_array[$item->invoice_id][] = $item;
        }

        foreach ($invoice_array as $invoices) {

            $flag = TRUE;

            $temp = array();

            foreach ($invoices as $invoice) {

                $temp1 = $invoice;

                $temp[] = $invoice->payment_status;
            }

            if (in_array('PAID', $temp)) {

                $flag = FALSE;
            } else if (in_array('PARTPAID', $temp)) {

                $flag = FALSE;
            }

            if ($flag) {

                $final[] = $temp1;
            }
        }

        return $final;
    }

    /* autocpmlete company invoice to move traine from one invoice to other invoice
     * added by pritam
     * 
     */

    public function get_company_not_paid_invoice1($tenant_id, $query = '', $company_id, $course_id, $class_id) {

        if (!empty($query)) {

            $extra = " AND comp.company_name LIKE '%$query%' ";
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $crsemgr_where = " AND FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=0";
        }

        $result_set = $this->db->query("SELECT inv.`invoice_id`, inv.`pymnt_due_id`, enrl.payment_status, inv.inv_date, inv.company_id, 

         enrl.course_id, enrl.class_id, crse.crse_name,

        cls.class_name, cls.class_start_datetime,cls.lock_status, cls.class_end_datetime, cls.class_fees, comp.company_name

        FROM enrol_invoice inv, class_enrol enrl, course crse, course_class cls, company_master comp

        WHERE 

        inv.`pymnt_due_id` = enrl.`pymnt_due_id`  AND

        enrl.class_id = cls.class_id AND

        enrl.course_id = cls.course_id AND

        cls.course_id = crse.course_id AND

        inv.company_id = comp.company_id AND

        enrl.enrolment_mode = 'COMPSPON' AND
        
        enrl.company_id!='$company_id' AND
        

        enrl.tenant_id = '$tenant_id'" . $crsemgr_where . "

        $extra

        ORDER BY date(cls.class_start_datetime)");

        $result = $result_set->result();

        foreach ($result as $item) {

            $invoice_array[$item->invoice_id][] = $item;
        }

        foreach ($invoice_array as $invoices) {

            $flag = TRUE;

            $temp = array();

            foreach ($invoices as $invoice) {

                $temp1 = $invoice;

                $temp[] = $invoice->payment_status;
            }

            if (in_array('PAID', $temp)) {

                $flag = FALSE;
            } else if (in_array('PARTPAID', $temp)) {

                $flag = FALSE;
            }

            if ($flag) {

                $final[] = $temp1;
            }
        }

        return $final;
    }

    /**

     * This Method holds the workflow to merge an individual invoice with 

     * an existing company invoice

     * @param type $args

     * @return type

     */
    public function merge_invoice($args) {

        $status = FALSE;

        if (!empty($args)) {
            $data = $this->db->select('user_id')
                            ->from('class_enrol')
                            ->where('pymnt_due_id', $args['individual_payment_due_id'])
                            ->get()->row(0);
            $user_id = $data->user_id;
            //// added by shubhranshu for update the sfc claim id to be zero
            $sfc_data = array('sfc_claim_id' => NULL);
            $this->db->where('pymnt_due_id', $args['individual_payment_due_id']);
            $this->db->where('user_id', $user_id);
            $this->db->update('class_enrol', $sfc_data);

            $check_attendance = $this->check_attendance_row($args["tenant_id"], $args['course_id'], $args['class_id']);
            if ($check_attendance > 0) {
                $check_attendance_trainee = $this->check_attendance_trainee($args["tenant_id"], $args['course_id'], $args['class_id'], $user_id);
                if ($check_attendance_trainee > 0) {
                    $training_score = 'C';
                    $att_status = 1;
                } else {
                    $training_score = 'ABS';
                    $att_status = 0;
                }
            }

            $data1 = $this->get_current_individule_invoice_data($args['individual_payment_due_id']);
            $data2 = $this->get_current_invoice_data($args['comp_payment_due_id']);
            $due_to = 'Change individual enrollment to company enrollment';
            $status = $this->enrol_invoice_view($args['individual_payment_due_id'], $data1, $args['logged_in_user_id'], $due_to);
            $status = $this->enrol_invoice_view($args['comp_payment_due_id'], $data2, $args['logged_in_user_id'], $due_to);

            $status = $this->update_classenrol_audittrail($args["tenant_id"], $args["individual_payment_due_id"], $args["individual_user_id"], $args["course_id"], $args["class_id"]);
            if ($status) {
                $status = $this->update_class_enrol($args, $args['individual_payment_due_id'], $args['comp_payment_due_id']);
                if ($status) {
                    $class_enrol_data = array(
                        'training_score' => $training_score
                    );
                    $this->db->where("user_id", $user_id);
                    $this->db->where("pymnt_due_id", $args['comp_payment_due_id']);
                    $this->db->update("class_enrol", $class_enrol_data);
                    $status = $this->recalc_merged_pymnt($args);
                }
            }
        }
        return $status;
    }

    /**

     * This method will generate a new company invoice and remove the existing individual invoice

     * @param type $args

     * @return type

     */
    public function new_invoice($args) {

        $status = FALSE;

        if (!empty($args)) {
            //// added by shubhranshu for update the sfc claim id to be zero
            $sfc_data = array('sfc_claim_id' => NULL);
            $this->db->where('pymnt_due_id', $args['individual_payment_due_id']);
            $this->db->where('user_id', $args["individual_user_id"]);
            $this->db->update('class_enrol', $sfc_data);
            //////end of code
            $status = $this->update_classenrol_audittrail($args["tenant_id"], $args["individual_payment_due_id"], $args["individual_user_id"], $args["course_id"], $args["class_id"]);
            $due_to = ' Change individual enrollment to company enrollment';  // s1
            $data = $this->get_current_individule_invoice_data($args["individual_payment_due_id"]); //s2

            $status = $this->enrol_invoice_view($args['individual_payment_due_id'], $data, $args['logged_in_user_id'], $due_to); //s3
            if ($status) {

                $status = $this->update_class_enrol($args, $args['individual_payment_due_id'], $args['individual_payment_due_id']);

                if ($status) {

                    $status = $this->recalc_existg_invoice_pymnt_due($args);
                }
            }
        }

        return $status;
    }

    /**

     * This method recalculates the payment due for a company invoice from an existing

     * Individual Invoice

     * @param type $args

     */
    private function recalc_existg_invoice_pymnt_due($args) {

        $status = TRUE;

        $invoice_details = $this->get_invoice_details($args['individual_payment_due_id']);

        $GSTRate = $invoice_details->gst_rate;

        $GSTRule = $invoice_details->gst_rule;

        $unit_fees = $args['unit_fees'];

        $subsidy_amount = $args['subsidy_amount'];

        $net_due = 0;

        $GST_amount = 0;

        $this->db->select('*');
        $this->db->from('enrol_pymnt_due epd');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
        $this->db->where('epd.pymnt_due_id', $args['comp_payment_due_id']);
        $this->db->where('ce.course_id', $args['course_id']);
        $this->db->where('ce.class_id', $args['class_id']);
        $this->db->where('ce.tenant_id', $args['tenant_id']);
        $this->db->where('epd.att_status', 1);
        $this->db->limit(1);
        $sql = $this->db->get();
        if ($sql->num_rows() > 0) {
            $result_array = $sql->row_array();

            $discount_type = $result_array['discount_type'];
            $discount_rate = $result_array['discount_rate'];
            $discount_amount = $unit_fees * ($discount_rate / 100);
        } else {

            $result_array = $this->get_discnt($args['tenant_id'], $args['company_id'], $args['course_id'], $args['class_id'], $unit_fees, 0);

            $discount_type = $result_array['discount_label'];

            $discount_rate = $result_array['discount_rate'];

            $discount_amount = $result_array['discount_amount'];
        }

        $result_array = $this->net_fees_payable($unit_fees, $discount_amount, $subsidy_amount, $GSTRate, $GSTRule);

        $net_due = $result_array['net_due'];

        $GST_amount = $result_array['GST_amount'];

        $status = $this->update_enrol_pymnt_due($args['individual_payment_due_id'], $net_due, $discount_type, $discount_rate, $GST_amount, $args['individual_payment_due_id']);

        if ($status) {

            $status = $this->update_invoice_audit_trail($args['individual_payment_due_id'], 0);

            if ($status) {

                $status = $this->remove_invoice($args['individual_payment_due_id']);

                if ($status) {

                    $attendance = $this->check_trainee_attendance($args['course_id'], $args['class_id'], $args['individual_user_id'], $args['individual_payment_due_id'], $args['tenant_id']);

                    if ($attendance->att_status == 0) {
                        $net_due = 0;
                        $unit_fees = 0;
                        $discount_amount = 0;
                        $subsidy_amount = 0;
                        $GST_amount = 0;

                        list($status, $new_invoice_id) = $this->create_new_invoice($args['individual_payment_due_id'], $args['company_id'], $net_due, $unit_fees, $discount_amount, $subsidy_amount, $GST_amount, $GSTRule, $GSTRate, 'INVCOMALL');
                    } else {
                        list($status, $new_invoice_id) = $this->create_new_invoice($args['individual_payment_due_id'], $args['company_id'], $net_due, $unit_fees, $discount_amount, $subsidy_amount, $GST_amount, $GSTRule, $GSTRate, 'INVCOMALL');
                    }


                    //list($status, $new_invoice_id) = $this->create_new_invoice($args['individual_payment_due_id'], $args['company_id'], $net_due, $unit_fees, $discount_amount, $subsidy_amount, $GST_amount, $GSTRule, $GSTRate, 'INVCOMALL');
// commented by skm 02-01-17 because of refunded status remark in invoice st                    
                    /* update invoice id into invoice related table if invoice is paid and refund start */
//                        $previous_inv_id =  $invoice_details->invoice_id;                       
//                        $query1="select * from enrol_paymnt_recd where invoice_id='$previous_inv_id'";
//			$query =  mysqli_query($query1);		   
//			if($query)
//			{ 
//				
//				
//				$sql="update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$fi = mysqli_query($sql); 
//				$sql1="update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$fi2 = mysqli_query($sql1); 
//				
//			}
//
//			$query2="select * from enrol_refund where invoice_id='$previous_inv_id'";
//			$query = mysqli_query($query2);
//			if($query)
//			{ 
//				   $previous_inv_id =  $invoice_details->invoice_id;    
//			
//				$sql="update enrol_refund set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$si = mysqli_query($sql);
//				$sql1="update enrol_refund_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$sifi = mysqli_query($sql1);
//				
//			}
                    /* end */

                    if ($status) {

                        $status = $this->set_audittrail_newinvoice_num($args['individual_payment_due_id'], $new_invoice_id);
                        $status = $this->set_viewinvoice_newinvoice_num($args['individual_payment_due_id'], $new_invoice_id); //s4
                    }
                }
            }
        }

        return $status;
    }

    public function check_trainee_attendance($course_id, $class_id, $trainee_id, $payment_due_id, $tenant_id) {
        $this->db->select('epd.att_status');
        $this->db->from('class_enrol ce');
        $this->db->join('enrol_pymnt_due epd', 'epd.user_id = ce.user_id and epd.pymnt_due_id = ce.pymnt_due_id');
        $this->db->where('ce.course_id', $course_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.user_id', $trainee_id);
        $this->db->where('epd.pymnt_due_id', $payment_due_id);
        $this->db->where('ce.tenant_id', $tenant_id);
        $sql = $this->db->get();
        return $sql->row();
    }

    /**

     * This method holds the payment due and invoice re-calculation flow for 

     * merging two existing  invoices (indivdidaul to company)

     * @param type $args

     */
    private function recalc_merged_pymnt($args) {


        $status = TRUE;

        $GSTRate = $args['comp_gst_rate'];

        $GSTRule = $args['comp_gst_rule'];

        $unit_fees = $args['unit_fees'];

        $subsidy_amount = $args['subsidy_amount']; ////added by shubhranshu to calculate and add subsidy of previous invoice

        $net_due = 0;

        $GST_amount = 0;

        $data = $this->db->select('user_id')
                        ->from('enrol_pymnt_due')
                        ->where('pymnt_due_id', $args['individual_payment_due_id'])
                        ->get()->row(0);

        $user_id = $data->user_id;
        $check_attendance = $this->check_attendance_row($args['tenant_id'], $args['course_id'], $args['class_id']);
        if ($check_attendance > 0) {
            $check_attendance_trainee = $this->check_attendance_trainee($args['tenant_id'], $args['course_id'], $args['class_id'], $user_id);
            if ($check_attendance_trainee > 0) {
                $training_score = 'C';
                $att_status = 1;
            } else {
                $training_score = 'ABS';
                $att_status = 0;
            }
        } else {
            $att_status = 1;
        }

        $this->db->select('*');
        $this->db->from('enrol_pymnt_due epd');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
        $this->db->where('epd.pymnt_due_id', $args['comp_payment_due_id']);
        $this->db->where('ce.course_id', $args['course_id']);
        $this->db->where('ce.class_id', $args['class_id']);
        $this->db->where('ce.tenant_id', $args['tenant_id']);
        $this->db->where('epd.att_status', 1);
        $this->db->limit(1);
        $sql = $this->db->get();
        if ($sql->num_rows() > 0) {
            $result_array = $sql->row_array();

            $discount_type = $result_array['discount_type'];
            $discount_rate = $result_array['discount_rate'];
            $discount_amount = $unit_fees * ($discount_rate / 100);
        } else {

            $result_array = $this->get_discnt($args['tenant_id'], $args['company_id'], $args['course_id'], $args['class_id'], $unit_fees, 0);

            $discount_type = $result_array['discount_label'];

            $discount_rate = $result_array['discount_rate'];

            $discount_amount = $result_array['discount_amount'];
        }

        $result_array = $this->net_fees_payable($unit_fees, $discount_amount, $subsidy_amount, $GSTRate, $GSTRule);

        $net_due = $result_array['net_due'];

        $GST_amount = $result_array['GST_amount'];

        $invoice_details = $this->get_invoice_details($args['comp_payment_due_id']);

        if ($check_attendance > 0) {
            if ($check_attendance_trainee > 0) {
                $total_inv_amount = (round($invoice_details->total_inv_amount, 2) + round($net_due, 2));
                $total_unit_fees = round($invoice_details->total_unit_fees + $unit_fees, 4);
                $total_inv_discnt = round($invoice_details->total_inv_discnt + $discount_amount, 4);
                $total_inv_subsidy = round($invoice_details->total_inv_subsdy + $subsidy_amount, 4);
                $total_gst = (round($invoice_details->total_gst, 2) + round($GST_amount, 2));
            } else {
                $total_inv_amount = $invoice_details->total_inv_amount;
                $total_unit_fees = $invoice_details->total_unit_fees;
                $total_inv_discnt = $invoice_details->total_inv_discnt;
                $total_inv_subsidy = $invoice_details->total_inv_subsdy;
                $total_gst = $invoice_details->total_gst;
            }
        } else {
            $total_inv_amount = (round($invoice_details->total_inv_amount, 2) + round($net_due, 2));
            $total_unit_fees = round($invoice_details->total_unit_fees + $unit_fees, 4);
            $total_inv_discnt = round($invoice_details->total_inv_discnt + $discount_amount, 4);
            $total_inv_subsidy = round($invoice_details->total_inv_subsdy + $subsidy_amount, 4);
            $total_gst = (round($invoice_details->total_gst, 2) + round($GST_amount, 2));
        }
        $status = $this->update_enrol_pymnt_due($args['comp_payment_due_id'], $net_due, $discount_type, $discount_rate, $GST_amount, $args['individual_payment_due_id']);
        if ($status) {
            $enrol_pymnt_due_array = array(
                'att_status' => $att_status
            );
            $this->db->where("user_id", $user_id);
            $this->db->where("pymnt_due_id", $args['comp_payment_due_id']);
            $this->db->update("enrol_pymnt_due", $enrol_pymnt_due_array);
            $status = $this->update_invoice_audit_trail($args['individual_payment_due_id'], 0);
            if ($status) {
                $status = $this->remove_invoice($args['individual_payment_due_id']);
                if ($status) {
                    $status = $this->update_invoice_audit_trail($args['comp_payment_due_id']);
                    if ($status) { // typo error.
                        $curr_comp_invoice_details = $this->get_invoice_details($args['comp_payment_due_id']);

                        $status = $this->remove_invoice($args['comp_payment_due_id']);
                        if ($status) {
                            list($status, $new_invoice_id) = $this->create_new_invoice($args['comp_payment_due_id'], $args['company_id'], $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $GSTRule, $GSTRate, 'INVCOMALL');
                            if ($status) {
// commented by skm 02-01-17 because of refunded status remark in invoice st                               
                                /* update invoice id into invoice related table if invoice is paid and refund start */
//                        $previous_inv_id =  $args['comp_invoice_id'];                       
//                        $query1="select * from enrol_paymnt_recd where invoice_id='$previous_inv_id'";
//			$query =  mysqli_query($query1);		   
//			if($query)
//			{ 
//				
//				
//				$sql="update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$fi = mysqli_query($sql); 
//				$sql1="update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$fi2 = mysqli_query($sql1); 
//				
//			}
//
//			$query2="select * from enrol_refund where invoice_id='$previous_inv_id'";
//			$query = mysqli_query($query2);
//			if($query)
//			{ 
//				   $previous_inv_id =  $args['comp_invoice_id'];    
//			
//				$sql="update enrol_refund set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$si = mysqli_query($sql);
//				$sql1="update enrol_refund_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'";
//				$sifi = mysqli_query($sql1);
//				
//			}
                                /* */

                                $status = $this->set_audittrail_newinvoice_num($args['comp_payment_due_id'], $new_invoice_id);

                                $status = $this->set_viewinvoice_newinvoice_num($args['comp_payment_due_id'], $new_invoice_id); // skm

                                $status = $this->set_audittrail_newinvoice_num($args['individual_payment_due_id'], $new_invoice_id);
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    /**

     * Caculate fees payable

     */
    private function net_fees_payable($unit_fees, $discount_amount, $subsidy_amount, $GSTRate, $GSTRule) {

        $discountedFees = round($unit_fees - $discount_amount, 2); //sk1

        if ($subsidy_amount != 0) {

            if ($GSTRule == 'GSTBSD') {

                $GST_amount = round(($discountedFees * ($GSTRate / 100)), 2); //sk2

                $net_due = round(($discountedFees + $GST_amount - $subsidy_amount), 2); //sk3
            }

            if ($GSTRule == 'GSTASD') {

                $temp_net_due = round(($discountedFees - $subsidy_amount), 4); //sk4

                $GST_amount = round(($temp_net_due * ($GSTRate / 100)), 2); //sk5

                $net_due = round(($temp_net_due + $GST_amount), 2); //sk6
            }
        } else {

            $GST_amount = round(($discountedFees * ($GSTRate / 100)), 2); //sk7

            $net_due = round(($discountedFees + $GST_amount), 2); //sk8
        }

        return array('net_due' => $net_due, "GST_amount" => $GST_amount);
    }

    /**

     * This method creates a newe invoice record

     * @param type $payment_due_id

     * @param type $total_inv_amount

     * @param type $total_unit_fees

     * @param type $total_inv_discnt

     * @param type $total_inv_subsidy

     * @param type $total_gst

     * @param type $GSTRule

     */
    private function create_new_invoice($payment_due_id, $company_id, $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $GSTRule, $GSTRate, $inv_type) {

        $insert_status = TRUE;

        $cur_date = date('Y-m-d H:i:s');

        $invoice_id = $this->generate_invoice_id();

        $enrol_invoice_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $cur_date,
            'inv_type' => $inv_type,
            'company_id' => $company_id,
            'total_inv_amount' => round($total_inv_amount, 4),
            'total_unit_fees' => round($total_unit_fees, 4),
            'total_inv_discnt' => round($total_inv_discnt, 4),
            'total_inv_subsdy' => round($total_inv_subsidy, 4),
            'total_gst' => round($total_gst, 4),
            'gst_rate' => round($GSTRate, 4),
            'gst_rule' => $GSTRule,
            'invoice_generated_on' => $cur_date,
        );

        $this->db->trans_start();

        $this->db->insert('enrol_invoice', $enrol_invoice_data);

        $new_invoice_id = $invoice_id;

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $insert_status = FALSE;
        }

        return array($insert_status, $new_invoice_id);
    }

    /**

     * This method removes an invoice from the table enrol_invoice

     * @param type $payment_due_id

     * @return boolean

     */
    private function remove_invoice($payment_due_id) {

        $delete_status = TRUE;
        if (!empty($payment_due_id)) {
            $this->db->where("pymnt_due_id", $payment_due_id);

            $this->db->trans_start();

            $this->db->delete("enrol_invoice");
            $this->db->trans_complete();
        }


        if ($this->db->trans_status() === FALSE) {

            $delete_status = FALSE;
        }

        return $delete_status;
    }

    /**

     * This method is usetd to copy from enrol_invoice, the invoice being changed

     * to enrol_invoice_audittrail 

     * @param type $payment_due_id

     * @param type $reg_inv_id

     * @return boolean

     */
    private function update_invoice_audit_trail($payment_due_id, $reg_inv_id = 0) {

        $insert_status = TRUE;

        $this->db->select("*");

        $this->db->from("enrol_invoice");

        $this->db->where("pymnt_due_id", $payment_due_id);

        $result = $this->db->get()->row();
        //print_r($this->db->last_query());exit;
//print_r($result);
        $audit_inv_data = array(
            'invoice_id' => $result->invoice_id,
            'pymnt_due_id' => $result->pymnt_due_id,
            'inv_date' => $result->inv_date,
            'inv_type' => $result->inv_type,
            'company_id' => $result->company_id,
            'regen_inv_id' => $reg_inv_id,
            'total_inv_amount' => $result->total_inv_amount,
            'total_unit_fees' => $result->total_unit_fees,
            'total_inv_discnt' => $result->total_inv_discnt,
            'total_inv_subsdy' => $result->total_inv_subsdy,
            'total_gst' => $result->total_gst,
            'gst_rate' => $result->gst_rate,
            'gst_rule' => $result->gst_rule,
            'invoice_generated_on' => $result->invoice_generated_on,
            'invoiced_on' => $result->invoiced_on,
            'invoice_excess_amt' => $result->invoice_excess_amt
        );
//        print_r($audit_inv_data);exit;
        $this->db->trans_start();

        $this->db->insert('enrol_invoice_audittrail', $audit_inv_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $insert_status = FALSE;
        }

        return $insert_status;
    }

    /**

     * This method is used to update enrol_pymnt_due with the new the payment details

     * @param type $new_payment_due_id

     * @param type $net_due

     * @param type $discount_type

     * @param type $discount_rate

     * @param type $GST_amount

     * @param type $prev_pymnt_due_id

     * @return boolean

     */
    private function update_enrol_pymnt_due($new_payment_due_id, $net_due, $discount_type, $discount_rate, $GST_amount, $prev_pymnt_due_id, $class_fees = NULL) {

        $update_status = TRUE;

        $enrol_pymnt_due_array = array(
            'total_amount_due' => $net_due,
            'discount_type' => $discount_type,
            'discount_rate' => $discount_rate,
            'gst_amount' => $GST_amount,
            'pymnt_due_id' => $new_payment_due_id
        );

        if (!empty($class_fees)) {

            $enrol_pymnt_due_array['class_fees'] = $class_fees;
        }

        $this->db->trans_start();

        $this->db->where("pymnt_due_id", $prev_pymnt_due_id);

        $this->db->update("enrol_pymnt_due", $enrol_pymnt_due_array);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $update_status = FALSE;
        }

        return $update_status;
    }

    /**

     * This method retrieves the invoice details

     * @param type $payment_due_id

     */
    private function get_invoice_details($payment_due_id) {

        $this->db->select("*");

        $this->db->from("enrol_invoice");

        $this->db->where("pymnt_due_id", $payment_due_id);

        $result = $this->db->get();

        return $result->row();
    }

    /**

     * This method updates the class_enrol details with the new details

     */
    private function update_class_enrol($args, $prev_pymnt_due_id, $new_pymnt_due_id) {

        $status = TRUE;

        $class_enrol_data = array(
            'pymnt_due_id' => $new_pymnt_due_id,
            'enrolment_mode' => 'COMPSPON',
            'company_id' => $args['company_id'],
            'enrolled_by' => $args['logged_in_user_id'],
            'enrolled_on' => date('Y-m-d H:i:s'),
        );

        $this->db->where("pymnt_due_id", $prev_pymnt_due_id);

        $this->db->where("user_id", $args['individual_user_id']);

        $this->db->where("course_id", $args['course_id']);

        $this->db->where("class_id", $args['class_id']);

        $this->db->where("tenant_id", $args['tenant_id']);

        $this->db->trans_start();

        $class_enrol_status = $this->db->update("class_enrol", $class_enrol_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /**

     * This method updates the class enrol audit trail table with data that needs to be enrolments deatiks

     * for individuals whose data is being changed.

     * @param type $args

     * @return boolean

     */
    private function update_classenrol_audittrail($tenant_id, $payment_due_id, $user_id, $course_id, $class_id) {

        $status = TRUE;

        $this->db->select('*');

        $this->db->from("class_enrol");

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->where("course_id", $course_id);

        $this->db->where("class_id", $class_id);

        $this->db->where("tenant_id", $tenant_id);

        $audit_data = $this->db->get()->row_array();

        $this->db->trans_start();

        $this->db->insert('class_enrol_audit_trail', $audit_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /** function to get company discount

     */
    public function company_discount_for_course($tenant_id, $company, $course) {

        $result = $this->db->select('Discount_Percent, Discount_Amount')->from('company_discount')
                        ->where('Tenant_ID', $tenant_id)->where('Company_ID', $company)->where('Course_ID', $course)
                        ->get()->row();
        return $result;
    }

    /**

     * function to get discount for class

     */
    public function calculate_classdiscount($class_id, $tenant_id) {

        $class = $this->db->select('cc.class_discount')
                        ->from('course_class cc')
                        ->where('cc.class_id', $class_id)
                        ->where('Tenant_ID', $tenant_id)
                        ->get()->row()->class_discount;

        return $class;
    }

    /**

     * This method returns back the discount for a trainee for a course

     * @param type $trainee_id

     * @param type $course_id

     */
    private function get_individual_discount($trainee_id, $course_id) {

        $individual_discount = 0;

        $data = $this->db->select('discount_percent,discount_amount')->from('tms_users_discount')->where('user_id', $trainee_id)
                        ->where('course_id', $course_id)->get();

        if ($data->num_rows() == 0) {

            $insert_data = array(
                'tenant_id' => $this->user->tenant_id,
                'user_id' => $trainee_id,
                'course_id' => $course_id,
                'discount_percent' => 0
            );

            $this->db->insert('tms_users_discount', $insert_data);

            $individual_discount = 0;
        } else {

            $individual_discount = $data->row();
        }

        return $individual_discount;
    }

    /**

     * This method returns the applicable discount amount and rate for a trainee being enrolled in a class

     * @param type $course_id

     * @param type $class_id

     * @param type $tenant_id

     */
    private function get_discnt($tenant_id, $company_id, $course_id, $class_id, $unitFees, $trainee_id = 0) {

        $discount_label = '';

        $discount_amount = 0;

        $discount_rate = 0;

        if ($trainee_id != 0) {

            $individual_discount = $this->get_individual_discount($trainee_id, $course_id);
        }

        if ($trainee_id != 0 && ($individual_discount->discount_percent != 0 || $individual_discount->discount_amount != 0)) {

            $discount_label = 'DISINDVI';

            if (!empty($individual_discount->discount_amount)) {

                $discount_rate = round(($individual_discount->discount_amount / $unitFees) * 100, 4);

                $discount_amount = $individual_discount->discount_amount;
            } else {

                if (!empty($individual_discount->discount_percent)) {

                    $discount_rate = $individual_discount->discount_percent;

                    $discount_amount = $unitFees * ($discount_rate / 100);
                }
            }
        } else {

            if ($company_id != 0) {

                $company_discount_result = $this->classtraineemodel->company_discount_for_course($tenant_id, $company_id, $course_id);
            }

            $class_discount = $this->classtraineemodel->calculate_classdiscount($class_id, $tenant_id);

            $discountFlag = FALSE;

            if (!empty($company_discount_result) && !empty($company_discount_result->Discount_Amount)) {

                $discount_label = 'DISCOMP';

                $discount_amount = $company_discount_result->Discount_Amount;

                $discount_rate = ($company_discount_result->Discount_Amount * 100) / $unitFees;

                $discountFlag = TRUE;
            } elseif (!empty($company_discount_result) && !empty($company_discount_result->Discount_Percent) && !$discountFlag) {

                $discount_label = 'DISCOMP';

                $discount_rate = $company_discount_result->Discount_Percent;

                $discount_amount = $unitFees * ($discount_rate / 100);
            } elseif (!empty($class_discount)) {

                $discount_label = 'DISCLASS';

                $discount_rate = $class_discount;

                $discount_amount = $unitFees * ($discount_rate / 100);
            } else {



                if ($company_id != 0)
                    $discount_label = 'DISCOMP';
                else
                    $discount_label = 'DISINDVI';

                $discount_amount = 0;

                $discount_rate = 0;
            }
        }



        return array('discount_label' => $discount_label, "discount_rate" => round($discount_rate, 4), 'discount_amount' => round($discount_amount, 2));
    }

    /**

     * This method updates the invoice audit trail with the regenerated Invoice Number

     * @param type $payment_due_id

     * @param type $reg_inv_id

     * @return boolean

     */
    private function set_audittrail_newinvoice_num($payment_due_id, $reg_inv_id) {

        $status = TRUE;

        $audit_array = array('regen_inv_id' => $reg_inv_id);

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->trans_start();



        $status = $this->db->update("enrol_invoice_audittrail", $audit_array);



        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /**

     * This method for redirect trainees to enroll new page if trainee status is active.

     * @return type

     */
    public function get_course_class_list($type = 'data', $tenant_id = 0, $limit = 0, $offset = 0, $sort_by = 0, $sort_order = 0) {
        $this->db->select("cls.course_id, cls.class_id,cls.lock_status, crse.crse_name,cls.class_name, cls.class_start_datetime, cls.class_end_datetime, "
                . "cls.total_seats,cls.total_classroom_duration, cls.total_lab_duration, cls.assmnt_duration,"
                . "cls.class_fees, cls.class_pymnt_enrol, count(enrol.user_id) as total_enrolled");

        $this->db->from("course crse");

        $this->db->join("course_class cls", "crse.course_id = cls.course_id AND crse.tenant_id = cls.tenant_id", "left");

        $this->db->join("class_enrol  enrol", "enrol.course_id = cls.course_id AND enrol.class_id = cls.class_id AND crse.tenant_id = cls.tenant_id", "left");
        /* get the course class list for sales executive on course level */
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->join("course_sales_exec  sales", "sales.course_id = crse.course_id AND sales.tenant_id = crse.tenant_id", "left");
        }
        $this->db->where("cls.class_status !=", "INACTIV");

        $this->db->where("crse.crse_status", "ACTIVE");

        $this->db->where("crse.tenant_id", $tenant_id);

        if ($type == 'data') {

            if ($sort_by) {

                $this->db->order_by($sort_by, $sort_order);
            } else {

                $this->db->order_by('cls.class_start_datetime', 'DESC');
            }

            if ($limit == $offset) {

                $this->db->limit($offset);
            } else if ($limit > 0) {

                $limitvalue = $offset - $limit;

                $this->db->limit($limit, $limitvalue);
            }
        }

        if ($this->user->role_id == 'SLEXEC') {

            //$this->db->like('cls.sales_executive', $this->user->user_id, 'both');
            $this->db->like('sales.user_id', $this->user->user_id, 'both');
        }

        $this->db->group_by("cls.course_id, cls.class_id");

        $result = $this->db->get();

        return $result->result();
    }

    /**

     *  This method calculates the fees payable

     * @param type $trainee_id

     * @param type $tenant_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $loggedin_user_id

     * @return type Array

     */
    public function fees_payable($trainee_id, $tenant_id, $course_id, $class_id, $subsidy, $company_id, $loggedin_user_id) {



        $courseDetails = $this->course->get_course_detailse($course_id);

        $classDetails = $this->class->get_class_details($tenant_id, $class_id);

        $unit_fees = $classDetails->class_fees;

        $gst_rule = $courseDetails->subsidy_after_before;

        $gst_rate = $this->get_gst_current();

        $gst_label = ($courseDetails->gst_on_off == 1) ? 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($courseDetails->subsidy_after_before), ', ') : 'GST OFF';



        if ($company_id == 0) {

            $result_array = $this->get_discnt($tenant_id, 0, $course_id, $class_id, $unit_fees, $trainee_id);
        } else {

            $result_array = $this->get_discnt($tenant_id, $company_id, $course_id, $class_id, $unit_fees, 0);
        }

        $discount_type = $result_array['discount_label'];

        $discount_rate = $result_array['discount_rate'];

        $discount_amount = $result_array['discount_amount'];



        $fees_due = $unit_fees - $discount_amount;

        $gst_amount = $this->calculate_gst($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, 0, $gst_rate);

        $gst_amount = round($gst_amount, 2); //sk1

        $net_fees_due = $this->calculate_net_due($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, $subsidy, $gst_rate);



        $result_array = array(
            'unit_fees' => $unit_fees,
            'discount_type' => $discount_type,
            'discount_rate' => $discount_rate,
            'discount_amount' => $discount_amount,
            'gst_amount' => is_null($gst_amount) ? 0 : $gst_amount,
            'gst_rate' => $gst_rate,
            'gst_label' => $gst_label,
            'net_fees_due' => $net_fees_due,
            'gst_rule' => $gst_rule
        );

        return $result_array;
    }

    /* This function calculate discount on based on previous given discount on enroll class or company */

    public function reg_enroll_fees_payable($trainee_id, $tenant_id, $course_id, $class_id, $subsidy, $company_id, $loggedin_user_id, $payment_due_id) {

        $courseDetails = $this->course->get_course_detailse($course_id);

        $classDetails = $this->class->get_class_details($tenant_id, $class_id);

        $unit_fees = $classDetails->class_fees;

        $gst_rule = $courseDetails->subsidy_after_before;

        $gst_rate = $this->get_gst_current();

        $gst_label = ($courseDetails->gst_on_off == 1) ? 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($courseDetails->subsidy_after_before), ', ') : 'GST OFF';



        if ($company_id == 0) {

            $result_array = $this->get_discnt($tenant_id, 0, $course_id, $class_id, $unit_fees, $trainee_id);
        } else {
            $this->db->select('*');
            $this->db->from('enrol_pymnt_due epd');
            $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
            $this->db->where('epd.pymnt_due_id', $payment_due_id);
            $this->db->where('ce.course_id', $course_id);
            $this->db->where('ce.class_id', $class_id);
            $this->db->where('ce.tenant_id', $tenant_id);
            $this->db->where('epd.att_status', 1);
            $this->db->limit(1);
            $sql = $this->db->get();
            if ($sql->num_rows() > 0) {
                $result_array = $sql->row_array();

                $discount_type = $result_array['discount_type'];
                $discount_rate = $result_array['discount_rate'];
                $discount_amount = $unit_fees * ($discount_rate / 100);
            } else {

                $result_array = $this->get_discnt($tenant_id, $company_id, $course_id, $class_id, $unit_fees, 0);
                $discount_type = $result_array['discount_label'];
                $discount_rate = $result_array['discount_rate'];
                $discount_amount = $result_array['discount_amount'];
            }
        }




        $fees_due = $unit_fees - $discount_amount;

        $gst_amount = $this->calculate_gst($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, 0, $gst_rate);

        $gst_amount = round($gst_amount, 2); //sk1

        $net_fees_due = $this->calculate_net_due($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, $subsidy, $gst_rate);



        $result_array = array(
            'unit_fees' => $unit_fees,
            'discount_type' => $discount_type,
            'discount_rate' => $discount_rate,
            'discount_amount' => $discount_amount,
            'gst_amount' => is_null($gst_amount) ? 0 : $gst_amount,
            'gst_rate' => $gst_rate,
            'gst_label' => $gst_label,
            'net_fees_due' => $net_fees_due,
            'gst_rule' => $gst_rule
        );

        return $result_array;
    }

    /**

      /**

     * This method check company discount when invoice is re-genrated

     */
    public function fees_payable_check_discount($trainee_id, $tenant_id, $course_id, $class_id, $subsidy, $company_id, $payment_due_id, $loggedin_user_id) {
        $courseDetails = $this->course->get_course_detailse($course_id);
        $classDetails = $this->class->get_class_details($tenant_id, $class_id);
        $unit_fees = $classDetails->class_fees;
        $gst_rule = $courseDetails->subsidy_after_before;
        $gst_rate = $this->get_gst_current();
        $gst_label = ($courseDetails->gst_on_off == 1) ? 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($courseDetails->subsidy_after_before), ', ') : 'GST OFF';
        if ($company_id == 0) {
            $result_array = $this->get_discnt($tenant_id, 0, $course_id, $class_id, $unit_fees, $trainee_id);
        } else {
            // this function check company discount applied on invoice or not
            $check_comp_discount = $this->check_discount_rate($payment_due_id, $course_id, $class_id, $tenant_id, $company_id);

            if ($check_comp_discount == 0) {
                $result_array = $this->get_discnt($tenant_id, $company_id, $course_id, $class_id, $unit_fees, 0);
                $discount_type = $result_array['discount_label'];
                $discount_rate = $result_array['discount_rate'];
                $discount_amount = $result_array['discount_amount'];
                $fees_due = $unit_fees - $discount_amount;
            } else {
                $discount_type = $check_comp_discount['discount_type'];
                $discount_rate = $check_comp_discount['discount_rate'];
                $discount_amount = $unit_fees * ($discount_rate / 100);
                $fees_due = $unit_fees - $discount_amount;
            }
        }

        $gst_amount = $this->calculate_gst($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, 0, $gst_rate);
        $gst_amount = round($gst_amount, 2); //sk1
        $net_fees_due = $this->calculate_net_due($courseDetails->gst_on_off, $courseDetails->subsidy_after_before, $fees_due, $subsidy, $gst_rate);
        $result_array = array(
            'unit_fees' => $unit_fees,
            'discount_type' => $discount_type,
            'discount_rate' => $discount_rate,
            'discount_amount' => $discount_amount,
            'gst_amount' => is_null($gst_amount) ? 0 : $gst_amount,
            'gst_rate' => $gst_rate,
            'gst_label' => $gst_label,
            'net_fees_due' => $net_fees_due,
            'gst_rule' => $gst_rule
        );
        return $result_array;
    }

    /* This method check company discount which already applied on invoice */

    public function check_discount_rate($payment_due_id, $course_id, $class_id, $tenant_id, $company_id) {
        $this->db->select('*');
        $this->db->from('enrol_pymnt_due epd');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = epd.pymnt_due_id');
        $this->db->where('epd.pymnt_due_id', $payment_due_id);
        $this->db->where('ce.course_id', $course_id);
        $this->db->where('ce.class_id', $class_id);
        $this->db->where('ce.tenant_id', $tenant_id);
        $this->db->where('ce.company_id', $company_id);
        $this->db->where('epd.att_status', 1);
        $this->db->limit(1);
        $sql = $this->db->get();
        if ($sql->num_rows() > 0) {
            return $sql->row_array();
        } else {
            return $sql = 0;
        }
    }

    /**

     * This method updates the data base with enrolment details

     * @param type $trainee_id

     * @param type $course_id

     * @param type $class_id

     * @param type $enrolment_type

     * @param type $parm_array

     * @param type $company_id

     */
    public function enroll_db_update($trainee_id, $course_id, $class_id, $enrolment_mode, $parm_array, $company_id, $pay_status) {
        $status = TRUE;

        $tenant_id = $this->tenant_id;


        $data = $this->db->select('class_start_datetime as start')
                        ->from('course_class')
                        ->where('class_id', $class_id)
                        ->where('tenant_id', $tenant_id)
                        ->get()->row(0);

        $start = $data->start;

        $cur_date = date('Y-m-d H:i:s');
        if ($start) {
            //$cur_date = $start;
        }

        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $class_status = $this->get_class_statustext($class_id);

        $loggedin_user_id = $this->user->user_id;

        if ($pay_status == 'PAID') {

            $enrol_status = 'ENRLACT';
        } else {

            $enrol_status = 'ENRLBKD';
        }

        $fees_array = $this->fees_payable($trainee_id, $tenant_id, $course_id, $class_id, 0, $company_id, $loggedin_user_id);

        $this->db->trans_start();

        // for implementing the sales executive missing in sales comission report         
        // Changes by ujwal 
        // Date : 24/07/2015        
        // for implementing the sales executive missing in sales comission report         
        // Changes by ujwal 
        // Date : 24/07/2015        

        /* $salesexec = $this->input->post('salesexec');
          if (empty($salesexec)) {
          $salesexec = ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'ADMN') ? $this->user->user_id : NULL;
          } */
        /* sales executive thread to tagged who enrolles on 18-07-2015 by prti */
        if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
            $salesexec = $this->user->user_id;
        } else {
            ///$salesexec = empty($salesexec) ? NULL : $salesexec;
            if (empty($salesexec)) {
                $salesexec = $this->user->user_id;
            } else {
                $salesexec = $salesexec;
            }
        }
        /* sk1 heck class attendance done or not start */
        $check_attendance = $this->check_attendance_row($tenant_id, $course_id, $class_id);
        if ($check_attendance > 0) {
            $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course_id, $class, $class_id);
            if ($check_attendance_trainee > 0) {
                $training_score = 'C';
                $att_status = 1;
            } else {
                $training_score = 'ABS';
                $att_status = 0;
            }
        } else {
            $att_status = 1;
        }
        //end

        $data = array(
            'tenant_id' => $tenant_id,
            'course_id' => $course_id,
            'class_id' => $class_id,
            'user_id' => $trainee_id,
            'enrolment_type' => 'FIRST',
            'enrolment_mode' => $enrolment_mode,
            'pymnt_due_id' => $payment_due_id,
            'company_id' => $company_id,
            'enrolled_on' => $cur_date,
            'enrolled_by' => $loggedin_user_id,
            'tg_number' => '',
            'payment_status' => $pay_status,
            'sales_executive_id' => $salesexec,
            'class_status' => $class_status,
            'enrol_status' => $enrol_status,
            'training_score' => $training_score//sk2
        );
        $this->db->insert('class_enrol', $data);

        $data = array(
            'user_id' => $trainee_id,
            'pymnt_due_id' => $payment_due_id,
            'class_fees' => round($fees_array['unit_fees'], 4),
            'total_amount_due' => round($fees_array['net_fees_due'], 2), //sk3
            'discount_type' => $fees_array['discount_type'],
            'discount_rate' => round($fees_array['discount_rate'], 4),
            'gst_amount' => round($fees_array['gst_amount'], 2), //sk4
            'att_status' => $att_status, //sk5
            'subsidy_amount' => 0,
            'subsidy_recd_date' => '0000-00-00'
        );

        $this->db->insert('enrol_pymnt_due', $data);

        if ($enrolment_mode == 'SELF') {

            $invoice_id = $this->generate_invoice_id();

            $data = array(
                'invoice_id' => $invoice_id,
                'pymnt_due_id' => $payment_due_id,
                'inv_date' => $cur_date,
                'inv_type' => 'INVINDV',
                'company_id' => $company_id,
                'total_inv_amount' => round($fees_array['net_fees_due'], 2), //sk6
                'total_unit_fees' => round($fees_array['unit_fees'], 4),
                'total_inv_discnt' => round($fees_array['discount_amount'], 4),
                'total_gst' => round($fees_array['gst_amount'], 2), //sk7
                'gst_rate' => round($fees_array['gst_rate'], 2), //sk8
                'gst_rule' => $fees_array['gst_rule'],
                'total_inv_subsdy' => 0
            );

            $this->db->insert('enrol_invoice', $data);

            if ($pay_status == 'PAID') {

                $this->insert_payment_recd($invoice_id, $trainee_id, $parm_array, $loggedin_user_id);
            }
        } else if ($enrolment_mode == 'COMPSPON') {

            $invoice_array = $this->get_enroll_invoice_details($course_id, $class_id, $company_id, $tenant_id, 'change', "", $trainee_id, 'second');

            $msg_status = (array) $invoice_array;

            if (!in_array($msg_status['msg_status'], array('not_found', 'cannot_change', 'not_company_user'))) {

                $class_enrol_data = array(
                    'pymnt_due_id' => $invoice_array->pymnt_due_id
                );

                $this->db->where("pymnt_due_id", $payment_due_id);

                $this->db->where("user_id", $trainee_id);

                $this->db->where("course_id", $course_id);

                $this->db->where("class_id", $class_id);

                $this->db->where("tenant_id", $tenant_id);

                $status = $this->db->update("class_enrol", $class_enrol_data);



                $this->db->where("pymnt_due_id", $payment_due_id);

                $this->db->where("user_id", $trainee_id);

                $status = $this->db->update("enrol_pymnt_due", $class_enrol_data);



//                $total_inv_amount = round($invoice_array->total_inv_amount + $fees_array['net_fees_due'], 4);
//
//                $total_unit_fees = round($invoice_array->total_unit_fees + $fees_array['unit_fees'], 4);
//
//                $total_inv_discnt = round($invoice_array->total_inv_discnt + $fees_array['discount_amount'], 4);
//
//                $total_inv_subsidy = round($invoice_array->total_inv_subsdy, 4);
//
//                $total_gst = round($invoice_array->total_gst + $fees_array['gst_amount'], 4);
                //sk9 start
                if ($check_attendance > 0) {
                    if ($check_attendance_trainee > 0) {

                        $discount_rate = $fees_array["discount_rate"];
                        $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                        $total_gst_due += $fees_array["gst_amount"];
                        $total_unit_fees_due += $fees_array["unit_fees"];
                        $total_net_fees_due += $fees_array["net_fees_due"];
                    }
                } else {
                    $discount_rate = $fees_array["discount_rate"];
                    $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                    $total_gst_due += $fees_array["gst_amount"];
                    $total_unit_fees_due += $fees_array["unit_fees"];
                    $total_net_fees_due += $fees_array["net_fees_due"];
                }//sk9 end

                $status = $this->update_invoice_audit_trail($invoice_array->pymnt_due_id);

                if ($status) {

                    $status = $this->remove_invoice($invoice_array->pymnt_due_id);

                    if ($status) {

                        //list($status, $new_invoice_id) = $this->create_new_invoice($invoice_array->pymnt_due_id, $company_id, $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $fees_array['gst_rule'], $fees_array['gst_rate'], 'INVCOMALL');
                        //sk10
                        list($status, $new_invoice_id) = $this->create_new_invoice($invoice_array->pymnt_due_id, $company_id, (round($invoice_array->total_inv_amount, 2) + round($total_net_fees_due, 2)), ($invoice_array->total_unit_fees + $total_unit_fees_due), ($invoice_array->total_inv_discnt + $total_discount_due), ($invoice_array->total_inv_subsdy + $total_subsidy_amount_due), (round($invoice_array->total_gst, 2) + round($total_gst_due, 2)), $invoice_array->gst_rule, $invoice_array->gst_rate, 'INVCOMALL');
                        if ($status) {

                            $invoice_id = $new_invoice_id;

                            $status = $this->set_audittrail_newinvoice_num($invoice_array->pymnt_due_id, $new_invoice_id);

                            $this->update_payment_recd($new_invoice_id, $invoice_array->invoice_id);
                        }
                    }
                }
            } else {

                $invoice_id = $this->generate_invoice_id();

                $data = array(
                    'invoice_id' => $invoice_id,
                    'pymnt_due_id' => $payment_due_id,
                    'inv_date' => $cur_date,
                    'inv_type' => 'INVCOMALL',
                    'company_id' => $company_id,
                    'total_inv_amount' => round($fees_array['net_fees_due'], 4),
                    'total_unit_fees' => round($fees_array['unit_fees'], 4),
                    'total_inv_discnt' => round($fees_array['discount_amount'], 4),
                    'total_inv_subsdy' => 0,
                    'total_gst' => round($fees_array['gst_amount'], 4),
                    'gst_rate' => round($fees_array['gst_rate'], 4),
                    'gst_rule' => $fees_array['gst_rule']
                );

                $this->db->insert('enrol_invoice', $data);
            }

            if ($pay_status == 'PAID') {

                $this->insert_payment_recd($invoice_id, $trainee_id, $parm_array, $loggedin_user_id);
            }
        } else {

            $status = FALSE;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /* This method update data base with enrollment details and class or company discount based on previosuly given discount */

    public function regisetr_enroll_db_update($trainee_id, $course_id, $class_id, $enrolment_mode, $parm_array, $company_id, $pay_status) {
        $status = TRUE;

        $tenant_id = $this->tenant_id;


        $data = $this->db->select('class_start_datetime as start')
                        ->from('course_class')
                        ->where('class_id', $class_id)
                        ->where('tenant_id', $tenant_id)
                        ->get()->row(0);

        $start = $data->start;

        $cur_date = date('Y-m-d H:i:s');
        if ($start) {
            //$cur_date = $start;
        }

        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $class_status = $this->get_class_statustext($class_id);

        $loggedin_user_id = $this->user->user_id;

        if ($pay_status == 'PAID') {

            $enrol_status = 'ENRLACT';
        } else {

            $enrol_status = 'ENRLBKD';
        }

//        $fees_array = $this->reg_enroll_fees_payable($trainee_id, $tenant_id, $course_id, $class_id,0, $company_id, $loggedin_user_id,$payment_due_id);
        $fees_array = $this->fees_payable($trainee_id, $tenant_id, $course_id, $class_id, 0, $company_id, $loggedin_user_id);
        $this->db->trans_start();

        // for implementing the sales executive missing in sales comission report         
        // Changes by ujwal 
        // Date : 24/07/2015        
        // for implementing the sales executive missing in sales comission report         
        // Changes by ujwal 
        // Date : 24/07/2015        

        /* $salesexec = $this->input->post('salesexec');
          if (empty($salesexec)) {
          $salesexec = ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'ADMN') ? $this->user->user_id : NULL;
          } */
        /* sales executive thread to tagged who enrolles on 18-07-2015 by prti */
        //print_r($this->user);exit;
        if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
            $salesexec = $this->user->user_id;
        } else {
            ///$salesexec = empty($salesexec) ? NULL : $salesexec;
            if (empty($salesexec)) {
                $salesexec = $this->user->user_id;
            } else {
                $salesexec = $salesexec;
            }
        }
        /* sk1 heck class attendance done or not start */
        $check_attendance = $this->check_attendance_row($tenant_id, $course_id, $class_id);
        if ($check_attendance > 0) {
            $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course_id, $class, $class_id);
            if ($check_attendance_trainee > 0) {
                $training_score = 'C';
                $att_status = 1;
            } else {
                $training_score = 'ABS';
                $att_status = 0;
            }
        } else {
            $att_status = 1;
        }
        //end

        $data = array(
            'tenant_id' => $tenant_id,
            'course_id' => $course_id,
            'class_id' => $class_id,
            'user_id' => $trainee_id,
            'enrolment_type' => 'FIRST',
            'enrolment_mode' => $enrolment_mode,
            'pymnt_due_id' => $payment_due_id,
            'company_id' => $company_id,
            'enrolled_on' => $cur_date,
            'enrolled_by' => $loggedin_user_id,
            'tg_number' => '',
            'payment_status' => $pay_status,
            'sales_executive_id' => $salesexec,
            'class_status' => $class_status,
            'enrol_status' => $enrol_status,
            'training_score' => $training_score//sk2
        );
        $this->db->insert('class_enrol', $data);

        if ($enrolment_mode == 'SELF') {

            $data = array(
                'user_id' => $trainee_id,
                'pymnt_due_id' => $payment_due_id,
                'class_fees' => round($fees_array['unit_fees'], 4),
                'total_amount_due' => round($fees_array['net_fees_due'], 2), //sk3
                'discount_type' => $fees_array['discount_type'],
                'discount_rate' => round($fees_array['discount_rate'], 4),
                'gst_amount' => round($fees_array['gst_amount'], 2), //sk4
                'att_status' => $att_status, //sk5
                'subsidy_amount' => 0,
                'subsidy_recd_date' => '0000-00-00'
            );
            $this->db->insert('enrol_pymnt_due', $data);
            $invoice_id = $this->generate_invoice_id();

            $data = array(
                'invoice_id' => $invoice_id,
                'pymnt_due_id' => $payment_due_id,
                'inv_date' => $cur_date,
                'inv_type' => 'INVINDV',
                'company_id' => $company_id,
                'total_inv_amount' => round($fees_array['net_fees_due'], 2), //sk6
                'total_unit_fees' => round($fees_array['unit_fees'], 4),
                'total_inv_discnt' => round($fees_array['discount_amount'], 4),
                'total_gst' => round($fees_array['gst_amount'], 2), //sk7
                'gst_rate' => round($fees_array['gst_rate'], 2), //sk8
                'gst_rule' => $fees_array['gst_rule'],
                'total_inv_subsdy' => 0
            );

            $this->db->insert('enrol_invoice', $data);

            if ($pay_status == 'PAID') {

                $this->insert_payment_recd($invoice_id, $trainee_id, $parm_array, $loggedin_user_id);
            }
        } else if ($enrolment_mode == 'COMPSPON') {


            $invoice_array = $this->get_enroll_invoice_details($course_id, $class_id, $company_id, $tenant_id, 'change', "", $trainee_id, 'second');



            $msg_status = (array) $invoice_array;

            if (!in_array($msg_status['msg_status'], array('not_found', 'cannot_change', 'not_company_user'))) {



                $fees_array = $this->reg_enroll_fees_payable($trainee_id, $tenant_id, $course_id, $class_id, 0, $company_id, $loggedin_user_id, $invoice_array->pymnt_due_id);

                $previous_inv_id = $invoice_array->invoice_id;

                $class_enrol_data = array(
                    'pymnt_due_id' => $invoice_array->pymnt_due_id
                );


                $data = array(
                    'user_id' => $trainee_id,
                    'pymnt_due_id' => $invoice_array->pymnt_due_id,
                    'class_fees' => round($fees_array['unit_fees'], 4),
                    'total_amount_due' => round($fees_array['net_fees_due'], 2), //sk3
                    'discount_type' => $fees_array['discount_type'],
                    'discount_rate' => round($fees_array['discount_rate'], 4),
                    'gst_amount' => round($fees_array['gst_amount'], 2), //sk4
                    'att_status' => $att_status, //sk5
                    'subsidy_amount' => 0,
                    'subsidy_recd_date' => '0000-00-00'
                );

                $this->db->insert('enrol_pymnt_due', $data);


                $this->db->where("pymnt_due_id", $payment_due_id);

                $this->db->where("user_id", $trainee_id);

                $this->db->where("course_id", $course_id);

                $this->db->where("class_id", $class_id);

                $this->db->where("tenant_id", $tenant_id);

                $status = $this->db->update("class_enrol", $class_enrol_data);




                //sk9 start
                if ($check_attendance > 0) {
                    if ($check_attendance_trainee > 0) {

                        $discount_rate = $fees_array["discount_rate"];
                        $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                        $total_gst_due += $fees_array["gst_amount"];
                        $total_unit_fees_due += $fees_array["unit_fees"];
                        $total_net_fees_due += $fees_array["net_fees_due"];
                    }
                } else {
                    $discount_rate = $fees_array["discount_rate"];
                    $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                    $total_gst_due += $fees_array["gst_amount"];
                    $total_unit_fees_due += $fees_array["unit_fees"];
                    $total_net_fees_due += $fees_array["net_fees_due"];
                }//sk9 end

                $status = $this->update_invoice_audit_trail($invoice_array->pymnt_due_id);

                if ($status) {

                    $status = $this->remove_invoice($invoice_array->pymnt_due_id);

                    if ($status) {

                        //list($status, $new_invoice_id) = $this->create_new_invoice($invoice_array->pymnt_due_id, $company_id, $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $fees_array['gst_rule'], $fees_array['gst_rate'], 'INVCOMALL');
                        //sk10
                        list($status, $new_invoice_id) = $this->create_new_invoice($invoice_array->pymnt_due_id, $company_id, (round($invoice_array->total_inv_amount, 2) + round($total_net_fees_due, 2)), ($invoice_array->total_unit_fees + $total_unit_fees_due), ($invoice_array->total_inv_discnt + $total_discount_due), ($invoice_array->total_inv_subsdy + $total_subsidy_amount_due), (round($invoice_array->total_gst, 2) + round($total_gst_due, 2)), $invoice_array->gst_rule, $invoice_array->gst_rate, 'INVCOMALL');


                        if ($status) {
// commented by skm 02-01-17 because of refunded status remark in invoice st                           
//                            $query1="select * from enrol_paymnt_recd where invoice_id='$previous_inv_id'";
//                            $query =  mysqli_query($query1);
//                           
//                            if($query)
//                            { 
//                                $invoice_id1 = $previous_inv_id;
//                                $invoice_id = $new_invoice_id;
//                             
//                                $sql = mysqli_query("update enrol_paymnt_recd set invoice_id='$new_invoice_id' where invoice_id='$invoice_id1'"); 
//                              
//                                $sql1 = mysqli_query("update enrol_pymnt_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'"); 
//                                
//                            }
//
//                            $query2="select * from enrol_refund where invoice_id='$previous_inv_id'";
//                            $query = mysqli_query($query2);
//
//                            if($query)
//                            { 
//                                $previous_inv_id=$previous_inv_id;
//                                $invoice_id = $new_invoice_id;
//                                
//                                $sql = mysqli_query("update enrol_refund set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
//                               
//                                $sql1 = mysqli_query("update enrol_refund_brkup_dt set invoice_id='$new_invoice_id' where invoice_id='$previous_inv_id'");
//                                
//                            }


                            $invoice_id = $new_invoice_id;

                            $status = $this->set_audittrail_newinvoice_num($invoice_array->pymnt_due_id, $new_invoice_id);

                            //$this->update_payment_recd($new_invoice_id, $invoice_array->invoice_id);pritm code commented by skm because it updates epr and epbt tables with new invoice id
                        }
                    }
                }
            } else {


                $data = array(
                    'user_id' => $trainee_id,
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($fees_array['unit_fees'], 4),
                    'total_amount_due' => round($fees_array['net_fees_due'], 2), //sk3
                    'discount_type' => $fees_array['discount_type'],
                    'discount_rate' => round($fees_array['discount_rate'], 4),
                    'gst_amount' => round($fees_array['gst_amount'], 2), //sk4
                    'att_status' => $att_status, //sk5
                    'subsidy_amount' => 0,
                    'subsidy_recd_date' => '0000-00-00'
                );
                $this->db->insert('enrol_pymnt_due', $data);



                $invoice_id = $this->generate_invoice_id();

                $data = array(
                    'invoice_id' => $invoice_id,
                    'pymnt_due_id' => $payment_due_id,
                    'inv_date' => $cur_date,
                    'inv_type' => 'INVCOMALL',
                    'company_id' => $company_id,
                    'total_inv_amount' => round($fees_array['net_fees_due'], 4),
                    'total_unit_fees' => round($fees_array['unit_fees'], 4),
                    'total_inv_discnt' => round($fees_array['discount_amount'], 4),
                    'total_inv_subsdy' => 0,
                    'total_gst' => round($fees_array['gst_amount'], 4),
                    'gst_rate' => round($fees_array['gst_rate'], 4),
                    'gst_rule' => $fees_array['gst_rule']
                );

                $this->db->insert('enrol_invoice', $data);
            }

            if ($pay_status == 'PAID') {

                $this->insert_payment_recd($invoice_id, $trainee_id, $parm_array, $loggedin_user_id);
            }
        } else {

            $status = FALSE;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /**

     * This method updates received details

     * @param type $invoice_id

     * @param type $trainee_id

     * @param type $parm_array

     * @param type $loggedin_user_id

     */
    public function insert_payment_recd($invoice_id, $trainee_id, $parm_array, $loggedin_user_id) {
        $cur_time = date('H:i:s'); //sk

        $payment_recd = array(
            'invoice_id' => $invoice_id,
            'recd_on' => date('Y-m-d ', strtotime($parm_array['paid_on'])) . $cur_time,
            'mode_of_pymnt' => $parm_array['payment_type'],
            'amount_recd' => round($parm_array['amount_recd'], 2),
            'cheque_number' => $parm_array['cheque_number'],
            'cheque_date' => $parm_array['cheque_date'],
            'bank_name' => strtoupper($parm_array['bank_name']),
            'recd_by' => $loggedin_user_id,
        );

        $this->db->insert('enrol_paymnt_recd', $payment_recd);

        $payment_recd_brkup = array(
            'invoice_id' => $invoice_id,
            'user_id' => $trainee_id,
            'amount_recd' => round($parm_array['amount_recd'], 2),
            'recd_on' => date('Y-m-d ', strtotime($parm_array['paid_on'])) . $cur_time
        );

        $this->db->insert('enrol_pymnt_brkup_dt', $payment_recd_brkup);
    }

    /**

     * Updates thew previous invoice id with the new invoice id

     * @param type $new_invoice_id

     * @param type $prev_invoice_id

     */
    public function update_payment_recd($new_invoice_id, $prev_invoice_id) {

        $payment_recd = array(
            'invoice_id' => $new_invoice_id,
        );

        $this->db->where('invoice_id', $prev_invoice_id);

        $this->db->update('enrol_paymnt_recd', $payment_recd);



        $this->db->where('invoice_id', $prev_invoice_id);

        $this->db->update('enrol_pymnt_brkup_dt', $payment_recd);
    }

    /**

     * This method removes the selected trainees from an invoice

     * @param type $tenant_id

     * @param type $logged_in_user_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $invoice_id

     * @param type $payment_due_id

     * @param type $seleced_trainee_list

     */
    public function remove_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $seleced_trainee_list) {

        $status = TRUE;

        if (empty($seleced_trainee_list)) {

            return FALSE;
        }
        //$curr_invoice_details = $this->get_invoice_details($payment_due_id);//commented beacuse we can use same data from get_current_invoice_data function

        $total_gst_due = 0;

        $total_unit_fees_due = 0;

        $total_net_fees_due = 0;

        $total_discount_due = 0;

        $total_subsidy_amount_due = 0;
        $data = $this->get_current_invoice_data($payment_due_id);

        $curr_invoice_details = json_decode($data);

        if (empty($curr_invoice_details->pymnt_due_id)) {// added by shubhranshu for blank data since attendance status 0 
            return FALSE;
        }

        $this->db->trans_start();

        foreach ($seleced_trainee_list as $user_id) {

            $status = $this->update_classenrol_audittrail($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);

            $this->remove_enrollment($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);

            $payments_result = $this->get_payment_due($payment_due_id, $user_id);

            $discount_rate = $payments_result->discount_rate;

            $total_discount_due += ($payments_result->class_fees * $discount_rate) / 100;

            $total_gst_due += $payments_result->gst_amount;

            $total_unit_fees_due += $payments_result->class_fees;

            $total_net_fees_due += $payments_result->total_amount_due;

            $total_subsidy_amount_due += $payments_result->subsidy_amount;

            $this->remove_payment_due($payment_due_id, $user_id);
        }

        $absent_trainee_present = $this->get_payment_due_absent($payment_due_id); ////added by shubhranshu for remove enrollment issue
        $status = $this->update_invoice_audit_trail($curr_invoice_details->pymnt_due_id);
        $due_to = 'Remove Enrollment From Company Invoice';
        $status = $this->enrol_invoice_view($curr_invoice_details->pymnt_due_id, $data, $logged_in_user_id, $due_to);

        if ($status) {

            $status = $this->remove_invoice($payment_due_id);

            if ($status && ($curr_invoice_details->total_inv_amount - $total_net_fees_due) != 0) {

                list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, (round($curr_invoice_details->total_inv_amount, 2) - round($total_net_fees_due, 2)), ($curr_invoice_details->total_unit_fees - $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt - $total_discount_due), ($curr_invoice_details->total_inv_subsdy - $total_subsidy_amount_due), (round($curr_invoice_details->total_gst, 2) - round($total_gst_due, 2)), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

                if ($status) {
                    /* update invoice id into invoice related table if invoice is paid and refund start */
                    $total_amount = (round($curr_invoice_details->total_inv_amount, 2) - round($total_net_fees_due, 2));
                    $invoice_id = $new_invoice_id;

                    $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                    $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id);
                }
            } else if ($status && (($curr_invoice_details->total_inv_amount - $total_net_fees_due) == 0) && (count($absent_trainee_present) > 0)) {///added by shubhransu from this block
                list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, (round($curr_invoice_details->total_inv_amount, 2) - round($total_net_fees_due, 2)), ($curr_invoice_details->total_unit_fees - $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt - $total_discount_due), ($curr_invoice_details->total_inv_subsdy - $total_subsidy_amount_due), (round($curr_invoice_details->total_gst, 2) - round($total_gst_due, 2)), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

                if ($status) {
                    /* update invoice id into invoice related table if invoice is paid and refund start */
                    $total_amount = (round($curr_invoice_details->total_inv_amount, 2) - round($total_net_fees_due, 2));
                    $invoice_id = $new_invoice_id;

                    $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                    $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    public function get_enroll_old_invoice($id, $inv) {
        $tenant_id = $this->tenant_id;
        $this->db->select('*');
        $this->db->from('enrol_invoice_view');
        $this->db->where('invoice_id', $inv);
        $this->db->where('pymnt_due_id', $id);
        $this->db->where('tenant_id', $tenant_id);
        $results = $this->db->get()->row();
        return $results;
    }

    public function check_paid_refund_invoice($invoice_id, $user_id) {
        $this->db->select('*');
        $this->db->from('enrol_pymnt_brkup_dt epbt');
        $this->db->join('enrol_refund_brkup_dt erbt', 'epbt.invoice_id = erbt.invoice_id and epbt.user_id = erbt.user_id');
        $this->db->where('epbt.invoice_id', $invoice_id);
        $this->db->where('epbt.user_id', $user_id);
        $sql = $this->db->get();
        return $sql->num_rows();
    }

    public function remove_paid_user($invoice_id, $user_id) {

        $this->db->where("invoice_id", $invoice_id);

        $this->db->where("user_id", $user_id);

        $this->db->delete("enrol_pymnt_brkup_dt");
    }

    public function remove_refund_user($invoice_id, $user_id) {

        $this->db->where("invoice_id", $invoice_id);

        $this->db->where("user_id", $user_id);

        $this->db->delete("enrol_refund_brkup_dt");
    }

    public function get_enroll_old_invoice1() {
        $tenant_id = $this->tenant_id;
        $this->db->select('eiv.invoice_id,eiv.pymnt_due_id,eiv.inv_date,eiv.reg_due_to,eiv.reg_by,eiv.regen_inv_id,tu.user_name');
        $this->db->from('enrol_invoice_view eiv');
        $this->db->join('tms_users tu', 'tu.user_id=eiv.reg_by');
        $this->db->where('eiv.tenant_id', $tenant_id);
        $this->db->order_by('eiv.invoice_id', 'desc');
        $results = $this->db->get()->result();
        return $results;
    }

    public function get_count_invoice($id) {

        $result = $this->db->select('*')->from('enrol_invoice ei')
                ->where('ei.pymnt_due_id', $id);


        $result = $this->db->get()->result();

        return $result;
    }

    private function set_viewinvoice_newinvoice_num($payment_due_id, $reg_inv_id) {

        $status = TRUE;
        $tenant_id = $this->tenant_id;
        $audit_array = array('regen_inv_id' => $reg_inv_id);
        $this->db->where("tenant_id", $tenant_id);
        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->trans_start();
        $status = $this->db->update("enrol_invoice_view", $audit_array);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    //private function enrol_invoice_view($payment_due_id,$data,$logged_in_user_id,$due_to,$due_to,$reg_inv_id = 0) {
    private function enrol_invoice_view($payment_due_id, $data, $logged_in_user_id, $due_to, $reg_inv_id = 0) {

        $tenant_id = $this->tenant_id;
        $insert_status = TRUE;

        $this->db->select("*");

        $this->db->from("enrol_invoice");

        $this->db->where("pymnt_due_id", $payment_due_id);

        $result = $this->db->get()->row();

        $audit_inv_data = array(
            'tenant_id' => $tenant_id,
            'invoice_id' => $result->invoice_id,
            'pymnt_due_id' => $result->pymnt_due_id,
            'inv_date' => $result->inv_date,
            'inv_type' => $result->inv_type,
            'company_id' => $result->company_id,
            'regen_inv_id' => $reg_inv_id,
            'total_inv_amount' => $result->total_inv_amount,
            'total_unit_fees' => $result->total_unit_fees,
            'total_inv_discnt' => $result->total_inv_discnt,
            'total_inv_subsdy' => $result->total_inv_subsdy,
            'total_gst' => $result->total_gst,
            'gst_rate' => $result->gst_rate,
            'gst_rule' => $result->gst_rule,
            'invoice_generated_on' => $result->invoice_generated_on,
            'invoiced_on' => $result->invoiced_on,
            'invoice_excess_amt' => $result->invoice_excess_amt,
            'invoice_details' => $data,
            'reg_by' => $logged_in_user_id,
            'reg_due_to' => $due_to
        );

        $this->db->trans_start();

        $this->db->insert('enrol_invoice_view', $audit_inv_data);


        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {

            $insert_status = FALSE;
        }

        return $insert_status;
    }

    /**

     * This method move the selected trainees from one company to other company

     * @param type $tenant_id

     * @param type $logged_in_user_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $invoice_id

     * @param type $payment_due_id

     * @param type $seleced_trainee_list
     * added by pritam

     */
    public function move_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $seleced_trainee_list) {
        $to_invoice_id = $this->input->post('to_comp_invoice_id');
        $to_payment_due_id = $this->input->post('to_comp_pymnt_due_id');
        $to_company_id = $this->input->post('to_company_id');
        $to_course_id = $this->input->post('to_course_id');
        $to_class_id = $this->input->post('to_class_id');
        if ($to_invoice_id == "") {
            return FALSE;
        }
//        if (empty($seleced_trainee_list)) 
//        {
//            return FALSE;
//        }
//        foreach($seleced_trainee_list as $user_id)
//        {
//            $check_enroll= $this->db->query("select * from class_enrol where tenant_id='$tenant_id' and course_id='$to_course_id' and class_id='$to_class_id' and user_id='$user_id'");
//              $count_enroll=$check_enroll->num_rows();
//            
//          
//            if($count_enroll>0)
//            {   
//                return FALSE;
//            }
//        }

        $check_invoice = $this->db->query("select invoice_id from enrol_invoice_audittrail where invoice_id='$invoice_id' or invoice_id='$to_invoice_id'");

        $count = $check_invoice->num_rows();
        if ($count == 0) {
            $status = TRUE;
            if (empty($seleced_trainee_list)) {

                return FALSE;
            }
            $payment_due_id;
            $curr_invoice_details = $this->get_invoice_details($payment_due_id);
            $total_gst_due = 0;
            $total_unit_fees_due = 0;
            $total_net_fees_due = 0;
            $total_discount_due = 0;
            $total_subsidy_amount_due = 0;
            $due_to = 'Move Trainee from one company Invoice to other company invoice';
            $data = $this->get_current_invoice_data($payment_due_id);
            $this->db->trans_start();
            $status = $this->enrol_invoice_view($payment_due_id, $data, $logged_in_user_id, $due_to);
            $sales = array();

            foreach ($seleced_trainee_list as $user_id) {

                //fetch sales executive 
                $data = $this->db->select('sales_executive_id')
                                ->from('class_enrol')
                                ->where("pymnt_due_id", $payment_due_id)
                                ->where("user_id", $user_id)
                                ->where("course_id", $course_id)
                                ->where('class_id', $class_id)
                                ->where("tenant_id", $tenant_id)
                                ->get()->row(0);
                $this->db->last_query();
                $sales[] = $data->sales_executive_id;
                //end

                $status = $this->update_classenrol_audittrail($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);

                $status = $this->remove_enrollment1($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);

                $payments_result = $this->get_payment_due($payment_due_id, $user_id);

                $discount_rate = $payments_result->discount_rate;

                $total_discount_due += ($payments_result->class_fees * $discount_rate) / 100;

                $total_gst_due += $payments_result->gst_amount;

                $total_unit_fees_due += $payments_result->class_fees;

                $total_net_fees_due += $payments_result->total_amount_due;

                $total_subsidy_amount_due += $payments_result->subsidy_amount;

                $status = $this->remove_payment_due1($payment_due_id, $user_id);
            }
            $status = $this->update_invoice_audit_trail($curr_invoice_details->pymnt_due_id);

            if ($status) {

                $status = $this->remove_invoice($payment_due_id);
                if ($status && ($curr_invoice_details->total_inv_amount - $total_net_fees_due) != 0) {

                    list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, ($curr_invoice_details->total_inv_amount - $total_net_fees_due), ($curr_invoice_details->total_unit_fees - $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt - $total_discount_due), ($curr_invoice_details->total_inv_subsdy - $total_subsidy_amount_due), ($curr_invoice_details->total_gst - $total_gst_due), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');
                    if ($status) {

                        $invoice_id = $new_invoice_id;

                        $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                        $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id); //s4
                    }
                }
            }
            if ($status) {

                $status = $this->move_to_company_enrollment($tenant_id, $logged_in_user_id, $to_course_id, $to_class_id, $to_company_id
                        , $to_invoice_id, $to_payment_due_id, $seleced_trainee_list, $sales);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {

                $status = FALSE;
            }
            return $status;
        } else {

            return FALSE;
        }
    }

// remove individual traine from enrollment
    // added by pritam

    public function remove_individual_enrollment($tenant_id, $logged_in_user_id, $user_id, $course_id, $class_id, $invoice_id, $payment_due_id) {
        $status = TRUE;
        $data = $this->get_current_individule_invoice_data($payment_due_id);
        $due_to = 'Remove Individual Enrollment';
        $this->db->trans_start();
        $status = $this->update_classenrol_audittrail($tenant_id, $payment_due_id, $user_id, $course_id, $class_id);
        $status = $this->enrol_invoice_view($payment_due_id, $data, $logged_in_user_id, $due_to);

        if ($status) {

            if ($status) {

                $this->db->where("pymnt_due_id", $payment_due_id);
                $this->db->where("user_id", $user_id);
                $this->db->where("course_id", $course_id);
                $this->db->where("class_id", $class_id);
                $this->db->where("tenant_id", $tenant_id);
                $status = $this->db->delete("class_enrol");
                if ($status) {

                    $this->db->where("pymnt_due_id", $payment_due_id);
                    $this->db->where("user_id", $user_id);
                    $status = $this->db->delete("enrol_pymnt_due");
                    $curr_invoice_details = $this->get_invoice_details($payment_due_id);
                    $status = $this->update_invoice_audit_trail($curr_invoice_details->pymnt_due_id);
                    if ($status) {

                        if ($status) {

                            if (!empty($payment_due_id) && !empty($invoice_id)) {
                                $this->db->where("pymnt_due_id", $payment_due_id);
                                $this->db->where("invoice_id", $invoice_id);
                                $status = $this->db->delete("enrol_invoice");
                            }
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    public function void_invoice($tenant_id, $logged_in_user_id, $invoice_id, $payment_due_id) {
        $status = TRUE;
        $data = array(
            'payment_status' => "VOID"
        );
        $this->db->trans_start();
        if ($status) {
            $this->db->where("pymnt_due_id", $payment_due_id);

            $this->db->where("tenant_id", $tenant_id);

            $status = $this->db->update("class_enrol", $data);
            $this->db->last_query();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /**

     * This function return the user details of selected trainees in remove enrollment.

     * @param type $seleced_trainee_list

     * @param type $tenant_id

     * @return type

     */
    public function user_details($seleced_trainee_list, $tenant_id) {

        $this->db->select("pers.first_name, pers.last_name, pers.user_id");

        $this->db->from("tms_users_pers pers");

        $this->db->where("pers.tenant_id", $tenant_id);

        $this->db->where_in("pers.user_id", $seleced_trainee_list);

        $result = $this->db->get();

        return $result->result();
    }

    /**

     * Removes an enrollment from class_enroll

     * @param type $tenant_id

     * @param type $payment_due_id

     * @param type $value

     * @param type $course_id

     * @param type $class_id

     * @return boolean

     */
    private function remove_enrollment($tenant_id, $payment_due_id, $user_id, $course_id, $class_id) {

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->where("course_id", $course_id);

        $this->db->where("class_id", $class_id);

        $this->db->where("tenant_id", $tenant_id);

        $this->db->delete("class_enrol");
    }

    private function remove_enrollment1($tenant_id, $payment_due_id, $user_id, $course_id, $class_id) {
        $status = TRUE;
        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->where("course_id", $course_id);

        $this->db->where("class_id", $class_id);

        $this->db->where("tenant_id", $tenant_id);
        $this->db->trans_start();

        $this->db->delete("class_enrol");

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $status = FALSE;
        }

        return $status;
    }

    /**

     * This method returns the payment details for a user.

     * @param type $payment_due_id

     * @param type $user_id

     * @return type

     */
    private function get_payment_due($payment_due_id, $user_id) {

        $this->db->select('*');

        $this->db->from('enrol_pymnt_due');

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);
        $this->db->where("att_status", 1);

        $result_set = $this->db->get()->row();

        return $result_set;
    }

    /////added by shubhranshu
    private function get_payment_due_absent($payment_due_id) {

        $this->db->select('*');

        $this->db->from('enrol_pymnt_due');

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("att_status", 0);

        $result_set = $this->db->get()->result();

        return $result_set;
    }

    /**

     * This method removes the payment due details

     * @param type $payment_due_id

     * @param type $user_id

     */
    private function remove_payment_due($payment_due_id, $user_id) {

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->delete("enrol_pymnt_due");
    }

    private function remove_payment_due1($payment_due_id, $user_id) {
        $status = TRUE;

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);
        $this->db->trans_start();

        $this->db->delete("enrol_pymnt_due");
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /**

     * To get trainee in a company but not enrolled in the selected class/ invoice.

     * @param type $tenant_id

     * @param type $payment_due_id

     * @param type $company_id          

     */
    public function trainee_not_enrolled_in_company_invoice($tenant_id, $course_id, $class_id, $company_id) {

        $result_set = $this->db->query("SELECT usr.tax_code, pers.user_id, pers.first_name, pers.last_name FROM tenant_company_users ten, tms_users_pers pers,tms_users usr "
                . "where ten.`user_acct_status` = 'ACTIVE'  AND ten.company_id = '$company_id' AND ten.`tenant_id` = '$tenant_id' AND "
                . "pers.tenant_id=ten.tenant_id AND pers.user_id = ten.user_id AND pers.tenant_id=usr.tenant_id AND pers.user_id = usr.user_id AND "
                . "ten.user_id  NOT IN (Select cls.user_id from class_enrol cls "
                . "where cls.tenant_id= '$tenant_id' and cls.`course_id` ='$course_id' and cls.`class_id` = '$class_id') AND usr.account_type='TRAINE'");





        return $result_set->result();
    }

    /**

     * This method adds the selected trainees to an existing company invoice

     * @param type $tenant_id

     * @param type $logged_in_user_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $invoice_id

     * @param type $payment_due_id

     * @param type $seleced_trainee_list

     */
    public function add_to_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $seleced_trainee_list) {


        $status = TRUE;
        if (empty($seleced_trainee_list)) {
            return FALSE;
        }
        $curr_invoice_details = $this->get_invoice_details($payment_due_id);
        $total_gst_due = 0;
        $total_unit_fees_due = 0;
        $total_net_fees_due = 0;
        $total_discount_due = 0;
        $total_subsidy_amount_due = 0;
        $due_to = 'Add Enrollment To Company Invoice';
        $data1 = $this->get_current_invoice_data($payment_due_id);
        $this->db->trans_start();
        $status = $this->enrol_invoice_view($payment_due_id, $data1, $logged_in_user_id, $due_to);
        $cur_date = date('Y-m-d');
        $crse_cls_detail = $this->get_class_detail($class_id, $course_id, $tenant_id);
        $str_class_status = $this->get_class_status($crse_cls_detail->class_status, $crse_cls_detail->class_start_datetime, $crse_cls_detail->class_end_datetime);

        $course = $course_id;
        $class = $class_id;
        $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);

        foreach ($seleced_trainee_list as $user_id) {
            if ($check_attendance > 0) {
                $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course, $class, $user_id);
                if ($check_attendance_trainee > 0) {
                    $training_score = 'C';
                    $att_status = 1;
                } else {
                    $training_score = 'ABS';
                    $att_status = 0;
                }
            } else {
                $att_status = 1;
            }
            // $fees_array = $this->fees_payable($user_id, $tenant_id, $course_id, $class_id, 0,$company_id, $logged_in_user_id);
            /* above commented beacuase it was not checking invoice discount(re-genrated) */
            $fees_array = $this->fees_payable_check_discount($user_id, $tenant_id, $course_id, $class_id, 0, $company_id, $payment_due_id, $logged_in_user_id); //SK1
            $previous_inv_id = $curr_invoice_details->invoice_id;
            $data = array(
                'tenant_id' => $tenant_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'user_id' => $user_id,
                'enrolment_type' => 'FIRST',
                'enrolment_mode' => 'COMPSPON',
                'pymnt_due_id' => $payment_due_id,
                'company_id' => $company_id,
                'enrolled_on' => $cur_date,
                'enrolled_by' => $logged_in_user_id,
                'tg_number' => '',
                'training_score' => $training_score,
                'payment_status' => 'NOTPAID',
                'sales_executive_id' => $logged_in_user_id,
                'class_status' => $str_class_status,
                'enrol_status' => 'ENRLBKD'
            );
            $this->db->insert('class_enrol', $data);

            $data = array(
                'user_id' => $user_id,
                'pymnt_due_id' => $payment_due_id,
                'class_fees' => round($fees_array["unit_fees"], 4), //sk1
                'total_amount_due' => round($fees_array["net_fees_due"], 2), //sk2
                'discount_type' => $fees_array["discount_type"],
                'discount_rate' => round($fees_array["discount_rate"], 4),
                'gst_amount' => round($fees_array["gst_amount"], 2), //sk3
                'subsidy_amount' => 0,
                'subsidy_recd_date' => '0000-00-00',
                'att_status' => $att_status
            );
            $this->db->insert('enrol_pymnt_due', $data);
            if ($check_attendance > 0) {
                if ($check_attendance_trainee > 0) {

                    $discount_rate = $fees_array["discount_rate"];
                    $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                    $total_gst_due += $fees_array["gst_amount"];
                    $total_unit_fees_due += $fees_array["unit_fees"];
                    $total_net_fees_due += round($fees_array["net_fees_due"], 2);
                }
            } else {
                $discount_rate = $fees_array["discount_rate"];
                $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                $total_gst_due += $fees_array["gst_amount"];
                $total_unit_fees_due += $fees_array["unit_fees"];
                $total_net_fees_due += round($fees_array["net_fees_due"], 2);
            }
        }
        $status = $this->update_invoice_audit_trail($payment_due_id);
        if ($status) {
            $status = $this->remove_invoice($payment_due_id);
            if ($status) {
                //sk4
                list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, (round($curr_invoice_details->total_inv_amount, 2) + round($total_net_fees_due, 2)), ($curr_invoice_details->total_unit_fees + $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt + $total_discount_due), ($curr_invoice_details->total_inv_subsdy + $total_subsidy_amount_due), (round($curr_invoice_details->total_gst, 2) + round($total_gst_due, 2)), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');
                if ($status) {
                    $invoice_id = $new_invoice_id;
                    $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                    $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id); //s4
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $status = FALSE;
        }
        return $status;
    }

    public function get_current_invoice_data($payment_due_id) {
        //starts
        $id = $payment_due_id;
        if (empty($id)) {
            return show_404();
        }

        $result = $this->get_enroll_invoice($id);

        $result->previous_inv_id = $this->get_enroll_prev_invoice($result->invoice_id);


        $result->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_state), ', ');
        $result->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_country), ', ');
        $result->inv_year = date('Y', strtotime($result->inv_date));
        $result->inv_date = date('d/m/Y', strtotime($result->inv_date));
        $result->discount_label = rtrim($this->course->get_metadata_on_parameter_id($result->discount_type), ', ');
        if ($result->total_gst) {
            $result->gst_label = 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($result->gst_rule), ', ');
        } else {
            $result->gst_label = 'GST OFF';
        }
        $course_manager = $this->course->get_managers($courses->crse_manager);
        $length = stripos($course_manager, ', ');
        $result->course_manager = (!empty($length)) ? substr($course_manager, 0, $length) : $course_manager;
        if ($result->company_id[0] == 'T') {
            $result->company_name = $result->tenant_name;
            $result->company_details->comp_state = $result->tenant_state;
            $result->company_details->comp_cntry = $result->tenant_country;
            $result->company_details->comp_phone = $result->tenant_contact_num;
            $result->company_details->comp_address = $result->tenant_address;
        } else {
            $company_details = $this->company->get_company_details($this->tenant_id, $result->company_id);
            $result->company_details = $company_details[0];
            $result->company_details->comp_state = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_state), ', ');
            $result->company_details->comp_cntry = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_cntry), ', ');

            $result->company_name = $company_details[0]->company_name;
        }

        //$result->discount_rate = round($result->discount_rate, 2);
        $result->total_inv_discnt = round($result->total_inv_discnt, 2);
        $result->total_unit_fees = round($result->total_unit_fees, 2);
        $result->total_inv_subsdy = round($result->total_inv_subsdy, 2);
        $result->gst_rate = round($result->gst_rate, 2);

        $result->indi_disc_total = ($result->class_fees * ($result->discount_rate / 100));

        $feesdue = $result->total_unit_fees - $result->total_inv_discnt;

        $result->payment_due_details = $this->classtraineemodel->get_company_payment_due_details($id);

        $company_received = $this->classtraineemodel->company_payment_recd($result->invoice_id);
        $user_paid = array();
        foreach ($company_received as $k => $v) {
            $user_paid[$v->user_id] = round($v->amount_recd, 2);
        }
        $company_refunded = $this->classtraineemodel->company_payment_refund($result->invoice_id);
        $user_refund = array();
        foreach ($company_refunded as $k => $v) {
            $user_refund[$v->user_id] = round($v->refund_amount, 2);
        }
        foreach ($result->payment_due_details as $key => $val) {
            $received = empty($user_paid[$val->user_id]) ? 0 : $user_paid[$val->user_id];
            $refunded = empty($user_refund[$val->user_id]) ? 0 : $user_refund[$val->user_id];
            $inv_amt+=$val->total_amount_due;
            $received_amt+=$received;
            $refunded_amt+=$refunded;
            if ((($val->total_amount_due + $refunded) - $received) <= 0) {
                $payment_label = 'PAID';
            } else {
                if ($refunded > 0) {
                    $payment_label = 'REFUNDED';
                } else {
                    if ($received == 0) {
                        $payment_label = 'NOT PAID';
                    } else if ($received > 0) {
                        $payment_label = 'PART PAID';
                    }
                }
            }
            $result->payment_due_details[$key]->payment_label = $payment_label;
        }
        $payable_amount = $inv_amt - $received_amt;
        $result->payble_amount = $inv_amt + $refunded_amt - $received_amt;
        $data = json_encode($result);
        return $data;
    }

    public function get_current_individule_invoice_data($payment_due_id) {
        $tenant_id = $this->tenant_id;
        $payid = $payment_due_id;
        $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
        $result = $this->classtraineemodel->get_enroll_invoice($payid);
        //added by pritam to generate previous invoice number
        $result->previous_inv_id = $this->classtraineemodel->get_enroll_prev_indvoice($payid);
        //
        $result->invoiced_on = ($result->invoiced_on == NULL || $result->invoiced_on == '0000-00-00 00:00:00') ? '' : date('d-m-Y', strtotime($result->invoiced_on));

        $result->personal_address_state = rtrim($this->course->get_metadata_on_parameter_id($result->personal_address_state), ', ');
        $result->personal_address_country = rtrim($this->course->get_metadata_on_parameter_id($result->personal_address_country), ', ');

        $result->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_state), ', ');
        $result->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_country), ', ');
        $result->total_inv_amount = number_format($result->total_inv_amount, 2, '.', '');
        $result->total_unit_fees = number_format($result->total_unit_fees, 2, '.', '');
        $result->gst_rate = number_format($result->gst_rate, 2, '.', '');
        $result->inv_year = date('Y', strtotime($result->inv_date));
        $result->inv_datinv_yeare = date('d/m/Y', strtotime($result->inv_date));
        $result->discount_rate_hidden = number_format($result->discount_rate, 4, '.', '');
        $result->discount_rate = number_format($result->discount_rate, 2, '.', '');
        $result->discount_label = rtrim($this->course->get_metadata_on_parameter_id($result->discount_type), ', ');
        $result->total_inv_discnt_hidden = number_format($result->total_inv_discnt, 4, '.', '');
        $result->total_inv_discnt = number_format($result->total_inv_discnt, 2, '.', '');
        $result->total_inv_subsdy = number_format(($result->total_inv_subsdy), 2, '.', '');

        $trainee_id = $this->classtraineemodel->get_trainee_by_pymnt_due_id($payid)->user_id;

        $gst_label = ($result->total_gst) ? 'GST ON, ' : 'GST OFF, ';

        if ($result->total_gst) {
            $gst_label .= rtrim($this->course->get_metadata_on_parameter_id($result->gst_rule), ', ');
        }
        $gst_label = rtrim($gst_label, ', ');
        $result->gst_label = $gst_label;
        $result->total_gst = number_format($result->total_gst, 2);
        $feesdue = $result->total_unit_fees - (($result->total_unit_fees * $result->discount_rate) / 100);
        $result->after_gst = $this->classtraineemodel->calculate_after_before_gst($result->gst_on_off, $result->gst_rule, $feesdue, $result->total_inv_subsdy, $result->gst_rate);

        if ($result->gst_on_off = 1 && $result->gst_rule == 'GSTBSD') {
            $result->after_discount = $result->total_unit_fees - $result->total_inv_discnt;
        } else {
            $result->after_discount = $result->total_unit_fees - $result->total_inv_discnt - $result->total_inv_subsdy;
        }

        $paid_details = $this->classtraineemodel->get_invoice_paid_details($result->invoice_id);

        $paid_arr = array();
        $paid_rcd_till_date = 0;
        //sfc_start
        $result->sfc_claimed = 0;
        foreach ($paid_details as $row) {

            if ($row->mode_of_pymnt == "SFC_SELF") {

                $mode_of_payment = explode('_', $row->mode_of_pymnt);
                $result->mode_of_pymnt = $mode_of_payment[1];
                $row->other_payment;
                $sfc_claimed = $row->sfc_claimed;

                $result->sfc_claimed = number_format($sfc_claimed, 2, '.', '');
            }
            if ($row->mode_of_pymnt == "SFC_ATO") {
                $mode_of_payment = explode('_', $row->mode_of_pymnt);
                $result->mode_of_pymnt = $mode_of_payment[1];
                $row->other_payment;
                $sfc_claimed = $row->sfc_claimed;

                $result->sfc_claimed = number_format($sfc_claimed, 2, '.', '');
            }
        }
        //sfc_start
        if (!empty($paid_details)) {
            $label = 'active';
            foreach ($paid_details as $row) {
                $mode_ext = ($row->mode_of_pymnt == 'CHQ') ? ' Chq#: ' . $row->cheque_number : '';
                $mode = rtrim($this->course->get_metadata_on_parameter_id($row->mode_of_pymnt), ', ');
                $paid_arr[] = array(
                    'recd_on' => date('d/m/Y', strtotime($row->recd_on)),
                    'mode' => $mode . $mode_ext,
                    'amount' => '$ ' . number_format($row->amount_recd, 2, '.', '') . ' SGD',
                );
                $paid_rcd_till_date = $row->amount_recd + $paid_rcd_till_date;
            }
            $total_paid = $paid_rcd_till_date;

            $result->refund_details = $refund_details = $this->classtraineemodel->get_refund_paid_details($result->invoice_id);
            $refund_amount = 0;
            foreach ($refund_details as $k => $row) {
                $row->mode_of_refund;

                if ($row->refnd_reason != 'OTHERS') {
                    $result->refund_details[$k]->refnd_reason = $this->course->get_metadata_on_parameter_id($row->refnd_reason);
                } else {
                    $result->refund_details[$k]->refnd_reason = 'Others (' . $row->refnd_reason_ot . ')';
                }

                $result->refund_details[$k]->refund_on = date('d/m/Y', strtotime($row->refund_on));
                $result->refund_details[$k]->mode_of_refund = $this->course->get_metadata_on_parameter_id($row->mode_of_refund);
                $refund_amount = $refund_amount + $row->amount_refund;
            }


            $paid_rcd_till_date = $paid_rcd_till_date - $refund_amount;

            $result->paid_rcd_till_date = number_format($paid_rcd_till_date, 2, '.', '');

            $course_manager = $this->course->get_managers($result->crse_manager, 1);
            $stripos = stripos($course_manager, ', ');
            $result->course_manager = (empty($stripos)) ? $course_manager : substr($course_manager, 0, $stripos);
            $result->ClassLoc = $this->get_classroom_location($result->classroom_location, $result->classroom_venue_oth);

            $result->class_start = date('M d, Y h:i A', strtotime($result->class_start_datetime));
            $result->courseLevel = rtrim($this->course->get_metadata_on_parameter_id($result->certi_level), ', ');
            $invoice = $this->classtraineemodel->get_invoice_for_class_trainee($result->class_id, $result->user_id);
            $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $result->user_id, $tenant_id);
            $name = $trainee_name->first . ' ' . $trainee_name->last;
            $trainee = ($trainee_name->gender == 'MALE') ? 'Mr.' . $name : 'Ms.' . $name;
            $invoice->recd_on_year = date('Y', strtotime($invoice->recd_on));
            $invoice->recd_on = date('d/m/Y', strtotime($invoice->recd_on));
            $invoice->mode_of_pymnt = rtrim($this->course->get_metadata_on_parameter_id($invoice->mode_of_pymnt), ', ');
        } else {
            $label = 'inactive';
        }
        $result->att_status;
        $result->enrolment_mode;
        if ((($result->total_inv_amount + $refund_amount) - $total_paid) == 0) {
            $payment_label = 'PAID';
        } else {
            if ($refund_amount > 0) {
                $payment_label = 'REFUNDED';
            } elseif ($total_paid == 0) {
                $payment_label = 'NOT PAID';
            }
        }
        if ($result->att_status == 0 && $result->enrolment_mode != "COMPSPON") {
            $payment_label = $result->payment_status;
        }
        // echo $paid_rcd_till_date."/".$result->sfc_claimed."/".$result->total_inv_amount."/<br />";

        $result->payble = $result->total_inv_amount - $paid_rcd_till_date;

        $result->subsidy_recd_date = (($result->subsidy_recd_date == '0000-00-00') || ($result->subsidy_recd_date == null)) ? '' : date('d-m-Y', strtotime($result->subsidy_recd_date));
        $result->payment_label = $payment_label;
        $subsidy_type = $this->classtraineemodel->get_subsidy_type($this->tenant_id);
        $subsidy_type_label = $this->classtraineemodel->get_subsidy_type_label($this->tenant_id, $result->subsidy_type_id);
        $subsidy_type_label = empty($subsidy_type_label) ? 'NA' : $subsidy_type_label;
        $res = array('data' => $result, 'recd' => $paid_arr, 'label' => $label, 'tenant' => $tenant_details,
            'invoice' => $invoice, 'trainee' => $trainee, 'subsidy_type' => $subsidy_type, 'subsidy_type_label' => $subsidy_type_label);

        $data = json_encode($res);
        return $data;
    }

    /**

     * This method adds the selected trainees to move an existing company invoice

     * @param type $tenant_id

     * @param type $logged_in_user_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $invoice_id

     * @param type $payment_due_id

     * @param type $seleced_trainee_list
     * added by pritam

     */
    private function move_to_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $seleced_trainee_list, $sales) {
        $status = TRUE;
        if (empty($seleced_trainee_list)) {

            return FALSE;
        }
        $curr_invoice_details = $this->get_invoice_details($payment_due_id);

        $total_gst_due = 0;

        $total_unit_fees_due = 0;

        $total_net_fees_due = 0;

        $total_discount_due = 0;

        $total_subsidy_amount_due = 0;

        $cur_date = date('Y-m-d');

        $crse_cls_detail = $this->get_class_detail($class_id, $course_id, $tenant_id);

        $str_class_status = $this->get_class_status($crse_cls_detail->class_status, $crse_cls_detail->class_start_datetime, $crse_cls_detail->class_end_datetime);

        $data = $this->get_current_invoice_data($payment_due_id);
        $due_to = 'Move Trainee from one company Invoice to other company invoice';
        $this->db->trans_start();
        $status = $this->enrol_invoice_view($payment_due_id, $data, $logged_in_user_id, $due_to);


        $count = 0;

        foreach ($sales as $key => $value) {
            if ($value != '') {
                $count++;
            }
        }


        if ($count > 0) {

            $user_sales = array_combine($seleced_trainee_list, $sales);
            foreach ($user_sales as $user_id => $sales) {

                $sales_course = $this->db->query("select * from course_sales_exec where tenant_id='$tenant_id' and course_id='$course_id' and user_id='$sales'");
                $count = $sales_course->num_rows();
                if ($count <= 0) {
                    $sales_details = $this->db->select('*')->from('course_sales_exec')->where("user_id", $sales)
                                    ->where("tenant_id", $tenant_id)
                                    ->get()->row(0);

                    $sales_array = array(
                        'tenant_id' => $tenant_id,
                        'course_id' => $course_id,
                        'user_id' => $sales,
                        'commission_rate' => '5',
                        'status' => $sales_details->status,
                        'acti_date_time' => $sales_details->acti_date_time,
                        'deacti_date_time' => $sales_details->deacti_date_time,
                        'deacti_reason' => $sales_details->deacti_reason,
                        'deacti_reason_oth' => $sales_details->deacti_reason_oth,
                        'deacti_by' => $sales_details->deacti_by,
                        'assigned_on' => $sales_details->assigned_on,
                        'assigned_by' => $sales_details->assigned_by,
                        'last_modified_by' => $sales_details->last_modified_by,
                        'last_modified_on' => $sales_details->last_modified_on
                    );
                    $this->db->insert('course_sales_exec', $sales_array);
                }
                $fees_array = $this->fees_payable($user_id, $tenant_id, $course_id, $class_id, 0, $company_id, $logged_in_user_id);
                $course = $course_id;
                $class = $class_id;
                $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);
                if ($check_attendance > 0) {
                    $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course, $class, $user_id);
                    if ($check_attendance_trainee > 0) {
                        $training_score = 'C';
                        $att_status = 1;
                    } else {
                        $training_score = 'ABS';
                        $att_status = 0;
                    }
                } else {
                    $att_status = 1;
                }
                $data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $course_id,
                    'class_id' => $class_id,
                    'user_id' => $user_id,
                    'enrolment_type' => 'FIRST',
                    'enrolment_mode' => 'COMPSPON',
                    'pymnt_due_id' => $payment_due_id,
                    'company_id' => $company_id,
                    'enrolled_on' => $cur_date,
                    'enrolled_by' => $logged_in_user_id,
                    'tg_number' => '',
                    'training_score' => $training_score,
                    'payment_status' => 'NOTPAID',
                    'sales_executive_id' => $sales,
                    'class_status' => $str_class_status,
                    'enrol_status' => 'ENRLBKD'
                );

                $this->db->insert('class_enrol', $data);



                $data = array(
                    'user_id' => $user_id,
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($fees_array["unit_fees"], 4),
                    'total_amount_due' => round($fees_array["net_fees_due"], 4),
                    'discount_type' => $fees_array["discount_type"],
                    'discount_rate' => round($fees_array["discount_rate"], 4),
                    'gst_amount' => round($fees_array["gst_amount"], 4),
                    'subsidy_amount' => 0,
                    'subsidy_recd_date' => '0000-00-00',
                    'att_status' => $att_status
                );

                $this->db->insert('enrol_pymnt_due', $data);

                $discount_rate = $fees_array["discount_rate"];
                if ($check_attendance > 0) {
                    if ($check_attendance_trainee > 0) {
                        $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                        $total_gst_due += $fees_array["gst_amount"];
                        $total_unit_fees_due += $fees_array["unit_fees"];
                        $total_net_fees_due += $fees_array["net_fees_due"];
                    }
                } else {
                    $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                    $total_gst_due += $fees_array["gst_amount"];
                    $total_unit_fees_due += $fees_array["unit_fees"];
                    $total_net_fees_due += $fees_array["net_fees_due"];
                }
            }
        } else {

            foreach ($seleced_trainee_list as $user_id) {
                $fees_array = $this->fees_payable($user_id, $tenant_id, $course_id, $class_id, 0, $company_id, $logged_in_user_id);
                $course = $course_id;
                $class = $class_id;
                $check_attendance = $this->check_attendance_row($tenant_id, $course, $class);
                if ($check_attendance > 0) {
                    $check_attendance_trainee = $this->check_attendance_trainee($tenant_id, $course, $class, $user_id);
                    if ($check_attendance_trainee > 0) {
                        $training_score = 'C';
                        $att_status = 1;
                    } else {
                        $training_score = 'ABS';
                        $att_status = 0;
                    }
                } else {
                    $att_status = 1;
                }
                $data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $course_id,
                    'class_id' => $class_id,
                    'user_id' => $user_id,
                    'enrolment_type' => 'FIRST',
                    'enrolment_mode' => 'COMPSPON',
                    'pymnt_due_id' => $payment_due_id,
                    'company_id' => $company_id,
                    'enrolled_on' => $cur_date,
                    'enrolled_by' => $logged_in_user_id,
                    'tg_number' => '',
                    'training_score' => $training_score,
                    'payment_status' => 'NOTPAID',
                    'sales_executive_id' => '',
                    'class_status' => $str_class_status,
                    'enrol_status' => 'ENRLBKD'
                );

                $this->db->insert('class_enrol', $data);
                $data = array(
                    'user_id' => $user_id,
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($fees_array["unit_fees"], 4),
                    'total_amount_due' => round($fees_array["net_fees_due"], 4),
                    'discount_type' => $fees_array["discount_type"],
                    'discount_rate' => round($fees_array["discount_rate"], 4),
                    'gst_amount' => round($fees_array["gst_amount"], 4),
                    'subsidy_amount' => 0,
                    'subsidy_recd_date' => '0000-00-00',
                    'att_status' => $att_status
                );

                $this->db->insert('enrol_pymnt_due', $data);

                $discount_rate = $fees_array["discount_rate"];

                if ($check_attendance > 0) {
                    if ($check_attendance_trainee > 0) {
                        $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                        $total_gst_due += $fees_array["gst_amount"];
                        $total_unit_fees_due += $fees_array["unit_fees"];
                        $total_net_fees_due += $fees_array["net_fees_due"];
                    }
                } else {
                    $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;
                    $total_gst_due += $fees_array["gst_amount"];
                    $total_unit_fees_due += $fees_array["unit_fees"];
                    $total_net_fees_due += $fees_array["net_fees_due"];
                }
            }
        }

        $status = $this->update_invoice_audit_trail($payment_due_id);

        if ($status) {


            $status = $this->remove_invoice($payment_due_id);

            if ($status) {

                list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $company_id, ($curr_invoice_details->total_inv_amount + $total_net_fees_due), ($curr_invoice_details->total_unit_fees + $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt + $total_discount_due), ($curr_invoice_details->total_inv_subsdy + $total_subsidy_amount_due), ($curr_invoice_details->total_gst + $total_gst_due), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL');

                if ($status) {

                    $invoice_id = $new_invoice_id;

                    $status = $this->set_audittrail_newinvoice_num($payment_due_id, $new_invoice_id);
                    $status = $this->set_viewinvoice_newinvoice_num($payment_due_id, $new_invoice_id); //s4
                }
            }
        }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }



        return $status;
    }

    /**

     * This methods returns back the status string of a class

     * @param type $class_status

     * @param type $class_start_date

     * @param type $class_end_date

     * @return string

     */
    public function get_class_status($class_status, $class_start_date, $class_end_date) {

        $cur_date = date('Y-m-d');

        $str_class_status = '';

        if ($class_status == 'INACTIV') {

            $str_class_status = 'INACTIV';
        } else if ($class_start_date > $cur_date) {

            $str_class_status = 'YTOSTRT';
        } else if ($class_end_date < $cur_date) {

            $str_class_status = 'COMPLTD';
        } else {

            $str_class_status = 'IN_PROG';
        }

        return $str_class_status;
    }

    public function get_class_detail($class_id, $course_id, $tenant_id) {

        $this->db->select("crse.*, cls.*");

        $this->db->from("course crse");

        $this->db->join("course_class cls", "crse.course_id = cls.class_id");

        $this->db->where("crse.tenant_id", $tenant_id);

        $this->db->where_in("cls.class_id", $class_id);

        $result = $this->db->get();

        return $result->result();
    }

    /**

     * This function for getting credit notes.

     * @param type $type

     * @param type $tenant_id

     * @param type $records_per_page

     * @param type $offset

     * @param type $field

     * @param type $order_by

     * @return type

     */
    public function get_credit_note_list($type = 'data', $credit_note_number, $tenant_id, $limit, $offset, $sort_by, $sort_order) {

        $this->db->select("crn.*");

        $this->db->from("credit_notes crn");

        if (!empty($credit_note_number)) {

            $this->db->like('crn.credit_note_number', $credit_note_number, 'both');
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('enrol_invoice ei', 'ei.invoice_id=crn.ori_invoice_number');

            $this->db->join('class_enrol ce', "ce.pymnt_due_id = ei.pymnt_due_id");

            $this->db->join('course crse', 'crse.course_id=ce.course_id');

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);

            $this->db->group_by("crn.credit_note_number");
        }

        if ($sort_by) {

            $this->db->order_by($sort_by, $sort_order);
        } else {

            $this->db->order_by('CAST(crn.credit_note_number AS decimal)', 'DESC');
        }

        if ($type == 'data') {

            if ($limit == $offset) {

                $this->db->limit($offset);
            } else if ($limit > 0) {

                $limitvalue = $offset - $limit;

                $this->db->limit($limit, $limitvalue);
            }
        }

        $query = $this->db->get();

        if ($type == 'count') {

            $result_set = $query->num_rows();
        } else {

            $result_set = $query->result();
        }

        return $result_set;
    }

    /**

     * This method is for insert into credit_notes.

     * @param type $data

     */
    public function insert_credit_notes($data) {

        $status = TRUE;

        if (empty($data)) {

            $status = FALSE;
        } else {

            $this->db->trans_start();

            $this->db->insert("credit_notes", $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {

                $status = FALSE;
            }
        }

        return $status;
    }

    /**

     * This function returns individual credit note details.

     * @param type $credit_note_number

     * @return type

     */
    public function get_credit_note($credit_id) {

        $this->db->select("*");

        $this->db->from("credit_notes");

        $this->db->where("credit_id", $credit_id);

        $result_set = $this->db->get();

        return $result_set->row();
    }

    /**

     * This method will format the date for UI Display in change individual invoice to comapny invoice on enrollment.

     * @param type $data

     * @return type

     */
    public function date_format_change($data) {

        foreach ($data as $key => $value) {

            $data[$key]->class_start_datetime = date('d-m-Y', strtotime($value->class_start_datetime));

            $data[$key]->class_end_datetime = date('d-m-Y', strtotime($value->class_end_datetime));

            $data[$key]->enrolled_on = date('d-m-Y', strtotime($value->enrolled_on));
        }

        return $data;
    }

    /**

     * This method used for unique checking of credit note number.

     * @param type $credit_number

     * @return type

     */
    public function unique_check_credit_number($credit_number) {

        $this->db->select("credit_note_number");

        $this->db->from("credit_notes");

        $this->db->where("credit_note_number", $credit_number);

        $result = $this->db->get();

        return $result->num_rows();
    }

    /**

     * This method to get invoice id for enrolled class_id and user_id

     */
    public function get_invoice_id_for_class_trainee($class_id, $user_id) {

        $data = $this->db->select('ei.invoice_id, ei.pymnt_due_id')->from('class_enrol ce')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->where('ce.class_id', $class_id)
                        ->where('ce.user_id', $user_id)->order_by('ei.invoice_id', 'DESC')->get()->row();

        return $data->invoice_id;
    }

    /* check attendance is marked or not
     */

    public function check_attendance_row($tenant_id, $course_id, $class_id) {
        $this->db->select('*');
        $this->db->from('class_attendance ca');
        $this->db->where('ca.tenant_id', $tenant_id);
        $this->db->where('ca.course_id', $course_id);
        $this->db->where('ca.class_id', $class_id);
        $query = $this->db->get();

        $query->num_rows();
        return $query->num_rows();
    }

    /* check attendance is marked or not for trainee
     */

    public function check_attendance_trainee($tenant_id, $course_id, $class_id, $user_id) {
        $query = $this->db->query("select * from class_schld where course_id='$course_id' and class_id='$class_id' and tenant_id='$tenant_id'");
        $query->num_rows();
        if ($query->num_rows() > 0) {
            $query = $this->db->query("select att.user_id as user_id,
                    SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) 
                    / (select count(cs.class_id) from class_schld cs where cs.course_id='$course_id' and cs.class_id='$class_id' and cs.tenant_id='$tenant_id') as attendence
            from class_attendance att
            join course_class cc on cc.class_id='$class_id' and cc.course_id='$course_id' and cc.tenant_id='$tenant_id'
            where att.class_id='$class_id' and att.course_id='$course_id' and att.user_id='$user_id'
            group by att.user_id,att.class_id
            having attendence >= 0.75");
            $this->db->last_query();
        } else {
            $sql = $this->db->query("select class_start_datetime,class_end_datetime from course_class where course_id='$course_id' and class_id='$class_id'");
            foreach ($sql->result_array() as $row) {
                $row['class_start_datetime'];
                $row['class_start_datetime'];
            }
            $query = $this->db->query("select att.user_id as user_id,
                            SUM(COALESCE(att.session_01 + att.session_02,att.session_01,att.session_02, 0 )) 
                            / (count(att.user_id)* cc.class_session_day) as attendence
            from class_attendance att
            join course_class cc on cc.class_id='$class_id' and cc.course_id='$course_id' and cc.tenant_id='$tenant_id'
            where att.class_id='$class_id' and att.course_id='$course_id' and att.user_id='$user_id'
            group by att.user_id,att.class_id
            having attendence >= 0.75");
            $this->db->last_query();
        }

        return $query->num_rows();
    }

    /* check competent in trainer feedback
     */

    public function check_competent($tenant_id, $course_id, $class_id, $user_id) {
        $this->db->select('*');
        $this->db->from('trainer_feedback tf');
        $this->db->where('tf.tenant_id', $tenant_id);
        $this->db->where('tf.course_id', $course_id);
        $this->db->where('tf.class_id', $class_id);
        $this->db->where('tf.user_id', $user_id);
        $this->db->where('tf.feedback_question_id', 'COMYTCOM');
        $this->db->where('tf.feedback_answer', 'C');

        $query = $this->db->get();
        $query->num_rows();
        return $query->num_rows();
    }

    /**

     * function to get enrol_payment_due

     * Reason: individual_discount_rate

     */
    public function get_enrol_payment_due_details($payid, $user) {

        $this->db->select('epd.discount_rate')->from('enrol_pymnt_due epd')
                ->where('epd.pymnt_due_id', $payid)->where('epd.user_id', $user);

        return $this->db->get()->row();
    }

    /**

     * This function to get refunded breakup details by invoice_id

     */
    public function get_refund_brkup($inv_id) {

        $this->db->select('user_id')->from('enrol_refund_brkup_dt')->where('invoice_id', $inv_id);

        return $this->db->get()->result();
    }

    /**

     * This method returns all subsidy type.

     * @param type $tenant_id

     * @return type

     */
    public function get_subsidy_type($tenant_id) {

        $this->db->select("*");

        $this->db->from("tenant_subsidy")->where("tenant_id", $tenant_id)->order_by("last_modified_on");

        return $this->db->get()->result();
    }

    /**

     * This method return subsidy_amount 

     * @param type $tenant_id

     * @param type $subsidy_type

     * @return type

     */
    public function get_subsidy_amount($tenant_id, $subsidy_type) {

        $this->db->select("subsidy_amount");

        $this->db->from("tenant_subsidy")
                ->where("tenant_id", $tenant_id)
                ->where("subsidy_id", $subsidy_type)
                ->order_by("last_modified_on");

        $subsidy_amount = $this->db->get()->row("subsidy_amount");

        return $subsidy_amount;
    }

    /**

     * Regenerate Invoice

     */
    public function re_generate_indi_invoice() {

        $cur_date = date('Y-m-d H:i:s');

        $post_invoice = $this->input->post('invoice_indi_hidden_id');

        $select_reinvoice = $this->input->post('select_indi_reinvoice');

        if (in_array(2, $select_reinvoice)) {

            $discount_totalamt = $this->input->post('regen2_indi_form_dis_amt');

            $discount_rate = $this->input->post('regen2_indi_form_dis_perc');

            $discount_type = $this->input->post('regen2_indi_form_dis_type');
        } else {

            $discount_totalamt = $this->input->post('regen2_indi_hid_dis_amt');

            $discount_rate = $this->input->post('regen2_indi_hid_dis_perc');

            $discount_type = $this->input->post('regen2_indi_hid_dis_type');
        }

        $this->db->select('ei.*, epd.*');

        $this->db->from('enrol_invoice ei');

        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ei.pymnt_due_id');

        $this->db->where('ei.invoice_id', $post_invoice);

        $this->db->group_by('ei.invoice_id');

        $res = $this->db->get()->result();

        $input = $res[0];

        if ($this->input->post('inv_class_fee') == 1) {

            $class_id = $this->db->select('class_id')->from('class_enrol')->where('pymnt_due_id', $input->pymnt_due_id)->get()->row('class_id');

            $input->class_fees = $this->db->select('class_fees')->from('course_class')->where('class_id', $class_id)->get()->row('class_fees');
        }

        $trainee = $this->input->post('trainee_indi');

        $subsidy_type_arr = $this->input->post('indi_subsidy_type');

        if (in_array(1, $select_reinvoice)) {

            $amount_paying = $this->input->post('amount_indi_paying');

            $amount_recd_paying = $this->input->post('amount_indi_recd_paying');
        } else {

            $amount_paying = $this->input->post('amount_indi_check');

            $amount_recd_paying = $this->input->post('amount_indi_recd_check');
        }

        $gst_on_off = ($input->total_gst == 0) ? 0 : 1;

        $subsidy_after_before = $input->gst_rule;

        if (!empty($discount_totalamt)) {

            $discount_total = round(($discount_totalamt / count($trainee)), 4);

            $discount_rate = ($discount_total * 100) / $input->class_fees;

            $discount_rate = round($discount_rate, 4);
        } else {

            $discount_rate = round($discount_rate, 4);

            $discount_total = ( $discount_rate * $input->class_fees) / 100;

            $discount_total = round($discount_total, 4);
        }

        $feesdue = $input->class_fees - ($discount_total);

        $feesdue = round($feesdue, 4);

        $company_net_due = 0;

        $company_discount = 0;

        $company_subsidy = 0;

        $company_total_gst = 0;
        $logged_in_user_id = $this->user->user_id;
        $data = $this->get_current_individule_invoice_data($input->pymnt_due_id);
        if (in_array(1, $select_reinvoice)) {
            $val = 'subsidy';
        } else {
            $val = 'discount';
        }
        $due_to = 'Regenrated Invoice due to changes in ' . $val;
        $status = $this->enrol_invoice_view($input->pymnt_due_id, $data, $logged_in_user_id, $due_to);

        foreach ($trainee as $k => $row) {

            $user_id = $k;

            $subsidy_amount = $amount_paying[$user_id];

            $subsidy_type = $subsidy_type_arr[$user_id];

            $subsidy_recd = $amount_recd_paying[$user_id];

            $subsidy_recd_on = ($subsidy_recd == '') ? '' : date('Y-m-d', strtotime($subsidy_recd));



            $netdue = $this->calculate_net_due($gst_on_off, $subsidy_after_before, $feesdue, $subsidy_amount, $input->gst_rate);

            if ($netdue < 0) {

                $this->session->set_flashdata("error", "The net amount is negative. Unable to regenerate.");

                redirect('accounting/generate_invoice');
            }

            $totalgst = $this->calculate_gst($gst_on_off, $subsidy_after_before, $feesdue, $subsidy_amount, $input->gst_rate);

            $data = array(
                'class_fees' => $input->class_fees,
                'total_amount_due' => round($netdue, 4),
                'discount_type' => $discount_type,
                'discount_rate' => round($discount_rate, 4),
                'subsidy_type_id' => $subsidy_type,
                'subsidy_amount' => round($subsidy_amount, 4),
                'subsidy_recd_date' => $subsidy_recd_on,
                'gst_amount' => round($totalgst, 4),
            );

            if ($row == 2) {

                $data['subsidy_modified_on'] = $cur_date;
            }

            $this->db->where('user_id', $user_id);

            $this->db->where('pymnt_due_id', $input->pymnt_due_id);

            $this->db->update('enrol_pymnt_due', $data);



            $company_total_gst = $company_total_gst + round($totalgst, 4); ### added by dummy

            $company_net_due = $company_net_due + round($netdue, 4);

            $company_discount = $company_discount + round($discount_total, 4);

            $company_subsidy = $company_subsidy + round($subsidy_amount, 4);
        }

        $invoice_id = $this->generate_invoice_id();

        $data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $input->pymnt_due_id,
            'inv_date' => $cur_date,
            'inv_type' => 'INVINDV',
            'company_id' => $input->company_id,
            'total_inv_amount' => round($company_net_due, 4),
            'total_unit_fees' => round($input->class_fees, 4),
            'total_inv_discnt' => round($company_discount, 4),
            'total_inv_subsdy' => round($company_subsidy, 4),
            'total_gst' => round($company_total_gst, 4),
            'gst_rate' => round($input->gst_rate, 4),
            'gst_rule' => $input->gst_rule,
            'invoiced_on' => $input->invoiced_on,
        );

        $this->db->insert('enrol_invoice', $data);

        $data = array(
            'invoice_id' => $input->invoice_id,
            'pymnt_due_id' => $input->pymnt_due_id,
            'inv_date' => $input->inv_date,
            'inv_type' => 'INVINDV',
            'company_id' => $input->company_id,
            'regen_inv_id' => $invoice_id,
            'total_inv_amount' => $input->total_inv_amount,
            'total_unit_fees' => $input->total_unit_fees,
            'total_inv_discnt' => $input->total_inv_discnt,
            'total_inv_subsdy' => $input->total_inv_subsdy,
            'total_gst' => round($input->total_gst, 4),
            'gst_rate' => round($input->gst_rate, 4),
            'gst_rule' => $input->gst_rule,
            'invoice_generated_on' => $cur_date,
            'invoiced_on' => $input->invoiced_on,
            'invoice_excess_amt' => $input->invoice_excess_amt
        );

        $this->db->insert('enrol_invoice_audittrail', $data);

        // $this->db->where('invoice_id', $post_invoice);
        //$delete_result = $this->db->delete('enrol_invoice');

        if (!empty($post_invoice)) {
            $this->db->where('invoice_id', $post_invoice);
            $delete_result = $this->db->delete('enrol_invoice');
        }

        $this->set_viewinvoice_newinvoice_num($input->pymnt_due_id, $invoice_id);
        return $invoice_id;
    }

    /**

     * Function used for subsidy type label.

     * @param type $tenant_id

     * @param type $subsidy_id

     * @return type

     */
    public function get_subsidy_type_label($tenant_id, $subsidy_id) {

        $this->db->select("subsidy_type");

        $this->db->from("tenant_subsidy")->where("tenant_id", $tenant_id)->where("subsidy_id", $subsidy_id);

        return $this->db->get()->row("subsidy_type");
    }

    /**

     * For generating tenent based invoice id.

     * @return string

     */
//    private function generate_invoice_id() {
//
//        $pre_fix_array = array("T01" => "T01", "T02" => "XPR", "T03" => "CAI", "T04" => "FL", "T12" => "XPR.A.","T16" => "XPR.B.","T22" => "CBLD","T20" => "WBLB","T17" => "EI");
//
//        $lookup_table = array("T01" => "test_invoice_id", "T02" => "xprienz_invoice_id", "T03" => "carrie_invoice_id", "T04" => "focus_invoice_id", "T12" => "xprienz2_invoice_id","T16" => "xprienz3_invoice_id","T22" => "cbld_invoice_id","T20" => "wablab_invoice_id","T17" => "ei_invoice_id");
//
//        $tenant_id = $this->tenant_id;
//
//        $invoice_id_tmp = get_max_lookup($lookup_table[$tenant_id]);
//
//        $invoice_id = $pre_fix_array[$tenant_id] . $invoice_id_tmp;
//
//        return $invoice_id;
//    }

    private function generate_invoice_id() {

        //$date_array = explode("-",$class_start_date);

        $pre_fix_array = array("T01" => "T01", "T02" => "XPR", "T03" => "CAI", "T04" => "FL", "T12" => "XPR.A.", "T16" => "XPR.B.", "T17" => "EVI", "T20" => "WABLAB", "T23" => "DEMO", "T24" => "RLIS" , "T18" => "SSI", "T25" => "FGE", "T26" => "CHIE", "T27" => "ASAS", "T28" => "MCLS", "T29" => "ABTTH");

        $lookup_table = array("T01" => "test_invoice_id", "T02" => "xprienz_invoice_id", "T03" => "carrie_invoice_id", "T04" => "focus_invoice_id", "T12" => "xprienz2_invoice_id", "T16" => "xprienz3_invoice_id", "T17" => "ei_new_invoice_id", "T20" => "wablab_invoice_id", "T23" => "demo_invoice_id", "T24" => "rlis_invoice_id", "T18" => "ssi_invoice_id", "T25" => "fge_invoice_id", "T26" => "chie_invoice_id", "T27" => "arium_invoice_id", "T28" => "mcls_invoice_id", "T29" => "ab_invoice_id");

        $tenant_id = $this->tenant_id;

        $invoice_id_tmp = get_max_lookup($lookup_table[$tenant_id]);

        if ($tenant_id == 'T17') {
            if (strlen($invoice_id_tmp) == 1) {
                $invoice_id_tmp = '000' . $invoice_id_tmp;
            } elseif (strlen($invoice_id_tmp) == 2) {
                $invoice_id_tmp = '00' . $invoice_id_tmp;
            } elseif (strlen($invoice_id_tmp) == 3) {
                $invoice_id_tmp = '0' . $invoice_id_tmp;
            } elseif (strlen($invoice_id_tmp) == 4) {
                $invoice_id_tmp = $invoice_id_tmp;
            } else {
                $invoice_id_tmp = $invoice_id_tmp;
            }

            $invoice_id = $pre_fix_array[$tenant_id] . '-20' . date('y') . '-' . date('m') . $invoice_id_tmp;
        } else {
            $invoice_id = $pre_fix_array[$tenant_id] . $invoice_id_tmp;
        }

        return $invoice_id;
    }

    /**

     * This method clean orphan data.

     * @param type $tenant_id

     * @param type $company

     * @param type $class

     */
    public function clean_orpham($tenant_id, $class) {

        $sql = "select cle.user_id from class_enrol cle where cle.class_id='$class' and cle.tenant_id ='$tenant_id' and cle.payment_status!='PYNOTREQD'"
                . " and cle.user_id not in(select due.user_id from enrol_pymnt_due due where cle.pymnt_due_id = due.pymnt_due_id)";

        $result = $this->db->query($sql);

        $user_array = $result->result_array();

        $user_array_string = "";

        if (!empty($user_array)) {

            foreach ($user_array as $user) {

                $user_array_string .= $user['user_id'] . ",";
            }
        }

        $user_array_string = rtrim($user_array_string, ",");

        if (!empty($user_array_string)) {

            $delete_sql = "delete from class_enrol where class_id='$class' and user_id in($user_array_string)";

            $delete_status = $this->db->query($delete_sql);

            return $delete_status;
        }
    }

    /* starts check existing invoice for compay */

    public function check_invoice($tenant_id, $company, $class) {
        $this->db->select('pymnt_due_id');
        $this->db->from('class_enrol');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('company_id', $company);
        $this->db->where('class_id', $class);

        return $this->db->get()->row();
    }

    /* ends * /
     * 
     * 
     */

    ////below code added by shubhranshu to check if the invoice is  paid/partpaid
    public function check_if_invoice_paid($company, $course, $class) {

        $this->db->select('pymnt_due_id');
        $this->db->from('class_enrol');
        $this->db->where('tenant_id', $this->tenant_id);
        $this->db->where('company_id', $company);
        $this->db->where('course_id', $course);
        $this->db->where('class_id', $class);
        $this->db->where('payment_status', 'PAID');
        $this->db->where("(payment_status='PAID' OR payment_status='PARTPAID')", NULL, FALSE);
        return $this->db->get()->row();
    }

    /**

     * This method returns the payment not required data.

     * @param type $tenant_id

     * @param type $limit

     * @param type $offset

     * @param type $sort_by

     * @param type $sort_order

     * @param type $course_id

     * @param type $class_id

     * @param type $class_status

     * @param type $search_select

     * @param type $taxcode_id

     * @param type $trainee_id

     * @param type $company_id

     * @return type

     */
    public function list_all_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $limit = NULL, $offset = NULL, $sort_by = 'ce.pymnt_due_id', $sort_order = 'DESC', $course_id = '', $class_id = '', $class_status = '', $search_select, $taxcode_id = '', $trainee_id = '', $company_id = 0, $eid = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';
        }

        $cur_date = date('Y-m-d');

        if ($offset <= 0 || empty($tenant_id)) {

            return;
        }

        //$this->db->select('cc.*, c.*, ce.*, tu.*, tup.*, tf.feedback_answer, cc.class_status as cc_class_status');
        $this->db->select('c.course_id , c.crse_name, c.tpg_crse,
 cc . class_id, cc. class_name, cc.class_start_datetime,cc.class_end_datetime, cc.certi_coll_date,cc . class_status  as cc_class_status, 
 ce . pymnt_due_id ,ce.enrolment_type, ce.enrolment_mode, ce.company_id,ce.referral_details,ce.eid_number, ce.certificate_coll_on, ce.payment_status,  
 tf.feedback_question_id,tf.feedback_question_id, tf.feedback_answer,
tu . user_id ,tu.tenant_id, tu. account_type, tu.tax_code, tu.account_status,
tup . first_name , tup . last_name, due.att_status, due.total_amount_due,due.subsidy_amount, ce.tg_number,ce.eid_number,cc.sales_executive, c.reference_num, c.external_reference_number, cc.tpg_course_run_id, due.class_fees, due.discount_rate, ce.tpg_enrolment_status');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id and ce.payment_status="PYNOTREQD" ');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->join('enrol_pymnt_due due', 'ce.pymnt_due_id = due.pymnt_due_id', 'LEFT');
        $this->db->join('trainer_feedback tf', 'tf.tenant_id=ce.tenant_id and tf.course_id=ce.course_id and tf.class_id=ce.class_id and tf.user_id=ce.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($eid)) {

            $this->db->where('ce.eid_number', $eid);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;
            }

            $this->db->where_not_in('cc.class_status', 'INACTIV');
        }

        if ($user_id) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }



        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        //$query = $this->db->get();        
        ///$query2 = $this->db->return_query_clear(); commented by shubhranshu

        $query2 = $this->db->get_compiled_select();   ///added by shubhranshu

        $query2 = str_replace('`', " ", $query2);

        $query2 = str_replace('( course_class  cc)', 'course_class cc', $query2);

        return $query2;
    }

    public function get_all_pymt_not_required_classtrainee_by_tenant_id($tenant_id, $course_id, $class_id, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id = 0) {

        $user_id = '';

        if ($search_select == 1) {

            $user_id = (!empty($taxcode_id)) ? $taxcode_id : '';
        } else {

            $user_id = (!empty($trainee_id)) ? $trainee_id : '';

            ;
        }

        $cur_date = date('Y-m-d');

        if (empty($tenant_id)) {

            return 0;
        }

        $this->db->select('ce.user_id');

        $this->db->from('course_class cc');

        $this->db->join('course c', 'c.course_id=cc.course_id');

        $this->db->join('class_enrol ce', 'ce.class_id=cc.class_id and ce.payment_status="PYNOTREQD" ');

        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id');

        $this->db->join('tms_users_pers tup', 'tup.user_id=tu.user_id');

        $this->db->where('cc.tenant_id', $tenant_id);

        $this->db->where_in('ce.enrol_status', array('ENRLBKD', 'ENRLACT'));

        if (!empty($company_id)) {

            $this->db->where('ce.company_id', $company_id);
        }

        if (!empty($course_id)) {

            $this->db->where('cc.course_id', $course_id);
        }

        if (!empty($class_id)) {

            $this->db->where('cc.class_id', $class_id);
        }

        if (!empty($class_status)) {

            switch ($class_status) {

                case 'IN_PROG':

                    $this->db->where('date(cc.class_start_datetime) <=', $cur_date);

                    $this->db->where('date(cc.class_end_datetime) >=', $cur_date);

                    break;

                case 'COMPLTD':

                    $this->db->where('date(cc.class_end_datetime) <', $cur_date);

                    break;

                case 'YTOSTRT':

                    $this->db->where('date(cc.class_start_datetime) >', $cur_date);

                    break;



                default:

                    break;
            }
        }

        if (!empty($user_id)) {

            $this->db->where('ce.user_id', $user_id);
        }

        if ($this->user->role_id == 'COMPACT') {

            $this->db->where('ce.company_id', $this->user->company_id);
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }

        if ($this->user->role_id == 'SLEXEC') {

            $this->db->where('ce.sales_executive_id', $this->user->user_id);
        }

        if ($this->user->role_id == 'TRAINER') {

            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",cc.classroom_trainer) !=", 0);
        }

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**

     * this method used for get_notpaid_notrequired_taxcode in change enrollment mode

     * @param type $tenant_id

     * @param type $taxcode

     * @param type $username

     * @param type $is_noreqd

     * @return type

     */
    public function get_notpaid_notrequired_taxcode($tenant_id, $taxcode, $username) {

        $taxcode = trim($taxcode);

        $username = trim($username);

        $this->db->select('tup.user_id, tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id and tu.tenant_id=ce.tenant_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id and tup.tenant_id=tu.tenant_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.enrolment_mode', 'SELF')
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'))
                ->where_in('ce.payment_status', array('PYNOTREQD', 'NOTPAID'));

        if ($this->user->role_id == 'CRSEMGR') {

            $this->db->join('course c', 'c.tenant_id = ce.tenant_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');
        }

        if (!empty($taxcode)) {

            $this->db->like('tu.tax_code', $taxcode, 'both');
        } elseif (!empty($username)) {

            $this->db->like('tup.first_name', $username, 'both');
        }

        $this->db->group_by('tup.user_id');

        $this->db->limit(200);

        return $this->db->get()->result_object();
    }

    /**

     * This method gets not paid and payment not required enrollment for a trainee/ company

     * @param type $taxcode_id

     * @param type $trainee_id

     * @param type $tenant_id

     * @return type

     */
    public function search_trainee_change_pay_mode($company_id, $taxcode_id, $trainee_id, $tenant_id) {

        $sql = " select ce.pymnt_due_id as payid, ce.payment_status, tup.first_name as first, tup.last_name as last,

            tu.tax_code as taxcode, cc.course_id, cc.class_id, cc.class_name, c.crse_name, inv.invoice_id, date(inv_date) as inv_date

            from  course_class cc, course c, tms_users tu, tms_users_pers tup, class_enrol ce left join enrol_invoice inv on 

            ce.pymnt_due_id = inv.pymnt_due_id

            where 

            cc.class_id=ce.class_id and c.course_id=cc.course_id and tu.user_id=ce.user_id and tup.user_id=tu.user_id

            and ce.tenant_id='$tenant_id' and ce.enrol_status in('ENRLACT','ENRLBKD') and 

            ce.pymnt_due_id not in(select distinct pymnt_due_id from class_enrol where payment_status in('PAID','PARTPAID'))";

        if ($company_id) {

            $where = " and ce.enrolment_mode ='COMPSPON' and ce.company_id='$company_id'";
        } else {

            $where = " and ce.enrolment_mode ='SELF'";
        }

        if ($taxcode_id) {

            $where .= " and tu.user_id ='$taxcode_id'";
        }

        if ($trainee_id) {

            $where .= " and tu.user_id ='$trainee_id'";
        }

        if ($this->user->role_id == 'CRSEMGR') {

            $where .= " and FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) != 0";
        }

        $where .= " group by ce.pymnt_due_id, cc.course_id, cc.class_id order by ce.pymnt_due_id";

        $sql1 = $sql . $where;

        $result = $this->db->query($sql1);

        return $result->result();
    }

    /**

     * This method used for changing the individual enrollment mode.

     * @param type $args

     * @return boolean

     */
    public function change_payment_mode_individual($args) {

        if (empty($args)) {

            return FALSE;
        }

        $status = FALSE;

        if ($args['payment_mode'] == 'NOTPAID') {

            $status = $this->pymnt_required_to_notrequired($args);
        } else {

            $status = $this->pymnt_notrequired_to_required($args);
        }

        return $status;
    }

    /**

     * This method convert pymnt required to pymnt not required.

     * @param type $args

     * @return type

     */
    private function pymnt_required_to_notrequired($args) {

        $status = FALSE;

        if ($status == FALSE) {

            $new_payment_due_id = 0;

            $status = $this->update_class_enroll($args['trainee_user_id'], $new_payment_due_id, $args['tenant_id'], $args['payment_due_id'], $args['course_id'], $args['class_id'], 'PYNOTREQD', 'SELF', 'ENRLACT');

            if ($status) {

                $status = $this->remove_enrol_pymnt_due($args['payment_due_id']);

                if ($status) {

                    $status = $this->update_invoice_audit_trail($args['payment_due_id']);

                    if ($status) {

                        $status = $this->remove_invoice($args['payment_due_id']);
                    }
                }
            }
        }

        return $status;
    }

    /**

     * This method convert pymnt not required to pymnt required.

     * @param type $args

     * @return type

     */
    private function pymnt_notrequired_to_required($args) {

        $status = FALSE;

        $subsidy_amount = 0;

        $status = $this->update_classenrol_audittrail($args['tenant_id'], $args['payment_due_id'], $args['trainee_user_id'], $args['course_id'], $args['class_id']);

        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $status = $this->update_class_enroll($args['trainee_user_id'], $payment_due_id, $args['tenant_id'], $args['payment_due_id'], $args['course_id'], $args['class_id'], 'NOTPAID', 'SELF', 'ENRLBKD');



        $classes = $this->db->select('certi_coll_date,class_fees')->from('course_class')->where('class_id', $args['class_id'])->get()->row();

        $courses = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $args['course_id'])->get()->row();

        $unit_fees = $classes->class_fees;



        $invoice_id = $this->generate_invoice_id();

        $result_array = $this->get_discnt($args['tenant_id'], 0, $args['course_id'], $args['class_id'], $classes->class_fees, $args['trainee_user_id']);

        $discount_type = $result_array['discount_label'];



        $gst_rate = $this->get_gst_current();

        $feesdue = $unit_fees - $result_array['discount_amount'];

        $netdue = $this->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

        $totalgst = $this->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

        $cur_date = date('Y-m-d H:i:s');

        if (!empty($payment_due_id)) {

            $data = array(
                'user_id' => $args['trainee_user_id'],
                'pymnt_due_id' => $payment_due_id,
                'class_fees' => round($classes->class_fees, 4),
                'total_amount_due' => round($netdue, 4),
                'discount_type' => $discount_type,
                'discount_rate' => round($result_array['discount_rate'], 4),
                'subsidy_amount' => 0,
                'subsidy_recd_date' => '0000-00-00',
                'gst_amount' => round($totalgst, 4)
            );

            $status = $this->db->insert('enrol_pymnt_due', $data);

            $gst_rule = (empty($courses->gst_on_off)) ? '' : $courses->subsidy_after_before;

            $data = array(
                'invoice_id' => $invoice_id,
                'pymnt_due_id' => $payment_due_id,
                'inv_date' => $cur_date,
                'inv_type' => 'INVINDV',
                'total_inv_amount' => round($netdue, 4),
                'total_unit_fees' => round($classes->class_fees, 4),
                'total_inv_discnt' => round($result_array['discount_amount'], 4),
                'total_inv_subsdy' => 0,
                'total_gst' => round($totalgst, 4),
                'gst_rate' => round($gst_rate, 4),
                'gst_rule' => $gst_rule,
            );

            $status = $this->db->insert('enrol_invoice', $data);
        }

        return $status;
    }

    /**

     * This method used for changing the company enrollment mode.

     * @param type $args

     * @return boolean

     */
    public function change_payment_mode_company($args) {

        if (empty($args)) {

            return FALSE;
        }

        $status = FALSE;

        if ($args['payment_mode'] == 'NOTPAID') {

            $status = $this->comp_pymnt_required_to_notrequired($args);
        } else {

            $status = $this->comp_pymnt_notrequired_to_required($args);
        }

        return $status;
    }

    /**

     * This method convert company pymnt  required to pymnt not required.

     * @param type $args

     * @return type

     */
    private function comp_pymnt_required_to_notrequired($args) {

        $status = FALSE;

        if ($status == FALSE) {

            $new_payment_due_id = 0;

            $status = $this->update_class_enroll('', $new_payment_due_id, $args['tenant_id'], $args['payment_due_id'], $args['course_id'], $args['class_id'], 'PYNOTREQD', 'COMPSPON', 'ENRLACT');

            if ($status) {

                $status = $this->remove_enrol_pymnt_due($args['payment_due_id']);

                if ($status) {

                    $status = $this->update_invoice_audit_trail($args['payment_due_id']);

                    if ($status) {

                        $status = $this->remove_invoice($args['payment_due_id']);
                    }
                }
            }
        }

        return $status;
    }

    /**

     * This method convert company pymnt not required to pymnt required.

     * @param type $args

     * @return type

     */
    private function comp_pymnt_notrequired_to_required($args) {

        $status = TRUE;

        $subsidy_amount = 0;

        $net_due = 0;

        $GST_amount = 0;

        $total_inv_subsidy = 0;



        $classes = $this->db->select('certi_coll_date,class_fees')->from('course_class')->where('class_id', $args['class_id'])->get()->row();

        $courses = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $args['course_id'])->get()->row();

        $GSTRate = $this->get_gst_current();

        $GSTRule = (empty($courses->gst_on_off)) ? '' : $courses->subsidy_after_before;

        $unit_fees = $classes->class_fees;



        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $status = $this->update_class_enroll('', $payment_due_id, $args['tenant_id'], $args['payment_due_id'], $args['course_id'], $args['class_id'], 'NOTPAID', 'COMPSPON', 'ENRLBKD');

        $result_array = $this->get_discnt($args['tenant_id'], $args['company_id'], $args['course_id'], $args['class_id'], $unit_fees, 0);

        $discount_type = $result_array['discount_label'];

        $discount_rate = $result_array['discount_rate'];

        $discount_amount = $result_array['discount_amount'];

        $result_array = $this->net_fees_payable($unit_fees, $discount_amount, $subsidy_amount, $GSTRate, $GSTRule);

        $net_due = $result_array['net_due'];

        $GST_amount = $result_array['GST_amount'];



        $check = $this->db->select('*')
                        ->from('class_enrol')->where('tenant_id', $args['tenant_id'])->where('course_id', $args['course_id'])
                        ->where('class_id', $args['class_id'])->where('pymnt_due_id', $payment_due_id)->get();

        $trainee_count = $check->num_rows();

        $total_inv_amount = round($trainee_count * $net_due, 4);

        $total_unit_fees = round($trainee_count * $unit_fees, 4);

        $total_inv_discnt = round($trainee_count * $discount_amount, 4);

        $total_gst = round($trainee_count * $GST_amount, 4);



        if (!empty($payment_due_id)) {

            foreach ($check->result_array() as $row) {

                $data[] = array(
                    'user_id' => $row['user_id'],
                    'pymnt_due_id' => $payment_due_id,
                    'class_fees' => round($classes->class_fees, 4),
                    'total_amount_due' => round($net_due, 4),
                    'discount_type' => $discount_type,
                    'discount_rate' => $discount_rate,
                    'subsidy_amount' => 0,
                    'subsidy_recd_date' => '0000-00-00',
                    'gst_amount' => round($GST_amount, 4)
                );
            }

            $status = $this->db->insert_batch('enrol_pymnt_due', $data);
        }

        if ($status) {

            list($status, $new_invoice_id) = $this->create_new_invoice($payment_due_id, $args['company_id'], $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $GSTRule, $GSTRate, 'INVCOMALL');
        }

        return $status;
    }

    /**

     * This method updates pymnt_due_id and payment_status in class enroll table.

     * @param type $new_payment_due_id

     * @param type $tenant_id

     * @param type $payment_due_id

     * @param type $course_id

     * @param type $class_id

     * @param type $payment_status

     * @return type

     */
    private function update_class_enroll($user_id = '', $new_payment_due_id, $tenant_id, $payment_due_id, $course_id, $class_id, $payment_status, $enrolment_mode, $enrol_status) {

        $data = array('pymnt_due_id' => $new_payment_due_id,
            'payment_status' => $payment_status,
            'enrol_status' => $enrol_status);

        $this->db->where("tenant_id", $tenant_id);

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("course_id", $course_id);

        $this->db->where("class_id", $class_id);

        $this->db->where("enrolment_mode", $enrolment_mode);

        if (!empty($user_id)) {

            $this->db->where("user_id", $user_id);
        }

        $status = $this->db->update('class_enrol', $data);

        return $status;
    }

    /**

     * This method remove enrol_pymnt_due for a payment_due_id

     * @param type $payment_due_id

     * @return boolean

     */
    private function remove_enrol_pymnt_due($payment_due_id) {

        $delete_status = TRUE;

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->trans_start();

        $this->db->delete("enrol_pymnt_due");

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $delete_status = FALSE;
        }

        return $delete_status;
    }

    /**

     * function created to get trainee names

     */
    public function get_trainer_names($trainer_id) {

        $tenantId = $this->session->userdata('userDetails')->tenant_id;

        if (!empty($trainer_id)) {

            $sql = "SELECT pers.user_id, pers.first_name, pers.last_name, rl.role_id FROM `tms_users_pers` pers, internal_user_role rl

            WHERE pers.tenant_id = rl.tenant_id AND pers.user_id = rl.user_id AND pers.tenant_id = '$tenantId' AND rl.role_id='TRAINER' AND rl.user_id in ($trainer_id)";

            $query = $this->db->query($sql);

            return $query->result();
        }
    }

    /**

     * function to identify whether trainee is enrolled in the class or not

     */
    public function check_trainee_taxcode_exist($course, $class, $tenant_id, $taxcode) {

        $result = $this->db->select('*')->from('tms_users tu')
                        ->join('class_enrol ce', 'ce.user_id=tu.user_id and ce.tenant_id=tu.tenant_id')
                        ->where('tu.tenant_id', $tenant_id)->where('tu.tax_code', $taxcode)
                        ->where('ce.course_id', $course)->where('ce.class_id', $class);

        return $this->db->get();
    }

    public function get_class_enrol_data($course_id, $class_id, $user_id = null, $company_id = null) {
        $this->db->select('*');
        $this->db->from('class_enrol');
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        if ($user_id != null) {
            $this->db->where('user_id', $user_id);
        } else if ($company_id != null) {
            $this->db->where('company_id', $company_id);
        }
        $sql = $this->db->get();
        return $data = $sql->result_array();
        // $data['payment_due'] = $this->payment_tbl($data['pymnt_due_id']);
    }

    /**

     * Insert trainer feedback data

     */
    public function update_trainer_feedback_data($tenant_id, $insert_data, $course, $trainer, $class) {

        $cur_date = date('Y-m-d H:i:s');

        foreach ($insert_data as $key => $excel) {

            $excel['rating'] = strtoupper(str_replace('COMP_', '', $excel['rating']));

            if ($excel['status'] == 'PASSED') {

                $ce_data = array(
                    'training_score' => $excel['rating'],
                    'trainer_fdbck_by' => $trainer,
                    'trainer_fdbck_on' => $cur_date,
                    'trainer_fdbck_modi_by' => $trainer,
                    'trainer_fdbck_modi_on' => $cur_date
                );

                $fdbk_data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $course,
                    'class_id' => $class,
                    'user_id' => $excel['user_id'],
                    'feedback_question_id' => 'COMYTCOM',
                    'feedback_answer' => $excel['rating']
                );

                $where_data = array(
                    'tenant_id' => $tenant_id,
                    'course_id' => $course,
                    'class_id' => $class,
                    'user_id' => $excel['user_id']
                );

                $this->db->trans_start();

                $this->db->where($where_data)->update('class_enrol', $ce_data);

                $this->db->where($where_data)->where('feedback_question_id', 'COMYTCOM')->delete('trainer_feedback');

                $this->db->insert('trainer_feedback', $fdbk_data);

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {

                    $error_msg .= ' Insertion failed. try again.';

                    $insert_data[$key]['failure_reason'] = $error_msg;

                    $insert_data[$key]['status'] = 'FAILED';
                }
            }

            $insert_data[$key]['rating'] = $insert_data[$key]['view_rating'];
        }

        return $insert_data;
    }

    /**

     * 

     * @param type $tenant_id

     * @param type $logged_in_user_id

     * @param type $course_id

     * @param type $class_id

     * @param type $company_id

     * @param type $invoice_id

     * @param type $payment_due_id

     * @param type $seleced_trainee_list

     * @return boolean

     */
    public function reschedule_add_to_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
    , $invoice_id, $payment_due_id, $seleced_trainee_list) {

        $status = TRUE;

        if (empty($seleced_trainee_list)) {

            return FALSE;
        }

        $curr_invoice_details = $this->get_invoice_details($payment_due_id);

        $total_gst_due = 0;

        $total_unit_fees_due = 0;

        $total_net_fees_due = 0;

        $total_discount_due = 0;

        $total_subsidy_amount_due = 0;

        $cur_date = date('Y-m-d');

        $crse_cls_detail = $this->get_class_detail($class_id, $course_id, $tenant_id);

        $str_class_status = $this->get_class_status($crse_cls_detail->class_status, $crse_cls_detail->class_start_datetime, $crse_cls_detail->class_end_datetime);

        $this->db->trans_start();

        foreach ($seleced_trainee_list as $user_id) {

            $fees_array = $this->fees_payable($user_id, $tenant_id, $course_id, $class_id, 0, $company_id, $logged_in_user_id);

            $data = array(
                'tenant_id' => $tenant_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'user_id' => $user_id,
                'enrolment_type' => 'FIRST',
                'enrolment_mode' => 'COMPSPON',
                'pymnt_due_id' => $payment_due_id,
                'company_id' => $company_id,
                'enrolled_on' => $cur_date,
                'enrolled_by' => $logged_in_user_id,
                'tg_number' => '',
                'payment_status' => 'NOTPAID',
                'sales_executive_id' => '',
                'class_status' => $str_class_status,
                'enrol_status' => 'ENRLBKD'
            );

            $this->db->insert('class_enrol', $data);



            $data = array(
                'user_id' => $user_id,
                'pymnt_due_id' => $payment_due_id,
                'class_fees' => round($fees_array["unit_fees"], 4),
                'total_amount_due' => round($fees_array["net_fees_due"], 4),
                'discount_type' => $fees_array["discount_type"],
                'discount_rate' => round($fees_array["discount_rate"], 4),
                'gst_amount' => round($fees_array["gst_amount"], 4),
                'subsidy_amount' => 0,
                'subsidy_recd_date' => '0000-00-00'
            );

            $this->db->insert('enrol_pymnt_due', $data);

            $discount_rate = $fees_array["discount_rate"];

            $total_discount_due += ($fees_array["unit_fees"] * $discount_rate) / 100;

            $total_gst_due += $fees_array["gst_amount"];

            $total_unit_fees_due += $fees_array["unit_fees"];

            $total_net_fees_due += $fees_array["net_fees_due"];
        }

        if ($status) {

            $status = $this->remove_invoice($payment_due_id);

            if ($status) {

                list($status) = $this->reschedule_create_new_invoice($payment_due_id, $company_id, ($curr_invoice_details->total_inv_amount + $total_net_fees_due), ($curr_invoice_details->total_unit_fees + $total_unit_fees_due), ($curr_invoice_details->total_inv_discnt + $total_discount_due), ($curr_invoice_details->total_inv_subsdy + $total_subsidy_amount_due), ($curr_invoice_details->total_gst + $total_gst_due), $curr_invoice_details->gst_rule, $curr_invoice_details->gst_rate, 'INVCOMALL', $invoice_id);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }



        return $status;
    }

    /**

     * This method will create new invoice for reschedule class.

     * @param type $payment_due_id

     * @param type $company_id

     * @param type $total_inv_amount

     * @param type $total_unit_fees

     * @param type $total_inv_discnt

     * @param type $total_inv_subsidy

     * @param type $total_gst

     * @param type $GSTRule

     * @param type $GSTRate

     * @param type $inv_type

     * @return type

     */
    private function reschedule_create_new_invoice($payment_due_id, $company_id, $total_inv_amount, $total_unit_fees, $total_inv_discnt, $total_inv_subsidy, $total_gst, $GSTRule, $GSTRate, $inv_type, $invoice_id) {

        $insert_status = TRUE;

        $cur_date = date('Y-m-d H:i:s');

        $enrol_invoice_data = array(
            'invoice_id' => $invoice_id,
            'pymnt_due_id' => $payment_due_id,
            'inv_date' => $cur_date,
            'inv_type' => $inv_type,
            'company_id' => $company_id,
            'total_inv_amount' => round($total_inv_amount, 4),
            'total_unit_fees' => round($total_unit_fees, 4),
            'total_inv_discnt' => round($total_inv_discnt, 4),
            'total_inv_subsdy' => round($total_inv_subsidy, 4),
            'total_gst' => round($total_gst, 4),
            'gst_rate' => round($GSTRate, 4),
            'gst_rule' => $GSTRule,
            'invoice_generated_on' => $cur_date,
        );

        $this->db->trans_start();

        $this->db->insert('enrol_invoice', $enrol_invoice_data);

        $new_invoice_id = $invoice_id;

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $insert_status = FALSE;
        }

        return array($insert_status, $new_invoice_id);
    }

    /**

     * This method will create new company enrolment for reschedule class

     * @param type $data

     * @param type $new_class_id

     * @param type $trainee_id

     */
    private function reschedule_create_new_comp_enroll($tenant_id, $data, $course_id, $new_class_id, $trainee_id) {

        $_POST['company'] = $data['company_id'];

        $_POST['class'] = $new_class_id;

        $_POST['course'] = $course_id;

        $_POST['data'][0]['user_id'] = $trainee_id;

        $_POST['data'][0]['subsidy_type'] = 0;

        $class_fees = $this->db->select('class_fees')->from('course_class')->where('class_id', $new_class_id)->get()->row('class_fees');

        $discount = $this->classtraineemodel->calculate_discount_enroll(0, $data['company_id'], $new_class_id, $course_id, $class_fees);

        $_POST['discount_label'] = $discount['discount_label'];

        $_POST['enrollment_type'] = ($data['enrolment_type'] == 'RETAKE') ? 1 : 2;

        $_POST['payment_retake'] = ($data['payment_status'] == 'PYNOTREQD') ? 2 : 1;

        $_POST['salesexec'] = $data['sales_executive_id'];

        $reschedule = 1;

        $res = $this->company_enrollment_db_update($tenant_id, $this->user->user_id, '', 'N', $reschedule);

        return $res;
    }

    /**

     * This method update class_enrol_audit_trail with reschedule reason.

     * @param type $tenant_id

     * @param type $payment_due_id

     * @param type $user_id

     * @param type $course_id

     * @param type $class_id

     * @param type $reschedule_reason

     * @param type $other_reason

     * @param type $new_class

     * @return boolean

     */
    private function update_rescheduled_reason($tenant_id, $payment_due_id, $user_id, $course_id, $class_id, $reschedule_reason, $other_reason, $new_class) {

        $status = TRUE;

        $this->db->where("pymnt_due_id", $payment_due_id);

        $this->db->where("user_id", $user_id);

        $this->db->where("course_id", $course_id);

        $this->db->where("class_id", $class_id);

        $this->db->where("tenant_id", $tenant_id);

        $audit_data = array("rescheduled_reason" => $reschedule_reason,
            "rescheduled_reason_oth" => $other_reason,
            "rescheduled_class_id" => $new_class,
            "enrol_status" => 'RESHLD');



        $this->db->trans_start();

        $this->db->update('class_enrol_audit_trail', $audit_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            $status = FALSE;
        }

        return $status;
    }

    /**

     * This method will check enrolment status before reschedule.

     * @param type $trainee_id_array

     * @param type $class_id

     * @return type

     */
    public function check_reschedule_status($trainee_id_array, $class_id) {

        $pymnt_due_id_array = $this->db->select('pymnt_due_id')->from('class_enrol')
                        ->where('class_id', $class_id)
                        ->where('enrolment_mode', 'COMPSPON')
                        ->where_in('user_id', $trainee_id_array)
                        ->get()->result_array();

        $payment_id_arr = array();

        foreach ($pymnt_due_id_array as $payment) {

            $payment_id_arr[] = $payment['pymnt_due_id'];
        }
        if (!empty($payment_id_arr)) {////added by shubhranshu tp prvent query error while payment_due_id not exist///
            $paid_array = $this->db->select('pymnt_due_id')->from('class_enrol')
                    ->where('class_id', $class_id)
                    ->where_in('payment_status', array("PARTPAID", "PAID"))
                    ->where_in('pymnt_due_id', $payment_id_arr)
                    ->get();
            if ($paid_array->num_rows() > 0) {

                return array('status' => 'PAID');
            } else {

                return array('status' => 'NOTPAID');
            }
        } else {
            return FALSE; ///added by shubhranshu tp prvent query error while payment_due_id not exist//
        }
    }

    /**
     * function to get all invoice with company name
     * author: CR02
     * date: 09 Apr 2015
     */
    public function get_all_invoice($tenant_id, $invoice, $paid = 0) {
        //$this->db->distinct();
        $this->db->select('ei.invoice_id, enrolment_mode, tup.first_name, tup.last_name, cm.company_name,cm.comp_regist_num, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('company_master cm', 'cm.company_id=ce.company_id', 'left');
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->join('course crse', 'crse.course_id=ce.course_id');
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);
        }
        // Modification ends here,
        $this->db->where('ce.tenant_id', $tenant_id);
        if (!empty($paid)) {
            $this->db->where('ce.payment_status', $paid);
        }
        if (!empty($invoice)) {
            $this->db->like('ei.invoice_id', $invoice, 'both');
        }
        $this->db->order_by('LENGTH(ei.invoice_id)');
        $this->db->order_by('ei.invoice_id');
        $this->db->group_by('ei.invoice_id');
        //echo $this->db->get_compiled_select(); exit;
        return $this->db->get()->result_object();
    }

    // skm code start
    public function get_rows_count($course_id, $class_id) {

        $this->db->select('class_id,course_id');
        $this->db->from('class_attendance');
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $sql = $this->db->get();

        return $sql->num_rows();
    }

    // skm code end
    /*
     * skm start-> this function called from export helper which count the total session at particular date
     */
    public function scheduled_session_count($class_id, $course_id, $formatted_day) {
        $this->db->select('class_id,course_id,class_date');
        $this->db->from('class_schld');
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('class_date', $formatted_day);
        $sql = $this->db->get();
        return $sql->num_rows();
    }

    /*
     * skm end 
     */

    // added by shubhranshu for invoice audit trail
    public function get_all_invoice_audit_trail($tenant_id, $invoice, $paid = 0) {
        //$this->db->distinct();
        $this->db->select('eia.invoice_id, enrolment_mode, tup.first_name, tup.last_name, cm.company_name,cm.comp_regist_num, tu.tax_code')
                ->from('enrol_invoice_audittrail eia')
                ->join('class_enrol ce', 'ce.pymnt_due_id=eia.pymnt_due_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('company_master cm', 'cm.company_id=ce.company_id', 'left');
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->join('course crse', 'crse.course_id=ce.course_id');
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",crse.crse_manager) !=", 0);
        }
        // Modification ends here,
        $this->db->where('ce.tenant_id', $tenant_id);
        if (!empty($paid)) {
            $this->db->where('ce.payment_status', $paid);
        }
        if (!empty($invoice)) {
            $this->db->like('eia.invoice_id', $invoice, 'both');
        }
        $this->db->order_by('LENGTH(eia.invoice_id)');
        $this->db->order_by('eia.invoice_id');
        $this->db->group_by('eia.invoice_id');
        //echo $this->db->get_compiled_select();exit;
        return $this->db->get()->result_object();
    }

    /* This Function get the sfc claim id of trainee by shubhranshu start */

    public function get_sfc_claim_id($class_id, $user_id, $payid, $tenant_id) {
        $this->db->select('sfc_claim_id');
        $this->db->from('class_enrol');
        $this->db->where('class_id', $class_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('pymnt_due_id', $payid);
        $this->db->where('tenant_id', $tenant_id);
        $qry = $this->db->get();
        $result = $qry->row();
        return $result->sfc_claim_id;
    }

    ///addded by shubhranshu for evrest pdf generation rcvd details all
    public function get_pymnt_rcvd_detailsforrpt($inv_id) {
        $this->db->select('*');
        $this->db->from('enrol_paymnt_recd');
        $this->db->where('invoice_id', $inv_id);
        $this->db->order_by('trigger_date', 'desc');
        $this->db->limit('1');
        $qry = $this->db->get();
        $result = $qry->row();
        //echo $this->db->last_query();exit;
        return $result;
    }

    ///addded by shubhranshu for evrest pdf generation rcvd details
    public function get_pymnt_rcvd_detailsforrpt_all($inv_id) {
        $this->db->select('*,sum(amount_recd) as total');
        $this->db->from('enrol_paymnt_recd');
        $this->db->where('invoice_id', $inv_id);
        $this->db->order_by('trigger_date', 'desc');
        $qry = $this->db->get();
        $result = $qry->row();
        //echo $this->db->last_query();exit;
        return $result;
    }

    ///addded by shubhranshu for evrest pdf generation refund details
    public function get_refund_rcvd_detailsforrpt($inv_id) {
        $this->db->select('*,sum(amount_refund) as total');
        $this->db->from('enrol_refund');
        $this->db->where('invoice_id', $inv_id);
        $qry = $this->db->get();
        $result = $qry->row();
        return $result;
    }

    ////added by shubhranshu to get the invoice details whether comp or ind
    public function check_enrol_invoice_compind($id, $inv) {
        $tenant_id = $this->tenant_id;
        $this->db->select('*');
        $this->db->from('enrol_invoice');
        $this->db->where('invoice_id', $inv);
        $this->db->where('pymnt_due_id', $id);
        $results = $this->db->get()->row();
        return $results;
    }

    ///added by shubhranshu to get old invoice 
    public function get_enroll_old_invoice_new($id, $inv) {
        $tenant_id = $this->tenant_id;
        $this->db->select('*');
        $this->db->from('enrol_invoice_view');
        $this->db->where('invoice_id', $inv);
        $this->db->where('pymnt_due_id', $id);
        $this->db->where('tenant_id', $tenant_id);
        $results = $this->db->get()->row();
        return $results;
    }

    ///addded by shubhranshu for evrest pdf generation individual rcvd details
    public function get_ind_trainee_pymnt_rcvd_amt($inv_id, $trn_id) {
        $this->db->select('sum(amount_recd) as total');
        $this->db->from('enrol_pymnt_brkup_dt');
        $this->db->where('invoice_id', $inv_id);
        $this->db->where('user_id', $trn_id);
        $qry = $this->db->get();
        $result = $qry->row();
        //echo $this->db->last_query();exit;
        return $result;
    }

    ////added by shubhranshu to fetch traineee who are absent
    public function fetch_absent_trainees($pay_due) {
        $this->db->select('*');
        $this->db->from('enrol_pymnt_due');
        $this->db->where('pymnt_due_id', $pay_due);
        $this->db->where('att_status', '0');
        $qry = $this->db->get();
        $result = $qry->result();
        //echo $this->db->last_query();exit;
        return $result;
    }

    //// added by shubhranshu to get company name for acknowledgement
    public function get_company_name($invoice_id, $trainee_id, $class_id, $tenant_id) {

        $this->db->select('cm.company_name')
                ->from('class_enrol ce')
                ->join('company_master cm', 'cm.company_id=ce.company_id')
                ->where('ce.tenant_id', $tenant_id)
                ->where('ce.class_id', $class_id);

        if (!empty($invoice_id)) {

            $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id');

            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if ($trainee_id) {

            $this->db->where('ce.user_id', $trainee_id);
        }

        return $this->db->get()->row();
    }

    public function internal_eid_list_autocomplete($name_startsWith) {
        $name_startsWith = trim($name_startsWith);
        $user = $this->session->userdata('userDetails');
        $results = array();
        if (!empty($name_startsWith)) {
            $this->db->select('eid_number,user_id');
            $this->db->from('class_enrol');
            $this->db->where('tenant_id', $user->tenant_id);
            $this->db->like('eid_number', $name_startsWith, 'both');
            $this->db->order_by('eid_number', 'ASC');
            $this->db->limit(200);
            $results = $this->db->get()->result();
            //echo $this->db->last_query();exit;
        }

        foreach ($results as $k => $v) {
            $output[] = array(
                'key' => $v->user_id,
                'value' => $v->eid_number,
                'label' => $v->eid_number,
            );
        }

        return $output;
    }

    public function get_full_trainee_details($id) {

        if ($id) {

            $this->db->select('*');

            $this->db->from('tms_users_pers pers');

            $this->db->join('tms_users usr', 'usr.user_id=pers.user_id');

            $this->db->join('tenant_master tm', 'tm.tenant_id=pers.tenant_id');

            $this->db->where('usr.account_type', 'TRAINE');

            $this->db->where('usr.tenant_id', $this->session->userdata('userDetails')->tenant_id);

            $this->db->where('usr.user_id', $id);

            $results = $this->db->get()->result_array();

            return $results[0];
        }
    }

    //// added by shubhranshu to fetch all trainee that are enrolled to the class
    public function get_enrolled_trainee($tenant_id, $courseID, $classID) {

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
                AND ce.eid_number != ''
                AND date(cc.class_end_datetime) <= '$today_date'";
        $res = $this->db->query($sql)->result();
        //echo $this->db->last_query();exit;
        $result = array();
        foreach ($res as $row) {
            $result[$row->user_id] = $row->tax_code;
        }

        return $result;
    }

    //////addded by shubhranshu to get the trainee session data for mark attendance to tpg
    function get_trainee_sessions_data($tenant_id, $course, $class, $userid) {
        $today_date = date('Y-m-d');        
        
        $sql = "SELECT * from (
                            select 
                                    tu.user_id,
                                    ce.course_id,
                                    ce.class_id,
                                    c.reference_num,
                                    cc.tpg_course_run_id,
                                    tup.first_name as fullname,
                                    tu.registered_email_id,
                                    tup.contact_number,
                                    ROUND(TIMESTAMPDIFF(second, cs.session_start_time, cs.session_end_time) / 3600, 1) as total_classroom_duration,
                                    cc.survey_language,
                                    tu.tax_code,
                                    tu.tax_code_type,
                                    cs.tpg_session_id as tpg_session_id,
                                    cs.session_type_id,
                                    (CASE 
                                        WHEN cs.session_type_id like '%S1%' THEN ca.session_01_tpg_uploaded_status ELSE ca.session_02_tpg_uploaded_status END
                                    ) as tpg_uploaded_status,                
                                    cs.class_date as class_date,
                                    (CASE 
                                        WHEN cs.session_type_id like '%S1%' THEN ca.session_01 ELSE 0 END
                                    ) as session_01,
                                    (CASE 
                                        WHEN cs.session_type_id like '%S2%' THEN ca.session_02 ELSE 0 END
                                    ) as session_02,
                                    tup.nationality as idtype,
                                    cs.mode_of_training
                                FROM course_class cc
                                JOIN course c ON c.course_id = cc.course_id 
                                JOIN class_enrol ce ON ce.class_id = cc.class_id 
                                JOIN tms_users tu ON tu.user_id = ce.user_id 
                                left join tms_users_pers tup on tup.user_id =tu.user_id 
                                LEFT JOIN class_schld cs ON cs.class_id = cc.class_id and cs.tenant_id = ce.tenant_id and cs.course_id = c.course_id
                                JOIN class_attendance ca ON ca.class_id = cc.class_id and ca.user_id = ce.user_id and ca.course_id = c.course_id and ca.class_attdn_date = cs.class_date
                                WHERE cc.tenant_id = '$tenant_id'
                                AND c.course_id = '$course'
                                AND cc.class_id = '$class'
                                AND ce.user_id = '$userid'
                                AND cs.session_type_id !='BRK'                                
                                AND ce.eid_number != ''
                                AND date(cc.class_end_datetime) <= '$today_date'
                            ) a where a.session_01 = 1 OR a.session_02 = 1
                UNION ALL 
                select
                    tu.user_id,
                    ce.course_id,
                    ce.class_id,
                    c.reference_num,
                    cc.tpg_course_run_id,
                    tup.first_name as fullname,
                    tu.registered_email_id,
                    tup.contact_number,
                    ROUND(TIMESTAMPDIFF(second, cas.assmnt_start_time, cas.assmnt_end_time) / 3600, 1) as total_classroom_duration,
                    cc.survey_language,
                    tu.tax_code,
                    tu.tax_code_type,
                    cas.tpg_assmnt_id as tpg_session_id,
                    null as session_type_id,
                    csn.tpg_uploaded_status as tpg_uploaded_status,
                    cas.assmnt_date as class_date,
                    null as session_01,
                    assmnt_attdn as session_02,
                    tup.nationality as idtype,
                    cas.mode_of_training
                FROM course_class cc
                JOIN course c ON c.course_id = cc.course_id 
                JOIN class_enrol ce ON ce.class_id = cc.class_id 
                JOIN tms_users tu ON tu.user_id = ce.user_id 
                left join tms_users_pers tup on tup.user_id =tu.user_id 
                LEFT JOIN class_assmnt_schld cas ON cas.class_id = cc.class_id and cas.tenant_id = ce.tenant_id and cas.course_id = c.course_id
                JOIN class_assessment csn ON csn.class_id = cc.class_id and csn.user_id = ce.user_id and csn.course_id = c.course_id and csn.class_assmnt_date = cas.assmnt_date
                WHERE cc.tenant_id = '$tenant_id'
                AND c.course_id = '$course'
                AND cc.class_id = '$class'
                AND ce.user_id = '$userid'
                AND csn.assmnt_attdn = '1'    
                AND ce.eid_number != ''
                AND date(cc.class_end_datetime) <= '$today_date'";
        
                $res = $this->db->query($sql)->result();
        //echo $this->db->last_query();exit;
        return $res;
    }
    
    function uploadTmsClassShdl($tenant_id,$course_id,$class_id,$tpg_session_id,$user_id,$session_type_id){
        $this->db->select('*');
        $this->db->from('class_schld');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('tpg_session_id', $tpg_session_id);
        $this->db->where('class_id', $class_id);
        $schd_data = $this->db->get()->row();
        
        if ($session_type_id == 'S1') {
            $data = array(
                'session_01_tpg_uploaded_status' => '1'
            );
        } else if($session_type_id == 'S2') {
            $data = array(
                'session_02_tpg_uploaded_status' => '1'
            );
        }        
        
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('class_attdn_date', $schd_data->class_date);
        $this->db->where('user_id', $user_id);
        $status = $this->db->update('class_attendance', $data);
        return $status;
    }
    
    function uploadTmsAssessShdl($tenant_id,$course_id,$class_id,$tpg_session_id,$user_id){        
        $this->db->select('*');
        $this->db->from('class_assmnt_schld');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('tpg_assmnt_id', $tpg_session_id);
        $this->db->where('class_id', $class_id);
        $schd_data = $this->db->get()->row();
        
            $data = array(
                'tpg_uploaded_status' => '1'
            );
            
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        $this->db->where('class_assmnt_date', $schd_data->assmnt_date);
        $this->db->where('user_id', $user_id);
        $status = $this->db->update('class_assessment', $data);
        return $status;
    }

}
