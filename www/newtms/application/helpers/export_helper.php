<?php
/////added by shubhranshu to print the TMS Report
function export_tms_report_sales_monthwise($result) {
    $CI = & get_instance();
    $tabledata = $result;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('TMS Sales Report Month Wise');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'M') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'TMS Sales MonthWise Report List as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'SL #');
    $sheet->setCellValue('B2', 'Invoice No.');
    $sheet->setCellValue('C2', 'Date Of Invoice');
    $sheet->setCellValue('D2', 'Amount Before GST');
    $sheet->setCellValue('E2', 'GST');
    $sheet->setCellValue('F2', 'Amount After GST');
    $sheet->setCellValue('G2', 'Customer Name');
    $sheet->setCellValue('H2', 'Class Details');
    $sheet->setCellValue('I2', 'Class Start Date');
    $sheet->setCellValue('J2', 'Class End Date');
    $sheet->setCellValue('K2', 'SSG Grant Amount');
    $sheet->setCellValue('L2', 'Net Invoice Amount');
    $sheet->setCellValue('M2', 'Payment Status');

    $sheet->getStyle('A2:M2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => '2551920')
                )
            )
    );
    $sheet->getStyle('A2:S2')->getFont()->setBold(true);
    $rn = 3;
    $CI->load->model('Reports_Model', 'reports');
    foreach ($tabledata as $row) {
//        if ($row->enrolment_mode == 'SELF') {
//           $inv_amt = $CI->reports->get_invoice_data_for_individual($row->invoice_id,$row->user_id);
//        } else {
//           $inv_amt = $CI->reports->get_invoice_data_for_comp($row->invoice_id,$row->user_id);
//           
//        }
        $amt_bfr_gst = ($row->discount_rate ? (ceil($row->class_fees-$row->discount_rate)): $row->class_fees);
        $amt_afr_gst = $amt_bfr_gst+$row->gst_amount;
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $row->invoice_id);
        $sheet->setCellValue('C' . $rn, $row->inv_date);
        $sheet->setCellValue('D' . $rn, number_format($amt_bfr_gst, 2, '.', ''));
        $sheet->setCellValue('E' . $rn, number_format($row->gst_amount, 2, '.', ''));
        
        $sheet->setCellValue('F' . $rn, number_format($amt_afr_gst, 2, '.', ''));
        $sheet->setCellValue('G' . $rn, $row->name);
        $sheet->setCellValue('H' . $rn, $row->class_name);
        $sheet->setCellValue('I' . $rn, date('d/m/Y', strtotime($row->class_start_datetime)));
        $sheet->setCellValue('J' . $rn, date('d/m/Y', strtotime($row->class_end_datetime)));
        $sheet->setCellValue('K' . $rn, $row->subsidy_amount);
        $sheet->setCellValue('L' . $rn, number_format((float)$row->total_amount_due, 2, '.', ''));
        $sheet->setCellValue('M' . $rn, $row->payment_status);
        $rn++;
    }
    $filename = 'Tms_Sales_Monthwise_Report_.xls';
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/////added by shubhranshu to print the TMS Report
function export_tms_report_page($result) {
    $CI = & get_instance();
    $tabledata = $result;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('TMS Report');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'S') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'TMS '.ucfirst(strtolower($result[0]->payment_status)).' Report List as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'SL #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'User ID');
    $sheet->setCellValue('D2', 'Invoice ID');
    $sheet->setCellValue('E2', 'Trainee Name');
    $sheet->setCellValue('F2', 'Company Name');
    $sheet->setCellValue('G2', 'Class Fees');
    $sheet->setCellValue('H2', 'Discount Rate');
    $sheet->setCellValue('I2', 'GST Amount');
    $sheet->setCellValue('J2', 'Net Amount');
    $sheet->setCellValue('K2', 'TG Number');
    $sheet->setCellValue('L2', 'Subsidy Amount');
    $sheet->setCellValue('M2', 'Payment Status');
    $sheet->setCellValue('N2', 'Mode Of Payment');
    $sheet->setCellValue('O2', 'Class Start Date');
    $sheet->setCellValue('P2', 'Class End Date');
    $sheet->setCellValue('Q2', 'Class Name');
    $sheet->setCellValue('R2', 'Training Score');
    $sheet->setCellValue('S2', 'Att Status');

    $sheet->getStyle('A2:S2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => '2551920')
                )
            )
    );
    $sheet->getStyle('A2:S2')->getFont()->setBold(true);
    $rn = 3;
    $CI->load->model('Reports_Model', 'reports');
    foreach ($tabledata as $row) {
        if ($row->enrolment_mode == 'SELF') {
           $inv_amt = $CI->reports->get_invoice_data_for_individual($row->invoice_id,$row->user_id);
        } else {
           $inv_amt = $CI->reports->get_invoice_data_for_comp($row->invoice_id,$row->user_id);
           
        }
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, mask_format($row->tax_code));
        $sheet->setCellValue('C' . $rn, $row->user_id);
        $sheet->setCellValue('D' . $rn, $row->invoice_id);
        $sheet->setCellValue('E' . $rn, $row->name);
        $sheet->setCellValue('F' . $rn, $row->company_name);
        $sheet->setCellValue('G' . $rn, $row->class_fees);
        $sheet->setCellValue('H' . $rn, $row->discount_rate);
        $sheet->setCellValue('I' . $rn, $row->gst_amount);
        $sheet->setCellValue('J' . $rn, ($row->payment_status == 'NOTPAID') ? $row->total_amount_due : $inv_amt);
        $sheet->setCellValue('K' . $rn, $row->tg_number);
        $sheet->setCellValue('L' . $rn, $row->subsidy_amount);
        $sheet->setCellValue('M' . $rn, $row->payment_status);
        $sheet->setCellValue('N' . $rn, $row->mode_of_pymnt);
        $sheet->setCellValue('O' . $rn, $row->class_start_datetime);
        $sheet->setCellValue('P' . $rn, $row->class_end_datetime);
        $sheet->setCellValue('Q' . $rn, $row->class_name);
        $sheet->setCellValue('R' . $rn, $row->training_score);
        $sheet->setCellValue('S' . $rn, $row->att_status);
        $rn++;
    }
    $filename = 'Tms_Report_'.ucfirst(strtolower($result[0]->payment_status)).'.xls';
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
function export_users_page($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->helper('metavalues');
    $data = $query;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Internal Staffs');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'F') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Internal Staff List as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Staff Name');
    $sheet->setCellValue('D2', 'Role');
    $sheet->setCellValue('E2', 'Current Status');
    $sheet->getStyle('A2:E2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:E2')->getFont()->setBold(true);
    $rn = 3;
    foreach ($data->result() as $row) {
        if ($row->tax_code_type && $row->tax_code) {
            if ($row->tax_code_type != 'SNG_3') {
                $type = get_param_value($row->tax_code_type);
                $taxcode = $type->category_name . ' - ' . mask_format($row->tax_code);
            }
        }
        if ($row->other_identi_type && $row->other_identi_code) {
            $tax_code_type = get_param_value($row->tax_code_type);
            $type = get_param_value($row->other_identi_type);
            $taxcode = $tax_code_type->category_name . ' - ' . $type->category_name . ' - ' . mask_format($row->other_identi_code);
        }
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $taxcode);
        $sheet->setCellValue('C' . $rn, $row->first_name . ' ' . $row->last_name);
        $sheet->setCellValue('D' . $rn, $row->role_name);
        $status = get_param_value($row->account_status);
        $sheet->setCellValue('E' . $rn, $status->category_name);
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="internal_staffs.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
function export_users_full($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->helper('metavalues');
    $data = $query;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Internal Staffs');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Q') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:Q1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('A1', 'Internal Staff List - All Details as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Country of Residence');
    $sheet->setCellValue('D2', 'Staff Name');
    $sheet->setCellValue('E2', 'Role');
    $sheet->setCellValue('F2', 'Gender');
    $sheet->setCellValue('G2', 'Date of Birth');
    $sheet->setCellValue('H2', 'Contact Number (P)');
    $sheet->setCellValue('I2', 'Email ID (P)');
    $sheet->setCellValue('J2', 'Personal Address');
    $sheet->setCellValue('K2', 'Company Name');
    $sheet->setCellValue('L2', 'Date of Joining');
    $sheet->setCellValue('M2', 'Designation');
    $sheet->setCellValue('N2', 'Email Id (O)');
    $sheet->setCellValue('O2', 'Contact Number (O)');
    $sheet->setCellValue('P2', 'Office Address');
    $sheet->setCellValue('Q2', 'Current Status');
    $sheet->getStyle('A2:Q2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
    $rn = 3;
    foreach ($data->result() as $row) {
        if ($row->tax_code_type && $row->tax_code) {
            if ($row->tax_code_type != 'SNG_3') {
                $type = get_param_value($row->tax_code_type);
                $taxcode = $type->category_name . ' - ' . mask_format($row->tax_code);
            }
        }
        if ($row->other_identi_type && $row->other_identi_code) {
            $tax_code_type = get_param_value($row->tax_code_type);
            $type = get_param_value($row->other_identi_type);
            $taxcode = $tax_code_type->category_name . ' - ' . $type->category_name . ' - ' . mask_format($row->other_identi_code);
        }
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $taxcode);
        $sheet->setCellValue('C' . $rn, $meta_map[$row->country_of_residence]);
        $sheet->setCellValue('D' . $rn, $row->first_name . ' ' . $row->last_name);
        $sheet->setCellValue('E' . $rn, $row->role_name);
        $sheet->setCellValue('F' . $rn, $meta_map[$row->gender]);
        $dob = ($row->dob == '0000-00-00' || $row->dob == NULL) ? '' : formated_date($row->dob, '-');
        $sheet->setCellValue('G' . $rn, $dob);
        $sheet->setCellValue('H' . $rn, $row->contact_number);
        $sheet->setCellValue('I' . $rn, $row->registered_email_id);
        $sheet->setCellValue('J' . $rn, $row->personal_address_bldg . ' ' . $row->personal_address_city . ' ' . $meta_map[$row->personal_address_state] . ' ' . $meta_map[$row->personal_address_country] . ' ' . $row->personal_address_zip);
        $sheet->setCellValue('K' . $rn, $row->company_name);
        $sheet->setCellValue('L' . $rn, ($row->doj)?date('d/m/Y', strtotime($row->doj)):'');
        $sheet->setCellValue('M' . $rn, ($row->designation =='OTHERS')?$row->designation_others:$meta_map[$row->designation]);
        $sheet->setCellValue('N' . $rn, $row->off_email_id);
        $sheet->setCellValue('O' . $rn, $row->off_contact_number);
        $sheet->setCellValue('P' . $rn, $row->off_address_bldg . ' ' . $row->off_address_city . ' ' . $meta_map[$row->off_address_state] . ' ' . $meta_map[$row->off_address_country] . ' ' . $row->off_address_zip);
        $status = get_param_value($row->account_status);
        $sheet->setCellValue('Q' . $rn, $status->category_name);
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="internal_staffs.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/*
 * for course page data exporting
 */
function export_course_page($query) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Course List');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'J') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:J1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'List of all Courses being offered by the training institute as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:J1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'Course Code');
    $sheet->setCellValue('C2', 'Course Name');
    $sheet->setCellValue('D2', 'Course Manager');
    $sheet->setCellValue('E2', 'Course Type');
    $sheet->setCellValue('F2', 'Class Type');
    $sheet->setCellValue('G2', 'Certification Level');
    $sheet->setCellValue('H2', 'Language');
    $sheet->setCellValue('I2', 'Pre-Requisite');
    $sheet->setCellValue('J2', 'Status');
    $sheet->getStyle('A2:J2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:J2')->getFont()->setBold(true);
    $rn = 3;
    foreach ($query->result_array() as $row) {
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $row['course_id']);
        $sheet->setCellValue('C' . $rn, $row['crse_name']);
        $sheet->setCellValue('D' . $rn, rtrim($CI->course->get_managers($row['crse_manager']), ', '));
        $sheet->setCellValue('E' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['crse_type']), ', '));
        $sheet->setCellValue('F' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['class_type']), ', '));
        $sheet->setCellValue('G' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['certi_level']), ', '));
        $sheet->setCellValue('H' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['language']), ', '));
        $sheet->setCellValue('I' . $rn, rtrim($CI->course->get_pre_requisite($row['pre_requisite']), ', '));
        $sheet->setCellValue('J' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['crse_status'])), ', ');
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Course_list.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * course full data export
 */
