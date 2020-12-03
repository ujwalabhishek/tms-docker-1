<?php

class Reports_finance extends CI_Controller {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('reports_model', 'reportsModel');
        $this->load->model('course_model', 'courseModel');
        $this->load->model('class_model', 'classModel');
        $this->load->model('tenant_model', 'tenantModel');
        $this->load->model('class_trainee_model', 'classTraineeModel');
        $this->load->model('internal_user_model', 'usersModel');
        $this->load->helper('common');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values', 'meta');

        $this->load->model('activity_log_model', 'activitylog');

        $this->load->model('common_model', 'commonmodel');

        $this->load->helper('url');

        $this->load->helper('pagination');

        $this->load->library('bcrypt');

        $this->user = $this->session->userdata('userDetails');
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
    }

    /**
     * Static repors page
     */
    public function index() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Reports';
        $data['main_content'] = 'reports/reports_finance';
        $this->load->view('layout', $data);
    }

    //// desgined by shubhranshu to pull the PAID /NOTPAID report
    public function tms_report() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if (!empty($_POST)) {


            $tenant_id = $this->session->userdata('userDetails')->tenant_id;
            if ($_POST['payStatus'] == '1') {
                $payment_status = "PAID','PARTPAID";
                $displayText = "Total Amount Received for Paid invoices :";
                $export_url = '?payStatus=1';
            } else if ($_POST['payStatus'] == '2') {
                $payment_status = "NOTPAID','PARTPAID";
                $displayText = "Total Amount Due for unpaid invoices :";
                $export_url = '?payStatus=2';
            }
            $data['text'] = $displayText;
            $year = $_POST['yearVal'];
            $month = $_POST['monthVal'];
            $training_score1 = $_POST['trainingStatus'];
            $export_url .='&yearVal=' . $year . '&monthVal=' . $month;
            if ($training_score1 == '1') {
                $training_score = 'C';
            } else if ($training_score1 == '2') {
                $training_score = "NYC','2NYC";
            } else if ($training_score1 == '3') {
                $training_score = 'ABS';
            } else if ($training_score1 == '4') {
                $training_score = "C','NYC','2NYC";
            }

            $export_url .= '&trainingStatus=' . $training_score1;
            $temp_data = array();
            if ($_POST['payStatus'] == '1') {
                $data['result'] = $this->reportsModel->tms_paid_report($tenant_id, $payment_status, $year, $month, $training_score);
            } else if ($_POST['payStatus'] == '2') {
                $data['result'] = $this->reportsModel->tms_unpaid_report($tenant_id, $payment_status, $year, $month, $training_score);
            }
        }
        $data['page_title'] = 'TMS Reports';
        $data['export_url'] = $export_url;
        $data['main_content'] = 'reports/tms_report';
        $this->load->view('layout', $data);
    }
    
    //// desgined by shubhranshu to pull the PAID /NOTPAID report
    public function tms_report_count() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        if (!empty($_POST)) {


            $tenant_id = $this->session->userdata('userDetails')->tenant_id;
            if ($_POST['pStatus'] == '1') {
                $payment_status = "PAID','PARTPAID";
                $displayTextCount = "Total Paid Trainees : ";
                $export_url = '?pStatus=1';
            } else if ($_POST['pStatus'] == '2') {
                $payment_status = "NOTPAID','PARTPAID";
                $displayTextCount = "Total Unpaid Trainees : ";
                $export_url = '?payStatus=2';
            }
            
            $year = $_POST['yVal'];
            $month = $_POST['mVal'];
            
            $training_score1 = $_POST['tStatus'];
            $export_url .='&yearVal=' . $year . '&monthVal=' . $month;
            if ($training_score1 == '1') {
                $training_score = 'C';
            } else if ($training_score1 == '2') {
                $training_score = "NYC','2NYC";
            } else if ($training_score1 == '3') {
                $training_score = 'ABS';
            } else if ($training_score1 == '4') {
                $training_score = "C','NYC','2NYC";
            }

            $export_url .= '&trainingStatus=' . $training_score1;
            $temp_data = array();
            if ($_POST['pStatus'] == '1') {
                $data_res = $this->reportsModel->tms_paid_report_count($tenant_id, $payment_status, $year, $month, $training_score);
                $paidVal1 = $this->calculate_paid($data_res);
                $count=count($data_res);
                $displayTextCount = 'Total Amount Received : ';
            } else if ($_POST['pStatus'] == '2') {
                $data_res = $this->reportsModel->tms_unpaid_report_count($tenant_id, $payment_status, $year, $month, $training_score);
                $amount_due = $data_res[0]->total_amount_due;
                $paidVal1 = $amount_due;
                $count=$data_res[0]->count;
                $displayTextCount = 'Total Amount Pending : ';
            } else if ($_POST['pStatus'] == '3'){
                $payment_status2 = "PAID','PARTPAID";
                $payment_status1 = "NOTPAID','PARTPAID";
                $data1_res = $this->reportsModel->tms_unpaid_report_count($tenant_id, $payment_status1, $year, $month, $training_score);
                $data2_res = $this->reportsModel->tms_paid_report_count($tenant_id, $payment_status2, $year, $month, $training_score);
                $amount_due = $data1_res[0]->total_amount_due;
                $paid_amout = $this->calculate_paid($data2_res);
                $count = ($data1_res[0]->count)+count($data2_res);
                $displayTextCount = "Total Paid + Unpaid Amount : ";
                
                $paidVal1 =$amount_due + $paid_amout;
                
            }
            
        }
        
        $data['text1'] = 'Total Trainee: '.$count;
        $data['amount1'] = $displayTextCount.$paidVal1;
        $data['page_title'] = 'TMS Reports';
        $data['export_url'] = $export_url;
        $data['main_content'] = 'reports/tms_report';
        $this->load->view('layout', $data);
    }
    
    public function calculate_paid($data_res){
        foreach($data_res as $raw){
            if($raw->enrolment_mode =='SELF'){
                $amount = $this->reportsModel->get_invoice_data_for_individual($raw->invoice_id, $raw->user_id);
                $paidVal = $paidVal + $amount;

            }else{
                $amount1= $this->reportsModel->get_invoice_data_for_comp($raw->invoice_id, $raw->user_id);
                $paidVal = $paidVal + $amount1;
            }

       }
       return $paidVal;
    }

    //// desgined by shubhranshu to pull the PAID /NOTPAID report by xls
    public function export_tms_report() {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        if (!empty($_GET)) {
            $tenant_id = $this->session->userdata('userDetails')->tenant_id;
            if ($_GET['payStatus'] == '1') {
                $payment_status = "PAID','PARTPAID";
            } else if ($_GET['payStatus'] == '2') {
                $payment_status = "NOTPAID','PARTPAID";
            }
            $data['text'] = $displayText;
            $year = $_GET['yearVal'];
            $month = $_GET['monthVal'];
            $training_score1 = $_GET['trainingStatus'];

            if ($training_score1 == '1') {
                $training_score = 'C';
            } else if ($training_score1 == '2') {
                $training_score = "NYC','2NYC";
            } else if ($training_score1 == '3') {
                $training_score = 'ABS';
            } else if ($training_score1 == '4') {
                $training_score = "C','NYC','2NYC";
            }


            if ($_GET['payStatus'] == '1') {
                $result = $this->reportsModel->tms_paid_report($tenant_id, $payment_status, $year, $month, $training_score);
            } else if ($_GET['payStatus'] == '2') {
                $result = $this->reportsModel->tms_unpaid_report($tenant_id, $payment_status, $year, $month, $training_score);
            }
        }

        $this->load->helper('export_helper');
        export_tms_report_page($result);
    }

    /**
     * List and Search Invoice Reports
     */
    public function invoice_list() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $payment_status = $this->input->get('payment_status');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company_id = $this->input->get('company_id');
        if (!empty($_GET)) {
            $totalrows = $this->reportsModel->get_all_invoice_count($tenant_id, $payment_status, $start_date, $end_date, $company_id);
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'reports_finance/invoice_list/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = ($pageno * $records_per_page);
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.inv_date';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $tabledata = $this->reportsModel->get_all_invoice($tenant_id, $records_per_page, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $company_id);
            $tabledata_count = count($tabledata);
            for ($i = 0; $i < $tabledata_count; $i++) {
                if ($tabledata[$i]->enrolment_mode === 'COMPSPON') {
                    $tabledata[$i]->payment_status = $this->reportsModel->check_not_part_paid($tabledata[$i]->pymnt_due_id);
                }
            }
            $data['tabledata'] = $tabledata;
            $data['sort_order'] = $order_by;
        }
        $data['controllerurl'] = 'reports_finance/invoice_list/';
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "payment_status=" . $this->input->get('payment_status') . "&start_date=" . $this->input->get('start_date') . "&end_date=" . $this->input->get('end_date') . "&company_id=" . $this->input->get('company_id') . "&company_name=" . $this->input->get('company_name');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['page_title'] = 'Accounting Reports - List and Search Invoice';
        $data['main_content'] = 'reports/invoice_list';
        $this->load->view('layout', $data);
    }

    /**
     * List and Search regenerated / deleted Invoice Reports
     */
    public function invoice_reg_list() {
//         $this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();

        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $payment_status = $this->input->get('payment_status');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company_id = $this->input->get('company_id');
        $invoice_id = $this->input->get('invoice_id'); //skm
        $prev_invoice_id = $this->input->get('prev_invoice_id'); //skm
        if (!empty($_GET)) {
            $totalrows = $this->reportsModel->get_reg_invoice_count($tenant_id, $payment_status, $start_date, $end_date, $company_id, $invoice_id, $prev_invoice_id);
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'reports_finance/invoice_reg_list/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = ($pageno * $records_per_page);
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'inv_date';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $tabledata = $this->reportsModel->get_reg_invoice($tenant_id, $records_per_page, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $company_id, $invoice_id, $prev_invoice_id);

            $tabledata_count = count($tabledata);

            $data['tabledata'] = $tabledata;
            $data['sort_order'] = $order_by;
        }
        $data['controllerurl'] = 'reports_finance/invoice_reg_list/';
        $this->load->helper('pagination');
        //$data['sort_link'] = $sort_link = "payment_status=" . $this->input->get('payment_status') . "&start_date=" . $this->input->get('start_date') . "&end_date=" . $this->input->get('end_date') . "&company_id=" . $this->input->get('company_id') . "&company_name=" . $this->input->get('company_name');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['page_title'] = 'Accounting Reports - Regenerated and Deleted Invoice';
        $data['main_content'] = 'reports/invoice_reg_list';
        $this->load->view('layout', $data);
    }

    /*
     * List and Search Invoice - Export to XLS
     */

    public function invoice_list_export_xls() {
        ini_set('memory_limit', '-1');
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $payment_status = $this->input->get('payment_status');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $company_id = $this->input->get('company_id');
        $tabledata = $this->reportsModel->get_all_invoice($tenant_id, $records_per_page, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $company_id);

        $tabledata_count = count($tabledata);
        for ($i = 0; $i < $tabledata_count; $i++) {
            if ($tabledata[$i]->enrolment_mode === 'COMPSPON') {
                $tabledata[$i]->payment_status = $this->reportsModel->check_not_part_paid($tabledata[$i]->pymnt_due_id);
            }
        }
        $this->load->helper('export_helper');
        $count_tabledata = count($tabledata);
        $excel_titles = array('Inv #', 'Course Dt.', 'Inv Dt.', 'Taxcode', 'Name', 'Discount', 'Subsidy', 'GST', 'Net Amt.', 'Status');
        $excel_data = array();
        for ($i = 0; $i < $count_tabledata; $i++) {
            $paid_arr = array('PAID' => 'Paid', 'PARTPAID' => 'Part Paid', 'NOTPAID' => 'Not Paid');
            $paid_sty_arr = array('PAID' => 'color:green;', 'PARTPAID' => 'color:red;', 'NOTPAID' => 'color:red;');
            if ($tabledata[$i]->enrolment_mode == 'SELF') {
                $taxcode = $this->mask_format($tabledata[$i]->tax_code);
                $name = $tabledata[$i]->first_name . ' ' . $tabledata[$i]->last_name;
                $status = $paid_arr[$tabledata[$i]->payment_status];
            } else {
                if ($tabledata[$i]->company_id[0] == 'T') {
                    $tenant_details = fetch_tenant_details($tabledata[$i]->company_id);
                    $taxcode = $tenant_details->tenant_name;
                    $name = $tenant_details->tenant_name . ' (Company)';
                } else {
                    $taxcode = $tabledata[$i]->comp_regist_num;
                    $name = $tabledata[$i]->company_name . ' (Company)';
                }
                $status = ($tabledata[$i]->payment_status > 0) ? 'Part Paid/Not Paid' : 'Paid';
            }
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->course_date));
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->inv_date));
            $excel_data[$i][] = $taxcode;
            $excel_data[$i][] = $name;
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_discnt, 2, '.', '') . ' SGD';
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_subsdy, 2, '.', '') . ' SGD';
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_gst, 2, '.', '') . ' SGD';
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_amount, 2, '.', '') . ' SGD';
            $excel_data[$i][] = $status;
        }
        if (empty($start_date) && empty($end_date)) {
            $period = ' for ' . date('F d Y, l');
        } else {
            $period = 'for the period';
            if (!empty($start_date))
                $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
            if (!empty($end_date))
                $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
        }
        $excel_filename = 'invlice_list.xls';
        $excel_sheetname = 'Invoice List';
        $excel_main_heading = 'Accounting Reports - Invoice List & Search' . $period;

        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    /**
     * This method creates the PDF Export format for certificates Report
     * @return type
     */
    public function invoice_export_PDF() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $tenant_details = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $query = $this->reportsModel->get_all_invoice_data($tenant_id, NULL, NULL, $field, $order_by, NULL, NULL, NULL, NULL, -1);

        $this->load->helper('pdf_reports_helper');
        return invoice_report_PDF($query, $tenant_details);
    }

    /**
     * Payments Due - Report
     */
    public function payments_due() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $executive = array('' => 'Select');
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }
        $data['executive'] = $executive;
        $salesexec = $this->input->get('salesexec');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (!empty($_GET)) {
            $totalrows = $this->reportsModel->get_payment_due_count($tenant_id, $salesexec, $start_date, $end_date);
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'reports_finance/payments_due/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = ($pageno * $records_per_page);
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $tabledata = $this->reportsModel->get_payment_due($tenant_id, $records_per_page, $offset, $field, $order_by, $salesexec, $start_date, $end_date);
            $tabledata_count = count($tabledata);
            $invs = array();
            for ($i = 0; $i < $tabledata_count; $i++) {
                $invs[] = $tabledata[$i]->invoice_id;
            }
            $tb_recd = $this->reportsModel->get_payment_recd_sum($invs);
            $tabledatarecd = array();
            for ($i = 0; $i < count($tb_recd); $i++) {
                $tabledatarecd[$tb_recd[$i]->invoice_id] = $tb_recd[$i]->amount_recd;
            }
            $tb_refund = $this->reportsModel->get_payment_refund_sum($invs);
            $tabledatarefund = array();
            for ($i = 0; $i < count($tb_refund); $i++) {
                $tabledatarefund[$tb_refund[$i]->invoice_id] = $tb_refund[$i]->amount_refund;
            }
            $data['tabledatarefund'] = $tabledatarefund;
            $data['tabledatarecd'] = $tabledatarecd;
            $data['tabledata'] = $tabledata;
            $data['sort_order'] = $order_by;
        }
        $data['controllerurl'] = 'reports_finance/payments_due/';
        $data['sort_link'] = $sort_link = "salesexec=" . $this->input->get('salesexec') . "&start_date=" . $this->input->get('start_date') . "&end_date=" . $this->input->get('end_date');
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['page_title'] = 'Accounting Reports - Payment Due';
        $data['main_content'] = 'reports/payments_due';
        $this->load->view('layout', $data);
    }

    /*
     * Payments Due Report - Export to XLS
     */

    public function payments_due_export_xls() {
        $tenant_id = $this->tenant_id;
        $executive = array('' => 'Select');
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }
        $salesexec = $this->input->get('salesexec');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_payment_due($tenant_id, $records_per_page, $offset, $field, $order_by, $salesexec, $start_date, $end_date);
        $tabledata_count = count($tabledata);
        $invs = array();
        for ($i = 0; $i < $tabledata_count; $i++) {
            $invs[] = $tabledata[$i]->invoice_id;
        }
        $tb_recd = $this->reportsModel->get_payment_recd_sum($invs);
        $tabledatarecd = array();
        for ($i = 0; $i < count($tb_recd); $i++) {
            $tabledatarecd[$tb_recd[$i]->invoice_id] = $tb_recd[$i]->amount_recd;
        }
        $tb_refund = $this->reportsModel->get_payment_refund_sum($invs);
        $tabledatarefund = array();
        for ($i = 0; $i < count($tb_refund); $i++) {
            $tabledatarefund[$tb_refund[$i]->invoice_id] = $tb_refund[$i]->amount_refund;
        }
        $this->load->helper('export_helper');
        $count_tabledata = count($tabledata);
        $excel_titles = array('Inv #', 'Inv Dt.', 'Inv Amt.', 'Amt. Recd.', 'Amt. Due.', 'Amt. Refd.', 'Course - Class', 'Name', 'Taxcode');
        $excel_data = array();
        for ($i = 0; $i < $count_tabledata; $i++) {
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->inv_date));
            $excel_data[$i][] = '$ ' . $total_inv = number_format($tabledata[$i]->total_inv_amount, 2, '.', '');
            $excel_data[$i][] = '$ ' . $total_recd = number_format($tabledatarecd[$tabledata[$i]->invoice_id], 2, '.', '');
            $excel_data[$i][] = '$ ' . number_format($total_inv - $total_recd, 2, '.', '');
            $excel_data[$i][] = '$ ' . number_format($tabledatarefund[$tabledata[$i]->invoice_id], 2, '.', '');
            $excel_data[$i][] = $tabledata[$i]->crse_name . ' - ' . $tabledata[$i]->class_name;
            if ($tabledata[$i]->inv_type == 'INVINDV') {
                $taxcode = $this->mask_format($tabledata[$i]->tax_code);
                $name = $tabledata[$i]->first_name . ' ' . $tabledata[$i]->last_name;
            } else {
                if ($tabledata[$i]->company_id[0] == 'T') {
                    $tenant_details = fetch_tenant_details($tabledata[$i]->company_id);
                    $taxcode = $tenant_details->tenant_name;
                    $name = $tenant_details->tenant_name . ' (Company)';
                } else {
                    $taxcode = $tabledata[$i]->comp_regist_num;
                    $name = $tabledata[$i]->company_name . ' (Company)';
                }
            }
            $excel_data[$i][] = $name;
            $excel_data[$i][] = $taxcode;
        }
        if (!empty($tabledata)) {
            if (empty($start_date) && empty($end_date)) {
                $period = ' for ' . date('F d Y, l');
            } else {
                $period = 'for the period';
                if (!empty($start_date))
                    $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
                if (!empty($end_date))
                    $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
            }
            if (!empty($salesexec)) {
                $period .= ' \'' . $executive[$salesexec] . '\'';
            }
        }
        $excel_filename = 'payments_due.xls';
        $excel_sheetname = 'Payments Due';
        $excel_main_heading = 'Accounting Reports - Payments Due' . $period;
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    /**
     * Payments due reports Export to PDF
     */
    public function payments_due_report_pdf() {
        $tenant_id = $this->tenant_id;
        $executive = array('' => 'Select');
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }
        $salesexec = $this->input->get('salesexec');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_payment_due($tenant_id, NULL, NULL, $field, $order_by, $salesexec, $start_date, $end_date);
        $tabledata_count = count($tabledata);
        $invs = array();
        for ($i = 0; $i < $tabledata_count; $i++) {
            $invs[] = $tabledata[$i]->invoice_id;
        }
        $tb_recd = $this->reportsModel->get_payment_recd_sum($invs);
        $tabledatarecd = array();
        for ($i = 0; $i < count($tb_recd); $i++) {
            $tabledatarecd[$tb_recd[$i]->invoice_id] = $tb_recd[$i]->amount_recd;
        }
        $tb_refund = $this->reportsModel->get_payment_refund_sum($invs);
        $tabledatarefund = array();
        for ($i = 0; $i < count($tb_refund); $i++) {
            $tabledatarefund[$tb_refund[$i]->invoice_id] = $tb_refund[$i]->amount_refund;
        }
        $tenant_details = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $this->load->helper('pdf_reports_helper');
        return payments_due_report_pdf($tabledatarefund, $tabledatarecd, $tabledata, $tenant_details, $executive);
    }

    /**
     * Refunds Report
     */
    public function refunds() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $data['companies'] = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        $data['invoices'] = $this->get_paid_invoice(1);
        $company = $this->input->get('company');
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (!empty($_GET)) {
            $totalrows = $this->reportsModel->get_refund_paid_count($tenant_id, $company, $invoice_id, $start_date, $end_date);
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'reports_finance/refunds/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = ($pageno * $records_per_page);
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'inv.invoice_id';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $tabledata = $this->reportsModel->get_refund_paid($tenant_id, $records_per_page, $offset, $field, $order_by, $company, $invoice_id, $start_date, $end_date);
            $invs = array();
            foreach ($tabledata as $k => $row) {
                if ($row->refnd_reason == 'OTHERS') {
                    $tabledata[$k]->refnd_reason = "Others (" . $row->refnd_reason_ot . ")";
                } else {
                    $tabledata[$k]->refnd_reason = $this->courseModel->get_metadata_on_parameter_id($row->refnd_reason);
                }
                if ($row->refund_type == 'INDV') {
                    $invs[$row->invoice_id] = $row->invoice_id;
                }
            }
            $tb_user = $this->reportsModel->get_refund_paid_user($invs);
            $tableuser = array();
            foreach ($tb_user as $row) {
                $tableuser[$row->invoice_id]['name'] = $row->first_name . ' ' . $row->last_name;
                $tableuser[$row->invoice_id]['taxcode'] = $row->tax_code;
            }
            $data['tableuser'] = $tableuser;
            $data['tabledata'] = $tabledata;
            $data['sort_order'] = $order_by;
        }
        $data['controllerurl'] = 'reports_finance/refunds/';
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "search_select=" . $this->input->get('search_select') . "&company=" . $this->input->get('company') . "&invoice_id=" . $this->input->get('invoice_id') . "&start_date=" . $this->input->get('start_date') . "&end_date=" . $this->input->get('end_date');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['page_title'] = 'Accounting Reports - Refunds';
        $data['main_content'] = 'reports/refunds';
        $this->load->view('layout', $data);
    }

    /*
     * Export to XLS - Refunds Report
     */

    public function refunds_export_xls() {
        $tenant_id = $this->tenant_id;
        $company = $this->input->get('company');
        $companies = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        $company_arr = array();
        foreach ($companies as $row) {
            $company_arr[$row->company_id] = $row->company_name;
        }
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $offset = ($pageno * $records_per_page);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'inv.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_refund_paid($tenant_id, $records_per_page, $offset, $field, $order_by, $company, $invoice_id, $start_date, $end_date);
        $invs = array();
        foreach ($tabledata as $k => $row) {
            if ($row->refnd_reason == 'OTHERS') {
                $tabledata[$k]->refnd_reason = "Others (" . $row->refnd_reason_ot . ")";
            } else {
                $tabledata[$k]->refnd_reason = $this->courseModel->get_metadata_on_parameter_id($row->refnd_reason);
            }
            if ($row->refund_type == 'INDV') {
                $invs[$row->invoice_id] = $row->invoice_id;
            }
        }
        $tb_user = $this->reportsModel->get_refund_paid_user($invs);
        $tableuser = array();
        foreach ($tb_user as $row) {
            $tableuser[$row->invoice_id]['name'] = $row->first_name . ' ' . $row->last_name;
            $tableuser[$row->invoice_id]['taxcode'] = $this->mask_format($row->tax_code);
        }
        $this->load->helper('export_helper');
        $count_tabledata = count($tabledata);
        $excel_titles = array('Inv #', 'Taxcode', 'Name', 'Refund Dt.', 'Refund Amt.', 'Reason', 'Refunded By');
        $excel_data = array();
        for ($i = 0; $i < $count_tabledata; $i++) {
            $k = $tabledata[$i]->invoice_id;
            if ($tabledata[$i]->refund_type == 'INDV') {
                $taxcode = $tableuser[$k]['taxcode'];
                $name = $tableuser[$k]['name'];
            } else {
                if ($tabledata[$i]->company_id[0] == 'T') {
                    $tenant_details = fetch_tenant_details($tabledata[$i]->company_id);
                    $taxcode = $tenant_details->tenant_name;
                    $name = $tenant_details->tenant_name . ' (Company)';
                } else {
                    $taxcode = $tabledata[$i]->comp_regist_num;
                    $name = $tabledata[$i]->company_name . ' (Company)';
                }
            }
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = $taxcode;
            $excel_data[$i][] = $name;
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->refund_on));
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->amount_refund, 2, '.', '');
            $excel_data[$i][] = $tabledata[$i]->refnd_reason;
            $excel_data[$i][] = $tabledata[$i]->first_name . " " . $tabledata[$i]->last_name;
        }
        if (!empty($tabledata)) {
            if (empty($start_date) && empty($end_date)) {
                $period = ' for ' . date('F d Y, l');
            } else {
                $period = 'for the period';
                if (!empty($start_date))
                    $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
                if (!empty($end_date))
                    $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
            }
            if (!empty($company)) {
                $period .= ' \'' . $company_arr[$company] . '\'';
            }
            if (!empty($invoice_id)) {
                $period .= ' -  \' Invoice No.: ' . $invoice_id . '\'';
            }
        }
        $excel_filename = 'refunds.xls';
        $excel_sheetname = 'Refunds';
        $excel_main_heading = 'Accounting Reports - Refunds' . $period;
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    /**
     * Refunds Report - Export to PDF
     */
    public function refund_report_pdf() {
        $tenant_id = $this->tenant_id;
        $company = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        $companies = array();
        foreach ($company as $row) {
            $companies[$row->company_id] = $row->company_name;
        }
        $company = $this->input->get('company');
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'inv.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_refund_paid($tenant_id, NULL, NULL, $field, $order_by, $company, $invoice_id, $start_date, $end_date);
        $invs = array();
        foreach ($tabledata as $k => $row) {
            if ($row->refnd_reason == 'OTHERS') {
                $tabledata[$k]->refnd_reason = "Others (" . $row->refnd_reason_ot . ")";
            } else {
                $tabledata[$k]->refnd_reason = $this->courseModel->get_metadata_on_parameter_id($row->refnd_reason);
            }
            if ($row->refund_type == 'INDV') {
                $invs[$row->invoice_id] = $row->invoice_id;
            }
        }
        $tb_user = $this->reportsModel->get_refund_paid_user($invs);
        $tableuser = array();
        foreach ($tb_user as $row) {
            $tableuser[$row->invoice_id]['name'] = $row->first_name . ' ' . $row->last_name;
            $tableuser[$row->invoice_id]['taxcode'] = $this->mask_format($row->tax_code);
        }
        $tenant_details = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $this->load->helper('pdf_reports_helper');
        return refunds_report_pdf($tabledata, $tableuser, $tenant_details, $companies);
    }

    /**
     * Payments Recd - Report
     */
    public function payments() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $data['companies'] = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        //$data['invoices'] = $this->get_paid_invoice(1);
        $company = $this->input->get('company');
        $invoice_id = $this->input->get('invoice_no_id') ? $this->input->get('invoice_no_id') : $this->input->get('invoice_id'); //added by shubhranshu
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (!empty($_GET)) {
            $totalrows = $this->reportsModel->get_payment_recd_count($company, $invoice_id, $start_date, $end_date);
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'reports_finance/payments/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = ($pageno * $records_per_page);
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $tabledata = $this->reportsModel->get_payment_recd($records_per_page, $offset, $field, $order_by, $company, $invoice_id, $start_date, $end_date);
            $tabledata_count = count($tabledata);
            $pids = array();
            for ($i = 0; $i < $tabledata_count; $i++) {
                $pids[] = $tabledata[$i]->pymnt_due_id;
            }
            if (!empty($pids)) {
                $tb_extra = $this->reportsModel->get_payment_recd_trainee_company($pids);
                $tabledataextra = array();
                for ($i = 0; $i < count($tb_extra); $i++) {
                    $tabledataextra[$tb_extra[$i]->pymnt_due_id] = $tb_extra[$i];
                }
                $data['tabledataextra'] = $tabledataextra;
            }


            $data['tabledata'] = $tabledata;
            $data['sort_order'] = $order_by;
        }
        $data['controllerurl'] = 'reports_finance/payments/';
        $this->load->helper('pagination');
        $data['sort_link'] = $sort_link = "search_select=" . $this->input->get('search_select') . "&company=" . $this->input->get('company') . "&invoice_id=" . $this->input->get('invoice_id') . "&start_date=" . $this->input->get('start_date') . "&end_date=" . $this->input->get('end_date');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['page_title'] = 'Accounting Reports - Payments Received';
        $data['main_content'] = 'reports/payments';
        $this->load->view('layout', $data);
    }

    /**
     * Payments Received Report - Export to PDF
     */
    public function payments_received_reports_pdf() {
        ini_set('memory_limit', '256M');
        $tenant_id = $this->tenant_id;
        $company = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        $companies = array();
        foreach ($company as $row) {
            $companies[$row->company_id] = $row->company_name;
        }
        $company = $this->input->get('company');
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_payment_recd($records_per_page, NULL, NULL, $order_by, $company, $invoice_id, $start_date, $end_date);
        $tabledata_count = count($tabledata);
        $pids = array();
        for ($i = 0; $i < $tabledata_count; $i++) {
            $pids[] = $tabledata[$i]->pymnt_due_id;
        }
        $tb_extra = $this->reportsModel->get_payment_recd_trainee_company($pids);
        $tabledataextra = array();
        for ($i = 0; $i < count($tb_extra); $i++) {
            $tabledataextra[$tb_extra[$i]->pymnt_due_id] = $tb_extra[$i];
        }
        $tenant_details = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $this->load->helper('pdf_reports_helper');
        return payments_recd_report_pdf($tabledataextra, $tabledata, $tenant_details, $companies);
    }

    /*
     * Payments Received Report - Export to XLS
     */

    public function payments_export_xls() {
        ini_set('memory_limit', '-1');
        $tenant_id = $this->tenant_id;
        $company = $this->input->get('company');
        $companies = $this->classTraineeModel->get_company_for_paidinvoice($tenant_id);
        $company_arr = array();
        foreach ($companies as $row) {
            $company_arr[$row->company_id] = $row->company_name;
        }
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ei.invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_payment_recd($records_per_page, $offset, $field, $order_by, $company, $invoice_id, $start_date, $end_date);

        $tabledata_count = count($tabledata);
        $pids = array();
        for ($i = 0; $i < $tabledata_count; $i++) {
            $pids[] = $tabledata[$i]->pymnt_due_id;
        }
        $tb_extra = $this->reportsModel->get_payment_recd_trainee_company($pids);
        $tabledataextra = array();
        for ($i = 0; $i < count($tb_extra); $i++) {
            $tabledataextra[$tb_extra[$i]->pymnt_due_id] = $tb_extra[$i];
        }
        $this->load->helper('export_helper');
        $count_tabledata = count($tabledata);
        $excel_titles = array('Inv #', 'Inv Dt.', 'Inv Amt.', 'Recd. On', 'Amt. Recd.', 'Course - Class', 'Name', 'Taxcode');
        $excel_data = array();
        for ($i = 0; $i < $count_tabledata; $i++) {
            $k = $tabledata[$i]->pymnt_due_id;
            if ($tabledata[$i]->inv_type == 'INVINDV') {
                $taxcode = $tabledataextra[$k]->tax_code;
                $name = $tabledataextra[$k]->first_name . ' ' . $tabledataextra[$k]->last_name;
            } else {
                if ($tabledata[$i]->company_id[0] == 'T') {
                    $tenant_details = fetch_tenant_details($tabledata[$i]->company_id);
                    $name = $tenant_details->tenant_name . ' (Company)';
                    $taxcode = $tenant_details->tenant_name;
                } else {
                    $taxcode = $tabledataextra[$k]->comp_regist_num;
                    $name = $tabledataextra[$k]->company_name . ' (Company)';
                }
            }
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->inv_date));
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_amount, 2, '.', '');
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->recd_on));
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->amount_recd, 2, '.', '');
            $excel_data[$i][] = $tabledataextra[$k]->crse_name . ' - ' . $tabledataextra[$k]->class_name;
            $excel_data[$i][] = $name;
            $excel_data[$i][] = $this->mask_format($taxcode);
        }
        if (!empty($tabledata)) {
            if (empty($start_date) && empty($end_date)) {
                $period = ' for ' . date('F d Y, l');
            } else {
                $period = 'for the period';
                if (!empty($start_date))
                    $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
                if (!empty($end_date))
                    $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
            }
            if (!empty($company)) {
                $period .= ' \'' . $company_arr[$company] . '\'';
            }
            if (!empty($invoice_id)) {
                $period .= ' -  \' Invoice No.: ' . $invoice_id . '\'';
            }
        }
        $excel_filename = 'payments_received.xls';
        $excel_sheetname = 'Payments Received';
        $excel_main_heading = 'Accounting Reports - Payments Received' . $period;

        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    /**
     * Sales Commission Report
     */
    public function sales() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $executive = array('' => 'Select');
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }
        $data['executive'] = $executive;

        // $non_executive = array('' => 'Select');
        $non_executive = $this->reportsModel->get_non_sales_executive($tenant_id);
        $data['non_executive'] = $non_executive;

        $sales_exec = $this->input->get('sales_exec');
        $non_sales_exec = $this->input->get('non_sales_exec');
        if (!empty($sales_exec)) {
            $sales_exec = $sales_exec;
        } else if (!empty($non_sales_exec)) {
            $sales_exec = $non_sales_exec;
        }

        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'reports_finance/sales/';
        $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
        $offset = ($pageno * $records_per_page);
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'scd.sales_exec_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $totalrows = $this->reportsModel->get_sales_comm('count', $tenant_id, $records_per_page, $offset, $field, $order_by, $sales_exec);
        $sales_comm_result = $this->reportsModel->get_sales_comm('data', $tenant_id, $records_per_page, $offset, $field, $order_by, $sales_exec);
        $comm_due_periods = $this->reportsModel->get_comm_due_period($tenant_id, $sales_exec);
        $year_arr = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 =>
            'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
            10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $periods = array();
        foreach ($comm_due_periods as $val) {
            $periods[$val->sales_exec_id] .= $year_arr[$val->comm_period_mth] . ' ' . $val->comm_period_yr . ", ";
        }
        $sort_link = 'sales_exec=' . $sales_exec;
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        $data['tabledata'] = $sales_comm_result;
        $data['perioddata'] = $periods;
        $data['page_title'] = 'Accounting Reports - Sales Assignment & Commission';
        $data['main_content'] = 'reports/sales';
        $this->load->view('layout', $data);
    }

    /**
      Sales Commission Report - Export to PDF
     */
    public function report_sales_pdf() {
        $tenant_id = $this->tenant_id;
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }

        $sales_exec = $this->input->get('sales_exec');
        $non_sales_exec = $this->input->get('non_sales_exec');
        if (!empty($sales_exec)) {
            $sales_exec = $sales_exec;
        } else if (!empty($non_sales_exec)) {
            $sales_exec = $non_sales_exec;
        }



        $sales_comm_result = $this->reportsModel->get_sales_comm('pdf', $tenant_id, $records_per_page, $offset, $field, $order_by, $sales_exec);
        $comm_due_periods = $this->reportsModel->get_comm_due_period($tenant_id, $sales_exec);
        $year_arr = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 =>
            'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
            10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $periods = array();
        foreach ($comm_due_periods as $val) {
            $periods[$val->sales_exec_id] .= $year_arr[$val->comm_period_mth] . ' ' . $val->comm_period_yr . ", ";
        }
        $tenant_details = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $this->load->helper('pdf_reports_helper');
        return sales_reports_pdf($sales_comm_result, $periods, $tenant_details, $executive);
    }

    /*
     * Sales Commission Report - Export to XLS
     */

    public function sales_export_xls() {
        $tenant_id = $this->tenant_id;
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }

        $sales_exec = $this->input->get('sales_exec');

        $non_sales_exec = $this->input->get('non_sales_exec');
        if (!empty($sales_exec)) {
            $sales_exec = $sales_exec;
        } else if (!empty($non_sales_exec)) {
            $sales_exec = $non_sales_exec;
        }


        $sales_comm_result = $this->reportsModel->get_sales_comm('excel', $tenant_id, $records_per_page, $offset, $field, $order_by, $sales_exec);
        $comm_due_periods = $this->reportsModel->get_comm_due_period($tenant_id, $sales_exec);
        $year_arr = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 =>
            'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
            10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $periods = array();
        foreach ($comm_due_periods as $val) {
            $periods[$val->sales_exec_id] .= $year_arr[$val->comm_period_mth] . ' ' . $val->comm_period_yr . ", ";
        }
        $this->load->helper('export_helper');
        $excel_titles = array('Sales Executive', 'Total Comm.', 'Total Recd.', 'Total Due', 'Due Period');
        $excel_data = array();
        $i = 0;
        foreach ($sales_comm_result as $k => $row) {
            $total = number_format($row->comm_due_amount, 2, '.', '');
            $paid = number_format($row->comm_paid_amount, 2, '.', '');
            $due = number_format($total - $paid, 2, '.', '');
            $excel_data[$i][] = $row->first_name . " " . $row->last_name;
            $excel_data[$i][] = '$ ' . $total . ' SGD';
            $excel_data[$i][] = '$ ' . $paid . ' SGD';
            $excel_data[$i][] = '$ ' . $due . ' SGD';
            $excel_data[$i][] = rtrim($periods[$row->sales_exec_id], ", ");
            $i++;
        }
        $excel_filename = 'sales.xls';
        $excel_sheetname = 'Sales';
        $excel_main_heading = 'Sales Commission report for ' . date('F d Y, l');
        if ($sales_exec != '') {
            $excel_main_heading .= " '" . $executive[$sales_exec] . "'";
        }
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    /**
     * This method Gets the Paid Invoices from - enrol_invoice
     * date: 28 aug 2014
     */
    public function get_paid_invoice($is_json = 0) {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classTraineeModel->get_paid_indi_company_invoice($this->tenant_id, $query_string);
        if ($result) {
            foreach ($result as $row) {
                if ($row->inv_type == 'INVINDV') {
                    $name = '(Name: ' . $row->first_name . ' ' . $row->last_name . ', Taxcode: ' . $row->tax_code . ')';
                } else {
                    $name = '(Name: ' . $row->company_name . ', Taxcode: ' . $row->comp_regist_num . ')';
                }
                $matches[] = array(
                    'key' => $row->invoice_id,
                    'label' => $row->invoice_id . $name,
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
     * This function  to get company autocomplete
     */
    public function get_company_json() {
        $company_arr = array();
        $company = $this->input->post('q');
        if (!empty($company)) {
            $company_arr = common_companies_autocomplete($company);
        }
        echo json_encode($company_arr);
        exit();
    }

    public function get_invoice_json1() {
        $tenant_id = $this->tenant_id;
        $invoice_arr = array();
        $invoice = $this->input->post('q');
        if (!empty($invoice)) {
            $invoice_arr = $this->reportsModel->invoice_autocomplete($tenant_id, $invoice);
        }

        echo json_encode($invoice_arr);
    }

    public function get_prev_invoice_json() {
        $tenant_id = $this->tenant_id;
        $prev_invoice_arr = array();
        $prev_invoice = $this->input->post('q');
        if (!empty($prev_invoice)) {
            $prev_invoice_arr = $this->reportsModel->prev_invoice_autocomplete($tenant_id, $prev_invoice);
        }

        echo json_encode($prev_invoice_arr);
    }

    /**
     * CR 03
     * This method generates the invoice audit trail report based on the search parameter passed to it
     */
    public function invoice_audit_trail() {
        //$this->output->enable_profiler(true);
        $data['sideMenuData'] = fetch_non_main_page_content();

        //Read page parameter to display report
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $company_id = $this->input->get('company_id');

        if ($invoice_id != '' || $company_id != '' || ($start_date != '' && $end_date != '')) {
            //Build required values to display invoice audit report in table format
            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'reports_finance/invoice_audit_trail/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = (($pageno - 1) * $records_per_page);
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'invoice_id';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
            $tabledata = $this->reportsModel->get_invoice_audit_trail($tenant_id, $records_per_page, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $invoice_id, $company_id);
            $totalrows = $this->reportsModel->get_invoice_audit_trail_rows($tenant_id, $records_per_page, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $invoice_id, $company_id);

            //echo $totalrows;exit;
            $data['tabledata'] = $tabledata;
            $data['sort_order'] = $order_by;
            $data['controllerurl'] = 'reports_finance/invoice_audit_trail/';
            $this->load->helper('pagination');
            $data['sort_link'] = $sort_link = "start_date=" . $this->input->get('start_date') . "&end_date=" . $this->input->get('end_date') . "&invoice_id=" . $this->input->get('invoice_id');
            $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by . '&' . $sort_link);
        }
        //Render audit trail page
        $data['page_title'] = 'Accounting Reports - Invoice Audit Trail';
        $data['main_content'] = 'reports/invoice_audit_trail';
        $this->load->view('layout', $data);
    }

    //added  by shubhranshu for invoice audittrail auto complete
    public function get_invoice_audittrail_json() {
        $invoice_arr = array();
        $invoice_no = $this->input->post('q');
        if (!empty($invoice_no)) {
            $invoice_arr = common_invoice_audittrail_autocomplete($this->tenant_id, $invoice_no);
        }
        echo json_encode($invoice_arr);
        exit();
    }

    /**
     * This function  to get invoice autocomplete
     * Author: CR03
     * Date: 08 Apr 2015
     */
    public function get_invoice_json() {
        $invoice_arr = array();
        $invoice_no = $this->input->post('q');
        if (!empty($invoice_no)) {
            $invoice_arr = common_invoice_autocomplete($this->tenant_id, $invoice_no);
        }
        echo json_encode($invoice_arr);
        exit();
    }

    /* shubhranshu  start: replace nric first 5 character with mas */

    function mask_format($nric) {
        if (is_numeric($nric) == 1) {
            return $nric;
        } else {
            $new_nric = substr_replace($nric, 'XXXXX', 0, 5);
            //$new_nric = substr_replace($nric,'XXXX',5);        
            return $new_nric;
        }
    }

    /* shubhranshu end */
    /*
     * Invoice audit trail - Export to XLS
     * Author   : CR03
     * Date     : 08 Apr 2015
     */

    public function invoice_audit_trail_export_xls() {

        //Read page parameter to display report
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company_id = $this->input->get('company_id');

        //Build required values to display invoice audit report in table format
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $tabledata = $this->reportsModel->get_invoice_audit_trail($tenant_id, $records_per_page, $offset, $field, $order_by, $payment_status, $start_date, $end_date, $invoice_id, $company_id);

        //EXPORT PART
        $this->load->helper('export_helper');
        $count_tabledata = count($tabledata);
        $excel_titles = array('Inv #', 'Inv Dt.', 'Inv Type', 'Taxcode', 'Discount', 'Subsidy', 'GST', 'Net Amt.', 'Prev. Inv. Number', 'Next Inv. Number');
        $excel_data = array();
        for ($i = 0; $i < $count_tabledata; $i++) {
            $paid_arr = array('PAID' => 'Paid', 'PARTPAID' => 'Part Paid', 'NOTPAID' => 'Not Paid');
            $paid_sty_arr = array('PAID' => 'color:green;', 'PARTPAID' => 'color:red;', 'NOTPAID' => 'color:red;');
            if ($tabledata[$i]->enrolment_mode == 'SELF') {
                $taxcode = $tabledata[$i]->tax_code;
                $name = $tabledata[$i]->first_name . ' ' . $tabledata[$i]->last_name;
                $status = $paid_arr[$tabledata[$i]->payment_status];
            } else {
                // Modified by dummy for internal staff enroll on 01 Dec 2014.
                if ($tabledata[$i]->company_id[0] == 'T') {
                    $tenant_details = fetch_tenant_details($tabledata[$i]->company_id);
                    $taxcode = $tenant_details->tenant_name;
                    $name = $tenant_details->tenant_name;
                } else {
                    $taxcode = $tabledata[$i]->comp_regist_num;
                    $name = $tabledata[$i]->company_name;
                }
                $status = ($tabledata[$i]->payment_status > 0) ? 'Part Paid/Not Paid' : 'Paid';
            }
            $inv_type1 = $tabledata[$i]->inv_type1 . ' (' . $name . ')';
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = date('d/m/Y', strtotime($tabledata[$i]->inv_date));
            $excel_data[$i][] = $inv_type1;
            $excel_data[$i][] = $this->mask_format($taxcode);
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_discnt, 2, '.', '') . ' SGD';
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_subsdy, 2, '.', '') . ' SGD';
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_gst, 2, '.', '') . ' SGD';
            $excel_data[$i][] = '$ ' . number_format($tabledata[$i]->total_inv_amount, 2, '.', '') . ' SGD';
            $excel_data[$i][] = $tabledata[$i]->invoice_id;
            $excel_data[$i][] = $tabledata[$i]->regen_inv_id;
        }
        if (empty($start_date) && empty($end_date)) {
            $period = ' for ' . date('F d Y, l');
        } else {
            $period = 'for the period';
            if (!empty($start_date))
                $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
            if (!empty($end_date))
                $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
        }
        $excel_filename = 'invlice_audit_list.xls';
        $excel_sheetname = 'Invoice Audit Trail';
        $excel_main_heading = 'Accounting Reports - Invoice Audit Trail' . $period;
        export_page_fields($excel_titles, $excel_data, $excel_filename, $excel_sheetname, $excel_main_heading);
    }

    /**
     * This method creates the PDF Export format for Invoice Audit Trail Report
     * Author: CR03
     * @return type
     */
    public function invoice_audit_trail_export_PDF() {
        //Read page parameter to display report
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $invoice_id = $this->input->get('invoice_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $company_id = $this->input->get('company_id');

        $tenant_details = $this->classTraineeModel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->courseModel->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'invoice_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $query = $this->reportsModel->get_invoice_audit_trail($tenant_id, NULL, NULL, $field, $order_by, $payment_status, $start_date, $end_date, $invoice_id, $company_id);
        $this->load->helper('pdf_reports_helper');
        return invoice_audit_trail_report_PDF($query, $tenant_details);
    }

    /*  activity log code start */

    public function activity_log() {
        //$this->output->enable_profiler(true);
        ini_set('memory_limit', '256M');
        $data['sideMenuData'] = fetch_non_main_page_content();


        $tenant_id = $this->tenant_id;

        extract($_GET);

        $data['course_list'] = $course_list = $this->activitylog->get_course_list($tenant_id); // get all course

        if ($crs) {

            $course_classes = $this->activitylog->get_course_class($tenant_id, $crs);

            $data['classes'] = $course_classes;
        }

        $export_url = '';

        $sort_url = '';

        if (!empty($_GET)) {

            $export_url = '?';

            foreach ($_GET as $k => $v) {

                if (!empty($v)) {

                    $export_url .="$k=$v&";
                }

                if ($k != 'f' && $k != 'o') {

                    if (!empty($v)) {

                        $sort_url .="$k=$v&";
                    }
                }
            }
        }

        $export_url = rtrim($export_url, '&');

        $sort_url = rtrim($sort_url, '&');

        $data['export_url'] = $export_url;

        $data['sort_url'] = '?' . $sort_url;

        $module = ($this->input->get('module')) ? $this->input->get('module') : '';

        $user_id = ($this->input->get('user_id')) ? $this->input->get('user_id') : '';
        
        $crse_id = ($this->input->get('crse_id')) ? $this->input->get('crse_id') : '';

        $com_id = ($this->input->get('com_id')) ? $this->input->get('com_id') : '';

        $invid = ($this->input->get('invid')) ? $this->input->get('invid') : '';

        $inv_taxcode = ($this->input->get('inv_taxcode')) ? $this->input->get('inv_taxcode') : '';

        $crs = ($this->input->get('crs')) ? $this->input->get('crs') : '';

        $cls_id = ($this->input->get('cls_id')) ? $this->input->get('cls_id') : '';

        $cls_name = ($this->input->get('cls_name')) ? $this->input->get('cls_name') : '';

        $account_type = ($this->input->get('account_type')) ? $this->input->get('account_type') : '';

        $pass = ($this->input->get('pass')) ? $this->input->get('pass') : '';

        $field = ($this->input->get('f')) ? $this->input->get('f') : 'at.trigger_datetime';

        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'desc';



        $records_per_page = RECORDS_PER_PAGE;

        $baseurl = base_url() . 'reports_finance/activity_log/';

        $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;



        $offset = ($pageno * $records_per_page);

        $data['tenant'] = $tenant_id;
        
        $data['err']='Choose Filter To Display The Data';

        if (!empty($module)) {//added by shubhranshu due to memory limit issue
            
            $this->db->cache_on();
            $tabledata = $this->activitylog->get_activity_list_by_tenant_id($tenant_id, $records_per_page, $offset, $field, $order_by, $module, $user_id, $com_id, $invid, $inv_taxcode, $crs, $cls_id, $cls_name, $account_type, $pass,$crse_id);


            //$tabledata = $this->classtraineemodel->list_all_classtrainee_by_tenant_id($tenant_id, $records_per_page, $offset, $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);

            $totalrows = $this->activitylog->get_activity_log_count_by_tenant_id($tenant_id, $module, $user_id, $com_id, $invid, $user_id, $inv_taxcode, $crs, $cls_id, $cls_name, $account_type, $pass,$crse_id);
        
        
            $this->db->cache_off();

            $data['tabledata'] = $tabledata;

            $data['err']='No Activity Available';
        
        }

        $data['sort_order'] = $order_by;

        $data['controllerurl'] = 'reports_finance/activity_log/';

        $this->load->helper('pagination');

        if ($sort_url) {

            $pag_sort = $order_by . '&' . $sort_url;
        } else {

            $pag_sort = $order_by;
        }

        $data['activity_module'] = $this->activitylog->get_module($tenant_id); // get all module name with activity


        $data['company_list'] = $this->activitylog->get_company_list($tenant_id); // get all company


        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $pag_sort);

        $data['page_title'] = 'Activity Log';


        $data['main_content'] = 'reports/activity';
        $this->load->view('layout', $data);
    }

    /* This function retrive all data for activity list */

    public function activity_log_view($id, $module, $act_on) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $res = $this->activitylog->get_activity_details($id, $module, $act_on);

        $data['res'] = $res;

        $data['page_title'] = 'Activity Log View';

        $data['main_content'] = 'reports/activity_log_view';

        $this->load->view('layout', $data);
    }

    public function get_internalstaff_name_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->internalstaff_name_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    public function get_companyname_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->company_name_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    public function get_coursename_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->course_name_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    public function get_invtaxcode_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->invtaxcode_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    public function get_inv_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->inv_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    public function get_password_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->password_list_autocomplete($query_string);

        print json_encode($result);

        exit;
    }

    public function get_course_class_name_autocomplete() {

        $query_string = htmlspecialchars($_GET['name_startsWith'], ENT_QUOTES, 'UTF-8');

        $query_string = trim($query_string);

        $result = $this->activitylog->course_class_list_autocomplete($query_string);



        print json_encode($result);

        exit;
    }

    public function get_course_class_name_json($course_id = '') {

        $user = $this->session->userdata('userDetails');

        $tenantId = $user->tenant_id;

        $courseId = $this->input->post('course_id');



        $course_classes = $this->activitylog->get_course_class($tenantId, $courseId);

        $classes_arr = array();

        foreach ($course_classes as $k => $v) {

            $classes_arr[] = array('key' => $k, 'value' => $v);
        }

        echo json_encode($classes_arr);

        exit;
    }

    /* activity log code end */
    
    public function sales_report(){
        ini_set("memory_limit","512M");
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $executive = array('' => 'Select');
        foreach ($this->reportsModel->get_sales_executive($tenant_id)->result() as $item) {
            $executive[$item->user_id] = $item->user_name;
        }
        $data['executive'] = $executive;
        if (!empty($_POST)) {
            $sales_executive_id = $this->input->post('sales_exec');
            $start = $this->input->post('start_date');
            $end = $this->input->post('end_date');
            $all_data = $this->reportsModel->salesrep($tenant_id,$sales_executive_id,$start,$end);
            $data['final_data']=$all_data;
        }
        if($this->input->post('start_date')=='' || $this->input->post('end_date')==''){
            $data['error']='Kindly Select The Date Range!!';
        }
        $data['page_title'] = 'Reports';
        $data['main_content'] = 'reports/sales_reports';
        $this->load->view('layout', $data);
    }
    ///added by shubhranshu to fetch sales report monthwise
    public function sales_report_export_xls() {

        //Read page parameter to display report
        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
        $sales_exec = $this->input->get('sales_exec');
        $start_date = $this->input->get('start');
        $end_date = $this->input->get('end');
        

        $tabledata = $this->reportsModel->get_sales_report_data_xls($tenant_id, $start_date, $end_date, $sales_exec);
        $this->load->helper('export_helper');
        
        export_sales_report_xls($tabledata);
    }
    ///added by shubhranshu to fetch sales report monthwise
    public function sales_summary_monthwise(){
        if (!empty($_POST)) {
            $yearVal = $this->input->post('yearVal');
            $monthVal = $this->input->post('monthVal');
            $tenant_id = $this->tenant_id;
            $data['result'] = $this->reportsModel->salessummary_monthwise($tenant_id,$yearVal,$monthVal);
            //print_r($data['result']);exit;
        }
        $data['sideMenuData'] = fetch_non_main_page_content(); 
        $data['page_title'] = 'Sales Summary Month Wise';
        $data['main_content'] = 'reports/sales_summary_monthwise';
        $data['export_url'] = '?yearVal=' . $yearVal.'&monthVal='.$monthVal;
        $this->load->view('layout', $data);
    }
    ///added by shubhranshu to fetch sales report monthwise
    public function export_sales_monthwise(){
        $tenant_id = $this->tenant_id;
        $yearVal = $this->input->get('yearVal');
        $monthVal = $this->input->get('monthVal');
        $result = $this->reportsModel->salessummary_monthwise($tenant_id,$yearVal,$monthVal);
        $this->load->helper('export_helper');
        export_tms_report_sales_monthwise($result);
    }
}
