<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* Used to genrate Trainee Feedback PDF (Training completed) start skm*/
function generate_feedback_pdf($tenant_id, $user_id, $course_id, $class_id) {
    $ci = & get_instance();
    $ci->load->library('TMS_TCPDF');
    $ci->load->model('meta_values_model', 'meta');
    $ci->load->model('courses_model', 'course');
    $meta_map = $ci->meta->get_param_map();
    $data = $ci->course->get_trainee_feedback_by_user_id($tenant_id, $course_id, $class_id, $user_id);
    $tabledata = $ci->course->get_feedback_values($tenant_id, $user_id, $course_id, $class_id);
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
    $pdf->SetCreator(PDF_CREATOR);

// set default header data
    //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

// set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// add a page
    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 10);

// -----------------------------------------------------------------------------
//    $pdf->Image(FCPATH . 'logos\\' . $tenant_details->Logo, 0, 10, null, null, 'PNG', '', '', true, 150, 'R', false, false, 1, false, false, false);
//
//    $pdf->writeHTML('<br>', true, false, false, false, '');
//
//    $tbl = '<table style="font-size:8px;">
//        <tr><td>' . $tenant_details->tenant_address . '</td></tr>
//        <tr><td>' . $tenant_details->tenant_city . '</td></tr>
//        <tr><td>Tel: ' . $tenant_details->tenant_contact_num . '</td></tr>
//        <tr><td>' . $tenant_details->tenant_email_id . '</td></tr>
//    </table>';
//    $pdf->writeHTML($tbl, true, false, false, false, 'R');
    
    // set font
    $pdf->SetFont('helvetica', 'B', 12);

    $pdf->Write(0, 'Trainee Feedack  Form', '', 0, 'C', true, 0, false, false, 0);

    $pdf->SetFont('helvetica', '', 10);

    $tbl = '<br><div>Rating Guide based on scale of 1 to 5 where:</div>
    <table style="font-size:9px;">
    <tr><td>1 - Strongly disagree</td><td>2 - Disagree</td><td>3 - Neutral</td><td>4 - Agree</td><td>5 - Strongly agree</td></tr>
