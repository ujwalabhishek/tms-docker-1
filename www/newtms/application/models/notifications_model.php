<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications_Model extends CI_Model
{
    /**
     * get notification detail
     * @return type
     */
    public function get_notifications()
    {
        $start_date = $this->input->get_post("start_date");
        $end_date = $this->input->get_post("end_date");
        $keyword = $this->input->get_post("keyword");
        $status = $this->input->get_post("status");
        $sort_field = $this->input->get_post("sort_field");
        $sort_order = $this->input->get_post("sort_order");
        $page_number = $this->input->get_post("page_number");

        if (empty($sort_field)) $sort_field="notification_id";
        if ($sort_order!="asc") $sort_order="desc";
        if (empty($page_number) || $page_number<1) $page_number=1;
        $offset = ($page_number-1) * RECORDS_PER_PAGE;

        $this->db->select("SQL_CALC_FOUND_ROWS *", FALSE);
        $this->db->from('custom_notifications');
        if (!empty($start_date))
            $this->db->where("broadcast_from >=", date_format_mysql($start_date));
        if (!empty($end_date))
            $this->db->where("broadcast_to <=", date_format_mysql($end_date));
        if (!empty($keyword))
            $this->db->like("noti_msg_txt", $keyword);

        $today = date('Y-m-d');
        if ($status=="ACTIVE")
        {
            $this->db->where("(broadcast_from <='".$today."' AND ( broadcast_to >='".$today."' OR broadcast_to is NULL ))" );
        } else if ($status=="INACTIVE")
        {
            $this->db->where("(broadcast_from >'".$today."' OR broadcast_to <'".$today."')" );
        }
        $this->db->where("tenant_id",$this->session->userdata('userDetails')->tenant_id);
        $this->db->order_by($sort_field, $sort_order);
        $this->db->limit(RECORDS_PER_PAGE, $offset);
        $query = $this->db->get();

        $count = $this->db->query('select FOUND_ROWS() AS count')->row()->count;
        return array('query'=>$query, 'count'=>$count ) ;
    }
    /**
     * update notification
     * @return type
     */
    public function update_notification()
    {	
        $broadcast_to = $this->input->get_post('broadcast_to');
        if (empty($broadcast_to)) $broadcast_to=NULL;
        $data = array(
            'tenant_id' => $this->session->userdata('userDetails')->tenant_id,
            'noti_msg_txt' => strip_tags($this->input->get_post('noti_msg_txt')),
            'noti_type' => $this->input->get_post('noti_type'),
            'broadcast_user_type' => $this->input->get_post('broadcast_user_type'),
            'broadcast_user_list' => $this->input->get_post('broadcast_user_list'),
            'broadcast_from' => date_format_mysql($this->input->get_post('broadcast_from')),
            'broadcast_to' => date_format_mysql($broadcast_to),
            'created_by' => $this->session->userdata('userDetails')->user_id,
            'created_on' => date('Y-m-d'),
            'updated_by' => $this->session->userdata('userDetails')->user_id,
            'updated_on' => date('Y-m-d')
        );
        $id = $this->input->get_post('notification_id');
        if (!empty($id))
        {
            $this->db->where("notification_id", $id);
            return $this->db->update('custom_notifications', $data);
        } else {		
			return $this->db->insert('custom_notifications', $data);
        }

    }
    /**
     * make a copy of the notification
     * @return type
     */
    public function copy_notification()
    {
        $id = $this->input->get_post('notification_id');
        $broadcast_from = $this->input->get_post('copy_broadcast_from');
        $broadcast_to = $this->input->get_post('copy_broadcast_to');
        if (empty($broadcast_to)) $broadcast_to=NULL;

        $old = $this->db->get_where('custom_notifications', array('notification_id' => $id))->row();
        $data = array(
            'tenant_id' => $old->tenant_id,
            'noti_msg_txt' => $old->noti_msg_txt,
            'noti_type' => $old->noti_type,
            'broadcast_user_type' => $old->broadcast_user_type,
            'broadcast_user_list' => $old->broadcast_user_list,
            'broadcast_from' => date_format_mysql($broadcast_from),
            'broadcast_to' => date_format_mysql($broadcast_to),
            'created_by' => $this->session->userdata('userDetails')->user_id,
            'created_on' => date('Y-m-d'),
            'updated_by' => $this->session->userdata('userDetails')->user_id,
            'updated_on' => date('Y-m-d')
        );
        return $this->db->insert('custom_notifications', $data);
    }
    /**
     * get notification by Id
     * @param type $id
     * @return type
     */
    public function get_notification($id)
    {
        return $this->db->get_where('custom_notifications', array('notification_id' => $id));
    }
    /**
     * get all active notifications
     * @return type
     */
    public function get_active_notifications()
    {
        $sql = "select * from custom_notifications where (broadcast_from =? OR ( broadcast_to =? OR broadcast_to is NULL ))";
        $today = date('Y-m-d');
        return $this->db->query($sql, array($today, $today));
    }

} 