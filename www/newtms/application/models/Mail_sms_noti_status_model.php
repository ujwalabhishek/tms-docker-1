<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail_sms_noti_status_model extends CI_Model {

    public function record_status( $status_object ){
        $this->db->insert('mail_sms_noti_status', $status_object);
    }

} 