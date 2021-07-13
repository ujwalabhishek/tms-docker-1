<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * his is the controller class for Accounting Use Cases
 */

class accounting extends CI_Controller {

    private $user;

    /**
     * constructor - loads Model and other objects required in this controller
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('class_trainee_model', 'classTraineeModel');
        $this->load->model('class_Model', 'classModel');
        $this->load->model('company_model', 'companyModel');
        $this->load->model('tenant_model', 'tenantModel');
        $this->load->helper('common');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values', 'meta');
        $this->user = $this->session->userdata('userDetails');
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
    }

    /*
     * This function loads the initial static page for accounting.
      */
   public function index() {
       $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'classtrainee/accounting';
        $this->load->view('layout', $data);
    }

    /*
     * This function loads the generate invoice form.
     */
    public function generate_invoice() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
        $data['companies'] = $this->classTraineeModel->get_company_for_allinvoice($tenant_id);
        //$data['invoices'] = $this->get_invoice(1); ///commented by shubhranshu
        //$data['total_invoice'] = $this->classTraineeModel->total_enrol_invoice(); ///commented by shubhranshu
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'classtrainee/generateinvoice';
        $this->load->view('layout', $data);
    }

    /*
     * This function loads the Update Payment form.
     */
    public function update_payment() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
         $data['tenant_id'] = $tenant_id;
        $data['companies'] = $this->classTraineeModel->get_notpaid_invoice_company($tenant_id);
        //$data['invoices'] = $this->get_notpaid_invoice(1); ///commented by shubhranshu
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('payment_type', 'Payment Type', 'required');
            $payment_type = $this->input->post('payment_type');
            if ($payment_type == 'CHQ') {
                $this->form_validation->set_rules('paid_on', 'Paid on', 'required');
                $this->form_validation->set_rules('cheque_number', 'Cheque number', 'required');
                $this->form_validation->set_rules('cheque_amount', 'Cheque amount', 'required');
                $this->form_validation->set_rules('cheque_date', 'Cheque date', 'required');
                $this->form_validation->set_rules('bank_name', 'Bank name', 'required');
            } else if ($payment_type == 'CASH' || $payment_type == 'NETS' ||  $payment_type == 'PSEA') {
                $this->form_validation->set_rules('cashpaid_on', ' Paid on', 'required');
                $this->form_validation->set_rules('cash_amount', 'Cash Amount', 'required');
            } else if ($payment_type == 'GIRO') {
                $this->form_validation->set_rules('transc_on', ' Transcation Date', 'required');
                $this->form_validation->set_rules('gbank_name', 'Bank Name', 'required');
                $this->form_validation->set_rules('giro_amount', 'Giro Amount', 'required');
            }
            else if ($payment_type == 'SFC_ATO') {
                $this->form_validation->set_rules('sfcatoclaim_on', 'SFC Claimed', 'required');
                $this->form_validation->set_rules('sfcato_amount', 'SFC Amount', 'required');
                if($tenant_id == 'T02'){////added by shubhranshu to check if xp mandatory sfc id
                    $this->form_validation->set_rules('sfc_ato_claim_id', 'SFC Claim ID', 'required');
                }
            }
            if ($payment_type1 == 'CHQ1') {
                $this->form_validation->set_rules('paid_on1', 'Paid on', 'required');
                $this->form_validation->set_rules('cheque_number1', 'Cheque number', 'required');
                $this->form_validation->set_rules('cheque_amount1', 'Cheque amount', 'required');
                $this->form_validation->set_rules('cheque_date1', 'Cheque date', 'required');
                $this->form_validation->set_rules('bank_name1', 'Bank name', 'required');
            }
            else if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') {
                $this->form_validation->set_rules('cashpaid_on1', ' Paid on', 'required');
                $this->form_validation->set_rules('cash_amount1', 'Cash Amount', 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $data['main_content'] = 'classtrainee/updatepayment';
                $this->load->view('layout', $data);
            } else {
                $trainee_selected = $this->input->post('trainee_selected');
                if (!empty($trainee_selected)) {
                    /* for activity log of company skm start */
                    $comp_payment_due_id = $this->input->post('payment_due_id');
                    $comp_invoice_id = $this->db->select('invoice_id')->from('enrol_invoice')->where('pymnt_due_id', $comp_payment_due_id)->get()->row()->invoice_id;
                    $comp_res = $this->classTraineeModel->get_last_comp_payment_update($comp_invoice_id);                   
                    if($comp_res !=0){  $comp_previous_data = json_encode($comp_res);  }
                     /* skm end*/
                    
                    $result = $this->classTraineeModel->update_company_payment_post($tenant_id, $user_id);
                    if ($result == TRUE) {
                         if($comp_res !=0){  user_activity(14,$comp_invoice_id,$comp_previous_data,2); }//s1
                        $this->session->set_flashdata("success", "Payment updated successfully.");
                    } else {
                        $this->session->set_flashdata("error", "Unable to update payment. Please try again later.");
                    }
                } else {
                     /* for activity log of individual skm start */
                    $payid = $this->input->post('payment_due_id'); 
                    $invoice_id = $this->db->select('invoice_id')->from('enrol_invoice')->where('pymnt_due_id', $payid)->get()->row()->invoice_id;
                    $res = $this->classTraineeModel->get_last_payment_update($invoice_id);
                    
                    if($res!=0){  $previous_data = json_encode($res); }
//                    /* skm end */
                    $result = $this->classTraineeModel->update_payment_post($tenant_id, $user_id);
//                    $payid = $this->input->post('payment_due_id');                    
                    $trainee_data = $this->classTraineeModel->get_trainee_by_pymnt_due_id($payid);
                    $name = $trainee_data->first_name . ' ' .
                            $trainee_data->last_name;
                    $trainee = ($trainee_data->gender == 'MALE') ?
                            'Mr.' . $name : 'Ms.' . $name;
                    if ($result == TRUE) {
                          if($res!=0){ user_activity(14,$invoice_id,$previous_data,1); }
                        $this->session->set_flashdata("success", "'$trainee' Payment updated successfully.");
                    } else {
                        $this->session->set_flashdata("error", "Unable to update payment for '$trainee'. Please try again later.");
                    }
                }
                redirect('accounting/update_payment');
            }
        }
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'classtrainee/updatepayment';
        $this->load->view('layout', $data);
    }
 

    /*
     * This function loads the Refund Payment form.
     */
    public function refund_payment() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
         $data['tenant_id'] = $tenant_id;
        $data['companies'] = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        //$data['invoices'] = $this->get_paid_invoice(1); ///commented by shubhranshu
        $data['refund_reason'] = fetch_metavalues_by_category_id(Meta_Values::REFUND_REASON);
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('refund_date', 'Refund Date', 'required');
            $this->form_validation->set_rules('payment_type', 'Payment Type', 'required');
            $payment_type = $this->input->get('payment_type');
            if ($payment_type == 'CHQ') {
                $this->form_validation->set_rules('cheque_number', 'Cheque number', 'required');
                $this->form_validation->set_rules('cheque_amount', 'Cheque amount', 'required');
                $this->form_validation->set_rules('cheque_date', 'Cheque date', 'required');
                $this->form_validation->set_rules('bank_name', 'Bank name', 'required');
            } else if ($payment_type == 'CASH' || $payment_type == 'NETS') {
                $this->form_validation->set_rules('cash_amount', 'Cash Amount', 'required');
            }
            else if ($payment_type == 'SFC_SELF') {
                $this->form_validation->set_rules('sfc_amount', 'SFC(SELF) Amount', 'required');
            }
             if ($payment_type1 == 'CASH1' || $payment_type1 == 'NETS1') {
                $this->form_validation->set_rules('cash_amount1', 'Cash Amount', 'required');
            }
            else if ($payment_type1 == 'CHQ1') {
                $this->form_validation->set_rules('cheque_number1', 'Cheque number', 'required');
                $this->form_validation->set_rules('cheque_amount1', 'Cheque amount', 'required');
                $this->form_validation->set_rules('cheque_date1', 'Cheque date', 'required');
                $this->form_validation->set_rules('bank_name1', 'Bank name', 'required');
            }
            if ($this->form_validation->run() == FALSE) {
                $data['main_content'] = 'classtrainee/refundpayment';
                $this->load->view('layout', $data);
            } else {
                $account_hidden_type = $this->input->post('account_hidden_id');
                if ($account_hidden_type == 'individual') 
                {   
                    $invoice_id = $this->input->post('invoice_hidden_id'); //s1
                    $refund_data = $this->classTraineeModel->get_last_payment_refund($invoice_id);//s2
                    $result = $this->classTraineeModel->refund_payment_post($tenant_id, $user_id);
                    if($refund_data!=0) //s3
                    {
                        $previous_data = json_encode($refund_data);//s4
                        user_activity(15,$invoice_id,$previous_data,1); //s5
                    }
                    $trainee_data = $this->classTraineeModel->get_trainee_by_pymnt_due_id($this->input->post('payment_due_id'));
                    $name = $trainee_data->first_name . ' ' .
                            $trainee_data->last_name;
                    $trainee = ($trainee_data->gender == 'MALE') ?
                            'Mr.' . $name : 'Ms.' . $name;
                } else {
                    $comp_invoice_id = $this->input->post('invoice_hidden_id'); //s1
                    $comp_refund_data = $this->classTraineeModel->get_last_comp_payment_refund($comp_invoice_id);    //s2                
                    $result = $this->classTraineeModel->refund_company_payment_post($tenant_id, $user_id);
                    if($comp_refund_data!=0)//s3
                    { 
                        $comp_previous_data = json_encode($comp_refund_data);//s4
                        user_activity(15,$comp_invoice_id,$comp_previous_data,2);//s5
                    }
                    $company_id = $this->input->post('company_hidden_id');
                    if($company_id[0] == 'T') {
                        $tenant_details = fetch_tenant_details($company_id);            
                        $trainee = $tenant_details->tenant_name;            
                    } else {
                        $company_details = $this->companyModel->get_company_details($this->tenant_id, $company_id);
                        $trainee = $company_details[0]->company_name;
                    }
                }
                if ($result == TRUE) {
                    $this->session->set_flashdata("success", "'$trainee' Refund payment updated successfully.");
                } else {
                    $this->session->set_flashdata("error", "Unable to update Refund payment for '$trainee'. Please try again later.");
                }
                redirect('accounting/refund_payment');
            }
        }
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'classtrainee/refundpayment';
        $this->load->view('layout', $data);
    }
    /*
     * This function Updates commision payment details for a sales executives
     */
    public function update_commission() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $user_id = $this->session->userdata('userDetails')->user_id;
         $data['tenant_id'] = $tenant_id;
        $data['salesexec'] = $this->classModel->get_all_sales_exec($tenant_id);
        if ($this->input->server('REQUEST_METHOD') === 'GET') {
            $sales_exec = $this->input->get('salesexec');
            if (!empty($sales_exec)) {
                $comm_due = $this->classModel->get_commission_payment_due($tenant_id, $sales_exec);
                
                $data['comm_due'] = $comm_due;
                $data['paid_details'] = $this->classModel->get_commission_payment($tenant_id, $sales_exec);
                
            }
        }
        $data['page_title'] = 'Accounting';
        $data['main_content'] = 'class/updatecommission';
        $this->load->view('layout', $data);
    }

    /**
     * get enrol_invoice
     */
    public function get_invoice($is_json = 0) {
        $matches = array();
        $paid = $this->input->post('paid');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classTraineeModel->get_invoice($this->tenant_id, $query_string, $paid);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id,
                    'label' => $row->invoice_id . ' (Name: ' .
                    $row->first_name . ' ' . $row->last_name . ', Taxcode: ' .
                    $row->tax_code . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
        if (!empty($is_json)) {
            return $matches;
        } else {
            echo json_encode($matches);
            exit();
        }
    }

    /**
     * get paid enrol_invoice
     */
    public function get_paid_invoice($is_json = 0) {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classTraineeModel->get_paid_invoice($this->tenant_id, $query_string);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id,
                    'label' => $row->invoice_id . ' (Name: ' .
                    $row->first_name . ' ' . $row->last_name . ', Taxcode: ' .
                    $row->tax_code . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
        if (!empty($is_json)) {
            return $matches;
        } else {
            echo json_encode($matches);
            exit();
        }
    }

    /**
     * get not paid enrol_invoice
     */
    public function get_notpaid_invoice($is_return = 0) {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classTraineeModel->get_notpaid_invoice($this->tenant_id, $query_string);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id,
                    'label' => $row->invoice_id . ' (Name: ' .
                    $row->first_name . ' ' . $row->last_name . ', Taxcode: ' .
                    $row->tax_code . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
       
        if ($is_return == 1) {
            return $matches;
        } else {
            echo json_encode($matches);
            exit();
        }
    }    
    /**
     * This method is invoked for credit note list and Search, data entry and viewing of a credit note
     * 
     */
    public function credit_note() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $export_url = '?';
        foreach ($_GET as $k => $v) {
            $export_url .="$k=$v&";
        }
        $export_url = rtrim($export_url, '&');
        $data['export_url'] = $export_url;
        $data['sort_link'] = $sort_link = "credit_note_number=" . $this->input->get('credit_note_number');
        
        $credit_note_number =  $this->input->get('credit_note_number');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'CAST(crn.credit_note_number AS decimal)';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'accounting/credit_note/';
        $pageno = $this->uri->segment(3);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        
        $data['tabledata'] = $this->classTraineeModel->get_credit_note_list("data",$credit_note_number,$tenant_id, $records_per_page, $offset, $field, $order_by);        
        $totalrows = $this->classTraineeModel->get_credit_note_list("count",$credit_note_number ,$tenant_id, $records_per_page, $offset, $field, $order_by);        
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'accounting/credit_note/';

        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        
        $data['page_title'] = 'Credit Notes';
        $data['main_content'] = 'classtrainee/credit_notes_list';
        $this->load->view('layout', $data);
    }
    
    public function invoice_search() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $invoice_no =  $this->input->get('invoice_number');
        $payment_due_id =  $this->input->get('invoice_no');
        $inv_type =  $this->input->get('inv_type');
        
        if(!empty($invoice_no) && !empty($payment_due_id)){
            $data['tabledata'] = $this->classTraineeModel->get_invoice_list($payment_due_id,$invoice_no,$tenant_id,$inv_type);
        }
        $data['main_content'] = 'classtrainee/invoice_search';
        $this->load->view('layout', $data);
    }
    
    public function search_invoice(){
        $tenant_id = $this->tenant_id;
        $invoice_no =  $this->input->post('q');
        $result = $this->classTraineeModel->invoice_list($tenant_id,$invoice_no);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->pymnt_due_id,
                    'label' => $row->invoice_id . ' (PAYMENT DUE ID: ' . $row->pymnt_due_id . ' ' . $row->last_name . ')',
                    'value' => $row->invoice_id,
                    'pymtdue' => $row->pymnt_due_id,
                    'invtype' => $row->inv_type
                );
            }
        }
        echo json_encode($matches);exit;
    }
    /**
     * This method for exporting credit note list.
     */
    public function export_credit_note() {
        $tenant_id = $this->tenant_id;
        $credit_note_number =  $this->input->get('credit_note_number');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'CAST(crn.credit_note_number AS decimal)';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = ""; $offset ="";
        $result = $this->classTraineeModel->get_credit_note_list("credit_xl",$credit_note_number,$tenant_id, $records_per_page, $offset, $field, $order_by);        
        $this->load->helper('export_helper');
        export_credit_note($result);
    }
    /**
     * This Method for generate pdf credit notes.
     */
    public function pdf_credit_note(){
        $tenant_id=$this->tenant_id;
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $credit_note_number =  $this->input->get('credit_note_number');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'CAST(crn.credit_note_number AS decimal)';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $records_per_page = ""; $offset ="";
        $result = $this->classTraineeModel->get_credit_note_list("credit_xl",$credit_note_number,$tenant_id, $records_per_page, $offset, $field, $order_by);        
        $this->load->helper('pdf_reports_helper');
        $tenant_details = $this->tenantModel->get_tenant_details($tenant_id);
      
      return pdf_credit_note($result,$tenant_details);
    }
    /**
     * This Method for adding New credit notes.
     */
    public function add_credit_note() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Credit Notes';
        $data['main_content'] = 'classtrainee/add_credit_notes';
        $this->load->view('layout', $data);
    }
    /**
     * This method insert credit notes into credit_notes table.
     */
    public function insert_credit_notes() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('credit_note_number', 'Credit Note Number', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('ori_invoice_number', 'Ori. Invoice Number', 'trim|required');
        $this->form_validation->set_rules('credit_note_amount', 'Credit Note Amount', 'trim|required');
        $this->form_validation->set_rules('credit_note_date', 'Credit Note Date', 'trim|required');
        $this->form_validation->set_rules('ori_invoice_date', 'Original Invoice Date', 'trim|required');
        
        
        //$credit_note_no = clean($this->input->post('credit_note_number'));
        $credit_note_no = $this->input->post('credit_note_number');
        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'credit_note_number' => strtoupper($credit_note_no),
                'credit_note_date' => date('Y-m-d',strtotime($this->input->post('credit_note_date'))),
                'ori_invoice_number' => strtoupper($this->input->post('ori_invoice_number')),
                'ori_invoice_date' => date('Y-m-d',strtotime($this->input->post('ori_invoice_date'))),
                'credit_note_amount' => round($this->input->post('credit_note_amount'),2),
                'credit_note_issued_by' => strtoupper($this->input->post('credit_note_issued_by')),
                'credit_note_issue_reason' => strtoupper($this->input->post('credit_note_issue_reason')),
                'tg_ref_number' => strtoupper($this->input->post('tg_ref_number')),
            ); 
            
            
            
            $unique_check = $this->classTraineeModel->unique_check_credit_number($credit_note_no);
            if($unique_check == 0) {
                $status = $this->classTraineeModel->insert_credit_notes($data);
            } else {
                $status= FALSE;
            }
            if($status == TRUE) {
                $this->session->set_flashdata("success_message", "Credit Note has been created successfully");
            } else {
                $this->session->set_flashdata("error_message", "Unable to create Credit Note. Please try again later.");
            }
            redirect("accounting/credit_note");
        } else {
            $data['main_content'] = 'classtrainee/add_credit_notes';
            $this->load->view('layout', $data);
        }
    }
    /**
     * This Method for adding New credit notes.
     */
    public function view_credit_note($credit_note_number=0) {
        $credit_note_number = $this->input->get('q');
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Credit Notes';
        $data['tabledata'] = $this->classTraineeModel->get_credit_note($credit_note_number);
        $data['main_content'] = 'classtrainee/view_credit_note';
        $this->load->view('layout', $data);
    }
    /**
     * This method used for unique checking of credit note number.
     */
    public function unique_check_credit_number() {
        $credit_number = $this->input->post("credit_number");
        $status = $this->classTraineeModel->unique_check_credit_number($credit_number);
        echo $status;
        exit;
    }

}