</table>';

    $pdf->writeHTML($tbl, true, false, false, false, 'C');

    $tbl = '<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <th width="50px" align="center"> <b>A.</b> </th>
        <th width="450px"> <b>' . $meta_map['FDBCK01'] . '</b></th>
        <th width="80px" align="center"><b>Answer</b></th>
        

    </tr>
    <tr>
        <td align="center">1.</td>
        <td>' . $meta_map['Q01'] . '</td>
        <td>' . $data['Q01']['feedback_answer'] . '</td>
      
    </tr>
    <tr>
        <td align="center">2.</td>
        <td>' . $meta_map['Q02'] . '.</td>
        <td>' . $data['Q02']['feedback_answer'] . '</td>
      
    </tr>
    <tr>
        <td align="center">3.</td>
        <td>' . $meta_map['Q03'] . '. </td>
        <td>' . $data['Q03']['feedback_answer'] . '</td>
       
    </tr>
    <tr>
        <td align="center">4.</td>
        <td>' . $meta_map['Q04'] . '.   </td>
        <td>' . $data['Q04']['feedback_answer'] . '</td>
       
    </tr>
    <tr>
        <td align="center">5.</td>
        <td>' . $meta_map['Q05'] . '. </td>
        <td>' . $data['Q05']['feedback_answer'] . '</td>
       
    </tr>
    <tr>
        <td align="center">6.</td>
        <td>' . $meta_map['Q06'] . '.</td>
        <td>' . $data['Q06']['feedback_answer'] . '</td>
       
    </tr>

    <tr> <td colspan="3"></td></tr>

    <tr>
        <th width="50px" align="center"> <b>B.</b> </th>
        <th width="450px"> <b>' . $meta_map['FDBCK02'] . '</b></th>
        <th width="80px" align="center"><b>Answer</b></th>
        

    </tr>
    <tr>
        <td align="center">1.</td>
        <td>' . $meta_map['Q07'] . '.</td>
        <td>' . $data['Q07']['feedback_answer'] . '</td>
     
    </tr>
    <tr>
        <td align="center">2.</td>
        <td>' . $meta_map['Q08'] . '.  </td>
        <td>' . $data['Q08']['feedback_answer'] . '</td>
      
    </tr>
    <tr>
        <td align="center">3.</td>
        <td>' . $meta_map['Q09'] . '.  </td>
        <td>' . $data['Q09']['feedback_answer'] . '</td>
        
    </tr>
    <tr>
        <td align="center">4.</td>
        <td>' . $meta_map['Q10'] . '. </td>
        <td>' . $data['Q10']['feedback_answer'] . '</td>
      
    </tr>
    <tr>
        <td align="center">5.</td>
        <td>' . $meta_map['Q11'] . '.  </td>
        <td>' . $data['Q11']['feedback_answer'] . '</td>
       
    </tr>

    <tr> <td colspan="3"></td></tr>

    <tr>
        <th width="50px" align="center"> <b>C.</b> </th>
        <th width="450px"> <b>' . $meta_map['FDBCK03'] . '</b></th>
        <th width="80px" align="center"><b>Answer</b></th>
       

    </tr>
    <tr>
        <td align="center">1.</td>
        <td>' . $meta_map['Q12'] . '.</td>
        <td>' . $data['Q12']['feedback_answer'] . '</td>
       
    </tr>
    <tr>
        <td align="center">2.</td>
        <td>' . $meta_map['Q13'] . '.</td>
        <td>' . $data['Q13']['feedback_answer'] . '</td>
       
    </tr>
    <tr>
        <td align="center">3.</td>
        <td>' . $meta_map['Q14'] . '.</td>
        <td>' . $data['Q14']['feedback_answer'] . '</td>
       
    </tr>
    <tr>
        <td align="center">4.</td>
        <td>' . $meta_map['Q15'] . '.</td>
        <td>' . $data['Q15']['feedback_answer'] . '</td>
       
    </tr>
     <tr> <td colspan="3"></td></tr>
        <tr><td align="center"><b>D.</b></td>
<td width="450px">Your satisfaction rating of the training program:</td><td>' . $tabledata['details'][0]['trainee_feedback_rating'] . '</td>
</tr>
    <tr>
    <td align="center"><b>E.</b></td>
    <td colspan="2"><strong>Remarks: </strong>' . $tabledata['details'][0]['other_remarks_trainee'] . '</td></tr>
</table>';

    $pdf->writeHTML($tbl, true, false, false, false, '');

    $pdf->Output('feedback.pdf', 'D');
}//end

