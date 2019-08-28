<?php

/*
 * Payments Model 
 * Author : Balwant Singh
 * Use: get payments details like invoice, acknowledgement and receipt of payment
 */

class Payments_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
      Function to get payments and invoices count.
      Author: Balwant Singh
     */

    public function paymentsInvoice_count() {
        $tenant_id = TENANT_ID;
        $userid = $this->session->userdata('userDetails')->user_id;
        $this->db->select('cc.class_id');
        $this->db->from('course_class cc');
        $this->db->join('class_enrol ce', 'cc.class_id = ce.class_id');
        $this->db->join('enrol_invoice ei', 'ce.pymnt_due_id = ei.pymnt_due_id');
        //$where  = "(ce.enrolled_by = $userid or  ce.user_id = $userid )";
        $where  = "(ce.user_id = $userid )";
        $this->db->where($where);
        $this->db->where('cc.tenant_id', $tenant_id);
        $result = $this->db->get();
        return $result->num_rows();
    }

    /*
      Function to get details of payments and invoices.
      Author: Balwant Singh
      Modified by:Blessy
     * Date:04/11/14
     */
  private function _get_admin_user_id() {
        $tenant_id = TENANT_ID;
        $this->db->select('tu.user_id');
        $this->db->from('tenant_master tm');
        $this->db->join('tms_users tu', 'tu.user_id = tm.tenant_org_id');
        $this->db->where('tm.tenant_id', $tenant_id);
        return $this->db->get()->row()->user_id;
    }
    public function get_sfc_details($invoice_id){
       
        $this->db->select('sfc_claimed,mode_of_pymnt,othr_mode_of_payment,other_amount_recd,amount_recd');
        $this->db->from('enrol_paymnt_recd');
        $this->db->where('invoice_id',$invoice_id);
        $this->db->order_by('recd_on','asc');
        $this->db->limit('1');
        $output = $this->db->get()->result_array();
        return $output[0];
    }

    public function paymentsInvoice_details($tenant_id, $limit = NULL, $offset = NULL, $sort_by = NULL, $sort_order = NULL) {
        $userid = $this->session->userdata('userDetails')->user_id;
        
    $this->db->select('cc.class_id, cc.course_id, cc.class_name, epd.class_fees, cc.class_start_datetime, cc.class_end_datetime,
                    cc.class_language,cc.class_status, cc.total_classroom_duration, cc.class_discount, cc.classroom_location,
                            ce.enrolment_mode,ce.user_id, ce.friend_id,ce.payment_status, ei.total_inv_discnt,ei.invoice_id, 
                            ei.total_inv_subsdy, ei.total_inv_amount, ei.total_gst,p.first_name,epd.att_status,epd.total_amount_due,
                            epd.subsidy_amount,epd.discount_rate,epd.gst_amount');
        $this->db->from('class_enrol ce');
        $this->db->join('course_class cc', 'cc.class_id = ce.class_id');
        $this->db->join('enrol_invoice ei', 'ce.pymnt_due_id = ei.pymnt_due_id');
         $this->db->join('enrol_pymnt_due epd', 'epd.pymnt_due_id = ce.pymnt_due_id and epd.user_id =ce.user_id');
        $this->db->join('tms_users_pers p','ce.user_id = p.user_id');
        //$where  = "(ce.enrolled_by = $userid or  ce.user_id = $userid )";
        $where  = "(ce.user_id = $userid )";
        $this->db->where($where);
        $this->db->where('cc.tenant_id', $tenant_id);
        if ($sort_by) {
            $this->db->order_by($sort_by, $sort_order);
        } else {
            $this->db->order_by('cc.class_name', 'DESC');
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

    /*
      Function to data of payment acknowledgement.
      Author: Balwant Singh
     * Modified   by  Blessy  Paul
     * 31/10/14
     */

    public function get_acknowledgement_data($clsid, $crsid, $userid = 0) {
        if(! $userid){
            $userid = $this->session->userdata('userDetails')->user_id;
        }
//        $class_status = array(IN_PROG, YT_STRT); ### commented by sankar
        $this->db->select('cc.class_id, cc.course_id, crs.crse_name, cc.class_name, cc.class_start_datetime, 
                              pers.first_name, pers.last_name,pers.gender,pers.user_id, cc.classroom_location,crs.certi_level, crs.crse_manager,ei.invoice_id,ei.inv_date, ei.total_inv_amount');
        $this->db->from('class_enrol ce');
        $this->db->join('course_class cc', 'cc.class_id = ce.class_id');
        $this->db->join('course crs', 'crs.course_id = cc.course_id');
        $this->db->join('tms_users_pers pers', 'pers.user_id = ce.user_id');
        $this->db->join('enrol_invoice ei', 'ce.pymnt_due_id = ei.pymnt_due_id');
        $this->db->where('ce.user_id', $userid);
        $this->db->where('cc.class_id', $clsid);
        $this->db->where('cc.course_id', $crsid);
//        $this->db->where_in('cc.class_status', $class_status); ### commented by sankar
        $this->db->where('cc.tenant_id', TENANT_ID);
        $output = $this->db->get()->result_array();
        $crse_manager = $output[0]['crse_manager'];
        $ack_data = $output[0];
        $this->db->select('u.registered_email_id, up.first_name, up.last_name, up.contact_number');
        $this->db->from('tms_users u');
        $this->db->join('tms_users_pers up', 'up.user_id = u.user_id');
        $this->db->where('u.user_id', $crse_manager);
        $crse_manager_data = $this->db->get()->result_array();
        $ack_data['crse_manager'] = $crse_manager_data[0];
        return $ack_data;
    }

    /*
      Function to details of tenant.
      Author: Balwant Singh
     */

 public function get_tanant() {
        $tenant_id = TENANT_ID;
        if (!empty($tenant_id)) {
            $output = '';
            $this->db->select('tenant_name, tenant_address, tenant_city, tenant_state, tenant_country, '
                    . 'tenant_email_id, Country, tenant_contact_num, invoice_name, invoice_footer_text');
            $this->db->from('tenant_master');
            $this->db->where('account_status', ACTIVE);
            $this->db->where('tenant_id', $tenant_id);
            $output = $this->db->get()->result_array();
            return $output[0];
        }
    }


    public function get_user_org_details($userid=0) {
        $tenant_id = TENANT_ID;
        if(! $userid){
            $userid = $this->session->userdata('userDetails')->user_id;
        }
        $this->db->select('u.tax_code, up.first_name, up.last_name,up.gender, cm.company_name, cm.comp_address,
                    up.contact_number, up.personal_address_bldg, up.personal_address_city, 
                    up.personal_address_state, up.personal_address_country,
                    cm.comp_city, cm.comp_state, cm.comp_cntry, cm.comp_phone');
        $this->db->from('tms_users u');
        $this->db->join('company_master cm', 'cm.company_id = u.tenant_org_id', 'LEFT');
        $this->db->join('tms_users_pers up', 'u.user_id = up.user_id');
        $this->db->where('u.tenant_id', $tenant_id);
        $this->db->where('u.user_id', $userid);
        $output = $this->db->get()->result_array();
        return $output[0];
    }

    public function get_invoice_details($clsid, $crsid, $userid) {
        $tenant_id = TENANT_ID;
        //$userid = $this->session->userdata('userDetails')->user_id;
        $this->db->select('cc.class_id, cc.course_id, cc.class_name,cc.class_start_datetime, epd.class_fees, ce.tg_number,
                        ce.payment_status,ei.invoice_id, ei.total_inv_discnt,ei.inv_date, ei.total_inv_subsdy, 
                        ei.total_inv_amount, ei.total_gst, ei.gst_rule, ei.gst_rate, epd.discount_type,
                        epd.discount_rate');
        $this->db->from('class_enrol ce');
        $this->db->join('course_class cc', 'cc.class_id = ce.class_id');
        $this->db->join('enrol_invoice ei', 'ce.pymnt_due_id = ei.pymnt_due_id');
        $this->db->join('enrol_pymnt_due epd', 'ce.pymnt_due_id = epd.pymnt_due_id');
        $this->db->where('cc.class_id', $clsid);
        $this->db->where('cc.course_id', $crsid);
        $this->db->where('ce.user_id', $userid);
        $this->db->where('cc.tenant_id', $tenant_id);
        $output = $this->db->get()->result_array();
        return $output[0];
    }
    /* Get paid details*/
    public function get_invoice_paid_details($invoice_id, $user_id = 0) {

        $mode_of_pymnt = $this->db->select('mode_of_pymnt')->from('enrol_paymnt_recd')->where('invoice_id', $invoice_id)
                ->order_by('recd_on','DESC')
                ->get()->row()->mode_of_pymnt;
         $mode_of_pymnt;
       
        if($mode_of_pymnt=="SFC_ATO" || $mode_of_pymnt=="SFC_SELF")
        {
            $order='ASC';
            //$mop=array('SFC_ATO','SFC_SELF');
            $mop=array('SFC_ATO','SFC_SELF','CASH','CHQ','GIRO','ONLINE');
        }
        else
        {
              $order='DESC';
              $mop=array('CASH','CHQ','GIRO','ONLINE');
        }
        $this->db->select('epbd.invoice_id, epbd.recd_on, epbd.amount_recd, 

                epr.mode_of_pymnt,epr.othr_mode_of_payment, epr.cheque_number,epr.sfc_claimed,epr.other_amount_recd,epr.cheque_date,

                tup.first_name, tup.last_name, tup.gender');

        $this->db->from('enrol_pymnt_brkup_dt epbd');

        $this->db->join('enrol_paymnt_recd epr', 'epr.invoice_id=epbd.invoice_id and epr.recd_on=epbd.recd_on', 'left');

        $this->db->join('tms_users_pers tup', 'tup.user_id = epbd.user_id', 'left');

        $this->db->where('epbd.invoice_id', $invoice_id);
        $this->db->where_in('epr.mode_of_pymnt',$mop);

        $this->db->order_by('epbd.recd_on',$order);

        if (!empty($user_id)) {

            $this->db->where('epbd.user_id', $user_id);
        }

        $result = $this->db->get()->result_object();

        return $result;
    }
      /**

     * function to get trainee refund details

     */
    public function get_refund_paid_details($invoice_id) {

        $result = $this->db->select('er.refund_on, er.mode_of_refund, er.amount_refund, er.cheque_number,

                er.cheque_date, er.refund_by, er.refnd_reason, er.refnd_reason_ot')
                        ->from('enrol_refund er')
                        ->where('er.invoice_id', $invoice_id)->get()->result_object();

        return $result;
    }


}
/*End  of the payments_model.php
Location:./application/models/payments_model.php */