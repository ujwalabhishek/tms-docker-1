<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_Model extends CI_Model {
    
    private $user;
    
    public function __construct() {
        parent::__construct();
        $this->user = $this->session->userdata('userDetails');
        
    }

    /**
     * This method gets trainee feedbacks for some class
     * @param $tenant_id
     * @param $course_id
     * @param $class_id
     */
    public function get_trainee_feedbacks($tenant_id, $course_id, $class_id) {
        $this->db->select("distinct ce.tenant_id, ce.other_remarks_trainee, "
                . "tf.feedback_question_id, rf.feedback_question_id as feedback_question, "
                . "ce.trainee_feedback_rating, ce.course_id, ce.class_id, ce.user_id,ce.pymnt_due_id,"
                . "ce.training_score,  "
                . " tup.first_name as name, tu.tax_code, "
                . "tu.tax_code_type, date(ce.trainee_fdbck_on) as feedback_date", FALSE);
        $this->db->from('class_enrol ce');
        $this->db->join('trainee_feedback tf', 'tf.user_id = ce.user_id and tf.class_id = ce.class_id and tf.tenant_id = ce.tenant_id and tf.course_id = ce.course_id', 'left');
        $this->db->join('trainer_feedback rf', 'rf.user_id = ce.user_id and rf.class_id = ce.class_id and rf.tenant_id = ce.tenant_id and rf.course_id = ce.course_id', 'left');
        $this->db->join('tms_users tu', 'ce.user_id = tu.user_id and ce.tenant_id = tu.tenant_id');
        $this->db->join('tms_users_pers tup', 'ce.user_id = tup.user_id and ce.tenant_id = tu.tenant_id');
        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $array = array('ce.tenant_id' => $tenant_id, 'ce.course_id' => $course_id, 'ce.class_id' => $class_id);
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('ce.company_id', $this->user->company_id);
        }

        $this->db->where($array);
        $query = $this->db->get();

        $result = $query->result_array();

        $grouped_by_user = array();
        foreach ($result as $res) {
            $user = $res['user_id'];
            $grouped_by_user[$user] = $res;
        }
        return $grouped_by_user;
    }

    /**
     * This method gets trainee feedbacks for some class nad user
     * @param $tenant_id
     * @param $course_id
     * @param $class_id
     * @param $user_id
     */
    public function get_trainee_feedback_by_user_id($tenant_id, $course_id, $class_id, $user_id) {
        $this->db->select('tf.*, mv.category_name, mv.parameter_id');
        $this->db->from('metadata_values mv');
        $this->db->join('trainee_feedback tf', 'tf.feedback_question_id = mv.parameter_id and tf.class_id = ' . $class_id . ' and tf.user_id =' . $user_id .
                ' and tf.course_id =' . $course_id, 'left');
        $this->db->like('mv.category_id', 'CAT32_01', 'after');

        $query = $this->db->get();

        $result = $query->result_array();
        $trainee_feedback = $this->db->select('trainee_feedback_rating, other_remarks_trainee')->from('class_enrol')
                        ->where('class_id', $class_id)
                        ->where('course_id', $course_id)
                        ->where('user_id', $user_id)
                        ->where('tenant_id', $tenant_id)
                        ->get()->row();
        $grouped_by_question = array();
        foreach ($result as $res) {
            $grouped_by_question[$res['parameter_id']] = $res;
            $grouped_by_question[$res['parameter_id']]['trainee_feedback_rating'] = $trainee_feedback->trainee_feedback_rating;
            $grouped_by_question[$res['parameter_id']]['trainee_other_remarks'] = $trainee_feedback->other_remarks_trainee;
        }
        return $grouped_by_question;
    }

    /**
     * This function gets Trainee Name, NRIC, Payment status of Current Invoice id
     * */
    public function get_invoice_trainee($invoice_id) {
        $this->db->select('ei.*,ce.*,tup.*,tu.*');
        $this->db->from('enrol_invoice ei');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = ei.pymnt_due_id');
        $this->db->join('tms_users_pers tup', 'tup.user_id = ce.user_id');
        $this->db->join('tms_users tu', 'tu.user_id = ce.user_id');
        $this->db->where('ei.invoice_id', $invoice_id);
        $sql = $this->db->get();
        return $sql->result();
    }

    public function fetch_all_tenants() {
        $this->db->select('tenant_id,tenant_name');
        $this->db->from('tenant_master');
        $res = $this->db->get()->result_array();
        //print_r($res);exit;
        return $res;
    }

    //// added by shubhranshu to fetch sales report/////////////
    public function fetch_all_sales_data($tenant_id, $start, $end, $payment_status, $training_score) {
        $start_date = date('Y-m-d 00:00:00', strtotime($start));
        $end_date = date('Y-m-d 00:00:00', strtotime($end));

        $sql = "select distinct ce.user_id,tu.tax_code,ei.invoice_id,tup.first_name,cm.company_name,epd.class_fees,
            ceil((epd.class_fees * epd.discount_rate)) DIV 100 as Discount_Rate,
            epd.subsidy_amount,epd.gst_amount,ce.tg_number,epd.total_amount_due as invoice_amount,epr.recd_on,epr.mode_of_pymnt,epr.cheque_number,
            epr.cheque_date,ce.payment_status,cc.class_start_datetime,cc.class_name,ce.training_score
        from class_enrol ce
        left join enrol_pymnt_due epd on epd.user_id=ce.user_id and epd.pymnt_due_id= ce.pymnt_due_id
        left join enrol_invoice ei on ei.pymnt_due_id and ce.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
        left join course_class cc on cc.class_id=ce.class_id and cc.course_id=ce.course_id 
        left join tms_users tu on tu.user_id =ce.user_id and tu.user_id=epd.user_id
        left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= epd.user_id
        left join company_master cm on cm.company_id=ce.company_id
        left join enrol_paymnt_recd epr on epr.invoice_id=ei.invoice_id ";

        if ($payment_status == 'NOTPAID') {
            $sql .="where ce.tenant_id='$tenant_id' and ce.payment_status ='NOTPAID' and epd.att_status=1 and ";
        } elseif ($payment_status == 'PAID') {
            $sql .="where ce.tenant_id='$tenant_id' and ce.payment_status ='PAID' and epd.att_status=1 and ";
        } else {
            $sql .="where ce.tenant_id='$tenant_id' and epd.att_status=1 and ";
        }

        if ($training_score == 'ALL') {
            $sql .="date(cc.class_start_datetime)>= '$start_date' and date(cc.class_start_datetime) <= '$end_date' 
                order by cc.class_start_datetime asc";
        } else {
            $sql .="ce.training_score = '$training_score' and 
                date(cc.class_start_datetime)>= '$start_date' and date(cc.class_start_datetime) <= '$end_date' 
                order by cc.class_start_datetime asc";
        }

        $tabledata = $this->db->query($sql)->result();
        //echo $this->db->last_query();
        //print_r($tabledata);exit;
        $tabledata_count = count($tabledata);

        $this->load->helper('export_helper');
        $count_tabledata = count($tabledata);
        $excel_titles = array('User ID #', 'Tax Code.', 'Invoice ID.', 'First name.', 'Company name.', 'Class Fees.', 'Discount Rate', 'Subsidy Amount', 'GST Amount', 'TG-Number', 'Invoice Amt', 'Received ON', 'Mode Of Payment', 'Cheque No', 'Cheque Date', 'Payment Status', 'Class Name', 'Class Start Date Time', 'Training Score');
        $excel_data = array();
        for ($i = 0; $i < $count_tabledata; $i++) {
            $excel_data[$i][] = $tabledata[$i]->user_id;
            $excel_data[$i][] = $tabledata[$i]->tax_code;
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = $tabledata[$i]->first_name;
            $excel_data[$i][] = $tabledata[$i]->company_name;
            $excel_data[$i][] = $tabledata[$i]->class_fees;
            $excel_data[$i][] = $tabledata[$i]->Discount_Rate;
            $excel_data[$i][] = $tabledata[$i]->subsidy_amount;
            $excel_data[$i][] = $tabledata[$i]->gst_amount;
            $excel_data[$i][] = $tabledata[$i]->tg_number;
            $excel_data[$i][] = $tabledata[$i]->invoice_amount;
            $excel_data[$i][] = $tabledata[$i]->recd_on;
            $excel_data[$i][] = $tabledata[$i]->mode_of_pymnt;
            $excel_data[$i][] = $tabledata[$i]->cheque_number;
            $excel_data[$i][] = $tabledata[$i]->cheque_date;
            $excel_data[$i][] = $tabledata[$i]->payment_status;
            $excel_data[$i][] = $tabledata[$i]->class_name;
            $excel_data[$i][] = $tabledata[$i]->class_start_datetime;
            $excel_data[$i][] = $tabledata[$i]->training_score;
        }

        $excel_filename = 'sales_report_all.xls';
        $excel_sheetname = 'Sales Report';
        $excel_main_heading = 'Accounting Reports - Sales Report';
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    //// added by shubhranshu to fetch sales report////////////////////////////////////////////////////////
    /**
     * This function gets Trainee Name, NRIC, Payment status of Previous Invoice id
     * */
    public function get_prev_invoice_trainee($prev_invoice_id, $inv_type) {
        $this->db->select('enrol_invoice_view.invoice_details');
        $this->db->from('enrol_invoice_view');
        $this->db->where('enrol_invoice_view.invoice_id', $prev_invoice_id);
        $sql = $this->db->get()->row(0);
        $invoice_details = $sql->invoice_details;


        if ($inv_type == "INVINDV") {
            $result = (array) json_decode($invoice_details);
            return $result;
        } else {
            $data = (array) json_decode($invoice_details);
            foreach ($data['payment_due_details'] as $val) {
                $array[] = array(
                    'first_name' => $val->first_name,
                    'last_name' => $val->last_name,
                    'tax_code' => $val->tax_code,
                    'payment_status' => $val->payment_status
                );
            }
            return $array;
        }
    }

    /**
     * trainer feedback
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $user_id
     * @return type
     */
    public function get_trainer_feedback_by_user_id($tenant_id, $course_id, $class_id, $user_id) {
        $this->db->select('tf.*, mv.category_name, mv.parameter_id');
        $this->db->from('metadata_values mv');
        $this->db->join('trainer_feedback tf', 'tf.feedback_question_id = mv.parameter_id and tf.class_id = ' . $class_id . ' and tf.user_id =' . $user_id .
                ' and tf.course_id =' . $course_id, 'left');

        $this->db->like('mv.category_id', 'CAT42_01', 'after');
        $query = $this->db->get();
        $result = $query->result_array();
        $grouped_by_question = array();
        foreach ($result as $res) {
            $grouped_by_question[$res['parameter_id']] = $res;
        }
        return $grouped_by_question;
    }

    /**
     * This method gets trainee feedbacks for some class for xls report
     * @param $tenant_id
     * @param $course_id
     * @param $class_id
     */
    public function get_trainee_feedback_for_report($tenant_id, $course_id, $class_id) {
        $this->db->select("enrl.training_score,enrl.trainee_feedback_rating, enrl.trainer_fdbck_on , enrl.user_id, usr.tax_code, pers.first_name, pers.last_name");
        $this->db->from('class_enrol enrl');
        $this->db->join('tms_users usr', 'enrl.user_id = usr.user_id and enrl.tenant_id = usr. tenant_id');
        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id and usr.tenant_id = pers.tenant_id');
        $this->db->where_in('enrl.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $array = array('enrl.tenant_id' => $tenant_id, 'enrl.course_id' => $course_id, 'enrl.class_id' => $class_id);
        $this->db->where($array);
        $this->db->group_by("enrl.user_id");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * This function gets the WDA report data
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee_name
     * @param type $tax_code
     * @param type $start_date
     * @param type $end_date
     * @param type $sort_field
     * @param type $sort_order
     * @param int $offset
     * @return type
     */
    public function get_wda_report_data($tenant_id, $course_id, $class_id, $trainee_name, $tax_code, $start_date, $end_date, $sort_field, $sort_order, $offset) {
        if (empty($offset))
            $offset = 0;
        $sql = "select SQL_CALC_FOUND_ROWS u.user_id, ce.class_id,
        concat(u.tax_code_type, ' - ',u.tax_code) as tax_code,
        up.first_name as name,
        u.account_type,
        concat(up.personal_address_bldg, ' ', up.personal_address_city, ' ', up.personal_address_state, ' ', up.personal_address_zip, ' ', up.personal_address_country) as address,
        concat('Contact Number: ', up.contact_number, ' Email Id: ', u.registered_email_Id) as contact_details,
        cc.class_start_datetime, assmnt.assmnt_date, pymnt.amount_recd
        from tms_users u
        join tms_users_pers up on u.user_id=up.user_id
        join class_enrol ce on ce.user_id = u.user_id
        join course_class cc on cc.class_id=ce.class_id
        left join class_assmnt_trainee assmnt on (assmnt.class_id=cc.class_id and assmnt.user_id=u.user_id)
        left join enrol_pymnt_due due on due.user_id = u.user_id
        left join enrol_invoice inv on inv.pymnt_due_id=due.pymnt_due_id
        left join enrol_paymnt_recd pymnt on pymnt.invoice_id=inv.invoice_id
        where u.tenant_id=" . $this->db->escape($tenant_id);
        if (!empty($start_date))
            $sql .= " AND date(class_end_datetime) >=" . $this->db->escape(date_format_mysql($start_date));
        if (!empty($end_date))
            $sql .= " AND date(class_end_datetime) <=" . $this->db->escape(date_format_mysql($end_date));
        if (!empty($course_id))
            $sql .= " AND cc.course_id=" . $this->db->escape($course_id);
        if (!empty($class_id))
            $sql .= " AND cc.class_id=" . $this->db->escape($class_id);
        if (!empty($trainee_name))
            $sql .= " AND (up.first_name like '" . $this->db->escape_like_str($trainee_name) . "%' OR up.last_name like '" . $this->db->escape_like_str($trainee_name) . "%') ";
        if (!empty($tax_code))
            $sql .= " AND u.tax_code=" . $this->db->escape($tax_code);
        ;
        if (!empty($sort_field))
            $sql .= " ORDER BY " . $sort_field . " " . $sort_order;
        if ($offset > -1)
            $sql .= " LIMIT " . $offset . "," . RECORDS_PER_PAGE;
        $query = $this->db->query($sql);
        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query' => $query, 'count' => $count);
    }

    public function invoice_autocomplete($tenant_id, $invoice) {
        $this->db->distinct("regen_inv_id");
        $this->db->select("regen_inv_id");
        $this->db->from('enrol_invoice_view');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->like('regen_inv_id', $invoice);
        $query = $this->db->get()->result();
        foreach ($query as $key => $v) {
            $output[] = array(
                'key' => $v->regen_inv_id,
                'value' => $v->regen_inv_id
            );
        }
        return $output;
    }

    /**
     * This function gets previous invoice list  
     * */
    public function prev_invoice_autocomplete($tenant_id, $prev_invoice) {
        //$this->db->distinct("regen_inv_id");
        $this->db->select("invoice_id");
        $this->db->from('enrol_invoice_view');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->like('invoice_id', $prev_invoice);
        $query = $this->db->get()->result();
        foreach ($query as $key => $v) {
            $output[] = array(
                'key' => $v->invoice_id,
                'value' => $v->invoice_id
            );
        }
        return $output;
    }

    /**
     * function to get  all invoice count
     * @param type $tenant_id
     * @param type $payment_status
     * @param type $start_date
     * @param type $end_date
     * @param type $company_id
     * @return type
     */
    public function get_reg_invoice_count($tenant_id, $payment_status, $start_date, $end_date, $company_id = 0, $invoice_id = 0, $prev_invoice_id = 0) {
        $this->db->select('eiv.invoice_id,eiv.pymnt_due_id,eiv.inv_date,eiv.date_on,eiv.reg_due_to,eiv.inv_type,'
                . 'eiv.reg_by,eiv.regen_inv_id,tu.user_name,cm.company_name');
        $this->db->from('enrol_invoice_view eiv');
        $this->db->join('tms_users tu', 'tu.user_id=eiv.reg_by');
        $this->db->join('company_master cm', 'cm.company_id=eiv.company_id', 'LEFT');
        $this->db->where('eiv.tenant_id', $tenant_id);
        if (!empty($company_id)) {
            $this->db->where('cm.company_id', $company_id);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(eiv.inv_date) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(eiv.inv_date) <=', $end_date_label);
        }
        if ($invoice_id != '') {
            $this->db->where('eiv.regen_inv_id', $invoice_id);
        }
        if ($prev_invoice_id != '') {
            $this->db->where('eiv.invoice_id', $prev_invoice_id);
        }

        $this->db->order_by('eiv.invoice_id', 'desc');

        //$results= $this->db->get()->num_rows();
        return $this->db->get()->num_rows();
    }

    /**
     * function to get  Regenerated / Deleted invoice
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $payment_status
     * @param type $start_date
     * @param type $end_date
     * @param type $company_id
     * @return type
     */
    public function get_reg_invoice($tenant_id, $limit, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $company_id = 0, $invoice_id = 0, $prev_invoice_id = 0) {

        $this->db->select('eiv.invoice_id,eiv.pymnt_due_id,eiv.inv_date,eiv.date_on,eiv.reg_due_to,eiv.inv_type,'
                . 'eiv.reg_by,eiv.regen_inv_id,tu.user_name,cm.company_name');
        $this->db->from('enrol_invoice_view eiv');
        $this->db->join('tms_users tu', 'tu.user_id=eiv.reg_by');
        $this->db->join('company_master cm', 'cm.company_id=eiv.company_id', 'LEFT');
        $this->db->where('eiv.tenant_id', $tenant_id);
        if (!empty($company_id)) {
            $this->db->where('cm.company_id', $company_id);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(eiv.inv_date) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(eiv.inv_date) <=', $end_date_label);
        }
        if ($invoice_id != '') {
            $this->db->where('eiv.regen_inv_id', $invoice_id);
        }
        if ($prev_invoice_id != '') {
            $this->db->where('eiv.invoice_id', $prev_invoice_id);
        }

        $this->db->order_by('eiv.invoice_id', 'desc');

        if ($limit) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }

//        $this->db->get();
//         echo $this->db->last_query();
        return $this->db->get()->result_object();
    }

    /**
     * Get Payment Report Data
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee_name
     * @param type $tax_code
     * @param type $start_date
     * @param type $end_date
     * @param type $sort_field
     * @param type $sort_order
     * @param int $offset
     * @return type
     */
    public function get_pymnt_report_data($tenant_id, $course_id, $class_id, $trainee_name, $tax_code, $start_date, $end_date, $sort_field, $sort_order, $offset) {
        if (empty($offset))
            $offset = 0;
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS * FROM (
                SELECT DISTINCT
                    IFNULL(cm.company_name, 'Individual') as companyName,
                    c.crse_name,
                    cc.class_name,
                    CASE
                        WHEN ei.inv_type = 'INVCOMALL' THEN cm.comp_regist_num
                        WHEN ei.inv_type = 'INVINDV' THEN CONCAT( IFNULL(mv.category_name, tu.country_of_residence), ', ', IFNULL(mv2.category_name, tu.tax_code_type), ', ', tu.tax_code)
                        ELSE ''
                    END AS tax_code,
                    CASE
                        WHEN ei.inv_type = '_INVCOMALL' THEN cm.company_name
                        WHEN ei.inv_type = 'INVINDV' THEN tup.first_name
                        ELSE ''
                    END AS name,
                    epr.recd_on,
                    epr.amount_recd
                FROM enrol_paymnt_recd epr
                    JOIN enrol_invoice ei ON ei.invoice_id = epr.invoice_id and ei.inv_type in ('INVCOMALL', 'INVINDV')
                    JOIN class_enrol ce ON ce.pymnt_due_id = ei.pymnt_due_id
                    JOIN course c on c.course_id = ce.course_id
                    JOIN course_class cc ON ce.class_id = cc.class_id
                    LEFT JOIN company_master cm ON cm.company_id = ei.company_id
                    LEFT JOIN tms_users tu ON tu.user_id = ce.user_id
                    LEFT JOIN metadata_values mv ON mv.parameter_id = tu.country_of_residence
                    LEFT JOIN metadata_values mv2 ON mv.parameter_id = tu.tax_code_type
                    LEFT JOIN tms_users_pers tup ON tup.user_id = ce.user_id
                WHERE
                    ce.tenant_id =
        " . $this->db->escape($tenant_id);
        if (!empty($start_date))
            $sql .= " AND epr.recd_on >=" . $this->db->escape(date_format_mysql($start_date));
        if (!empty($end_date))
            $sql .= " AND epr.recd_on <=" . $this->db->escape(date_format_mysql($end_date));
        if (!empty($course_id))
            $sql .= " AND cc.course_id=" . $this->db->escape($course_id);
        if (!empty($class_id))
            $sql .= " AND cc.class_id=" . $this->db->escape($class_id);
        $sql .= ' )T ';
        if (!empty($trainee_name) || !empty($tax_code))
            $sql .= ' WHERE ';
        if (!empty($trainee_name))
            $sql .= " name like '" . $this->db->escape_like_str($trainee_name) . "%' AND ";
        if (!empty($tax_code))
            $sql .= " tax_code like '" . $this->db->escape_like_str($tax_code) . "%'";
        if (!empty($sort_field))
            $sql .= " ORDER BY " . $sort_field . " " . $sort_order;
        if ($offset > -1)
            $sql .= " LIMIT " . $offset . "," . RECORDS_PER_PAGE;
        $query = $this->db->query($sql);
        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query' => $query, 'count' => $count);
    }

    /**
     * Get refunds report data
     * @param type $tenantID
     * @param type $courseID
     * @param type $classID
     * @param type $companyName
     * @param type $taxCode
     * @param type $startDate
     * @param type $endDate
     * @param type $sortField
     * @param type $sortOrder
     * @param int $offset
     * @return type
     */
    public function getRefundsReportData($tenantID, $courseID, $classID, $companyName, $taxCode, $startDate, $endDate, $sortField, $sortOrder, $offset) {
        if (empty($offset))
            $offset = 0;
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS * FROM (
                SELECT
                    er.invoice_id,
                    CASE
                        WHEN ei.inv_type = 'INVCOMALL' THEN cm.comp_regist_num
                        WHEN ei.inv_type = 'INVINDV' THEN CONCAT( IFNULL(mv.category_name, tu.country_of_residence), ', ', IFNULL(mv2.category_name, tu.tax_code_type), ', ', tu.tax_code)
                        ELSE ''
                    END AS tax_code,
                    CASE
                        WHEN ei.inv_type = 'INVCOMALL' THEN cm.company_name
                        WHEN ei.inv_type = 'INVINDV' THEN tup.first_name
                        ELSE ''
                    END AS name,
                    er.refund_on,
                    er.amount_refund,
                    CONCAT( IFNULL(mv3.category_name, er.refnd_reason), IF( IFNULL(er.refnd_reason_ot, '') <> '', CONCAT(', ', er.refnd_reason_ot), '') ) AS reason,
                    tup.first_name AS refund_by
                FROM enrol_refund er
                    JOIN enrol_invoice ei ON ei.invoice_id = er.invoice_id AND ei.inv_type IN ('INVCOMALL', 'INVINDV')
                    JOIN company_master cm ON cm.company_id = ei.company_id
                    JOIN class_enrol ce ON ce.pymnt_due_id = ei.pymnt_due_id
                    JOIN tms_users tu ON tu.user_id = ce.user_id
                    JOIN tms_users_pers tup ON tup.user_id = ce.user_id
                    JOIN metadata_values mv ON mv.parameter_id = tu.country_of_residence
                    JOIN metadata_values mv2 ON mv2.parameter_id = tu.tax_code_type
                    JOIN metadata_values mv3 ON mv3.parameter_id = er.refnd_reason
                WHERE
                    ce.user_id = er.refund_by AND ce.tenant_id = " . $this->db->escape($tenantID);

        if (!empty($startDate))
            $sql .= " AND er.refund_on >=" . $this->db->escape(date_format_mysql($startDate));
        if (!empty($endDate))
            $sql .= " AND er.refund_on <=" . $this->db->escape(date_format_mysql($endDate));
        if (!empty($courseID))
            $sql .= " AND ce.course_id=" . $this->db->escape($courseID);
        if (!empty($classID))
            $sql .= " AND ce.class_id=" . $this->db->escape($classID);
        $sql .= ' )T ';
        if (!empty($companyName) || !empty($taxCode))
            $sql .= ' WHERE ';
        if (!empty($companyName))
            $sql .= " name like '" . $this->db->escape_like_str($companyName) . "%' AND ";
        if (!empty($taxCode))
            $sql .= " tax_code like '" . $this->db->escape_like_str($taxCode) . "%'";

        if (!empty($sortField))
            $sql .= " ORDER BY " . $sortField . " " . $sortOrder;

        if ($offset > -1)
            $sql .= " LIMIT " . $offset . "," . RECORDS_PER_PAGE;
        $query = $this->db->query($sql);
        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query' => $query, 'count' => $count);
    }

    /**
     * This method gets certifications report
     * @param $tenant_id
     * @param $course_id
     * @param $class_id
     * @param $trainee_name
     * @param $status
     * @param $start_date
     * @param $end_date
     * @param $sort_field
     * @param $sort_order
     * @param $offset
     */
    public function get_cert_report_data($tenant_id, $course_id, $class_id, $trainee_name, $status, $start_date, $end_date, $sort_field, $sort_order, $offset, $extSearch) {
        if (empty($offset))
            $offset = 0;
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS * from (
                SELECT
                    c.crse_name,
                    c.crse_cert_validity,
                    cc.class_name,
                    CONCAT(u.tax_code_type, ' - ',u.tax_code) AS tax_code,
                    up.first_name AS name,
                    cc.class_end_datetime,
                    ce.certificate_coll_on,
                CASE
                    WHEN IFNULL(c.crse_cert_validity, 0) = 0 THEN 'PENDCOLL'
                    WHEN DATEDIFF(ADDDATE(cc.class_end_datetime, c.crse_cert_validity), CURDATE()) <= 0
                        THEN 'EXPIRD'
                        ELSE ''
                    END
                        AS status,
                ADDDATE(cc.class_end_datetime, c.crse_cert_validity) as validityDate
                FROM tms_users u
                    JOIN tms_users_pers up ON u.user_id=up.user_id
                    JOIN class_enrol ce ON ce.user_id = u.user_id
                    JOIN course_class cc ON cc.class_id = ce.class_id
                    JOIN course c ON c.course_id = cc.course_id
                WHERE
                    u.tenant_id = " . $this->db->escape($tenant_id);
        if (!empty($start_date))
            $sql .= " AND date(class_end_datetime) >=" . $this->db->escape(date_format_mysql($start_date));
        if (!empty($end_date))
            $sql .= " AND date(class_end_datetime) <=" . $this->db->escape(date_format_mysql($end_date));
        if (!empty($course_id))
            $sql .= " AND cc.course_id=" . $this->db->escape($course_id);
        if (!empty($class_id))
            $sql .= " AND cc.class_id=" . $this->db->escape($class_id);
        if (!empty($trainee_name) && $extSearch == 'name')
            $sql .= " AND (up.first_name like '" . $this->db->escape_like_str($trainee_name) . "%' OR up.last_name like '" . $this->db->escape_like_str($trainee_name) . "%') ";
        $sql .= ")T";

        if (!empty($status) && $extSearch == 'status')
            $sql .= " WHERE status ='$status'";

        if (!empty($sort_field))
            $sql .= " ORDER BY " . $sort_field . " " . $sort_order;

        if ($offset > -1)
            $sql .= " LIMIT " . $offset . "," . RECORDS_PER_PAGE;
        $query = $this->db->query($sql);
        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query' => $query, 'count' => $count);
    }

    /**
     * Gets the report data for Enrolment Report sales
     * @param type $tenant_id
     * @param type $sales_id
     * @param type $start_date
     * @param type $end_date
     * @param type $sort_by
     * @param type $sort_order
     * @param type $offset
     * @param type $limit
     * @return type
     */
    ////modified by shubhranshu 23/04/2019
    public function get_enrolments_for_sales($tenant_id, $sales_id, $abs_pr, $start_date = null, $end_date = null, $sort_by = null, $sort_order = null, $offset = null, $recordsPerPage = null) {
        $this->db->select("SQL_CALC_FOUND_ROWS c.crse_name, cc.class_name, "
                . "tu.tax_code_type, tu.tax_code, tup.first_name as name, "
                . "date(ce.enrolled_on) as enrolment_date, CONCAT(tup.contact_number,', ', "
                . "tu.registered_email_id) as contact, date(ce.enrolled_on) as enrolment_date, "
                . "tu.country_of_residence as country", FALSE);
        $this->db->from('class_enrol ce');
        $this->db->join('tms_users tu', 'ce.user_id = tu.user_id and ce.tenant_id = tu.tenant_id');
        $this->db->join('tms_users_pers tup', 'ce.user_id = tup.user_id and ce.tenant_id = tup.tenant_id');
        $this->db->join('course c', 'ce.course_id = c.course_id and c.tenant_id = ce.tenant_id');
        //$this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id');
        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id=ce.user_id');
        $this->db->join('course_class cc', 'cc.class_id = ce.class_id and cc.course_id = ce.course_id and cc.tenant_id = ce.tenant_id');
        $array = array('ce.tenant_id' => $tenant_id);
        $this->db->where($array);

        if (!empty($abs_pr)) {
            if ($abs_pr == "pr") {
                $this->db->where('epd.att_status', 1);
            } else {
                $this->db->where('epd.att_status', 0);
            }
        }
        if (!empty($sales_id)) {
            $this->db->where('ce.sales_executive_id', $sales_id);
        }
        if (!empty($start_date)) {
            $this->db->where('date(ce.enrolled_on)>=', $start_date);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('date(ce.enrolled_on)<=', $end_date);
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('ce.enrolled_on, c.crse_name, cc.class_name, tup.last_name', 'asc');
        }

        ////modified by shubhranshu 23/04/2019    
        if ($offset != null && $recordsPerPage != null) {
            if ($recordsPerPage == $offset) {
                $this->db->limit($offset);
            } else if ($recordsPerPage > 0) {
                $limitvalue = $offset - $recordsPerPage;
                $this->db->limit($recordsPerPage, $limitvalue);
            }
        }


        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        //$count = $query->num_rows();
        return array($query->result_array());
    }

    ////added by shubhranshu/////
    public function get_enrolments_for_sales_count($tenant_id, $sales_id, $abs_pr, $start_date = null, $end_date = null, $sort_by = null) {
        $this->db->select("SQL_CALC_FOUND_ROWS c.crse_name, cc.class_name, "
                . "tu.tax_code_type, tu.tax_code, tup.first_name as name, "
                . "date(ce.enrolled_on) as enrolment_date, CONCAT(tup.contact_number,', ', "
                . "tu.registered_email_id) as contact, date(ce.enrolled_on) as enrolment_date, "
                . "tu.country_of_residence as country", FALSE);
        $this->db->from('class_enrol ce');
        $this->db->join('tms_users tu', 'ce.user_id = tu.user_id and ce.tenant_id = tu.tenant_id');
        $this->db->join('tms_users_pers tup', 'ce.user_id = tup.user_id and ce.tenant_id = tup.tenant_id');
        $this->db->join('course c', 'ce.course_id = c.course_id and c.tenant_id = ce.tenant_id');
        //$this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id');
        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id=ce.user_id');
        $this->db->join('course_class cc', 'cc.class_id = ce.class_id and cc.course_id = ce.course_id and cc.tenant_id = ce.tenant_id');
        $array = array('ce.tenant_id' => $tenant_id);
        $this->db->where($array);

        if (!empty($abs_pr)) {
            if ($abs_pr == "pr") {
                $this->db->where('epd.att_status', 1);
            } else {
                $this->db->where('epd.att_status', 0);
            }
        }
        if (!empty($sales_id)) {
            $this->db->where('ce.sales_executive_id', $sales_id);
        }
        if (!empty($start_date)) {
            $this->db->where('date(ce.enrolled_on)>=', $start_date);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('date(ce.enrolled_on)<=', $end_date);
        }
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('ce.enrolled_on, c.crse_name, cc.class_name, tup.last_name', 'asc');
        }

        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        $count = $query->num_rows();
        return $count;
    }

////added by shubhranshu/////

    /**
     * This method gets Selas report
     * @param $tenant_id
     * @param $course_id
     * @param $class_id
     * @param $trainee_name
     * @param $status
     * @param $start_date
     * @param $end_date
     * @param $sort_field
     * @param $sort_order
     * @param $offset
     */
    public function get_sales_report_data($tenant_id, $course_id, $class_id, $execId, $start_date, $end_date, $sort_field, $sort_order, $offset) {
        if (empty($offset))
            $offset = 0;
        $sql = "SELECT SQL_CALC_FOUND_ROWS
	scd.comm_detail as name,
	tup.first_name AS executive_name,
	scp.amount_paid as commision_paid,
	scd.comm_amount as commision_due,
	scp.cheque_date as last_payed,
	DATE_FORMAT(CONCAT(scd.comm_period_yr,'-', scd.comm_period_mth, '-01'), '%M, %Y') as due_period,
	scd.pymnt_status
FROM sales_comm_due scd
	INNER JOIN course_sales_exec cse ON cse.course_id = scd.course_id
	INNER JOIN tms_users_pers tup ON tup.user_id = cse.user_id
	INNER JOIN sales_comm_pymnt scp ON scd.pymnt_due_id = scp.pymnt_due_id
WHERE scd.tenant_id = " . $this->db->escape($tenant_id);
        if (!empty($execId))
            $sql .= " AND tup.user_id = " . $this->db->escape($execId);
        if (!empty($start_date))
            $sql .= " AND scp.cheque_date >=" . $this->db->escape(date_format_mysql($start_date));
        if (!empty($end_date))
            $sql .= " AND scp.cheque_date <=" . $this->db->escape(date_format_mysql($end_date));
        if (!empty($course_id))
            $sql .= " AND scd.course_id=" . $this->db->escape($course_id);
        if (!empty($sort_field))
            $sql .= " ORDER BY " . $sort_field . " " . $sort_order;
        if ($offset > -1)
            $sql .= " LIMIT " . $offset . "," . RECORDS_PER_PAGE;
        $query = $this->db->query($sql);
        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query' => $query, 'count' => $count);
    }

    /**
     * This method gets Selas Executive
     * @param $tenant_id
     */
    function get_sales_executive($tenant_id) {
        $sql = " SELECT tup.user_id,
	tup.first_name as user_name
	FROM internal_user_role iur
	INNER JOIN tms_users_pers tup ON tup.user_id = iur.user_id
	INNER JOIN tms_users usr ON tup.user_id = usr.user_id
        WHERE iur.role_id = 'SLEXEC' AND usr.account_status !='PENDACT' 
	AND iur.tenant_id = " . $this->db->escape($tenant_id) .
                " ORDER BY tup.first_name";
        return $this->db->query($sql);
    }

    /**
     * This method gets Selas Executive
     * @param type $tenant_id
     * @param type $execId
     * @param type $start_date
     * @param type $end_date
     * @param type $sort_field
     * @param type $sort_order
     * @param int $offset
     * @param type $get_total
     * @return type
     */
    public function get_pymnt_due_report_data($tenant_id, $execId, $start_date, $end_date, $sort_field, $sort_order, $offset, $get_total = false) {
        if (empty($offset))
            $offset = 0;
        if ($get_total) {
            $sql = 'SELECT
		SUM(IFNULL(total_inv_discnt, 0)) total_inv_discnt,
		SUM(IFNULL(total_inv_subsdy, 0)) total_inv_subsdy,
		SUM(IFNULL(total_gst, 0)) total_gst,
		SUM(IFNULL(total_unit_fees, 0)) total_unit_fees,
		SUM(IFNULL(total_inv_amount, 0)) total_inv_amount
                FROM (';
        } else
            $calc_rows = " SQL_CALC_FOUND_ROWS";

        $sql .="
SELECT  {$calc_rows} c.crse_name,
		cc.class_name,
		tup.first_name as exec_name,
		CONCAT(mv.category_name, ' - ', IFNULL(mv2.category_name, tu.tax_code)) tax_code,
		tup2.first_name as user_name,
		ce.enrolled_on,
		ei.total_inv_discnt,
		ei.total_inv_subsdy,
		ei.total_gst,
		ei.total_unit_fees,
		ei.total_inv_amount
	FROM course c
	JOIN course_class cc ON cc.course_id = c.course_id
	JOIN class_enrol ce ON  ce.class_id = cc.class_id
	JOIN enrol_invoice ei ON ei.pymnt_due_id = ce.pymnt_due_id
	JOIN tms_users tu ON tu.user_id = ce.sales_executive_id
	JOIN tms_users_pers tup ON tup.user_id = ce.sales_executive_id
	JOIN tms_users_pers tup2 ON tup2.user_id = ce.user_id
	LEFT JOIN metadata_values mv ON mv.parameter_id = tu.tax_code_type
	LEFT JOIN metadata_values mv2 ON mv2.parameter_id = tu.tax_code
		WHERE ce.payment_status in ('NOTPAID','PARTPAID')
		    AND c.tenant_id = " . $this->db->escape($tenant_id);

        if (!empty($start_date))
            $sql .= " AND ei.inv_date >=" . $this->db->escape(date_format_mysql($start_date));
        if (!empty($end_date))
            $sql .= " AND ei.inv_date <=" . $this->db->escape(date_format_mysql($end_date));

        if (!empty($execId))
            $sql .= " AND ce.sales_executive_id =" . $execId;
        if ($get_total) {
            $sql .=')T';
        } else {
            if (!empty($sort_field))
                $sql .= " ORDER BY " . $sort_field . " " . $sort_order;

            if ($offset > -1)
                $sql .= " LIMIT " . $offset . "," . RECORDS_PER_PAGE;
        }

        $query = $this->db->query($sql);

        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query' => $query, 'count' => $count);
    }

    /**
     * function to get  all invoice count
     * @param type $tenant_id
     * @param type $payment_status
     * @param type $start_date
     * @param type $end_date
     * @param type $company_id
     * @return type
     */
    public function get_all_invoice_count($tenant_id, $payment_status, $start_date, $end_date, $company_id = 0) {
        $not_paid = $this->db->select('ei.invoice_id')
                        ->from('class_enrol ce')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id and epd.user_id=ce.user_id')
                        ->where('epd.att_status', 1)
                        ->where_in('ce.payment_status', array('PARTPAID', 'NOTPAID'))->where('ce.tenant_id', $tenant_id)->group_by('ei.invoice_id')->get()->result();
        $not_paid_arr = array();
        foreach ($not_paid as $row) {
            $not_paid_arr[] = $row->invoice_id;
        }
        $this->db->select('ei.invoice_id, ei.pymnt_due_id, ei.inv_date, ei.total_inv_amount, ei.total_inv_discnt, ei.total_inv_subsdy, 
                ei.total_gst,cm.company_name, cm.comp_regist_num, ce.payment_status, ce.enrolment_mode,
                tup.first_name, tup.last_name, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('company_master cm', 'cm.company_id=ce.company_id', 'left')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id and epd.user_id=ce.user_id')
                ->where('epd.att_status', 1)
                ->where('ce.tenant_id', $tenant_id)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        if (!empty($company_id)) {
            $this->db->where('ce.company_id', $company_id);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(ei.inv_date) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(ei.inv_date) <=', $end_date_label);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('ce.company_id', $this->user->company_id);
        }
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->join('course_class ccl', 'ccl.course_id=ce.course_id and ccl.class_id=ce.class_id');
            $this->db->join('course c', 'c.tenant_id = ccl.tenant_id AND c.course_id=ccl.course_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');
        }
        if (!empty($payment_status) && !empty($not_paid_arr)) {
            if ($payment_status == 'PAID') {
                $this->db->where_not_in('ei.invoice_id', $not_paid_arr);
            } elseif ($payment_status == 'NOINVD') {
                $this->db->where('ei.invoiced_on', NULL);
                $this->db->or_where('ei.invoiced_on', '0000-00-00 00:00:00');
            } elseif ($payment_status == 'INVD') {
                $this->db->where('ei.invoiced_on IS NOT NULL', NULL, FALSE);
                $this->db->where('ei.invoiced_on !=', '0000-00-00 00:00:00');
            } else {
                $this->db->where_in('ei.invoice_id', $not_paid_arr);
            }
        }
        $this->db->group_by('ce.pymnt_due_id');
        return $this->db->get()->num_rows();
    }

    /**
     * function to get  all wda count
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $taxcode
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_wda_count($tenant_id, $course_id, $class_id, $trainee, $taxcode, $start_date, $end_date) {
        $this->db->select('usr.tax_code, usr.user_id, usr.account_type, enrol.class_id, cls.class_start_datetime,cls.class_end_datetime,  enrol.payment_status, pay.total_amount_due,  pers.first_name, pers.last_name, pers.contact_number, usr.registered_email_id, pers.personal_address_bldg, pers.personal_address_city, pers.personal_address_state, pers.personal_address_country, pers.personal_address_zip');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id');
        $this->db->join('class_enrol enrol', 'usr.user_id = enrol.user_id');
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id');
        $this->db->join('enrol_pymnt_due pay', 'enrol.user_id = pay.user_id and enrol.pymnt_due_id = pay.pymnt_due_id', 'left');
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($taxcode)) {
            $this->db->where('enrol.user_id', $taxcode);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_start_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_start_datetime) <=', $end_date_label);
        }
        $this->db->where('enrol.tenant_id', $tenant_id);
        return $this->db->get()->num_rows();
    }

    /**
     * function to get  all wda
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $taxcode
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_wda($tenant_id, $limit, $offset, $field, $order_by, $course_id, $class_id, $trainee, $taxcode, $start_date, $end_date) {
        $this->db->select('usr.tax_code, usr.user_id, usr.account_type, enrol.class_id, cls.class_start_datetime,cls.class_end_datetime,  enrol.payment_status, pay.total_amount_due,  pers.first_name, pers.last_name, pers.contact_number, usr.registered_email_id, pers.personal_address_bldg, pers.personal_address_city, pers.personal_address_state, pers.personal_address_country, pers.personal_address_zip');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.user_id = pers.user_id');
        $this->db->join('class_enrol enrol', 'usr.user_id = enrol.user_id');
        $this->db->select('crs.crse_name');
        $this->db->join('course crs', 'crs.course_id=enrol.course_id');
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id');
        $this->db->join('enrol_pymnt_due pay', 'enrol.user_id = pay.user_id and enrol.pymnt_due_id = pay.pymnt_due_id', 'left');
        $this->db->order_by('cls.class_end_datetime', 'DESC');
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($taxcode)) {
            $this->db->where('enrol.user_id', $taxcode);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_start_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_start_datetime) <=', $end_date_label);
        }
        $this->db->where('enrol.tenant_id', $tenant_id);
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }

        return $this->db->get()->result_object();
    }

    /**
     * function to get all certificates report
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_certificates_count($tenant_id, $course_id, $class_id, $trainee, $status, $start_date, $end_date) {
        $cur_date = date('Y-m-d');
        $this->db->select('enrol.course_id,enrol.company_id, crse.crse_name, enrol.class_id,cls.class_name, enrol.user_id, 
        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name, crse.crse_cert_validity, 
        cls.class_end_datetime, cls.certi_coll_date');
        //,cm.comp_email,cm.company_name,cm.company_id
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');
        //$this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to');
        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
        $this->db->order_by('enrol.course_id');
        $this->db->order_by('enrol.class_id');
        $this->db->order_by('enrol.user_id');
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }

        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        return $this->db->get()->num_rows();
    }

    /**
     * function to get all trainee summary report
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $order_by
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_trainee_summary($tenant_id, $limit, $offset, $sort_by, $order_by, $course_id, $class_id, $trainee, $status, $start_date, $end_date, $invoice_id) {
        $cur_date = date('Y-m-d');
        $this->db->select('enrol.course_id,enrol.tg_number, enrol.payment_status,cm.company_name,crse.crse_name, enrol.class_id,cls.class_name, cls.class_id, 
        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name, crse.crse_cert_validity, 
        cls.class_end_datetime, cls.certi_coll_date, enrol.user_id,tf.feedback_answer,epd.att_status,epd.pymnt_due_id,epd.total_amount_due,ei.invoice_id,ei.pymnt_due_id');
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('company_master cm', 'cm.company_id=enrol.company_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');

        $this->db->join('trainer_feedback tf', 'tf.tenant_id=enrol.tenant_id and tf.course_id=enrol.course_id and tf.class_id=enrol.class_id and tf.user_id=enrol.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');
        $this->db->join('enrol_pymnt_due epd', 'epd.user_id=enrol.user_id and epd.pymnt_due_id=enrol.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=epd.pymnt_due_id and ei.pymnt_due_id=enrol.pymnt_due_id');

        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
        if ($sort_by) {
            //$this->db->order_by($sort_by, $order_by);
            $this->db->order_by('cls.class_end_datetime', 'DESC');
        } else {
//            $this->db->order_by('enrol.course_id');
//            $this->db->order_by('enrol.class_id');
//            $this->db->order_by('enrol.user_id');
            $this->db->order_by('cls.class_end_datetime');
        }
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($invoice_id)) {
            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        return $this->db->get()->result();
    }

    /**
     * function to get all gst report
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $order_by
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_gst_report($tenant_id, $limit, $offset, $sort_by, $order_by, $course_id, $class_id, $trainee, $status, $start_date, $end_date) {
        $cur_date = date('Y-m-d');
        $this->db->select('enrol.course_id,enrol.tg_number, enrol.payment_status,cm.company_name,crse.crse_name, enrol.class_id,cls.class_name, cls.class_id, 
        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name, crse.crse_cert_validity, 
            cls.class_start_datetime,cls.class_end_datetime, cls.certi_coll_date, enrol.user_id,tf.feedback_answer,epd.class_fees,epd.total_amount_due,epd.discount_rate,epd.subsidy_amount,epd.gst_amount,epd.att_status,epd.pymnt_due_id,epd.total_amount_due,ei.invoice_id,ei.pymnt_due_id,ei.total_inv_amount,(SELECT COUNT(*) FROM enrol_pymnt_due ei WHERE ei.pymnt_due_id = epd.pymnt_due_id ) AS  total_inv_people');
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('company_master cm', 'cm.company_id=enrol.company_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');

        $this->db->join('trainer_feedback tf', 'tf.tenant_id=enrol.tenant_id and tf.course_id=enrol.course_id and tf.class_id=enrol.class_id and tf.user_id=enrol.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');
        $this->db->join('enrol_pymnt_due epd', 'epd.user_id=enrol.user_id and epd.pymnt_due_id=enrol.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=epd.pymnt_due_id and ei.pymnt_due_id=enrol.pymnt_due_id');

        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('epd.att_status', 1);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
        if ($sort_by) {
            //$this->db->order_by($sort_by, $order_by);
            $this->db->order_by('cls.class_end_datetime', 'DESC');
        } else {
//            $this->db->order_by('enrol.course_id');
//            $this->db->order_by('enrol.class_id');
//            $this->db->order_by('enrol.user_id');
            $this->db->order_by('cls.class_end_datetime');
        }
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($comp_id)) {
            $this->db->where('enrol.company_id', $comp_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }

        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }

        return $this->db->get()->result();
    }

    /**
     * function to get all trainee summary  report
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_trainee_summary_count($tenant_id, $course_id, $class_id, $trainee, $status, $start_date, $end_date, $invoice_id) {
        $cur_date = date('Y-m-d');
        $this->db->select('enrol.course_id,enrol.tg_number,enrol.company_id,cm.company_name, crse.crse_name, enrol.class_id,cls.class_name, enrol.user_id, 
        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name, crse.crse_cert_validity, 
        cls.class_end_datetime, cls.certi_coll_date,tf.feedback_answer,epd.pymnt_due_id,epd.att_status,epd.total_amount_due,ei.invoice_id,ei.pymnt_due_id');
        //,cm.comp_email,cm.company_name,cm.company_id
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');

        $this->db->join('trainer_feedback tf', 'tf.tenant_id=enrol.tenant_id and tf.course_id=enrol.course_id and tf.class_id=enrol.class_id and tf.user_id=enrol.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');
        $this->db->join('enrol_pymnt_due epd', 'epd.user_id=enrol.user_id and epd.pymnt_due_id=enrol.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=epd.pymnt_due_id and ei.pymnt_due_id=enrol.pymnt_due_id');
        $this->db->join('company_master cm', 'cm.company_id=enrol.company_id');

        //$this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to');


        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
//        $this->db->order_by('enrol.course_id');
//        $this->db->order_by('enrol.class_id');
//        $this->db->order_by('enrol.user_id');

        $this->db->order_by('cls.class_end_datetime', 'DESC');

        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }

        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($invoice_id)) {
            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        return $this->db->get()->num_rows();
    }

    /**
     * function to get all gst  report
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_gst_report_count($tenant_id, $course_id, $class_id, $trainee, $status, $start_date, $end_date) {
        $cur_date = date('Y-m-d');
        //////modified by shubhranshu for optimized the query///////
//        $this->db->select('enrol.course_id,enrol.tg_number,enrol.company_id,cm.company_name, crse.crse_name, enrol.class_id,cls.class_name, enrol.user_id, 
//        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name, crse.crse_cert_validity, 
//        cls.class_end_datetime, cls.certi_coll_date,tf.feedback_answer,,epd.class_fees,epd.total_amount_due,epd.discount_rate,epd.subsidy_amount,epd.gst_amount,epd.att_status,epd.pymnt_due_id,epd.pymnt_due_id,epd.att_status,epd.total_amount_due,ei.invoice_id,ei.pymnt_due_id,ei.total_inv_amount');
        //,cm.comp_email,cm.company_name,cm.company_id
        $this->db->select('enrol.course_id');
        ////////////////------//////////////////////
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');
        ////////commented by shubhranshu to optimized the query//////////////////
        //$this->db->join('trainer_feedback tf', 'tf.tenant_id=enrol.tenant_id and tf.course_id=enrol.course_id and tf.class_id=enrol.class_id and tf.user_id=enrol.user_id and tf.feedback_question_id="COMYTCOM"', 'LEFT');
        ////////-------------/////////
        $this->db->join('enrol_pymnt_due epd', 'epd.user_id=enrol.user_id and epd.pymnt_due_id=enrol.pymnt_due_id');
        $this->db->join('enrol_invoice ei', 'ei.pymnt_due_id=epd.pymnt_due_id and ei.pymnt_due_id=enrol.pymnt_due_id');
        $this->db->join('company_master cm', 'cm.company_id=enrol.company_id');

        //$this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to');


        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
//        $this->db->order_by('enrol.course_id');
//        $this->db->order_by('enrol.class_id');
//        $this->db->order_by('enrol.user_id');

        $this->db->order_by('cls.class_end_datetime', 'DESC');

        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($comp_id)) {
            $this->db->where('enrol.company_id', $comp_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        return $this->db->get()->num_rows();
    }

    //added by pritam
    /**
     * function to get all certificates report
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_certificates_dist_count($tenant_id, $course_id, $class_id, $comp_id, $trainee, $status, $start_date, $end_date) {
        $cur_date = date('Y-m-d');
        $this->db->select('*'); ////modified by shubhranshu to optimize query
        //,cm.comp_email,cm.company_name,cm.company_id
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');
        //$this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to');
        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
        $this->db->order_by('enrol.course_id');
        $this->db->order_by('enrol.class_id');
        $this->db->order_by('enrol.user_id');
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($comp_id)) {
            $this->db->where('enrol.company_id', $comp_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        return $this->db->get()->num_rows();
    }

    ///end
    /**
     * function to get all certificates report
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $order_by
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee
     * @param type $status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_certificates($tenant_id, $limit, $offset, $sort_by, $order_by, $course_id, $class_id, $trainee, $status, $start_date, $end_date) {
        $cur_date = date('Y-m-d');
        $this->db->select('enrol.course_id, enrol.payment_status, crse.crse_name, enrol.class_id,cls.class_name, cls.class_id, 
        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name, crse.crse_cert_validity, 
        cls.class_end_datetime, cls.certi_coll_date, enrol.user_id');
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');
        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
        if ($sort_by) {
            $this->db->order_by($sort_by, $order_by);
        } else {
            $this->db->order_by('enrol.course_id');
            $this->db->order_by('enrol.class_id');
            $this->db->order_by('enrol.user_id');
        }
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(cls.class_end_datetime) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(cls.class_end_datetime) <=', $end_date_label);
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $this->db->where('enrol.certificate_coll_on', NULL);
            } elseif ($status == 'EXPIRD') {
                $this->db->where('DATE(DATE_ADD(cls.class_end_datetime, INTERVAL crse.crse_cert_validity DAY)) <=', $cur_date);
                $this->db->where('crse.crse_cert_validity >', 0);
            }
        }
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        return $this->db->get()->result();
    }

    /**
     * function to get  certificates distribution report
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $sort_by
     * @param type $order_by
     * @param type $course_id
     * @param type $class_id
     * @param type $trainee


     * @return type
     */
    public function get_certificates_dist($tenant_id, $limit, $offset, $sort_by, $order_by, $course_id, $class_id, $comp_id, $trainee) {
        $cur_date = date('Y-m-d');
        $this->db->select('enrol.course_id,enrol.company_id,crse.crse_name,enrol.class_id,cls.class_name, cls.class_id, 
        enrol.certificate_coll_on, usrs.tax_code, pers.first_name, pers.last_name,pers.cert_sent_to, crse.crse_cert_validity, 
        cls.class_end_datetime, cls.certi_coll_date, enrol.user_id,cm.comp_email,cm.company_name,cm.company_id');
        $this->db->from('class_enrol enrol');
        if ($this->user->role_id == 'CRSEMGR') {
            $crsemgr_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',crse.crse_manager)';
        }
        $this->db->join('course crse', 'enrol.course_id = crse.course_id ' . $crsemgr_where);
        if ($this->user->role_id == 'TRAINER') {
            $trainer_where = 'AND FIND_IN_SET(' . $this->user->user_id . ',cls.classroom_trainer)';
        }

        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id ' . $trainer_where);
        $this->db->join('tms_users usrs', 'enrol.user_id = usrs.user_id');
        $this->db->join('tms_users_pers pers', 'enrol.user_id = pers.user_id and usrs.user_id = pers.user_id');
        $this->db->join('company_master cm', 'cm.company_id=pers.cert_sent_to', 'left');

        $this->db->where_in('enrol.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where('date(cls.class_end_datetime) <=', $cur_date);
        $this->db->where_not_in('cls.class_status', array('INACTIV'));
        if ($sort_by) {
            $this->db->order_by($sort_by, $order_by);
        } else {
            $this->db->order_by('enrol.course_id');
            $this->db->order_by('enrol.class_id');
            $this->db->order_by('enrol.user_id');
        }
        if (!empty($course_id)) {
            $this->db->where('enrol.course_id', $course_id);
        }
        if (!empty($class_id)) {
            $this->db->where('enrol.class_id', $class_id);
        }
        if (!empty($comp_id)) {
            $this->db->where('enrol.company_id', $comp_id);
        }
        if (!empty($trainee)) {
            $this->db->where('enrol.user_id', $trainee);
        }

        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where('enrol.sales_executive_id', $this->user->user_id);
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('enrol.company_id', $this->user->company_id);
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_company_details($user_id) {
        $this->db->select('cm.company_name as com_name, cm.company_id,cm.comp_email');
        $this->db->from('tenant_company_users tcu');
        $this->db->join('company_master cm', 'cm.company_id=tcu.company_id');
        $this->db->where('tcu.user_id', $user_id);
        $this->db->where("tcu.tenant_id", $this->user->tenant_id);
        $this->db->where("cm.company_status", 'ACTIVE');
        $this->db->where("tcu.user_acct_status", 'ACTIVE');
        return $this->db->get();
    }

    /**
     * function to get not paid/ part paid invoice
     * @param type $payment_due_id
     * @return type
     */
//    public function check_not_part_paid($payment_due_id) {
//        $result = $this->db->select('count(*) as totalrow')->from('class_enrol')->where('pymnt_due_id', $payment_due_id)
//                        ->where_in('payment_status', array('PARTPAID', 'NOTPAID'))->get()->row(0)->totalrow;
//        return $result;
//    }
    public function check_not_part_paid($payment_due_id) {

        $result = $this->db->select('count(*) as totalrow')
                        ->from('class_enrol ce')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id and epd.user_id=ce.user_id')
                        ->where('ce.pymnt_due_id', $payment_due_id)
                        ->where('epd.pymnt_due_id', $payment_due_id)
                        ->where('epd.att_status', 1)
                        ->where_in('ce.payment_status', array('PARTPAID', 'NOTPAID'))
                        ->get()->row(0)->totalrow;

        return $result;
    }

    /**
     * function to get  all invoice
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $payment_status
     * @param type $start_date
     * @param type $end_date
     * @param type $company_id
     * @return type
     */
    public function get_all_invoice($tenant_id, $limit, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $company_id = 0) {
        $not_paid = $this->db->select('ei.invoice_id')
                        ->from('class_enrol ce')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id and epd.user_id=ce.user_id')
                        ->where('epd.att_status', 1)
                        ->where_in('ce.payment_status', array('PARTPAID', 'NOTPAID'))->where('ce.tenant_id', $tenant_id)->group_by('ei.invoice_id')->get()->result();
        $not_paid_arr = array();
        foreach ($not_paid as $row) {
            $not_paid_arr[] = $row->invoice_id;
        }
        $this->db->select('ei.invoice_id, ei.invoiced_on, ei.pymnt_due_id, ei.inv_date, ei.total_inv_amount, ei.total_inv_discnt, ei.total_inv_subsdy, 
                ei.total_gst,cm.company_name, cm.comp_regist_num, ce.payment_status, ce.enrolment_mode,
                tup.first_name, tup.last_name, tu.tax_code, ce.company_id, date(ccl.class_start_datetime) as course_date')
                ->from('class_enrol ce')
                ->join('course_class ccl', 'ccl.course_id=ce.course_id and ccl.class_id=ce.class_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('company_master cm', 'cm.company_id=ce.company_id', 'left')
                ->join('enrol_pymnt_due epd', 'epd.pymnt_due_id=ce.pymnt_due_id and epd.user_id=ce.user_id')
                ->where('epd.att_status', 1)
                ->where('ce.tenant_id', $tenant_id)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        if (!empty($company_id)) {
            $this->db->where('ce.company_id', $company_id);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d', strtotime($start_date));
            $this->db->where('date(ei.inv_date) >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d', strtotime($end_date));
            $this->db->where('date(ei.inv_date) <=', $end_date_label);
        }
        if ($field) {
            $this->db->order_by($field, $order_by);
        } else {
            $this->db->order_by('ei.inv_date', 'DESC');
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('ce.company_id', $this->user->company_id);
        }
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->join('course c', 'c.tenant_id = ccl.tenant_id AND c.course_id=ccl.course_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');
        }
        if (!empty($payment_status) && !empty($not_paid_arr)) {
            if ($payment_status == 'PAID') {
                $this->db->where_not_in('ei.invoice_id', $not_paid_arr);
            } elseif ($payment_status == 'NOINVD') {
                $this->db->where('ei.invoiced_on', NULL);
                $this->db->or_where('ei.invoiced_on', '0000-00-00 00:00:00');
            } elseif ($payment_status == 'INVD') {
                $this->db->where('ei.invoiced_on IS NOT NULL', NULL, FALSE);
                $this->db->where('ei.invoiced_on !=', '0000-00-00 00:00:00');
            } else {
                $this->db->where_in('ei.invoice_id', $not_paid_arr);
            }
        }
        if ($limit) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        $this->db->group_by('ei.invoice_id');
        return $this->db->get()->result_object();

        //$this->db->get();var_dump(realpath_cache_size());
        //echo $this->db->last_query();
        //echo memory_get_usage().'dd';exit;
    }

    /**
     * function to get count of all payment recd
     * @param type $company
     * @param type $invoice_id
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_payment_recd_count($company, $invoice_id, $start_date, $end_date) {
        $pymnt_due_id = $this->tenant_pymnt_due_id();
        $res = $this->db->select('count(*) as totalrow')
                ->from('enrol_paymnt_recd epr')
                ->join('enrol_invoice ei', 'ei.invoice_id=epr.invoice_id')
                ->where_in('ei.pymnt_due_id', $pymnt_due_id);
        if (!empty($company)) {
            $this->db->where('ei.company_id', $company);
        }

        if (!empty($invoice_id)) {
            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('epr.recd_on >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('epr.recd_on <=', $end_date_label);
        }
        //$this->db->get();
        //echo $this->db->last_query();
        //exit;
        return $this->db->get()->row()->totalrow;
    }

    /**
     * function to get count of all payment recd
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $company
     * @param type $invoice_id
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_payment_recd($limit, $offset, $field, $order_by, $company, $invoice_id, $start_date, $end_date) {
        $pymnt_due_id = $this->tenant_pymnt_due_id();
        $this->db->select('epr.invoice_id, ei.pymnt_due_id, ei.inv_date, ei.total_inv_amount, epr.recd_on, 
                ei.inv_type, ei.pymnt_due_id, ei.company_id, epr.amount_recd')
                ->from('enrol_paymnt_recd epr')
                ->join('enrol_invoice ei', 'ei.invoice_id=epr.invoice_id')
                ->where_in('ei.pymnt_due_id', $pymnt_due_id);

        if (!empty($company)) {
            $this->db->where('ei.company_id', $company);
        }

        if (!empty($invoice_id)) {
            $this->db->where('ei.invoice_id', $invoice_id);
        }

        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('epr.recd_on >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('epr.recd_on <=', $end_date_label);
        }
        if ($field) {
            $this->db->order_by($field, $order_by);
        } else {
            $this->db->order_by('epr.invoice_id', 'DESC');
            $this->db->order_by('epr.recd_on', 'DESC');
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        //$this->db->get(); echo $this->db->last_query();exit;
        return $this->db->get()->result();
    }

    /**
     * function to get company_trainee_details
     * @param type $pids
     * @return type
     */
    public function get_payment_recd_trainee_company($pids) {
        if (!empty($pids)) {
            $this->db->where_in('ce.pymnt_due_id', $pids);
        }
        $res = $this->db->select('ce.pymnt_due_id, cc.class_name, c.crse_name, tu.tax_code, tup.first_name, tup.last_name, cm.company_name, 
                cm.comp_regist_num, ce.company_id')
                        ->from('class_enrol ce')
                        ->join('course_class cc', 'cc.class_id=ce.class_id')
                        ->join('course c', 'c.course_id=ce.course_id')
                        ->join('tms_users tu', 'tu.user_id=ce.user_id')
                        ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                        ->join('company_master cm', 'cm.company_id=ce.company_id', 'left')
                        ->get()->result();
        //echo $this->db->last_query();exit;
        //print_r($res);exit;
        return $res;
    }

    /**
     * function to gt payment due
     * @param type $tenant_id
     * @param type $sales_id
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_payment_due_count($tenant_id, $sales_id, $start_date, $end_date) {

        $this->db->distinct('enrol.pymnt_due_id as pymnt_due_id');
        $this->db->select('inv.invoice_id, inv.inv_type, inv.total_inv_amount, inv.inv_date, inv.company_id,'
                . ' enrol.user_id, crse.crse_name, cls.class_name,cls.class_start_datetime,cls.class_end_datetime, comp.company_name, comp.comp_regist_num, usr.first_name,usr.last_name');
        $this->db->from('class_enrol enrol');
        $this->db->join('enrol_invoice inv', 'enrol.pymnt_due_id = inv.pymnt_due_id');
        $this->db->join('course crse', 'enrol.course_id = crse.course_id');
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id');
        $this->db->join('company_master comp', 'comp.company_id = enrol.company_id', 'left');
        $this->db->join('tms_users_pers usr', 'enrol.user_id = usr.user_id');
        $this->db->join('tms_users tu', 'tu.user_id=enrol.user_id');
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where_in('enrol.payment_status', array('NOTPAID', 'PARTPAID'));
        $this->db->group_by('inv.invoice_id');

        if (!empty($sales_id)) {
            $this->db->where('enrol.sales_executive_id', $sales_id);
        }
        /* for payment period
          //        if (!empty($start_date)) {
          //            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
          //            $this->db->where('inv.inv_date >=', $start_date_label);
          //        }
          //        if (!empty($end_date)) {
          //            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
          //            $this->db->where('inv.inv_date <=', $end_date_label);
          //        }
          end for payment period */
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('cls.class_start_datetime >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('cls.class_end_datetime <=', $end_date_label);
        }

        $res = $this->db->get();
        return $res->num_rows;
    }

    /**
     * function to gt payment due
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $sales_id
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_payment_due($tenant_id, $limit, $offset, $field, $order_by, $sales_id, $start_date, $end_date) {
        $this->db->distinct('enrol.pymnt_due_id as pymnt_due_id');
        $this->db->select('inv.invoice_id, inv.inv_type, inv.total_inv_amount, inv.inv_date, inv.company_id, enrol.user_id,'
                . ' crse.crse_name,cls.class_start_datetime,cls.class_end_datetime, '
                . 'cls.class_name, comp.company_name, comp.comp_regist_num, usr.first_name,usr.last_name, tu.tax_code');
        $this->db->from('class_enrol enrol');
        $this->db->join('enrol_invoice inv', 'enrol.pymnt_due_id = inv.pymnt_due_id');
        $this->db->join('course crse', 'enrol.course_id = crse.course_id');
        $this->db->join('course_class cls', 'enrol.class_id = cls.class_id and crse.course_id = cls.course_id');
        $this->db->join('company_master comp', 'comp.company_id = enrol.company_id', 'left');
        $this->db->join('tms_users_pers usr', 'enrol.user_id = usr.user_id');
        $this->db->join('tms_users tu', 'tu.user_id=enrol.user_id');
        $this->db->where('enrol.tenant_id', $tenant_id);
        $this->db->where_in('enrol.payment_status', array('NOTPAID', 'PARTPAID'));
        $this->db->group_by('inv.invoice_id');
        if (!empty($sales_id)) {
            $this->db->where('enrol.sales_executive_id', $sales_id);
        }
        /* start invoice payment
          if (!empty($start_date)) {
          $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
          $this->db->where('inv.inv_date >=', $start_date_label);
          }
          if (!empty($end_date)) {
          $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
          $this->db->where('inv.inv_date <=', $end_date_label);
          }
          if (!empty($limit)) {
          if ($limit == $offset) {
          $this->db->limit($offset);
          } else if ($limit > 0) {
          $limitvalue = $offset - $limit;
          $this->db->limit($limit, $limitvalue);
          }
          }
         * /* end invoice payment
         */
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('cls.class_start_datetime >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('cls.class_end_datetime <=', $end_date_label);
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        return $this->db->get()->result();
    }

    /**
     * function to gt payment recd total
     * @param type $invs
     * @return type
     */
    public function get_payment_recd_sum($invs) {
        $this->db->select_sum('amount_recd');
        $this->db->select('invoice_id');
        $this->db->from('enrol_paymnt_recd');
        if (!empty($invs)) {
            $this->db->where_in('invoice_id', $invs);
        }
        $this->db->group_by('invoice_id');
        return $this->db->get()->result();
    }

    /**
     * function to gt payment refund total
     * @param type $invs
     * @return string
     */
    public function get_payment_refund_sum($invs) {
        if (empty($invs)) {
            return '';
        }
        $this->db->select_sum('amount_refund');
        $this->db->select('invoice_id');
        $this->db->from('enrol_refund');
        $this->db->where_in('invoice_id', $invs);
        $this->db->group_by('invoice_id');
        return $this->db->get()->result();
    }

    /**
     * Get all invoice detail
     * @param type $tenant_id
     * @param type $limit
     * @param int $offset
     * @param type $field
     * @param type $order_by
     * @param type $payment_status
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_all_invoice_data($tenant_id, $limit, $offset, $field, $order_by, $payment_status, $start_date, $end_date) {

        $payment_status = $this->input->get('payment_status');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company_id = $this->input->get('company_id');
        if (empty($offset))
            $offset = 0;
        $not_paid = $this->db->select('ei.invoice_id')
                        ->from('class_enrol ce')
                        ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                        ->where_in('ce.payment_status', array('PARTPAID', 'NOTPAID'))->where('ce.tenant_id', $tenant_id)->group_by('ei.invoice_id')->get()->result();
        $not_paid_arr = array();
        foreach ($not_paid as $row) {
            $not_paid_arr[] = $row->invoice_id;
        }
        $this->db->select('ei.invoice_id, ei.invoiced_on, ei.pymnt_due_id, ei.inv_date, ei.total_inv_amount, ei.total_inv_discnt, ei.total_inv_subsdy, 
                ei.total_gst,cm.company_name, cm.comp_regist_num, ce.payment_status, ce.enrolment_mode,
                tup.first_name, tup.last_name, tu.tax_code, ce.company_id, date(ccl.class_start_datetime) as course_date')  // ce.company_id Added by dummy for internall staff enroll on 01 Dec 2014.
                ->from('class_enrol ce')
                ->join('course_class ccl', 'ccl.course_id=ce.course_id and ccl.class_id=ce.class_id')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->join('enrol_invoice ei', 'ei.pymnt_due_id=ce.pymnt_due_id')
                ->join('company_master cm', 'cm.company_id=ce.company_id', 'left')
                ->where('ce.tenant_id', $tenant_id)
                ->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        if (!empty($payment_status) && !empty($not_paid_arr)) {
            if ($payment_status == 'PAID') {
                $this->db->where_not_in('ei.invoice_id', $not_paid_arr);
            } elseif ($payment_status == 'NOINVD') {
                $this->db->where('ei.invoiced_on', NULL);
                $this->db->or_where('ei.invoiced_on', '0000-00-00 00:00:00');
            } elseif ($payment_status == 'INVD') {
                $this->db->where('ei.invoiced_on IS NOT NULL', NULL, FALSE);
                $this->db->where('ei.invoiced_on !=', '0000-00-00 00:00:00');
            } else {
                $this->db->where_in('ei.invoice_id', $not_paid_arr);
            }
        }
        if ($company_id) {
            $this->db->where('ce.company_id', $company_id);
        }
        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('ei.inv_date >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('ei.inv_date <=', $end_date_label);
        }
        if ($field) {
            $this->db->order_by($field, $order_by);
        } else {
            $this->db->order_by('ei.invoice_id', 'DESC');
        }
        if ($this->user->role_id == 'COMPACT') {
            $this->db->where('ce.company_id', $this->user->company_id);
        }
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->join('course c', 'c.tenant_id = ccl.tenant_id AND c.course_id=ccl.course_id '
                    . 'AND FIND_IN_SET(' . $this->user->user_id . ',c.crse_manager)');
        }
        $this->db->group_by('ei.invoice_id');
        return $sql = $this->db->get();
    }

    /**
     * function to get sales comm count
     * @param type $tenant_id
     * @param type $sales_exec
     * @return type
     */
    public function get_sales_comm_count($tenant_id, $sales_exec) {
        $this->db->select('scd.sales_exec_id');
        $this->db->from('sales_comm_due scd');
        $this->db->join('tms_users_pers tup', 'tup.user_id = scd.sales_exec_id');
        $this->db->where('scd.tenant_id', $tenant_id);
        if ($sales_exec) {
            $this->db->where('scd.sales_exec_id', $sales_exec);
        }
        $this->db->group_by('scd.sales_exec_id');
        return $this->db->get()->num_rows();
    }

    /**
     * This method gets the commission to be paid to a Sales Executive and the commission paid till date
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $sales_exec
     * @return type
     */
    public function get_sales_comm($type = 'data', $tenant_id, $limit, $offset, $field, $order_by, $sales_exec) {
        $this->db->select('scd.sales_exec_id, scd.pymnt_status, sum(scd.comm_amount) as comm_due_amount');
        $this->db->select('sum(scp.amount_paid) as comm_paid_amount');
        $this->db->select('tup.first_name,tup.last_name');
        $this->db->from('sales_comm_due scd');
        $this->db->join('tms_users_pers tup', 'tup.user_id = scd.sales_exec_id');
        $this->db->join('sales_comm_pymnt scp', 'scp.sales_exec_id = scd.sales_exec_id AND scp.pymnt_due_id = scd.pymnt_due_id', 'left');
        $this->db->where('scd.tenant_id', $tenant_id);
        if ($sales_exec) {
            $this->db->where('scd.sales_exec_id', $sales_exec);
        }
        if ($field) {
            $this->db->order_by($field, $order_by);
        } else {
            $this->db->order_by('scd.sales_exec_id', 'DESC');
        }
        if ($type != 'count') {
            if ($limit) {
                if ($limit == $offset) {
                    $this->db->limit($offset);
                } else if ($limit > 0) {
                    $limitvalue = $offset - $limit;
                    $this->db->limit($limit, $limitvalue);
                }
            }
        }
        $this->db->group_by('scd.sales_exec_id');
        $query_result = $this->db->get();

        if ($type == 'count') {
            return $query_result->num_rows();
        } else {
            return $query_result->result();
        }
    }

    /**
     * This method gets Non Selas Executive
     * @param $tenant_id
     */
    function get_non_sales_executive($tenant_id) {
        $this->db->distinct();
        $this->db->select("ce.sales_executive_id,pers.first_name,pers.last_name,GROUP_CONCAT(distinct iur.role_id) as roles");
        $this->db->from("class_enrol ce");
        $this->db->join("internal_user_role iur", "iur.tenant_id=ce.tenant_id and iur.user_id=ce.sales_executive_id");
        $this->db->join("tms_users_pers pers ", "pers.tenant_id=ce.tenant_id and pers.user_id=ce.sales_executive_id");
        $this->db->where("ce.tenant_id", $tenant_id);
        $this->db->join("tms_users tu", "tu.user_id=ce.sales_executive_id");
        $this->db->where("tu.account_status", "ACTIVE");
        // $this->db->where_not_in("iur.role_id",$role_id);
        $this->db->order_by("pers.first_name");
        $this->db->group_by('ce.sales_executive_id');
        if ($this->user->role_id == 'SLEXEC') {
            $this->db->where("pers.user_id", $this->data['user_id']->user_id);
        }
        $result = $this->db->get();
        $non_executive = array('' => 'Select');
        foreach ($result->result() as $item) {
            $arr = explode(',', $item->roles);
            if (in_array("SLEXEC", $arr)) {
                
            } else {
                $non_executive[$item->sales_executive_id] = $item->first_name;
            }
        }
        return $non_executive;
        //return $this->db->query($sql);
    }

    /**
     *  This method for calculating commision due period.
     * @param type $tenant_id
     * @param type $sales_exec
     * @return type
     */
    public function get_comm_due_period($tenant_id, $sales_exec) {
        $this->db->distinct();
        $this->db->select('scd.sales_exec_id, scd.comm_period_mth, scd.comm_period_yr');
        $this->db->from('sales_comm_due scd');
        $this->db->join('sales_comm_pymnt scp', 'scp.sales_exec_id != scd.sales_exec_id', 'left');
        $this->db->where('scd.tenant_id', $tenant_id);
        $this->db->where('scd.pymnt_status', 'NOTPAID');
        if ($sales_exec) {
            $this->db->where('scd.sales_exec_id', $sales_exec);
        }
        $query_result = $this->db->get();
        return $query_result->result();
    }

    /**
     * function to get total sales paid
     * @param type $sales_exec
     * @return type
     */
    public function get_total_paid_sales($sales_exec) {
        $this->db->select_sum('scp.amount_paid');
        $this->db->from('sales_comm_due scd');
        $this->db->join('sales_comm_pymnt scp', 'scp.pymnt_due_id=scd.pymnt_due_id', 'left');
        $this->db->where('scd.sales_exec_id', $sales_exec);

        return $this->db->get()->row()->amount_paid;
    }

    /**
     * function to get refund count
     * @param type $tenant_id
     * @param type $company
     * @param type $invoice_id
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_refund_paid_count($tenant_id, $company, $invoice_id, $start_date, $end_date) {
        $this->db->select('refnd.invoice_id,refnd.refund_on, refnd.refund_type, refnd.refnd_reason, 
                refnd.refnd_reason_ot, usr.first_name, cm.company_name, 
                cm.comp_regist_num');
        $this->db->from('enrol_refund refnd');
        $this->db->join('tms_users_pers usr', 'refnd.refund_by = usr.user_id');
        $this->db->join('enrol_invoice inv', 'refnd.invoice_id = inv.invoice_id');
        $this->db->join('company_master cm', 'cm.company_id=inv.company_id', 'left');
        $this->db->order_by('refnd.invoice_id, refnd.refund_on');
        $this->db->where('usr.tenant_id', $tenant_id);
        if (!empty($company)) {
            $this->db->where('inv.company_id', $company);
        }

        if (!empty($invoice_id)) {
            $this->db->where('inv.invoice_id', $invoice_id);
        }

        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('refnd.refund_on >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('refnd.refund_on <=', $end_date_label);
        }
        return $this->db->get()->num_rows;
    }

    /**
     * function to get refund paid
     * @param type $tenant_id
     * @param type $limit
     * @param type $offset
     * @param type $field
     * @param type $order_by
     * @param type $company
     * @param type $invoice_id
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function get_refund_paid($tenant_id, $limit, $offset, $field, $order_by, $company, $invoice_id, $start_date, $end_date) {
        $this->db->select('refnd.invoice_id, refnd.refund_on, refnd.refund_type, refnd.refnd_reason, 
                refnd.refnd_reason_ot, usr.first_name,usr.last_name, cm.company_name, refnd.amount_refund,
                cm.comp_regist_num, inv.company_id');
        $this->db->from('enrol_refund refnd');
        $this->db->join('tms_users_pers usr', 'refnd.refund_by = usr.user_id');
        $this->db->join('enrol_invoice inv', 'refnd.invoice_id = inv.invoice_id');
        $this->db->join('company_master cm', 'cm.company_id=inv.company_id', 'left');


        $this->db->where('usr.tenant_id', $tenant_id);

        if (!empty($company)) {
            $this->db->where('inv.company_id', $company);
        }

        if (!empty($invoice_id)) {
            $this->db->where('inv.invoice_id', $invoice_id);
        }

        if (!empty($start_date)) {
            $start_date_label = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('refnd.refund_on >=', $start_date_label);
        }
        if (!empty($end_date)) {
            $end_date_label = date('Y-m-d 23:59:59', strtotime($end_date));
            $this->db->where('refnd.refund_on <=', $end_date_label);
        }
        if ($field) {
            $this->db->order_by($field, $order_by);
        } else {
            $this->db->order_by('refnd.invoice_id', 'DESC');
            $this->db->order_by('refnd.refund_on', 'DESC');
        }
        if (!empty($limit)) {
            if ($limit == $offset) {
                $this->db->limit($offset);
            } else if ($limit > 0) {
                $limitvalue = $offset - $limit;
                $this->db->limit($limit, $limitvalue);
            }
        }
        return $this->db->get()->result();
    }

    /**
     * function for subquery  in refund_paid report
     * @param type $invs
     * @return string
     */
    public function get_refund_paid_user($invs) {
        if (empty($invs)) {
            return '';
        }
        $this->db->distinct('brk.user_id');
        $this->db->select('brk.invoice_id, usr.first_name,usr.last_name, tu.tax_code');
        $this->db->from('enrol_refund_brkup_dt brk');
        $this->db->join('tms_users_pers usr', 'brk.user_id = usr.user_id');
        $this->db->join('tms_users tu', 'tu.user_id = usr.user_id');
        $this->db->where_in('brk.invoice_id', $invs);
        $this->db->order_by('brk.invoice_id, brk.refund_date');
        return $this->db->get()->result();
    }

    /**
     * function to get class courses
     * @param type $tenant_id
     * @return type
     */
    public function get_class_courses($tenant_id) {
        $this->db->select("c.course_id, c.crse_name, c.crse_type, c.class_type, c.language, c.pre_requisite, c.certi_level, c.crse_manager");
        $this->db->from('class_enrol ce');
        $this->db->join("course c", "c.course_id = ce.course_id");
        $this->db->where("ce.tenant_id", $tenant_id);
        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('c.crse_status', 'ACTIVE');
        $this->db->order_by("c.crse_name");
        $this->db->group_by("c.course_id");
        $result = $this->db->get();

        $courses = $result->result();
        $tenant_courses = array();


        if ($this->user->role_id == 'CRSEMGR') {
            $logged_in_user_id = $this->user->user_id;
            foreach ($courses as $item) {
                $crse_manager_arr = explode(',', $item->crse_manager);
                if (in_array($logged_in_user_id, $crse_manager_arr)) {
                    $tenant_courses[$item->course_id] = $item->crse_name;
                }
            }
        } else {
            foreach ($courses as $item) {
                $tenant_courses[$item->course_id] = $item->crse_name;
            }
        }

        return $tenant_courses;
    }

    /**
     * Gets the payment due id from Class_Enrol for enrolments which have not been re-scheduled for a tenant
     * @return int
     */
    private function tenant_pymnt_due_id() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id; ///////modofied by shubhranshu
        $pymnt = $this->db->select('pymnt_due_id')->from('class_enrol')->where('enrol_status !=', 'RESHLD')->where('tenant_id', $tenant_id)->get()->result_array();
        $pymnt_arr = array();
        foreach ($pymnt as $row) {
            $pymnt_arr[] = $row['pymnt_due_id'];
        }
        if (empty($pymnt_arr)) {
            $pymnt_arr = array(0);
        }
        return $pymnt_arr;
    }

    /**
     * This method gets the trainee name etc. deatils for the feedback PDF
     * @param type $tenantid
     * @param type $user_id
     * @return type
     */
    public function get_trainee_details($tenantid, $user_id) {
        $this->db->select('usr.tax_code,  pers.first_name, pers.last_name');
        $this->db->from('tms_users usr');
        $this->db->join('tms_users_pers pers', 'usr.tenant_id = pers.tenant_id '
                . 'AND usr.user_id = pers.user_id');
        $this->db->where('usr.user_id', $user_id);
        $this->db->where('usr.tenant_id', $tenantid);
        return $this->db->get()->row();
    }

    /**
     * Get the SOA Report data
     * @param type $tenant
     * @param type $course
     * @param type $class
     * @param type $from
     * @param type $to
     * @return type
     */
    public function get_soa_data($tenant, $course, $class, $from, $to) {
        $generateby = $this->input->post('generateby');
        $cur_date = date('Y-m-d');
        if(TENANT_ID=='T02' || TENANT_ID=='T24'){/// added by shubhranshu due to client requirentment for xp course run id
            $this->db->select('ce.training_score, ce.class_id, ce.user_id,ce.class_id,cc.class_name,cc.tpg_course_run_id');
        }else{
            $this->db->select('ce.training_score, ce.class_id, ce.user_id,ce.class_id,cc.class_name');
        }
        
        $this->db->select('c.reference_num, c.competency_code, c.certi_level,c.crse_manager');
        $this->db->select('cc.class_language, cc.classroom_trainer, cc.assessor, cc.class_start_datetime, cc.class_end_datetime');
        $this->db->select('tu.tax_code_type, tu.tax_code, tu.other_identi_type, tu.other_identi_code, tu.registered_email_id, tu.account_type'); // account_type added by dummy for internal staff enrollment On 08 Dec 2014.
        $this->db->select('tup.first_name, tup.gender, tup.nationality, tup.dob, tup.race, tup.occupation_code, emp.designation');
        $this->db->select('tup.highest_educ_level, tup.salary_range, tup.contact_number, tup.alternate_contact_number');
        $this->db->select('cm.business_type, cm.comp_regist_num, cm.company_name, ce.company_id, cm.comp_email'); // company_id added by dummy for internal staff enrollment On 29 Nov 2014.

        $this->db->from('class_enrol ce');
        $this->db->join('course c', 'ce.tenant_id=c.tenant_id and ce.course_id=c.course_id');
        $this->db->join('course_class cc', 'ce.tenant_id=cc.tenant_id and ce.course_id=cc.course_id and ce.class_id=cc.class_id');
        $this->db->join('tms_users tu', 'ce.tenant_id=tu.tenant_id and ce.user_id=tu.user_id');
        $this->db->join('tms_users_pers tup', 'ce.tenant_id=tup.tenant_id and ce.user_id = tup.user_id');
        $this->db->join('company_master cm', 'ce.company_id=cm.company_id', 'left');
        $this->db->join('internal_user_emp_detail emp', 'emp.user_id=tu.user_id AND emp.tenant_id=tu.tenant_id', 'left');
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }
        $this->db->where('ce.tenant_id', $tenant);
        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        $this->db->where('date(cc.class_end_datetime) <', $cur_date);
        if ($generateby == 1) {
            if (!empty($course)) {
                $this->db->where('ce.course_id', $course);
            }
            if (!empty($class)) {
                $this->db->where('ce.class_id', $class);
            }
        } elseif ($generateby == 2) {
            if (!empty($from)) {
                $from_label = date('Y-m-d', strtotime($from));
                $this->db->where('date(cc.class_end_datetime) >=', $from_label);
            }
            if (!empty($to)) {
                $to_label = date('Y-m-d', strtotime($to));
                $this->db->where('date(cc.class_end_datetime) <=', $to_label);
            }
        }

        return $this->db->get()->result();
    }

    /**
     * Get the TRAQOM Report data
     * @param type $tenant
     * @param type $course
     * @param type $class
     * @param type $from
     * @param type $to
     * @return type
     */
    public function get_traqom_data($tenant, $course, $class, $start_date1, $start_date2) {
        $generateby = $this->input->post('generateby');
        $cur_date = date('Y-m-d');
        if (!empty($start_date1)) {
            $date = strtotime("+3 day", strtotime($start_date1));
            $days3 = date('Y-m-d', $date);
            $traqom_date = $days3;
        } else if (!empty($start_date2)) {
            $traqom_date = date('Y-m-d', strtotime($start_date2));
        }
        // add 3 days to date
        if(TENANT_ID == 'T02' || TENANT_ID=='T24'){
            $this->db->select('cc.class_language, cc.class_name, cc.classroom_trainer, cc.assessor, cc.class_start_datetime, cc.class_end_datetime,cc.tpg_course_run_id');
        }else{
            $this->db->select('cc.class_language, cc.class_name, cc.classroom_trainer, cc.assessor, cc.class_start_datetime, cc.class_end_datetime');
        }
        $this->db->select('c.reference_num, c.competency_code, c.certi_level,c.crse_manager');
        
        $this->db->select('tu.tax_code_type, tu.tax_code, tu.other_identi_type, tu.other_identi_code, tu.registered_email_id, tu.account_type'); // account_type added by dummy for internal staff enrollment On 08 Dec 2014.
        $this->db->select('tup.first_name,  tup.nationality, tup.dob, tup.race, tup.occupation_code, emp.designation');
        $this->db->select('tup.highest_educ_level, tup.salary_range, tup.contact_number, tup.alternate_contact_number');
        $this->db->select('cm.business_type, cm.comp_regist_num, cm.company_name, ce.company_id, cm.comp_email'); // company_id added by dummy for internal staff enrollment On 29 Nov 2014.
        $this->db->select('tm.comp_reg_no,tm.tenant_name');

        $this->db->from('class_enrol ce');
        $this->db->join('course c', 'ce.tenant_id=c.tenant_id and ce.course_id=c.course_id');
        $this->db->join('course_class cc', 'ce.tenant_id=cc.tenant_id and ce.course_id=cc.course_id and ce.class_id=cc.class_id');
        $this->db->join('tms_users tu', 'ce.tenant_id=tu.tenant_id and ce.user_id=tu.user_id');
        $this->db->join('tms_users_pers tup', 'ce.tenant_id=tup.tenant_id and ce.user_id = tup.user_id');
        $this->db->join('company_master cm', 'ce.company_id=cm.company_id', 'left');
        $this->db->join('internal_user_emp_detail emp', 'emp.user_id=tu.user_id AND emp.tenant_id=tu.tenant_id', 'left');
        $this->db->join('tenant_master tm', 'ce.tenant_id=tm.tenant_id', 'left');
        if ($this->user->role_id == 'CRSEMGR') {
            $this->db->where("FIND_IN_SET(" . $this->user->user_id . ",c.crse_manager) !=", 0);
        }
        $this->db->where('ce.tenant_id', $tenant);
        $this->db->where_in('ce.enrol_status', array('ENRLACT', 'ENRLBKD'));
        //$this->db->where('date(cc.class_end_datetime) <', $cur_date);
        //$this->db->where('date(cc.class_end_datetime) ', $traqom_date);
        if ($generateby == 1) {
            $this->db->where('date(cc.class_end_datetime)', $traqom_date);
        } elseif ($generateby == 2) {
            if(TENANT_ID=='T02'){
                $ar = array('C','NYC');
                $this->db->where_in('ce.training_score', $ar);
            }else{
                $this->db->where('ce.training_score', 'C');
            }
           
            $this->db->where('date(cc.class_end_datetime)', $traqom_date);
        }
        return $this->db->get()->result();
        
    }

    /**
     * This method is used by the SOA report to get the assessment details
     * @param type $class_id
     * @param type $user_id
     * @return type
     */
    public function get_assessment_details($class_id, $user_id) {
        $result = $this->db->select('assmnt_date, assmnt_venue, assmnt_venue_oth, assessor_id')->from('class_assmnt_schld')
                ->where('class_id', $class_id)
                ->where('assmnt_type', 'DEFAULT')
                ->get();
        if ($result->num_rows() == 1) {
            return $result->row();
        } else {
            $result = $this->db->select('cas.assmnt_date, cas.assmnt_venue, cas.assmnt_venue_oth, cas.assessor_id')
                            ->from('class_assmnt_trainee cat')
                            ->join('class_assmnt_schld cas', 'cas.assmnt_id=cat.assmnt_id and cas.class_id=cat.class_id')
                            ->where('cat.class_id', $class_id)
                            ->where('cat.user_id', $user_id)
                            ->where('cas.assmnt_type', 'CUSTOM')->get()->row();
            return $result;
        }
    }

    /**
     * This method is used by the SOA report to get the taxcodes of the Trainee.
     * @param type $ids
     * @return type
     */
    public function get_user_taxcode($ids) {
        if (!empty($ids)) {
            $this->db->select('tax_code');
            $this->db->from('tms_users');
            $this->db->where_in('user_id', $ids);
            return $this->db->get()->result();
        }
        return;
    }

    /**
     * This method used for generating trainee feedback form.
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @return type
     */
    public function get_trainee_feedback($tenant_id, $course_id, $class_id) {
        $this->db->select('mv.category_name, mv.parameter_id');
        $this->db->from('metadata_values mv');
        $this->db->like('mv.category_id', 'CAT32_01', 'after');
        $query = $this->db->get();
        $result = $query->result_array();
        $grouped_by_question = array();
        foreach ($result as $res) {
            $grouped_by_question[$res['parameter_id']] = $res;
        }
        return $grouped_by_question;
    }

    /**
     * This method used for saving trainee feedback data.
     * @param type $tenant_id
     * @param type $class_id
     * @param type $course_id
     * @param type $trainee_user_id
     * @return type
     */
    public function save_feedback($tenant_id, $class_id, $course_id, $trainee_user_id) {
        $q1 = $this->input->post('Q01');
        $q2 = $this->input->post('Q02');
        $q3 = $this->input->post('Q03');
        $q4 = $this->input->post('Q04');
        $q5 = $this->input->post('Q05');
        $q6 = $this->input->post('Q06');
        $q7 = $this->input->post('Q07');
        $q8 = $this->input->post('Q08');
        $q9 = $this->input->post('Q09');
        $q10 = $this->input->post('Q10');
        $q11 = $this->input->post('Q11');
        $q12 = $this->input->post('Q12');
        $q13 = $this->input->post('Q13');
        $q14 = $this->input->post('Q14');
        $q15 = $this->input->post('Q15');
        $que_ans = array(
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q01',
                'feedback_answer' => $q1),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q02',
                'feedback_answer' => $q2),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q03',
                'feedback_answer' => $q3),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q04',
                'feedback_answer' => $q4),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q05',
                'feedback_answer' => $q5),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q06',
                'feedback_answer' => $q6),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q07',
                'feedback_answer' => $q7),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q08',
                'feedback_answer' => $q8),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q09',
                'feedback_answer' => $q9),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q10',
                'feedback_answer' => $q10),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q11',
                'feedback_answer' => $q11),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q12',
                'feedback_answer' => $q12),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q13',
                'feedback_answer' => $q13),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q14',
                'feedback_answer' => $q14),
            array(
                'tenant_id' => $tenant_id,
                'user_id' => $trainee_user_id,
                'course_id' => $course_id,
                'class_id' => $class_id,
                'feedback_question_id' => 'Q15',
                'feedback_answer' => $q15)
        );
        $this->db->where('user_id', $trainee_user_id);
        $this->db->where('class_id', $class_id);
        $this->db->delete('trainee_feedback');

        $this->db->insert_batch('trainee_feedback', $que_ans);
        $rating = $this->input->post('rating');
        $remarks = $this->input->post('remarks');
        $data = array(
            'trainee_fdbck_on' => date('Y-m-d H:i:s'),
            'trainee_feedback_rating' => $rating,
            'other_remarks_trainee' => strtoupper($remarks)
        );
        $where = array(
            'tenant_id' => $tenant_id,
            'user_id' => $trainee_user_id,
            'course_id' => $course_id,
            'class_id' => $class_id
        );
        $this->db->where($where);
        $result = $this->db->update('class_enrol', $data);
        return $result;
    }

    /**
     * Used for trainee remarks
     * @param type $tenant_id
     * @param type $course_id
     * @param type $class_id
     * @param type $user_id
     * @return type
     */
    public function get_trainee_remarks($tenant_id, $course_id, $class_id, $user_id) {
        $trainee_feedback = $this->db->select('trainee_feedback_rating, other_remarks_trainee')->from('class_enrol')
                        ->where('class_id', $class_id)
                        ->where('course_id', $course_id)
                        ->where('user_id', $user_id)
                        ->where('tenant_id', $tenant_id)
                        ->get()->row();
        return $trainee_feedback;
    }

    /**
     * Function to get monthly enrollment count
     * @param type $year
     * @return type
     */
    public function get_monthly_enrollment_count($year) {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $this->db->select('MONTHNAME(enrolled_on) AS month, count(*) AS count')->from('class_enrol')
                ->where('YEAR(enrolled_on)', $year)->where('tenant_id', $tenant_id)->group_by('MONTH(enrolled_on)')
                ->order_by('MONTH(enrolled_on)', 'DESC');
        return $this->db->get();
    }

    /**
     * Function to get monthly attendance count
     * @param type $year
     * @return type
     */
    public function get_monthly_attendance_count($year) {
        $attendance_where = "(ca.session_01 = 1 OR ca.session_02 = 1)";
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $this->db->select('MONTHNAME(ca.class_attdn_date) AS month, count(*) AS count,epd.att_status as att_status');
        $this->db->from('class_attendance ca');

        $this->db->join('class_enrol ce', 'ce.class_id = ca.class_id and ce.user_id=ca.user_id');
        $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id=ca.user_id');

        $this->db->where('YEAR(ca.class_attdn_date)', $year);
        $this->db->where('ca.tenant_id', $tenant_id);
        $this->db->where('epd.att_status', 1);
        $this->db->where($attendance_where);
        $this->db->group_by('MONTH(ca.class_attdn_date)');
        $this->db->order_by('MONTH(ca.class_attdn_date)', 'DESC');

        return $this->db->get();
    }

    /**
     * This method gets the invoice audit trail data based on the search condition
     * CREATED BY SHUBHRANSHU ON 28/05/2019
     */
    public function get_invoice_audit_trail($tenant_id, $limit, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $invoice_id = 0, $companyId) {

        //Call stored procedure to get linked invoice list
        //$sql = "call tms.procGetLinkedInvoice('T02', 'XPR4816', '', '', '', 10, 0, null, null)";
        //$sql = "call {$this->db->database}.procGetLinkedInvoice(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; commented by shubhranshu

        $sql_start_date = null;
        if (!empty($start_date)) {
            $sql_start_date = date('Y-m-d', strtotime($start_date));
        }

        $sql_end_date = null;
        if (!empty($end_date)) {
            $sql_end_date = date('Y-m-d', strtotime($end_date));
        }

        //$parameters = array($tenant_id,$invoice_id,$companyId,$sql_start_date,$sql_end_date,$payment_status,$limit,$offset,$field,$order_by);
        //$query = $this->db->query($sql, $parameters);

        $audit_trail_data = array();
        ///////////////////////////
        $this->db->select('eia.*,ce.user_id,ce.tenant_id,ce.payment_status,ce.enrolment_mode,cm.company_name,cm.comp_regist_num');
        $this->db->from('enrol_invoice_audittrail eia');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id=eia.pymnt_due_id');
        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id', 'left');
        $this->db->join('tms_users_pers tup', 'tup.user_id=ce.user_id', 'left');
        $this->db->join('company_master cm', 'cm.company_id=eia.company_id', 'left');

        $this->db->where('ce.tenant_id', $tenant_id);
        if ($invoice_id) {
            $this->db->where('eia.invoice_id', $invoice_id);
        }
        if ($companyId) {
            $this->db->where('eia.company_id', $companyId);
        }
        if ($sql_start_date && $sql_end_date) {
            $this->db->where('eia.inv_date >=', $sql_start_date);
            $this->db->where('eia.inv_date <=', $sql_end_date);
        }
        if ($order_by && $field) {
            $this->db->order_by('eia.' . $field, $order_by);
        }
        $this->db->group_by('eia.invoice_id');
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        //echo $this->db->get_compiled_select();exit;
        $audit_trail_data = $this->db->get()->result_object();
        //print_r($audit_trail_data);exit;
        ///////////////////////////
        return $audit_trail_data;
    }

    //// added by shubhranshu
    public function get_invoice_audit_trail_rows($tenant_id, $limit, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $invoice_id = 0, $companyId) {
        //Call stored procedure to get linked invoice list
        //$sql = "call tms.procGetLinkedInvoice('T02', 'XPR4816', '', '', '', 10, 0, null, null)";
        //$sql = "call {$this->db->database}.procGetLinkedInvoice(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; commented by shubhranshu
        $sql_start_date = null;
        if (!empty($start_date)) {
            $sql_start_date = date('Y-m-d', strtotime($start_date));
        }

        $sql_end_date = null;
        if (!empty($end_date)) {
            $sql_end_date = date('Y-m-d', strtotime($end_date));
        }
        //$parameters = array($tenant_id,$invoice_id,$companyId,$sql_start_date,$sql_end_date,$payment_status,$limit,$offset,$field,$order_by);
        //$query = $this->db->query($sql, $parameters);
        $audit_trail_data = array();
        ///////////////////////////
        $this->db->select('eia.invoice_id');
        $this->db->from('enrol_invoice_audittrail eia');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id=eia.pymnt_due_id');
        $this->db->join('tms_users tu', 'tu.user_id=ce.user_id', 'left');
        $this->db->join('tms_users_pers tup', 'tup.user_id=ce.user_id', 'left');
        $this->db->join('company_master cm', 'cm.company_id=eia.company_id', 'left');

        $this->db->where('ce.tenant_id', $tenant_id);
        if ($invoice_id) {
            $this->db->where('eia.invoice_id', $invoice_id);
        }
        if ($companyId) {
            $this->db->where('eia.company_id', $companyId);
        }
        if ($sql_start_date && $sql_end_date) {
            $this->db->where('eia.inv_date >=', $sql_start_date);
            $this->db->where('eia.inv_date <=', $sql_end_date);
        }
        if ($order_by && $field) {
            $this->db->order_by('eia.' . $field, $order_by);
        }
        $this->db->group_by('eia.invoice_id');

        //echo $this->db->get_compiled_select();exit;
        $rows = $this->db->get()->num_rows();
        //print_r($audit_trail_data);exit;
        ///////////////////////////
        return $rows;
    }

    //add by pritam
    public function update_coll_on($tenant_id, $course, $class, $cert_col_on, $user_id) {
        $cert_col_on = (empty($cert_col_on)) ? NULL : date('Y-m-d', strtotime($cert_col_on));
        $this->db->where("tenant_id", $tenant_id);
        $this->db->where("course_id", $course);
        $this->db->where("class_id", $class);
        $this->db->where("user_id", $user_id);
        $audit_data = array("certificate_coll_on" => $cert_col_on);
        $this->db->trans_start();
        $this->db->update('class_enrol', $audit_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $status = FALSE;
        }
        return $status;
    }

    //end 
    /*

     * function created to get trainee name

     */

    public function get_trainee_name($invoice_id, $taxcode_id, $trainee_id, $tenant_id) {
        $company_post = $this->input->get('off_company_name');

        if ($company_post) {
            $company_id = $company_post;
        }


        $this->db->select('tup.first_name as first, tup.last_name as last, tup.gender, tu.tax_code')
                ->from('class_enrol ce')
                ->join('tms_users tu', 'tu.user_id=ce.user_id')
                ->join('tms_users_pers tup', 'tup.user_id=tu.user_id')
                ->where('ce.tenant_id', $tenant_id);
        if (!empty($company_id)) {
            echo $company_id;
            $this->db->join('tenant_company_users com', 'tu.tenant_id=com.tenant_id and tu.user_id=com.user_id');
            $this->db->join('company_master com_mst', 'com.company_id=com_mst.company_id');
            $this->db->where('com.company_id', $company_id);
            $this->db->where('com_mst.company_status', 'ACTIVE');
        }
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

    //add by pritam
    public function trainee_getAutoCompleteTrainee_List($trainee, $tenantID, $courseID = 0, $classID = 0, $comp_id) {
        //  $this->output->enable_profiler(TRUE);
        $taxcode = trim($taxcode);

        $matches = array();
        $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code, ce.course_id, ce.class_id,ce.company_id');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.user_id=pers.user_id and usr.tenant_id=pers.tenant_id');

        $this->db->join('class_enrol ce', 'usr.user_id=ce.user_id and usr.tenant_id=ce.tenant_id');

        $this->db->where('usr.tenant_id', $tenantID);
        $this->db->where('ce.company_id', $comp_id);

        if ((int) $courseID > 0) {

            $this->db->where("ce.course_id", $courseID);
        }

        if ((int) $classID > 0) {

            $this->db->where("ce.class_id", $classID);
        }

        //$this->db->like('usr.tax_code', $taxcode, 'both'); commented by shubhranshu

        $this->db->like('pers.first_name', $trainee, 'both'); /////added by shubhranshu since we are searching for trainee name

        $this->db->group_by('ce.user_id');

        $this->db->limit(200);

        $results = $this->db->get()->result();



        return $results;
    }

    public function all_invoice($invoice_id, $tenantID) {
        $this->db->select('invoice_id');
        $this->db->from('enrol_invoice');
        $this->db->like('invoice_id', $invoice_id);
        $sql = $this->db->get();
        return $sql->result();
    }

    /////added by shubhranshu///
    public function all_invoices($invoice_id, $tenantID) {
        $this->db->select('ei.invoice_id');
        $this->db->from('enrol_invoice ei');
        $this->db->join('class_enrol ce', 'ce.pymnt_due_id = ei.pymnt_due_id');
        $this->db->like('ei.invoice_id', $invoice_id);
        $this->db->where('ce.tenant_id', $tenantID);
        $sql = $this->db->get();
        return $sql->result();
    }