/* Used to generate Trainer Feedback PDF (Training completed) start skm*/
function traner_feedback_pdf($feedbackDetails, $tenant_details,$user_details) {
    $ci = & get_instance();
    $ci->load->library('TMS_TCPDF');
    $ci->load->model('Meta_Values', 'meta');
    $meta_map = $ci->meta->get_param_map();
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, TRUE, 'UTF-8', FALSE);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetPrintHeader(FALSE);
    $pdf->SetPrintFooter(FALSE);
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Image(FCPATH . 'logos/' . $tenant_details->Logo, 0, 10, null, null, 'PNG', '', '', TRUE, 150, 'R', FALSE, FALSE, 1, FALSE, FALSE, FALSE);
    $pdf->writeHTML('<br>', TRUE, FALSE, FALSE, FALSE, '');
    $tbl = '
        <table style="font-size:8px;">
            <tr>
                <td>' . $tenant_details->tenant_address . '</td>
            </tr>
            <tr>
                <td>' . $tenant_details->tenant_country . '</td>
            </tr>
            <tr>
                <td>Tel: ' . $tenant_details->tenant_contact_num . '</td>
            </tr>
            <tr>
                <td>' . $tenant_details->tenant_email_id . '</td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($tbl, true, false, false, false, 'R');
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Write(0, 'Trainer Feedback', '', 0, 'C', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 10);
    $tbl = '
        <table cellspacing="0" cellpadding="1" border="1">
            <tr>
                <td width="70%">
                    1. ' . $feedbackDetails['CERTCOLDT']['category_name'] . ': 
                </td>
                <td align="right" width="30%">
                    ' . $feedbackDetails['CERTCOLDT']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td width="70%">
                    2. ' . $feedbackDetails['SATSRATE']['category_name'] . ':
                </td>
                <td align="right" width="30%">
                    ' . $feedbackDetails['SATSRATE']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td>
                    3. ' . $feedbackDetails['CERTCOM1']['category_name'] . ':
                </td>
                <td align="right">
                    ' . $feedbackDetails['CERTCOM1']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td>
                    4. ' . $feedbackDetails['APPKNLSKL']['category_name'] . ':
                </td>
                <td align="right">
                    ' . $feedbackDetails['APPKNLSKL']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td>
                    5. ' . $feedbackDetails['EXPJOBSCP']['category_name'] . ': 
                </td>
                <td align="right">
                    ' . $feedbackDetails['EXPJOBSCP']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td>
                    6. ' . $feedbackDetails['RT3MNTHS']['category_name'] . ': 
                </td>
                <td align="right">
                    ' . $feedbackDetails['RT3MNTHS']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td>
                    7. ' . $feedbackDetails['DTCOMMEMP']['category_name'] . ': 
                </td>
                <td align="right">
                    ' . $feedbackDetails['DTCOMMEMP']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td>
                    8. ' . $feedbackDetails['COMYTCOM']['category_name'] . ': 
                </td>
                <td align="right">
                    ' . $feedbackDetails['COMYTCOM']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    9. ' . $feedbackDetails['COMMNTS']['category_name'] . ' : 
                    ' . $feedbackDetails['COMMNTS']['feedback_answer'] . '
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <span style="font-size:8px;color:#f00;text-align:right">
                        (1. Strongly disagree, 2. Disagree, 3. Neutral, 4. Agree, 5. Strongly agree)
                    </span>
                </td>
            </tr>
        </table>
    ';
    $pdf->writeHTML($tbl, true, false, false, false, '');
        $html = '<div>Name: ' . $user_details->first_name . ' ' . $user_details->last_name . '</div>
        <div>NRIC or Work Pass No.: ' . $user_details->tax_code . '</div>
        <div>Course Title: ' . $user_details->course_title . ' - ' . $user_details->class_title . '</div>
        <div>Course Date: ' . date('d/m/Y', strtotime($user_details->class_end_datetime)) . '</div>'; 
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('trainer_feedback.pdf', 'D');
}//end


function generate_booking_acknowledge_pdf($data, $tenant_details, $booking_no, $booking_date) {
    $ci = & get_instance();
    $ci->load->library('TMS_TCPDF');


    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetPrintHeader(false);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(3, PDF_MARGIN_TOP, 3);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


    $pdf->SetFont('helvetica', 'B', 11);

    $pdf->AddPage();

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


    $pdf->SetFont('helvetica', '', 8);
    $pdf->writeHTML('<br><br>', true, false, false, false, '');
    $tbl = '<table width="100%">
                <tr>
                    <td width="75%">
                        &nbsp;
                    </td>
                    <td width="25%">
                        <table align="right">
                            <tr>
                                <td>' . $pdf->Image(FCPATH . 'logos/' . $tenant_details->Logo, 0, 10, null, null, 'PNG', '', '', true, 150, 'R', false, false, 1, false, false, false) . '</td>
                            </tr>
                            <tr>
                                <td>' . $tenant_details->tenant_name . '</td>
                            </tr>
                            <tr>
                                <td>' . $tenant_details->tenant_address . '<br>' .
            $tenant_details->tenant_country .
            '</td>
                            </tr>
                            <tr>
                                <td>Email: ' . $tenant_details->tenant_email_id . '</td>
                            </tr>
                            <tr>
                                <td>Phone: ' . $tenant_details->tenant_contact_num . '</td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');

    $pdf->SetFont('helvetica', 'B', 11);
    $info = 'Booking Acknowledgment';

    $pdf->Write(0, $info, '', 0, 'C', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);
    $tbl = '<br/><br/><table><tr>
                                <td><b>Booking No.: </b>&nbsp;&nbsp;' . $booking_no . '</td>
                            </tr>
                            <tr>
                                <td><b>Booking Date: </b>' . $booking_date . '</td>
                            </tr></table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $tbl = $data;
    $pdf->writeHTML($tbl, true, false, false, false, '');

    $tbl = '<br><br><span style = "color:red;"><i>This is a computer generated acknowledgement and requires no signature - ' . $tenant_details->tenant_name . ' Administrator.</i></span>';
    $pdf->writeHTML($tbl, true, false, false, false, '');

    $pdf->Output('booking_acknowledge.pdf', 'D');
}

