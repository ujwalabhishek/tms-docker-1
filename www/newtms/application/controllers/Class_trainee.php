<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * This is the controller class for Class Trainee Use case features. 
 */

class Class_Trainee extends CI_Controller {

    private $user;

    public function __construct() {
        parent::__construct();
        $this->load->model('class_trainee_model', 'classtraineemodel');
        $this->load->model('manage_tenant_model', 'manage_tenant');
        $this->load->model('course_model', 'course');
        $this->load->model('class_model', 'class');
        $this->load->model('company_model', 'company');
        $this->load->model('reports_model', 'reportsModel');
        $this->load->helper('common');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values', 'meta');
        $this->load->model('trainee_model', 'traineemodel');
        $this->load->model('activity_log_model', 'activitylog');
        $this->user = $this->session->userdata('userDetails');
        $this->tenant_id = $this->session->userdata('userDetails')->tenant_id;
    }

    /*
     * This function loads the initial list view page for class trainee.
     */

    public function index() {
        //ini_set('max_execution_time', 0);
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        extract($_GET);
        $data['courses'] = $courses = $this->course->get_active_course_list_by_tenant($tenant_id, 'classTrainee');
        if ($course) {

            $course_classes = $this->class->get_course_class($tenant_id, $course, "", "", "classTrainee");
            $data['classes'] = $course_classes;
        }
        $export_url = '';
        $sort_url = '';
        $data['error_msg'] = 'Kindly apply filter to fetch the trainees'; ////ssp/////
        if (!empty($_GET)) { /// added by shubhranshu to remove the classtrainee list on 26/11/2018
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
            $data['error_msg'] = ''; ////ssp/////
            $export_url = rtrim($export_url, '&');
            $sort_url = rtrim($sort_url, '&');
            $data['export_url'] = $export_url;
            $data['sort_url'] = '?' . $sort_url;
            $course = ($this->input->get('course')) ? $this->input->get('course') : '';
            $class = ($this->input->get('class')) ? $this->input->get('class') : '';
            $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
            $search_select = ($this->input->get('search_select')) ? $this->input->get('search_select') : '';
            $taxcode_id = ($this->input->get('taxcode_id')) ? $this->input->get('taxcode_id') : '';
            $trainee_id = ($this->input->get('trainee_id')) ? $this->input->get('trainee_id') : '';
            $eid = ($this->input->get('eid')) ? $this->input->get('eid') : '';
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'ce.pymnt_due_id';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'desc';

            //      $records_per_page = RECORDS_PER_PAGE;
            $records_per_page = 25;
            $baseurl = base_url() . 'class_trainee/';
            $pageno = ($this->uri->segment(2)) ? $this->uri->segment(2) : 1;
            $offset = ($pageno * $records_per_page);
            $data['tenant'] = $tenant_id;
            $company_id = $this->input->get('company_id');
            $this->db->cache_on();
            $tabledata = $this->classtraineemodel->list_all_classtrainee_by_tenant_id($tenant_id, $records_per_page, $offset, $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id, $eid);

            $totalrows = $this->classtraineemodel->get_all_classtrainee_count_by_tenant_id($tenant_id, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id, $eid);

            $new_tabledata = array();
            $role_array = array("TRAINER", "COMPACT", "SLEXEC");
            foreach ($tabledata as $k => $row) {
                if ($row['enrolment_mode'] == 'COMPSPON') {
                    if ($row['company_id'][0] == 'T') {
                        $tenant_details = fetch_tenant_details($row['company_id']);
                        $company[0]->company_name = $tenant_details->tenant_name;
                    } else {
                        $company = $this->company->get_company_details($tenant_id, $row['company_id']);
                    }
                    $new_tabledata[$k]['enroll_mode'] = $company[0]->company_name;
                } else {
                    $new_tabledata[$k]['enroll_mode'] = 'Individual';
                }
                $paidlabel = rtrim($this->course->get_metadata_on_parameter_id($row['payment_status']), ', ');
                if ($row['payment_status'] == 'PAID') {
                    $new_tabledata[$k]['paid'] = '<a href="javascript:;" class="small_text1 paid_href" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">' . $paidlabel . '<br> $' . number_format($row['total_amount_due'], 2, '.', '') . '</span></a>';
                } else if ($row['payment_status'] == 'PYNOTREQD') {
                    $new_tabledata[$k]['paid'] = '<span style="color:#ffcc66;">' . $paidlabel . '</span>';
                } else if (in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                    $new_tabledata[$k]['paid'] = '<span class="error">' . $paidlabel . '</span>';
                } else {
                    $enrol_mode = ($row['enrolment_mode'] == 'COMPSPON') ? 'company&company_id=' . $row['company_id'] : 'individual';
                    $invoice_id = $this->classtraineemodel->get_invoice_id_for_class_trainee($row['class_id'], $row['user_id']);
                    $get_data = '?invoice_id=' . $invoice_id . '&enrol_mode=' . $enrol_mode;
                    $new_tabledata[$k]['paid'] = '<a href="' . base_url() . 'accounting/update_payment' . $get_data . '"><span class="error">' . $paidlabel . '<br> $' . number_format($row['total_amount_due'], 2, '.', '') . '</span></a>';
                }
                $status = $this->class->get_class_status($row['class_id'], $this->input->get('class_status'));
                $class_end_date = $this->class->get_end_date($row['class_id']);
                if ($status == 'Completed') {
                    $new_tabledata[$k]['status_text'] = '<span class="red">' . $status . '</span>';
                } elseif ($status == 'Yet to Start') {
                    $new_tabledata[$k]['status_text'] = '<span class="green">' . $status . '</span>';
                } elseif ($status == 'In-Progress') {
                    $cur_date = strtotime(date("Y-m-d"));
                    $new_tabledata[$k]['status_text'] = '<span style="color:blue;">' . $status . '</span>';
                    if ($class_end_date == $cur_date) {
                        $new_tabledata[$k]['end_class'] = '<form name="end_class" method="get" action="' . base_url() . 'classes/end_class">'
                                . '<input type="hidden" name="end_class" value="' . $row['class_id'] . '">'
                                . '<button style="color:blue;" >End Class</button></form>';
                    }
                } else {
                    $new_tabledata[$k]['status_text'] = $status;
                }
                if ($row['account_type'] == 'INTUSR') {
                    $new_tabledata[$k]['taxcode'] = '<a href="' . base_url() . 'internal_user/view_user/' . $row['user_id'] . '">' . $row['tax_code'] . '</a>';
                } else {
                    $new_tabledata[$k]['taxcode'] = '<a href="' . base_url() . 'trainee/view_trainee/' . $row['user_id'] . '">' . $row['tax_code'] . '</a>';
                }
                $new_tabledata[$k]['name'] = $row['first_name'] . ' ' . $row['last_name'];
                $new_tabledata[$k]['certi_coll'] = !empty($row['certificate_coll_on']) ? date('d/m/Y', strtotime($row['certificate_coll_on'])) : '';
                $new_tabledata[$k]['class_end_datetime'] = $row['class_end_datetime'];
                $new_tabledata[$k]['course_id'] = $row['course_id'];
                $new_tabledata[$k]['class_id'] = $row['class_id'];
                $new_tabledata[$k]['user_id'] = $row['user_id'];
                $new_tabledata[$k]['feedback_answer'] = $row['feedback_answer'];

                //Added by abdulla for TPG
                $new_tabledata[$k]['tpg_crse'] = $row['tpg_crse'];
                $new_tabledata[$k]['enrolment_mode'] = $row['enrolment_mode'];
                $new_tabledata[$k]['company_id'] = $row['company_id'];
                $new_tabledata[$k]['payment_status'] = $row['payment_status'];
                $new_tabledata[$k]['reference_num'] = $row['reference_num'];
                $new_tabledata[$k]['external_reference_number'] = $row['external_reference_number'];
                $new_tabledata[$k]['tpg_course_run_id'] = $row['tpg_course_run_id'];
                $new_tabledata[$k]['feeDiscountAmount'] = round((($row['discount_rate'] / 100) * $row['class_fees']), 2);

                if ($row['payment_status'] == 'PAID') {
                    $feeCollectionStatus_options[''] = 'Select';
                    $feeCollectionStatus_options['Pending Payment'] = 'Pending Payment';
                    $feeCollectionStatus_options['Partial Payment'] = 'Partial Payment';
                    $feeCollectionStatus_options['Full Payment'] = 'Full Payment';
                    $feeCollectionStatus_options['Cancelled'] = 'Cancelled';
                } else {
                    $feeCollectionStatus_options[''] = 'Select';
                    $feeCollectionStatus_options['Pending Payment'] = 'Pending Payment';
                    $feeCollectionStatus_options['Partial Payment'] = 'Partial Payment';
                    $feeCollectionStatus_options['Cancelled'] = 'Cancelled';
                }
                $new_tabledata[$k]['feecollectionStatus_options'] = $feeCollectionStatus_options;

                if ($row['payment_status'] == 'PAID') {
                    $feecollectionStatus_val = "Full Payment";
                } else if ($row['payment_status'] == 'NOTPAID') {
                    $feecollectionStatus_val = "Pending Payment";
                } else if ($row['payment_status'] == 'PARTPAID') {
                    $feecollectionStatus_val = "Partial Payment";
                } else if ($paymentStatus == 'PYNOTREQD') {
                    $feecollectionStatus_val = "Pending Payment";
                }
                $new_tabledata[$k]['feecollectionStatus_val'] = $feecollectionStatus_val;

                $editEnrolmentAction_options[''] = 'Select';
                $editEnrolmentAction_options['Update'] = 'Update';
                $editEnrolmentAction_options['Cancel'] = 'Cancel';
                $new_tabledata[$k]['editEnrolmentAction'] = $editEnrolmentAction_options;

                $new_tabledata[$k]['enrolmentReferenceNumber'] = $row['eid_number'];
                $new_tabledata[$k]['enrolmentStatus'] = $row['tpg_enrolment_status'];

                //$new_tabledata[$k]['SalesExec'] = $this->class->get_class_salesexec1($tenant_id, $row['course_id'],$row['sales_executive_id']);
                $new_tabledata[$k]['SalesExec'] = $this->class->get_class_salesexec1($tenant_id, $row['course_id'], $row['class_id'], $row['user_id']);

                $new_tabledata[$k]['course_class'] = $row['crse_name'] . ' - ' . $row['class_name'];
                $new_tabledata[$k]['duration'] = date('d/m/Y', strtotime($row['class_start_datetime'])) . ' - ' . date('d/m/Y', strtotime($row['class_end_datetime']));
                $new_tabledata[$k]['subsidy'] = '<a href="javascript:;" class="get_update" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">Update</a>';
                $TGAMT = !empty($row['subsidy_amount']) ? "$" . $row['subsidy_amount'] : "NA";
                $TGNO = !empty($row['tg_number']) ? $row['tg_number'] : "NA";
                $EIDNO = !empty($row['eid_number']) ? $row['eid_number'] : "NA";
                $TGNOBR = !empty($row['tg_number']) ? "<br>" : "";
                $data['trainee_feedback'] = $this->reportsModel->get_trainee_feedback_by_user_id($tenant_id, $new_tabledata[$k]['course_id'], $new_tabledata[$k]['class_id'], $new_tabledata[$k]['user_id']);
                $linkStr = '';
                if ($row['account_status'] == 'PENDACT') {
                    $linkStr = '<span style="color:red;">Account not yet activated.</span>';
                } else if ($row['account_status'] == 'INACTIV') {
                    $linkStr = get_links($row['enrolment_mode'], $row['payment_status'], $row['invoice_id'], $row['user_id'], $row['pymnt_due_id'], $row['class_id'], $this, $row['account_status'], $row['cc_class_status'], $row['company_id'], $row['att_status']);
                } else {
                    $linkStr = '';
                    $cur_date = strtotime(date('Y-m-d'));
                    $class_end_datetime = date("Y-m-d", strtotime($row['class_end_datetime']));
                    $class_end_datetime_str = strtotime($class_end_datetime);
                    if ($cur_date >= $class_end_datetime_str) {
                        $check_attendance = $this->classtraineemodel->check_attendance_row($tenant_id, $row['course_id'], $row['class_id']);
                        $check_competent = $this->classtraineemodel->check_competent($tenant_id, $row['course_id'], $row['class_id'], $row['user_id']);
                        $linkStr = '';
                        if ($this->user->role_id == 'ADMN' || $this->user->role_id == 'CRSEMGR') {
                            $status = $this->class->get_class_status($row['class_id'], $this->input->get('class_status'));
                            if ($status == 'Completed') {
                                if ($check_attendance > 0) {
                                    $linkStr .= ' <a href="#ex7" rel="modal:open" data-course="' . $row['course_id'] . '" '
                                            . 'data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '"data-payment="' . $row['pymnt_due_id'] . '"class="training_update small_text1">Trainer Feedback</a><br/>';
                                    if ($check_competent > 0) {
                                        $linkStr .= ' <a  href="#ex6" rel="modal:open" data-course="' . $row['course_id'] . '" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '" class="training_update small_text1"><span>Trainee Feedback</span></a><br/>';
                                    }
                                } else {
                                    $linkStr.='<form action="' . base_url() . 'class_trainee/mark_attendance" method="post" name="maarkatt[]"><input type="hidden" name="course_id" value="' . $row['course_id'] . '" /><input type="hidden" name="class_id" value="' . $row['class_id'] . '" /><input type="submit" class="red" value ="Mark Attendance" /></form><br />';
                                }
                            }
                        }
                        if ($row['payment_status'] != 'PYNOTREQD' && $this->user->role_id == 'ADMN' || $row['payment_status'] != 'PYNOTREQD' && $this->user->role_id == 'CRSEMGR') {
                            $linkStr .= '<a href="javascript:;" class="get_update" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">TG No: <span style="font-weight:normal;color:#000">' . $TGNO . ' </span> </a>';

                            $linkStr .= '<br><a href="#"> TG Amt : <span style="font-weight:normal;color:#000"> ' . $TGAMT . ' </span> </a><br/>';
                        }


                        // if($check_attendance<=0 || $check_competent>0)
                        if ($check_competent > 0) {
                            $wsq_courses_array = $this->config->item('wsq_courses'); // wsq courses modified by shubhranshu
                            $tenant_array = array('T02', 'T12'); // xp and xp2 
                            $linkStr .= '<a href="' . base_url() . 'trainee/print_loc/' . $row['class_id'] . '/' . $row['user_id'] . '">LOC</a><br/>';
                            //////added by shubhranshu for wablab and everest TCS for all courses
                            if ($tenant_id == 'T20' || $tenant_id == 'T17') {
                                $linkStr .= '<a href="' . base_url() . 'trainee/print_wsq_loc/' . $row['course_id'] . '/' . $row['class_id'] . '/' . $row['user_id'] . '">TCS</a><br/>';
                            } else {
                                if (in_array($row['course_id'], $wsq_courses_array) && in_array($tenant_id, $tenant_array)) {
                                    $linkStr .= '<a href="' . base_url() . 'trainee/print_wsq_loc/' . $row['course_id'] . '/' . $row['class_id'] . '/' . $row['user_id'] . '">TCS</a><br/>';
                                }
                            }



                            //                        $linkStr .= '<a href="' . base_url() . 'trainee/print_loc/' . $row['class_id'] . '/' . $row['user_id'] . '">LOC</a><br/>';
                        }
                    } else {
                        if ($row['payment_status'] != 'PYNOTREQD' &&
                                $this->user->role_id == 'ADMN') {
                            $linkStr = '<a href="javascript:;" class="get_update" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">TG No: <span style="font-weight:normal;color:#000">' . $TGNO . ' </span> </a>';

                            $linkStr .= '<br><a href="#"> TG Amt : <span style="font-weight:normal;color:#000"> ' . $TGAMT . ' </span> </a><br/>';
                        }
                    }
                    $linkStr .= get_links($row['enrolment_mode'], $row['payment_status'], $row['invoice_id'], $row['user_id'], $row['pymnt_due_id'], $row['class_id'], $this, $row['account_status'], $row['cc_class_status'], $row['company_id'], $row['att_status']);
                }
                //////add by shubhranshu to save enrollment id on 18/03/2021
                //Commented by abdulla nofal - Since, it's being updated from TPG.
                //$linkStr .= '<a href="javascript:;" class="get_update_eid" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">EID No: <span style="font-weight:normal;color:#000">' . $EIDNO . ' </span> </a><br/>';
                if($row['tpg_crse']) {
                    $linkStr .= 'EID No: <span style="font-weight:normal;color:#000">' . $EIDNO . ' </span><br/>';    
                }                                
                $new_tabledata[$k]['action_link'] = $linkStr;
                $new_tabledata[$k]['referrer'] = $row['referral_details'];
            }
            $this->db->cache_off();
            $data['tabledata'] = $new_tabledata;
            //echo "<pre>"; print_r($new_tabledata);exit;
            $data['sort_order'] = $order_by;
            $data['controllerurl'] = 'class_trainee/';
            $this->load->helper('pagination');
            if ($sort_url) {
                $pag_sort = $order_by . '&' . $sort_url;
            } else {
                $pag_sort = $order_by;
            }
            $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $pag_sort);
        }
        $data['page_title'] = 'Class Trainee';
        $data['main_content'] = 'classtrainee/classtraineelist';
        $this->load->view('layout', $data);
    }

    public function tpg_search_enrolment() {

        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        extract($_GET);
        $data['courses'] = $courses = $this->course->get_active_course_list_all_tpg($tenant_id, 'classTrainee');
        if ($course) {

            $course_classes = $this->class->get_course_class($tenant_id, $course, "", "", "classTrainee");
            $data['classes'] = $course_classes;
        }

        $feeCollectionStatus_options[''] = 'Select';
        $feeCollectionStatus_options['Pending Payment'] = 'Pending Payment';
        $feeCollectionStatus_options['Partial Payment'] = 'Partial Payment';
        $feeCollectionStatus_options['Full Payment'] = 'Full Payment';
        $feeCollectionStatus_options['Cancelled'] = 'Cancelled';

        $data['feeCollectionStatus_options'] = $feeCollectionStatus_options;

        $sponsorshipType_options[''] = 'Select';
        $sponsorshipType_options['INDIVIDUAL'] = 'Individual';
        $sponsorshipType_options['EMPLOYER'] = 'Employer';

        $data['sponsorshipType_options'] = $sponsorshipType_options;

        $idType_options[''] = 'Select';
        $idType_options['NRIC'] = 'NRIC';
        $idType_options['FIN'] = 'FIN';
        $idType_options['Others'] = 'Others';

        $data['idType_options'] = $idType_options;

        $noResultsPerPage_options['25'] = '25';
        $noResultsPerPage_options['50'] = '50';
        $noResultsPerPage_options['100'] = '100';

        $data['noResultsPerPage_options'] = $noResultsPerPage_options;

        $enrolmentStatus_options[''] = 'Select';
        $enrolmentStatus_options['Confirmed'] = 'Confirmed';
        $enrolmentStatus_options['Cancelled'] = 'Cancelled';

        $data['enrolmentStatus_options'] = $enrolmentStatus_options;

        $course = $this->input->get('course');
        $class = $this->input->get('class');
        $date_from = $this->input->get('from_date');
        $date_to = $this->input->get('to_date');
        $traineeIdType = $this->input->get('idType');
        $enrolmentDate = $this->input->get('enrol_date');
        $sponsorshipType = $this->input->get('sponsorshipType');
        $feeCollectionStatus = $this->input->get('feeCollectionStatus');
        $enrolmentStatus = $this->input->get('enrolmentStatus');
        $traineeId = $this->input->get('taxcode');
        $pageSize = $this->input->get('noResultsPerPage');

        $class_details = $this->class->get_class_details($tenant_id, $class);
        $crse_details = $this->course->get_course_detailse($class_details->course_id);

        $course_run_id = $class_details->tpg_course_run_id;
        $crse_ref_no = $crse_details->reference_num;

        $export_url = '';
        $sort_url = '';

        if (!empty($_GET)) {

            $encrypt_method = "AES-256-CBC";

            $tenant_id = $this->tenant_id;
            $key = base64_decode($this->config->item(TPG_KEY_ . $tenant_id));  // don't hash to derive the (32 bytes) key

            $iv = 'SSGAPIInitVector';                      // don't hash to derive the (16 bytes) IV        

            $api_version = 'v1';
            $url = "https://" . TPG_URL . "/tpg/enrolments/search";

            //Training Partner
            $tenant_details = fetch_tenant_details($tenant_id);
            $trainingPartnerUEN = $tenant_details->comp_reg_no;
            $trainingPartnerCode = $tenant_details->comp_reg_no . '-03';

            $tpg_search_json_data = '{
                                        "meta": {
                                          "lastUpdateDateTo": "' . $date_to . '",
                                          "lastUpdateDateFrom": "' . $date_from . '"
                                        },
                                        "sortBy": {
                                          "field": "updatedOn",
                                          "order": "asc"
                                        },
                                        "enrolment": {
                                          "course": {
                                            "run": {
                                              "id": "' . $course_run_id . '"
                                            },
                                            "referenceNumber": "' . $crse_ref_no . '"
                                          },
                                          "status": "' . $enrolmentStatus . '",
                                          "trainee": {
                                            "id": "' . $traineeId . '",
                                            "fees": {
                                              "feeCollectionStatus": "' . $feeCollectionStatus . '"
                                            },
                                            "idType": {
                                              "type": "' . $traineeIdType . '"
                                            },
                                            "employer": {
                                              "uen": ""
                                            },
                                            "enrolmentDate": "' . $enrolmentDate . '",
                                            "sponsorshipType": "' . $sponsorshipType . '"
                                          },
                                          "trainingPartner": {
                                            "uen": "' . $trainingPartnerUEN . '",
                                            "code": "' . $trainingPartnerCode . '"
                                          }
                                        },
                                        "parameters": {
                                          "page": 0,
                                          "pageSize": ' . $pageSize . '
                                        }
                                      }';

            $encrypted_output = openssl_encrypt($tpg_search_json_data, $encrypt_method, $key, 0, $iv); // remove explicit Base64 encoding (alternatively set OPENSSL_RAW_DATA)

            $request = $this->curl_request('POST', $url, $encrypted_output, $api_version);

            $decrypted_output = openssl_decrypt($request, $encrypt_method, $key, 0, $iv); // remove explicit Base64 decoding (alternatively set OPENSSL_RAW_DATA)

            $tpg_response = json_decode($decrypted_output);
                        
//            echo "URL : ".print_r($url, true);
//            
//            echo "JSON DATA : ".print_r($tpg_search_json_data, true);
//            
//            echo "Encrypted Input : ".print_r($encrypted_output, true);
//            
//            echo "Decrypted Output : ".print_r($decrypted_output, true);
//            
//            echo "TPG Response : ".print_r($tpg_response, true);     
//            
//            echo $today = date("Y-m-d H:i:s"); exit;
            
            if ($tpg_response->status == 200) {
                $data['tabledata_tpg'] = $tpg_response;
            } else {
                if ($tpg_response->status == 400) {
                    $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
                } elseif ($tpg_response->status == 403) {
                    $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
                } elseif ($tpg_response->status == 404) {
                    $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
                } elseif ($tpg_response->status == 500) {
                    $this->session->set_flashdata('error', $tpg_response->error->details[0]->message);
                } else {
                    $this->session->set_flashdata('error', "TPG is not responding. Please, check back again.");
                }
            }
        }

        if (empty($data['tabledata_tpg'])) {
            $data['error_msg'] = 'Kindly apply filter to fetch the trainees';
            //$this->session->set_flashdata('error', "Kindly, apply filter to fetch the trainees.");
        } else {
            $data['error_msg'] = 'Please, select a filter to display the data.';
            //$this->session->set_flashdata('error', "Please, select a filter to display the data.");
        }        

        $data['page_title'] = 'Class Trainee';
        $data['main_content'] = 'classtrainee/search_enrol_tpg';
        $this->load->view('layout', $data);
    }

    public function curl_request($mode, $url, $encrypted_data, $api_version) {

        $tenant_id = $this->tenant_id;

        $pemfile = "/var/www/newtms/assets/certificates/" . $tenant_id . "/cert.pem";
        $keyfile = "/var/www/newtms/assets/certificates/" . $tenant_id . "/key.pem";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $mode,
            CURLOPT_SSLCERT => $pemfile,
            CURLOPT_SSLCERTTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_POSTFIELDS => $encrypted_data,
            CURLOPT_HTTPHEADER => array(
                "Authorization:  ",
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "x-api-version: $api_version"
            ),
        ));

        $response = curl_exec($curl);
        if ($response === false) {
            print_r(curl_error($curl));
            exit;
        } else {
            //print_r($response);exit;
            return $response;
        }
        curl_close($curl);
    }

    ///by shubhranshu for client requirement for declaration data to save
    public function save_declaration_trainee_data() {
        $tenant_id = $this->tenant_id ?? TENANT_ID;
        $tax_code = $this->input->post('tax_code');
        $name = $this->input->post('name');
        $type = $this->input->post('type');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $trainee_id = $this->input->post('user_id');
        $class_id = $this->input->post('class_id');
        $condition = $this->input->post('res');
        $lesson_timing = $this->input->post('lesson_timing');
        $overseas = $this->input->post('overseas');
        $status = $this->classtraineemodel->save_declaration_data($tenant_id, $trainee_id, $class_id, $tax_code, $name, $type, $email, $mobile, $condition, $lesson_timing, $overseas);
        echo $status;
    }

    /* This function loads the trainee list of public portal skm start */

    public function online_trainee() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        extract($_GET);
        $data['courses'] = $courses = $this->course->get_active_course_list_by_tenant($tenant_id, 'classTrainee');
        if ($course) {

            $course_classes = $this->class->get_course_class($tenant_id, $course, "", "", "classTrainee");
            $data['classes'] = $course_classes;
        }
        $export_url = '';
        $sort_url = '';
        if (!empty($_GET)) { /// added by shubhranshu to remove the classtrainee list on 01/01/2019
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
            $course = ($this->input->get('course')) ? $this->input->get('course') : '';
            $class = ($this->input->get('class')) ? $this->input->get('class') : '';
            $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
            $search_select = ($this->input->get('search_select')) ? $this->input->get('search_select') : '';
            $taxcode_id = ($this->input->get('taxcode_id')) ? $this->input->get('taxcode_id') : '';
            $trainee_id = ($this->input->get('trainee_id')) ? $this->input->get('trainee_id') : '';
            $field = ($this->input->get('f')) ? $this->input->get('f') : 'ce.pymnt_due_id';
            $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'desc';

            $records_per_page = RECORDS_PER_PAGE;
            $baseurl = base_url() . 'class_trainee/online_trainee/';
            $pageno = ($this->uri->segment(3)) ? $this->uri->segment(3) : 1;
            $offset = ($pageno * $records_per_page);
            $data['tenant'] = $tenant_id;
            $company_id = $this->input->get('company_id');
            $this->db->cache_on();

            $tabledata = $this->classtraineemodel->online_list_classtrainee_by_tenant_id($tenant_id, $records_per_page, $offset, $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);

            $totalrows = $this->classtraineemodel->get_all_online_classtrainee_count_by_tenant_id($tenant_id, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);
//        echo $this->db->last_query();
//        print_r($totalrows);
//        exit();

            $new_tabledata = array();
            $role_array = array("TRAINER", "COMPACT", "SLEXEC");
            foreach ($tabledata as $k => $row) {
                if ($row['enrolment_mode'] == 'COMPSPON') {
                    if ($row['company_id'][0] == 'T') {
                        $tenant_details = fetch_tenant_details($row['company_id']);
                        $company[0]->company_name = $tenant_details->tenant_name;
                    } else {
                        $company = $this->company->get_company_details($tenant_id, $row['company_id']);
                    }
                    $new_tabledata[$k]['enroll_mode'] = $company[0]->company_name;
                } else {
                    $new_tabledata[$k]['enroll_mode'] = 'Individual';
                }
                $paidlabel = rtrim($this->course->get_metadata_on_parameter_id($row['payment_status']), ', ');
                if ($row['payment_status'] == 'PAID') {
                    $new_tabledata[$k]['paid'] = '<a href="javascript:;" class="small_text1 paid_href" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">' . $paidlabel . '<br> $' . number_format($row['total_amount_due'], 2, '.', '') . '</span></a>';
                } else if ($row['payment_status'] == 'PYNOTREQD') {
                    $new_tabledata[$k]['paid'] = '<span style="color:#ffcc66;">' . $paidlabel . '</span>';
                } else if (in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                    $new_tabledata[$k]['paid'] = '<span class="error">' . $paidlabel . '</span>';
                } else {
                    $enrol_mode = ($row['enrolment_mode'] == 'COMPSPON') ? 'company&company_id=' . $row['company_id'] : 'individual';
                    $invoice_id = $this->classtraineemodel->get_invoice_id_for_class_trainee($row['class_id'], $row['user_id']);
                    $get_data = '?invoice_id=' . $invoice_id . '&enrol_mode=' . $enrol_mode;
                    $new_tabledata[$k]['paid'] = '<a href="' . base_url() . 'accounting/update_payment' . $get_data . '"><span class="error">' . $paidlabel . '<br> $' . number_format($row['total_amount_due'], 2, '.', '') . '</span></a>';
                }
                $status = $this->class->get_class_status($row['class_id'], $this->input->get('class_status'));
                $class_end_date = $this->class->get_end_date($row['class_id']);
                if ($status == 'Completed') {
                    $new_tabledata[$k]['status_text'] = '<span class="red">' . $status . '</span>';
                } elseif ($status == 'Yet to Start') {
                    $new_tabledata[$k]['status_text'] = '<span class="green">' . $status . '</span>';
                } elseif ($status == 'In-Progress') {
                    $cur_date = strtotime(date("Y-m-d"));
                    $new_tabledata[$k]['status_text'] = '<span style="color:blue;">' . $status . '</span>';
                    if ($class_end_date == $cur_date) {
                        $new_tabledata[$k]['end_class'] = '<form name="end_class" method="get" action="' . base_url() . 'classes/end_class">'
                                . '<input type="hidden" name="end_class" value="' . $row['class_id'] . '">'
                                . '<button style="color:blue;" >End Class</button></form>';
                    }
                } else {
                    $new_tabledata[$k]['status_text'] = $status;
                }
                if ($row['account_type'] == 'INTUSR') {
                    $new_tabledata[$k]['taxcode'] = '<a href="' . base_url() . 'internal_user/view_user/' . $row['user_id'] . '">' . $row['tax_code'] . '</a>';
                } else {
                    $new_tabledata[$k]['taxcode'] = '<a href="' . base_url() . 'trainee/view_trainee/' . $row['user_id'] . '">' . $row['tax_code'] . '</a>';
                }
                $new_tabledata[$k]['name'] = $row['first_name'] . ' ' . $row['last_name'];
                $new_tabledata[$k]['certi_coll'] = !empty($row['certificate_coll_on']) ? date('d/m/Y', strtotime($row['certificate_coll_on'])) : '';
                $new_tabledata[$k]['class_end_datetime'] = $row['class_end_datetime'];
                $new_tabledata[$k]['course_id'] = $row['course_id'];
                $new_tabledata[$k]['class_id'] = $row['class_id'];
                $new_tabledata[$k]['user_id'] = $row['user_id'];
                $new_tabledata[$k]['feedback_answer'] = $row['feedback_answer'];

                $new_tabledata[$k]['SalesExec'] = $this->class->get_class_salesexec1($tenant_id, $row['course_id'], $row['sales_executive_id']);

                $new_tabledata[$k]['course_class'] = $row['crse_name'] . ' - ' . $row['class_name'];
                $new_tabledata[$k]['duration'] = date('d/m/Y', strtotime($row['class_start_datetime'])) . ' - ' . date('d/m/Y', strtotime($row['class_end_datetime']));
                $new_tabledata[$k]['subsidy'] = '<a href="javascript:;" class="get_update" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">Update</a>';
                $TGAMT = !empty($row['subsidy_amount']) ? "$" . $row['subsidy_amount'] : "NA";
                $TGNO = !empty($row['tg_number']) ? $row['tg_number'] : "NA";
                $TGNOBR = !empty($row['tg_number']) ? "<br>" : "";
                $data['trainee_feedback'] = $this->reportsModel->get_trainee_feedback_by_user_id($tenant_id, $new_tabledata[$k]['course_id'], $new_tabledata[$k]['class_id'], $new_tabledata[$k]['user_id']);
                $linkStr = '';
                if ($row['account_status'] == 'PENDACT') {
                    $linkStr = '<span style="color:red;">Account not yet activated.</span>';
                } else if ($row['account_status'] == 'INACTIV') {
                    $linkStr = get_links($row['enrolment_mode'], $row['payment_status'], $row['invoice_id'], $row['user_id'], $row['pymnt_due_id'], $row['class_id'], $this, $row['account_status'], $row['cc_class_status'], $row['company_id']);
                } else {
                    $linkStr = '';
                    $cur_date = strtotime(date('Y-m-d'));
                    $class_end_datetime = date("Y-m-d", strtotime($row['class_end_datetime']));
                    $class_end_datetime_str = strtotime($class_end_datetime);
                    if ($cur_date >= $class_end_datetime_str) {
                        $check_attendance = $this->classtraineemodel->check_attendance_row($tenant_id, $row['course_id'], $row['class_id']);
                        $check_competent = $this->classtraineemodel->check_competent($tenant_id, $row['course_id'], $row['class_id'], $row['user_id']);
                        $linkStr = '';
                        if ($this->user->role_id == 'ADMN' || $this->user->role_id == 'CRSEMGR') {
                            $status = $this->class->get_class_status($row['class_id'], $this->input->get('class_status'));
                            if ($status == 'Completed') {
                                if ($check_attendance > 0) {
                                    $linkStr .= ' <a href="#ex7" rel="modal:open" data-course="' . $row['course_id'] . '" '
                                            . 'data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '"data-payment="' . $row['pymnt_due_id'] . '"class="training_update small_text1">Trainer Feedback</a><br/>';
                                    if ($check_competent > 0) {
                                        $linkStr .= ' <a  href="#ex6" rel="modal:open" data-course="' . $row['course_id'] . '" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '" class="training_update small_text1"><span>Trainee Feedback</span></a><br/>';
                                    }
                                } else {
                                    $linkStr.='<form action="' . base_url() . 'class_trainee/mark_attendance" method="post" name="maarkatt[]"><input type="hidden" name="course_id" value="' . $row['course_id'] . '" /><input type="hidden" name="class_id" value="' . $row['class_id'] . '" /><input type="submit" class="red" value ="Mark Attendance" /></form><br />';
                                }
                            }
                        }
                        if ($row['payment_status'] != 'PYNOTREQD' &&
                                ($this->user->role_id == 'ADMN' || $this->user->role_id == 'CRSEMGR')
                        ) {
                            $linkStr .= '<a href="javascript:;" class="get_update" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">TG No: <span style="font-weight:normal;color:#000">' . $TGNO . ' </span> </a>';

                            $linkStr .= '<br><a href="#"> TG Amt : <span style="font-weight:normal;color:#000"> ' . $TGAMT . ' </span> </a><br/>';
                        }



                        if ($check_competent > 0) {
                            $linkStr .= '<a href="' . base_url() . 'trainee/print_loc/' . $row['class_id'] . '/' . $row['user_id'] . '">LOC</a><br/>';
                        }
                    } else {
                        if ($row['payment_status'] != 'PYNOTREQD' &&
                                $this->user->role_id == 'ADMN') {
                            $linkStr = '<a href="javascript:;" class="get_update" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '">TG No: <span style="font-weight:normal;color:#000">' . $TGNO . ' </span> </a>';

                            $linkStr .= '<br><a href="#"> TG Amt : <span style="font-weight:normal;color:#000"> ' . $TGAMT . ' </span> </a><br/>';
                        }
                    }
                    $linkStr .= get_links($row['enrolment_mode'], $row['payment_status'], $row['invoice_id'], $row['user_id'], $row['pymnt_due_id'], $row['class_id'], $this, $row['account_status'], $row['cc_class_status'], $row['company_id'], $row['att_status']);
                }
                $new_tabledata[$k]['action_link'] = $linkStr;
                $new_tabledata[$k]['referrer'] = $row['referral_details']; // referrer details
                if ($row['friend_id'] != '') {
                    $new_tabledata[$k]['friend_id'] = $row['friend_id'];
                    $res = $check_attendance = $this->classtraineemodel->get_friend_details($tenant_id, $row['course_id'], $row['class_id'], $row['user_id'], $row['friend_id']);
                    $new_tabledata[$k]['friend_details'] = $res;
                }
            }
            $this->db->cache_off();
        }
        $data['tabledata'] = $new_tabledata;
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'class_trainee/online_trainee/';
        $this->load->helper('pagination');
        if ($sort_url) {
            $pag_sort = $order_by . '&' . $sort_url;
        } else {
            $pag_sort = $order_by;
        }