function export_course_full($query) {
    $CI = & get_instance();
    $CI->load->helper('common');
    $CI->load->helper('metavalues_helper');
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->model('meta_values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Course List');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:AG1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('A1', 'List of all Courses [Complete Detail] being offered by the training institute as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:AG1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Course Code');
    $sheet->setCellValue('B2', 'Course Name');
    $sheet->setCellValue('C2', 'Course Manager');
    $sheet->setCellValue('D2', 'Course Type');
    $sheet->setCellValue('E2', 'Course Level');
    $sheet->setCellValue('F2', 'Course Duration');
    $sheet->setCellValue('G2', 'Language');
    $sheet->setCellValue('H2', 'Class Type');
    $sheet->setCellValue('I2', 'Course Ref No.');
    $sheet->setCellValue('J2', 'Course Competency Code');
    $sheet->setCellValue('K2', 'Course Material Available');
    $sheet->setCellValue('L2', 'Course Validity');
    $sheet->setCellValue('M2', 'Course Description');
    $sheet->setCellValue('N2', 'Course Pre-requisite');
    $sheet->setCellValue('O2', 'Status');
    $sheet->mergeCells('P2:R2');
    $sheet->getStyle('P2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('P2', 'Sales Executive Commission Detail');
    $sheet->setCellValue('P3', 'User Id');
    $sheet->setCellValue('Q3', 'Exec Name');
    $sheet->setCellValue('R3', 'Rate');
    $sheet->mergeCells('S2:AG2');
    $sheet->getStyle('S2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('S2', 'Classroom Detail');
    $sheet->setCellValue('S3', 'Class Code');
    $sheet->setCellValue('T3', 'Class Name');
    $sheet->setCellValue('U3', 'Start Date');
    $sheet->setCellValue('V3', 'Start Time');
    $sheet->setCellValue('W3', 'End Date');
    $sheet->setCellValue('X3', 'End Time');
    $sheet->setCellValue('Y3', 'Total Seats');
    $sheet->setCellValue('Z3', 'Booked');
    $sheet->setCellValue('AA3', 'Available');
    $sheet->setCellValue('AB3', 'Classroom Trainer');
    $sheet->setCellValue('AC3', 'Classroom Aide');
    $sheet->setCellValue('AD3', 'Lab Assistant');
    $sheet->setCellValue('AE3', 'Assessor');
    $sheet->setCellValue('AF3', 'Language');
    $sheet->setCellValue('AG3', 'Status');
    $rn = 4;
    foreach ($query->result() as $row) {
        $sheet->setCellValue('A' . $rn, $row->course_id);
        $sheet->setCellValue('B' . $rn, $row->crse_name);
        $sheet->setCellValue('C' . $rn, rtrim($CI->course->get_managers($row->crse_manager)), ', ');
        $sheet->setCellValue('D' . $rn, $meta_map[$row->crse_type]);
        $sheet->setCellValue('E' . $rn, $meta_map[$row->certi_level]);
        $sheet->setCellValue('F' . $rn, $row->crse_duration);
        $sheet->setCellValue('G' . $rn, get_param_values_from_map($meta_map, $row->language));
        $sheet->setCellValue('H' . $rn, $meta_map[$row->class_type]);
        $sheet->setCellValueExplicit('I' . $rn, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('I' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('J' . $rn, $row->competency_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('J' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); 
        $sheet->setCellValue('K' . $rn, empty($row->crse_content_path) ? 'No' : 'Yes' );
        $sheet->setCellValue('L' . $rn, ($row->crse_cert_validity) ? 'Yes(' . $row->crse_cert_validity . ')' : 'No');
        $sheet->setCellValue('M' . $rn, $row->description);
        $sheet->setCellValue('N' . $rn, rtrim($CI->course->get_pre_requisite($row->pre_requisite)), ', ');
        $sheet->setCellValue('O' . $rn, $meta_map[$row->crse_status]);
        $exec_query = $CI->course->get_sales_exec_for_course($row->course_id);
        $rn_exec = 0;
        foreach ($exec_query->result() as $exec_row) {
            $sheet->setCellValue('P' . ($rn + $rn_exec), $exec_row->user_id);
            $sheet->setCellValue('Q' . ($rn + $rn_exec), $exec_row->name);
            $sheet->setCellValue('R' . ($rn + $rn_exec), number_format($exec_row->commission_rate, 2, '.', ''));
            $rn_exec++;
        }
        $class_query = $CI->course->get_classes_for_course($row->course_id);
        $rn_class = 0;
        foreach ($class_query->result() as $class_row) {
            $status = $CI->class->get_class_status($class_row->class_id);
            $sheet->setCellValue('S' . ($rn + $rn_class), $class_row->class_id);
            $sheet->setCellValue('T' . ($rn + $rn_class), $class_row->class_name);
            $sheet->setCellValue('U' . ($rn + $rn_class), date_format_singapore($class_row->class_start_datetime));
            $sheet->setCellValue('V' . ($rn + $rn_class), time_format_singapore($class_row->class_start_datetime));
            $sheet->setCellValue('W' . ($rn + $rn_class), date_format_singapore($class_row->class_end_datetime));
            $sheet->setCellValue('X' . ($rn + $rn_class), time_format_singapore($class_row->class_end_datetime));
            $sheet->setCellValue('Y' . ($rn + $rn_class), $class_row->total_seats);
            $booked = $CI->class->get_class_booked_seats_count($class_row->class_id);
            $sheet->setCellValue('Z' . ($rn + $rn_class), $booked);
            $sheet->setCellValue('AA' . ($rn + $rn_class), $class_row->total_seats - $booked);
            $sheet->setCellValue('AB' . ($rn + $rn_class), $CI->course->get_managers($class_row->classroom_trainer));
            $sheet->setCellValue('AC' . ($rn + $rn_class), $CI->course->get_managers($class_row->training_aide));
            $sheet->setCellValue('AD' . ($rn + $rn_class), $CI->course->get_managers($class_row->lab_trainer));
            $sheet->setCellValue('AE' . ($rn + $rn_class), $CI->course->get_managers($class_row->assessor));
            $sheet->setCellValue('AF' . ($rn + $rn_class), get_param_values_from_map($meta_map, $class_row->class_language));
            $sheet->setCellValue('AG' . ($rn + $rn_class), $status); //$meta_map[$class_row->class_status]
            $rn_class++;
        }
        $rn = $rn + max($rn_exec, $rn_class);
        $rn++;
    }
    $sheet->getStyle('A2:AG2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:AG2')->getFont()->setBold(true);
    $sheet->getStyle('P3:AG3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('P3:AG3')->getFont()->setBold(true);
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Course_list.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/* Export all page fields
 */
function export_company_page($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $data = $CI->db->query($query);
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Company List');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Company List as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'Company Name');
    $sheet->setCellValue('C2', 'Last Activity');
    $sheet->setCellValue('D2', 'Contact Details');
    $sheet->setCellValue('E2', 'Registered Users');
    $sheet->setCellValue('F2', 'Active Users');
    $sheet->setCellValue('G2', 'SCN');
    $sheet->setCellValue('H2', 'Status');
    $sheet->getStyle('A2:H2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    $rn = 3;
    foreach ($data->result() as $row) {
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $row->company_name);
        $sheet->setCellValue('C' . $rn, $row->last_activity_details);
        $sheet->setCellValue('D' . $rn, $row->comp_address . ', ' . $row->comp_city . ', ' . $row->comp_state . ', ' . $row->comp_cntry . ', ' . $row->comp_zip);
        $sheet->setCellValue('E' . $rn, $row->num_registered_users);
        $sheet->setCellValue('F' . $rn, $row->num_active_users);
        $sheet->setCellValue('G' . $rn, $row->SCN);
        $sheet->setCellValue('H' . $rn, $row->comp_status);
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="company_list.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
function export_trainee_page($result) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $data = $result;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('User List');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'G') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainee List as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Country');
    $sheet->setCellValue('D2', 'Registration Date');
    $sheet->setCellValue('E2', 'Trainee Name');
    $sheet->setCellValue('F2', 'Account Type');
    $sheet->setCellValue('G2', 'Status');
    $sheet->getStyle('A2:G2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:G2')->getFont()->setBold(true);
    $rn = 3;
    $tabledata = $data->result();
    if(! isset($tabledata[0]->company_name)){
        $CI->load->model('Trainee_Model', 'trainee');
        foreach($tabledata as $k=>$tbldata){
            $company_details = $CI->trainee->get_company_details($tbldata->user_id);
            if($company_details->num_rows()){
                $company_data = $company_details->row();
                $tabledata[$k]->company_name = $company_data->company_name;
                $tabledata[$k]->company_id = $company_data->company_id;
            }
        }
    }    
    foreach ($tabledata as $row) {
        if ($row->company_id != NULL && $row->company_id != '') {
            $ACCOUNTTYPE = $row->company_name . ' (Company)';
        } else {
            $ACCOUNTTYPE = 'Individual';
        }
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $meta_map[$row->tax_code_type] . '-' . mask_format($row->tax_code));
        $sheet->setCellValue('C' . $rn, $meta_map[$row->country_of_residence]);
        $sheet->setCellValue('D' . $rn, date('d/m/Y H:i:s', strtotime($row->registration_date)));
        $sheet->setCellValue('E' . $rn, $row->first_name . ' ' . $row->last_name);
        $sheet->setCellValue('F' . $rn, $ACCOUNTTYPE);
        $trainee_status = get_param_value($row->account_status);
        $sheet->setCellValue('G' . $rn, $trainee_status->category_name);
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Trainee_list.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/*
 * Excel Export (Can be reused)
 */
function export_trainee_full($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $CI->load->model('Trainee_Model', 'trainee');
    $meta_map = $CI->meta->get_param_map();
    $data = $query;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Full List of Trainees');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:AO1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('A1', 'List of Trainees in the training Institute as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:AO1')->getFont()->setBold(true);
    $sheet->getStyle('A2:AE3')->getAlignment()->setWrapText(true);
    $sheet->getStyle('A2:AE3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A2:AE3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    $sheet->getRowDimension(2)->setRowHeight(30);
    $sheet->getRowDimension(3)->setRowHeight(30);
    $sheet->getColumnDimension('M')->setWidth(10);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Country of Residence');
    $sheet->setCellValue('D2', 'Registration Date');
    $sheet->setCellValue('E2', 'Trainee Name');
    $sheet->setCellValue('F2', 'Nationality');
    $sheet->setCellValue('G2', 'Highest Education Level');
    $sheet->setCellValue('H2', 'User Name');
    $sheet->setCellValue('I2', 'Date of Birth');
    $sheet->setCellValue('J2', 'Account Type');
    $sheet->setCellValue('K2', 'Company Name');
    $sheet->setCellValue('L2', 'Gender');
    $sheet->setCellValue('M2', 'Race');
    $sheet->setCellValue('N2', 'Salary Range');
    $sheet->setCellValue('O2', 'Occupation');
    $sheet->setCellValue('P2', 'Email Activation');
    $sheet->setCellValue('Q2', 'Email ID');
    $sheet->setCellValue('R2', 'Certificate Pickup');
    $sheet->setCellValue('S2', 'Address');
    $sheet->setCellValue('T2', 'Status');
    $sheet->setCellValue('U2', 'Educational Details');
    $sheet->setCellValue('U3', 'Level');
    $sheet->setCellValue('V3', 'Yr. of Completion');
    $sheet->setCellValue('W3', 'Score/Grade');
    $sheet->setCellValue('X3', 'Remarks');
    $sheet->setCellValue('Y2', 'Other Certification/ Training Details');
    $sheet->setCellValue('Y3', 'Certificate Name');
    $sheet->setCellValue('Z3', 'Yr. of Certification');
    $sheet->setCellValue('AA3', 'Validity');
    $sheet->setCellValue('AB3', 'Remarks');
    $sheet->setCellValue('AC2', 'Work Experience');
    $sheet->setCellValue('AC3', 'Name of Org.');
    $sheet->setCellValue('AD3', 'Employment From');
    $sheet->setCellValue('AE3', 'Employment To');
    $sheet->setCellValue('AF3', 'Designation / Remarks');
    $sheet->setCellValue('AG2', 'Institute Training Details');
    $sheet->setCellValue('AG3', 'Training Name');
    $sheet->setCellValue('AH3', 'Enrollment Date');
    $sheet->setCellValue('AI3', 'Enrollment Type');
    $sheet->setCellValue('AJ3', 'Training End Date');
    $sheet->setCellValue('AK3', 'Validity');
    $sheet->setCellValue('AL3', 'Trainer Name');
    $sheet->setCellValue('AM3', 'Subsidy');
    $sheet->setCellValue('AN3', 'Feedback Rating');
    $sheet->setCellValue('AO3', 'Status');
    $c_green = 'FFD7E4BC';
    $c_purpur = 'FFE5E0EC';
    $background = array('fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('argb' => 'FFCCCCCC')
    ));
    $sheet->getStyle('A2:T2')->applyFromArray($background);
    $sheet->getStyle('U3:X3')->applyFromArray($background);
    $sheet->getStyle('Y2:AB2')->applyFromArray($background);
    $sheet->getStyle('AC3:AF3')->applyFromArray($background);
    $sheet->getStyle('AG2:AO2')->applyFromArray($background);
    $background['fill']['color']['argb'] = $c_green;
    $sheet->getStyle('U2:X2')->applyFromArray($background);
    $sheet->getStyle('Y3:AB3')->applyFromArray($background);
    $sheet->getStyle('AC2:AF2')->applyFromArray($background);
    $sheet->getStyle('AG3:AO3')->applyFromArray($background);
    $sheet->mergeCells('A3:T3');
    $sheet->mergeCells('U2:X2');
    $sheet->mergeCells('Y2:AB2');
    $sheet->mergeCells('AC2:AF2');
    $sheet->mergeCells('AG2:AO2');
    $sheet->getStyle('A1:T3')->getFont()->setBold(true);
    $sheet->getStyle('S2:AO3')->getFont()->setBold(true);
    $sheet->getStyle('A1:T3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle('S2:AO3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $background['fill']['color']['argb'] = $c_purpur;
    $sheet->getStyle('A3:T3')->applyFromArray($background);
    $rn = 4;
    $rown = 1;
    $tabledata = $data->result();
    if(! isset($tabledata[0]->company_name)){
        foreach($tabledata as $k=>$tbldata){
            $company_details = $CI->trainee->get_company_details($tbldata->user_id);
            if($company_details->num_rows()){
                $company_data = $company_details->row();
                $tabledata[$k]->company_name = $company_data->company_name;
                $tabledata[$k]->company_id = $company_data->company_id;
            }
        }
    }   
    foreach ($tabledata as $row) {
        $sheet->setCellValue('A' . $rn, $rown);
        $sheet->setCellValue('B' . $rn, $meta_map[$row->tax_code_type] . '-' . mask_format($row->tax_code));
        $sheet->setCellValue('C' . $rn, $meta_map[$row->country_of_residence]);
        $sheet->setCellValue('D' . $rn, date('d/m/Y H:i:s', strtotime($row->registration_date)));
        $sheet->setCellValue('E' . $rn, $row->first_name . ' ' . $row->last_name);
        $sheet->setCellValue('F' . $rn, $meta_map[$row->nationality]);
        $sheet->setCellValue('G' . $rn, $meta_map[$row->highest_educ_level]);
        $sheet->setCellValue('H' . $rn, $row->user_name);
        $sheet->setCellValue('I' . $rn, date_format_singapore($row->dob));
        $sheet->setCellValue('J' . $rn, ($row->company_id) ? 'Company' : 'Individual');
        $sheet->setCellValue('K' . $rn, $row->company_name);
        $sheet->setCellValue('L' . $rn, $meta_map[$row->gender]);
        $sheet->setCellValue('M' . $rn, $meta_map[$row->race]);
        $sheet->setCellValue('N' . $rn, $meta_map[$row->salary_range]);
        $sheet->setCellValue('O' . $rn, $meta_map[$row->occupation_code]);
        $sheet->setCellValue('P' . $rn, $meta_map[$row->acc_activation_type]);
        $sheet->setCellValue('Q' . $rn, $row->registered_email_id);
        if ($row->certificate_pick_pref == 'cerit_self')
            $certificate_pick_pref = "I will pickup myself";
        else if ($row->certificate_pick_pref == 'cerit_mail')
            $certificate_pick_pref = "Mail to my personal email Id";
        else if ($row->certificate_pick_pref == 'cerit_post')
            $certificate_pick_pref = "Mail to my postal address";
        $sheet->setCellValue('R' . $rn, $certificate_pick_pref);
        $address = '';
        $pac = '';
        if (!empty($row->personal_address_country)) {
            $pac = $meta_map[$row->personal_address_country];
        }
        $pas = '';
        if (!empty($row->personal_address_state)) {
            $pas = $meta_map[$row->personal_address_state];
        }
        if (!empty($row->personal_address_bldg)) {
            $address .=$row->personal_address_bldg . ', ';
        }
        if (!empty($row->personal_address_city)) {
            $address .=$row->personal_address_city . ', ';
        }
        if (!empty($pas)) {
            $address .=$pas . ', ';
        }
        if (!empty($pac)) {
            $address .=$pac . ', ';
        }
        if (!empty($row->personal_address_zip)) {
            $address .='Zip: ' . $row->personal_address_zip;
        }
        $address = rtrim($address, ', ');
        $sheet->setCellValue('S' . $rn, $address);
        $trainee_status = get_param_value($row->account_status);
        $sheet->setCellValue('T' . $rn, $trainee_status->category_name);
        $max = $no = $rn;
        $info = $CI->trainee->get_trainee($row->user_id);
        foreach ($info['edudetails'] as $item) {
            $sheet->setCellValue('U' . $no, $meta_map[$item['educ_level']]);
            $sheet->setCellValue('V' . $no, $item['educ_yr_completion']);
            $sheet->setCellValue('W' . $no, $item['educ_score']);
            $sheet->setCellValue('X' . $no, $item['educ_remarks']);
            $no++;
        }
        $max = max($max, $no - 1);
        $no = $rn;
        foreach ($info['otherdetails'] as $item) {
            $sheet->setCellValue('Y' . $no, $item['cert_name']);
            $sheet->setCellValue('Z' . $no, $item['yr_completion']);
            $sheet->setCellValue('AA' . $no, date_format_singapore($item['valid_till']));
            $sheet->setCellValue('AB' . $no, $item['oth_remarks']);
            $no++;
        }
        $max = max($max, $no - 1);
        $no = $rn;
        foreach ($info['workdetails'] as $item) {
            $sheet->setCellValue('AC' . $no, $item['org_name']);
            $sheet->setCellValue('AD' . $no, date_format_singapore($item['emp_from_date']));
            $sheet->setCellValue('AE' . $no, date_format_singapore($item['emp_to_date']));
            $sheet->setCellValue('AF' . $no, $meta_map[$item['designation']]);
            $no++;
        }
        $max = max($max, $no - 1);
        $no = $rn;
        $training_details = $CI->trainee->get_excel_all_training_details($row->user_id);
        foreach ($training_details as $item) {
            $sheet->setCellValue('AG' . $no, $item['crse_name'] . "-" . $item['class_name']);
            $sheet->setCellValue('AH' . $no, date('d-m-Y', strtotime($item['enrolled_on'])));
            $sheet->setCellValue('AI' . $no, $meta_map[$item['enrolment_type']]);
            $sheet->setCellValue('AJ' . $no, date('d-m-Y', strtotime($item['class_end_datetime'])));
            $class_end_datetime = date("Y/m/d", strtotime($item['class_end_datetime']));
            $cur_date = strtotime(date('Y/m/d'));
            if ($item['crse_cert_validity'] != 0) {
                $date = strtotime("+" . $item['crse_cert_validity'] . " days", strtotime($class_end_datetime));
                $validity_date = strtotime(date('Y/m/d', $date));
                $validity = date('d/m/Y', $date);
            } else {
                $validity = "Life Long";
            }
            if ($validity == "Life Long") {
                $Status = 'ACTIVE';
            } elseif ($cur_date <= $validity_date) {
                $Status = 'ACTIVE';
            } elseif ($cur_date > $validity_date) {
                $Status = 'Renewal Due';
            }
            $sheet->setCellValue('AK' . $no, $validity);
            $sheet->setCellValue('AL' . $no, ($item['classroom_trainer']) ? $CI->trainee->get_full_name($item['classroom_trainer']) : '');
            $sheet->setCellValue('AM' . $no, ($item['subsidy_amount']) ? $item['subsidy_amount'] : '');
            $sheet->setCellValue('AN' . $no, ($item['trainee_feedback_rating']) ? $item['trainee_feedback_rating'] : '');
            $sheet->setCellValue('AO' . $no, $Status);
            $no++;
        }
        $max = max($max, $no - 1);
        $rn = $max + 1;
        $rown++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Full_Trainee_List.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/*
 * Excel Export (Can be reused)
 */
function export_page_fields($titles, $data, $filename, $sheetname = "", $main_heading = "") {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle($sheetname);
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $sheet->mergeCells('A1:' . $column_names[count($titles)] . '1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', $main_heading);
    $sheet->getStyle('A1:' . $column_names[count($titles)] . '1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $titles_count = count($titles);
    for ($i = 0; $i < $titles_count; $i++) {
        $sheet->setCellValue($column_names[$i + 1] . '2', $titles[$i]);
    }
    $sheet->getStyle('A2:' . $column_names[count($titles)] . '2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:' . $column_names[count($titles)] . '2')->getFont()->setBold(true);
    $rn = 3;
    $data_count = count($data);
    for ($i = 0; $i < $data_count; $i++) {
        $sheet->setCellValue($column_names[0] . $rn, $rn - 2);
        $rn++;
    }
    $rn = 3;
    for ($i = 0; $i < $data_count; $i++) {
        $col_index = 1;
        for ($j = 0; $j < count($data[$i]); $j++) {
            $sheet->setCellValue($column_names[$col_index] . $rn, $data[$i][$j]);
            $col_index++;
        }
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/*
 * Excel Export. Currently used for company all page fields export
 */
function export_all_fields($titles, $sub_titles, $data, $sub_data, $filename, $sheetname = "", $main_heading = "") {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle($sheetname);
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA');
    $sheet->mergeCells('A1:' . $column_names[count($titles)] . '1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('A1:' . $column_names[count($titles)] . '1')->getFont()->setBold(true);
    $sheet->setCellValue('A1', $main_heading);
    $sheet->setCellValue('A2', 'Sl #');
    $titles_count = count($titles);
    for ($i = 0; $i < $titles_count; $i++) {
        $sheet->setCellValue($column_names[$i + 1] . '2', $titles[$i]);
    }
    $sheet->mergeCells($column_names[$i + 1] . '2:' . $column_names[$i + count($sub_titles)] . '2');
    $sheet->getStyle($column_names[$i + 1] . '2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue($column_names[$i + 1] . '2', 'Contact Details');
    $sheet->getStyle('A2:' . $column_names[$i + count($sub_titles)] . '2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:' . $column_names[$i + count($sub_titles)] . '2')->getFont()->setBold(true);
    $sheet->mergeCells('A3:' . $column_names[count($titles)] . '3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A3', '');
    $index = count($titles) + 1;
    for ($j = 0; $j < count($sub_titles); $j++) {
        $sheet->setCellValue($column_names[$index] . '3', $sub_titles[$j]);
        $index++;
    }
    $sheet->getStyle('A3:' . $column_names[$i + count($sub_titles)] . '3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFAAAAAA')
                )
            )
    );
    $sheet->getStyle('A3:' . $column_names[$i + count($sub_titles)] . '3')->getFont()->setBold(true);
    $rn = 4;
    $serial_no = 1;
    $data_count = count($data);
    for ($i = 0; $i < $data_count; $i++) {
        $sheet->setCellValue($column_names[0] . $rn, $serial_no);

        $col_index = 1;
        for ($j = 0; $j < count($data[$i]); $j++) {
            $sheet->setCellValue($column_names[$col_index] . $rn, $data[$i][$j]);
            $col_index++;
        }

        $contact_start_index = $col_index;
        foreach ($sub_data[$i] as $contacts) {
            $col_index = $contact_start_index;
            for ($k = 0; $k < count($contacts); $k++) {
                $sheet->setCellValue($column_names[$col_index] . $rn, $contacts[$k]);
                $col_index++;
            }
            $rn++;
        }

        $serial_no++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

/*
 * for mark attendance page
 */
function export_attendance($results, $class_details, $start, $end, $class_schedule_data='') 
{       
        $data=PAX_PER_SHEET;
        $num_of_sheets=ceil(count($results)/$data);
        $j=0;
        $i=1;
        while($i<=$num_of_sheets)
        {
            $arr[$i] = array_slice($results,$j,$data);//0 - 5, 5- 10, 
            $i++;
            $j=$j+$data;
        }
        $results=$arr1;
        $ci = & get_instance();
        $ci->load->model('class_trainee_model', 'classtraineemodel');
        $class_id = $class_details->class_id;  // skm1
        $course_id = $class_details->course_id; // skm2
        $tenant_id = $class_details->tenant_id; // skm2
        $check_attendance=$ci->classtraineemodel->check_attendance_row($tenant_id,$course_id,$class_id);
//        echo $ci->db->last_query();
//        echo 'check_att='.$check_attendance;
//        exit();
        $interval = date_diff($start, $end);
        $total_days = $interval->format('%a');
        if(count($class_schedule_data)>0 or $total_days==0)
        {
            if($total_days!=0){
                  $count = count($class_schedule_data);
                  $total_days = ($count-1);
              }
              else 
              {
                  $total_days = $interval->format('%a');
              }

            $CI = & get_instance();
            
            $CI->load->library('excel');
            $i=1;
            while($i<=$num_of_sheets)
            {
                //$a="arr".$i;
                $results=$arr[$i];
                $CI->excel->createSheet();
                $CI->excel->setActiveSheetIndex($i);
                $CI->excel->getActiveSheet()->setTitle("Attendance Sheet".$i);

                $sheet = $CI->excel->getActiveSheet();
                $CI->excel->getActiveSheet()->getRowDimension('3')->setRowHeight(50);
                $column_index = PHPExcel_Cell::columnIndexFromString('D');
                $adjusted_column_index = $column_index + $total_days;
                $last_column_name = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index);
                $assmnt_sign_column = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index+1);        
                $sheet->mergeCells('A1:' . $assmnt_sign_column . '1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $period = ($start->format("d M Y") == $end->format("d M Y")) ? "'. Period: " . $start->format("d M Y") : "'. Period: " . $start->format("d M Y") . " - " . $end->format("d M Y");
                $sheet->setCellValue('A1', "Attendance Sheet for '" . $class_details->crse_name . "-" . $class_details->class_name.$period);
                $sheet->setCellValue('A2', 'Sl #');
                $sheet->setCellValue('B2', 'NRIC/FIN No.');
                $sheet->setCellValue('C2', 'Trainee Name');
                $sheet->setCellValue('D2', '');
                $sheet->setCellValue('E2', 'Attendance for the period:' . $period);
                $sheet->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('E2:' . $assmnt_sign_column . '2');
                $sheet->setCellValue($assmnt_sign_column.'3','Assmnt. Sign.');
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('D3', 'Session');
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);

                $days = array();

                $current_date = $start;
                while ($current_date < $end || compare_dates_without_time($current_date, $end)) {
                    $days[] = $current_date;
                    $next_day = DateTime::createFromFormat('U', strtotime("tomorrow 12:00:00", $current_date->getTimestamp()));
                    $current_date = $next_day;
                }
                $sheet->getStyle('A2:' . $assmnt_sign_column . '3')->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCCCCC')
                            )
                        )
                );

                if($total_days != 0)
                { 
                    $weeks = create_week_days_array($start ,$end, $class_schedule_data);
                }
                else
                {
                 $weeks = array($start);
                }
                $rn = 3;
                $cell = 4;
                $is_two_sessions = $class_details->class_session_day == 2;
                foreach ($weeks as $day) 
                {
                    $session_time = "\n " . $class_schedule_data[$day->format('d/m/y')][1];
                    if ($is_two_sessions) {
                        $session_time2 = $class_schedule_data[$day->format('d/m/y')][2];
                        if ($session_time2) {
                            $session_time .= "\n " . $session_time2;
                        }
                    }
                    if($class_schedule_data[$day->format('d/m/y')][1])
                    {
                      // $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time); previious code
                        $sk = explode("to",$session_time);
                        $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $sk[0]);
                        $cell++;
                    }
                    elseif($total_days == 0)
                    {
                        $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $sk[0]);
                    }
                }

                $row = 4;
                $index = 1;
                foreach ($results as $res) 
                {
                    if ($is_two_sessions) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                    }
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, mask_format($res['record']['tax_code']));
                    $sheet->setCellValue('C' . $row, $res['record']['name']);

                    $sheet->setCellValue('D' . $row, 'Session1:');
                    if ($is_two_sessions) {
                        $sheet->setCellValue('D' . ($row + 1), 'Session2:');
                    }
                   

                    $cell = 4;
                    foreach ($weeks as $day) 
                    {
                        $formatted_day = $day->format('Y-m-d'); //echo "<br/>";
                        $ses1 = '';
                        $ses2 = '';

                        if($class_schedule_data[$day->format('d/m/y')])
                        {   
                            if (isset($res[$formatted_day])) {
                            $day_visit = $res[$formatted_day]; //print_r($day_visit);exit;
                            if (isset($day_visit['session_01']) && $day_visit['session_01'] == '1') {
                                $ses1 = 'P';
                            }
                            if ($is_two_sessions && isset($day_visit['session_02']) && $day_visit['session_02'] == '1') {
                                $ses2 = 'P';
                            }
                            }
                            $session_presnet = $ci->classtraineemodel->scheduled_session_count($class_id,$course_id,$formatted_day); //skm3
                            if ($ses1 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses1 = 'AB';
                            }
                            if($session_presnet!=2 and $session_presnet!=3) // skm4 st
                            {
                                if($ses2=='' && $day_visit['session_02'] == '') // this code check that session two is created or not 
                                {
                                    $ses2 ='';
                                }
                            } //skm4 ed
                            elseif ($ses2 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses2 = 'AB';
                            }
                            
                             if($check_attendance<=0){
                                $ses1='P';
                                $ses2='P';
                             }
                            $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                            if ($is_two_sessions) {
                                $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                            }
                            $cell++;
                        }
                        elseif($total_days == 0)
                        {
                            if (isset($res[$formatted_day])) {
                            $day_visit = $res[$formatted_day];
                            if (isset($day_visit['session_01']) && $day_visit['session_01'] == '1') {
                                $ses1 = 'P';
                            }
                            if ($is_two_sessions && isset($day_visit['session_02']) && $day_visit['session_02'] == '1') {
                                $ses2 = 'P';
                            }
                            }
                            if ($ses1 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses1 = 'AB';
                            }
                            if ($ses2 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses2 = 'AB';
                            }

                             if($check_attendance<=0){
                                $ses1='P';
                                $ses2='P';
                             }
                            $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                            if ($is_two_sessions) {
                                $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                            }
                       }
                    }

                    $row += ($is_two_sessions) ? 2 : 1;
                    $index++;
                }

                $sheet->getStyle('A4:C' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A3:A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF000000'),
                        ),
                    ),
                );


                $sheet->getStyle(
                        'A1:' . $sheet->getHighestColumn() . ($sheet->getHighestRow() - 1)
                )->applyFromArray($styleArray);
                $i++;
                
            }
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
      
      
        }
        else 
        {   // if condition on line no :830
            $CI = & get_instance();
            $CI->load->library('excel');
            $i=1;
            while($i<=$num_of_sheets)
            {
                //$a="arr".$i;
                $results=$arr[$i];
                $CI->excel->createSheet();
                $CI->excel->setActiveSheetIndex($i);
                $CI->excel->getActiveSheet()->setTitle("Attendance Sheet".$i);

                $sheet = $CI->excel->getActiveSheet();
                $CI->excel->getActiveSheet()->getRowDimension('3')->setRowHeight(50);
                $column_index = PHPExcel_Cell::columnIndexFromString('D');
                $adjusted_column_index = $column_index + $total_days;
                $last_column_name = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index);
                $assmnt_sign_column = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index+1);        
                $sheet->mergeCells('A1:' . $assmnt_sign_column . '1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $period = ($start->format("d M Y") == $end->format("d M Y")) ? "'. Period: " . $start->format("d M Y") : "'. Period: " . $start->format("d M Y") . " - " . $end->format("d M Y");
                $sheet->setCellValue('A1', "Attendance Sheet for '" . $class_details->crse_name . "-" . $class_details->class_name.$period);
                $sheet->setCellValue('A2', 'Sl #');
                $sheet->setCellValue('B2', 'NRIC/FIN No.');
                $sheet->setCellValue('C2', 'Trainee Name');
                $sheet->setCellValue('D2', '');
                $sheet->setCellValue('E2', 'Attendance for the period:' . $period);
                $sheet->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('E2:' . $assmnt_sign_column . '2');
                $sheet->setCellValue($assmnt_sign_column.'3','Assmnt. Sign.');
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('D3', 'Session');
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);

                $days = array();

                $current_date = $start;
                while ($current_date < $end || compare_dates_without_time($current_date, $end)) {
                    $days[] = $current_date;
                    $next_day = DateTime::createFromFormat('U', strtotime("tomorrow 12:00:00", $current_date->getTimestamp()));
                    $current_date = $next_day;
                }
                $sheet->getStyle('A2:' . $assmnt_sign_column . '3')->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCCCCC')
                            )
                        )
                );

                $rn = 3;
                $cell = 4;
                $is_two_sessions = $class_details->class_session_day == 2;
                foreach ($days as $day) {
                    $session_time = "\n " . $class_schedule_data[$day->format('d/m/y')][1];
                    if ($is_two_sessions) {
                        $session_time2 = $class_schedule_data[$day->format('d/m/y')][2];
                        if ($session_time2) {
                            $session_time .= "\n " . $session_time2;
                        }
                    }
                    $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time);
                    $cell++;
                }

                $row = 4;
                $index = 1;
                foreach ($results as $res) {
                    if ($is_two_sessions) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                    }
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, mask_format($res['record']['tax_code']));
                    $sheet->setCellValue('C' . $row, $res['record']['name']);

                    $sheet->setCellValue('D' . $row, 'Session1:');
                    if ($is_two_sessions) {
                        $sheet->setCellValue('D' . ($row + 1), 'Session2:');
                    }
                  
                    
                    $cell = 4;
                    foreach ($days as $day) {
                        $formatted_day = $day->format('Y-m-d');
                        $ses1 = '';
                        $ses2 = '';
                        if($check_attendance>0)
                        {
                            if (isset($res[$formatted_day])) 
                            {
                                $day_visit = $res[$formatted_day];
                                if (isset($day_visit['session_01']) && $day_visit['session_01'] == '1') {
                                    $ses1 = 'P';
                                }
                                if ($is_two_sessions && isset($day_visit['session_02']) && $day_visit['session_02'] == '1') {
                                    $ses2 = 'P';
                                }
                            }
                            if ($ses1 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses1 = 'AB';
                            }
                            if ($ses2 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses2 = 'AB';
                            }
                        }
                        else{
                            if (isset($res[$formatted_day])) 
                            {
                                $day_visit = $res[$formatted_day];
                                if (isset($day_visit['session_01']) && $day_visit['session_01'] == '1') {
                                    $ses1 = 'P';
                                }
                                if ($is_two_sessions && isset($day_visit['session_02']) && $day_visit['session_02'] == '1') {
                                    $ses2 = 'P';
                                }
                            }
                            if ($ses1 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses1 = 'P';
                            }
                            if ($ses2 == '' && strtotime($formatted_day) <= strtotime(date('Y-m-d'))) {
                                $ses2 = 'P';
                            }
                        }
                        if($check_attendance<=0){
                                $ses1='P';
                                $ses2='P';
                             }
                        $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                        if ($is_two_sessions) {
                            $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                        }
                        $cell++;
                    }

                    $row += ($is_two_sessions) ? 2 : 1;
                    $index++;
                }

                $sheet->getStyle('A4:C' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A3:A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF000000'),
                        ),
                    ),
                );


                $sheet->getStyle(
                        'A1:' . $sheet->getHighestColumn() . ($sheet->getHighestRow() - 1)
                )->applyFromArray($styleArray);
                $i++;
            }
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output'); 
        }
    
}


//end export attendance
/*
 * Export trainee feedback
 */