//function generate_payment_receipt($data) {
//    $tenant_details = $data['tenant_det'];
//    $ci = & get_instance();
//    $ci->load->library('TMS_TCPDF');
//
//
//    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//
//    $pdf->SetPrintHeader(false);
//
//    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
//
//    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//
//    $pdf->SetMargins(3, PDF_MARGIN_TOP, 3);
//    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//
//    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//
//
//
//
//    $pdf->AddPage();
//    $pdf->SetFont('helvetica', '', 8);
//    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//    $tbl = '<table width="100%">
//                <tr>
//                    <td width="75%">
//                        &nbsp;
//                    </td>
//                    <td width="25%">
//                        <table align="right">
//                            <tr>
//                                <td>' . $pdf->Image(FCPATH . 'logos/' . $tenant_details->Logo, 0, 10, null, null, 'PNG', '', '', true, 150, 'R', false, false, 1, false, false, false) . '</td>
//                            </tr>
//                            <tr>
//                                <td>' . $tenant_details->tenant_name . '</td>
//                            </tr>
//                            <tr>
//                                <td>' . $tenant_details->tenant_address . '<br>' .
//            $tenant_details->tenant_country .
//            '</td>
//                            </tr>
//                            <tr>
//                                <td>Email: ' . $tenant_details->tenant_email_id . '</td>
//                            </tr>
//                            <tr>
//                                <td>Phone: ' . $tenant_details->tenant_contact_num . '</td>
//                            </tr>
//                            
//                        </table>
//                    </td>
//                </tr>
//            </table>';
//    $pdf->writeHTML($tbl, true, false, false, false, '');
//    $pdf->SetFont('helvetica', 'B', 11);
//
//    $info = 'Payment Receipt';
//
//    $pdf->Write(0, $info, '', 0, 'C', true, 0, false, false, 0);
//    $pdf->SetFont('helvetica', '', 8);
//    $tbl = '<br/><br/><table><tr>
//                                <td><b>Receipt No.: </b>&nbsp;&nbsp;' . $data['book_no'] . '</td>
//                            </tr>
//                            <tr>
//                                <td><b>Receipt Date: </b>' . $data['book_date'] . '</td>
//                            </tr></table>';
//    $pdf->writeHTML($tbl, true, false, false, false, '');
//
//    $pdf->writeHTML($data['message2'], true, false, false, false, '');
//
//    $tbl = '<br><br><table cellspacing="0" cellpadding="1" border="1">
//        <tbody>
//            <tr>
//                <td width="25%" align="center"><b>Class Start Date:</b></td><!--11/12/14-->
//                <td align="center" width="75%" >' . $data['start_date'] . '</td>
//            </tr>
//
//            <tr>
//                <td width="25%" align="center"><b>Location:</b></td><!--11/12/14-->
//                <td align="center" width="75%">' . $data['loc'] . '</td>
//            </tr>
//
//            <tr>
//                <td width="25%" align="center"><b>Contact Details:</b></td> <!--11/12/14-->
//                <td align="center" width="75%">' . $data['contact'] . '</td>
//            </tr>
//        </tbody>
//    </table><p style="color:red;"><i>This is a computer generated receipt and doesn\'t require a seal or signature - ' . $tenant_details->tenant_name . ' Administrator.</i></p>';
//    $pdf->writeHTML($tbl, true, false, false, false, '');
//    $pdf->Output('payment_receipt.pdf', 'D');
//}
function generate_payment_receipt($result) {

    $t_data = $result['tanant'];
    $inv_data = $result['ack_data'];
    $meta_data = $result['meta_data'];
    $ci = & get_instance();
    $ci->load->library('TMS_TCPDF');


    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetPrintHeader(false);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(3, PDF_MARGIN_TOP, 3);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// ---------------------------------------------------------

    $pdf->AddPage();


    $pdf->SetFont('helvetica', 'B', 11);

    $pdf->Write(0, 'Payment Receipt', '', 0, 'C', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);
    $date = date('M d Y');
    $id = $inv_data['invoice_id'];
    $inv_date = $inv_data['inv_date'];
    $in_date = date('M d Y', strtotime($inv_date));
     $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->Image($result['logourl'], 0, 10, null, null, 'PNG', '', '', true, 150, 'R', false, false, 1, false, false, false);
   
    $tbl = '
    <br><br><table cellspacing="0" cellpadding="1">
                <tr>
                    <td width="75%">
                        &nbsp;
                    </td>
                    <td width="25%">
                        <table align="right">
                            <tr>
                                <td>' .  $t_data['tenant_name'] . '</td>
                            </tr>
                            <tr>
                                <td>' . $t_data['tenant_address'] . '<br>'
            .  $t_data['tenant_city'] . ' ' . $meta_data[$t_data['tenant_state']] . ' ' . $meta_data[$t_data['tenant_country']] .
            '</td>
                            </tr>
                            <tr>
                                <td>Tel :' . $t_data['tenant_contact_num'] . '</td>
                            </tr>
                            <tr>
                                <td>Email :' . $t_data['tenant_email_id'] . '</td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td><strong>Receipt No.:</strong> ' . date('Y') . '' . $id .  '</td>
                            </tr>
                            <tr>
                                <td><strong>Receipt Date:</strong> ' . $in_date . '</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>';
    
    
    
    
    
    $tsbl = '<br>
        <table width="100%">
        <tbody>       
                            <tr>
                                <td rowspan="3"><img src="' . $result['logourl'] . '" border="0" height="40px"/></td>
                                <td colspan="2">' . $t_data['tenant_name'] . '</td>
                         
                            </tr>
                            <tr>
                                <td colspan="2">' . $t_data['tenant_address'] . ',' . $t_data['tenant_city'] . '</td>
                        
                            </tr>
                            <tr>
                                <td colspan="4">' . $t_data['tenant_contact_num'] . '</td>
                            </tr>
               
                </table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $p = '<p><font style="font-size:8px; font-weight:bold;">Received</font> with thanks SGD ' . $inv_data['total_inv_amount'] . ' from <b>' . $inv_data['first_name'] . ' ' . $inv_data['last_name'] . '</b> for " <b>Course : ' . $inv_data['crse_name'] . ',Class: ' . $inv_data['class_name'] . ',Certificate Code: ' . $inv_data['certi_level'].'</b> ". Mode of payment:  Online Transfer </p>';
    $pdf->writeHTML($p, true, false, false, false, '');
    $startdate = date('M d Y, l', strtotime($inv_data['class_start_datetime'])) . ' @ ' . date('h:i A', strtotime($inv_data['class_start_datetime']));
    $tbl = '<br><table cellspacing="0" cellpadding="1" border="1">
        <tbody>
            <tr>
                <td><b>Class Start Date:</b></td>
                <td >' . $startdate . '</td>
            </tr>

            <tr>
                <td class="td_heading"><b>Location:</b></td>
                <td>' . $inv_data['classroom_location'] . '</td>
            </tr>

            <tr>
                <td class="td_heading"><b>Contact Details:</b></td>
                <td >' . $inv_data['crse_manager']['first_name'] . ', ' . $inv_data['crse_manager']['contact_number'] . ', ' . $inv_data['crse_manager']['registered_email_id'] . '</td>
            </tr>
        </tbody>
    </table>  
    <p>
</br><p style="color:red;"><i>This is a computer generated receipt and does not require a seal or signature.</i></p><br>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Output('payment_receipt.pdf', 'D');
}

