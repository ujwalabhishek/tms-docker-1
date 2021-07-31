<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
//------------------------ Common Pages accessible to logged in user -------------------------------------
$tms_route['COMMON_CONTROLLERS'] = array('profile');
//------------------------ Activity Log -------------------------------------
$tms_route['ACT']['controller_name'] = 'activity_log';
//------------------------ Internal-User mapping -------------------------------------
$tms_route['INTUSR']['controller_name'] = 'internal_user';
$tms_route['INTUSR']['ops'] = array('ADD' => 'add_user', 'EDIT' => 'edit_user');
//------------------------ Trainee mapping -------------------------------------
$tms_route['TRAINEE']['controller_name'] = 'trainee';
$tms_route['TRAINEE']['ops'] = array(
    'ADD' => 'add_new_trainee', 'EDIT' => 'edit_trainee', 'BULKREG' => 'bulk_registration', 'REFFRND' => 'referrals');
//------------------------ Course mapping ---------------Modified for DM June 02/2015----------------------
$tms_route['CRSE']['controller_name'] = 'course';
$tms_route['CRSE']['ops'] = array('ADD' => 'add_new_course', 'EDIT' => 'edit_course','ASSMT_TMP'=>'assessment_templates',
    'BULKREG' => 'bulk_registration', 'REFFRND' => 'referrals', 'COPY' => 'copy_course', 'SALESCOMM' => 'sales_commission_rate', 'WEPT' => 'wedgit');
//------------------------ Class mapping -------------------------------------
$tms_route['CLSS']['controller_name'] = 'classes';
$tms_route['CLSS']['ops'] = array('ADD' => 'add_new_class', 'EDIT' => 'edit_class', 'COPY' => 'copy_class', 'SCHD' => 'calendar', 'ADASTOTPG' => 'tpg_assessments');
//------------------------ Class-Trainee mapping -------------------------------------
$tms_route['CLTRAINE']['controller_name'] = 'class_trainee';
$tms_route['CLTRAINE']['ops'] = array(
    'ATTDN' => 'mark_attendance',
    'EDIT' => 'edit_user',
    'BULK_ENROL' => 'bulk_enrollment',
    'ENROL' => 'add_new_enrol',
    'ONENROL' => 'online_trainee',
    'RESCHD' => 're_schedule',
    'UPDT_TRFDB'=>'update_trainer_feedback',
    'CHG_PYMODE'=>'change_payment_mode',
    'STPGENR' => 'tpg_search_enrolment',
    'MATTPG' => 'mark_attendance_tpg');
//------------------------ Company mapping -------------------------------------
$tms_route['COMP']['controller_name'] = 'company';
$tms_route['COMP']['ops'] = array('ADD' => 'add_new_company', 'EDIT' => 'edit_company');
//------------------------ Reports General Mapping -------------------------------------
$tms_route['RPTS']['controller_name'] = 'reports';
$tms_route['RPTS']['ops'] = array(
    'ATTDN' => 'attendance', 
    'SALESRPT' => 'sales_report', 
    'ATTDN_ARCHI'=> 'attendance_archive',
    'TRSR' => 'trainee_summary',
    'CERTI' => 'certificates', 
    'ENROL' => 'enrolment_report',
    'GSTRPT' => 'gst_report',
    'FDBCK' => 'trainee_feedback',
    'SOA' => 'soa_report',  
    'WDA' => 'wda',  
    'TRAQOM' => 'traqom_report',
    'MENRLCT'=>'monthly_enrollment_count',
    'SCNRPT' => 'search_company_name',
    'CRTD'=>'certificate_distribution',
    'ERFS'=>'enrolment_report_for_tpg'
    );
//------------------------ Settings mapping -------------------------------------
$tms_route['SETTG']['controller_name'] = 'settings';
$tms_route['SETTG']['ops'] = array('NOTI_ANN' => 'notifications', 'EDIT' => 'edit_user', 'GST' => 'gst_rates');
//------------------------ Accounting mapping -------------------------------------
$tms_route['ACCTNG']['controller_name'] = 'accounting';
$tms_route['ACCTNG']['ops'] = array(
    'COMMPYMNTS' => 'update_commission',
    'GENINVOICE' => 'generate_invoice',
    'UPDTPYMNT' => 'update_payment',
    'INVSRCH' => 'invoice_search',
    'REFNDPYMNT' => 'refund_payment',
    'CREDTNTES' => 'credit_note');
//------------------------ Report - Accounting -------------------------------------
$tms_route['RPTFIN']['controller_name'] = 'reports_finance';
$tms_route['RPTFIN']['ops'] = array(
    'LSTSRCHINV' => 'invoice_list',
    'REDI' => 'invoice_reg_list',
    'PYMNTDUE' => 'payments_due',
    'PYMNTRECD' => 'payments',
    'RPTREFND' => 'refunds',
    'SLASMNTCOM' => 'sales',
    'SLSUMBYMN' => 'sales_summary_monthwise',
    'ACTLOG' => 'activity_log',
    'TMSRPTS' => 'tms_report',
    'SALREP' => 'sales_report',
    'INVADTRAIL' => 'invoice_audit_trail'); //Added for new Invoice Audit Trail Report
    //
    //////---ssg api course
$tms_route['SSGCRSE']['controller_name'] = 'ssgapi_course';
$tms_route['SSGCRSE']['ops'] = array(
    'LSTSRCSSGCRSE' => 'list_search_course');
//------------------------ Manage Tenant -------------------------------------
$tms_route['MNGTENT']['controller_name'] = 'manage_tenant';
$tms_route['MNGTENT']['ops'] = array(
    'TENTNEW' => 'add_new_tenant',
    'TENTEDIT' => 'edit_tenant',
    'ENRLCNT' => 'enrollment_count');
//------------------------ Metadata -------------------------------------
$tms_route['METADATA']['controller_name'] = 'metadata';
//------------------------ Manage Subsidy -------------------------------------
$tms_route['METASUBS']['controller_name'] = 'manage_subsidy';
//------------------------ Manage Block NRIC -------------------------------------
$tms_route['MNGNRIC']['controller_name'] = 'manage_block_nric';
$tms_route['MNGNRIC']['ops'] = array(
    'NRIC_LOG' => 'fetch_nric_restriction_log');