//        $data['trainee_count'] = $totalrows;
        $total_trainee_enrolled = $this->classtraineemodel->get_total_enrolled_trainee($tenant_id);
        $data['trainee_count'] = $total_trainee_enrolled ? $total_trainee_enrolled : 00;
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $pag_sort);
        $data['page_title'] = 'Online Class Trainee';
        $data['main_content'] = 'classtrainee/onlinetraineelist';
        $this->load->view('layout', $data);
    }

    /* END */

    /* This function loads only notpaid invoice list of selected company skm st */

    public function get_company_all_invoice_for_remove() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $company = $this->input->post('company');

        $paid = $this->input->post('paid');
        $result = $this->classtraineemodel->get_company_notpaid_invoices_list($this->tenant_id, $company);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id . '#' . $row->pymnt_due_id . '#' . $row->course_id . '#' . $row->class_id . '#' . $row->company_id . '#' . $row->company_name . '#' . $row->crse_name . '#' . $row->class_name,
                    'label' => $row->invoice_id . ' (Class: ' . $row->class_name . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /* skm ed */


    /* Get all taxcode of trainee who enrolled from public portal skm start */

    public function get_online_alltaxcode_with_courseclass() {
        $class = $this->input->post('class');
        $course = $this->input->post('course');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_online_user_with_class_course($this->tenant_id, '', $query_string, $class, $course);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . '( Name : ' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /* End */

    /* Get all the trainee name which enroll from public portal skm start */

    public function get_online_trainee_with_courseclass() {
        $class = $this->input->post('class');
        $course = $this->input->post('course');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_online_user_with_class_course($this->tenant_id, $query_string, '', $class, $course);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . '( Taxcode : ' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /* END */

    /**
     * gets the payment id
     */
    public function get_payment_class_user() {
        $class = $this->input->post('class');
        $user = $this->input->post('user');
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $result = $this->get_payid_details($payid, 1);
        echo json_encode($result);
        exit();
    }

    /*
     * this function to display add new enroll
     */

    public function add_new_enrol() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['page_title'] = 'Class Trainee';
        $tenant_id = $this->tenant_id;
        $data['privilage'] = $this->manage_tenant->get_privilage(); //added by shubhranshu
        $data['companies'] = $this->classtraineemodel->get_company_list($tenant_id);
        $data['main_content'] = 'classtrainee/addnewenroll';
        $this->load->view('layout', $data);
    }

    /**
     * This method will format the date for UI Display in company not paid list on enrollment.
     * @param type $data
     */
    private function formate_company_not_paid($data) {
        foreach ($data as $key => $value) {
            $data[$key]->class_start_datetime = date('d-m-Y', strtotime($value->class_start_datetime));
            $data[$key]->class_end_datetime = date('d-m-Y', strtotime($value->class_end_datetime));
            $data[$key]->inv_date = date('d-m-Y', strtotime($value->inv_date));
            $data[$key]->company_name = substr($value->company_name, 0, 25);
        }
        return $data;
    }

    /**
     * This method will format the date for UI Display in change individual invoice to comapny invoice on enrollment.
     * @param type $data
     */
    private function formate_change_individual($data) {
        foreach ($data as $key => $value) {
            $data[$key]->class_start_datetime = date('d-m-Y', strtotime($value->class_start_datetime));
            $data[$key]->class_end_datetime = date('d-m-Y', strtotime($value->class_end_datetime));
            $data[$key]->enrolled_on = date('d-m-Y', strtotime($value->enrolled_on));
        }
        return $data;
    }

    /**
     * get classes by course 
     */
    public function get_classes_by_courseid() {
        $courseid = $this->input->post('courseid');
        $this->load->helper('common_helper');

        $classes = getclasses_by_courseid($courseid);

        $states_arr = array();
        foreach ($classes as $item) {
            $classes_arr[] = $item;
        }

        echo json_encode($classes_arr);
        exit;
    }

    /**
     * regenerate_invoice
     */
    public function regenerate_invoice() {
        $post_invoice = $this->input->post('invoice_hidden_id');
        $ind_post_invoice = $this->input->post('invoice_indi_hidden_id');
        $inv_type = $this->input->post('invoice_type');
        if ($inv_type == 'individual') {
            if (!empty($ind_post_invoice)) {
                $invoice = $this->classtraineemodel->re_generate_indi_invoice();
                $this->session->set_flashdata("success", "Invoice id - $invoice has been regenerated successfully. ");
            } else {
                $this->session->set_flashdata("error", "Unable to Regenerate Invoice ! ");
            }
        } else {
            if (!empty($post_invoice)) {
                $invoice = $this->classtraineemodel->re_generate_invoice();
                $this->session->set_flashdata("success", "Invoice id - $invoice has been regenerated successfully. ");
            } else {
                $this->session->set_flashdata("error", "Unable to Regenerate Invoice ! ");
            }
        }
        redirect('accounting/generate_invoice');
    }

    /**
     * enroll booking company pdf
     */
    public function booking_acknowledge_company_pdf($trainee_ids, $class_id, $company) {
        $tenant_id = $this->tenant_id;
        $trainee_allid = explode('-', $trainee_ids);
        $tr_count = 0;
        foreach ($trainee_allid as $row) {
            $trainee_id = $row;
            $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $row, $tenant_id);
            $name = $trainee_name->first . ' ' . $trainee_name->last;
            $trainee_names = ($trainee_name->gender == 'MALE') ? 'Mr.' . $name : 'Ms.' . $name;
            $trainee .=$trainee_names . ', ';
            $tr_count++;
        }
        $trainee = rtrim($trainee, ', ');

        $company_details = $this->company->get_company_details($tenant_id, $company);
        $classes = $this->class->get_class_details($tenant_id, $class_id);
        $ClassLoc = $this->get_classroom_location($classes->classroom_location, $classes->classroom_venue_oth);
        $courses = $this->course->get_course_detailse($classes->course_id);
        $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $courseLevel = rtrim($this->course->get_metadata_on_parameter_id($courses->certi_level), ', ');
        $data = '';
        $booking_details = $this->classtraineemodel->get_paydue_invoice($trainee_id, $class_id);
        $company_details1 = $this->classtraineemodel->get_company_name($booking_details->invoice_id, $trainee_id, $class_id, $tenant_id); //added by shubhranshu to fetch the company name
        if ($booking_details) {
            $booking_no = date('Y', strtotime($booking_details->inv_date)) . ' ' . $booking_details->invoice_id;
            $booking_date = date('d/m/Y', strtotime($booking_details->inv_date));
        } else {
            $booking_no = date('Y') . ' ' . $trainee_id;
            $booking_date = date('d/m/Y');
        }

        if ($tr_count > 0) {
            $contact_details = '';
            if ($tenant_details->tenant_id == 'T12') {
                if (!empty($tenant_details->contact_name)) {
                    $contact_details .=$tenant_details->contact_name . ' ';
                }
            }

            if (!empty($tenant_details->tenant_contact_num)) {
                $contact_details .='(Phone: ' . $tenant_details->tenant_contact_num . ', ';
            }
            if (!empty($tenant_details->tenant_email_id)) {
                $contact_details .='Email Id: ' . $tenant_details->tenant_email_id . ')';
            }
            $contact_details = rtrim($contact_details, ', ');
            if ($company[0] == 'T') {
                $company_details->company_name = $tenant_details->tenant_name;
            }

            /* skm code start for remark. reporting time skm start bcoz of sam request for AOP(67) on 18-may-17 */
            $time = strtotime($classes->class_start_datetime);
            $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
            if ($classes->course_id == 67 || $classes->course_id == 121) {
                $li = "Report at center at $reporting_time to register for class.";
            } else {
                $li = "Report at center at 8:30 AM to register for class.";
            }
            /* end */
            if ($tenant_details->tenant_id == 'T02') {
                $li2 = "<li>Payment via Paynow/ Paylah, GIRO or SkillsFuture Credit.</li><li>Dress code : Covered shoes</li>";
            } else {
                $li2 = '';
            }
            ///// added by shubhranshu for wablab points
            if ($tenant_details->tenant_id == 'T20' || $tenant_details->tenant_id == 'T17') {
                $li_first = "<li>Your NRIC, work permit or will be photocopied on the class date.</li>";
            } else {
                $li_first = "<li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>";
            }
            $message3 = '
             <ol style="font-size:13px;color:#4f4b4b">
                          ' . $li_first . '
                            
                            <li>Trim finger nails and remove nail polish.</li>
                            <li>' . $li . '</li>
                            ' . $li2 . '
                        </ol>';

            /* skm end */
            $comp_var = '';
            if (!empty($company_details1) && $tenant_id == 'T02') {
                $comp_var = $company_details->company_name;
            }
            $data = '<br><br>
            <table style="font-size:15px">
                <tr>
                    <td>' . $tr_count . ' Seats for your company ' . $comp_var . ' has been booked. Booking details for your employees: 
                    ' . $trainee . ' for \'Course: <b>' . $courses->crse_name . '</b>, Class: <b>' . $classes->class_name . '</b>, Certificate Code: ' . $courseLevel . '\'<br><br>
            <strong>Class start date:</strong>
            ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
            <br>
            <br>
             <strong>Class end date:</strong>
            ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '
            <br>
            <br>
           
            <strong>Location: </strong>
            ' . $ClassLoc . '<br><br>
            <strong>Contact Details: </strong>
            ' . $contact_details . '<br><br>
            <strong>Remark: </strong>
            ' . $message3 . '</td>
                </tr>
            </table>';
        }

        $this->load->helper('pdf_reports_helper');
        generate_booking_acknowledge_pdf($data, $tenant_details, $booking_no, $booking_date);
    }

    /**
     * enroll booking pdf
     */
    public function booking_acknowledge_pdf($trainee_id, $class_id) {
        $tenant_id = $this->tenant_id;
        $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $trainee_id, $tenant_id);
        $name = $trainee_name->first . ' ' . $trainee_name->last;
        $trainee = ($trainee_name->gender == 'MALE') ? 'Mr. ' . $name : 'Ms. ' . $name;
        $classes = $this->class->get_class_details($tenant_id, $class_id);
        $ClassLoc = $this->get_classroom_location($classes->classroom_location, $classes->classroom_venue_oth);
        $courses = $this->course->get_course_detailse($classes->course_id);
        $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
        $tenant_details->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
        $tenant_details->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
        $courseLevel = rtrim($this->course->get_metadata_on_parameter_id($courses->certi_level), ', ');
        $contact_details = '';
        if ($tenant_details->tenant_id == 'T12') {
            if (!empty($tenant_details->contact_name)) {
                $contact_details .=$tenant_details->contact_name . ' ';
            }
        }
        if (!empty($tenant_details->tenant_contact_num)) {
            $contact_details .='(Phone: ' . $tenant_details->tenant_contact_num . ', ';
        }
        if (!empty($tenant_details->tenant_email_id)) {
            $contact_details .='Email Id: ' . $tenant_details->tenant_email_id . ')';
        }
        $contact_details = rtrim($contact_details, ', ');
        //added by pritam
        // $contact_details= explode("(",  $contact_details);
        //end

        /* reporting time skm start bcoz of sam request for AOP(67) on 18-may-17 */
        $time = strtotime($classes->class_start_datetime);
        $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
        if ($classes->course_id == 67 || $classes->course_id == 121) {
            $li = "Report at center at $reporting_time to register for class.";
        } else {
            $li = "Report at center at 8:30 AM to register for class.";
        }
        //   echo removed by shubhranshu to [revent TCPDF header sent issue.
        /* end */
        if ($tenant_details->tenant_id == 'T02') {
            $li2 = "<li>Payment via Paynow/ Paylah, GIRO or SkillsFuture Credit.</li>
                    <li>Dress code : Covered shoes.</li>";
        } else {
            $li2 = '';
        }

        ///// added by shubhranshu for wablab points
        if ($tenant_details->tenant_id == 'T20' || $tenant_details->tenant_id == 'T17') {
            $li_first = "Your NRIC, work permit or will be photocopied on the class date.";
        } else {
            $li_first = "All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.";
        }

        if(($tenant_id == 'T02' && $classes->course_id == 189) || (($tenant_id == 'T02' && $classes->course_id == 190)) {
            $li_trim = "";
        } else {
            $li_trim = "<li>Trim finger nails and remove nail polish.</li>";
        }
                
        $booking_details = $this->classtraineemodel->get_paydue_invoice($trainee_id, $class_id);
        $company_details = $this->classtraineemodel->get_company_name($booking_details->invoice_id, $trainee_id, $class_id, $tenant_id); //added by shubhranshu to fetch the company name
        if ($booking_details) {
            $booking_no = date('Y', strtotime($booking_details->inv_date)) . ' ' . $booking_details->invoice_id;
            $booking_date = date('d/m/Y', strtotime($booking_details->inv_date));
        } else {
            $booking_no = date('Y') . ' ' . $trainee_id;
            $booking_date = date('d/m/Y');
        }
        $comp_var = '';
        if (!empty($company_details) && $tenant_id == 'T02') {
            $comp_var = '(Company Name:' . $company_details->company_name . ')';
        }

        $data = '<br><br>
            <table style="font-size:15px">
                <tr>
                    <td>Your seat has been booked. Please pay the class fees on or before the class start date.
                        Booking for <strong>' . $trainee . $comp_var . '</strong> for \'Course: <b>' . $courses->crse_name . '</b>, Class: <b>' . $classes->class_name . '</b>, Certificate Code: ' . $courseLevel . '\'.<br><br>
                        <strong>Class start date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                        <br><br>
                        <strong>Class end date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '
                       
                        <br>
                        <br>
                        <strong>Location:</strong>
                        ' . $ClassLoc . '<br><br>
                        <strong>Contact Details: </strong>
                        ' . $contact_details . '
                            <br><br>
                        <strong style="font-size:13px">Remark *: </strong>
                        <ol style="font-size:13px;color:#4f4b4b">
                            <li>' . $li_first . '</li>
                            ' . $li_trim . '
                            <li>' . $li . '</li>
                            ' . $li2 . '
                        </ol>
                            
                    </td>
                </tr>
            </table>';
        ///////// below code added by shubhranshu for elearning class only for xp
        if ($tenant_details->tenant_id == 'T02') {
            if ($classes->course_id == 166 || $classes->course_id == 167) {
                $data = '<br><br>
                <table style="font-size:15px">
                    <tr>
                        <td>Your seat has been booked. Please pay the class fees on or before the class start date.
                            Booking for <strong>' . $trainee . $comp_var . '</strong> for \'Course: <b>' . $courses->crse_name . '</b>, Class: <b>' . $classes->class_name . '</b>, Certificate Code: ' . $courseLevel . '\'.<br><br>
                            <strong>Class start date:</strong>
                            ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                            <br><br>
                            <strong>Class end date:</strong>
                            ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '

                            <br>
                            <br>
                            <strong>Location:</strong>
                            ' . $ClassLoc . '<br><br>
                            <strong>Contact Details: </strong>
                            ' . $contact_details . '
                                <br><br>
                            <strong style="font-size:13px">Remark *: </strong>
                            <ol style="font-size:13px;color:#4f4b4b">
                                <li>' . $li_first . '</li>
                                <li>Trim finger nails and remove nail polish.</li>
                                ' . $li2 . '
                            </ol>
                        </td>
                    </tr>
                </table>';
            }
        }

        $this->load->helper('pdf_reports_helper');
        generate_booking_acknowledge_pdf($data, $tenant_details, $booking_no, $booking_date);
    }

    /**
     * create enroll message
     */
    public function update_enroll_message($trainee_id, $class_id) {
        $tenant_id = $this->tenant_id;
        $trainee_id = explode('-', $trainee_id);
        $trainee_id = array_filter($trainee_id);
        $classes = $this->class->get_class_details($tenant_id, $class_id);
        $trainee = '';
        foreach ($trainee_id as $row) {
            $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $row, $tenant_id);
            $name = $trainee_name->first . ' ' . $trainee_name->last;
            $trainee .= ($trainee_name->gender == 'MALE') ? 'Mr. ' . $name . ', ' : 'Ms. ' . $name . ', ';
        }
        $trainee = rtrim($trainee, ', ');
        $this->session->set_flashdata("success", "'$trainee' has been enrolled successfully to class-'$classes->class_name'.");
        redirect('class_trainee');
    }

    /**
     * function to get the book count of a class
     */
    public function get_class_booked_count() {
        $class = $this->input->post('class');
        $result = $this->class->get_class_details($this->tenant_id, $class);
        if ($result->class_pymnt_enrol == 'PDENROL') {
            $totalbooked = $this->class->get_class_booked($result->course_id, $result->class_id, $this->tenant_id);
            $available = $result->total_seats - $totalbooked;
        } else {
            $available = 'any';
        }
        echo $available;
    }

    /**
     * calculate gst for subsidy
     */
    public function calculate_gst_get_class_for_subsidy() {
        $tenant_id = $this->tenant_id;
        extract($_POST);
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $classes = $this->class->get_class_details($tenant_id, $class);
        $courses = $this->course->get_course_detailse($classes->course_id);
        $invoice = $this->classtraineemodel->get_allinvoice($tenant_id, $payid);
        $enrol_payment_due_details = $this->classtraineemodel->get_enrol_payment_due_details($payid, $user);
        $individual_discount_amount = round((($enrol_payment_due_details->discount_rate / 100) * $classes->class_fees), 2);
        $gst_onoff = $courses->gst_on_off;
        $subsidy_after_before = $courses->subsidy_after_before;
        $class_fees = $classes->class_fees;
        $feesdue = $class_fees - $individual_discount_amount;
        $gst_rate = $invoice->gst_rate;
        $result = $this->classtraineemodel->calculate_net_due($gst_onoff, $subsidy_after_before, $feesdue, $subsidy, $gst_rate);
        $arr = array();
        $subsidy_per = ($subsidy * 100) / $feesdue;
        if ($result < 0) {
            $arr['label'] = "The net amount is negative";
            $arr['amount'] = "";
        } else {
            $arr['label'] = "";
            $arr['amount'] = number_format($result, 2);
            $arr['subsidy_per'] = number_format($subsidy_per, 2);
        }
        echo json_encode($arr);
        exit();
    }

    /**
     * to update TG Number
     */
    public function update_tgnumber() {
        $tenant_id = $this->tenant_id;
        extract($_POST);
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $invoice = $this->classtraineemodel->get_allinvoice($tenant_id, $payid);
        $tg = $this->classtraineemodel->get_tg_number($tenant_id, $payid, $user);
        $result = $this->classtraineemodel->update_tgnumber($tenant_id, $payid, $tg_number, $user);
        if ($result == TRUE) {
            if ($tg != '') {
                $data = array('user_id' => $user, 'class_id' => $class, 'payment_due_id' => $payid, 'tg' => $tg);
                $previous_data = json_encode($data);
                user_activity(18, $user, $previous_data);
            }
            $this->db->cache_delete_all();
            echo 'success';
        } else {
            echo 'Fail';
        }
        exit();
    }

    public function update_eidnumber() {
        $tenant_id = $this->tenant_id;
        extract($_POST);
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $result = $this->classtraineemodel->update_eidnumber($tenant_id, $class, $user, $eid_number, $payid);
        if ($result == TRUE) {
            if ($tg != '') {
                $data = array('eid_number' => $eid_number, 'class_id' => $class, 'payment_due_id' => $payid);
                $previous_data = json_encode($data);
                user_activity(18, $user, $previous_data);
            }
            $this->db->cache_delete_all();
            echo 'success';
        } else {
            echo 'Fail';
        }
        exit();
    }

    /**
     * calculate gst for subsidy
     */
    public function calculate_gst_get_class_for_subsidy_pers() {
        $tenant_id = $this->tenant_id;
        extract($_POST);
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $classes = $this->class->get_class_details($tenant_id, $class);
        $courses = $this->course->get_course_detailse($classes->course_id);
        $invoice = $this->classtraineemodel->get_allinvoice($tenant_id, $payid);

        $gst_onoff = $courses->gst_on_off;
        $subsidy_after_before = $courses->subsidy_after_before;
        $class_fees = $classes->class_fees;
        $feesdue = $class_fees - $invoice->total_inv_discnt;
        $gst_rate = $invoice->gst_rate;


        $subsidy = ($subsidy_per * $feesdue) / 100;
        $result = $this->classtraineemodel->calculate_net_due($gst_onoff, $subsidy_after_before, $feesdue, $subsidy, $gst_rate);
        $arr = array();
        if ($result < 0) {
            $arr['label'] = "The net amount is negative";
            $arr['amount'] = "";
        } else {
            $arr['label'] = "";
            $arr['amount'] = number_format($result, 2);
            $arr['subsidy'] = number_format($subsidy, 2);
        }
        echo json_encode($arr);
        exit();
    }

    /**
     * calculate gst for subsidy
     */
    public function calculate_gst_for_subsidy() {
        extract($_POST);
        $fees_due = $class_fees - $discount_amount;
        $result = $this->classtraineemodel->calculate_net_due($gst_onoff, $subsidy_after_before, $fees_due, $subsidy, $gst_rate);
        $arr = array();
        $subsidy_per = ($subsidy * 100) / $fees_due;
        if ($result < 0) {
            $arr['label'] = "NEGATIVE Total Fees Due NOT ALLOWED. Please correct Discount AND/ OR Subsidy Amounts.";
            $arr['amount'] = "";
        } else {
            $arr['label'] = "";
            $arr['amount'] = number_format($result, 2, '.', '');
            $arr['subsidy_per'] = number_format($subsidy_per, 2, '.', '');
            $gst_amount = $this->classtraineemodel->calculate_gst($gst_onoff, $subsidy_after_before, $fees_due, $subsidy, $gst_rate);
            $arr['gst_amount'] = number_format($gst_amount, 2, '.', '');
        }
        echo json_encode($arr);
        exit();
    }

    /**
     * calculate gst for subsidy
     */
    public function calculate_discount_percentage() {
        extract($_POST);
        $disc_rate = ($discount_amount / $class_fees) * 100;
        $fees_due = $class_fees - $discount_amount;
        $subsidy_per = ($subsidy * 100) / $fees_due;
        $result = $this->classtraineemodel->calculate_net_due($gst_onoff, $subsidy_after_before, $fees_due, $subsidy, $gst_rate);
        $arr = array();
        if ($result < 0) {
            $arr['label'] = "NEGATIVE Total Fees Due NOT ALLOWED. Please correct Discount AND/ OR Subsidy Amounts.";
            $arr['amount'] = "";
        } else {
            $arr['label'] = "";
            $arr['amount'] = number_format($result, 4, '.', '');
            $arr['disc_rate'] = number_format($disc_rate, 4, '.', '');
            $arr['subsidy_per'] = number_format($subsidy_per, 4, '.', '');
            $gst_amount = $this->classtraineemodel->calculate_gst($gst_onoff, $subsidy_after_before, $fees_due, $subsidy, $gst_rate);
            $arr['gst_amount'] = number_format($gst_amount, 4, '.', '');
        }
        echo json_encode($arr);
        exit();
    }

    /**
     * calculate gst for subsidy
     */
    public function calculate_gst_for_subsidy_percentage() {
        extract($_POST);
        $disc_rate = ($discount_amount / $class_fees) * 100;
        $fees_due = $class_fees - $discount_amount;
        $subsidy = ($subsidy_per * $fees_due) / 100;
        $result = $this->classtraineemodel->calculate_net_due($gst_onoff, $subsidy_after_before, $fees_due, $subsidy, $gst_rate);
        $arr = array();
        if ($result < 0) {
            $arr['label'] = "NEGATIVE Total Fees Due NOT ALLOWED. Please correct Discount AND/ OR Subsidy Amounts.";
            $arr['amount'] = "";
        } else {
            $arr['label'] = "";
            $arr['amount'] = number_format($result, 4, '.', '');
            $arr['subsidy'] = number_format($subsidy, 4, '.', '');
            $gst_amount = $this->classtraineemodel->calculate_gst($gst_onoff, $subsidy_after_before, $fees_due, $subsidy, $gst_rate);
            $arr['gst_amount'] = number_format($gst_amount, 4, '.', '');
        }
        echo json_encode($arr);
        exit();
    }

    /**
     * checks trainee enrolled in a class
     */
    function check_userenroll($class) {
        $search_select = $this->input->post('search_select');
        $taxcode_id = $this->input->post('taxcode_id');
        $trainee_id = $this->input->post('trainee_id');
        if ($search_select == 1) {
            $user_id = $taxcode_id;
        } else {
            $user_id = $trainee_id;
        }
        if ($user_id) {
            $exists = $this->classtraineemodel->check_userenroll($user_id, $class);
            if (!$exists) {
                $this->form_validation->set_message('check_userenroll', "Trainee is enrolled in the class.");
                return FALSE;
            }
            return TRUE;
        }
    }

    /**
     * checks availabel class
     */
    function check_class_available($class) {
        $tenant_id = $this->tenant_id;
        $course = $this->input->post('course');
        $classes = $this->class->get_class_details($tenant_id, $class);
        $totalbooked = $this->class->get_class_booked($course, $class, $tenant_id);
        $available = ($classes->total_seats - $totalbooked);
        if ($available <= 0) {
            $this->form_validation->set_message('check_class_available', "There are no seats available in this class");
            return FALSE;
        }
        return TRUE;
    }

    /*
     * This function loads the Bulk Enrollment form.
     */

    public function bulk_enrollment() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $data['companies'] = $companies = $this->classtraineemodel->get_active_tenant_company($tenant_id);
        $data['courses'] = $disp_courses = $this->get_active_classcourse_list_by_tenant($tenant_id);

        $company = $this->input->post('company');
        $course = $this->input->post('course');
        $class = $this->input->post('class');
        $salesexec = $this->input->post('salesexec');

        if ($this->input->post('upload')) {
            ////////below added by shubhranshu to prevent enrol for invoice paid/partpaid company///////start////
            $check_invoice = $this->classtraineemodel->check_if_invoice_paid($company, $course, $class);
            if (!empty($check_invoice)) {
                $this->session->set_flashdata('error', 'You can not enroll to this class since the invoice is Already paid/partpaid.');
            } else {//////////shubhranshu code end////////////////////////////////////
                $config['upload_path'] = './uploads/';
                $config['allowed_types'] = '*';
                $config['max_size'] = '2048';
                $config['max_width'] = '1024';
                $config['max_height'] = '768';

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
                    $data['error'] = $this->upload->display_errors();
                } else {
                    $data = $this->upload->data();
                    $this->load->library('excel_reader');
                    $this->excel_reader->setOutputEncoding('CP1251');
                    $read_perm = $this->excel_reader->read($data['full_path']);
                    if ($read_perm == 'FALSE') {
                        $data['error'] = 'File is not readable.';
                    } else {
                        $excel_data = $this->excel_reader->sheets[0][cells];
                        $class_detail = $this->class->get_class_details($tenant_id, $class);
                        $trainee = $this->validate_bulk_enroll($excel_data, $class, $course, $company, $salesexec, $class_detail);

                        if (!empty($trainee)) {
                            $data['details'] = $trainee;
                            //print_r($data);exit;
                            $this->load->helper('export');
                            $files = write_import_enroll_status($trainee, $company);
                            $filesa = write_import_enroll_statussuccess($trainee, $company);
                            $filesb = write_import_enroll_statusfailure($trainee, $company);
                        } else {
                            $data['error'] = $this->class_error_msg;
                        }

                        unlink('./uploads/' . $data['file_name']);
                    }
                }
            }
        }

        $data['courses'] = $disp_courses;
        $data['companies'] = $companies;
        if (!empty($course)) {
            $data['classes'] = $this->get_trainee_classes_forcourse($course);
        }
        if (!empty($class)) {
            $data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $course, $class_detail->sales_executive);
        }
        if ($this->user->role_id == 'SLEXEC') {
            $data['salesexec_check'] = 1;
        }

        $data['files'] = $files;
        $data['filesa'] = $filesa;
        $data['filesb'] = $filesb;
        $data['privilage'] = $this->manage_tenant->get_privilage(); //added by shubhranshu
        $data['page_title'] = 'Class Trainee';
        $data['main_content'] = 'classtrainee/bulkenrollment';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /**
     * validate bulk upload
     */
    public function validate_bulk_enroll($excel_data, $class, $course, $company_id, $salesexec, $class_detail) {
        $tenant_id = $this->tenant_id;
        $curuser_id = $this->session->userdata('userDetails')->user_id;
        $enrol_type = $excel_data[1][2];
        $enrol_retake_pay_mode = $excel_data[2][2];
        unset($excel_data[1]);
        unset($excel_data[2]);
        unset($excel_data[3]);
        $total_booked_seats = $this->class->get_class_booked($class_detail->course_id, $class_detail->class_id, $tenant_id);
        $available_seats = ($class_detail->total_seats - $total_booked_seats);
        if ((($class_detail->class_pymnt_enrol == 'PDENROL') && ($available_seats - count($excel_data) < 0))) {
            $this->class_error_msg = "Total seats being enrolled, exceeds total available seats. Total seats available: " . $available_seats . ". Please reduce the number of enrollments and try again.";
            return;
        } else {
            $this->class_error_msg = '';
        }
        $insert_data = array();
        $restrict_arr = array(); //added by shubhranshu
        foreach ($excel_data as $key => $row) {
            $status = '';
            $excel = array();
            $excel['taxcode'] = $row[2];
            $excel['enrollment_type'] = $enrol_type;
            $excel['enrol_retake_pay_mode'] = $enrol_retake_pay_mode;
            $excel['tg_number'] = $row[3];
            $excel['subsidy_amount'] = $row[4];
            $excel['subsidy_recd_on'] = str_replace('/', '-', $row[5]);
            $insert_data[$key] = $excel;
            $error_msg = $this->check_is_empty($excel);
            //////////////////added by shubhranshu/////////////////////////

            $restriction = $this->chk_nric_restriction($excel['taxcode'], 'BULK_ENROL'); //added by shubhranshu to prevent restricted nric on 22/03/2019
            if ($restriction > 0) {
                $restrict_arr[] = $excel['taxcode'];
                //$error_msg .= 'Restricted NRIC.';
            }//////////////////added by shubhranshu******/////////////////////////
            if (!empty($error_msg)) {
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'FAILED';
            } else {
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'PASSED';
            }
            if ($company_id[0] == "T") {
                $user_id = $this->classtraineemodel->get_id_for_taxcode_tenant(trim($excel['taxcode']), $company_id);
            } else {
                $user_id = $this->classtraineemodel->get_id_for_taxcode_company(trim($excel['taxcode']), $company_id);
            }
            if (empty($user_id)) {
                $error_msg .= ' Invalid Employee Credentials.';
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'FAILED';
            } else {
                $insert_data[$key]['user_id'] = $user_id;
            }
            $user_enrol_status = $this->classtraineemodel->check_user_enrol_status($user_id, $course, $class, $tenant_id);
            if ($user_enrol_status > 0) {
                $error_msg .= ' Trainee enrolled in this class.';
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'FAILED';
            }
        }
        /////added by shubhranshu 0n 26/03/2019
        $restrict_data = array();
        $restrict_data['flag'] = $restrict_arr ? 'true' : 'false';
        $restrict_data['restrict_arr'] = $restrict_arr;
        ////////////////        
        $company_details = $this->company->get_company_details($tenant_id, $company_id);
        $insert_status = $this->classtraineemodel->create_bulk_enrol($tenant_id, $insert_data, $company_id, $course, $salesexec, $class, $class_detail, $curuser_id, $company_details);
        $insert_status['flag'] = $restrict_data['flag']; //added by shubhranshu 0n 26/03/2019
        return $insert_status;
    }

    /**
     * This method checks if the mandatory fields in XLS are empty or not
     */
    private function check_is_empty($excel) {
        $error_msg = '';
        if (empty($excel['taxcode']))
            $error_msg .= 'Employee Taxcode Required.';
        if (empty($excel['enrollment_type'])) {
            $error_msg .= 'Enrollment Type Required.';
        } else {
            if (($excel['enrollment_type'] != 'FIRST')) {
                if ($excel['enrollment_type'] != 'RETAKE') {
                    $error_msg .='Invalid Enrollment Type';
                }
            }
        }
        if ($excel['enrollment_type'] == 'RETAKE') {
            if (empty($excel['enrol_retake_pay_mode'])) {
                $error_msg .= 'Enrollment Type being RE-TAKE, Mode of Payment is required.';
            } else {
                if ($excel['enrol_retake_pay_mode'] != 'REQUIRED') {
                    if ($excel['enrol_retake_pay_mode'] != 'BYPASS') {
                        $error_msg .='Invalid Re-take Payment Mode.';
                    }
                }
            }
        }
        if (!empty($excel['subsidy_amount'])) {
            if (!is_numeric($excel['subsidy_amount'])) {
                $error_msg .= 'Invalid Subsidy Amount.';
            } else {
                if (empty($excel['subsidy_recd_on'])) {
                    $error_msg .= 'Subsidy recd. on date required, as subsidy amount is present.';
                } else {
                    if (strtotime($excel['subsidy_recd_on']) === FALSE) {
                        $error_msg .= 'Invalid Subsidy Received on Date Format.';
                    }
                }
            }
        }
        return $error_msg;
    }

    /**
     * function to get company trainees
     */
    public function get_companytrainees() {

        $tenant_id = $this->tenant_id;
        $company = $this->input->post('company');
        $class = $this->input->post('class');
        // $trainees = '';

        $check_inovice = $this->classtraineemodel->check_invoice($tenant_id, $company, $class);
        $check_inovice->pymnt_due_id;
        if ($check_inovice->pymnt_due_id > 0) {
            echo json_encode($check_inovice);
            exit();
        } else {
            $status = $this->classtraineemodel->clean_orpham($tenant_id, $class);
            if ($company[0] == 'T') {
                $trainees = $this->company->get_tenent_active_users($tenant_id, $company, $class);
            } else if ($company && $class) {

                $trainees = $this->company->get_company_trainees($tenant_id, $company, $class);
            }
            /////added by shubhranshu to check block list nric as per client requirement on 25/03/2019
//        $block_list_nric = array();
//        foreach($trainees as $s){ 
//            if($this->chk_nric_restriction($s->tax_code)){
//                $block_list_nric[] = $s->tax_code;
//                
//            }
//        }//echo print_r($block_list_nric);exit;
//         $trainees['blocklist'] = $block_list_nric;
            //////////////////******/////////////////////////////////////////////////////////////////////////////
            echo json_encode($trainees);
            exit();
        }
    }

    ////// below function added by shubhranshu to speed up the re-schedule form
    public function reschedule_company_json() {
        $tenant_id = $this->tenant_id;
        $companies = $this->company->get_activeuser_companies_for_tenant($tenant_id, 1);
        if ($companies) {
            foreach ($companies as $row) {
                $comp['company'][] = array(
                    'key' => $row->company_id,
                    'label' => $row->company_name,
                );
            }
        }

        echo json_encode($comp);
        exit();
    }

    /*
     * This function loads the Re-Schedule form.
     */

    public function re_schedule() {
        //$this->output->enable_profiler(true);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $data['courses'] = $this->classtraineemodel->get_active_course_classenroll_list_by_tenant($tenant_id);
        $data['companies'] = $this->company->get_activeuser_companies_for_tenant($tenant_id, 0);
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->load->library('form_validation');
            $submit = $this->input->post('submit');
            if ($submit == 'Search' || $submit == 'reschedule') {
                $type = $this->input->post('type');
                if ($type == 1 || $type == 4) {
                    $taxcode_id = ($type == 1) ? $this->input->post('taxcode_id') : $this->input->post('taxcode_user_id');
                    if (!empty($taxcode_id)) {
                        $users = $this->classtraineemodel->get_user_details($tenant_id, $taxcode_id);
                        $name = $users->first_name . ' ' . $users->last_name . ' (' . $users->tax_code . ')';
                        $data['users'] = ($users->gender == 'FEMALE') ? 'Ms.' . $name : 'Mr.' . $name;
                        $active_enroll_class = $this->classtraineemodel->get_active_class_enrol($tenant_id, '', $taxcode_id);
                        $data['active_enroll_class'] = get_course_class_starttime($active_enroll_class);
                        $data['active_enroll_course_id'] = get_course_id($active_enroll_class);
                        $data['active_enroll_lock_att_status'] = get_active_class_att_status($active_enroll_class);
                        //Added by abdulla for TPG re_schedule
                        //$data['active_enroll_eid_no'] = get_eid_id($active_enroll_class);
                        //$data['active_enroll_tpg_status'] = get_active_class_tpg_status($active_enroll_class);
                        $active_enroll_class_id = get_class_id($active_enroll_class);
                        //$reschedule_enroll_class = $this->classtraineemodel->get_reschedule_class_enrol($tenant_id, '', $active_enroll_class_id);////commented by shubhranshu
                        $reschedule_enroll_class = $this->classtraineemodel->get_reschedule_class_enrol($tenant_id, $data['active_enroll_course_id'], $active_enroll_class_id); ////added by shubhranshu to show only the enrolled course id list
                        foreach ($reschedule_enroll_class as $k => $row) {
                            if ($row->class_pymnt_enrol == 'PDENROL') {
                                $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                                if ($totalbooked >= $row->total_seats) {
                                    unset($reschedule_enroll_class[$k]);
                                }
                            }
                        }
                        $data['reschedule_enroll_class'] = get_course_class_starttime($reschedule_enroll_class);
                        $data['reschedule_enroll_course_id'] = get_course_id($reschedule_enroll_class);
                        $data['reschedule_enroll_lock_att_status'] = get_reschedule_class_att_status($reschedule_enroll_class);
                    }
                    if ($type == 1) {
                        $this->form_validation->set_rules('tax_code', 'Taxcode', 'required');
                    } else {
                        $this->form_validation->set_rules('trainee_name_serach', 'Trainee Name', 'required');
                    }
                    if ($submit == 'reschedule') {

                        $reschedule_class = $this->input->post('reschedule_class');
                        $this->form_validation->set_rules('active_class', 'Active Enrollment Class', 'required');
                        $this->form_validation->set_rules('reschedule_class', 'Reschedule Enrollment Class', 'required');
                    }
                } else if ($type == 3) {
                    $taxcode_id = $this->input->post('trainee_id');
                    if (!empty($taxcode_id)) {
                        $users = $this->classtraineemodel->get_user_details($tenant_id, $taxcode_id);
                        $name = $users->first_name . ' ' . $users->last_name . ' (' . $users->tax_code . ')';
                        $data['users'] = ($users->gender == 'FEMALE') ? 'Ms.' . $name : 'Mr.' . $name;
                        $active_enroll_class = $this->classtraineemodel->get_active_class_enrol($tenant_id, '', $taxcode_id);
                        $data['active_enroll_class'] = get_course_class_starttime($active_enroll_class);
                        $data['active_enroll_course_id'] = get_course_id($active_enroll_class);
                        $data['active_enroll_lock_att_status'] = get_active_class_att_status($active_enroll_class);
                        //Added by abdulla for TPG re_schedule
                        //$data['active_enroll_eid_no'] = get_eid_id($active_enroll_class);
                        //$data['active_enroll_tpg_status'] = get_active_class_tpg_status($active_enroll_class);
                        $active_enroll_class_id = get_class_id($active_enroll_class);
                        //$reschedule_enroll_class = $this->classtraineemodel->get_reschedule_class_enrol($tenant_id, '', $active_enroll_class_id);
                        $reschedule_enroll_class = $this->classtraineemodel->get_reschedule_class_enrol($tenant_id, $data['active_enroll_course_id'], $active_enroll_class_id); ////added by shubhranshu to show only the enrolled course id list
                        foreach ($reschedule_enroll_class as $k => $row) {
                            if ($row->class_pymnt_enrol == 'PDENROL') {
                                $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                                if ($totalbooked >= $row->total_seats) {
                                    unset($reschedule_enroll_class[$k]);
                                }
                            }
                        }
                        $data['reschedule_enroll_class'] = get_course_class_starttime($reschedule_enroll_class);
                        $data['reschedule_enroll_course_id'] = get_course_id($reschedule_enroll_class);
                        $data['reschedule_enroll_lock_att_status'] = get_reschedule_class_att_status($reschedule_enroll_class);
                    }
                    $this->form_validation->set_rules('trainee_name', 'Trainee', 'required');
                    if ($submit == 'reschedule') {
                        $reschedule_class = $this->input->post('reschedule_class');
                        $this->form_validation->set_rules('active_class', 'Active Enrollment Class', 'required');
                        $this->form_validation->set_rules('reschedule_class', 'Reschedule Enrollment Class', 'required');
                    }
                } else {
                    $course_id = $this->input->post('course_id');
                    if (!empty($course_id)) {
                        $active_enroll_class = $this->classtraineemodel->get_active_class_enrol($tenant_id, $course_id, '');
                        $activeenroll_class = get_class_starttime($active_enroll_class);
                        if ($activeenroll_class) {
                            foreach ($activeenroll_class as $k => $v) {
                                $data['course_active_enroll_class'][$k] = $v;
                            }
                        }

                        $reschedule_enroll_class = $this->classtraineemodel->get_reschedule_class_enrol($tenant_id, $course_id, '');
                        foreach ($reschedule_enroll_class as $k => $row) {
                            if ($row->class_pymnt_enrol == 'PDENROL') {
                                $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                                if ($totalbooked >= $row->total_seats) {
                                    unset($reschedule_enroll_class[$k]);
                                }
                            }
                        }
                        $rescheduleenroll_class = get_class_starttime($reschedule_enroll_class);
                        if ($rescheduleenroll_class) {
                            foreach ($rescheduleenroll_class as $k => $v) {
                                $data['course_reschedule_enroll_class'][$k] = $v;
                            }
                        }
                        $class_id = $this->input->post('course_active_class');
                        if ($class_id) {
                            $trainees = $this->classtraineemodel->get_all_trainee_details($tenant_id, '', $class_id);
                            if ($trainees) {
                                foreach ($trainees as $row) {
                                    $data['course_trainee'][$row->user_id] = $row->first_name . ' ' . $row->last_name . ' (Tax Code: ' . $row->tax_code . ')';
                                }
                            }
                        }
                        $reschedule_class = $this->input->post('course_reschedule_class');
                    }
                    $this->form_validation->set_rules('course_id', 'Course', 'required');
                    $this->form_validation->set_rules('course_active_class', 'Active Enrollment Class', 'required');
                    $this->form_validation->set_rules('course_reschedule_class', 'Reschedule Enrollment Class', 'required');
                    $this->form_validation->set_rules('control_6[]', 'Trainee', 'required');
                }

                if ($this->form_validation->run() == TRUE && !empty($reschedule_class)) {
                    $data['reschedule_classes'] = $class = $this->class->get_class_details($tenant_id, $reschedule_class);

                    $data['reshedule_courses'] = $course = $this->course->get_course_detailse($class->course_id);
                    $data['courseLevel'] = $this->course->get_metadata_on_parameter_id($course->certi_level);
                    $data['courseManager'] = $this->course->get_managers($course->crse_manager);
                    $data['ClassPay'] = $this->course->get_metadata_on_parameter_id($class->class_pymnt_enrol);
                    $data['SalesExec'] = $this->class->get_class_salesexec($tenant_id, $class->course_id, $class->sales_executive);
                    $totalbooked = $this->class->get_class_booked($class->course_id, $class->class_id, $tenant_id);
                    $data['available'] = ($class->total_seats - $totalbooked);
                    $data['totalbooked'] = $totalbooked;
                    $data['ClassLang'] = $this->course->get_metadata_on_parameter_id($class->class_language);
                    $data['ClassLoc'] = $this->get_classroom_location($class->classroom_location, $class->classroom_venue_oth);
                    $data['ClassTrainer'] = $this->class->get_trainer_names($class->classroom_trainer);
                    $data['LabTrainer'] = $this->class->get_trainer_names($class->lab_trainer);
                    $data['Assessor'] = $this->class->get_trainer_names($class->assessor);
                    $data['TrainingAide'] = $this->class->get_course_manager_names($class->training_aide);
                }
            } elseif ($submit == 'save') {
                $this->form_validation->set_rules('reschedule_reason', 'Reschedule reason', 'required');
                if ($this->form_validation->run() == TRUE) {
                    $result = $this->classtraineemodel->create_reschedule();
                    //echo "final";exit;
                    if ($result == TRUE) {
                        $type = $this->input->post('type');
                        if ($type == 1 || $type == 4) {
                            $reschedule_class = $this->input->post('reschedule_class');
                            $user_id = ($type == 1) ? $this->input->post('taxcode_id') : $this->input->post('taxcode_user_id');
                        } else if ($type == 3) {
                            $reschedule_class = $this->input->post('reschedule_class');
                            $user_id = $this->input->post('trainee_id');
                        } else {
                            $reschedule_class = $this->input->post('course_reschedule_class');
                            $user_id = $this->input->post('control_6');
                        }
                        $users = $this->classtraineemodel->get_users_details($tenant_id, $user_id);
                        $username = '';
                        foreach ($users as $user) {
                            $name = $user->first_name . ' ' . $user->last_name . ', ';
                            $username .= ($users->gender == 'FEMALE') ? 'Ms.' . $name : 'Mr.' . $name;
                        }
                        $username = rtrim($username, ', ');
                        $class = $this->class->get_class_details($tenant_id, $reschedule_class)->class_name;
                        $this->session->set_flashdata("success", "'$username' rescheduled successfully to class-'$class'.");
                    } else {
                        $this->session->set_flashdata("error", "Unable to reschedule '$username' to class-'$class'. Please try again later.");
                    }
                    redirect('class_trainee');
                }
            }
        }
        $data['page_title'] = 'Class Trainee';
        $data['main_content'] = 'classtrainee/reschedule';
        //$data['sideMenuData'] = $this->sideMenu;
        $this->load->view('layout', $data);
    }

    /**
     * function to get sales class executive
     */
    public function get_class_salesexec() {
        $tenant_id = $this->tenant_id;
        $course_id = $this->input->post('course');
        $class_id = $this->input->post('class');
        //$class_details = $this->class->get_class_details($tenant_id, $class_id);
        $result = $this->class->get_all_salesexec_course($tenant_id, $course_id);
        echo json_encode($result);
        exit();
    }

    /*
     * This function loads the Mark Attendance form.
     */

    public function mark_attendance($message = NULL) {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $subsidy = $this->input->post('subsidy');
        $sort_by = $this->input->get('b');
        $sort_order = $this->input->get('o');
        $class_details = $this->class->get_class_by_id($tenant_id, $course_id, $class_id);

        $from_date = parse_date($class_details->class_start_datetime, SERVER_DATE_TIME_FORMAT); ///added by shubhranshu
        $to_date = parse_date($class_details->class_end_datetime, SERVER_DATE_TIME_FORMAT); //added by shubhranshu
        $week_start_date = parse_date($this->input->post('week_start'), CLIENT_DATE_FORMAT); //added by shubhranshu
        //echo print_r($from_date);print_r($to_date);print_r($week_start_date);exit;

        $week = $this->input->post('week');
        $export = $this->input->post('export');
        $export1 = $this->input->post('export1');
        $this->load->helper('attendance_helper');
        if (!empty($export)) {
            $class_details = $this->class->get_class_details_for_report($tenant_id, $course_id, $class_id);
            $class_start = parse_date($class_details->class_start_datetime, SERVER_DATE_TIME_FORMAT);
            $class_end = parse_date($class_details->class_end_datetime, SERVER_DATE_TIME_FORMAT);
            if (empty($class_start))
                $class_start = new DateTime();
            if (empty($class_end))
                $class_end = new DateTime();
            $class_schedule = $this->class->get_all_class_schedule($tenant_id, $class_id);
            $class_schedule_data = array();
            foreach ($class_schedule as $row) {
                $session_arr = array('S1' => '1', 'BRK' => '3', 'S2' => '2');
                $class_schedule_data[date('d/m/y', strtotime($row['class_date']))][$session_arr[$row['session_type_id']]] = date('h:i A', strtotime($row['session_start_time']));
            }

            if ($export == 'xls') {
                $results = $this->classtraineemodel->get_class_trainee_list_for_attendance($tenant_id, $course_id, $class_id, $subsidy, $class_start, $class_end, $sort_by, $sort_order);
                $this->load->helper('export_helper');
                export_attendance($results, $class_details, $class_start, $class_end, $class_schedule_data);
            } else {
                $results = $this->classtraineemodel->get_class_trainee_list_for_attendance($tenant_id, $course_id, $class_id, $subsidy, $class_start, $class_end, $sort_by, $sort_order);
                $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
                $tenant_details->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($tenant_details->tenant_state), ', ');
                $tenant_details->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($tenant_details->tenant_country), ', ');
                $mark_count = $this->classtraineemodel->get_rows_count($course_id, $class_id);

                if ($export == 'xls_week') {
                    $this->load->helper('export_helper');
                    if (TENANT_ID == 'T02') {
                        return generate_class_attendance_sheet_xls_xp($results, $class_details, $class_start, $class_end, $tenant_details, $class_schedule_data);
                    } else {
                        return generate_class_attendance_sheet_xls($results, $class_details, $class_start, $class_end, $tenant_details, $class_schedule_data);
                    }
                }
                $this->load->helper('pdf_reports_helper');
                if ($export == 'pdf') {
                    //return generate_class_attendance_pdf($results, $class_details, $tenant_details, $class_schedule_data, $mark_count);
                    //print_r($results);exit;
                    return generate_class_attendance_pdf($results, $class_details, $tenant_details, $class_schedule_data, $mark_count); // removed mark count by shubhranshu
                } else if ($export == 'pdf_week') {
                    if (TENANT_ID == 'T02') {
                        return generate_class_attendance_sheet_pdf_xp($results, $class_details, $tenant_details, $class_schedule_data);
                    } else {
                        return generate_class_attendance_sheet_pdf($results, $class_details, $tenant_details, $class_schedule_data);
                    }
                }
            }
        } else {
            if ($export1 == 'lock') {
                $lock_msg = $this->classtraineemodel->lock_class_attendance($tenant_id, $course_id, $class_id);
                if ($lock_msg == TRUE) {
                    $this->session->set_flashdata("success", "Succesfully Locked.");
                } else {
                    $this->session->set_flashdata("error", "Something went wrong while locking.");
                }
            } else if ($export1 == 'unlock') {
                $lock_msg = $this->classtraineemodel->unlock_class_attendance($tenant_id, $course_id, $class_id);
                if ($lock_msg == TRUE) {
                    $this->session->set_flashdata("success", "Succesfully Unocked");
                } else {
                    $this->session->set_flashdata("error", "Somthing went wrong while Unlocking !");
                }
            }


            $data = get_data_for_renderring_attendance($tenant_id, $course_id, $class_id, $subsidy, $from_date, $to_date, $week_start_date, $week, $sort_by, $sort_order, '');

            $data['class_schedule'] = $this->class->get_all_class_schedule($tenant_id, $class_id);
            $att = $this->classtraineemodel->get_attendance_lock_status($tenant_id, $course_id, $class_id);
            $data['lock_status'] = $att->lock_status;
            $data['class_start_datetime'] = $att->class_start_datetime;
            $data['user'] = $this->user;
            $data['controllerurl'] = 'class_trainee/mark_attendance';
            $data['page_title'] = 'Class Trainee Enrollment - Mark Attendance';
            $data['main_content'] = 'classtrainee/markattendance';
            //$data['week_start'] = $from_date;
            //$data['sideMenuData'] = $this->sideMenu;
            if (!empty($message))
                $data['message'] = $message;
            $this->load->view('layout', $data);
        }
    }

    /*
     * Upload course session attendance API
     */

    public function mark_attendance_tpg() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $data['page_title'] = 'TPG Mark Attendance';
        $data['courses'] = $this->course->get_active_course_list_all_tpg($tenant_id, 'classTrainee');
        $course = $this->input->get('course');
        $class = $this->input->get('class');
        $userid = $this->input->get('nric');

        if (!empty($course) && !empty($class) && !empty($userid)) {
            $data['classes'] = $this->class->get_course_class($tenant_id, $course, $mark_attendance, "", "classTrainee");
            $data['tabledata'] = $this->classtraineemodel->get_trainee_sessions_data($tenant_id, $course, $class, $userid);
            $data['nric'] = $this->classtraineemodel->get_enrolled_trainee($tenant_id, $course, $class);
        }

        $data['main_content'] = 'classtrainee/markattendance_tpg';

        $this->load->view('layout', $data);
    }

    public function get_enrolled_trainee() {
        $courseID = $this->input->post('course_id');
        $classID = $this->input->post('class_id');
        $tenant_id = $this->tenant_id;
        $res = $this->classtraineemodel->get_enrolled_trainee($tenant_id, $courseID, $classID);
        $classes_arr = array();
        foreach ($res as $k => $v) {
            $classes_arr[] = array('key' => $k, 'value' => $v);
        }
        echo json_encode($classes_arr);
    }

    /* locking class attendance 
      Author : Prit
      Date   : 03/08/2016 */

    public function lock_class_attendance() {
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $tenant_id = $this->tenant_id;
        $result = $this->classtraineemodel->lock_class_attendance($tenant_id, $course_id, $class_id);
        if ($result == TRUE) {
            echo $sucess = 1;
        } else {
            echo $fail = 0;
        }
    }

    /* locking class attendance 
      Author : Prit
      Date   : 03/08/2016 */

    public function unlock_class_attendance() {
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $tenant_id = $this->tenant_id;
        $result = $this->classtraineemodel->unlock_class_attendance($tenant_id, $course_id, $class_id);
        if ($result == TRUE) {
            echo $sucess = 1;
        } else {
            echo $fail = 0;
        }
    }

