<?php

/*
 * Payment Controller 
 * Author : Balwant Singh
 * Use: Make paypal payment
 * Dependency : Paypal class (Path: application/library )
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('paypal_class');
        $this->load->model('courses_model');
    }

    public function index() {
        $data['page_title'] = 'Login Page';
        $this->load->view('login', $data);
        $this->load->library('session');
    }

    /*
      Function to fetch data, use papal library.
      Author: Balwant Singh
     */

    public function pay() {
        $url_string = strchr($_SERVER['HTTP_REFERER'], '&action=');
        $return_url = str_replace($url_string, '', $_SERVER['HTTP_REFERER']);
        $return_url = str_replace('&exists=1', '', $return_url);
        $url_string2 = strchr($_SERVER['HTTP_REFERER'], '&enrol_to=');
        $return_url = str_replace($url_string2, '', $return_url);
        $parm[enrol_to] = $this->input->get('enrolto'); //modified by sankar
        $parm[class_id] = $this->input->post('classId');
        $parm[course_id] = $this->input->post('courseId');
        $user_id = $this->session->userdata('userDetails')->user_id;
        $pay = $this->input->get('pay');
        $parm['user_id'] = $user_id;
        if (empty($parm['enrol_to'])) {
            $parm['enrol_to'] = $this->session->userdata('userDetails')->user_id;
        }
        if (!$pay) {
            $exists = $this->courses_model->check_user_enrol_exists($parm);
            if ($exists) {
                header('location: ' . $return_url . '&exists=1');
                exit();
                //code modification by sankar, reason: work flow change
            }else{
                $pay = 'update';
                $this->db->trans_start();
                $enrol_parm = $parm;
                $data['details'] = $this->courses_model->get_class_details($parm);
                $data['discount'] = $this->calculate_discount($data['details']);
                $fee_details = $this->calculate_fee($data['details'], $data['discount']);
                $enrol_parm['discount_amount'] = $fee_details['discount_amount']; ### added by sankar
                $enrol_parm['amount'] = $fee_details['net_fee'];
                $enrol_parm['discount'] = $fee_details['discount'];
                $enrol_parm['discount_type'] = $fee_details['discount_type'];
                $enrol_parm['class_fee'] = $fee_details['class_fee'];
                $enrol_parm['payment_status'] = NOT_PAID; ### changed by sankar
                $enrol_parm['gstrate'] = $fee_details['gstrate']; ### added by sankar
                $enrol_parm['gstamount'] = $fee_details['gstamount'];
                $data['fee'] = $fee_details;
                $enrolled = $this->courses_model->insert_class_enroll($enrol_parm);
                $this->db->trans_complete();
                //code modification starts here by sankar, on 17/03/2015, reason: work flow change
                if(!$enrolled){
                    $data['error'] = 'Error!! While enrollment.';
                    header('location: ' . $return_url);
                }
                //code modification ends here
            }
        } //else {
        //code modification ends here
            $this->session->set_userdata('payment_update', 'update');
        //} commented by sankar on 17/03/2015

        if (!$this->input->get('action')):

            $tenant_details = $this->fetch_tenant_details();
            $paypal_email = $tenant_details['paypal_email_id'];
            $currency = $tenant_details['Currency'];
            $country = $tenant_details['Country'];
            $admin_mail = $tenant_details['tenant_email_id'];

            $p = new paypal_class(); // paypal class
            $p->admin_mail = $admin_mail;
            $p->add_field('business', $paypal_email);
            $p->add_field('cmd', $this->input->post('cmd')); // cmd should be _cart for cart checkout
            $p->add_field('upload', '1');
            $p->add_field('return', $return_url . '&action=success');
            $p->add_field('cancel_return', $return_url . '&action=cancel');
            $p->add_field('notify_url', $return_url . '&action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
            $p->add_field('currency_code', $currency);
//            $p->add_field('country', $tenant_details->Country);
            $p->add_field('no_shipping', 1); //shipping address will not display
            $p->add_field('item_name', $_POST["course_name"] . '-' . $_POST["class_name"]);
            $p->add_field('quantity', 1);
            $p->add_field('amount', $this->input->post('amount'));
            $pid = $this->courses_model->store_paypal_enrol_details($parm);
            $p->add_field('invoice', $pid);
            $this->session->set_userdata('enrol_to', $parm['enrol_to']);
            $p->submit_paypal_post(); // POST it to paypal
            $p->dump_fields(); // Show the posted values for a reference
        endif;
    }

    /*
      Function to fetch Tenant details.
      Author: Balwant Singh
     */

    public function fetch_tenant_details() {
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $this->db->select('paypal_email_id, Currency,Country, tenant_email_id');
        $this->db->from('tenant_master');
        $this->db->where('tenant_id', $tenant_id);
        $output = $this->db->get()->result_array();
        return $output[0];
    }
    
    //functions added to get discount
    public function calculate_discount($details) {

        $userid = $this->input->get('enrolto');
        if (!$userid) {
            $userid = $this->session->userdata('userDetails')->user_id;
        }

        // get the individual discount
        $discount['discount'] = $this->courses_model->get_individual_discount($userid, $details['course']['id']);
        $discount['discount_type'] = 'DISINDVI';
        if (!$discount['discount']) {
            $discount['discount'] = $details['class'][class_discount];
            $discount['discount_type'] = 'DISCLASS';
        }

        if ($discount['discount']) {
            return $discount;
        } else {
            return array(
                'discount' => 0,
                'discount_type' => 'DISCLASS'
            );
        }
    }
    
     public function calculate_fee($details, $discount) {

        if ($discount['discount'] > 0) {
            $discount_amount = ($details['class']['class_fees'] / 100) * $discount['discount'];
            $feeafterdiscount = $details['class']['class_fees'] - $discount_amount;
        } else {
            $feeafterdiscount = $details['class']['class_fees'];
        }

        $isgst = $details['course']['isgst'];
        if ($isgst) {

            $gstamount = ($feeafterdiscount / 100 ) * $details['gst'];
            $fee['gstamount'] = number_format($gstamount, 2, '.', '');
            $fee['gstrate'] = $details['gst'];
            $fee['net_fee'] = $feeafterdiscount + $fee['gstamount'];
        } else {

            $fee['net_fee'] = $feeafterdiscount;
        }

        $fee['gstrate'] = number_format($details['gst'], 2, '.', '');
        $fee['net_fee'] = number_format($fee['net_fee'], 2, '.', '');
        $fee['discount'] = number_format($discount['discount'], 2, '.', '');
        $fee['class_fee'] = $details['class']['class_fees'];
        $fee['discount_amount'] = $discount_amount;
        $fee['feeafter_discount'] = $feeafterdiscount;
        $fee['discount_type'] = $discount['discount_type'];

        return $fee;
    }

}