function export_trainee_feedback($results, $class_details) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle("Trainee Feedback Sheet");

    $sheet = $CI->excel->getActiveSheet();

    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', "Trainer Feedback for '" . $class_details->crse_name . "-" . $class_details->class_name . "'");
    $sheet->setCellValue('A2', 'NRIC/FIN No.');
    $sheet->setCellValue('B2', 'Trainee Name');
    $sheet->setCellValue('C2', 'Feedback Date');
    $sheet->setCellValue('D2', 'Overall Rating');

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(25);
    $sheet->getColumnDimension('D')->setWidth(25);

    $sheet->getStyle('A2:D2')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );

    $sheet->getStyle('A2:D2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($results as $row) {
        $sheet->setCellValue('A' . $rn, mask_format($row['tax_code']));
        $sheet->setCellValue('B' . $rn, $row['first_name']. ' ' .$row['last_name']);
        $sheet->setCellValue('C' . $rn, ($row['trainer_fdbck_on']) ? date('d/m/Y',strtotime($row['trainer_fdbck_on'])) : '');
        if($row['training_score']!="ABS")
        {
             $sheet->setCellValue('D' . $rn, $row['trainee_feedback_rating']);
        }
        else
        {
            $sheet->setCellValue('D' . $rn, $row['training_score']);
        }

        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Trainee_feedback.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

/*
 * Export WDA report
 */
function export_wda_report($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Internal Users');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'WDA report as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'NRIC/FIN No.');
    $sheet->setCellValue('B2', 'Trainee Name');
    $sheet->setCellValue('C2', 'Account Type');
    $sheet->setCellValue('D2', 'Home Address');
    $sheet->setCellValue('E2', 'Contact Details');
    $sheet->setCellValue('F2', 'Class Start Date');
    $sheet->setCellValue('G2', 'Assessment Date');
    $sheet->setCellValue('H2', 'Amount Paid');
    $sheet->getStyle('A2:H2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:H2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($query->result() as $row) {
        $sheet->setCellValue('A' . $rn, $row->tax_code);
        $sheet->setCellValue('B' . $rn, $row->name);
        $sheet->setCellValue('C' . $rn, get_param_values_from_map($meta_map, $row->account_type));
        $sheet->setCellValue('D' . $rn, $row->address);
        $sheet->setCellValue('E' . $rn, $row->contact_details);
        $sheet->setCellValue('F' . $rn, date_format_singapore($row->class_start_datetime));
        $sheet->setCellValue('G' . $rn, date_format_singapore($row->assmnt_date));
        $sheet->setCellValue('H' . $rn, $row->amount_recd);
        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="WDA.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

/*
 * Export Payment Recevied report
 */
function export_pymnt_report($query) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Payment Recevied');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Payment Recevied report as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Company Name');
    $sheet->setCellValue('B2', 'Course Name');
    $sheet->setCellValue('C2', 'Class Name');
    $sheet->setCellValue('D2', 'NRIC/FIN No.');
    $sheet->setCellValue('E2', 'Name');
    $sheet->setCellValue('F2', 'Received on');
    $sheet->setCellValue('G2', 'Amount Received');
    $sheet->getStyle('A2:G2')->getAlignment()->setWrapText(TRUE);
    $sheet->getStyle('A2:G2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A2:G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(16);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(16);
    $sheet->getColumnDimension('F')->setWidth(10);
    $sheet->getColumnDimension('G')->setWidth(10);

    $sheet->getStyle('A2:G2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:G2')->getFont()->setBold(TRUE);

    $rn = 3;
    foreach ($query->result() as $row) {
        $sheet->setCellValue('A' . $rn, $row->companyName);
        $sheet->setCellValue('B' . $rn, $row->crse_name);
        $sheet->setCellValue('C' . $rn, $row->class_name);
        $sheet->setCellValue('D' . $rn, $row->tax_code);
        $sheet->setCellValue('E' . $rn, $row->name);
        $sheet->setCellValue('F' . $rn, date_format_singapore($row->recd_on) . ' ' . time_format_singapore($row->recd_on));
        $sheet->setCellValue('G' . $rn, $row->amount_recd);
        $rn++;
    }

    $sheet->getStyle('G3:G' . $rn)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Payment-Recevied.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/*
 * Export Refunds report
 */
function export_refunds_report($query) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Refunds');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Refunds report as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Invoice/Receipt #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Name');
    $sheet->setCellValue('D2', 'Refund Date');
    $sheet->setCellValue('E2', 'Re fund Amount');
    $sheet->setCellValue('F2', 'Reason');
    $sheet->setCellValue('G2', 'Refunded By');

    $sheet->getStyle('A2:G2')->getAlignment()->setWrapText(TRUE);
    $sheet->getStyle('A2:G2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A2:G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $sheet->getColumnDimension('A')->setWidth(7);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(30);
    $sheet->getColumnDimension('D')->setWidth(7);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(40);
    $sheet->getColumnDimension('G')->setWidth(40);

    $sheet->getStyle('A2:G2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:G2')->getFont()->setBold(TRUE);

    $rn = 3;
    foreach ($query->result() as $row) {
        $sheet->setCellValue('A' . $rn, $row->invoice_id);
        $sheet->setCellValue('B' . $rn, $row->tax_code);
        $sheet->setCellValue('C' . $rn, $row->name);
        $sheet->setCellValue('D' . $rn, date_format_singapore($row->refund_on));
        $sheet->setCellValue('E' . $rn, $row->amount_refund);
        $sheet->setCellValue('F' . $rn, $row->reason);
        $sheet->setCellValue('G' . $rn, $row->refund_by);
        $rn++;
    }

    $sheet->getStyle('G3:G' . $rn)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Refunds.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to create XLS for Certifications
 */
function export_cert_report($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Certifications report');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Certifications report as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Course Name');
    $sheet->setCellValue('B2', 'Class Name');
    $sheet->setCellValue('C2', 'NRIC/FIN No.');
    $sheet->setCellValue('D2', 'Trainee Name');
    $sheet->setCellValue('E2', 'Course Completion Date');
    $sheet->setCellValue('F2', 'Collection Date');
    $sheet->setCellValue('G2', 'Current Status');

    $sheet->getStyle('A2:G2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:G2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($query->result() as $row) {
        switch ($row->status) {
            case 'PENDCOLL':
                $stat = 'Life Long';
                break;
            case 'EXPIRD':
                $stat = $meta_map[$row->status] . ' [' . date_format_singapore($row->validityDate) . ']';
                break;
            default:
                $stat = 'Active [' . date_format_singapore($row->validityDate) . ']';
                break;
        }

        $sheet->setCellValue('A' . $rn, $row->crse_name);
        $sheet->setCellValue('B' . $rn, $row->class_name);
        $sheet->setCellValue('C' . $rn, $row->tax_code);
        $sheet->setCellValue('D' . $rn, $row->name);
        $sheet->setCellValue('E' . $rn, date_format_singapore($row->class_end_datetime));
        $sheet->setCellValue('F' . $rn, date_format_singapore($row->certificate_coll_on));
        $sheet->setCellValue('G' . $rn, $stat);
        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Certifications_report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
function export_invoice_report($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Invoice report');

    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Invoice report as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Invoice No.');
    $sheet->setCellValue('B2', 'Invoice Date');
    $sheet->setCellValue('C2', 'NRIC/FIN No.');
    $sheet->setCellValue('D2', 'Name');
    $sheet->setCellValue('E2', 'Discount');
    $sheet->setCellValue('F2', 'Subsidy');
    $sheet->setCellValue('G2', 'GST');
    $sheet->setCellValue('H2', 'Net Amt.');
    $sheet->setCellValue('I2', 'Status');

    $sheet->getStyle('A2:I2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:G2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($query->result() as $data) {


        if ($data->enrolment_mode === 'COMPSPON') {
            $CI->load->model('reports_model');
            $data->payment_status = $CI->reports_model->check_not_part_paid($data->pymnt_due_id);
        }


        $paid_arr = array('PAID' => 'Paid', 'PARTPAID' => 'Part Paid', 'NOTPAID' => 'Not Paid');
        if ($data->enrolment_mode == 'SELF') {
            $taxcode = 'Individual ( ' . $data->tax_code . ' )';
            $name = 'Individual ( ' . $data->first_name . ' ' . $data->last_name . ' )';
            $status = $paid_arr[$data->payment_status];
        } else {
            $taxcode = 'company ( ' . $data->comp_regist_num . ' )';
            $name = 'company ( ' . $data->company_name . ' )';
            $status = ($data->payment_status > 0) ? 'Part Paid/Not Paid' : 'Paid';
        }


        $sheet->setCellValue('A' . $rn, $data->invoice_id);
        $sheet->setCellValue('B' . $rn, date('d/m/Y', strtotime($data->inv_date)));
        $sheet->setCellValue('C' . $rn, $taxcode);
        $sheet->setCellValue('D' . $rn, $name);
        $sheet->setCellValue('E' . $rn, '$' . number_format($data->total_inv_discnt, 2, '.', '') . ' SGD');
        $sheet->setCellValue('F' . $rn, '$' . number_format($data->total_inv_subsdy, 2, '.', '') . ' SGD');
        $sheet->setCellValue('G' . $rn, '$' . number_format($data->total_gst, 2, '.', '') . ' SGD');
        $sheet->setCellValue('H' . $rn, '$' . number_format($data->total_inv_amount, 2, '.', '') . ' SGD');
        $sheet->setCellValue('I' . $rn, $status);

        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Invoice_report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/*
 * Export enrol report
 */
function export_report_enroll($results, $sales_details) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle("Enrollment Report Sheet");
    $sheet = $CI->excel->getActiveSheet();
   $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $info = 'Enrollment Report for Sales Executive ' . $sales_details->first_name . ' ' . $sales_details->last_name .
            '[' . $sales_details->tax_code . '/' . $meta_map[$sales_details->tax_code_type] . '] Contact Detail:' . $sales_details->contact_number . ', ' . $sales_details->off_email_id;
    $sheet->setCellValue('A1', $info);
    $sheet->setCellValue('A2', 'Course Name');
    $sheet->setCellValue('B2', 'Class Name');
    $sheet->setCellValue('C2', 'NRIC/FIN No.');
    $sheet->setCellValue('D2', 'Trainee Name');
    $sheet->setCellValue('E2', 'Enrollment Date');
    $sheet->setCellValue('F2', 'Country');
    $sheet->setCellValue('G2', 'Trainee Contact Details');

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(25);
    $sheet->getColumnDimension('D')->setWidth(25);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(35);

    $sheet->getStyle('A2:G2')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );

    $sheet->getStyle('A2:G2')->getFont()->setBold(true);

    $rn = 3;
    if (empty($results)) {
        $sheet->setCellValue('A' . $rn, 'There are enrollments done during this period by the sales executive');
    } else {
        foreach ($results as $row) {
            $sheet->setCellValue('A' . $rn, $row['crse_name']);
            $sheet->setCellValue('B' . $rn, $row['class_name']);
            $sheet->setCellValue('C' . $rn, $row['tax_code'] . '/' . $meta_map[$row['tax_code_type']]);
            $sheet->setCellValue('D' . $rn, $row['name']);
            $sheet->setCellValue('E' . $rn, $row['enrolment_date']);
            $sheet->setCellValue('F' . $rn, $meta_map[$row['country']]);
            $sheet->setCellValue('G' . $rn, $row['contact']);

            $rn++;
        }
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Enrollment_report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to export class page fields
 */
function export_class_page($result) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Course List');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Q') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $course_id = $CI->input->get('course_id');
    $course = $CI->course->get_course_detailse($course_id);
    $sheet->mergeCells('A1:Q1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('A1', 'List of all Classes being offered by the training institute as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
    $sheet->mergeCells('H2:Q2');
    $sheet->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'Course Code');
    $sheet->setCellValue('C2', 'Course Name');
    $sheet->setCellValue('D2', 'Course Manager');
    $sheet->setCellValue('E2', 'Course Ref No.');
    $sheet->setCellValue('F2', 'Course Competency Code');
    $sheet->setCellValue('G2', 'Course Level');
    $sheet->setCellValue('H2', 'Class Details');
    $sheet->getStyle('A2:Q2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:Q2')->getFont()->setBold(true);
    $sheet->mergeCells('A3:G3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A3', ' ');
    $sheet->setCellValue('H3', 'Class Code');
    $sheet->setCellValue('I3', 'Class Name');
    $sheet->setCellValue('J3', 'Start Date & Time');
    $sheet->setCellValue('K3', 'Start Date & Time');
    $sheet->setCellValue('L3', 'Classroom Trainer');
    $sheet->setCellValue('M3', 'Lab Trainer');
    $sheet->setCellValue('N3', 'Assessor');
    $sheet->setCellValue('O3', 'Training Aide');
    $sheet->setCellValue('P3', 'Language');
    $sheet->setCellValue('Q3', 'Status');
    $sheet->getStyle('A3:Q3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCFF')
                )
            )
    );
    $sheet->getStyle('A3:Q3')->getFont()->setBold(true);

    $rn = 4;
    foreach ($result as $row) {
        $sheet->setCellValue('A' . $rn, $rn - 3);
        $sheet->setCellValue('B' . $rn, $course->course_id);
        $sheet->setCellValue('C' . $rn, $course->crse_name);
        $sheet->setCellValue('D' . $rn, rtrim($CI->course->get_managers($course->crse_manager), ', '));
        $sheet->setCellValueExplicit('E' . $rn, $course->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('E' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('F' . $rn, $course->competency_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('F' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); 
        $sheet->setCellValue('G' . $rn, $CI->course->get_metadata_on_parameter_id($course->certi_level));
        $sheet->setCellValue('H' . $rn, $row->class_id);
        $sheet->setCellValue('I' . $rn, $row->class_name);
        $sheet->setCellValue('J' . $rn, date('d/m/Y h:i A', strtotime($row->class_start_datetime)));
        $sheet->setCellValue('K' . $rn, date('d/m/Y h:i A', strtotime($row->class_end_datetime)));
        $sheet->setCellValue('L' . $rn, $CI->class->get_trainer_names($row->classroom_trainer));
        $sheet->setCellValue('M' . $rn, $CI->class->get_trainer_names($row->lab_trainer));
        $sheet->setCellValue('N' . $rn, $CI->class->get_trainer_names($row->assessor));
        $sheet->setCellValue('O' . $rn, $CI->class->get_course_manager_names($row->training_aide));
        $sheet->setCellValue('P' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row->class_language), ', '));
        $sheet->setCellValue('Q' . $rn, $CI->class->get_class_status($row->class_id, $CI->input->get('class_status')));
        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Class_list.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to export class all fields
 */
function export_class_full($result) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Course List');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'H') as $columnID) {
        $var = 'A';
        $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }

    $course_id = $CI->input->get('course_id');
    $course = $CI->course->get_course_detailse($course_id);
    $sheet->mergeCells('A1:Z1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $sheet->setCellValue('A1', 'List of all Classes being offered by the training institute as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:Z1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'Course Code');
    $sheet->setCellValue('C2', 'Course Name');
    $sheet->setCellValue('D2', 'Course Manager');
    $sheet->setCellValue('E2', 'Course Ref No.');
    $sheet->setCellValue('F2', 'Course Competency Code');
    $sheet->setCellValue('G2', 'Course Level');
    $sheet->mergeCells('H2:AC2');
    $sheet->getStyle('H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('H2', 'Classroom Details');

    $sheet->mergeCells('AD2:AG2');
    $sheet->getStyle('AD2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('AD2', 'Assessment Details');
    $sheet->setCellValue('AH2', ' ');

    $sheet->getStyle('A2:AH2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:AH2')->getFont()->setBold(true);
    $sheet->mergeCells('A3:G3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A3', '');

    $sheet->setCellValue('H3', 'Class Code');
    $sheet->setCellValue('I3', 'Class Name');
    $sheet->setCellValue('J3', 'Start Date & Time');
    $sheet->setCellValue('K3', 'End Date & Time');
    $sheet->setCellValue('L3', 'Duration');
    $sheet->setCellValue('M3', 'Min. Seats');
    $sheet->setCellValue('N3', 'Total Seats');
    $sheet->setCellValue('O3', 'Booked Seats');
    $sheet->setCellValue('P3', 'Available Seats');
    $sheet->setCellValue('Q3', 'Classroom Trainer');
    $sheet->setCellValue('R3', 'Lab Trainer');
    $sheet->setCellValue('S3', 'Assessor');
   $sheet->setCellValue('T3', 'Training Aide');
    $sheet->setCellValue('U3', 'Language');
    $sheet->setCellValue('V3', 'Classroom Location');
    $sheet->setCellValue('W3', 'Lab Location');
    $sheet->setCellValue('X3', 'Fees');
    $sheet->setCellValue('Y3', 'Class Discount');
    $sheet->setCellValue('Z3', 'Class Sessions');
    $sheet->setCellValue('AA3', 'Payment Details');
    $sheet->setCellValue('AB3', 'Sales Exec.');
    $sheet->setCellValue('AC3', 'Certi. Coll Date');
    $sheet->setCellValue('AD3', 'Date & Time');
    $sheet->setCellValue('AE3', 'Assessor');
    $sheet->setCellValue('AF3', 'Trainee');
    $sheet->setCellValue('AG3', 'Venue');
    $sheet->setCellValue('AH3', 'Status');

    $sheet->getStyle('A3:AH3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCFF')
                )
            )
    );
    $sheet->getStyle('A3:AH3')->getFont()->setBold(true);
    $rnnn = $rn = 4;

    foreach ($result as $row) {
        $sales_arr = $CI->class->get_class_salesexec($row->tenant_id, $row->course_id, $row->sales_executive);
        $sales_exec = '';
        foreach ($sales_arr as $r):
            $sales_exec .= $r['first_name'] . ' ' . $r['last_name'] . ' (' . number_format($r['commission_rate'], 2, '.', '') . '%), ';
        endforeach;
        $sales_exec = rtrim($sales_exec, ', ');
        $classroom_location = ($row->classroom_location == 'OTH') ? 'Others (' . $row->classroom_venue_oth . ')' : $CI->course->get_metadata_on_parameter_id($row->classroom_location);
        $lab_location = ($row->lab_location == 'OTH') ? 'Others (' . $row->lab_venue_oth . ')' : $CI->course->get_metadata_on_parameter_id($row->lab_location);

        $class_assmnt = $CI->classmodel->get_class_details_assmnts($row->tenant_id, $row->class_id);
        $def_assessment = $CI->class->get_def_assessment($row->tenant_id, $row->class_id, $class_assmnt->assmnt_type);
        if ($class_assmnt->assmnt_type == 'DEFAULT') {
            $def_assessment->DefAssLoc = ($def_assessment->assmnt_venue == 'OTH') ? 'Others (' . $def_assessment->assmnt_venue_oth . ')' : $CI->coursemodel->get_metadata_on_parameter_id($def_assessment->assmnt_venue);
            $def_assessment->DefAssId = $CI->classmodel->get_trainer_names($def_assessment->assessor_id);
            $cdef_assmnt = $def_assessment;
        } else {
            foreach ($def_assmnt as $k => $r) {
                $def_assmnt[$k]->DefAssLoc = ($r->assmnt_venue == 'OTH') ? 'Others (' . $r->assmnt_venue_oth . ')' : $CI->coursemodel->get_metadata_on_parameter_id($r->assmnt_venue);
                $def_assmnt[$k]->DefAssId = $CI->classmodel->get_trainer_names($r->assessor_id);
            }
            $assmnt = array();
            foreach ($def_assessment as $r) {
                $assmnt[$r->assmnt_id]['DefAssLoc'] = ($r->assmnt_venue == 'OTH') ? 'Others (' . $r->assmnt_venue_oth . ')' : $CI->coursemodel->get_metadata_on_parameter_id($r->assmnt_venue);
                $assmnt[$r->assmnt_id]['DefAssId'] = $CI->classmodel->get_trainer_names($r->assessor_id);
                $assmnt[$r->assmnt_id]['assessor_id'] = $r->assessor_id;
                $assmnt[$r->assmnt_id]['assmnt_id'] = $r->assmnt_id;
                $assmnt[$r->assmnt_id]['assmnt_date'] = $r->assmnt_date;
                $assmnt[$r->assmnt_id]['assmnt_venue'] = $r->assmnt_venue;
                $assmnt[$r->assmnt_id]['assmnt_venue_oth'] = $r->assmnt_venue_oth;
                $assmnt[$r->assmnt_id]['assmnt_date'] = $r->assmnt_date;
                $assmnt[$r->assmnt_id]['trainee'][] = $r->first_name . ' ' . $r->last_name;
                $assmnt[$r->assmnt_id]['trainee_id'][] = $r->user_id;
                $assmnt[$r->assmnt_id]['assmnt_start_time'] = $r->assmnt_start_time;
                $assmnt[$r->assmnt_id]['assmnt_end_time'] = $r->assmnt_end_time;
            }
            $cdef_assmnt = $assmnt;
        }
        $totalbooked = $CI->class->get_class_booked($row->course_id, $row->class_id, $row->tenant_id);
        $available = $row->total_seats - $totalbooked;
        $available = ($available < 0) ? 0 : $available;
        $sessions = ($row->class_session_day == 1) ? 'One Session' : 'Two Sessions';
        $sheet->setCellValue('A' . $rn, $rnnn - 3);
        $sheet->setCellValue('B' . $rn, $course->course_id);
        $sheet->setCellValue('C' . $rn, $course->crse_name);
        $sheet->setCellValue('D' . $rn, rtrim($CI->course->get_managers($course->crse_manager), ', '));
        $sheet->setCellValueExplicit('E' . $rn, $course->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('E' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('F' . $rn, $course->competency_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('F' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); 
        $sheet->setCellValue('G' . $rn, $CI->course->get_metadata_on_parameter_id($course->certi_level));
        $sheet->setCellValue('H' . $rn, $row->class_id);
        $sheet->setCellValue('I' . $rn, $row->class_name);
        $sheet->setCellValue('J' . $rn, date('d/m/Y h:i A', strtotime($row->class_start_datetime)));
        $sheet->setCellValue('K' . $rn, date('d/m/Y h:i A', strtotime($row->class_end_datetime)));
        $sheet->setCellValue('L' . $rn, ($row->total_classroom_duration + $row->total_lab_duration));
        $sheet->setCellValue('M' . $rn, $row->min_reqd_students);
        $sheet->setCellValue('N' . $rn, $row->total_seats);
        $sheet->setCellValue('O' . $rn, $totalbooked);
        $sheet->setCellValue('P' . $rn, $available);
        $sheet->setCellValue('Q' . $rn, $CI->class->get_trainer_names($row->classroom_trainer));
        $sheet->setCellValue('R' . $rn, $CI->class->get_trainer_names($row->lab_trainer));
        $sheet->setCellValue('S' . $rn, $CI->class->get_trainer_names($row->assessor));
        $sheet->setCellValue('T' . $rn, $CI->class->get_course_manager_names($row->training_aide));
        $sheet->setCellValue('U' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row->class_language), ', '));
        $sheet->setCellValue('V' . $rn, $classroom_location); //$CI->course->get_metadata_on_parameter_id($row->classroom_location)
        $sheet->setCellValue('W' . $rn, $lab_location); //modified on 1/12/2014
        $sheet->setCellValue('X' . $rn, '$' . number_format($row->class_fees, 2, '.', '') . ' SGD');
        $sheet->setCellValue('Y' . $rn, number_format($row->class_discount, 2, '.', '') . '%');
        $sheet->setCellValue('Z' . $rn, $sessions);
        $sheet->setCellValue('AA' . $rn, $CI->course->get_metadata_on_parameter_id($row->class_pymnt_enrol));
        $sheet->setCellValue('AB' . $rn, $sales_exec);
        $sheet->setCellValue('AC' . $rn, ($row->certi_coll_date == NULL || $row->certi_coll_date == '0000-00-00') ? '' : date('d/m/Y', strtotime($row->certi_coll_date)));
        $rnn = $rn;
        if ($class_assmnt->assmnt_type == 'DEFAULT') {
            $assmnt_date = ($cdef_assmnt->assmnt_date == NULL || $cdef_assmnt->assmnt_date == '0000-00-00') ? '' : date('d/m/Y', strtotime($cdef_assmnt->assmnt_date));
            if (!empty($assmnt_date)) {
                $assmnt_date .= '(' . date('h:i A', strtotime($cdef_assmnt->assmnt_start_time)) . ' - ' . date('h:i A', strtotime($cdef_assmnt->assmnt_end_time)) . ')';
            }
            $sheet->setCellValue('AD' . $rn, $assmnt_date);
            $sheet->setCellValue('AE' . $rn, $cdef_assmnt->DefAssId);
            $sheet->setCellValue('AF' . $rn, 'ALL');
            $sheet->setCellValue('AG' . $rn, $cdef_assmnt->DefAssLoc);
        } else {
            foreach ($cdef_assmnt as $assmnt) {
                $assmnt_date = ($assmnt['assmnt_date'] == NULL || $assmnt['assmnt_date'] == '0000-00-00') ? '' : date('d/m/Y', strtotime($assmnt['assmnt_date']));
                if (!empty($assmnt_date)) {
                    $assmnt_date .= '(' . date('h:i A', strtotime($assmnt['assmnt_start_time'])) . ' - ' . date('h:i A', strtotime($assmnt['assmnt_end_time'])) . ')';
                }
                $sheet->setCellValue('AD' . $rnn, $assmnt_date);
                $sheet->setCellValue('AE' . $rnn, $assmnt['DefAssId']);
                $sheet->setCellValue('AF' . $rnn, implode(',', $assmnt['trainee']));
                $sheet->setCellValue('AG' . $rnn, $assmnt['DefAssLoc']);
                $rnn++;
            }
        }
        $sheet->setCellValue('AH' . $rn, $CI->class->get_class_status($row->class_id, $CI->input->get('class_status')));
        if ($rnn == $rn) {
            $rn++;
        } else {
            $rn = $rnn;
        }
        $rnnn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Class_list.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to export course class all fields
 */
function export_course_class_full($result) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Course List');
    $sheet = $CI->excel->getActiveSheet();
    $course_id = $CI->input->get('course_id');
    $course = $CI->course->get_course_detailse($course_id);
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'D') as $columnID) {
        $var = 'A';
        $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:AD1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'List of all Courses[Complete Detail] being offered by the training institute as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'Course Code');
    $sheet->setCellValue('C2', 'Course Name');
    $sheet->setCellValue('D2', 'Course Manager');
    $sheet->setCellValue('E2', 'Course Type');
    $sheet->setCellValue('F2', 'Certification Code/ Level');
    $sheet->setCellValue('G2', 'Course Duration');
    $sheet->setCellValue('H2', 'Language');
    $sheet->setCellValue('I2', 'Class Type');
    $sheet->setCellValue('J2', 'Course Ref No.');
    $sheet->setCellValue('K2', 'Course Competency Code');
    $sheet->setCellValue('L2', 'Course Material Available');
    $sheet->setCellValue('M2', 'Course Validity');
    $sheet->setCellValue('N2', 'Course Description');
    $sheet->setCellValue('O2', 'Course Pre-requisite');
    $sheet->setCellValue('P2', 'Status');
    $sheet->getStyle('Q2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('Q2', 'Sales Executive Commission Details');

    $sheet->mergeCells('R2:AD2');
    $sheet->getStyle('R2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('R2', 'Classroom Details');


    $sheet->getStyle('A2:AD2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:AF2')->getFont()->setBold(true);
    $sheet->mergeCells('A3:P3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A3', '');

    $sheet->setCellValue('Q3', 'Exec Name (Rate)');


    $sheet->setCellValue('R3', 'Class Code');
    $sheet->setCellValue('S3', 'Class Name');
    $sheet->setCellValue('T3', 'Start Date & Time');
    $sheet->setCellValue('U3', 'End Date & Time');
    $sheet->setCellValue('V3', 'Total Seats');
    $sheet->setCellValue('W3', 'Booked Seats');
    $sheet->setCellValue('X3', 'Available Seats');
    $sheet->setCellValue('Y3', 'Classroom Trainer');
    $sheet->setCellValue('Z3', 'Training Aide');
    $sheet->setCellValue('AA3', 'Lab Trainer');
    $sheet->setCellValue('AB3', 'Assessor');
    $sheet->setCellValue('AC3', 'Language');
    $sheet->setCellValue('AD3', 'Status');


    $sheet->getStyle('A3:AD3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCFF')
                )
            )
    );
    $sheet->getStyle('A3:AD3')->getFont()->setBold(true);
    $rn = 4;

    foreach ($result as $row) {
        $def_assessment = $CI->class->get_def_assessment($row->tenant_id, $row->class_id);
        $sessions = ($row->class_session_day == 1) ? 'One Session' : 'Two Sessions';
        $sheet->setCellValue('A' . $rn, $rn - 3);
        $sheet->setCellValue('B' . $rn, $course->course_id);
        $sheet->setCellValue('C' . $rn, $course->crse_name);
        $sheet->setCellValue('D' . $rn, $CI->course->get_managers($course->crse_manager));
        $sheet->setCellValue('E' . $rn, $CI->course->get_metadata_on_parameter_id($course->crse_type));
        $sheet->setCellValue('F' . $rn, $CI->course->get_metadata_on_parameter_id($course->certi_level));
        $sheet->setCellValue('G' . $rn, $course->crse_duration);
        $sheet->setCellValue('H' . $rn, $CI->course->get_metadata_on_parameter_id($course->language));
        $sheet->setCellValue('I' . $rn, $CI->course->get_metadata_on_parameter_id($course->class_type));
        $sheet->setCellValueExplicit('J' . $rn, $course->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('J' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('K' . $rn, $course->competency_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('K' .$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); 

        $sheet->setCellValue('L' . $rn, empty($course->crse_content_path) ? 'No' : 'Yes' );
        $sheet->setCellValue('M' . $rn, ($course->crse_cert_validity) ? 'Yes(' . $course->crse_cert_validity . ')' : 'No');
        $sheet->setCellValue('N' . $rn, $course->crse_duration);
        $sheet->setCellValue('O' . $rn, $CI->course->get_pre_requisite($course->pre_requisite));
        $sheet->setCellValue('P' . $rn, $CI->course->get_metadata_on_parameter_id($course->crse_status));

        $sales_exec_array = $CI->class->get_class_salesexec($row->tenant_id, $row->course_id, $row->sales_executive);
        $name = '';
        foreach ($sales_exec_array as $r) {
            $name .=$r['first_name'] . ' ' . $r['last_name'] . ' (' . number_format($r['commission_rate'], 2, '.', '') . '%), ';
        }
        $name = rtrim($name, ', ');
        $sheet->setCellValue('Q' . $rn, $name);

        $sheet->setCellValue('R' . $rn, $row->class_id);
        $sheet->setCellValue('S' . $rn, $row->class_name);
        $sheet->setCellValue('T' . $rn, date('d/m/Y h:i A', strtotime($row->class_start_datetime)));
        $sheet->setCellValue('U' . $rn, date('d/m/Y h:i A', strtotime($row->class_end_datetime)));
        $sheet->setCellValue('V' . $rn, $row->total_seats);
        $booked = $CI->class->get_class_booked($row->course_id, $row->class_id, $row->tenant_id);
        $sheet->setCellValue('W' . $rn, $booked);
        $available = $row->total_seats - $booked;
        $sheet->setCellValue('X' . $rn, ($available < 0) ? 0 : $available);
        $sheet->setCellValue('Y' . $rn, $CI->class->get_trainer_names($row->classroom_trainer));
        $sheet->setCellValue('Z' . $rn, $CI->class->get_course_manager_names($row->training_aide));
        $sheet->setCellValue('AA' . $rn, $CI->class->get_trainer_names($row->lab_trainer));
        $sheet->setCellValue('AB' . $rn, $CI->class->get_trainer_names($row->assessor));
        $sheet->setCellValue('AC' . $rn, $CI->course->get_metadata_on_parameter_id($row->class_language));
        $sheet->setCellValue('AD' . $rn, $CI->class->get_class_status($row->class_id, $CI->input->get('class_status')));

        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Class_list.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to export all booked seats for a class
 */
function export_booked_seats($result, $class, $course, $totalbooked, $tenant_id) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->model('company_model', 'company');

    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Course List');

    $sheet = $CI->excel->getActiveSheet();
    $sheet->setCellValue('A1', '');
    $sheet->mergeCells('B1:H1');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('B1', 'Booking Details');
    $sheet->setCellValue('H1', '');

    $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', '');
    $sheet->mergeCells('B2:H2');
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('B2', 'Course: ' . $course->crse_name . 'Â Â  Class: ' . $class['class_name'] . ' Â Â Start Date: ' . date('M d Y', strtotime($class['class_start_datetime'])) . ' Â Â End Date: ' . date('M d Y', strtotime($class['class_end_datetime'])) . 'Â Â Total Seats:' . $class['total_seats'] . ' Â Â Total Booked: ' . $totalbooked);
    $sheet->setCellValue('H2', '');
    $sheet->getStyle('A2:H2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                )
            )
    );

    $sheet->setCellValue('A3', 'Sl#');
    $sheet->setCellValue('B3', 'Nationality');
    $sheet->setCellValue('C3', 'NRIC/FIN No.');
    $sheet->setCellValue('D3', 'Trainee Name');
    $sheet->setCellValue('E3', 'Enrollment Mode');
    $sheet->setCellValue('F3', 'Enrollment Date');
    $sheet->setCellValue('G3', 'Contact Details');
    $sheet->setCellValue('H3', 'Fee Paid');

    $sheet->getStyle('A3:H3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCFF')
                )
            )
    );
    $sheet->getStyle('A3:H3')->getFont()->setBold(true);

    $rn = 4;
    foreach ($result as $row) {
        if ($row->enrolment_mode == 'COMPSPON') {
            $company = $CI->company->get_company_details($tenant_id, $row->company_id);
            $enrol_mode = 'Company (' . $company[0]->company_name . ')';
        } else {
            $enrol_mode = 'Individual';
        }

        $contact_number = !empty($row->contact_number) ? 'Contact Number: ' . $row->contact_number . ', ' : '';
        $email = !empty($row->registered_email_id) ? 'Email Id: ' . $row->registered_email_id . ', ' : '';
        $address = '';
        $pac = '';
        if (!empty($row->personal_address_country)) {
            $pac = get_catname_by_parm($row->personal_address_country);
        }
        $pas = '';
        if (!empty($row->personal_address_state)) {
            $pas = get_catname_by_parm($row->personal_address_state);
        }
        if (!empty($row->personal_address_bldg)) {
            $address .=$row->personal_address_bldg . ', ';
        }
        if (!empty($row->personal_address_city)) {
            $address .=$row->personal_address_city . ', ';
        }
        if (!empty($pas)) {
            $address .=$pas . ', ';
        }
        if (!empty($pac)) {
            $address .=$pac . ', ';
        }
        if (!empty($row->personal_address_zip)) {
            $address .='Zip: ' . $row->personal_address_zip;
        }

        if (!empty($address)) {
            $address = 'Address: ' . $address;
        }
        $contact = rtrim($contact_number . $email . $address, ', ');

        $sheet->setCellValue('A' . $rn, $rn - 3);
        $sheet->setCellValue('B' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row->country_of_residence), ', '));
        $sheet->setCellValue('C' . $rn, mask_format($row->tax_code));
        $sheet->setCellValue('D' . $rn, $row->first_name . ' ' . $row->last_name);
        $sheet->setCellValue('E' . $rn, $enrol_mode);
        $sheet->setCellValue('F' . $rn, date('M d Y', strtotime($row->enrolled_on)));

        $sheet->setCellValue('G' . $rn, $contact);
        $sheet->setCellValue('H' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row->payment_status), ', '));

        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Booked_Seats.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * for class trainee page field
 */
function export_classtrainee_page($result, $tenant_id) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->model('company_model', 'company');

    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Class Trainee List');

    $sheet = $CI->excel->getActiveSheet();

    $sheet->setCellValue('A1', '');
    $sheet->mergeCells('B1:L1');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('B1', 'List of all Class-Trainee Enrollment Details as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:L1')->getFont()->setBold(true);
    foreach (range('A', 'L') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->setCellValue('A2', 'Sl#');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Trainee Name');
    $sheet->setCellValue('D2', 'Course / Class Details');
    $sheet->setCellValue('E2', 'Class Duration');
    $sheet->setCellValue('F2', 'Company Name');
    $sheet->setCellValue('G2', 'Certi. Coll.');
    $sheet->setCellValue('H2', 'Class Status');
    $sheet->setCellValue('I2', 'Payment Status');
    //Added by abdulla
    $sheet->setCellValue('J2', 'TG Number');
    $sheet->setCellValue('K2', 'Enrolment Reference Number');
    $sheet->setCellValue('L2', 'Enrolment Status');

    $sheet->getStyle('A2:L2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCFF')
                )
    ));
    $sheet->getStyle('A2:L2')->getFont()->setBold(true);
    $rn = 4;

    foreach ($result as $row) {
        $enr_mod = 'Individual';
        if ($row['enrolment_mode'] == 'COMPSPON') {
            if($row['company_id'][0] == 'T') {
                $tenant_details = fetch_tenant_details($row['company_id']);                
                $enr_mod = 'Company (' . $tenant_details->tenant_name . ')';
            } else {
                $company = $CI->company->get_company_details($tenant_id, $row['company_id']);
                $enr_mod = 'Company (' . $company[0]->company_name . ')';
            }
        }
        $sheet->setCellValue('A' . $rn, $rn - 3);
        if((TENANT_ID == 'T02' && $CI->session->userdata('userDetails')->user_id == '140490') || (TENANT_ID == 'T02' && $CI->session->userdata('userDetails')->user_id == '94679') || (TENANT_ID == 'T12' && $CI->session->userdata('userDetails')->user_id == '173804') || (TENANT_ID == 'T12' && $CI->session->userdata('userDetails')->user_id == '105310')){
            $sheet->setCellValue('B' . $rn, $row['tax_code']);
        }else{
            $sheet->setCellValue('B' . $rn, mask_format($row['tax_code']));
        }
        
        $sheet->setCellValue('C' . $rn, $row['first_name'] . ' ' . $row['last_name']);
        $sheet->setCellValue('D' . $rn, $row['crse_name'] . ' - ' . $row['class_name']);
        $sheet->setCellValue('E' . $rn, date('d/m/Y', strtotime($row['class_start_datetime'])) . ' - ' . date('d/m/Y', strtotime($row['class_end_datetime'])));
        $sheet->setCellValue('F' . $rn, $enr_mod);
        $sheet->setCellValue('G' . $rn, !empty($row['certificate_coll_on']) ? date('d/m/Y',  strtotime($row['certificate_coll_on'])):'');
        $result_text = !empty($row['feedback_answer']) ? ' (Result: ' . $row['feedback_answer'].')' : '';
        $sheet->setCellValue('H' . $rn, $CI->class->get_class_status($row['class_id'], '') . $result_text);
        $sheet->setCellValue('I' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['payment_status']), ', '));
        //Added by abdulla
        $sheet->setCellValue('J' . $rn, $row['tg_number']);
        $sheet->setCellValue('K' . $rn, $row['eid_number']);
        $sheet->setCellValue('L' . $rn, $row['tpg_enrolment_status']);
        
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="classtrainee_list.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * for class trainee full
 */
