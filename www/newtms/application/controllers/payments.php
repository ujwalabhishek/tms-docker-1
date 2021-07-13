<?php

class Payments extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('payments_model');
        $this->load->model('courses_model');
            $this->load->model('meta_values_model');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values');
        $this->load->helper('public_pdf_reports_helper');
        //getting all metadata values
        $this->meta_data = $this->meta_values_model->get_param_map();
    }

    public function index() {
        if($this->session->userdata('userDetails')->user_id==""){
            redirect("course");
        }

        $data['page_title'] = 'Payments and invoices';
        $data['main_content'] = 'payments/paymentslist';
        $totalrows = $this->payments_model->paymentsInvoice_count();
        $field = $this->input->get('f');
        $order_by = $this->input->get('o');
        $records_per_page = RECORDS_PER_PAGE;
        $baseurl = base_url() . 'payments/';
        $pageno = $this->uri->segment(2);
        if (empty($pageno)) {
            $pageno = 1;
        }
        $offset = ($pageno * $records_per_page);
        $data['sort_order'] = $order_by;
        $data['controllerurl'] = 'payments/';
        $tenant_id = TENANT_ID;
        //$data['tabledata'] =$tabledata= $this->payments_model->paymentsInvoice_details($tenant_id, $records_per_page, $offset, $field, $order_by);
        $tabledata= $this->payments_model->paymentsInvoice_details($tenant_id, $records_per_page, $offset, $field, $order_by);
        
        //print_r($tabledata[0]['payment_status']);
        $new_tabledata = array();
        foreach($tabledata as $k=> $row)
        {
            $sfc_claimed=0;
            if($row['payment_status']=='PAID')
            {
                $sfc= $this->payments_model->get_sfc_details($row['invoice_id']);
                $new_tabledata[$k]['sfc_claimed']=$sfc_claimed=$sfc['sfc_claimed'];
            }
         
            $invoice_id= $row['invoice_id'];
            $paid_details = $this->payments_model->get_invoice_paid_details($invoice_id);
            $recieved_amount = 0;
            if(count($paid_details)>0)
            {
                 foreach ($paid_details as $row1) 
                {
                    $recieved_amount = $row1->amount_recd + $recieved_amount;
                }
            }
            $refund_details = $this->payments_model->get_refund_paid_details($invoice_id);
            $refund_amount = 0;
            if(count($refund_details)>0)
            {
                foreach ($refund_details as  $row2) 
                {
                     $refund_amount = $refund_amount + $row2->amount_refund;
                }
            }
            $total_paid_till_date = $recieved_amount - $refund_amount;
            
            $new_tabledata[$k]['recieved_amount']=  number_format($recieved_amount,2,'.','');
            $new_tabledata[$k]['refund_amount']=  number_format($refund_amount,2,'.','');
            $new_tabledata[$k]['total_paid_till_date']=  number_format($total_paid_till_date,2,'.','');
            $new_tabledata[$k]['class_id']=$row['class_id'];
            $new_tabledata[$k]['course_id']=$row['course_id'];
            $new_tabledata[$k]['class_name']=$row['class_name'];
            $new_tabledata[$k]['class_fees']=$row['class_fees'];
            $new_tabledata[$k]['class_start_datetime']=$row['class_start_datetime'];
            $new_tabledata[$k]['class_end_datetime']=$row['class_end_datetime'];
            $new_tabledata[$k]['class_language']=$row['class_language'];
            $new_tabledata[$k]['class_status']=$row['class_status'];
            $new_tabledata[$k]['total_classroom_duration']=$row['total_classroom_duration'];
            $new_tabledata[$k]['class_discount']=$row['class_discount'];
            $new_tabledata[$k]['classroom_location']=$row['classroom_location'];
            $new_tabledata[$k]['enrolment_mode']=$row['enrolment_mode'];
            $new_tabledata[$k]['user_id']=$row['user_id'];
            $new_tabledata[$k]['friend_id']=$row['friend_id'];
            $new_tabledata[$k]['payment_status']=$row['payment_status'];
            $new_tabledata[$k]['total_inv_discnt']=$row['total_inv_discnt'];
            $new_tabledata[$k]['invoice_id']=$row['invoice_id'];
            $new_tabledata[$k]['total_inv_subsdy']=$row['total_inv_subsdy'];
            $new_tabledata[$k]['total_inv_amount']=$row['total_inv_amount'];
            $new_tabledata[$k]['total_gst']=$row['total_gst'];
            $new_tabledata[$k]['first_name']=$row['first_name'];
            $new_tabledata[$k]['att_status']=$row['att_status'];
            $new_tabledata[$k]['total_amount_due']=$row['total_amount_due'];
            $new_tabledata[$k]['subsidy_amount']=$row['subsidy_amount'];
            $new_tabledata[$k]['discount_rate']=$row['discount_rate'];
            $new_tabledata[$k]['gst_amount']=$row['gst_amount'];
          
        }
//        print_r($new_tabledata);
//     exit;
        $data['tabledata']=$new_tabledata;
         
        $this->load->helper('pagination');
        $data['pagination'] = get_pagination($records_per_page, $pageno, $baseurl, $totalrows, $field, $order_by);
        $metaresult = fetch_all_metavalues();
        $values = $metaresult[Meta_Values_Model::CLASS_LANGUAGE];
        $class_values = $metaresult[Meta_Values_Model::CLASSROOM_LOCATION];
        $class_status = $metaresult[Meta_Values_Model::CLASSROOM_STATUS];
        foreach ($values as $value) {
            $status_lookup_language[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_values as $value) {
            $status_lookup_location[$value['parameter_id']] = $value['category_name'];
        }
        foreach ($class_status as $value) {
            $status_lookup_class_status[$value['parameter_id']] = $value['category_name'];
        }
        $data['status_lookup_language'] = $status_lookup_language;
        $data['status_lookup_location'] = $status_lookup_location;
        $data['status_lookup_class_status'] = $status_lookup_class_status;
        $this->load->view('layout_public', $data);
    }
    /*
     * Function  to  generate acknowledgement popup
     * Author:Balwant
     * modified by:Sankar 
     *      */

    public function generate_acknowledgement() {
       
        $clsid = $this->input->post('cls');
        $crsid = $this->input->post('crs');
        $usrid = $this->input->post('usr');
        $ack_data = $this->payments_model->get_acknowledgement_data($clsid, $crsid, $usrid);

        $tanant = $this->courses_model->get_tenant_details();
       
        //sankar code starts here
        //resetting classroom location parameter id with category name
        $ack_data[classroom_location] = $this->meta_data[$ack_data[classroom_location]];
        $ack_data[cert_level] = $this->meta_data[$ack_data[certi_level]];
        //sankar code ends here
        $gender = ($ack_data['gender'] == 'MALE') ? 'Mr. ' : 'Ms. ';
        $Url = base_url() . 'payments/print_ack';

        $date = date('M d Y');
        $startdate = date('M d Y, l', strtotime($ack_data[class_start_datetime])) . ' @ ' . date('h:i A', strtotime($ack_data[class_start_datetime]));
        
        /* skm code start for remark.
         reporting time skm start bcoz of sam request for AOP(67) on 18-may-17*/
        $time = strtotime($ack_data[class_start_datetime]);
        $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
            if($crsid == 67 || $crsid == 121)
            {
                 $li = "Report at center at $reporting_time to register for class";
            }else{
                 $li = "Report at center at 8:30 AM to register for class";
            }
        /* end */
        $tenant_id = TENANT_ID;
        if($tenant_id == 'T12')
        { 
          $name = $tanant->contact_name;
        }else{

            $name = $gender. $ack_data[crse_manager][first_name] . ' ' . $ack_data[crse_manager][last_name];
        }
            
          $message3 = '
             <ol>
                            <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>'.$li.'</li>
                        </ol>';
          /* skm end */
        
        echo '<form action="' . $Url . '" method="post"><p>  
        <a class="close-modal " rel="modal:close" href="#close-modal">Close</a>
			  <h2 class="panel_heading_style">Booking Acknowledgment</h2>			  
		
			  Your seat has been temporarily booked. Please pay the class fees on or before the class start date.
			Temporary booking for <b>' . $ack_data[first_name] . ' ' . $ack_data[last_name] . ' </b> for <b> Course:"' . $ack_data[crse_name] . ',Class:' . $ack_data[class_name] . ',Certificate Code:' . $ack_data[cert_level] . ' </b>".</i><br>
			 </br><strong>Class start date:</strong> ' . $startdate . '<br>
			 </br><strong>Location:</strong> ' . $ack_data[classroom_location] . '<br>
			</br><strong>Contact Details:</strong> ' . $name . ',(Phone: ' . $tanant->tenant_contact_num . ',Email Id: ' . $tanant->tenant_email_id . ')
                         </br><strong>Remark:</strong> ' . $message3 . '<br>
                        <input  type="hidden" value="' . $clsid . '" name="classId"/>
                        <input  type="hidden" value="' . $crsid . '" name="courseId"/>
                        <input  type="hidden" value="' . $usrid . '" name="userId"/> 
                       <input type="hidden"  value="' . $ack_data . '" name="ackdata"/>
                        <div style="clear:both;"></div><br>			 
			 <span style="float:right;">
			<button class="btn btn-primary" type="submit">Print</button>
			  </p></form> ';
    }

    /* Function  for  generating  receipt popup
     * Author:Blessy
     * Modified by:Sankar
     *   */

    public function generate_ack_receipt() {
        $clsid = $this->input->post('cls');
        $crsid = $this->input->post('crs');
        $userid = $this->input->post('usr');
        $invoice_id = $this->input->post('invoice');
        $ack_data = $this->payments_model->get_acknowledgement_data($clsid, $crsid, $userid);

        $sfc_amount= $this->payments_model->get_sfc_details($invoice_id);
        if(!empty($sfc_amount['othr_mode_of_payment'])){ 
        $other_mod_of_payment= " & Other Mode Of Payment : ($ ".number_format($sfc_amount['other_amount_recd'],2,'.','').")";}
        else{
            $other_mod_of_payment="";
        }
        if(($sfc_amount['sfc_claimed']>0))
        { 
              $mod =   'Mode of payment: '.$sfc_amount['mode_of_pymnt'].'($ '.number_format($sfc_amount['sfc_claimed'],2,'.','').')'.''.$other_mod_of_payment;
        }else
        {
            $mod =   'Mode of payment: '.$sfc_amount['mode_of_pymnt'].'($ '.number_format($sfc_amount['amount_recd'],2,'.','').')'.''.$other_mod_of_payment;
        }
        //resetting classroom location parameter id with category name
        $ack_data[classroom_location] = $this->meta_data[$ack_data[classroom_location]];

        $ack_data[cert_level] = $this->meta_data[$ack_data[certi_level]];

        $tanant = $this->courses_model->get_tenant_details();
        $gender = ($ack_data['gender'] == 'MALE') ? 'Mr. ' : 'Ms. ';
        $date = date('M d Y', strtotime($ack_data['inv_date']));
        $logourl = base_url() . 'logos/' . $this->session->userdata(public_tenant_details)->Logo;
        $startdate = date('M d Y, l', strtotime($ack_data[class_start_datetime])) . ' @ ' . date('h:i A', strtotime($ack_data[class_start_datetime]));
        $Url = base_url() . 'payments/print_receipt';
        echo ' <form action="' . $Url . '" method="post"><p>
 
  <h2 class="panel_heading_style">Payment Receipt</h2><br><a class="close-modal " rel="modal:close" href="#close-modal">Close</a>
  <table width="100%">      
      <tbody>
        <tr>
          <td rowspan="4"><img src="' . $logourl . '" border="0" /></td>
          <td colspan="2">' . $tanant->tenant_name . '</td>
          <td class="td_heading">Receipt No.:</td>
          <td>' . date('Y') . '' . $ack_data['invoice_id'] . '</td>
        </tr>
        <tr>
          <td colspan="2">' . $tanant->tenant_address, $tanant->tenant_city . '</td>
          <td class="td_heading">Receipt Date:</td>
          <td>' . $date . '</td>
        </tr>
        <tr>
          <td colspan="4"></td>
        </tr>
        <tr>
          <td colspan="4">' . $tanant->tenant_contact_num . '</td>
        </tr>
        
      </tbody>
    </table><br>
    
    <p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD ' . $ack_data[total_inv_amount] . ' from <b>' 
                . $ack_data[first_name] . ' ' . $ack_data[last_name] . ' </b> for <b> Course: "' . $ack_data[crse_name] . ' ,Class: '
                . $ack_data[class_name] . '  Certificate Code:' . $ack_data[cert_level] . '</b>". '.$mod.'</p>

  <table class="table table-bordered">
      
      <tbody>
        <tr>
        <td class="td_heading">Class Start Date:</td>
        <td>' . $startdate . '</td>
        </tr>
        
        <tr>
        <td class="td_heading">Location:</td>
        <td>' . $ack_data[classroom_location] . '</td>
        </tr>
        
        <tr>
        <td class="td_heading">Contact Details:</td>
        <td>' . $gender . $ack_data[crse_manager][first_name] . ' ' . $ack_data[crse_manager][last_name] . '(Phone:' . $tanant->tenant_contact_num . ',Email Id: ' . $tanant->tenant_email_id . ')
        <input  type="hidden" value="' . $clsid . '" name="classId"/>
        <input  type="hidden" value="' . $crsid . '" name="courseId"/>   
        <input  type="hidden" value="' . $userid . '" name="userId"/>
        </tr>
        </tbody>
        </table>
       
  <p>

</br><p class="red"><i>This is a computer generated receipt and does not require a seal or signature.</i></p>

<div style="clear:both;"></div><br>
<span style="float:right;">
<button id="print_ack_receipt" class="btn btn-primary" type="submit">Print</button>
</p> </form>  ';
    }

    /*
     * Function  to  get the  details to generate pdf
     * Author:Blessy Paul
     * Modified  by:Sankar
     * 
     */

    public function get_pdf_data($userid = 0) {
        $data['clsid'] = $this->input->post('classId');
        $data['crsid'] = $this->input->post('courseId');
        $data['logourl'] = base_url() . 'logos/' . $this->session->userdata(public_tenant_details)->Logo;
        $data['ack_data'] = $this->payments_model->get_acknowledgement_data($data['clsid'], $data['crsid'], $userid);
        //resetting classroom location parameter id with category name
        $data['ack_data']['classroom_location'] = $this->meta_data[$data['ack_data']['classroom_location']];
        $data['ack_data']['certi_level'] = $this->meta_data[$data['ack_data']['certi_level']];
        $data['tanant'] = $this->courses_model->get_tanant();
        return $data;
    }

    /* Function  to  generate  receipt pdf
     * Author:Blessy  */

    public function print_receipt() {
        $userid = $this->input->post('userId');
        $data = $this->get_pdf_data($userid);
        //modified on 27/11/2014
        $data['meta_data'] = $this->meta_data;
        return generate_payment_receipt($data);
    }

    /* Function  to  generate  booking  acknowledgement pdf
     * Author:Blessy  */

    public function print_ack() {
        $userid = $this->input->post('userId');
        $data = $this->get_pdf_data($userid);
        //modified on 27/11/2014
        $data['meta_data'] = $this->meta_data;
        return generate_acknowledgment($data);
    }

    /* Function  to  generate  invoice pdf
     * Author:Blessy  */

    public function print_invoice() {
        $userid = $this->input->post('userId');
        $data = $this->get_pdf_data($userid);
        $data['user_org'] = $this->payments_model->get_user_org_details($userid);
        $data['invoice'] = $this->payments_model->get_invoice_details($data['clsid'], $data['crsid'], $userid);
        $data['meta_data'] = $this->meta_data;
        return generate_invoice($data);
    }

    /* Function  for  invoice  popup

     * Author:Blessy
     * Modified by:Sankar (27/11/2014) */

    public function generate_invoice() {
        $clsid = $this->input->post('cls');
        $crsid = $this->input->post('crs');
        $userid = $this->input->post('usr');
        $invoice_id = $this->input->post('invoice');
        $status = $this->input->post('status');
        $this->load->helper('common_helper');
        
        $paid_details = $this->payments_model->get_invoice_paid_details($invoice_id);
        $paid_arr = array();
        $paid_rcd_till_date = 0;
            foreach ($paid_details as $row) 
            {
                $paid_rcd_till_date = $row->amount_recd + $paid_rcd_till_date;
            }
            $total_paid = $paid_rcd_till_date;
        $refund_details = $this->payments_model->get_refund_paid_details($invoice_id);
        $refund_amount = 0;
            foreach ($refund_details as $k => $row) 
            {
                $refund_amount = $refund_amount + $row->amount_refund;
            }
        $paid_paid_till_date = $paid_rcd_till_date - $refund_amount;
        
           
        $ack_data = $this->payments_model->get_acknowledgement_data($clsid, $crsid, $userid);
        $invoice =  $this->payments_model->get_invoice_details($clsid, $crsid, $userid);
        $user_org = $this->payments_model->get_user_org_details($userid);
        $invoice_amount=$invoice['total_inv_amount'];
        $invoice_amount_temp=$invoice['total_inv_amount'];
        if($status=="PAID"){
           
                 $sfc_amount= $this->payments_model->get_sfc_details($invoice_id);
                  $invoice_amount=$sfc_amount['amount_recd'];
                   $invoice_amount1=$sfc_amount['amount_recd'];
                $invoice_amount=$invoice['total_inv_amount']-$sfc_amount['sfc_claimed'];
                if($sfc_amount['sfc_claimed']>0){
                    $mop=explode('_',$sfc_amount['mode_of_pymnt']);
                    
                }
              
        }
        //$outstanding_amount=$invoice_amount_temp-$invoice_amount1;
        $outstanding_amount=$invoice_amount_temp-$paid_paid_till_date;
        $tanant = $this->payments_model->get_tanant();
        $date = date('M d Y', strtotime($invoice['inv_date']));
        $logourl = base_url() . 'logos/' . $this->session->userdata(public_tenant_details)->Logo;
        $startdate = date('M d Y, l', strtotime($ack_data[class_start_datetime])) . ' @ ' . date(''
                . 'h:i A', strtotime($ack_data[class_start_datetime]));
        $after_discount = $invoice['class_fees'] - $invoice['total_inv_discnt'];
        $after_gst = $after_discount + $invoice['total_gst'];
        $Url = base_url() . 'payments/print_invoice';
        $gender = ($ack_data['gender'] == 'MALE') ? 'Mr. ' : 'Ms. ';
        //added by sankar
        $gst_label = 'GST Not Applicable';
        $gst_prelabel = 'GST OFF';
        if (!empty($invoice['gst_rule'])) {
            $gst_label = 'GST ON, ' . $this->meta_data[$invoice['gst_rule']];
            $gst_prelabel = 'GST (' . number_format($invoice['gst_rate'], 2, '.', '') . '%)';
        }
        
        //code ends here

        echo ' <form action="' . $Url . '" method="post"><p><a class="close-modal " rel="modal:close" href="#close-modal">Close</a>
            <h2 class="sub_panel_heading_style">Invoice 
            <!-- commended by sankar <span class="label label-default push_right"><a href="#export" rel="modal:open" class="small_text1">Print/ Export to PDF</a></span>-->
            </h2>
             <div class="col-lg-6">
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left"><img src="' . $logourl . '" border="0" /></td>
              </tr>
              <tr>
                <td class="td_heading" align="left">' . $tanant[tenant_address] . ' </br>' .
        $tanant[tenant_city] . ' ' . get_catname_by_parm($tanant[tenant_state]) . ' ' . get_catname_by_parm($tanant[tenant_country]) . '</br>
                Tel:' . $tanant[tenant_contact_num] . ' <br>
                ' . $tanant[tenant_email_id] . ' </td>
              </tr>

            </table>

                </div>
                <div class="col-lg-6">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2"><font style="font-size:18px; font-weight:bold;">' . $tanant['invoice_name'] . '</font></td>
              </tr>
              <tr>
                <td colspan="2"><strong>' . $tanant['invoice_name'] . ' No. </strong> ' . $invoice['invoice_id'] . '<br><strong>' . $tanant['invoice_name'] . ' Date</strong>: ' . $date . '<br><br></td>
              </tr>
              <tr>
                <td colspan="2"><strong><u>Individual Details:</u></strong></td>
              </tr>
          
              <tr>
                <td class="td_heading">Address:</td>
                <td>' . $user_org[personal_address_bldg] . '</br>
                    ' . $user_org[personal_address_city] . ' '
        . $this->meta_data[$user_org[personal_address_state]] . ' '
        . $this->meta_data[$user_org[personal_address_country]] . '</td>
              </tr>
              <tr>
                <td class="td_heading">Contact No:</td>
                <td>' . $user_org[contact_number] . '</td>
              </tr>
              <tr>
                <td class="td_heading">Attention:</td>
                <td>' .$gender. $user_org[first_name] . ' ' . $user_org[last_name] . '</td>
              </tr>
            </table>

                </div>

                <div style="clear:both;"></div>
                <br>
                <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th class="th_header">Description</th>
                      <th class="th_header">Unit Price</th>
                      <th class="th_header">Total</th>
                    
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                    <td>' . $ack_data['crse_name'] . ', ' . $invoice['class_name'] . '</td>
                    <td>$' . number_format($invoice['class_fees'], 2, '.', '') . '</td>
                    <td>$' . number_format($invoice['class_fees'], 2, '.', '') . '</td>
                   
                    </tr>
                    
                    <tr>
                    <td>' . $this->meta_data[$invoice['discount_type']] . ' Discount @ ' . number_format($invoice['discount_rate'], 2, '.', '') . '%</td>
                    <td>$' . number_format($invoice['total_inv_discnt'], 2, '.', '') . '</td>
                    <td>$' . number_format($invoice['total_inv_discnt'], 2, '.', '') . '</td>
                  
                    </tr>

                    <tr>
                    <td colspan="2" align="right" class="td_heading">After Discounts</td>
                    <td>$' . number_format($after_discount, 2, '.', '') . '</td>
                    </tr>

                    <tr>
                    <td colspan="2" align="right" class="td_heading">' . $gst_prelabel . '</td>
                    <td>$' . number_format($invoice['total_gst'], 2, '.', '') . '</td>
                   
                    </tr>

                    <tr>
                    <td colspan="2" align="right" class="td_heading">' . $gst_label . '</td>
                    <td>$' . number_format($after_gst, 2, '.', '') . '</td>
                  
                    </tr>
                    <tr>
                    <td>Subsidy :</td>
                    <td>$' . number_format($invoice[total_inv_subsdy], 2, '.', '') . '</td>
                    <td>$' . number_format($invoice[total_inv_subsdy], 2, '.', '') . '</td>
                   
                    </tr>
                    <tr>
                    <td>Skills Future Credit  :</td>
                    <td></td>
                    <td>$' .number_format($sfc_amount['sfc_claimed'],2,'.','').'</td>
                    </tr>
                   
                    <tr class="info">
                    <td colspan="2" align="right" class="td_heading">Invoice Amount</td>
                    <td>$' . number_format($invoice_amount, 2, '.', '') . '</td>
                   
                    </tr>
                  </tbody>
                </table>
                </div>
                <br>
                <div class="table-responsive">
                <div>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th colspan="9">Particulars of Participants</th>
                    </tr>
                    <tr>
                      <th width="16%" class="th_header">Name</th>
                      <th width="13%" class="th_header">Tax Code</th>
                      <th width="9%" class="th_header">TG# </th>
                      <th width="10%" class="th_header">Subsidy</th>
                      <th width="12%" class="th_header">Discount</th>
                      <th width="13%" class="th_header">GST Amount</th>
                      <th width="13%" class="th_header">SFC('.$mop[1].') Amount</th>
                      <th width="16%" class="th_header">Unit Fee(GST Applicable)</th>
                      <th width="11%"  class="th_header">Remarks </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                    <td>' .$gender. $user_org[first_name] . ' ' . $user_org[last_name] . '</td>
                    <td>' . $user_org[tax_code] . '</td>
                    <td>' . $invoice[tg_number] . '</td>
                    <td>$' . number_format($invoice[total_inv_subsdy], 2, '.', '') . '</td>
                    <td>$' . number_format($invoice[total_inv_discnt], 2, '.', '') . '</td>
                    <td>$' . number_format($invoice[total_gst], 2, '.', '') . '</td>
                    <td>$' . number_format($sfc_amount['sfc_claimed'],2,'.','').'</td>
                    <td>$' . number_format($invoice_amount, 2, '.', '') . '</td>
                    <td>'.   $invoice['payment_status'].'</td>    
                            <input  type="hidden" value="' . $clsid . '" name="classId"/>
                            <input  type="hidden" value="' . $crsid . '" name="courseId"/>  
                            <input  type="hidden" value="' . $userid . '" name="userId"/>
                    </tr>
                    <tr>
                     <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                   
                    <td colspan="2">OUT STANDING AMOUNT</td>
                    <td>$' . number_format($outstanding_amount, 2, '.', '') . '</td>
                    <td></td>    
                          
                    </tr>
                  </tbody>
                </table>
                </div>
                </div>
                <div style="clear:both;"></div>
                <br>
                <p class="red">This is a computer generated invoice. No signature is required- ' . $tanant['tenant_name'] . ' Administrator.</p>
                <div style="color:red;">' . html_entity_decode($tanant['invoice_footer_text']) . '</div>
              <div style="clear:both;"></div><br>
              <span style="float:right;">
<!--           <button class="btn btn-primary" type="submit">Print</button></form><br><br>-->
            ';
    }

}

/*End of thr payments.php
  Location:application/controllers/payments.php */