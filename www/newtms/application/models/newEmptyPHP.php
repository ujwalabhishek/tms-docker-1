<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * This is the controller class for Trainee use case all features. 
 */
class Trainee extends CI_Controller {
            
            
            
            
 public function create_bulk_enrol($tenant_id, $insert_data, $company_id, $course, $salesexec, $class, $class_detail, $curuser_id, $company_details) {

        $cur_date = date('Y-m-d H:i:s');

        $company_net_due = 0;

        $company_discount = 0;

        $company_subsidy = 0;

        $company_gst = 0;

        $company_total_unitfees = 0;

        
        //// Below code added by shubhranshu to fetch the company discount 
        $comp_discounts_details = $this->fetch_compnay_discount($tenant_id,$course,$company_id);
        if (($comp_discounts_details->Discount_Percent > 0) || ($comp_discounts_details->Discount_Amount > 0)) {

            if($comp_discounts_details->Discount_Percent > 0){
                $discount_rate = round($comp_discounts_details->Discount_Percent, 4);
                $discount_total = ( $discount_rate * $class_detail->class_fees) / 100;
            }else{
                $discount_total = $comp_discounts_details->Discount_Amount;
                $discount_rate =  round((($discount_total / $class_detail->class_fees) * 100), 4);
            }
            $discount_label = 'DISCOMP';
        } else if($class_detail->class_discount > 0){

            $discount_rate = round($class_detail->class_discount, 4);
            $discount_total = ( $discount_rate * $class_detail->class_fees) / 100;
            $discount_label = 'DISCLASS';
        }else{
            $discount_rate = 0;
            $discount_total = 0;
            $discount_label = 'DISCOMP';
        }
        //////end of code by ssp


        $course_detail = $this->db->select('subsidy_after_before,gst_on_off')->from('course')->where('course_id', $course)->get()->row();

        $feesdue = $class_detail->class_fees - ($discount_total);

        $gst_rate = $this->get_gst_current();



        $payment_due_id = get_max_lookup(ENROL_PYMNT_DUE);

        $invoice_id = $this->generate_invoice_id();

        foreach ($insert_data as $key => $excel) {
             ////////////////////////added by shubhranshu to prevent negative invoice due to subsidy on 4/1/2019////////////
                $k= 4;
                $subsidy_amount = $excel['subsidy_amount'];
                $netdue = $this->calculate_net_due($course_detail->gst_on_off, $course_detail->subsidy_after_before, $feesdue, $subsidy_amount, $gst_rate);

                if($netdue <= 0){
                    $insert_data[$k]['status'] = 'FAILED';
                    $excel['status']='FAILED';
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

                    if ($this->user->role_id == 'SLEXEC') {

                        $salesexec = $this->user->user_id;
                    } else {

                        $salesexec = empty($salesexec) ? NULL : $salesexec;
                    }
                    /////////below block was added by shubhranshu for training score to be update for bulk enrol/////
                    $check_attendance=$this->check_attendance_row($tenant_id,$course,$class);
                    //echo $check_attendance.' -'.$tenant_id.'-'.$course.'-'.$class;exit;
                    if($check_attendance>0)
                    { 
                        $training_score='ABS';
                        $att_status=0;

                    }else { 
                        $att_status=1;$training_score='C';

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
                        'training_score' => $training_score,////added by shubhranshu to by default the score should be present.
                        'payment_status' => $pay_status,
                        'sales_executive_id' => $salesexec,
                        'class_status' => $class_status,
                        'enrol_status' => $enrol_status
                    );

                    $this->db->insert('class_enrol', $data);

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
                $check = $this->db->select('*')
                    ->from('class_enrol')->where('tenant_id', $tenant_id)->where('course_id', $course)
                    ->where('class_id', $class)->where('company_id', $company_id)->get();

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

                        $this->db->insert('enrol_invoice', $data);

                        $insert_data['invoice_id'] = $invoice_id;

                    }

                }else{    

                        $inv_detls = $this->fetch_enrol_invoice_check($tenant_id,$course,$class,$company_id);
                        
                        if (!empty($inv_detls->pymnt_due_id)) {


                            $company_net_due = $company_net_due + round($netdue, 4) + $inv_detls->total_inv_amount;

                            $company_discount = $company_discount + round($discount_total, 4) + $inv_detls->total_inv_discnt;

                            $company_subsidy = $company_subsidy + round($subsidy_amount, 4) + $inv_detls->total_inv_subsdy;

                            $totalgst = $this->calculate_gst($course_detail->gst_on_off, $course_detail->subsidy_after_before, ($feesdue+$inv_detls->total_inv_amount), ($subsidy_amount+$inv_detls->total_inv_subsdy), $gst_rate);

                            $company_gst = $company_gst + round($totalgst, 4);

                            $company_total_unitfees = $company_total_unitfees + $class_detail->class_fees;

                            $data = array(
                                'invoice_id' => $inv_detls->invoice_id,
                                'pymnt_due_id' => $inv_detls->pymnt_due_id,
                                'inv_date' => $cur_date,
                                'inv_type' => 'INVCOMALL',
                                'company_id' => $company_id,
                                'total_inv_amount' => round($company_net_due, 4),
                                'total_unit_fees' => round($company_total_unitfees, 4),
                                'total_inv_discnt' => round(($company_discount) , 4),
                                'total_inv_subsdy' => round(($company_subsidy), 4),
                                'total_gst' => round($company_gst, 4),
                                'gst_rate' => round($gst_rate, 4),
                                'gst_rule' => $gst_rule,
                            );

                            $this->db->where('pymnt_due_id', $inv_detls->pymnt_due_id);

                            $this->db->update('enrol_invoice', $data);

                            $insert_data['invoice_id'] = $inv_detls->invoice_id;

                        }

                }
            

        return $insert_data;
    }
    
    public function fetch_enrol_invoice_check($tenant_id,$course_id,$class_id,$comp_id) {

        $result = $this->db->select('ei.*')->from('enrol_invoice ei')
                        ->join('class_enrol ce', 'ce.pymnt_due_id=ei.pymnt_due_id')                       
                        ->where('ce.course_id', $course_id) 
                        ->where('ce.class_id', $class_id) 
                         ->where('ce.tenant_id', $tenant_id) 
                        ->where('ce.company_id',$comp_id)
                        ->get()->row();

        return $result;
    }
    ///////////addded by shubhranshu for company invoice if exist
    
    
    
}