function export_classtrainee_full($result, $tenant_id) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->model('company_model', 'company');
    $CI->load->model('class_trainee_model', 'classtraineemodel');

    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Class Trainee List');

    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Y') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $sheet->setCellValue('A1', '');
    $sheet->mergeCells('B1:Y1');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment:: HORIZONTAL_LEFT);
    $sheet->setCellValue('B1', 'List of all Class-Trainee Enrollment Details as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:Y1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', '');
    $sheet->setCellValue('B2', 'Course Code');
    $sheet->setCellValue('C2', 'Course Name');
    $sheet->setCellValue('D2', 'Course Manager');
    $sheet->setCellValue('E2', 'Class Code');
    $sheet->setCellValue('F2', 'Class Name');
    $sheet->setCellValue('G2', 'Class Duration');
    $sheet->setCellValue('H2', 'Unit Fees');
    $sheet->setCellValue('I2', 'Class Discount');
    $sheet->setCellValue('J2', 'Minimum Seats');
    $sheet->setCellValue('K2', 'Total Seats');
    $sheet->setCellValue('L2', 'Class Language');
    $sheet->setCellValue('M2', 'Class Type');
    $sheet->setCellValue('N2', 'Class Venue');
    $sheet->setCellValue('O2', 'Certi. Coll.');
    $sheet->setCellValue('P2', 'Class Status');
    $sheet->mergeCells('Q2:Y2');
    $sheet->getStyle('Q2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('Y2', 'Trainee Details');

    $sheet->getStyle('A2:Y2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC'))
            )
    );
    $sheet->getStyle('A2:Y2')->getFont()->setBold(true);
    $sheet->setCellValue('A3', 'Sl#');
    $sheet->mergeCells('B3:P3');
    $sheet->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment ::HORIZONTAL_CENTER);
    $sheet->setCellValue('Q3', 'NRIC/FIN No.');
    $sheet->setCellValue('R3', 'Trainee Name');
    $sheet->setCellValue('S3', 'Company Name');
    $sheet->setCellValue('T3', 'Enrolled On');
    $sheet->setCellValue('U3', 'Payment Status');
    $sheet->setCellValue('V3', 'Subsidy Amount');
    $sheet->setCellValue('W3', 'GST Amount');
    $sheet->setCellValue('X3', 'Discount Amount');
    $sheet->setCellValue('Y3', 'Net Amount Paid');

    $sheet->getStyle('A3:Y3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCFF'))
            )
    );
    $sheet->getStyle('A3:Y3')->getFont()->setBold(true);
    $rn = 4;
    foreach ($result as $row) {
        $payid = $CI->classtraineemodel->get_payid_for_class_user($row['class_id'], $row['user_id']);
        $result = $CI->classtraineemodel->get_enroll_invoice($payid);
        $get_subsidy_data = $CI->classtraineemodel->get_enrol_payment_due($payid, $row['user_id']);
        $discount = ($get_subsidy_data->class_fees * $get_subsidy_data->discount_rate) / 100;
        $paid_details = $CI->classtraineemodel->get_invoice_paid_details($result->invoice_id, $row['user_id']);
        $paid_rcd_till_date = 0;
        if (!empty($paid_details)) {
            foreach ($paid_details as $r) {
                $paid_rcd_till_date = $r->amount_recd + $paid_rcd_till_date;
            }
        }
        $result->paid_rcd_till_date = number_format($paid_rcd_till_date, 2, '.', '');

        $enr_mod = 'Individual';
        if ($row['enrolment_mode'] == 'COMPSPON') {
            if($row['company_id'][0] == 'T') {
                $tenant_details = fetch_tenant_details($row['company_id']);                
                $enr_mod = 'Company (' . $tenant_details->tenant_name . ')';
            } else {
                $company = $CI->company->get_company_details($tenant_id, $row['company_id']);
                $enr_mod = 'Company (' . $company[0]->company_name . ')';
            }
        }
        if ($row ['classroom_location'] == 'OTH') {
            $class_loc = 'Others (' . $row ['classroom_venue_oth'] . ')';
        } else {
            $class_loc = $CI->course->get_metadata_on_parameter_id($row ['classroom_location']);
        }

        $sheet->setCellValue('A' . $rn, $rn - 3);
        $sheet->setCellValue('B' . $rn, $row ['course_id']);
        $sheet->setCellValue('C' . $rn, $row['crse_name']);
        $sheet->setCellValue('D' . $rn, $CI->course->get_managers($row ['crse_manager']));
        $sheet->setCellValue('E' . $rn, $row['class_id']);
        $sheet->setCellValue('F' . $rn, $row['class_name']);
        $sheet->setCellValue('G' . $rn, date('M d Y', strtotime($row['class_start_datetime'])) . ' - ' . date('M d Y', strtotime($row['class_end_datetime'])));
        $sheet->setCellValue('H' . $rn, '$ ' . number_format($row['class_fees'], 2, '.', '') . ' SGD');
        $sheet->setCellValue('I' . $rn, number_format($row['class_discount'], 2, '.', '') . ' %');
        $sheet->setCellValue('J' . $rn, $row['min_reqd_students']);
        $sheet->setCellValue('K' . $rn, $row['total_seats']);
        $sheet->setCellValue('L' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['class_language']), ', '));
        $sheet->setCellValue('M' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['class_type']), ', '));
        $sheet->setCellValue('N' . $rn, rtrim($class_loc, ', '));
        $sheet->setCellValue('O' . $rn, !empty($row['certificate_coll_on']) ? date('d/m/Y',  strtotime($row['certificate_coll_on'])):'');
        $result_text = !empty($row['feedback_answer']) ? ' (Result: ' . $row['feedback_answer'].')' : '';
        $sheet->setCellValue('P' . $rn, $CI->class->get_class_status($row['class_id'], '').$result_text);
        if((TENANT_ID == 'T02' && $CI->session->userdata('userDetails')->user_id == '140490') || (TENANT_ID == 'T02' && $CI->session->userdata('userDetails')->user_id == '94679') || (TENANT_ID == 'T12' && $CI->session->userdata('userDetails')->user_id == '173804') || (TENANT_ID == 'T12' && $CI->session->userdata('userDetails')->user_id == '105310')){
            $sheet->setCellValue('Q' . $rn, $row['tax_code']);
        }else{
            $sheet->setCellValue('Q' . $rn, mask_format($row['tax_code']));
        }
        
        $sheet->setCellValue('R' . $rn, $row['first_name'] . ' ' . $row['last_name']);
        $sheet->setCellValue('S' . $rn, $enr_mod);
        $sheet->setCellValue('T' . $rn, empty($row['enrolled_on']) ? '' : date('d/m/Y', strtotime($row['enrolled_on'])));
        $sheet->setCellValue('U' . $rn, rtrim($CI->course->get_metadata_on_parameter_id($row['payment_status']), ', '));
        $sheet->setCellValue('V' . $rn, '$ ' . number_format($get_subsidy_data->subsidy_amount, 2, '.', '') . ' SGD');
        $sheet->setCellValue('W' . $rn, '$ ' . number_format($get_subsidy_data->gst_amount, 2, '.', '') . ' SGD');
        $sheet->setCellValue('X' . $rn, '$ ' . number_format($discount, 2, '.', '') . ' SGD');
        $sheet->setCellValue('Y' . $rn, '$ ' . number_format($result->paid_rcd_till_date, 2, '.', '') . ' SGD');
        $rn++;
    }
    ob_end_clean();

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="classtrainee_list.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to create XLS for Sales Assignment and Commission
 */
function export_sales_report($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Sales Commision report');
    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue('A1', 'Sales Commision report as on ' . date('M j Y, l')); // Remake!!!
    $sheet->setCellValue('A2', 'Course Name-Class Name');
    $sheet->setCellValue('B2', 'Sales Executive Name');
    $sheet->setCellValue('C2', 'Commission Paid (SGD)');
    $sheet->setCellValue('D2', 'Commission Due (SGD)');
    $sheet->setCellValue('E2', 'Due Period');
    $sheet->setCellValue('F2', 'Last Paid On');

    $sheet->getStyle('A2:F2')->applyFromArray(
            array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
    ));
    $sheet->getStyle('A1:F2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($query->result() as $row) {
        $sheet->setCellValue('A' . $rn, $row->name);
        $sheet->setCellValue('B' . $rn, $row->executive_name);
        $sheet->setCellValue('C' . $rn, $row->commision_paid);
        $sheet->setCellValue('D' . $rn, $row->commision_due);
        $sheet->setCellValue('E' . $rn, $row->due_period);
        $sheet->setCellValue('F' . $rn, date_format_singapore($row->last_payed));
        $rn++;
    }

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Sales_Commision_report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * function to create XLS for Payment due
 */
function export_payments_due_report($query) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();

    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Payments Due report');

    $sheet = $CI->excel->getActiveSheet();
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue('A1', 'Payments Due report as on ' . date('M j Y, l')); // Remake!!!
    $sheet->setCellValue('A2', 'Course Name');
    $sheet->setCellValue('B2', 'Class Name');
    $sheet->setCellValue('C2', 'Sales Executive Name');
    $sheet->setCellValue('D2', 'Account Type)');
    $sheet->setCellValue('E2', 'Name');
    $sheet->setCellValue('F2', 'Enrollment Date');
    $sheet->setCellValue('G2', 'Discount');
    $sheet->setCellValue('H2', 'Sabsidy');
    $sheet->setCellValue('I2', 'GST');
    $sheet->setCellValue('J2', 'Price');
    $sheet->setCellValue('K2', 'Net Amt.');

    $sheet->getStyle('A2:K2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill ::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A1:K2')->getFont()->setBold(true);
    $total = array(
        'DISC' => 0,
        'SABSIDY' => 0,
        'GST' => 0,
        'PRICE' => 0,
        'NET_AMT' => 0
    );

    $rn = 3;
    foreach ($query->result() as $row) {
        $sheet->setCellValue('A' . $rn, $row->crse_name);
        $sheet->setCellValue('B' . $rn, $row->class_name);
        $sheet->setCellValue('C' . $rn, $row->exec_name);
        $sheet->setCellValue('D' . $rn, mask_format($row->tax_code));
        $sheet->setCellValue('E' . $rn, $row->user_name);
        $sheet->setCellValue('F' . $rn, date_format_singapore($row->enrolled_on));
        $sheet->setCellValue('G' . $rn, $row->total_inv_discnt);
        $sheet->setCellValue('H' . $rn, $row->total_inv_subsdy);
        $sheet->setCellValue('I' . $rn, $row->total_gst);
        $sheet->setCellValue('J' . $rn, $row->total_unit_fees);
        $sheet->setCellValue('K' . $rn, $row->total_inv_amount);

        $total['DISC'] += $row->total_inv_discnt;
        $total['SABSIDY'] += $row->total_inv_subsdy;
        $total['GST'] += $row->total_gst;
        $total ['PRICE'] += $row->total_unit_fees;
        $total ['NET_AMT'] += $row->total_inv_amount;
        $rn++;
    }
    $sheet->setCellValue('F' . $rn, 'Total');
    $sheet->setCellValue('G' . $rn, '$' . $total['DISC']);
    $sheet->setCellValue('H' . $rn, '$' . $total['SABSIDY']);
    $sheet->setCellValue('I' . $rn, '$' . $total['GST']);
    $sheet->setCellValue('J' . $rn, '$' . $total['PRICE']);
    $sheet->setCellValue('K' .
            $rn, '$' . $total['NET_AMT']);
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Payments_Due_report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
function write_import_status($data, $uid) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $spreadsheet = new PHPExcel();
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:J1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment ::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainee Bulk Registration status as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Account Type');
    $sheet->setCellValue('D2', 'Company Name');
    $sheet->setCellValue('E2', 'User Name');
    $sheet->setCellValue('F2', 'Password');
    $sheet->setCellValue('G2', 'Name');
    $sheet->setCellValue('H2', 'Status');
    $sheet->setCellValue('I2', 'Failure Reason');

    $sheet->getStyle('A2:I2')->applyFromArray(
            array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:I2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
        $sheet->setCellValue('C' . $rn, ($row['CompanyCode']) ? 'Company' : 'Individual');
        $sheet->setCellValue('D' . $rn, ($row['CompanyName']) ? $row['CompanyName'] . '(' . $row['CompanyCode'] . ')' : $row ['CompanyCode'] );
        $sheet->setCellValue('E' . $rn, $row['username']);
        $sheet->setCellValue('F' . $rn, $row['password']);
        $sheet->setCellValue('G' . $rn, $row['firstname']);
        $sheet->setCellValue('H' . $rn, $row['rowstatus']);
        $sheet->setCellValue('I' . $rn, $row['failure_reason']);
        $rn++;
    }
    $writer = new PHPExcel_Writer_Excel2007($spreadsheet);
    $file_name = 'Trainee_' . $uid . '.xlsx';
    $filepath = 'tmp/trainee/import_status/' . $file_name;
    $writer->save($filepath);
    return $file_name;
}

/**
 * for downloading import status of trainee bulk registraton
 * @param type $data
 * @param type $uid
 * @return string
 */