/**
 * modified by pritam (31/06/2016)
 * @param type $data
 */
function generate_loc_pdf($data) {
    $meta_data = $data->meta_data;
    $ci = & get_instance();
    $ci->load->library('TMS_TCPDF');


// create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetPrintHeader(false);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(3, PDF_MARGIN_TOP, 3);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// ---------------------------------------------------------
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 8);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->Image(FCPATH . 'logos/' . $data->Logo, 0, 10, null, null, 'PNG', '', '', true, 150, 'R', false, false, 1, false, false, false);
    $tbl = '<br><br><table cellspacing="0" cellpadding="1">
                <tr>
                    <td width="75%">
                        &nbsp;
                    </td>
                    <td width="25%">
                        <table align="right">
                            <tr>
                                <td>' . $data->tenant_name . '</td>
                            </tr>
                            <tr>
                                <td>' . $data->tenant_address . '<br>'
            . $data->tenant_city . ' ' . $meta_data[$data->tenant_state] . ' ' . $meta_data[$data->tenant_country] .
            '</td>
                            </tr>
                            <tr>
                                <td>Tel :' . $data->tenant_contact_num . '</td>
                            </tr>
                            <tr>
                                <td>Email :' . $data->tenant_email_id . '</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->SetFont('helvetica', 'B', 11);

    $info = 'LOC for ' . $data->crse_name . ' - ' . $data->class_name . '\'';

    $pdf->Write(0, $info, '', 0, 'C', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);
    $gender = ($data->gender == "MALE") ? 'Mr. ' : 'Ms. ';
    $name = $gender . $data->first_name . ' ' . $data->last_name;
    if (!empty($data->exam_date)) {
        $test_date = date('d/m/Y', strtotime($data->exam_date));
    } else {
        $test_date = date('d/m/Y', strtotime($data->class_end_datetime));
    }
    $tbl = '<p>&nbsp;</p><p><b>File Ref: </b></p>
            <p><b>Trainee Ref:</b> ' . $data->tax_code . '</p>
            <p><b>Date: </b>' . date('d-m-Y') . '</p>
            <p>To Whom it May Concern: </p>
            <p><b>LETTER OF CERTIFICATION FOR ATTAINMENT OF SOA FOR THE \'' . $data->crse_name . ' - ' . $data->class_name . '\'</b></p>
                <p>This letter serves to certify that <b><u>' . $name . '
                (' . mask_format($data->tax_code) . ')</u></b>, has successfully completed the course
             of study and certified competent in the<b> ' .
            $data->crse_name . ' - ' . $data->class_name . '</b> on 
            <b><u>' . $test_date . '</u></b> [TEST DATE].</p>
            <p style="color:red;"><i>This is a computer generated report and doesn\'t require a seal or signature - ' . $data->tenant_name . ' Administrator.</i></p>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->Output('LOC.pdf', 'D');
}
/**
 * modified by: Sankar (27/11/2014)
 * @param type $data
 */