// This method is to get class start date & skm code start here
    public function get_class_date() {
        $res = '';
        $class_id = $this->input->post('class_id');
        // $res = $this->classtraineemodel->get_class_stdate($class_id);
        $res = $this->classtraineemodel->schedule_chck($class_id);
        if ($res != '') {
            if ($res == TRUE) {
                echo $xxx1 = 1;
            } else {
                echo $xxx1 = 0;
            }
        }
    }

    // skm code end

    /* this function get the activity log of mark attendance skm start */
    public function mark_att_log($course_id = 0, $class_id = 0) {
        $this->load->model('Activity_Log_Model', 'activitylog');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');

        $course_name = $this->activitylog->get_course_name($course_id);
        $class_name = $this->activitylog->class_name($class_id);

        $data = array(
            'course_id' => $course_id,
            'course_name' => $course_name->crse_name,
            'class_id' => $class_id,
            'class_name' => $class_name->class_name
        );

        $previous_data = json_encode($data);
        user_activity(6, $class_id, $previous_data);
    }

    /**
     * return start and end time for given class_id as json
     */
    public function get_times_for_class() {
        $class_id = $this->input->get("class_id");
        $class_details = $this->class->get_class_details($this->tenant_id, $class_id);
        $data = array("start_date" => date_format_singapore($class_details->class_start_datetime), "end_date" => date_format_singapore($class_details->class_end_datetime));
        header('Content-Type: application / json;
            charset = utf-8');
        echo json_encode(array("success" => true, "data" => $data));
    }

    /**
     * updates attendance data
     */
    public function mark_attendance_update() {
        //$this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content(); /////added by shubhranshu
        $tenant_id = $this->tenant_id;
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $data_table = $this->input->post('mark_attendance');
        $trainees = $this->input->post('trainees');
        $result = $this->classtraineemodel->update_for_mark_attendance($tenant_id, $course_id, $class_id, $data_table, $trainees);

        $message = NULL;
        if (!empty($data_table) && count($data_table) > 0) {
            if ($result == true) {
                $message = 'Attendance has been updated successfully.';
            }
        }
        $this->mark_attendance($message);
    }

    /**
     * gets trainee by tax code for auto complete help 
     */
    public function get_trainees_by_taxcode_autocomplete() {

        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $this->load->model('trainee_model', 'traineemodel');
        $result = $this->traineemodel->trainee_user_list_autocomplete($query_string);
        print json_encode($result);
        exit;
    }

    /**
     * get trainee by taxcode
     */
    public function get_trainees_by_taxcode() {
        $query_string = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->trainee_user_list_autocomplete($query_string);
        print json_encode($result);
        exit;
    }

    /**
     * add trainee to the list
     */
    public function add_trainee_to_list() {
        $taxcode = $this->input->post('taxcode');
        $trainee_data = $this->classtraineemodel->get_trainee_details($taxcode);
        if ($trainee_data) {
            echo '<tr>
            <td><input type = "checkbox" value = "' . $trainee_data [user_id] . '">
            <a class = "small_text1" rel = "modal:open" href = "' . $taxcode . '">' . $taxcode . '</a></td>
            <td>' . $trainee_data [first_name] . ' ' . $trainee_data [last_name] . '</td>
            <td>' . $trainee_data [gender] . '</td>
            <td>' . $trainee_data [dob] . '</td>
            <td>' . $trainee_data[user_id] . '</td>
            <td>' . $trainee_data[registration_date] . '</td>
            </tr>';
        }
    }

    /**
     * get class details
     */
    public function get_class_details() {
        $classid = $this->input->post('classid');
        $class_data = $this->classtraineemodel->get_class_details($classid);
        print_r($class_data);
        echo '<tbody>
            <tr>
            <td width = "15%" class = "td_heading">Course Name:</td>
            <td width = "17%"><label class = "label_font">Corporte Class</label></td>
            <td width = "21%" class = "td_heading">Course Duration:</td>
            <td width = "19%"><label class = "label_font">6 hrs</label></td>
            <td width = "14%" class = "td_heading">Course Manager:</td>
            <td width = "14%"><label class = "label_font">Jim White</label></td>
            </tr>
            <tr>
            <td width = "15%" class = "td_heading">Class Name:</td>
            <td width = "17%"><label class = "label_font">Class 01</label></td>
            <td width = "21%" class = "td_heading">Start Date & Time:</td>
            <td width = "19%"><label class = "label_font">01/05/2014 10:00 AM</label></td>
            <td width = "14%" class = "td_heading">End Date & Time:</td>
            <td width = "14%"><label class = "label_font">05/05/2014 03:00 PM</label></td>
            </tr>
            <tr>
            <td class = "td_heading">Total Seats:</td>
            <td><label class = "label_font">20</label></td>
            <td class = "td_heading">Total Booked:</td>
            <td><label class = "label_font">12</label></td>
            <td class = "td_heading">Total Seats Available:</td>
            <td><label class = "label_font">8</label></td>
            </tr>
            <tr>
            <td class = "td_heading">Class Fees:</td>
            <td><label class = "label_font">$350.00 SGD</label></td>
            <td class = "td_heading">Class Discount:</td>
            <td><label class = "label_font">0.00 %</label></td>
            <td class = "td_heading">Trainer:</td>
            <td><label class = "label_font">Jim White</label></td>
            </tr>
            <tr>
            <td class = "td_heading">Training Aide:</td>
            <td><label class = "label_font">Kim Thomas</label></td>
            <td class = "td_heading">Lab/ Assistant Trainer:</td>
            <td><label class = "label_font">Kim Thomas</label></td>
            <td class = "td_heading">Assessor:</td>
            <td><label class = "label_font">Kim Thomas</label></td>
            </tr>
            <tr>
            <td class = "td_heading">Location/Address:</td>
            <td colspan = "5"><label class = "label_font">Blk 215, Ang Mo Kio Ave 1, #01-877, SINGAPORE  560215 </label></td>                
            </tr>
            <tr>
            <td class = "td_heading">Sales Executive:</td>
            <td><label class = "label_font">Elizabeth Harris, Jim White</label></td>
            <td class = "td_heading">No. of sessions per day:</td>
            <td colspan = "3"><label class = "label_font"> One Session </label></td>
            </tr>

            </tbody>';
    }

    /**
     * function to get trainee taxcode (first name and last name ) in autocomplete
     */
    public function get_all_trainees() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_all_trainee_details($this->tenant_id, $query_string, '');
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . ' (' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * function to get trainee First Name (Tax code) in autocomplete
     */
    public function get_all_trainee_names() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_all_trainee_details($this->tenant_id, "", '', $query_string);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . ' (' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * function to get company trainee taxcode (first name and last name ) in autocomplete
     */
    public function get_all_companytrainees() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $company = $this->input->post('company_id');
        $result = $this->classtraineemodel->get_all_company_trainee_details($this->tenant_id, $query_string, $company);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . ' (' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * get reschedule course details
     */
    public function get_reschedule_course_details() {
        $data = array();
        $tenant_id = $this->tenant_id;
        $course_id = $this->input->post('course_id');
        $active_class = $this->input->post('active_class');
        if (!empty($course_id)) {
            $active_enroll_class = $this->classtraineemodel->get_active_class_enrol($tenant_id, $course_id, '');
            $activeenroll_class = get_class_starttime($active_enroll_class);
            if ($activeenroll_class) {
                foreach ($activeenroll_class as $k => $v) {
                    $data['active'][] = array(
                        'key' => $k,
                        'value' => $v
                    );
                }
            }
            $reschedule_enroll_class = $this->classtraineemodel->get_reschedule_class_enrol($tenant_id, $course_id, $active_class);
            foreach ($reschedule_enroll_class as $k => $row) {
                if ($row->class_pymnt_enrol == 'PDENROL') {
                    $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                    if ($totalbooked >= $row->total_seats) {
                        unset($reschedule_enroll_class[$k]);
                    }
                }
            }
            $rescheduleenroll_class = get_class_starttime($reschedule_enroll_class);
            if ($rescheduleenroll_class) {
                foreach ($rescheduleenroll_class as $k => $v) {
                    $data['reschedule'][] = array(
                        'key' => $k,
                        'value' => $v
                    );
                }
            }
        }

        echo json_encode($data);
        exit();
    }

    /*
     * function to get class users
     */

    public function get_trainee_related_json() {
        $trainee = array();
        $tenant_id = $this->tenant_id;
        $class_id = $this->input->post('class_id');
        if ($class_id) {
            $trainees = $this->classtraineemodel->get_all_trainee_details($tenant_id, '', $class_id);
            if ($trainees) {
                foreach ($trainees as $row) {
                    $trainee['trainee'][] = array(
                        'key' => $row->user_id,
                        'label' => $row->first_name . ' ' . $row->last_name . ' (Tax Code: ' . $row->tax_code . ')',
                    );
                }
            }
        }
        echo json_encode($trainee);
        exit();
    }

    /**
     * get company update payment
     */
    public function get_company_updatepayment() {
        $invoice_id = $this->input->post('invoice');
        $company_id = $this->input->post('company');
        $company_received = $this->classtraineemodel->company_payment_recd($invoice_id);
        $user_paid = array();
        foreach ($company_received as $k => $v) {
            $user_paid[$v->user_id] = $v->amount_recd;
        }
        $company_invoice = $this->classtraineemodel->company_invoice($invoice_id);
        $company_invoice->discount_label = rtrim($this->course->get_metadata_on_parameter_id($company_invoice->discount_type), ', ');

        //$company_invoice->total_inv_amount = number_format((float)round($company_invoice->total_inv_amount,1), 2, '.', '');

        $gst_label = ($company_invoice->total_gst > 0) ? 'GST ON, ' : 'GST OFF ';
        if ($company_invoice->total_gst > 0) {
            $gst_label .= rtrim($this->course->get_metadata_on_parameter_id($company_invoice->gst_rule), ', ');
        }
        $company_invoice->gst_label = $gst_label;
        $company_received = $this->classtraineemodel->company_payment_refund($invoice_id);
        $user_refund = array();
        foreach ($company_received as $k => $v) {
            $user_refund[$v->user_id] = round($v->refund_amount, 2);
        }
        $company_invoice->user_refunded = $user_refund;
        $company_invoice->trainees = $this->classtraineemodel->get_company_trainees_by_payid($company_invoice->pymnt_due_id);
        foreach ($company_invoice->trainees as $k => $row) {
            $amount_paid = empty($user_paid[$row->user_id]) ? 0 : $user_paid[$row->user_id];
            $company_invoice->trainees[$k]->amount_paid = $amount_paid;
            $amt_refunded = (!empty($user_refund[$row->user_id])) ? $user_refund[$row->user_id] : 0;
            $company_invoice->trainees[$k]->amount_remain = round(($row->total_amount_due - $amount_paid + $amt_refunded), 2);
//             $company_invoice->trainees[$k]->amount_remain = round(($row->total_amount_due - $amount_paid + $amt_refunded), 3);skm
        }
//        print_r($company_invoice);
        ///added by shubhranshu to disable the update payment dropdown for everest
        $company_invoice->tenant_id = $this->tenant_id;
        echo json_encode($company_invoice);

        exit();
    }

    /**
     * get company refund payment
     */
    public function get_company_refundpayment() {
        $invoice_id = $this->input->post('invoice');
        $company_id = $this->input->post('company');
        $company_received = $this->classtraineemodel->company_payment_recd($invoice_id);
        $user_paid = array();
        foreach ($company_received as $k => $v) {
            $user_paid[$v->user_id] = $v->amount_recd;
        }
        $company_received = $this->classtraineemodel->company_payment_refund($invoice_id);
        $user_refund = array();
        foreach ($company_received as $k => $v) {
            $user_refund[$v->user_id] = $v->refund_amount;
        }
        $company_invoice = $this->classtraineemodel->company_invoice($invoice_id);
        $company_invoice->invoice_excess_amt = (empty($company_invoice->invoice_excess_amt)) ? 0 : $company_invoice->invoice_excess_amt;
        $excess_paid = $company_invoice->invoice_excess_amt;
        $excess_refunded = empty($user_refund[0]) ? 0 : $user_refund[0];
        $excess_remain = round($excess_paid, 2) - round($excess_refunded, 2);
        $company_invoice->excess_refunded = round($excess_refunded, 2);
        $company_invoice->excess_remain = round($excess_remain, 2);

        $company_invoice->invoiced_on = ($company_invoice->invoiced_on == NULL || $company_invoice->invoiced_on == '0000-00-00 00:00:00') ? '' : date('d-m-Y', strtotime($company_invoice->invoiced_on));
        $company_invoice->discount_label = rtrim($this->course->get_metadata_on_parameter_id($company_invoice->discount_type), ', ');
        $gst_label = ($company_invoice->total_gst > 0) ? 'GST ON, ' : 'GST OFF ';
        if ($company_invoice->total_gst > 0) {
            $gst_label .= rtrim($this->course->get_metadata_on_parameter_id($company_invoice->gst_rule), ', ');
        }
        $company_invoice->gst_label = $gst_label;
        $company_invoice->trainees = $this->classtraineemodel->get_company_trainees_by_payid1($company_invoice->pymnt_due_id);
        foreach ($company_invoice->trainees as $k => $row) {
            $company_invoice->trainees[$k]->subsidy_recd_date = (($row->subsidy_recd_date == '0000-00-00') || ($row->subsidy_recd_date == null)) ? '' : date('d-m-Y', strtotime($row->subsidy_recd_date));
            $amount_paid = empty($user_paid[$row->user_id]) ? 0 : $user_paid[$row->user_id];
            $amount_refund = empty($user_refund[$row->user_id]) ? 0 : $user_refund[$row->user_id];
            $company_invoice->trainees[$k]->amount_paid = round($amount_paid, 2);
            $company_invoice->trainees[$k]->amount_refund = round($amount_refund, 2);
            $company_invoice->trainees[$k]->amount_remain = round(($amount_paid - $amount_refund), 2);
        }
        $company_invoice->subsidy_type = $this->classtraineemodel->get_subsidy_type($this->tenant_id);

        echo json_encode($company_invoice);
        exit();
    }

    /**
     * get not paid enrol_invoice for company
     */
    public function get_company_notpaid_invoice($is_json = 0) {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $company = $this->input->post('company');
        $result = $this->classtraineemodel->get_company_based_pay_invoice($this->tenant_id, $query_string, $company, 0);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id, //Course: ' . $row->crse_name . ', 
                    'label' => $row->invoice_id . ' (Class: ' . $row->class_name . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
        if ($is_json == 1) {
            return $matches;
        } else {
            echo json_encode($matches);
            exit();
        }
    }

    /**
     * get paid enrol_invoice for company
     */
    public function get_company_all_invoice() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $company = $this->input->post('company');
        $paid = $this->input->post('paid');
        $result = $this->classtraineemodel->get_company_based_pay_invoice($this->tenant_id, $query_string, $company, 0, 1, $paid);

        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id, //Course: ' . $row->crse_name . ', 
                    'label' => $row->invoice_id . ' (Class: ' . $row->class_name . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * get paid enrol_invoice for company
     */
    public function get_company_paid_invoice() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $company = $this->input->post('company');
        $result = $this->classtraineemodel->get_company_based_pay_invoice($this->tenant_id, $query_string, $company, 1);

        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id, //Course: ' . $row->crse_name . ', 
                    'label' => $row->invoice_id . ' (Class: ' . $row->class_name . ')',
                    'value' => $row->invoice_id,
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * created for not paid taxcode
     */
    public function get_notpaid_taxcode() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_notpaid_user($this->tenant_id, $query_string, '');
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . ' (Name: ' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * created to get taxcode
     */
    public function get_taxcode() {
        $matches = array();
        $paid = $this->input->post('paid');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_user($this->tenant_id, $query_string, '', $paid);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . ' (Name: ' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * created for not paid taxcode
     */
    public function get_paid_taxcode() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_paid_user($this->tenant_id, $query_string, '');
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . ' (Name: ' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * created for not paid trainee
     */
    public function get_notpaid_trainee() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_notpaid_user($this->tenant_id, '', $query_string);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . ' (Tax Code: ' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name,
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * created for not paid trainee
     */
    public function get_trainee() {
        $matches = array();
        $paid = $this->input->post('paid');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_user($this->tenant_id, '', $query_string, $paid);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . ' (Tax Code: ' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name,
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * created for paid trainee
     */
    public function get_paid_trainee() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_paid_user($this->tenant_id, '', $query_string);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . ' (Tax Code: ' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name,
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * search trainee update payment
     */
    public function search_trainee_updatepayment() {
        $invoice_id = $this->input->post('invoice_id');
        $taxcode_id = $this->input->post('taxcode_id');
        $trainee_id = $this->input->post('trainee_id');
        $result['data'] = $this->classtraineemodel->search_trainee_updatepayment($invoice_id, $taxcode_id, $trainee_id, $this->tenant_id);
        if (empty($result['data'])) {
            $result['trainer'] = $this->get_trainee_name();
        }
        echo json_encode($result);
        exit();
    }

    /**
     * search trainee update payment
     */
    public function search_trainee_invoice() {
        $invoice_id = $this->input->post('invoice_id');
        $taxcode_id = $this->input->post('taxcode_id');
        $trainee_id = $this->input->post('trainee_id');
        $paid = $this->input->post('paid');
        $result['data'] = $this->classtraineemodel->search_trainee_invoice($invoice_id, $taxcode_id, $trainee_id, $this->tenant_id, $paid);
        if (empty($result['data'])) {
            $result['trainer'] = $this->get_trainee_name();
        } else {
            foreach ($result['data'] as $k => $v) {
                $result['data'][$k]->payment_status = rtrim($this->course->get_metadata_on_parameter_id($v->payment_status), ', ');
            }
        }
        echo json_encode($result);
        exit();
    }

    /**
     * search trainee update payment
     */
    public function search_trainee_refundpayment() {
        $invoice_id = $this->input->post('invoice_id');
        $taxcode_id = $this->input->post('taxcode_id');
        $trainee_id = $this->input->post('trainee_id');
        $result['data'] = $this->classtraineemodel->search_trainee_refundpayment($invoice_id, $taxcode_id, $trainee_id, $this->tenant_id);
        if (empty($result['data'])) {
            $result['trainer'] = $this->get_trainee_name();
        }
        echo json_encode($result);
        exit();
    }

    /**
     * search trainee update payment
     */
    public function get_trainee_name() {
        $invoice_id = $this->input->post('invoice_id');
        $taxcode_id = $this->input->post('taxcode_id');
        $trainee_id = $this->input->post('trainee_id');
        $result = $this->classtraineemodel->get_trainee_name($invoice_id, $taxcode_id, $trainee_id, $this->tenant_id);
        return $result->first . ' ' . $result->last;
    }

    /**
     * get enrol_invoice basically getting invoice for payid
     */
    public function get_enroll_invoice_by_user_class($json_check = 0) {
        $class = $this->input->post('class_id');
        $user = $this->input->post('user_id');
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $this->get_payid_details($payid, $json_check);
    }

    /**
     * get enrol_invoice basically getting invoice for payid
     */
    public function get_enroll_invoice($json_check = 0) {
        $payid = $this->input->post('payid');
        $this->get_payid_details($payid, $json_check);
    }

    /**
     * update invoiced on
     */
    public function update_invoiced_on() {
        $submit = $this->input->post('submit');
        if ($submit == 'company') {
            $invoice_date = $this->input->post('invd_date');
            $invoice_id = $this->input->post('inv_no');
        } else {
            $invoice_date = $this->input->post('iinvd_date');
            $invoice_id = $this->input->post('iinv_no');
        }

        $result = $this->classtraineemodel->update_invoiced_on($invoice_id, $invoice_date);
        if ($result == TRUE) {
            $this->session->set_flashdata("success", "Invoice sent on updated successfully.");
        } else {
            $this->session->set_flashdata("error", "Unable to update Invoice sent on date. Please try again later.");
        }
        redirect('accounting/generate_invoice');
        exit();
    }

    /**
     * get payid details
     */
    public function get_payid_details($payid, $json_check) {
        $tenant_id = $this->tenant_id;
        $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
        //$result = $this->classtraineemodel->get_enroll_invoice($payid);
        $result = $this->classtraineemodel->get_enroll_individual_invoice($payid); //sk1
        //added by pritam to generate previous invoice number
        $result->previous_inv_id = $this->classtraineemodel->get_enroll_prev_indvoice($payid);

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
        //$paid_details = $this->classtraineemodel->get_invoice_paid_details($result->invoice_id);
        $paid_details = $this->classtraineemodel->get_invoice_paid_details_new($result->invoice_id); ///modified by shubhranshu to fix the backdate issue while update payment
        //print_r($paid_details);exit;
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
                $other_mode = ($row->othr_mode_of_payment) ? '+' . $row->othr_mode_of_payment : ''; // added by shubhranshu to display the other mode if exist 0n 19feb
                $paid_arr[] = array(
                    'recd_on' => date('d/m/Y', strtotime($row->recd_on)),
                    'mode' => $mode . $mode_ext . $other_mode,
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
                $result->refund_details[$k]->mode_of_refund = $this->course->get_metadata_on_parameter_id($row->mode_of_refund) . (($row->othr_mode_of_refund) ? ('+' . $row->othr_mode_of_refund) : ''); /// part added by shubhranshu
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
            $sfc_claim_id = $this->classtraineemodel->get_sfc_claim_id($result->class_id, $result->user_id, $payid, $tenant_id); // addded by shubhranshu for sfc claim id
        } else {
            $label = 'inactive';
        }
        $result->sfc_claim_id = $sfc_claim_id; // added by shubhranshu
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
        if ($json_check == 0) {
            echo json_encode($res);
            exit();
        } else {
            return $res;
        }
    }

    /**
     * get payid details
     */

    /**
     * get payid details
     */
    public function get_payid_details_indv($payid, $json_check) {
        $tenant_id = $this->tenant_id;

        $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
        $result = $this->classtraineemodel->get_enroll_invoice($payid);
        //added by pritam to generate previous invoice number
        $result->previous_inv_id = $this->classtraineemodel->get_enroll_prev_indvoice($payid);
        //
        $result->invoiced_on = ($result->invoiced_on == NULL || $result->invoiced_on == '0000-00-00 00:00:00') ? '' : date('d-m-Y', strtotime($result->invoiced_on));

        $result->invoice_id = $result->invoice_id;
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
        //$paid_details = $this->classtraineemodel->get_invoice_paid_details_indv($result->invoice_id);
        $paid_details = $this->classtraineemodel->get_invoice_paid_details_indv_new($result->invoice_id); //// mmodified by shubhranshu to fix the sfc issue while giving backdate

        $paid_arr = array();
        $paid_rcd_till_date = 0;
        //sfc_start
        $result->sfc_claimed = 0;
        foreach ($paid_details as $row) {

            if ($row->mode_of_pymnt == "SFC_SELF") {

                $mode_of_payment = explode('_', $row->mode_of_pymnt);
                $result->mode_of_pymnt = $mode_of_payment[1];
                $result->sfc_of_pymnt = $mode_of_payment[1];
                $result->other_payment = $row->other_payment;
                $sfc_claimed = $row->sfc_claimed;
                $result->othr_mode_of_payment = $row->othr_mode_of_payment;
                $result->other_amount_recd = $row->other_amount_recd;
                $result->recd_on = $row->recd_on;

                $result->sfc_claimed = number_format($sfc_claimed, 2, '.', '');
            }
            if ($row->mode_of_pymnt == "SFC_ATO") {
                $mode_of_payment = explode('_', $row->mode_of_pymnt);
                $result->mode_of_pymnt = $mode_of_payment[1];
                $result->sfc_of_pymnt = $mode_of_payment[1];
                $result->other_payment = $row->other_payment;
                $result->othr_mode_of_payment = $row->othr_mode_of_payment;
                $result->other_amount_recd = $row->other_amount_recd;
                $result->recd_on = $row->recd_on;
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

            $result->recd_on = $row->recd_on;
            $result->mode_of_pymnt = $row->mode_of_pymnt;
            $result->amount_recd = $row->amount_recd;

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
            $sfc_claim_id = $this->classtraineemodel->get_sfc_claim_id($result->class_id, $result->user_id, $payid, $tenant_id); // addded by shubhranshu for sfc claim id
        } else {
            $label = 'inactive';
        }
        $result->sfc_claim_id = $sfc_claim_id; // added by shubhranshu
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
        if ($json_check == 0) {
            echo json_encode($res);
            exit();
        } else {
            return $res;
        }
    }

    /**
     * Export class trainee
     */
    public function export_classtrainee_full() {
        $tenant_id = $this->tenant_id;

        $course = ($this->input->get('course')) ? $this->input->get('course') : '';
        $class = ($this->input->get('class')) ? $this->input->get('class') : '';
        $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
        $search_select = ($this->input->get('search_select')) ? $this->input->get('search_select') : '';
        $taxcode_id = ($this->input->get('taxcode_id')) ? $this->input->get('taxcode_id') : '';
        $trainee_id = ($this->input->get('trainee_id')) ? $this->input->get('trainee_id') : '';
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ce.pymnt_due_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $company_id = $this->input->get('company_id');
        $result = $this->classtraineemodel->list_all_classtrainee_by_tenant_id($tenant_id, '', '1', $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);
        $this->load->helper('export_helper');
        export_classtrainee_full($result, $tenant_id);
    }

    /**
     *  export class trainee
     */
    public function export_classtrainee_page() {
        $tenant_id = $this->tenant_id;
        $course = ($this->input->get('course')) ? $this->input->get('course') : '';
        $class = ($this->input->get('class')) ? $this->input->get('class') : '';
        $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
        $search_select = ($this->input->get('search_select')) ? $this->input->get('search_select') : '';
        $taxcode_id = ($this->input->get('taxcode_id')) ? $this->input->get('taxcode_id') : '';
        $trainee_id = ($this->input->get('trainee_id')) ? $this->input->get('trainee_id') : '';
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ce.pymnt_due_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $company_id = $this->input->get('company_id');
        //added by abdulla
        $enrolment_id = $this->input->get('eid');
        $result = $this->classtraineemodel->list_all_classtrainee_by_tenant_id($tenant_id, '', '1', $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id, $enrolment_id);
        $this->load->helper('export_helper');
        export_classtrainee_page($result, $tenant_id);
    }

    /**
     * function to get payment recd popup details
     */
    public function get_company_invoice_payment_recd() {
        $invoice = $this->input->post('invoice');
        $data = $this->classtraineemodel->company_invoice($invoice);
        $data->invoice_excess_amt = empty($data->invoice_excess_amt) ? 0 : $data->invoice_excess_amt;

        if ($data->company_id[0] == 'T') {
            $tenant_details = fetch_tenant_details($data->company_id);
            $data->company_name = $tenant_details->tenant_name;
        } else {
            $company_details = $this->company->get_company_details($this->tenant_id, $data->company_id);
            $data->company_name = $company_details[0]->company_name;
        }
        $data->discount_label = rtrim($this->course->get_metadata_on_parameter_id($data->discount_type), ', ');
        $gst_label = ($data->total_gst > 0) ? 'GST ON, ' : 'GST OFF ';
        if ($data->total_gst > 0) {
            $gst_label .= rtrim($this->course->get_metadata_on_parameter_id($data->gst_rule), ', ');
        }
        $data->gst_label = $gst_label;
        $data->inv_date = date('d/m/Y', strtotime($data->inv_date));
        $data->amount_refund = round($data->amount_refund, 2);
        $recd = $this->classtraineemodel->get_invoice_paid_details($invoice);
        foreach ($recd as $k => $row) {
            $mode_ext = ($row->mode_of_pymnt == 'CHQ') ? ' Chq#: ' . $row->cheque_number : '';
            $recd[$k]->recd_on = date('d/m/Y', strtotime($row->recd_on));
            $recd[$k]->mode = rtrim($this->course->get_metadata_on_parameter_id($row->mode_of_pymnt), ', ') . $mode_ext;
        }
        $res = array('data' => $data, 'recd' => $recd);
        echo json_encode($res);
        exit();
    }

    /**
     * function to get payment recd popup details
     */
    public function get_company_invoice_payment_refund() {
        $invoice = $this->input->post('invoice');
        $data = $this->classtraineemodel->company_invoice($invoice);
        if ($data->company_id[0] == 'T') {
            $tenant_details = fetch_tenant_details($data->company_id);
            $data->company_name = $tenant_details->tenant_name;
        } else {
            $company_details = $this->company->get_company_details($this->tenant_id, $data->company_id);
            $data->company_name = $company_details[0]->company_name;
        }
        $data->discount_label = rtrim($this->course->get_metadata_on_parameter_id($data->discount_type), ', ');
        $gst_label = ($data->total_gst > 0) ? 'GST ON, ' : 'GST OFF ';
        if ($data->total_gst > 0) {
            $gst_label .= rtrim($this->course->get_metadata_on_parameter_id($data->gst_rule), ', ');
        }
        $data->gst_label = $gst_label;
        $data->inv_date = date('d/m/Y', strtotime($data->inv_date));
        $data->amount_refund = round($data->amount_refund, 2);
        $refund_details = $refund_details = $this->classtraineemodel->get_refund_paid_details($invoice);
        foreach ($refund_details as $k => $row) {
            if ($row->refnd_reason != 'OTHERS') {
                $refund_details[$k]->refnd_reason = $this->course->get_metadata_on_parameter_id($row->refnd_reason);
            } else {
                $refund_details[$k]->refnd_reason = 'Others (' . $row->refnd_reason_ot . ')';
            }

            $refund_details[$k]->refund_on = date('d/m/Y', strtotime($row->refund_on));
            $refund_details[$k]->mode_of_refund = $this->course->get_metadata_on_parameter_id($row->mode_of_refund);
        }
        $res = array('data' => $data, 'refund' => $refund_details);
        echo json_encode($res);
        exit();
    }

    /* export public portal trainee list xls skm start */

    public function export_online_classtrainee_page() {
        $tenant_id = $this->tenant_id;
        $course = ($this->input->get('course')) ? $this->input->get('course') : '';
        $class = ($this->input->get('class')) ? $this->input->get('class') : '';
        $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
        $search_select = ($this->input->get('search_select')) ? $this->input->get('search_select') : '';
        $taxcode_id = ($this->input->get('taxcode_id')) ? $this->input->get('taxcode_id') : '';
        $trainee_id = ($this->input->get('trainee_id')) ? $this->input->get('trainee_id') : '';
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ce.pymnt_due_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $company_id = $this->input->get('company_id');
        $result = $this->classtraineemodel->online_list_classtrainee_by_tenant_id($tenant_id, '', '1', $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);
        $this->load->helper('export_helper');
        export_classtrainee_page($result, $tenant_id);
    }

    /* skm END */

    /* Export trainee list who enrolled from public portal skm start */

    public function export_online_classtrainee_full() {
        $tenant_id = $this->tenant_id;

        $course = ($this->input->get('course')) ? $this->input->get('course') : '';
        $class = ($this->input->get('class')) ? $this->input->get('class') : '';
        $class_status = ($this->input->get('class_status')) ? $this->input->get('class_status') : '';
        $search_select = ($this->input->get('search_select')) ? $this->input->get('search_select') : '';
        $taxcode_id = ($this->input->get('taxcode_id')) ? $this->input->get('taxcode_id') : '';
        $trainee_id = ($this->input->get('trainee_id')) ? $this->input->get('trainee_id') : '';
        $field = ($this->input->get('f')) ? $this->input->get('f') : 'ce.pymnt_due_id';
        $order_by = ($this->input->get('o')) ? $this->input->get('o') : 'DESC';
        $company_id = $this->input->get('company_id');
        $result = $this->classtraineemodel->online_list_classtrainee_by_tenant_id($tenant_id, '', '1', $field, $order_by, $course, $class, $class_status, $search_select, $taxcode_id, $trainee_id, $company_id);

        $this->load->helper('export_helper');
        export_classtrainee_full($result, $tenant_id);
    }

    /* end */

    /**
     * get_count of invoice no subsidy
     */
    public function get_count_company_invoice_no_subsidy() {
        $invoice_id = $this->input->post('invoice');
        $subsidy = $this->input->post('subsidy');
        if (empty($subsidy)) {
            $result = $this->classtraineemodel->get_company_invoice_no_subsidy($invoice_id);
        } else {
            $result = $this->classtraineemodel->get_company_invoice_subsidy($invoice_id);
        }
        echo count($result);
    }

    /**
     * get_count of invoice no subsidy
     */
    public function get_count_invoice() {
        $id = $this->input->post('pay_id');
        $result = $this->classtraineemodel->get_count_invoice($id);

        echo count($result);
    }

    /**
     * get_count of invoice for foreigner
     */
    public function get_count_company_invoice_foreigner() {
        $invoice_id = $this->input->post('invoice');
        $subsidy = $this->input->post('subsidy');

        $result = $this->classtraineemodel->get_company_invoice_foreigner($invoice_id);

        echo count($result);
    }

    /**
     * function export company generate invoice no subsidy
     */
    public function gen_inv_pdf_basedon_subsidy($invoice_id, $is_subsidy) {
        $tenant_id = $this->tenant_id;
        if (empty($invoice_id)) {
            return show_404();
        }
        if (empty($is_subsidy)) {
            $res = $this->classtraineemodel->get_company_invoice_no_subsidy($invoice_id);
            $text = 'b';
        } else {
            $res = $this->classtraineemodel->get_company_invoice_subsidy($invoice_id);
            $text = 'a';
        }

        $result = $res[0];
        //added by pritam to generate previous invoice number
        $result->previous_inv_id = $this->classtraineemodel->get_enroll_prev_invoice($invoice_id);
        //
        $result->invoice_label = $text;
        $result->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_state), ', ');
        $result->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_country), ', ');

        $result->inv_year = date('Y', strtotime($result->inv_date));
        //Commented by Abdulla Nofal
        //$result->inv_date = date('d/m/Y', strtotime($result->inv_date));
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

            $company_person = $this->company->get_company_user($result->tenant_id, $result->company_id); //sk1
            $result->company_person_name = $company_person[0]->first_name; //sk2
        } else {
            $company_details = $this->company->get_company_details($this->tenant_id, $result->company_id);
            $result->company_details = $company_details[0];
            $result->company_details->comp_state = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_state), ', ');
            $result->company_details->comp_cntry = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_cntry), ', ');
            $result->company_name = $company_details[0]->company_name;

            $company_person = $this->company->get_company_user($result->tenant_id, $result->company_id); //sk1
            $result->company_person_name = $company_person[0]->first_name; //sk2
        }
        //$result->discount_rate = round($result->discount_rate, 2);
        $result->gst_rate = round($result->gst_rate, 2);
        $total_unitfees = 0;
        $total_discount = 0;
        $total_gst = 0;
        $total_subsidy = 0;
        $total_inv_amount = 0;
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
        foreach ($res as $k => $row) {
            $received = empty($user_paid[$row->user_id]) ? 0 : $user_paid[$row->user_id];
            $refunded = empty($user_refund[$row->user_id]) ? 0 : $user_refund[$row->user_id];
            $received_amt+=$received;
            $refunded_amt+=$refunded;
            if ((($row->total_amount_due + $refunded) - $received) <= 0) {
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
            $res[$k]->payment_label = $payment_label;
            $total_unitfees +=$row->class_fees;
            $total_discount += ($row->class_fees * ($result->discount_rate / 100));
            $total_gst += $row->gst_amount;
            $total_subsidy += $row->subsidy_amount;
            $total_inv_amount += $row->total_amount_due;
            $result->total_inv_amount1 = $row->total_inv_amount;
        }
        $result->payble_amount = $total_inv_amount + $refunded_amt - $received_amt;
        $result->total_unit_fees = round($total_unitfees, 2);
        $result->total_inv_discnt = round($total_discount, 2);
        $result->total_inv_subsdy = round($total_subsidy, 2);
        $result->total_gst = round($total_gst, 2);
        $result->indi_disc_total = ($result->class_fees * ($result->discount_rate / 100));
        $result->total_inv_amount = round($total_inv_amount, 2);
        $result->payment_due_details = $res;

        $this->load->helper('pdf_reports_helper');
        if ($tenant_id == 'T17') {
            generate_company_pdf_invoice_everest($result);
        } else {
            generate_company_pdf_invoice_all($result);
        }
    }

    /**
     * function export company generate invoice for foreigner
     */
    public function gen_inv_pdf_basedon_forgeigner($invoice_id, $is_subsidy) {
        //$this->output->enable_profiler();
        $tenant_id = $this->tenant_id;

        if (empty($invoice_id)) {
            return show_404();
        }
        if (empty($is_subsidy)) {
            $res = $this->classtraineemodel->get_company_invoice_foreigner($invoice_id);
            $text = 'f';
        }

        $result = $res[0];
        //added by pritam to generate previous invoice number
        $result->previous_inv_id = $this->classtraineemodel->get_enroll_prev_invoice($invoice_id);
        //
        $result->invoice_label = $text;
        $result->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_state), ', ');
        $result->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_country), ', ');

        $result->inv_year = date('Y', strtotime($result->inv_date));
        //Commented by Abdulla Nofal
        //$result->inv_date = date('d/m/Y', strtotime($result->inv_date));
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

            $company_person = $this->company->get_company_user($this->tenant_id, $result->company_id); //sk1
            $result->company_person_name = $company_person[0]->first_name; //sk2
        } else {
            $company_details = $this->company->get_company_details($this->tenant_id, $result->company_id);
            $result->company_details = $company_details[0];
            $result->company_details->comp_state = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_state), ', ');
            $result->company_details->comp_cntry = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_cntry), ', ');
            $result->company_name = $company_details[0]->company_name;

            $company_person = $this->company->get_company_user($this->tenant_id, $result->company_id); //sk1
            $result->company_person_name = $company_person[0]->first_name; //sk2
        }
        //$result->discount_rate = round($result->discount_rate, 2);
        $result->gst_rate = round($result->gst_rate, 2);
        $total_unitfees = 0;
        $total_discount = 0;
        $total_gst = 0;
        $total_subsidy = 0;
        $total_inv_amount = 0;
