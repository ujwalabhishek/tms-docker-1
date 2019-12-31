<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Scheduler extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notifications_model');
        $this->load->model('internal_user_model', 'user');
        $this->load->model('mail_sms_noti_status_model', 'status');
        $this->load->model('scheduler_model', 'scheduler');
        $this->load->model('class_model', 'class');
        $this->load->library('email');
        $this->load->helper('common_helper');
        $this->load->model('tenant_model', 'tenant');
    }
    public function recalculation(){ 
        $table = $this->scheduler->get_data_for_recalc();
        foreach ($table->result() as $row) {
            $data = array(
                'tenant_id' => $row->tenant_id,
                'course_id' => $row->course_id,
                'sales_exec_id' => $row->sales_executive_id,
                'comm_period_mth' => $row->month,
                'comm_period_yr' => $row->year,
                'comm_amount' => round($row->class_feets * $row->commission_rate / 100, 2),
                'comm_detail' => "Processed from {$row->month}/{$row->year}",
                'pymnt_status' => 'NOTPAID'
            );  
        log_message('debug', 'Sales executive commission report '.print_r($data,TRUE) );
        if(!$this->scheduler->insert_data_for_recalc($data)) $err = true;
        }
        if(!$err){
            $this->scheduler->update_data_for_recalc();
        }
        if(!$err) echo('OK!');
        else echo('With ERRORS!');
    }
    /**
     * Notifications cron job
     */
    public function notifications()
    {
        $notifictions = $this->notifications_model->get_active_notifications();
        log_message("debug", "Found active notifications: ".$notifictions->num_rows());
        foreach ($notifictions->result() as $notification) {
            $users = NULL;
            log_message("debug", "Processing notification ID: " . $notification->notification_id . " type: " . $notification->noti_type);
            switch ($notification->noti_type) {
                case "INTEMLDD": 
                    $users = $this->user->get_users_by_types(array("INTUSR"),$notification->tenant_id);//tenant  id  check ,modified by:dummy
                    break;
                case "LNDEMAIL": 
                    $users = $this->user->get_users_by_types(array("TRAINE"),$notification->tenant_id);
                    break;
                case "LNDEMALDB":
                    $users = $this->user->get_users_by_types(array("INTUSR", "COMUSR", "TRAINE"),$notification->tenant_id); // account_type of company user changed by dummy.
                    break;
                case "TRNDDBEML": 
                    $users = $this->user->get_users_by_types(array("TRAINE"),$notification->tenant_id);
                    break;
                case "ALLEBDDB": 
                    $users = $this->user->get_users_by_types(array("INTUSR", "COMUSR", "TRAINE"),$notification->tenant_id); // account_type of company user changed by dummy.
                    break;
                default:
                    log_message("debug", "Notification type is: ".$notification->noti_type.", ignoring this notification ");
            }
            $tenantDetails = $this->tenant->get_tenant_details($notification->tenant_id);
            if ($users != NULL && $users->num_rows() > 0) {
                $total_sent  = 0;
                $sent_errors = 0;
                $errors      = array();
                $error_users = array();
                $cur_date = date('Y-m-d');
                foreach ($users->result() as $user) {
                    log_message('debug', '      Sending notification to ' . $user->registered_email_id);
                    if (!empty($user->registered_email_id)) {
                        if((strtotime($cur_date) == strtotime($notification->broadcast_from)) || 
                                    (strtotime($cur_date) == strtotime($notification->broadcast_to))) { 
                                    $send_result = $this->sendNotificationEmail($user->registered_email_id, $notification->noti_msg_txt);
                        }
                        $total_sent++;
                        if (!$send_result) {
                            log_message('error', '      ERROR sending notification to ' . $user->registered_email_id);
                            $last_error = $this->email->print_debugger();
                            log_message('error', $last_error);
                            $errors[]      = $last_error;
                            $error_users[] = $user->user_id;
                            $sent_errors++;
                        }                        
                    } else {
                        $error = ' User [ID:' . $user->user_id . '] email is empty.';
                        log_message('error', $error);
                        $errors[]      = $error;
                        $error_users[] = $user->user_id;
                        $sent_errors++;
                    }
                }
                if (!empty($tenantDetails)) {
                    log_message('debug', '      Sending notification to tenant ' . $tenantDetails->tenant_email_id);
                    if (!empty($tenantDetails->tenant_email_id)) {
                        $send_result = $this->sendNotificationEmail($tenantDetails->tenant_email_id, $notification->noti_msg_txt);
                        $total_sent++;
                        if (!$send_result) {
                            log_message('error', '      ERROR sending notification to tenant ' . $tenantDetails->tenant_email_id);
                            $last_error = $this->email->print_debugger();
                            log_message('error', $last_error);
                            $errors[]      = $last_error;
                            $error_users[] = 'tenant: ' . $tenantDetails->tenant_email_id;
                            $sent_errors++;
                        }
                    }
                }
                $notification_status = array(
                    'noti_template_id' => $notification->notification_id,
                    'noti_type'        => $notification->noti_type,
                    'noti_sent_on'     => date("Y-m-d H:i:s"),
                    'tenant_id'        => $notification->tenant_id,
                    'total_success'    => $total_sent - $sent_errors,
                    'total_failure'    => $sent_errors,
                    'failure_reason'   => substr(implode(", ", $errors), 0, 500),
                    'failure_user_id'  => substr(implode(", ", $error_users), 0, 250)
                );
                $this->status->record_status($notification_status);
            }
        }
        $notifictions->free_result();
        echo "OK";
    }
    /*
     * This method for sending booking_notifications for managers.
     */
    public function booking_notifications() {
        $data = $this->class->get_all_active_classes();// model for data.        
        log_message("debug", "Found active classes: ".count($data));        
        foreach ($data as $class) {            
            log_message("debug", "Processing class ID: " . $class->class_id);
            $current_date = date('Y-m-d');
            $freq1_date = NULL;
            $freq2_date = NULL;
            $freq3_date = NULL;
            $managers = NULL;
            if($class->min_reqd_noti_freq1 != 0) {
                $freq1_date = date ( 'Y-m-d', strtotime ( "-$class->min_reqd_noti_freq1 day" . $class->class_start_date) );
            } 
            if($class->min_reqd_noti_freq2 != 0) {
                $freq2_date = date ( 'Y-m-d', strtotime ( "-$class->min_reqd_noti_freq2 day" . $class->class_start_date) );
            }
            if($class->min_reqd_noti_freq3 != 0) {
                $freq3_date = date ( 'Y-m-d', strtotime ( "-$class->min_reqd_noti_freq3 day" . $class->class_start_date) );
            }
            if(strtotime($freq1_date) == strtotime($current_date)) {
                $managers = $this->class->get_manager_details($class->crse_manager);
            } else if(strtotime($freq2_date) == strtotime($current_date)) {
                $managers = $this->class->get_manager_details($class->crse_manager);
            } else if(strtotime($freq3_date) == strtotime($current_date)) {
                $managers = $this->class->get_manager_details($class->crse_manager);
            }
            if (!empty($managers)) {                
                foreach ($managers as $user) {
                    $total_enrol_count = 0;
                    $subject = NULL;
                    $noti_msg_txt = NULL;
                    log_message('debug', '      Sending notification to ' . $user->registered_email_id);
                    if (!empty($user->registered_email_id)) {
                        $total_enrol_count = $this->class->class_enrol_count($class->course_id, $class->class_id);                        
                        $subject = "Class booking notifications for '".$class->class_name."'";
                        $noti_msg_txt = "Dear ".$user->first_name." ".$user->last_name.", <br/><br/>"
                                . "Total seats booked in the class '".$class->class_name."' as on '".date('d-m-Y')."' is: <b>'".$total_enrol_count."'</b>";                                                
                        $send_result = $this->sendBookingEmail($user->registered_email_id, $noti_msg_txt, $subject);
                        if (!$send_result) {
                            log_message('error', '      ERROR sending notification to ' . $user->registered_email_id);
                            $last_error = $this->email->print_debugger();
                            log_message('error', $last_error);                            
                        }
                    } else {
                        $error = ' User [ID:' . $user->user_id . '] email is empty.';
                        log_message('error', $error);                        
                    }
                }
            }
        }        
        echo "OK";
    }
    /**
     * send mail
     * @param type $email
     * @param type $message
     * @return type
     */
    private function sendNotificationEmail($email, $message) {
        $this->email->clear();
        $this->email->from(FROM_EMAIL_ID, INBOX_MAIL_NAME);
        $this->email->subject(NOTIFICATION_MAIL_SUBJECT);
        $this->email->to($email);
        $this->email->message(NOTIFICATION_MAIL_HEADER . '<br><br>' . $message . '<br><br>' . FORGOT_PWD_MAIL_FOOTER);
        $send_result = $this->email->send();
        return $send_result;
    }
    /*
     * This method for sending class notification for managers.
     */
    private function sendBookingEmail($email, $message, $subject) {        
        $this->email->clear();
        $this->email->from(FROM_EMAIL_ID, INBOX_MAIL_NAME);
        $this->email->subject($subject);
        $this->email->to($email);
        $this->email->message($message . '<br><br>' . FORGOT_PWD_MAIL_FOOTER);
        $send_result = $this->email->send();
        return $send_result;
    }
} 