/*shubhranshu  start: replace nric first 5 character with mask */
function mask_format($nric) {  
    if(is_numeric($nric) == 1){
        return $nric;
    }else{
        $new_nric = substr_replace($nric,'XXXXX',0,5);   
        //$new_nric = substr_replace($nric,'XXXX',5);        
        return $new_nric;
    }   
}
/* shubhranshu end */
function generate_acknowledgment($data) {
    $tenant = $data['tanant'];
    $inv_data = $data['ack_data'];
    $meta_data = $data['meta_data'];
    $booking_no = date('Y') . ' ' .$inv_data['user_id'];
    $gender = ($inv_data['gender'] == 'MALE') ? 'Mr. ' : 'Ms. ';
    $booking_date = date('d/m/Y');
    $ci = & get_instance();
    $ci->load->library('TMS_TCPDF');
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetPrintHeader(false);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(3, PDF_MARGIN_TOP, 3);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// ---------------------------------------------------------



    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 8);
    $tbl = '<br><br><table cellspacing="0" cellpadding="1">
                <tr>
                    <td>&nbsp;</td>
                    <td width="50%" align="right">
                        <table>
                            <tr>
                                <td>
                                    ' . $pdf->Image($data['logourl'], 0, 10, null, null, 'PNG', '', '', true, 150, 'R', false, false, 1, false, false, false) . '
                                </td>
                            </tr>
                            <tr>
                                <td>' . $tenant['tenant_name'] . '</td>
                            </tr>
                            <tr>
                                <td>' . $tenant['tenant_address'] . '<br>' .
            $tenant['tenant_city'] . ' ' . $meta_data[$tenant['tenant_state']] . ' ' . $meta_data[$tenant['tenant_country']] . '</td>
                            </tr>
                          
                            <tr><td><b>Booking No:</b>'.$booking_no.'</td></tr>
                            <tr><td><b>Booking Date:</b>'.$booking_date.'</td></tr>
                        </table>
                    </td>
                </tr>    
            </table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
    $pdf->SetFont('helvetica', 'B', 12);