//        $company_received = $this->classtraineemodel->company_payment_recd($result->invoice_id);
        $company_received = $this->classtraineemodel->company_payment_recd_forgeiner($result->invoice_id);
        $user_paid = array();
        foreach ($company_received as $k => $v) {
            $user_paid[$v->user_id] = round($v->amount_recd, 2);
        }
//        $company_refunded = $this->classtraineemodel->company_payment_refund($result->invoice_id);
        $company_refunded = $this->classtraineemodel->company_payment_refund_foreigner($result->invoice_id);
        $user_refund = array();
        foreach ($company_refunded as $k => $v) {
            $user_refund[$v->user_id] = round($v->refund_amount, 2);
        }
        foreach ($res as $k => $row) {
            $received = empty($user_paid[$row->user_id]) ? 0 : $user_paid[$row->user_id];
            $refunded = empty($user_refund[$row->user_id]) ? 0 : $user_refund[$row->user_id];
            $received_amt+=$received;
            $refunded_amt+=$refunded;
            if ((($row->total_amount_due + $refunded) - $received) <= 0) {
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

            $res[$k]->payment_label = $payment_label;
            $total_unitfees +=$row->class_fees;
            $total_discount += ($row->class_fees * ($result->discount_rate / 100));
            $total_gst += $row->gst_amount;
            $total_subsidy += $row->subsidy_amount;
            $total_inv_amount += $row->total_amount_due;
            $result->total_inv_amount1 = $row->total_inv_amount;
        }
        $result->payble_amount = $total_inv_amount + $refunded_amt - $received_amt;
        $result->total_unit_fees = round($total_unitfees, 2);
        $result->total_inv_discnt = round($total_discount, 2);
        $result->total_inv_subsdy = round($total_subsidy, 2);
        $result->total_gst = round($total_gst, 2);
        $result->indi_disc_total = ($result->class_fees * ($result->discount_rate / 100));
        $result->total_inv_amount = round($total_inv_amount, 2);

        $result->payment_due_details = $res;

        $this->load->helper('pdf_reports_helper');
        if ($tenant_id == 'T17') {
            generate_company_pdf_invoice_everest($result);
        } else {
            generate_company_pdf_invoice_all($result);
        }
    }

    /**
     * function export company generate invoice
     */
    public function export_company_generate_invoice($id) {
        $tenant_id = $this->tenant_id;

        if (empty($id)) {
            return show_404();
        }
//        $result = $this->classtraineemodel->get_enroll_invoice($id);

        $result = $this->classtraineemodel->get_company_enroll_invoice($id);
        //added by pritam to generate previous invoice number
        $result->previous_inv_id = $this->classtraineemodel->get_enroll_prev_invoice($result->invoice_id);

        //sk st
        $discount = $this->classtraineemodel->get_discount($result->pymnt_due_id);
        $result->discount_label = $discount->discount_type;
        $result->discount_rate = $discount->discount_rate;
        //sk ed
        //
        $result->tenant_state = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_state), ', ');
        $result->tenant_country = rtrim($this->course->get_metadata_on_parameter_id($result->tenant_country), ', ');
        $result->inv_year = date('Y', strtotime($result->inv_date));
        //Commented by Abdulla Nofal
        //$result->inv_date = date('d/m/Y', strtotime($result->inv_date));
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

            $company_person = $this->company->get_company_user($result->tenant_id, $result->company_id); //sk1
            $result->company_person_name = $company_person[0]->first_name; //sk2
        } else {
            $company_details = $this->company->get_company_details($this->tenant_id, $result->company_id);
            $result->company_details = $company_details[0];
            $result->company_details->comp_state = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_state), ', ');
            $result->company_details->comp_cntry = rtrim($this->course->get_metadata_on_parameter_id($result->company_details->comp_cntry), ', ');

            $result->company_name = $company_details[0]->company_name;

            $company_person = $this->company->get_company_user($this->tenant_id, $result->company_id); //skm1
            $result->company_person_name = $company_person[0]->first_name; //skm2
        }

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

                if ($received > 0 && $val->payment_status == 'PARTPAID') {
                    $payment_label = 'PART PAID';
                } else if ($refunded > 0) {
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

        $this->load->helper('pdf_reports_helper');
        if ($tenant_id == 'T17') {
            generate_company_pdf_invoice_everest($result);
        } else {
            generate_company_pdf_invoice_all($result);
        }
    }

    ///////below 2 function has been introduce by shubhranshu to fix the report finance regenerated deleted invoice
    /// function to fetch the current invoice details
    public function export_current_invoice_new($id, $inv) {  //echo $id.'--'.$inv;exit;
        $tenant_id = $this->tenant_id;
        if (empty($id)) {
            return show_404();
        }

        $res = $this->classtraineemodel->check_enrol_invoice_compind($id, $inv);
        //print_r($result);exit;

        $this->load->helper('pdf_reports_helper');
        ///for enrol invoice table data current invoice
        //echo "A ".$id.'--'.$inv;print_r($result);exit;exit;

        if ($res->inv_type == "INVINDV") {
            $this->export_generate_invoice($id);
        } else {
            $this->export_company_generate_invoice($id);
        }
    }

    ////added by shubhranshu
    //function to fetch the previous invoice details
    public function export_previous_generate_invoice($id, $inv) {  //echo $id.'--'.$inv;exit;
        $tenant_id = $this->tenant_id;
        if (empty($id)) {
            return show_404();
        }

        $result = $this->classtraineemodel->get_enroll_old_invoice_new($id, $inv);
        //print_r($result);exit;

        $this->load->helper('pdf_reports_helper');
        ////for view table data previous invoice
        //echo "B ".$id.'--'.$inv;print_r($result);exit;
        if (($result->inv_type != "INVINDV") && (!empty($result->company_id))) {
            $data = json_decode($result->invoice_details);
            //generate_company_pdf_invoice_all($data);

            if ($tenant_id == 'T17') {
                generate_company_pdf_invoice_everest($data);
            } else {
                generate_company_pdf_invoice_all($data);
            }
        } else {
            $data = (array) json_decode($result->invoice_details);
            //generate_pdf_invoice($data);
            if ($tenant_id == 'T17') {
                generate_pdf_invoice_everest($data);
            } else {
                generate_pdf_invoice($data);
            }
        }
    }

    public function export_old_generate_invoice($id, $inv) {  //echo $id.'--'.$inv;exit;
        $tenant_id = $this->tenant_id;
        if (empty($id)) {
            return show_404();
        }

        $result = $this->classtraineemodel->get_enroll_old_invoice($id, $inv);
        //print_r($result);exit;


        $this->load->helper('pdf_reports_helper');

        //if($result->company_id!=""){
        if (($result->inv_type != "INVINDV") && (!empty($result->company_id))) {
            $data = json_decode($result->invoice_details);
            generate_company_pdf_invoice_all($data);
        } else {

            $data = (array) json_decode($result->invoice_details);
            generate_pdf_invoice($data);
        }
    }

    /**
     * function export generate invoice
     */
    public function export_generate_invoice($id) {
        if (empty($id)) {
            //return show_404();
            // above is commented & below code is added by shubhranshu for 404 issue while payment not required
            $this->session->set_flashdata('error', 'Oops! Invoice is not available since payment is not required!');
            redirect("accounting/generate_invoice/");
        }
        $result = $this->get_payid_details($id, 1);
        //print_r($result);exit;
        $this->load->helper('pdf_reports_helper');
        $tenant_id = $this->tenant_id;
        if ($tenant_id == 'T17') {
            generate_pdf_invoice_everest($result);
        } else {
            generate_pdf_invoice($result);
        }
    }

    /**
     * function to get payment Receipt PDF
     */
    public function export_payment_receipt($payid) {
        if (empty($payid)) {
            return show_404();
        }
        //$result = $this->get_payid_details($payid, 1);
        $result = $this->get_payid_details_indv($payid, 1);
        $this->load->helper('pdf_reports_helper');
        generate_payment_receipt($result);
    }

    /**
     * function to export payment received
     */
    public function export_payment_received($payid) {
        if (empty($payid)) {
            return show_404();
        }
        $result = $this->get_payid_details($payid, 1);
        if ($result['data']->enrolment_mode == 'COMPSPON') {
            if ($result['data']->company_id[0] == 'T') {
                $tenant_details = fetch_tenant_details($result['data']->company_id);
                $trainee_det = $tenant_details->tenant_name;
            } else {
                $company_details = $this->company->get_company_details($this->tenant_id, $result['data']->company_id);
                $trainee_det = $company_details[0]->company_name;
            }
        } else {
            $trainee_details = $this->classtraineemodel->get_trainee_by_pymnt_due_id($payid);
            $trainee_det = $trainee_details->first_name . ' ' . $trainee_details->last_name;
            $trainee_id = $trainee_details->user_id;
        }
        $paid_details = $this->classtraineemodel->get_invoice_paid_details($result['data']->invoice_id);
        $paid_arr = array();
        $total_paid = 0;
        foreach ($paid_details as $row) {
            $mode_ext = ($row->mode_of_pymnt == 'CHQ') ? ' Chq#: ' . $row->cheque_number : '';
            $other_mode = $row->othr_mode_of_payment ? '+' . $row->othr_mode_of_payment : ''; // added by shubhranshu to display the other mode if exist 0n 19feb
            $mode = rtrim($this->course->get_metadata_on_parameter_id($row->mode_of_pymnt), ', ');
            $gender = ($row->gender == 'MALE') ? 'Mr. ' : 'Ms. ';
            $name = $gender . $row->first_name . ' ' . $row->last_name;
            $paid_arr[] = array(
                'recd_on' => date('d/m/Y', strtotime($row->recd_on)),
                'mode' => $mode . $mode_ext . $other_mode,
                'name' => $name,
                'amount' => '$ ' . number_format($row->amount_recd, 2, '.', '') . ' SGD',
            );
            $total_paid = $total_paid + $row->amount_recd;
        }
        if ($result['data']->enrolment_mode == 'COMPSPON') {
            if (!empty($result['data']->invoice_excess_amt)) {
                $paid_arr[] = array(
                    'recd_on' => '',
                    'mode' => '',
                    'name' => 'Over Payment Recd.',
                    'amount' => '$ ' . number_format($result['data']->invoice_excess_amt, 2, '.', '') . ' SGD',
                );
                $total_paid = $total_paid + $result['data']->invoice_excess_amt;
            }
        }
        $result['data']->total_paid = '$ ' . number_format($total_paid, 2, '.', '') . ' SGD';
        $this->load->helper('pdf_reports_helper');
        //print_r($paid_arr);exit;
        generate_trainee_payment_recieved($result, $paid_arr, $trainee_det);
    }

    /**
     * function to export payment refund
     */
    public function export_payment_refund($payid) {
        if (empty($payid)) {
            return show_404();
        }
        $result = $this->get_payid_details($payid, 1);
        if ($result['data']->enrolment_mode == 'COMPSPON') {
            if ($result['data']->company_id[0] == 'T') {
                $tenant_details = fetch_tenant_details($result['data']->company_id);
                $trainee_det = $tenant_details->tenant_name;
            } else {
                $company_details = $this->company->get_company_details($this->tenant_id, $result['data']->company_id);
                $trainee_det = $company_details[0]->company_name;
            }
        } else {
            $trainee_details = $this->classtraineemodel->get_trainee_by_pymnt_due_id($payid);
            $trainee_det = $trainee_details->first_name . ' ' . $trainee_details->last_name;
            $trainee_id = $trainee_details->user_id;
        }
        $this->load->helper('pdf_reports_helper');
        generate_trainee_payment_refund($result, $refund_arr, $trainee_det);
    }

    /**
     * to get trainee class
     */
    function get_trainee_classes() {
        extract($_POST);
        $result = $this->classtraineemodel->get_trainee_classes($this->tenant_id, $course, $trainee_id, $taxcode_id);
        foreach ($result as $k => $row) {
            if ($row->class_pymnt_enrol == 'PDENROL') {
                $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                if ($totalbooked >= $row->total_seats) {
                    unset($result[$k]);
                }
            }
        }
        echo json_encode($result);
        exit();
    }

    function get_trainee_classes_forcourse($course) {
        $result = $this->classtraineemodel->get_trainee_classes($this->tenant_id, $course, '', '');
        foreach ($result as $k => $row) {
            if ($row->class_pymnt_enrol == 'PDENROL') {
                $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                if ($totalbooked >= $row->total_seats) {
                    unset($result[$k]);
                }
            }
        }
        return $result;
    }

    /** function to get active courses 
     */
    public function get_active_classcourse_list_by_tenant() {
        $result = $this->classtraineemodel->get_trainee_classes($this->tenant_id, $course, $trainee_id, $taxcode_id);
        $course_arr = array();
        foreach ($result as $k => $row) {
            if ($row->class_pymnt_enrol == 'PDENROL') {
                $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                if ($totalbooked >= $row->total_seats) {
                    unset($result[$k]);
                } else {
                    $course_arr[] = $row->course_id;
                }
            } else {
                $course_arr[] = $row->course_id;
            }
        }
        $res = '';
        if (!empty($course_arr)) {
            $res = $this->classtraineemodel->get_course_by_courseid($course_arr);
        }
        $out = array();
        foreach ($res as $item) {
            $out[$item->course_id] = $item->crse_name;
        }
        return $out;
    }

    /**
     * function to get all trainee
     */
    public function get_alltrainee() {
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_alluser($this->tenant_id, $query_string, '');
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . '( Taxcode : ' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name,
                    'nric' => $row->tax_code /* added by shubhranshu on 22/03/2019 for restriction list check */
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * Used by Class Trainee - Search by Trainee Name
     */
    public function get_alltrainee_with_courseclass() {
        $class = $this->input->post('class');
        $course = $this->input->post('course');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_alluser_with_class_course($this->tenant_id, $query_string, '', $class, $course);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->first_name . ' ' . $row->last_name . '( Taxcode : ' . $row->tax_code . ')',
                    'value' => $row->first_name . ' ' . $row->last_name
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * Used by Class Trainee Search By Taxcode
     */
    public function get_alltaxcode_with_courseclass() {
        $class = $this->input->post('class');
        $course = $this->input->post('course');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_alluser_with_class_course($this->tenant_id, '', $query_string, $class, $course);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . '( Name : ' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * to get all taxcode
     */
    public function get_alltaxcode() {

        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_alluser($this->tenant_id, '', $query_string);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->user_id,
                    'label' => $row->tax_code . ' (Name: ' . $row->first_name . ' ' . $row->last_name . ')',
                    'value' => $row->tax_code
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * function check the company pendinh payments
     */
    public function check_company_pending_payment() {
        $company = $this->input->post('company');
        $result = $this->classtraineemodel->check_company_pending_payment($company);
        echo $result;
        exit();
    }

    /**
     * function to get subsidy, tg data
     */
    public function get_subsidy_tg_data() {
        $class = $this->input->post('class');
        $user = $this->input->post('user');
        $payid = $this->classtraineemodel->get_payid_for_class_user($class, $user);
        $get_subsidy_tg_data = $this->classtraineemodel->get_enrol_payment_due($payid, $user);
        $get_subsidy_tg_data->subsidy_recd_date = ($get_subsidy_tg_data->subsidy_recd_date == '0000-00-00' || $get_subsidy_tg_data->subsidy_recd_date == '1970-01-01' || $get_subsidy_tg_data->subsidy_recd_date == NULL) ? '' : date('d-m-Y', strtotime($get_subsidy_tg_data->subsidy_recd_date));
        $get_subsidy_tg_data->subsidy_percentage = (($get_subsidy_tg_data->subsidy_amount * 100) / $get_subsidy_tg_data->class_fees);
        echo json_encode($get_subsidy_tg_data);
        exit();
    }

    /**
     * function to get eid by shubhranshu
     */
    public function get_eid_data() {
        $class = $this->input->post('class');
        $course = $this->input->post('course');
        $user = $this->input->post('user');
        $eid = $this->classtraineemodel->get_eid_for_class_user($class, $user);
        echo json_encode($eid);
        exit();
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

    /*
     * This method is for Changing Individual Enrolment Type
     */

    public function change_individual_enrolment() {
        $tax_code = $this->input->post('taxcode_id');
        $reuslt['data'] = $this->classtraineemodel->get_enrolment_by_tax_code($tax_code);
        $reuslt['user_details'] = $this->classtraineemodel->get_user_company_name($reuslt['data'][0]->user_id);
        echo json_encode($reuslt);
    }

    /*
     * This Method is for getting enroll invoice details used in Changing Individual Enrolment Type
     */

    public function get_enroll_invoice_details() {
        $tenant_id = $this->tenant_id;
        $opt_type = $this->input->post('opt_type');
        $payid = $this->input->post('payid');
        $course_id = $this->input->post('course');
        $class_id = $this->input->post('class');
        $company_id = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');
        $return_data['data'] = "";
        $return_data['trainees'] = "";
        $return_data['error'] = "";
        $return_data['lock'] = "";
        $lock_status = $this->classtraineemodel->get_attendance_lock_status($tenant_id, $course_id, $class_id);
        $lock_status = $lock_status->lock_status;
        if ($opt_type == "remvind") {
            $result = $this->classtraineemodel->get_enroll_invoice_details($course_id, $class_id, $company_id, $tenant_id, $opt_type, $payid, $user_id);
            $error = (array) $result;

            if (empty($error['msg_status'])) {
                $return_data['data1'] = $result;
                $return_data['lock'] = $lock_status;
                echo json_encode($return_data);
            }
        } else if ($opt_type == "change" || $opt_type == "new" || $opt_type == "remove_invoice" || $opt_type == "add_invoice" || $opt_type == "move_invoice") {
            $result = $this->classtraineemodel->get_enroll_invoice_details($course_id, $class_id, $company_id, $tenant_id, $opt_type, $payid, $user_id);
            $error = (array) $result;
            //print_r($result);exit;
            if (empty($error['msg_status'])) {
                $result->discount_label = rtrim($this->course->get_metadata_on_parameter_id($result->discount_type), ', ');
                $gst_label = ($result->gst_on_off == 1) ? 'GST ON, ' : 'GST OFF, ';
                if ($result->gst_on_off == 1) {
                    $gst_label .= rtrim($this->course->get_metadata_on_parameter_id($result->gst_rule), ', ');
                }
                $gst_label = rtrim($gst_label, ', ');
                $result->gst_label = $gst_label;
                $result->inv_date = date('d-m-Y', strtotime($result->inv_date));
                $result->total_inv_amount = number_format($result->total_inv_amount, 2);
                $result->total_unit_fees = number_format($result->total_unit_fees, 2);
                $result->discount_rate = number_format($result->discount_rate, 2);
                $result->total_inv_subsdy = number_format($result->total_inv_subsdy, 2);
                $result->total_gst = number_format($result->total_gst, 2);
                $result->total_inv_discnt = number_format($result->total_inv_discnt, 2);
                $result->gst_rate = number_format($result->gst_rate, 2);
                $result->gst_rate = $result->gst_rate;

                $return_data['data'] = $result;
                $return_data['lock'] = $lock_status;
                $company_id = ($company_id == "" || $company_id == 0) ? $result->company_id : $company_id;
                $return_data['trainees'] = $this->classtraineemodel->get_all_trainees_details($course_id, $class_id, $company_id, $result->pymnt_due_id);
                if ($opt_type == 'add_invoice') {
                    $return_data['not_enrolled_trainees'] = $this->classtraineemodel->trainee_not_enrolled_in_company_invoice($tenant_id, $course_id, $class_id, $company_id);
                }
                echo json_encode($return_data);
            } else {

                $return_data['error'] = $result;
                $return_data['lock'] = $lock_status;
                echo json_encode($return_data);
            }
        } else {
            $return_data['lock'] = $lock_status;
            $return_data['error'] = $result;
            echo json_encode($return_data);
        }
    }

    /*
     * This is  method is used for the enrol page - gets the data and on-change events etc.
     */

    public function enrollment_view_page() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $this->load->model('internal_user_model');
        $tenant_id = $this->tenant_id;
        $loggedin_user_id = $this->user->user_id;
        extract($_POST);
        if ($search_select == 1) {
            $user_id = $taxcode_id;
        } else {
            $user_id = $trainee_id;
        }
        $account_type = $this->input->post('account_type');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('course', 'Course', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data['courses'] = $this->get_active_classcourse_list_by_tenant($tenant_id);
            if ($course) {
                $result = $this->classtraineemodel->get_trainee_classes($this->tenant_id, $course, $trainee_id, $taxcode_id);
                foreach ($result as $k => $row) {
                    if ($row->class_pymnt_enrol == 'PDENROL') {
                        $totalbooked = $this->class->get_class_booked($row->course_id, $row->class_id, $this->tenant_id);
                        if ($totalbooked >= $row->total_seats) {
                            unset($result[$k]);
                        }
                    }
                }
                $data['classes'] = $result;
            }
            $data['companies'] = $this->company->get_activeuser_companies_for_tenant($tenant_id);
            $data['page_title'] = 'Class Trainee';
            $data['main_content'] = 'classtrainee/addnewenroll';
            //$data['sideMenuData'] = $this->sideMenu;
            $this->load->view('layout', $data);
        } else {
            $data['courses'] = $courses = $this->course->get_course_detailse($course);
            $data['course_manager'] = rtrim($this->course->get_managers($courses->crse_manager), ', ');
            $data['courseLevel'] = $this->course->get_metadata_on_parameter_id($courses->certi_level);
            $data['classes'] = $classes = $this->class->get_class_details($tenant_id, $class);



            /* get the sales executive name based on course- prit* 18-07-2016 */
            $course_salesexec = $this->class->get_course_salesexec1($tenant_id, $course);
            $sales = array();
            foreach ($course_salesexec as $value) {
                $sales[] = $value->user_id;
            }
            $sales_executive = implode(',', $sales);
            //$data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $classes->course_id, $classes->sales_executive);
            $data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $classes->course_id, $sales_executive);


            $totalbooked = $this->class->get_class_booked($classes->course_id, $classes->class_id, $tenant_id);
            $data['available'] = $classes->total_seats - $totalbooked;
            $data['ClassPay'] = rtrim($this->course->get_metadata_on_parameter_id($classes->class_pymnt_enrol), ', ');
            $data['ClassLang'] = rtrim($this->course->get_metadata_on_parameter_id($classes->class_language), ', ');
            $data['ClassLoc'] = $this->get_classroom_location($classes->classroom_location, $classes->classroom_venue_oth);
            $data['ClassTrainer'] = $this->class->get_trainer_names($classes->classroom_trainer);
            $data['LabTrainer'] = $this->class->get_trainer_names($classes->lab_trainer);
            $data['Assessor'] = $this->class->get_trainer_names($classes->assessor);
            $data['TrainingAide'] = $this->class->get_course_manager_names($classes->training_aide);
            $data['gstrate'] = $gstrate = $this->classtraineemodel->get_gst_current();
            $data['gstlabel'] = $gst_label = ($courses->gst_on_off == 1) ? 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($courses->subsidy_after_before), ', ') : 'GST OFF';
            $data['subsidy_type'] = $this->classtraineemodel->get_subsidy_type($tenant_id);
            if ($account_type == 'individual') {
                $data['trainee_name'] = $this->classtraineemodel->get_notenrol_trainee_name('', '', $user_id, $tenant_id);
                $data['discount'] = $discount = $this->classtraineemodel->calculate_discount_enroll($user_id, 0, $class, $course, $classes->class_fees);
                $data['feesdue'] = $feesdue = round($classes->class_fees - round((($discount['discount_rate']) / 100 * ($classes->class_fees)), 2), 2);
                $data['gst_total'] = $this->classtraineemodel->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
                $data['netdue'] = $this->classtraineemodel->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
            } elseif ($account_type == 'company') {
                $data['company_details'] = $company_details = $this->company->get_company_details($tenant_id, $company);
                if ($company[0] == "T") {
                    $tenant_details = fetch_tenant_details($tenant_id);
                    $data['company_details'][0]->company_name = $tenant_details->tenant_name;
                }
                $data['discount'] = $discount = $this->classtraineemodel->calculate_discount_enroll(0, $company, $class, $course, $classes->class_fees);
                $discount_label = $discount['discount_label'];
                $discount_rate = round($discount['discount_rate'], 4);
                $discount_amount = round(($classes->class_fees * ($discount_rate / 100)), 2);
                if ($discount_amount > $classes->class_fees) {
                    $discount_rate = 100;
                    $discount_amount = $classes->class_fees;
                }
                $feesdue = round(($classes->class_fees - $discount_amount), 2);
                $company_net_due = 0;
                $company_unit_fees = 0;
                $company_discount_amount = 0;
                $company_gst_total = 0;
                if (!empty($control_6)) {
                    $data['trainees'] = $this->classtraineemodel->get_trainee_details_for_trainee_ids($tenant_id, $control_6);
                }
                foreach ($control_6 as $user_id) {
                    $gst_total = $this->classtraineemodel->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
                    $calculated_net_due = $this->classtraineemodel->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
                    $company_net_due = round(($company_net_due + $calculated_net_due), 2);
                    $company_unit_fees = round(($company_unit_fees + $classes->class_fees), 2);
                    $company_discount_amount = round(($company_discount_amount + $discount_amount), 2);
                    $company_gst_total = round(($company_gst_total + $gst_total), 2);
                }
                $data['company_net_due'] = $company_net_due;
                $data['company_unit_fees'] = $company_unit_fees;
                $data['company_gst_total'] = $company_gst_total;
                $data['company_discount_amount'] = round($discount_amount, 2);
                $data['company_discount_label'] = $discount_label;
                $data['company_discount_rate'] = $discount_rate;
                $data['pending_payments'] = $this->classtraineemodel->check_company_pending_payment($company);
            }
            $role = $this->internal_user_model->check_sales_exec1($loggedin_user_id);
            if ($role->role_id !== "ADMN") {
                if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
                    $data['salesexec_check'] = 1;
                }
            }
            $data['tenant_id'] = $tenant_id;
            $data['page_title'] = 'Class Trainee';
            $data['main_content'] = 'classtrainee/enrollpayment';
            // $data['sideMenuData'] = $this->sideMenu;
            $data['restriction_flag'] = $this->input->post('restriction_flag'); ///added by shubhranshu
            $data['privilage'] = $this->input->post('privilage'); ///added by shubhranshu
            $this->load->view('layout', $data);
        }
    }

    /* This Method is used for the enrol page - gets the data from direct register and enroll page skm start */

    public function individual_enrollment_view_page() {

        $data['sideMenuData'] = fetch_non_main_page_content();
        $this->load->model('internal_user_model');
        $tenant_id = $this->tenant_id;
        $loggedin_user_id = $this->user->user_id;
        extract($_POST);
        if ($search_select == 1) {
            $user_id = $taxcode_id;
        } else {
            $user_id = $trainee_id;
        }

        $account_type = $this->input->post('account_type'); //sk1
        $course = $this->input->post('course_id'); //sk2
        $class = $this->input->post('class_id'); //sk3
        $data['courses'] = $courses = $this->course->get_course_detailse($course);
        $data['course_manager'] = rtrim($this->course->get_managers($courses->crse_manager), ', ');
        $data['courseLevel'] = $this->course->get_metadata_on_parameter_id($courses->certi_level);
        $data['classes'] = $classes = $this->class->get_class_details($tenant_id, $class);

        /* get the sales executive name based on course- prit* 18-07-2016 */
        $course_salesexec = $this->class->get_course_salesexec1($tenant_id, $course);
        $sales = array();
        foreach ($course_salesexec as $value) {
            $sales[] = $value->user_id;
        }
        $sales_executive = implode(',', $sales);
//$data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $classes->course_id, $classes->sales_executive);
        $data['salesexec'] = $this->class->get_class_salesexec($tenant_id, $classes->course_id, $sales_executive);
        $totalbooked = $this->class->get_class_booked($classes->course_id, $classes->class_id, $tenant_id);
        $data['available'] = $classes->total_seats - $totalbooked;
        $data['ClassPay'] = rtrim($this->course->get_metadata_on_parameter_id($classes->class_pymnt_enrol), ', ');
        $data['ClassLang'] = rtrim($this->course->get_metadata_on_parameter_id($classes->class_language), ', ');
        $data['ClassLoc'] = $this->get_classroom_location($classes->classroom_location, $classes->classroom_venue_oth);
        $data['ClassTrainer'] = $this->class->get_trainer_names($classes->classroom_trainer);
        $data['LabTrainer'] = $this->class->get_trainer_names($classes->lab_trainer);
        $data['Assessor'] = $this->class->get_trainer_names($classes->assessor);
        $data['TrainingAide'] = $this->class->get_course_manager_names($classes->training_aide);
        $data['gstrate'] = $gstrate = $this->classtraineemodel->get_gst_current();
        $data['gstlabel'] = $gst_label = ($courses->gst_on_off == 1) ? 'GST ON, ' . rtrim($this->course->get_metadata_on_parameter_id($courses->subsidy_after_before), ', ') : 'GST OFF';
        $data['subsidy_type'] = $this->classtraineemodel->get_subsidy_type($tenant_id);

        if ($account_type == 'individual') {

            $data['direct'] = 1; //Added by abdulla
            $data['course'] = $course; //Added by abdulla
            $data['class'] = $class; //Added by abdulla
            $data['trainee_id'] = $user_id; //Added by abdulla

            $data['trainee_name'] = $this->classtraineemodel->get_notenrol_trainee_name('', '', $user_id, $tenant_id);
            $data['discount'] = $discount = $this->classtraineemodel->calculate_discount_enroll($user_id, 0, $class, $course, $classes->class_fees);
            $data['feesdue'] = $feesdue = round($classes->class_fees - round((($discount['discount_rate']) / 100 * ($classes->class_fees)), 2), 2);
            $data['gst_total'] = $this->classtraineemodel->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
            $data['netdue'] = $this->classtraineemodel->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
        } elseif ($account_type == 'company') {
            $data['company_details'] = $company_details = $this->company->get_company_details($tenant_id, $company);
            if ($company[0] == "T") {
                $tenant_details = fetch_tenant_details($tenant_id);
                $data['company_details'][0]->company_name = $tenant_details->tenant_name;
            }
            $data['discount'] = $discount = $this->classtraineemodel->calculate_discount_enroll(0, $company, $class, $course, $classes->class_fees);
            $discount_label = $discount['discount_label'];
            $discount_rate = round($discount['discount_rate'], 4);
            $discount_amount = round(($classes->class_fees * ($discount_rate / 100)), 2);
            if ($discount_amount > $classes->class_fees) {
                $discount_rate = 100;
                $discount_amount = $classes->class_fees;
            }
            $feesdue = round(($classes->class_fees - $discount_amount), 2);
            $company_net_due = 0;
            $company_unit_fees = 0;
            $company_discount_amount = 0;
            $company_gst_total = 0;
            if (!empty($control_6)) {
                $data['trainees'] = $this->classtraineemodel->get_trainee_details_for_trainee_ids($tenant_id, $control_6);
            }
            foreach ($control_6 as $user_id) {
                $gst_total = $this->classtraineemodel->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
                $calculated_net_due = $this->classtraineemodel->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, 0, $gstrate);
                $company_net_due = round(($company_net_due + $calculated_net_due), 2);
                $company_unit_fees = round(($company_unit_fees + $classes->class_fees), 2);
                $company_discount_amount = round(($company_discount_amount + $discount_amount), 2);
                $company_gst_total = round(($company_gst_total + $gst_total), 2);
            }
            $data['company_net_due'] = $company_net_due;
            $data['company_unit_fees'] = $company_unit_fees;
            $data['company_gst_total'] = $company_gst_total;
            $data['company_discount_amount'] = round($discount_amount, 2);
            $data['company_discount_label'] = $discount_label;
            $data['company_discount_rate'] = $discount_rate;
            $data['pending_payments'] = $this->classtraineemodel->check_company_pending_payment($company);
        }

        $role = $this->internal_user_model->check_sales_exec1($loggedin_user_id);

        if ($role->role_id !== "ADMN") {
            if ($this->user->role_id == 'SLEXEC' || $this->user->role_id == 'CRSEMGR' || $this->user->role_id == 'TRAINER') {
                $data['salesexec_check'] = 1;
            }
        }

        $data['tenant_id'] = $tenant_id;
        $data['page_title'] = 'Class Trainee';
        $data['main_content'] = 'classtrainee/enrollpayment';
        // $data['sideMenuData'] = $this->sideMenu;
        $data['restriction_flag'] = $this->input->post('restriction_flag'); ///added by shubhranshu
        $data['privilage'] = $this->input->post('privilage'); ///added by shubhranshu
        $this->load->view('layout', $data);
    }

    /**
     * This method generates the receipt and gets the individual enrollment data and updates the DB
     */
    public function individual_enrollment() {
        // $this->output->enable_profiler(TRUE);
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $loggedin_user_id = $this->session->userdata('userDetails')->user_id;
        extract($_POST);
        $classes = $this->class->get_class_details($tenant_id, $class);
        $this->load->model('common_model', 'commonmodel');
        $enrollmentStatus = $this->commonmodel->is_user_enrolled($user_id, $course, $class, $tenant_id);
        if ($enrollmentStatus) {
            $res['result'] = FALSE;
            $res['error_status'] = "duplicate";
            echo json_encode($res);
            exit();
        }
        $result = $this->classtraineemodel->individual_enrollment_db_update($tenant_id, $loggedin_user_id, $classes->class_fees);
        $res['result'] = $result;
        if ($result == TRUE) {
            $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $user_id, $tenant_id);
            $name = $trainee_name->first . ' ' . $trainee_name->last;
            $trainee = ($trainee_name->gender == 'MALE') ? 'Mr. ' . $name : 'Ms. ' . $name;
            $ClassLoc = $this->get_classroom_location($classes->classroom_location, $classes->classroom_venue_oth);
            $courses = $this->course->get_course_detailse($course);
            $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
            $courseLevel = rtrim($this->course->get_metadata_on_parameter_id($courses->certi_level), ', ');
            if ($mode_of_payment == '') {
                $contact_details = '';
                if ($tenant_details->tenant_id == 'T12') {
                    if (!empty($tenant_details->contact_name)) {
                        $contact_details .=$tenant_details->contact_name . ' ';
                    }
                }
                if (!empty($tenant_details->tenant_contact_num)) {
                    $contact_details .='<br/>(Phone: ' . $tenant_details->tenant_contact_num . ', ';
                }
                if (!empty($tenant_details->tenant_email_id)) {
                    $contact_details .='Email Id: ' . $tenant_details->tenant_email_id . ')';
                }
                $contact_details = rtrim($contact_details, ', ');

                /* reporting time skm start bcoz of sam request for AOP(67) on 18-may-17 */
                $time = strtotime($classes->class_start_datetime);
                $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
                if ($course == 67 || $course == 121) {
                    $li = "<li>Report at center at $reporting_time to register for class</li>";
                } else {
                    $li = "<li>Report at center at 8:30 AM to register for class</li>";
                }

                ///////// below code added by shubhranshu for elearning class only for xp
                if ($tenant_details->tenant_id == 'T02') {
                    if ($course == 166 || $course == 167) {
                        $li = '';
                    }
                }
                /* end */
                if ($tenant_details->tenant_id == 'T20' || $tenant_details->tenant_id == 'T17') {
                    $data = 'Your seat has been booked. Please pay the class fees on or before the class start date.
                     for <strong>' . $trainee . '</strong> for \'Course: ' . $courses->crse_name . ', Class: ' . $classes->class_name . ', Certificate Code: ' . $courseLevel . '\'<br><br>
                    <strong>Class start date:</strong>
                    ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                    <br><br>
                     <strong>Class end date:</strong>
                    ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '
                   
                <br>
                <br>
                    <strong>Location:</strong>
                    ' . $ClassLoc . '<br><br>
                    <strong>Contact Details: </strong>
                    ' . $contact_details . ' <br>
                <br>
                    <strong>Remark *: </strong>
                        <ol>
                           
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            ' . $li . '
                        </ol>';
                } elseif ($tenant_details->tenant_id == 'T02') {
                    $data = 'Your seat has been booked. Please pay the class fees on or before the class start date.
                         for <strong>' . $trainee . '</strong> for \'Course: ' . $courses->crse_name . ', Class: ' . $classes->class_name . ', Certificate Code: ' . $courseLevel . '\'<br><br>
                        <strong>Class start date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                        <br><br>
                         <strong>Class end date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '

                    <br>
                    <br>
                        <strong>Location:</strong>
                        ' . $ClassLoc . '<br><br>
                        <strong>Contact Details: </strong>
                        ' . $contact_details . ' <br>
                    <br>
                        <strong>Remark *: </strong>
                            <ol>
                                <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                                <li>Trim finger nails and remove nail polish.</li>
                                ' . $li . '
                                <li>Payment via Paynow/ Paylah, GIRO or SkillsFuture Credit.</li>
                                 <li>Dress code : Covered shoes.</li>
                            </ol>';
                } else {
                    $data = 'Your seat has been booked. Please pay the class fees on or before the class start date.
                         for <strong>' . $trainee . '</strong> for \'Course: ' . $courses->crse_name . ', Class: ' . $classes->class_name . ', Certificate Code: ' . $courseLevel . '\'<br><br>
                        <strong>Class start date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                        <br><br>
                         <strong>Class end date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '

                    <br>
                    <br>
                        <strong>Location:</strong>
                        ' . $ClassLoc . '<br><br>
                        <strong>Contact Details: </strong>
                        ' . $contact_details . ' <br>
                    <br>
                        <strong>Remark *: </strong>
                            <ol>
                                <li> All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                                <li>Your NRIC, work permit or will be photocopied on the class date</li>
                                <li>Trim finger nails and remove nail polish</li>
                               ' . $li . '
                            </ol>';
                }

                $res['data'] = $data;
            } else {
                $invoice = $this->classtraineemodel->get_invoice_for_class_trainee($class, $user_id);
                $invoice->recd_on_year = date('Y', strtotime($invoice->recd_on));
                $invoice->recd_on = date('d/m/Y', strtotime($invoice->recd_on));
                $invoice->mode_of_pymnt = rtrim($this->course->get_metadata_on_parameter_id($invoice->mode_of_pymnt), ', ');
                $class_start = date('M d, Y h:i A', strtotime($classes->class_start_datetime));

                $att_details = $this->classtraineemodel->get_enroll_invoice($invoice->pymnt_due_id);

                $res['data'] = array('class' => $classes, 'trainee' => $trainee, 'classloc' => $ClassLoc,
                    'courses' => $courses, 'class_start' => $class_start, 'invoice' => $invoice,
                    'coursemanager' => $tenant_details->contact_name, 'courselevel' => $courseLevel, 'tenant' => $tenant_details, 'att_status' => $att_details->att_status);
            }
        }
        echo json_encode($res);
        exit();
    }

    /**
     * This method generates the receipt and gets the Company enrollment data 
     */
    public function company_enrollment() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $tenant_id = $this->tenant_id;
        $curuser_id = $this->session->userdata('userDetails')->user_id;
        extract($_POST);
        $company_details = $this->company->get_company_details($tenant_id, $company);
        $classes = $this->class->get_class_details($tenant_id, $class);
        $totalbooked = $this->class->get_class_booked($course, $class, $tenant_id);
        $available = ($classes->total_seats - $totalbooked);
        $this->load->model('common_model', 'commonmodel');
        $enrollmentStatus = $this->commonmodel->is_user_enrolled($data[0]["user_id"], $course, $class, $tenant_id);
        if ($enrollmentStatus) {
            $res['status'] = FALSE;
            $res['error_status'] = "duplicate";
            echo json_encode($res);
            exit();
        }
        $result = $this->classtraineemodel->company_enrollment_db_update($tenant_id, $curuser_id, $company_details, $discount_changed);
        //echo print_r($result, true);  exit;
        $res['status'] = $result['status'];
        if ($result['status'] == TRUE) {
            $result['err'] = array();
            $user_arr = array();
            foreach ($data as $row) {
                $user_arr[] = $row['user_id'];
            }
            $aded_user_arr = array_diff($user_arr, $result['err']);
            $trainee = '';
            $trainee_id = '';
            $tr_count = 0;
            foreach ($aded_user_arr as $row) {
                $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $row, $tenant_id);
                $name = $trainee_name->first . ' ' . $trainee_name->last;
                $trainee_names = ($trainee_name->gender == 'MALE') ? 'Mr.' . $name : 'Ms.' . $name;
                $trainee .=$trainee_names . ', ';
                $trainee_id .=$row . '-';
                $tr_count++;
            }
            $trainee = rtrim($trainee, ', ');
            $trainee_id = rtrim($trainee_id, '-');

            $err = '';
            $err_trainee = '';
            foreach ($result['err'] as $row) {
                $trainee_name = $this->classtraineemodel->get_trainee_name('', '', $row, $tenant_id);
                $name = $trainee_name->first . ' ' . $trainee_name->last;
                $trainee_names = ($trainee_name->gender == 'MALE') ? 'Mr.' . $name : 'Ms.' . $name;
                $err_trainee .=$trainee_names . ', ';
            }
            $err = rtrim($err_trainee, ', ');
            $err = (!empty($err)) ? $err . ' already exists' : '';
            $res['result'] = $err;

            $ClassLoc = $this->get_classroom_location($classes->classroom_location, $classes->classroom_venue_oth);
            $courses = $this->course->get_course_detailse($course);
            $tenant_details = $this->classtraineemodel->get_tenant_masters($tenant_id);
            $courseLevel = rtrim($this->course->get_metadata_on_parameter_id($courses->certi_level), ', ');
            $data = '';
            if ($tr_count > 0) {

                /* reporting time skm start bcoz of sam request for AOP(67) on 18-may-17 */
                $time = strtotime($classes->class_start_datetime);
                $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
                if ($course == 67 || $course == 121) {
                    $li = "Report at center at $reporting_time to register for class";
                } else {
                    $li = "Report at center at 8:30 AM to register for class";
                }
                /* end */

                $contact_details = '';
                if ($tenant_details->tenant_id == 'T12') {
                    if (!empty($tenant_details->contact_name)) {
                        $contact_details .=$tenant_details->contact_name . ' ';
                    }
                }
                if (!empty($tenant_details->tenant_contact_num)) {
                    $contact_details .='<br/>(Phone: ' . $tenant_details->tenant_contact_num . ', ';
                }
                if (!empty($tenant_details->tenant_email_id)) {
                    $contact_details .='Email Id: ' . $tenant_details->tenant_email_id . ')';
                }
                $contact_details = rtrim($contact_details, ', ');
                if ($company[0] == 'T') {
                    $company_details->company_name = $tenant_details->tenant_name;
                }
                if (!empty($company_details[0]->company_name)) {
                    $company_val = '(Company Name:' . $company_details[0]->company_name . ')';
                }
                ///// added by shubhranshu for wablab points
                if ($tenant_details->tenant_id == 'T20' || $tenant_details->tenant_id == 'T17') {
                    $data .='<div class="table-responsive payment_scroll" style="height: 50px;min-height:50px;">' . $tr_count . ' Seats for your company ' . $company_details[0]->company_name . ' has been booked. Booking details for your employees: ';
                    $data .= '<b>' . $trainee . '</b> for \'Course: ' . $courses->crse_name . ', Class: ' . $classes->class_name . ', Certificate Code: ' . $courseLevel . '\'</div><br><br>
                        <strong>Class start date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                        <br><br>
                         <strong>Class end date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '
                        <br><br>

                        <strong>Location: </strong>
                        ' . $ClassLoc . '<br><br>
                        <strong>Contact Details: </strong>
                        ' . $contact_details . ' <br>
                    <br>
                        <strong>Remark *: </strong>
                            <ol>
                              
                                <li>Your NRIC, work permit or will be photocopied on the class date</li>
                                <li>Trim finger nails and remove nail polish</li>
                                <li>' . $li . '</li>
                            </ol>';
                } elseif ($tenant_details->tenant_id == 'T02') {
                    $data = 'Your seat has been booked. Please pay the class fees on or before the class start date.
                         for <strong>' . $trainee . $company_val . '</strong> for \'Course: ' . $courses->crse_name . ', Class: ' . $classes->class_name . ', Certificate Code: ' . $courseLevel . '\'<br><br>
                        <strong>Class start date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                        <br><br>
                         <strong>Class end date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '

                    <br>
                    <br>
                        <strong>Location:</strong>
                        ' . $ClassLoc . '<br><br>
                        <strong>Contact Details: </strong>
                        ' . $contact_details . ' <br>
                    <br>
                        <strong>Remark *: </strong>
                            <ol>
                                <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                                <li>Trim finger nails and remove nail polish.</li>
                                 ' . $li . '
                                <li>Payment via Paynow/ Paylah, GIRO or SkillsFuture Credit.</li>
                                 <li>Dress code : Covered shoes.</li>
                            </ol>';
                } else {
                    $data .='<div class="table-responsive payment_scroll" style="height: 50px;min-height:50px;">' . $tr_count . ' Seats for your company ' . $company_details->company_name . ' has been booked. Booking details for your employees: ';
                    $data .= '<b>' . $trainee . '</b> for \'Course: ' . $courses->crse_name . ', Class: ' . $classes->class_name . ', Certificate Code: ' . $courseLevel . '\'</div><br><br>
                        <strong>Class start date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_start_datetime)) . '
                        <br><br>
                         <strong>Class end date:</strong>
                        ' . date('M d, Y h:i A', strtotime($classes->class_end_datetime)) . '
                        <br><br>

                        <strong>Location: </strong>
                        ' . $ClassLoc . '<br><br>
                        <strong>Contact Details: </strong>
                        ' . $contact_details . ' <br>
                    <br>
                        <strong>Remark *: </strong>
                            <ol>
                                <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                                <li>Your NRIC, work permit or will be photocopied on the class date</li>
                                <li>Trim finger nails and remove nail polish</li>
                                <li>' . $li . '</li>
                            </ol>';
                }
            }
            $res['data'] = $data;
            $res['trainee_id'] = $trainee_id;
            echo json_encode($res);
        } else {
            echo json_encode($res);
        }
        exit();
    }

    /**
     * This method for transaction_fail in enrollment.
     */
    public function transaction_fail() {
        $error = $this->input->get("err");
        if ($error == "duplicate") {
            $this->session->set_flashdata("error", "This trainee is already enrolled in the class.");
        } else {
            $this->session->set_flashdata("error", "We have not been able to enrol the trainee(s). Please try again later.");
        }
        redirect("class_trainee");
    }

    /**
     *  This method gets the invoice drop down values based on the enrollment type selected.
     */
    public function get_select_box() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        ini_set('memory_limit', '256M'); // added by shubhranshu since 500 error due to huge data
        $type = $this->input->post('type');
        $tenant_id = $this->tenant_id;
        $data = array();
        $role_array = array("COMPACT");
        if ($type == "change") {

            $query = $this->input->post('q');
            $change_individual = $this->classtraineemodel->get_individual_enrol_trainees($tenant_id, $query);


            $data['change_individual'] = $this->formate_change_individual($change_individual);
        } else if ($type == "remvind") {

            $query = $this->input->post('q');
            $change_individual = $this->classtraineemodel->get_remv_individual_enrol_trainees($tenant_id, $query);
            $data['change_individual'] = $this->formate_change_individual($change_individual);
        } else if ($type == "remove_invoice" || $type == "add_invoice") {
            $query = $this->input->post('q');
            $company_not_paid_invoice = $this->classtraineemodel->get_company_not_paid_invoice($tenant_id, $query);
            $data['company_not_paid_invoice'] = $this->formate_company_not_paid($company_not_paid_invoice);
        } else if ($type == "move_invoice") {
            $query = $this->input->post('q');
            $company_not_paid_invoice = $this->classtraineemodel->get_company_not_paid_invoice($tenant_id, $query);
            $data['company_not_paid_invoice'] = $this->formate_company_not_paid($company_not_paid_invoice);
        } else if ($type == "to_move_invoice") {
            $query = $this->input->post('q');
            $company_id = $this->input->post('company_id');
            $course_id = $this->input->post('course_id');
            $class_id = $this->input->post('class_id');
            $company_not_paid_invoice = $this->classtraineemodel->get_company_not_paid_invoice1($tenant_id, $query, $company_id, $course_id, $class_id);
            $data['company_not_paid_invoice'] = $this->formate_company_not_paid($company_not_paid_invoice);
        } else if ($type == "new") {
            $data['courses'] = $this->get_active_classcourse_list_by_tenant($tenant_id);
            $data['companies'] = $this->company->get_activeuser_companies_for_tenant($tenant_id);
            $tenant_company = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
            $tenant_obj = new stdClass;
            if (!in_array($this->session->userdata('userDetails')->role_id, $role_array)) {
                $tenant_obj->company_id = $tenant_company->tenant_id;
                $tenant_obj->company_name = $tenant_company->tenant_name;
                $data['companies'][] = $tenant_obj;
            }
        }
        echo json_encode($data);
        exit();
    }

    /**
     * This Method Merges an existing individual invoice with Company Invoice
     * @param type $opType
     */
    public function enrolment_type_change($opType) {
        $args = array();
        $args['tenant_id'] = $this->tenant_id;
        $args['logged_in_user_id'] = $this->user->user_id;
        $args['individual_user_id'] = $this->input->post('individual_user_id');
        $args['individual_payment_due_id'] = $this->input->post('pymnt_due_id');
        $args['subsidy_amount'] = $this->input->post('subsidy_amount');
        $args['unit_fees'] = $this->input->post('unit_fees');
        $args['comp_invoice_id'] = $this->input->post('comp_invoice_id');
        $args['comp_payment_due_id'] = $this->input->post('comp_pymnt_due_id');
        $args['company_id'] = $this->input->post('company_id');
        $args['comp_gst_rate'] = $this->input->post('comp_gst_rate');
        $args['comp_gst_rule'] = $this->input->post('comp_gst_rule');
        $args['course_id'] = $this->input->post('course_id');
        $args['class_id'] = $this->input->post('class_id');
        ///addded by shubhranshu since the subsidy is not coming during form post to apply the subsidy during invoice cretion
        $subsidy = $this->classtraineemodel->get_individual_enrol_trainees_subsidy($args['tenant_id'], $args['individual_payment_due_id'], $args['individual_user_id']);
        $args['subsidy_amount'] = $subsidy[0]->subsidy_amount;
        ////end --------
        if ($opType == 'mergeInvoice') {
            $status = $this->classtraineemodel->merge_invoice($args);
            if ($status == FALSE) {
                $this->session->set_flashdata("error", "Unable to move Individual Enrollment to the selected Company Invoice. Please try again later.");
            } else {
                $this->session->set_flashdata("success", "Successfully merged Individual Enrollment to the selected Company Invoice.");
            }
        }
        if ($opType == 'newInvoice') {
            $status = $this->classtraineemodel->new_invoice($args);
            if ($status == FALSE) {
                $this->session->set_flashdata("error", "Unable to create a new Invoice for the company. Please try again later.");
            } else {
                $this->session->set_flashdata("success", "Successfully created a new invoice for the Company.");
            }
        }
        redirect("class_trainee");
    }

    /**
     * This methos used for confirme the enrollment in trainee registration.
     */
    public function enroll_pay_now() {
        $data['sideMenuData'] = fetch_non_main_page_content(); // added by shubhranshu
        $trainee_id = $this->session->userdata('new_trainee_user_id');
        $trainee_name = $this->input->post('trainee_name');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $enrolment_mode = ($this->input->post('enrolment_mode')) ? $this->input->post('enrolment_mode') : 'SELF';
        if ($enrolment_mode == 'SELF') {
            $company_id = 0;
        } else
            $company_id = $this->input->post('company_id');
        $opType = $this->input->post('optype');
        $parm_array = array();
        if ($opType == 'PAYNOW') {
            $pay_status = 'PAID';
            $parm_array['payment_type'] = $payment_type = $this->input->post('payment_type');
            if ($payment_type == "GIRO") {
                $parm_array['paid_on'] = $this->input->post('transc_on');
                $parm_array['cheque_date'] = $this->input->post('transc_on');
                $parm_array['bank_name'] = strtoupper($this->input->post('gbank_name'));
                $parm_array['amount_recd'] = $this->input->post('giro_amount');
            } else if ($payment_type == "CASH" || $payment_type == "NETS") {
                $parm_array['paid_on'] = $this->input->post('cashpaid_on');
                $parm_array['cheque_date'] = '';
                $parm_array['amount_recd'] = $this->input->post('cash_amount');
            } else if ($payment_type == "CHQ") {
                $parm_array['paid_on'] = $this->input->post('paid_on');
                $parm_array['cheque_number'] = strtoupper($this->input->post('cheque_number'));
                $parm_array['amount_recd'] = $this->input->post('cheque_amount');
                $parm_array['cheque_date'] = $this->input->post('cheque_date');
                $parm_array['bank_name'] = strtoupper($this->input->post('bank_name'));
            }
        }
        if ($opType == 'PAYLATER') {
            $pay_status = 'NOTPAID';
        }

        // commented below bcoz some change sk 
//      $status = $this->classtraineemodel->enroll_db_update($trainee_id, $course_id, $class_id, $enrolment_mode, $parm_array, $company_id, $pay_status);
        $status = $this->classtraineemodel->regisetr_enroll_db_update($trainee_id, $course_id, $class_id, $enrolment_mode, $parm_array, $company_id, $pay_status);


        if ($status) {
            $this->session->set_flashdata("success_message", "Successfully enrolled '" . $trainee_name . "'");
        } else {
            $this->session->set_flashdata("error_message", "We were not able to enrol '" . $trainee_name . "'");
        }
        redirect("trainee");
    }

    /**
     * This method removes the selected trainees from an invoice.
     */
    public function remove_company_enrollment() {
        $tenant_id = $this->tenant_id;
        $logged_in_user_id = $this->user->user_id;
        $invoice_id = $this->input->post('comp_invoice_id');
        $payment_due_id = $this->input->post('comp_pymnt_due_id');
        $company_id = $this->input->post('company_id');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $seleced_trainee_list = $this->input->post('checked_trainees');
        $status = $this->classtraineemodel->remove_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
                , $invoice_id, $payment_due_id, $seleced_trainee_list);
        if ($status) {
            $trainee_name_list = $this->get_trainee_names($seleced_trainee_list, $tenant_id);
            $this->session->set_flashdata("success", "Successfully removed trainee(s) '" . $trainee_name_list . "' from the Invoice #: '" . $invoice_id . "'");
        } else {
            $this->session->set_flashdata("error", "We were not able to remove the selected trainee(s). Please try again later.");
        }
        redirect("class_trainee");
    }

    /**
     * This method removes the selected trainees from an invoice.
     */
    public function move_company_enrollment() {
        //   $this->output->enable_profiler(TRUE);
        $tenant_id = $this->tenant_id;
        $logged_in_user_id = $this->user->user_id;

        $invoice_id = $this->input->post('comp_invoice_id');
        $payment_due_id = $this->input->post('comp_pymnt_due_id');
        $company_id = $this->input->post('company_id');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $seleced_trainee_list = $this->input->post('checked_trainees');
        $status = $this->classtraineemodel->move_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
                , $invoice_id, $payment_due_id, $seleced_trainee_list);
        if ($status) {

            $trainee_name_list = $this->get_trainee_names($seleced_trainee_list, $tenant_id);
            $this->session->set_flashdata("success", "Successfully moved trainee(s) '" . $trainee_name_list . "' from the Invoice #: '" . $invoice_id . "'");
        } else {

            $this->session->set_flashdata("error", "We were not able to move the selected trainee(s). Please try again later.");
        }
        redirect("class_trainee");
    }

    /**
     * This method removes individual trainee from enrolment 
     * added by pritam.
     */

    /**
     * This method removes individual trainee from enrolment 
     * added by pritam.
     */
    public function remove_ind_enrll() {
        $this->output->enable_profiler(TRUE);
        $tenant_id = $this->tenant_id;

        $logged_in_user_id = $this->user->user_id;
        $invoice_id = $this->input->post('invoice_no1');
        $user_id = $this->input->post('user_id1');
        $user = $this->input->post('user1');
        $pymnt_due_id = $this->input->post('pymnt_due_id1');
        $course_id = $this->input->post('crouse_id1');
        $class_id = $this->input->post('class_id1');

        $status = $this->classtraineemodel->remove_individual_enrollment($tenant_id, $logged_in_user_id, $user_id, $course_id, $class_id, $invoice_id, $pymnt_due_id);
        if ($status) {

            $this->session->set_flashdata("success", "Successfully removed trainee '" . $user . "'");
        } else {
            $this->session->set_flashdata("error", "We were not able to remove the selected trainee(s). Please try again later.");
        }

        redirect("class_trainee");
    }

    /**
     * This method removes individual trainee from enrolment 
     * added by pritam.
     */
    public function void_invoice() {

        $tenant_id = $this->tenant_id;

        $logged_in_user_id = $this->user->user_id;
        $invoice_id = $this->input->post('invoice_id');
        $pymnt_due_id = $this->input->post('pymnt_due_id');

        $status = $this->classtraineemodel->void_invoice($tenant_id, $logged_in_user_id, $invoice_id, $pymnt_due_id);
        if ($status) {

            $this->session->set_flashdata("success", "Successfully invoice is voided '" . $invoice_id . "'");
        } else {
            $this->session->set_flashdata("error", "We were not able to voide invoice. Please try again later.");
        }

        redirect("accounting/generate_invoice");
    }

    /**
     * This method for displaying trainee names in remove enrollment.
     * @param type $seleced_trainee_list
     * @return type
     */
    private function get_trainee_names($seleced_trainee_list, $tenant_id) {
        if (empty($seleced_trainee_list)) {
            return FALSE;
        }
        $trainee_name_list = "";
        $removed_trainees = $this->classtraineemodel->user_details($seleced_trainee_list, $tenant_id);
        foreach ($removed_trainees as $trainee) {
            $trainee_name_list .= $trainee->first_name . " " . $trainee->last_name . ", ";
        }
        return rtrim($trainee_name_list, ", ");
    }

    /**
     * This method adds the selected trainees from an invoice.
     */
    public function add_to_company_enrollment() {
        $tenant_id = $this->tenant_id;
        $logged_in_user_id = $this->user->user_id;
        $invoice_id = $this->input->post('comp_invoice_id');
        $payment_due_id = $this->input->post('comp_pymnt_due_id');
        $company_id = $this->input->post('company_id');
        $course_id = $this->input->post('course_id');
        $class_id = $this->input->post('class_id');
        $seleced_trainee_list = $this->input->post('checked_trainees');
        $status = $this->classtraineemodel->add_to_company_enrollment($tenant_id, $logged_in_user_id, $course_id, $class_id, $company_id
                , $invoice_id, $payment_due_id, $seleced_trainee_list);
        if ($status == TRUE) {
            $trainee_name_list = $this->get_trainee_names($seleced_trainee_list, $tenant_id);
            $this->session->set_flashdata("success", "Successfully added trainee(s) '" . $trainee_name_list . "' to the Invoice #: '" . $invoice_id . "'");
        } else {
            $this->session->set_flashdata("error", "We were not able to add the selected trainee(s). Please try again later.");
        }
        redirect("class_trainee");
    }

    /**
     * Re-calculate - the net due
     */
    public function get_company_net_calculation() {
        $amount = '';
        $percentage = '';
        $tenant_id = $this->tenant_id;
        $discount = $this->input->post('discount');
        $class = $this->input->post('class');
        $company = $this->input->post('company');
        $amt = $this->input->post('amt');
        $per = $this->input->post('per');
        $classes = $this->class->get_class_details($tenant_id, $class);
        $courses = $this->course->get_course_detailse($classes->course_id);
        $gstrate = $this->classtraineemodel->get_gst_current();
        $data = $this->input->post('data');
        $discount_changed = $this->input->post('discount_changed');
        if ($discount_changed == 'Y') {
            $temp_ind_discnt_amt = $discount;
            $discount_rate = round((($temp_ind_discnt_amt / $classes->class_fees) * 100), 4);
            $discount_total = round(($classes->class_fees * ($discount_rate / 100)), 4);
        } else {
            $discount = $this->classtraineemodel->calculate_discount_enroll(0, $company, $classes->class_id, $classes->course_id, $classes->class_fees);
            $discount_rate = $discount['discount_rate'];
            $discount_total = round(($classes->class_fees * ($discount_rate / 100)), 4);
            if ($discount_total > $classes->class_fees) {
                $discount_rate = 100;
                $discount_total = $classes->class_fees;
            }
        }
        $feesdue = round(($classes->class_fees - $discount_total), 4);
        $company_net_due = 0;
        $company_subsidy = 0;
        $company_gst = 0;
        foreach ($data as $row) {
            if (!empty($row['subsidy_amount']) && !empty($row['subsidy_pers'])) {
                $subsidy = $row['subsidy_amount'];
            } else if (!empty($row['subsidy_amount'])) {
                $subsidy = $row['subsidy_amount'];
            } else if (!empty($row['subsidy_pers'])) {
                $subsidy = ($row['subsidy_pers'] * $feesdue) / 100;
            } else {
                $subsidy = 0;
            }
            if ($per == $row['user_id']) {
                $amount = $subsidy;
            }
            if ($amt == $row['user_id']) {
                $percentage = ($subsidy * 100) / $feesdue;
            }
            $gst_total = $this->classtraineemodel->calculate_gst($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, $subsidy, $gstrate);
            $calculated_net_due = $this->classtraineemodel->calculate_net_due($courses->gst_on_off, $courses->subsidy_after_before, $feesdue, $subsidy, $gstrate);
            if ($calculated_net_due < 0) {
                echo json_encode(array('error' => 'The Net amount is negative', 'amount' => $amount, 'percentage' => $percentage));
                exit();
            }
            $company_net_due = $company_net_due + round($calculated_net_due, 4);
            $company_subsidy = $company_subsidy + round($subsidy, 4);
            $company_gst = $company_gst + $gst_total;
        }
        echo json_encode(array('error' => '', 'company_net' => round($company_net_due, 4), 'amount' => $amount,
            'discount_rate' => $discount_rate,
            'percentage' => $percentage, 'company_subsidy' => round($company_subsidy, 4), 'company_gst' => round($company_gst, 4)));
        exit();
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

    public function get_eid_json() {
        $eid_arr = array();
        $eid = $this->input->post('q');
        if (!empty($eid)) {
            $eid_arr = $this->classtraineemodel->internal_eid_list_autocomplete($eid);
        }
        echo json_encode($eid_arr);
        exit();
    }

    /**
     * This function  to update trainee feedback
     */
    public function trainer_feedback($user_id, $course_id, $class_id) {
        $tenant_id = $this->tenant_id;
        $this->load->model('trainee_model', 'traineemodel');
        $res = $this->classtraineemodel->get_trainer_feedback($user_id, $course_id, $class_id); // s1
        $result = $this->traineemodel->trainer_feedback($user_id, $course_id, $class_id);
        $feedback_score = $this->input->post('feedback_score');
        $feedback_grade = $this->input->post('feedback_grade');
        if ($result == TRUE) {
            $this->db->cache_delete_all();
            if ($res != 0) {
                $previous_data = json_encode($res); //s2 
                user_activity(11, $user_id, $previous_data); //s3 
            }
            $this->session->set_flashdata("success", "Your feedback has been updated successfully.");
        } else {
            $this->session->set_flashdata("error", "We have not been able to save the feedback.Please try again later or get in touch with your Administrator.");
        }
        $extra = '';
        $page = $this->input->post('page');
        $query_string = $this->input->post('query_string');
        if (!empty($page)) {
            $extra .= '/' . $page;
        }
        if (!empty($query_string)) {
            $extra .= $query_string;
        }

        redirect("class_trainee$extra");
    }

    /**
     * For updating trainee feedback
     * @param type $user_id
     * @param type $course_id
     * @param type $class_id
     */
    public function trainee_feedback($user_id, $course_id, $class_id) {
        $tenant_id = $this->tenant_id;
        $result = $this->reportsModel->save_feedback($tenant_id, $class_id, $course_id, $user_id);
        if ($result == TRUE) {
            $this->db->cache_delete_all();
            $this->session->set_flashdata("success", "Your feedback has been updated successfully.");
        } else {
            $this->session->set_flashdata("error", "We have not been able to save the feedback.Please try again later or get in touch with your Administrator.");
        }
        $extra = '';
        $page = $this->input->post('page');
        $query_string = $this->input->post('query_string');
        if (!empty($page)) {
            $extra .= '/' . $page;
        }
        if (!empty($query_string)) {
            $extra .= $query_string;
        }
        redirect("class_trainee$extra");
    }

    /**
     * This method return subsidy_amount 
     */
    public function get_subsidy_amount() {
        $tenant_id = $this->tenant_id;
        $subsidy_type = $this->input->post("subsidy_type");
        $subsidy_amount = $this->classtraineemodel->get_subsidy_amount($tenant_id, $subsidy_type);
        $subsidy_amount = empty($subsidy_amount) ? 0 : $subsidy_amount;
        echo $subsidy_amount;
    }

    /**
     * function to clean_orphan data for selected class
     */
    public function clean_orphan_data() {
        $tenant_id = $this->tenant_id;
        $class = $this->input->post('class');
        $trainees = '';
        $status = $this->classtraineemodel->clean_orpham($tenant_id, $class);
        echo $status;
        exit();
    }

    /**
     * This method used for changing payment mode of the invoice.
     */
    public function change_payment_mode() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $data['companies'] = $this->company->get_activeuser_companies_for_tenant($this->tenant_id);
        $data['page_title'] = 'Class Trainee Enrollment - Change Payment Mode';
        $data['main_content'] = 'classtrainee/change_payment_mode';
        $this->load->view('layout', $data);
    }

    /**
     * This method gets enrollment for a trainee/ company
     */
    public function search_trainee_change_pay_mode() {
        $taxcode_id = $this->input->post('taxcode_id');
        $trainee_id = $this->input->post('trainee_id');
        $company_id = $this->input->post('company_id');
        $result['data'] = $this->classtraineemodel->search_trainee_change_pay_mode($company_id, $taxcode_id, $trainee_id, $this->tenant_id);
        if (empty($result['data'])) {
            $result['trainer'] = $this->get_trainee_name();
            $result['data'] = '';
        }
        echo json_encode($result);
        exit();
    }

    /**
     * This method used for changing the payment mode of not paid enrolments.
     */
    public function change_payment_mode_update() {
        $args = array();
        $args['tenant_id'] = $this->tenant_id;
        $args['trainee_user_id'] = $this->input->post('trainee_user_id');
        $args['payment_due_id'] = $this->input->post('payment_due_id');
        $args['payment_mode'] = $this->input->post('payment_mode');
        $args['course_id'] = $this->input->post('course_id');
        $args['class_id'] = $this->input->post('class_id');
        $args['company_id'] = $this->input->post('company_id');
        $res = $this->classtraineemodel->get_class_enrol_data($args['course_id'], $args['class_id'], $args['trainee_user_id'], $args['company_id']);
        if (empty($args['company_id'])) {
            $status = $this->classtraineemodel->change_payment_mode_individual($args);
            $account_type = '1'; // 1 = individual
            $act_on = $args['trainee_user_id'];
        } else {
            $status = $this->classtraineemodel->change_payment_mode_company($args);
            $account_type = '2'; // 2 = company
            $act_on = $args['company_id'];
        }
        if ($status) {
            $course_id = $this->input->post('course_id');
            $class_id = $this->input->post('class_id');
            $company_id = $this->input->post('company_id');
            $trainee_id = $this->input->post('trainee_user_id');

            if ($this->input->post('payment_mode') == 'NOTPAID') {
                $payment_mode = 'Payment required';
            } else {
                $payment_mode = 'Payment not required';
            }
            $course_name = $this->activitylog->get_course_name($course_id);
            $class_name = $this->activitylog->class_name($class_id);
            $company_name = $this->activitylog->company_name($company_id);
            $trainee_name = $this->activitylog->trainee_name($trainee_id);
            $data = array(
                'course_id' => $course_id,
                'course_name' => $course_name->crse_name,
                'class_id' => $class_id,
                'class_name' => $class_name->class_name,
                'company_id' => $company_id,
                'company_name' => $company_name->company_name,
                'trainee_id' => $trainee_id,
                'trainee_name' => $trainee_name->first_name,
                'payment_mode' => $payment_mode,
                'details' => $res
            );

            $previous_data = json_encode($data);
            user_activity(10, $act_on, $previous_data, $account_type);
            $this->session->set_flashdata("success", "Enrollment mode has been updated successfully.");
        } else {
            $this->session->set_flashdata("error", "We have not been able to change the enrollment mode.Please try again later or get in touch with your Administrator.");
        }
        redirect("class_trainee/change_payment_mode");
    }

    /**
     * This method returns notpaid and notrequired enrollment trainees.
     */
    public function get_notpaid_notrequired_taxcode() {
        $matches = array();
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
        if ($type == 'taxcode') {
            $result = $this->classtraineemodel->get_notpaid_notrequired_taxcode($this->tenant_id, $query_string, '');
            if ($result) {
                foreach ($result as $row) {
                    $matches[] = array(
                        'key' => $row->user_id,
                        'label' => $row->tax_code . ' (Name: ' . $row->first_name . ' ' . $row->last_name . ')',
                        'value' => $row->tax_code
                    );
                }
            }
        } else {
            $result = $this->classtraineemodel->get_notpaid_notrequired_taxcode($this->tenant_id, '', $query_string);
            if ($result) {
                foreach ($result as $row) {
                    $matches[] = array(
                        'key' => $row->user_id,
                        'label' => $row->first_name . ' ' . $row->last_name . ' (NRIC: ' . $row->tax_code . ')',
                        'value' => $row->first_name
                    );
                }
            }
        }
        echo json_encode($matches);
        exit();
    }

    /**
     * function to import trainee feedback
     */
    public function update_trainer_feedback() {
        $data['sideMenuData'] = fetch_non_main_page_content();
        $course = $this->input->post('course');
        $class = $this->input->post('class');
        $trainer = $this->input->post('trainers');
        if ($this->input->post("upload")) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = '*';
            $config['max_size'] = '2048';
            $config['max_width'] = '1024';
            $config['max_height'] = '768';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                $data['error'] = $this->upload->display_errors();
            } else {
                $data = $this->upload->data();
                $this->load->library('excel_reader');
                $this->excel_reader->setOutputEncoding('CP1251');
                $read_perm = $this->excel_reader->read($data['full_path']);
                if ($read_perm == 'FALSE') {
                    $data['error'] = 'File is not readable.';
                } else {
                    $excel_data = $this->excel_reader->sheets[0][cells];
                    $class_detail = $this->class->get_class_details($this->tenant_id, $class);
                    $feedback = $this->validate_trainer_feedback($excel_data, $class, $course, $trainer);
                    if (!empty($feedback)) {
                        $data['details'] = $feedback;
                        $this->load->helper('export');
                        $data['files'] = write_trainer_feedback_status($feedback, $trainer);
                        $data['filesa'] = write_trainer_feedback_status($feedback, $trainer, 'success');
                        $data['filesb'] = write_trainer_feedback_status($feedback, $trainer, 'failed');
                    }
                    unlink('./uploads/' . $data['file_name']);
                }
            }
        }
        $data['page_title'] = 'Update Trainer Feedback';
        $data['main_content'] = 'classtrainee/updatetrainerfeedback';
        $data['courses'] = $this->reportsModel->get_class_courses($this->tenant_id);
        if (!empty($course)) {
            $this->load->model('Course_Model', 'courseModel');
            $data['classes'] = $this->courseModel->get_classes_active_enrollment_certificate_courses($course)->result();
        }
        if (!empty($class)) {
            $data['trainers'] = $this->classtraineemodel->get_trainer_names($class_detail->classroom_trainer);
        }
        $this->load->view('layout', $data);
    }

    /**
     * function created to get class trainer
     */
    public function get_classtrainer() {
        $class = $this->input->get('class');
        $class_details = $this->class->get_class_details($this->tenant_id, $class);
        $trainer_details = $this->classtraineemodel->get_trainer_names($class_details->classroom_trainer);
        $trainers = array();
        foreach ($trainer_details as $trainer) {
            $trainers[] = array('user_id' => $trainer->user_id, 'trainer_name' => $trainer->first_name . ' ' . $trainer->last_name);
        }
        echo json_encode(array('success' => true, 'data' => $trainers));
        exit();
    }

    /**
     * function to validate bulk trainer feedback
     */
    public function validate_trainer_feedback($excel_data, $class, $course, $trainer) {
        unset($excel_data[1]);
        $excel_data = array_filter($excel_data);
        $insert_data = array();
        $fdbk_arr = array('Competent' => 'COMP_C', 'Not Yet Competent' => 'COMP_NYC',
            'Exempted' => 'COMP_EX', 'Absent' => 'COMP_ABS', 'Twice Not Competent' => 'COMP_2NYC');
        foreach ($excel_data as $key => $row) {
            $status = '';
            $excel = array();
            $excel['taxcode'] = $row[1];
            $excel['fullname'] = $row[2];

            $excel['rating'] = $fdbk_arr[trim($row[3])];
            $excel['view_rating'] = $row[3];

            $insert_data[$key] = $excel;

            $error_msg = $this->check_feedback_is_empty($excel, $fdbk_arr);
            if (!empty($error_msg)) {
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'FAILED';
            } else {
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'PASSED';
            }
            $user_enrol_status = $this->classtraineemodel->check_trainee_taxcode_exist($course, $class, $this->tenant_id, trim($excel['taxcode']));
            if ($user_enrol_status->num_rows() == 0) {
                $error_msg .= ' Trainee credentials not found in this class.';
                $insert_data[$key]['failure_reason'] = $error_msg;
                $insert_data[$key]['status'] = 'FAILED';
            } else {
                $insert_data[$key]['user_id'] = $user_enrol_status->row('user_id');
            }
        }
        $insert_status = $this->classtraineemodel->update_trainer_feedback_data($this->tenant_id, $insert_data, $course, $trainer, $class);
        return $insert_status;
    }

    /**
     * This method checks if the mandatory fields in XLS are empty or not
     */
    private function check_feedback_is_empty($excel, $fdbk_arr) {
        $error_msg = '';
        if (empty($excel['taxcode'])) {
            $error_msg .= 'Trainee NRIC/FIN No. is Required.';
        }
        if (empty($excel['rating'])) {
            $error_msg .= ' Trainee Overall Rating is Required.';
        } elseif (!in_array($excel['rating'], $fdbk_arr)) {
            $error_msg .=' Invalid Overall Rating.';
        }
        return $error_msg;
    }

    public function get_invoice() {
        $matches = array();
        $paid = $this->input->post('paid');
        $query_string = htmlspecialchars($_POST['q'], ENT_QUOTES, 'UTF-8');
        $result = $this->classtraineemodel->get_invoice($this->tenant_id, $query_string, $paid);
        if ($result) {
            foreach ($result as $row) {
                $matches[] = array(
                    'key' => $row->invoice_id,
                    'label' => $row->invoice_id . ' (Name: ' . $row->first_name . ' ' . $row->last_name . ')' . $row->tax_code,
                    'value' => $row->invoice_id
                );
            }
        }
        echo json_encode($matches);
        exit();
    }

    /*  added by shubhranshu for client requirement on 21/03/2019 */

    public function chk_nric_restriction($nric = '', $operation) {
        $exists = $this->traineemodel->check_nric_restriction($nric, $operation);
        if ($exists) {
            return 1;
        } else {
            return 0;
        }
    }

    public function check_nric_restriction() {
        extract($_POST);
        $tax_code = trim(($tax_code));
        $operation = trim(($operation));
        $exists = $this->traineemodel->check_nric_restriction($tax_code, $operation);
        if ($exists) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /*  added by shubhranshu for client requirement on 21/03/2019 */

    /**
     * This function will check trainee enrolment status before reschedule.
     */
    public function check_reschedule_status() {
        $class_id = $this->input->post("class_id");
        $trainee_id = $this->input->post("trainee_id");
        $trainee_id_array = explode(",", $trainee_id);
        $status = $this->classtraineemodel->check_reschedule_status($trainee_id_array, $class_id);
        echo json_encode($status);
        exit();
    }

}