function write_import_enroll_status($data, $company_id) {
    unset($data['invoice_id']);
    $CI = & get_instance();
    $CI->load->library('excel');
    $spreadsheet = new PHPExcel();
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('A', 'G') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment ::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainee Bulk Enrollment status as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Enroll Type');
    $sheet->setCellValue('D2', 'TG Number');
    $sheet->setCellValue('E2', 'Status');
    $sheet->setCellValue('F2', 'Failure Reason');

    $sheet->getStyle('A2:F2')->applyFromArray(
            array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:F2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($data as $key => $row) {
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
        $sheet->setCellValue('C' . $rn, $row['enrollment_type']);
        $sheet->setCellValue('D' . $rn, $row['tg_number']);
        $sheet->setCellValue('E' . $rn, ($row['status'] == 'FAILED') ? 'Fail.' : 'Success');
        $sheet->setCellValue('F' . $rn, $row['failure_reason']);
        $rn++;
    }
    $writer = new PHPExcel_Writer_Excel2007($spreadsheet);
    $file_name = 'bulk_enrollment_' . $company_id . '.xlsx';
    $filepath = 'tmp/trainee/import_status/' . $file_name;
    $writer->save($filepath);
    return $file_name;
}
function write_import_enroll_statussuccess($data, $company_id) {
    unset($data['invoice_id']);
    $CI = & get_instance();
    $CI->load->library('excel');
    $spreadsheet = new PHPExcel();
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('A', 'G') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment ::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainee Bulk Enrollment status as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Enroll Type');
    $sheet->setCellValue('D2', 'TG Number');
    $sheet->setCellValue('E2', 'Status');
    $sheet->setCellValue('F2', 'Failure Reason');

    $sheet->getStyle('A2:F2')->applyFromArray(
            array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:F2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($data as $key => $row) {
        if ($row['status'] != 'FAILED') {
            $sheet->setCellValue('A' . $rn, $rn - 2);
            $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
            $sheet->setCellValue('C' . $rn, $row['enrollment_type']);
            $sheet->setCellValue('D' . $rn, $row['tg_number']);
            $sheet->setCellValue('E' . $rn, ($row['status'] == 'FAILED') ? 'Fail.' : 'Success');
            $sheet->setCellValue('F' . $rn, $row['failure_reason']);
            $rn++;
        }
    }
    $writer = new PHPExcel_Writer_Excel2007($spreadsheet);
    $file_name = 'bulk_enrollment_' . $company_id . 'A.xlsx';
    $filepath = 'tmp/trainee/import_status/' . $file_name;
    $writer->save($filepath);
    return $file_name;
}
function write_import_enroll_statusfailure($data, $company_id) {
    unset($data['invoice_id']);
    $CI = & get_instance();
    $CI->load->library('excel');
    $spreadsheet = new PHPExcel();
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('A', 'G') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment ::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainee Bulk Enrollment status as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Enroll Type');
    $sheet->setCellValue('D2', 'TG Number');
    $sheet->setCellValue('E2', 'Status');
    $sheet->setCellValue('F2', 'Failure Reason');

    $sheet->getStyle('A2:F2')->applyFromArray(
            array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:F2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($data as $key => $row) {
        if ($row['status'] == 'FAILED') {
            $sheet->setCellValue('A' . $rn, $rn - 2);
            $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
            $sheet->setCellValue('C' . $rn, $row['enrollment_type']);
            $sheet->setCellValue('D' . $rn, $row['tg_number']);
            $sheet->setCellValue('E' . $rn, ($row['status'] == 'FAILED') ? 'Fail.' : 'Success');
            $sheet->setCellValue('F' . $rn, $row['failure_reason']);
            $rn++;
        }
    }
    //$rand = rand(1000, 9999);
    $writer = new PHPExcel_Writer_Excel2007($spreadsheet);
    $file_name = 'bulk_enrollment_' . $company_id . 'B.xlsx';
    $filepath = 'tmp/trainee/import_status/' . $file_name;
    $writer->save($filepath);
    return $file_name;
}
/**
 * This methods generates the SOA XLS report output
 * @param type $tabledata
 * @param type $metadata
 */
function generate_soa_report_xls_xp($tabledata, $metadata) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->load->model('course_model');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('SOA Report');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
$CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'C') as $columnID) {
        $var = 'A';
$CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }
    $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'BB', 'CC');
    $column_title = array(
        'ID Type', 'ID Number', 'Name (As in NRIC)', 'Gender', 'Nationality',
        'Date of Birth (DDMMYYYY)', 'Race', 'Trainee Contact No(Mobile)',
        'Trainee Contact No (Others)', 'Trainee Email Address', 'Company Name (Key in NA if not applicable)',
        'Designation', 'Medium of Assessment', 'Education Level', 'Salary Range',
        'Assessment Venue','Course Start Date (DDMMYYYY)', 'Course Reference Number (Refer to Course Listing in SkillsConnect)',
        'Competency Standard Code (Refer to Course Listing in SkillsConnect)',
        'Cert Code', 'Submission Type', 'Date Of Assessment (DD-MM-YYYY)', 'Result',
        'Trainer ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Assessor ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Printing of SOA/ Generating of e-Cert','TPGateway Course Run ID');
    for ($i = 0; $i < count($column_title); $i++) {
        $sheet->setCellValue($column_names[$i] . '1', $column_title[$i]);
    }
    $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
    $r = 2;
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    foreach ($tabledata as $row) 
    {

        $assment_det = $CI->reportsmodel->get_assessment_details($row->class_id, $row->user_id);
        $crse_manager = explode(',', $row->crse_manager);
        $manager = $CI->reportsmodel->get_user_taxcode($crse_manager);
        $manager_text = '';
        foreach ($manager as $man) {
            $manager_text .= $man->tax_code . ', ';
            //break;
        }
        $manager_text = rtrim($manager_text, ', ');
        $classroom_trainer = explode(',', $row->classroom_trainer);
        $trainer = $CI->reportsmodel->get_user_taxcode($classroom_trainer);
        $trainer_text = '';
        foreach ($trainer as $train) {
            $trainer_text .= $train->tax_code . '; ';
         
        }
        $trainer_text = rtrim($trainer_text, '; ');
        $classroom_assessor = explode(',', $assment_det->assessor_id);
        $assessor = $CI->reportsmodel->get_user_taxcode($classroom_assessor);
        $assessor_text = '';
        foreach ($assessor as $assess) {
            $assessor_text .= $assess->tax_code . '; ';
            //break;
        }
        //echo $assessor_text;exit;
        $assessor_text = rtrim($assessor_text, '; ');
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        
        if($row->nationality=="KP")
        {
            $nationality="KOREAN, NORTH";
        }
        else if($row->nationality=="KR")
        {
            $nationality="KOREAN, SOUTH";
        }
        else
        {
                $data = $ci->db->select('category_name as nationality')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->nationality)
                         ->get()->row(0);

                if($data->nationality=="MALAY")
                {
                    $nationality="MALAYSIAN";
                }
                else
                {
                   $nationality= $data->nationality;
                    if($nationality=="BURMESE")
                   {
                       $nationality="MYANMAR";
                   }
                }
               
        }
        if($row->race!="")
        {
            $data = $ci->db->select('category_name as race')
                ->from('metadata_values')
                ->where('parameter_id', $row->race)
                ->get()->row(0);
            $race= $data->race;
        }
        else
        {
            $race=$row->race;
        }
       //exit();
        $designation = '';
        if($row->account_type == 'INTUSR' && $row->designation != 'OTHERS') 
        {
            $strlength = strpos($row->designation, '_');
            $designation = empty($strlength) ? $row->designation : substr($row->designation, $strlength + 1);
            if($row->designation!="") {
                $data = $ci->db->select('category_name as designation')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->designation)
                    ->get()->row(0);
                $designation= $data->designation;
            }
            else {
                $designation=$row->designation;
            }
            
        } else if($row->account_type == 'TRAINE'){
            $strlength = strpos($row->occupation_code, '_');
            $designation = empty($strlength) ? $row->occupation_code : substr($row->occupation_code, $strlength + 1);
            if($row->occupation_code!="") {
                $data = $ci->db->select('category_name as occupation_code')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->occupation_code)
                    ->get()->row(0);
                $designation= $data->occupation_code;
            }
            else {
                $designation=$row->occupation_code;
            }
        }
        $strlength = strpos($row->class_language, '_');
        $class_language = empty($strlength) ? $row->class_language : substr($row->class_language, $strlength + 1);
        $data = $ci->db->select('category_name as class_language')
                ->from('metadata_values')
                ->where('parameter_id', $row->class_language)
                 ->get()->row(0);
        $class_language= $data->class_language;
        
        
        $strlength = strpos($row->highest_educ_level, '_');
        $highest_educ_level = empty($strlength) ? $row->highest_educ_level : substr($row->highest_educ_level, $strlength + 1);
        if($row->highest_educ_level!="") {
                $data = $ci->db->select('category_name as highest_educ_level')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->highest_educ_level)
                    ->get()->row(0);
                $highest_educ_level= $data->highest_educ_level;
            }
            else {
                $highest_educ_level=$row->highest_educ_level;
            }
        
        $strlength = strpos($row->salary_range, '_');
        $row->salary_range;
       
        $salary_range = empty($strlength) ? $row->salary_range : substr($row->salary_range, $strlength + 1);
        if($row->salary_range!="") 
        {
                $data = $ci->db->select('category_name as salary_range1')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->salary_range)
                    ->get()->row(0);
                $salary_range= $data->salary_range1;
               
                if($row->salary_range!="BL1000_01")
                {
                    if($row->salary_range!="3500_07")
                    {
                        $salary=explode("to",$salary_range);
                        if(str_replace(' ', '', $salary[0])!="UNEMPLOYED")
                        {
                            $sal=explode(".",$salary[0]);
                            $sal1=explode(".",$salary[1]);
                            $salary_range=  str_replace(' ', '', $sal[0])." - $".str_replace(' ', '', $sal1[0]);
                        }
                    }
                    else
                    {
                        $salary_range="$3,500 and Above";
                    }
                }
                else
                {
                    
                    $salary_range="Below $1,000";
                    //$salary_range= $data->salary_range1;
                }
        }
        else 
        {
                $salary_range=$row->salary_range;
        }
        $gender_arr = array('MALE' => 'MALE', 'FEMALE' => 'FEMALE');
        if($row->company_id[0] == 'T') {           
            $tenant_details = fetch_tenant_details($row->company_id);            
            $row->company_name = $tenant_details->tenant_name; 
            $row->comp_email = $tenant_details->tenant_email_id;            
        }
        if($row->training_score)
        {
            if($row->training_score=="C")
            {
                $score="Competent";
            }
            else if($row->training_score=="ABS")
            {
                $score="Absent";
            }
            else if($row->training_score=="NYC")
            {
                $score="Not Yet Competent";
            }
            else if($row->training_score=="EX")
            {
                $score="Exempted";
            }
            
            else if($row->training_score=="2NYC")
            {
                $score="Twice Not Competent";
            }
            else if($row->training_score=="ATR")
            {
                $score="Attrition";
            }
            else
            {
                $score=$row->training_score;
            }
        }
        else
        {
             $score=$row->training_score;
        }
        
        if($row->tpg_course_run_id == ''){
            $tpg_id =$row->class_name;
        }else{
            $tpg_id=$row->tpg_course_run_id;
        }
        $course_code =  rtrim($CI->course_model->get_metadata_on_parameter_id($row->certi_level), ', '); //sk2
        $sheet->setCellValue('A' . $r, $tax_code_type);
        $sheet->setCellValue('B' . $r, $row->tax_code);
        $sheet->setCellValue('C' . $r, $row->first_name);
        $sheet->setCellValue('D' . $r, $gender_arr[$row->gender]);
        $sheet->setCellValue('E' . $r, $nationality);
        $sheet->setCellValueExplicit('F' . $r, (!empty($row->dob)) ? date('dmY', strtotime($row->dob)) : '', PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('F' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('G' . $r, $race);
        $sheet->setCellValueExplicit('H' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('H' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('I' . $r, $row->alternate_contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('I' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('J' . $r, empty($row->registered_email_id) ? $row->comp_email : $row->registered_email_id);
        $sheet->setCellValue('K' . $r, empty($row->company_name) ? 'NA' : $row->company_name);
        $sheet->setCellValueExplicit('L' . $r, $designation, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('L' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('M' . $r, $class_language);
        $sheet->setCellValueExplicit('N' . $r, $highest_educ_level, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('N' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('O' . $r, $salary_range, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('O' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('P' . $r, ($assment_det->assmnt_venue == 'OTH') ? 'Others (' . $assment_det->assmnt_venue_oth . ')' : $metadata[$assment_det->assmnt_venue]);
        $sheet->setCellValueExplicit('Q' . $r, date('dmY', strtotime($row->class_start_datetime)), PHPExcel_Cell_DataType::TYPE_STRING);
      
        $sheet->getStyle('Q' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('R' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('R' .$r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('S' . $r, $row->competency_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('S' .$r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); 
//        $sheet->setCellValue('T' . $r, $row->certi_level);
         $sheet->setCellValue('T' . $r, $course_code);//sk3
        $sheet->setCellValue('U' . $r, 'NEW');
        $sheet->setCellValueExplicit('V' . $r, (!empty($assment_det->assmnt_date)) ? date('d-m-Y', strtotime($assment_det->assmnt_date)) : date('d-m-Y',strtotime($row->class_end_datetime)), PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('V' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('W' . $r, $score);
        $sheet->setCellValue('X' . $r, $trainer_text);
        $sheet->setCellValue('Y' . $r, $assessor_text);
        $sheet->setCellValue('Z' . $r, 'No');
        $sheet->setCellValue('AA' . $r, $tpg_id);
        $r++;
    }

    ob_end_clean();

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="SOA_Report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

///added by shubhranshu for xp SOA report
function generate_soa_report_xls($tabledata, $metadata) {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->load->model('course_model');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('SOA Report');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
$CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'C') as $columnID) {
        $var = 'A';
$CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }
    $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'BB', 'CC');
    $column_title = array(
        'ID Type', 'ID Number', 'Name (As in NRIC)', 'Gender', 'Nationality',
        'Date of Birth (DDMMYYYY)', 'Race', 'Trainee Contact No(Mobile)',
        'Trainee Contact No (Others)', 'Trainee Email Address', 'Company Name (Key in NA if not applicable)',
        'Designation', 'Medium of Assessment', 'Education Level', 'Salary Range',
        'Assessment Venue', 'Course Start Date (DDMMYYYY)', 'Course Reference Number (Refer to Course Listing in SkillsConnect)',
        'Competency Standard Code (Refer to Course Listing in SkillsConnect)',
        'Cert Code', 'Submission Type', 'Date Of Assessment (DDMMYYYY)', 'Result',
        'Trainer ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Assessor ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Printing of SOA/ Generating of e-Cert','Class Name');
    for ($i = 0; $i < count($column_title); $i++) {
        $sheet->setCellValue($column_names[$i] . '1', $column_title[$i]);
    }
    $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
    $r = 2;
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    foreach ($tabledata as $row) 
    {

        $assment_det = $CI->reportsmodel->get_assessment_details($row->class_id, $row->user_id);
        $crse_manager = explode(',', $row->crse_manager);
        $manager = $CI->reportsmodel->get_user_taxcode($crse_manager);
        $manager_text = '';
        foreach ($manager as $man) {
            $manager_text .= $man->tax_code . ', ';
            //break;
        }
        $manager_text = rtrim($manager_text, ', ');
        $classroom_trainer = explode(',', $row->classroom_trainer);
        $trainer = $CI->reportsmodel->get_user_taxcode($classroom_trainer);
        $trainer_text = '';
        foreach ($trainer as $train) {
            $trainer_text .= $train->tax_code . '; ';
         
        }
        $trainer_text = rtrim($trainer_text, '; ');
        $classroom_assessor = explode(',', $assment_det->assessor_id);
        $assessor = $CI->reportsmodel->get_user_taxcode($classroom_assessor);
        $assessor_text = '';
        foreach ($assessor as $assess) {
            $assessor_text .= $assess->tax_code . '; ';
            //break;
        }
        //echo $assessor_text;exit;
        $assessor_text = rtrim($assessor_text, '; ');
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        
        if($row->nationality=="KP")
        {
            $nationality="KOREAN, NORTH";
        }
        else if($row->nationality=="KR")
        {
            $nationality="KOREAN, SOUTH";
        }
        else
        {
                $data = $ci->db->select('category_name as nationality')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->nationality)
                         ->get()->row(0);

                if($data->nationality=="MALAY")
                {
                    $nationality="MALAYSIAN";
                }
                else
                {
                   $nationality= $data->nationality;
                    if($nationality=="BURMESE")
                   {
                       $nationality="MYANMAR";
                   }
                }
               
        }
        if($row->race!="")
        {
            $data = $ci->db->select('category_name as race')
                ->from('metadata_values')
                ->where('parameter_id', $row->race)
                ->get()->row(0);
            $race= $data->race;
        }
        else
        {
            $race=$row->race;
        }
       //exit();
        $designation = '';
        if($row->account_type == 'INTUSR' && $row->designation != 'OTHERS') 
        {
            $strlength = strpos($row->designation, '_');
            $designation = empty($strlength) ? $row->designation : substr($row->designation, $strlength + 1);
            if($row->designation!="") {
                $data = $ci->db->select('category_name as designation')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->designation)
                    ->get()->row(0);
                $designation= $data->designation;
            }
            else {
                $designation=$row->designation;
            }
            
        } else if($row->account_type == 'TRAINE'){
            $strlength = strpos($row->occupation_code, '_');
            $designation = empty($strlength) ? $row->occupation_code : substr($row->occupation_code, $strlength + 1);
            if($row->occupation_code!="") {
                $data = $ci->db->select('category_name as occupation_code')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->occupation_code)
                    ->get()->row(0);
                $designation= $data->occupation_code;
            }
            else {
                $designation=$row->occupation_code;
            }
        }
        $strlength = strpos($row->class_language, '_');
        $class_language = empty($strlength) ? $row->class_language : substr($row->class_language, $strlength + 1);
        $data = $ci->db->select('category_name as class_language')
                ->from('metadata_values')
                ->where('parameter_id', $row->class_language)
                 ->get()->row(0);
        $class_language= $data->class_language;
        
        
        $strlength = strpos($row->highest_educ_level, '_');
        $highest_educ_level = empty($strlength) ? $row->highest_educ_level : substr($row->highest_educ_level, $strlength + 1);
        if($row->highest_educ_level!="") {
                $data = $ci->db->select('category_name as highest_educ_level')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->highest_educ_level)
                    ->get()->row(0);
                $highest_educ_level= $data->highest_educ_level;
            }
            else {
                $highest_educ_level=$row->highest_educ_level;
            }
        
        $strlength = strpos($row->salary_range, '_');
        $row->salary_range;
       
        $salary_range = empty($strlength) ? $row->salary_range : substr($row->salary_range, $strlength + 1);
        if($row->salary_range!="") 
        {
                $data = $ci->db->select('category_name as salary_range1')
                    ->from('metadata_values')
                    ->where('parameter_id', $row->salary_range)
                    ->get()->row(0);
                $salary_range= $data->salary_range1;
               
                if($row->salary_range!="BL1000_01")
                {
                    if($row->salary_range!="3500_07")
                    {
                        $salary=explode("to",$salary_range);
                        if(str_replace(' ', '', $salary[0])!="UNEMPLOYED")
                        {
                            $sal=explode(".",$salary[0]);
                            $sal1=explode(".",$salary[1]);
                            $salary_range=  str_replace(' ', '', $sal[0])." - $".str_replace(' ', '', $sal1[0]);
                        }
                    }
                    else
                    {
                        $salary_range="$3,500 and Above";
                    }
                }
                else
                {
                    
                    $salary_range="Below $1,000";
                    //$salary_range= $data->salary_range1;
                }
        }
        else 
        {
                $salary_range=$row->salary_range;
        }
        $gender_arr = array('MALE' => 'MALE', 'FEMALE' => 'FEMALE');
        if($row->company_id[0] == 'T') {           
            $tenant_details = fetch_tenant_details($row->company_id);            
            $row->company_name = $tenant_details->tenant_name; 
            $row->comp_email = $tenant_details->tenant_email_id;            
        }
        if($row->training_score)
        {
            if($row->training_score=="C")
            {
                $score="Competent";
            }
            else if($row->training_score=="ABS")
            {
                $score="Absent";
            }
            else if($row->training_score=="NYC")
            {
                $score="Not Yet Competent";
            }
            else if($row->training_score=="EX")
            {
                $score="Exempted";
            }
            
            else if($row->training_score=="2NYC")
            {
                $score="Twice Not Competent";
            }
            else
            {
                $score=$row->training_score;
            }
        }
        else
        {
             $score=$row->training_score;
        }
        $course_code =  rtrim($CI->course_model->get_metadata_on_parameter_id($row->certi_level), ', '); //sk2
        $sheet->setCellValue('A' . $r, $tax_code_type);
        $sheet->setCellValue('B' . $r, $row->tax_code);
        $sheet->setCellValue('C' . $r, $row->first_name);
        $sheet->setCellValue('D' . $r, $gender_arr[$row->gender]);
        $sheet->setCellValue('E' . $r, $nationality);
        $sheet->setCellValueExplicit('F' . $r, (!empty($row->dob)) ? date('dmY', strtotime($row->dob)) : '', PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('F' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('G' . $r, $race);
        $sheet->setCellValueExplicit('H' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('H' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('I' . $r, $row->alternate_contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('I' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('J' . $r, empty($row->registered_email_id) ? $row->comp_email : $row->registered_email_id);
        $sheet->setCellValue('K' . $r, empty($row->company_name) ? 'NA' : $row->company_name);
        $sheet->setCellValueExplicit('L' . $r, $designation, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('L' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('M' . $r, $class_language);
        $sheet->setCellValueExplicit('N' . $r, $highest_educ_level, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('N' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('O' . $r, $salary_range, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('O' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('P' . $r, ($assment_det->assmnt_venue == 'OTH') ? 'Others (' . $assment_det->assmnt_venue_oth . ')' : $metadata[$assment_det->assmnt_venue]);
        $sheet->setCellValueExplicit('Q' . $r, date('dmY', strtotime($row->class_start_datetime)), PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('Q' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('R' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('R' .$r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('S' . $r, $row->competency_code, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('S' .$r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); 
//        $sheet->setCellValue('T' . $r, $row->certi_level);
         $sheet->setCellValue('T' . $r, $course_code);//sk3
        $sheet->setCellValue('U' . $r, 'NEW');
        $sheet->setCellValueExplicit('V' . $r, (!empty($assment_det->assmnt_date)) ? date('dmY', strtotime($assment_det->assmnt_date)) : date('dmY',strtotime($row->class_end_datetime)), PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->getStyle('V' . $r)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $sheet->setCellValue('W' . $r, $score);
        $sheet->setCellValue('X' . $r, $trainer_text);
        $sheet->setCellValue('Y' . $r, $assessor_text);
        $sheet->setCellValue('Z' . $r, 'No');
        $sheet->setCellValue('AA' . $r, $row->class_name);
        $r++;
    }

    ob_end_clean();

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="SOA_Report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/**
 * This methods generates the SOA XLS report output
 * @param type $tabledata
 * @param type $metadata
 */
function generate_traqom2_report_xls($tabledata, $metadata) {
    $total_data = count($tabledata);

    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'C') as $columnID) {
        $var = 'A';
        $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }
    
        
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'TRAQAM-2 REPORT As ON ' . date('M j Y, l'));
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    
    /* skm code st */
    $course_end_time_filename = date('His', strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename = date('Ymd', strtotime($tabledata[0]->class_end_datetime));

    $filename = $tabledata[0]->comp_reg_no . "_" . $course_end_date_filename . "_" . $course_end_time_filename . ".xls";
//    $sheet->setCellValueExplicit('A1', '');
//    $sheet->setCellValueExplicit('B1', 'TRAQAM-2 REPORT');
//    $sheet->setCellValueExplicit('C1', 'Total Trainees: '.$total_data);

    //$sheet->setCellValueExplicit('A2', '');
     $sheet->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueExplicit('D2', 'Total Trainees: '.$total_data);
    //$sheet->setCellValueExplicit('C2', $total_data);
    /* skm code end */
    $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3', 'H3', 'I3', 'J3', 'K3', 'L3', 'M3', 'N3', 'O3', 'P3', 'Q3', 'R3', 'S3', 'T3', 'U3','V3');
    $column_title = array('SL #',
        'Trainee Name', 'Trainee ID', 'ID Type', 'Email', 'Mobile Country Code', 'Mobile Area Code', 'Mobile', 'TP Alias', 'Course Title', 'Area of Training',
        'Course Reference Number', 'Course Run Reference Number',
        'Course Start Date', 'Course End Date', 'Postal Code', 'Floor', 'Unit', 'Room', 'Full Qualification', 'Trainer Name','Class Name'
    );
    for ($i = 0; $i < count($column_title); $i++) {
        $sheet->setCellValue($column_names[$i], $column_title[$i]);
    }

//            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
//                    array('fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('argb' => 'FFCCCCCC')
//                        )
//                    )
//            );
    $sheet->getStyle('A3:V3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );

//            echo count($column_title); echo"<br/>";
//            print_r($sheet);
//            exit();
//            
    //$sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
    $sheet->getStyle('A3:V3')->getFont()->setBold(true);
    $r = 4;
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    $duplicate_mobile_arry = array();
    foreach ($tabledata as $row) {
        //////////////////start of code added by shubhranshu for removing duplicate mobile no/////////////
        if(in_array($row->contact_number,$duplicate_mobile_arry)){
            $remove_duplicate_contact_number = '';
        }else{
            $duplicate_mobile_arry[] = $row->contact_number;
            $remove_duplicate_contact_number = $row->contact_number;
        }
        
        //////////////////end of code added by shubhranshu for removing duplicate mobile no/////////////
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci = & get_instance();
        $ci->load->database();
        $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                        ->get()->row(0);
        $tax_code_type = $data->code_type;
        //author: added by shubhranshu as per client requirement on 11/03/2020
        if(TENANT_ID == 'T02' || TENANT_ID == 'T12'){
            if($tax_code_type == 'NRIC')
            {
            ///author: added by shubhranshu as per client requirement on 11/03/2020
            //Singapore PR
                if($row->nationality == 'NS'){
                    $tax_code_type='Singapore Blue Identification Card';
                }else if($row->nationality == 'SG'){//Singapore Citizen
                    $tax_code_type='Singapore Pink Identification Card';
                }else{
                    $tax_code_type='Others';
                }

            }else if($tax_code_type == 'FIN'){
                //$tax_code_type='SO';
               $tax_code_type= 'FIN/Work Permit';
            }
            else{
                $tax_code_type='Others';
            }
        }else{
            if($tax_code_type == 'NRIC')
            {
                $tax_code_type='SP';

            }else if($tax_code_type == 'FIN'){
                $tax_code_type='SO';
            }
            else{
                 $tax_code_type='OT';
            }
        }
        
        /* skm code for new style email which is the combination of taxocde and class name intials st */
        $pattern = "/[_,-]/";
        $string = $row->class_name;
        $class_name = preg_split($pattern, $string);
        $trainee_classname = $class_name[0];
        $trainee_taxcode = substr($row->tax_code, -4);

        $CI->load->model('class_model', 'class_Model');
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);

        //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
        $trainee_email = $trainee_taxcode . $trainee_classname . '@yopmail.com';
        /* end */
        $course_start_time = date('His', strtotime($row->class_start_datetime));
        $course_start_date = date('Ymd', strtotime($row->class_start_datetime));
        $dob = str_replace('-', '', $row->dob);

        $course_end_time = date('His', strtotime($row->class_end_datetime));
        $course_end_date = date('Ymd', strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d', strtotime($row->enrolled_on));

        $sheet->setCellValueExplicit('A' . $r, $r - 3);
        $sheet->setCellValue('B' . $r, $row->first_name);
        $sheet->setCellValue('C' . $r, $row->tax_code);
        $sheet->setCellValue('D' . $r, $tax_code_type);
        $sheet->setCellValue('E' . $r, $trainee_email);
        $sheet->setCellValue('F' . $r, '');
        $sheet->setCellValue('G' . $r, '');
        $sheet->setCellValueExplicit('H' . $r, $remove_duplicate_contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('I' . $r, $row->tenant_name);
        $sheet->setCellValue('J' . $r, $row->crse_name);
        $sheet->setCellValue('K' . $r, '');
        $sheet->setCellValueExplicit('L' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValue('M' . $r, '');
        $sheet->setCellValueExplicit('N' . $r, $course_start_date);
        $sheet->setCellValueExplicit('O' . $r, $course_end_date);
        $sheet->setCellValue('P' . $r, '');
        $sheet->setCellValue('Q' . $r, '');
        $sheet->setCellValue('R' . $r, '');
        $sheet->setCellValue('S' . $r, '');
        $sheet->setCellValue('T' . $r, '');
        $sheet->setCellValue('U' . $r, $trainer_name);
        $sheet->setCellValue('V' . $r, $row->class_name);
        $r++;
    }
    ob_end_clean();
    $filename = $row->comp_reg_no . "_" . $course_end_date . "_" . $course_end_time . ".xls";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $filename);
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

function generate_traqom2_report_xls_xp($tabledata, $metadata) {
    $total_data = count($tabledata);

    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'C') as $columnID) {
        $var = 'A';
        $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }
    
        
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'TRAQAM-2 REPORT As ON ' . date('M j Y, l'));
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    
    /* skm code st */
    $course_end_time_filename = date('His', strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename = date('Ymd', strtotime($tabledata[0]->class_end_datetime));

    $filename = $tabledata[0]->comp_reg_no . "_" . $course_end_date_filename . "_" . $course_end_time_filename . ".xls";
//    $sheet->setCellValueExplicit('A1', '');
//    $sheet->setCellValueExplicit('B1', 'TRAQAM-2 REPORT');
//    $sheet->setCellValueExplicit('C1', 'Total Trainees: '.$total_data);

    //$sheet->setCellValueExplicit('A2', '');
     $sheet->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueExplicit('D2', 'Total Trainees: '.$total_data);
    //$sheet->setCellValueExplicit('C2', $total_data);
    /* skm code end */
    $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3', 'H3', 'I3', 'J3', 'K3', 'L3', 'M3', 'N3', 'O3', 'P3', 'Q3', 'R3', 'S3', 'T3', 'U3','V3');
    $column_title = array('SL #',
        'Trainee ID','Trainee Name', 'ID Type', 'Email', 'Mobile Country Code', 'Mobile Area Code', 'Mobile',  'Course Title', 'Area of Training',
        'Course Reference Number', 'Course Run Reference Number',
        'Course Start Date', 'Course End Date', 'Postal Code', 'Floor', 'Unit', 'Room', 'Full Qualification', 'TP Alias', 'Trainer Name', 'Class Name'
    );
    for ($i = 0; $i < count($column_title); $i++) {
        $sheet->setCellValue($column_names[$i], $column_title[$i]);
    }

//            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
//                    array('fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('argb' => 'FFCCCCCC')
//                        )
//                    )
//            );
    $sheet->getStyle('A3:V3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );

//            echo count($column_title); echo"<br/>";
//            print_r($sheet);
//            exit();
//            
    //$sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
    $sheet->getStyle('A3:V3')->getFont()->setBold(true);
    $r = 4;
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    $duplicate_mobile_arry = array();
    foreach ($tabledata as $row) {
        //////////////////start of code added by shubhranshu for removing duplicate mobile no/////////////
        if(in_array($row->contact_number,$duplicate_mobile_arry)){
            $remove_duplicate_contact_number = '';
        }else{
            $duplicate_mobile_arry[] = $row->contact_number;
            $remove_duplicate_contact_number = $row->contact_number;
        }
        
        if($row->tpg_course_run_id == ''){
            $tpg_id =$row->class_name;
        }else{
            $tpg_id=$row->tpg_course_run_id;
        }
        //////////////////end of code added by shubhranshu for removing duplicate mobile no/////////////
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci = & get_instance();
        $ci->load->database();
        $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                        ->get()->row(0);
        $tax_code_type = $data->code_type;
        //author: added by shubhranshu as per client requirement on 11/03/2020
        if(TENANT_ID == 'T02' || TENANT_ID == 'T12'){
            if($tax_code_type == 'NRIC')
            {
            ///author: added by shubhranshu as per client requirement on 11/03/2020
            //Singapore PR
                if($row->nationality == 'NS'){
                    $tax_code_type='Singapore Blue Identification Card';
                }else if($row->nationality == 'SG'){//Singapore Citizen
                    $tax_code_type='Singapore Pink Identification Card';
                }else{
                    $tax_code_type='Others';
                }

            }else if($tax_code_type == 'FIN'){
                //$tax_code_type='SO';
               $tax_code_type= 'FIN/Work Permit';
            }
            else{
                $tax_code_type='Others';
            }
        }else{
            if($tax_code_type == 'NRIC')
            {
                $tax_code_type='SP';

            }else if($tax_code_type == 'FIN'){
                $tax_code_type='SO';
            }
            else{
                 $tax_code_type='OT';
            }
        }
        
        /* skm code for new style email which is the combination of taxocde and class name intials st */
        $pattern = "/[_,-]/";
        $string = $row->class_name;
        $class_name = preg_split($pattern, $string);
        $trainee_classname = $class_name[0];
        $trainee_taxcode = substr($row->tax_code, -4);

        $CI->load->model('class_model', 'class_Model');
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);

        //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
        $trainee_email = $trainee_taxcode . $trainee_classname . '@yopmail.com';
        /* end */
        $course_start_time = date('His', strtotime($row->class_start_datetime));
        $course_start_date = date('Ymd', strtotime($row->class_start_datetime));
        $dob = str_replace('-', '', $row->dob);

        $course_end_time = date('His', strtotime($row->class_end_datetime));
        $course_end_date = date('Ymd', strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d', strtotime($row->enrolled_on));

        $sheet->setCellValueExplicit('A' . $r, $r - 3);
        $sheet->setCellValue('B' . $r, $row->tax_code);
        $sheet->setCellValue('C' . $r, $row->first_name);
        
        $sheet->setCellValue('D' . $r, $tax_code_type);
        $sheet->setCellValue('E' . $r, $row->registered_email_id);
        $sheet->setCellValue('F' . $r, '65');
        $sheet->setCellValue('G' . $r, '');
        $sheet->setCellValueExplicit('H' . $r, $remove_duplicate_contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
        
        $sheet->setCellValue('I' . $r, $row->crse_name);
        $sheet->setCellValue('J' . $r, '');
        $sheet->setCellValueExplicit('K' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
        $sheet->setCellValue('L' . $r, '');
        $sheet->setCellValueExplicit('M' . $r, $course_start_date);
        $sheet->setCellValueExplicit('N' . $r, $course_end_date);
        $sheet->setCellValue('O' . $r, '');
        $sheet->setCellValue('P' . $r, '');
        $sheet->setCellValue('Q' . $r, '');
        $sheet->setCellValue('R' . $r, '');
        $sheet->setCellValue('S' . $r, '');
        $sheet->setCellValueExplicit('T' . $r, $row->tenant_name);
        $sheet->setCellValue('U' . $r, $trainer_name);
        $sheet->setCellValue('V' . $r, $row->class_name);
        $r++;
    }
    ob_end_clean();
    $filename = $row->comp_reg_no . "_" . $course_end_date . "_" . $course_end_time . ".xls";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $filename);
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

function generate_traqom_report_xls_wablab($tabledata, $metadata) 
{
    $total_data = count($tabledata);

            $CI = & get_instance();
            $CI->load->library('excel');
            $CI->excel->setActiveSheetIndex(0);
            $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
            $sheet = $CI->excel->getActiveSheet();
            foreach (range('A', 'Z') as $columnID) 
            {
                    $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                                ->setAutoSize(true);
            }
            foreach (range('A', 'C') as $columnID) 
            {
                $var = 'A';
                $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                        ->setAutoSize(true);
            }
            /* skm code st*/
            $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
            $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
            
            $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".xls";
            $sheet->setCellValueExplicit('A1' , 'H');
            $sheet->setCellValueExplicit('B1' , $filename);
            $sheet->setCellValueExplicit('C1' , $total_data);
            
            $sheet->setCellValueExplicit('A2' , 'H');
            $sheet->setCellValueExplicit('B2' , 'Scenario');
            $sheet->setCellValueExplicit('C2' , 'Outcome');
            /* skm code end */
            $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3','H3','I3','J3','K3','L3');
            $column_title = array('H',
                'Course Reference Number',
                 'Course Start Date','Course End Date','TP UEN','NRIC/Passport', 'ID Type', 'Full Name',
                'Mobile',
                'Email',
                'Enrollment Date','Class Name'
                );
            for ($i = 0; $i < count($column_title); $i++) 
            {
                $sheet->setCellValue($column_names[$i] , $column_title[$i]);
            }
            
//            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
//                    array('fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('argb' => 'FFCCCCCC')
//                        )
//                    )
//            );
             $sheet->getStyle('A3:L3')->applyFromArray(
                array('fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFCCCCCC')
                                )
                    )
            );
            
//            echo count($column_title); echo"<br/>";
//            print_r($sheet);
//            exit();
//            
            //$sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
            $sheet->getStyle('A3:L3')->getFont()->setBold(true);
            $r = 4;
            $CI->load->model('reports_model', 'reportsmodel');
            $data_arr = array();
            foreach ($tabledata as $row) 
            {
                
                $strlength = strpos($row->tax_code_type, '_');
                $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
                $row->tax_code_type;
                //new update
                $ci =& get_instance(); 
                $ci->load->database(); 
                $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                         ->get()->row(0);
                $tax_code_type= $data->code_type;
                if($tax_code_type == 'NRIC')
                {
                    $tax_code_type='SP';
                }else if($tax_code_type == 'FIN'){
                    $tax_code_type='SO';
                }
                else{
                    $tax_code_type='OT';
                }
                /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                 $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
                $course_start_time=date('His',strtotime($row->class_start_datetime));
                $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
                
                $course_end_time=date('His',strtotime($row->class_end_datetime));
                $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
                $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
                
                $sheet->setCellValueExplicit('A' . $r, 'I');
                $sheet->setCellValueExplicit('B' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $r, $course_start_date);
                $sheet->setCellValueExplicit('D' . $r, $course_end_date);
                $sheet->setCellValue('E' . $r, $row->comp_reg_no);
                $sheet->setCellValue('F' . $r, $row->tax_code);
                $sheet->setCellValue('G' . $r, $tax_code_type);
                $sheet->setCellValue('H' . $r, $row->first_name);
                $sheet->setCellValueExplicit('I' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('J' . $r, $trainee_email);
                $sheet->setCellValue('K' . $r, $enrollment_date);
                $sheet->setCellValue('L' . $r, $row->class_name);
                $r++;
            }
            ob_end_clean();
            $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".xls";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
}
function generate_traqom_report_xls($tabledata, $metadata) 
{
    $total_data = count($tabledata);

            $CI = & get_instance();
            $CI->load->library('excel');
            $CI->excel->setActiveSheetIndex(0);
            $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
            $sheet = $CI->excel->getActiveSheet();
            foreach (range('A', 'Z') as $columnID) 
            {
                    $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                                ->setAutoSize(true);
            }
            foreach (range('A', 'C') as $columnID) 
            {
                $var = 'A';
                $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                        ->setAutoSize(true);
            }
            /* skm code st*/
            $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
            $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
            
            $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".xls";
            $sheet->setCellValueExplicit('A1' , 'H');
            $sheet->setCellValueExplicit('B1' , $filename);
            $sheet->setCellValueExplicit('C1' , $total_data);
            
            $sheet->setCellValueExplicit('A2' , 'H');
            $sheet->setCellValueExplicit('B2' , 'Scenario');
            $sheet->setCellValueExplicit('C2' , 'Outcome');
            /* skm code end */
            $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3','H3','I3','J3','K3','L3','M3','N3','O3','P3','Q3','R3','S3','T3','U3');
            $column_title = array('H',
                'Trainee Name','Trainee ID','ID Type','Date Of Birth','Email','Mobile','TP Alias','Course Title','Area of Training',
                'Course Reference Number','Course Run Reference Number',
                'Course Start Date','Course End Date','Postel Code','Floor','Unit','Room','Full Qualification','Trainer Name','Class Name'
               
                );
            for ($i = 0; $i < count($column_title); $i++) 
            {
                $sheet->setCellValue($column_names[$i] , $column_title[$i]);
            }
            
//            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
//                    array('fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('argb' => 'FFCCCCCC')
//                        )
//                    )
//            );
             $sheet->getStyle('A3:U3')->applyFromArray(
                array('fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFCCCCCC')
                                )
                    )
            );
            
//            echo count($column_title); echo"<br/>";
//            print_r($sheet);
//            exit();
//            
            //$sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
            $sheet->getStyle('A3:U3')->getFont()->setBold(true);
            $r = 4;
            $CI->load->model('reports_model', 'reportsmodel');
            $data_arr = array();
            foreach ($tabledata as $row) 
            {
                
                $strlength = strpos($row->tax_code_type, '_');
                $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
                $row->tax_code_type;
                //new update
                $ci =& get_instance(); 
                $ci->load->database(); 
                $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                         ->get()->row(0);
                $tax_code_type= $data->code_type;
                if($tax_code_type == 'NRIC')
                {
                    $tax_code_type='SP';
                }else if($tax_code_type == 'FIN'){
                    $tax_code_type='SO';
                }
                else{
                    $tax_code_type='OT';
                }
                /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                 $CI->load->model('class_model', 'class_Model');              
                 $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);
               
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                 $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
                $course_start_time=date('His',strtotime($row->class_start_datetime));
                $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
                $dob = str_replace('-','',$row->dob);
                
                $course_end_time=date('His',strtotime($row->class_end_datetime));
                $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
                $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
                
                $sheet->setCellValueExplicit('A' . $r, 'I');
                $sheet->setCellValue('B' . $r, $row->first_name);
                $sheet->setCellValue('C' . $r, $row->tax_code);
                $sheet->setCellValue('D' . $r, $tax_code_type);
                $sheet->setCellValue('E' . $r, $dob);
                $sheet->setCellValue('F' . $r, $trainee_email);
                $sheet->setCellValueExplicit('G' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('H' . $r, $row->tenant_name);
                $sheet->setCellValue('I' . $r, $row->crse_name);
                $sheet->setCellValue('J' . $r, '');
                $sheet->setCellValueExplicit('K' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('L' . $r, '');                
                $sheet->setCellValueExplicit('M' . $r, $course_start_date);
                $sheet->setCellValueExplicit('N' . $r, $course_end_date);                
                $sheet->setCellValue('O' . $r, '');
                $sheet->setCellValue('P' . $r, '');
                $sheet->setCellValue('Q' . $r, '');
                $sheet->setCellValue('R' . $r, '');
                $sheet->setCellValue('S' . $r, '');
                $sheet->setCellValue('T' . $r, $trainer_name);
                $sheet->setCellValue('U' . $r, $row->class_name);
                $r++;
            }
            ob_end_clean();
            $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".xls";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
}

///added by shubhranshu for traqam report xls only for XP course run id
function generate_traqom_report_xls_xp($tabledata, $metadata) 
{
    $total_data = count($tabledata);

            $CI = & get_instance();
            $CI->load->library('excel');
            $CI->excel->setActiveSheetIndex(0);
            $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
            $sheet = $CI->excel->getActiveSheet();
            foreach (range('A', 'Z') as $columnID) 
            {
                    $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                                ->setAutoSize(true);
            }
            foreach (range('A', 'C') as $columnID) 
            {
                $var = 'A';
                $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                        ->setAutoSize(true);
            }
            /* skm code st*/
            $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
            $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
            
            $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".xls";
            $sheet->setCellValueExplicit('A1' , 'H');
            $sheet->setCellValueExplicit('B1' , $filename);
            $sheet->setCellValueExplicit('C1' , $total_data);
            
            $sheet->setCellValueExplicit('A2' , 'H');
            $sheet->setCellValueExplicit('B2' , 'Scenario');
            $sheet->setCellValueExplicit('C2' , 'Outcome');
            /* skm code end */
            $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3','H3','I3','J3','K3','L3','M3','N3','O3','P3','Q3','R3','S3','T3','U3');
            $column_title = array('H',
                'Trainee ID','Trainee Name','ID Type','Date Of Birth','Email','Mobile','Course Title','Area of Training',
                'Course Reference Number','Course Run Reference Number',
                'Course Start Date','Course End Date','Postel Code','Floor','Unit','Room','Full Qualification','TP Alias','Trainer Name','Class Name'
               
                );
            for ($i = 0; $i < count($column_title); $i++) 
            {
                $sheet->setCellValue($column_names[$i] , $column_title[$i]);
            }
            
//            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
//                    array('fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('argb' => 'FFCCCCCC')
//                        )
//                    )
//            );
             $sheet->getStyle('A3:U3')->applyFromArray(
                array('fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFCCCCCC')
                                )
                    )
            );
            
//            echo count($column_title); echo"<br/>";
//            print_r($sheet);
//            exit();
//            
            //$sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
            $sheet->getStyle('A3:U3')->getFont()->setBold(true);
            $r = 4;
            $CI->load->model('reports_model', 'reportsmodel');
            $data_arr = array();
            foreach ($tabledata as $row) 
            {
                if($row->tpg_course_run_id == ''){
                    $tpg_id =$row->class_name;
                }else{
                    $tpg_id=$row->tpg_course_run_id;
                }
                $strlength = strpos($row->tax_code_type, '_');
                $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
                $row->tax_code_type;
                //new update
                $ci =& get_instance(); 
                $ci->load->database(); 
                $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                         ->get()->row(0);
                $tax_code_type= $data->code_type;
                if($tax_code_type == 'NRIC')
                {
                    $tax_code_type='SP';
                }else if($tax_code_type == 'FIN'){
                    $tax_code_type='SO';
                }
                else{
                    $tax_code_type='OT';
                }
                /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                 $CI->load->model('class_model', 'class_Model');              
                 $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);
               
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                 $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
                $course_start_time=date('His',strtotime($row->class_start_datetime));
                $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
                $dob = str_replace('-','',$row->dob);
                
                $course_end_time=date('His',strtotime($row->class_end_datetime));
                $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
                $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
                
                $sheet->setCellValueExplicit('A' . $r, 'I');
                $sheet->setCellValue('B' . $r, $row->tax_code);
                $sheet->setCellValue('C' . $r, $row->first_name);
                
                $sheet->setCellValue('D' . $r, $tax_code_type);
                $sheet->setCellValue('E' . $r, $dob);
                $sheet->setCellValue('F' . $r, $row->registered_email_id);
                $sheet->setCellValueExplicit('G' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
                
                $sheet->setCellValue('H' . $r, $row->crse_name);
                $sheet->setCellValue('I' . $r, '');
                $sheet->setCellValueExplicit('J' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('K' . $r, '');                
                $sheet->setCellValueExplicit('L' . $r, $course_start_date);
                $sheet->setCellValueExplicit('M' . $r, $course_end_date);                
                $sheet->setCellValue('N' . $r, '');
                $sheet->setCellValue('O' . $r, '');
                $sheet->setCellValue('P' . $r, '');
                $sheet->setCellValue('Q' . $r, '');
                $sheet->setCellValue('R' . $r, '');
                $sheet->setCellValueExplicit('S' . $r, $row->tenant_name);
                $sheet->setCellValue('T' . $r, $trainer_name);
                $sheet->setCellValue('U' . $r, $row->class_name);
                $r++;
            }
            ob_end_clean();
            $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".xls";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
}

function generate_traqom_report_xls_old($tabledata, $metadata) 
{
    $total_data = count($tabledata);

            $CI = & get_instance();
            $CI->load->library('excel');
            $CI->excel->setActiveSheetIndex(0);
            $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
            $sheet = $CI->excel->getActiveSheet();
            foreach (range('A', 'Z') as $columnID) 
            {
                    $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                                ->setAutoSize(true);
            }
            foreach (range('A', 'C') as $columnID) 
            {
                $var = 'A';
                $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                        ->setAutoSize(true);
            }
            /* skm code st*/
            $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
            $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
            
            $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".xls";
            $sheet->setCellValueExplicit('A1' , 'H');
            $sheet->setCellValueExplicit('B1' , $filename);
            $sheet->setCellValueExplicit('C1' , $total_data);
            
            $sheet->setCellValueExplicit('A2' , 'H');
            $sheet->setCellValueExplicit('B2' , 'Scenario');
            $sheet->setCellValueExplicit('C2' , 'Outcome');
            /* skm code end */
            $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3','H3','I3','J3','K3');
            $column_title = array('H',
                'Course Reference Number',
                 'Course Start Date','Course End Date','TP UEN','NRIC/Passport', 'ID Type', 'Full Name',
                'Mobile',
                'Email',
                'Enrollment Date'
                );
            for ($i = 0; $i < count($column_title); $i++) 
            {
                $sheet->setCellValue($column_names[$i] , $column_title[$i]);
            }
            
//            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
//                    array('fill' => array(
//                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                            'color' => array('argb' => 'FFCCCCCC')
//                        )
//                    )
//            );
             $sheet->getStyle('A3:K3')->applyFromArray(
                array('fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFCCCCCC')
                                )
                    )
            );
            
//            echo count($column_title); echo"<br/>";
//            print_r($sheet);
//            exit();
//            
            //$sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
            $sheet->getStyle('A3:K3')->getFont()->setBold(true);
            $r = 4;
            $CI->load->model('reports_model', 'reportsmodel');
            $data_arr = array();
            foreach ($tabledata as $row) 
            {
                
                $strlength = strpos($row->tax_code_type, '_');
                $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
                $row->tax_code_type;
                //new update
                $ci =& get_instance(); 
                $ci->load->database(); 
                $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                         ->get()->row(0);
                $tax_code_type= $data->code_type;
                if($tax_code_type == 'NRIC')
                {
                    $tax_code_type='SP';
                }else if($tax_code_type == 'FIN'){
                    $tax_code_type='SO';
                }
                else{
                    $tax_code_type='OT';
                }
                /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                 $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
                $course_start_time=date('His',strtotime($row->class_start_datetime));
                $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
                
                $course_end_time=date('His',strtotime($row->class_end_datetime));
                $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
                $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
                
                $sheet->setCellValueExplicit('A' . $r, 'I');
                $sheet->setCellValueExplicit('B' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $r, $course_start_date);
                $sheet->setCellValueExplicit('D' . $r, $course_end_date);
                $sheet->setCellValue('E' . $r, $row->comp_reg_no);
                $sheet->setCellValue('F' . $r, $row->tax_code);
                $sheet->setCellValue('G' . $r, $tax_code_type);
                $sheet->setCellValue('H' . $r, $row->first_name);
                $sheet->setCellValueExplicit('I' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('J' . $r, $trainee_email);
                $sheet->setCellValue('K' . $r, $enrollment_date);
                $r++;
            }
            ob_end_clean();
            $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".xls";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
}

function generate_traqom_report_xls_pritam($tabledata, $metadata) 
{
            $CI = & get_instance();
            $CI->load->library('excel');
            $CI->excel->setActiveSheetIndex(0);
            $CI->excel->getActiveSheet()->setTitle('TRAQOM  Report');
            $sheet = $CI->excel->getActiveSheet();
            foreach (range('A', 'Z') as $columnID) 
            {
                    $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                                ->setAutoSize(true);
            }
            foreach (range('A', 'C') as $columnID) 
            {
                $var = 'A';
                $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                        ->setAutoSize(true);
            }
            $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G','H');
            $column_title = array(
                'Course Reference Number',
                'Course End Date','TP UEN','NRIC/FIN/OTHER', 'ID Type', 'Full Name',
                'Mobile',
                'Email'
                );
            for ($i = 0; $i < count($column_title); $i++) 
            {
                $sheet->setCellValue($column_names[$i] . '1', $column_title[$i]);
            }
            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->applyFromArray(
                    array('fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('argb' => 'FFCCCCCC')
                        )
                    )
            );
            $sheet->getStyle('A1:' . $column_names[count($column_title) - 1] . '1')->getFont()->setBold(true);
            $r = 2;
            $CI->load->model('reports_model', 'reportsmodel');
            $data_arr = array();
            foreach ($tabledata as $row) 
            {
                
                $strlength = strpos($row->tax_code_type, '_');
                $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
                $row->tax_code_type;
                //new update
                $ci =& get_instance(); 
                $ci->load->database(); 
                $data = $ci->db->select('category_name as code_type')
                        ->from('metadata_values')
                        ->where('parameter_id', $row->tax_code_type)
                         ->get()->row(0);
                $tax_code_type= $data->code_type;
                if($tax_code_type == 'NRIC')
                {
                    $tax_code_type='SP';
                }else if($tax_code_type == 'FIN'){
                    $tax_code_type='SO';
                }
                else{
                    $tax_code_type='OT';
                }
                // sk code start for email id
                $pattern = "/[-_ ]+/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                //sk code end for email id
//                $trainee_email = $row->tax_code.'@yopmail.com';
                $course_end_time=date('His',strtotime($row->class_end_datetime));
                $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
                $sheet->setCellValueExplicit('A' . $r, $row->reference_num, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B' . $r, $course_end_date);
                $sheet->setCellValue('C' . $r, $row->comp_reg_no);
                $sheet->setCellValue('D' . $r, $row->tax_code);
                $sheet->setCellValue('E' . $r, $tax_code_type);
                $sheet->setCellValue('F' . $r, $row->first_name);
                $sheet->setCellValueExplicit('G' . $r, $row->contact_number, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('H' . $r, $trainee_email);
                $r++;
            }
            ob_end_clean();
            $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".xls";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
}
/**
 * This method generates the SOA CSV Report
 * @param type $tabledata
 * @param type $metadata
 * @return type
 */

function generate_traqom2_report_csv($tabledata, $metadata) {
    $CI = & get_instance();
    
    /* skm code st*/
    $total_data = count($tabledata);
    $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
    $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".csv";
    $column_title_first_row = array('H', $filename, $total_data);
    $column_title_second_row = array('H','Scenario', 'Outcome');
    /* skm code end */
    
    
    $column_title = array('H',
        'Trainee Name','Trainee ID','ID Type','Email','Mobile Country Code','Mobile Area Code','Mobile','TP Alias','Course Title','Area of Training',
        'Course Reference Number','Course Run Reference Number',
        'Course Start Date','Course End Date','Postel Code','Floor','Unit','Room','Full Qualification','Trainer Name','Class Name'

        );
    
    
    $data_arr = array();
    foreach ($tabledata as $row) 
    {
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        //$trainee_email = $row->tax_code.'@yopmail.com';
        $course_start_time=date('His',strtotime($row->class_start_datetime));
        $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
        
        $course_end_time=date('His',strtotime($row->class_end_datetime));
        $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
        
         $dob = str_replace('-','',$row->dob);
	
	$CI->load->model('class_model', 'class_Model');              
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);
        
        /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
        $i = 'I';
        $data_arr[] = array(
           $i,
           $row->first_name,$row->tax_code,$tax_code_type,$trainee_email,'','',$row->contact_number,$row->tenant_name,$row->crse_name,'',
            $row->reference_num,'',$course_start_date,$course_end_date,'','','','','',$trainer_name,$row->class_name
           
        );
    }
    $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $output = fopen('php://output', 'w');
    
    fputcsv($output, $column_title_first_row);
    fputcsv($output, $column_title_second_row);
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value))
                $data[$key] = '"' . $value . '"';
        }
        fputcsv($output, $data);
    }
    return;
}

function generate_traqom2_report_csv_xp($tabledata, $metadata) {
    $CI = & get_instance();
    
    /* skm code st*/
    $total_data = count($tabledata);
    $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
    $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".csv";
    $column_title_first_row = array('H', $filename, $total_data);
    $column_title_second_row = array('H','Scenario', 'Outcome');
    /* skm code end */
    
    
    $column_title = array('H',
        'Trainee ID','Trainee Name','ID Type','Email','Mobile Country Code','Mobile Area Code','Mobile','Course Title','Area of Training',
        'Course Reference Number','Course Run Reference Number',
        'Course Start Date','Course End Date','Postel Code','Floor','Unit','Room','Full Qualification','TP Alias','Trainer Name','Class Name'

        );
    
    
    $data_arr = array();
    foreach ($tabledata as $row) 
    {
        if($row->tpg_course_run_id == ''){
            $tpg_id =$row->class_name;
        }else{
            $tpg_id=$row->tpg_course_run_id;
        }
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        //$trainee_email = $row->tax_code.'@yopmail.com';
        $course_start_time=date('His',strtotime($row->class_start_datetime));
        $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
        
        $course_end_time=date('His',strtotime($row->class_end_datetime));
        $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
        
         $dob = str_replace('-','',$row->dob);
	
	$CI->load->model('class_model', 'class_Model');              
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);
        
        /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                
                /*end */
        $i = 'I';
       
        $data_arr[] = array(
           $i,
           $row->tax_code,$row->first_name,$tax_code_type,$row->registered_email_id,'','65',$row->contact_number,$row->crse_name,'',
            $row->reference_num,'',$course_start_date,$course_end_date,'','','','','',$row->tenant_name,$trainer_name,$row->class_name
           
        );
    }
    $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $output = fopen('php://output', 'w');
    
    fputcsv($output, $column_title_first_row);
    fputcsv($output, $column_title_second_row);
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value))
                $data[$key] = '"' . $value . '"';
        }
        fputcsv($output, $data);
    }
    return;
}

function generate_traqom_report_csv($tabledata, $metadata) {
    $CI = & get_instance();
    
    /* skm code st*/
    $total_data = count($tabledata);
    $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
    $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".csv";
    $column_title_first_row = array('H', $filename, $total_data);
    $column_title_second_row = array('H','Scenario', 'Outcome');
    /* skm code end */
    
    
    $column_title = array('H',
        'Trainee Name','Trainee ID','ID Type','Date Of Birth','Email','Mobile','TP Alias','Course Title','Area of Training',
        'Course Reference Number','Course Run Reference Number',
        'Course Start Date','Course End Date','Postel Code','Floor','Unit','Room','Full Qualification','Trainer Name','Class Name'

        );
    
    
    $data_arr = array();
    foreach ($tabledata as $row) 
    {
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        //$trainee_email = $row->tax_code.'@yopmail.com';
        $course_start_time=date('His',strtotime($row->class_start_datetime));
        $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
        
        $course_end_time=date('His',strtotime($row->class_end_datetime));
        $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
        
         $dob = str_replace('-','',$row->dob);
	
	$CI->load->model('class_model', 'class_Model');              
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);
        
        /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
        $i = 'I';
        $data_arr[] = array(
           $i,
           $row->first_name,$row->tax_code,$tax_code_type,$dob,$trainee_email,$row->contact_number,$row->tenant_name,$row->crse_name,'',
            $row->reference_num,'',course_start_date,$course_end_date,'','','','','',$trainer_name,$row->class_name
           
        );
    }
    $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $output = fopen('php://output', 'w');
    
    fputcsv($output, $column_title_first_row);
    fputcsv($output, $column_title_second_row);
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value))
                $data[$key] = '"' . $value . '"';
        }
        fputcsv($output, $data);
    }
    return;
}

function generate_traqom_report_csv_xp($tabledata, $metadata) {
    $CI = & get_instance();
    
    /* skm code st*/
    $total_data = count($tabledata);
    $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
    $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".csv";
    $column_title_first_row = array('H', $filename, $total_data);
    $column_title_second_row = array('H','Scenario', 'Outcome');
    /* skm code end */
    
    
    $column_title = array('H',
        'Trainee ID','Trainee Name','ID Type','Date Of Birth','Email','Mobile','Course Title','Area of Training',
        'Course Reference Number','Course Run Reference Number',
        'Course Start Date','Course End Date','Postel Code','Floor','Unit','Room','Full Qualification','TP Alias','Trainer Name','Class Name'

        );
    
    
    $data_arr = array();
    foreach ($tabledata as $row) 
    {
         if($row->tpg_course_run_id == ''){
            $tpg_id =$row->class_name;
        }else{
            $tpg_id=$row->tpg_course_run_id;
        }
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        //$trainee_email = $row->tax_code.'@yopmail.com';
        $course_start_time=date('His',strtotime($row->class_start_datetime));
        $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
        
        $course_end_time=date('His',strtotime($row->class_end_datetime));
        $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
        
         $dob = str_replace('-','',$row->dob);
	
	$CI->load->model('class_model', 'class_Model');              
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);
        
        /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
        $i = 'I';
      
        $data_arr[] = array(
           $i,
           $row->tax_code,$row->first_name,$tax_code_type,$dob,$row->registered_email_id,$row->contact_number,$row->crse_name,'',
            $row->reference_num,'',$course_start_date,$course_end_date,'','','','','',$row->tenant_name,$trainer_name,$row->class_name
           
        );
    }
    $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $output = fopen('php://output', 'w');
    
    fputcsv($output, $column_title_first_row);
    fputcsv($output, $column_title_second_row);
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value))
                $data[$key] = '"' . $value . '"';
        }
        fputcsv($output, $data);
    }
    return;
}

function generate_traqom_report_csv_old($tabledata, $metadata) {
    $CI = & get_instance();
    
    /* skm code st*/
    $total_data = count($tabledata);
    $course_end_time_filename=date('His',strtotime($tabledata[0]->class_end_datetime));
    $course_end_date_filename=date('Ymd',strtotime($tabledata[0]->class_end_datetime));
    $filename=$tabledata[0]->comp_reg_no."_".$course_end_date_filename."_".$course_end_time_filename.".csv";
    $column_title_first_row = array('H', $filename, $total_data);
    $column_title_second_row = array('H','Scenario', 'Outcome');
    /* skm code end */
    
    
    $column_title = array(
        'H',
        'Course Reference Number',
                'Course Start Date','Course End Date','TP UEN','NRIC/Passport', 'ID Type', 'Full Name',
                'Mobile',
                'Email','Enrollment Date');
    $data_arr = array();
    foreach ($tabledata as $row) 
    {
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        //$trainee_email = $row->tax_code.'@yopmail.com';
        $course_start_time=date('His',strtotime($row->class_start_datetime));
        $course_start_date=date('Ymd',strtotime($row->class_start_datetime));
        
        $course_end_time=date('His',strtotime($row->class_end_datetime));
        $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
        $enrollment_date = date('Y-m-d',strtotime($row->enrolled_on));
        
        /* skm code for new style email which is the combination of taxocde and class name intials st*/
                $pattern = "/[_,-]/";
                $string = $row->class_name;
                $class_name = preg_split($pattern, $string);
                $trainee_classname = $class_name[0];
                $trainee_taxcode = substr($row->tax_code, -4);
                
                //$trainee_email = $row->tax_code.'@yopmail.com';
//                $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
                $trainee_email = $trainee_taxcode.$trainee_classname.'@yopmail.com';
                /*end */
        $i = 'I';
        $data_arr[] = array(
           $i,$row->reference_num,$course_start_date,$course_end_date,$row->comp_reg_no, $row->tax_code,$tax_code_type,$row->first_name,$row->contact_number,$trainee_email,$enrollment_date
        );
    }
    $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $output = fopen('php://output', 'w');
    
    fputcsv($output, $column_title_first_row);
    fputcsv($output, $column_title_second_row);
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value))
                $data[$key] = '"' . $value . '"';
        }
        fputcsv($output, $data);
    }
    return;
}

function generate_traqom_report_csv_pritam($tabledata, $metadata) {
    $CI = & get_instance();
    $column_title = array(
        'Course Reference Number',
                'Course End Date','TP UEN','NRIC/FIN/OTHER', 'ID Type', 'Full Name',
                'Mobile',
                'Email');
    $data_arr = array();
    foreach ($tabledata as $row) 
    {
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $row->tax_code_type;
        //new update
        $ci =& get_instance(); 
        $ci->load->database(); 
        $data = $ci->db->select('category_name as code_type')
                ->from('metadata_values')
                ->where('parameter_id', $row->tax_code_type)
                 ->get()->row(0);
        $tax_code_type= $data->code_type;
        // sk code start for email id
        $pattern = "/[-_ ]+/";
        $string = $row->class_name;
        $class_name = preg_split($pattern, $string);
        $trainee_classname = $class_name[0];
        $trainee_taxcode = substr($row->tax_code, -4);
        $trainee_email = $trainee_classname.$trainee_taxcode.'@yopmail.com';
        //sk code end for email id
        
//        $trainee_email = $row->tax_code.'@yopmail.com';
        $course_end_time=date('His',strtotime($row->class_end_datetime));
        $course_end_date=date('Ymd',strtotime($row->class_end_datetime));
        $data_arr[] = array(
           $row->reference_num,$course_end_date,$row->comp_reg_no, $row->tax_code,$tax_code_type,$row->first_name,$row->contact_number,$trainee_email
        );
    }
    $filename=$row->comp_reg_no."_".$course_end_date."_".$course_end_time.".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $output = fopen('php://output', 'w');
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value))
                $data[$key] = '"' . $value . '"';
        }
        fputcsv($output, $data);
    }
    return;
}