//$ht='<style>'.file_get_contents(base_url().'assets/css/bootstrap.min.css').'</style>';
    $pdf->Write(0, 'Booking Acknowledgment', '', 0, 'C', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);
    $date = date('M d Y');
    $startdate = date('M d, Y h:i A', strtotime($inv_data['class_start_datetime']));
    
    /* reporting time skm start bcoz of sam request for AOP(67) on 18-may-17*/
        $time = strtotime($startdate);
        $reporting_time = date("H:i A", strtotime('-30 minutes', $time));
            if($crsid == 67 || $crsid == 121)
            {
                 $li = "Report at center at $reporting_time to register for class";
            }else{
                 $li = "Report at center at 8:30 AM to register for class";
            }
        /* end */
                 
        if($tenant['tenant_id'] == 'T12')
        { 
             $name = $tenant['contact_name'];
        }else{
             
              $name =  $gender.''. $inv_data[crse_manager][first_name] . ' ' . $inv_data[crse_manager][last_name];
        }
            
          $message3 = '
             <ol>
                            <li>All participants please bring along their photo ID card with either their Nric/Fin number stated upon class date.</li>
                            <li>Your NRIC, work permit or will be photocopied on the class date</li>
                            <li>Trim finger nails and remove nail polish</li>
                            <li>'.$li.'</li>
                        </ol>';
    
    
    
    $ack = '      	 
			  Your seat has been temporarily booked. Please pay the class fees on or before the class start date.
			 Temporary booking for <b>'.$gender.'' . $inv_data[first_name] . ' ' . $inv_data[last_name] . ' </b> for "' . $inv_data[crse_name] . ' - ' . $inv_data[class_name] . ',Certificate Code:'.$inv_data[certi_level] .'".<br><br>
                         <strong>Class start date:</strong> ' . $startdate . '<br><br>
			 <strong>Location:</strong> ' . $inv_data[classroom_location] . '<br><br>
			 <strong>Contact Details:</strong> ' .$name . '( Phone:' . $tenant['tenant_contact_num'] . ', Email Id: ' . $tenant['tenant_email_id']  . ')
			   <br/><br/><strong>Remark:</strong> ' . $message3 . '<br><br>
                        <br><br>';
    $pdf->writeHTML($ack, true, false, false, false, '');
     $tbl = '<br><br><span style = "color:red;"><i>This is a computer generated acknowledgement and requires no signature - ' . $tenant['tenant_name'] . ' Administrator.</i></span>';
    $pdf->writeHTML($tbl, true, false, false, false, '');

    $pdf->Output('acknowledgment.pdf', 'D');
}