/**
 * function to get course_class_starttime list
 * @param type $data_arr
 * @return string
 */
function get_course_class_starttime($data_arr) {

    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            if ($row->lock_status == 1) {
                $loked = "<strong>Class Attendance  :</strong> Locked(" . $row->lock_status . ")";
            } else {
                $loked = "";
            }
            $arr[$row->class_id] = "<strong>Course Name:</strong> " . $row->crse_name . " (" . $row->course_id
                    . ") &nbsp;&nbsp;&nbsp;&nbsp;<strong>Class Name</strong>: " . $row->class_name . " (" . $row->class_id
                    . ") &nbsp;&nbsp;&nbsp;&nbsp;<strong>Start Date: </strong>" . date('d/m/Y (h:i A)', strtotime($row->class_start_datetime))
                    . " ---- <strong>End Date:</strong> " . date('d/m/Y (h:i A)', strtotime($row->class_end_datetime)) . "<br />&nbsp;&nbsp;&nbsp;&nbsp;" . $loked;
        }
        return $arr;
    }
}

/**
 * function to get course_class_starttime list
 * @param type $data_arr
 * @return string
 */
function get_class_starttime($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {

            $arr[$row->class_id . ',' . $row->lock_status] = "<strong>Class Name</strong>: " . $row->class_name . " (" . $row->class_id . ") &nbsp;&nbsp;&nbsp;&nbsp;<strong>Start Date: </strong>" . date('d/m/Y (h:i A)', strtotime($row->class_start_datetime)) . " ---- <strong>End Date:</strong> " . date('d/m/Y (h:i A)', strtotime($row->class_end_datetime));
        }
        return $arr;
    }
}

