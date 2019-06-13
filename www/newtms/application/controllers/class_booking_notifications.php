<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_booking_notifications extends CI_Controller {

    public function __construct()
    {
        parent::__construct();        
        $this->load->model('class_model', 'class');
        $this->load->library('email');
        $this->load->helper('common_helper');
        
    }
    /**
     * Booking notifications
     */
    public function booking_notifications() {
        
        $data = $this->class->get_all_active_classes();
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
            } else if($class->min_reqd_noti_freq2 != 0) {
                $freq2_date = date ( 'Y-m-d', strtotime ( "-$class->min_reqd_noti_freq2 day" . $class->class_start_date) );
            } else if($class->min_reqd_noti_freq3 != 0) {
                $freq3_date = date ( 'Y-m-d', strtotime ( "-$class->min_reqd_noti_freq3 day" . $class->class_start_date) );
            }
              
            if(strtotime($freq1_date) == strtotime($current_date)) {
                $managers = $this->class->get_manager_details($class->crse_manager);
            } else if(strtotime($freq2_date) == strtotime($current_date)) {
                $managers = $this->class->get_manager_details($class->crse_manager);
            } else if(strtotime($freq3_date) == strtotime($current_date)) {
                $managers = $this->class->get_manager_details($class->crse_manager);
            }
            
            if (count($managers) >0 ) {
                $total_sent  = 0;
                $sent_errors = 0;
                $errors      = array();
                $error_users = array();
                foreach ($managers as $user) {
                    log_message('debug', '      Sending notification to ' . $user->registered_email_id);
                    if (!empty($user->registered_email_id)) {
                        $total_enrol_count = $this->class->class_enrol_count($class->course_id, $class->class_id);                        
                        $subject = "Class booking notifications for '".$class->class_name."'";
                        $noti_msg_txt = "Dear ".$user->first_name." ".$user->last_name.", <br/><br/>"
                                . "Total seats booked in the class '".$class->class_name."' as on '".date('d-m-Y')."' is: <b>'".$total_enrol_count."'</b>";                                                
                        $send_result = $this->sendEmail($user->registered_email_id, $noti_msg_txt, $subject);
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
                                
            }
        }        
        echo "OK";
    }

    private function sendEmail($email, $message, $subject) {                
        $this->email->clear();
        $this->email->from(FROM_EMAIL_ID, INBOX_MAIL_NAME);
        $this->email->subject($subject);
        $this->email->to($email);
        $this->email->message($message . '<br><br>' . FORGOT_PWD_MAIL_FOOTER);
        $send_result = $this->email->send();
        return $send_result;
    }

} 