/**
 * This method generates the SOA CSV Report
 * @param type $tabledata
 * @param type $metadata
 * @return type
 */
function generate_soa_report_csv($tabledata, $metadata) {
    $CI = & get_instance();
    $CI->load->model('course_model');
    $column_title = array(
        'ID Type', 'ID Number', 'Name (As in NRIC)', 'Gender', 'Nationality',
        'Date of Birth (DDMMYYYY)', 'Race', 'Trainee Contact No(Mobile)',
        'Trainee Contact No (Others)', 'Trainee Email Address', 'Company Name (Key in NA if not applicable)',
        'Designation', 'Medium of Assessment', 'Education Level', 'Salary Range',
        'Assessment Venue', 'Course Start Date (DDMMYYYY)', 'Course Reference Number (Refer to Course Listing in SkillsConnect)',
        'Competency Standard Code (Refer to Course Listing in SkillsConnect)',
        'Cert Code', 'Submission Type', 'Date Of Assessment (DDMMYYYY)', 'Result',
        'Trainer ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Assessor ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Printing of SOA/ Generating of e-Cert','Class Name');
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    foreach ($tabledata as $row) {
        $assment_det = $CI->reportsmodel->get_assessment_details($row->class_id, $row->user_id);
        $crse_manager = explode(',', $row->crse_manager);
        $manager = $CI->reportsmodel->get_user_taxcode($crse_manager);
        $manager_text = '';
        foreach ($manager as $man) {
            $manager_text .= $man->tax_code . ', ';
            break;
        }
        $manager_text = rtrim($manager_text, ', ');
        $classroom_trainer = explode(',', $row->classroom_trainer);
        $trainer = $CI->reportsmodel->get_user_taxcode($classroom_trainer);
        $trainer_text = '';
        foreach ($trainer as $train) {
            $trainer_text .= $train->tax_code . ', ';
            break;
        }
        $trainer_text = rtrim($trainer_text, ', ');
        $classroom_assessor = explode(',', $assment_det->assessor_id);
        $assessor = $CI->reportsmodel->get_user_taxcode($classroom_assessor);
        $assessor_text = '';
        foreach ($assessor as $assess) {
            $assessor_text .= $assess->tax_code . ', ';
            break;
        }
        $assessor_text = rtrim($assessor_text, ', ');
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $designation = '';
        if($row->account_type == 'INTUSR' && $row->designation != 'OTHERS') {
            $strlength = strpos($row->designation, '_');
            $designation = empty($strlength) ? $row->designation : substr($row->designation, $strlength + 1);
        } else if($row->account_type == 'TRAINE'){
            $strlength = strpos($row->occupation_code, '_');
            $designation = empty($strlength) ? $row->occupation_code : substr($row->occupation_code, $strlength + 1);
        }
        $strlength = strpos($row->class_language, '_');
        $class_language = empty($strlength) ? $row->class_language : substr($row->class_language, $strlength + 1);
        $strlength = strpos($row->highest_educ_level, '_');
        $highest_educ_level = empty($strlength) ? $row->highest_educ_level : substr($row->highest_educ_level, $strlength + 1);
        $strlength = strpos($row->salary_range, '_');
        $salary_range = empty($strlength) ? $row->salary_range : substr($row->salary_range, $strlength + 1);
        $gender_arr = array('MALE' => 'M', 'FEMALE' => 'F');
        if($row->company_id[0] == 'T') {           
            $tenant_details = fetch_tenant_details($row->company_id);            
            $row->company_name = $tenant_details->tenant_name;
            $row->comp_email = $tenant_details->tenant_email_id;
        }
        $course_code =  rtrim($CI->course_model->get_metadata_on_parameter_id($row->certi_level), ', '); //sk2
        $data_arr[] = array(
            $tax_code_type, $row->tax_code, $row->first_name, $gender_arr[$row->gender], $row->nationality,
            (!empty($row->dob)) ? date('dmY', strtotime($row->dob)) : '', $row->race, $row->contact_number,
            $row->alternate_contact_number, empty($row->registered_email_id) ? $row->comp_email : $row->registered_email_id,
            empty($row->company_name) ? 'NA' : $row->company_name, $designation, $class_language,
            $highest_educ_level, $salary_range, ($assment_det->assmnt_venue == 'OTH') ? 'Others (' . $assment_det->assmnt_venue_oth . ')' : $metadata[$assment_det->assmnt_venue],
            date('dmY', strtotime($row->class_start_datetime)), $row->reference_num, $row->competency_code,
            $course_code, 'N',
            (!empty($assment_det->assmnt_date)) ? date('dmY', strtotime($assment_det->assmnt_date)) : date('dmY',strtotime($row->class_end_datetime)),
            $row->training_score, $trainer_text, $assessor_text, 'No',$row->class_name
        );
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=SOA_Report.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value)){
                $data[$key] = '"' . $value . '"';
            }
        }
        fputcsv($output, $data);
    }
   exit;
    //return; commented by shubhranshu since html are coming in csv file
}

/////added by shubhranshu soa report csv only for XP
function generate_soa_report_csv_xp($tabledata, $metadata) {
    $CI = & get_instance();
    $CI->load->model('course_model');
    $column_title = array(
        'ID Type', 'ID Number', 'Name (As in NRIC)', 'Gender', 'Nationality',
        'Date of Birth (DDMMYYYY)', 'Race', 'Trainee Contact No(Mobile)',
        'Trainee Contact No (Others)', 'Trainee Email Address', 'Company Name (Key in NA if not applicable)',
        'Designation', 'Medium of Assessment', 'Education Level', 'Salary Range',
        'Assessment Venue','Course Start Date (DDMMYYYY)', 'Course Reference Number (Refer to Course Listing in SkillsConnect)',
        'Competency Standard Code (Refer to Course Listing in SkillsConnect)',
        'Cert Code', 'Submission Type', 'Date Of Assessment (DD-MM-YYYY)', 'Result',
        'Trainer ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Assessor ID (For NRIC/FIN/Other ID only,Names should not be included)',
        'Printing of SOA/ Generating of e-Cert','TPGateway Course Run ID');
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    foreach ($tabledata as $row) {
        $assment_det = $CI->reportsmodel->get_assessment_details($row->class_id, $row->user_id);
        $crse_manager = explode(',', $row->crse_manager);
        $manager = $CI->reportsmodel->get_user_taxcode($crse_manager);
        $manager_text = '';
        foreach ($manager as $man) {
            $manager_text .= $man->tax_code . ', ';
            break;
        }
        $manager_text = rtrim($manager_text, ', ');
        $classroom_trainer = explode(',', $row->classroom_trainer);
        $trainer = $CI->reportsmodel->get_user_taxcode($classroom_trainer);
        $trainer_text = '';
        foreach ($trainer as $train) {
            $trainer_text .= $train->tax_code . ', ';
            break;
        }
        $trainer_text = rtrim($trainer_text, ', ');
        $classroom_assessor = explode(',', $assment_det->assessor_id);
        $assessor = $CI->reportsmodel->get_user_taxcode($classroom_assessor);
        $assessor_text = '';
        foreach ($assessor as $assess) {
            $assessor_text .= $assess->tax_code . ', ';
            break;
        }
        $assessor_text = rtrim($assessor_text, ', ');
        $strlength = strpos($row->tax_code_type, '_');
        $tax_code_type = empty($strlength) ? $row->tax_code_type : substr($row->tax_code_type, $strlength + 1);
        $designation = '';
        if($row->account_type == 'INTUSR' && $row->designation != 'OTHERS') {
            $strlength = strpos($row->designation, '_');
            $designation = empty($strlength) ? $row->designation : substr($row->designation, $strlength + 1);
        } else if($row->account_type == 'TRAINE'){
            $strlength = strpos($row->occupation_code, '_');
            $designation = empty($strlength) ? $row->occupation_code : substr($row->occupation_code, $strlength + 1);
        }
        $strlength = strpos($row->class_language, '_');
        $class_language = empty($strlength) ? $row->class_language : substr($row->class_language, $strlength + 1);
        $strlength = strpos($row->highest_educ_level, '_');
        $highest_educ_level = empty($strlength) ? $row->highest_educ_level : substr($row->highest_educ_level, $strlength + 1);
        $strlength = strpos($row->salary_range, '_');
        $salary_range = empty($strlength) ? $row->salary_range : substr($row->salary_range, $strlength + 1);
        $gender_arr = array('MALE' => 'M', 'FEMALE' => 'F');
        if($row->company_id[0] == 'T') {           
            $tenant_details = fetch_tenant_details($row->company_id);            
            $row->company_name = $tenant_details->tenant_name;
            $row->comp_email = $tenant_details->tenant_email_id;
        }
        $course_code =  rtrim($CI->course_model->get_metadata_on_parameter_id($row->certi_level), ', '); //sk2
        if($row->tpg_course_run_id == ''){
            $tpg_id =$row->class_name;
        }else{
            $tpg_id=$row->tpg_course_run_id;
        }
        
        if($row->training_score)
        {
            if($row->training_score=="C")
            {
                $score="Competent";
            }
            else if($row->training_score=="ABS")
            {
                $score="Absent";
            }
            else if($row->training_score=="NYC")
            {
                $score="Not Yet Competent";
            }
            else if($row->training_score=="EX")
            {
                $score="Exempted";
            }
            
            else if($row->training_score=="2NYC")
            {
                $score="Twice Not Competent";
            }
            else if($row->training_score=="ATR")
            {
                $score="Attrition";
            }
            else
            {
                $score=$row->training_score;
            }
        }
        else
        {
             $score=$row->training_score;
        }
        
        $data_arr[] = array(
            $tax_code_type, $row->tax_code, $row->first_name, $gender_arr[$row->gender], $row->nationality,
            (!empty($row->dob)) ? date('dmY', strtotime($row->dob)) : '', $row->race, $row->contact_number,
            $row->alternate_contact_number, empty($row->registered_email_id) ? $row->comp_email : $row->registered_email_id,
            empty($row->company_name) ? 'NA' : $row->company_name, $designation, $class_language,
            $highest_educ_level, $salary_range, ($assment_det->assmnt_venue == 'OTH') ? 'Others (' . $assment_det->assmnt_venue_oth . ')' : $metadata[$assment_det->assmnt_venue],
            
            date('dmY', strtotime($row->class_start_datetime)), $row->reference_num, $row->competency_code,
            $course_code, 'N',
            (!empty($assment_det->assmnt_date)) ? date('d-m-Y', strtotime($assment_det->assmnt_date)) : date('d-m-Y',strtotime($row->class_end_datetime)),
            $score, $trainer_text, $assessor_text, 'No',$tpg_id
        );
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=SOA_Report.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, $column_title);
    foreach ($data_arr as $data) {
        foreach ($data as $key => $value) {
            if (!empty($value)){
                $data[$key] = '"' . $value . '"';
            }
        }
        fputcsv($output, $data);
    }
   exit;
    //return; commented by shubhranshu since html are coming in csv file
}
/**
 * This function export credit notes to xl.
 * @param type $query
 */