/////added by shubhranshu///
    //end
    //added by pritm
    public function trainee_getAutoCompleteTaxcodeList($taxcode, $tenantID, $courseID = 0, $classID = 0, $comp_id) {

        $taxcode = trim($taxcode);

        $matches = array();



        $this->db->select('pers.user_id, usr.user_name, pers.first_name, pers.last_name, usr.tax_code, ce.course_id, ce.class_id');

        $this->db->from('tms_users usr');

        $this->db->join('tms_users_pers pers', 'usr.user_id=pers.user_id and usr.tenant_id=pers.tenant_id');

        $this->db->join('class_enrol ce', 'usr.user_id=ce.user_id and usr.tenant_id=ce.tenant_id');

        $this->db->where('usr.tenant_id', $tenantID);
        $this->db->where('ce.company_id', $comp_id);

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

    /* skm */

    public function get_all_tenant() {
        $this->db->select('tenant_id,tenant_name');
        $this->db->from('tenant_master');
        $query = $this->db->get();
//        echo $this->db->last_query();
        return $query;
    }

    public function get_all_records() {
        $this->db->select('COUNT(ce.user_id) as total,ce.tenant_id,tm.tenant_name');
        $this->db->from('class_enrol ce');
        $this->db->join('tenant_master tm', 'tm.tenant_id = ce.tenant_id');
        $this->db->group_by('tenant_id');
        $query = $this->db->get();
//        echo $this->db->last_query();
//        return $query->result();
        return $query;
    }

    public function get_monthly_tenant_enrollment_count($tenant_id, $fyear, $fmonth, $lyear, $lmonth) {

        if ($fmonth != '' && $fyear != '' && $lmonth == '' && $lyear == '') {
            $this->db->select('MONTHNAME(ce.enrolled_on) AS month,YEAR(ce.enrolled_on) AS year,tm.tenant_name, count(*) AS count');
            $this->db->from('class_enrol ce');
            $this->db->join('tenant_master tm', 'tm.tenant_id = ce.tenant_id');
            $this->db->like('ce.enrolled_on', $fyear . '-' . $fmonth . '-', 'after');
            $this->db->where('ce.tenant_id', $tenant_id);
            $this->db->group_by(array('MONTH(ce.enrolled_on)', 'ce.tenant_id'));
            $this->db->order_by('MONTH(ce.enrolled_on)', 'DESC');
            $qry = $this->db->get();
//            echo $this->db->last_query();
            return $qry;
        }

        if ($fmonth != '' && $fyear != '' && $lmonth != '' && $lyear != '') {
            $start_month_date = $fyear . '-' . $fmonth . '-01';
            $last_month_date = $lyear . '-' . $lmonth . '-01';
            $last_month_date = date("Y-m-t", strtotime($last_month_date));


//            $qry = "SELECT MONTHNAME(enrolled_on) AS month, YEAR(enrolled_on) AS year count(*) AS count FROM (`class_enrol`) WHERE `tenant_id` = '".$tenant_id."' AND
//DATE(enrolled_on)>= '".$start_month_date."' and DATE(enrolled_on) <= '".$last_month_date."' 
//GROUP BY MONTH(enrolled_on), YEAR(enrolled_on) ORDER BY MONTH(enrolled_on) DESC";
//            $qry = "SELECT MONTHNAME(enrolled_on) AS month,YEAR(enrolled_on) AS year, count(*) AS count FROM (`class_enrol`)"
//                    . " WHERE `tenant_id` = '".$tenant_id."' AND DATE(enrolled_on)>= '".$start_month_date."' and DATE(enrolled_on) <= '".$last_month_date."' "
//                    . "GROUP BY MONTH(enrolled_on),YEAR(enrolled_on) ORDER BY YEAR(enrolled_on) DESC";

            $qry = "SELECT tm.tenant_name,MONTHNAME(ce.enrolled_on) AS month,YEAR(ce.enrolled_on) AS year,DATE_FORMAT(ce.enrolled_on,'%Y%m') AS date, count(*) AS count FROM class_enrol ce"
                    . " LEFT JOIN tenant_master tm on tm.tenant_id = ce.tenant_id "
                    . " WHERE ce.tenant_id = 'T01' AND DATE(ce.enrolled_on)>= '" . $start_month_date . "' and DATE(ce.enrolled_on) <= '" . $last_month_date . "'"
                    . " GROUP BY MONTH(ce.enrolled_on),YEAR(ce.enrolled_on) ORDER BY MONTH(ce.enrolled_on)  DESC";

//    echo $qry;
            return $this->db->query($qry);
        }
    }

    public function get_archive_trainee($tenant_id, $course_id, $class_id, $subsidy) {
        $this->db->select('*');
        $this->db->from('class_attendance_archive');
        $this->db->where('tenant_id', $tenant_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_id', $class_id);
        if ($subsidy == 'ws') {
            $this->db->where("subsidy_amount >", 0);
        } else if ($subsidy == 'wts') {
            $this->db->where('subsidy_amount', 0);
            $this->db->where('taxcode_type', 'SNG_1');
        } else if ($subsidy == 'fr') {
            $taxcode = 'SNG_3 || SNG_2';
            $this->db->where('taxcode_type', $taxcode);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function tms_unpaid_report($tenant_id, $payment_status, $year, $month, $training_score) {
        $start_date = $year . '-' . $month . '-01';
        $end_date = $year . '-' . $month . '-31';

        $query = "SELECT 
            tu.tax_code,
            ei.invoice_id,
            tup.first_name as name,
            cm.company_name,
            due.class_fees,
            ceil((due.class_fees * due.discount_rate))/ 100 as discount_rate,
            due.gst_amount,
            ce.tg_number,
            due.subsidy_amount,
            due.total_amount_due,
            ce.payment_status,
            ce.enrolment_mode,
            cc.class_start_datetime,
            cc.class_end_datetime,
            cc.class_name,
            ce.training_score,
            due.att_status
           
                    FROM ( course_class cc) 
                    JOIN course c ON c.course_id = cc.course_id 
                    JOIN class_enrol ce ON ce.class_id = cc.class_id 
                    JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
                    join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
                    JOIN tms_users tu ON tu.user_id = ce.user_id 
                    left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
                    left join company_master cm on cm.company_id=ce.company_id
                    WHERE cc . tenant_id = '" . $tenant_id . "' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT') 
                    AND ce.training_score in ('" . $training_score . "')
                    AND ce.payment_status in ('" . $payment_status . "')
                    AND date(cc.class_end_datetime)>= '" . $start_date . "' and date(cc.class_end_datetime) <= '" . $end_date . "'";

        $result = $this->db->query($query)->result();

        return $result;
    }

    public function tms_paid_report($tenant_id, $payment_status, $year, $month, $training_score) {
        $start_date = $year . '-' . $month . '-01';
        $end_date = $year . '-' . $month . '-31';

        $query = "SELECT 
            tu.tax_code,
            tu.user_id,
            ei.invoice_id,
            tup.first_name as name,
            cm.company_name,
            due.class_fees,
            ceil((due.class_fees * due.discount_rate))/ 100 as discount_rate,
            due.gst_amount,
            ce.tg_number,
            due.subsidy_amount,
            ce.payment_status,
            ce.enrolment_mode,
            epr.mode_of_pymnt,
            cc.class_start_datetime,
            cc.class_end_datetime,
            cc.class_name,
            ce.training_score,
            due.att_status
                    FROM ( course_class cc) 
                    JOIN course c ON c.course_id = cc.course_id 
                    JOIN class_enrol ce ON ce.class_id = cc.class_id 
                    JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
                    join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
                    JOIN tms_users tu ON tu.user_id = ce.user_id 
                    left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
                    left join company_master cm on cm.company_id=ce.company_id
                    JOIN(SELECT ttt.*
                        FROM enrol_paymnt_recd ttt
                        JOIN
                        (SELECT `invoice_id`, MAX(`trigger_date`) AS Maxdate FROM enrol_paymnt_recd GROUP BY invoice_id) gttt ON ttt.invoice_id = gttt.invoice_id AND ttt.trigger_date = gttt.Maxdate) epr on epr.invoice_id=ei.invoice_id 
                    WHERE cc . tenant_id = '" . $tenant_id . "' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT') 
                    AND ce.training_score in ('" . $training_score . "')
                    AND ce.payment_status in ('" . $payment_status . "')
                    AND date(cc.class_end_datetime)>= '" . $start_date . "' and date(cc.class_end_datetime) <= '" . $end_date . "'";

        $result = $this->db->query($query)->result();

        return $result;
    }

    public function get_invoice_data_for_individual($invoice_id, $user_id) {

        $query = "
            SELECT tt.*
                                FROM enrol_pymnt_brkup_dt tt
                                JOIN
                                    (SELECT `invoice_id`,user_id, MAX(`trigger_date`) AS Maxdate FROM enrol_pymnt_brkup_dt where invoice_id='" . $invoice_id . "' and user_id='" . $user_id . "' GROUP BY invoice_id) gtt ON tt.invoice_id = gtt.invoice_id AND tt.trigger_date = gtt.Maxdate and tt.user_id=gtt.user_id";

        $result = $this->db->query($query)->result();

        return $result[0]->amount_recd;
    }

    public function get_invoice_data_for_comp($invoice_id, $user_id) {

        $query = "SELECT tt.*
                                FROM enrol_pymnt_brkup_dt tt
                                JOIN
                                    (SELECT `invoice_id`, user_id,MAX(`trigger_date`) AS Maxdate FROM enrol_pymnt_brkup_dt where invoice_id='" . $invoice_id . "' and user_id='" . $user_id . "' GROUP BY invoice_id) gtt ON tt.invoice_id = gtt.invoice_id and tt.user_id=gtt.user_id";

        $result = $this->db->query($query)->result();
        //echo print_r($result,true);exit;
        return $result[0]->amount_recd;
    }
    
    public function tms_unpaid_report_count($tenant_id, $payment_status, $year, $month, $training_score) {
        $start_date = $year . '-' . $month . '-01';
        $end_date = $year . '-' . $month . '-31';
        if($month =='ALL'){
            $start_date = $year . '-01-01';
            $end_date = $year . '-12-31';
        }

        $query = "SELECT 
           
           
            count(tu.user_id) as count,
            sum(due.total_amount_due) as total_amount_due
           
           
                    FROM ( course_class cc) 
                    JOIN course c ON c.course_id = cc.course_id 
                    JOIN class_enrol ce ON ce.class_id = cc.class_id 
                    JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
                    join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
                    JOIN tms_users tu ON tu.user_id = ce.user_id 
                    left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
                    left join company_master cm on cm.company_id=ce.company_id
                    WHERE cc . tenant_id = '" . $tenant_id . "' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT') 
                    AND ce.training_score in ('" . $training_score . "')
                    AND ce.payment_status in ('" . $payment_status . "')
                    AND date(cc.class_end_datetime)>= '" . $start_date . "' and date(cc.class_end_datetime) <= '" . $end_date . "'";

        $result = $this->db->query($query)->result();

        return $result;
    }

    public function tms_paid_report_count($tenant_id, $payment_status, $year, $month, $training_score) {
        $start_date = $year . '-' . $month . '-01';
        $end_date = $year . '-' . $month . '-31';
        
        if($month =='ALL'){
            $start_date = $year . '-01-01';
            $end_date = $year . '-12-31';
        }
        
        $query = "SELECT 
            
            tu.user_id,
            ei.invoice_id,
            ce.enrolment_mode
                    FROM ( course_class cc) 
                    JOIN course c ON c.course_id = cc.course_id 
                    JOIN class_enrol ce ON ce.class_id = cc.class_id 
                    JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
                    join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
                    JOIN tms_users tu ON tu.user_id = ce.user_id 
                    left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
                    left join company_master cm on cm.company_id=ce.company_id
                    JOIN(SELECT ttt.*
                        FROM enrol_paymnt_recd ttt
                        JOIN
                        (SELECT `invoice_id`, MAX(`trigger_date`) AS Maxdate FROM enrol_paymnt_recd GROUP BY invoice_id) gttt ON ttt.invoice_id = gttt.invoice_id AND ttt.trigger_date = gttt.Maxdate) epr on epr.invoice_id=ei.invoice_id 
                    WHERE cc . tenant_id = '" . $tenant_id . "' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT') 
                    AND ce.training_score in ('" . $training_score . "')
                    AND ce.payment_status in ('" . $payment_status . "')
                    AND date(cc.class_end_datetime)>= '" . $start_date . "' and date(cc.class_end_datetime) <= '" . $end_date . "'";

        $result = $this->db->query($query)->result();

        return $result;
    }
    ///added by shubhranshu to fetch the compoay name by invoice id
    public function fetch_company_name_by_invoice_id($invoice_id){
        $this->db->select('cm.company_name');
        $this->db->from('enrol_invoice ei');
        $this->db->join('company_master cm', 'cm.company_id = ei.company_id');
        //$this->db->where('tenant_id', $tenant_id);
        $this->db->where('ei.invoice_id', $invoice_id);
        $current= $this->db->get()->row();
        
        $this->db->select('cm.company_name');
        $this->db->from('enrol_invoice_audittrail ei');
        $this->db->join('company_master cm', 'cm.company_id = ei.company_id');
        //$this->db->where('tenant_id', $tenant_id);
        $this->db->where('ei.invoice_id', $invoice_id);
        $old= $this->db->get()->row();
        
        $comp = array(
            'current' => $current,
            'old' => $old
        );
        
        return $comp;
        
    }
    
    public function salesrep($tenant_id,$sales_executive_id,$start,$end){
        if (!empty($start)) {
            $sql_start_date = date('Y-m-d', strtotime($start));
        }
        if (!empty($end)) {
            $sql_end_date = date('Y-m-d', strtotime($end));
        }
        $get_course_id_query ="SELECT 
            DISTINCT(crse.course_id)
            from class_enrol ce
            left join course_class cc on cc.class_id=ce.class_id and cc.course_id=ce.course_id 
            left join course crse on crse.course_id=ce.course_id 
            where ce.tenant_id='$tenant_id' and ce.sales_executive_id='$sales_executive_id' and
            date(ce.enrolled_on)>= '$sql_start_date' and date(ce.enrolled_on) <= '$sql_end_date' 
            group by cc.course_id"; 
        $get_course_ids = $this->db->query($get_course_id_query)->result();
        
        $final_data = array();
        
        foreach ($get_course_ids as $course){
            $qury ="
            SELECT 
            crse.crse_name,      
            cc.class_start_datetime,
            ce.tenant_id as provider,
            cc.class_fees as coursefee,
            tup.first_name,
            tu.tax_code,
            ce.training_score
            from class_enrol ce
            left join course_class cc on cc.class_id=ce.class_id and cc.course_id=ce.course_id 
            left join tms_users tu on tu.user_id =ce.user_id 
            left join tms_users_pers tup on tup.user_id =ce.user_id 
            left join course crse on crse.course_id=ce.course_id 
            where ce.tenant_id='$tenant_id' and ce.sales_executive_id='$sales_executive_id' and ce.course_id='$course->course_id' and
            date(ce.enrolled_on)>= '$sql_start_date' and date(ce.enrolled_on) <= '$sql_end_date' 
            order by cc.course_id asc";
            $result = $this->db->query($qury)->result();

            $final_data[]= $result;
         
        }
     
        return $final_data;
        
    }
    
    public function get_sales_report_data_xls($tenant_id, $start_date, $end_date, $sales_exec){
        if (!empty($start_date)) {
            $sql_start_date = date('Y-m-d', strtotime($start_date));
        }
        if (!empty($end_date)) {
            $sql_end_date = date('Y-m-d', strtotime($end_date));
        }
        $get_course_id_query ="SELECT 
            DISTINCT(crse.course_id)
            from class_enrol ce
            left join course_class cc on cc.class_id=ce.class_id and cc.course_id=ce.course_id 
            left join course crse on crse.course_id=ce.course_id 
            where ce.tenant_id='$tenant_id' and ce.sales_executive_id='$sales_exec' and
            date(ce.enrolled_on)>= '$sql_start_date' and date(ce.enrolled_on) <= '$sql_end_date' 
            group by cc.course_id"; 
        $get_course_ids = $this->db->query($get_course_id_query)->result();
        
        $final_data = array();
        
        foreach ($get_course_ids as $course){
            $qury ="
            SELECT 
            crse.crse_name,      
            cc.class_start_datetime,
            ce.tenant_id as provider,
            cc.class_fees as coursefee,
            tup.first_name,
            tu.tax_code,
            ce.training_score
            from class_enrol ce
            left join course_class cc on cc.class_id=ce.class_id and cc.course_id=ce.course_id 
            left join tms_users tu on tu.user_id =ce.user_id 
            left join tms_users_pers tup on tup.user_id =ce.user_id 
            left join course crse on crse.course_id=ce.course_id 
            where ce.tenant_id='$tenant_id' and ce.sales_executive_id='$sales_exec' and ce.course_id='$course->course_id' and
            date(ce.enrolled_on)>= '$sql_start_date' and date(ce.enrolled_on) <= '$sql_end_date' 
            order by cc.course_id asc";
            $result = $this->db->query($qury)->result();

            $final_data[]= $result;
         
        }
     
        return $final_data;
    }
    
    public function salessummary_monthwise($tenant_id,$yearVal,$monthVal) {
        $start_date = $yearVal . '-' . $monthVal . '-01';
        $end_date = $yearVal . '-' . $monthVal . '-31';

        $query = "SELECT 
            tu.tax_code,
            tu.user_id,
            ei.invoice_id,
            ei.inv_date,
            tup.first_name as name,
            ei.total_inv_amount,
            cm.company_name,
            due.class_fees,
            ceil((due.class_fees * due.discount_rate))/ 100 as discount_rate,
            due.gst_amount,
            due.total_amount_due,
            ce.tg_number,
            due.subsidy_amount,
            ce.payment_status,
            ce.enrolment_mode,
            epr.mode_of_pymnt,
            cc.class_start_datetime,
            cc.class_end_datetime,
            cc.class_name,
            ce.training_score,
            due.att_status
                    FROM ( course_class cc) 
                    JOIN course c ON c.course_id = cc.course_id 
                    JOIN class_enrol ce ON ce.class_id = cc.class_id 
                    JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
                    join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
                    JOIN tms_users tu ON tu.user_id = ce.user_id 
                    left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
                    left join company_master cm on cm.company_id=ce.company_id
                    LEFT JOIN(SELECT ttt.*
                        FROM enrol_paymnt_recd ttt
                        JOIN
                        (SELECT `invoice_id`, MAX(`trigger_date`) AS Maxdate FROM enrol_paymnt_recd GROUP BY invoice_id) gttt ON ttt.invoice_id = gttt.invoice_id AND ttt.trigger_date = gttt.Maxdate) epr on epr.invoice_id=ei.invoice_id 
                    WHERE cc . tenant_id = '" . $tenant_id . "' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT') 
                    AND date(cc.class_end_datetime)>= '" . $start_date . "' and date(cc.class_end_datetime) <= '" . $end_date . "'";

        $result = $this->db->query($query)->result();

        return $result;
    }
    /////added by shubhranshu for new enrolment report tpg on 19.11.2020
    public function enrolment_report_tpg_fetchdata($tenant_id,$class_id,$course_id) {

        $query = "SELECT
	(CASE 
	 WHEN tu.tax_code_type like '%SNG_1%' THEN 'NRIC' 
	 WHEN tu.tax_code_type like '%SNG_2%' THEN 'FIN' 
	 WHEN tu.tax_code_type like '%SNG_3%' THEN 'Others'
	 WHEN tu.tax_code_type like '%SNG_4%' THEN 'Others' ELSE NULL END
	) as 'TraineeIDType',
        tu.tax_code as 'TraineeID',
        DATE_FORMAT(tup.dob,'%d-%m-%Y') as 'DateofBirth',
        tup.first_name as 'TraineeName',
        cc.class_name as 'CourseRun',
        cc.tpg_course_run_id as 'CourseRunid',
        tu.registered_email_id as 'TraineeEmail',
        '65' as 'TraineePhoneCountryCode',            
        ' ' as 'TraineePhoneAreaCode',
        tup.contact_number as 'TraineePhone',			
        (CASE 
        WHEN ei.inv_type like '%INVINDV%' THEN 'Individual' 
        WHEN ei.inv_type like '%INVCOMALL%' THEN 'Employer' 
        ELSE NULL END
        ) as 'SponsorshipType',
        COALESCE(cm.comp_regist_num,'') as 'EmployerUEN',
        COALESCE(cm.comp_attn,'') as 'EmployerContactName',
        (CASE 
        WHEN ei.inv_type like '%INVINDV%' THEN ' ' 
        WHEN ei.inv_type like '%INVCOMALL%' THEN '65' 
        ELSE NULL END
        ) as 'EmployerPhoneCountryCode',
        ' ' as 'EmployerPhoneAreaCode',
        COALESCE(cm.comp_phone,'') as 'EmployerPhone',
        COALESCE(cm.comp_email,'') as 'EmployerContactEmail',
        ei.total_inv_discnt as 'CourseFeeDiscountAmount',
        (CASE 
         WHEN ce.payment_status like 'PARTPAID' THEN 'Partial Payment' 
         WHEN ce.payment_status like 'PAID' THEN 'Full Payment' 
         WHEN ce.payment_status like 'NOTPAID' THEN 'Pending Payment' ELSE NULL END
        ) as 'FeeCollectionStatus'

        FROM ( course_class cc)
        JOIN course c ON c.course_id = cc.course_id 
        JOIN class_enrol ce ON ce.class_id = cc.class_id 
        JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
        join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
        JOIN tms_users tu ON tu.user_id = ce.user_id 
        left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
        left join company_master cm on cm.company_id=ce.company_id
        WHERE cc . tenant_id = '$tenant_id' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT')                    
        AND c.course_id ='$course_id'
        AND cc.class_id = '$class_id'";
        $result = $this->db->query($query)->result();

        return $result;
    }
    
    /**
     * Tenant : Fonda
     * Requested on 12-10-2021
     * Developed by : Abdulla Nofal
     * 
     */
    public function class_report($tenant_id,$class_id,$course_id) {

        $query = "SELECT
	(CASE 
	 WHEN tu.tax_code_type like '%SNG_1%' THEN 'NRIC' 
	 WHEN tu.tax_code_type like '%SNG_2%' THEN 'FIN' 
	 WHEN tu.tax_code_type like '%SNG_3%' THEN 'Others'
	 WHEN tu.tax_code_type like '%SNG_4%' THEN 'Others' ELSE NULL END
	) as 'TraineeIDType',
        tu.tax_code as 'TraineeID',
        DATE_FORMAT(tup.dob,'%d-%m-%Y') as 'DateofBirth',
        tup.first_name as 'TraineeName',
        tu.country_of_residence as 'CountryOfResidence'
        tu.registered_email_id as 'TraineeEmail',
        '65' as 'TraineePhoneCountryCode',            
        ' ' as 'TraineePhoneAreaCode',
        tup.gender as 'Gender',
        tup.race as 'Race',
        tup.contact_number as 'TraineePhone',			
        (CASE 
        WHEN ei.inv_type like '%INVINDV%' THEN 'Individual' 
        WHEN ei.inv_type like '%INVCOMALL%' THEN 'Employer' 
        ELSE NULL END
        ) as 'SponsorshipType',
        COALESCE(cm.company_name,'') as 'EmployerName',
        COALESCE(cm.comp_regist_num,'') as 'EmployerUEN',
        COALESCE(cm.comp_attn,'') as 'EmployerContactName',
        DATE_FORMAT(cm.created_on,'%d-%m-%Y') as 'EmployerRegistrationDate',
        (CASE 
        WHEN ei.inv_type like '%INVINDV%' THEN ' ' 
        WHEN ei.inv_type like '%INVCOMALL%' THEN '65' 
        ELSE NULL END
        ) as 'EmployerPhoneCountryCode',
        ' ' as 'EmployerPhoneAreaCode',
        COALESCE(cm.comp_phone,'') as 'EmployerPhone',
        COALESCE(cm.comp_email,'') as 'EmployerContactEmail',
        ei.total_inv_discnt as 'CourseFeeDiscountAmount',
        (CASE 
         WHEN ce.payment_status like 'PARTPAID' THEN 'Partial Payment' 
         WHEN ce.payment_status like 'PAID' THEN 'Full Payment' 
         WHEN ce.payment_status like 'NOTPAID' THEN 'Pending Payment' ELSE NULL END
        ) as 'FeeCollectionStatus'

        FROM ( course_class cc)
        JOIN course c ON c.course_id = cc.course_id 
        JOIN class_enrol ce ON ce.class_id = cc.class_id 
        JOIN enrol_pymnt_due due ON ce.pymnt_due_id = due.pymnt_due_id and ce.user_id = due.user_id 
        join enrol_invoice ei on ei.pymnt_due_id and due.pymnt_due_id and ei.pymnt_due_id=ce.pymnt_due_id
        JOIN tms_users tu ON tu.user_id = ce.user_id 
        left join tms_users_pers tup on tup.user_id =ce.user_id and tup.user_id= due.user_id
        left join company_master cm on cm.company_id=ce.company_id
        WHERE cc . tenant_id = '$tenant_id' AND ce . enrol_status IN ('ENRLBKD', 'ENRLACT')                    
        AND c.course_id ='$course_id'
        AND cc.class_id = '$class_id'";
        $result = $this->db->query($query)->result();

        return $result;
    }
    

}