/**
 * function to get class_id
 * @param type $data_arr
 * @return type
 */
function get_class_id($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            $arr[] = $row->class_id;
        }
        return $arr;
    }
}

/**
 * function to get course id
 * @param type $data_arr
 * @return type
 */
function get_course_id($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            $arr[$row->class_id] = $row->course_id;
        }
        return $arr;
    }
}

/**
 * function to get course id
 * @param type $data_arr
 * @return type
 */
function get_eid_id($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            $arr[$row->class_id] = $row->eid_number;
        }
        return $arr;
    }
}

/**
 * function to get course id
 * @param type $data_arr
 * @return type
 */
function get_active_class_tpg_status($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            $arr[$row->class_id] = $row->tpg_enrolment_status;
        }
        return $arr;
    }
}

/**
 * function to get course id
 * @param type $data_arr
 * @return type
 */
function get_active_class_att_status($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            $arr[$row->class_id] = $row->lock_status;
        }
        return $arr;
    }
}

function get_reschedule_class_att_status($data_arr) {
    if (!empty($data_arr)) {
        $arr = array();
        foreach ($data_arr as $row) {
            $arr[$row->class_id] = $row->lock_status;
        }
        return $arr;
    }
}

/**
 * Function returns the action link.
 * @param type $enrolment_mode
 * @param type $payment_status
 * @param type $invoice_id
 * @param type $user_id
 * @param type $pymnt_due_id
 * @param type $class_id
 * @param type $view_trainee_data
 * @param type $trainee_Status
 * @param type $classStatus
 * @param type $company_id
 * @return string
 */