function export_credit_note($result_set) {
    $CI = & get_instance();   
    $data = $result_set;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Credit Note List');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'I') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:I1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Credit Note List as on ' . date('M j Y, l'));
    $sheet->getStyle('A1:I1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'Credit Note Number');
    $sheet->setCellValue('C2', 'Credit Note Date');
    $sheet->setCellValue('D2', 'Ori. Invoice Number');
    $sheet->setCellValue('E2', 'Ori. Invoice Date');
    $sheet->setCellValue('F2', 'Amount');
    $sheet->setCellValue('G2', 'Credit Note Issued By');
    $sheet->setCellValue('H2', 'Credit Note Issue Reason');
    $sheet->setCellValue('I2', 'Tg.Ref Number');
    $sheet->getStyle('A2:I2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:I2')->getFont()->setBold(true);
    $rn = 3;
    foreach ($data as $row) {        
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $row->credit_note_number);
        $sheet->setCellValue('C' . $rn, date('d-m-Y', strtotime($row->credit_note_date)));
        $sheet->setCellValue('D' . $rn, $row->ori_invoice_number);        
        $sheet->setCellValue('E' . $rn, date('d-m-Y', strtotime($row->ori_invoice_date)));
        $sheet->setCellValue('F' . $rn, "$ ".number_format($row->credit_note_amount, 2));
        $sheet->setCellValue('G' . $rn, $row->credit_note_issued_by);
        $sheet->setCellValue('H' . $rn, $row->credit_note_issue_reason);
        $sheet->setCellValue('I' . $rn, $row->tg_ref_number);
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="credit_notes.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}


// start create week days
function create_week_days_array(DateTime $start_date, DateTime $end_date , $class_schedule_data) {
    $days = array();
    $current_date = clone $start_date;

            do {
                 if($class_schedule_data[$current_date->format('d/m/y')][1] || $class_schedule_data[$current_date->format('d/m/y')][2])
                       {
                            $days[] = clone $current_date; 
                       }
                   elseif($start_date == $end_date)
                   {
                       $days[] = clone $current_date; 
                   }

                $current_date->add(new DateInterval('P1D'));
            } while ($current_date->format('Y-m-d') <= $end_date->format('Y-m-d'));
  
  return array_filter($days);
}
function generate_class_attendance_sheet_xls($results, $class_details,$start, $end, $tenant, $class_schedule_data) 
{
    $statment = 'I understand that the training provider will not be held responsible to resubmit the results should the Name and ID number was found incorrect after the course date.';
        $data=PAX_PER_SHEET;
        $num_of_sheets=ceil(count($results)/$data);
        $j=0;
        $i=1;
        while($i<=$num_of_sheets)
        {
            $arr[$i] = array_slice($results,$j,$data);//0 - 5, 5- 10, 
            $i++;
            $j=$j+$data;
        }
        $results=$arr1;
        $interval = date_diff($start, $end);
        $total_days = $interval->format('%a');
        if(count($class_schedule_data)>0 or $total_days == 0)  // else condition on line no :3447
        {
            if($total_days!=0)
            {
                $count = count($class_schedule_data);
                $total_days = ($count-1);
            }
            else
            {
                $total_days = $interval->format('%a');
            }
            $CI = & get_instance();
            $CI->load->model('trainee_model', 'traineemodel');
            $className = $class_details->class_name;
            $courseName = $class_details->crse_name;
            $class_start_date_formatted = date_format_singapore($class_details->class_start_datetime) . " " . time_format_singapore($class_details->class_start_datetime);
            $class_end_date_formatted = date_format_singapore($class_details->class_end_datetime) . " " . time_format_singapore($class_details->class_end_datetime);
            $CI->load->library('excel');
            $i=1;
            while($i<=$num_of_sheets)
            {
                //$a="arr".$i;
                $results=$arr[$i];
                $CI->excel->createSheet();
                $CI->excel->setActiveSheetIndex($i);
                $CI->excel->getActiveSheet()->setTitle("Attendance List".$i);
                $sheet = $CI->excel->getActiveSheet();
                $CI->excel->getActiveSheet()->getRowDimension('13')->setRowHeight(50);
                $column_index = PHPExcel_Cell::columnIndexFromString('I');
                $adjusted_column_index = $column_index + $total_days;
                $last_column_name = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index);
                $assmnt_sign_column = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index+1);        
                $sheet->mergeCells('A1:' . $assmnt_sign_column . '1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);
                $sheet->setCellValue('A1', "Attendance List".$i);
                $sheet->mergeCells('A2:' . $assmnt_sign_column . '2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A2', "");
                $sheet->mergeCells('A3:' . $assmnt_sign_column . '3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A3', "Course Title: $courseName");
                $sheet->mergeCells('A4:' . $assmnt_sign_column . '4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A4', "Name of Training: $className");
                $sheet->mergeCells('A5:' . $assmnt_sign_column . '5');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A5', "Training Duration: ".$class_start_date_formatted . ' To ' . $class_end_date_formatted);
                $sheet->mergeCells('A6:' . $assmnt_sign_column . '6');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A6', "Trainer: ".$class_details->classroom_trainer);
                $sheet->mergeCells('A7:' . $assmnt_sign_column . '7');
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A7', "Assessor: ".$class_details->assessor);
                $sheet->mergeCells('A8:' . $assmnt_sign_column . '8');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A8', "Company: ".$class_details->company_name);
                $sheet->mergeCells('A9:' . $assmnt_sign_column . '9');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A9', "Class ID: ".$class_details->class_id);
                $sheet->mergeCells('A10:' . $assmnt_sign_column . '10');
                $sheet->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A10', "Training Hrs: ".($class_details->total_classroom_duration + $class_details->total_lab_duration));
                $sheet->mergeCells('A11:' . $assmnt_sign_column . '11');
                $sheet->getStyle('A11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A11', "Assessment Hrs: ".$class_details->assmnt_duration);
                $sheet->setCellValue('A12', 'Sl #');
                $sheet->setCellValue('B12', 'Name');
                $sheet->setCellValue('C12', 'NRIC/FIN No.');
                $sheet->setCellValue('D12', 'Comp Id');
                $sheet->setCellValue('E12', 'Country');
                $sheet->setCellValue('F12', 'Class Start Date');
                $sheet->setCellValue('G12', 'Assmnt. Dt.');
                $sheet->setCellValue('H12', 'A/P');
                $sheet->setCellValue('I12', '');
                $sheet->setCellValue('J12', "Trainees' Attendance Sign-in");
                $sheet->getStyle('J12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('J12:' . $assmnt_sign_column . '12');
                $sheet->setCellValue($assmnt_sign_column.'13','Assmnt. Sign.');
                $sheet->mergeCells('A13:H13');
                $sheet->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('I13', 'Session');

                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(5);
                $sheet->getColumnDimension('I')->setWidth(15);

                $days = array();

                $current_date = $start;
                while ($current_date < $end || compare_dates_without_time($current_date, $end)) 
                {
                    $days[] = $current_date;
                    $next_day = DateTime::createFromFormat('U', strtotime("tomorrow 12:00:00", $current_date->getTimestamp()));
                    $current_date = $next_day;
                }

                // echo "<pre>";
                if($total_days != 0)
                { 
                    $weeks = create_week_days_array($start ,$end, $class_schedule_data);
                }
                else
                {
                  $weeks = array($start);
                }
                $sheet->getStyle('A12:' . $assmnt_sign_column . '13')->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCCCCC')
                            )
                        )
                );

                $rn = 13;
                $cell = 9;
                $is_two_sessions = $class_details->class_session_day == 2;
                foreach ($weeks as $day) {
                    $session_time = "\n " . $class_schedule_data[$day->format('d/m/y')][1];
                    if ($is_two_sessions) {
                        $session_time2 = $class_schedule_data[$day->format('d/m/y')][2];
                        if ($session_time2) {
                            $session_time .= "\n " . $session_time2;
                        }
                    }
                    if($class_schedule_data[$day->format('d/m/y')])
                    {
                         $sk =  explode("to",$session_time);
                         $sk[0];
                     //$sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time); previous code
                       $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y'). $sk[0]);
                    $cell++;
                    }
                    elseif($total_days == 0)
                    {

                    //$sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time); previous code
                        $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y'). $sk[0]);
                    }
                }

                $row = 14;
                $index = 1;
                $footer_text = array();
                //print_r($results);
                //exit;
                foreach ($results as $res) 
                {
                    if ($is_two_sessions) 
                    {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                        $sheet->mergeCells('D' . $row . ':D' . ($row + 1));
                        $sheet->mergeCells('E' . $row . ':E' . ($row + 1));
                        $sheet->mergeCells('F' . $row . ':F' . ($row + 1));
                        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
                        $sheet->mergeCells('H' . $row . ':H' . ($row + 1));
                    }
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, $res['record']['name']);
                    $sheet->setCellValue('C' . $row, mask_format($res['record']['tax_code']));
                    $res['record']['company_id'] = ($res['record']['company_id'] == 0)? '': $res['record']['company_id'];
                    $sheet->setCellValue('D' . $row, $res['record']['company_id']);
                    if(!empty($res['record']['company_id']))
                    {
                            if($res['record']['company_id'][0]== 'T'){
                                $footer_text[$res['record']['company_id']] = $tenant->tenant_name;
                            }else {
                                $footer_text[$res['record']['company_id']] = $res['record']['company_name'];
                            }
                    }
                    $sheet->setCellValue('E' . $row, $res['record']['nationality']);
                    $sheet->setCellValue('F' . $row, date('d/m/Y',  strtotime($class_details->class_start_datetime)));
                    $assmnt_date = $CI->traineemodel->get_assessment_date($res['record']['class_id'], $res['record']['user_id']);
                    $formatd_assmnt_date = empty($assmnt_date) ? '' : date('d/m/Y', strtotime($assmnt_date));
                    $sheet->setCellValue('G' . $row, $formatd_assmnt_date);
                   // PRESENT AND ABSENT CODE START 
                    if($res['record']['att']==1)
                    {
                      $sheet->setCellValue('H' .$row,'P');  
                    }
                    else
                    {
                      $sheet->setCellValue('H' .$row,'A');  
                    }
                   // PRESENT AND ABSENT CODE END 

                    $sheet->setCellValue('I' . $row, 'Session1:');
                    if ($is_two_sessions) {
                        $sheet->setCellValue('I' . ($row + 1), 'Session2:');
                    }

                    $cell = 9;
                    foreach ($weeks as $day) 
                    {
                        $formatted_day = $day->format('Y-m-d');
                        $ses1 = '';
                        $ses2 = '';

                        $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                        if ($is_two_sessions) 
                        {
                            $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                        }
                        $cell++;
                    }

                    $row += ($is_two_sessions) ? 2 : 1;
                    $index++;
                }
                $row_update_start_assmntsign = $row;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('I'.$row, 'Absent');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('I'.$row, 'Present');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('I'.$row, 'Total');
                $cell = 9;
                $total_users = --$index;
                /*foreach ($days as $day) {
                    $sheet->setCellValueByColumnAndRow($cell, $row, $total_users);
                    $cell++;
                }*/
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('I'.$row, 'Trainer Sign-in');
                $sheet->mergeCells($assmnt_sign_column.$row_update_start_assmntsign.':'.$assmnt_sign_column.$row);

                $sheet->getStyle('A14:G' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A13:A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $row++;
                $row_after_table = $row+1;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Name of Company: '.$class_details->company_name);
                $row++;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Full Name & Designation of Director:');
                $row++;
                
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, $statment);
                $row++;
                
                
                foreach($footer_text as $k=>$v)
                {
                    $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                    $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $sheet->setCellValue('A'.$row, '** '.$k.' - '.$v);
                    $row++;
                }
                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF000000'),
                        ),
                    ),
                );
                $sheet->getStyle(
                        'A1:' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($styleArray);
                $style2Array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FFFFFFFF'),
                        ),
                    ),
                );
                $sheet->getStyle('A3:A11')->applyFromArray($style2Array);
                $sheet->getStyle(
                        'A'.$row_after_table.':' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($style2Array);

            $i++;
            }
            ob_end_clean();
        
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
        }
        else 
        {  // if condition on line no 3191
           
            $CI = & get_instance();
            $CI->load->model('trainee_model', 'traineemodel');
            $className = $class_details->class_name;
            $courseName = $class_details->crse_name;
            $class_start_date_formatted = date_format_singapore($class_details->class_start_datetime) . " " . time_format_singapore($class_details->class_start_datetime);
            $class_end_date_formatted = date_format_singapore($class_details->class_end_datetime) . " " . time_format_singapore($class_details->class_end_datetime);
            $CI->load->library('excel');
            $i=1;
            while($i<=$num_of_sheets)
            {
                 //$a="arr".$i;
                $results=$arr[$i];
                $CI->excel->createSheet();
                $CI->excel->setActiveSheetIndex($i);
                $CI->excel->getActiveSheet()->setTitle("Attendance List".$i);
                $sheet = $CI->excel->getActiveSheet();
                $CI->excel->getActiveSheet()->getRowDimension('13')->setRowHeight(50);
                $column_index = PHPExcel_Cell::columnIndexFromString('H');
                $adjusted_column_index = $column_index + $total_days;
                $last_column_name = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index);
                $assmnt_sign_column = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index+1);        
                $sheet->mergeCells('A1:' . $assmnt_sign_column . '1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);
                $sheet->setCellValue('A1', "Attendance List1.");
                $sheet->mergeCells('A2:' . $assmnt_sign_column . '2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A2', "");
                $sheet->mergeCells('A3:' . $assmnt_sign_column . '3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A3', "Course Title: $courseName");
                $sheet->mergeCells('A4:' . $assmnt_sign_column . '4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A4', "Name of Training: $className");
                $sheet->mergeCells('A5:' . $assmnt_sign_column . '5');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A5', "Training Duration: ".$class_start_date_formatted . ' To ' . $class_end_date_formatted);
                $sheet->mergeCells('A6:' . $assmnt_sign_column . '6');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A6', "Trainer: ".$class_details->classroom_trainer);
                $sheet->mergeCells('A7:' . $assmnt_sign_column . '7');
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A7', "Assessor: ".$class_details->assessor);
                $sheet->mergeCells('A8:' . $assmnt_sign_column . '8');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A8', "Company: ".$class_details->company_name);
                $sheet->mergeCells('A9:' . $assmnt_sign_column . '9');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A9', "Class ID: ".$class_details->class_id);
                $sheet->mergeCells('A10:' . $assmnt_sign_column . '10');
                $sheet->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A10', "Training Hrs: ".($class_details->total_classroom_duration + $class_details->total_lab_duration));
                $sheet->mergeCells('A11:' . $assmnt_sign_column . '11');
                $sheet->getStyle('A11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A11', "Assessment Hrs: ".$class_details->assmnt_duration);
                $sheet->setCellValue('A12', 'Sl #');
                $sheet->setCellValue('B12', 'Name');
                $sheet->setCellValue('C12', 'NRIC/FIN No.');
                $sheet->setCellValue('D12', 'Comp Id');
                $sheet->setCellValue('E12', 'Country');
                $sheet->setCellValue('F12', 'Class Start Date');
                $sheet->setCellValue('G12', 'Assmnt. Dt.');
                $sheet->setCellValue('H12', '');
                $sheet->setCellValue('I12', "Trainees' Attendance Sign-in");
                $sheet->getStyle('I12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('I12:' . $assmnt_sign_column . '12');
                $sheet->setCellValue($assmnt_sign_column.'13','Assmnt. Sign.');
                $sheet->mergeCells('A13:G13');
                $sheet->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('H13', 'Session');

                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(15);

                $days = array();

                $current_date = $start;
                while ($current_date < $end || compare_dates_without_time($current_date, $end)) 
                {
                    $days[] = $current_date;
                    $next_day = DateTime::createFromFormat('U', strtotime("tomorrow 12:00:00", $current_date->getTimestamp()));
                    $current_date = $next_day;
                }
                $sheet->getStyle('A12:' . $assmnt_sign_column . '13')->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCCCCC')
                            )
                        )
                );

                $rn = 13;
                $cell = 8;
                $is_two_sessions = $class_details->class_session_day == 2;
                foreach ($days as $day) {
                    $session_time = "\n " . $class_schedule_data[$day->format('d/m/y')][1];
                    if ($is_two_sessions) {
                        $session_time2 = $class_schedule_data[$day->format('d/m/y')][2];
                        if ($session_time2) {
                            $session_time .= "\n " . $session_time2;
                        }
                    }
                    $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time);
                    $cell++;
                }

                $row = 14;
                $index = 1;
                $footer_text = array();
                foreach ($results as $res) {
                    if ($is_two_sessions) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                        $sheet->mergeCells('D' . $row . ':D' . ($row + 1));
                        $sheet->mergeCells('E' . $row . ':E' . ($row + 1));
                        $sheet->mergeCells('F' . $row . ':F' . ($row + 1));
                        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
                    }
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, $res['record']['name']);
                    $sheet->setCellValue('C' . $row, mask_format($res['record']['tax_code']));
                    $res['record']['company_id'] = ($res['record']['company_id'] == 0)? '': $res['record']['company_id'];
                    $sheet->setCellValue('D' . $row, $res['record']['company_id']);
                    if(!empty($res['record']['company_id'])){
                            if($res['record']['company_id'][0]== 'T'){
                                $footer_text[$res['record']['company_id']] = $tenant->tenant_name;
                            }else {
                                $footer_text[$res['record']['company_id']] = $res['record']['company_name'];
                            }
                    }
                    $sheet->setCellValue('E' . $row, $res['record']['nationality']);
                    $sheet->setCellValue('F' . $row, date('d/m/Y',  strtotime($class_details->class_start_datetime)));
                    $assmnt_date = $CI->traineemodel->get_assessment_date($res['record']['class_id'], $res['record']['user_id']);
                    $formatd_assmnt_date = empty($assmnt_date) ? '' : date('d/m/Y', strtotime($assmnt_date));
                    $sheet->setCellValue('G' . $row, $formatd_assmnt_date);

                    $sheet->setCellValue('H' . $row, 'Session1:');
                    if ($is_two_sessions) {
                        $sheet->setCellValue('H' . ($row + 1), 'Session2:');
                    }

                    $cell = 8;
                    foreach ($days as $day) {
                        $formatted_day = $day->format('Y-m-d');
                        $ses1 = '';
                        $ses2 = '';

                        $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                        if ($is_two_sessions) {
                            $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                        }
                        $cell++;
                    }

                    $row += ($is_two_sessions) ? 2 : 1;
                    $index++;
                }
                $row_update_start_assmntsign = $row;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Absent');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Present');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Total');
                $cell = 8;
                $total_users = --$index;
                foreach ($days as $day) {
                    $sheet->setCellValueByColumnAndRow($cell, $row, $total_users);
                    $cell++;
                }
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Trainer Sign-in');
                $sheet->mergeCells($assmnt_sign_column.$row_update_start_assmntsign.':'.$assmnt_sign_column.$row);

                $sheet->getStyle('A14:G' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A13:A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $row++;
                $row_after_table = $row+1;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Name of Company: '.$class_details->company_name);
                $row++;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Full Name & Designation of Director:');
                $row++;
                
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, $statment);
                $row++;
                
                foreach($footer_text as $k=>$v){
                    $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                    $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $sheet->setCellValue('A'.$row, '** '.$k.' - '.$v);
                    $row++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF000000'),
                        ),
                    ),
                );


                $sheet->getStyle(
                        'A1:' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($styleArray);
                $style2Array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FFFFFFFF'),
                        ),
                    ),
                );
                $sheet->getStyle('A3:A11')->applyFromArray($style2Array);
                $sheet->getStyle(
                        'A'.$row_after_table.':' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($style2Array);
                 $i++;
            }
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
        }
    
}


function generate_class_attendance_sheet_xls_xp($results, $class_details,$start, $end, $tenant, $class_schedule_data) 
{
    $statment = 'I understand that the training provider will not be held responsible to resubmit the results should the Name and ID number was found incorrect after the course date.';
        $data=PAX_PER_SHEET;
        $num_of_sheets=ceil(count($results)/$data);
        $j=0;
        $i=1;
        while($i<=$num_of_sheets)
        {
            $arr[$i] = array_slice($results,$j,$data);//0 - 5, 5- 10, 
            $i++;
            $j=$j+$data;
        }
        $results=$arr1;
        $interval = date_diff($start, $end);
        $total_days = $interval->format('%a');
        if(count($class_schedule_data)>0 or $total_days == 0)  // else condition on line no :3447
        {
            if($total_days!=0)
            {
                $count = count($class_schedule_data);
                $total_days = ($count-1);
            }
            else
            {
                $total_days = $interval->format('%a');
            }
            $CI = & get_instance();
            $CI->load->model('trainee_model', 'traineemodel');
            $className = $class_details->class_name;
            $courseName = $class_details->crse_name;
            $class_start_date_formatted = date_format_singapore($class_details->class_start_datetime) . " " . time_format_singapore($class_details->class_start_datetime);
            $class_end_date_formatted = date_format_singapore($class_details->class_end_datetime) . " " . time_format_singapore($class_details->class_end_datetime);
            $CI->load->library('excel');
            $i=1;
            while($i<=$num_of_sheets)
            { 
                //$a="arr".$i;
                $results=$arr[$i];
                $CI->excel->createSheet();
                $CI->excel->setActiveSheetIndex($i);
                $CI->excel->getActiveSheet()->setTitle("Attendance List".$i);
                $sheet = $CI->excel->getActiveSheet();
                $CI->excel->getActiveSheet()->getRowDimension('13')->setRowHeight(50);
                $column_index = PHPExcel_Cell::columnIndexFromString('H');
                $adjusted_column_index = $column_index + $total_days;
                $last_column_name = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index);
                $assmnt_sign_column = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index+1);    
               
                $sheet->mergeCells('A1:' . $assmnt_sign_column . '1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);
                $sheet->setCellValue('A1', "Attendance List".$i);
                $sheet->mergeCells('A2:' . $assmnt_sign_column . '2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A2', "");
                $sheet->mergeCells('A3:' . $assmnt_sign_column . '3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A3', "Course Title: $courseName");
                $sheet->mergeCells('A4:' . $assmnt_sign_column . '4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A4', "Name of Training: $className");
                $sheet->mergeCells('A5:' . $assmnt_sign_column . '5');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A5', "Training Duration: ".$class_start_date_formatted . ' To ' . $class_end_date_formatted);
                $sheet->mergeCells('A6:' . $assmnt_sign_column . '6');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A6', "Trainer: ".$class_details->classroom_trainer);
                $sheet->mergeCells('A7:' . $assmnt_sign_column . '7');
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A7', "Assessor: ".$class_details->assessor);
                $sheet->mergeCells('A8:' . $assmnt_sign_column . '8');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A8', "Company: ".$class_details->company_name);
                $sheet->mergeCells('A9:' . $assmnt_sign_column . '9');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A9', "Class ID: ".$class_details->class_id);
                $sheet->mergeCells('A10:' . $assmnt_sign_column . '10');
                $sheet->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A10', "Training Hrs: ".($class_details->total_classroom_duration + $class_details->total_lab_duration));
                $sheet->mergeCells('A11:' . $assmnt_sign_column . '11');
                $sheet->getStyle('A11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A11', "Assessment Hrs: ".$class_details->assmnt_duration);
                $sheet->setCellValue('A12', 'Sl #');
                $sheet->setCellValue('B12', 'Name');
                $sheet->setCellValue('C12', 'NRIC/FIN No.');
                $sheet->setCellValue('D12', 'Comp Id');
                $sheet->setCellValue('E12', 'Country');
                $sheet->setCellValue('F12', 'Class Start Date');
                $sheet->setCellValue('G12', 'Assmnt. Dt.');
                //$sheet->setCellValue('H12', 'A/P');
                $sheet->setCellValue('H12', '');
                $sheet->setCellValue('I12', "Trainees' Attendance Sign-in");
                $sheet->getStyle('I12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('I12:' . $assmnt_sign_column . '12');
                $sheet->setCellValue($assmnt_sign_column.'13','Assmnt. Sign.');
                $sheet->mergeCells('A13:G13');
                $sheet->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('H13', 'Session');

                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(25);
                $sheet->getColumnDimension('J')->setWidth(15);
                $days = array();

                $current_date = $start;
                while ($current_date < $end || compare_dates_without_time($current_date, $end)) 
                {
                    $days[] = $current_date;
                    $next_day = DateTime::createFromFormat('U', strtotime("tomorrow 12:00:00", $current_date->getTimestamp()));
                    $current_date = $next_day;
                }

                // echo "<pre>";
                if($total_days != 0)
                { 
                    $weeks = create_week_days_array($start ,$end, $class_schedule_data);
                }
                else
                {
                  $weeks = array($start);
                }
                $sheet->getStyle('A12:' . $assmnt_sign_column . '13')->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCCCCC')
                            )
                        )
                );

                $rn = 13;
                $cell = 8;
                $is_two_sessions = $class_details->class_session_day == 2;
                foreach ($weeks as $day) {
                    $session_time = "\n " . $class_schedule_data[$day->format('d/m/y')][1];
                    if ($is_two_sessions) {
                        $session_time2 = $class_schedule_data[$day->format('d/m/y')][2];
                        if ($session_time2) {
                            $session_time .= "\n " . $session_time2;
                        }
                    }
                    if($class_schedule_data[$day->format('d/m/y')])
                    {
                         $sk =  explode("to",$session_time);
                         $sk[0];
                     //$sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time); previous code
                       $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y'). $sk[0]);
                    $cell++;
                    }
                    elseif($total_days == 0)
                    {

                    //$sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time); previous code
                        $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y'). $sk[0]);
                    }
                }

                $row = 14;
                $index = 1;
                $footer_text = array();
                //print_r($results);
                //exit;
                foreach ($results as $res) 
                {
                    if ($is_two_sessions) 
                    {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                        $sheet->mergeCells('D' . $row . ':D' . ($row + 1));
                        $sheet->mergeCells('E' . $row . ':E' . ($row + 1));
                        $sheet->mergeCells('F' . $row . ':F' . ($row + 1));
                        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
                        //$sheet->mergeCells('H' . $row . ':H' . ($row + 1));
                    }
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, $res['record']['name']);
                    $sheet->setCellValue('C' . $row, mask_format($res['record']['tax_code']));
                    $res['record']['company_id'] = ($res['record']['company_id'] == 0)? '': $res['record']['company_id'];
                    $sheet->setCellValue('D' . $row, $res['record']['company_id']);
                    if(!empty($res['record']['company_id']))
                    {
                            if($res['record']['company_id'][0]== 'T'){
                                $footer_text[$res['record']['company_id']] = $tenant->tenant_name;
                            }else {
                                $footer_text[$res['record']['company_id']] = $res['record']['company_name'];
                            }
                    }
                    $sheet->setCellValue('E' . $row, $res['record']['nationality']);
                    $sheet->setCellValue('F' . $row, date('d/m/Y',  strtotime($class_details->class_start_datetime)));
                    $assmnt_date = $CI->traineemodel->get_assessment_date($res['record']['class_id'], $res['record']['user_id']);
                    $formatd_assmnt_date = empty($assmnt_date) ? '' : date('d/m/Y', strtotime($assmnt_date));
                    $sheet->setCellValue('G' . $row, $formatd_assmnt_date);
                   // PRESENT AND ABSENT CODE START 