function get_links($enrolment_mode, $payment_status, $invoice_id, $user_id, $pymnt_due_id, $class_id, $view_trainee_data, $trainee_Status, $classStatus, $company_id, $att_status = NULL) {
    if ($payment_status == 'PYNOTREQD') {
        $tempLinkStr .= '<span style="color:red">Payment Not Required</span>  <br>';
    } else {
        $tempLinkStr = '';
        if ($view_trainee_data->data['user']->role_id != 'ADMN') {
            if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD') {
                $tempLinkStr = '<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>  <br>';
            }
            if ($enrolment_mode == 'SELF' && $payment_status == 'PAID') {
                $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt (Paid)</a>  <br>';
            } else if ($enrolment_mode == 'COMPSPON' && $payment_status == 'PAID') {
                $tempLinkStr .='<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Receipt (Paid)</a> <br>';
            } else if ($enrolment_mode == 'COMPSPON' && $payment_status == 'PARTPAID') {
                $tempLinkStr .='<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Receipt (Part Paid)</a>  <br>';
            }
        } else {
            if ($enrolment_mode == 'SELF' && $payment_status == 'PAID' && $att_status == '1') {
                $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/export_payment_receipt/' . $pymnt_due_id . '">Receipt</a> <br>';
            } elseif ($enrolment_mode == 'SELF' && $payment_status == 'PAID' && $att_status == '0') {
                $tempLinkStr .= '<i>Receipt Not Available(Trainee is absent)</i> <br>';
            } elseif ($enrolment_mode == 'SELF' && $payment_status == 'NOTPAID') {
                if ($trainee_Status == 'ACTIVE') {
                    if ($classStatus != 'COMPLTD')
                        $tempLinkStr .= '<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a> <br>';
                }
            } elseif ($enrolment_mode == 'COMPSPON' && $payment_status == 'PAID') {
                $tempLinkStr .='<a href="' . base_url() . 'class_trainee/export_payment_received/' . $pymnt_due_id . '">Received</a>  <br>';
                if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD')
                    $tempLinkStr .='<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a> <br>';
            } elseif ($enrolment_mode == 'COMPSPON' && ($payment_status == 'PARTPAID' || $payment_status == 'NOTPAID')) {
                if ($trainee_Status == 'ACTIVE' && $classStatus != 'COMPLTD')
                    $tempLinkStr .='<a href="' . base_url() . 'class_trainee/booking_acknowledge_pdf/' . $user_id . '/' . $class_id . '">Booking ACK.</a>  <br>';
            }
        }
    }
    return $tempLinkStr;
}