//                    if($res['record']['att']==1)
//                    {
//                      $sheet->setCellValue('H' .$row,'P');  
//                    }
//                    else
//                    {
//                      $sheet->setCellValue('H' .$row,'A');  
//                    }
                   // PRESENT AND ABSENT CODE END 

                    $sheet->setCellValue('H' . $row, 'Session1:');
                    if ($is_two_sessions) {
                        $sheet->setCellValue('H' . ($row + 1), 'Session2:');
                    }

                    $cell = 9;
                    foreach ($weeks as $day) 
                    {
                        $formatted_day = $day->format('Y-m-d');
                        $ses1 = '';
                        $ses2 = '';

                        $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                        if ($is_two_sessions) 
                        {
                            $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                        }
                        $cell++;
                    }

                    $row += ($is_two_sessions) ? 2 : 1;
                    $index++;
                }
                $row_update_start_assmntsign = $row;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Absent');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Present');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Total');
                $cell = 9;
                $total_users = --$index;
                /*foreach ($days as $day) {
                    $sheet->setCellValueByColumnAndRow($cell, $row, $total_users);
                    $cell++;
                }*/
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Trainer Sign-in');
                $sheet->mergeCells($assmnt_sign_column.$row_update_start_assmntsign.':'.$assmnt_sign_column.$row);

                $sheet->getStyle('A14:G' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A13:A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $row++;
                $row_after_table = $row+1;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Name of Company: '.$class_details->company_name);
                $row++;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Full Name & Designation of Director:');
                $row++;
                
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, $statment);
                $row++;
                
                
                foreach($footer_text as $k=>$v)
                {
                    $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                    $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $sheet->setCellValue('A'.$row, '** '.$k.' - '.$v);
                    $row++;
                }
                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF000000'),
                        ),
                    ),
                );
                $sheet->getStyle(
                        'A1:' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($styleArray);
                $style2Array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FFFFFFFF'),
                        ),
                    ),
                );
                $sheet->getStyle('A3:A11')->applyFromArray($style2Array);
                $sheet->getStyle(
                        'A'.$row_after_table.':' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($style2Array);
                
               

            $i++;
            }
            ob_end_clean();
        
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
        }
        else 
        {  // if condition on line no 3191
           
            $CI = & get_instance();
            $CI->load->model('trainee_model', 'traineemodel');
            $className = $class_details->class_name;
            $courseName = $class_details->crse_name;
            $class_start_date_formatted = date_format_singapore($class_details->class_start_datetime) . " " . time_format_singapore($class_details->class_start_datetime);
            $class_end_date_formatted = date_format_singapore($class_details->class_end_datetime) . " " . time_format_singapore($class_details->class_end_datetime);
            $CI->load->library('excel');
            $i=1;
            while($i<=$num_of_sheets)
            {
                 //$a="arr".$i;
                $results=$arr[$i];
                $CI->excel->createSheet();
                $CI->excel->setActiveSheetIndex($i);
                $CI->excel->getActiveSheet()->setTitle("Attendance List".$i);
                $sheet = $CI->excel->getActiveSheet();
                $CI->excel->getActiveSheet()->getRowDimension('13')->setRowHeight(50);
                $column_index = PHPExcel_Cell::columnIndexFromString('H');
                $adjusted_column_index = $column_index + $total_days;
                $last_column_name = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index);
                $assmnt_sign_column = PHPExcel_Cell::stringFromColumnIndex($adjusted_column_index+1);        
                $sheet->mergeCells('A1:' . $assmnt_sign_column . '1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true);
                $sheet->setCellValue('A1', "Attendance List1.");
                $sheet->mergeCells('A2:' . $assmnt_sign_column . '2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A2', "");
                $sheet->mergeCells('A3:' . $assmnt_sign_column . '3');
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A3', "Course Title: $courseName");
                $sheet->mergeCells('A4:' . $assmnt_sign_column . '4');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A4', "Name of Training: $className");
                $sheet->mergeCells('A5:' . $assmnt_sign_column . '5');
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A5', "Training Duration: ".$class_start_date_formatted . ' To ' . $class_end_date_formatted);
                $sheet->mergeCells('A6:' . $assmnt_sign_column . '6');
                $sheet->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A6', "Trainer: ".$class_details->classroom_trainer);
                $sheet->mergeCells('A7:' . $assmnt_sign_column . '7');
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A7', "Assessor: ".$class_details->assessor);
                $sheet->mergeCells('A8:' . $assmnt_sign_column . '8');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A8', "Company: ".$class_details->company_name);
                $sheet->mergeCells('A9:' . $assmnt_sign_column . '9');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A9', "Class ID: ".$class_details->class_id);
                $sheet->mergeCells('A10:' . $assmnt_sign_column . '10');
                $sheet->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A10', "Training Hrs: ".($class_details->total_classroom_duration + $class_details->total_lab_duration));
                $sheet->mergeCells('A11:' . $assmnt_sign_column . '11');
                $sheet->getStyle('A11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A11', "Assessment Hrs: ".$class_details->assmnt_duration);
                $sheet->setCellValue('A12', 'Sl #');
                $sheet->setCellValue('B12', 'Name');
                $sheet->setCellValue('C12', 'NRIC/FIN No.');
                $sheet->setCellValue('D12', 'Comp Id');
                $sheet->setCellValue('E12', 'Country');
                $sheet->setCellValue('F12', 'Class Start Date');
                $sheet->setCellValue('G12', 'Assmnt. Dt.');
                $sheet->setCellValue('H12', '');
                $sheet->setCellValue('I12', "Trainees' Attendance Sign-in");
                $sheet->getStyle('I12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells('I12:' . $assmnt_sign_column . '12');
                $sheet->setCellValue($assmnt_sign_column.'13','Assmnt. Sign.');
                $sheet->mergeCells('A13:G13');
                $sheet->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('H13', 'Session');

                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(15);

                $days = array();

                $current_date = $start;
                while ($current_date < $end || compare_dates_without_time($current_date, $end)) 
                {
                    $days[] = $current_date;
                    $next_day = DateTime::createFromFormat('U', strtotime("tomorrow 12:00:00", $current_date->getTimestamp()));
                    $current_date = $next_day;
                }
                $sheet->getStyle('A12:' . $assmnt_sign_column . '13')->applyFromArray(
                        array(
                            'fill' => array(
                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCCCCC')
                            )
                        )
                );

                $rn = 13;
                $cell = 8;
                $is_two_sessions = $class_details->class_session_day == 2;
                foreach ($days as $day) {
                    $session_time = "\n " . $class_schedule_data[$day->format('d/m/y')][1];
                    if ($is_two_sessions) {
                        $session_time2 = $class_schedule_data[$day->format('d/m/y')][2];
                        if ($session_time2) {
                            $session_time .= "\n " . $session_time2;
                        }
                    }
                    $sheet->setCellValueByColumnAndRow($cell, $rn, $day->format('d/m/y') . $session_time);
                    $cell++;
                }

                $row = 14;
                $index = 1;
                $footer_text = array();
                foreach ($results as $res) {
                    if ($is_two_sessions) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
                        $sheet->mergeCells('C' . $row . ':C' . ($row + 1));
                        $sheet->mergeCells('D' . $row . ':D' . ($row + 1));
                        $sheet->mergeCells('E' . $row . ':E' . ($row + 1));
                        $sheet->mergeCells('F' . $row . ':F' . ($row + 1));
                        $sheet->mergeCells('G' . $row . ':G' . ($row + 1));
                    }
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, $res['record']['name']);
                    $sheet->setCellValue('C' . $row, mask_format($res['record']['tax_code']));
                    $res['record']['company_id'] = ($res['record']['company_id'] == 0)? '': $res['record']['company_id'];
                    $sheet->setCellValue('D' . $row, $res['record']['company_id']);
                    if(!empty($res['record']['company_id'])){
                            if($res['record']['company_id'][0]== 'T'){
                                $footer_text[$res['record']['company_id']] = $tenant->tenant_name;
                            }else {
                                $footer_text[$res['record']['company_id']] = $res['record']['company_name'];
                            }
                    }
                    $sheet->setCellValue('E' . $row, $res['record']['nationality']);
                    $sheet->setCellValue('F' . $row, date('d/m/Y',  strtotime($class_details->class_start_datetime)));
                    $assmnt_date = $CI->traineemodel->get_assessment_date($res['record']['class_id'], $res['record']['user_id']);
                    $formatd_assmnt_date = empty($assmnt_date) ? '' : date('d/m/Y', strtotime($assmnt_date));
                    $sheet->setCellValue('G' . $row, $formatd_assmnt_date);

                    $sheet->setCellValue('H' . $row, 'Session1:');
                    if ($is_two_sessions) {
                        $sheet->setCellValue('H' . ($row + 1), 'Session2:');
                    }

                    $cell = 8;
                    foreach ($days as $day) {
                        $formatted_day = $day->format('Y-m-d');
                        $ses1 = '';
                        $ses2 = '';

                        $sheet->setCellValueByColumnAndRow($cell, $row, $ses1);
                        if ($is_two_sessions) {
                            $sheet->setCellValueByColumnAndRow($cell, $row + 1, $ses2);
                        }
                        $cell++;
                    }

                    $row += ($is_two_sessions) ? 2 : 1;
                    $index++;
                }
                $row_update_start_assmntsign = $row;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Absent');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Present');
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Total');
                $cell = 8;
                $total_users = --$index;
                foreach ($days as $day) {
                    $sheet->setCellValueByColumnAndRow($cell, $row, $total_users);
                    $cell++;
                }
                $row++;
                $sheet->mergeCells('A'.$row.':G'.$row);
                $sheet->setCellValue('H'.$row, 'Trainer Sign-in');
                $sheet->mergeCells($assmnt_sign_column.$row_update_start_assmntsign.':'.$assmnt_sign_column.$row);

                $sheet->getStyle('A14:G' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A13:A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $row++;
                $row_after_table = $row+1;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Name of Company: '.$class_details->company_name);
                $row++;
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, 'Full Name & Designation of Director:');
                $row++;
                
                $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet->setCellValue('A'.$row, $statment);
                $row++;
                
                foreach($footer_text as $k=>$v){
                    $sheet->mergeCells('A'.$row.':'.$assmnt_sign_column.$row);
                    $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $sheet->setCellValue('A'.$row, '** '.$k.' - '.$v);
                    $row++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FF000000'),
                        ),
                    ),
                );


                $sheet->getStyle(
                        'A1:' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($styleArray);
                $style2Array = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'FFFFFFFF'),
                        ),
                    ),
                );
                $sheet->getStyle('A3:A11')->applyFromArray($style2Array);
                $sheet->getStyle(
                        'A'.$row_after_table.':' . $sheet->getHighestColumn() . ($sheet->getHighestRow()-1)
                )->applyFromArray($style2Array);
                 $i++;
            }
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Attendance.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
            $objWriter->save('php://output');
        }
    
}


/**
 * function added to import posted data in trainer feedback
 */
function write_trainer_feedback_status($data, $trainer, $status='') {
    $CI = & get_instance();
    $CI->load->library('excel');
    $spreadsheet = new PHPExcel();
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();
    foreach (range('A', 'G') as $columnID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment ::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainer Feedback Update as on ' . date('M j Y, l'));
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Trainee Name');
    $sheet->setCellValue('D2', 'Overall Rating');
    $sheet->setCellValue('E2', 'Status');
    $sheet->setCellValue('F2', 'Failure Reason');
    $sheet->getStyle('A2:F2')->applyFromArray(
            array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:F2')->getFont()->setBold(true);
    $rn = 3;
    if ($status == 'success'){
        foreach ($data as $key => $row) {
            if ($row['status'] != 'FAILED') {
                $sheet->setCellValue('A' . $rn, $rn - 2);
                $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
                $sheet->setCellValue('C' . $rn, $row['fullname']);
                $sheet->setCellValue('D' . $rn, $row['rating']);
                $sheet->setCellValue('E' . $rn, ($row['status'] == 'FAILED') ? 'Fail.' : 'Success');
                $sheet->setCellValue('F' . $rn, $row['failure_reason']);
                $rn++;
            }
        }
    } elseif ($status == 'failed'){
        foreach ($data as $key => $row) {
            if ($row['status'] == 'FAILED') {
                $sheet->setCellValue('A' . $rn, $rn - 2);
                $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
                $sheet->setCellValue('C' . $rn, $row['fullname']);
                $sheet->setCellValue('D' . $rn, $row['rating']);
                $sheet->setCellValue('E' . $rn, ($row['status'] == 'FAILED') ? 'Fail.' : 'Success');
                $sheet->setCellValue('F' . $rn, $row['failure_reason']);
                $rn++;
            }
        }
    } else {
        foreach ($data as $key => $row) {
            $sheet->setCellValue('A' . $rn, $rn - 2);
            $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
            $sheet->setCellValue('C' . $rn, $row['fullname']);
            $sheet->setCellValue('D' . $rn, $row['rating']);
            $sheet->setCellValue('E' . $rn, ($row['status'] == 'FAILED') ? 'Fail.' : 'Success');
            $sheet->setCellValue('F' . $rn, $row['failure_reason']);
            $rn++;
        }
    }
    $writer = new PHPExcel_Writer_Excel2007($spreadsheet);
    $file_name = 'trainer_feedback_' . $trainer . '_' . $status . '.xlsx';
    $filepath = 'tmp/trainee/import_status/' . $file_name;
    $writer->save($filepath);
    return $file_name;
}

/* skm start*/
function export__tenant_page_fields($titles, $data, $filename, $sheetname = "", $main_heading = "") {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle($sheetname);
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $sheet->mergeCells('A1:' . $column_names[count($titles)] . '1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', $main_heading);
    $sheet->getStyle('A1:' . $column_names[count($titles)] . '1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $titles_count = count($titles);
    for ($i = 0; $i < $titles_count; $i++) {
        $sheet->setCellValue($column_names[$i + 1] . '2', $titles[$i]);
    }
    $sheet->getStyle('A2:' . $column_names[count($titles)] . '2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:' . $column_names[count($titles)] . '2')->getFont()->setBold(true);
    $rn = 3;
    $data_count = count($data);
    for ($i = 0; $i < $data_count; $i++) {
        $sheet->setCellValue($column_names[0] . $rn, $rn - 2);
        $rn++;
    }
    $rn = 3;
    for ($i = 0; $i < $data_count; $i++) {
        $col_index = 1;
        for ($j = 0; $j < count($data[$i]); $j++) {
            $sheet->setCellValue($column_names[$col_index] . $rn, $data[$i][$j]);
            $col_index++;
        }
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

function export__total_tenant_page_fields($titles, $data, $filename, $sheetname = "", $main_heading = "") {
    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle($sheetname);
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $column_names = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $sheet->mergeCells('A1:' . $column_names[count($titles)] . '1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', $main_heading);
    $sheet->getStyle('A1:' . $column_names[count($titles)] . '1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $titles_count = count($titles);
    for ($i = 0; $i < $titles_count; $i++) {
        $sheet->setCellValue($column_names[$i + 1] . '2', $titles[$i]);
    }
    $sheet->getStyle('A2:' . $column_names[count($titles)] . '2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:' . $column_names[count($titles)] . '2')->getFont()->setBold(true);
    $rn = 3;
    $data_count = count($data);
    for ($i = 0; $i < $data_count; $i++) {
        $sheet->setCellValue($column_names[0] . $rn, $rn - 2);
        $rn++;
    }
    $rn = 3;
    for ($i = 0; $i < $data_count; $i++) {
        $col_index = 1;
        for ($j = 0; $j < count($data[$i]); $j++) {
            $sheet->setCellValue($column_names[$col_index] . $rn, $data[$i][$j]);
            $col_index++;
        }
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
/* skm end */

/*shubhranshu  start: replace nric first 5 character with mas */
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


function export_archive_trainee($data,$course_name,$class_name) {
    $CI = & get_instance();
    $CI->load->model('Meta_Values', 'meta');
    $meta_map = $CI->meta->get_param_map();
    $CI->load->helper('metavalues');
//    $data = $query;
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Trainee Acrhive Records');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'F') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    $sheet->mergeCells('A1:C1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'Trainee Acrhive Records ' .'-'. $course_name .'-'.$class_name);
    $sheet->getStyle('A1:C1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl #');
    $sheet->setCellValue('B2', 'NRIC/FIN No.');
    $sheet->setCellValue('C2', 'Trainee Name');

    $sheet->getStyle('A2:C2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );
    $sheet->getStyle('A2:C2')->getFont()->setBold(true);
    $rn = 3;
    foreach ($data as $row) {
       
         $sheet->setCellValue('A' . $rn, $rn - 2);
         $sheet->setCellValue('B' . $rn, mask_format($row['taxcode']));
         $sheet->setCellValue('C' . $rn, $row['first_name']); 
        $rn++;
    }
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="trainee_archive.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

function export_sales_report_xls($tabledata) {
    $total_data = count($tabledata);

    $CI = & get_instance();
    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Sales  Report');
    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Z') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }
    foreach (range('A', 'C') as $columnID) {
        $var = 'A';
        $CI->excel->getActiveSheet()->getColumnDimension($var . $columnID)
                ->setAutoSize(true);
    }
    
        
    $sheet->mergeCells('A1:J1');
    $sheet->getStyle('A1:J1')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '#FF0000')
                )
            )
    );
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A1', 'CUMULATIVE REPORT AS ON ' . date('M j Y, l'));
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);

     //$sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //$sheet->setCellValueExplicit('B2', 'Total Courses: '.count($tabledata));

    $column_names = array('A3', 'B3', 'C3', 'D3', 'E3', 'F3', 'G3', 'H3', 'I3','J3');
    $column_title = array('SL #',
        'COURSE NAME','COURSE DATE','TRAINNING PROVIDER','COURSE FEE','NO. OF PAX','TOTAL SALES','TRAINEE NAME','NRIC NO','STATUS'
    );
    for ($i = 0; $i < count($column_title); $i++) {
        $sheet->setCellValue($column_names[$i], $column_title[$i]);
    }
    
    $sheet->getStyle('A3:J3')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC')
                )
            )
    );

    $sheet->getStyle('A3:J3')->getFont()->setBold(true);
    $r = 4;
    $CI->load->model('reports_model', 'reportsmodel');
    $data_arr = array();
    $duplicate_mobile_arry = array();
    //$start_row = 'B4';
    //$count = 4;
    foreach ($tabledata as $dat) {
        
       
        
        foreach ($dat as $row) {
            //$start_row = $continue? $continue:$start_row;
            //$merge_row= $start_row.":B".($count+count($dat));
        //$sheet->mergeCells($merge_row);   
        //author: added by shubhranshu as per client requirement on 11/03/2020
        if($row->provider == 'T02'){
           $provider = 'Xprienz';
        }else if($row->provider == 'T17'){
            $provider = 'Wablab';
        }
        else if($row->provider == 'T13'){
            $provider = 'Everest';
        }else{
            $provider = 'Test';
        }
       
        
        /* skm code for new style email which is the combination of taxocde and class name intials st */

        $CI->load->model('class_model', 'class_Model');
        $trainer_name = $CI->class_Model->get_trainer_names($row->classroom_trainer);

      
        $enrollment_date = date('Y-m-d', strtotime($row->class_start_datetime));

        $sheet->setCellValueExplicit('A' . $r, $r - 3);
        $sheet->setCellValue('B' . $r, $row->crse_name);
        $sheet->setCellValue('C' . $r, $enrollment_date);
        $sheet->setCellValue('D' . $r, $provider);
        $sheet->setCellValue('E' . $r, $row->coursefee);
        $sheet->setCellValue('F' . $r, count($dat));
        $sheet->setCellValue('G' . $r, (count($dat)*$row->coursefee));
        $sheet->setCellValue('H' . $r, $row->first_name);
        $sheet->setCellValue('I' . $r, $row->tax_code);
        $sheet->setCellValue('J' . $r, $row->training_score);
        
        $r++;
        //$count=$count+count($dat);
        //$continue = 'B'.$count;
        }
    }
    ob_end_clean();
    $filename = "Sales_Report_ ".date('H-i-s').".xls";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $filename);
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

/////added by shubhranshu for new enrolment report tpg xls on 19.11.2020
function export_enrolment_report_for_tpg_full($result, $tenant_id) {
    $CI = & get_instance();
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->model('company_model', 'company');
    $CI->load->model('class_trainee_model', 'classtraineemodel');

    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Enrolment Report For TPG');

    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Y') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $sheet->setCellValue('A1', '');
    $sheet->mergeCells('B1:Y1');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment:: HORIZONTAL_LEFT);
    $sheet->setCellValue('B1', 'List of all Enrolment Report As On ' . date('M j Y, l'));
    $sheet->getStyle('A1:Y1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl#');
    $sheet->setCellValue('B2', 'Trainee ID Type');
    $sheet->setCellValue('C2', 'Trainee ID');
    $sheet->setCellValue('D2', 'Date of Birth');
    $sheet->setCellValue('E2', 'Trainee Name');
    $sheet->setCellValue('F2', 'TPG Course Run ID');
    $sheet->setCellValue('G2', 'Trainee Email');
    $sheet->setCellValue('H2', 'Trainee Phone Country Code');
    $sheet->setCellValue('I2', 'Trainee Phone Area Code');
    $sheet->setCellValue('J2', 'Trainee Phone');
    $sheet->setCellValue('K2', 'Sponsorship Type');
    $sheet->setCellValue('L2', 'Employer UEN');
    $sheet->setCellValue('M2', 'Employer Contact Name');
    $sheet->setCellValue('N2', 'Employer Phone Country Code');
    $sheet->setCellValue('O2', 'Employer Phone Area Code');
    $sheet->setCellValue('P2', 'Employer Phone');
    $sheet->setCellValue('Q2', 'Employer Contact Email');
    $sheet->setCellValue('R2', 'Course Fee Discount Amount');
    $sheet->setCellValue('S2', 'Fee Collection Status');

    $sheet->getStyle('A2:Y2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC'))
            )
    );
    $sheet->getStyle('A2:Y2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($result as $row) {
        

        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $row->TraineeIDType);
        $sheet->setCellValue('C' . $rn, $row->TraineeID);
        $sheet->setCellValue('D' . $rn, $row->DateofBirth);
        $sheet->setCellValue('E' . $rn, $row->TraineeName);
        $sheet->setCellValue('F' . $rn, $row->CourseRunid);
        $sheet->setCellValue('G' . $rn, $row->TraineeEmail);
        $sheet->setCellValue('H' . $rn, $row->TraineePhoneCountryCode);
        $sheet->setCellValue('I' . $rn, $row->TraineePhoneAreaCode);
        $sheet->setCellValue('J' . $rn, $row->TraineePhone);
        $sheet->setCellValue('K' . $rn, $row->SponsorshipType);
        $sheet->setCellValue('L' . $rn, $row->EmployerUEN);
        $sheet->setCellValue('M' . $rn, $row->EmployerContactName);
        $sheet->setCellValue('N' . $rn, $row->EmployerPhoneCountryCode);
        $sheet->setCellValue('O' . $rn, $row->EmployerPhoneAreaCode);
        $sheet->setCellValue('P' . $rn, $row->EmployerPhone);
        $sheet->setCellValue('Q' . $rn, $row->EmployerContactEmail);
        $sheet->setCellValue('R' . $rn, $row->CourseFeeDiscountAmount);
        $sheet->setCellValue('S' . $rn, $row->FeeCollectionStatus);
        
        $rn++;
    }
    ob_end_clean();

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="enrolment_report_tpg.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}

    /**
     * Tenant : Fonda
     * Requested on 12-10-2021
     * Developed by : Abdulla Nofal
     * Export : XLS format
     * 
     */
function export_class_report_full($result, $tenant_id) {
    $CI = & get_instance();
       
    $CI->load->model('Meta_Values', 'meta');    
    $meta_map = $CI->meta->get_param_map();
        
    $CI->load->model('course_model', 'course');
    $CI->load->model('class_model', 'class');
    $CI->load->model('company_model', 'company');
    $CI->load->model('class_trainee_model', 'classtraineemodel');

    $CI->load->library('excel');
    $CI->excel->setActiveSheetIndex(0);
    $CI->excel->getActiveSheet()->setTitle('Class Report');

    $sheet = $CI->excel->getActiveSheet();
    foreach (range('A', 'Y') as $columnID) {
        $CI->excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
    }

    $sheet->setCellValue('A1', '');
    $sheet->mergeCells('B1:Y1');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment:: HORIZONTAL_LEFT);
    $sheet->setCellValue('B1', 'List of all Class Report As On ' . date('M j Y, l'));
    $sheet->getStyle('A1:Y1')->getFont()->setBold(true);
    $sheet->setCellValue('A2', 'Sl#');
    $sheet->setCellValue('B2', 'Trainee ID Type');
    $sheet->setCellValue('C2', 'Trainee ID');
    $sheet->setCellValue('D2', 'Trainee Name');
    $sheet->setCellValue('E2', 'Gender');
    $sheet->setCellValue('F2', 'Nationality');
    $sheet->setCellValue('G2', 'Date Of Birth');
    $sheet->setCellValue('H2', 'Race');
    $sheet->setCellValue('I2', 'Trainee Contact No.');
    $sheet->setCellValue('J2', 'Trainee Email');
    $sheet->setCellValue('K2', 'Sponsorship Type');
    $sheet->setCellValue('L2', 'Employer Name');
    $sheet->setCellValue('M2', 'Employer UEN');
    $sheet->setCellValue('N2', 'Registration Date');
    $sheet->setCellValue('O2', 'Employer Contact Name');
    $sheet->setCellValue('P2', 'Employer Contact No.');
    $sheet->setCellValue('Q2', 'Employer Contact Email');    

    $sheet->getStyle('A2:Y2')->applyFromArray(
            array('fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('argb' => 'FFCCCCCC'))
            )
    );
    $sheet->getStyle('A2:Y2')->getFont()->setBold(true);

    $rn = 3;
    foreach ($result as $row) {
        
        if ($row->TraineeIDType && $row->TraineeID) {
            if ($row->TraineeIDType != 'SNG_3') {
                $type = get_param_value($row->TraineeIDType);
                $taxcode = $type->category_name;
            }
        }
        if ($row->OtherIdentiType != NULL && $row->OtherIdentiType != '') {
            $tax_code_type = get_param_value($row->TraineeIDType);
            $type = get_param_value($row->OtherIdentiType);
            $taxcode = $tax_code_type->category_name . ' - ' . $type->category_name;
        }
                
        $sheet->setCellValue('A' . $rn, $rn - 2);
        $sheet->setCellValue('B' . $rn, $taxcode);
        $sheet->setCellValue('C' . $rn, $row->TraineeID);
        $sheet->setCellValue('D' . $rn, $row->TraineeName);
        $sheet->setCellValue('E' . $rn, $row->Gender);
        $sheet->setCellValue('F' . $rn, $meta_map[$row->Nationality]);
        $sheet->setCellValue('G' . $rn, $row->DateofBirth);
        $sheet->setCellValue('H' . $rn, $meta_map[$row->Race]);
        $sheet->setCellValue('I' . $rn, $row->TraineePhone);
        $sheet->setCellValue('J' . $rn, $row->TraineeEmail);
        $sheet->setCellValue('K' . $rn, $row->SponsorshipType);
        $sheet->setCellValue('L' . $rn, $row->EmployerName);
        $sheet->setCellValue('M' . $rn, $row->EmployerUEN);
        $sheet->setCellValue('N' . $rn, $row->EmployerRegistrationDate);
        $sheet->setCellValue('O' . $rn, $row->EmployerContactName);
        $sheet->setCellValue('P' . $rn, $row->EmployerPhone);
        $sheet->setCellValue('Q' . $rn, $row->EmployerContactEmail);        
        
        $rn++;
    }
    ob_end_clean();

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="class_report.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($CI->excel, 'Excel5');
    $objWriter->save('php://output');
}
